<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\ApprenantController;
use App\Http\Controllers\FormateurController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\CertificatController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\SessionFormationController;
use App\Http\Controllers\VacanceController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\AdminProjetController;
use App\Http\Controllers\PresenceController;

Route::pattern('formateur', '[0-9]+');
Route::pattern('apprenant', '[0-9]+');

Route::get('/', function () {
    return view('welcome');
});

// Routes pour le téléchargement de l'application mobile
Route::get('/download-app', [App\Http\Controllers\MobileAppController::class, 'downloadPage'])->name('download-app.page');
Route::get('/download-app/apk', [App\Http\Controllers\MobileAppController::class, 'download'])->name('mobile-app.download');
Route::get('/download-app/store', [App\Http\Controllers\MobileAppController::class, 'redirectToStore'])->name('mobile-app.store');

Route::get('/apprenants/dashboard', [ApprenantController::class, 'dashboard'])->middleware('auth')->name('apprenants.dashboard');

// Route admin : voir un formateur
Route::get('/admin/formateur/{id}', [AdminController::class, 'showFormateur'])->middleware('auth')->name('admin.formateur.show');
// Route admin : refuser un formateur
Route::post('/admin/formateur/{id}/refuser', [AdminController::class, 'refuserFormateur'])->middleware('auth')->name('admin.formateur.refuser');

// Admin: inscrire un apprenant aux modules de son niveau
Route::post('/admin/apprenants/{apprenant}/inscrire-niveau', [ApprenantController::class, 'adminInscrireNiveau'])
    ->middleware('auth')
    ->name('admin.apprenants.inscrire-niveau');

// Admin: faire passer un apprenant au niveau supérieur
Route::post('/admin/apprenants/{apprenant}/passer-niveau', [ApprenantController::class, 'adminPasserNiveau'])
    ->middleware('auth')
    ->name('admin.apprenants.passer-niveau');

// Achat modules niveau apprenant
Route::middleware(['auth'])->group(function () {
    Route::get('/apprenant/achat-modules', [\App\Http\Controllers\AchatModuleController::class, 'showAchat'])->name('apprenants.achat-modules');
    Route::post('/apprenant/payer-module/{module}', [\App\Http\Controllers\AchatModuleController::class, 'payerModule'])->name('apprenants.payer-module');
});
Route::resource('utilisateurs', UtilisateurController::class);
Route::resource('apprenants', ApprenantController::class);
// Générer un certificat de niveau pour un apprenant
Route::post('/apprenants/{apprenant}/generer-certificat', [ApprenantController::class, 'genererCertificatNiveau'])->name('apprenants.generer-certificat');
// Route pour activer/désactiver un apprenant
Route::post('/apprenants/{apprenant}/toggle-activation', [ApprenantController::class, 'toggleActivation'])->name('apprenants.toggle-activation');
Route::get('/formateurs/dashboard', [FormateurController::class, 'dashboard'])->middleware('auth')->name('formateurs.dashboard');
Route::get('/formateurs/documents', [FormateurController::class, 'documentFormateur'])->middleware('auth')->name('formateurs.documents');
Route::get('/formateurs/questionnaires', [FormateurController::class, 'questionnairesFormateur'])->middleware('auth')->name('formateurs.questionnaires');

