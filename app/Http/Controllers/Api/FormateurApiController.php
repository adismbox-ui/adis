<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Formateur;
use App\Models\Document;
use App\Models\Module;
use App\Models\Niveau;

class FormateurApiController extends Controller
{
    public function index()
    {
        $formateurs = Formateur::with('utilisateur')->where('valide', true)->get();

        // On mappe pour ajouter nom/prenom à la racine
        $formateurs = $formateurs->map(function ($f) {
            return [
                'id' => $f->id,
                'utilisateur_id' => $f->utilisateur_id,
                // ... autres champs du formateur ...
                'nom' => $f->utilisateur ? $f->utilisateur->nom : null,
                'prenom' => $f->utilisateur ? $f->utilisateur->prenom : null,
                // ... tu peux ajouter d'autres champs si besoin ...
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formateurs
        ]);
    }

    public function create()
    {
        // Pour une API, généralement inutile, mais on peut retourner les champs attendus
        return response()->json(['fields' => [
            'utilisateur_id', 'connaissance_adis', 'formation_adis', 'formation_autre', 'niveau_coran', 'niveau_arabe', 'niveau_francais', 'diplome_religieux', 'diplome_general', 'fichier_diplome_religieux', 'fichier_diplome_general', 'ville', 'commune', 'quartier'
        ]], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'utilisateur_id' => 'required|integer|exists:utilisateurs,id',
            'connaissance_adis' => 'nullable|string',
            'formation_adis' => 'nullable|boolean',
            'formation_autre' => 'nullable|boolean',
            'niveau_coran' => 'nullable|string',
            'niveau_arabe' => 'nullable|string',
            'niveau_francais' => 'nullable|string',
            'diplome_religieux' => 'nullable|string',
            'diplome_general' => 'nullable|string',
            'fichier_diplome_religieux' => 'nullable|string',
            'fichier_diplome_general' => 'nullable|string',
            'ville' => 'nullable|string',
            'commune' => 'nullable|string',
            'quartier' => 'nullable|string',
        ]);
        $formateur = Formateur::create($data);
        return response()->json(['formateur' => $formateur, 'message' => 'Formateur créé avec succès'], 201);
    }

    public function show(Formateur $formateur)
    {
        return response()->json(['formateur' => $formateur], 200);
    }

    public function edit(Formateur $formateur)
    {
        return response()->json(['formateur' => $formateur], 200);
    }

    public function update(Request $request, Formateur $formateur)
    {
        $data = $request->validate([
            // Ajoute ici les règles de validation pour chaque champ
        ]);
        $formateur->update($data);
        return response()->json(['formateur' => $formateur, 'message' => 'Formateur mis à jour avec succès'], 200);
    }

    public function destroy(Formateur $formateur)
    {
        $formateur->delete();
        return response()->json(null, 204);
    }

