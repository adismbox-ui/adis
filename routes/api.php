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
    function https_url($path = null, $parameters = [], $secure = true) {
        $url = url($path, $parameters, $secure);
        // Force HTTPS by replacing http:// with https://
        return str_replace('http://', 'https://', $url);
    }
}

// Route racine de l'API - Liste des endpoints disponibles
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'API ADIS - Bienvenue',
        'version' => '1.0',
        'base_url' => https_url('/api'),
        'endpoints' => [
            'test' => 'GET ' . https_url('/api/test'),
            'login' => 'POST ' . https_url('/api/login'),
            'register' => 'POST ' . https_url('/api/register'),
            'supports' => 'GET ' . https_url('/api/supports'),
        ],
        'documentation' => 'Consultez ' . https_url('/api/test') . ' pour plus d\'informations',
    ]);
});

// Routes publiques (sans authentification)
Route::post('/login', [ApiAuthController::class, 'login']);
// Route GET pour /login - Message informatif
Route::get('/login', function () {
    return response()->json([
        'success' => false,
        'error' => 'Méthode non autorisée',
        'message' => 'Cette route nécessite une requête POST, pas GET',
        'method' => 'POST',
        'url' => https_url('/api/login'), // Force HTTPS
        'example' => [
            'method' => 'POST',
            'url' => https_url('/api/login'),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'body' => [
                'email' => 'votre@email.com',
                'password' => 'votre_mot_de_passe'
            ]
        ],
        'curl_example' => "curl -X POST " . https_url('/api/login') . " -H \"Content-Type: application/json\" -H \"Accept: application/json\" -d '{\"email\":\"votre@email.com\",\"password\":\"votre_mot_de_passe\"}'",
        'test_url' => https_url('/api/test'), // Force HTTPS
        'documentation' => 'Utilisez Postman, curl ou l\'application mobile pour faire une requête POST',
        'note' => 'L\'application mobile utilise automatiquement POST, donc elle fonctionnera correctement'
    ], 405);
});

Route::post('/register', [ApiAuthController::class, 'register']);
// Route GET pour /register - Message informatif
Route::get('/register', function () {
    return response()->json([
        'success' => false,
        'error' => 'Méthode non autorisée',
        'message' => 'Cette route nécessite une requête POST, pas GET',
        'method' => 'POST',
        'url' => https_url('/api/register'), // Force HTTPS
        'example' => [
            'method' => 'POST',
            'url' => https_url('/api/register'),
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
        'curl_example' => "curl -X POST " . https_url('/api/register') . " -H \"Content-Type: application/json\" -H \"Accept: application/json\" -d '{\"prenom\":\"John\",\"nom\":\"Doe\",\"email\":\"john@example.com\",\"password\":\"password123\",\"password_confirmation\":\"password123\",\"sexe\":\"Homme\",\"type_compte\":\"apprenant\",\"categorie\":\"Etudiant\"}'",
        'test_url' => https_url('/api/test'), // Force HTTPS
        'documentation' => 'Utilisez Postman, curl ou l\'application mobile pour faire une requête POST',
        'note' => 'L\'application mobile utilise automatiquement POST, donc elle fonctionnera correctement'
    ], 405);
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
    
    // Webhook CinetPay
    Route::post('/payment-notify', [ApiPaiementController::class, 'handleNotification']);
});