// Routes dashboard assistant
// Correction: route correcte pour la création de sessions côté assistant
Route::get('/assistant/sessions/create', [App\Http\Controllers\AssistantSessionsController::class, 'create'])->middleware('auth')->name('assistant.sessions.create');
Route::get('/assistant/dashboard', [AssistantController::class, 'dashboard'])->middleware('auth')->name('assistant.dashboard');
Route::get('/assistant/apprenants', [AssistantController::class, 'apprenants'])->middleware('auth')->name('assistant.apprenants');
Route::get('/assistant/formateurs', [AssistantController::class, 'formateurs'])->middleware('auth')->name('assistant.formateurs');
Route::get('/assistant/modules', [App\Http\Controllers\AssistantModulesController::class, 'index'])->middleware('auth')->name('assistant.modules');
Route::get('/assistant/modules/create', [App\Http\Controllers\AssistantModulesController::class, 'create'])->middleware('auth')->name('assistant.modules.create');
Route::post('/assistant/modules', [App\Http\Controllers\AssistantModulesController::class, 'store'])->middleware('auth')->name('assistant.modules.store');
Route::get('/assistant/modules/{id}/edit', [App\Http\Controllers\AssistantModulesController::class, 'edit'])->middleware('auth')->name('assistant.modules.edit');
Route::put('/assistant/modules/{id}', [App\Http\Controllers\AssistantModulesController::class, 'update'])->middleware('auth')->name('assistant.modules.update');
Route::delete('/assistant/modules/{id}', [App\Http\Controllers\AssistantModulesController::class, 'destroy'])->middleware('auth')->name('assistant.modules.destroy');
Route::get('/assistant/modules/{id}', [App\Http\Controllers\AssistantModulesController::class, 'show'])->middleware('auth')->name('assistant.modules.show');
Route::get('/assistant/inscriptions', [AssistantController::class, 'inscriptions'])->middleware('auth')->name('assistant.inscriptions');
Route::get('/assistant/inscriptions/{id}', [App\Http\Controllers\AssistantInscriptionsController::class, 'show'])->middleware('auth')->name('assistant.inscriptions.show');
Route::get('/assistant/inscriptions/{id}/edit', [App\Http\Controllers\AssistantInscriptionsController::class, 'edit'])->middleware('auth')->name('assistant.inscriptions.edit');
Route::get('/assistant/paiements', [AssistantController::class, 'paiements'])->middleware('auth')->name('assistant.paiements');
Route::get('/assistant/paiements/{id}', [App\Http\Controllers\AssistantPaiementsController::class, 'show'])->middleware('auth')->name('assistant.paiements.show');
Route::get('/assistant/paiements/{id}/edit', [App\Http\Controllers\AssistantPaiementsController::class, 'edit'])->middleware('auth')->name('assistant.paiements.edit');
Route::get('/assistant/certificats/{id}', [App\Http\Controllers\AssistantCertificatsController::class, 'show'])->middleware('auth')->name('assistant.certificats.show');
Route::get('/assistant/certificats/{id}/edit', [App\Http\Controllers\AssistantCertificatsController::class, 'edit'])->middleware('auth')->name('assistant.certificats.edit');
Route::get('/assistant/documents', [AssistantController::class, 'documents'])->middleware('auth')->name('assistant.documents');
// create/store AVANT les routes dynamiques pour éviter la capture de "create" par {id}
Route::get('/assistant/documents/create', [AssistantController::class, 'createDocument'])->middleware('auth')->name('assistant.documents.create');
Route::post('/assistant/documents', [AssistantController::class, 'storeDocument'])->middleware('auth')->name('assistant.documents.store');
// Contraindre {id} à être numérique
Route::get('/assistant/documents/{id}', [App\Http\Controllers\AssistantDocumentsController::class, 'show'])->whereNumber('id')->middleware('auth')->name('assistant.documents.show');
Route::get('/assistant/documents/{id}/edit', [App\Http\Controllers\AssistantDocumentsController::class, 'edit'])->whereNumber('id')->middleware('auth')->name('assistant.documents.edit');
Route::delete('/assistant/documents/{id}', [App\Http\Controllers\AssistantDocumentsController::class, 'destroy'])->whereNumber('id')->middleware('auth')->name('assistant.documents.destroy');
Route::get('/assistant/niveaux', [App\Http\Controllers\AssistantNiveauxController::class, 'index'])->middleware('auth')->name('assistant.niveaux');
Route::get('/assistant/sessions', [App\Http\Controllers\AssistantSessionsController::class, 'index'])->middleware('auth')->name('assistant.sessions');
Route::get('/assistant/sessions/{id}', [App\Http\Controllers\AssistantSessionsController::class, 'show'])->middleware('auth')->name('assistant.sessions.show');
Route::get('/assistant/calendrier', [App\Http\Controllers\AssistantCalendrierController::class, 'index'])->middleware('auth')->name('assistant.calendrier');
Route::get('/assistant/calendrier/{id}/edit', [App\Http\Controllers\AssistantCalendrierController::class, 'edit'])->middleware('auth')->name('assistant.calendrier.edit');
Route::put('/assistant/calendrier/{id}', [App\Http\Controllers\AssistantCalendrierController::class, 'update'])->middleware('auth')->name('assistant.calendrier.update');
Route::get('/assistant/vacances', [App\Http\Controllers\AssistantVacancesController::class, 'index'])->middleware('auth')->name('assistant.vacances');
Route::get('/assistant/vacances/{id}/edit', [App\Http\Controllers\AssistantVacancesController::class, 'edit'])->middleware('auth')->name('assistant.vacances.edit');
Route::put('/assistant/vacances/{id}', [App\Http\Controllers\AssistantVacancesController::class, 'update'])->middleware('auth')->name('assistant.vacances.update');
Route::delete('/assistant/vacances/{id}', [App\Http\Controllers\AssistantVacancesController::class, 'destroy'])->middleware('auth')->name('assistant.vacances.destroy');
Route::get('/assistant/paiements', [App\Http\Controllers\AssistantPaiementsController::class, 'index'])->middleware('auth')->name('assistant.paiements');
Route::get('/assistant/certificats', [App\Http\Controllers\AssistantCertificatsController::class, 'index'])->middleware('auth')->name('assistant.certificats');
Route::get('/assistant/questionnaires', [App\Http\Controllers\AssistantQuestionnairesController::class, 'index'])->middleware('auth')->name('assistant.questionnaires');
Route::get('/assistant/niveaux/create', [App\Http\Controllers\AssistantNiveauxController::class, 'create'])->middleware('auth')->name('assistant.niveaux.create');
Route::post('/assistant/niveaux', [App\Http\Controllers\AssistantNiveauxController::class, 'store'])->middleware('auth')->name('assistant.niveaux.store');
Route::get('/assistant/sessions/create', [App\Http\Controllers\AssistantSessionsController::class, 'create'])->middleware('auth')->name('assistant.sessions.create');
Route::post('/assistant/sessions', [App\Http\Controllers\AssistantSessionsController::class, 'store'])->middleware('auth')->name('assistant.sessions.store');
Route::get('/assistant/sessions/{id}/edit', [App\Http\Controllers\AssistantSessionsController::class, 'edit'])->middleware('auth')->name('assistant.sessions.edit');
Route::put('/assistant/sessions/{id}', [App\Http\Controllers\AssistantSessionsController::class, 'update'])->middleware('auth')->name('assistant.sessions.update');
Route::delete('/assistant/sessions/{id}', [App\Http\Controllers\AssistantSessionsController::class, 'destroy'])->middleware('auth')->name('assistant.sessions.destroy');
Route::get('/assistant/calendrier/create', [App\Http\Controllers\AssistantCalendrierController::class, 'create'])->middleware('auth')->name('assistant.calendrier.create');
Route::post('/assistant/calendrier', [App\Http\Controllers\AssistantCalendrierController::class, 'store'])->middleware('auth')->name('assistant.calendrier.store');
Route::get('/assistant/vacances/create', [App\Http\Controllers\AssistantVacancesController::class, 'create'])->middleware('auth')->name('assistant.vacances.create');
Route::post('/assistant/vacances', [App\Http\Controllers\AssistantVacancesController::class, 'store'])->middleware('auth')->name('assistant.vacances.store');
Route::get('/assistant/paiements/create', [App\Http\Controllers\AssistantPaiementsController::class, 'create'])->middleware('auth')->name('assistant.paiements.create');
Route::post('/assistant/paiements', [App\Http\Controllers\AssistantPaiementsController::class, 'store'])->middleware('auth')->name('assistant.paiements.store');
Route::get('/assistant/certificats/create', [App\Http\Controllers\AssistantCertificatsController::class, 'create'])->middleware('auth')->name('assistant.certificats.create');
Route::post('/assistant/certificats', [App\Http\Controllers\AssistantCertificatsController::class, 'store'])->middleware('auth')->name('assistant.certificats.store');
Route::get('/assistant/questionnaires/create', [App\Http\Controllers\AssistantQuestionnairesController::class, 'create'])->middleware('auth')->name('assistant.questionnaires.create');
Route::post('/assistant/questionnaires', [App\Http\Controllers\AssistantQuestionnairesController::class, 'store'])->middleware('auth')->name('assistant.questionnaires.store');
Route::get('/assistant/modules/create', [App\Http\Controllers\AssistantModulesController::class, 'create'])->middleware('auth')->name('assistant.modules.create');
Route::post('/assistant/modules', [App\Http\Controllers\AssistantModulesController::class, 'store'])->middleware('auth')->name('assistant.modules.store');

