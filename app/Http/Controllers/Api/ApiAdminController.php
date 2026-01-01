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
    public function getDemandesPaiementParStatut(Request $request, $statut)
    {
        $user = $request->user();
        
        if ($user->type_compte !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé'
            ], 403);
        }

        // Statuts valides (normaliser les statuts)
        $statutsMapping = [
            'en_attente' => 'en_attente',
            'valide' => 'valide',
            'validee' => 'valide',
            'refuse' => 'refuse',
            'refusee' => 'refuse',
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
}

