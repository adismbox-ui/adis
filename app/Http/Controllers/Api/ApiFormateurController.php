<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Formateur;
use App\Models\Module;
use App\Models\Niveau;
use App\Models\Document;
use App\Models\Questionnaire;
use App\Models\Apprenant;
use App\Models\Inscription;
use App\Models\Vacance;
use App\Models\DemandeCoursMaison;
use App\Models\ReponseQuestionnaire;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiFormateurController extends Controller
{
    /**
     * Récupère le calendrier du formateur
     */
    public function getCalendrier(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $modules = Module::where('formateur_id', $formateur->id)
            ->with(['niveau', 'inscriptions.apprenant.utilisateur'])
            ->get();

        $calendrier = $modules->map(function ($module) {
            return [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'date_debut' => $module->date_debut,
                'date_fin' => $module->date_fin,
                'horaire' => $module->horaire,
                'lien' => $module->lien,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom,
                ] : null,
                'nombre_inscrits' => $module->inscriptions->where('statut', 'valide')->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'calendrier' => $calendrier,
        ], 200);
    }

    /**
     * Récupère les modules du formateur
     */
    public function getModules(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $modules = Module::where('formateur_id', $formateur->id)
            ->with(['niveau', 'documents', 'questionnaires'])
            ->get();

        $modulesFormates = $modules->map(function ($module) {
            return [
                'id' => $module->id,
                'titre' => $module->titre,
                'description' => $module->description,
                'date_debut' => $module->date_debut,
                'date_fin' => $module->date_fin,
                'prix' => $module->prix,
                'niveau' => $module->niveau ? [
                    'id' => $module->niveau->id,
                    'nom' => $module->niveau->nom,
                ] : null,
                'nombre_documents' => $module->documents->count(),
                'nombre_questionnaires' => $module->questionnaires->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'modules' => $modulesFormates,
        ], 200);
    }

    /**
     * Récupère les niveaux du formateur
     */
    public function getNiveaux(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $niveaux = Niveau::where('formateur_id', $formateur->id)
            ->where('actif', true)
            ->orderBy('ordre')
            ->get();

        $niveauxFormates = $niveaux->map(function ($niveau) {
            return [
                'id' => $niveau->id,
                'nom' => $niveau->nom,
                'description' => $niveau->description,
                'ordre' => $niveau->ordre,
                'lien_meet' => $niveau->lien_meet,
            ];
        });

        return response()->json([
            'success' => true,
            'niveaux' => $niveauxFormates,
        ], 200);
    }

    /**
     * Récupère les modules d'un niveau
     */
    public function getModulesParNiveau(Request $request, $niveauId)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $modules = Module::where('formateur_id', $formateur->id)
            ->where('niveau_id', $niveauId)
            ->with(['niveau', 'documents'])
            ->get();

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
            ];
        });

        return response()->json([
            'success' => true,
            'modules' => $modulesFormates,
        ], 200);
    }

    /**
     * Récupère le profil du formateur
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'telephone' => $user->telephone,
            ],
            'formateur' => [
                'id' => $formateur->id,
                'specialite' => $formateur->specialite,
                'valide' => $formateur->valide,
            ],
        ], 200);
    }

    /**
     * Met à jour le profil du formateur
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $data = $request->validate([
            'prenom' => 'sometimes|string|max:255',
            'nom' => 'sometimes|string|max:255',
            'telephone' => 'sometimes|string|max:20',
            'specialite' => 'sometimes|string|max:255',
        ]);

        $user->update($data);
        
        if (isset($data['specialite'])) {
            $formateur->specialite = $data['specialite'];
            $formateur->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
        ], 200);
    }

    /**
     * Récupère les apprenants du formateur
     */
    public function getApprenants(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        // Récupérer les modules du formateur
        $moduleIds = Module::where('formateur_id', $formateur->id)->pluck('id');

        // Récupérer les apprenants inscrits à ces modules
        $inscriptions = Inscription::whereIn('module_id', $moduleIds)
            ->where('statut', 'valide')
            ->with(['apprenant.utilisateur', 'module'])
            ->get();

        $apprenants = $inscriptions->map(function ($inscription) {
            return [
                'id' => $inscription->apprenant->id,
                'utilisateur' => [
                    'id' => $inscription->apprenant->utilisateur->id,
                    'nom' => $inscription->apprenant->utilisateur->nom,
                    'prenom' => $inscription->apprenant->utilisateur->prenom,
                    'email' => $inscription->apprenant->utilisateur->email,
                ],
                'module' => [
                    'id' => $inscription->module->id,
                    'titre' => $inscription->module->titre,
                ],
            ];
        })->unique('id')->values();

        return response()->json([
            'success' => true,
            'apprenants' => $apprenants,
        ], 200);
    }

    /**
     * Récupère les apprenants assignés aux niveaux du formateur
     */
    public function getApprenantsAssignes(Request $request)
    {
        try {
            $user = $request->user();
            $formateur = $user->formateur;

            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil formateur non trouvé'
                ], 404);
            }

            // Récupérer les niveaux assignés au formateur
            $niveaux = Niveau::where('formateur_id', $formateur->id)
                ->where('actif', true)
                ->pluck('id');

            if ($niveaux->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'apprenants' => [],
                    'message' => 'Aucun niveau assigné à ce formateur'
                ], 200);
            }

            // Récupérer les apprenants de ces niveaux
            $apprenants = Apprenant::whereIn('niveau_id', $niveaux)
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
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des apprenants assignés', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des apprenants assignés: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les documents du formateur
     */
    public function getDocuments(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }
        // Documents visibles par le formateur :
        // - documents de ses modules (module.formateur_id)
        // - documents des modules de ses niveaux (niveau.formateur_id)
        // - documents généraux de niveau (niveau_id défini, module_id NULL)
        $niveauIds = $formateur->niveaux()->pluck('id');
        $moduleIds = Module::query()
            ->where('formateur_id', $formateur->id)
            ->when($niveauIds->isNotEmpty(), function ($q) use ($niveauIds) {
                $q->orWhereIn('niveau_id', $niveauIds);
            })
            ->pluck('id');

        $documents = Document::query()
            ->where(function ($q) use ($moduleIds, $niveauIds, $formateur) {
                if ($moduleIds->isNotEmpty()) {
                    $q->whereIn('module_id', $moduleIds);
                } else {
                    $q->whereRaw('1=0');
                }

                if ($niveauIds->isNotEmpty()) {
                    $q->orWhere(function ($qq) use ($niveauIds) {
                        $qq->whereNull('module_id')
                           ->whereIn('niveau_id', $niveauIds);
                    });
                }

                // fallback: documents explicitement rattachés au formateur (si utilisés)
                $q->orWhere('formateur_id', $formateur->id);
            })
            ->with(['module', 'niveau'])
            ->get();

        $documentsFormates = $documents->map(function ($document) {
            return [
                'id' => $document->id,
                'titre' => $document->titre,
                'type' => $document->type,
                'fichier' => $document->fichier ? url('/storage/' . $document->fichier) : null,
                'module' => $document->module ? [
                    'id' => $document->module->id,
                    'titre' => $document->module->titre,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'documents' => $documentsFormates,
        ], 200);
    }

    /**
     * Upload un document
     */
    public function uploadDocument(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'type' => 'required|string',
            'module_id' => 'nullable|exists:modules,id',
            'fichier' => 'required|file',
        ]);

        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('documents', $filename, 'public');
            $data['fichier'] = $path;
        }

        $data['formateur_id'] = $formateur->id;
        $document = Document::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Document uploadé avec succès',
            'document' => $document,
        ], 201);
    }

    /**
     * Récupère les questionnaires du formateur
     */
    public function getQuestionnaires(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $moduleIds = Module::where('formateur_id', $formateur->id)->pluck('id');

        $questionnaires = Questionnaire::whereIn('module_id', $moduleIds)
            ->with(['module', 'questions'])
            ->get();

        $questionnairesFormates = $questionnaires->map(function ($questionnaire) {
            return [
                'id' => $questionnaire->id,
                'titre' => $questionnaire->titre,
                'description' => $questionnaire->description,
                'minutes' => $questionnaire->minutes,
                'module' => $questionnaire->module ? [
                    'id' => $questionnaire->module->id,
                    'titre' => $questionnaire->module->titre,
                ] : null,
                'nombre_questions' => $questionnaire->questions->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'questionnaires' => $questionnairesFormates,
        ], 200);
    }

    /**
     * Crée un questionnaire
     */
    public function createQuestionnaire(Request $request)
    {
        $user = $request->user();
        $formateur = $user->formateur;

        if (!$formateur) {
            return response()->json([
                'success' => false,
                'error' => 'Profil formateur non trouvé'
            ], 404);
        }

        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'required|exists:modules,id',
            'minutes' => 'nullable|integer',
        ]);

        // Vérifier que le module appartient au formateur
        $module = Module::where('id', $data['module_id'])
            ->where('formateur_id', $formateur->id)
            ->first();

        if (!$module) {
            return response()->json([
                'success' => false,
                'error' => 'Module non trouvé ou non autorisé'
            ], 404);
        }

        $questionnaire = Questionnaire::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Questionnaire créé avec succès',
            'questionnaire' => $questionnaire,
        ], 201);
    }

    /**
     * Récupère les documents du formateur (alias pour mes-documents)
     */
    public function getMesDocuments(Request $request)
    {
        return $this->getDocuments($request);
    }

    /**
     * Récupère les vacances
     */
    public function getVacances(Request $request)
    {
        try {
            $vacances = Vacance::orderBy('date_debut', 'desc')->get();
            
            $vacancesFormates = $vacances->map(function ($vacance) {
                return [
                    'id' => $vacance->id,
                    'nom' => $vacance->nom,
                    'description' => $vacance->description,
                    'date_debut' => $vacance->date_debut->format('Y-m-d'),
                    'date_fin' => $vacance->date_fin->format('Y-m-d'),
                    'actif' => $vacance->actif,
                ];
            });

            return response()->json([
                'success' => true,
                'vacances' => $vacancesFormates,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des vacances', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des vacances: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crée une vacance
     */
    public function createVacance(Request $request)
    {
        try {
            $data = $request->validate([
                'nom' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after_or_equal:date_debut',
                'actif' => 'boolean',
            ]);

            $vacance = Vacance::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Vacance créée avec succès',
                'vacance' => [
                    'id' => $vacance->id,
                    'nom' => $vacance->nom,
                    'description' => $vacance->description,
                    'date_debut' => $vacance->date_debut->format('Y-m-d'),
                    'date_fin' => $vacance->date_fin->format('Y-m-d'),
                    'actif' => $vacance->actif,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de la vacance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la création de la vacance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère une vacance spécifique
     */
    public function getVacance(Request $request, $vacanceId)
    {
        try {
            $vacance = Vacance::findOrFail($vacanceId);

            return response()->json([
                'success' => true,
                'vacance' => [
                    'id' => $vacance->id,
                    'nom' => $vacance->nom,
                    'description' => $vacance->description,
                    'date_debut' => $vacance->date_debut->format('Y-m-d'),
                    'date_fin' => $vacance->date_fin->format('Y-m-d'),
                    'actif' => $vacance->actif,
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Vacance non trouvée'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération de la vacance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération de la vacance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour une vacance
     */
    public function updateVacance(Request $request, $vacanceId)
    {
        try {
            $vacance = Vacance::findOrFail($vacanceId);

            $data = $request->validate([
                'nom' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'date_debut' => 'sometimes|date',
                'date_fin' => 'sometimes|date|after_or_equal:date_debut',
                'actif' => 'boolean',
            ]);

            $vacance->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Vacance mise à jour avec succès',
                'vacance' => [
                    'id' => $vacance->id,
                    'nom' => $vacance->nom,
                    'description' => $vacance->description,
                    'date_debut' => $vacance->date_debut->format('Y-m-d'),
                    'date_fin' => $vacance->date_fin->format('Y-m-d'),
                    'actif' => $vacance->actif,
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Vacance non trouvée'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour de la vacance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la mise à jour de la vacance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprime une vacance
     */
    public function deleteVacance(Request $request, $vacanceId)
    {
        try {
            $vacance = Vacance::findOrFail($vacanceId);
            $vacance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vacance supprimée avec succès',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Vacance non trouvée'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression de la vacance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la suppression de la vacance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les statistiques du formateur
     */
    public function getStatistiques(Request $request)
    {
        try {
            $user = $request->user();
            $formateur = $user->formateur;

            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil formateur non trouvé'
                ], 404);
            }

            $moduleIds = Module::where('formateur_id', $formateur->id)->pluck('id');
            
            $stats = [
                'total_modules' => Module::where('formateur_id', $formateur->id)->count(),
                'total_apprenants' => Inscription::whereIn('module_id', $moduleIds)
                    ->where('statut', 'valide')
                    ->distinct('apprenant_id')
                    ->count('apprenant_id'),
                'total_questionnaires' => Questionnaire::whereIn('module_id', $moduleIds)->count(),
                'total_documents' => Document::where('formateur_id', $formateur->id)->count(),
            ];

            return response()->json([
                'success' => true,
                'statistiques' => $stats,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des statistiques formateur', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les liens Google Meet des niveaux du formateur
     */
    public function getLiensGoogleMeet(Request $request)
    {
        try {
            $user = $request->user();
            $formateur = $user->formateur;

            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil formateur non trouvé'
                ], 404);
            }

            $niveaux = Niveau::where('formateur_id', $formateur->id)
                ->where('actif', true)
                ->whereNotNull('lien_meet')
                ->orderBy('ordre')
                ->get();

            $liens = $niveaux->map(function ($niveau) {
                return [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'lien_meet' => $niveau->lien_meet,
                    'description' => $niveau->description,
                ];
            });

            return response()->json([
                'success' => true,
                'liens' => $liens,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des liens Google Meet', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des liens Google Meet: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les demandes de cours à domicile
     */
    public function getDemandesCoursDomicile(Request $request)
    {
        try {
            $user = $request->user();
            $formateur = $user->formateur;

            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil formateur non trouvé'
                ], 404);
            }

            // Récupérer les demandes assignées au formateur ou en attente
            $demandes = DemandeCoursMaison::where('formateur_id', $formateur->id)
                ->orWhere(function($query) {
                    $query->whereNull('formateur_id')
                          ->where('statut', 'en_attente');
                })
                ->with(['user', 'niveau', 'module'])
                ->orderBy('created_at', 'desc')
                ->get();

            $demandesFormates = $demandes->map(function ($demande) {
                return [
                    'id' => $demande->id,
                    'user' => $demande->user ? [
                        'id' => $demande->user->id,
                        'nom' => $demande->user->nom,
                        'prenom' => $demande->user->prenom,
                        'email' => $demande->user->email,
                        'telephone' => $demande->user->telephone,
                    ] : null,
                    'niveau' => $demande->niveau ? [
                        'id' => $demande->niveau->id,
                        'nom' => $demande->niveau->nom,
                    ] : null,
                    'module' => $demande->module ? [
                        'id' => $demande->module->id,
                        'titre' => $demande->module->titre,
                    ] : $demande->module, // Si c'est une string
                    'nombre_enfants' => $demande->nombre_enfants,
                    'ville' => $demande->ville,
                    'commune' => $demande->commune,
                    'quartier' => $demande->quartier,
                    'numero' => $demande->numero,
                    'message' => $demande->message,
                    'statut' => $demande->statut,
                    'created_at' => $demande->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'demandes' => $demandesFormates,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des demandes de cours à domicile', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des demandes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accepte une demande de cours à domicile
     */
    public function accepterDemandeCoursDomicile(Request $request, $demandeId)
    {
        try {
            $user = $request->user();
            $formateur = $user->formateur;

            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil formateur non trouvé'
                ], 404);
            }

            $demande = DemandeCoursMaison::findOrFail($demandeId);
            
            // Vérifier que la demande peut être acceptée
            if ($demande->statut !== 'en_attente') {
                return response()->json([
                    'success' => false,
                    'error' => 'Cette demande ne peut plus être acceptée'
                ], 400);
            }

            $demande->formateur_id = $formateur->id;
            $demande->statut = 'acceptee';
            $demande->save();

            return response()->json([
                'success' => true,
                'message' => 'Demande acceptée avec succès',
                'demande' => [
                    'id' => $demande->id,
                    'statut' => $demande->statut,
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Demande non trouvée'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'acceptation de la demande', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'acceptation de la demande: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refuse une demande de cours à domicile
     */
    public function refuserDemandeCoursDomicile(Request $request, $demandeId)
    {
        try {
            $user = $request->user();
            $formateur = $user->formateur;

            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil formateur non trouvé'
                ], 404);
            }

            $demande = DemandeCoursMaison::findOrFail($demandeId);
            
            // Vérifier que la demande peut être refusée
            if ($demande->statut !== 'en_attente' && $demande->statut !== 'acceptee') {
                return response()->json([
                    'success' => false,
                    'error' => 'Cette demande ne peut plus être refusée'
                ], 400);
            }

            $data = $request->validate([
                'motif_refus' => 'nullable|string|max:500',
            ]);

            $demande->statut = 'refusee';
            if (isset($data['motif_refus'])) {
                $demande->motif_refus = $data['motif_refus'];
            }
            $demande->save();

            return response()->json([
                'success' => true,
                'message' => 'Demande refusée avec succès',
                'demande' => [
                    'id' => $demande->id,
                    'statut' => $demande->statut,
                    'motif_refus' => $demande->motif_refus,
                ],
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Demande non trouvée'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur lors du refus de la demande', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du refus de la demande: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère la progression des apprenants assignés
     */
    public function getProgressionApprenantsAssignes(Request $request)
    {
        try {
            $user = $request->user();
            $formateur = $user->formateur;

            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil formateur non trouvé'
                ], 404);
            }

            // Récupérer les modules du formateur
            $moduleIds = Module::where('formateur_id', $formateur->id)->pluck('id');
            
            // Récupérer les apprenants inscrits à ces modules
            $inscriptions = Inscription::whereIn('module_id', $moduleIds)
                ->where('statut', 'valide')
                ->with(['apprenant.utilisateur', 'apprenant.niveau', 'module'])
                ->get();

            $progression = [];
            
            foreach ($inscriptions as $inscription) {
                $apprenantId = $inscription->apprenant_id;
                
                if (!isset($progression[$apprenantId])) {
                    $questionnairesCompletes = ReponseQuestionnaire::where('apprenant_id', $apprenantId)
                        ->distinct('questionnaire_id')
                        ->count('questionnaire_id');
                    
                    $progression[$apprenantId] = [
                        'apprenant' => [
                            'id' => $inscription->apprenant->id,
                            'nom' => $inscription->apprenant->utilisateur->nom,
                            'prenom' => $inscription->apprenant->utilisateur->prenom,
                            'email' => $inscription->apprenant->utilisateur->email,
                        ],
                        'niveau' => $inscription->apprenant->niveau ? [
                            'id' => $inscription->apprenant->niveau->id,
                            'nom' => $inscription->apprenant->niveau->nom,
                        ] : null,
                        'modules_inscrits' => 0,
                        'questionnaires_completes' => $questionnairesCompletes,
                        'modules' => [],
                    ];
                }
                
                $progression[$apprenantId]['modules_inscrits']++;
                $progression[$apprenantId]['modules'][] = [
                    'id' => $inscription->module->id,
                    'titre' => $inscription->module->titre,
                ];
            }

            return response()->json([
                'success' => true,
                'progression' => array_values($progression),
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération de la progression', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération de la progression: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère la progression des apprenants (alias)
     */
    public function getProgressionApprenants(Request $request)
    {
        return $this->getProgressionApprenantsAssignes($request);
    }
}








