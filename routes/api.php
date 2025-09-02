<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UtilisateurApiController;
use App\Http\Controllers\Api\ApprenantApiController;
use App\Http\Controllers\Api\FormateurApiController;
use App\Http\Controllers\Api\ModuleApiController;
use App\Http\Controllers\Api\InscriptionController;
use App\Http\Controllers\Api\PaiementApiController;
use App\Http\Controllers\Api\CertificatController;
use App\Http\Controllers\Api\DocumentApiController;
use App\Http\Controllers\Api\QuestionnaireApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\NiveauApiController;
use App\Http\Controllers\Api\SessionFormationApiController;
use App\Http\Controllers\Api\VacanceApiController;
use App\Http\Controllers\Api\ProfilApiController;
use App\Http\Controllers\Api\InscriptionApiController;
use App\Http\Controllers\Api\CertificatApiController;

// Route pour récupérer les apprenants assignés au formateur connecté
Route::middleware('auth:api')->get('/formateur/apprenants-assignes', [FormateurApiController::class, 'mesApprenantsAssignes']);

// Route pour récupérer la progression des apprenants assignés au formateur connecté
Route::middleware('auth:api')->get('/formateur/progression-apprenants-assignes', [FormateurApiController::class, 'progressionApprenantsAssignes']);


Route::middleware('auth:api')->get('/profile', [AuthApiController::class, 'profile']);
Route::middleware('auth:api')->put('/profile', [AuthApiController::class, 'updateProfile']);
Route::get('/apprenants/dashboard', [ApprenantApiController::class, 'dashboard'])->middleware('auth')->name('apprenants.dashboard');
Route::resource('questions', QuestionnaireApiController::class);
// Achat modules niveau apprenant
Route::middleware('auth:api')->group(function () {
    Route::get('/apprenant/achat-modules', [\App\Http\Controllers\Api\AchatApiModuleController::class, 'showAchat']);
    Route::post('/apprenant/payer-niveau', [\App\Http\Controllers\Api\AchatApiModuleController::class, 'payerModule']);
});
Route::middleware('auth:api')->resource('modules', ModuleApiController::class);
Route::middleware('auth:api')->put('/admin/modules/{module}', [ModuleApiController::class, 'update']);
Route::middleware('auth:api')->delete('/admin/modules/{module}', [ModuleApiController::class, 'destroy']);
Route::middleware('auth:api')->get('/modules/niveau/{niveauId}', [ModuleApiController::class, 'getModulesByNiveau']);
Route::resource('utilisateurs', UtilisateurApiController::class);
Route::resource('apprenants', ApprenantApiController::class);