// Apprenants du formateur
Route::middleware(['auth'])->group(function () {
    Route::get('/formateurs/apprenants', [\App\Http\Controllers\ApprenantsFormateurController::class, 'index'])->name('formateurs.apprenants.index');
    Route::get('/formateurs/apprenants/{apprenant}', [\App\Http\Controllers\ApprenantsFormateurController::class, 'show'])->name('formateurs.apprenants.show');
    // Profil formateur
    Route::get('/formateurs/profil', [\App\Http\Controllers\FormateurProfileController::class, 'show'])->name('formateurs.profil');
    Route::get('/formateurs/profil/edit', [\App\Http\Controllers\FormateurProfileController::class, 'edit'])->name('formateurs.profil.edit');
    Route::put('/formateurs/profil', [\App\Http\Controllers\FormateurProfileController::class, 'update'])->name('formateurs.profil.update');
});
// Validation cours à domicile
Route::get('/validation-cours-domicile', [\App\Http\Controllers\ValidationCoursDomicileController::class, 'index'])->name('validation_cours_domicile.index');
Route::post('/validation-cours-domicile/valider/{id}', [\App\Http\Controllers\ValidationCoursDomicileController::class, 'valider'])->name('validation_cours_domicile.valider');
Route::post('/validation-cours-domicile/accepter/{id}', [\App\Http\Controllers\ValidationCoursDomicileController::class, 'accepter'])->name('validation_cours_domicile.accepter');
Route::post('/validation-cours-domicile/refuser/{id}', [\App\Http\Controllers\ValidationCoursDomicileController::class, 'refuser'])->name('validation_cours_domicile.refuser');
Route::get('/validation-cours-domicile/historique', [\App\Http\Controllers\ValidationCoursDomicileController::class, 'historique'])->name('validation_cours_domicile.historique');

Route::resource('formateurs', FormateurController::class);
// Route pour activer/désactiver un formateur
Route::post('/formateurs/{formateur}/toggle-activation', [FormateurController::class, 'toggleActivation'])->name('formateurs.toggle-activation');
Route::resource('modules', ModuleController::class);
Route::resource('inscriptions', InscriptionController::class);
Route::resource('paiements', PaiementController::class);
Route::resource('admin/certificats', CertificatController::class)->names([
    'index' => 'admin.certificats.index',
    'create' => 'admin.certificats.create',
    'store' => 'admin.certificats.store',
    'show' => 'admin.certificats.show',
    'edit' => 'admin.certificats.edit',
    'update' => 'admin.certificats.update',
    'destroy' => 'admin.certificats.destroy',
]);

