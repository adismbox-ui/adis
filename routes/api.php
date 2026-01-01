<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiApprenantController;
use App\Http\Controllers\Api\ApiAdminController;
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

/**
 * Helper function to generate HTTPS URLs
 * Force HTTPS even if APP_URL is HTTP
 */
if (!function_exists('https_url')) {
    function https_url($path = null) {
        try {
            // Get the current request
            $request = request();
            $host = $request->getHost();
            $path = $path ? ltrim($path, '/') : '';
            // Always use HTTPS
            return 'https://' . $host . ($path ? '/' . $path : '');
        } catch (\Exception $e) {
            // Fallback: use config
            $appUrl = config('app.url', 'https://www.adis-ci.net');
            $appUrl = str_replace('http://', 'https://', $appUrl);
            $path = $path ? ltrim($path, '/') : '';
            return rtrim($appUrl, '/') . ($path ? '/' . $path : '');
        }
    }
}

// Route racine de l'API - Liste des endpoints disponibles
Route::get('/', function () {
    try {
        $baseUrl = 'https://www.adis-ci.net';
        return response()->json([
            'success' => true,
            'message' => 'API ADIS - Bienvenue',
            'version' => '1.0',
            'base_url' => $baseUrl . '/api',
            'endpoints' => [
                'test' => 'GET ' . $baseUrl . '/api/test',
                'login' => 'POST ' . $baseUrl . '/api/login',
                'register' => 'POST ' . $baseUrl . '/api/register',
                'supports' => 'GET ' . $baseUrl . '/api/supports',
            ],
            'documentation' => 'Consultez ' . $baseUrl . '/api/test pour plus d\'informations',
        ]);
    } catch (\Exception $e) {
        \Log::error('Error in GET /api/ route', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'error' => 'Erreur serveur',
            'message' => 'Une erreur est survenue'
        ], 500);
    }
});

// Routes publiques (sans authentification)
Route::post('/login', [ApiAuthController::class, 'login']);
// Route GET pour /login - Message informatif
Route::get('/login', function () {
    try {
        $baseUrl = 'https://www.adis-ci.net';
        $loginUrl = $baseUrl . '/api/login';
        $testUrl = $baseUrl . '/api/test';
        
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

Route::post('/register', [ApiAuthController::class, 'register']);
// Route GET pour /register - Message informatif
Route::get('/register', function () {
    try {
        $baseUrl = 'https://www.adis-ci.net';
        $registerUrl = $baseUrl . '/api/register';
        $testUrl = $baseUrl . '/api/test';
        
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
            'test_url' => $testUrl,
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
Route::get('/supports', [ApiModuleController::class, 'getSupports']);

// Route de test pour vérifier que l'API fonctionne
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API ADIS fonctionne correctement',
        'version' => '1.0',
        'endpoints' => [
            'login' => 'POST /api/login',
            'register' => 'POST /api/register',
            'supports' => 'GET /api/supports',
            'test' => 'GET /api/test',
        ],
    ]);
});

// Routes protégées (nécessitent authentification)
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Routes d'authentification
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', [ApiAuthController::class, 'user']);
    
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
        Route::post('/paiements/initialize', [ApiPaiementController::class, 'initialize']);
    });
    
    // Routes Admin
    Route::prefix('admin')->group(function () {
        Route::get('/statistiques', [ApiAdminController::class, 'getStatistiques']);
        Route::get('/utilisateurs', [ApiAdminController::class, 'getUtilisateurs']);
        Route::get('/utilisateurs/types', [ApiAdminController::class, 'getUtilisateursTypes']);
        Route::post('/utilisateurs', [ApiAdminController::class, 'addUser']);
        Route::get('/apprenants', [ApiAdminController::class, 'getApprenants']);
        Route::get('/formateurs', [ApiAdminController::class, 'getFormateurs']);
        Route::get('/niveaux', [ApiAdminController::class, 'getNiveaux']);
        Route::get('/niveaux/{niveauId}/apprenants', [ApiAdminController::class, 'getApprenantsByNiveau']);
        Route::put('/apprenants/{apprenantId}/changer-niveau', [ApiAdminController::class, 'changerNiveauApprenant']);
        Route::get('/formateurs-avec-profil-assistant', [ApiAdminController::class, 'formateursAvecProfilAssistant']);
        Route::post('/formateurs/{id}/devenir-assistant', [ApiAdminController::class, 'devenirAssistant']);
        Route::get('/modules', [ApiAdminController::class, 'getModules']);
        Route::post('/modules', [ApiAdminController::class, 'createModule']);
        Route::put('/modules/{id}', [ApiAdminController::class, 'updateModule']);
        Route::delete('/modules/{id}', [ApiAdminController::class, 'deleteModule']);
        // Routes pour les demandes de cours à domicile
        Route::get('/demandes-cours-domicile/par-annee/{annee?}', [ApiAdminController::class, 'getDemandesCoursDomicileParAnnee']);
    });
    
    // Routes Formateur
    Route::prefix('formateur')->group(function () {
        Route::get('/calendrier', [ApiFormateurController::class, 'getCalendrier']);
        Route::get('/modules', [ApiFormateurController::class, 'getModules']);
        Route::get('/niveaux', [ApiFormateurController::class, 'getNiveaux']);
        Route::get('/niveaux/{niveauId}/modules', [ApiFormateurController::class, 'getModulesParNiveau']);
        Route::get('/profile', [ApiFormateurController::class, 'getProfile']);
        Route::put('/profile', [ApiFormateurController::class, 'updateProfile']);
        Route::get('/apprenants', [ApiFormateurController::class, 'getApprenants']);
        Route::get('/documents', [ApiFormateurController::class, 'getDocuments']);
        Route::post('/documents', [ApiFormateurController::class, 'uploadDocument']);
        Route::get('/questionnaires', [ApiFormateurController::class, 'getQuestionnaires']);
        Route::post('/questionnaires', [ApiFormateurController::class, 'createQuestionnaire']);
    });
    
    // Routes Assistant
    Route::prefix('assistant')->group(function () {
        Route::get('/profile', [ApiAssistantController::class, 'getProfile']);
        Route::put('/profile', [ApiAssistantController::class, 'updateProfile']);
        Route::get('/apprenants', [ApiAssistantController::class, 'getApprenants']);
        Route::get('/formateurs', [ApiAssistantController::class, 'getFormateurs']);
    });
    
    // Routes communes
    Route::prefix('modules')->group(function () {
        Route::get('/', [ApiModuleController::class, 'index']);
        Route::get('/mes-modules', [ApiModuleController::class, 'getMesModules']);
        Route::get('/{id}', [ApiModuleController::class, 'show']);
    });
    
    Route::prefix('questionnaires')->group(function () {
        Route::get('/', [ApiQuestionnaireController::class, 'index']);
        Route::get('/{id}', [ApiQuestionnaireController::class, 'show']);
    });
    
    Route::prefix('documents')->group(function () {
        Route::get('/{id}/download', [ApiDocumentController::class, 'download']);
    });
    
    // Routes paiement
    Route::prefix('paiements')->group(function () {
        Route::post('/initialize', [ApiPaiementController::class, 'initialize']);
        Route::get('/status/{id}', [ApiPaiementController::class, 'getStatus']);
    });
    
    // Routes demandes de paiement (admin)
    Route::prefix('demandes-paiement')->group(function () {
        Route::get('/admin/par-statut/{statut}', [ApiAdminController::class, 'getDemandesPaiementParStatut']);
    });
    
    // Webhook CinetPay
    Route::post('/payment-notify', [ApiPaiementController::class, 'handleNotification']);
});

