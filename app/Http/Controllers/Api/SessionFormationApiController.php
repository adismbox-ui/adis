<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SessionFormation;
use App\Models\Niveau;
use App\Models\Vacance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SessionFormationApiController extends Controller
{
    /**
     * Récupère toutes les sessions de formation
     */
    public function index()
    {
        $sessions = SessionFormation::orderBy('date_debut')->get();
        return response()->json(['sessions' => $sessions], 200);
    }

    /**
     * Récupère uniquement les sessions activées (actif = true et prix > 0 ou prix IS NULL)
     */
    public function getSessionsActives()
    {
        $sessions = SessionFormation::where('actif', true)
            ->where(function($query) {
                $query->where('prix', '>', 0)
                      ->orWhereNull('prix');
            })
            ->orderBy('date_debut')
            ->get();
            
        return response()->json([
            'sessions_actives' => $sessions,
            'total' => $sessions->count(),
            'message' => 'Sessions actives récupérées avec succès'
        ], 200);
    }

    /**
     * Récupère uniquement les sessions désactivées (actif = false ou prix = 0)
     */
    public function getSessionsDesactivees()
    {
        $sessions = SessionFormation::where(function($query) {
                $query->where('actif', false)
                      ->orWhere('prix', '=', 0);
            })
            ->orderBy('date_debut')
            ->get();
            
        return response()->json([
            'sessions_desactivees' => $sessions,
            'total' => $sessions->count(),
            'message' => 'Sessions désactivées récupérées avec succès'
        ], 200);
    }

    public function create()
    {
        $niveaux = Niveau::where('actif', true)->orderBy('ordre')->get();
        $vacances = Vacance::actives()->orderBy('date_debut')->get();
        $modules = \App\Models\Module::orderBy('titre')->get();
        return response()->json(['niveaux' => $niveaux, 'vacances' => $vacances, 'modules' => $modules], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'duree_seance_minutes' => 'required|integer|min:15|max:480',
            'places_max' => 'nullable|integer|min:1',
            'actif' => 'boolean'
        ]);

        // Calculer automatiquement le nombre de séances en fonction des semaines
        $dateDebut = \Carbon\Carbon::parse($request->date_debut);
        $dateFin = \Carbon\Carbon::parse($request->date_fin);
        $nombreSemaines = $dateDebut->diffInWeeks($dateFin) + 1; // +1 car on compte la semaine de début

        $vacancesConflit = Vacance::actives()
            ->pourPeriode($request->date_debut, $request->date_fin)
            ->get();
        if ($vacancesConflit->count() > 0) {
            return response()->json([
                'error' => 'La période sélectionnée chevauche des vacances : ' . $vacancesConflit->pluck('nom')->implode(', ')
            ], 422);
        }

        // Créer la session (plus de niveau_id)
        $sessionData = $data;
        $sessionData['nombre_seances'] = $nombreSemaines;
        $sessionData['heure_debut'] = null;
        $sessionData['heure_fin'] = null;
        $sessionData['jour_semaine'] = null;

        $session = SessionFormation::create($sessionData);
        if ($request->has('modules')) {
            $session->modules()->sync($request->input('modules'));
        }
        return response()->json([
            'session' => $session, 
            'message' => 'Session créée avec succès. Nombre de séances calculé automatiquement : ' . $nombreSemaines
        ], 201);
    }

    public function edit(SessionFormation $session)
    {
        $niveaux = Niveau::where('actif', true)->orderBy('ordre')->get();
        $vacances = Vacance::actives()->orderBy('date_debut')->get();
        $modules = \App\Models\Module::orderBy('titre')->get();
        return response()->json(['session' => $session, 'niveaux' => $niveaux, 'vacances' => $vacances, 'modules' => $modules], 200);
    }

    /**
     * Mettre à jour une session avec validation avancée
     */
    public function update(Request $request, SessionFormation $session)
    {
        try {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'duree_seance_minutes' => 'required|integer|min:15|max:480',
                'prix' => 'nullable|numeric|min:0',
            'places_max' => 'nullable|integer|min:1',
            'actif' => 'boolean'
        ]);

        // Calculer automatiquement le nombre de séances en fonction des semaines
        $dateDebut = \Carbon\Carbon::parse($request->date_debut);
        $dateFin = \Carbon\Carbon::parse($request->date_fin);
        $nombreSemaines = $dateDebut->diffInWeeks($dateFin) + 1; // +1 car on compte la semaine de début

            // Vérifier les conflits avec les vacances
        $vacancesConflit = Vacance::actives()
                ->pourPeriode($data['date_debut'], $data['date_fin'])
            ->get();

        if ($vacancesConflit->count() > 0) {
            return response()->json([
                    'error' => 'La période sélectionnée chevauche des vacances',
                    'vacances_conflit' => $vacancesConflit->pluck('nom')->toArray(),
                    'periode' => [
                        'date_debut' => $data['date_debut'],
                        'date_fin' => $data['date_fin']
                    ]
            ], 422);
        }

            // Sauvegarder les anciennes valeurs pour comparaison
            $anciennesValeurs = [
                'nom' => $session->nom,
                'date_debut' => $session->date_debut,
                'date_fin' => $session->date_fin,
                'prix' => $session->prix,
                'places_max' => $session->places_max
            ];

            // Mettre à jour la session avec le nombre de séances calculé automatiquement
            $sessionData = $data;
            $sessionData['nombre_seances'] = $nombreSemaines;
            $sessionData['heure_debut'] = null;
            $sessionData['heure_fin'] = null;
            $sessionData['jour_semaine'] = null;

            $session->update($sessionData);

            // Gérer les modules si fournis
        if ($request->has('modules')) {
            $session->modules()->sync($request->input('modules'));
            }

            // Charger les relations pour la réponse
            $session->load(['modules', 'inscriptions']);

            // Statistiques après mise à jour
            $statistiques = [
                'inscriptions_count' => $session->inscriptions->count(),
                'modules_count' => $session->modules->count(),
                'places_disponibles' => $session->places_max ? 
                    max(0, $session->places_max - $session->inscriptions->count()) : null,
                'complet' => $session->places_max ? 
                    $session->inscriptions->count() >= $session->places_max : false
            ];

            return response()->json([
                'success' => true,
                'message' => 'Session mise à jour avec succès',
                'session' => [
                    'id' => $session->id,
                    'nom' => $session->nom,
                    'description' => $session->description,
                    'date_debut' => $session->date_debut,
                    'date_fin' => $session->date_fin,
                    'heure_debut' => $session->heure_debut,
                    'heure_fin' => $session->heure_fin,
                    'jour_semaine' => $session->jour_semaine,
                    'duree_seance_minutes' => $session->duree_seance_minutes,
                    'nombre_seances' => $session->nombre_seances,
                    'prix' => $session->prix,
                    'places_max' => $session->places_max,
                    'actif' => $session->actif,
                    'updated_at' => $session->updated_at
                ],
                'modifications' => [
                    'nom' => $anciennesValeurs['nom'] !== $session->nom,
                    'date_debut' => $anciennesValeurs['date_debut'] !== $session->date_debut,
                    'date_fin' => $anciennesValeurs['date_fin'] !== $session->date_fin,
                    'prix' => $anciennesValeurs['prix'] !== $session->prix,
                    'places_max' => $anciennesValeurs['places_max'] !== $session->places_max
                ],
                'statistiques' => $statistiques
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Erreur de validation',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la mise à jour de la session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une session avec vérifications complètes
     */
    public function destroy(SessionFormation $session)
    {
        try {
            // Vérifications avant suppression
            $verifications = [
                'inscriptions' => $session->inscriptions()->count(),
                'modules' => $session->modules()->count()
            ];

            $totalLiens = array_sum($verifications);

            if ($totalLiens > 0) {
                return response()->json([
                    'error' => 'Impossible de supprimer cette session car elle est utilisée',
                    'verifications' => $verifications,
                    'session' => [
                        'id' => $session->id,
                        'nom' => $session->nom,
                        'date_debut' => $session->date_debut,
                        'date_fin' => $session->date_fin
                    ],
                    'message' => 'Vous devez d\'abord supprimer ou déplacer les données liées à cette session'
                ], 422);
            }

            // Sauvegarder les informations avant suppression
            $sessionInfo = [
                'id' => $session->id,
                'nom' => $session->nom,
                'description' => $session->description,
                'date_debut' => $session->date_debut,
                'date_fin' => $session->date_fin,
                'prix' => $session->prix,
                'places_max' => $session->places_max
            ];

            // Supprimer la session
        $session->delete();

            return response()->json([
                'success' => true,
                'message' => 'Session supprimée avec succès',
                'session_supprimee' => $sessionInfo,
                'timestamp' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la suppression de la session: ' . $e->getMessage()
            ], 500);
        }
    }

    public function calendrier()
    {
        $sessions = SessionFormation::where('actif', true)
            ->orderBy('date_debut')
            ->get();
        $vacances = Vacance::actives()->orderBy('date_debut')->get();
        return response()->json(['sessions' => $sessions, 'vacances' => $vacances], 200);
    }

    /**
     * Afficher les détails d'une session avec ses relations
     */
    public function show(SessionFormation $session)
    {
        try {
            // Charger la session avec ses relations (sans niveau)
            $session = SessionFormation::with(['inscriptions.apprenant.utilisateur', 'modules'])
                ->find($session->id);

            if (!$session) {
                return response()->json([
                    'error' => 'Session non trouvée',
                    'session_id' => $session->id ?? 'inconnu'
                ], 404);
            }

            // Formater la réponse avec les détails complets
            $sessionFormatee = [
                'id' => $session->id,
                'nom' => $session->nom,
                'description' => $session->description,
                'date_debut' => $session->date_debut,
                'date_fin' => $session->date_fin,
                'heure_debut' => $session->heure_debut,
                'heure_fin' => $session->heure_fin,
                'jour_semaine' => $session->jour_semaine,
                'duree_seance_minutes' => $session->duree_seance_minutes,
                'nombre_seances' => $session->nombre_seances,
                'prix' => $session->prix,
                'places_max' => $session->places_max,
                'actif' => $session->actif,
                'created_at' => $session->created_at,
                'updated_at' => $session->updated_at,
                'statistiques' => [
                    'inscriptions_count' => $session->inscriptions->count(),
                    'modules_count' => $session->modules->count(),
                    'places_disponibles' => $session->places_max ? 
                        max(0, $session->places_max - $session->inscriptions->count()) : null,
                    'complet' => $session->places_max ? 
                        $session->inscriptions->count() >= $session->places_max : false
                ],
                'inscriptions' => $session->inscriptions->map(function ($inscription) {
                    return [
                        'id' => $inscription->id,
                        'date_inscription' => $inscription->date_inscription,
                        'statut' => $inscription->statut,
                        'apprenant' => $inscription->apprenant ? [
                            'id' => $inscription->apprenant->id,
                            'nom' => $inscription->apprenant->utilisateur->nom ?? 'N/A',
                            'prenom' => $inscription->apprenant->utilisateur->prenom ?? 'N/A',
                            'email' => $inscription->apprenant->utilisateur->email ?? 'N/A'
                        ] : null
                    ];
                }),
                'modules' => $session->modules->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'description' => $module->description,
                        'discipline' => $module->discipline,
                        'date_debut' => $module->date_debut,
                        'date_fin' => $module->date_fin,
                        'prix' => $module->prix
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'session' => $sessionFormatee
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération de la session: ' . $e->getMessage(),
                'session_id' => $session->id ?? 'inconnu'
            ], 500);
        }
    }

    /**
     * Afficher les détails d'une session par ID (méthode alternative)
     */
    public function showById($id)
    {
        try {
            // Vérifier si l'ID est valide
            if (!is_numeric($id)) {
                return response()->json([
                    'error' => 'ID de session invalide',
                    'session_id' => $id
                ], 400);
            }

            // Charger la session avec ses relations (sans niveau)
            $session = SessionFormation::with(['inscriptions.apprenant.utilisateur', 'modules'])
                ->find($id);

            if (!$session) {
                return response()->json([
                    'error' => 'Session non trouvée',
                    'session_id' => $id,
                    'message' => 'Aucune session trouvée avec l\'ID ' . $id
                ], 404);
            }

            // Formater la réponse avec les détails complets
            $sessionFormatee = [
                'id' => $session->id,
                'nom' => $session->nom,
                'description' => $session->description,
                'date_debut' => $session->date_debut,
                'date_fin' => $session->date_fin,
                'heure_debut' => $session->heure_debut,
                'heure_fin' => $session->heure_fin,
                'jour_semaine' => $session->jour_semaine,
                'duree_seance_minutes' => $session->duree_seance_minutes,
                'nombre_seances' => $session->nombre_seances,
                'prix' => $session->prix,
                'places_max' => $session->places_max,
                'actif' => $session->actif,
                'created_at' => $session->created_at,
                'updated_at' => $session->updated_at,
                'statistiques' => [
                    'inscriptions_count' => $session->inscriptions->count(),
                    'modules_count' => $session->modules->count(),
                    'places_disponibles' => $session->places_max ? 
                        max(0, $session->places_max - $session->inscriptions->count()) : null,
                    'complet' => $session->places_max ? 
                        $session->inscriptions->count() >= $session->places_max : false
                ],
                'inscriptions' => $session->inscriptions->map(function ($inscription) {
                    return [
                        'id' => $inscription->id,
                        'date_inscription' => $inscription->date_inscription,
                        'statut' => $inscription->statut,
                        'apprenant' => $inscription->apprenant ? [
                            'id' => $inscription->apprenant->id,
                            'nom' => $inscription->apprenant->utilisateur->nom ?? 'N/A',
                            'prenom' => $inscription->apprenant->utilisateur->prenom ?? 'N/A',
                            'email' => $inscription->apprenant->utilisateur->email ?? 'N/A'
                        ] : null
                    ];
                }),
                'modules' => $session->modules->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'description' => $module->description,
                        'discipline' => $module->discipline,
                        'date_debut' => $module->date_debut,
                        'date_fin' => $module->date_fin,
                        'prix' => $module->prix
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'session' => $sessionFormatee
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération de la session: ' . $e->getMessage(),
                'session_id' => $id
            ], 500);
        }
    }

    /**
     * Méthode de diagnostic pour vérifier les relations d'une session
     */
    public function diagnosticSession($id)
    {
        // Vérifier si l'utilisateur est admin
        $user = auth()->user();
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Non autorisé. Seuls les administrateurs peuvent accéder à cette fonction.'], 403);
        }

        $session = SessionFormation::find($id);
        if (!$session) {
            return response()->json(['error' => 'Session non trouvée'], 404);
        }

        // Vérifier les inscriptions
        $inscriptions = $session->inscriptions()->get();
        $inscriptionsCount = $inscriptions->count();

        // Vérifier les modules
        $modules = $session->modules()->get();
        $modulesCount = $modules->count();

        // Vérifier si la session existe encore en base
        $sessionExists = SessionFormation::where('id', $id)->exists();

        return response()->json([
            'session_id' => $id,
            'session_exists' => $sessionExists,
            'session_data' => $session,
            'inscriptions' => [
                'count' => $inscriptionsCount,
                'data' => $inscriptions
            ],
            'modules' => [
                'count' => $modulesCount,
                'data' => $modules
            ],
            'can_delete' => $inscriptionsCount === 0
        ], 200);
    }

    /**
     * Méthode de suppression alternative avec transaction
     */
    public function destroyAlternative($id)
    {
        // Vérifier si l'utilisateur est admin
        $user = auth()->user();
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Non autorisé. Seuls les administrateurs peuvent supprimer des sessions.'], 403);
        }

        try {
            DB::beginTransaction();
            
            $session = SessionFormation::find($id);
            if (!$session) {
                DB::rollBack();
                return response()->json(['error' => 'Session non trouvée'], 404);
            }

            Log::info('Suppression alternative - Session ID: ' . $id);

            // Supprimer les inscriptions
            $inscriptionsDeleted = $session->inscriptions()->delete();
            Log::info('Inscriptions supprimées: ' . $inscriptionsDeleted);

            // Détacher les modules
            $modulesDetached = $session->modules()->detach();
            Log::info('Modules détachés: ' . $modulesDetached);

            // Supprimer la session
            $sessionDeleted = $session->delete();
            Log::info('Session supprimée: ' . ($sessionDeleted ? 'Oui' : 'Non'));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Session supprimée avec succès (méthode alternative).',
                'session_id' => $id,
                'inscriptions_deleted' => $inscriptionsDeleted,
                'modules_detached' => $modulesDetached
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur suppression alternative session ID ' . $id . ': ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Erreur lors de la suppression alternative: ' . $e->getMessage(),
                'session_id' => $id
            ], 500);
        }
    }

    /**
     * Méthode de modification alternative avec transaction
     */
    public function updateAlternative(Request $request, $id)
    {
        // Vérifier si l'utilisateur est admin
        $user = auth()->user();
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Non autorisé. Seuls les administrateurs peuvent modifier des sessions.'], 403);
        }

        try {
            DB::beginTransaction();
            
            $session = SessionFormation::find($id);
            if (!$session) {
                DB::rollBack();
                return response()->json(['error' => 'Session non trouvée'], 404);
            }

            Log::info('Modification alternative - Session ID: ' . $id);
            Log::info('Données reçues: ' . json_encode($request->all()));

            $data = $request->validate([
                'nom' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after_or_equal:date_debut',
                'heure_debut' => 'nullable|date_format:H:i',
                'heure_fin' => 'nullable|date_format:H:i|after:heure_debut',
                'jour_semaine' => 'nullable|string',
                'duree_seance_minutes' => 'required|integer|min:15|max:480',
                'nombre_seances' => 'required|integer|min:1',
                'places_max' => 'nullable|integer|min:1',
                'actif' => 'boolean'
            ]);

            Log::info('Données validées: ' . json_encode($data));

            // Mettre à jour la session
            $updated = $session->update($data);
            Log::info('Résultat de la mise à jour: ' . ($updated ? 'Succès' : 'Échec'));

            if ($request->has('modules')) {
                $session->modules()->sync($request->input('modules'));
            } else {
                $session->modules()->detach();
            }

            // Recharger la session
            $session->refresh();

            DB::commit();

            return response()->json([
                'success' => true,
                'session' => $session,
                'message' => 'Session mise à jour avec succès (méthode alternative).',
                'session_id' => $id
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Erreur validation session update alternative: ' . json_encode($e->errors()));
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour alternative session ID ' . $id . ': ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Erreur lors de la mise à jour alternative: ' . $e->getMessage(),
                'session_id' => $id
            ], 500);
        }
    }

    /**
     * Activer une session de formation (réservé aux administrateurs)
     */
    public function activer(Request $request, $id)
    {
        try {
            // Vérifier que l'utilisateur est admin
            if (Auth::user()->type_compte !== 'admin') {
                return response()->json([
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent activer des sessions.'
                ], 403);
            }

            $session = SessionFormation::findOrFail($id);
            
            // Validation du prix (optionnel)
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'prix' => 'nullable|numeric|min:1|max:999999'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Données invalides',
                    'details' => $validator->errors()
                ], 422);
            }

            // Le prix est optionnel - le modèle gère la logique
            $prix = $request->prix;
            $session->activer($prix);

            return response()->json([
                'success' => true,
                'message' => 'Session activée avec succès',
                'session' => [
                    'id' => $session->id,
                    'nom' => $session->nom,
                    'prix' => $session->prix,
                    'actif' => $session->actif,
                    'statut_prix' => $session->statut_prix
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de l\'activation',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Désactiver une session de formation (réservé aux administrateurs)
     */
    public function desactiver(Request $request, $id)
    {
        try {
            // Vérifier que l'utilisateur est admin
            if (Auth::user()->type_compte !== 'admin') {
                return response()->json([
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent désactiver des sessions.'
                ], 403);
            }

            $session = SessionFormation::findOrFail($id);
            $session->desactiver();

            return response()->json([
                'success' => true,
                'message' => 'Session désactivée avec succès',
                'session' => [
                    'id' => $session->id,
                    'nom' => $session->nom,
                    'prix' => $session->prix,
                    'actif' => $session->actif,
                    'statut_prix' => $session->statut_prix
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la désactivation',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer le statut d'une session
     */
    public function statut($id)
    {
        try {
            $session = SessionFormation::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'session' => [
                    'id' => $session->id,
                    'nom' => $session->nom,
                    'prix' => $session->prix,
                    'actif' => $session->actif,
                    'est_active' => $session->estActive(),
                    'est_gratuite' => $session->estGratuite(),
                    'statut_prix' => $session->statut_prix
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Session non trouvée',
                'message' => $e->getMessage()
            ], 404);
        }
    }
} 