// Route pour que l'admin ajoute un apprenant
Route::middleware('auth:api')->post('/admin/apprenants', [ApprenantApiController::class, 'adminCreateApprenant']);
Route::get('/niveaux', [NiveauApiController::class, 'index']);
Route::get('/app/niveaux', [NiveauApiController::class, 'indexForApp']); // Route spécifique pour l'app mobile
Route::resource('/admin/niveaux', NiveauApiController::class);
Route::middleware('auth:api')->get('/admin/niveaux/{niveau}/check-deletable', [NiveauApiController::class, 'checkDeletable']);
Route::middleware('auth:api')->get('/admin/formateurs', [NiveauApiController::class, 'getFormateurs']);
Route::middleware('auth:api')->get('/admin/formateurs-valides', [NiveauApiController::class, 'getFormateursValides']);
Route::middleware('auth:api')->get('/apprenant/infos', [ApprenantApiController::class, 'infosApprenant']);
Route::middleware('auth:api')->get('/admin/apprenants/infos', [ApprenantApiController::class, 'listeApprenantsInfos']);
Route::middleware('auth:api')->get('/apprenant/modules', [ApprenantApiController::class, 'mesModules']);
Route::middleware('auth:api')->get('/apprenant/niveau', [ApprenantApiController::class, 'getNiveauApprenantConnecte']);
Route::middleware('auth:api')->get('/apprenant/niveau-complet', [ApprenantApiController::class, 'getNiveauApprenantConnecteComplet']);
Route::middleware('auth:api')->get('/apprenant/supports-cours', [ApprenantApiController::class, 'mesSupportsCours']);
Route::middleware('auth:api')->get('/apprenant/supports-cours/{moduleId}', [ApprenantApiController::class, 'supportsModule']);
Route::middleware('auth:api')->get('/apprenant/diagnostic-modules', [ApprenantApiController::class, 'diagnosticModules']);
Route::middleware('auth:api')->get('/apprenant/questions-disponibles', [ApprenantApiController::class, 'mesQuestionsDisponibles']);
Route::middleware('auth:api')->get('/apprenant/questions-module/{moduleId}', [ApprenantApiController::class, 'questionsModule']);
Route::middleware('auth:api')->get('/apprenant/toutes-questions', [ApprenantApiController::class, 'toutesQuestionsDisponibles']);
Route::middleware('auth:api')->get('/apprenant/certificats', [CertificatApiController::class, 'mesCertificats']);
Route::middleware('auth:api')->get('/apprenant/modules-payes', [ApprenantApiController::class, 'mesModulesPayes']);
Route::middleware('auth:api')->get('/apprenant/liens-google-meet', [ApprenantApiController::class, 'mesLiensGoogleMeet']);
Route::middleware('auth:api')->get('/apprenant/modules-disponibles', [ApprenantApiController::class, 'modulesDisponibles']);
Route::middleware('auth:api')->get('/apprenant/resultats-questionnaires', [ApprenantApiController::class, 'resultatsQuestionnaires']);
Route::middleware('auth:api')->get('/apprenant/module/{moduleId}/pourcentage', [ApprenantApiController::class, 'pourcentageModule']);
Route::middleware('auth:api')->get('/apprenant/passage-niveau', [ApprenantApiController::class, 'passageNiveau']);
Route::middleware('auth:api')->get('/apprenant/verifier-modules-valides', [ApprenantApiController::class, 'verifierModulesValides']);

// Nouvelle route pour récupérer les modules d'un niveau (apprenant connecté)
Route::middleware('auth:api')->get('/apprenant/niveaux/{niveauId}/modules', [ApprenantApiController::class, 'getModulesByNiveau']);

// Routes pour les demandes de cours à domicile
Route::middleware('auth:api')->resource('demandes-cours-maison', \App\Http\Controllers\Api\DemandeCoursMaisonApiController::class);
Route::middleware('auth:api')->get('/admin/demandes-cours-domicile/{demandeId}/formateurs', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'trouverFormateursCorrespondants']);
Route::middleware('auth:api')->get('/admin/demandes-cours-domicile', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'adminIndex']);
Route::middleware('auth:api')->get('/admin/demandes-cours-domicile/refusees', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'adminIndexRefusees']);
Route::middleware('auth:api')->get('/admin/demandes-cours-domicile/par-annee', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'adminIndexByYear']);
Route::middleware('auth:api')->get('/admin/demandes-cours-domicile/par-annee-statut', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'adminIndexByYearAndStatus']);
Route::middleware('auth:api')->get('/admin/demandes-cours-domicile/toutes', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'adminIndexAll']);
Route::middleware('auth:api')->get('/admin/demandes-cours-domicile/validees', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'adminIndexValidees']);
Route::middleware('auth:api')->get('/admin/demandes-cours-domicile/statistiques', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'statistiques']);
Route::middleware('auth:api')->get('/admin/modules-tous', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'getAllModules']);
Route::middleware('auth:api')->get('/admin/niveaux-tous', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'getAllNiveaux']);
Route::middleware('auth:api')->get('/admin/demandes-cours-domicile/test/liste', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'testerDemandesExistentes']);

// Nouvelles routes admin pour les demandes de cours à domicile
Route::middleware('auth:api')->prefix('admin')->group(function () {
    Route::get('/demandes-cours-domicile/en-attente', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'getDemandesEnAttente']);
    Route::get('/demandes-cours-domicile/validees', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'getDemandesValidees']);
    Route::get('/demandes-cours-domicile/refusees', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'getDemandesRefusees']);
    
    // Nouvelles routes pour l'assignation des formateurs
    Route::post('/demandes-cours-domicile/{demandeId}/assigner-formateur', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'assignerFormateur']);
});

