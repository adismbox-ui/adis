<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiAdminController;
use App\Http\Controllers\Api\ApiApprenantController;
use App\Http\Controllers\Api\ApiFormateurController;
use App\Http\Controllers\Api\ApiAssistantController;
use App\Http\Controllers\Api\ApiModuleController;
use App\Http\Controllers\Api\ApiQuestionnaireController;
use App\Http\Controllers\Api\ApiPaiementController;
use App\Http\Controllers\Api\ApiDocumentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route pour lister les endpoints disponibles
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'API ADIS - Endpoints disponibles',
        'endpoints' => [
            'auth' => [
                'POST /api/login' => 'Connexion',
                'POST /api/register' => 'Inscription',
                'POST /api/logout' => 'Déconnexion (protégé)',
                'GET /api/user' => 'Utilisateur connecté (protégé)',
            ],
            'admin' => [
                'GET /api/admin/statistiques' => 'Statistiques',
                'GET /api/admin/utilisateurs' => 'Liste des utilisateurs',
                'GET /api/admin/apprenants' => 'Liste des apprenants',
                'GET /api/admin/formateurs' => 'Liste des formateurs',
            ],
        ],
        'base_url' => url('/api'),
    ], 200);
});

// Routes publiques (sans authentification)
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/register', [ApiAuthController::class, 'register']);

// Route GET pour /api/login (message informatif)
Route::get('/login', function () {
    try {
        $loginUrl = url('/api/login');
        $testUrl = url('/api/test');
        
        return response()->json([
            'success' => false,
            'error' => 'Méthode non autorisée',
            'message' => 'Cette route nécessite une requête POST, pas GET',
            'method' => 'POST',
            'url' => $loginUrl,
            'example' => [
                'method' => 'POST',
                'url' => $loginUrl,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'body' => [
                    'email' => 'votre@email.com',
                    'password' => 'votre_mot_de_passe'
                ]
            ],
            'curl_example' => "curl -X POST {$loginUrl} -H \"Content-Type: application/json\" -H \"Accept: application/json\" -d '{\"email\":\"votre@email.com\",\"password\":\"votre_mot_de_passe\"}'",
            'test_url' => $testUrl,
            'documentation' => 'Utilisez Postman, curl ou l\'application mobile pour faire une requête POST',
            'note' => 'L\'application mobile utilise automatiquement POST, donc elle fonctionnera correctement'
        ], 405);
    } catch (\Exception $e) {
        \Log::error('Error in GET /api/login route', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'error' => 'Erreur serveur',
            'message' => 'Une erreur est survenue lors de la génération de la réponse'
        ], 500);
    }
});

// Route GET pour /api/register (message informatif)
Route::get('/register', function () {
    try {
        $registerUrl = url('/api/register');
        
        return response()->json([
            'success' => false,
            'error' => 'Méthode non autorisée',
            'message' => 'Cette route nécessite une requête POST, pas GET',
            'method' => 'POST',
            'url' => $registerUrl,
            'example' => [
                'method' => 'POST',
                'url' => $registerUrl,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'body' => [
                    'prenom' => 'John',
                    'nom' => 'Doe',
                    'email' => 'john@example.com',
                    'password' => 'password123',
                    'password_confirmation' => 'password123',
                    'sexe' => 'Homme',
                    'type_compte' => 'apprenant',
                    'categorie' => 'Etudiant'
                ]
            ],
            'curl_example' => "curl -X POST {$registerUrl} -H \"Content-Type: application/json\" -H \"Accept: application/json\" -d '{\"prenom\":\"John\",\"nom\":\"Doe\",\"email\":\"john@example.com\",\"password\":\"password123\",\"password_confirmation\":\"password123\",\"sexe\":\"Homme\",\"type_compte\":\"apprenant\",\"categorie\":\"Etudiant\"}'",
            'documentation' => 'Utilisez Postman, curl ou l\'application mobile pour faire une requête POST',
            'note' => 'L\'application mobile utilise automatiquement POST, donc elle fonctionnera correctement'
        ], 405);
    } catch (\Exception $e) {
        \Log::error('Error in GET /api/register route', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'error' => 'Erreur serveur',
            'message' => 'Une erreur est survenue lors de la génération de la réponse'
        ], 500);
    }
});

