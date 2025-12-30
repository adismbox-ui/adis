<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Inscription;

class ApiModuleController extends Controller
{
    /**
     * Récupère tous les modules
     */
    public function index(Request $request)
    {
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
            'data' => $modulesFormates,
        ], 200);
    }

    /**
     * Récupère les modules avec support (pour route publique)
     */
    public function getSupports(Request $request)
    {
        $modules = Module::whereNotNull('support')
            ->with(['niveau', 'formateur.utilisateur'])
            ->get();

        $supports = $modules->map(function ($module) {
            return [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'support' => $module->support,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $supports,
        ], 200);
    }

    /**
     * Récupère les modules de l'utilisateur connecté
     */
    public function getMesModules(Request $request)
    {
        $user = $request->user();
        
        if ($user->type_compte === 'apprenant' && $user->apprenant) {
            $inscriptions = Inscription::where('apprenant_id', $user->apprenant->id)
                ->where('statut', 'valide')
                ->with(['module.niveau', 'module.formateur.utilisateur'])
                ->get();

            $modules = $inscriptions->map(function ($inscription) {
                $module = $inscription->module;
                return [
                    'id' => $module->id,
                    'titre' => $module->titre,
                    'description' => $module->description,
                    'prix' => $module->prix,
                    'niveau' => $module->niveau ? [
                        'id' => $module->niveau->id,
                        'nom' => $module->niveau->nom,
                    ] : null,
                ];
            });
        } elseif ($user->type_compte === 'formateur' && $user->formateur) {
            $modules = Module::where('formateur_id', $user->formateur->id)
                ->with(['niveau'])
                ->get()
                ->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'description' => $module->description,
                        'prix' => $module->prix,
                        'niveau' => $module->niveau ? [
                            'id' => $module->niveau->id,
                            'nom' => $module->niveau->nom,
                        ] : null,
                    ];
                });
        } else {
            $modules = collect([]);
        }

        return response()->json([
            'success' => true,
            'data' => $modules->values(),
        ], 200);
    }

    /**
     * Récupère un module spécifique
     */
    public function show(Request $request, $id)
    {
        $module = Module::with(['niveau', 'formateur.utilisateur', 'documents', 'questionnaires'])->find($id);

        if (!$module) {
            return response()->json([
                'success' => false,
                'error' => 'Module non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'module' => [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'prix' => $module->prix,
                'date_debut' => $module->date_debut,
                'date_fin' => $module->date_fin,
                'horaire' => $module->horaire,
                'lien' => $module->lien,
                'support' => $module->support,
                'audio' => $module->audio,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom,
                ] : null,
                'formateur' => $module->formateur && $module->formateur->utilisateur ? [
                    'id' => $module->formateur->id,
                    'nom' => $module->formateur->utilisateur->nom,
                    'prenom' => $module->formateur->utilisateur->prenom,
                ] : null,
            ],
        ], 200);
    }
}