// Nouvelles routes pour les formateurs
Route::middleware('auth:api')->prefix('formateur')->group(function () {
    // Gestion des demandes de cours à domicile
    Route::get('/demandes-cours-domicile', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'getDemandesFormateur']);
    Route::post('/demandes-cours-domicile/{demandeId}/accepter', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'accepterDemande']);
    Route::post('/demandes-cours-domicile/{demandeId}/refuser', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'refuserDemande']);
});

// Nouvelles routes pour les apprenants
Route::middleware('auth:api')->prefix('apprenant')->group(function () {
    // Récupération des informations du formateur assigné
    Route::get('/demandes-cours-domicile/{demandeId}/formateur', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'getFormateurAssignee']);
    // Récupération du statut détaillé d'une demande
    Route::get('/demandes-cours-domicile/{demandeId}/statut', [\App\Http\Controllers\Api\DemandeCoursMaisonApiController::class, 'getStatutDemande']);
});

// Routes pour les certificats
Route::middleware('auth:api')->resource('certificats', \App\Http\Controllers\Api\CertificatApiController::class);

Route::resource('formateurs', FormateurApiController::class);
Route::post('admin/formateurs/{id}/valider', [AdminApiController::class, 'validerFormateur']);
Route::get('admin/formateurs/en-attente', [AdminApiController::class, 'formateursEnAttente']);
Route::resource('inscriptions', InscriptionApiController::class);
Route::resource('paiements', PaiementApiController::class);
Route::middleware('auth:api')->put('/paiements/{paiement}', [PaiementApiController::class, 'update']);
Route::middleware('auth:api')->delete('/paiements/{paiement}', [PaiementApiController::class, 'destroy']);

// Routes CinetPay
Route::post('/payment-notify', [\App\Http\Controllers\Api\CinetPayNotificationController::class, 'handleNotification']);
Route::middleware('auth:api')->post('/paiements/initialize', [\App\Http\Controllers\Api\CinetPayNotificationController::class, 'initializePayment']);
Route::middleware('auth:api')->post('/paiements/check-status', [\App\Http\Controllers\Api\CinetPayNotificationController::class, 'checkPaymentStatus']);
Route::middleware('auth:api')->put('/paiements/update-status', [\App\Http\Controllers\Api\CinetPayNotificationController::class, 'updatePaymentStatus']);
Route::get('/paiements/success', [\App\Http\Controllers\Api\CinetPayNotificationController::class, 'paymentSuccess']);
Route::get('/paiements/cancel', [\App\Http\Controllers\Api\CinetPayNotificationController::class, 'paymentCancel']);
Route::middleware('auth:api')->get('/me', [UtilisateurApiController::class, 'me']);
Route::middleware('auth:api')->resource('paiements', PaiementApiController::class);
Route::resource('questionnaires', QuestionnaireApiController::class);
Route::middleware('auth:api')->resource('documents', DocumentApiController::class);
Route::middleware('auth:api')->get('/apprenant/mes-documents', [DocumentApiController::class, 'mesDocumentsApprenant']);
Route::middleware('auth:api')->get('/formateur/mes-documents', [DocumentApiController::class, 'mesDocumentsFormateur']);
// Route de test pour vérifier l'authentification
Route::middleware('auth:api')->get('/test-auth', function() {
    $user = auth('api')->user();
    return response()->json([
        'success' => true,
        'message' => 'Authentification réussie',
        'user' => [
            'id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'type_compte' => $user->type_compte
        ]
    ]);
});

Route::middleware('auth:api')->get('/documents/{id}/telecharger', [DocumentApiController::class, 'telechargerDocument']);
Route::middleware('auth:api')->get('/documents/{id}/telecharger-audio', [DocumentApiController::class, 'telechargerAudio']);