// Route générique /api/profile qui redirige selon le type de compte
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', function (Request $request) {
        $user = $request->user();
        $typeCompte = $user->type_compte;
        
        switch ($typeCompte) {
            case 'admin':
                return app(ApiAdminController::class)->getProfile($request);
            case 'apprenant':
                return app(ApiApprenantController::class)->getProfile($request);
            case 'formateur':
                return app(ApiFormateurController::class)->getProfile($request);
            case 'assistant':
                return app(ApiAssistantController::class)->getProfile($request);
            default:
                return response()->json([
                    'success' => false,
                    'error' => 'Type de compte non reconnu'
                ], 400);
        }
    });
    
    Route::put('/profile', function (Request $request) {
        $user = $request->user();
        $typeCompte = $user->type_compte;
        
        switch ($typeCompte) {
            case 'admin':
                return app(ApiAdminController::class)->updateProfile($request);
            case 'apprenant':
                return app(ApiApprenantController::class)->updateProfile($request);
            case 'formateur':
                return app(ApiFormateurController::class)->updateProfile($request);
            case 'assistant':
                return app(ApiAssistantController::class)->updateProfile($request);
            default:
                return response()->json([
                    'success' => false,
                    'error' => 'Type de compte non reconnu'
                ], 400);
        }
    });
});