// Demande spécifique: rediriger /admin/certificats/{certificat}/generator vers le dashboard admin
Route::get('/admin/certificats/{certificat}/generator', function () {
    return redirect()->route('admin.dashboard');
})->name('admin.certificats.generator');
Route::resource('admin/documents', DocumentController::class)->names([
    'index' => 'admin.documents.index',
    'create' => 'admin.documents.create',
    'store' => 'admin.documents.store',
    'show' => 'admin.documents.show',
    'edit' => 'admin.documents.edit',
    'update' => 'admin.documents.update',
    'destroy' => 'admin.documents.destroy',
]);
Route::resource('questionnaires', QuestionnaireController::class);

// Routes pour la gestion des projets côté admin
Route::resource('admin/projets', AdminProjetController::class)->names([
    'index' => 'admin.projets.index',
    'create' => 'admin.projets.create',
    'store' => 'admin.projets.store',
    'show' => 'admin.projets.show',
    'edit' => 'admin.projets.edit',
    'update' => 'admin.projets.update',
    'destroy' => 'admin.projets.destroy',
]);

Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::view('/register/confirmation', 'auth.register_confirmation')->name('register.confirmation');
Route::view('/register/formateur/confirmation', 'auth.register_formateur_confirmation')->name('register.formateur_confirmation');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Routes dashboard admin
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/logo/edit', [\App\Http\Controllers\AdminLogoController::class, 'edit'])->name('admin.logo.edit');
    Route::post('/admin/logo-upload', [\App\Http\Controllers\AdminLogoController::class, 'upload'])->name('admin.logo.upload');
    Route::post('/utilisateur/logo-upload', [\App\Http\Controllers\UtilisateurLogoController::class, 'upload'])->name('utilisateur.logo.upload');
    Route::get('/admin/formateur/{id}', [AdminController::class, 'showFormateur'])->name('admin.formateur.show');
    Route::post('/admin/formateur/{id}/valider', [AdminController::class, 'validerFormateur'])->name('admin.formateur.valider');
    
    // Routes de gestion
    Route::get('/admin/utilisateurs', [AdminController::class, 'utilisateurs'])->name('admin.utilisateurs');
    Route::get('/admin/modules', [AdminController::class, 'modules'])->name('admin.modules');
    Route::get('/admin/modules/{module}', [AdminController::class, 'showModule'])->name('admin.modules.show');
    Route::get('/admin/modules/{module}/edit', [AdminController::class, 'editModule'])->name('admin.modules.edit');
    Route::put('/admin/modules/{module}', [AdminController::class, 'updateModule'])->name('admin.modules.update');
    Route::delete('/admin/modules/{module}', [AdminController::class, 'destroyModule'])->name('admin.modules.destroy');
    Route::get('/admin/modules/confirm-destroy-multiple', [ModuleController::class, 'confirmDestroyMultiple'])->name('admin.modules.confirmDestroyMultiple');
    Route::post('/admin/modules/destroy-multiple', [ModuleController::class, 'destroyMultiple'])->name('admin.modules.destroyMultiple');
    Route::get('/admin/inscriptions', [AdminController::class, 'inscriptions'])->name('admin.inscriptions');
    Route::get('/admin/paiements', [AdminController::class, 'paiements'])->name('admin.paiements');
    Route::get('/admin/paiements/en-attente', [AdminController::class, 'demandesPaiementEnAttente'])->name('admin.paiements.en_attente');
    Route::post('/admin/paiements/{id}/valider', [AdminController::class, 'validerPaiement'])->name('admin.paiements.valider');
    Route::post('/admin/paiements/{id}/refuser', [AdminController::class, 'refuserPaiement'])->name('admin.paiements.refuser');
    Route::get('/admin/paiements/{paiement}', [AdminController::class, 'showPaiement'])->name('admin.paiements.show');
    Route::get('/admin/paiements/{paiement}/edit', [AdminController::class, 'editPaiement'])->name('admin.paiements.edit');
    Route::put('/admin/paiements/{paiement}', [AdminController::class, 'updatePaiement'])->name('admin.paiements.update');
    Route::delete('/admin/paiements/{paiement}', [AdminController::class, 'destroyPaiement'])->name('admin.paiements.destroy');
    
    // Routes pour les niveaux
    Route::resource('niveaux', NiveauController::class)->names([
        'index' => 'admin.niveaux.index',
        'create' => 'admin.niveaux.create',
        'store' => 'admin.niveaux.store',
        'show' => 'admin.niveaux.show',
        'edit' => 'admin.niveaux.edit',
        'update' => 'admin.niveaux.update',
        'destroy' => 'admin.niveaux.destroy',
    ]);
    Route::post('/niveaux/{niveau}/assign-formateur', [NiveauController::class, 'assignFormateur'])->name('admin.niveaux.assign_formateur');
    
    // ROUTE CALENDRIER AVANT RESOURCE SESSIONS
    Route::get('/sessions/calendrier', [SessionFormationController::class, 'calendrier'])->name('admin.sessions.calendrier');
    
    // Routes pour les sessions de formation
    Route::resource('sessions', SessionFormationController::class)->names([
        'index' => 'admin.sessions.index',
        'create' => 'admin.sessions.create',
        'store' => 'admin.sessions.store',
        'show' => 'admin.sessions.show',
        'edit' => 'admin.sessions.edit',
        'update' => 'admin.sessions.update',
        'destroy' => 'admin.sessions.destroy',
    ]);
    
    // Routes pour les vacances
    Route::resource('vacances', VacanceController::class)->names([
        'index' => 'admin.vacances.index',
        'create' => 'admin.vacances.create',
        'store' => 'admin.vacances.store',
        'show' => 'admin.vacances.show',
        'edit' => 'admin.vacances.edit',
        'update' => 'admin.vacances.update',
        'destroy' => 'admin.vacances.destroy',
    ]);

    Route::get('/admin/assistants', [AdminController::class, 'assistants'])->name('admin.assistants');
    Route::get('/admin/assistants/create', [AdminController::class, 'createAssistant'])->name('admin.assistants.create');
    Route::post('/admin/assistants', [AdminController::class, 'storeAssistant'])->name('admin.assistants.store');
    Route::get('/admin/assistants/{id}', [AdminController::class, 'showAssistant'])->name('assistants.show');
    Route::get('/admin/assistants/{id}/edit', [AdminController::class, 'editAssistant'])->name('assistants.edit');
    Route::put('/admin/assistants/{id}', [AdminController::class, 'updateAssistant'])->name('assistants.update');
    Route::delete('/admin/assistants/{id}', [AdminController::class, 'destroyAssistant'])->name('assistants.destroy');
    // Route pour activer/désactiver un assistant
    Route::post('/admin/assistants/{assistant}/toggle-activation', [AdminController::class, 'toggleAssistantActivation'])->name('admin.assistants.toggle-activation');
    Route::get('/admin/inscriptions-en-attente', [App\Http\Controllers\AdminController::class, 'inscriptionsEnAttente'])->name('admin.inscriptions_en_attente');
    Route::post('/admin/inscriptions/{id}/valider', [App\Http\Controllers\AdminController::class, 'validerInscription'])->name('admin.inscriptions.valider');
    Route::get('/admin/inscriptions/{inscription}', [AdminController::class, 'showInscription'])->name('admin.inscriptions.show');
    Route::get('/admin/inscriptions/{inscription}/edit', [AdminController::class, 'editInscription'])->name('admin.inscriptions.edit');
    Route::put('/admin/inscriptions/{inscription}', [AdminController::class, 'updateInscription'])->name('admin.inscriptions.update');
    Route::delete('/admin/inscriptions/{inscription}', [AdminController::class, 'destroyInscription'])->name('admin.inscriptions.destroy');
    
    // Routes pour les notifications admin
    Route::get('/admin/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/admin/notifications/{id}/mark-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('admin.notifications.mark-read');
    Route::post('/admin/notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-read');
    Route::delete('/admin/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'delete'])->name('admin.notifications.delete');
    Route::get('/admin/notifications/ajax', [\App\Http\Controllers\Admin\NotificationController::class, 'getNotificationsAjax'])->name('admin.notifications.ajax');

// Route pour la page Appel à projets
Route::get('/projets/appel-a-projets', function() {
    return view('projets.appel-a-projets');
})->name('projets.appel_a_projets');

// Route pour le profil test de l'apprenant
Route::get('/prifil_test', [App\Http\Controllers\ProfilController::class, 'editTest'])->middleware('auth')->name('apprenants.prifil_test');
Route::post('/prifil_test', [App\Http\Controllers\ProfilController::class, 'updateTest'])->middleware('auth')->name('apprenants.prifil_test.update');
Route::post('/prifil_test/password', [App\Http\Controllers\ProfilController::class, 'updatePassword'])->middleware('auth')->name('apprenants.prifil_test.update_password');
// Route notification test
Route::get('/notification_test', [App\Http\Controllers\ApprenantNotificationController::class, 'index'])->middleware('auth')->name('apprenants.notification_test');
Route::post('/apprenants/notifications/{id}/mark-read', [App\Http\Controllers\ApprenantNotificationController::class, 'markAsRead'])->middleware('auth')->name('apprenants.notifications.mark-read');
Route::post('/apprenants/notifications/mark-all-read', [App\Http\Controllers\ApprenantNotificationController::class, 'markAllAsRead'])->middleware('auth')->name('apprenants.notifications.mark-all-read');
Route::get('/apprenants/notifications/ajax', [App\Http\Controllers\ApprenantNotificationController::class, 'getNotificationsAjax'])->middleware('auth')->name('apprenants.notifications.ajax');
// Route paramètre test
Route::get('/parametre_test', function () {
    return view('parametre_test');
})->middleware('auth')->name('apprenants.parametre_test');
// Route questionnaire test
Route::get('/questionnaire_test', [App\Http\Controllers\QuestionnaireController::class, 'apprenantTest'])->middleware('auth')->name('apprenants.questionnaire_test');
});

