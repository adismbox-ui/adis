<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\Formateur;
use App\Models\Apprenant;
use App\Models\Assistant;
use App\Models\Module;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Certificat;
use App\Models\Document;
use App\Models\Niveau;
use App\Models\SessionFormation;
use App\Models\Vacance;
use App\Models\Questionnaire;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AdminApiController extends Controller
{
    private function checkAdmin()
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Vous devez être connecté pour accéder à cette page.'], 401);
        }
        if ($user->type_compte !== 'admin') {
            return response()->json(['error' => 'Accès réservé aux administrateurs.'], 403);
        }
        return null;
    }

    /**
     * Vérifier si l'utilisateur est admin ou assistant (pour les fonctionnalités de consultation)
     */
    private function checkAdminOrAssistant()
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Vous devez être connecté pour accéder à cette page.'], 401);
        }
        
        // Log de débogage
        \Illuminate\Support\Facades\Log::info('Vérification AdminOrAssistant - User ID: ' . $user->id . ', Type: ' . $user->type_compte . ', Email: ' . $user->email);
        
        // Vérifier si l'utilisateur est admin
        if ($user->type_compte === 'admin') {
            \Illuminate\Support\Facades\Log::info('Accès autorisé pour admin: ' . $user->email);
            return null;
        }
        
        // Vérifier si l'utilisateur a un profil assistant (peu importe son type_compte)
        $assistant = \App\Models\Assistant::where('utilisateur_id', $user->id)->first();
        if ($assistant && $assistant->actif) {
            \Illuminate\Support\Facades\Log::info('Accès autorisé pour assistant: ' . $user->email . ' (profil ID: ' . $assistant->id . ')');
            return null;
        }
        
        \Illuminate\Support\Facades\Log::warning('Accès refusé - Utilisateur non admin et pas de profil assistant actif: ' . $user->email);
        return response()->json(['error' => 'Accès réservé aux administrateurs et assistants.'], 403);
    }

    public function validerFormateur($id)
    {
        $formateur = \App\Models\Formateur::with('utilisateur')->find($id);

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'message' => 'Formateur non trouvé.'
            ], 404);
        }

        if ($formateur->valide) {
            return response()->json([
                'success' => false,
                'message' => 'Ce formateur est déjà validé.'
            ], 400);
        }

        $formateur->valide = true;
        $formateur->save();

        // Générer un nouveau token de vérification pour le formateur
        $utilisateur = $formateur->utilisateur;
        $verification_token = \Illuminate\Support\Str::random(48);
        $utilisateur->verification_token = $verification_token;
        $utilisateur->email_verified_at = null; // Remettre à null pour forcer la vérification email
        $utilisateur->save();

        // Envoi de l'email de validation avec le nouveau token pour l'app Adis
        $baseUrl = config('app_urls.base_url');
        $mobileScheme = config('app_urls.mobile_scheme');
        $webPath = config('app_urls.web_verification_path');
        $mobilePath = config('app_urls.mobile_verification_path');
        
        $mobileVerificationUrl = "{$mobileScheme}://{$mobilePath}/{$verification_token}";
        $webVerificationUrl = "{$baseUrl}{$webPath}/{$verification_token}";
        
        Mail::send('emails.email_verification', [
            'prenom' => $utilisateur->prenom,
            'mobileVerificationUrl' => $mobileVerificationUrl,
            'webVerificationUrl' => $webVerificationUrl,
            'type_compte' => 'formateur'
        ],
            function ($message) use ($utilisateur) {
                $message->to($utilisateur->email)
                        ->subject('Validation de votre compte Formateur ADIS');
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'Formateur validé et email de confirmation envoyé.'
        ]);
    }

    /**
     * Liste les formateurs en attente de validation (valide = false)
     */
    public function formateursEnAttente()
    {
        $formateurs = \App\Models\Formateur::with('utilisateur')
            ->where('valide', false)
            ->get();

        // Ajouter les URLs de téléchargement sécurisées pour chaque formateur
        $formateurs->each(function ($formateur) {
            $formateur->urls_telechargement = [
                'diplome_religieux' => $formateur->diplome_religieux ? url("/api/admin/formateurs/{$formateur->id}/telecharger-diplome-religieux") : null,
                'diplome_general' => $formateur->diplome_general ? url("/api/admin/formateurs/{$formateur->id}/telecharger-diplome-general") : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formateurs
        ]);
    }

    /**
     * Télécharger le diplôme religieux d'un formateur
     */
    public function telechargerDiplomeReligieux($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        $formateur = Formateur::find($id);
        if (!$formateur) {
            return response()->json(['error' => 'Formateur non trouvé'], 404);
        }

        if (!$formateur->diplome_religieux) {
            return response()->json(['error' => 'Aucun diplôme religieux trouvé'], 404);
        }

        // Vérifier si le chemin est valide
        $cheminStocke = $formateur->diplome_religieux;
        
        // Si le chemin ne contient pas de slash, essayer de le construire
        if (strpos($cheminStocke, '/') === false) {
            // Chercher le fichier dans le dossier diplomes/religieux
            $dossierDiplomes = storage_path('app/public/diplomes/religieux');
            if (is_dir($dossierDiplomes)) {
                $fichiers = scandir($dossierDiplomes);
                $fichierTrouve = null;
                $fichiersDisponibles = [];
                
                foreach ($fichiers as $fichier) {
                    if ($fichier !== '.' && $fichier !== '..') {
                        $fichiersDisponibles[] = $fichier;
                        
                        // Essayer de trouver par nom exact
                        if (pathinfo($fichier, PATHINFO_FILENAME) === $cheminStocke) {
                            $fichierTrouve = 'diplomes/religieux/' . $fichier;
                            break;
                        }
                        
                        // Essayer de trouver par nom partiel
                        if (strpos($fichier, $cheminStocke) !== false) {
                            $fichierTrouve = 'diplomes/religieux/' . $fichier;
                            break;
                        }
                    }
                }
                
                if ($fichierTrouve) {
                    $cheminStocke = $fichierTrouve;
                }
            }
        }
        
        $cheminFichier = storage_path('app/public/' . $cheminStocke);
        
        // Debug pour identifier le problème
        if (!file_exists($cheminFichier)) {
            return response()->json([
                'error' => 'Fichier non trouvé sur le serveur',
                'debug' => [
                    'chemin_stocke_bd' => $formateur->diplome_religieux,
                    'chemin_corrige' => $cheminStocke,
                    'chemin_complet' => $cheminFichier,
                    'storage_path' => storage_path('app/public'),
                    'fichier_existe' => file_exists($cheminFichier),
                    'dossier_existe' => is_dir(dirname($cheminFichier)),
                    'contenu_dossier' => is_dir(dirname($cheminFichier)) ? scandir(dirname($cheminFichier)) : 'Dossier inexistant',
                    'fichiers_disponibles' => $fichiersDisponibles ?? [],
                    'suggestion' => 'Utilisez la route /api/admin/formateurs/' . $id . '/corriger-diplome-religieux pour corriger le chemin'
                ]
            ], 404);
        }

        $nomFichier = 'diplome_religieux_' . $formateur->utilisateur->nom . '_' . $formateur->utilisateur->prenom . '.' . pathinfo($formateur->diplome_religieux, PATHINFO_EXTENSION);
        
        return response()->download($cheminFichier, $nomFichier);
    }

    /**
     * Corriger le chemin du diplôme religieux d'un formateur
     */
    public function corrigerDiplomeReligieux(Request $request, $id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        $formateur = Formateur::with('utilisateur')->find($id);
        if (!$formateur) {
            return response()->json(['error' => 'Formateur non trouvé'], 404);
        }

        // Valider la requête
        $request->validate([
            'nouveau_chemin' => 'required|string'
        ]);

        $nouveauChemin = $request->nouveau_chemin;
        
        // Vérifier que le fichier existe
        $cheminFichier = storage_path('app/public/' . $nouveauChemin);
        if (!file_exists($cheminFichier)) {
            return response()->json(['error' => 'Le fichier spécifié n\'existe pas'], 404);
        }

        // Mettre à jour la base de données
        $formateur->diplome_religieux = $nouveauChemin;
        $formateur->save();

        return response()->json([
            'success' => true,
            'message' => 'Chemin du diplôme religieux corrigé avec succès',
            'ancien_chemin' => $formateur->getOriginal('diplome_religieux'),
            'nouveau_chemin' => $nouveauChemin
        ]);
    }

    /**
     * Télécharger le diplôme général d'un formateur
     */
    public function telechargerDiplomeGeneral($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        $formateur = Formateur::find($id);
        if (!$formateur) {
            return response()->json(['error' => 'Formateur non trouvé'], 404);
        }

        if (!$formateur->diplome_general) {
            return response()->json(['error' => 'Aucun diplôme général trouvé'], 404);
        }

        $cheminFichier = storage_path('app/public/' . $formateur->diplome_general);
        if (!file_exists($cheminFichier)) {
            return response()->json(['error' => 'Fichier non trouvé sur le serveur'], 404);
        }

        $nomFichier = 'diplome_general_' . $formateur->utilisateur->nom . '_' . $formateur->utilisateur->prenom . '.' . pathinfo($formateur->diplome_general, PATHINFO_EXTENSION);
        
        return response()->download($cheminFichier, $nomFichier);
    }

    /**
     * Corriger le chemin du diplôme général d'un formateur
     */
    public function corrigerDiplomeGeneral(Request $request, $id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        $formateur = Formateur::with('utilisateur')->find($id);
        if (!$formateur) {
            return response()->json(['error' => 'Formateur non trouvé'], 404);
        }

        // Valider la requête
        $request->validate([
            'nouveau_chemin' => 'required|string'
        ]);

        $nouveauChemin = $request->nouveau_chemin;
        
        // Vérifier que le fichier existe
        $cheminFichier = storage_path('app/public/' . $nouveauChemin);
        if (!file_exists($cheminFichier)) {
            return response()->json(['error' => 'Le fichier spécifié n\'existe pas'], 404);
        }

        // Mettre à jour la base de données
        $formateur->diplome_general = $nouveauChemin;
        $formateur->save();

        return response()->json([
            'success' => true,
            'message' => 'Chemin du diplôme général corrigé avec succès',
            'ancien_chemin' => $formateur->getOriginal('diplome_general'),
            'nouveau_chemin' => $nouveauChemin
        ]);
    }

    public function logout(Request $request)
    {
        \Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Déconnexion réussie.'], 200);
    }

    /**
     * Récupérer les statistiques de tous les formateurs (vue admin)
     */
    public function statistiquesFormateurs()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }

        try {
            // Récupérer tous les formateurs validés
            $formateurs = Formateur::with(['utilisateur', 'niveaux'])
                ->where('valide', true)
                ->get();

            $statistiquesGlobales = [
                'total_formateurs' => $formateurs->count(),
                'total_niveaux_assignes' => 0,
                'total_modules' => 0,
                'total_apprenants' => 0,
                'total_paiements' => 0,
                'montant_total' => 0,
                'total_certificats' => 0
            ];

            $statistiquesParFormateur = [];

            foreach ($formateurs as $formateur) {
                // Récupérer les niveaux assignés au formateur
                $niveauxAssignes = Niveau::where('formateur_id', $formateur->id)
                    ->with(['modules', 'sessionFormation'])
                    ->get();

                // Récupérer tous les modules des niveaux assignés
                $modulesFormateur = collect();
                foreach ($niveauxAssignes as $niveau) {
                    $modulesFormateur = $modulesFormateur->merge($niveau->modules);
                }

                // Récupérer les apprenants inscrits
                $apprenants = collect();
                foreach ($modulesFormateur as $module) {
                    $inscriptions = $module->inscriptions()->with(['apprenant.utilisateur'])->get();
                    $apprenants = $apprenants->merge($inscriptions);
                }

                // Récupérer les paiements
                $paiements = collect();
                foreach ($modulesFormateur as $module) {
                    $paiementsModule = $module->paiements()->where('statut', 'valide')->get();
                    $paiements = $paiements->merge($paiementsModule);
                }

                // Récupérer les certificats
                $certificats = collect();
                foreach ($modulesFormateur as $module) {
                    $certificatsModule = $module->certificats;
                    $certificats = $certificats->merge($certificatsModule);
                }

                // Récupérer les documents
                $documents = Document::whereIn('niveau_id', $niveauxAssignes->pluck('id'))
                    ->orWhereIn('module_id', $modulesFormateur->pluck('id'))
                    ->get();

                // Statistiques du formateur
                $statsFormateur = [
                    'id' => $formateur->id,
                    'nom' => $formateur->utilisateur->nom,
                    'prenom' => $formateur->utilisateur->prenom,
                    'email' => $formateur->utilisateur->email,
                    'specialite' => $formateur->specialite,
                    'niveaux_assignes' => $niveauxAssignes->count(),
                    'modules_geres' => $modulesFormateur->count(),
                    'apprenants' => $apprenants->unique('apprenant_id')->count(),
                    'paiements_valides' => $paiements->count(),
                    'montant_genere' => $paiements->sum('montant'),
                    'certificats_delivres' => $certificats->count(),
                    'documents_crees' => $documents->count(),
                    'niveau_activite' => $this->calculerNiveauActiviteFormateur($documents, $modulesFormateur),
                    'derniere_activite' => $documents->max('created_at'),
                    'niveaux' => $niveauxAssignes->map(function($niveau) {
                        return [
                            'id' => $niveau->id,
                            'nom' => $niveau->nom,
                            'modules_count' => $niveau->modules->count(),
                            'session_formation' => $niveau->sessionFormation ? [
                                'id' => $niveau->sessionFormation->id,
                                'nom' => $niveau->sessionFormation->nom
                            ] : null
                        ];
                    })
                ];

                $statistiquesParFormateur[] = $statsFormateur;

                // Mettre à jour les statistiques globales
                $statistiquesGlobales['total_niveaux_assignes'] += $niveauxAssignes->count();
                $statistiquesGlobales['total_modules'] += $modulesFormateur->count();
                $statistiquesGlobales['total_apprenants'] += $apprenants->unique('apprenant_id')->count();
                $statistiquesGlobales['total_paiements'] += $paiements->count();
                $statistiquesGlobales['montant_total'] += $paiements->sum('montant');
                $statistiquesGlobales['total_certificats'] += $certificats->count();
            }

            // Calculer les moyennes et pourcentages
            $statistiquesGlobales['moyenne_modules_par_formateur'] = $formateurs->count() > 0 ? round($statistiquesGlobales['total_modules'] / $formateurs->count(), 2) : 0;
            $statistiquesGlobales['moyenne_apprenants_par_formateur'] = $formateurs->count() > 0 ? round($statistiquesGlobales['total_apprenants'] / $formateurs->count(), 2) : 0;
            $statistiquesGlobales['moyenne_montant_par_formateur'] = $formateurs->count() > 0 ? round($statistiquesGlobales['montant_total'] / $formateurs->count(), 2) : 0;

            return response()->json([
                'success' => true,
                'message' => 'Statistiques des formateurs récupérées avec succès',
                'statistiques_globales' => $statistiquesGlobales,
                'formateurs' => $statistiquesParFormateur,
                'periode' => [
                    'generation' => now()->format('Y-m-d H:i:s'),
                    'total_formateurs_analyses' => $formateurs->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des statistiques des formateurs',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculer le niveau d'activité d'un formateur
     */
    private function calculerNiveauActiviteFormateur($documents, $modules)
    {
        $activiteRecente = $documents->where('created_at', '>=', now()->subDays(30))->count();
        $modulesActifs = $modules->where('date_debut', '<=', now())->where('date_fin', '>=', now())->count();

        if ($activiteRecente >= 10 && $modulesActifs >= 3) {
            return 'Très actif';
        } elseif ($activiteRecente >= 5 && $modulesActifs >= 2) {
            return 'Actif';
        } elseif ($activiteRecente >= 2 && $modulesActifs >= 1) {
            return 'Modérément actif';
        } else {
            return 'Peu actif';
        }
    }

    /**
     * Donner un profil apprenant à un formateur existant
     */
    public function donnerProfilApprenantAFormateur(Request $request, $formateurId)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }

        try {
            // Validation des données
            $request->validate([
                'niveau_id' => 'required|exists:niveaux,id',
                'connaissance_adis' => 'nullable|string',
                'formation_adis' => 'nullable|boolean',
                'formation_autre' => 'nullable|boolean',
                'niveau_coran' => 'nullable|string',
                'niveau_arabe' => 'nullable|string',
                'connaissance_tomes_medine' => 'nullable|string',
                'tomes_medine_etudies' => 'nullable|array',
                'disciplines_souhaitees' => 'nullable|array',
                'attentes' => 'nullable|array',
                'formateur_domicile' => 'nullable|boolean'
            ]);

            // Vérifier que le formateur existe
            $formateur = Formateur::with('utilisateur')->find($formateurId);
            if (!$formateur) {
                // Vérifier si l'utilisateur existe et a le type_compte "formateur"
                $utilisateur = Utilisateur::where('id', $formateurId)
                    ->where('type_compte', 'formateur')
                    ->first();
                
                if (!$utilisateur) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Utilisateur formateur non trouvé'
                    ], 404);
                }

                // Créer automatiquement le profil formateur manquant
                $formateur = Formateur::create([
                    'utilisateur_id' => $utilisateur->id,
                    'valide' => true, // Par défaut validé si créé par admin
                    'specialite' => null,
                    'connaissance_adis' => null,
                    'formation_adis' => false,
                    'formation_autre' => false,
                    'niveau_coran' => null,
                    'niveau_arabe' => null,
                    'niveau_francais' => null,
                    'diplome_religieux' => null,
                    'diplome_general' => null,
                    'fichier_diplome_religieux' => null,
                    'fichier_diplome_general' => null,
                    'ville' => null,
                    'commune' => null,
                    'quartier' => null
                ]);

                // Recharger avec la relation utilisateur
                $formateur->load('utilisateur');

                \Log::info("Profil formateur manquant créé automatiquement pour l'utilisateur {$utilisateur->email}");
            }

            // Vérifier que le formateur est validé
            if (!$formateur->valide) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce formateur n\'est pas encore validé'
                ], 400);
            }

            // Vérifier que le niveau existe et est actif
            $niveau = Niveau::find($request->niveau_id);
            if (!$niveau) {
                return response()->json([
                    'success' => false,
                    'error' => 'Niveau non trouvé'
                ], 404);
            }

            if (!$niveau->actif) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce niveau n\'est pas actif'
                ], 400);
            }

            // Vérifier que l'utilisateur n'a pas déjà un profil apprenant pour ce niveau
            $apprenantExistant = Apprenant::where('utilisateur_id', $formateur->utilisateur_id)
                ->where('niveau_id', $request->niveau_id)
                ->first();

            if ($apprenantExistant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce formateur a déjà un profil apprenant pour ce niveau'
                ], 400);
            }

            // Créer le profil apprenant
            $apprenant = Apprenant::create([
                'utilisateur_id' => $formateur->utilisateur_id,
                'niveau_id' => $request->niveau_id,
                'connaissance_adis' => $request->connaissance_adis,
                'formation_adis' => $request->formation_adis ?? false,
                'formation_autre' => $request->formation_autre ?? false,
                'niveau_coran' => $request->niveau_coran,
                'niveau_arabe' => $request->niveau_arabe,
                'connaissance_tomes_medine' => $request->connaissance_tomes_medine,
                'tomes_medine_etudies' => $request->tomes_medine_etudies ? json_encode($request->tomes_medine_etudies) : null,
                'disciplines_souhaitees' => $request->disciplines_souhaitees ? json_encode($request->disciplines_souhaitees) : null,
                'attentes' => $request->attentes ? json_encode($request->attentes) : null,
                'formateur_domicile' => $request->formateur_domicile ?? false
            ]);

            // Log de l'action
            \Log::info("Admin a donné un profil apprenant au formateur {$formateur->utilisateur->nom} {$formateur->utilisateur->prenom} pour le niveau {$niveau->nom}");

            return response()->json([
                'success' => true,
                'message' => 'Profil apprenant créé avec succès pour ce formateur',
                'formateur' => [
                    'id' => $formateur->id,
                    'nom' => $formateur->utilisateur->nom,
                    'prenom' => $formateur->utilisateur->prenom,
                    'email' => $formateur->utilisateur->email,
                    'specialite' => $formateur->specialite
                ],
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre
                ],
                'profil_apprenant' => [
                    'id' => $apprenant->id,
                    'niveau_id' => $apprenant->niveau_id,
                    'connaissance_adis' => $apprenant->connaissance_adis,
                    'niveau_coran' => $apprenant->niveau_coran,
                    'niveau_arabe' => $apprenant->niveau_arabe,
                    'created_at' => $apprenant->created_at
                ],
                'created_by' => auth('api')->user()->email,
                'created_at' => now()->format('Y-m-d H:i:s')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la création du profil apprenant',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Donner le profil assistant à un formateur
     */
    public function donnerProfilAssistantAFormateur(Request $request, $formateurId)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }

        try {
            // Validation des données
            $request->validate([
                'bio' => 'nullable|string|max:500',
                'actif' => 'boolean'
            ]);

            // Vérifier que le formateur existe
            $formateur = Formateur::with('utilisateur')->find($formateurId);
            if (!$formateur) {
                // Vérifier si l'utilisateur existe et a le type_compte "formateur"
                $utilisateur = Utilisateur::where('id', $formateurId)
                    ->where('type_compte', 'formateur')
                    ->first();
                
                if (!$utilisateur) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Utilisateur formateur non trouvé'
                    ], 404);
                }

                // Créer automatiquement le profil formateur manquant
                $formateur = Formateur::create([
                    'utilisateur_id' => $utilisateur->id,
                    'valide' => true, // Par défaut validé si créé par admin
                    'specialite' => null,
                    'connaissance_adis' => null,
                    'formation_adis' => false,
                    'formation_autre' => false,
                    'niveau_coran' => null,
                    'niveau_arabe' => null,
                    'niveau_francais' => null,
                    'diplome_religieux' => null,
                    'diplome_general' => null,
                    'fichier_diplome_religieux' => null,
                    'fichier_diplome_general' => null,
                    'ville' => null,
                    'commune' => null,
                    'quartier' => null
                ]);

                // Recharger avec la relation utilisateur
                $formateur->load('utilisateur');

                \Log::info("Profil formateur manquant créé automatiquement pour l'utilisateur {$utilisateur->email}");
            }

            // Vérifier que le formateur est validé
            if (!$formateur->valide) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce formateur n\'est pas encore validé'
                ], 400);
            }

            // Vérifier que l'utilisateur n'a pas déjà un profil assistant
            $assistantExistant = \App\Models\Assistant::where('utilisateur_id', $formateur->utilisateur_id)->first();

            if ($assistantExistant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce formateur a déjà un profil assistant'
                ], 400);
            }

            // Créer le profil assistant
            $assistant = \App\Models\Assistant::create([
                'utilisateur_id' => $formateur->utilisateur_id,
                'formateur_id' => $formateur->id, // Lier à son profil formateur
                'bio' => $request->bio ?? 'Assistant créé à partir du profil formateur',
                'actif' => $request->actif ?? true
            ]);

            // Log de l'action
            \Log::info("Admin a donné un profil assistant au formateur {$formateur->utilisateur->nom} {$formateur->utilisateur->prenom}");

            return response()->json([
                'success' => true,
                'message' => 'Profil assistant créé avec succès pour ce formateur',
                'formateur' => [
                    'id' => $formateur->id,
                    'nom' => $formateur->utilisateur->nom,
                    'prenom' => $formateur->utilisateur->prenom,
                    'email' => $formateur->utilisateur->email,
                    'specialite' => $formateur->specialite
                ],
                'profil_assistant' => [
                    'id' => $assistant->id,
                    'utilisateur_id' => $assistant->utilisateur_id,
                    'formateur_id' => $assistant->formateur_id,
                    'bio' => $assistant->bio,
                    'actif' => $assistant->actif,
                    'created_at' => $assistant->created_at
                ],
                'created_by' => auth('api')->user()->email,
                'created_at' => now()->format('Y-m-d H:i:s')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la création du profil assistant',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lister les formateurs qui ont aussi un profil apprenant
     */
    public function formateursAvecProfilApprenant()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }

        try {
            // Récupérer les formateurs qui ont aussi un profil apprenant
            // Vérifier que l'utilisateur a bien type_compte = "formateur"
            // Grouper par utilisateur_id pour éliminer les doublons
            $formateurs = Formateur::with(['utilisateur', 'apprenants.niveau'])
                ->whereHas('utilisateur', function($query) {
                    $query->where('type_compte', 'formateur');
                })
                ->whereHas('apprenants')
                ->get()
                ->groupBy('utilisateur_id')
                ->map(function($formateursGroupe) {
                    // Prendre le premier formateur du groupe (le plus ancien ou le plus valide)
                    return $formateursGroupe->sortBy('created_at')->first();
                })
                ->values();

            $formateursFormates = $formateurs->map(function ($formateur) {
                return [
                    'id' => $formateur->id,
                    'utilisateur' => [
                        'id' => $formateur->utilisateur->id,
                        'nom' => $formateur->utilisateur->nom,
                        'prenom' => $formateur->utilisateur->prenom,
                        'email' => $formateur->utilisateur->email,
                        'type_compte' => $formateur->utilisateur->type_compte
                    ],
                    'specialite' => $formateur->specialite,
                    'valide' => $formateur->valide,
                    'profils_apprenants' => $formateur->apprenants->map(function ($apprenant) {
                        return [
                            'id' => $apprenant->id,
                            'niveau' => [
                                'id' => $apprenant->niveau->id,
                                'nom' => $apprenant->niveau->nom,
                                'description' => $apprenant->niveau->description
                            ],
                            'connaissance_adis' => $apprenant->connaissance_adis,
                            'niveau_coran' => $apprenant->niveau_coran,
                            'niveau_arabe' => $apprenant->niveau_arabe,
                            'created_at' => $apprenant->created_at
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Formateurs avec profil apprenant récupérés avec succès',
                'total_formateurs' => $formateurs->count(),
                'formateurs' => $formateursFormates
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des formateurs avec profil apprenant',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les formateurs qui ont un profil assistant
     */
    public function formateursAvecProfilAssistant()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }

        try {
            // Récupérer les formateurs qui ont aussi un profil assistant
            $formateurs = Formateur::with(['utilisateur', 'assistant'])
                ->whereHas('utilisateur', function($query) {
                    $query->where('type_compte', 'formateur');
                })
                ->whereHas('assistant')
                ->get()
                ->groupBy('utilisateur_id')
                ->map(function($formateursGroupe) {
                    // Prendre le premier formateur du groupe
                    return $formateursGroupe->sortBy('created_at')->first();
                })
                ->values();

            $formateursFormates = $formateurs->map(function ($formateur) {
                return [
                    'id' => $formateur->id,
                    'utilisateur' => [
                        'id' => $formateur->utilisateur->id,
                        'nom' => $formateur->utilisateur->nom,
                        'prenom' => $formateur->utilisateur->prenom,
                        'email' => $formateur->utilisateur->email,
                        'type_compte' => $formateur->utilisateur->type_compte
                    ],
                    'specialite' => $formateur->specialite,
                    'valide' => $formateur->valide,
                    'profil_assistant' => [
                        'id' => $formateur->assistant->id,
                        'bio' => $formateur->assistant->bio,
                        'actif' => $formateur->assistant->actif,
                        'created_at' => $formateur->assistant->created_at
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Formateurs avec profil assistant récupérés avec succès',
                'total_formateurs' => $formateurs->count(),
                'formateurs' => $formateursFormates
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des formateurs avec profil assistant',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les niveaux supérieurs au niveau assigné à un formateur
     */
    public function niveauxSuperieursFormateur($formateurId)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }

        try {
            // Vérifier que le formateur existe
            $formateur = Formateur::with(['utilisateur', 'niveaux'])->find($formateurId);
            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Formateur non trouvé'
                ], 404);
            }

            // Récupérer les niveaux assignés au formateur
            $niveauxAssignes = $formateur->niveaux;
            
            if ($niveauxAssignes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce formateur n\'est assigné à aucun niveau'
                ], 400);
            }

            // Trouver le niveau avec l'ordre le plus élevé parmi ceux assignés
            $niveauMaxAssigné = $niveauxAssignes->sortByDesc('ordre')->first();
            
            // Récupérer tous les niveaux supérieurs (ordre > ordre_max_assigné)
            $niveauxSuperieurs = Niveau::where('ordre', '>', $niveauMaxAssigné->ordre)
                ->where('actif', true)
                ->orderBy('ordre', 'asc')
                ->get();

            // Formater les niveaux assignés
            $niveauxAssignesFormates = $niveauxAssignes->map(function ($niveau) {
                return [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre,
                    'actif' => $niveau->actif,
                    'formateur_id' => $niveau->formateur_id,
                    'lien_meet' => $niveau->lien_meet,
                    'session_id' => $niveau->session_id,
                    'created_at' => $niveau->created_at
                ];
            });

            // Formater les niveaux supérieurs
            $niveauxSuperieursFormates = $niveauxSuperieurs->map(function ($niveau) {
                return [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre,
                    'actif' => $niveau->actif,
                    'formateur_id' => $niveau->formateur_id,
                    'lien_meet' => $niveau->lien_meet,
                    'session_id' => $niveau->session_id,
                    'statut_assignment' => $niveau->formateur_id ? 'assigné' : 'disponible',
                    'formateur_actuel' => $niveau->formateur_id ? [
                        'id' => $niveau->formateur->id ?? null,
                        'nom' => $niveau->formateur->utilisateur->nom ?? null,
                        'prenom' => $niveau->formateur->utilisateur->prenom ?? null,
                        'email' => $niveau->formateur->utilisateur->email ?? null
                    ] : null,
                    'created_at' => $niveau->created_at
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Niveaux supérieurs récupérés avec succès',
                'formateur' => [
                    'id' => $formateur->id,
                    'nom' => $formateur->utilisateur->nom,
                    'prenom' => $formateur->utilisateur->prenom,
                    'email' => $formateur->utilisateur->email,
                    'specialite' => $formateur->specialite,
                    'valide' => $formateur->valide
                ],
                'niveaux_assignes' => [
                    'total' => $niveauxAssignes->count(),
                    'niveau_max_ordre' => $niveauMaxAssigné->ordre,
                    'niveaux' => $niveauxAssignesFormates
                ],
                'niveaux_superieurs' => [
                    'total' => $niveauxSuperieurs->count(),
                    'niveaux' => $niveauxSuperieursFormates
                ],
                'analyse' => [
                    'message' => "Le formateur est actuellement assigné à {$niveauxAssignes->count()} niveau(x) avec un ordre maximum de {$niveauMaxAssigné->ordre}",
                    'niveaux_disponibles' => $niveauxSuperieurs->where('formateur_id', null)->count(),
                    'niveaux_assignes_autres' => $niveauxSuperieurs->where('formateur_id', '!=', null)->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des niveaux supérieurs',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer tous les niveaux supérieurs pour tous les formateurs
     */
    public function niveauxSuperieursTousFormateurs()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }

        try {
            // Récupérer tous les formateurs avec leurs niveaux assignés
            $formateurs = Formateur::with(['utilisateur', 'niveaux'])
                ->where('valide', true)
                ->get();

            $resultats = [];

            foreach ($formateurs as $formateur) {
                if ($formateur->niveaux->isEmpty()) {
                    continue; // Ignorer les formateurs sans niveau assigné
                }

                // Trouver le niveau avec l'ordre le plus élevé
                $niveauMaxAssigné = $formateur->niveaux->sortByDesc('ordre')->first();
                
                // Récupérer les niveaux supérieurs
                $niveauxSuperieurs = Niveau::where('ordre', '>', $niveauMaxAssigné->ordre)
                    ->where('actif', true)
                    ->orderBy('ordre', 'asc')
                    ->get();

                $resultats[] = [
                    'formateur' => [
                        'id' => $formateur->id,
                        'nom' => $formateur->utilisateur->nom,
                        'prenom' => $formateur->utilisateur->prenom,
                        'email' => $formateur->utilisateur->email,
                        'specialite' => $formateur->specialite
                    ],
                    'niveaux_assignes' => [
                        'total' => $formateur->niveaux->count(),
                        'ordre_max' => $niveauMaxAssigné->ordre,
                        'niveaux' => $formateur->niveaux->pluck('nom')->toArray()
                    ],
                    'niveaux_superieurs' => [
                        'total' => $niveauxSuperieurs->count(),
                        'niveaux' => $niveauxSuperieurs->map(function ($niveau) {
                            return [
                                'id' => $niveau->id,
                                'nom' => $niveau->nom,
                                'ordre' => $niveau->ordre,
                                'disponible' => !$niveau->formateur_id
                            ];
                        })
                    ]
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Analyse des niveaux supérieurs pour tous les formateurs',
                'total_formateurs_analyses' => count($resultats),
                'formateurs' => $resultats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'analyse des niveaux supérieurs',
                'details' => $e->getMessage()
            ], 500);
        }
    }






















    // Affiche le formulaire d'inscription rempli par le formateur
    public function showFormateur($id)
    {
        // Vérifier si l'utilisateur est admin
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $formateur = Formateur::with('utilisateur')->findOrFail($id);
        return response()->json(['formateur' => $formateur], 200);
    }

    // Valide le formateur et envoie un email
    public function validerFormateur1($id)
    {
        // Vérifier si l'utilisateur est admin
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $formateur = Formateur::with('utilisateur')->findOrFail($id);
        $formateur->valide = true;
        // Générer un token unique
        $token = bin2hex(random_bytes(32));
        $formateur->validation_token = $token;
        $formateur->save();
        // Envoi d'un email au formateur validé
        $utilisateur = $formateur->utilisateur;
        if ($utilisateur) {
            $autoLoginUrl = url('/formateur/auto-login/' . $token);
            Mail::raw(
                "Bonjour " . $utilisateur->prenom . ",\n\nVotre compte formateur a été validé par l'administrateur.\nCliquez sur ce lien pour accéder directement à votre espace formateur :\n" . $autoLoginUrl . "\n\nCe lien est à usage unique.\n\nL'équipe ADIS.",
                function ($message) use ($utilisateur) {
                    $message->to($utilisateur->email)
                            ->subject('Votre compte formateur a été validé !');
                }
            );
        }
        //return response()->json(['message' => 'Formateur validé et email envoyé.'], 200);
        return redirect()->route('admin.dashboard')->with('success', 'Formateur validé et email envoyé.');
    }

    // Gestion des utilisateurs
    public function utilisateurs()
    {
        // Vérifier si l'utilisateur est admin
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $utilisateurs = Utilisateur::with(['apprenant', 'formateur'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return response()->json(['utilisateurs' => $utilisateurs], 200);
    }

    // Gestion des modules
    public function modules()
    {
        // Vérifier si l'utilisateur est admin
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $modules = Module::with(['formateur.utilisateur'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return response()->json(['modules' => $modules], 200);
    }

    // Afficher un module (admin)
    public function showModule(Module $module)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $module->load('formateur.utilisateur');
        return response()->json(['module' => $module], 200);
    }

    // Editer un module (admin)
    public function editModule(Module $module)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $formateurs = Formateur::with('utilisateur')->where('valide', true)->get();
        return response()->json(['module' => $module, 'formateurs' => $formateurs], 200);
    }

    // Mettre à jour un module (admin)
    public function updateModule(Request $request, Module $module)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'discipline' => 'required|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'formateur_id' => 'required|exists:formateurs,id',
            'lien' => 'nullable|string|max:255',
            'support' => 'nullable|file|mimes:pdf|max:10240',
            'audio' => 'nullable|file|mimes:mp3,wav,m4a|max:20480',
            'prix' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'certificat' => 'required|boolean',
        ]);
        if ($request->hasFile('support')) {
            $data['support'] = $request->file('support')->store('supports', 'public');
        }
        if ($request->hasFile('audio')) {
            $data['audio'] = $request->file('audio')->store('audios', 'public');
        }
        $module->update($data);
        return response()->json(['module' => $module, 'message' => 'Module modifié avec succès'], 200);
    }

    // Supprimer un module (admin)
    public function destroyModule(Module $module)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $module->delete();
        return response()->json(['message' => 'Module supprimé avec succès'], 204);
    }

    // Gestion des inscriptions
    public function inscriptions()
    {
        // Vérifier si l'utilisateur est admin
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $inscriptions = Inscription::with(['apprenant.utilisateur', 'module'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return response()->json(['inscriptions' => $inscriptions], 200);
    }

    // Gestion des paiements
    public function paiements()
    {
        // Vérifier si l'utilisateur est admin
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $paiements = Paiement::with(['apprenant.utilisateur', 'module'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return response()->json(['paiements' => $paiements], 200);
    }

    /**
     * Liste des demandes de paiement en attente de validation
     */
    public function demandesPaiementEnAttente()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        
        $paiements = Paiement::with(['apprenant.utilisateur', 'module'])
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json(['paiements' => $paiements], 200);
    }

    /**
     * Valider un paiement
     */
    public function validerPaiement($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        
        $paiement = Paiement::with(['apprenant.utilisateur', 'module'])->findOrFail($id);
        $paiement->statut = 'valide';
        $paiement->save();
        
        // Créer automatiquement l'inscription au module
        $inscription = Inscription::create([
            'apprenant_id' => $paiement->apprenant_id,
            'module_id' => $paiement->module_id,
            'date_inscription' => now(),
            'statut' => 'valide',
        ]);
        
        return response()->json(['paiement' => $paiement, 'inscription' => $inscription, 'message' => 'Paiement validé et inscription créée avec succès.'], 200);
    }

    /**
     * Refuser un paiement
     */
    public function refuserPaiement($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        
        $paiement = Paiement::findOrFail($id);
        $paiement->statut = 'refuse';
        $paiement->save();
        
        return response()->json(['paiement' => $paiement, 'message' => 'Paiement refusé avec succès.'], 200);
    }

    /**
     * Liste des inscriptions en attente de validation
     */
    public function inscriptionsEnAttente()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $inscriptions = Inscription::with(['apprenant.utilisateur', 'module'])
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['inscriptions' => $inscriptions], 200);
    }

    /**
     * Valider une inscription (paiement)
     */
    public function validerInscription($id)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $inscription = Inscription::findOrFail($id);
        $inscription->statut = 'valide';
        $inscription->save();
        // (Optionnel) Notifier l'apprenant ici
        return response()->json(['inscription' => $inscription, 'message' => 'Inscription validée avec succès.'], 200);
    }

    // Liste des assistants
    public function assistants()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        $assistants = Utilisateur::where('type_compte', 'assistant')->orderBy('created_at', 'desc')->get();
        return response()->json(['assistants' => $assistants], 200);
    }

    // Formulaire de création d'assistant
    public function createAssistant()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        return response()->json(['message' => 'Endpoint pour création d\'assistant'], 200);
    }

    // Enregistrement d'un assistant
    public function storeAssistant(Request $request)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) {
            return $adminCheck;
        }
        
        $data = $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'sexe' => 'required|in:Homme,Femme',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:utilisateurs,email',
            'mot_de_passe' => 'required|string|min:4',
            'bio' => 'nullable|string|max:500',
            'formateur_id' => 'nullable|exists:formateurs,id'
        ]);
        
        try {
            // Créer l'utilisateur
            $data['mot_de_passe'] = bcrypt($data['mot_de_passe']);
            $data['type_compte'] = 'assistant';
            $data['actif'] = true;
            $data['email_verified_at'] = now();
            
            $utilisateur = Utilisateur::create($data);
            
            // Créer le profil assistant
            $profilAssistant = \App\Models\Assistant::create([
                'utilisateur_id' => $utilisateur->id,
                'formateur_id' => $data['formateur_id'] ?? null,
                'bio' => $data['bio'] ?? 'Assistant créé par l\'administrateur',
                'actif' => true
            ]);
            
            // Charger les relations pour la réponse
            $utilisateur->load('assistant');
            
            return response()->json([
                'success' => true,
                'message' => 'Assistant ajouté avec succès!',
                'assistant' => [
                    'utilisateur' => [
                        'id' => $utilisateur->id,
                        'nom' => $utilisateur->nom,
                        'prenom' => $utilisateur->prenom,
                        'email' => $utilisateur->email,
                        'telephone' => $utilisateur->telephone,
                        'sexe' => $utilisateur->sexe,
                        'type_compte' => $utilisateur->type_compte,
                        'actif' => $utilisateur->actif,
                        'email_verified_at' => $utilisateur->email_verified_at
                    ],
                    'profil_assistant' => [
                        'id' => $profilAssistant->id,
                        'bio' => $profilAssistant->bio,
                        'actif' => $profilAssistant->actif,
                        'formateur_id' => $profilAssistant->formateur_id,
                        'created_at' => $profilAssistant->created_at->format('Y-m-d H:i:s')
                    ]
                ]
            ], 201);
            
        } catch (\Exception $e) {
            // En cas d'erreur, supprimer l'utilisateur créé
            if (isset($utilisateur)) {
                $utilisateur->delete();
            }
            
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la création de l\'assistant',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un paiement (admin)
     */
    public function showPaiement(Paiement $paiement)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $paiement->load(['apprenant.utilisateur', 'module']);
        return response()->json(['paiement' => $paiement], 200);
    }

    /**
     * Editer un paiement (admin)
     */
    public function editPaiement(Paiement $paiement)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $paiement->load(['apprenant.utilisateur', 'module']);
        return response()->json(['paiement' => $paiement], 200);
    }

    /**
     * Mettre à jour un paiement (admin)
     */
    public function updatePaiement(Request $request, Paiement $paiement)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $data = $request->validate([
            'montant' => 'required|numeric|min:0',
            'statut' => 'required|in:en_attente,valide,refuse',
            'methode' => 'nullable|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $paiement->update($data);
        return response()->json(['paiement' => $paiement, 'message' => 'Paiement modifié avec succès'], 200);
    }

    /**
     * Supprimer un paiement (admin)
     */
    public function destroyPaiement(Paiement $paiement)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $paiement->delete();
        return response()->json(['message' => 'Paiement supprimé avec succès'], 204);
    }

    /**
     * Afficher une inscription (admin)
     */
    public function showInscription(Inscription $inscription)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $inscription->load(['apprenant.utilisateur', 'module']);
        return response()->json(['inscription' => $inscription], 200);
    }

    /**
     * Editer une inscription (admin)
     */
    public function editInscription(Inscription $inscription)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $inscription->load(['apprenant.utilisateur', 'module']);
        return response()->json(['inscription' => $inscription], 200);
    }

    /**
     * Mettre à jour une inscription (admin)
     */
    public function updateInscription(Request $request, Inscription $inscription)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $data = $request->validate([
            'statut' => 'required|in:en_attente,valide,refuse',
            'date_inscription' => 'required|date',
        ]);
        $inscription->update($data);
        return response()->json(['inscription' => $inscription, 'message' => 'Inscription modifiée avec succès'], 200);
    }

    /**
     * Supprimer une inscription (admin)
     */
    public function destroyInscription(Inscription $inscription)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;
        $inscription->delete();
        return response()->json(['message' => 'Inscription supprimée avec succès'], 204);
    }

    public function listeAdmins()
    {
        $user = auth()->user();
        // Optionnel : vérifier que c'est un admin
        // if (!$user || $user->type_compte !== 'admin') {
        //     return response()->json(['error' => 'Non autorisé'], 403);
        // }

        $admins = \App\Models\Utilisateur::where('type_compte', 'admin')->get();

        $result = $admins->map(function($admin) {
            return [
                'nom' => $admin->nom ?? null,
                'prenom' => $admin->prenom ?? null,
                'email' => $admin->email ?? null,
                'telephone' => $admin->telephone ?? null,
            ];
        });

        return response()->json(['admins' => $result], 200);
    }

    /**
     * Récupère les statistiques globales de la plateforme
     */
    public function getStatistiques()
    {
        $adminOrAssistantCheck = $this->checkAdminOrAssistant();
        if ($adminOrAssistantCheck) return $adminOrAssistantCheck;

        try {
            // Compter le nombre total d'utilisateurs
            $totalUtilisateurs = Utilisateur::count();
            
            // Compter le nombre total d'admins
            $totalAdmins = Utilisateur::where('type_compte', 'admin')->count();
            
            // Compter le nombre total d'assistants
            $totalAssistants = Utilisateur::where('type_compte', 'assistant')->count();
            
            // Compter le nombre total de modules
            $totalModules = Module::count();
            
            // Compter le nombre total d'apprenants
            $totalApprenants = Apprenant::count();
            
            // Compter le nombre total de niveaux
            $totalNiveaux = Niveau::count();
            
            // Compter le nombre total de sessions
            $totalSessions = \App\Models\SessionFormation::count();
            
            // Compter le nombre total de formateurs
            $totalFormateurs = Formateur::count();
            
            // Compter le nombre d'apprenants inscrits (qui ont au moins une inscription validée)
            $apprenantsInscrits = Apprenant::whereHas('inscriptions', function($query) {
                $query->where('statut', 'valide');
            })->count();
            
            // Compter le nombre d'apprenants non inscrits
            $apprenantsNonInscrits = $totalApprenants - $apprenantsInscrits;
            
            // Compter les sessions actives et inactives
            $sessionsActives = \App\Models\SessionFormation::where('actif', true)->count();
            $sessionsInactives = \App\Models\SessionFormation::where('actif', false)->count();
            
            // Compter les formateurs validés et en attente
            $formateursValides = Formateur::where('valide', true)->count();
            $formateursEnAttente = Formateur::where('valide', false)->count();

            return response()->json([
                'statistiques' => [
                    'total_utilisateurs' => $totalUtilisateurs,
                    'total_admins' => $totalAdmins,
                    'total_assistants' => $totalAssistants,
                    'total_modules' => $totalModules,
                    'total_apprenants' => $totalApprenants,
                    'total_niveaux' => $totalNiveaux,
                    'total_sessions' => $totalSessions,
                    'total_formateurs' => $totalFormateurs,
                    'apprenants_inscrits' => $apprenantsInscrits,
                    'apprenants_non_inscrits' => $apprenantsNonInscrits,
                    'sessions_actives' => $sessionsActives,
                    'sessions_inactives' => $sessionsInactives,
                    'formateurs_valides' => $formateursValides,
                    'formateurs_en_attente' => $formateursEnAttente,
                    'resume' => [
                        'utilisateurs' => [
                            'total' => $totalUtilisateurs,
                            'admins' => $totalAdmins,
                            'assistants' => $totalAssistants,
                            'apprenants' => $totalApprenants,
                            'formateurs' => $totalFormateurs
                        ],
                        'contenus' => [
                            'niveaux' => $totalNiveaux,
                            'modules' => $totalModules,
                            'sessions' => $totalSessions
                        ]
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors du calcul des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modifier le profil de l'admin connecté
     */
    public function updateProfile(Request $request)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        try {
            $user = auth()->user();
            
            $data = $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:utilisateurs,email,' . $user->id,
                'telephone' => 'nullable|string|max:20',
                'sexe' => 'nullable|in:homme,femme',
                'categorie' => 'nullable|string|max:100',
                'infos_complementaires' => 'nullable|string|max:1000'
            ]);

            // Sauvegarder les anciennes valeurs pour comparaison
            $anciennesValeurs = [
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'telephone' => $user->telephone
            ];

            // Mettre à jour l'utilisateur
            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'admin' => [
                    'id' => $user->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'telephone' => $user->telephone,
                    'sexe' => $user->sexe,
                    'categorie' => $user->categorie,
                    'type_compte' => $user->type_compte,
                    'actif' => $user->actif,
                    'updated_at' => $user->updated_at
                ],
                'modifications' => [
                    'nom' => $anciennesValeurs['nom'] !== $user->nom,
                    'prenom' => $anciennesValeurs['prenom'] !== $user->prenom,
                    'email' => $anciennesValeurs['email'] !== $user->email,
                    'telephone' => $anciennesValeurs['telephone'] !== $user->telephone
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Erreur de validation',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la mise à jour du profil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les modules d'un niveau spécifique
     */
    public function getModulesByNiveau($niveauId)
    {
        try {
            $niveau = Niveau::findOrFail($niveauId);
            $modules = $niveau->modules()
                ->with(['formateur.utilisateur', 'niveau'])
                ->orderBy('date_debut', 'asc')
                ->get()
                ->map(function($module) {
                    return [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'description' => $module->description,
                        'prix' => $module->prix,
                        'date_debut' => $module->date_debut,
                        'date_fin' => $module->date_fin,
                        'formateur' => $module->formateur ? [
                            'id' => $module->formateur->id,
                            'nom' => $module->formateur->utilisateur->nom,
                            'prenom' => $module->formateur->utilisateur->prenom,
                            'email' => $module->formateur->utilisateur->email
                        ] : null,
                        'niveau' => [
                            'id' => $module->niveau->id,
                            'nom' => $module->niveau->nom,
                            'description' => $module->niveau->description
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Modules du niveau récupérés avec succès',
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description
                ],
                'modules' => $modules,
                'total_modules' => $modules->count()
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des modules',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère tous les formateurs disponibles (validés)
     */
    public function getFormateursDisponibles()
    {
        try {
            $formateurs = Formateur::where('valide', true)
                ->with('utilisateur:id,nom,prenom,email,telephone')
                ->get()
                ->map(function($formateur) {
                    return [
                        'id' => $formateur->id,
                        'nom' => $formateur->utilisateur->nom,
                        'prenom' => $formateur->utilisateur->prenom,
                        'email' => $formateur->utilisateur->email,
                        'telephone' => $formateur->utilisateur->telephone,
                        'specialite' => $formateur->specialite,
                        'niveau_coran' => $formateur->niveau_coran,
                        'niveau_arabe' => $formateur->niveau_arabe,
                        'modules_assignes' => $formateur->modules()->count(),
                        'disponibilite' => $formateur->modules()->count() < 5 ? 'Disponible' : 'Chargé'
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Formateurs disponibles récupérés avec succès',
                'formateurs' => $formateurs,
                'total' => $formateurs->count(),
                'statistiques' => [
                    'disponibles' => $formateurs->where('disponibilite', 'Disponible')->count(),
                    'charges' => $formateurs->where('disponibilite', 'Chargé')->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des formateurs',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assigner un formateur à un module
     */
    public function assignerFormateurAuModule(Request $request, $moduleId)
    {
        try {
            // Validation des données
            $validator = Validator::make($request->all(), [
                'formateur_id' => 'required|integer|exists:formateurs,id',
                'commentaire' => 'nullable|string|max:500',
                'raison_assignation' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Données invalides',
                    'details' => $validator->errors()
                ], 422);
            }

            // Vérifier que le module existe
            $module = Module::with(['niveau', 'formateur.utilisateur'])->findOrFail($moduleId);
            
            // Vérifier que le formateur est validé
            $formateur = Formateur::where('id', $request->formateur_id)
                ->where('valide', true)
                ->firstOrFail();

            // Vérifier si le module a déjà un formateur
            if ($module->formateur_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce module a déjà un formateur assigné',
                    'details' => [
                        'formateur_actuel' => [
                            'id' => $module->formateur->id,
                            'nom' => $module->formateur->utilisateur->nom,
                            'prenom' => $module->formateur->utilisateur->prenom,
                            'email' => $module->formateur->utilisateur->email
                        ],
                        'date_assignation' => $module->updated_at
                    ]
                ], 409);
            }

            // Vérifier la charge de travail du formateur
            $modulesFormateur = $formateur->modules()->count();
            if ($modulesFormateur >= 5) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce formateur a déjà trop de modules assignés',
                    'details' => [
                        'modules_actuels' => $modulesFormateur,
                        'limite_recommandee' => 5
                    ]
                ], 400);
            }

            // Assigner le formateur au module
            $module->update([
                'formateur_id' => $formateur->id
            ]);

            // Récupérer le module mis à jour avec les relations
            $module->load(['formateur.utilisateur', 'niveau']);

            return response()->json([
                'success' => true,
                'message' => 'Formateur assigné au module avec succès',
                'module' => [
                    'id' => $module->id,
                    'titre' => $module->titre,
                    'description' => $module->description,
                    'prix' => $module->prix,
                    'niveau' => [
                        'id' => $module->niveau->id,
                        'nom' => $module->niveau->nom,
                        'description' => $module->niveau->description
                    ],
                    'formateur' => [
                        'id' => $module->formateur->id,
                        'nom' => $module->formateur->utilisateur->nom,
                        'prenom' => $module->formateur->utilisateur->prenom,
                        'email' => $module->formateur->utilisateur->email,
                        'modules_assignes' => $formateur->modules()->count()
                    ],
                    'date_assignation' => $module->updated_at
                ],
                'commentaire' => $request->commentaire,
                'raison_assignation' => $request->raison_assignation
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'assignation du formateur',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère tous les formateurs assignés à un niveau spécifique
     */
    public function getFormateursByNiveau($niveauId)
    {
        try {
            // Vérifier que le niveau existe
            $niveau = Niveau::with('formateur.utilisateur')->findOrFail($niveauId);
            
            // Vérifier si le niveau a un formateur assigné
            if (!$niveau->formateur_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce niveau n\'a pas de formateur assigné'
                ], 404);
            }

            // Récupérer le formateur assigné au niveau
            $formateur = $niveau->formateur;
            
            // Récupérer les modules du niveau
            $modules = Module::where('niveau_id', $niveauId)->get();

            // Formater les informations du formateur
            $formateurFormate = [
                'id' => $formateur->id,
                'utilisateur_id' => $formateur->utilisateur_id,
                'nom' => $formateur->utilisateur->nom,
                'prenom' => $formateur->utilisateur->prenom,
                'email' => $formateur->utilisateur->email,
                'telephone' => $formateur->utilisateur->telephone,
                'sexe' => $formateur->utilisateur->sexe,
                'categorie' => $formateur->utilisateur->categorie,
                'specialite' => $formateur->specialite,
                'valide' => $formateur->valide,
                'connaissance_adis' => $formateur->connaissance_adis,
                'formation_adis' => $formateur->formation_adis,
                'formation_autre' => $formateur->formation_autre,
                'niveau_coran' => $formateur->niveau_coran,
                'niveau_arabe' => $formateur->niveau_arabe,
                'niveau_francais' => $formateur->niveau_francais,
                'diplome_religieux' => $formateur->diplome_religieux,
                'diplome_general' => $formateur->diplome_general,
                'ville' => $formateur->ville,
                'commune' => $formateur->commune,
                'quartier' => $formateur->quartier,
                'date_assignation' => $niveau->updated_at,
                'modules_du_niveau' => $modules->count(),
                'charge_travail' => $modules->count() >= 5 ? 'Chargé' : 'Disponible'
            ];

            // Statistiques du niveau
            $statistiques = [
                'total_modules' => $modules->count(),
                'formateur_assigné' => true,
                'date_assignation' => $niveau->updated_at->format('Y-m-d H:i:s'),
                'lien_meet' => $niveau->lien_meet,
                'session_id' => $niveau->session_id
            ];

            return response()->json([
                'success' => true,
                'message' => 'Formateur du niveau récupéré avec succès',
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre,
                    'actif' => $niveau->actif,
                    'formateur_id' => $niveau->formateur_id
                ],
                'formateur' => $formateurFormate,
                'modules' => $modules->map(function($module) {
                    return [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'description' => $module->description,
                        'discipline' => $module->discipline,
                        'date_debut' => $module->date_debut,
                        'date_fin' => $module->date_fin,
                        'prix' => $module->prix,
                        'certificat' => $module->certificat
                    ];
                }),
                'statistiques' => $statistiques
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération du formateur du niveau',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère la liste des apprenants qui n'ont pas payé leur niveau actuel
     */
    public function getApprenantsNonPayants()
    {
        try {
            $adminCheck = $this->checkAdmin();
            if ($adminCheck) return $adminCheck;

            // Récupérer tous les apprenants avec leur niveau actuel
            $apprenants = Apprenant::with([
                'utilisateur:id,nom,prenom,email,telephone',
                'niveau:id,nom,description'
            ])
            ->whereNotNull('niveau_id')
            ->get();

            $apprenantsNonPayants = [];

            foreach ($apprenants as $apprenant) {
                // Récupérer tous les modules du niveau actuel de l'apprenant
                $modulesDuNiveau = Module::where('niveau_id', $apprenant->niveau_id)
                    ->get();

                if ($modulesDuNiveau->isEmpty()) {
                    continue; // Pas de modules pour ce niveau
                }

                // Calculer le montant total du niveau
                $montantTotalNiveau = $modulesDuNiveau->sum('prix');

                // Récupérer tous les paiements valides de l'apprenant pour ce niveau
                $paiementsValides = Paiement::where('apprenant_id', $apprenant->id)
                    ->whereIn('module_id', $modulesDuNiveau->pluck('id'))
                    ->where('statut', 'valide')
                    ->get();

                // Récupérer les paiements en attente ou refusés
                $paiementsEnAttente = Paiement::where('apprenant_id', $apprenant->id)
                    ->whereIn('module_id', $modulesDuNiveau->pluck('id'))
                    ->whereIn('statut', ['en_attente', 'refuse'])
                    ->get();

                // Calculer le montant payé
                $montantPaye = $paiementsValides->sum('montant');
                $montantRestant = $montantTotalNiveau - $montantPaye;

                // Vérifier si l'apprenant a payé complètement son niveau
                $aPayeCompletement = $montantPaye >= $montantTotalNiveau;

                if (!$aPayeCompletement) {
                    // Détails des modules non payés
                    $modulesNonPayes = $modulesDuNiveau->map(function($module) use ($paiementsValides, $paiementsEnAttente) {
                        $paiementValide = $paiementsValides->where('module_id', $module->id)->first();
                        $paiementEnAttente = $paiementsEnAttente->where('module_id', $module->id)->first();
                        
                        return [
                            'id' => $module->id,
                            'titre' => $module->titre,
                            'description' => $module->description,
                            'prix' => $module->prix,
                            'date_debut' => $module->date_debut,
                            'date_fin' => $module->date_fin,
                            'statut_paiement' => $paiementValide ? 'paye' : ($paiementEnAttente ? $paiementEnAttente->statut : 'non_demande'),
                            'paiement_id' => $paiementValide ? $paiementValide->id : ($paiementEnAttente ? $paiementEnAttente->id : null),
                            'montant_paye' => $paiementValide ? $paiementValide->montant : 0,
                            'methode_paiement' => $paiementValide ? $paiementValide->methode : null,
                            'date_paiement' => $paiementValide ? $paiementValide->date_paiement : null
                        ];
                    });

                    $apprenantsNonPayants[] = [
                        'apprenant' => [
                            'id' => $apprenant->id,
                            'nom' => $apprenant->utilisateur->nom,
                            'prenom' => $apprenant->utilisateur->prenom,
                            'email' => $apprenant->utilisateur->email,
                            'telephone' => $apprenant->utilisateur->telephone,
                            'niveau_actuel' => [
                                'id' => $apprenant->niveau->id,
                                'nom' => $apprenant->niveau->nom,
                                'description' => $apprenant->niveau->description,
                                'prix_global' => null // Le prix est au niveau des modules, pas du niveau
                            ]
                        ],
                        'situation_paiement' => [
                            'montant_total_niveau' => $montantTotalNiveau,
                            'montant_paye' => $montantPaye,
                            'montant_restant' => $montantRestant,
                            'pourcentage_paye' => $montantTotalNiveau > 0 ? round(($montantPaye / $montantTotalNiveau) * 100, 2) : 0,
                            'statut_general' => $montantPaye > 0 ? 'paiement_partiel' : 'aucun_paiement'
                        ],
                        'modules' => [
                            'total_modules' => $modulesDuNiveau->count(),
                            'modules_payes' => $paiementsValides->count(),
                            'modules_en_attente' => $paiementsEnAttente->where('statut', 'en_attente')->count(),
                            'modules_refuses' => $paiementsEnAttente->where('statut', 'refuse')->count(),
                            'modules_non_demandes' => $modulesDuNiveau->count() - $paiementsValides->count() - $paiementsEnAttente->count(),
                            'details' => $modulesNonPayes
                        ],
                        'paiements' => [
                            'valides' => $paiementsValides->map(function($p) {
                                return [
                                    'id' => $p->id,
                                    'module_id' => $p->module_id,
                                    'montant' => $p->montant,
                                    'methode' => $p->methode,
                                    'date_paiement' => $p->date_paiement,
                                    'reference' => $p->reference
                                ];
                            }),
                            'en_attente' => $paiementsEnAttente->where('statut', 'en_attente')->map(function($p) {
                                return [
                                    'id' => $p->id,
                                    'module_id' => $p->module_id,
                                    'montant' => $p->montant,
                                    'methode' => $p->methode,
                                    'date_paiement' => $p->date_paiement,
                                    'reference' => $p->reference
                                ];
                            }),
                            'refuses' => $paiementsEnAttente->where('statut', 'refuse')->map(function($p) {
                                return [
                                    'id' => $p->id,
                                    'module_id' => $p->module_id,
                                    'montant' => $p->montant,
                                    'methode' => $p->methode,
                                    'date_paiement' => $p->date_paiement,
                                    'reference' => $p->reference
                                ];
                            })
                        ],
                        'derniere_activite' => [
                            'dernier_paiement' => $paiementsValides->max('date_paiement'),
                            'derniere_demande' => $paiementsEnAttente->max('created_at'),
                            'derniere_modification' => $apprenant->updated_at
                        ]
                    ];
                }
            }

            // Organiser les apprenants par niveau
            $apprenantsParNiveau = collect($apprenantsNonPayants)
                ->groupBy('apprenant.niveau_actuel.id')
                ->map(function($apprenantsNiveau, $niveauId) {
                    $premierApprenant = $apprenantsNiveau->first();
                    $niveau = $premierApprenant['apprenant']['niveau_actuel'];
                    
                    return [
                        'niveau' => [
                            'id' => $niveau['id'],
                            'nom' => $niveau['nom'],
                            'description' => $niveau['description']
                        ],
                        'apprenants_non_payants' => $apprenantsNiveau->map(function($apprenant) {
                            // Retirer les informations du niveau de chaque apprenant pour éviter la duplication
                            unset($apprenant['apprenant']['niveau_actuel']);
                            return $apprenant;
                        })->values(),
                        'statistiques_niveau' => [
                            'total_apprenants' => $apprenantsNiveau->count(),
                            'montant_total_du' => $apprenantsNiveau->sum('situation_paiement.montant_restant'),
                            'modules_concernes' => $apprenantsNiveau->sum('modules.total_modules'),
                            'paiements_en_attente' => $apprenantsNiveau->sum('modules.modules_en_attente'),
                            'paiements_refuses' => $apprenantsNiveau->sum('modules.modules_refuses'),
                            'paiements_partiels' => $apprenantsNiveau->where('situation_paiement.statut_general', 'paiement_partiel')->count(),
                            'aucun_paiement' => $apprenantsNiveau->where('situation_paiement.statut_general', 'aucun_paiement')->count()
                        ]
                    ];
                })->values();

            // Statistiques globales
            $statistiques = [
                'total_niveaux_concernes' => $apprenantsParNiveau->count(),
                'total_apprenants_avec_niveau' => $apprenants->count(),
                'total_apprenants_non_payants' => count($apprenantsNonPayants),
                'total_apprenants_payants' => $apprenants->count() - count($apprenantsNonPayants),
                'taux_impayes' => $apprenants->count() > 0 ? round((count($apprenantsNonPayants) / $apprenants->count()) * 100, 2) : 0,
                'montant_total_du' => collect($apprenantsNonPayants)->sum(function($a) {
                    return $a['situation_paiement']['montant_restant'];
                }),
                'repartition_par_niveau' => $apprenantsParNiveau->map(function($niveau) {
                    return [
                        'niveau' => $niveau['niveau']['nom'],
                        'nombre_apprenants' => $niveau['statistiques_niveau']['total_apprenants'],
                        'montant_total_du' => $niveau['statistiques_niveau']['montant_total_du']
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'message' => 'Liste des apprenants non payants organisée par niveau',
                'niveaux' => $apprenantsParNiveau,
                'statistiques' => $statistiques,
                'total_niveaux' => $apprenantsParNiveau->count(),
                'total_apprenants' => count($apprenantsNonPayants)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des apprenants non payants',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère la liste des apprenants qui ont payé leur niveau actuel
     */
    public function getApprenantsPayants()
    {
        try {
            $adminCheck = $this->checkAdmin();
            if ($adminCheck) return $adminCheck;

            // Récupérer tous les apprenants avec leur niveau actuel
            $apprenants = Apprenant::with([
                'utilisateur:id,nom,prenom,email,telephone',
                'niveau:id,nom,description'
            ])
            ->whereNotNull('niveau_id')
            ->get();

            $apprenantsPayants = [];

            foreach ($apprenants as $apprenant) {
                // Récupérer tous les modules du niveau actuel de l'apprenant
                $modulesDuNiveau = Module::where('niveau_id', $apprenant->niveau_id)
                    ->get();

                if ($modulesDuNiveau->isEmpty()) {
                    continue; // Pas de modules pour ce niveau
                }

                // Calculer le montant total du niveau
                $montantTotalNiveau = $modulesDuNiveau->sum('prix');

                // Récupérer tous les paiements valides de l'apprenant pour ce niveau
                $paiementsValides = Paiement::where('apprenant_id', $apprenant->id)
                    ->whereIn('module_id', $modulesDuNiveau->pluck('id'))
                    ->where('statut', 'valide')
                    ->get();

                // Récupérer les paiements en attente ou refusés
                $paiementsEnAttente = Paiement::where('apprenant_id', $apprenant->id)
                    ->whereIn('module_id', $modulesDuNiveau->pluck('id'))
                    ->whereIn('statut', ['en_attente', 'refuse'])
                    ->get();

                // Calculer le montant payé
                $montantPaye = $paiementsValides->sum('montant');
                $montantRestant = $montantTotalNiveau - $montantPaye;

                // Vérifier si l'apprenant a payé complètement son niveau
                $aPayeCompletement = $montantPaye >= $montantTotalNiveau;

                if ($aPayeCompletement) {
                    // Détails des modules payés
                    $modulesPayes = $modulesDuNiveau->map(function($module) use ($paiementsValides) {
                        $paiementValide = $paiementsValides->where('module_id', $module->id)->first();
                        
                        return [
                            'id' => $module->id,
                            'titre' => $module->titre,
                            'description' => $module->description,
                            'prix' => $module->prix,
                            'date_debut' => $module->date_debut,
                            'date_fin' => $module->date_fin,
                            'statut_paiement' => 'paye',
                            'paiement_id' => $paiementValide ? $paiementValide->id : null,
                            'montant_paye' => $paiementValide ? $paiementValide->montant : 0,
                            'methode_paiement' => $paiementValide ? $paiementValide->methode : null,
                            'date_paiement' => $paiementValide ? $paiementValide->date_paiement : null
                        ];
                    });

                    $apprenantsPayants[] = [
                        'apprenant' => [
                            'id' => $apprenant->id,
                            'nom' => $apprenant->utilisateur->nom,
                            'prenom' => $apprenant->utilisateur->prenom,
                            'email' => $apprenant->utilisateur->email,
                            'telephone' => $apprenant->utilisateur->telephone,
                            'niveau_actuel' => [
                                'id' => $apprenant->niveau->id,
                                'nom' => $apprenant->niveau->nom,
                                'description' => $apprenant->niveau->description
                            ]
                        ],
                        'situation_paiement' => [
                            'montant_total_niveau' => $montantTotalNiveau,
                            'montant_paye' => $montantPaye,
                            'montant_restant' => $montantRestant,
                            'pourcentage_paye' => $montantTotalNiveau > 0 ? round(($montantPaye / $montantTotalNiveau) * 100, 2) : 0,
                            'statut_general' => 'niveau_completement_paye',
                            'date_paiement_complet' => $paiementsValides->max('date_paiement')
                        ],
                        'modules' => [
                            'total_modules' => $modulesDuNiveau->count(),
                            'modules_payes' => $paiementsValides->count(),
                            'modules_en_attente' => $paiementsEnAttente->where('statut', 'en_attente')->count(),
                            'modules_refuses' => $paiementsEnAttente->where('statut', 'refuse')->count(),
                            'modules_non_demandes' => $modulesDuNiveau->count() - $paiementsValides->count() - $paiementsEnAttente->count(),
                            'details' => $modulesPayes
                        ],
                        'paiements' => [
                            'valides' => $paiementsValides->map(function($p) {
                                return [
                                    'id' => $p->id,
                                    'module_id' => $p->module_id,
                                    'montant' => $p->montant,
                                    'methode' => $p->methode,
                                    'date_paiement' => $p->date_paiement,
                                    'reference' => $p->reference
                                ];
                            }),
                            'en_attente' => $paiementsEnAttente->where('statut', 'en_attente')->map(function($p) {
                                return [
                                    'id' => $p->id,
                                    'module_id' => $p->module_id,
                                    'montant' => $p->montant,
                                    'methode' => $p->methode,
                                    'date_paiement' => $p->date_paiement,
                                    'reference' => $p->reference
                                ];
                            }),
                            'refuses' => $paiementsEnAttente->where('statut', 'refuse')->map(function($p) {
                                return [
                                    'id' => $p->id,
                                    'module_id' => $p->module_id,
                                    'montant' => $p->montant,
                                    'methode' => $p->methode,
                                    'date_paiement' => $p->date_paiement,
                                    'reference' => $p->reference
                                ];
                            })
                        ],
                        'derniere_activite' => [
                            'dernier_paiement' => $paiementsValides->max('date_paiement'),
                            'derniere_demande' => $paiementsEnAttente->max('created_at'),
                            'derniere_modification' => $apprenant->updated_at
                        ]
                    ];
                }
            }

            // Organiser les apprenants par niveau
            $apprenantsParNiveau = collect($apprenantsPayants)
                ->groupBy('apprenant.niveau_actuel.id')
                ->map(function($apprenantsNiveau, $niveauId) {
                    $premierApprenant = $apprenantsNiveau->first();
                    $niveau = $premierApprenant['apprenant']['niveau_actuel'];
                    
                    return [
                        'niveau' => [
                            'id' => $niveau['id'],
                            'nom' => $niveau['nom'],
                            'description' => $niveau['description']
                        ],
                        'apprenants_payants' => $apprenantsNiveau->map(function($apprenant) {
                            // Retirer les informations du niveau de chaque apprenant pour éviter la duplication
                            unset($apprenant['apprenant']['niveau_actuel']);
                            return $apprenant;
                        })->values(),
                        'statistiques_niveau' => [
                            'total_apprenants' => $apprenantsNiveau->count(),
                            'montant_total_paye' => $apprenantsNiveau->sum('situation_paiement.montant_paye'),
                            'modules_concernes' => $apprenantsNiveau->sum('modules.total_modules'),
                            'paiements_en_attente' => $apprenantsNiveau->sum('modules.modules_en_attente'),
                            'paiements_refuses' => $apprenantsNiveau->sum('modules.modules_refuses'),
                            'paiements_complets' => $apprenantsNiveau->where('situation_paiement.statut_general', 'niveau_completement_paye')->count(),
                            'moyenne_pourcentage_paye' => round($apprenantsNiveau->avg('situation_paiement.pourcentage_paye'), 2)
                        ]
                    ];
                })->values();

            // Statistiques globales
            $statistiques = [
                'total_niveaux_concernes' => $apprenantsParNiveau->count(),
                'total_apprenants_avec_niveau' => $apprenants->count(),
                'total_apprenants_payants' => count($apprenantsPayants),
                'total_apprenants_non_payants' => $apprenants->count() - count($apprenantsPayants),
                'taux_payants' => $apprenants->count() > 0 ? round((count($apprenantsPayants) / $apprenants->count()) * 100, 2) : 0,
                'montant_total_paye' => collect($apprenantsPayants)->sum(function($a) {
                    return $a['situation_paiement']['montant_paye'];
                }),
                'repartition_par_niveau' => $apprenantsParNiveau->map(function($niveau) {
                    return [
                        'niveau' => $niveau['niveau']['nom'],
                        'nombre_apprenants' => $niveau['statistiques_niveau']['total_apprenants'],
                        'montant_total_paye' => $niveau['statistiques_niveau']['montant_total_paye']
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'message' => 'Liste des apprenants payants organisée par niveau',
                'niveaux' => $apprenantsParNiveau,
                'statistiques' => $statistiques,
                'total_niveaux' => $apprenantsParNiveau->count(),
                'total_apprenants' => count($apprenantsPayants)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des apprenants payants',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recherche universelle d'utilisateurs par email (apprenants, formateurs, assistants, admins)
     */
    public function rechercherUtilisateurParEmail(Request $request)
    {
        try {
            $adminCheck = $this->checkAdmin();
            if ($adminCheck) return $adminCheck;

            // Validation des données
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first()
                ], 400);
            }

            $email = $request->email;

            // Rechercher l'utilisateur par email
            $utilisateur = Utilisateur::where('email', $email)->first();

            if (!$utilisateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun utilisateur trouvé avec cet email'
                ], 404);
            }

            // Récupérer les informations spécifiques selon le type de compte
            $resultat = [
                'utilisateur' => [
                    'id' => $utilisateur->id,
                    'nom' => $utilisateur->nom,
                    'prenom' => $utilisateur->prenom,
                    'email' => $utilisateur->email,
                    'telephone' => $utilisateur->telephone,
                    'sexe' => $utilisateur->sexe,
                    'type_compte' => $utilisateur->type_compte,
                    'actif' => $utilisateur->actif,
                    'email_verified_at' => $utilisateur->email_verified_at,
                    'created_at' => $utilisateur->created_at,
                    'updated_at' => $utilisateur->updated_at
                ],
                'profil_specifique' => null,
                'statistiques' => []
            ];

            // Récupérer les informations spécifiques selon le type de compte
            switch ($utilisateur->type_compte) {
                case 'apprenant':
                    $apprenant = $utilisateur->apprenant;
                    if ($apprenant) {
                        $resultat['profil_specifique'] = [
                            'type' => 'apprenant',
                            'id' => $apprenant->id,
                            'niveau' => $apprenant->niveau ? [
                                'id' => $apprenant->niveau->id,
                                'nom' => $apprenant->niveau->nom,
                                'description' => $apprenant->niveau->description
                            ] : null,
                            'connaissance_adis' => $apprenant->connaissance_adis,
                            'formation_adis' => $apprenant->formation_adis,
                            'formation_autre' => $apprenant->formation_autre,
                            'niveau_coran' => $apprenant->niveau_coran,
                            'niveau_arabe' => $apprenant->niveau_arabe,
                            'connaissance_tomes_medine' => $apprenant->connaissance_tomes_medine,
                            'tomes_medine_etudies' => $apprenant->tomes_medine_etudies,
                            'disciplines_souhaitees' => $apprenant->disciplines_souhaitees,
                            'attentes' => $apprenant->attentes,
                            'formateur_domicile' => $apprenant->formateur_domicile
                        ];
                        
                        // Statistiques de l'apprenant
                        $resultat['statistiques'] = [
                            'inscriptions_count' => $apprenant->inscriptions()->count(),
                            'paiements_count' => $apprenant->paiements()->count(),
                            'certificats_count' => $apprenant->certificats()->count(),
                            'paiements_valides' => $apprenant->paiements()->where('statut', 'valide')->count(),
                            'paiements_en_attente' => $apprenant->paiements()->where('statut', 'en_attente')->count(),
                            'paiements_refuses' => $apprenant->paiements()->where('statut', 'refuse')->count()
                        ];
                    }
                    break;

                case 'formateur':
                    $formateur = $utilisateur->formateur;
                    if ($formateur) {
                        $resultat['profil_specifique'] = [
                            'type' => 'formateur',
                            'id' => $formateur->id,
                            'specialite' => $formateur->specialite,
                            'niveau_coran' => $formateur->niveau_coran,
                            'niveau_arabe' => $formateur->niveau_arabe,
                            'valide' => $formateur->valide,
                            'experience' => $formateur->experience,
                            'bio' => $formateur->bio
                        ];
                        
                        // Statistiques du formateur
                        $resultat['statistiques'] = [
                            'modules_count' => $formateur->modules()->count(),
                            'apprenants_count' => $formateur->modules()->withCount('inscriptions')->get()->sum('inscriptions_count'),
                            'paiements_count' => $formateur->modules()->withCount('paiements')->get()->sum('paiements_count'),
                            'certificats_count' => $formateur->modules()->withCount('certificats')->get()->sum('certificats_count'),
                            'questionnaires_count' => $formateur->modules()->withCount('questionnaires')->get()->sum('questionnaires_count'),
                            'documents_count' => $formateur->modules()->withCount('documents')->get()->sum('documents_count')
                        ];
                    }
                    break;

                case 'assistant':
                    $resultat['profil_specifique'] = [
                        'type' => 'assistant',
                        'role' => 'Assistant administratif',
                        'permissions' => 'Gestion des apprenants, formateurs et modules'
                    ];
                    
                    // Statistiques de l'assistant (peuvent être ajoutées selon vos besoins)
                    $resultat['statistiques'] = [
                        'role' => 'Assistant administratif',
                        'permissions' => 'Gestion des apprenants, formateurs et modules'
                    ];
                    break;

                case 'admin':
                    $resultat['profil_specifique'] = [
                        'type' => 'admin',
                        'role' => 'Administrateur système',
                        'permissions' => 'Accès complet à toutes les fonctionnalités',
                        'niveau_acces' => 'Super administrateur'
                    ];
                    
                    // Statistiques de l'admin
                    $resultat['statistiques'] = [
                        'role' => 'Administrateur système',
                        'permissions' => 'Accès complet à toutes les fonctionnalités'
                    ];
                    break;

                default:
                    $resultat['profil_specifique'] = [
                        'type' => 'utilisateur',
                        'role' => 'Utilisateur standard',
                        'note' => 'Type de compte non reconnu'
                    ];
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur trouvé avec succès',
                'data' => $resultat
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la recherche de l\'utilisateur',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifie les certificats d'un apprenant par email (accessible aux admins et assistants)
     */
    public function verifierCertificatsApprenant(Request $request)
    {
        try {
            // Vérifier que l'utilisateur connecté est admin ou assistant
            $user = auth('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Vous devez être connecté pour accéder à cette fonctionnalité.'
                ], 401);
            }

            if (!in_array($user->type_compte, ['admin', 'assistant'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé. Seuls les administrateurs et assistants peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            // Validation des données
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first()
                ], 400);
            }

            $email = $request->email;

            // Rechercher l'apprenant par email
            $apprenant = Apprenant::with([
                'utilisateur:id,nom,prenom,email,telephone,sexe,actif',
                'niveau:id,nom,description',
                'certificats.module.niveau',
                'certificats.module.formateur.utilisateur:id,nom,prenom',
                'inscriptions.module.niveau',
                'paiements.module.niveau'
            ])
            ->whereHas('utilisateur', function($query) use ($email) {
                $query->where('email', $email);
            })
            ->first();

            if (!$apprenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun apprenant trouvé avec cet email'
                ], 404);
            }

            // Vérifier si l'apprenant est actif
            if (!$apprenant->utilisateur->actif) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cet apprenant est désactivé',
                    'apprenant' => [
                        'id' => $apprenant->id,
                        'nom' => $apprenant->utilisateur->nom,
                        'prenom' => $apprenant->utilisateur->prenom,
                        'email' => $apprenant->utilisateur->email,
                        'statut' => 'désactivé'
                    ]
                ], 400);
            }

            // Vérifier que le niveau est bien chargé
            if (!$apprenant->niveau) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cet apprenant n\'a pas de niveau assigné',
                    'apprenant' => [
                        'id' => $apprenant->id,
                        'nom' => $apprenant->utilisateur->nom,
                        'prenom' => $apprenant->utilisateur->prenom,
                        'email' => $apprenant->utilisateur->email,
                        'niveau_id' => $apprenant->niveau_id,
                        'statut' => 'niveau_non_assigne'
                    ]
                ], 400);
            }

            // Récupérer tous les certificats de l'apprenant
            $certificats = $apprenant->certificats()->with([
                'module.niveau',
                'module.formateur.utilisateur:id,nom,prenom'
            ])->get();

            // Récupérer les modules du niveau actuel
            $modulesNiveauActuel = Module::where('niveau_id', $apprenant->niveau_id)->get();

            // Calculer les statistiques
            $statistiques = [
                'total_modules_niveau' => $modulesNiveauActuel->count(),
                'modules_payes' => $apprenant->paiements()->where('statut', 'valide')->count(),
                'modules_inscrits' => $apprenant->inscriptions()->where('statut', 'valide')->count(),
                'certificats_obtenus' => $certificats->count(),
                'pourcentage_completion' => $modulesNiveauActuel->count() > 0 
                    ? round(($certificats->count() / $modulesNiveauActuel->count()) * 100, 2) 
                    : 0
            ];

            // Organiser les certificats par module
            $certificatsParModule = $certificats->map(function($certificat) {
                return [
                    'id' => $certificat->id,
                    'module' => [
                        'id' => $certificat->module ? $certificat->module->id : null,
                        'titre' => $certificat->module ? $certificat->module->titre : 'Module non trouvé',
                        'description' => $certificat->module ? $certificat->module->description : null,
                        'discipline' => $certificat->module ? $certificat->module->discipline : null,
                        'prix' => $certificat->module ? $certificat->module->prix : null,
                        'date_debut' => $certificat->module ? $certificat->module->date_debut : null,
                        'date_fin' => $certificat->module ? $certificat->module->date_fin : null,
                        'niveau' => $certificat->module && $certificat->module->niveau ? [
                            'id' => $certificat->module->niveau->id,
                            'nom' => $certificat->module->niveau->nom,
                            'description' => $certificat->module->niveau->description
                        ] : null,
                        'formateur' => $certificat->module && $certificat->module->formateur ? [
                            'id' => $certificat->module->formateur->id,
                            'nom' => $certificat->module->formateur->utilisateur ? $certificat->module->formateur->utilisateur->nom : 'Nom non défini',
                            'prenom' => $certificat->module->formateur->utilisateur ? $certificat->module->formateur->utilisateur->prenom : 'Prénom non défini'
                        ] : null
                    ],
                    'certificat' => [
                        'id' => $certificat->id,
                        'date_obtention' => $certificat->date_obtention,
                        'note' => $certificat->note,
                        'commentaire' => $certificat->commentaire,
                        'created_at' => $certificat->created_at,
                        'updated_at' => $certificat->updated_at
                    ]
                ];
            });

            // Modules sans certificat (modules payés mais pas encore de certificat)
            $modulesSansCertificat = $apprenant->paiements()
                ->where('statut', 'valide')
                ->whereHas('module', function($query) use ($apprenant) {
                    $query->where('niveau_id', $apprenant->niveau_id);
                })
                ->whereDoesntHave('module.certificats', function($query) use ($apprenant) {
                    $query->where('apprenant_id', $apprenant->id);
                })
                ->with(['module.niveau', 'module.formateur.utilisateur:id,nom,prenom'])
                ->get()
                ->map(function($paiement) {
                    return [
                        'module' => [
                            'id' => $paiement->module ? $paiement->module->id : null,
                            'titre' => $paiement->module ? $paiement->module->titre : 'Module non trouvé',
                            'description' => $paiement->module ? $paiement->module->description : null,
                            'discipline' => $paiement->module ? $paiement->module->discipline : null,
                            'prix' => $paiement->module ? $paiement->module->prix : null,
                            'niveau' => $paiement->module && $paiement->module->niveau ? [
                                'id' => $paiement->module->niveau->id,
                                'nom' => $paiement->module->niveau->nom
                            ] : null,
                            'formateur' => $paiement->module && $paiement->module->formateur ? [
                                'nom' => $paiement->module->formateur->utilisateur ? $paiement->module->formateur->utilisateur->nom : 'Nom non défini',
                                'prenom' => $paiement->module->formateur->utilisateur ? $paiement->module->formateur->utilisateur->prenom : 'Prénom non défini'
                            ] : null
                        ],
                        'paiement' => [
                            'id' => $paiement->id,
                            'date_paiement' => $paiement->date_paiement,
                            'methode' => $paiement->methode,
                            'montant' => $paiement->montant
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Vérification des certificats effectuée avec succès',
                'apprenant' => [
                    'id' => $apprenant->id,
                    'nom' => $apprenant->utilisateur->nom,
                    'prenom' => $apprenant->utilisateur->prenom,
                    'email' => $apprenant->utilisateur->email,
                    'telephone' => $apprenant->utilisateur->telephone,
                    'sexe' => $apprenant->utilisateur->sexe,
                                    'niveau_actuel' => [
                    'id' => $apprenant->niveau ? $apprenant->niveau->id : null,
                    'nom' => $apprenant->niveau ? $apprenant->niveau->nom : 'Non défini',
                    'description' => $apprenant->niveau ? $apprenant->niveau->description : 'Aucune description'
                ],
                    'connaissance_adis' => $apprenant->connaissance_adis,
                    'niveau_coran' => $apprenant->niveau_coran,
                    'niveau_arabe' => $apprenant->niveau_arabe
                ],
                'statistiques' => $statistiques,
                'certificats_obtenus' => [
                    'total' => $certificats->count(),
                    'details' => $certificatsParModule
                ],
                'modules_sans_certificat' => [
                    'total' => $modulesSansCertificat->count(),
                    'details' => $modulesSansCertificat
                ],
                'resume' => [
                    'total_modules_niveau' => $modulesNiveauActuel->count(),
                    'modules_payes' => $statistiques['modules_payes'],
                    'modules_inscrits' => $statistiques['modules_inscrits'],
                    'certificats_obtenus' => $statistiques['certificats_obtenus'],
                    'pourcentage_completion' => $statistiques['pourcentage_completion'],
                    'statut_general' => $statistiques['pourcentage_completion'] >= 100 ? 'Niveau complété' : 'Niveau en cours'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la vérification des certificats',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permet à l'admin de créer un formateur et de lui assigner un niveau
     */
    public function creerFormateur(Request $request)
    {
        try {
            // Vérifier que l'utilisateur est admin ou assistant
            $user = auth('api')->user();
            if (!$user) {
                return response()->json(['error' => 'Vous devez être connecté pour accéder à cette page.'], 401);
            }
            if (!in_array($user->type_compte, ['admin', 'assistant'])) {
                return response()->json(['error' => 'Accès réservé aux administrateurs et assistants.'], 403);
            }

            // Validation des données
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:utilisateurs,email',
                'telephone' => 'required|string|max:20',
                'sexe' => 'required|in:Homme,Femme',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|same:password',
                'niveau_id' => 'required|exists:niveaux,id',
                'ville' => 'nullable|string|max:255',
                'commune' => 'nullable|string|max:255',
                'quartier' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Données invalides',
                    'details' => $validator->errors()
                ], 400);
            }

            // Vérifier que le niveau existe et est actif
            $niveau = Niveau::find($request->niveau_id);
            if (!$niveau) {
                return response()->json([
                    'success' => false,
                    'error' => 'Le niveau spécifié n\'existe pas'
                ], 404);
            }

            if (!$niveau->actif) {
                return response()->json([
                    'success' => false,
                    'error' => 'Le niveau spécifié n\'est pas actif'
                ], 400);
            }

            // Vérifier que le niveau n'est pas déjà assigné à un autre formateur
            if ($niveau->formateur_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce niveau est déjà assigné à un autre formateur'
                ], 400);
            }

            // Créer l'utilisateur
            $utilisateur = Utilisateur::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'sexe' => $request->sexe,
                'mot_de_passe' => bcrypt($request->password),
                'type_compte' => 'formateur',
                'actif' => true,
                'email_verified_at' => now(), // Auto-validé par l'admin
                'infos_complementaires' => json_encode([
                    'créé_par' => $user->type_compte,
                    'créé_le' => now()->toISOString(),
                    'niveau_assigné_id' => $niveau->id
                ])
            ]);

            // Créer le profil formateur
            $formateur = Formateur::create([
                'utilisateur_id' => $utilisateur->id,
                'valide' => true, // Auto-validé par l'admin/assistant
                'validation_token' => null, // Pas besoin de validation
                'ville' => $request->ville ?? null,
                'commune' => $request->commune ?? null,
                'quartier' => $request->quartier ?? null
            ]);

            // Assigner le niveau au formateur via la relation directe
            $niveau->update(['formateur_id' => $formateur->id]);
            
            // Vérifier que la mise à jour a bien fonctionné
            $niveauMisAJour = Niveau::find($request->niveau_id);
            if (!$niveauMisAJour || $niveauMisAJour->formateur_id !== $formateur->id) {
                // Si la mise à jour a échoué, essayer une approche alternative
                \DB::table('niveaux')->where('id', $request->niveau_id)->update(['formateur_id' => $formateur->id]);
                $niveauMisAJour = Niveau::find($request->niveau_id);
            }
            
            // Utiliser le niveau mis à jour
            $niveau = $niveauMisAJour;

            // Récupérer le formateur avec ses relations
            $formateurCree = Formateur::with([
                'utilisateur:id,nom,prenom,email,telephone,sexe,actif,type_compte,created_at,infos_complementaires'
            ])->find($formateur->id);

            return response()->json([
                'success' => true,
                'message' => 'Formateur créé avec succès',
                'formateur' => [
                    'id' => $formateurCree->id,
                    'utilisateur' => [
                        'id' => $formateurCree->utilisateur->id,
                        'nom' => $formateurCree->utilisateur->nom,
                        'prenom' => $formateurCree->utilisateur->prenom,
                        'email' => $formateurCree->utilisateur->email,
                        'telephone' => $formateurCree->utilisateur->telephone,
                        'sexe' => $formateurCree->utilisateur->sexe,
                        'type_compte' => $formateurCree->utilisateur->type_compte,
                        'actif' => $formateurCree->utilisateur->actif,
                        'email_verified_at' => $formateurCree->utilisateur->email_verified_at,
                        'created_at' => $formateurCree->utilisateur->created_at
                    ],
                    'niveau_assigné' => [
                        'id' => $niveau->id,
                        'nom' => $niveau->nom,
                        'description' => $niveau->description,
                        'ordre' => $niveau->ordre,
                        'actif' => $niveau->actif,
                        'formateur_id' => $niveau->formateur_id
                    ],
                    'localisation' => [
                        'ville' => $formateurCree->ville,
                        'commune' => $formateurCree->commune,
                        'quartier' => $formateurCree->quartier
                    ],
                    'debug' => [
                        'niveau_id_demande' => $request->niveau_id,
                        'formateur_id_cree' => $formateur->id,
                        'niveau_formateur_id_apres_maj' => $niveau->formateur_id,
                        'verification_db' => \DB::table('niveaux')->where('id', $request->niveau_id)->value('formateur_id')
                    ],
                    'statut' => 'formateur_creé_et_validé',
                    'date_creation' => now()->toISOString()
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la création du formateur',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un formateur
     */
    public function supprimerFormateur($id)
    {
        try {
            // Vérifier que l'utilisateur est admin ou assistant
            $user = auth('api')->user();
            if (!$user) {
                return response()->json(['error' => 'Vous devez être connecté pour accéder à cette page.'], 401);
            }
            if (!in_array($user->type_compte, ['admin', 'assistant'])) {
                return response()->json(['error' => 'Accès réservé aux administrateurs et assistants.'], 403);
            }

            // Vérifier que le formateur existe
            $formateur = Formateur::with('utilisateur')->find($id);
            
            // Si le profil formateur n'existe pas, vérifier s'il y a un utilisateur orphelin
            if (!$formateur) {
                $utilisateurOrphelin = Utilisateur::where('id', $id)
                    ->where('type_compte', 'formateur')
                    ->first();
                
                if ($utilisateurOrphelin) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Profil formateur manquant',
                        'details' => 'L\'utilisateur existe mais le profil formateur est manquant',
                        'utilisateur_orphelin' => [
                            'id' => $utilisateurOrphelin->id,
                            'nom' => $utilisateurOrphelin->nom,
                            'prenom' => $utilisateurOrphelin->prenom,
                            'email' => $utilisateurOrphelin->email,
                            'type_compte' => $utilisateurOrphelin->type_compte,
                            'created_at' => $utilisateurOrphelin->created_at
                        ],
                        'solution' => 'Créer le profil formateur manquant ou supprimer l\'utilisateur orphelin'
                    ], 404);
                }
                
                return response()->json([
                    'success' => false,
                    'error' => 'Formateur non trouvé'
                ], 404);
            }

            // Vérifier que le formateur n'est pas assigné à des modules
            $modulesAssigned = \DB::table('modules')->where('formateur_id', $id)->count();
            if ($modulesAssigned > 0) {
                // Mettre à NULL le formateur_id dans les modules assignés
                \DB::table('modules')->where('formateur_id', $id)->update(['formateur_id' => null]);
            }

            // Vérifier que le formateur n'est pas assigné à des niveaux
            $niveauxAssigned = \DB::table('niveaux')->where('formateur_id', $id)->count();
            if ($niveauxAssigned > 0) {
                // Mettre à NULL le formateur_id dans les niveaux assignés
                \DB::table('niveaux')->where('formateur_id', $id)->update(['formateur_id' => null]);
            }

            // Récupérer l'ID de l'utilisateur avant suppression
            $utilisateurId = $formateur->utilisateur_id;

            // Supprimer le formateur
            $formateur->delete();

            // Supprimer l'utilisateur
            $utilisateur = Utilisateur::find($utilisateurId);
            if ($utilisateur) {
                $utilisateur->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Formateur supprimé avec succès',
                'formateur_supprime' => [
                    'id' => $id,
                    'nom' => $formateur->utilisateur->nom ?? 'N/A',
                    'prenom' => $formateur->utilisateur->prenom ?? 'N/A',
                    'email' => $formateur->utilisateur->email ?? 'N/A',
                    'utilisateur_id' => $utilisateurId
                ],
                'statut' => 'formateur_supprimé',
                'date_suppression' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la suppression du formateur',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer le profil formateur manquant pour un utilisateur existant
     */
    public function creerProfilFormateurManquant($id)
    {
        try {
            // Vérifier que l'utilisateur est admin ou assistant
            $user = auth('api')->user();
            if (!$user) {
                return response()->json(['error' => 'Vous devez être connecté pour accéder à cette page.'], 401);
            }
            if (!in_array($user->type_compte, ['admin', 'assistant'])) {
                return response()->json(['error' => 'Accès réservé aux administrateurs et assistants.'], 403);
            }

            // Vérifier que l'utilisateur existe et est de type formateur
            $utilisateur = Utilisateur::where('id', $id)
                ->where('type_compte', 'formateur')
                ->first();
            
            if (!$utilisateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Utilisateur formateur non trouvé'
                ], 404);
            }

            // Vérifier qu'il n'y a pas déjà un profil formateur
            $formateurExistant = Formateur::where('utilisateur_id', $id)->first();
            if ($formateurExistant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Le profil formateur existe déjà'
                ], 400);
            }

            // Créer le profil formateur
            $formateur = Formateur::create([
                'utilisateur_id' => $utilisateur->id,
                'valide' => true, // Auto-validé
                'validation_token' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profil formateur créé avec succès',
                'formateur' => [
                    'id' => $formateur->id,
                    'utilisateur' => [
                        'id' => $utilisateur->id,
                        'nom' => $utilisateur->nom,
                        'prenom' => $utilisateur->prenom,
                        'email' => $utilisateur->email,
                        'telephone' => $utilisateur->telephone,
                        'sexe' => $utilisateur->sexe,
                        'type_compte' => $utilisateur->type_compte,
                        'actif' => $utilisateur->actif,
                        'created_at' => $utilisateur->created_at
                    ],
                    'profil_formateur' => [
                        'id' => $formateur->id,
                        'valide' => $formateur->valide,
                        'created_at' => $formateur->created_at
                    ],
                    'statut' => 'profil_formateur_créé',
                    'date_creation' => now()->toISOString()
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la création du profil formateur',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un formateur par son ID utilisateur
     */
    public function supprimerFormateurParUtilisateur($utilisateurId)
    {
        try {
            // Vérifier que l'utilisateur est admin ou assistant
            $user = auth('api')->user();
            if (!$user) {
                return response()->json(['error' => 'Vous devez être connecté pour accéder à cette page.'], 401);
            }
            if (!in_array($user->type_compte, ['admin', 'assistant'])) {
                return response()->json(['error' => 'Accès réservé aux administrateurs et assistants.'], 403);
            }

            // Vérifier que l'utilisateur existe et est de type formateur
            $utilisateur = Utilisateur::where('id', $utilisateurId)
                ->where('type_compte', 'formateur')
                ->first();
            
            if (!$utilisateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Utilisateur formateur non trouvé'
                ], 404);
            }

            // Vérifier que le profil formateur existe
            $formateur = Formateur::where('utilisateur_id', $utilisateurId)->first();
            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil formateur non trouvé'
                ], 404);
            }

            // Vérifier que le formateur n'est pas assigné à des modules
            $modulesAssigned = \DB::table('modules')->where('formateur_id', $formateur->id)->count();
            if ($modulesAssigned > 0) {
                // Mettre à NULL le formateur_id dans les modules assignés
                \DB::table('modules')->where('formateur_id', $formateur->id)->update(['formateur_id' => null]);
            }

            // Vérifier que le formateur n'est pas assigné à des niveaux
            $niveauxAssigned = \DB::table('niveaux')->where('formateur_id', $formateur->id)->count();
            if ($niveauxAssigned > 0) {
                // Mettre à NULL le formateur_id dans les niveaux assignés
                \DB::table('niveaux')->where('formateur_id', $formateur->id)->update(['formateur_id' => null]);
            }

            // Récupérer l'ID du formateur avant suppression
            $formateurId = $formateur->id;

            // Supprimer le formateur
            $formateur->delete();

            // Supprimer l'utilisateur
            $utilisateur->delete();

            return response()->json([
                'success' => true,
                'message' => 'Formateur supprimé avec succès',
                'formateur_supprime' => [
                    'formateur_id' => $formateurId,
                    'utilisateur_id' => $utilisateurId,
                    'nom' => $utilisateur->nom,
                    'prenom' => $utilisateur->prenom,
                    'email' => $utilisateur->email
                ],
                'statut' => 'formateur_supprimé',
                'date_suppression' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la suppression du formateur',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un apprenant par son ID utilisateur
     */
    public function supprimerApprenant($utilisateurId)
    {
        try {
            // Vérifier que l'utilisateur est admin ou assistant
            $user = auth('api')->user();
            if (!$user) {
                return response()->json(['error' => 'Vous devez être connecté pour accéder à cette page.'], 401);
            }
            if (!in_array($user->type_compte, ['admin', 'assistant'])) {
                return response()->json(['error' => 'Accès réservé aux administrateurs et assistants.'], 403);
            }

            // Vérifier que l'utilisateur existe et est de type apprenant
            $utilisateur = Utilisateur::where('id', $utilisateurId)
                ->where('type_compte', 'apprenant')
                ->first();
            
            if (!$utilisateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Utilisateur apprenant non trouvé'
                ], 404);
            }

            // Vérifier que le profil apprenant existe
            $apprenant = Apprenant::where('utilisateur_id', $utilisateurId)->first();
            if (!$apprenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil apprenant non trouvé'
                ], 404);
            }

            // Vérifier que l'apprenant n'est pas inscrit à des modules
            $inscriptionsCount = \DB::table('inscriptions')->where('apprenant_id', $apprenant->id)->count();
            if ($inscriptionsCount > 0) {
                // Mettre à NULL l'apprenant_id dans les inscriptions
                \DB::table('inscriptions')->where('apprenant_id', $apprenant->id)->update(['apprenant_id' => null]);
            }

            // Vérifier que l'apprenant n'a pas de paiements
            $paiementsCount = \DB::table('paiements')->where('apprenant_id', $apprenant->id)->count();
            if ($paiementsCount > 0) {
                // Mettre à NULL l'apprenant_id dans les paiements
                \DB::table('paiements')->where('apprenant_id', $apprenant->id)->update(['apprenant_id' => null]);
            }

            // Vérifier que l'apprenant n'a pas de certificats
            $certificatsCount = \DB::table('certificats')->where('apprenant_id', $apprenant->id)->count();
            if ($certificatsCount > 0) {
                // Supprimer les certificats de l'apprenant
                \DB::table('certificats')->where('apprenant_id', $apprenant->id)->delete();
            }

            // Supprimer l'apprenant
            $apprenant->delete();

            // Supprimer l'utilisateur
            $utilisateur = Utilisateur::find($utilisateurId);
            if ($utilisateur) {
                $utilisateur->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Apprenant supprimé avec succès',
                'apprenant_supprime' => [
                    'apprenant_id' => $apprenant->id,
                    'utilisateur_id' => $utilisateurId,
                    'nom' => $utilisateur->nom,
                    'prenom' => $utilisateur->prenom,
                    'email' => $utilisateur->email
                ],
                'statistiques_suppression' => [
                    'inscriptions_annulees' => $inscriptionsCount,
                    'paiements_annules' => $paiementsCount,
                    'certificats_supprimes' => $certificatsCount
                ],
                'statut' => 'apprenant_supprimé',
                'date_suppression' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la suppression de l\'apprenant',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un assistant par son ID utilisateur
     */
    public function supprimerAssistant($utilisateurId)
    {
        try {
            // Vérifier que l'utilisateur est admin
            $user = auth('api')->user();
            if (!$user) {
                return response()->json(['error' => 'Vous devez être connecté pour accéder à cette page.'], 401);
            }
            if ($user->type_compte !== 'admin') {
                return response()->json(['error' => 'Accès réservé aux administrateurs uniquement.'], 403);
            }

            // Vérifier que l'assistant existe
            $assistant = Utilisateur::where('id', $utilisateurId)
                ->where('type_compte', 'assistant')
                ->first();
            
            if (!$assistant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Assistant non trouvé'
                ], 404);
            }

            // Vérifier que l'assistant n'est pas l'utilisateur connecté
            if ($assistant->id === $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Impossible de supprimer votre propre compte'
                ], 400);
            }

            // Supprimer l'assistant
            $assistant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Assistant supprimé avec succès',
                'assistant_supprime' => [
                    'utilisateur_id' => $utilisateurId,
                    'nom' => $assistant->nom,
                    'prenom' => $assistant->prenom,
                    'email' => $assistant->email,
                    'type_compte' => $assistant->type_compte
                ],
                'statut' => 'assistant_supprimé',
                'date_suppression' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la suppression de l\'assistant',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assigner un formateur à un niveau
     */
    public function assignerFormateurAuNiveau(Request $request, $niveauId)
    {
        try {
            // Validation des données
            $validator = Validator::make($request->all(), [
                'formateur_id' => 'required|integer|exists:formateurs,id',
                'commentaire' => 'nullable|string|max:500',
                'raison_assignation' => 'nullable|string|max:500',
                'remplacer_existant' => 'nullable|boolean' // Nouvelle option
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Données invalides',
                    'details' => $validator->errors()
                ], 422);
            }

            // Vérifier que le niveau existe
            $niveau = Niveau::findOrFail($niveauId);
            
            // Vérifier que le formateur existe (avec ou sans validation)
            $formateur = Formateur::find($request->formateur_id);
            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Formateur non trouvé',
                    'details' => 'Aucun formateur trouvé avec l\'ID ' . $request->formateur_id
                ], 404);
            }

            // Avertissement si le formateur n'est pas validé
            $avertissement = null;
            if (!$formateur->valide) {
                $avertissement = 'Attention : Ce formateur n\'est pas encore validé. L\'assignation peut être temporaire.';
            }

            // Vérifier si le niveau a déjà un formateur
            if ($niveau->formateur_id) {
                // Si on veut remplacer le formateur existant
                if ($request->boolean('remplacer_existant')) {
                    $ancienFormateurId = $niveau->formateur_id;
                    $ancienFormateur = Formateur::find($ancienFormateurId);
                    
                    // Vérifier que le nouveau formateur est différent de l'ancien
                    if ($niveau->formateur_id == $request->formateur_id) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Le nouveau formateur est identique à l\'ancien',
                            'details' => 'Aucun changement nécessaire'
                        ], 400);
                    }
                    
                    // Remplacer le formateur
                    $niveau->update([
                        'formateur_id' => $formateur->id
                    ]);
                    
                    // Récupérer le niveau mis à jour
                    $niveau->refresh();
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Formateur remplacé avec succès',
                        'avertissement' => $avertissement,
                        'ancien_formateur' => [
                            'id' => $ancienFormateurId,
                            'utilisateur_id' => $ancienFormateur ? $ancienFormateur->utilisateur_id : null,
                            'specialite' => $ancienFormateur ? $ancienFormateur->specialite : null
                        ],
                        'nouveau_formateur' => [
                            'id' => $formateur->id,
                            'utilisateur_id' => $formateur->utilisateur_id,
                            'specialite' => $formateur->specialite,
                            'valide' => $formateur->valide
                        ],
                        'niveau' => [
                            'id' => $niveau->id,
                            'nom' => $niveau->nom,
                            'description' => $niveau->description,
                            'ordre' => $niveau->ordre,
                            'actif' => $niveau->actif,
                            'formateur_id' => $niveau->formateur_id,
                            'lien_meet' => $niveau->lien_meet,
                            'session_id' => $niveau->session_id,
                            'date_modification' => $niveau->updated_at
                        ],
                        'commentaire' => $request->commentaire,
                        'raison_assignation' => $request->raison_assignation
                    ], 200);
                }
                
                // Si on ne veut pas remplacer, retourner l'erreur
                return response()->json([
                    'success' => false,
                    'error' => 'Ce niveau a déjà un formateur assigné',
                    'details' => [
                        'formateur_actuel' => [
                            'id' => $niveau->formateur_id,
                            'message' => 'Un formateur est déjà assigné à ce niveau'
                        ],
                        'date_assignation' => $niveau->updated_at,
                        'solution' => 'Utilisez remplacer_existant: true pour changer de formateur'
                    ]
                ], 409);
            }

            // Vérifier la charge de travail du formateur (nombre de niveaux assignés)
            $niveauxFormateur = Niveau::where('formateur_id', $formateur->id)->count();
            if ($niveauxFormateur >= 3) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce formateur a déjà trop de niveaux assignés',
                    'details' => [
                        'niveaux_actuels' => $niveauxFormateur,
                        'limite_recommandee' => 3
                    ]
                ], 400);
            }

            // Assigner le formateur au niveau
            $niveau->update([
                'formateur_id' => $formateur->id
            ]);

            // Récupérer le niveau mis à jour
            $niveau->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Formateur assigné au niveau avec succès',
                'avertissement' => $avertissement,
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre,
                    'actif' => $niveau->actif,
                    'formateur_id' => $niveau->formateur_id,
                    'lien_meet' => $niveau->lien_meet,
                    'session_id' => $niveau->session_id,
                    'formateur' => [
                        'id' => $formateur->id,
                        'utilisateur_id' => $formateur->utilisateur_id,
                        'specialite' => $formateur->specialite,
                        'valide' => $formateur->valide,
                        'niveaux_assignes' => $niveauxFormateur + 1
                    ],
                    'date_assignation' => $niveau->updated_at
                ],
                'commentaire' => $request->commentaire,
                'raison_assignation' => $request->raison_assignation
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'assignation du formateur au niveau',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Désassigner un formateur d'un niveau
     */
    public function desassignerFormateurDuNiveau(Request $request, $niveauId)
    {
        try {
            // Vérifier que le niveau existe
            $niveau = Niveau::findOrFail($niveauId);
            
            // Vérifier si le niveau a un formateur assigné
            if (!$niveau->formateur_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce niveau n\'a pas de formateur assigné'
                ], 404);
            }

            // Récupérer les informations du formateur avant désassignation
            $formateur = Formateur::find($niveau->formateur_id);
            $formateurInfo = [
                'id' => $formateur->id,
                'utilisateur_id' => $formateur->utilisateur_id,
                'specialite' => $formateur->specialite
            ];

            // Désassigner le formateur du niveau
            $niveau->update([
                'formateur_id' => null
            ]);

            // Récupérer le niveau mis à jour
            $niveau->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Formateur désassigné du niveau avec succès',
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre,
                    'actif' => $niveau->actif,
                    'formateur_id' => null,
                    'lien_meet' => $niveau->lien_meet,
                    'session_id' => $niveau->session_id
                ],
                'formateur_desassigne' => $formateurInfo,
                'date_desassignation' => $niveau->updated_at
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la désassignation du formateur du niveau',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Donner le profil assistant à un apprenant (admin et assistant)
     * @param Request $request
     * @param int $apprenantId L'ID de l'utilisateur (pas l'ID de l'apprenant)
     */
    public function donnerProfilAssistantAApprenant(Request $request, $apprenantId)
    {
        // Vérifier si l'utilisateur est admin ou assistant
        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Non authentifié'
            ], 401);
        }
        
        if ($user->type_compte !== 'admin' && $user->type_compte !== 'assistant') {
            return response()->json([
                'success' => false,
                'error' => 'Accès réservé aux administrateurs et assistants'
            ], 403);
        }

        try {
            // Validation des données
            $request->validate([
                'bio' => 'nullable|string|max:500',
                'actif' => 'boolean'
            ]);

            // Vérifier que l'apprenant existe en cherchant par utilisateur_id
            $apprenant = Apprenant::where('utilisateur_id', $apprenantId)->with('utilisateur')->first();
            if (!$apprenant) {
                // Vérifier si l'utilisateur existe et a le type_compte "apprenant"
                $utilisateur = Utilisateur::where('id', $apprenantId)
                    ->where('type_compte', 'apprenant')
                    ->first();
                
                if (!$utilisateur) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Utilisateur apprenant non trouvé'
                    ], 404);
                }

                // Créer automatiquement le profil apprenant manquant
                $apprenant = Apprenant::create([
                    'utilisateur_id' => $utilisateur->id,
                    'niveau_id' => 1, // Niveau par défaut
                    'connaissance_adis' => 'Débutant',
                    'formation_adis' => false,
                    'formation_autre' => false,
                    'niveau_coranique' => 'Débutant',
                    'niveau_arabe' => 'Débutant',
                    'tomes_medine' => 1,
                    'tomes_etudies' => json_encode([1]),
                    'disciplines' => json_encode(['Coran', 'Langue arabe']),
                    'attentes' => json_encode(['Apprendre les bases']),
                    'formateur_domicile' => false,
                    'categorie' => 'Etudiant'
                ]);

                // Recharger avec la relation utilisateur
                $apprenant->load('utilisateur');

                \Log::info("Profil apprenant manquant créé automatiquement pour l'utilisateur {$utilisateur->email}");
            }

            // Vérifier que l'utilisateur n'a pas déjà un profil assistant
            $assistantExistant = Assistant::where('utilisateur_id', $apprenant->utilisateur_id)->first();

            if ($assistantExistant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cet apprenant a déjà un profil assistant'
                ], 400);
            }

            // Créer le profil assistant
            $assistant = Assistant::create([
                'utilisateur_id' => $apprenant->utilisateur_id,
                'bio' => $request->bio ?? 'Assistant pédagogique',
                'actif' => $request->actif ?? true,
                'formateur_id' => null
            ]);

            // Mettre à jour le type de compte de l'utilisateur
            $utilisateur = $apprenant->utilisateur;
            $utilisateur->update([
                'type_compte' => 'assistant'
            ]);

            // Log de l'action
            \Log::info("Admin a donné un profil assistant à l'apprenant {$utilisateur->nom} {$utilisateur->prenom}");

            return response()->json([
                'success' => true,
                'message' => 'Profil assistant créé avec succès pour cet apprenant',
                'apprenant' => [
                    'id' => $apprenant->id,
                    'niveau_id' => $apprenant->niveau_id,
                    'connaissance_adis' => $apprenant->connaissance_adis,
                    'niveau_coranique' => $apprenant->niveau_coranique,
                    'niveau_arabe' => $apprenant->niveau_arabe
                ],
                'utilisateur' => [
                    'id' => $utilisateur->id,
                    'nom' => $utilisateur->nom,
                    'prenom' => $utilisateur->prenom,
                    'email' => $utilisateur->email,
                    'type_compte' => $utilisateur->type_compte,
                    'actif' => $utilisateur->actif
                ],
                'profil_assistant' => [
                    'id' => $assistant->id,
                    'bio' => $assistant->bio,
                    'actif' => $assistant->actif,
                    'formateur_id' => $assistant->formateur_id,
                    'created_at' => $assistant->created_at
                ],
                'created_by' => auth('api')->user()->email,
                'created_at' => now()->format('Y-m-d H:i:s')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la création du profil assistant',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