// Routes pour le nombre de questions dans les questionnaires
Route::middleware('auth:api')->get('/questionnaires/{questionnaire}/nombre-questions', [QuestionnaireApiController::class, 'nombreQuestions']);
Route::middleware('auth:api')->get('/questionnaires/nombre-questions/tous', [QuestionnaireApiController::class, 'nombreQuestionsTous']);

// Routes pour les questionnaires apprenant
Route::middleware('auth:api')->get('/questionnaires/{questionnaire}/apprenant', [QuestionnaireApiController::class, 'showForApprenant']);
Route::middleware('auth:api')->post('/questionnaires/{questionnaire}/repondre', [QuestionnaireApiController::class, 'repondre']);

Route::get('/register', [AuthApiController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthApiController::class, 'register'])->name('register.post');
Route::post('/login', [AuthApiController::class, 'login'])->name('login.post');
Route::view('/register/confirmation', 'auth.register_confirmation')->name('register.confirmation');
Route::view('/register/formateur/confirmation', 'auth.register_formateur_confirmation')->name('register.formateur_confirmation');

// Route API pour vérification email (pour app mobile)
Route::get('/verify-email/{token}', [AuthApiController::class, 'verifyEmail'])->name('api.verify.email');

Route::post('/logout', [AuthApiController::class, 'logout'])->name('logout');

Route::middleware('auth:api')->get('/apprenant/questionnaires-test', [QuestionnaireApiController::class, 'apprenantTest']);
Route::middleware('auth:api')->get('/apprenant/certificat-test', [ApprenantApiController::class, 'certificatTest']);
Route::middleware('auth:api')->post('/apprenant/generate-certificat-pdf', [ApprenantApiController::class, 'generateCertificatPDF']);

Route::middleware('auth:api')->get('/admin/formateurs/valides', [FormateurApiController::class, 'listeFormateursValides']);
Route::middleware('auth:api')->get('/admin/formateurs/statistiques', [AdminApiController::class, 'statistiquesFormateurs']);

// Routes pour télécharger les diplômes des formateurs
Route::middleware('auth:api')->get('/admin/formateurs/{id}/telecharger-diplome-religieux', [AdminApiController::class, 'telechargerDiplomeReligieux']);
Route::middleware('auth:api')->get('/admin/formateurs/{id}/telecharger-diplome-general', [AdminApiController::class, 'telechargerDiplomeGeneral']);
Route::middleware('auth:api')->post('/admin/formateurs/{id}/corriger-diplome-religieux', [AdminApiController::class, 'corrigerDiplomeReligieux']);
Route::middleware('auth:api')->post('/admin/formateurs/{id}/corriger-diplome-general', [AdminApiController::class, 'corrigerDiplomeGeneral']);
Route::middleware('auth:api')->post('/admin/formateurs/{id}/devenir-apprenant', [AdminApiController::class, 'donnerProfilApprenantAFormateur']);
Route::middleware('auth:api')->post('/admin/formateurs/{id}/devenir-assistant', [AdminApiController::class, 'donnerProfilAssistantAFormateur']);

// Route pour transformer un apprenant en assistant (admin et assistant) - {id} = ID utilisateur
Route::middleware('auth:api')->post('/admin/apprenants/{id}/devenir-assistant', [AdminApiController::class, 'donnerProfilAssistantAApprenant']);
Route::middleware('auth:api')->get('/admin/formateurs/avec-profil-apprenant', [AdminApiController::class, 'formateursAvecProfilApprenant']);
Route::middleware('auth:api')->get('/admin/formateurs/avec-profil-assistant', [AdminApiController::class, 'formateursAvecProfilAssistant']);
Route::middleware('auth:api')->get('/admin/formateurs/{id}/niveaux-superieurs', [AdminApiController::class, 'niveauxSuperieursFormateur']);
Route::middleware('auth:api')->get('/admin/formateurs/niveaux-superieurs', [AdminApiController::class, 'niveauxSuperieursTousFormateurs']);
Route::middleware('auth:api')->get('/formateur/progression-apprenants', [FormateurApiController::class, 'progressionApprenants']);
Route::middleware('auth:api')->get('/admin/admins', [AdminApiController::class, 'listeAdmins']);
Route::middleware('auth:api')->put('/admin/profile', [AdminApiController::class, 'updateProfile']);
Route::middleware('auth:api')->get('/admin/utilisateurs/types', [UtilisateurApiController::class, 'listeUtilisateursParType']);
Route::middleware('auth:api')->post('/admin/utilisateurs/admin', [UtilisateurApiController::class, 'storeAdmin']);
Route::middleware('auth:api')->get('/admin/modules/details', [ModuleApiController::class, 'listeModulesDetailsAdmin']);
Route::middleware('auth:api')->get('/admin/modules/{id}/details', [ModuleApiController::class, 'detailsModuleAdmin']);
Route::middleware('auth:api')->get('/admin/modules/{moduleId}/formateurs', [ModuleApiController::class, 'getFormateursByModule']);

