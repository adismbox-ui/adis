<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Apprenant;
use App\Models\Module;
use App\Models\Niveau;

class DemandePaiementApiController extends Controller
{
    /**
     * L'apprenant connecté crée une demande de paiement automatiquement
     */
    public function store(Request $request)
    {
        try {
            // Vérifier que l'utilisateur est connecté et est un apprenant
            $user = Auth::guard('api')->user();
            if (!$user || $user->type_compte !== 'apprenant') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé. Seuls les apprenants peuvent créer des demandes de paiement.'
                ], 403);
            }

            // Récupérer l'apprenant
            $apprenant = $user->apprenant;
            if (!$apprenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun profil apprenant trouvé pour cet utilisateur.'
                ], 404);
            }

            // Vérifier si l'apprenant a déjà un niveau assigné
            if (!$apprenant->niveau_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun niveau assigné à cet apprenant. Veuillez contacter l\'administrateur.'
                ], 400);
            }

            // Récupérer automatiquement le niveau actuel de l'apprenant
            $niveau = $apprenant->niveau;
            if (!$niveau) {
                return response()->json([
                    'success' => false,
                    'error' => 'Niveau non trouvé.'
                ], 404);
            }

            // Récupérer automatiquement tous les modules du niveau de l'apprenant
            $modules = Module::where('niveau_id', $apprenant->niveau_id)
                ->orderBy('date_debut', 'asc') // Utiliser date_debut au lieu de ordre
                ->get();

            if ($modules->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun module disponible pour votre niveau actuel.'
                ], 400);
            }

            // Vérifier si l'apprenant a déjà des paiements en attente pour ce niveau
            $paiementsExistants = \App\Models\Paiement::where('apprenant_id', $apprenant->id)
                ->whereIn('module_id', $modules->pluck('id'))
                ->where('statut', 'en_attente')
                ->get();

            if ($paiementsExistants->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Vous avez déjà des demandes de paiement en attente pour certains modules de ce niveau.',
                    'paiements_existants' => $paiementsExistants->map(function($paiement) {
                        return [
                            'id' => $paiement->id,
                            'module_id' => $paiement->module_id,
                            'statut' => $paiement->statut,
                            'date_paiement' => $paiement->date_paiement
                        ];
                    })
                ], 409);
            }

            // Préparer automatiquement les données des modules avec leurs prix
            $modulesDemandes = $modules->map(function ($module) {
                return [
                    'id' => $module->id,
                    'titre' => $module->titre,
                    'prix' => $module->prix ?? 0,
                    'description' => $module->description,
                    'date_debut' => $module->date_debut,
                    'date_fin' => $module->date_fin
                ];
            })->toArray();

            // Calculer automatiquement le montant total
            $montantTotal = $modules->sum('prix');

            // Créer automatiquement les paiements pour tous les modules du niveau
            $paiementsCrees = [];
            
            foreach ($modules as $module) {
                $paiement = \App\Models\Paiement::create([
                'apprenant_id' => $apprenant->id,
                    'module_id' => $module->id,
                    'montant' => $module->prix ?? 0,
                    'date_paiement' => now(),
                'statut' => 'en_attente',
                    'methode' => 'demande_automatique',
                    'reference' => 'DEMANDE_' . $apprenant->id . '_MODULE_' . $module->id . '_' . time(),
                    'notes' => 'Demande de paiement automatique pour le module ' . $module->titre,
                    'informations_paiement' => json_encode([
                        'type_demande' => 'automatique_niveau',
                        'niveau_id' => $apprenant->niveau_id,
                        'niveau_nom' => $niveau->nom,
                        'module_titre' => $module->titre,
                        'module_description' => $module->description,
                        'date_debut_module' => $module->date_debut,
                        'date_fin_module' => $module->date_fin,
                        'demande_creer_le' => now()->toISOString(),
                        'apprenant_nom' => $apprenant->utilisateur->nom,
                        'apprenant_prenom' => $apprenant->utilisateur->prenom
                    ])
                ]);
                
                $paiementsCrees[] = [
                    'id' => $paiement->id,
                    'module_id' => $paiement->module_id,
                    'module_titre' => $module->titre,
                    'montant' => $paiement->montant,
                    'reference' => $paiement->reference,
                    'statut' => $paiement->statut
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Demandes de paiement créées avec succès pour tous les modules du niveau. Elles seront traitées par l\'administrateur.',
                'apprenant' => [
                    'id' => $apprenant->id,
                    'nom' => $apprenant->utilisateur->nom,
                    'prenom' => $apprenant->utilisateur->prenom,
                    'email' => $apprenant->utilisateur->email
                ],
                    'niveau' => [
                        'id' => $niveau->id,
                        'nom' => $niveau->nom,
                        'description' => $niveau->description
                    ],
                'paiements_crees' => $paiementsCrees,
                'total_modules' => $modules->count(),
                'montant_total' => $montantTotal,
                'date_creation' => now()
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la création de la demande: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * L'apprenant connecté récupère ses demandes de paiement
     */
    public function mesDemandes()
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user || $user->type_compte !== 'apprenant') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé.'
                ], 403);
            }

            $apprenant = $user->apprenant;
            if (!$apprenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun profil apprenant trouvé.'
                ], 404);
            }

            $paiements = \App\Models\Paiement::where('apprenant_id', $apprenant->id)
                ->with(['module.niveau:id,nom,description', 'module.formateur.utilisateur:id,nom,prenom'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Enrichir les données avec plus d'informations
            $paiementsEnrichis = $paiements->map(function ($paiement) {
                $paiementData = [
                    'id' => $paiement->id,
                    'module' => [
                        'id' => $paiement->module->id,
                        'titre' => $paiement->module->titre,
                        'description' => $paiement->module->description,
                        'discipline' => $paiement->module->discipline,
                        'date_debut' => $paiement->module->date_debut,
                        'date_fin' => $paiement->module->date_fin,
                    'niveau' => [
                            'id' => $paiement->module->niveau->id,
                            'nom' => $paiement->module->niveau->nom,
                            'description' => $paiement->module->niveau->description
                        ],
                        'formateur' => $paiement->module->formateur ? [
                            'id' => $paiement->module->formateur->id,
                            'nom' => $paiement->module->formateur->utilisateur->nom,
                            'prenom' => $paiement->module->formateur->utilisateur->prenom
                        ] : null
                    ],
                    'montant' => $paiement->montant,
                    'statut' => $paiement->statut,
                    'methode' => $paiement->methode,
                    'reference' => $paiement->reference,
                    'notes' => $paiement->notes,
                    'date_paiement' => $paiement->date_paiement,
                    'date_creation' => $paiement->created_at,
                    'derniere_modification' => $paiement->updated_at,
                    'informations_paiement' => $paiement->informations_paiement ? json_decode($paiement->informations_paiement, true) : null
                ];

                // Ajouter des informations sur le statut
                switch ($paiement->statut) {
                    case 'en_attente':
                        $paiementData['statut_info'] = 'Votre demande de paiement est en cours de traitement par l\'administration';
                        break;
                    case 'valide':
                        $paiementData['statut_info'] = 'Votre paiement a été validé ! Vous pouvez accéder au module';
                        break;
                    case 'refuse':
                        $paiementData['statut_info'] = 'Votre demande de paiement a été refusée. Contactez l\'administration pour plus de détails';
                        break;
                    case 'annule':
                        $paiementData['statut_info'] = 'Votre demande de paiement a été annulée';
                        break;
                }

                return $paiementData;
            });

            return response()->json([
                'success' => true,
                'paiements' => $paiementsEnrichis,
                'total' => $paiements->count(),
                'resume' => [
                    'total_paiements' => $paiements->count(),
                    'en_attente' => $paiements->where('statut', 'en_attente')->count(),
                    'valides' => $paiements->where('statut', 'valide')->count(),
                    'refuses' => $paiements->where('statut', 'refuse')->count(),
                    'annules' => $paiements->where('statut', 'annule')->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des demandes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * L'admin récupère toutes les demandes de paiement
     */
    public function index()
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user || $user->type_compte !== 'admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            $paiements = \App\Models\Paiement::with([
                'apprenant.utilisateur:id,nom,prenom,email,telephone',
                'module.niveau:id,nom,description',
                'module.formateur.utilisateur:id,nom,prenom'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

            return response()->json([
                'success' => true,
                'paiements' => $paiements,
                'total' => $paiements->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des demandes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * L'admin récupère une demande spécifique avec toutes les données de l'apprenant
     */
    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user || $user->type_compte !== 'admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            $demande = \App\Models\Paiement::with([
                'apprenant.utilisateur:id,nom,prenom,email,telephone,sexe,categorie',
                'niveau:id,nom,description,ordre',
                'admin:id,nom,prenom'
            ])->find($id);

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'error' => 'Demande de paiement non trouvée.'
                ], 404);
            }

            // Récupérer les modules du niveau avec plus de détails
            $modulesNiveau = Module::where('niveau_id', $demande->niveau_id)
                ->with(['formateur.utilisateur:id,nom,prenom', 'niveau:id,nom'])
                ->get();

            // Préparer la réponse complète
            $response = [
                'success' => true,
                'demande' => $demande,
                'apprenant' => [
                    'id' => $demande->apprenant->id,
                    'nom' => $demande->apprenant->utilisateur->nom,
                    'prenom' => $demande->apprenant->utilisateur->prenom,
                    'email' => $demande->apprenant->utilisateur->email,
                    'telephone' => $demande->apprenant->utilisateur->telephone,
                    'sexe' => $demande->apprenant->utilisateur->sexe,
                    'categorie' => $demande->apprenant->utilisateur->categorie,
                    'niveau_actuel' => $demande->apprenant->niveau ? [
                        'id' => $demande->apprenant->niveau->id,
                        'nom' => $demande->apprenant->niveau->nom,
                        'description' => $demande->apprenant->niveau->description
                    ] : null
                ],
                'niveau_demande' => [
                    'id' => $demande->niveau->id,
                    'nom' => $demande->niveau->nom,
                    'description' => $demande->niveau->description,
                    'ordre' => $demande->niveau->ordre
                ],
                'modules_demandes' => $demande->modules_demandes,
                'modules_disponibles_niveau' => $modulesNiveau->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'titre' => $module->titre,
                        'description' => $module->description,
                        'prix' => $module->prix,
                        'date_debut' => $module->date_debut,
                        'date_fin' => $module->date_fin,
                        'formateur' => $module->formateur ? [
                            'id' => $module->formateur->id,
                            'nom' => $module->formateur->utilisateur->nom,
                            'prenom' => $module->formateur->utilisateur->prenom
                        ] : null
                    ];
                }),
                'montant_total' => $demande->montant_total,
                'statut' => $demande->statut,
                'date_demande' => $demande->date_demande,
                'date_traitement' => $demande->date_traitement
            ];

            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération de la demande: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * L'admin traite une demande (approuve, refuse, annule)
     */
    public function traiterDemande(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user || $user->type_compte !== 'admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent traiter les demandes.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'statut' => 'required|in:approuvee,refusee,annulee',
                'commentaire_admin' => 'nullable|string|max:500',
                'reference_paiement' => 'nullable|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Données invalides',
                    'details' => $validator->errors()
                ], 422);
            }

            $demande = \App\Models\Paiement::find($id);
            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'error' => 'Demande de paiement non trouvée.'
                ], 404);
            }

            // Mettre à jour la demande
            $demande->update([
                'statut' => $request->statut,
                'notes' => $request->commentaire_admin,
                'reference' => $request->reference_paiement
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Demande traitée avec succès',
                'demande' => $demande
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du traitement de la demande: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * L'admin récupère les demandes par statut
     */
    public function demandesParStatut($statut)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user || $user->type_compte !== 'admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé.'
                ], 403);
            }

                    $paiements = \App\Models\Paiement::where('statut', $statut)
                ->with([
                'apprenant.utilisateur:id,nom,prenom,email,telephone',
                'module.niveau:id,nom,description'
                ])
                ->orderBy('created_at', 'desc')
                ->get();

        // Regrouper les paiements par apprenant
        $paiementsParApprenant = $paiements->groupBy('apprenant_id')->map(function($paiementsApprenant, $apprenantId) {
            $premierPaiement = $paiementsApprenant->first();
            $apprenant = $premierPaiement->apprenant;
            
            return [
                'apprenant' => [
                    'id' => $apprenant->id,
                    'nom' => $apprenant->utilisateur->nom,
                    'prenom' => $apprenant->utilisateur->prenom,
                    'email' => $apprenant->utilisateur->email,
                    'telephone' => $apprenant->utilisateur->telephone,
                    'niveau_actuel' => [
                        'id' => $apprenant->niveau_id,
                        'nom' => $apprenant->niveau ? $apprenant->niveau->nom : 'Non défini'
                    ],
                    'niveau_coran' => $apprenant->niveau_coran,
                    'niveau_arabe' => $apprenant->niveau_arabe,
                    'connaissance_adis' => $apprenant->connaissance_adis,
                    'formation_adis' => $apprenant->formation_adis,
                    'formation_autre' => $apprenant->formation_autre,
                    'disciplines_souhaitees' => $apprenant->disciplines_souhaitees ? json_decode($apprenant->disciplines_souhaitees, true) : [],
                    'attentes' => $apprenant->attentes ? json_decode($apprenant->attentes, true) : []
                ],
                'modules_demandes' => $paiementsApprenant->map(function($paiement) {
                    return [
                        'id' => $paiement->id,
                        'module' => [
                            'id' => $paiement->module->id,
                            'titre' => $paiement->module->titre,
                            'description' => $paiement->module->description,
                            'discipline' => $paiement->module->discipline,
                            'prix' => $paiement->module->prix,
                            'date_debut' => $paiement->module->date_debut,
                            'date_fin' => $paiement->module->date_fin,
                            'horaire' => $paiement->module->horaire,
                            'lien' => $paiement->module->lien,
                            'support' => $paiement->module->support,
                            'audio' => $paiement->module->audio,
                            'certificat' => $paiement->module->certificat,
                            'niveau' => [
                                'id' => $paiement->module->niveau->id,
                                'nom' => $paiement->module->niveau->nom,
                                'description' => $paiement->module->niveau->description
                            ],
                            'formateur' => null
                        ],
                        'paiement' => [
                            'id' => $paiement->id,
                            'montant' => $paiement->montant,
                            'methode' => $paiement->methode,
                            'reference' => $paiement->reference,
                            'notes' => $paiement->notes,
                            'date_paiement' => $paiement->date_paiement,
                            'date_creation' => $paiement->created_at,
                            'statut' => $paiement->statut,
                            'informations_paiement' => $paiement->informations_paiement ? json_decode($paiement->informations_paiement, true) : null
                        ]
                    ];
                })->values(),
                'resume' => [
                    'total_modules' => $paiementsApprenant->count(),
                    'montant_total' => $paiementsApprenant->sum('montant'),
                    'date_premiere_demande' => $paiementsApprenant->min('created_at'),
                    'date_derniere_demande' => $paiementsApprenant->max('created_at')
                ]
            ];
        })->values();

        // Statistiques globales
        $statistiques = [
            'total_apprenants' => $paiementsParApprenant->count(),
            'total_modules_demandes' => $paiements->count(),
            'montant_total_demandes' => $paiements->sum('montant'),
            'modules_par_apprenant_moyenne' => $paiements->count() > 0 ? round($paiements->count() / $paiementsParApprenant->count(), 2) : 0
        ];

            return response()->json([
                'success' => true,
            'message' => 'Demandes de paiement récupérées et organisées par apprenant',
            'statut' => $statut,
            'apprenants' => $paiementsParApprenant,
            'statistiques' => $statistiques,
            'total_apprenants' => $paiementsParApprenant->count(),
            'total_modules' => $paiements->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des demandes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * L'admin valide automatiquement tous les modules d'un apprenant
     */
    public function validerDemande(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user || $user->type_compte !== 'admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent valider les paiements.'
                ], 403);
            }

            // Récupérer le premier paiement pour identifier l'apprenant
            $paiementReference = \App\Models\Paiement::with(['apprenant.utilisateur', 'module.niveau'])
                ->findOrFail($id);

            if ($paiementReference->statut !== 'en_attente') {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce paiement ne peut plus être validé. Statut actuel: ' . $paiementReference->statut
                ], 400);
            }

            $apprenantId = $paiementReference->apprenant_id;
            $apprenant = $paiementReference->apprenant;

            // Récupérer TOUS les paiements en attente de cet apprenant
            $paiementsEnAttente = \App\Models\Paiement::with(['module.niveau'])
                ->where('apprenant_id', $apprenantId)
                ->where('statut', 'en_attente')
                ->orderBy('module_id')
                ->get();

            if ($paiementsEnAttente->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun paiement en attente trouvé pour cet apprenant.'
                ], 404);
            }

            $paiementsValides = [];
            $inscriptionsCrees = [];
            $montantTotalValide = 0;

            // Valider TOUS les paiements en attente de l'apprenant
            foreach ($paiementsEnAttente as $paiement) {
                // Valider le paiement
                $paiement->update([
                    'statut' => 'valide',
                    'notes' => $paiement->notes . ' | Validé par l\'admin ' . $user->nom . ' ' . $user->prenom . ' le ' . now()->format('d/m/Y H:i'),
                    'informations_paiement' => json_encode(array_merge(
                        json_decode($paiement->informations_paiement ?? '{}', true) ?: [],
                        [
                            'validation_admin' => [
                'admin_id' => $user->id,
                                'admin_nom' => $user->nom,
                                'admin_prenom' => $user->prenom,
                                'date_validation' => now()->toISOString(),
                                'methode_validation' => 'validation_automatique_tous_modules',
                                'validation_en_lot' => true
                            ]
                        ]
                    ))
                ]);

                // Créer automatiquement l'inscription au module
                $inscription = \App\Models\Inscription::create([
                    'apprenant_id' => $paiement->apprenant_id,
                    'module_id' => $paiement->module_id,
                    'date_inscription' => now(),
                    'statut' => 'valide',
                    'session_formation_id' => null, // Sera assigné plus tard si nécessaire
                    'notes' => 'Inscription automatique suite à la validation en lot du paiement #' . $paiement->id
                ]);

                $paiementsValides[] = [
                    'id' => $paiement->id,
                    'module' => [
                        'id' => $paiement->module->id,
                        'titre' => $paiement->module->titre,
                        'niveau' => $paiement->module->niveau->nom,
                        'discipline' => $paiement->module->discipline,
                        'prix' => $paiement->module->prix
                    ],
                    'montant' => $paiement->montant,
                    'statut' => $paiement->statut,
                    'date_validation' => now()
                ];

                $inscriptionsCrees[] = [
                    'id' => $inscription->id,
                    'module_id' => $paiement->module_id,
                    'module_titre' => $paiement->module->titre,
                    'statut' => $inscription->statut,
                    'date_inscription' => $inscription->date_inscription
                ];

                $montantTotalValide += $paiement->montant;
            }

            $message = 'Tous les modules de l\'apprenant ont été validés avec succès ! ' . count($paiementsValides) . ' paiement(s) validé(s) et ' . count($inscriptionsCrees) . ' inscription(s) créée(s).';

            return response()->json([
                'success' => true,
                'message' => $message,
                'apprenant' => [
                    'id' => $apprenant->id,
                    'nom' => $apprenant->utilisateur->nom,
                    'prenom' => $apprenant->utilisateur->prenom,
                    'email' => $apprenant->utilisateur->email,
                    'telephone' => $apprenant->utilisateur->telephone,
                    'niveau_actuel' => [
                        'id' => $apprenant->niveau_id,
                        'nom' => $apprenant->niveau ? $apprenant->niveau->nom : 'Non défini'
                    ]
                ],
                'paiements_valides' => $paiementsValides,
                'inscriptions_crees' => $inscriptionsCrees,
                'resume_validation' => [
                    'total_paiements_valides' => count($paiementsValides),
                    'total_inscriptions_crees' => count($inscriptionsCrees),
                    'montant_total_valide' => $montantTotalValide,
                    'date_validation' => now(),
                    'admin_validateur' => $user->nom . ' ' . $user->prenom
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la validation des modules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * L'admin refuse automatiquement tous les modules d'un apprenant
     */
    public function refuserDemande(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user || $user->type_compte !== 'admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent refuser les paiements.'
                ], 403);
            }

            // Récupérer le premier paiement pour identifier l'apprenant
            $paiementReference = \App\Models\Paiement::with(['apprenant.utilisateur', 'module.niveau', 'module.formateur.utilisateur'])
                ->findOrFail($id);

            if ($paiementReference->statut !== 'en_attente') {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce paiement ne peut plus être refusé. Statut actuel: ' . $paiementReference->statut
                ], 400);
            }

            $apprenantId = $paiementReference->apprenant_id;
            $apprenant = $paiementReference->apprenant;

            // Récupérer TOUS les paiements en attente de cet apprenant
            $paiementsEnAttente = \App\Models\Paiement::with(['module.niveau', 'module.formateur.utilisateur'])
                ->where('apprenant_id', $apprenantId)
                ->where('statut', 'en_attente')
                ->orderBy('module_id')
                ->get();

            if ($paiementsEnAttente->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun paiement en attente trouvé pour cet apprenant.'
                ], 404);
            }

            $paiementsRefuses = [];
            $montantTotalRefuse = 0;

            // Refuser TOUS les paiements en attente de l'apprenant
            foreach ($paiementsEnAttente as $paiement) {
                // Refuser le paiement
                $paiement->update([
                    'statut' => 'refuse',
                    'notes' => $paiement->notes . ' | Refusé par l\'admin ' . $user->nom . ' ' . $user->prenom . ' le ' . now()->format('d/m/Y H:i'),
                    'informations_paiement' => json_encode(array_merge(
                        json_decode($paiement->informations_paiement ?? '{}', true) ?: [],
                        [
                            'refus_admin' => [
                'admin_id' => $user->id,
                                'admin_nom' => $user->nom,
                                'admin_prenom' => $user->prenom,
                                'date_refus' => now()->toISOString(),
                                'methode_refus' => 'refus_automatique_tous_modules',
                                'refus_en_lot' => true
                            ]
                        ]
                    ))
                ]);

                $paiementsRefuses[] = [
                    'id' => $paiement->id,
                    'module' => [
                        'id' => $paiement->module->id,
                        'titre' => $paiement->module->titre,
                        'niveau' => $paiement->module->niveau->nom,
                        'discipline' => $paiement->module->discipline,
                        'prix' => $paiement->module->prix
                    ],
                    'montant' => $paiement->montant,
                    'statut' => $paiement->statut,
                    'date_refus' => now()
                ];

                $montantTotalRefuse += $paiement->montant;
            }

            $message = 'Tous les modules de l\'apprenant ont été refusés avec succès ! ' . count($paiementsRefuses) . ' paiement(s) refusé(s).';

            return response()->json([
                'success' => true,
                'message' => $message,
                'apprenant' => [
                    'id' => $apprenant->id,
                    'nom' => $apprenant->utilisateur->nom,
                    'prenom' => $apprenant->utilisateur->prenom,
                    'email' => $apprenant->utilisateur->email,
                    'telephone' => $apprenant->utilisateur->telephone,
                    'niveau_actuel' => [
                        'id' => $apprenant->niveau_id,
                        'nom' => $apprenant->niveau ? $apprenant->niveau->nom : 'Non défini'
                    ]
                ],
                'paiements_refuses' => $paiementsRefuses,
                'resume_refus' => [
                    'total_paiements_refuses' => count($paiementsRefuses),
                    'montant_total_refuse' => $montantTotalRefuse,
                    'date_refus' => now(),
                    'admin_refuseur' => $user->nom . ' ' . $user->prenom
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du refus des modules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * L'admin annule une demande de paiement
     */
    public function annulerDemande(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user || $user->type_compte !== 'admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent annuler les demandes.'
                ], 403);
            }

            $demande = \App\Models\Paiement::find($id);
            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'error' => 'Demande de paiement non trouvée.'
                ], 404);
            }

            if ($demande->statut !== 'en_attente') {
                return response()->json([
                    'success' => false,
                    'error' => 'Cette demande ne peut plus être annulée. Statut actuel: ' . $demande->statut
                ], 400);
            }

            // Annuler la demande automatiquement
            $demande->update([
                'statut' => 'annulee',
                'notes' => 'Demande annulée par l\'administrateur'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Demande de paiement annulée avec succès',
                'demande' => [
                    'id' => $demande->id,
                    'statut' => $demande->statut,
                    'admin_id' => $demande->admin_id,
                    'date_traitement' => $demande->date_traitement,
                    'commentaire_admin' => $demande->commentaire_admin
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'annulation de la demande: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Méthode de test pour diagnostiquer les modules disponibles
     */
    public function testModules(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user || $user->type_compte !== 'apprenant') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé. Seuls les apprenants peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            $apprenant = $user->apprenant;
            if (!$apprenant) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun profil apprenant trouvé pour cet utilisateur.'
                ], 404);
            }

            // Informations de l'apprenant
            $infoApprenant = [
                'id' => $apprenant->id,
                'niveau_id' => $apprenant->niveau_id,
                'niveau_nom' => $apprenant->niveau ? $apprenant->niveau->nom : 'Aucun niveau'
            ];

            // Modules disponibles pour ce niveau
            $modules = Module::where('niveau_id', $apprenant->niveau_id)->get();
            
            $modulesInfo = $modules->map(function ($module) {
                return [
                    'id' => $module->id,
                    'titre' => $module->titre,
                    'prix' => $module->prix,
                    'description' => $module->description,
                    'date_debut' => $module->date_debut,
                    'date_fin' => $module->date_fin
                ];
            });

            // Vérifier s'il y a déjà des paiements en attente
            $paiementsExistants = \App\Models\Paiement::where('apprenant_id', $apprenant->id)
                ->whereIn('module_id', $modules->pluck('id'))
                ->where('statut', 'en_attente')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Diagnostic des modules',
                'apprenant' => $infoApprenant,
                'modules_disponibles' => $modulesInfo,
                'total_modules' => $modules->count(),
                'paiements_existants' => $paiementsExistants->map(function($paiement) {
                    return [
                        'id' => $paiement->id,
                        'module_id' => $paiement->module_id,
                        'statut' => $paiement->statut,
                        'date_paiement' => $paiement->date_paiement
                    ];
                }),
                'montant_total_calcule' => $modules->sum('prix')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du diagnostic: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * L'admin récupère les demandes refusées
     */
    public function demandesRefusees()
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user || $user->type_compte !== 'admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            // Récupérer tous les paiements refusés
            $paiements = \App\Models\Paiement::where('statut', 'refuse')
                ->with([
                    'apprenant.utilisateur:id,nom,prenom,email,telephone',
                    'module.niveau:id,nom,description',
                    'module.formateur.utilisateur:id,nom,prenom'
                ])
                ->orderBy('updated_at', 'desc')
                ->get();

            // Regrouper les paiements par apprenant
            $paiementsParApprenant = $paiements->groupBy('apprenant_id')->map(function($paiementsApprenant, $apprenantId) {
                $premierPaiement = $paiementsApprenant->first();
                $apprenant = $premierPaiement->apprenant;
                
                return [
                    'apprenant' => [
                        'id' => $apprenant->id,
                        'nom' => $apprenant->utilisateur->nom,
                        'prenom' => $apprenant->utilisateur->prenom,
                        'email' => $apprenant->utilisateur->email,
                        'telephone' => $apprenant->utilisateur->telephone,
                        'niveau_actuel' => [
                            'id' => $apprenant->niveau_id,
                            'nom' => $apprenant->niveau ? $apprenant->niveau->nom : 'Non défini'
                        ]
                    ],
                    'modules_refuses' => $paiementsApprenant->map(function($paiement) {
                        return [
                            'id' => $paiement->id,
                            'module' => [
                                'id' => $paiement->module->id,
                                'titre' => $paiement->module->titre,
                                'description' => $paiement->module->description,
                                'discipline' => $paiement->module->discipline,
                                'prix' => $paiement->module->prix,
                                'date_debut' => $paiement->module->date_debut,
                                'date_fin' => $paiement->module->date_fin,
                    'niveau' => [
                                    'id' => $paiement->module->niveau->id,
                                    'nom' => $paiement->module->niveau->nom,
                                    'description' => $paiement->module->niveau->description
                                ],
                                'formateur' => $paiement->module->formateur ? [
                                    'id' => $paiement->module->formateur->id,
                                    'nom' => $paiement->module->formateur->utilisateur->nom,
                                    'prenom' => $paiement->module->formateur->utilisateur->prenom,
                                    'specialite' => $paiement->module->formateur->specialite
                                ] : null
                            ],
                            'paiement' => [
                                'id' => $paiement->id,
                                'montant' => $paiement->montant,
                                'methode' => $paiement->methode,
                                'reference' => $paiement->reference,
                                'notes' => $paiement->notes,
                                'date_paiement' => $paiement->date_paiement,
                                'date_refus' => $paiement->updated_at,
                                'statut' => $paiement->statut,
                                'informations_paiement' => $paiement->informations_paiement ? json_decode($paiement->informations_paiement, true) : null
                            ]
                        ];
                    })->values(),
                    'resume' => [
                        'total_modules' => $paiementsApprenant->count(),
                        'montant_total' => $paiementsApprenant->sum('montant'),
                        'date_premiere_refus' => $paiementsApprenant->min('updated_at'),
                        'date_dernier_refus' => $paiementsApprenant->max('updated_at')
                    ]
                ];
            })->values();

            // Statistiques globales
            $statistiques = [
                'total_apprenants' => $paiementsParApprenant->count(),
                'total_modules_refuses' => $paiements->count(),
                'montant_total_refuse' => $paiements->sum('montant'),
                'modules_par_apprenant_moyenne' => $paiements->count() > 0 ? round($paiements->count() / $paiementsParApprenant->count(), 2) : 0
            ];

            return response()->json([
                'success' => true,
                'message' => 'Paiements refusés récupérés et organisés par apprenant',
                'paiements_refuses' => $paiementsParApprenant,
                'statistiques' => $statistiques,
                'total_apprenants' => $paiementsParApprenant->count(),
                'total_modules' => $paiements->count(),
                'resume' => [
                    'total_refusees' => $paiementsParApprenant->count(),
                    'total_montant_refuse' => $paiements->sum('montant')
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des demandes refusées: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * L'admin récupère les paiements validés (acceptés)
     */
    public function demandesAcceptees()
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user || $user->type_compte !== 'admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            // Récupérer tous les paiements validés
            $paiements = \App\Models\Paiement::where('statut', 'valide')
                ->with([
                    'apprenant.utilisateur:id,nom,prenom,email,telephone',
                    'module.niveau:id,nom,description'
                ])
                ->orderBy('updated_at', 'desc')
                ->get();

            // Regrouper les paiements par apprenant
            $paiementsParApprenant = $paiements->groupBy('apprenant_id')->map(function($paiementsApprenant, $apprenantId) {
                $premierPaiement = $paiementsApprenant->first();
                $apprenant = $premierPaiement->apprenant;
                
                return [
                    'apprenant' => [
                        'id' => $apprenant->id,
                        'nom' => $apprenant->utilisateur->nom,
                        'prenom' => $apprenant->utilisateur->prenom,
                        'email' => $apprenant->utilisateur->email,
                        'telephone' => $apprenant->utilisateur->telephone,
                        'niveau_actuel' => [
                            'id' => $apprenant->niveau_id,
                            'nom' => $apprenant->niveau ? $apprenant->niveau->nom : 'Non défini'
                        ]
                    ],
                    'modules_valides' => $paiementsApprenant->map(function($paiement) {
                        return [
                            'id' => $paiement->id,
                            'module' => [
                                'id' => $paiement->module->id,
                                'titre' => $paiement->module->titre,
                                'description' => $paiement->module->description,
                                'discipline' => $paiement->module->discipline,
                                'prix' => $paiement->module->prix,
                                'date_debut' => $paiement->module->date_debut,
                                'date_fin' => $paiement->module->date_fin,
                    'niveau' => [
                                    'id' => $paiement->module->niveau->id,
                                    'nom' => $paiement->module->niveau->nom,
                                    'description' => $paiement->module->niveau->description
                                ],
                                'formateur' => $paiement->module->formateur ? [
                                    'id' => $paiement->module->formateur->id,
                                    'nom' => $paiement->module->formateur->utilisateur->nom,
                                    'prenom' => $paiement->module->formateur->utilisateur->prenom,
                                    'specialite' => $paiement->module->formateur->specialite
                                ] : null
                            ],
                            'paiement' => [
                                'id' => $paiement->id,
                                'montant' => $paiement->montant,
                                'methode' => $paiement->methode,
                                'reference' => $paiement->reference,
                                'notes' => $paiement->notes,
                                'date_paiement' => $paiement->date_paiement,
                                'date_validation' => $paiement->updated_at,
                                'statut' => $paiement->statut,
                                'informations_paiement' => $paiement->informations_paiement ? json_decode($paiement->informations_paiement, true) : null
                            ]
                        ];
                    })->values(),
                    'resume' => [
                        'total_modules' => $paiementsApprenant->count(),
                        'montant_total' => $paiementsApprenant->sum('montant'),
                        'date_premiere_validation' => $paiementsApprenant->min('updated_at'),
                        'date_derniere_validation' => $paiementsApprenant->max('updated_at')
                    ]
                ];
            })->values();

            // Statistiques globales
            $statistiques = [
                'total_apprenants' => $paiementsParApprenant->count(),
                'total_modules_valides' => $paiements->count(),
                'montant_total_valide' => $paiements->sum('montant'),
                'modules_par_apprenant_moyenne' => $paiements->count() > 0 ? round($paiements->count() / $paiementsParApprenant->count(), 2) : 0
            ];

            return response()->json([
                'success' => true,
                'message' => 'Paiements validés récupérés et organisés par apprenant',
                'paiements_valides' => $paiementsParApprenant,
                'statistiques' => $statistiques,
                'total_apprenants' => $paiementsParApprenant->count(),
                'total_modules' => $paiements->count(),
                'resume' => [
                    'total_acceptees' => $paiementsParApprenant->count(),
                    'total_montant_accepte' => $paiements->sum('montant'),
                    'total_revenus' => $paiements->sum('montant')
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des paiements validés: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * L'admin récupère les demandes en attente
     */
    public function demandesEnAttente()
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user || $user->type_compte !== 'admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            $demandes = DemandePaiement::where('statut', 'en_attente')
                ->with([
                    'apprenant.utilisateur:id,nom,prenom,email,telephone',
                    'niveau:id,nom,description'
                ])
                ->orderBy('date_demande', 'asc')
                ->get();

            // Enrichir les données
            $demandesEnrichies = $demandes->map(function ($demande) {
                return [
                    'id' => $demande->id,
                    'apprenant' => [
                        'nom' => $demande->apprenant->utilisateur->nom,
                        'prenom' => $demande->apprenant->utilisateur->prenom,
                        'email' => $demande->apprenant->utilisateur->email,
                        'telephone' => $demande->apprenant->utilisateur->telephone
                    ],
                    'niveau' => [
                        'nom' => $demande->niveau->nom,
                        'description' => $demande->niveau->description
                    ],
                    'modules_demandes' => $demande->modules_demandes,
                    'montant_total' => $demande->montant_total,
                    'date_demande' => $demande->date_demande,
                    'commentaire_apprenant' => $demande->commentaire_apprenant,
                    'duree_attente' => now()->diffInDays($demande->date_demande) . ' jour(s)'
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Demandes en attente récupérées avec succès',
                'demandes' => $demandesEnrichies,
                'total' => $demandes->count(),
                'resume' => [
                    'total_en_attente' => $demandes->count(),
                    'total_montant_en_attente' => $demandes->sum('montant_total'),
                    'demandes_urgentes' => $demandes->filter(function ($demande) {
                        return now()->diffInDays($demande->date_demande) >= 3;
                    })->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des demandes en attente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * L'admin récupère un tableau de bord avec toutes les statistiques
     */
    public function tableauBord()
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user || $user->type_compte !== 'admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès refusé. Seuls les administrateurs peuvent accéder à cette fonctionnalité.'
                ], 403);
            }

            // Statistiques globales
            $totalDemandes = DemandePaiement::count();
            $demandesEnAttente = DemandePaiement::where('statut', 'en_attente')->count();
            $demandesAcceptees = DemandePaiement::where('statut', 'approuvee')->count();
            $demandesRefusees = DemandePaiement::where('statut', 'refusee')->count();
            $demandesAnnulees = DemandePaiement::where('statut', 'annulee')->count();

            // Montants
            $totalMontantEnAttente = DemandePaiement::where('statut', 'en_attente')->sum('montant_total');
            $totalMontantAccepte = DemandePaiement::where('statut', 'approuvee')->sum('montant_total');
            $totalMontantRefuse = DemandePaiement::where('statut', 'refusee')->sum('montant_total');

            // Dernières demandes par statut
            $dernieresEnAttente = DemandePaiement::where('statut', 'en_attente')
                ->with(['apprenant.utilisateur:id,nom,prenom', 'niveau:id,nom'])
                ->orderBy('date_demande', 'desc')
                ->limit(5)
                ->get();

            $dernieresAcceptees = DemandePaiement::where('statut', 'approuvee')
                ->with(['apprenant.utilisateur:id,nom,prenom', 'niveau:id,nom'])
                ->orderBy('date_traitement', 'desc')
                ->limit(5)
                ->get();

            $dernieresRefusees = DemandePaiement::where('statut', 'refusee')
                ->with(['apprenant.utilisateur:id,nom,prenom', 'niveau:id,nom'])
                ->orderBy('date_traitement', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Tableau de bord récupéré avec succès',
                'statistiques' => [
                    'total_demandes' => $totalDemandes,
                    'en_attente' => $demandesEnAttente,
                    'acceptees' => $demandesAcceptees,
                    'refusees' => $demandesRefusees,
                    'annulees' => $demandesAnnulees
                ],
                'montants' => [
                    'total_en_attente' => $totalMontantEnAttente,
                    'total_accepte' => $totalMontantAccepte,
                    'total_refuse' => $totalMontantRefuse,
                    'total_revenus' => $totalMontantAccepte
                ],
                'dernieres_demandes' => [
                    'en_attente' => $dernieresEnAttente->map(function ($demande) {
                        return [
                            'id' => $demande->id,
                            'apprenant' => $demande->apprenant->utilisateur->nom . ' ' . $demande->apprenant->utilisateur->prenom,
                            'niveau' => $demande->niveau->nom,
                            'montant' => $demande->montant_total,
                            'date_demande' => $demande->date_demande
                        ];
                    }),
                    'acceptees' => $dernieresAcceptees->map(function ($demande) {
                        return [
                            'id' => $demande->id,
                            'apprenant' => $demande->apprenant->utilisateur->nom . ' ' . $demande->apprenant->utilisateur->prenom,
                            'niveau' => $demande->niveau->nom,
                            'montant' => $demande->montant_total,
                            'date_traitement' => $demande->date_traitement
                        ];
                    }),
                    'refusees' => $dernieresRefusees->map(function ($demande) {
                        return [
                            'id' => $demande->id,
                            'apprenant' => $demande->apprenant->utilisateur->nom . ' ' . $demande->apprenant->utilisateur->prenom,
                            'niveau' => $demande->niveau->nom,
                            'montant' => $demande->montant_total,
                            'date_traitement' => $demande->date_traitement
                        ];
                    })
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération du tableau de bord: ' . $e->getMessage()
            ], 500);
        }
    }
}
