<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apprenant;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ApprenantAdminController extends Controller
{
    /**
     * Change le niveau d'un apprenant
     * @param Request $request
     * @param int $id ID de l'apprenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function changerNiveau(Request $request, $id)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'nouveau_niveau_id' => 'required|integer|exists:niveaux,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 400);
        }

        try {
            // Récupérer l'apprenant
            $apprenant = Apprenant::with(['utilisateur', 'niveau'])->find($id);
            
            if (!$apprenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Apprenant non trouvé'
                ], 404);
            }

            // Vérifier si l'apprenant peut changer de niveau
            if (!$apprenant->peutChangerNiveau()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cet apprenant ne peut pas changer de niveau car il a des inscriptions en cours'
                ], 400);
            }

            // Récupérer l'ancien niveau pour l'historique
            $ancienNiveau = $apprenant->niveau;
            $nouveauNiveauId = $request->nouveau_niveau_id;

            // Changer le niveau
            $resultat = $apprenant->changerNiveau($nouveauNiveauId);

            if ($resultat) {
                // Récupérer le nouveau niveau
                $nouveauNiveau = Niveau::find($nouveauNiveauId);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Niveau de l\'apprenant changé avec succès',
                    'data' => [
                        'apprenant_id' => $apprenant->id,
                        'apprenant_nom' => $apprenant->utilisateur->nom . ' ' . $apprenant->utilisateur->prenom,
                        'ancien_niveau' => [
                            'id' => $ancienNiveau->id,
                            'nom' => $ancienNiveau->nom
                        ],
                        'nouveau_niveau' => [
                            'id' => $nouveauNiveau->id,
                            'nom' => $nouveauNiveau->nom
                        ],
                        'date_changement' => now()->toISOString()
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Erreur lors du changement de niveau'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Récupère la liste de tous les apprenants avec leurs niveaux
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $apprenants = Apprenant::with(['utilisateur', 'niveau'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'apprenants' => $apprenants,
            'total' => $apprenants->count()
        ], 200);
    }

    /**
     * Récupère un apprenant spécifique avec ses détails
     * @param int $id ID de l'apprenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $apprenant = Apprenant::with(['utilisateur', 'niveau', 'inscriptions.sessionFormation'])
            ->find($id);

        if (!$apprenant) {
            return response()->json([
                'success' => false,
                'error' => 'Apprenant non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'apprenant' => $apprenant
        ], 200);
    }

    /**
     * Récupère tous les niveaux disponibles
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNiveaux()
    {
        $niveaux = Niveau::where('actif', true)
            ->orderBy('ordre')
            ->get();

        return response()->json([
            'success' => true,
            'niveaux' => $niveaux
        ], 200);
    }

    /**
     * Recherche un apprenant par son email
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rechercherParEmail(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 400);
        }

        try {
            $email = $request->email;

            // Rechercher l'apprenant par email via la relation utilisateur
            $apprenant = Apprenant::with([
                'utilisateur', 
                'niveau', 
                'inscriptions.sessionFormation',
                'paiements',
                'certificats'
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

            return response()->json([
                'success' => true,
                'apprenant' => [
                    'id' => $apprenant->id,
                    'utilisateur' => [
                        'id' => $apprenant->utilisateur->id,
                        'nom' => $apprenant->utilisateur->nom,
                        'prenom' => $apprenant->utilisateur->prenom,
                        'email' => $apprenant->utilisateur->email,
                        'telephone' => $apprenant->utilisateur->telephone,
                        'sexe' => $apprenant->utilisateur->sexe,
                        'categorie' => $apprenant->utilisateur->categorie,
                        'actif' => $apprenant->utilisateur->actif,
                        'email_verified_at' => $apprenant->utilisateur->email_verified_at,
                        'created_at' => $apprenant->utilisateur->created_at
                    ],
                    'niveau' => $apprenant->niveau ? [
                        'id' => $apprenant->niveau->id,
                        'nom' => $apprenant->niveau->nom,
                        'description' => $apprenant->niveau->description,
                        'ordre' => $apprenant->niveau->ordre
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
                    'formateur_domicile' => $apprenant->formateur_domicile,
                    'inscriptions_count' => $apprenant->inscriptions->count(),
                    'paiements_count' => $apprenant->paiements->count(),
                    'certificats_count' => $apprenant->certificats->count(),
                    'created_at' => $apprenant->created_at,
                    'updated_at' => $apprenant->updated_at
                ],
                'message' => 'Apprenant trouvé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la recherche de l\'apprenant'
            ], 500);
        }
    }

    /**
     * Récupère tous les apprenants d'un niveau spécifique
     * @param int $niveauId ID du niveau
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApprenantsByNiveau($niveauId)
    {
        try {
            // Vérifier que le niveau existe
            $niveau = Niveau::find($niveauId);
            if (!$niveau) {
                return response()->json([
                    'success' => false,
                    'error' => 'Niveau non trouvé'
                ], 404);
            }

            // Récupérer tous les apprenants de ce niveau
            $apprenants = Apprenant::with(['utilisateur', 'niveau'])
                ->where('niveau_id', $niveauId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre
                ],
                'apprenants' => $apprenants,
                'total' => $apprenants->count(),
                'message' => "Apprenants du niveau '{$niveau->nom}' récupérés avec succès"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des apprenants'
            ], 500);
        }
    }

    

    /**
     * Valide le paiement d'un apprenant en 1 clic (admin uniquement)
     * @param int $id ID de l'apprenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function validerPaiement($id)
    {
        try {
            // Récupérer l'apprenant
            $apprenant = Apprenant::with(['utilisateur', 'niveau'])->find($id);
            
            if (!$apprenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Apprenant non trouvé'
                ], 404);
            }

            // Récupérer le module du niveau de l'apprenant
            $module = \App\Models\Module::where('niveau_id', $apprenant->niveau_id)
                ->first();

            if (!$module) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun module actif trouvé pour le niveau de cet apprenant'
                ], 404);
            }

            // Vérifier que l'apprenant n'a pas déjà payé ce module
            $paiementExistant = \App\Models\Paiement::where('apprenant_id', $id)
                ->where('module_id', $module->id)
                ->where('statut', 'valide')
                ->first();

            if ($paiementExistant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cet apprenant a déjà payé ce module'
                ], 400);
            }

            // Créer le paiement validé automatiquement
            $paiement = \App\Models\Paiement::create([
                'apprenant_id' => $id,
                'module_id' => $module->id,
                'montant' => 0, // Gratuit par défaut
                'date_paiement' => now(),
                'statut' => 'valide',
                'methode' => 'validation_admin',
                'reference' => 'ADMIN_1CLIC_' . time() . '_' . rand(1000, 9999),
                'notes' => 'Paiement validé en 1 clic par l\'administrateur',
                'informations_paiement' => json_encode([
                    'validated_by' => Auth::user()->id,
                    'validation_date' => now()->toISOString(),
                    'payment_method' => 'validation_admin',
                    'admin_notes' => 'Validation automatique en 1 clic'
                ])
            ]);



            return response()->json([
                'success' => true,
                'message' => 'Paiement validé en 1 clic avec succès !',
                'data' => [
                    'paiement_id' => $paiement->id,
                    'apprenant' => [
                        'id' => $apprenant->id,
                        'nom' => $apprenant->utilisateur->nom . ' ' . $apprenant->utilisateur->prenom,
                        'email' => $apprenant->utilisateur->email,
                        'niveau' => $apprenant->niveau->nom ?? 'N/A'
                    ],
                    'module' => [
                        'id' => $module->id,
                        'nom' => $module->titre,
                        'niveau' => $module->niveau->nom ?? 'N/A'
                    ],
                    'paiement' => [
                        'montant' => $paiement->montant,
                        'methode' => $paiement->methode,
                        'statut' => $paiement->statut,
                        'reference' => $paiement->reference,
                        'date_paiement' => $paiement->date_paiement,
                        'notes' => $paiement->notes
                    ],
                    'date_validation' => now()->toISOString()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la validation du paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère tous les certificats des apprenants d'un niveau spécifique
     * @param int $niveauId ID du niveau
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCertificatsByNiveau($niveauId)
    {
        try {
            // Vérifier que l'utilisateur est admin
            if (Auth::user()->type_compte !== 'admin') {
                return response()->json([
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            // Vérifier que le niveau existe
            $niveau = \App\Models\Niveau::find($niveauId);
            if (!$niveau) {
                return response()->json([
                    'success' => false,
                    'error' => 'Niveau non trouvé'
                ], 404);
            }

            // Récupérer tous les certificats des apprenants du niveau
            $certificats = \App\Models\Certificat::with([
                'apprenant.utilisateur',
                'apprenant.niveau',
                'module'
            ])
            ->whereHas('apprenant', function($query) use ($niveauId) {
                $query->where('niveau_id', $niveauId);
            })
            ->orderBy('date_obtention', 'desc')
            ->get();

            // Vérifier que les certificats ont des relations valides
            $certificats = $certificats->filter(function($certificat) {
                return $certificat->apprenant && $certificat->module;
            });

            // Organiser les certificats par apprenant
            $certificatsParApprenant = $certificats->groupBy('apprenant_id');

            // Calculer les statistiques
            $totalCertificats = $certificats->count();
            $totalApprenantsAvecCertificats = $certificatsParApprenant->count();
            $apprenantsDuNiveau = \App\Models\Apprenant::where('niveau_id', $niveauId)->count();
            $tauxReussite = $apprenantsDuNiveau > 0 ? round(($totalApprenantsAvecCertificats / $apprenantsDuNiveau) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre
                ],
                'certificats' => $certificats->map(function($certificat) {
                    // Vérifier que toutes les relations existent avant d'accéder aux propriétés
                    if (!$certificat->apprenant || !$certificat->apprenant->utilisateur || !$certificat->module) {
                        return null;
                    }
                    
                    return [
                        'id' => $certificat->id,
                        'titre' => $certificat->titre,
                        'date_obtention' => $certificat->date_obtention,
                        'fichier' => $certificat->fichier,
                        'apprenant' => [
                            'id' => $certificat->apprenant->id,
                            'nom' => $certificat->apprenant->utilisateur->nom ?? 'N/A',
                            'prenom' => $certificat->apprenant->utilisateur->prenom ?? 'N/A',
                            'email' => $certificat->apprenant->utilisateur->email ?? 'N/A',
                            'telephone' => $certificat->apprenant->utilisateur->telephone ?? 'N/A'
                        ],
                        'module' => [
                            'id' => $certificat->module->id,
                            'titre' => $certificat->module->titre ?? 'N/A',
                            'description' => $certificat->module->description ?? 'N/A'
                        ],
                        'created_at' => $certificat->created_at,
                        'updated_at' => $certificat->updated_at
                    ];
                })->filter(function($item) {
                    return $item !== null; // Filtrer les éléments null
                }),
                'statistiques' => [
                    'total_certificats' => $totalCertificats,
                    'total_apprenants_avec_certificats' => $totalApprenantsAvecCertificats,
                    'total_apprenants_du_niveau' => $apprenantsDuNiveau,
                    'taux_reussite' => $tauxReussite . '%'
                ],
                'certificats_par_apprenant' => $certificatsParApprenant->map(function($certificatsApprenant, $apprenantId) {
                    $apprenant = $certificatsApprenant->first()->apprenant;
                    
                    // Vérifier que l'apprenant et ses relations existent
                    if (!$apprenant || !$apprenant->utilisateur) {
                        return null;
                    }
                    
                    return [
                        'apprenant_id' => $apprenantId,
                        'nom' => ($apprenant->utilisateur->nom ?? 'N/A') . ' ' . ($apprenant->utilisateur->prenom ?? 'N/A'),
                        'email' => $apprenant->utilisateur->email ?? 'N/A',
                        'nombre_certificats' => $certificatsApprenant->count(),
                        'certificats' => $certificatsApprenant->map(function($cert) {
                            // Vérifier que le module existe
                            if (!$cert->module) {
                                return null;
                            }
                            
                            return [
                                'id' => $cert->id,
                                'titre' => $cert->titre ?? 'N/A',
                                'module' => $cert->module->titre ?? 'N/A',
                                'date_obtention' => $cert->date_obtention ?? 'N/A'
                            ];
                        })->filter(function($item) {
                            return $item !== null; // Filtrer les éléments null
                        })
                    ];
                })->filter(function($item) {
                    return $item !== null; // Filtrer les éléments null
                }),
                'message' => 'Certificats du niveau ' . $niveau->nom . ' récupérés avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des certificats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère la liste des apprenants d'un niveau qui ont des certificats disponibles
     * @param int $niveauId ID du niveau
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApprenantsAvecCertificats($niveauId)
    {
        try {
            // Vérifier que l'utilisateur est admin
            if (Auth::user()->type_compte !== 'admin') {
                return response()->json([
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            // Vérifier que le niveau existe
            $niveau = \App\Models\Niveau::find($niveauId);
            if (!$niveau) {
                return response()->json([
                    'success' => false,
                    'error' => 'Niveau non trouvé'
                ], 404);
            }

            // Récupérer les apprenants du niveau qui ont des certificats
            $apprenantsAvecCertificats = \App\Models\Apprenant::with([
                'utilisateur',
                'niveau',
                'certificats.module'
            ])
            ->where('niveau_id', $niveauId)
            ->whereHas('certificats') // Seulement ceux qui ont des certificats
            ->get()
            ->map(function($apprenant) {
                // Vérifier que l'apprenant et ses relations existent
                if (!$apprenant->utilisateur) {
                    return null;
                }

                return [
                    'id' => $apprenant->id,
                    'utilisateur_id' => $apprenant->utilisateur_id,
                    'niveau_id' => $apprenant->niveau_id,
                    'date_inscription' => $apprenant->date_inscription,
                    'statut' => $apprenant->statut,
                    'utilisateur' => [
                        'id' => $apprenant->utilisateur->id,
                        'nom' => $apprenant->utilisateur->nom ?? 'N/A',
                        'prenom' => $apprenant->utilisateur->prenom ?? 'N/A',
                        'email' => $apprenant->utilisateur->email ?? 'N/A',
                        'telephone' => $apprenant->utilisateur->telephone ?? 'N/A',
                        'sexe' => $apprenant->utilisateur->sexe ?? 'N/A',
                        'categorie' => $apprenant->utilisateur->categorie ?? 'N/A',
                        'actif' => $apprenant->utilisateur->actif,
                        'email_verified_at' => $apprenant->utilisateur->email_verified_at
                    ],
                    'niveau' => [
                        'id' => $apprenant->niveau->id ?? 'N/A',
                        'nom' => $apprenant->niveau->nom ?? 'N/A',
                        'description' => $apprenant->niveau->description ?? 'N/A',
                        'ordre' => $apprenant->niveau->ordre ?? 'N/A'
                    ],
                    'certificats' => $apprenant->certificats->map(function($certificat) {
                        // Vérifier que le certificat et ses relations existent
                        if (!$certificat->module) {
                            return null;
                        }

                        return [
                            'id' => $certificat->id,
                            'titre' => $certificat->titre ?? 'N/A',
                            'date_obtention' => $certificat->date_obtention ?? 'N/A',
                            'fichier' => $certificat->fichier ?? 'N/A',
                            'module' => [
                                'id' => $certificat->module->id ?? 'N/A',
                                'titre' => $certificat->module->titre ?? 'N/A',
                                'description' => $certificat->module->description ?? 'N/A'
                            ],
                            'created_at' => $certificat->created_at,
                            'updated_at' => $certificat->updated_at
                        ];
                    })->filter(function($item) {
                        return $item !== null; // Filtrer les éléments null
                    }),
                    'nombre_certificats' => $apprenant->certificats->count(),
                    'dernier_certificat' => $apprenant->certificats->sortByDesc('date_obtention')->first() ? [
                        'titre' => $apprenant->certificats->sortByDesc('date_obtention')->first()->titre ?? 'N/A',
                        'date' => $apprenant->certificats->sortByDesc('date_obtention')->first()->date_obtention ?? 'N/A',
                        'module' => $apprenant->certificats->sortByDesc('date_obtention')->first()->module->titre ?? 'N/A'
                    ] : null,
                    'created_at' => $apprenant->created_at,
                    'updated_at' => $apprenant->updated_at
                ];
            })
            ->filter(function($item) {
                return $item !== null; // Filtrer les éléments null
            });

            // Calculer les statistiques
            $totalApprenantsAvecCertificats = $apprenantsAvecCertificats->count();
            $totalApprenantsDuNiveau = \App\Models\Apprenant::where('niveau_id', $niveauId)->count();
            $tauxCertification = $totalApprenantsDuNiveau > 0 ? round(($totalApprenantsAvecCertificats / $totalApprenantsDuNiveau) * 100, 2) : 0;

            // Organiser par nombre de certificats (du plus certifié au moins)
            $apprenantsAvecCertificats = $apprenantsAvecCertificats->sortByDesc('nombre_certificats');

            return response()->json([
                'success' => true,
                'niveau' => [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description,
                    'ordre' => $niveau->ordre
                ],
                'apprenants_avec_certificats' => $apprenantsAvecCertificats->values(),
                'statistiques' => [
                    'total_apprenants_avec_certificats' => $totalApprenantsAvecCertificats,
                    'total_apprenants_du_niveau' => $totalApprenantsDuNiveau,
                    'taux_certification' => $tauxCertification . '%',
                    'moyenne_certificats_par_apprenant' => $totalApprenantsAvecCertificats > 0 ? 
                        round($apprenantsAvecCertificats->sum('nombre_certificats') / $totalApprenantsAvecCertificats, 2) : 0
                ],
                'resume' => [
                    'niveau' => $niveau->nom,
                    'apprenants_certifies' => $totalApprenantsAvecCertificats,
                    'total_apprenants' => $totalApprenantsDuNiveau,
                    'pourcentage_reussite' => $tauxCertification . '%'
                ],
                'message' => 'Apprenants avec certificats du niveau ' . $niveau->nom . ' récupérés avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des apprenants avec certificats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère tous les certificats d'un utilisateur spécifique via sa relation apprenant
     * @param int $utilisateurId ID de l'utilisateur
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCertificatsParUtilisateur($utilisateurId)
    {
        try {
            // Vérifier que l'utilisateur est admin
            if (Auth::user()->type_compte !== 'admin') {
                return response()->json([
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            // Récupérer l'utilisateur avec sa relation apprenant et ses certificats
            $utilisateur = \App\Models\Utilisateur::with([
                'apprenant.niveau',
                'apprenant.certificats.module'
            ])->find($utilisateurId);

            if (!$utilisateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Utilisateur non trouvé'
                ], 404);
            }

            // Vérifier que l'utilisateur a un profil apprenant
            if (!$utilisateur->apprenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cet utilisateur n\'a pas de profil apprenant'
                ], 404);
            }

            $apprenant = $utilisateur->apprenant;

            // Récupérer tous les certificats de cet apprenant (déjà chargés via la relation)
            $certificats = $apprenant->certificats;

            // Vérifier que l'apprenant a des certificats
            if ($certificats->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'apprenant' => [
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
                        'utilisateur' => [
                            'id' => $apprenant->utilisateur->id ?? 'N/A',
                            'nom' => $apprenant->utilisateur->nom ?? 'N/A',
                            'prenom' => $apprenant->utilisateur->prenom ?? 'N/A',
                            'email' => $apprenant->utilisateur->email ?? 'N/A',
                            'telephone' => $apprenant->utilisateur->telephone ?? 'N/A',
                            'sexe' => $apprenant->utilisateur->sexe ?? 'N/A',
                            'categorie' => $apprenant->utilisateur->categorie ?? 'N/A',
                            'actif' => $apprenant->utilisateur->actif ?? 'N/A',
                            'email_verified_at' => $apprenant->utilisateur->email_verified_at
                        ],
                        'niveau' => [
                            'id' => $apprenant->niveau->id ?? 'N/A',
                            'nom' => $apprenant->niveau->nom ?? 'N/A',
                            'description' => $apprenant->niveau->description ?? 'N/A',
                            'ordre' => $apprenant->niveau->ordre ?? 'N/A'
                        ]
                    ],
                    'certificats' => [],
                    'total_certificats' => 0,
                    'message' => 'Cet apprenant n\'a pas encore de certificats'
                ], 200);
            }

            // Traiter les certificats
            $certificatsTraites = $certificats->map(function($certificat) {
                // Gérer le cas où module_id est NULL
                $moduleInfo = [
                    'id' => $certificat->module_id ?? 'N/A',
                    'titre' => $certificat->module ? ($certificat->module->titre ?? 'N/A') : 'N/A',
                    'description' => $certificat->module ? ($certificat->module->description ?? 'N/A') : 'N/A',
                    'niveau_id' => $certificat->module ? ($certificat->module->niveau_id ?? 'N/A') : 'N/A'
                ];

                return [
                    'id' => $certificat->id,
                    'apprenant_id' => $certificat->apprenant_id,
                    'module_id' => $certificat->module_id,
                    'titre' => $certificat->titre ?? 'N/A',
                    'date_obtention' => $certificat->date_obtention ?? 'N/A',
                    'fichier' => $certificat->fichier ?? 'N/A',
                    'created_at' => $certificat->created_at,
                    'updated_at' => $certificat->updated_at,
                    'module' => $moduleInfo
                ];
            });

            // Calculer les statistiques
            $totalCertificats = $certificatsTraites->count();
            $certificatsParModule = $certificatsTraites->groupBy('module.id');
            $dernierCertificat = $certificatsTraites->sortByDesc('date_obtention')->first();

            // Organiser les certificats par module
            $certificatsOrganises = $certificatsTraites->groupBy('module.titre');

            return response()->json([
                'success' => true,
                'apprenant' => [
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
                    'utilisateur' => [
                        'id' => $apprenant->utilisateur->id ?? 'N/A',
                        'nom' => $apprenant->utilisateur->nom ?? 'N/A',
                        'prenom' => $apprenant->utilisateur->prenom ?? 'N/A',
                        'email' => $apprenant->utilisateur->email ?? 'N/A',
                        'telephone' => $apprenant->utilisateur->telephone ?? 'N/A',
                        'sexe' => $apprenant->utilisateur->sexe ?? 'N/A',
                        'categorie' => $apprenant->utilisateur->categorie ?? 'N/A',
                        'actif' => $apprenant->utilisateur->actif ?? 'N/A',
                        'email_verified_at' => $apprenant->utilisateur->email_verified_at
                    ],
                    'niveau' => [
                        'id' => $apprenant->niveau->id ?? 'N/A',
                        'nom' => $apprenant->niveau->nom ?? 'N/A',
                        'description' => $apprenant->niveau->description ?? 'N/A',
                        'ordre' => $apprenant->niveau->ordre ?? 'N/A'
                    ]
                ],
                'certificats' => $certificatsTraites->values(),
                'certificats_par_module' => $certificatsOrganises->map(function($certificatsModule, $nomModule) {
                    return [
                        'module' => $nomModule,
                        'nombre_certificats' => $certificatsModule->count(),
                        'certificats' => $certificatsModule->map(function($cert) {
                            return [
                                'id' => $cert['id'],
                                'titre' => $cert['titre'],
                                'date_obtention' => $cert['date_obtention'],
                                'fichier' => $cert['fichier']
                            ];
                        })
                    ];
                }),
                'statistiques' => [
                    'total_certificats' => $totalCertificats,
                    'nombre_modules_certifies' => $certificatsParModule->count(),
                    'dernier_certificat' => $dernierCertificat ? [
                        'titre' => $dernierCertificat['titre'],
                        'date' => $dernierCertificat['date_obtention'],
                        'module' => $dernierCertificat['module']['titre']
                    ] : null,
                    'moyenne_certificats_par_module' => $certificatsParModule->count() > 0 ? 
                        round($totalCertificats / $certificatsParModule->count(), 2) : 0
                ],
                'resume' => [
                    'apprenant' => ($apprenant->utilisateur->prenom ?? 'N/A') . ' ' . ($apprenant->utilisateur->nom ?? 'N/A'),
                    'niveau' => $apprenant->niveau->nom ?? 'N/A',
                    'total_certificats' => $totalCertificats,
                    'statut' => $totalCertificats > 0 ? 'Certifié' : 'Non certifié'
                ],
                'message' => 'Certificats de l\'apprenant récupérés avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération du certificat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Télécharge un certificat spécifique
     * @param int $certificatId ID du certificat
     * @return \Illuminate\Http\Response
     */
    public function telechargerCertificat($certificatId)
    {
        try {
            // Vérifier que l'utilisateur est admin
            if (Auth::user()->type_compte !== 'admin') {
                return response()->json([
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent télécharger des certificats.'
                ], 403);
            }

            // Récupérer le certificat avec ses relations
            $certificat = \App\Models\Certificat::with(['apprenant.utilisateur', 'module'])
                ->find($certificatId);

            if (!$certificat) {
                return response()->json([
                    'success' => false,
                    'error' => 'Certificat non trouvé'
                ], 404);
            }

            // Vérifier que le fichier existe
            if (!$certificat->fichier || empty($certificat->fichier)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce certificat n\'a pas de fichier associé'
                ], 404);
            }

            // Construire le chemin du fichier
            $cheminFichier = storage_path('app/certificats/' . $certificat->fichier);

            // Vérifier que le fichier existe physiquement
            if (!file_exists($cheminFichier)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Le fichier du certificat n\'existe pas sur le serveur'
                ], 404);
            }

            // Générer le nom du fichier pour le téléchargement
            $nomFichier = 'certificat_' . $certificat->apprenant->utilisateur->nom . '_' . 
                          $certificat->apprenant->utilisateur->prenom . '_' . 
                          $certificat->date_obtention . '.pdf';

            // Retourner le fichier pour téléchargement
            return response()->download($cheminFichier, $nomFichier, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $nomFichier . '"'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du téléchargement du certificat: ' . $e->getMessage()
            ], 500);
        }
    }
}