// Presence routes
Route::middleware(['auth'])->group(function () {
    // Formateur
    Route::get('/formateurs/presence', [PresenceController::class, 'formateurIndex'])->name('formateurs.presence.index');
    Route::post('/formateurs/presence/open', [PresenceController::class, 'formateurOpen'])->name('formateurs.presence.open');
    Route::post('/formateurs/presence/{presenceRequest}/close', [PresenceController::class, 'formateurClose'])->name('formateurs.presence.close');
    Route::get('/formateurs/presence/{presenceRequest}/feuille', [PresenceController::class, 'formateurSheet'])->name('formateurs.presence.sheet');
    Route::get('/formateurs/presence/{presenceRequest}/debug', [PresenceController::class, 'formateurDebug'])->name('formateurs.presence.debug');
    Route::get('/formateurs/present-format', [PresenceController::class, 'presentFormat'])->name('formateurs.present.format');
    Route::get('/formateurs/presentformat', [PresenceController::class, 'presentFormat'])->name('formateurs.present.format.alias1');
    Route::get('/formateurs/present_format', [PresenceController::class, 'presentFormat'])->name('formateurs.present.format.alias2');

    // Apprenant
    Route::get('/apprenants/presence', [PresenceController::class, 'apprenantIndex'])->name('apprenants.presence.index');
    Route::post('/apprenants/presence/mark', [PresenceController::class, 'apprenantMark'])->name('apprenants.presence.mark');

    // Admin
    Route::get('/admin/presence', [PresenceController::class, 'adminIndex'])->name('admin.presence.index');
    Route::get('/admin/presence/{presenceRequest}/pdf', [PresenceController::class, 'adminPdf'])->name('admin.presence.pdf');
});

