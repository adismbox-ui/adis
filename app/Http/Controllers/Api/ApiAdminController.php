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
use Illuminate\Support\Facades\Hash;

class ApiAdminController extends Controller
{
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
}

