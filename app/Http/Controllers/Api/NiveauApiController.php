<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Niveau;
use App\Models\SessionFormation;
use App\Models\Formateur;
use Illuminate\Http\Request;

class NiveauApiController extends Controller
{
    public function index()
    {
        $niveaux = Niveau::orderBy('ordre')->get();
        return response()->json([
            'success' => true,
            'data' => $niveaux
        ]);
    }

    public function create()
    {
        // Récupérer toutes les sessions disponibles
        $sessionsDisponibles = SessionFormation::orderBy('date_debut')->get();
        return response()->json([
            'sessions_disponibles' => $sessionsDisponibles,
            'message' => 'Sessions disponibles pour liaison avec niveau'
        ], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'boolean',
            'formateur_id' => 'nullable|exists:formateurs,id',
            'lien_meet' => 'nullable|string|max:255',
            'session_id' => 'nullable|exists:sessions_formation,id'
        ]);

        // Créer le niveau avec session_id
        $niveau = Niveau::create($data);

        if ($request->filled('session_id')) {
            return response()->json([
                'niveau' => $niveau, 
                'message' => 'Niveau créé avec succès et lié à la session.'
            ], 201);
        }

        return response()->json([
            'niveau' => $niveau, 
            'message' => 'Niveau créé avec succès. Aucune session liée.'
        ], 201);
    }

    public function edit(Niveau $niveau)
    {
        // Récupérer toutes les sessions disponibles
        $sessionsDisponibles = SessionFormation::orderBy('date_debut')->get();
            
        return response()->json([
            'niveau' => $niveau,
            'sessions_disponibles' => $sessionsDisponibles
        ], 200);
    }

    /**
     * Mettre à jour un niveau avec validation avancée
     */
    public function update(Request $request, Niveau $niveau)
    {
        try {
            $data = $request->validate([
                'nom' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'ordre' => 'nullable|integer|min:0',
                'actif' => 'boolean',
                'formateur_id' => 'nullable|exists:formateurs,id',
                'lien_meet' => 'nullable|string|max:255',
                'session_id' => 'nullable|exists:sessions_formation,id'
            ]);

            // Vérifier si l'ordre est fourni et s'il est déjà utilisé par un autre niveau
            if (isset($data['ordre'])) {
                $niveauExistant = Niveau::where('ordre', $data['ordre'])
                    ->where('id', '!=', $niveau->id)
                    ->first();

                if ($niveauExistant) {
                    return response()->json([
                        'error' => 'L\'ordre ' . $data['ordre'] . ' est déjà utilisé par le niveau "' . $niveauExistant->nom . '"',
                        'niveau_conflit' => [
                            'id' => $niveauExistant->id,
                            'nom' => $niveauExistant->nom,
                            'ordre' => $niveauExistant->nom
                        ]
                    ], 422);
                }
            }

            // Sauvegarder l'ancien ordre pour la comparaison
            $ancienOrdre = $niveau->ordre;
            $nouvelOrdre = $data['ordre'] ?? $niveau->ordre;

            // Mettre à jour le niveau
            $niveau->update($data);

            // Statistiques sur les modules liés
            $statistiques = [
                'modules_count' => $niveau->modules()->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Niveau mis à jour avec succès' . ($request->filled('session_id') ? ' et lié à la session' : ''),
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre,
                    'actif' => $niveau->actif,
                    'formateur_id' => $niveau->formateur_id,
                    'lien_meet' => $niveau->lien_meet,
                    'session_id' => $niveau->session_id,
                    'ancien_ordre' => $ancienOrdre,
                    'nouvel_ordre' => $nouvelOrdre,
                    'updated_at' => $niveau->updated_at
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
                'error' => 'Erreur lors de la mise à jour du niveau: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un niveau avec vérifications complètes
     */
    public function destroy(Niveau $niveau)
    {
        try {
            // Vérifications avant suppression
            $verifications = [
                'modules' => $niveau->modules()->count()
            ];

            // Vérifier s'il y a des données liées
            $totalLiens = array_sum($verifications);
            
            if ($totalLiens > 0) {
                return response()->json([
                    'error' => 'Impossible de supprimer ce niveau car il est utilisé par d\'autres données',
                    'verifications' => $verifications,
                    'niveau' => [
                        'id' => $niveau->id,
                        'nom' => $niveau->nom,
                        'ordre' => $niveau->ordre
                    ],
                    'message' => 'Vous devez d\'abord supprimer ou déplacer les données liées à ce niveau'
                ], 422);
            }

            // Sauvegarder les informations avant suppression
            $niveauInfo = [
                'id' => $niveau->id,
                'nom' => $niveau->nom,
                'ordre' => $niveau->ordre,
                'description' => $niveau->description
            ];

            $niveau->delete();

            return response()->json([
                'success' => true,
                'message' => 'Niveau supprimé avec succès',
                'niveau_supprime' => $niveauInfo,
                'timestamp' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la suppression du niveau: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier si un niveau peut être supprimé
     */
    public function checkDeletable(Niveau $niveau)
    {
        $verifications = [
            'modules' => $niveau->modules()->count()
        ];

        $totalLiens = array_sum($verifications);
        $peutEtreSupprime = $totalLiens === 0;

        return response()->json([
            'success' => true,
            'niveau' => [
                'id' => $niveau->id,
                'nom' => $niveau->nom,
                'ordre' => $niveau->ordre
            ],
            'peut_etre_supprime' => $peutEtreSupprime,
            'verifications' => $verifications,
            'total_liens' => $totalLiens,
            'message' => $peutEtreSupprime 
                ? 'Ce niveau peut être supprimé en toute sécurité'
                : 'Ce niveau ne peut pas être supprimé car il est utilisé par d\'autres données'
        ], 200);
    }

    /**
     * Récupérer tous les formateurs depuis la table formateurs
     * Accessible uniquement aux administrateurs connectés
     */
    public function getFormateurs()
    {
        try {
            // Vérifier que l'utilisateur est connecté et est admin ou assistant
            $user = auth('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Vous devez être connecté pour accéder à cette ressource.'
                ], 401);
            }

            if ($user->type_compte !== 'admin' && $user->type_compte !== 'assistant') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès réservé aux administrateurs et assistants.'
                ], 403);
            }

            // Récupérer tous les formateurs avec leurs informations utilisateur
            $formateurs = Formateur::with('utilisateur')
                ->orderBy('created_at', 'desc')
                ->get();

            // Formater les données des formateurs
            $formateursFormates = $formateurs->map(function($formateur) {
                return [
                    'id' => $formateur->id,
                    'utilisateur_id' => $formateur->utilisateur_id,
                    'specialite' => $formateur->specialite,
                    'valide' => $formateur->valide,
                    'validation_token' => $formateur->validation_token,
                    'connaissance_adis' => $formateur->connaissance_adis,
                    'formation_adis' => $formateur->formation_adis,
                    'formation_autre' => $formateur->formation_autre,
                    'niveau_coran' => $formateur->niveau_coran,
                    'niveau_arabe' => $formateur->niveau_arabe,
                    'niveau_francais' => $formateur->niveau_francais,
                    'diplome_religieux' => $formateur->diplome_religieux,
                    'diplome_general' => $formateur->diplome_general,
                    'fichier_diplome_religieux' => $formateur->fichier_diplome_religieux,
                    'fichier_diplome_general' => $formateur->fichier_diplome_general,
                    'ville' => $formateur->ville,
                    'commune' => $formateur->commune,
                    'quartier' => $formateur->quartier,
                    'created_at' => $formateur->created_at,
                    'updated_at' => $formateur->updated_at,
                    'utilisateur' => $formateur->utilisateur ? [
                        'id' => $formateur->utilisateur->id,
                        'prenom' => $formateur->utilisateur->prenom,
                        'nom' => $formateur->utilisateur->nom,
                        'sexe' => $formateur->utilisateur->sexe,
                        'categorie' => $formateur->utilisateur->categorie,
                        'telephone' => $formateur->utilisateur->telephone,
                        'email' => $formateur->utilisateur->email,
                        'type_compte' => $formateur->utilisateur->type_compte,
                        'actif' => $formateur->utilisateur->actif,
                        'email_verified_at' => $formateur->utilisateur->email_verified_at,
                        'verification_token' => $formateur->utilisateur->verification_token
                    ] : null,
                    'statistiques' => [
                        'niveaux_assignes' => Niveau::where('formateur_id', $formateur->id)->count(),
                        'modules_assignes' => $formateur->modules ? $formateur->modules()->count() : 0
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Formateurs récupérés avec succès',
                'total' => $formateursFormates->count(),
                'formateurs' => $formateursFormates
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
     * Récupérer les formateurs validés uniquement
     */
    public function getFormateursValides()
    {
        try {
            // Vérifier que l'utilisateur est connecté et est admin ou assistant
            $user = auth('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Vous devez être connecté pour accéder à cette ressource.'
                ], 401);
            }

            if ($user->type_compte !== 'admin' && $user->type_compte !== 'assistant') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès réservé aux administrateurs et assistants.'
                ], 403);
            }

            // Récupérer uniquement les formateurs validés
            $formateurs = Formateur::with('utilisateur')
                ->where('valide', true)
                ->orderBy('created_at', 'desc')
                ->get();

            // Formater les données des formateurs
            $formateursFormates = $formateurs->map(function($formateur) {
                return [
                    'id' => $formateur->id,
                    'utilisateur_id' => $formateur->utilisateur_id,
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
                    'created_at' => $formateur->created_at,
                    'updated_at' => $formateur->updated_at,
                    'utilisateur' => $formateur->utilisateur ? [
                        'id' => $formateur->utilisateur->id,
                        'prenom' => $formateur->utilisateur->prenom,
                        'nom' => $formateur->utilisateur->nom,
                        'sexe' => $formateur->utilisateur->sexe,
                        'categorie' => $formateur->utilisateur->categorie,
                        'telephone' => $formateur->utilisateur->telephone,
                        'email' => $formateur->utilisateur->email,
                        'type_compte' => $formateur->utilisateur->type_compte,
                        'actif' => $formateur->utilisateur->actif,
                        'email_verified_at' => $formateur->utilisateur->email_verified_at
                    ] : null,
                    'statistiques' => [
                        'niveaux_assignes' => Niveau::where('formateur_id', $formateur->id)->count(),
                        'modules_assignes' => $formateur->modules ? $formateur->modules()->count() : 0
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Formateurs validés récupérés avec succès',
                'total' => $formateursFormates->count(),
                'formateurs' => $formateursFormates
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des formateurs validés',
                'details' => $e->getMessage()
            ], 500);
        }
    }
} 