// Routes protégées (nécessitent authentification)
Route::middleware('auth:sanctum')->group(function () {
    
    // Routes d'authentification
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', [ApiAuthController::class, 'user']);
    
    // Routes Admin
    Route::prefix('admin')->group(function () {
        Route::get('/statistiques', [ApiAdminController::class, 'getStatistiques']);
        Route::get('/utilisateurs', [ApiAdminController::class, 'getUtilisateurs']);
        Route::get('/utilisateurs/types', [ApiAdminController::class, 'getUtilisateursTypes']);
        Route::post('/utilisateurs', [ApiAdminController::class, 'addUser']);
        Route::put('/utilisateurs/{utilisateurId}/toggle', [ApiAdminController::class, 'toggleUtilisateur']);
        Route::get('/apprenants', [ApiAdminController::class, 'getApprenants']);
        Route::post('/apprenants', [ApiAdminController::class, 'createApprenant']);
        Route::get('/apprenants-payants', [ApiAdminController::class, 'getApprenantsPayants']);
        Route::get('/apprenants-non-payants', [ApiAdminController::class, 'getApprenantsNonPayants']);
        Route::get('/formateurs', [ApiAdminController::class, 'getFormateurs']);
        Route::post('/creer-formateur', [ApiAdminController::class, 'createFormateur']);
        Route::delete('/formateurs/utilisateur/{utilisateurId}', [ApiAdminController::class, 'deleteFormateur']);
        Route::get('/assistants', [ApiAdminController::class, 'getAssistants']);
        Route::get('/niveaux', [ApiAdminController::class, 'getNiveaux']);
        Route::get('/niveaux/{niveauId}/apprenants', [ApiAdminController::class, 'getApprenantsParNiveau']);
        Route::get('/niveaux/{niveauId}/apprenants-avec-certificats', [ApiAdminController::class, 'getApprenantsAvecCertificats']);
        Route::put('/apprenants/{apprenantId}/changer-niveau', [ApiAdminController::class, 'changerNiveauApprenant']);
        Route::get('/demandes-cours-domicile/par-annee', [ApiAdminController::class, 'getDemandesCoursDomicileParAnnee']);
        Route::get('/liens-sociaux', [ApiAdminController::class, 'getLiensSociaux']);
        Route::post('/liens-sociaux', [ApiAdminController::class, 'createLienSocial']);
        Route::put('/liens-sociaux/{lienId}', [ApiAdminController::class, 'updateLienSocial']);
        Route::delete('/liens-sociaux/{lienId}', [ApiAdminController::class, 'deleteLienSocial']);
        Route::get('/liens-sociaux/tous', [ApiAdminController::class, 'getAllLiensSociaux']);
        Route::get('/profile', [ApiAdminController::class, 'getProfile']);
        Route::put('/profile', [ApiAdminController::class, 'updateProfile']);
    });
    
    // Routes Demandes de Paiement
    Route::prefix('demandes-paiement')->group(function () {
        Route::prefix('admin')->group(function () {
            Route::get('/par-statut/{statut}', [ApiAdminController::class, 'getDemandesPaiementParStatut']);
            Route::get('/en_attente', [ApiAdminController::class, 'getDemandesPaiementParStatut']);
            Route::get('/validees', [ApiAdminController::class, 'getDemandesPaiementParStatut']);
            Route::get('/refusees', [ApiAdminController::class, 'getDemandesPaiementParStatut']);
            Route::get('/acceptees', [ApiAdminController::class, 'getDemandesPaiementParStatut']);
        });
    });
    
    // Routes Apprenant
    Route::prefix('apprenant')->group(function () {
        Route::get('/mes-formations', [ApiApprenantController::class, 'getMesFormations']);
        Route::get('/modules', [ApiApprenantController::class, 'getModules']);
        Route::get('/mes-documents', [ApiApprenantController::class, 'getMesDocuments']);
        Route::get('/questionnaires', [ApiApprenantController::class, 'getQuestionnaires']);
        Route::get('/questionnaires/{id}', [ApiApprenantController::class, 'getQuestionnaire']);
        Route::post('/questionnaires/{id}/repondre', [ApiApprenantController::class, 'repondreQuestionnaire']);
        Route::get('/resultats-questionnaires', [ApiApprenantController::class, 'getResultatsQuestionnaires']);
        Route::get('/profile', [ApiApprenantController::class, 'getProfile']);
        Route::put('/profile', [ApiApprenantController::class, 'updateProfile']);
        Route::get('/progression', [ApiApprenantController::class, 'getProgression']);
        Route::get('/paiements', [ApiApprenantController::class, 'getPaiements']);
    });
    
    // Routes Formateur
    Route::prefix('formateur')->group(function () {
        Route::get('/calendrier', [ApiFormateurController::class, 'getCalendrier']);
        Route::get('/modules', [ApiFormateurController::class, 'getModules']);
        Route::get('/niveaux', [ApiFormateurController::class, 'getNiveaux']);
        Route::get('/profile', [ApiFormateurController::class, 'getProfile']);
        Route::put('/profile', [ApiFormateurController::class, 'updateProfile']);
    });
    
    // Routes Assistant
    Route::prefix('assistant')->group(function () {
        Route::get('/profile', [ApiAssistantController::class, 'getProfile']);
        Route::put('/profile', [ApiAssistantController::class, 'updateProfile']);
        Route::get('/apprenants', [ApiAssistantController::class, 'getApprenants']);
        Route::get('/formateurs', [ApiAssistantController::class, 'getFormateurs']);
    });
    
    // Routes Documents
    Route::post('/documents', [ApiDocumentController::class, 'store']);
    Route::get('/documents', [ApiDocumentController::class, 'index']);
    
    // Routes Modules
    Route::get('/modules', [ApiModuleController::class, 'index']);
    Route::get('/modules/{id}', [ApiModuleController::class, 'show']);
    
    // Routes Questionnaires
    Route::get('/questionnaires', [ApiQuestionnaireController::class, 'index']);
    Route::get('/questionnaires/{id}', [ApiQuestionnaireController::class, 'show']);
    
    // Routes Paiements
    Route::prefix('paiements')->group(function () {
        Route::post('/initier', [ApiPaiementController::class, 'initierPaiement']);
        Route::post('/verifier', [ApiPaiementController::class, 'verifierPaiement']);
    });
    
    // Routes Utilisateurs (génériques)
    Route::get('/utilisateurs', [ApiAdminController::class, 'getUtilisateurs']);
    Route::post('/utilisateurs', [ApiAdminController::class, 'addUser']);
    Route::get('/utilisateurs/{id}', function (Request $request, $id) {
        // Route pour récupérer un utilisateur spécifique
        $user = $request->user();
        if ($user->type_compte !== 'admin') {
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }
        $utilisateur = \App\Models\Utilisateur::find($id);
        if (!$utilisateur) {
            return response()->json(['success' => false, 'error' => 'Utilisateur non trouvé'], 404);
        }
        return response()->json(['success' => true, 'data' => $utilisateur], 200);
    });
    Route::put('/utilisateurs/{id}/desactiver', function (Request $request, $id) {
        // Alias pour toggle
        return app(ApiAdminController::class)->toggleUtilisateur($request, $id);
    });
    Route::put('/utilisateurs/{id}/activer', function (Request $request, $id) {
        // Alias pour toggle
        return app(ApiAdminController::class)->toggleUtilisateur($request, $id);
    });
});