Route::middleware('auth:api')->resource('sessionformations', SessionFormationApiController::class);
Route::middleware('auth:api')->resource('vacances', VacanceApiController::class);
Route::middleware('auth:api')->get('/admin/sessionformations/{id}/diagnostic', [SessionFormationApiController::class, 'diagnosticSession']);
Route::middleware('auth:api')->delete('/admin/sessionformations/{id}/force', [SessionFormationApiController::class, 'destroyAlternative']);
Route::middleware('auth:api')->put('/admin/sessionformations/{id}/force', [SessionFormationApiController::class, 'updateAlternative']);

// Nouvelles routes pour les sessions activées/désactivées
Route::middleware('auth:api')->get('/admin/sessions-actives', [SessionFormationApiController::class, 'getSessionsActives']);
Route::middleware('auth:api')->get('/admin/sessions-desactivees', [SessionFormationApiController::class, 'getSessionsDesactivees']);

// Routes pour la gestion des apprenants par l'admin
Route::middleware('auth:api')->prefix('admin')->group(function () {
    Route::get('/apprenants', [App\Http\Controllers\Api\ApprenantAdminController::class, 'index']);
    Route::get('/apprenants/{id}', [App\Http\Controllers\Api\ApprenantAdminController::class, 'show']);
    Route::put('/apprenants/{id}/changer-niveau', [App\Http\Controllers\Api\ApprenantAdminController::class, 'changerNiveau']);
    Route::get('/niveaux', [App\Http\Controllers\Api\ApprenantAdminController::class, 'getNiveaux']);
    Route::get('/niveaux/{niveauId}/apprenants', [App\Http\Controllers\Api\ApprenantAdminController::class, 'getApprenantsByNiveau']);
    Route::get('/niveaux/{niveauId}/certificats', [App\Http\Controllers\Api\ApprenantAdminController::class, 'getCertificatsByNiveau']);
    Route::get('/niveaux/{niveauId}/apprenants-avec-certificats', [App\Http\Controllers\Api\ApprenantAdminController::class, 'getApprenantsAvecCertificats']);
    Route::get('/utilisateurs/{utilisateurId}/certificats', [App\Http\Controllers\Api\ApprenantAdminController::class, 'getCertificatsParUtilisateur']);
    Route::get('/certificats/{certificatId}/download', [App\Http\Controllers\Api\ApprenantAdminController::class, 'telechargerCertificat']);
    
    // NOUVELLES ROUTES : Gestion des assignations formateur-niveau
    Route::get('/niveaux/{niveauId}/modules', [App\Http\Controllers\Api\AdminApiController::class, 'getModulesByNiveau']);
    Route::get('/formateurs/disponibles', [App\Http\Controllers\Api\AdminApiController::class, 'getFormateursDisponibles']);
    Route::post('/niveaux/{niveauId}/assigner-formateur', [App\Http\Controllers\Api\AdminApiController::class, 'assignerFormateurAuNiveau']);
    Route::delete('/niveaux/{niveauId}/desassigner-formateur', [App\Http\Controllers\Api\AdminApiController::class, 'desassignerFormateurDuNiveau']);
    
    // Routes pour la gestion des liens sociaux
    Route::prefix('liens-sociaux')->group(function () {
        // Route publique pour les apprenants (pas besoin d'authentification)
        Route::get('/actifs', [App\Http\Controllers\Api\LienSocialController::class, 'liensActifs']);
        
        // Route pour les apprenants connectés (récupère tous les liens)
        Route::middleware('auth:api')->get('/tous', [App\Http\Controllers\Api\LienSocialController::class, 'tousLesLiens']);
        
        // Routes protégées pour l'admin
        Route::middleware('auth:api')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\LienSocialController::class, 'index']);
            Route::get('/{id}', [App\Http\Controllers\Api\LienSocialController::class, 'show']);
            Route::post('/', [App\Http\Controllers\Api\LienSocialController::class, 'store']);
            Route::put('/{id}', [App\Http\Controllers\Api\LienSocialController::class, 'update']);
            Route::delete('/{id}', [App\Http\Controllers\Api\LienSocialController::class, 'destroy']);
            Route::patch('/{id}/toggle-actif', [App\Http\Controllers\Api\LienSocialController::class, 'toggleActif']);
            Route::patch('/update-ordre', [App\Http\Controllers\Api\LienSocialController::class, 'updateOrdre']);
        });
    });
    Route::post('/apprenants/rechercher-email', [App\Http\Controllers\Api\ApprenantAdminController::class, 'rechercherParEmail']);
    Route::post('/apprenants/{id}/valider-paiement', [App\Http\Controllers\Api\ApprenantAdminController::class, 'validerPaiement']);
});