// Test route to diagnose 404 issues quickly
Route::get('/test-present-format', function () {
	return response('OK present-format test', 200);
});

// Routes dashboard apprenant et pages associées
Route::middleware(['auth'])->group(function () {
    Route::get('/apprenants/dashboard', [App\Http\Controllers\ApprenantController::class, 'dashboard'])->name('apprenants.dashboard');
    Route::get('/achat', [App\Http\Controllers\AchatController::class, 'index'])->name('achat');
    
    Route::post('/achat/traiter', [App\Http\Controllers\AchatController::class, 'traiterPaiement'])->name('achat.traiter');
    Route::post('/achat/envoyer-demande', [App\Http\Controllers\AchatController::class, 'envoyerDemande'])->name('achat.envoyer_demande');
    // Profil
    Route::get('/apprenants/profil', [\App\Http\Controllers\ProfilController::class, 'index'])->name('apprenants.profil');
    // Paramètres
    Route::get('/apprenants/parametres', [\App\Http\Controllers\ParametreController::class, 'index'])->name('apprenants.parametres');
    Route::post('/apprenants/parametres', [\App\Http\Controllers\ParametreController::class, 'update'])->name('apprenants.parametres.update');
    // Notifications
    Route::get('/apprenants/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('apprenants.notifications');

    // Générateur de certificat en lecture seule pour apprenant
    Route::get('/certificats/generator/{certificat}', [\App\Http\Controllers\CertificatController::class, 'showCertificateGeneratorReadonly'])->name('certificats.generator.readonly');
    // Cours Maison
    Route::get('/cours-maison', [App\Http\Controllers\CoursMaisonController::class, 'index'])->name('cours.maison');
    Route::post('/cours-maison/demande', [App\Http\Controllers\DemandeCoursMaisonController::class, 'store'])->name('demande.cours.maison');
    Route::get('/mes-demandes-cours-maison', [App\Http\Controllers\DemandeCoursMaisonController::class, 'index'])->name('demandes.cours.maison.index');
    Route::get('/mes-demandes-cours-maison/{id}/edit', [App\Http\Controllers\DemandeCoursMaisonController::class, 'edit'])->name('demandes.cours.maison.edit');
    Route::put('/mes-demandes-cours-maison/{id}', [App\Http\Controllers\DemandeCoursMaisonController::class, 'update'])->name('demandes.cours.maison.update');
    Route::delete('/mes-demandes-cours-maison/{id}', [App\Http\Controllers\DemandeCoursMaisonController::class, 'destroy'])->name('demandes.cours.maison.destroy');

    // Validation des demandes de cours à domicile (admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/valider-cours', [App\Http\Controllers\ValiderCoursController::class, 'index'])->name('valider.cours.index');
    Route::get('/valider-cours/{id}', [App\Http\Controllers\ValiderCoursController::class, 'show'])->name('valider.cours.show');
    Route::post('/valider-cours/{id}/valider', [App\Http\Controllers\ValiderCoursController::class, 'valider'])->name('valider.cours.valider');
    Route::post('/valider-cours/{id}/refuser', [App\Http\Controllers\ValiderCoursController::class, 'refuser'])->name('valider.cours.refuser');
});

// Questionnaires
    Route::get('/apprenants/questionnaires', [\App\Http\Controllers\QuestionnaireController::class, 'apprenantIndex'])->name('apprenants.questionnaires');
    Route::get('/apprenants/questionnaires/{questionnaire}/repondre', [\App\Http\Controllers\QuestionnaireController::class, 'showForApprenant'])->name('apprenants.questionnaires.show');
    Route::post('/apprenants/questionnaires/{questionnaire}', [\App\Http\Controllers\QuestionnaireController::class, 'repondre'])->name('apprenants.questionnaires.repondre');
});

// Route inscription-module accessible sans auth pour test
Route::get('/apprenants/inscription-module', [App\Http\Controllers\ApprenantController::class, 'create'])->name('apprenants.inscription');

