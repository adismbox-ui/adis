<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Formateur;
use App\Models\Niveau;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ModuleApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index1()
    {
        $user = auth()->user();
        $modules = collect();
        if ($user && $user->formateur) {
            $modules = $user->formateur->modules()->with('niveau')->get();
        }
        return response()->json(['modules' => $modules], 200);
    }

    public function index2()
    {
        // Vérifier si l'utilisateur est admin
        $user = auth()->user();
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Non autorisé'], 403);
        }
        
        $modules = Module::with(['niveau'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return response()->json(['modules' => $modules], 200);
    }
    
    public function index()
    {
        // Vérifier si l'utilisateur est admin
        $user = auth()->user();
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $modules = Module::with(['niveau'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['modules' => $modules], 200);
    }

    public function listeModulesDetailsAdmin()
    {
        $user = auth()->user();
        // Optionnel : vérifier que c'est un admin
        // if (!$user || $user->type_compte !== 'admin') {
        //     return response()->json(['error' => 'Non autorisé'], 403);
        // }

        $modules = \App\Models\Module::with(['niveau'])->get();

        $result = $modules->map(function($module) {
            return [
                'titre' => $module->titre,
                'niveau' => $module->niveau ? $module->niveau->nom : null,
                'date_debut' => $module->date_debut,
                'date_fin' => $module->date_fin,
            ];
        });

        return response()->json(['modules' => $result], 200);
    }

    public function detailsModuleAdmin($id)
    {
        $user = auth()->user();
        // Optionnel : vérifier que c'est un admin
        // if (!$user || $user->type_compte !== 'admin') {
        //     return response()->json(['error' => 'Non autorisé'], 403);
        // }

        $module = \App\Models\Module::with(['niveau'])->find($id);
        if (!$module) {
            return response()->json(['error' => 'Module non trouvé'], 404);
        }
        
        $result = [
            'titre' => $module->titre,
            'niveau' => $module->niveau ? $module->niveau->nom : null,
            'date_debut' => $module->date_debut,
            'date_fin' => $module->date_fin,
        ];
        return response()->json(['module' => $result], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $niveaux = Niveau::where('actif', true)->orderBy('ordre')->get();
        return response()->json([
            'niveaux' => $niveaux
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'titre' => 'required|string|max:255',
                'niveau_id' => 'required|exists:niveaux,id',
                'prix' => 'required|integer|min:0',
                'description' => 'nullable|string',
            ]);

            $module = Module::create($data);
            return response()->json(['module' => $module, 'message' => 'Module créé avec succès'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur validation module: ' . json_encode($e->errors()));
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur création module: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création du module.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        return response()->json(['module' => $module], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        return response()->json(['module' => $module], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
        // Vérifier si l'utilisateur est admin
        $user = auth()->user();
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Non autorisé. Seuls les administrateurs peuvent modifier des modules.'], 403);
        }

        try {
            $data = $request->validate([
                'titre' => 'required|string|max:255',
                'niveau_id' => 'required|exists:niveaux,id',
                'prix' => 'required|integer|min:0',
                'description' => 'nullable|string',
                'certificat' => 'boolean',
            ]);

            // Mettre à jour le module
            $module->update($data);

            return response()->json([
                'success' => true,
                'module' => $module->load(['niveau']),
                'message' => 'Module mis à jour avec succès.'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur validation module update: ' . json_encode($e->errors()));
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Erreur mise à jour module: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la mise à jour du module.'], 500);
        }
    }

    /**
     * Récupérer les formateurs d'un module donné
     */
    public function getFormateursByModule($moduleId)
    {
        // Vérifier si l'utilisateur est admin
        $user = auth()->user();
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Non autorisé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'], 403);
        }

        try {
            // Vérifier si le module existe
            $module = Module::find($moduleId);
            if (!$module) {
                return response()->json([
                    'error' => 'Module non trouvé.'
                ], 404);
            }

            // Récupérer le formateur principal du module
            $formateurPrincipal = $module->formateur;
            
            // Récupérer tous les formateurs qui ont des modules dans la même discipline
            $formateursDiscipline = Formateur::whereHas('modules', function($query) use ($module) {
                $query->where('discipline', $module->discipline);
            })->with('utilisateur')->get();

            // Formater les données des formateurs
            $formateurs = collect();
            
            // Ajouter le formateur principal en premier
            if ($formateurPrincipal) {
                $formateurs->push([
                    'id' => $formateurPrincipal->id,
                    'nom_complet' => $formateurPrincipal->utilisateur ? 
                        $formateurPrincipal->utilisateur->prenom . ' ' . $formateurPrincipal->utilisateur->nom : 
                        'Nom non disponible',
                    'email' => $formateurPrincipal->utilisateur ? $formateurPrincipal->utilisateur->email : null,
                    'specialite' => $formateurPrincipal->specialite,
                    'ville' => $formateurPrincipal->ville,
                    'commune' => $formateurPrincipal->commune,
                    'quartier' => $formateurPrincipal->quartier,
                    'valide' => $formateurPrincipal->valide,
                    'is_principal' => true,
                    'modules_count' => $formateurPrincipal->modules()->count(),
                    'modules_discipline_count' => $formateurPrincipal->modules()->where('discipline', $module->discipline)->count()
                ]);
            }

            // Ajouter les autres formateurs de la même discipline
            foreach ($formateursDiscipline as $formateur) {
                if (!$formateurPrincipal || $formateur->id !== $formateurPrincipal->id) {
                    $formateurs->push([
                        'id' => $formateur->id,
                        'nom_complet' => $formateur->utilisateur ? 
                            $formateur->utilisateur->prenom . ' ' . $formateur->utilisateur->nom : 
                            'Nom non disponible',
                        'email' => $formateur->utilisateur ? $formateur->utilisateur->email : null,
                        'specialite' => $formateur->specialite,
                        'ville' => $formateur->ville,
                        'commune' => $formateur->commune,
                        'quartier' => $formateur->quartier,
                        'valide' => $formateur->valide,
                        'is_principal' => false,
                        'modules_count' => $formateur->modules()->count(),
                        'modules_discipline_count' => $formateur->modules()->where('discipline', $module->discipline)->count()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'module' => [
                    'id' => $module->id,
                    'titre' => $module->titre,
                    'discipline' => $module->discipline
                ],
                'formateurs' => $formateurs,
                'total_formateurs' => $formateurs->count(),
                'message' => 'Formateurs récupérés avec succès.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur récupération formateurs par module: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la récupération des formateurs.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        // Vérifier si l'utilisateur est admin
        $user = auth()->user();
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Non autorisé. Seuls les administrateurs peuvent supprimer des modules.'], 403);
        }

        try {
            // Vérifier s'il y a des inscriptions liées à ce module
            $inscriptionsCount = $module->inscriptions()->count();
            if ($inscriptionsCount > 0) {
                return response()->json([
                    'error' => 'Impossible de supprimer ce module car il y a ' . $inscriptionsCount . ' inscription(s) associée(s).'
                ], 422);
            }

            // Vérifier s'il y a des paiements liés à ce module
            $paiementsCount = $module->paiements()->count();
            if ($paiementsCount > 0) {
                return response()->json([
                    'error' => 'Impossible de supprimer ce module car il y a ' . $paiementsCount . ' paiement(s) associé(s).'
                ], 422);
            }

            // Vérifier s'il y a des documents liés à ce module
            $documentsCount = $module->documents()->count();
            if ($documentsCount > 0) {
                return response()->json([
                    'error' => 'Impossible de supprimer ce module car il y a ' . $documentsCount . ' document(s) associé(s).'
                ], 422);
            }

            // Supprimer le module
            $module->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Module supprimé avec succès.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur suppression module: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la suppression du module.'
            ], 500);
        }
    }

    /**
     * Récupérer tous les modules d'un niveau donné
     */
    public function getModulesByNiveau($niveauId)
    {
        try {
            // Vérifier si le niveau existe
            $niveau = \App\Models\Niveau::with(['formateur.utilisateur', 'sessionFormation'])
                ->find($niveauId);
            if (!$niveau) {
                return response()->json([
                    'error' => 'Niveau non trouvé.'
                ], 404);
            }

            // Récupérer tous les modules du niveau
            $modules = Module::where('niveau_id', $niveauId)
                ->orderBy('titre')
                ->get();

            // Formater la réponse
            $modulesFormatted = $modules->map(function($module) use ($niveau) {
                $formateurNom = null;
                $formateurInfo = null;
                
                if ($niveau->formateur && $niveau->formateur->utilisateur) {
                    $formateurNom = $niveau->formateur->utilisateur->prenom . ' ' . $niveau->formateur->utilisateur->nom;
                    $formateurInfo = [
                        'id' => $niveau->formateur->id,
                        'utilisateur_id' => $niveau->formateur->utilisateur_id,
                        'specialite' => $niveau->formateur->specialite,
                        'valide' => $niveau->formateur->valide,
                        'nom' => $niveau->formateur->utilisateur->nom,
                        'prenom' => $niveau->formateur->utilisateur->prenom,
                        'email' => $niveau->formateur->utilisateur->email,
                        'telephone' => $niveau->formateur->utilisateur->telephone
                    ];
                }

                return [
                    'id' => $module->id,
                    'titre' => $module->titre,
                    'discipline' => $module->discipline,
                    'niveau_id' => $module->niveau_id,
                    'niveau_nom' => $niveau->nom,
                    'date_debut' => $module->date_debut,
                    'date_fin' => $module->date_fin,
                    'formateur_id' => $niveau->formateur_id,
                    'formateur_nom' => $formateurNom,
                    'formateur_info' => $formateurInfo,
                    'prix' => $module->prix,
                    'description' => $module->description,
                    'horaire' => $module->horaire,
                    'certificat' => $module->certificat,
                    'created_at' => $module->created_at,
                    'updated_at' => $module->updated_at,
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
                    'formateur_id' => $niveau->formateur_id,
                    'lien_meet' => $niveau->lien_meet,
                    'session_formation' => $niveau->sessionFormation ? [
                        'id' => $niveau->sessionFormation->id,
                        'nom' => $niveau->sessionFormation->nom,
                        'date_debut' => $niveau->sessionFormation->date_debut,
                        'date_fin' => $niveau->sessionFormation->date_fin,
                    ] : null,
                ],
                'modules' => $modulesFormatted,
                'total_modules' => $modulesFormatted->count(),
                'message' => 'Modules récupérés avec succès.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur récupération modules par niveau: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la récupération des modules.'
            ], 500);
        }
    }
}