// Routes pour la gestion des assistants par l'admin
Route::middleware('auth:api')->prefix('admin')->group(function () {
    Route::get('/assistants', [AdminApiController::class, 'assistants']);
    Route::post('/assistants', [AdminApiController::class, 'storeAssistant']);
    Route::get('/assistants/create', [AdminApiController::class, 'createAssistant']);
});

// Nouvelles routes admin dans AuthApiController
Route::middleware('auth:api')->prefix('admin')->group(function () {
    // Gestion des apprenants
    Route::get('/apprenants', [AuthApiController::class, 'getApprenants']);
    Route::delete('/apprenants/{id}', [AuthApiController::class, 'deleteApprenant']);
    Route::put('/apprenants/{id}/desactiver', [AuthApiController::class, 'desactiverApprenant']);
    
    // Gestion des formateurs
    Route::get('/formateurs', [AuthApiController::class, 'getFormateurs']);
    Route::delete('/formateurs/{id}', [AuthApiController::class, 'deleteFormateur']);
    Route::put('/formateurs/{id}/desactiver', [AuthApiController::class, 'desactiverFormateur']);
    
    // Gestion des assistants
    Route::get('/assistants-list', [AuthApiController::class, 'getAssistants']);
    
    // Gestion générale des utilisateurs
    Route::put('/utilisateurs/{id}/reactiver', [AuthApiController::class, 'reactiverUtilisateur']);
    
    // Nouvelles méthodes génériques pour la gestion des utilisateurs
    Route::put('/utilisateurs/{id}/toggle', [AuthApiController::class, 'toggleUtilisateur']);
    Route::get('/utilisateurs/{id}/statut', [AuthApiController::class, 'getStatutUtilisateur']);
    
    // Gestion des apprenants non payants
    Route::get('/apprenants-non-payants', [App\Http\Controllers\Api\AdminApiController::class, 'getApprenantsNonPayants']);
    
    // Gestion des apprenants payants
    Route::get('/apprenants-payants', [App\Http\Controllers\Api\AdminApiController::class, 'getApprenantsPayants']);
    
    // Recherche universelle d'utilisateurs par email
    Route::post('/utilisateurs/rechercher-email', [App\Http\Controllers\Api\AdminApiController::class, 'rechercherUtilisateurParEmail']);
    
    // Vérification des certificats d'un apprenant par email
    Route::post('/verifier-certificats-apprenant', [App\Http\Controllers\Api\AdminApiController::class, 'verifierCertificatsApprenant']);
    
    // Création de formateurs par l'admin
    Route::post('/creer-formateur', [App\Http\Controllers\Api\AdminApiController::class, 'creerFormateur']);
    
    // Suppression de formateurs par l'admin
    Route::delete('/formateurs/{id}', [App\Http\Controllers\Api\AdminApiController::class, 'supprimerFormateur']);
    
    // Suppression de formateur par ID utilisateur
    Route::delete('/formateurs/utilisateur/{utilisateurId}', [App\Http\Controllers\Api\AdminApiController::class, 'supprimerFormateurParUtilisateur']);
    
    // Création de profil formateur manquant
    Route::post('/formateurs/{id}/creer-profil', [App\Http\Controllers\Api\AdminApiController::class, 'creerProfilFormateurManquant']);
    
    // Suppression d'apprenants
    Route::delete('/apprenants/{id}', [App\Http\Controllers\Api\AdminApiController::class, 'supprimerApprenant']);
    
    // Suppression d'assistants
    Route::delete('/assistants/{id}', [App\Http\Controllers\Api\AdminApiController::class, 'supprimerAssistant']);
});