// Route pour que l'apprenant puisse voir et sauvegarder son certificat
Route::get('/apprenants/certificat/{certificat}', [App\Http\Controllers\ApprenantController::class, 'showCertificat'])->middleware('auth')->name('apprenants.certificat.show');
Route::get('/apprenants/inscription-module/documents', function() { return view('apprenants.inscription-module-documents'); })->name('apprenants.inscription.module.documents');
Route::get('/apprenants/inscription-module/certificats', function() { return view('apprenants.inscription-module-certificats'); })->name('apprenants.inscription.module.certificats');
Route::get('/apprenants/inscription-module/profil', function() { return view('apprenants.inscription-module-profil'); })->name('apprenants.inscription.module.profil');

// Paiement page (affichage du formulaire)
Route::get('/paiement', [\App\Http\Controllers\PaiementController::class, 'page'])->middleware('auth')->name('paiement.page');
// Paiement processing (si besoin d'une logique plus avancée, utilisez PaiementController)
Route::post('/paiement', [\App\Http\Controllers\PaiementController::class, 'store'])->middleware('auth')->name('paiement.process');

// Route de test pour vérifier l'authentification
Route::get('/test-auth', function () {
    if (auth()->check()) {
        return response()->json([
            'authenticated' => true,
            'user' => auth()->user()->only(['id', 'nom', 'prenom', 'email', 'type_compte']),
            'session_id' => session()->getId()
        ]);
    }
    return response()->json(['authenticated' => false]);
});



Route::view('/don', 'don')->name('don');

Route::view('/qui-sommes-nous', 'qui-sommes-nous')->name('qui-sommes-nous');

// Page Formations (publique)
Route::view('/formations', 'formations')->name('formations');

// Page Marketplace (publique)
Route::view('/marketplace', 'marketplace')->name('marketplace');

// Page Vie associative (publique)
Route::view('/vie-associative', 'vie-associative')->name('vie-associative');

// Page Actualités (publique)
Route::view('/actualites', 'actualites')->name('actualites');

Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify.email');

// Route de test Cascade
Route::get('/formation-test', [App\Http\Controllers\FormationController::class, 'index'])->name('formation.test');
Route::get('/certificat-test', [App\Http\Controllers\ApprenantController::class, 'certificatTest'])->middleware('auth')->name('apprenants.certificats.test');
Route::get('/module-test', [App\Http\Controllers\ApprenantController::class, 'moduleTest'])->middleware('auth')->name('apprenants.modules.test');
Route::view('/achat-test', 'apprenants.achat-test')->name('apprenants.achat.test');
Route::get('/document-test', [App\Http\Controllers\ApprenantController::class, 'documentsTest'])->middleware('auth')->name('apprenants.documents.test');

Route::get('/test-cascade', function() {
    return 'Cascade OK';
});

Route::get('/formateur/auto-login/{token}', [\App\Http\Controllers\FormateurController::class, 'autoLogin'])->name('formateur.auto_login');

Route::get('/documents/debug-generaux', [\App\Http\Controllers\DocumentController::class, 'debugDocumentsGeneraux']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/apprenants/questionnaires-test', function () {
    return 'Test OK';
});

// Fallback pour éviter les 404 Laravel
// Route::fallback(function () {
//     return redirect('/');
// });

// Routes pour les formations côté assistant
Route::prefix('assistant')->name('assistant.')->middleware(['auth'])->group(function () {
    Route::resource('formations', App\Http\Controllers\AssistantFormationController::class);
    Route::get('/formations/calendrier', [App\Http\Controllers\AssistantFormationController::class, 'calendrier'])->name('formations.calendrier');
});

Route::get('/assistant/cours-a-domicile', [App\Http\Controllers\AssistantCoursDomicileController::class, 'index'])->middleware('auth')->name('assistant.cours_domicile');
Route::get('/assistant/cours-a-domicile/create', [App\Http\Controllers\AssistantCoursDomicileController::class, 'create'])->middleware('auth')->name('assistant.cours_domicile.create');
Route::post('/assistant/cours-a-domicile', [App\Http\Controllers\AssistantCoursDomicileController::class, 'store'])->middleware('auth')->name('assistant.cours_domicile.store');
Route::post('/assistant/cours-a-domicile/valider/{id}', [App\Http\Controllers\AssistantCoursDomicileController::class, 'valider'])->middleware('auth')->name('assistant.cours_domicile.valider');
Route::post('/assistant/cours-a-domicile/refuser/{id}', [App\Http\Controllers\AssistantCoursDomicileController::class, 'refuser'])->middleware('auth')->name('assistant.cours_domicile.refuser');
// (déplacé plus haut)

Route::get('/admin/modules/bulk-delete', [App\Http\Controllers\AdminModuleBulkDeleteController::class, 'showBulkDelete'])->name('admin.modules.bulkDelete');
Route::post('/admin/modules/bulk-delete', [App\Http\Controllers\AdminModuleBulkDeleteController::class, 'bulkDelete'])->name('admin.modules.bulkDelete.post');

