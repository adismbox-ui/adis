<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utilisateur;
use App\Models\Apprenant;
use App\Models\Formateur;
use App\Models\Niveau;
use App\Models\Module;
use App\Models\Assistant;
use App\Models\DemandeCoursMaison;
use App\Models\Paiement;
use App\Models\Certificat;
use App\Models\LienSocial;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ApiAdminController extends Controller
{
    /**
     * Récupère le profil de l'admin
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'sexe' => $user->sexe,
                'telephone' => $user->telephone,
                'type_compte' => $user->type_compte,
                'actif' => $user->actif,
                'email_verified_at' => $user->email_verified_at,
            ],
        ], 200);
    }

    /**
     * Met à jour le profil de l'admin
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $data = $request->validate([
            'prenom' => 'sometimes|string|max:255',
            'nom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:utilisateurs,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'sexe' => 'sometimes|in:Homme,Femme',
        ]);

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'sexe' => $user->sexe,
                'telephone' => $user->telephone,
                'type_compte' => $user->type_compte,
            ],
        ], 200);
    }

    /**
     * Récupère les statistiques admin
     */
    public function getStatistiques(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $stats = [
            'total_utilisateurs' => Utilisateur::count(),
            'total_apprenants' => Apprenant::count(),
            'total_formateurs' => Formateur::count(),
            'total_niveaux' => Niveau::where('actif', true)->count(),
            'total_modules' => Module::count(),
        ];

        return response()->json([
            'success' => true,
            'statistiques' => $stats,
        ], 200);
    }

    /**
     * Récupère la liste des utilisateurs
     */
    public function getUtilisateurs(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $utilisateurs = Utilisateur::with(['apprenant', 'formateur', 'assistant'])->get();

        $utilisateursFormates = $utilisateurs->map(function ($utilisateur) {
            return [
                'id' => $utilisateur->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'email' => $utilisateur->email,
                'type_compte' => $utilisateur->type_compte,
                'actif' => $utilisateur->actif,
                'telephone' => $utilisateur->telephone,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $utilisateursFormates,
        ], 200);
    }

    /**
     * Ajoute un utilisateur
     */
    public function addUser(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,formateur,apprenant,assistant',
        ]);

        $utilisateur = Utilisateur::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'mot_de_passe' => Hash::make($data['password']),
            'type_compte' => $data['role'],
            'actif' => true,
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès',
            'user' => [
                'id' => $utilisateur->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'email' => $utilisateur->email,
                'type_compte' => $utilisateur->type_compte,
            ],
        ], 201);
    }

    /**
     * Récupère la liste des apprenants
     */
    public function getApprenants(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $apprenants = Apprenant::with(['utilisateur', 'niveau'])->get();

        $apprenantsFormates = $apprenants->map(function ($apprenant) {
            return [
                'id' => $apprenant->id,
                'utilisateur' => [
                    'id' => $apprenant->utilisateur->id,
                    'nom' => $apprenant->utilisateur->nom,
                    'prenom' => $apprenant->utilisateur->prenom,
                    'email' => $apprenant->utilisateur->email,
                    'telephone' => $apprenant->utilisateur->telephone,
                ],
                'niveau' => $apprenant->niveau ? [
                    'id' => $apprenant->niveau->id,
                    'nom' => $apprenant->niveau->nom,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'apprenants' => $apprenantsFormates,
        ], 200);
    }

    /**
     * Récupère la liste des formateurs
     */
    public function getFormateurs(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $formateurs = Formateur::with(['utilisateur', 'niveaux'])->get();

        $formateursFormates = $formateurs->map(function ($formateur) {
            return [
                'id' => $formateur->id,
                'utilisateur' => [
                    'id' => $formateur->utilisateur->id,
                    'nom' => $formateur->utilisateur->nom,
                    'prenom' => $formateur->utilisateur->prenom,
                    'email' => $formateur->utilisateur->email,
                ],
                'valide' => $formateur->valide,
                'specialite' => $formateur->specialite,
            ];
        });

        return response()->json([
            'success' => true,
            'formateurs' => $formateursFormates,
        ], 200);
    }

    /**
     * Récupère la liste des niveaux
     */
    public function getNiveaux(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $niveaux = Niveau::where('actif', true)->orderBy('ordre')->get();

        $niveauxFormates = $niveaux->map(function ($niveau) {
            return [
                'id' => $niveau->id,
                'nom' => $niveau->nom,
                'description' => $niveau->description,
                'ordre' => $niveau->ordre,
            ];
        });

        return response()->json([
            'success' => true,
            'niveaux' => $niveauxFormates,
        ], 200);
    }

    /**
     * Récupère les apprenants d'un niveau
     */
    public function getApprenantsByNiveau(Request $request, $niveauId)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $apprenants = Apprenant::where('niveau_id', $niveauId)
            ->with(['utilisateur', 'niveau'])
            ->get();

        $apprenantsFormates = $apprenants->map(function ($apprenant) {
            return [
                'id' => $apprenant->id,
                'utilisateur' => [
                    'id' => $apprenant->utilisateur->id,
                    'nom' => $apprenant->utilisateur->nom,
                    'prenom' => $apprenant->utilisateur->prenom,
                    'email' => $apprenant->utilisateur->email,
                ],
                'niveau' => $apprenant->niveau ? [
                    'id' => $apprenant->niveau->id,
                    'nom' => $apprenant->niveau->nom,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'apprenants' => $apprenantsFormates,
        ], 200);
    }

    /**
     * Change le niveau d'un apprenant
     */
    public function changerNiveauApprenant(Request $request, $apprenantId)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $data = $request->validate([
            'nouveau_niveau_id' => 'required|exists:niveaux,id',
        ]);

        $apprenant = Apprenant::find($apprenantId);

        if (!$apprenant) {
            return response()->json([
                'success' => false,
                'error' => 'Apprenant non trouvé'
            ], 404);
        }

        $apprenant->niveau_id = $data['nouveau_niveau_id'];
        $apprenant->save();

        return response()->json([
            'success' => true,
            'message' => 'Niveau de l\'apprenant modifié avec succès',
            'apprenant' => [
                'id' => $apprenant->id,
                'niveau' => $apprenant->niveau ? [
                    'id' => $apprenant->niveau->id,
                    'nom' => $apprenant->niveau->nom,
                ] : null,
            ],
        ], 200);
    }

    /**
     * Récupère les formateurs avec profil assistant
     */
    public function formateursAvecProfilAssistant(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $formateurs = Formateur::with(['utilisateur'])
            ->whereHas('utilisateur', function($query) {
                $query->where('type_compte', 'formateur');
            })
            ->whereHas('assistant')
            ->get();

        $formateursFormates = $formateurs->map(function ($formateur) {
            $assistant = $formateur->assistant;
            return [
                'id' => $formateur->id,
                'utilisateur' => [
                    'id' => $formateur->utilisateur->id,
                    'nom' => $formateur->utilisateur->nom,
                    'prenom' => $formateur->utilisateur->prenom,
                    'email' => $formateur->utilisateur->email,
                    'type_compte' => $formateur->utilisateur->type_compte,
                ],
                'specialite' => $formateur->specialite,
                'valide' => $formateur->valide,
                'profil_assistant' => $assistant ? [
                    'id' => $assistant->id,
                    'actif' => $assistant->actif ?? true,
                    'created_at' => $assistant->created_at,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Formateurs avec profil assistant récupérés avec succès',
            'total_formateurs' => $formateurs->count(),
            'formateurs' => $formateursFormates,
        ], 200);
    }

    /**
     * Transforme un formateur en assistant
     */
    public function devenirAssistant(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $formateur = Formateur::with('utilisateur')->find($id);

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Formateur non trouvé'
            ], 404);
        }

        // Vérifier si l'assistant existe déjà
        $assistant = Assistant::where('formateur_id', $formateur->id)->first();

        if (!$assistant) {
            $assistant = Assistant::create([
                'formateur_id' => $formateur->id,
                'actif' => true,
            ]);
        } else {
            $assistant->actif = true;
            $assistant->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil assistant créé avec succès pour ce formateur',
            'formateur' => [
                'id' => $formateur->id,
                'utilisateur' => [
                    'id' => $formateur->utilisateur->id,
                    'nom' => $formateur->utilisateur->nom,
                    'prenom' => $formateur->utilisateur->prenom,
                ],
            ],
            'profil_assistant' => [
                'id' => $assistant->id,
                'actif' => $assistant->actif,
            ],
            'created_by' => $user->prenom . ' ' . $user->nom,
            'created_at' => $assistant->created_at,
        ], 200);
    }

    /**
     * Récupère les modules
     */
    public function getModules(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $modules = Module::with(['niveau', 'formateur.utilisateur'])->get();

        $modulesFormates = $modules->map(function ($module) {
            return [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'prix' => $module->prix,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom,
                ] : null,
                'formateur' => $module->formateur && $module->formateur->utilisateur ? [
                    'id' => $module->formateur->id,
                    'nom' => $module->formateur->utilisateur->nom,
                    'prenom' => $module->formateur->utilisateur->prenom,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'modules' => $modulesFormates,
        ], 200);
    }

    /**
     * Crée un module
     */
    public function createModule(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'niveau_id' => 'required|exists:niveaux,id',
            'formateur_id' => 'required|exists:formateurs,id',
            'prix' => 'nullable|numeric',
        ]);

        $module = Module::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Module créé avec succès',
            'module' => $module,
        ], 201);
    }

    /**
     * Met à jour un module
     */
    public function updateModule(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $module = Module::find($id);

        if (!$module) {
            return response()->json([
                'success' => false,
                'error' => 'Module non trouvé'
            ], 404);
        }

        $data = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'nullable|numeric',
        ]);

        $module->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Module mis à jour avec succès',
            'module' => $module,
        ], 200);
    }

    /**
     * Supprime un module
     */
    public function deleteModule(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $module = Module::find($id);

        if (!$module) {
            return response()->json([
                'success' => false,
                'error' => 'Module non trouvé'
            ], 404);
        }

        $module->delete();

        return response()->json([
            'success' => true,
            'message' => 'Module supprimé avec succès',
        ], 200);
    }

    /**
     * Récupère les types d'utilisateurs disponibles
     */
    public function getUtilisateursTypes(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $types = [
            [
                'value' => 'admin',
                'label' => 'Admin',
                'icon' => 'shield',
            ],
            [
                'value' => 'apprenant',
                'label' => 'Apprenant',
                'icon' => 'person',
            ],
            [
                'value' => 'formateur',
                'label' => 'Formateur',
                'icon' => 'graduation-cap',
            ],
            [
                'value' => 'assistant',
                'label' => 'Assistant',
                'icon' => 'user-tie',
            ],
        ];

        return response()->json([
            'success' => true,
            'types' => $types,
        ], 200);
    }

    /**
     * Récupère les demandes de cours à domicile par année
     */
    public function getDemandesCoursDomicileParAnnee(Request $request, $annee = null)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        // Si aucune année n'est fournie, utiliser l'année actuelle
        if (!$annee) {
            $annee = date('Y');
        }

        // Récupérer les demandes pour l'année spécifiée
        $demandes = DemandeCoursMaison::whereYear('created_at', $annee)
            ->with(['user', 'niveau', 'formateur.utilisateur'])
            ->orderBy('created_at', 'desc')
            ->get();

        $demandesFormatees = $demandes->map(function ($demande) {
            return [
                'id' => $demande->id,
                'user' => [
                    'id' => $demande->user->id,
                    'nom' => $demande->user->nom,
                    'prenom' => $demande->user->prenom,
                    'email' => $demande->user->email,
                ],
                'niveau' => $demande->niveau ? [
                    'id' => $demande->niveau->id,
                    'nom' => $demande->niveau->nom,
                ] : null,
                'module' => $demande->module,
                'nombre_enfants' => $demande->nombre_enfants,
                'ville' => $demande->ville,
                'commune' => $demande->commune,
                'quartier' => $demande->quartier,
                'numero' => $demande->numero,
                'statut' => $demande->statut,
                'message' => $demande->message,
                'formateur' => $demande->formateur && $demande->formateur->utilisateur ? [
                    'id' => $demande->formateur->id,
                    'nom' => $demande->formateur->utilisateur->nom,
                    'prenom' => $demande->formateur->utilisateur->prenom,
                ] : null,
                'created_at' => $demande->created_at,
                'updated_at' => $demande->updated_at,
            ];
        });

        // Statistiques par statut
        $stats = [
            'total' => $demandes->count(),
            'en_attente' => $demandes->where('statut', 'en_attente')->count(),
            'validee' => $demandes->where('statut', 'validee')->count(),
            'en_attente_formateur' => $demandes->where('statut', 'en_attente_formateur')->count(),
            'refusee' => $demandes->where('statut', 'refusee')->count(),
            'acceptee_formateur' => $demandes->where('statut', 'acceptee_formateur')->count(),
            'refusee_formateur' => $demandes->where('statut', 'refusee_formateur')->count(),
        ];

        return response()->json([
            'success' => true,
            'annee' => $annee,
            'statistiques' => $stats,
            'demandes' => $demandesFormatees,
        ], 200);
    }

    /**
     * Récupère les demandes de paiement par statut
     */
    public function getDemandesPaiementParStatut(Request $request, $statut = null)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        // Si le statut n'est pas fourni, essayer de le récupérer depuis l'URL
        if ($statut === null) {
            $path = $request->path();
            if (str_contains($path, 'refusees')) {
                $statut = 'refusees';
            } elseif (str_contains($path, 'en_attente')) {
                $statut = 'en_attente';
            } elseif (str_contains($path, 'validees')) {
                $statut = 'validees';
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Statut non spécifié'
                ], 400);
            }
        }

        // Statuts valides (normaliser les statuts)
        $statutsMapping = [
            'en_attente' => 'en_attente',
            'valide' => 'valide',
            'validee' => 'valide',
            'validees' => 'valide',
            'refuse' => 'refuse',
            'refusee' => 'refuse',
            'refusees' => 'refuse',
            'annule' => 'annule',
            'annulee' => 'annule',
        ];
        
        $statutNormalise = $statutsMapping[$statut] ?? $statut;
        
        // Récupérer les paiements avec le statut spécifié
        $paiements = Paiement::where('statut', $statutNormalise)
            ->with(['apprenant.utilisateur', 'module'])
            ->orderBy('created_at', 'desc')
            ->get();

        $paiementsFormates = $paiements->map(function ($paiement) {
            return [
                'id' => $paiement->id,
                'montant' => $paiement->montant,
                'statut' => $paiement->statut,
                'methode_paiement' => $paiement->methode ?? null,
                'date_paiement' => $paiement->date_paiement,
                'reference' => $paiement->reference,
                'notes' => $paiement->notes,
                'apprenant' => $paiement->apprenant && $paiement->apprenant->utilisateur ? [
                    'id' => $paiement->apprenant->utilisateur->id,
                    'nom' => $paiement->apprenant->utilisateur->nom,
                    'prenom' => $paiement->apprenant->utilisateur->prenom,
                    'email' => $paiement->apprenant->utilisateur->email,
                ] : null,
                'module' => $paiement->module ? [
                    'id' => $paiement->module->id,
                    'titre' => $paiement->module->titre,
                ] : null,
                'created_at' => $paiement->created_at,
                'updated_at' => $paiement->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'statut' => $statut,
            'statut_normalise' => $statutNormalise,
            'total' => $paiements->count(),
            'paiements' => $paiementsFormates,
        ], 200);
    }

    /**
     * Récupère les apprenants avec certificats pour un niveau donné
     */
    public function getApprenantsAvecCertificats(Request $request, $niveauId)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $apprenants = Apprenant::where('niveau_id', $niveauId)
            ->whereHas('certificats')
            ->with(['utilisateur', 'niveau', 'certificats'])
            ->get();

        $apprenantsFormates = $apprenants->map(function ($apprenant) {
            return [
                'id' => $apprenant->id,
                'utilisateur' => [
                    'id' => $apprenant->utilisateur->id,
                    'nom' => $apprenant->utilisateur->nom,
                    'prenom' => $apprenant->utilisateur->prenom,
                    'email' => $apprenant->utilisateur->email,
                ],
                'niveau' => $apprenant->niveau ? [
                    'id' => $apprenant->niveau->id,
                    'nom' => $apprenant->niveau->nom,
                ] : null,
                'certificats' => $apprenant->certificats->map(function ($certificat) {
                    return [
                        'id' => $certificat->id,
                        'titre' => $certificat->titre,
                        'date_obtention' => $certificat->date_obtention,
                        'fichier' => $certificat->fichier ? url('/storage/' . $certificat->fichier) : null,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'niveau_id' => $niveauId,
            'total' => $apprenants->count(),
            'apprenants' => $apprenantsFormates,
        ], 200);
    }

    /**
     * Récupère les apprenants payants organisés par niveau
     */
    public function getApprenantsPayants(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        // Récupérer les apprenants qui ont au moins un paiement validé
        $apprenants = Apprenant::whereHas('paiements', function($query) {
                $query->where('statut', 'valide');
            })
            ->with(['utilisateur', 'niveau', 'paiements' => function($query) {
                $query->where('statut', 'valide');
            }])
            ->get();

        // Organiser par niveau
        $apprenantsParNiveau = [];
        foreach ($apprenants as $apprenant) {
            $niveauId = $apprenant->niveau_id ?? 'sans_niveau';
            $niveauNom = $apprenant->niveau ? $apprenant->niveau->nom : 'Sans niveau';
            
            if (!isset($apprenantsParNiveau[$niveauId])) {
                $apprenantsParNiveau[$niveauId] = [
                    'niveau_id' => $apprenant->niveau_id,
                    'niveau_nom' => $niveauNom,
                    'apprenants' => [],
                ];
            }
            
            $apprenantsParNiveau[$niveauId]['apprenants'][] = [
                'id' => $apprenant->id,
                'utilisateur' => [
                    'id' => $apprenant->utilisateur->id,
                    'nom' => $apprenant->utilisateur->nom,
                    'prenom' => $apprenant->utilisateur->prenom,
                    'email' => $apprenant->utilisateur->email,
                ],
                'paiements_valides' => $apprenant->paiements->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'apprenants_par_niveau' => array_values($apprenantsParNiveau),
            'total' => $apprenants->count(),
        ], 200);
    }

    /**
     * Récupère les apprenants non payants organisés par niveau
     */
    public function getApprenantsNonPayants(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        // Récupérer les apprenants qui n'ont pas de paiement validé
        $apprenants = Apprenant::whereNotNull('niveau_id')
            ->whereDoesntHave('paiements', function($query) {
                $query->where('statut', 'valide');
            })
            ->with(['utilisateur', 'niveau'])
            ->get();

        // Organiser par niveau
        $apprenantsParNiveau = [];
        foreach ($apprenants as $apprenant) {
            $niveauId = $apprenant->niveau_id ?? 'sans_niveau';
            $niveauNom = $apprenant->niveau ? $apprenant->niveau->nom : 'Sans niveau';
            
            if (!isset($apprenantsParNiveau[$niveauId])) {
                $apprenantsParNiveau[$niveauId] = [
                    'niveau_id' => $apprenant->niveau_id,
                    'niveau_nom' => $niveauNom,
                    'apprenants' => [],
                ];
            }
            
            $apprenantsParNiveau[$niveauId]['apprenants'][] = [
                'id' => $apprenant->id,
                'utilisateur' => [
                    'id' => $apprenant->utilisateur->id,
                    'nom' => $apprenant->utilisateur->nom,
                    'prenom' => $apprenant->utilisateur->prenom,
                    'email' => $apprenant->utilisateur->email,
                ],
            ];
        }

        return response()->json([
            'success' => true,
            'apprenants_par_niveau' => array_values($apprenantsParNiveau),
            'total' => $apprenants->count(),
        ], 200);
    }

    /**
     * Récupère la liste des assistants
     */
    public function getAssistants(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        // Récupérer tous les assistants actifs
        $assistantsFromTable = Assistant::with('utilisateur', 'formateur.utilisateur')
            ->where('actif', true)
            ->get()
            ->map(function($assistant) {
                return $assistant->utilisateur;
            });
            
        // Ajouter aussi les utilisateurs avec type_compte = 'assistant'
        $assistantsDirect = Utilisateur::where('type_compte', 'assistant')
            ->whereNotIn('id', $assistantsFromTable->pluck('id'))
            ->get();
            
        $assistants = $assistantsFromTable->merge($assistantsDirect)->sortByDesc('created_at');

        $assistantsFormates = $assistants->map(function ($assistant) {
            $assistantRecord = Assistant::where('utilisateur_id', $assistant->id)->first();
            return [
                'id' => $assistant->id,
                'nom' => $assistant->nom,
                'prenom' => $assistant->prenom,
                'email' => $assistant->email,
                'telephone' => $assistant->telephone,
                'sexe' => $assistant->sexe,
                'actif' => $assistant->actif ? 1 : 0,
                'formateur_id' => $assistantRecord ? $assistantRecord->formateur_id : null,
                'created_at' => $assistant->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'assistants' => [
                'data' => $assistantsFormates->values(),
            ],
        ], 200);
    }

    /**
     * Crée un nouveau formateur
     */
    public function createFormateur(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        try {
            $data = $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:utilisateurs,email',
                'telephone' => 'nullable|string|max:20',
                'sexe' => 'required|in:Homme,Femme',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|same:password',
                'niveau_id' => 'nullable|exists:niveaux,id',
                'ville' => 'nullable|string|max:255',
                'commune' => 'nullable|string|max:255',
                'quartier' => 'nullable|string|max:255',
            ]);

            // Créer l'utilisateur
            $utilisateur = Utilisateur::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'telephone' => $data['telephone'] ?? null,
                'sexe' => $data['sexe'],
                'mot_de_passe' => Hash::make($data['password']),
                'password' => Hash::make($data['password']), // Pour compatibilité
                'type_compte' => 'formateur',
                'actif' => true,
                'email_verified_at' => now(),
            ]);

            // Créer le formateur
            $formateur = Formateur::create([
                'utilisateur_id' => $utilisateur->id,
                'valide' => true,
                'ville' => $data['ville'] ?? null,
                'commune' => $data['commune'] ?? null,
                'quartier' => $data['quartier'] ?? null,
            ]);

            // Assigner le niveau si fourni
            if (isset($data['niveau_id'])) {
                $niveau = Niveau::find($data['niveau_id']);
                if ($niveau) {
                    $niveau->formateur_id = $formateur->id;
                    $niveau->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Formateur créé avec succès',
                'formateur' => [
                    'id' => $formateur->id,
                    'utilisateur' => [
                        'id' => $utilisateur->id,
                        'nom' => $utilisateur->nom,
                        'prenom' => $utilisateur->prenom,
                        'email' => $utilisateur->email,
                    ],
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création du formateur', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la création du formateur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprime un formateur
     */
    public function deleteFormateur(Request $request, $utilisateurId)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        try {
            $utilisateur = Utilisateur::findOrFail($utilisateurId);
            
            if ($utilisateur->type_compte !== 'formateur') {
                return response()->json([
                    'success' => false,
                    'error' => 'Cet utilisateur n\'est pas un formateur'
                ], 400);
            }

            // Supprimer le formateur associé
            $formateur = Formateur::where('utilisateur_id', $utilisateurId)->first();
            if ($formateur) {
                $formateur->delete();
            }

            // Supprimer l'utilisateur
            $utilisateur->delete();

            return response()->json([
                'success' => true,
                'message' => 'Formateur supprimé avec succès',
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du formateur', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la suppression du formateur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère la liste des liens sociaux
     */
    public function getLiensSociaux(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        $liens = LienSocial::where('actif', true)
            ->orderBy('ordre')
            ->orderBy('created_at', 'desc')
            ->get();

        $liensFormates = $liens->map(function ($lien) {
            return [
                'id' => $lien->id,
                'plateforme' => $lien->plateforme,
                'titre' => $lien->titre,
                'description' => $lien->description,
                'url' => $lien->url,
                'actif' => $lien->actif,
                'ordre' => $lien->ordre,
                'created_at' => $lien->created_at,
                'updated_at' => $lien->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'liens' => $liensFormates,
        ], 200);
    }

    /**
     * Crée un nouveau lien social
     */
    public function createLienSocial(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        try {
            $data = $request->validate([
                'plateforme' => 'required|string|max:255',
                'titre' => 'required|string|max:255',
                'description' => 'nullable|string',
                'url' => 'required|url|max:500',
                'actif' => 'nullable|boolean',
                'ordre' => 'nullable|integer|min:0',
            ]);

            $data['actif'] = $data['actif'] ?? true;
            $data['ordre'] = $data['ordre'] ?? 0;

            $lien = LienSocial::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Lien social créé avec succès',
                'lien' => [
                    'id' => $lien->id,
                    'plateforme' => $lien->plateforme,
                    'titre' => $lien->titre,
                    'description' => $lien->description,
                    'url' => $lien->url,
                    'actif' => $lien->actif,
                    'ordre' => $lien->ordre,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création du lien social', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la création du lien social: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour un lien social
     */
    public function updateLienSocial(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        try {
            $lien = LienSocial::findOrFail($id);

            $data = $request->validate([
                'plateforme' => 'sometimes|string|max:255',
                'titre' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'url' => 'sometimes|url|max:500',
                'actif' => 'nullable|boolean',
                'ordre' => 'nullable|integer|min:0',
            ]);

            $lien->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Lien social mis à jour avec succès',
                'lien' => [
                    'id' => $lien->id,
                    'plateforme' => $lien->plateforme,
                    'titre' => $lien->titre,
                    'description' => $lien->description,
                    'url' => $lien->url,
                    'actif' => $lien->actif,
                    'ordre' => $lien->ordre,
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Lien social non trouvé'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour du lien social', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la mise à jour du lien social: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprime un lien social
     */
    public function deleteLienSocial(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        try {
            $lien = LienSocial::findOrFail($id);
            $lien->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lien social supprimé avec succès',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Lien social non trouvé'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du lien social', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la suppression du lien social: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère tous les liens sociaux (pour les apprenants)
     */
    public function getAllLiensSociaux(Request $request)
    {
        $liens = LienSocial::where('actif', true)
            ->orderBy('ordre')
            ->orderBy('created_at', 'desc')
            ->get();

        $liensFormates = $liens->map(function ($lien) {
            return [
                'id' => $lien->id,
                'plateforme' => $lien->plateforme,
                'titre' => $lien->titre,
                'description' => $lien->description,
                'url' => $lien->url,
            ];
        });

        return response()->json([
            'success' => true,
            'liens' => $liensFormates,
        ], 200);
    }
}