Route::middleware('auth:api')->get('/formateur/modules', [FormateurApiController::class, 'mesModules']);
Route::middleware('auth:api')->get('/formateur/niveaux', [FormateurApiController::class, 'mesNiveaux']);
Route::middleware('auth:api')->get('/formateur/niveaux/{niveau}/modules', [FormateurApiController::class, 'mesModulesParNiveau']);
Route::middleware('auth:api')->get('/formateur/liens-google-meet', [FormateurApiController::class, 'mesLiensGoogleMeet']);
Route::middleware('auth:api')->get('/formateur/calendrier', [FormateurApiController::class, 'calendrierFormateur']);

// Route pour récupérer tous les documents du formateur connecté
Route::middleware('auth:api')->get('/formateur/mes-documents', [FormateurApiController::class, 'mesDocuments']);
Route::middleware('auth:api')->get('/formateur/statistiques', [FormateurApiController::class, 'statistiques']);
Route::middleware('auth:api')->get('/formateur/profil-apprenant/verifier', [FormateurApiController::class, 'verifierProfilApprenant']);

// Route pour récupérer les formateurs d'un niveau (accessible aux admins)
Route::middleware('auth:api')->get('/niveaux/{niveauId}/formateurs', [AdminApiController::class, 'getFormateursByNiveau']);

// Routes pour vérifier les profils multiples des formateurs
Route::middleware('auth:api')->get('/formateur/profil-assistant/verifier', [FormateurApiController::class, 'verifierProfilAssistant']);

// Routes pour les assistants
Route::middleware('auth:api')->prefix('assistant')->group(function () {
    Route::get('/profil-formateur/verifier', [App\Http\Controllers\Api\AssistantApiController::class, 'verifierProfilFormateur']);
    Route::get('/profile', [App\Http\Controllers\Api\AssistantApiController::class, 'getProfile']);
});

Route::middleware('auth:api')->get('/modules/{module}/apprenants', [InscriptionApiController::class, 'apprenantsParModule']);

// Route pour les statistiques admin
Route::middleware('auth:api')->get('/admin/statistiques', [AdminApiController::class, 'getStatistiques']);

// Route pour les statistiques du formateur
Route::middleware('auth:api')->get('/formateur/statistiques', [FormateurApiController::class, 'getStatistiquesFormateur']);

// Route pour le profil du formateur
Route::middleware('auth:api')->get('/formateur/profile', [FormateurApiController::class, 'getProfile']);

// Route pour mettre à jour le profil du formateur
Route::middleware('auth:api')->put('/formateur/profile', [FormateurApiController::class, 'updateProfile']);

// Route pour modifier le mot de passe du formateur
Route::middleware('auth:api')->put('/formateur/password', [FormateurApiController::class, 'updatePassword']);

// Route générique pour modifier le mot de passe (tous types de comptes)
Route::middleware('auth:api')->put('/change-password', [AuthApiController::class, 'changePassword']);