Route::get('/assistant/questionnaires/{questionnaire}', [App\Http\Controllers\AssistantQuestionnairesController::class, 'show'])->middleware('auth')->name('assistant.questionnaires.show');
Route::get('/assistant/questionnaires/{questionnaire}/edit', [App\Http\Controllers\AssistantQuestionnairesController::class, 'edit'])->middleware('auth')->name('assistant.questionnaires.edit');
Route::put('/assistant/questionnaires/{questionnaire}', [App\Http\Controllers\AssistantQuestionnairesController::class, 'update'])->middleware('auth')->name('assistant.questionnaires.update');
Route::delete('/assistant/questionnaires/{questionnaire}', [App\Http\Controllers\AssistantQuestionnairesController::class, 'destroy'])->middleware('auth')->name('assistant.questionnaires.destroy');

// PROJETS
Route::prefix('projets')->group(function () {
    Route::get('/', [App\Http\Controllers\ProjetController::class, 'index'])->name('projets.index');
    Route::get('/realises', [App\Http\Controllers\ProjetController::class, 'realises'])->name('projets.realises');
    Route::get('/a-financer', [App\Http\Controllers\ProjetController::class, 'aFinancer'])->name('projets.financer');
    Route::get('/don', [App\Http\Controllers\DonController::class, 'create'])->name('projets.don');
    Route::post('/don', [App\Http\Controllers\DonController::class, 'store'])->name('projets.don.store');
    Route::resource('appel-a-projets', App\Http\Controllers\AppelAProjetController::class);
    Route::post('/candidatures', [App\Http\Controllers\CandidatureController::class, 'store'])->name('candidatures.store');
    Route::resource('partenaires', App\Http\Controllers\PartenaireController::class);
    Route::resource('rapports', App\Http\Controllers\RapportController::class);
    Route::resource('galeries', App\Http\Controllers\GalerieController::class);
    Route::resource('faq', App\Http\Controllers\FAQController::class);
});

// Routes admin pour les dons
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::resource('dons', App\Http\Controllers\AdminDonController::class);
    Route::post('/dons/{don}/confirmer', [App\Http\Controllers\AdminDonController::class, 'confirmer'])->name('dons.confirmer');
    Route::post('/dons/{don}/annuler', [App\Http\Controllers\AdminDonController::class, 'annuler'])->name('dons.annuler');
    
    // Routes admin pour les appels à projets
    Route::resource('appels-a-projets', App\Http\Controllers\AdminAppelAProjetController::class);
    Route::post('/appels-a-projets/{appelAProjet}/cloturer', [App\Http\Controllers\AdminAppelAProjetController::class, 'cloturer'])->name('appels-a-projets.cloturer');
    
    // Routes admin pour les candidatures
    Route::resource('candidatures', App\Http\Controllers\AdminCandidatureController::class)->except(['create', 'store']);
    Route::get('/candidatures/{candidature}/download/{fileType}', [App\Http\Controllers\AdminCandidatureController::class, 'downloadFile'])->name('candidatures.download');

    // Admin: Galerie
    Route::resource('galeries', App\Http\Controllers\AdminGalerieController::class)->names([
        'index' => 'galeries.index',
        'create' => 'galeries.create',
        'store' => 'galeries.store',
        'show' => 'galeries.show',
        'edit' => 'galeries.edit',
        'update' => 'galeries.update',
        'destroy' => 'galeries.destroy',
    ]);

    // Admin: Partenaires (Entreprises)
    Route::resource('partenaires', App\Http\Controllers\AdminPartenaireController::class)->names([
        'index' => 'partenaires.index',
        'create' => 'partenaires.create',
        'store' => 'partenaires.store',
        'show' => 'partenaires.show',
        'edit' => 'partenaires.edit',
        'update' => 'partenaires.update',
        'destroy' => 'partenaires.destroy',
    ]);

    // Admin: Certificats
    Route::resource('certificats', CertificatController::class)->names([
        'index' => 'certificats.index',
        'create' => 'certificats.create',
        'store' => 'certificats.store',
        'show' => 'certificats.show',
        'edit' => 'certificats.edit',
        'update' => 'certificats.update',
        'destroy' => 'certificats.destroy',
    ]);
    
    // Routes spéciales pour les certificats
    Route::get('/certificats/{certificat}/download', [CertificatController::class, 'download'])->name('certificats.download');
    Route::get('/certificats/{certificat}/image', [CertificatController::class, 'generateCertificatImage'])->name('certificats.image');
    Route::get('/certificats/{certificat}/image-model', [CertificatController::class, 'generateCertificatImageFromModel'])->name('certificats.image-model');
    Route::get('/certificats/{certificat}/generator', [CertificatController::class, 'showCertificateGenerator'])->name('certificats.generator');
    Route::get('/certificats/{certificat}/generate-with-state', [CertificatController::class, 'generateWithSavedState'])->name('certificats.generate-with-state');
});

// Diagnostic routes (public) to isolate 404 causes
Route::get('/present-format', [\App\Http\Controllers\PresenceController::class, 'presentFormat'])->name('present.format.public');
Route::get('/formateurs/present-format-debug', function () {
	return response('OK formateurs present-format debug', 200);
});
Route::get('/apprenants/presence-debug', function () {
	return response('OK apprenants presence debug', 200);
});