    public function dashboard()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        $formateur = $user->formateur;
        $modules = $formateur ? $formateur->modules()->with(['documents', 'inscriptions.apprenant.utilisateur', 'questionnaires.questions'])->get() : collect();
        foreach ($modules as $module) {
            foreach ($module->inscriptions as $inscription) {
                $apprenant = $inscription->apprenant;
                $totalPoints = 0;
                $questions = $module->questionnaires->flatMap->questions;
                foreach ($questions as $question) {
                    $bonne = $question->bonne_reponse;
                    $reponse = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                        ->where('question_id', $question->id)
                        ->value('reponse');
                    if ($reponse === $bonne) {
                        $totalPoints += $question->points ?? 1;
                    }
                }
                $inscription->points = $totalPoints;
            }
        }
        return response()->json([
            'user' => $user,
            'formateur' => $formateur,
            'modules' => $modules
        ], 200);
    }

    public function documentFormateur()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }
        $formateur = $user->formateur;
        $modules = $formateur ? $formateur->modules()->with('documents')->get() : collect();
        foreach ($modules as $module) {
            if ($module->audio && !str_starts_with($module->audio, 'audios/')) {
                $module->audio = 'audios/' . $module->audio;
            }
        }
        return response()->json(['modules' => $modules], 200);
    }

    public function autoLogin($token)
    {
        $formateur = Formateur::where('validation_token', $token)->where('valide', true)->with('utilisateur')->first();
        if (!$formateur || !$formateur->utilisateur) {
            return response()->json(['error' => 'Lien invalide ou expiré.'], 401);
        }
        auth()->login($formateur->utilisateur);
        $formateur->validation_token = null;
        $formateur->save();
        return response()->json(['message' => 'Bienvenue, votre compte a été activé !'], 200);
    }

    public function listeFormateursValides()
    {
        $user = auth()->user();
        // Optionnel : vérifier que c'est un admin
        // if (!$user || $user->type_compte !== 'admin') {
        //     return response()->json(['error' => 'Non autorisé'], 403);
        // }

        // Utiliser la table utilisateurs avec type_compte = 'formateur'
        $formateurs = \App\Models\Utilisateur::where('type_compte', 'formateur')
            ->where('actif', true)
            ->get();

        $result = $formateurs->map(function($formateur) {
            return [
                'id' => $formateur->id,
                'nom' => $formateur->nom,
                'prenom' => $formateur->prenom,
                'email' => $formateur->email,
                'telephone' => $formateur->telephone,
                'sexe' => $formateur->sexe,
                'categorie' => $formateur->categorie,
                'type_compte' => $formateur->type_compte,
                'actif' => $formateur->actif,
                'created_at' => $formateur->created_at
            ];
        });

        return response()->json(['formateurs' => $result], 200);
    }
    // Récupère tous les modules du formateur 
    public function mesModules()
    {
        $user = auth()->user();
        if (!$user || !$user->formateur) {
            return response()->json(['error' => 'Aucun formateur connecté.'], 401);
        }

        // Récupérer les niveaux auxquels le formateur est assigné
        $niveaux = \App\Models\Niveau::where('formateur_id', $user->formateur->id)
            ->with(['modules', 'sessionFormation'])
            ->get();

        // Récupérer tous les modules de ces niveaux
        $modules = collect();
        foreach ($niveaux as $niveau) {
            $modules = $modules->merge($niveau->modules);
        }

        // Formater la réponse avec les informations des niveaux
        $modulesFormates = $modules->map(function ($module) use ($niveaux) {
            $niveau = $niveaux->where('id', $module->niveau_id)->first();
            
            return [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'discipline' => $module->discipline,
                'date_debut' => $module->date_debut,
                'date_fin' => $module->date_fin,
                'horaire' => $module->horaire,
                'prix' => $module->prix,
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre,
                    'actif' => $niveau->actif,
                    'lien_meet' => $niveau->lien_meet,
                    'session_formation' => $niveau->sessionFormation ? [
                        'id' => $niveau->sessionFormation->id,
                        'nom' => $niveau->sessionFormation->nom,
                        'date_debut' => $niveau->sessionFormation->date_debut,
                        'date_fin' => $niveau->sessionFormation->date_fin,
                    ] : null,
                ],
                'created_at' => $module->created_at,
                'updated_at' => $module->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'total_modules' => $modulesFormates->count(),
            'total_niveaux' => $niveaux->count(),
            'modules' => $modulesFormates,
            'niveaux' => $niveaux->map(function ($niveau) {
                return [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre,
                    'actif' => $niveau->actif,
                    'lien_meet' => $niveau->lien_meet,
                    'session_formation' => $niveau->sessionFormation ? [
                        'id' => $niveau->sessionFormation->id,
                        'nom' => $niveau->sessionFormation->nom,
                        'date_debut' => $niveau->sessionFormation->date_debut,
                        'date_fin' => $niveau->sessionFormation->date_fin,
                    ] : null,
                    'modules_count' => $niveau->modules->count()
                ];
            })
        ], 200);
    }

    // Récupère tous les niveaux des modules du formateur, sans doublons
    public function mesNiveaux()
    {
        $user = auth()->user();
        if (!$user || !$user->formateur) {
            return response()->json(['error' => 'Aucun formateur connecté.'], 401);
        }

        // Récupérer directement les niveaux assignés au formateur
        $niveaux = \App\Models\Niveau::where('formateur_id', $user->formateur->id)
            ->with(['modules', 'sessionFormation'])
            ->orderBy('ordre')
            ->get();

        // Formater la réponse
        $niveauxFormates = $niveaux->map(function ($niveau) {
            return [
                'id' => $niveau->id,
                'nom' => $niveau->nom,
                'description' => $niveau->description,
                'ordre' => $niveau->ordre,
                'actif' => $niveau->actif,
                'lien_meet' => $niveau->lien_meet,
                'session_formation' => $niveau->sessionFormation ? [
                    'id' => $niveau->sessionFormation->id,
                    'nom' => $niveau->sessionFormation->nom,
                    'date_debut' => $niveau->sessionFormation->date_debut,
                    'date_fin' => $niveau->sessionFormation->date_fin,
                ] : null,
                'modules_count' => $niveau->modules->count(),
                'modules' => $niveau->modules->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'description' => $module->description,
                        'discipline' => $module->discipline,
                        'date_debut' => $module->date_debut,
                        'date_fin' => $module->date_fin,
                        'horaire' => $module->horaire,
                        'prix' => $module->prix,
                        'created_at' => $module->created_at,
                        'updated_at' => $module->updated_at
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'total_niveaux' => $niveauxFormates->count(),
            'niveaux' => $niveauxFormates
        ], 200);
    }

    public function mesModulesParNiveau(\App\Models\Niveau $niveau)
    {
        $user = auth()->user();
        if (!$user || !$user->formateur) {
            return response()->json(['error' => 'Aucun formateur connecté.'], 401);
        }

        // Vérifier que le formateur est bien assigné à ce niveau
        if ($niveau->formateur_id !== $user->formateur->id) {
            return response()->json([
                'error' => 'Vous n\'êtes pas assigné à ce niveau.',
                'niveau_id' => $niveau->id,
                'niveau_nom' => $niveau->nom
            ], 403);
        }

        // Récupérer les modules de ce niveau
        $modules = $niveau->modules()->get();

        // Formater la réponse
        $modulesFormates = $modules->map(function ($module) use ($niveau) {
            return [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'discipline' => $module->discipline,
                'date_debut' => $module->date_debut,
                'date_fin' => $module->date_fin,
                'horaire' => $module->horaire,
                'prix' => $module->prix,
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre,
                    'actif' => $niveau->actif,
                    'lien_meet' => $niveau->lien_meet,
                    'session_formation' => $niveau->sessionFormation ? [
                        'id' => $niveau->sessionFormation->id,
                        'nom' => $niveau->sessionFormation->nom,
                        'date_debut' => $niveau->sessionFormation->date_debut,
                        'date_fin' => $niveau->sessionFormation->date_fin,
                    ] : null,
                ],
                'created_at' => $module->created_at,
                'updated_at' => $module->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'niveau' => [
                'id' => $niveau->id,
                'nom' => $niveau->nom,
                'description' => $niveau->description,
                'ordre' => $niveau->ordre,
                'actif' => $niveau->actif,
                'lien_meet' => $niveau->lien_meet,
                'session_formation' => $niveau->sessionFormation ? [
                    'id' => $niveau->sessionFormation->id,
                    'nom' => $niveau->sessionFormation->nom,
                    'date_debut' => $niveau->sessionFormation->date_debut,
                    'date_fin' => $niveau->sessionFormation->date_fin,
                ] : null,
            ],
            'total_modules' => $modulesFormates->count(),
            'modules' => $modulesFormates
        ], 200);
    }

    /**
     * Récupère les statistiques du formateur connecté
     */
    public function getStatistiquesFormateur()
    {
        $user = auth()->user();
        
        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return response()->json(['error' => 'Non authentifié.'], 401);
        }

        // Vérifier si l'utilisateur est un formateur
        if (!$user->formateur) {
            return response()->json(['error' => 'Accès réservé aux formateurs.'], 403);
        }

        try {
            $formateur = $user->formateur;
            
            // Récupérer les niveaux assignés au formateur
            $niveauxAssignes = \App\Models\Niveau::where('formateur_id', $formateur->id)->get();
            
            // Récupérer tous les modules des niveaux assignés
            $modules = \App\Models\Module::whereIn('niveau_id', $niveauxAssignes->pluck('id'))->get();
            
            // Récupérer les apprenants des niveaux assignés
            $apprenants = \App\Models\Apprenant::whereIn('niveau_id', $niveauxAssignes->pluck('id'))->get();
            
            // Récupérer les paiements pour les modules des niveaux assignés
            $paiements = \App\Models\Paiement::whereIn('module_id', $modules->pluck('id'))
                ->where('statut', 'valide')
                ->get();
            
            // Récupérer les documents créés
            $documents = \App\Models\Document::whereIn('niveau_id', $niveauxAssignes->pluck('id'))
                ->orWhereIn('module_id', $modules->pluck('id'))
                ->get();

            // Compter le nombre total de modules
            $totalModules = $modules->count();
            
            // Compter les modules avec support (fichier PDF)
            $modulesAvecSupport = $modules->whereNotNull('support')->count();
            
            // Compter les modules avec audio
            $modulesAvecAudio = $modules->whereNotNull('audio')->count();
            
            // Compter le nombre total d'apprenants inscrits dans tous les modules
            $totalApprenantsInscrits = $apprenants->count();

            return response()->json([
                'statistiques' => [
                    'total_modules' => $totalModules,
                    'modules_avec_support' => $modulesAvecSupport,
                    'modules_avec_audio' => $modulesAvecAudio,
                    'total_apprenants_inscrits' => $totalApprenantsInscrits,
                    'niveaux_assignes' => $niveauxAssignes->count(),
                    'total_paiements' => $paiements->count(),
                    'montant_total' => $paiements->sum('montant'),
                    'documents_crees' => $documents->count()
                ],
                'debug' => [
                    'formateur_id' => $formateur->id,
                    'niveaux_assignes_ids' => $niveauxAssignes->pluck('id'),
                    'modules_ids' => $modules->pluck('id'),
                    'apprenants_ids' => $apprenants->pluck('id')
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors du calcul des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les informations du formateur connecté
     */
    public function getProfile()
    {
        $user = auth()->user();
        
        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return response()->json(['error' => 'Non authentifié.'], 401);
        }

        // Vérifier si l'utilisateur est un formateur
        if (!$user->formateur) {
            return response()->json(['error' => 'Accès réservé aux formateurs.'], 403);
        }

        try {
            $formateur = $user->formateur;
            
            // Charger les relations nécessaires
            $formateur->load('utilisateur');
            
            return response()->json([
                'formateur' => [
                    'id' => $formateur->id,
                    'valide' => $formateur->valide,
                    'specialite' => $formateur->specialite,
                    'experience' => $formateur->experience,
                    'niveau_arabe' => $formateur->niveau_arabe,
                    'niveau_francais' => $formateur->niveau_francais,
                    'diplome_religieux' => $formateur->diplome_religieux,
                    'diplome_general' => $formateur->diplome_general,
                    'fichier_diplome_religieux' => $formateur->fichier_diplome_religieux,
                    'fichier_diplome_general' => $formateur->fichier_diplome_general,
                    'ville' => $formateur->ville,
                    'commune' => $formateur->commune,
                    'quartier' => $formateur->quartier,
                    'utilisateur' => [
                        'id' => $formateur->utilisateur->id,
                        'nom' => $formateur->utilisateur->nom,
                        'prenom' => $formateur->utilisateur->prenom,
                        'email' => $formateur->utilisateur->email,
                        'telephone' => $formateur->utilisateur->telephone,
                        'sexe' => $formateur->utilisateur->sexe,
                        'categorie' => $formateur->utilisateur->categorie,
                        'type_compte' => $formateur->utilisateur->type_compte,
                        'actif' => $formateur->utilisateur->actif,
                        'email_verified_at' => $formateur->utilisateur->email_verified_at,
                        'created_at' => $formateur->utilisateur->created_at,
                        'updated_at' => $formateur->utilisateur->updated_at,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération du profil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour les informations du formateur connecté
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return response()->json(['error' => 'Non authentifié.'], 401);
        }

        // Vérifier si l'utilisateur est un formateur
        if (!$user->formateur) {
            return response()->json(['error' => 'Accès réservé aux formateurs.'], 403);
        }

        try {
            $formateur = $user->formateur;
            
            // Validation des données du formateur
            $formateurData = $request->validate([
                'specialite' => 'nullable|string|max:255',
                'experience' => 'nullable|string|max:255',
                'niveau_arabe' => 'nullable|string|max:255',
                'niveau_francais' => 'nullable|string|max:255',
                'diplome_religieux' => 'nullable|string|max:255',
                'diplome_general' => 'nullable|string|max:255',
                'ville' => 'nullable|string|max:255',
                'commune' => 'nullable|string|max:255',
                'quartier' => 'nullable|string|max:255',
            ]);

            // Validation des données de l'utilisateur
            $utilisateurData = $request->validate([
                'nom' => 'nullable|string|max:255',
                'prenom' => 'nullable|string|max:255',
                'telephone' => 'nullable|string|max:20',
                'sexe' => 'nullable|in:Homme,Femme',
                'categorie' => 'nullable|in:Enfant,Etudiant,Professionnel,Enseignant',
            ]);

            // Mettre à jour le formateur
            if (!empty($formateurData)) {
                $formateur->update($formateurData);
            }

            // Mettre à jour l'utilisateur
            if (!empty($utilisateurData)) {
                $formateur->utilisateur->update($utilisateurData);
            }

            // Recharger les relations
            $formateur->load('utilisateur');

            return response()->json([
                'message' => 'Profil mis à jour avec succès',
                'formateur' => [
                    'id' => $formateur->id,
                    'valide' => $formateur->valide,
                    'specialite' => $formateur->specialite,
                    'experience' => $formateur->experience,
                    'niveau_arabe' => $formateur->niveau_arabe,
                    'niveau_francais' => $formateur->niveau_francais,
                    'diplome_religieux' => $formateur->diplome_religieux,
                    'diplome_general' => $formateur->diplome_general,
                    'fichier_diplome_religieux' => $formateur->fichier_diplome_religieux,
                    'fichier_diplome_general' => $formateur->fichier_diplome_general,
                    'ville' => $formateur->ville,
                    'commune' => $formateur->commune,
                    'quartier' => $formateur->quartier,
                    'utilisateur' => [
                        'id' => $formateur->utilisateur->id,
                        'nom' => $formateur->utilisateur->nom,
                        'prenom' => $formateur->utilisateur->prenom,
                        'email' => $formateur->utilisateur->email,
                        'telephone' => $formateur->utilisateur->telephone,
                        'sexe' => $formateur->utilisateur->sexe,
                        'categorie' => $formateur->utilisateur->categorie,
                        'type_compte' => $formateur->utilisateur->type_compte,
                        'actif' => $formateur->utilisateur->actif,
                        'email_verified_at' => $formateur->utilisateur->email_verified_at,
                        'created_at' => $formateur->utilisateur->created_at,
                        'updated_at' => $formateur->utilisateur->updated_at,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la mise à jour du profil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour le mot de passe du formateur connecté
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe mis à jour avec succès'
        ], 200);
    }

    /**
     * Récupérer les demandes de cours à domicile assignées au formateur connecté
     */
    public function mesDemandesCoursDomicile()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $formateur = $user->formateur;
        if (!$formateur) {
            return response()->json(['error' => 'Aucun formateur trouvé pour cet utilisateur'], 404);
        }

        // Récupérer toutes les demandes assignées au formateur
        $demandes = \App\Models\DemandeCoursMaison::where('formateur_id', $formateur->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get();

        $demandesFormatees = $demandes->map(function ($demande) {
            return [
                'id' => $demande->id,
                'module' => $demande->module,
                'nombre_enfants' => $demande->nombre_enfants,
                'ville' => $demande->ville,
                'commune' => $demande->commune,
                'quartier' => $demande->quartier,
                'numero' => $demande->numero,
                'message' => $demande->message,
                'statut' => $demande->statut,
                'created_at' => $demande->created_at,
                'updated_at' => $demande->updated_at,
                'client' => $demande->user ? [
                    'id' => $demande->user->id,
                    'nom' => $demande->user->nom,
                    'prenom' => $demande->user->prenom,
                    'email' => $demande->user->email,
                    'telephone' => $demande->user->telephone
                ] : null,
                'statut_details' => [
                    'validee' => $demande->statut === 'validee',
                    'en_attente_formateur' => $demande->statut === 'en_attente_formateur',
                    'acceptee_formateur' => $demande->statut === 'acceptee_formateur',
                    'refusee_formateur' => $demande->statut === 'refusee_formateur',
                    'terminee' => $demande->statut === 'terminee',
                    'annulee' => $demande->statut === 'annulee'
                ]
            ];
        });

        // Statistiques par statut
        $statistiques = [
            'total_demandes' => $demandes->count(),
            'demandes_validees' => $demandes->where('statut', 'validee')->count(),
            'en_attente_formateur' => $demandes->where('statut', 'en_attente_formateur')->count(),
            'acceptees_formateur' => $demandes->where('statut', 'acceptee_formateur')->count(),
            'refusees_formateur' => $demandes->where('statut', 'refusee_formateur')->count(),
            'terminees' => $demandes->where('statut', 'terminee')->count(),
            'annulees' => $demandes->where('statut', 'annulee')->count()
        ];

                return response()->json([
            'success' => true,
            'formateur' => [
                'id' => $formateur->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'ville' => $formateur->ville,
                'commune' => $formateur->commune,
                'quartier' => $formateur->quartier
            ],
            'statistiques' => $statistiques,
            'demandes' => $demandesFormatees
        ], 200);
    }

    /**
     * Récupérer les formations à domicile validées par l'admin et assignées au formateur, filtrées par année
     */
    public function mesFormationsValideesParAnnee(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $formateur = $user->formateur;
        if (!$formateur) {
            return response()->json(['error' => 'Aucun formateur trouvé pour cet utilisateur'], 404);
        }

        // Récupérer l'année depuis la requête, par défaut année courante
        $annee = $request->get('annee', date('Y'));
        
        // Valider que l'année est un nombre valide
        if (!is_numeric($annee) || $annee < 2000 || $annee > 2100) {
            return response()->json([
                'error' => 'Année invalide. Veuillez fournir une année entre 2000 et 2100.'
            ], 400);
        }

        // Récupérer les demandes validées par l'admin et assignées au formateur pour l'année spécifiée
        $demandes = \App\Models\DemandeCoursMaison::where('formateur_id', $formateur->id)
            ->whereIn('statut', ['validee', 'en_attente_formateur', 'acceptee_formateur', 'terminee'])
            ->whereYear('created_at', $annee)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get();

        $demandesFormatees = $demandes->map(function ($demande) {
            return [
                'id' => $demande->id,
                'module' => $demande->module,
                'nombre_enfants' => $demande->nombre_enfants,
                'ville' => $demande->ville,
                'commune' => $demande->commune,
                'quartier' => $demande->quartier,
                'numero' => $demande->numero,
                'message' => $demande->message,
                'statut' => $demande->statut,
                'created_at' => $demande->created_at,
                'updated_at' => $demande->updated_at,
                'client' => $demande->user ? [
                    'id' => $demande->user->id,
                    'nom' => $demande->user->nom,
                    'prenom' => $demande->user->prenom,
                    'email' => $demande->user->email,
                    'telephone' => $demande->user->telephone
                ] : null,
                'statut_details' => [
                    'validee' => $demande->statut === 'validee',
                    'en_attente_formateur' => $demande->statut === 'en_attente_formateur',
                    'acceptee_formateur' => $demande->statut === 'acceptee_formateur',
                    'terminee' => $demande->statut === 'terminee'
                ]
            ];
        });

        // Statistiques par statut pour l'année
        $statistiques = [
            'annee' => $annee,
            'total_formations_validees' => $demandes->count(),
            'validees_admin' => $demandes->where('statut', 'validee')->count(),
            'en_attente_formateur' => $demandes->where('statut', 'en_attente_formateur')->count(),
            'acceptees_formateur' => $demandes->where('statut', 'acceptee_formateur')->count(),
            'terminees' => $demandes->where('statut', 'terminee')->count()
        ];

        // Statistiques par mois pour l'année
        $statistiquesParMois = [];
        for ($mois = 1; $mois <= 12; $mois++) {
            $demandesMois = $demandes->filter(function($demande) use ($mois) {
                return $demande->created_at->month === $mois;
            });
            
            $statistiquesParMois[$mois] = [
                'mois' => $mois,
                'nom_mois' => date('F', mktime(0, 0, 0, $mois, 1)),
                'total' => $demandesMois->count(),
                'validees_admin' => $demandesMois->where('statut', 'validee')->count(),
                'en_attente_formateur' => $demandesMois->where('statut', 'en_attente_formateur')->count(),
                'acceptees_formateur' => $demandesMois->where('statut', 'acceptee_formateur')->count(),
                'terminees' => $demandesMois->where('statut', 'terminee')->count()
            ];
        }

        return response()->json([
            'success' => true,
            'annee' => $annee,
            'formateur' => [
                'id' => $formateur->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'ville' => $formateur->ville,
                'commune' => $formateur->commune,
                'quartier' => $formateur->quartier
            ],
            'statistiques' => $statistiques,
            'statistiques_par_mois' => $statistiquesParMois,
            'formations_validees' => $demandesFormatees,
            'message' => "Formations à domicile validées par l'admin pour l'année $annee"
        ], 200);
    }

    /**
     * Récupérer les formations à domicile validées par l'admin et assignées au formateur, avec filtre par année et statut
     */
    public function mesFormationsValideesFiltrees(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $formateur = $user->formateur;
        if (!$formateur) {
            return response()->json(['error' => 'Aucun formateur trouvé pour cet utilisateur'], 404);
        }

        // Récupérer l'année et le statut depuis la requête
        $annee = $request->get('annee', date('Y'));
        $statut = $request->get('statut');
        
        // Valider que l'année est un nombre valide
        if (!is_numeric($annee) || $annee < 2000 || $annee > 2100) {
            return response()->json([
                'error' => 'Année invalide. Veuillez fournir une année entre 2000 et 2100.'
            ], 400);
        }

        // Statuts valides pour les formations validées
        $statutsValides = [
            'validee', 'en_attente_formateur', 'acceptee_formateur', 'terminee'
        ];

        // Construire la requête
        $query = \App\Models\DemandeCoursMaison::where('formateur_id', $formateur->id)
            ->whereIn('statut', $statutsValides)
            ->whereYear('created_at', $annee);

        // Ajouter le filtre par statut si spécifié
        if ($statut && in_array($statut, $statutsValides)) {
            $query->where('statut', $statut);
        }

        $demandes = $query->with(['user'])->orderByDesc('created_at')->get();

        $demandesFormatees = $demandes->map(function ($demande) {
            return [
                'id' => $demande->id,
                'module' => $demande->module,
                'nombre_enfants' => $demande->nombre_enfants,
                'ville' => $demande->ville,
                'commune' => $demande->commune,
                'quartier' => $demande->quartier,
                'numero' => $demande->numero,
                'message' => $demande->message,
                'statut' => $demande->statut,
                'created_at' => $demande->created_at,
                'updated_at' => $demande->updated_at,
                'client' => $demande->user ? [
                    'id' => $demande->user->id,
                    'nom' => $demande->user->nom,
                    'prenom' => $demande->user->prenom,
                    'email' => $demande->user->email,
                    'telephone' => $demande->user->telephone
                ] : null
            ];
        });

        // Calculer les statistiques
        $statistiques = [
            'annee' => $annee,
            'statut_filtre' => $statut ?: 'tous',
            'total_formations' => $demandes->count(),
            'validees_admin' => $demandes->where('statut', 'validee')->count(),
            'en_attente_formateur' => $demandes->where('statut', 'en_attente_formateur')->count(),
            'acceptees_formateur' => $demandes->where('statut', 'acceptee_formateur')->count(),
            'terminees' => $demandes->where('statut', 'terminee')->count()
        ];

        return response()->json([
            'success' => true,
            'annee' => $annee,
            'statut_filtre' => $statut ?: 'tous',
            'formateur' => [
                'id' => $formateur->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email
            ],
            'statistiques' => $statistiques,
            'formations_validees' => $demandesFormatees,
            'message' => $statut 
                ? "Formations à domicile avec statut '$statut' pour l'année $annee"
                : "Formations à domicile validées par l'admin pour l'année $annee"
        ], 200);
    }

    /**
     * Récupérer les demandes de cours à domicile en attente d'action du formateur
     */
    public function mesDemandesEnAttente()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $formateur = $user->formateur;
        if (!$formateur) {
            return response()->json(['error' => 'Aucun formateur trouvé pour cet utilisateur'], 404);
        }

        // Récupérer les demandes qui nécessitent une action du formateur
        $demandes = \App\Models\DemandeCoursMaison::where('formateur_id', $formateur->id)
            ->whereIn('statut', ['validee', 'en_attente_formateur'])
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get();

        $demandesFormatees = $demandes->map(function ($demande) {
            return [
                'id' => $demande->id,
                'module' => $demande->module,
                'nombre_enfants' => $demande->nombre_enfants,
                'ville' => $demande->ville,
                'commune' => $demande->commune,
                'quartier' => $demande->quartier,
                'numero' => $demande->numero,
                'message' => $demande->message,
                'statut' => $demande->statut,
                'created_at' => $demande->created_at,
                'client' => $demande->user ? [
                    'id' => $demande->user->id,
                    'nom' => $demande->user->nom,
                    'prenom' => $demande->user->prenom,
                    'email' => $demande->user->email,
                    'telephone' => $demande->user->telephone
                ] : null,
                'action_requise' => $demande->statut === 'en_attente_formateur' ? 'accepter_refuser' : 'suivre'
            ];
        });

                return response()->json([
            'success' => true,
            'formateur' => [
                'id' => $formateur->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email
            ],
            'statistiques' => [
                'total_en_attente' => $demandes->count(),
                'validees' => $demandes->where('statut', 'validee')->count(),
                'en_attente_formateur' => $demandes->where('statut', 'en_attente_formateur')->count()
            ],
            'demandes_en_attente' => $demandesFormatees
        ], 200);
    }

    /**
     * Récupérer les apprenants qui ont payé les modules du formateur connecté
     */
    public function mesApprenantsPayes()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $formateur = $user->formateur;
        if (!$formateur) {
            return response()->json(['error' => 'Accès réservé aux formateurs.'], 403);
        }

        // Récupérer tous les modules du formateur
        $modules = $formateur->modules()->with(['inscriptions.apprenant.utilisateur', 'questionnaires.questions'])->get();

        $apprenantsPayes = collect();

        foreach ($modules as $module) {
            foreach ($module->inscriptions as $inscription) {
                $apprenant = $inscription->apprenant;
                
                // Calculer les points pour ce module
                $totalPoints = 0;
                $earnedPoints = 0;
                $questions = $module->questionnaires->flatMap->questions;
                
                foreach ($questions as $question) {
                    $questionPoints = $question->points ?? 1;
                    $totalPoints += $questionPoints;
                    
                    $reponse = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                        ->where('question_id', $question->id)
                        ->value('reponse');
                    
                    if ($reponse === $question->bonne_reponse) {
                        $earnedPoints += $questionPoints;
                    }
                }
                
                $pourcentage = ($totalPoints > 0) ? ($earnedPoints / $totalPoints) * 100 : 0;
                
                $apprenantsPayes->push([
                    'apprenant_id' => $apprenant->id,
                    'apprenant_nom' => $apprenant->utilisateur->nom ?? '',
                    'apprenant_prenom' => $apprenant->utilisateur->prenom ?? '',
                    'apprenant_email' => $apprenant->utilisateur->email ?? '',
                    'module_id' => $module->id,
                    'module_titre' => $module->titre,
                    'module_discipline' => $module->discipline,
                    'niveau_id' => $module->niveau_id,
                    'niveau_nom' => $module->niveau->nom ?? '',
                    'inscription_statut' => $inscription->statut,
                    'total_points' => $totalPoints,
                    'earned_points' => $earnedPoints,
                    'pourcentage' => round($pourcentage, 2),
                    'date_inscription' => $inscription->date_inscription,
                    'date_paiement' => $inscription->paiements->first()->date_paiement ?? null,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'formateur' => [
                'id' => $formateur->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
            ],
            'apprenants_payes' => $apprenantsPayes,
            'total_apprenants' => $apprenantsPayes->count(),
            'statistiques' => [
                'moyenne_pourcentage' => $apprenantsPayes->avg('pourcentage'),
                'meilleur_pourcentage' => $apprenantsPayes->max('pourcentage'),
                'plus_bas_pourcentage' => $apprenantsPayes->min('pourcentage'),
            ]
        ], 200);
    }

    /**
     * Affiche les progressions et le classement des apprenants du formateur
     * Groupés par niveau avec calculs de progression et classement
     */
    public function progressionApprenants(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $formateur = $user->formateur;
        if (!$formateur) {
            return response()->json(['error' => 'Accès réservé aux formateurs.'], 403);
        }

        // Paramètres de filtrage
        $niveauId = $request->input('niveau_id');
        $moduleId = $request->input('module_id');
        $tri = $request->input('tri', 'pourcentage'); // pourcentage, nom, date
        $ordre = $request->input('ordre', 'desc'); // asc, desc

        // Récupérer les modules du formateur avec filtres
        $modulesQuery = $formateur->modules()->with([
            'niveau',
            'inscriptions.apprenant.utilisateur',
            'inscriptions.apprenant.niveau',
            'paiements.apprenant.utilisateur',
            'paiements.apprenant.niveau',
            'questionnaires.questions'
        ]);

        if ($niveauId) {
            $modulesQuery->where('niveau_id', $niveauId);
        }

        if ($moduleId) {
            $modulesQuery->where('id', $moduleId);
        }

        $modules = $modulesQuery->get();

        // Collecter tous les apprenants avec leurs progressions
        $apprenantsProgressions = collect();
        $apprenantsTraites = collect(); // Pour éviter les doublons

        foreach ($modules as $module) {
            // Récupérer les apprenants qui ont payé pour ce module
            $paiementsModule = \App\Models\Paiement::where('module_id', $module->id)
                ->where('statut', 'valide')
                ->with(['apprenant.utilisateur', 'apprenant.niveau'])
                ->get();
                
            foreach ($paiementsModule as $paiement) {
                $apprenant = $paiement->apprenant;
                
                // Vérifier que l'apprenant appartient au bon niveau pour ce module
                // Si l'apprenant est au niveau "Intermédiaires" mais paie pour un module "Débutants",
                // il ne devrait pas apparaître dans les résultats
                if ($apprenant->niveau_id !== $module->niveau_id) {
                    continue; // L'apprenant n'est pas du bon niveau pour ce module
                }
                
                $apprenantKey = $apprenant->id . '_' . $module->id;
                
                if ($apprenantsTraites->contains($apprenantKey)) {
                    continue; // Éviter les doublons
                }
                
                $apprenantsTraites->push($apprenantKey);
                
                // Calculer la progression pour ce module
                $totalPoints = 0;
                $earnedPoints = 0;
                $questions = $module->questionnaires->flatMap->questions;
                
                foreach ($questions as $question) {
                    $questionPoints = $question->points ?? 1;
                    $totalPoints += $questionPoints;
                    
                    $reponse = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                        ->where('question_id', $question->id)
                        ->value('reponse');
                    
                    if ($reponse === $question->bonne_reponse) {
                        $earnedPoints += $questionPoints;
                    }
                }
                
                $pourcentage = ($totalPoints > 0) ? ($earnedPoints / $totalPoints) * 100 : 0;
                
                // Calculer la progression globale de l'apprenant (tous ses modules)
                $progressionGlobale = $this->calculerProgressionGlobale($apprenant);
                
                $apprenantsProgressions->push([
                    'apprenant_id' => $apprenant->id,
                    'apprenant_nom' => $apprenant->utilisateur->nom ?? '',
                    'apprenant_prenom' => $apprenant->utilisateur->prenom ?? '',
                    'apprenant_email' => $apprenant->utilisateur->email ?? '',
                    'apprenant_niveau_id' => $apprenant->niveau_id,
                    'apprenant_niveau_nom' => $apprenant->niveau->nom ?? '',
                    'module_id' => $module->id,
                    'module_titre' => $module->titre,
                    'module_discipline' => $module->discipline,
                    'module_niveau_id' => $module->niveau_id,
                    'module_niveau_nom' => $module->niveau->nom ?? '',
                    'inscription_statut' => 'paye',
                    'total_points' => $totalPoints,
                    'earned_points' => $earnedPoints,
                    'pourcentage_module' => round($pourcentage, 2),
                    'progression_globale' => $progressionGlobale,
                    'date_inscription' => $paiement->date_paiement,
                    'derniere_activite' => $this->getDerniereActivite($apprenant),
                    'type_acces' => 'paiement'
                ]);
            }
            

        }

        // Trier les résultats
        if ($tri === 'pourcentage') {
            $apprenantsProgressions = $apprenantsProgressions->sortBy('pourcentage_module', SORT_NUMERIC, $ordre === 'desc');
        } elseif ($tri === 'nom') {
            $apprenantsProgressions = $apprenantsProgressions->sortBy('apprenant_nom', SORT_STRING, $ordre === 'desc');
        } elseif ($tri === 'date') {
            $apprenantsProgressions = $apprenantsProgressions->sortBy('date_inscription', SORT_NUMERIC, $ordre === 'desc');
        }

        // Grouper par niveau
        $progressionsParNiveau = $apprenantsProgressions->groupBy('module_niveau_id');

        // Calculer les statistiques par niveau
        $statistiquesParNiveau = [];
        foreach ($progressionsParNiveau as $niveauId => $apprenants) {
            $statistiquesParNiveau[$niveauId] = [
                'niveau_nom' => $apprenants->first()['module_niveau_nom'],
                'nb_apprenants' => $apprenants->count(),
                'moyenne_pourcentage' => round($apprenants->avg('pourcentage_module'), 2),
                'meilleur_pourcentage' => $apprenants->max('pourcentage_module'),
                'plus_bas_pourcentage' => $apprenants->min('pourcentage_module'),
                'apprenants' => $apprenants->values()
            ];
        }

        // Classement global
        $classementGlobal = $apprenantsProgressions
            ->groupBy('apprenant_id')
            ->map(function ($apprenantModules) {
                $apprenant = $apprenantModules->first();
                return [
                    'apprenant_id' => $apprenant['apprenant_id'],
                    'apprenant_nom' => $apprenant['apprenant_nom'],
                    'apprenant_prenom' => $apprenant['apprenant_prenom'],
                    'apprenant_email' => $apprenant['apprenant_email'],
                    'apprenant_niveau_nom' => $apprenant['apprenant_niveau_nom'],
                    'moyenne_pourcentage' => round($apprenantModules->avg('pourcentage_module'), 2),
                    'nb_modules' => $apprenantModules->count(),
                    'progression_globale' => $apprenant['progression_globale'],
                    'derniere_activite' => $apprenant['derniere_activite'],
                ];
            })
            ->sortBy('moyenne_pourcentage', SORT_NUMERIC, true)
            ->values();

        return response()->json([
            'success' => true,
            'formateur' => [
                'id' => $formateur->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
            ],
            'filtres_appliques' => [
                'niveau_id' => $niveauId,
                'module_id' => $moduleId,
                'tri' => $tri,
                'ordre' => $ordre
            ],
            'progressions_par_niveau' => $statistiquesParNiveau,
            'classement_global' => $classementGlobal,
            'statistiques_globales' => [
                'total_apprenants' => $apprenantsProgressions->unique('apprenant_id')->count(),
                'total_modules' => $modules->count(),
                'moyenne_globale' => round($apprenantsProgressions->avg('pourcentage_module'), 2),
                'meilleur_apprenant' => $classementGlobal->first(),
                'niveaux_disponibles' => $modules->pluck('niveau')->unique('id')->values()
            ]
        ], 200);
    }

    /**
     * Calcule la progression globale d'un apprenant (uniquement pour les modules du formateur)
     */
    private function calculerProgressionGlobale($apprenant)
    {
        // Récupérer les modules du formateur connecté
        $user = auth()->user();
        $formateur = $user->formateur;
        $modulesFormateur = $formateur->modules()->pluck('id')->toArray();
        
        // Récupérer uniquement les modules payés par l'apprenant pour ce formateur
        $modulesPayes = $apprenant->paiements()
            ->where('statut', 'valide')
            ->whereIn('module_id', $modulesFormateur)
            ->pluck('module_id')
            ->toArray();
            
        $moduleIds = array_unique($modulesPayes);

        if (empty($moduleIds)) {
            return [
                'pourcentage_global' => 0,
                'nb_modules_completes' => 0,
                'nb_modules_total' => count($modulesFormateur),
                'niveau_actuel' => $apprenant->niveau->nom ?? 'Non défini'
            ];
        }

        $sommePourcentages = 0;
        $nbModules = 0;

        foreach ($moduleIds as $moduleId) {
            $module = \App\Models\Module::find($moduleId);
            if (!$module) continue;

            $questionnaires = $module->questionnaires;
            $questions = collect();
            foreach ($questionnaires as $q) {
                $questions = $questions->merge($q->questions);
            }

            $pointsPossibles = $questions->sum(function($q) { return $q->points ?? 1; });
            $pointsObtenus = 0;

            foreach ($questions as $q) {
                $reponse = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                    ->where('question_id', $q->id)
                    ->value('reponse');
                
                if ($reponse === $q->bonne_reponse) {
                    $pointsObtenus += $q->points ?? 1;
                }
            }

            $pourcentageModule = ($pointsPossibles > 0) ? ($pointsObtenus / $pointsPossibles) * 100 : 0;
            $sommePourcentages += $pourcentageModule;
            $nbModules++;
        }

        $pourcentageGlobal = ($nbModules > 0) ? $sommePourcentages / $nbModules : 0;

        return [
            'pourcentage_global' => round($pourcentageGlobal, 2),
            'nb_modules_completes' => $nbModules,
            'nb_modules_total' => count($modulesFormateur),
            'niveau_actuel' => $apprenant->niveau->nom ?? 'Non défini'
        ];
    }

    /**
     * Récupère la dernière activité d'un apprenant
     */
    private function getDerniereActivite($apprenant)
    {
        $derniereReponse = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($derniereReponse) {
            return [
                'type' => 'derniere_reponse',
                'date' => $derniereReponse->updated_at,
                'question_id' => $derniereReponse->question_id
            ];
        }

        $dernierPaiement = $apprenant->paiements()
            ->orderBy('created_at', 'desc')
            ->first();

        if ($dernierPaiement) {
            return [
                'type' => 'dernier_paiement',
                'date' => $dernierPaiement->created_at,
                'module_id' => $dernierPaiement->module_id
            ];
        }

        return [
            'type' => 'aucune_activite',
            'date' => null
        ];
    }



    /**
     * Récupère les liens Google Meet des niveaux assignés au formateur
     * @return \Illuminate\Http\JsonResponse
     */
    public function mesLiensGoogleMeet()
    {
        $user = auth()->user();
        if (!$user || !$user->formateur) {
            return response()->json(['error' => 'Aucun formateur connecté.'], 401);
        }

        // Récupérer les niveaux assignés au formateur avec leurs liens Google Meet
        $niveaux = \App\Models\Niveau::where('formateur_id', $user->formateur->id)
            ->with(['modules', 'sessionFormation'])
            ->orderBy('ordre')
            ->get();

        // Formater la réponse avec les liens Google Meet
        $liensGoogleMeet = $niveaux->map(function ($niveau) {
            return [
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre,
                    'actif' => $niveau->actif,
                ],
                'lien_google_meet' => $niveau->lien_meet,
                'session_formation' => $niveau->sessionFormation ? [
                    'id' => $niveau->sessionFormation->id,
                    'nom' => $niveau->sessionFormation->nom,
                    'date_debut' => $niveau->sessionFormation->date_debut,
                    'date_fin' => $niveau->sessionFormation->date_fin,
                ] : null,
                'modules_count' => $niveau->modules->count(),
                'modules' => $niveau->modules->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'discipline' => $module->discipline,
                        'date_debut' => $module->date_debut,
                        'date_fin' => $module->date_fin,
                        'horaire' => $module->horaire,
                    ];
                }),
                'statut_lien' => $niveau->lien_meet ? 'disponible' : 'non_defini',
                'derniere_mise_a_jour' => $niveau->updated_at
            ];
        });

        // Statistiques des liens
        $totalNiveaux = $liensGoogleMeet->count();
        $liensDisponibles = $liensGoogleMeet->where('statut_lien', 'disponible')->count();
        $liensManquants = $liensGoogleMeet->where('statut_lien', 'non_defini')->count();

        return response()->json([
            'success' => true,
            'formateur' => [
                'id' => $user->formateur->id,
                'utilisateur_id' => $user->formateur->utilisateur_id,
                'specialite' => $user->formateur->specialite,
                'valide' => $user->formateur->valide,
                'utilisateur' => [
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email
                ]
            ],
            'statistiques' => [
                'total_niveaux' => $totalNiveaux,
                'liens_disponibles' => $liensDisponibles,
                'liens_manquants' => $liensManquants,
                'pourcentage_complet' => $totalNiveaux > 0 ? round(($liensDisponibles / $totalNiveaux) * 100, 2) : 0
            ],
            'niveaux_avec_liens' => $liensGoogleMeet,
            'liens_rapides' => $liensGoogleMeet->where('statut_lien', 'disponible')->map(function ($niveau) {
                return [
                    'niveau_nom' => $niveau['niveau']['nom'],
                    'lien' => $niveau['lien_google_meet'],
                    'horaire_suggere' => $niveau['modules'][0]['horaire'] ?? 'Non défini'
                ];
            })->values()
        ], 200);
    }

    /**
     * Récupérer les détails d'un apprenant spécifique qui a payé les modules du formateur
     */
    public function detailsApprenantPaye($apprenantId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $formateur = $user->formateur;
        if (!$formateur) {
            return response()->json(['error' => 'Aucun formateur trouvé pour cet utilisateur'], 404);
        }

        // Vérifier que l'apprenant a payé des modules du formateur
        $paiements = \App\Models\Paiement::where('apprenant_id', $apprenantId)
            ->whereIn('module_id', $formateur->modules()->pluck('id'))
            ->where('statut', 'valide')
            ->with(['apprenant.utilisateur', 'module.niveau', 'module.questionnaires.questions'])
            ->orderByDesc('created_at')
            ->get();

        if ($paiements->isEmpty()) {
            return response()->json(['error' => 'Aucun paiement trouvé pour cet apprenant'], 404);
        }

        $apprenant = $paiements->first()->apprenant;
        $utilisateur = $apprenant->utilisateur;

        // Récupérer les inscriptions de l'apprenant pour les modules du formateur
        $inscriptions = $apprenant->inscriptions()
            ->whereIn('module_id', $formateur->modules()->pluck('id'))
            ->with(['module.niveau', 'sessionFormation'])
            ->get();

        $detailsApprenant = [
            'apprenant' => [
                'id' => $apprenant->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'email' => $utilisateur->email,
                'telephone' => $utilisateur->telephone,
                'date_inscription' => $apprenant->created_at,
                'niveau' => $apprenant->niveau ? [
                    'id' => $apprenant->niveau->id,
                    'nom' => $apprenant->niveau->nom,
                    'ordre' => $apprenant->niveau->ordre
                ] : null,
                'formateur_domicile' => $apprenant->formateur_domicile
            ],
            'modules_payes' => $paiements->map(function ($paiement) {
                return [
                    'module' => [
                        'id' => $paiement->module->id,
                        'titre' => $paiement->module->titre,
                        'description' => $paiement->module->description,
                        'discipline' => $paiement->module->discipline,
                        'date_debut' => $paiement->module->date_debut,
                        'date_fin' => $paiement->module->date_fin,
                        'horaire' => $paiement->module->horaire,
                        'lien' => $paiement->module->lien,
                        'support' => $paiement->module->support,
                        'audio' => $paiement->module->audio,
                        'niveau' => $paiement->module->niveau ? [
                            'id' => $paiement->module->niveau->id,
                            'nom' => $paiement->module->niveau->nom,
                            'ordre' => $paiement->module->niveau->ordre
                        ] : null,
                        'questionnaires' => $paiement->module->questionnaires->map(function ($questionnaire) {
                            return [
                                'id' => $questionnaire->id,
                                'titre' => $questionnaire->titre,
                                'description' => $questionnaire->description,
                                'nombre_questions' => $questionnaire->questions->count()
                            ];
                        })
                    ],
                    'paiement' => [
                        'id' => $paiement->id,
                        'montant' => $paiement->montant,
                        'methode' => $paiement->methode,
                        'reference' => $paiement->reference,
                        'date_paiement' => $paiement->date_paiement,
                        'statut' => $paiement->statut
                    ]
                ];
            }),
            'inscriptions' => $inscriptions->map(function ($inscription) {
                return [
                    'id' => $inscription->id,
                    'date_inscription' => $inscription->date_inscription,
                    'statut' => $inscription->statut,
                    'mobile_money' => $inscription->mobile_money,
                    'moyen_paiement' => $inscription->moyen_paiement,
                    'module' => [
                        'id' => $inscription->module->id,
                        'titre' => $inscription->module->titre,
                        'niveau' => $inscription->module->niveau ? [
                            'id' => $inscription->module->niveau->id,
                            'nom' => $inscription->module->niveau->nom
                        ] : null
                    ],
                    'session_formation' => $inscription->sessionFormation ? [
                        'id' => $inscription->sessionFormation->id,
                        'nom' => $inscription->sessionFormation->nom,
                        'date_debut' => $inscription->sessionFormation->date_debut,
                        'date_fin' => $inscription->sessionFormation->date_fin
                    ] : null
                ];
            }),
            'statistiques' => [
                'total_modules_payes' => $paiements->count(),
                'total_montant_paye' => $paiements->sum('montant'),
                'total_inscriptions' => $inscriptions->count(),
                'dernier_paiement' => $paiements->max('created_at'),
                'premier_paiement' => $paiements->min('created_at'),
                'moyenne_montant_par_module' => $paiements->count() > 0 ? 
                    round($paiements->sum('montant') / $paiements->count(), 2) : 0
            ]
        ];

        return response()->json([
            'success' => true,
            'formateur' => [
                'id' => $formateur->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email
            ],
            'details_apprenant' => $detailsApprenant
        ], 200);
    }

    /**
     * Récupère les documents des modules assignés au formateur connecté
     */
    public function mesDocuments()
    {
        try {
            // Récupère le formateur connecté
            $formateur = Auth::user()->formateur;
            
            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Utilisateur non reconnu comme formateur'
                ], 403);
            }

            // Récupère les niveaux assignés au formateur
            $niveauxAssignes = Niveau::where('formateur_id', $formateur->id)
                ->with(['modules', 'sessionFormation'])
                ->get();

            // Récupère les IDs des niveaux assignés
            $niveauxIds = $niveauxAssignes->pluck('id');

            // Récupère les documents des niveaux assignés au formateur, accessibles à partir de la date d'envoi
            $documents = Document::whereIn('niveau_id', $niveauxIds)
                ->where('date_envoi', '<=', now()) // Seulement les documents dont la date d'envoi est passée
                ->with([
                    'niveau',
                    'module',
                    'formateur.utilisateur', // Ajouter les informations du formateur créateur
                    'certificat' => function($query) {
                        $query->select('id', 'titre', 'apprenant_id');
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            // Récupère les modules des niveaux assignés
            $modulesFormateur = collect();
            foreach ($niveauxAssignes as $niveau) {
                $modulesFormateur = $modulesFormateur->merge($niveau->modules);
            }

            // Formate les données
            $documentsFormates = $documents->map(function($document) {
                return [
                    'id' => $document->id,
                    'titre' => $document->titre,
                    'type' => $document->type,
                    'fichier' => $document->fichier,
                    'audio' => $document->audio,
                    'url_telechargement' => url("/api/documents/{$document->id}/telecharger"),
                    'url_telechargement_audio' => $document->audio ? url("/api/documents/{$document->id}/telecharger-audio") : null,
                    'semaine' => $document->semaine,
                    'date_envoi' => $document->date_envoi,
                    'created_at' => $document->created_at,
                    'updated_at' => $document->updated_at,
                    'niveau' => $document->niveau ? [
                        'id' => $document->niveau->id,
                        'nom' => $document->niveau->nom,
                        'description' => $document->niveau->description,
                        'ordre' => $document->niveau->ordre
                    ] : null,
                    'module' => $document->module ? [
                        'id' => $document->module->id,
                        'titre' => $document->module->titre,
                        'description' => $document->module->description,
                        'prix' => $document->module->prix,
                        'date_debut' => $document->module->date_debut,
                        'date_fin' => $document->module->date_fin
                    ] : null,
                    'formateur' => $document->formateur && $document->formateur->utilisateur ? [
                        'id' => $document->formateur->id,
                        'nom' => $document->formateur->utilisateur->nom,
                        'prenom' => $document->formateur->utilisateur->prenom,
                        'email' => $document->formateur->utilisateur->email
                    ] : null,
                    'certificat' => $document->certificat ? [
                        'id' => $document->certificat->id,
                        'titre' => $document->certificat->titre
                    ] : null,
                    'created_by_admin' => $document->created_by_admin
                ];
            });

            // Statistiques
            $statistiques = [
                'total_documents' => $documents->count(),
                'total_modules_assignes' => $modulesFormateur->count(),
                'niveaux_assignes' => $niveauxAssignes->count(),
                'documents_par_niveau' => $documents->groupBy('niveau.nom')->map->count(),
                'documents_par_semaine' => $documents->groupBy('semaine')->map->count(),
                'derniers_documents' => $documents->take(5)->count(),
                'modules_par_niveau' => $niveauxAssignes->mapWithKeys(function($niveau) {
                    return [$niveau->nom => $niveau->modules->count()];
                })
            ];

            return response()->json([
                'success' => true,
                'message' => 'Documents des niveaux assignés au formateur récupérés avec succès (filtrés par date d\'envoi, liens de téléchargement inclus)',
                'formateur' => [
                    'id' => $formateur->id,
                    'nom' => $formateur->utilisateur->nom,
                    'prenom' => $formateur->utilisateur->prenom,
                    'email' => $formateur->utilisateur->email
                ],
                'niveaux_assignes' => $niveauxAssignes->map(function($niveau) {
                    return [
                        'id' => $niveau->id,
                        'nom' => $niveau->nom,
                        'description' => $niveau->description,
                        'ordre' => $niveau->ordre,
                        'lien_meet' => $niveau->lien_meet,
                        'modules_count' => $niveau->modules->count(),
                        'session_formation' => $niveau->sessionFormation ? [
                            'id' => $niveau->sessionFormation->id,
                            'nom' => $niveau->sessionFormation->nom,
                            'date_debut' => $niveau->sessionFormation->date_debut,
                            'date_fin' => $niveau->sessionFormation->date_fin
                        ] : null
                    ];
                }),
                'modules_assignes' => $modulesFormateur->map(function($module) {
                    return [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'niveau' => $module->niveau ? [
                            'id' => $module->niveau->id,
                            'nom' => $module->niveau->nom
                        ] : null
                    ];
                }),
                'documents' => $documentsFormates,
                'statistiques' => $statistiques
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des documents',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les statistiques complètes du formateur connecté
     */
    public function statistiques()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            $formateur = $user->formateur;
            if (!$formateur) {
                return response()->json(['error' => 'Aucun profil formateur trouvé pour cet utilisateur'], 404);
            }

            // Récupérer les niveaux assignés au formateur
            $niveauxAssignes = \App\Models\Niveau::where('formateur_id', $formateur->id)->get();
            
            // Récupérer tous les modules des niveaux assignés
            $modulesFormateur = \App\Models\Module::whereIn('niveau_id', $niveauxAssignes->pluck('id'))->get();
            
            // Récupérer les apprenants des niveaux assignés
            $apprenants = \App\Models\Apprenant::whereIn('niveau_id', $niveauxAssignes->pluck('id'))->get();
            
            // Récupérer les paiements pour les modules des niveaux assignés
            $paiements = \App\Models\Paiement::whereIn('module_id', $modulesFormateur->pluck('id'))
                ->where('statut', 'valide')
                ->get();
            
            // Récupérer les certificats délivrés
            $certificats = \App\Models\Certificat::whereIn('module_id', $modulesFormateur->pluck('id'))->get();
            
            // Récupérer les documents créés
            $documents = \App\Models\Document::whereIn('niveau_id', $niveauxAssignes->pluck('id'))
                ->orWhereIn('module_id', $modulesFormateur->pluck('id'))
                ->get();

            // Calculer les statistiques
            $statistiques = [
                // 👥 Gestion des apprenants
                'apprenants' => [
                    'total' => $apprenants->count(),
                    'par_niveau' => $niveauxAssignes->mapWithKeys(function($niveau) use ($apprenants) {
                        return [$niveau->nom => $apprenants->where('niveau_id', $niveau->id)->count()];
                    })
                ],

                // 📚 Modules et formations
                'modules' => [
                    'total' => $modulesFormateur->count(),
                    'actifs' => $modulesFormateur->where('date_debut', '<=', now())->where('date_fin', '>=', now())->count(),
                    'termines' => $modulesFormateur->where('date_fin', '<', now())->count(),
                    'a_venir' => $modulesFormateur->where('date_debut', '>', now())->count(),
                    'par_niveau' => $niveauxAssignes->mapWithKeys(function($niveau) use ($modulesFormateur) {
                        return [$niveau->nom => $modulesFormateur->where('niveau_id', $niveau->id)->count()];
                    })
                ],

                // 💰 Aspects financiers
                'financier' => [
                    'total_paiements' => $paiements->count(),
                    'montant_total' => $paiements->sum('montant'),
                    'moyenne_paiement' => $paiements->count() > 0 ? round($paiements->avg('montant'), 2) : 0
                ],

                // 📈 Progression et performance
                'performance' => [
                    'certificats_delivres' => $certificats->count(),
                    'taux_reussite' => $apprenants->count() > 0 ? round(($certificats->count() / $apprenants->count()) * 100, 2) : 0
                ],

                // 📅 Activité et planning
                'activite' => [
                    'niveaux_assignes' => $niveauxAssignes->count(),
                    'documents_crees' => $documents->count(),
                    'derniere_activite' => $documents->max('created_at')
                ],

                // 📊 Résumé global
                'resume' => [
                    'total_ressources' => $modulesFormateur->count() + $documents->count(),
                    'impact_apprenants' => $apprenants->count(),
                    'contribution_financiere' => $paiements->sum('montant')
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistiques du formateur récupérées avec succès',
                'formateur' => [
                    'id' => $formateur->id,
                    'nom' => $formateur->utilisateur->nom,
                    'prenom' => $formateur->utilisateur->prenom,
                    'email' => $formateur->utilisateur->email,
                    'specialite' => $formateur->specialite,
                    'niveau_coran' => $formateur->niveau_coran,
                    'niveau_arabe' => $formateur->niveau_arabe
                ],
                'statistiques' => $statistiques,
                'debug' => [
                    'niveaux_assignes_ids' => $niveauxAssignes->pluck('id'),
                    'modules_ids' => $modulesFormateur->pluck('id'),
                    'apprenants_ids' => $apprenants->pluck('id'),
                    'paiements_count' => $paiements->count(),
                    'documents_count' => $documents->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des statistiques',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculer le niveau d'activité du formateur
     */
    private function calculerNiveauActivite($documents, $modules)
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
     * Vérifier si le formateur connecté a aussi un profil apprenant
     */
    public function verifierProfilApprenant()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Utilisateur non authentifié'
                ], 401);
            }

            $formateur = $user->formateur;
            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil formateur non trouvé'
                ], 404);
            }

            // Vérifier si l'utilisateur a des profils apprenants
            $profilsApprenants = \App\Models\Apprenant::where('utilisateur_id', $user->id)
                ->with(['niveau'])
                ->get();

            if ($profilsApprenants->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vous n\'avez pas de profil apprenant',
                    'a_profil_apprenant' => false,
                    'formateur' => [
                        'id' => $formateur->id,
                        'nom' => $user->nom,
                        'prenom' => $user->prenom,
                        'email' => $user->email,
                        'specialite' => $formateur->specialite,
                        'valide' => $formateur->valide
                    ],
                    'profil_apprenant' => null
                ], 200);
            }

            // Formater les profils apprenants
            $profilsFormates = $profilsApprenants->map(function ($apprenant) {
                return [
                    'id' => $apprenant->id,
                    'niveau' => [
                        'id' => $apprenant->niveau->id,
                        'nom' => $apprenant->niveau->nom,
                        'description' => $apprenant->niveau->description,
                        'ordre' => $apprenant->niveau->ordre
                    ],
                    'connaissance_adis' => $apprenant->connaissance_adis,
                    'formation_adis' => $apprenant->formation_adis,
                    'formation_autre' => $apprenant->formation_autre,
                    'niveau_coran' => $apprenant->niveau_coran,
                    'niveau_arabe' => $apprenant->niveau_arabe,
                    'connaissance_tomes_medine' => $apprenant->connaissance_tomes_medine,
                    'tomes_medine_etudies' => $apprenant->tomes_medine_etudies ? json_decode($apprenant->tomes_medine_etudies) : [],
                    'disciplines_souhaitees' => $apprenant->disciplines_souhaitees ? json_decode($apprenant->disciplines_souhaitees) : [],
                    'attentes' => $apprenant->attentes ? json_decode($apprenant->attentes) : [],
                    'formateur_domicile' => $apprenant->formateur_domicile,
                    'date_inscription' => $apprenant->created_at->format('Y-m-d H:i:s'),
                    'derniere_mise_a_jour' => $apprenant->updated_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Vous avez un profil apprenant',
                'a_profil_apprenant' => true,
                'formateur' => [
                    'id' => $formateur->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'specialite' => $formateur->specialite,
                    'valide' => $formateur->valide
                ],
                'profils_apprenants' => $profilsFormates,
                'total_profils' => $profilsApprenants->count(),
                'resume' => [
                    'message' => "Vous êtes formateur ET apprenant !",
                    'niveaux_apprentissage' => $profilsApprenants->pluck('niveau.nom')->toArray(),
                    'statut' => "Double profil actif"
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la vérification du profil apprenant',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier si le formateur connecté a aussi un profil assistant
     */
    public function verifierProfilAssistant()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Utilisateur non authentifié'
                ], 401);
            }

            $formateur = $user->formateur;
            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil formateur non trouvé'
                ], 404);
            }

            // Vérifier si l'utilisateur a un profil assistant
            $profilAssistant = \App\Models\Assistant::where('utilisateur_id', $user->id)->first();

            if (!$profilAssistant) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vous n\'avez pas de profil assistant',
                    'a_profil_assistant' => false,
                    'formateur' => [
                        'id' => $formateur->id,
                        'nom' => $user->nom,
                        'prenom' => $user->prenom,
                        'email' => $user->email,
                        'specialite' => $formateur->specialite,
                        'valide' => $formateur->valide
                    ],
                    'profil_assistant' => null
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Vous avez un profil assistant',
                'a_profil_assistant' => true,
                'formateur' => [
                    'id' => $formateur->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'specialite' => $formateur->specialite,
                    'valide' => $formateur->valide
                ],
                'profil_assistant' => [
                    'id' => $profilAssistant->id,
                    'bio' => $profilAssistant->bio,
                    'actif' => $profilAssistant->actif,
                    'date_creation' => $profilAssistant->created_at->format('Y-m-d H:i:s'),
                    'derniere_mise_a_jour' => $profilAssistant->updated_at->format('Y-m-d H:i:s')
                ],
                'resume' => [
                    'message' => "Vous êtes formateur ET assistant !",
                    'statut' => "Double profil actif",
                    'permissions' => [
                        'peut_former' => true,
                        'peut_assister' => $profilAssistant->actif,
                        'peut_gerer_contenu' => true
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la vérification du profil assistant',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer le calendrier du formateur connecté avec les dates des modules de son niveau
     */
    public function calendrierFormateur()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Utilisateur non authentifié'
                ], 401);
            }

            $formateur = $user->formateur;
            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil formateur non trouvé'
                ], 404);
            }

            // Récupérer les niveaux assignés au formateur
            $niveauxAssignes = Niveau::where('formateur_id', $formateur->id)
                ->where('actif', true)
                ->with(['modules' => function($query) {
                    $query->orderBy('date_debut', 'asc');
                }])
                ->get();

            if ($niveauxAssignes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun niveau assigné à ce formateur'
                ], 404);
            }

            $calendrier = [];
            $totalModules = 0;
            $totalHeures = 0;

            foreach ($niveauxAssignes as $niveau) {
                $modulesDuNiveau = [];
                
                foreach ($niveau->modules as $module) {
                    $modulesDuNiveau[] = [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'description' => $module->description,
                        'discipline' => $module->discipline,
                        'date_debut' => $module->date_debut,
                        'date_fin' => $module->date_fin,
                        'prix' => $module->prix,
                        'certificat' => $module->certificat,
                        'duree_jours' => \Carbon\Carbon::parse($module->date_debut)->diffInDays($module->date_fin) + 1,
                        'statut' => $this->determinerStatutModule($module),
                        'apprenants_inscrits' => $module->inscriptions()->count(),
                        'documents_disponibles' => $module->documents()->count(),
                        'questionnaires_disponibles' => $module->questionnaires()->count()
                    ];

                    $totalModules++;
                }

                $calendrier[] = [
                    'niveau' => [
                        'id' => $niveau->id,
                        'nom' => $niveau->nom,
                        'description' => $niveau->description,
                        'ordre' => $niveau->ordre,
                        'formateur_id' => $niveau->formateur_id,
                        'date_assignation' => $niveau->date_assignation ?? null
                    ],
                    'modules' => $modulesDuNiveau,
                    'total_modules' => count($modulesDuNiveau),
                    'periode' => [
                        'debut' => $niveau->modules->min('date_debut'),
                        'fin' => $niveau->modules->max('date_fin')
                    ]
                ];
            }

            // Calculer les statistiques globales
            $statistiques = [
                'total_niveaux' => $niveauxAssignes->count(),
                'total_modules' => $totalModules,
                'periode_globale' => [
                    'debut' => $niveauxAssignes->flatMap->modules->min('date_debut'),
                    'fin' => $niveauxAssignes->flatMap->modules->max('date_fin')
                ],
                'formateur' => [
                    'id' => $formateur->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'specialite' => $formateur->specialite,
                    'valide' => $formateur->valide
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Calendrier du formateur récupéré avec succès',
                'calendrier' => $calendrier,
                'statistiques' => $statistiques,
                'periode_courante' => [
                    'mois_actuel' => now()->format('F Y'),
                    'semaine_actuelle' => now()->weekOfYear,
                    'jour_actuel' => now()->format('l d/m/Y')
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération du calendrier',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Déterminer le statut d'un module basé sur ses dates
     */
    private function determinerStatutModule($module)
    {
        $aujourdhui = now();
        $dateDebut = \Carbon\Carbon::parse($module->date_debut);
        $dateFin = \Carbon\Carbon::parse($module->date_fin);

        if ($aujourdhui < $dateDebut) {
            return 'à_venir';
        } elseif ($aujourdhui >= $dateDebut && $aujourdhui <= $dateFin) {
            return 'en_cours';
        } else {
            return 'termine';
        }
    }

        /**
     * Récupère tous les apprenants assignés au formateur connecté via ses niveaux
     */
    public function mesApprenantsAssignes()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            $formateur = $user->formateur;
            if (!$formateur) {
                return response()->json(['error' => 'Aucun formateur trouvé pour cet utilisateur'], 404);
            }

            // Récupérer les niveaux du formateur avec leurs apprenants
            $niveaux = $formateur->niveaux()
                ->with(['apprenants' => function($query) {
                    $query->with(['utilisateur:id,nom,prenom,email,telephone,sexe,categorie,actif,created_at']);
                }])
                ->get();

            $apprenantsParNiveau = $niveaux->map(function ($niveau) {
                return [
                    'niveau' => [
                        'id' => $niveau->id,
                        'nom' => $niveau->nom,
                        'description' => $niveau->description,
                        'ordre' => $niveau->ordre,
                        'actif' => $niveau->actif,
                        'lien_meet' => $niveau->lien_meet
                    ],
                    'apprenants' => $niveau->apprenants->map(function ($apprenant) {
                        return [
                            'id' => $apprenant->id,
                            'utilisateur_id' => $apprenant->utilisateur_id,
                            'niveau_id' => $apprenant->niveau_id,
                            'connaissance_adis' => $apprenant->connaissance_adis,
                            'formation_adis' => $apprenant->formation_adis,
                            'formation_autre' => $apprenant->formation_autre,
                            'niveau_coran' => $apprenant->niveau_coran,
                            'niveau_arabe' => $apprenant->niveau_arabe,
                            'connaissance_tomes_medine' => $apprenant->connaissance_tomes_medine,
                            'tomes_medine_etudies' => $apprenant->tomes_medine_etudies,
                            'disciplines_souhaitees' => $apprenant->disciplines_souhaitees,
                            'attentes' => $apprenant->attentes,
                            'formateur_domicile' => $apprenant->formateur_domicile,
                            'created_at' => $apprenant->created_at,
                            'updated_at' => $apprenant->updated_at,
                            'utilisateur' => $apprenant->utilisateur
                        ];
                    })
                ];
            });

            $totalApprenants = $niveaux->sum(function ($niveau) {
                return $niveau->apprenants->count();
            });

            return response()->json([
                'success' => true,
                'message' => 'Apprenants assignés récupérés avec succès',
                'data' => [
                    'formateur' => [
                        'id' => $formateur->id,
                        'utilisateur_id' => $formateur->utilisateur_id,
                        'specialite' => $formateur->specialite,
                        'valide' => $formateur->valide
                    ],
                    'total_niveaux' => $niveaux->count(),
                    'total_apprenants' => $totalApprenants,
                    'niveaux' => $apprenantsParNiveau
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des apprenants assignés: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère la progression des apprenants assignés au formateur connecté
     * Inclut tous les apprenants assignés, même sans paiements ou questionnaires
     */
    public function progressionApprenantsAssignes(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            $formateur = $user->formateur;
            if (!$formateur) {
                return response()->json(['error' => 'Accès réservé aux formateurs.'], 403);
            }

            // Paramètres de filtrage
            $niveauId = $request->input('niveau_id');
            $tri = $request->input('tri', 'nom'); // nom, niveau, date_inscription
            $ordre = $request->input('ordre', 'asc'); // asc, desc

            // CORRECTION : Ignorer le filtre niveau_id=1 incorrect et toujours retourner les niveaux du formateur
            // Récupérer les niveaux du formateur avec leurs apprenants
            $niveauxQuery = $formateur->niveaux();
            
            // Ne filtrer que si le niveau_id est valide et appartient au formateur
            if ($niveauId && $niveauId != 1) {
                $niveauxQuery->where('id', $niveauId);
            }

            $niveaux = $niveauxQuery->with([
                'apprenants' => function($query) {
                    $query->with(['utilisateur:id,nom,prenom,email,telephone,sexe,categorie,actif,created_at']);
                }
            ])->get();

            $progressionsParNiveau = [];
            $totalApprenants = 0;
            $totalModules = 0;

            foreach ($niveaux as $niveau) {
                $apprenantsNiveau = [];
                $modulesNiveau = $formateur->modules()->where('niveau_id', $niveau->id)->get();
                $totalModules += $modulesNiveau->count();

                foreach ($niveau->apprenants as $apprenant) {
                    $totalApprenants++;
                    
                    // Calculer la progression pour chaque module du niveau
                    $modulesProgressions = [];
                    $totalPourcentage = 0;
                    $modulesCompletes = 0;
                    $totalModulesApprenant = 0;

                    foreach ($modulesNiveau as $module) {
                        $totalModulesApprenant++;
                        
                        // Vérifier s'il y a des paiements pour ce module
                        $paiement = \App\Models\Paiement::where('module_id', $module->id)
                            ->where('apprenant_id', $apprenant->id)
                            ->where('statut', 'valide')
                            ->first();

                        $moduleProgression = [
                            'module_id' => $module->id,
                            'module_titre' => $module->titre,
                            'module_discipline' => $module->discipline,
                            'a_paye' => $paiement ? true : false,
                            'statut_paiement' => $paiement ? $paiement->statut : 'non_paye',
                            'date_paiement' => $paiement ? $paiement->created_at : null,
                            'pourcentage' => 0,
                            'questionnaires_completes' => 0,
                            'total_questionnaires' => 0
                        ];

                        // Si l'apprenant a payé, calculer la progression avec le nouveau calcul synchronisé
                        if ($paiement) {
                            $questionnaires = $module->questionnaires()->with('questions')->get();
                            $totalQuestionnaires = $questionnaires->count();
                            $questionnairesCompletes = 0;
                            $totalPoints = 0;
                            $earnedPoints = 0;

                            // Vérifier que tous les questionnaires sont complétés
                            foreach ($questionnaires as $questionnaire) {
                                $questions = $questionnaire->questions;
                                foreach ($questions as $question) {
                                    $questionPoints = $question->points ?? 1;
                                    $totalPoints += $questionPoints;
                                    
                                    $reponse = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                                        ->where('question_id', $question->id)
                                        ->first();
                                    
                                    if ($reponse && $reponse->reponse === $question->bonne_reponse) {
                                        $earnedPoints += $questionPoints;
                                    }
                                }
                                
                                // Vérifier si le questionnaire est complet
                                $reponsesQuestionnaire = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                                    ->whereIn('question_id', $questions->pluck('id'))
                                    ->count();
                                
                                if ($reponsesQuestionnaire === $questions->count()) {
                                    $questionnairesCompletes++;
                                }
                            }

                            $pourcentage = ($totalPoints > 0) ? ($earnedPoints / $totalPoints) * 100 : 0;
                            
                            // Validation avec pondération par type : Module valide selon le système de pourcentages
                            if ($totalQuestionnaires > 0) {
                                // Calculer le pourcentage selon le type de questionnaire
                                $pourcentageCalcule = 0;
                                $questionnairesParType = [];
                                
                                foreach ($questionnaires as $questionnaire) {
                                    $type = $questionnaire->type_devoir;
                                    
                                    // Compter les bonnes réponses
                                    $questionsReponduesCorrectement = 0;
                                    foreach ($questionnaire->questions as $question) {
                                        $reponse = \App\Models\ReponseQuestionnaire::where('apprenant_id', $apprenant->id)
                                            ->where('question_id', $question->id)
                                            ->first();
                                        
                                        if ($reponse && $reponse->reponse === $question->bonne_reponse) {
                                            $questionsReponduesCorrectement++;
                                        }
                                    }
                                    
                                    $totalQuestionsQuestionnaire = $questionnaire->questions->count();
                                    $pourcentageQuestionnaire = ($totalQuestionsQuestionnaire > 0) ? ($questionsReponduesCorrectement / $totalQuestionsQuestionnaire) * 100 : 0;
                                    
                                    // Pondération selon le type
                                    $ponderation = 0;
                                    switch ($type) {
                                        case 'hebdomadaire':
                                            $ponderation = 2; // 2% par semaine
                                            break;
                                        case 'mensuel':
                                            $ponderation = 8; // 8% par mois
                                            break;
                                        case 'final':
                                            $ponderation = 66; // 66% final
                                            break;
                                    }
                                    
                                    $pourcentageCalcule += ($pourcentageQuestionnaire * $ponderation) / 100;
                                    
                                    if (!isset($questionnairesParType[$type])) {
                                        $questionnairesParType[$type] = [
                                            'completes' => 0,
                                            'total' => 0,
                                            'pourcentage' => 0
                                        ];
                                    }
                                    
                                    if ($questionsReponduesCorrectement === $totalQuestionsQuestionnaire) {
                                        $questionnairesParType[$type]['completes']++;
                                    }
                                    $questionnairesParType[$type]['total']++;
                                    $questionnairesParType[$type]['pourcentage'] += $pourcentageQuestionnaire;
                                }
                                
                                // Normaliser les pourcentages par type
                                foreach ($questionnairesParType as $type => $data) {
                                    if ($data['total'] > 0) {
                                        $questionnairesParType[$type]['pourcentage'] = $data['pourcentage'] / $data['total'];
                                    }
                                }
                                
                                $moduleComplet = ($questionnairesCompletes === $totalQuestionnaires);
                                $moduleValide = ($moduleComplet && $pourcentageCalcule >= 60);
                                $pourcentage = $pourcentageCalcule; // Utiliser le pourcentage pondéré
                            }
                            
                            $moduleProgression['pourcentage'] = round($pourcentage, 2);
                            $moduleProgression['questionnaires_completes'] = $questionnairesCompletes;
                            $moduleProgression['total_questionnaires'] = $totalQuestionnaires;
                            $moduleProgression['module_valide'] = $moduleValide ?? false;
                            $moduleProgression['module_complet'] = $moduleComplet ?? false;
                            
                            $totalPourcentage += $pourcentage;
                            if (($moduleValide ?? false)) {
                                $modulesCompletes++;
                            }
                        }

                        $modulesProgressions[] = $moduleProgression;
                    }

                    // Calculer la progression globale du niveau
                    $progressionGlobale = ($totalModulesApprenant > 0) ? ($totalPourcentage / $totalModulesApprenant) : 0;
                    $progressionNiveau = ($totalModulesApprenant > 0) ? ($modulesCompletes / $totalModulesApprenant) * 100 : 0;

                    $apprenantsNiveau[] = [
                        'apprenant_id' => $apprenant->id,
                        'utilisateur_id' => $apprenant->utilisateur_id,
                        'nom' => $apprenant->utilisateur->nom,
                        'prenom' => $apprenant->utilisateur->prenom,
                        'email' => $apprenant->utilisateur->email,
                        'telephone' => $apprenant->utilisateur->telephone,
                        'sexe' => $apprenant->utilisateur->sexe,
                        'categorie' => $apprenant->utilisateur->categorie,
                        'actif' => $apprenant->utilisateur->actif,
                        'date_inscription' => $apprenant->created_at,
                        'niveau_coran' => $apprenant->niveau_coran,
                        'niveau_arabe' => $apprenant->niveau_arabe,
                        'connaissance_adis' => $apprenant->connaissance_adis,
                        'formation_adis' => $apprenant->formation_adis,
                        'formation_autre' => $apprenant->formation_autre,
                        'modules_progression' => $modulesProgressions,
                        'progression_globale' => round($progressionGlobale, 2),
                        'progression_niveau' => round($progressionNiveau, 2),
                        'modules_completes' => $modulesCompletes,
                        'total_modules' => $totalModulesApprenant,
                        'derniere_activite' => $this->getDerniereActivite($apprenant)
                    ];
                }

                // Trier les apprenants du niveau
                if ($tri === 'nom') {
                    usort($apprenantsNiveau, function($a, $b) use ($ordre) {
                        $comparison = strcmp($a['nom'], $b['nom']);
                        return $ordre === 'desc' ? -$comparison : $comparison;
                    });
                } elseif ($tri === 'niveau') {
                    usort($apprenantsNiveau, function($a, $b) use ($ordre) {
                        $comparison = $a['progression_niveau'] <=> $b['progression_niveau'];
                        return $ordre === 'desc' ? -$comparison : $comparison;
                    });
                } elseif ($tri === 'date_inscription') {
                    usort($apprenantsNiveau, function($a, $b) use ($ordre) {
                        $comparison = strtotime($a['date_inscription']) <=> strtotime($b['date_inscription']);
                        return $ordre === 'desc' ? -$comparison : $comparison;
                    });
                }

                $progressionsParNiveau[] = [
                    'niveau' => [
                        'id' => $niveau->id,
                        'nom' => $niveau->nom,
                        'description' => $niveau->description,
                        'ordre' => $niveau->ordre,
                        'actif' => $niveau->actif,
                        'lien_meet' => $niveau->lien_meet,
                        'session_id' => $niveau->session_id
                    ],
                    'apprenants' => $apprenantsNiveau,
                    'total_apprenants' => count($apprenantsNiveau),
                    'moyenne_progression' => count($apprenantsNiveau) > 0 ? 
                        round(array_sum(array_column($apprenantsNiveau, 'progression_niveau')) / count($apprenantsNiveau), 2) : 0
                ];
            }

            // Calculer les statistiques globales
            $tousApprenants = collect($progressionsParNiveau)->flatMap(function($niveau) {
                return $niveau['apprenants'];
            });
            $moyenneGlobale = $tousApprenants->count() > 0 ? 
                round($tousApprenants->avg('progression_globale'), 2) : 0;

            // Trouver le meilleur apprenant
            $meilleurApprenant = $tousApprenants->sortByDesc('progression_globale')->first();

            return response()->json([
                'success' => true,
                'formateur' => [
                    'id' => $formateur->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'specialite' => $formateur->specialite,
                    'valide' => $formateur->valide
                ],
                'filtres_appliques' => [
                    'niveau_id' => $niveauId,
                    'tri' => $tri,
                    'ordre' => $ordre
                ],
                'progressions_par_niveau' => $progressionsParNiveau,
                'statistiques_globales' => [
                    'total_niveaux' => $niveaux->count(),
                    'total_apprenants' => $totalApprenants,
                    'total_modules' => $totalModules,
                    'moyenne_globale' => $moyenneGlobale,
                    'meilleur_apprenant' => $meilleurApprenant ? [
                        'nom' => $meilleurApprenant['nom'],
                        'prenom' => $meilleurApprenant['prenom'],
                        'progression_globale' => $meilleurApprenant['progression_globale'],
                        'niveau' => $meilleurApprenant['niveau_coran'] ?? 'Non défini'
                    ] : null
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des progressions: ' . $e->getMessage()
            ], 500);
        }
    }
}
