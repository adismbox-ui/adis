<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\DemandeCoursMaison;

class DemandeCoursMaisonApiController extends Controller
{
    // Affiche la liste des demandes de l'utilisateur connecté
    public function index()
    {
        $user = Auth::user();
        $demandes = DemandeCoursMaison::where('user_id', $user->id)->latest()->get();
        return response()->json(['demandes' => $demandes], 200);
    }

    // Affiche toutes les demandes pour l'admin (exclut les demandes validées et refusées)
    public function adminIndex()
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est admin
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Accès réservé aux administrateurs.'], 403);
        }

        // Récupérer toutes les demandes NON validées et NON refusées avec les relations
        $demandes = DemandeCoursMaison::with(['user'])
            ->whereNotIn('statut', ['validee', 'terminee', 'refuse', 'refusee_formateur', 'annulee'])
            ->latest()
            ->get();

        // Calculer les statistiques (uniquement pour les demandes actives)
        $statistiques = [
            'total_demandes_actives' => $demandes->count(),
            'demandes_en_attente' => $demandes->where('statut', 'en_attente')->count(),
            'en_attente_formateur' => $demandes->where('statut', 'en_attente_formateur')->count(),
            'acceptees_formateur' => $demandes->where('statut', 'acceptee_formateur')->count()
        ];

        return response()->json([
            'success' => true,
            'demandes' => $demandes,
            'statistiques' => $statistiques,
            'message' => 'Liste des demandes de cours à domicile actives (non validées et non refusées)'
        ], 200);
    }

    // Affiche uniquement les demandes refusées pour l'admin
    public function adminIndexRefusees()
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est admin
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Accès réservé aux administrateurs.'], 403);
        }

        // Récupérer uniquement les demandes refusées avec les relations
        $demandes = DemandeCoursMaison::with(['user'])
            ->whereIn('statut', ['refuse', 'refusee_formateur', 'annulee'])
            ->latest()
            ->get();

        // Calculer les statistiques pour les demandes refusées
        $statistiques = [
            'total_demandes_refusees' => $demandes->count(),
            'refusees_admin' => $demandes->where('statut', 'refuse')->count(),
            'refusees_formateur' => $demandes->where('statut', 'refusee_formateur')->count(),
            'annulees' => $demandes->where('statut', 'annulee')->count()
        ];

        return response()->json([
            'success' => true,
            'demandes' => $demandes,
            'statistiques' => $statistiques,
            'message' => 'Liste des demandes de cours à domicile refusées'
        ], 200);
    }

    // Affiche les demandes filtrées par année pour l'admin
    public function adminIndexByYear(Request $request)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est admin
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Accès réservé aux administrateurs.'], 403);
        }

        // Récupérer l'année depuis la requête, par défaut année courante
        $annee = $request->get('annee', date('Y'));
        
        // Valider que l'année est un nombre valide
        if (!is_numeric($annee) || $annee < 2000 || $annee > 2100) {
            return response()->json([
                'error' => 'Année invalide. Veuillez fournir une année entre 2000 et 2100.'
            ], 400);
        }

        // Récupérer les demandes pour l'année spécifiée
        $demandes = DemandeCoursMaison::with(['user'])
            ->whereYear('created_at', $annee)
            ->latest()
            ->get();

        // Calculer les statistiques par statut pour l'année
        $statistiques = [
            'annee' => $annee,
            'total_demandes' => $demandes->count(),
            'demandes_en_attente' => $demandes->where('statut', 'en_attente')->count(),
            'demandes_validees' => $demandes->where('statut', 'validee')->count(),
            'en_attente_formateur' => $demandes->where('statut', 'en_attente_formateur')->count(),
            'acceptees_formateur' => $demandes->where('statut', 'acceptee_formateur')->count(),
            'refusees_admin' => $demandes->where('statut', 'refuse')->count(),
            'refusees_formateur' => $demandes->where('statut', 'refusee_formateur')->count(),
            'terminees' => $demandes->where('statut', 'terminee')->count(),
            'annulees' => $demandes->where('statut', 'annulee')->count()
        ];

        // Statistiques par mois pour l'année
        $statistiquesParMois = [];
        for ($mois = 1; $mois <= 12; $mois++) {
            $demandesMois = $demandes->filter(function($demande) use ($mois) {
                return $demande->created_at->month === $mois;
            });
            
            $statistiquesParMois[$mois] = [
                'mois' => $mois,
                'nom_mois' => date('F', mktime(0, 0, 0, $mois, 1)),
                'total' => $demandesMois->count(),
                'en_attente' => $demandesMois->where('statut', 'en_attente')->count(),
                'validees' => $demandesMois->where('statut', 'validee')->count(),
                'refusees' => $demandesMois->whereIn('statut', ['refuse', 'refusee_formateur'])->count(),
                'terminees' => $demandesMois->where('statut', 'terminee')->count()
            ];
        }

        return response()->json([
            'success' => true,
            'annee' => $annee,
            'demandes' => $demandes,
            'statistiques' => $statistiques,
            'statistiques_par_mois' => $statistiquesParMois,
            'message' => "Liste des demandes de cours à domicile pour l'année $annee"
        ], 200);
    }

    // Affiche les demandes filtrées par année et statut pour l'admin
    public function adminIndexByYearAndStatus(Request $request)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est admin
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Accès réservé aux administrateurs.'], 403);
        }

        // Récupérer l'année et le statut depuis la requête
        $annee = $request->get('annee', date('Y'));
        $statut = $request->get('statut');
        
        // Valider que l'année est un nombre valide
        if (!is_numeric($annee) || $annee < 2000 || $annee > 2100) {
            return response()->json([
                'error' => 'Année invalide. Veuillez fournir une année entre 2000 et 2100.'
            ], 400);
        }

        // Statuts valides
        $statutsValides = [
            'en_attente', 'validee', 'en_attente_formateur', 
            'acceptee_formateur', 'refuse', 'refusee_formateur', 
            'terminee', 'annulee'
        ];

        // Construire la requête
        $query = DemandeCoursMaison::with(['user'])
            ->whereYear('created_at', $annee);

        // Ajouter le filtre par statut si spécifié
        if ($statut && in_array($statut, $statutsValides)) {
            $query->where('statut', $statut);
        }

        $demandes = $query->latest()->get();

        // Calculer les statistiques
        $statistiques = [
            'annee' => $annee,
            'statut_filtre' => $statut ?: 'tous',
            'total_demandes' => $demandes->count(),
            'demandes_en_attente' => $demandes->where('statut', 'en_attente')->count(),
            'demandes_validees' => $demandes->where('statut', 'validee')->count(),
            'en_attente_formateur' => $demandes->where('statut', 'en_attente_formateur')->count(),
            'acceptees_formateur' => $demandes->where('statut', 'acceptee_formateur')->count(),
            'refusees_admin' => $demandes->where('statut', 'refuse')->count(),
            'refusees_formateur' => $demandes->where('statut', 'refusee_formateur')->count(),
            'terminees' => $demandes->where('statut', 'terminee')->count(),
            'annulees' => $demandes->where('statut', 'annulee')->count()
        ];

        return response()->json([
            'success' => true,
            'annee' => $annee,
            'statut_filtre' => $statut ?: 'tous',
            'demandes' => $demandes,
            'statistiques' => $statistiques,
            'message' => $statut 
                ? "Liste des demandes de cours à domicile avec statut '$statut' pour l'année $annee"
                : "Liste des demandes de cours à domicile pour l'année $annee"
        ], 200);
    }

    // Affiche toutes les demandes (y compris validées) pour l'admin
    public function adminIndexAll()
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est admin
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Accès réservé aux administrateurs.'], 403);
        }

        // Récupérer toutes les demandes avec les relations
        $demandes = DemandeCoursMaison::with(['user'])
            ->latest()
            ->get();

        // Calculer les statistiques complètes
        $statistiques = [
            'total_demandes' => $demandes->count(),
            'demandes_en_attente' => $demandes->where('statut', 'en_attente')->count(),
            'demandes_validees' => $demandes->where('statut', 'validee')->count(),
            'en_attente_formateur' => $demandes->where('statut', 'en_attente_formateur')->count(),
            'acceptees_formateur' => $demandes->where('statut', 'acceptee_formateur')->count(),
            'refusees_formateur' => $demandes->where('statut', 'refusee_formateur')->count(),
            'terminees' => $demandes->where('statut', 'terminee')->count(),
            'annulees' => $demandes->where('statut', 'annulee')->count(),
            'refusees' => $demandes->where('statut', 'refuse')->count()
        ];

        return response()->json([
            'success' => true,
            'demandes' => $demandes,
            'statistiques' => $statistiques,
            'message' => 'Liste complète des demandes de cours à domicile'
        ], 200);
    }

    // Affiche uniquement les demandes validées pour l'admin
    public function adminIndexValidees()
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est admin
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Accès réservé aux administrateurs.'], 403);
        }

        // Récupérer uniquement les demandes validées avec les relations
        $demandes = DemandeCoursMaison::with(['user'])
            ->whereIn('statut', ['validee', 'terminee'])
            ->latest()
            ->get();

        // Calculer les statistiques pour les demandes validées
        $statistiques = [
            'total_demandes_validees' => $demandes->count(),
            'demandes_validees' => $demandes->where('statut', 'validee')->count(),
            'terminees' => $demandes->where('statut', 'terminee')->count()
        ];

        return response()->json([
            'success' => true,
            'demandes' => $demandes,
            'statistiques' => $statistiques,
            'message' => 'Liste des demandes de cours à domicile validées'
        ], 200);
    }

    // Statistiques des demandes de formation à domicile
    public function statistiques()
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est admin
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Accès réservé aux administrateurs.'], 403);
        }

        // Récupérer toutes les demandes
        $demandes = DemandeCoursMaison::all();

        // Calculer les statistiques détaillées
        $statistiques = [
            'total_demandes' => $demandes->count(),
            'demandes_en_attente' => $demandes->where('statut', 'en_attente')->count(),
            'demandes_validees' => $demandes->where('statut', 'validee')->count(),
            'demandes_refusees' => $demandes->where('statut', 'refuse')->count(),
            'demandes_acceptees_formateur' => $demandes->where('statut', 'acceptee_formateur')->count(),
            'demandes_refusees_formateur' => $demandes->where('statut', 'refusee_formateur')->count(),
            'demandes_terminees' => $demandes->where('statut', 'terminee')->count(),
            'demandes_annulees' => $demandes->where('statut', 'annulee')->count(),
            'pourcentage_validees' => $demandes->count() > 0 ? round(($demandes->where('statut', 'validee')->count() / $demandes->count()) * 100, 2) : 0,
            'pourcentage_refusees' => $demandes->count() > 0 ? round(($demandes->where('statut', 'refuse')->count() / $demandes->count()) * 100, 2) : 0,
            'pourcentage_en_attente' => $demandes->count() > 0 ? round(($demandes->where('statut', 'en_attente')->count() / $demandes->count()) * 100, 2) : 0
        ];

        // Statistiques par niveau
        $statistiquesParNiveau = $demandes->groupBy('niveau_id')->map(function ($demandesNiveau) {
            return [
                'total' => $demandesNiveau->count(),
                'en_attente' => $demandesNiveau->where('statut', 'en_attente')->count(),
                'validees' => $demandesNiveau->where('statut', 'validee')->count(),
                'refusees' => $demandesNiveau->where('statut', 'refuse')->count(),
                'acceptees_formateur' => $demandesNiveau->where('statut', 'acceptee_formateur')->count(),
                'refusees_formateur' => $demandesNiveau->where('statut', 'refusee_formateur')->count(),
                'terminees' => $demandesNiveau->where('statut', 'terminee')->count(),
                'annulees' => $demandesNiveau->where('statut', 'annulee')->count()
            ];
        });

        return response()->json([
            'success' => true,
            'statistiques_generales' => $statistiques,
            'statistiques_par_niveau' => $statistiquesParNiveau,
            'derniere_mise_a_jour' => now()->format('Y-m-d H:i:s')
        ], 200);
    }

    // Enregistre une nouvelle demande
    public function store(Request $request)
    {
        $request->validate([
            'niveau_id' => 'required|integer|exists:niveaux,id',
            'nombre_enfants' => 'required|integer|min:1|max:20',
            'ville' => 'required|string|max:100',
            'commune' => 'required|string|max:100',
            'quartier' => 'required|string|max:100',
            'numero' => 'required|string|max:20',
            'message' => 'required|string|min:10|max:2000',
        ]);
        $user = Auth::user();
        $demande = DemandeCoursMaison::create([
            'user_id' => $user->id,
            'niveau_id' => $request->niveau_id,
            'module' => 'N/A', // Valeur temporaire pour compatibilité
            'nombre_enfants' => $request->nombre_enfants,
            'ville' => $request->ville,
            'commune' => $request->commune,
            'quartier' => $request->quartier,
            'numero' => $request->numero,
            'message' => $request->message,
        ]);
        return response()->json(['demande' => $demande, 'message' => 'Votre demande de cours à domicile a bien été envoyée à l\'administrateur.'], 201);
    }

    // Affiche le formulaire d'édition d'une demande
    public function edit($id)
    {
        $user = Auth::user();
        $demande = DemandeCoursMaison::where('id', $id)->where('user_id', $user->id)->first();
        if (!$demande) {
            return response()->json(['error' => 'Demande non trouvée'], 404);
        }
        return response()->json(['demande' => $demande], 200);
    }

    // Met à jour une demande
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $demande = DemandeCoursMaison::where('id', $id)->where('user_id', $user->id)->first();
        if (!$demande) {
            return response()->json(['error' => 'Demande non trouvée'], 404);
        }
        $request->validate([
            'niveau_id' => 'required|integer|exists:niveaux,id',
            'nombre_enfants' => 'required|integer|max:20',
            'ville' => 'required|string|max:100',
            'commune' => 'required|string|max:100',
            'quartier' => 'required|string|max:100',
            'numero' => 'required|string|max:20',
            'message' => 'required|string|min:10|max:2000',
        ]);
        $demande->update([
            'niveau_id' => $request->niveau_id,
            'module' => 'N/A', // Valeur temporaire pour compatibilité
            'nombre_enfants' => $request->nombre_enfants,
            'ville' => $request->ville,
            'commune' => $request->commune,
            'quartier' => $request->quartier,
            'numero' => $request->numero,
            'message' => $request->message,
        ]);
        return response()->json(['demande' => $demande, 'message' => 'Demande modifiée avec succès.'], 200);
    }

    // Récupère tous les niveaux disponibles
    public function getAllNiveaux()
    {
        try {
            // Récupérer tous les niveaux
            $niveaux = \App\Models\Niveau::where('actif', true)
                ->orderBy('nom')
                ->get();

            // Formater les données pour l'affichage
            $niveauxFormates = $niveaux->map(function ($niveau) {
                return [
                    'id' => $niveau->id,
                    'nom' => $niveau->nom,
                    'description' => $niveau->description ?? '',
                    'actif' => $niveau->actif,
                    'created_at' => $niveau->created_at,
                    'updated_at' => $niveau->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'niveaux' => $niveauxFormates,
                'total' => $niveauxFormates->count(),
                'message' => 'Liste de tous les niveaux disponibles'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des niveaux: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des niveaux',
                'message' => 'Une erreur est survenue lors de la récupération des niveaux.'
            ], 500);
        }
    }

    // ===== MÉTHODES ADMIN =====

    /**
     * Vérifier si l'utilisateur connecté est admin
     */
    private function checkAdmin()
    {
        $user = Auth::user();
        if (!$user || $user->type_compte !== 'admin') {
            return response()->json(['error' => 'Accès réservé aux administrateurs.'], 403);
        }
        return null;
    }

    /**
     * Récupère toutes les demandes de formation à domicile en attente (admin uniquement)
     */
    public function getDemandesEnAttente()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        try {
            $demandes = DemandeCoursMaison::with(['user'])
                ->where('statut', 'en_attente')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($demande) {
                    return [
                        'id' => $demande->id,
                        'niveau_id' => $demande->niveau_id,
                        'formateur_id' => $demande->formateur_id,
                        'nombre_enfants' => $demande->nombre_enfants,
                        'adresse_complete' => [
                            'ville' => $demande->ville,
                            'commune' => $demande->commune,
                            'quartier' => $demande->quartier,
                            'numero' => $demande->numero
                        ],
                        'message' => $demande->message,
                        'statut' => $demande->statut,
                        'date_demande' => $demande->created_at,
                        'derniere_modification' => $demande->updated_at,
                        'utilisateur' => [
                            'id' => $demande->user->id,
                            'nom' => $demande->user->nom,
                            'prenom' => $demande->user->prenom,
                            'email' => $demande->user->email,
                            'telephone' => $demande->user->telephone,
                            'type_compte' => $demande->user->type_compte
                        ]
                    ];
                });

            $statistiques = [
                'total_demandes_en_attente' => $demandes->count(),
                'demandes_aujourd_hui' => $demandes->where('created_at', '>=', now()->startOfDay())->count(),
                'demandes_cette_semaine' => $demandes->where('created_at', '>=', now()->startOfWeek())->count(),
                'demandes_ce_mois' => $demandes->where('created_at', '>=', now()->startOfMonth())->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Demandes en attente récupérées avec succès',
                'demandes' => $demandes,
                'statistiques' => $statistiques,
                'total' => $demandes->count()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des demandes en attente: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des demandes en attente',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère toutes les demandes de formation à domicile validées (admin uniquement)
     */
    public function getDemandesValidees()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        try {
            $demandes = DemandeCoursMaison::with(['user'])
                ->whereIn('statut', ['validee', 'terminee'])
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function($demande) {
                    return [
                        'id' => $demande->id,
                        'niveau_id' => $demande->niveau_id,
                        'formateur_id' => $demande->formateur_id,
                        'nombre_enfants' => $demande->nombre_enfants,
                        'adresse_complete' => [
                            'ville' => $demande->ville,
                            'commune' => $demande->commune,
                            'quartier' => $demande->quartier,
                            'numero' => $demande->numero
                        ],
                        'message' => $demande->message,
                        'statut' => $demande->statut,
                        'date_demande' => $demande->created_at,
                        'date_validation' => $demande->updated_at,
                        'utilisateur' => [
                            'id' => $demande->user->id,
                            'nom' => $demande->user->nom,
                            'prenom' => $demande->user->prenom,
                            'email' => $demande->user->email,
                            'telephone' => $demande->user->telephone,
                            'type_compte' => $demande->user->type_compte
                        ]
                    ];
                });

            $statistiques = [
                'total_demandes_validees' => $demandes->count(),
                'demandes_validees' => $demandes->where('statut', 'validee')->count(),
                'demandes_terminees' => $demandes->where('statut', 'terminee')->count(),
                'validations_ce_mois' => $demandes->where('updated_at', '>=', now()->startOfMonth())->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Demandes validées récupérées avec succès',
                'demandes' => $demandes,
                'statistiques' => $statistiques,
                'total' => $demandes->count()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des demandes validées: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des demandes validées',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère toutes les demandes de formation à domicile refusées (admin uniquement)
     */
    public function getDemandesRefusees()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        try {
            $demandes = DemandeCoursMaison::with(['user'])
                ->whereIn('statut', ['refuse', 'refusee_formateur', 'annulee'])
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function($demande) {
                    return [
                        'id' => $demande->id,
                        'niveau_id' => $demande->niveau_id,
                        'formateur_id' => $demande->formateur_id,
                        'nombre_enfants' => $demande->nombre_enfants,
                        'adresse_complete' => [
                            'ville' => $demande->ville,
                            'commune' => $demande->commune,
                            'quartier' => $demande->quartier,
                            'numero' => $demande->numero
                        ],
                        'message' => $demande->message,
                        'statut' => $demande->statut,
                        'date_demande' => $demande->created_at,
                        'date_refus' => $demande->updated_at,
                        'utilisateur' => [
                            'id' => $demande->user->id,
                            'nom' => $demande->user->nom,
                            'prenom' => $demande->user->prenom,
                            'email' => $demande->user->email,
                            'telephone' => $demande->user->telephone,
                            'type_compte' => $demande->user->type_compte
                        ]
                    ];
                });

            $statistiques = [
                'total_demandes_refusees' => $demandes->count(),
                'refusees_admin' => $demandes->where('statut', 'refuse')->count(),
                'refusees_formateur' => $demandes->where('statut', 'refusee_formateur')->count(),
                'annulees' => $demandes->where('statut', 'annulee')->count(),
                'refus_ce_mois' => $demandes->where('updated_at', '>=', now()->startOfMonth())->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Demandes refusées récupérées avec succès',
                'demandes' => $demandes,
                'statistiques' => $statistiques,
                'total' => $demandes->count()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des demandes refusées: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des demandes refusées',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Trouve les formateurs correspondants à une demande de cours à domicile
     * Logique de priorité géographique et de compétences
     */
    public function trouverFormateursCorrespondants($demandeId)
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        try {
            // Log pour débogage
            Log::info('Recherche de formateurs pour la demande ID: ' . $demandeId);
            
            // Récupérer la demande avec ses détails
            $demande = DemandeCoursMaison::with(['niveau'])->find($demandeId);
            
            // Log pour débogage
            if (!$demande) {
                Log::warning('Demande non trouvée avec ID: ' . $demandeId);
                return response()->json([
                    'success' => false,
                    'error' => 'Demande non trouvée',
                    'demande_id_recherchee' => $demandeId,
                    'message' => 'Aucune demande trouvée avec cet ID dans la table demandes_cours_maison'
                ], 404);
            }
            
            Log::info('Demande trouvée: ' . json_encode($demande->toArray()));

            // Récupérer le niveau demandé
            $niveauDemande = $demande->niveau;

            // Logique de recherche par priorité géographique
            $formateurs = $this->rechercherFormateursParPriorite($demande, $niveauDemande);

            return response()->json([
                'success' => true,
                'demande' => [
                    'id' => $demande->id,
                    'niveau' => $niveauDemande ? $niveauDemande->nom : 'Niveau non défini',
                    'ville' => $demande->ville,
                    'commune' => $demande->commune,
                    'quartier' => $demande->quartier,
                    'nombre_enfants' => $demande->nombre_enfants
                ],
                'formateurs' => $formateurs,
                'total_formateurs' => count($formateurs),
                'message' => 'Formateurs correspondants trouvés avec succès'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche des formateurs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la recherche des formateurs',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recherche les formateurs par priorité géographique et compétences
     */
    private function rechercherFormateursParPriorite($demande, $niveauDemande)
    {
        $formateurs = collect();

        // Priorité 1: Même quartier + même ville + même commune
        $formateursQuartier = $this->rechercherFormateursNiveau(
            $demande->ville, 
            $demande->commune, 
            $demande->quartier, 
            $niveauDemande,
            'Même quartier, commune et ville'
        );
        
        if ($formateursQuartier->isNotEmpty()) {
            return $formateursQuartier;
        }

        // Priorité 2: Même commune + même ville (quartier différent)
        $formateursCommune = $this->rechercherFormateursNiveau(
            $demande->ville, 
            $demande->commune, 
            null, 
            $niveauDemande,
            'Même commune et ville'
        );
        
        if ($formateursCommune->isNotEmpty()) {
            return $formateursCommune;
        }

        // Priorité 3: Même ville (commune/quartier différents)
        $formateursVille = $this->rechercherFormateursNiveau(
            $demande->ville, 
            null, 
            null, 
            $niveauDemande,
            'Même ville'
        );
        
        if ($formateursVille->isNotEmpty()) {
            return $formateursVille;
        }

        // Priorité 4: Même ville (tous confondus)
        $formateursVilleTous = $this->rechercherFormateursNiveau(
            $demande->ville, 
            null, 
            null, 
            null,
            'Même ville (tous niveaux)'
        );

        return $formateursVilleTous;
    }

    /**
     * Recherche les formateurs selon les critères géographiques et de niveau
     */
    private function rechercherFormateursNiveau($ville, $commune = null, $quartier = null, $niveauDemande = null, $priorite = '')
    {
        $query = \App\Models\Formateur::with(['utilisateur'])
            ->where('valide', 1) // Formateurs validés uniquement
            ->where('ville', $ville);

        if ($commune) {
            $query->where('commune', $commune);
        }

        if ($quartier) {
            $query->where('quartier', $quartier);
        }

        $formateurs = $query->get();

        // Filtrer par niveau si spécifié
        if ($niveauDemande) {
            $formateurs = $formateurs->filter(function ($formateur) use ($niveauDemande) {
                return $this->formateurCorrespondNiveau($formateur, $niveauDemande);
            });
        }

        // Formater les résultats
        return $formateurs->map(function ($formateur) use ($priorite) {
            // Vérifier si le formateur a déjà une formation à domicile active
            $formationActive = $this->verifierFormationActive($formateur->id);
            
            return [
                'id' => $formateur->id,
                'utilisateur_id' => $formateur->utilisateur_id,
                'priorite' => $priorite,
                'specialite' => $formateur->specialite,
                'valide' => $formateur->valide,
                'disponibilite' => [
                    'disponible' => !$formationActive['a_formation_active'],
                    'statut' => $formationActive['statut'],
                    'details' => $formationActive['details']
                ],
                'niveaux' => [
                    'arabe' => $formateur->niveau_arabe,
                    'francais' => $formateur->niveau_francais,
                    'coran' => $formateur->niveau_coran
                ],
                'diplomes' => [
                    'religieux' => $formateur->diplome_religieux,
                    'general' => $formateur->diplome_general
                ],
                'formation' => [
                    'adis' => $formateur->formation_adis,
                    'autre' => $formateur->formation_autre
                ],
                'adresse' => [
                    'ville' => $formateur->ville,
                    'commune' => $formateur->commune,
                    'quartier' => $formateur->quartier
                ],
                'utilisateur' => [
                    'nom' => $formateur->utilisateur->nom ?? 'N/A',
                    'prenom' => $formateur->utilisateur->prenom ?? 'N/A',
                    'email' => $formateur->utilisateur->email ?? 'N/A',
                    'telephone' => $formateur->utilisateur->telephone ?? 'N/A'
                ],
                'created_at' => $formateur->created_at,
                'updated_at' => $formateur->updated_at
            ];
        });
    }

    /**
     * Vérifie si un formateur correspond au niveau demandé
     */
    private function formateurCorrespondNiveau($formateur, $niveauDemande)
    {
        // Vérifier les niveaux de compétences du formateur
        $niveauxFormateur = [
            'arabe' => $formateur->niveau_arabe,
            'francais' => $formateur->niveau_francais,
            'coran' => $formateur->niveau_coran
        ];

        // Si le formateur a des niveaux définis, vérifier la correspondance
        foreach ($niveauxFormateur as $niveau => $valeur) {
            if ($valeur && $valeur === $niveauDemande->nom) {
                return true;
            }
        }

        // Si pas de correspondance exacte, vérifier si le formateur a des diplômes
        if ($formateur->diplome_religieux || $formateur->diplome_general) {
            return true;
        }

        // Si le formateur a une formation ADIS
        if ($formateur->formation_adis) {
            return true;
        }

        return false;
    }

    /**
     * Vérifie si un formateur a déjà une formation à domicile active
     */
    private function verifierFormationActive($formateurId)
    {
        // Statuts qui indiquent une formation active
        $statutsActifs = [
            'en_attente_formateur',    // En cours d'évaluation
            'acceptee_formateur',       // Accepté et en cours
            'validee'                  // Validée et en cours
        ];

        // Rechercher les demandes actives pour ce formateur
        $demandeActive = DemandeCoursMaison::where('formateur_id', $formateurId)
            ->whereIn('statut', $statutsActifs)
            ->with(['user', 'niveau'])
            ->first();

        if ($demandeActive) {
            // Le formateur a une formation active
            $details = [
                'demande_id' => $demandeActive->id,
                'ville' => $demandeActive->ville,
                'commune' => $demandeActive->commune,
                'quartier' => $demandeActive->quartier,
                'niveau' => $demandeActive->niveau ? $demandeActive->niveau->nom : 'Niveau non défini',
                'nombre_enfants' => $demandeActive->nombre_enfants,
                'utilisateur' => $demandeActive->user ? [
                    'nom' => $demandeActive->user->nom,
                    'prenom' => $demandeActive->user->prenom,
                    'email' => $demandeActive->user->email
                ] : 'Utilisateur non trouvé',
                'date_debut' => $demandeActive->created_at,
                'statut_demande' => $demandeActive->statut
            ];

            return [
                'a_formation_active' => true,
                'statut' => 'Occupé - Formation en cours',
                'details' => $details
            ];
        } else {
            // Le formateur est disponible
            return [
                'a_formation_active' => false,
                'statut' => 'Disponible',
                'details' => [
                    'message' => 'Aucune formation active en cours'
                ]
            ];
        }
    }

    /**
     * Méthode de test pour vérifier toutes les demandes existantes
     */
    public function testerDemandesExistentes()
    {
        $adminCheck = $this->checkAdmin();
        if ($adminCheck) return $adminCheck;

        try {
            // Récupérer toutes les demandes
            $demandes = DemandeCoursMaison::with(['niveau', 'user'])->get();
            
            $demandesFormatees = $demandes->map(function($demande) {
                return [
                    'id' => $demande->id,
                    'user_id' => $demande->user_id,
                    'niveau_id' => $demande->niveau_id,
                    'niveau_nom' => $demande->niveau ? $demande->niveau->nom : 'Niveau non défini',
                    'ville' => $demande->ville,
                    'commune' => $demande->commune,
                    'quartier' => $demande->quartier,
                    'statut' => $demande->statut,
                    'created_at' => $demande->created_at,
                    'utilisateur' => $demande->user ? [
                        'nom' => $demande->user->nom,
                        'prenom' => $demande->user->prenom,
                        'email' => $demande->user->email
                    ] : 'Utilisateur non trouvé'
                ];
            });

            return response()->json([
                'success' => true,
                'total_demandes' => $demandes->count(),
                'demandes' => $demandesFormatees,
                'message' => 'Liste de toutes les demandes existantes'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des demandes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des demandes',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // ===== NOUVELLES MÉTHODES POUR L'ASSIGNATION DES FORMATEURS =====

    /**
     * Assigner un formateur à une demande de cours à domicile (Admin/Assistant uniquement)
     */
    public function assignerFormateur(Request $request, $demandeId)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est admin ou assistant
        if (!$user || !in_array($user->type_compte, ['admin', 'assistant'])) {
            return response()->json([
                'success' => false,
                'error' => 'Accès réservé aux administrateurs et assistants.'
            ], 403);
        }

        try {
            // Valider la requête
            $request->validate([
                'formateur_id' => 'required|integer|exists:formateurs,id'
            ]);

            // Récupérer la demande
            $demande = DemandeCoursMaison::find($demandeId);
            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'error' => 'Demande non trouvée'
                ], 404);
            }

            // Vérifier que la demande est en attente
            if ($demande->statut !== 'en_attente') {
                return response()->json([
                    'success' => false,
                    'error' => 'Cette demande ne peut plus être assignée. Statut actuel: ' . $demande->statut
                ], 400);
            }

            // Vérifier que le formateur est valide
            $formateur = \App\Models\Formateur::find($request->formateur_id);
            if (!$formateur || !$formateur->valide) {
                return response()->json([
                    'success' => false,
                    'error' => 'Formateur invalide ou non validé'
                ], 400);
            }

            // Vérifier que le formateur n'a pas déjà une formation active
            $formationActive = DemandeCoursMaison::where('formateur_id', $request->formateur_id)
                ->whereIn('statut', ['en_attente_formateur', 'acceptee_formateur', 'validee'])
                ->first();

            if ($formationActive) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce formateur a déjà une formation active en cours'
                ], 400);
            }

            // Assigner le formateur et mettre à jour le statut
            $demande->update([
                'formateur_id' => $request->formateur_id,
                'statut' => 'en_attente_formateur'
            ]);

            // Recharger les relations
            $demande->load(['formateur.utilisateur', 'user', 'niveau']);

            // Log de l'action
            Log::info("Formateur {$formateur->utilisateur->nom} {$formateur->utilisateur->prenom} assigné à la demande {$demandeId} par {$user->type_compte} {$user->nom} {$user->prenom}");

            return response()->json([
                'success' => true,
                'message' => 'Formateur assigné avec succès à la demande',
                'demande' => [
                    'id' => $demande->id,
                    'statut' => $demande->statut,
                    'formateur_assigne' => [
                        'id' => $demande->formateur->id,
                        'nom' => $demande->formateur->utilisateur->nom,
                        'prenom' => $demande->formateur->utilisateur->prenom,
                        'email' => $demande->formateur->utilisateur->email,
                        'telephone' => $demande->formateur->utilisateur->telephone
                    ],
                    'apprenant' => [
                        'nom' => $demande->user->nom,
                        'prenom' => $demande->user->prenom,
                        'email' => $demande->user->email
                    ],
                    'niveau' => $demande->niveau ? $demande->niveau->nom : 'Niveau non défini',
                    'date_assignation' => now()->format('Y-m-d H:i:s')
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'assignation du formateur: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'assignation du formateur',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les demandes assignées au formateur connecté
     */
    public function getDemandesFormateur(Request $request)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un formateur
        if (!$user || $user->type_compte !== 'formateur') {
            return response()->json([
                'success' => false,
                'error' => 'Accès réservé aux formateurs.'
            ], 403);
        }

        try {
            // Récupérer le formateur
            $formateur = $user->formateur;
            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil formateur non trouvé'
                ], 404);
            }

            // Récupérer le statut de filtre depuis la requête
            $statutFiltre = $request->get('statut');
            
            // Construire la requête
            $query = DemandeCoursMaison::with(['user', 'niveau'])
                ->where('formateur_id', $formateur->id);

            // Appliquer le filtre par statut si spécifié
            if ($statutFiltre && in_array($statutFiltre, ['en_attente_formateur', 'acceptee_formateur', 'refusee_formateur'])) {
                $query->where('statut', $statutFiltre);
            }

            $demandes = $query->orderBy('created_at', 'desc')->get();

            // Formater les résultats
            $demandesFormatees = $demandes->map(function($demande) {
                return [
                    'id' => $demande->id,
                    'statut' => $demande->statut,
                    'niveau' => [
                        'id' => $demande->niveau_id,
                        'nom' => $demande->niveau ? $demande->niveau->nom : 'Niveau non défini'
                    ],
                    'nombre_enfants' => $demande->nombre_enfants,
                    'adresse' => [
                        'ville' => $demande->ville,
                        'commune' => $demande->commune,
                        'quartier' => $demande->quartier,
                        'numero' => $demande->numero
                    ],
                    'message' => $demande->message,
                    'date_demande' => $demande->created_at->format('Y-m-d H:i:s'),
                    'date_assignation' => $demande->updated_at->format('Y-m-d H:i:s'),
                    'apprenant' => [
                        'id' => $demande->user->id,
                        'nom' => $demande->user->nom,
                        'prenom' => $demande->user->prenom,
                        'email' => $demande->user->email,
                        'telephone' => $demande->user->telephone
                    ]
                ];
            });

            // Calculer les statistiques
            $statistiques = [
                'total_demandes' => $demandes->count(),
                'en_attente' => $demandes->where('statut', 'en_attente_formateur')->count(),
                'acceptees' => $demandes->where('statut', 'acceptee_formateur')->count(),
                'refusees' => $demandes->where('statut', 'refusee_formateur')->count()
            ];

            return response()->json([
                'success' => true,
                'message' => 'Demandes du formateur récupérées avec succès',
                'demandes' => $demandesFormatees,
                'statistiques' => $statistiques,
                'filtre_applique' => $statutFiltre ?: 'aucun'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des demandes du formateur: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des demandes',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accepter une demande de cours à domicile (Formateur uniquement)
     */
    public function accepterDemande($demandeId)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un formateur
        if (!$user || $user->type_compte !== 'formateur') {
            return response()->json([
                'success' => false,
                'error' => 'Accès réservé aux formateurs.'
            ], 403);
        }

        try {
            // Récupérer le formateur
            $formateur = $user->formateur;
            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil formateur non trouvé'
                ], 404);
            }

            // Récupérer la demande
            $demande = DemandeCoursMaison::where('id', $demandeId)
                ->where('formateur_id', $formateur->id)
                ->with(['user', 'niveau'])
                ->first();

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'error' => 'Demande non trouvée ou non assignée à ce formateur'
                ], 404);
            }

            // Vérifier que la demande est en attente de réponse du formateur
            if ($demande->statut !== 'en_attente_formateur') {
                return response()->json([
                    'success' => false,
                    'error' => 'Cette demande ne peut plus être acceptée. Statut actuel: ' . $demande->statut
                ], 400);
            }

            // Accepter la demande
            $demande->update([
                'statut' => 'acceptee_formateur'
            ]);

            // Log de l'action
            Log::info("Demande {$demandeId} acceptée par le formateur {$formateur->utilisateur->nom} {$formateur->utilisateur->prenom}");

            return response()->json([
                'success' => true,
                'message' => 'Demande acceptée avec succès',
                'demande' => [
                    'id' => $demande->id,
                    'statut' => $demande->statut,
                    'date_acceptation' => now()->format('Y-m-d H:i:s'),
                    'apprenant' => [
                        'nom' => $demande->user->nom,
                        'prenom' => $demande->user->prenom,
                        'email' => $demande->user->email,
                        'telephone' => $demande->user->telephone
                    ],
                    'niveau' => $demande->niveau ? $demande->niveau->nom : 'Niveau non défini',
                    'adresse' => [
                        'ville' => $demande->ville,
                        'commune' => $demande->commune,
                        'quartier' => $demande->quartier,
                        'numero' => $demande->numero
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'acceptation de la demande: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'acceptation de la demande',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refuser une demande de cours à domicile (Formateur uniquement)
     */
    public function refuserDemande(Request $request, $demandeId)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un formateur
        if (!$user || $user->type_compte !== 'formateur') {
            return response()->json([
                'success' => false,
                'error' => 'Accès réservé aux formateurs.'
            ], 403);
        }

        try {
            // Valider la requête
            $request->validate([
                'motif_refus' => 'nullable|string|max:500'
            ]);

            // Récupérer le formateur
            $formateur = $user->formateur;
            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Profil formateur non trouvé'
                ], 404);
            }

            // Récupérer la demande
            $demande = DemandeCoursMaison::where('id', $demandeId)
                ->where('formateur_id', $formateur->id)
                ->with(['user', 'niveau'])
                ->first();

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'error' => 'Demande non trouvée ou non assignée à ce formateur'
                ], 404);
            }

            // Vérifier que la demande est en attente de réponse du formateur
            if ($demande->statut !== 'en_attente_formateur') {
                return response()->json([
                    'success' => false,
                    'error' => 'Cette demande ne peut plus être refusée. Statut actuel: ' . $demande->statut
                ], 400);
            }

            // Refuser la demande
            $demande->update([
                'statut' => 'refusee_formateur'
            ]);

            // Log de l'action
            Log::info("Demande {$demandeId} refusée par le formateur {$formateur->utilisateur->nom} {$formateur->utilisateur->prenom}. Motif: " . ($request->motif_refus ?: 'Aucun motif spécifié'));

            return response()->json([
                'success' => true,
                'message' => 'Demande refusée avec succès',
                'demande' => [
                    'id' => $demande->id,
                    'statut' => $demande->statut,
                    'date_refus' => now()->format('Y-m-d H:i:s'),
                    'motif_refus' => $request->motif_refus,
                    'apprenant' => [
                        'nom' => $demande->user->nom,
                        'prenom' => $demande->user->prenom,
                        'email' => $demande->user->email
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors du refus de la demande: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du refus de la demande',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les informations du formateur assigné pour un apprenant
     */
    public function getFormateurAssignee($demandeId)
    {
        $user = Auth::user();
        
        try {
            // Récupérer la demande
            $demande = DemandeCoursMaison::where('id', $demandeId)
                ->where('user_id', $user->id)
                ->with(['formateur.utilisateur', 'niveau'])
                ->first();

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'error' => 'Demande non trouvée ou non autorisée'
                ], 404);
            }

            // Vérifier que la demande a un formateur assigné
            if (!$demande->formateur) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun formateur n\'est encore assigné à cette demande'
                ], 404);
            }

            // Vérifier que la demande est acceptée par le formateur
            if ($demande->statut !== 'acceptee_formateur') {
                return response()->json([
                    'success' => false,
                    'error' => 'Le formateur n\'a pas encore accepté cette demande',
                    'statut_actuel' => $demande->statut
                ], 400);
            }

            // Formater les informations du formateur
            $formateurInfo = [
                'id' => $demande->formateur->id,
                'nom' => $demande->formateur->utilisateur->nom,
                'prenom' => $demande->formateur->utilisateur->prenom,
                'email' => $demande->formateur->utilisateur->email,
                'telephone' => $demande->formateur->utilisateur->telephone,
                'specialite' => $demande->formateur->specialite,
                'niveaux' => [
                    'arabe' => $demande->formateur->niveau_arabe,
                    'francais' => $demande->formateur->niveau_francais,
                    'coran' => $demande->formateur->niveau_coran
                ],
                'diplomes' => [
                    'religieux' => $demande->formateur->diplome_religieux,
                    'general' => $demande->formateur->diplome_general
                ],
                'formation' => [
                    'adis' => $demande->formateur->formation_adis,
                    'autre' => $demande->formateur->formation_autre
                ],
                'adresse' => [
                    'ville' => $demande->formateur->ville,
                    'commune' => $demande->formateur->commune,
                    'quartier' => $demande->formateur->quartier
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Informations du formateur récupérées avec succès',
                'formateur' => $formateurInfo,
                'demande' => [
                    'id' => $demande->id,
                    'statut' => $demande->statut,
                    'niveau' => $demande->niveau ? $demande->niveau->nom : 'Niveau non défini',
                    'date_acceptation' => $demande->updated_at->format('Y-m-d H:i:s')
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des informations du formateur: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des informations du formateur',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer le statut détaillé d'une demande pour un apprenant
     */
    public function getStatutDemande($demandeId)
    {
        $user = Auth::user();
        
        try {
            // Récupérer la demande
            $demande = DemandeCoursMaison::where('id', $demandeId)
                ->where('user_id', $user->id)
                ->with(['formateur.utilisateur', 'niveau'])
                ->first();

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'error' => 'Demande non trouvée ou non autorisée'
                ], 404);
            }

            // Formater le statut détaillé
            $statutDetaille = [
                'id' => $demande->id,
                'statut' => $demande->statut,
                'niveau' => $demande->niveau ? $demande->niveau->nom : 'Niveau non défini',
                'nombre_enfants' => $demande->nombre_enfants,
                'adresse' => [
                    'ville' => $demande->ville,
                    'commune' => $demande->commune,
                    'quartier' => $demande->quartier,
                    'numero' => $demande->numero
                ],
                'message' => $demande->message,
                'date_demande' => $demande->created_at->format('Y-m-d H:i:s'),
                'derniere_modification' => $demande->updated_at->format('Y-m-d H:i:s'),
                'formateur_assigne' => null,
                'etapes' => []
            ];

            // Ajouter les informations du formateur si assigné
            if ($demande->formateur) {
                $statutDetaille['formateur_assigne'] = [
                    'id' => $demande->formateur->id,
                    'nom' => $demande->formateur->utilisateur->nom,
                    'prenom' => $demande->formateur->utilisateur->prenom,
                    'email' => $demande->formateur->utilisateur->email,
                    'telephone' => $demande->formateur->utilisateur->telephone
                ];
            }

            // Définir les étapes selon le statut
            switch ($demande->statut) {
                case 'en_attente':
                    $statutDetaille['etapes'] = [
                        ['etape' => 1, 'description' => 'Demande soumise', 'statut' => 'terminee', 'date' => $demande->created_at->format('Y-m-d H:i:s')],
                        ['etape' => 2, 'description' => 'En cours d\'évaluation par l\'administrateur', 'statut' => 'en_cours', 'date' => null],
                        ['etape' => 3, 'description' => 'Formateur assigné', 'statut' => 'en_attente', 'date' => null],
                        ['etape' => 4, 'description' => 'Formateur accepte la demande', 'statut' => 'en_attente', 'date' => null],
                        ['etape' => 5, 'description' => 'Formation en cours', 'statut' => 'en_attente', 'date' => null]
                    ];
                    break;

                case 'en_attente_formateur':
                    $statutDetaille['etapes'] = [
                        ['etape' => 1, 'description' => 'Demande soumise', 'statut' => 'terminee', 'date' => $demande->created_at->format('Y-m-d H:i:s')],
                        ['etape' => 2, 'description' => 'En cours d\'évaluation par l\'administrateur', 'statut' => 'terminee', 'date' => $demande->updated_at->format('Y-m-d H:i:s')],
                        ['etape' => 3, 'description' => 'Formateur assigné', 'statut' => 'terminee', 'date' => $demande->updated_at->format('Y-m-d H:i:s')],
                        ['etape' => 4, 'description' => 'Formateur accepte la demande', 'statut' => 'en_cours', 'date' => null],
                        ['etape' => 5, 'description' => 'Formation en cours', 'statut' => 'en_attente', 'date' => null]
                    ];
                    break;

                case 'acceptee_formateur':
                    $statutDetaille['etapes'] = [
                        ['etape' => 1, 'description' => 'Demande soumise', 'statut' => 'terminee', 'date' => $demande->created_at->format('Y-m-d H:i:s')],
                        ['etape' => 2, 'description' => 'En cours d\'évaluation par l\'administrateur', 'statut' => 'terminee', 'date' => $demande->updated_at->format('Y-m-d H:i:s')],
                        ['etape' => 3, 'description' => 'Formateur assigné', 'statut' => 'terminee', 'date' => $demande->updated_at->format('Y-m-d H:i:s')],
                        ['etape' => 4, 'description' => 'Formateur accepte la demande', 'statut' => 'terminee', 'date' => $demande->updated_at->format('Y-m-d H:i:s')],
                        ['etape' => 5, 'description' => 'Formation en cours', 'statut' => 'en_cours', 'date' => $demande->updated_at->format('Y-m-d H:i:s')]
                    ];
                    break;

                case 'refusee_formateur':
                    $statutDetaille['etapes'] = [
                        ['etape' => 1, 'description' => 'Demande soumise', 'statut' => 'terminee', 'date' => $demande->created_at->format('Y-m-d H:i:s')],
                        ['etape' => 2, 'description' => 'En cours d\'évaluation par l\'administrateur', 'statut' => 'terminee', 'date' => $demande->updated_at->format('Y-m-d H:i:s')],
                        ['etape' => 3, 'description' => 'Formateur assigné', 'statut' => 'terminee', 'date' => $demande->updated_at->format('Y-m-d H:i:s')],
                        ['etape' => 4, 'description' => 'Formateur refuse la demande', 'statut' => 'terminee', 'date' => $demande->updated_at->format('Y-m-d H:i:s')],
                        ['etape' => 5, 'description' => 'Demande refusée', 'statut' => 'terminee', 'date' => $demande->updated_at->format('Y-m-d H:i:s')]
                    ];
                    break;

                default:
                    $statutDetaille['etapes'] = [
                        ['etape' => 1, 'description' => 'Statut non reconnu', 'statut' => 'inconnu', 'date' => null]
                    ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Statut de la demande récupéré avec succès',
                'statut' => $statutDetaille
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du statut de la demande: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération du statut de la demande',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