// Demandes de cours à domicile pour le formateur
Route::middleware('auth:api')->get('/formateur/demandes-cours-domicile', [FormateurApiController::class, 'mesDemandesCoursDomicile']);
Route::middleware('auth:api')->get('/formateur/demandes-en-attente', [FormateurApiController::class, 'mesDemandesEnAttente']);
Route::middleware('auth:api')->get('/formateur/formations-validees-par-annee', [FormateurApiController::class, 'mesFormationsValideesParAnnee']);
Route::middleware('auth:api')->get('/formateur/formations-validees-filtrees', [FormateurApiController::class, 'mesFormationsValideesFiltrees']);

// Apprenants qui ont payé les modules du formateur
Route::middleware('auth:api')->get('/formateur/apprenants-payes', [FormateurApiController::class, 'mesApprenantsPayes']);
Route::middleware('auth:api')->get('/formateur/apprenants-payes/{apprenantId}', [FormateurApiController::class, 'detailsApprenantPaye']);

// Routes pour la validation des cours à domicile
Route::middleware('auth:api')->get('/admin/valider-cours', [\App\Http\Controllers\Api\ValiderCoursApiController::class, 'index']);
Route::middleware('auth:api')->get('/admin/valider-cours/{id}', [\App\Http\Controllers\Api\ValiderCoursApiController::class, 'show']);
Route::middleware('auth:api')->post('/admin/valider-cours/{id}/valider', [\App\Http\Controllers\Api\ValiderCoursApiController::class, 'valider']);
Route::middleware('auth:api')->post('/admin/valider-cours/{id}/refuser', [\App\Http\Controllers\Api\ValiderCoursApiController::class, 'refuser']);

// Routes pour les demandes de paiement
Route::middleware('auth:api')->prefix('demandes-paiement')->group(function () {
    // Routes pour les apprenants
    Route::post('/', [App\Http\Controllers\Api\DemandePaiementApiController::class, 'store']);
    Route::get('/mes-demandes', [App\Http\Controllers\Api\DemandePaiementApiController::class, 'mesDemandes']);
    Route::get('/test-modules', [App\Http\Controllers\Api\DemandePaiementApiController::class, 'testModules']);
    
    // Routes pour les admins
    Route::middleware('auth:api')->prefix('admin')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\DemandePaiementApiController::class, 'index']);
        
        // Routes pour consulter les demandes par statut (PLACÉES EN PREMIER)
        Route::get('/refusees', [App\Http\Controllers\Api\DemandePaiementApiController::class, 'demandesRefusees']);
        Route::get('/acceptees', [App\Http\Controllers\Api\DemandePaiementApiController::class, 'demandesAcceptees']);
        Route::get('/en-attente', [App\Http\Controllers\Api\DemandePaiementApiController::class, 'demandesEnAttente']);
        
        // Tableau de bord
        Route::get('/tableau-bord', [App\Http\Controllers\Api\DemandePaiementApiController::class, 'tableauBord']);
        
        // Route avec paramètre {id} (PLACÉE EN DERNIER)
        Route::get('/{id}', [App\Http\Controllers\Api\DemandePaiementApiController::class, 'show']);
        
        // Routes pour traiter les demandes
        Route::post('/{id}/traiter', [App\Http\Controllers\Api\DemandePaiementApiController::class, 'traiterDemande']);
        Route::post('/{id}/valider', [App\Http\Controllers\Api\DemandePaiementApiController::class, 'validerDemande']);
        Route::post('/{id}/refuser', [App\Http\Controllers\Api\DemandePaiementApiController::class, 'refuserDemande']);
        Route::post('/{id}/annuler', [App\Http\Controllers\Api\DemandePaiementApiController::class, 'annulerDemande']);
        
        Route::get('/par-statut/{statut}', [App\Http\Controllers\Api\DemandePaiementApiController::class, 'demandesParStatut']);
    });
});

// Routes pour l'activation/désactivation des sessions de formation (admin seulement)
Route::middleware('auth:api')->post('/admin/sessions-formation/{id}/activer', [SessionFormationApiController::class, 'activer']);
Route::middleware('auth:api')->post('/admin/sessions-formation/{id}/desactiver', [SessionFormationApiController::class, 'desactiver']);
Route::middleware('auth:api')->get('/admin/sessions-formation/{id}/statut', [SessionFormationApiController::class, 'statut']);
