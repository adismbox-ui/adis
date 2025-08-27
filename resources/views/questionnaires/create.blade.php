@extends('admin.layout')
@section('content')

<style>
/* Fond sombre animé */
body {
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 30%, rgba(45,80,22,0.1) 100%);
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}

/* Particules flottantes */
.particles {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 0;
}

.particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: rgba(127, 176, 105, 0.6);
    border-radius: 50%;
    animation: float 15s infinite linear;
}

@keyframes float {
    0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
}

/* Conteneur principal */
.main-container {
    position: relative;
    z-index: 1;
    padding: 2rem;
    animation: fadeInUp 1s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Carte principale */
.card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(127, 176, 105, 0.3);
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    position: relative;
    transition: all 0.3s ease;
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(127, 176, 105, 0.2), transparent);
    transition: left 0.5s;
}

.card:hover::before {
    left: 100%;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 50px rgba(127, 176, 105, 0.3);
    border-color: rgba(127, 176, 105, 0.5);
}

/* En-tête de carte */
.card-header {
    background: linear-gradient(135deg, rgba(127, 176, 105, 0.2) 0%, rgba(45, 80, 22, 0.3) 100%);
    border-bottom: 2px solid rgba(127, 176, 105, 0.3);
    color: #ffffff;
    font-weight: 700;
    position: relative;
    overflow: hidden;
}

.card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #7fb069, #a7c957);
    animation: shimmer 2s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { transform: translateX(-100%); }
    50% { transform: translateX(100%); }
}

.card-header h3, .card-header h5, .card-header h6 {
    color: #ffffff;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    font-weight: 700;
    margin: 0;
}

.card-header i {
    color: #a7c957;
    text-shadow: 0 1px 3px rgba(127, 176, 105, 0.5);
}

/* Corps de carte */
.card-body {
    background: rgba(255, 255, 255, 0.03);
    color: #ffffff;
    padding: 2rem;
}

/* Labels */
.form-label {
    color: #ffffff;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    margin-bottom: 0.5rem;
}

/* Champs de formulaire */
.form-control, .form-select {
    background: rgba(255, 255, 255, 0.08);
    border: 2px solid rgba(127, 176, 105, 0.3);
    border-radius: 10px;
    color: #ffffff;
    transition: all 0.3s ease;
    font-weight: 500;
}

.form-control:focus, .form-select:focus {
    background: rgba(255, 255, 255, 0.12);
    border-color: #a7c957;
    box-shadow: 0 0 0 0.2rem rgba(127, 176, 105, 0.25);
    color: #ffffff;
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

/* Boutons */
.btn {
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, #7fb069 0%, #4a7c59 100%);
    border: none;
    color: #ffffff;
    box-shadow: 0 4px 15px rgba(127, 176, 105, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #a7c957 0%, #7fb069 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(127, 176, 105, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, #4a7c59 0%, #2d5016 100%);
    border: none;
    color: #ffffff;
    box-shadow: 0 4px 15px rgba(74, 124, 89, 0.3);
}

.btn-success:hover {
    background: linear-gradient(135deg, #7fb069 0%, #4a7c59 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(127, 176, 105, 0.4);
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
    color: #ffffff;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.btn-danger:hover {
    background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
}

.btn-outline-secondary {
    background: transparent;
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: #ffffff;
}

.btn-outline-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
    color: #ffffff;
}

/* Messages d'erreur */
.invalid-feedback {
    color: #ff6b6b;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

/* Textes d'aide */
.text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

/* Lisibilité des listes déroulantes */
select.form-control, select.form-select { color: #000 !important; background-color: #fff !important; }
select.form-control option, select.form-select option { color: #000 !important; background-color: #fff !important; }

/* Alertes */
.alert {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(127, 176, 105, 0.3);
    border-radius: 10px;
    color: #ffffff;
}

.alert-info {
    background: linear-gradient(135deg, rgba(127, 176, 105, 0.1) 0%, rgba(45, 80, 22, 0.2) 100%);
    border-color: rgba(127, 176, 105, 0.5);
}

.alert h6 {
    color: #a7c957;
    font-weight: 700;
}

.alert p {
    color: rgba(255, 255, 255, 0.9);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

/* Question cards */
.question-card {
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid rgba(127, 176, 105, 0.3);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.question-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #7fb069, #a7c957);
    transition: left 0.3s ease;
}

.question-card:hover::before {
    left: 0;
}

.question-card:hover {
    transform: translateX(5px);
    box-shadow: 0 10px 30px rgba(127, 176, 105, 0.3);
}

/* Compteur de questions */
.question-counter {
    background: linear-gradient(135deg, #7fb069, #4a7c59);
    color: #ffffff;
    padding: 8px 16px;
    border-radius: 25px;
    font-weight: 600;
    display: inline-block;
    animation: bounce 2s infinite;
    box-shadow: 0 4px 15px rgba(127, 176, 105, 0.4);
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% { transform: translateY(0); }
    40%, 43% { transform: translateY(-10px); }
    70% { transform: translateY(-5px); }
}

/* Section d'importation */
.import-section {
    background: linear-gradient(135deg, rgba(127, 176, 105, 0.1) 0%, rgba(45, 80, 22, 0.2) 100%);
    border: 2px dashed rgba(127, 176, 105, 0.5);
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
}

.import-section:hover {
    background: linear-gradient(135deg, rgba(127, 176, 105, 0.2) 0%, rgba(45, 80, 22, 0.3) 100%);
    transform: scale(1.02);
}

/* Progress bar */
.progress {
    height: 8px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.2);
    overflow: hidden;
    margin-bottom: 2rem;
}

.progress-bar {
    background: linear-gradient(90deg, #7fb069, #a7c957);
    border-radius: 10px;
    transition: width 0.5s ease;
    height: 100%;
}

/* Titre principal */
.page-title {
    color: #ffffff;
    text-shadow: 0 3px 6px rgba(0, 0, 0, 0.8);
    font-weight: 900;
    font-size: 2.5rem;
    text-align: center;
    margin-bottom: 2rem;
    animation: glow 2s ease-in-out infinite alternate;
}

@keyframes glow {
    from { text-shadow: 0 3px 6px rgba(0, 0, 0, 0.8); }
    to { text-shadow: 0 3px 6px rgba(127, 176, 105, 0.5); }
}

/* Effet de lueur sur les icônes */
.fas, .far, .fab {
    color: #a7c957;
    text-shadow: 0 1px 3px rgba(127, 176, 105, 0.5);
    transition: all 0.3s ease;
}

.fas:hover, .far:hover, .fab:hover {
    color: #ffffff;
    text-shadow: 0 2px 6px rgba(127, 176, 105, 0.8);
    transform: scale(1.2);
}

/* Animations d'entrée */
.animate__animated {
    animation-duration: 1s;
}

.animate__fadeInUp {
    animation-name: fadeInUp;
}

.animate__fadeInLeft {
    animation-name: fadeInLeft;
}

.animate__fadeInRight {
    animation-name: fadeInRight;
}

@keyframes fadeInLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Input groups */
.input-group-text {
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(127, 176, 105, 0.3);
    color: #a7c957;
}

/* Toast notification */
.toast {
    background: linear-gradient(135deg, #7fb069, #4a7c59);
    color: #ffffff;
    border: none;
    border-radius: 10px;
    box-shadow: 0 8px 25px rgba(127, 176, 105, 0.3);
}
</style>

<!-- Particules flottantes -->
<div class="particles" id="particles"></div>

<div class="main-container">
    <h1 class="page-title">
        <i class="fas fa-clipboard-list me-3"></i>
        Créer un questionnaire
    </h1>
    
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas fa-plus fa-lg me-3"></i>
                    <div>
                        <h2 class="mb-0 fw-bold">Créer un Questionnaire</h2>
                        <p class="mb-0 opacity-75">Créez des évaluations interactives et engageantes</p>
                    </div>
                </div>
                <div class="text-end">
                    <div class="progress">
                        <div class="progress-bar" id="formProgress" style="width: 10%"></div>
                    </div>
                    <small class="opacity-75">Progression du formulaire</small>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('questionnaires.store') }}" id="questionnaireForm">
                @csrf
                
                <!-- Informations de base -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Informations générales
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-heading me-1"></i>Titre du questionnaire *
                                </label>
                                <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre') }}" required placeholder="Entrez le titre...">
                                @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock me-1"></i>Temps limite (minutes) *
                                </label>
                                <input type="number" class="form-control @error('minutes') is-invalid @enderror" id="minutes" name="minutes" value="{{ old('minutes') }}" min="1" max="180" required placeholder="30">
                                @error('minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Durée recommandée : 15-120 minutes</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-align-left me-1"></i>Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Décrivez le contenu et les objectifs...">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- Configuration du devoir -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>Configuration du devoir
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-list-ul me-1"></i>Type de devoir *
                                </label>
                                <select class="form-control @error('type_devoir') is-invalid @enderror" id="type_devoir" name="type_devoir" required>
                                    <option value="">-- Choisir le type --</option>
                                    <option value="hebdomadaire" {{ old('type_devoir') == 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire (min. 2 questions)</option>
                                    <option value="mensuel" {{ old('type_devoir') == 'mensuel' ? 'selected' : '' }}>Mensuel (min. 8 questions)</option>
                                    <option value="final" {{ old('type_devoir') == 'final' ? 'selected' : '' }}>Final (min. 66 questions)</option>
                                </select>
                                @error('type_devoir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar-week me-1"></i>Semaine *
                                </label>
                                <select class="form-control @error('semaine') is-invalid @enderror" id="semaine" name="semaine" required>
                                    <option value="">-- Choisir la semaine --</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('semaine') == $i ? 'selected' : '' }}>Semaine {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('semaine')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-layer-group me-1"></i>Niveau concerné *
                                </label>
                                <select class="form-control" id="niveau_id" name="niveau_id" required>
                                    <option value="">-- Choisir un niveau --</option>
                                    @foreach($niveaux as $niveau)
                                        <option value="{{ $niveau->id }}" {{ old('niveau_id') == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Programmation automatique -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-clock me-2"></i>Programmation automatique
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-calendar-alt me-1"></i>Session de formation *
                                        </label>
                                        <select class="form-control" id="session_id" name="session_id" required>
                                            <option value="">-- Choisir une session --</option>
                                            @foreach($sessions ?? [] as $session)
                                                <option value="{{ $session->id }}" data-debut="{{ $session->date_debut }}" data-fin="{{ $session->date_fin }}">
                                                    {{ $session->nom }} ({{ \Carbon\Carbon::parse($session->date_debut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($session->date_fin)->format('d/m/Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">La session définit les dates de début et fin pour calculer les dimanches</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-calendar-check me-1"></i>Date et heure d'envoi
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            <input type="datetime-local" class="form-control" id="date_envoi" name="date_envoi" required>
                                        </div>
                                        <small class="text-muted">Définissez manuellement la date et l'heure d'envoi</small>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Programmation d'envoi :</h6>
                                    <ul class="mb-0">
                                        <li>Le questionnaire sera envoyé automatiquement à la date et heure spécifiées</li>
                                        <li>Vous pouvez définir manuellement la date et l'heure d'envoi</li>
                                        <li>Les apprenants recevront une notification par email</li>
                                        <li>Le système vérifie toutes les heures les contenus à envoyer</li>
                                    </ul>
                                </div>
                                
                                <!-- Bouton de confirmation de la programmation -->
                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-primary btn-lg" id="confirmProgrammationBtn" onclick="confirmProgrammation()">
                                        <i class="fas fa-check-circle me-2"></i>OK - Confirmer la date et l'heure d'envoi
                                    </button>
                                    <div id="programmationStatus" class="mt-2" style="display: none;">
                                        <div class="alert alert-success" id="programmationSuccess" style="display: none;">
                                            <i class="fas fa-check-circle me-2"></i>Date et heure confirmées avec succès !
                                        </div>
                                        <div class="alert alert-danger" id="programmationError" style="display: none;">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Veuillez sélectionner une date et une heure valides.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-book me-1"></i>Module concerné *
                            </label>
                            <select class="form-control @error('module_id') is-invalid @enderror" id="module_id" name="module_id" required>
                                <option value="">-- Choisir un module --</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module->id }}" data-niveau="{{ $module->niveau_id }}" style="display:none;">
                                        {{ $module->titre }} @if($module->niveau) (Niveau : {{ $module->niveau->nom }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('module_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Règles par type -->
                        <div class="alert alert-info" id="typeRules" style="display: none;">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Règles pour ce type :</strong>
                            <div id="rulesContent"></div>
                        </div>
                    </div>
                </div>

                <!-- Section d'importation -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-file-import me-2"></i>Importer des questions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="import-section" id="importSection">
                            <div class="text-center mb-3">
                                <i class="fas fa-file-import fa-3x mb-3" style="color: #a7c957;"></i>
                                <h5 style="color: #a7c957;"><i class="fas fa-cloud-upload-alt me-2"></i>Importer des questions depuis un fichier</h5>
                            </div>
                            <div class="mb-3">
                                <label for="questionFile" class="form-label">Fichier de questions (JSON, CSV ou TXT)</label>
                                <input type="file" class="form-control" id="questionFile" accept=".json,.csv,.txt">
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <strong>Formats acceptés :</strong><br>
                                        <code style="background: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 4px;">JSON:</code> [{"texte":"Question?", "choix":["option1","option2"], "bonne_reponse":"option1", "points":1}]<br>
                                        <code style="background: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 4px;">TXT:</code> Question?|option1;option2;option3|bonne_reponse|points<br>
                                        <strong>💡 L'importation se fait automatiquement dès qu'un fichier est sélectionné !</strong>
                                    </small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-secondary" id="importQuestionsBtn" style="display: none;">
                                <i class="fas fa-file-import me-2"></i>Importer les questions (optionnel)
                            </button>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <strong>Format TXT :</strong> Question|Option1;Option2;Option3|BonneRéponse|Points<br>
                                <strong>Format JSON :</strong> [{"texte":"Question?", "choix":["opt1","opt2"], "bonne_reponse":"opt1", "points":1}]
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Section des questions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-question-circle me-2"></i>Questions du questionnaire
                            </h5>
                            <span class="question-counter" id="questionCounter">0 question(s) ajoutée(s)</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="questionsContainer">
                            <!-- Questions dynamiques -->
                        </div>
                        
                        <div class="text-center">
                            <button type="button" class="btn btn-primary btn-lg" onclick="addQuestion()">
                                <i class="fas fa-plus me-2"></i>Ajouter une question
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-end gap-3">
                    <button type="button" class="btn btn-outline-secondary">
                        <i class="fas fa-save me-2"></i>Enregistrer comme brouillon
                    </button>
                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                        <i class="fas fa-check me-2"></i>Publier le questionnaire
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let questionIndex = 0;
let formProgress = 10;

// Génération des particules
document.addEventListener('DOMContentLoaded', function() {
    const particlesContainer = document.getElementById('particles');
    for (let i = 0; i < 20; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 15 + 's';
        particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
        particlesContainer.appendChild(particle);
    }
});

// Mise à jour de la barre de progression
function updateProgress() {
    const form = document.getElementById('questionnaireForm');
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    const filled = Array.from(inputs).filter(input => input.value.trim() !== '').length;
    const questionsCount = document.querySelectorAll('#questionsContainer .question-card').length;
    
    formProgress = Math.min(90, (filled / inputs.length) * 60 + (questionsCount > 0 ? 30 : 0));
    
    document.getElementById('formProgress').style.width = formProgress + '%';
}

// Gestion des règles par type de devoir
function updateTypeRules() {
    const typeDevoir = document.getElementById('type_devoir').value;
    const rulesDiv = document.getElementById('typeRules');
    const rulesContent = document.getElementById('rulesContent');
    
    if (typeDevoir) {
        const rules = {
            'hebdomadaire': '• 2-10 questions minimum<br>• Durée recommandée : 15-30 minutes<br>• Évaluation hebdomadaire',
            'mensuel': '• 8-20 questions minimum<br>• Durée recommandée : 45-60 minutes<br>• Évaluation mensuelle',
            'final': '• 66+ questions minimum<br>• Durée recommandée : 90-120 minutes<br>• Évaluation finale'
        };
        
        rulesContent.innerHTML = rules[typeDevoir] || '';
        rulesDiv.style.display = 'block';
    } else {
        rulesDiv.style.display = 'none';
    }
    
    updateQuestionCounter();
}

// Mise à jour du compteur de questions
function updateQuestionCounter() {
    const count = document.querySelectorAll('#questionsContainer .question-card').length;
    const counter = document.getElementById('questionCounter');
    const typeDevoir = document.getElementById('type_devoir').value;
    
    counter.textContent = `${count} question(s) ajoutée(s)`;
    
    const minQuestions = getMinQuestions(typeDevoir);
    const submitBtn = document.getElementById('submitBtn');
    
    if (count < minQuestions && typeDevoir) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>Minimum ${minQuestions} questions requises`;
        submitBtn.classList.remove('btn-success');
        submitBtn.classList.add('btn-danger');
    } else {
        submitBtn.disabled = false;
        submitBtn.innerHTML = `<i class="fas fa-check me-2"></i>Publier le questionnaire`;
        submitBtn.classList.remove('btn-danger');
        submitBtn.classList.add('btn-success');
    }
    
    updateProgress();
}

function getMinQuestions(typeDevoir) {
    const mins = { 'hebdomadaire': 2, 'mensuel': 8, 'final': 66 };
    return mins[typeDevoir] || 0;
}

// Ajout d'une question
function addQuestion() {
    const container = document.getElementById('questionsContainer');
    const questionDiv = document.createElement('div');
    questionDiv.className = 'question-card';
    
    questionDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="text-primary mb-0 fw-bold">
                <i class="fas fa-question-circle me-2"></i>Question ${questionIndex + 1}
            </h6>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion(this)">
                <i class="fas fa-trash me-1"></i>Supprimer
            </button>
        </div>
        
        <div class="mb-3">
            <label class="form-label">
                <i class="fas fa-edit me-1"></i>Question *
            </label>
            <input type="text" class="form-control" name="questions[${questionIndex}][texte]" required placeholder="Saisissez votre question...">
        </div>
        
        <div class="mb-3">
            <label class="form-label">
                <i class="fas fa-list me-1"></i>Choix de réponses
            </label>
            <div class="choix-container" data-question="${questionIndex}">
                <div class="choix-list"></div>
                <button type="button" class="btn btn-success btn-sm mt-2" onclick="addChoix(${questionIndex})">
                    <i class="fas fa-plus me-1"></i>Ajouter un choix
                </button>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8 mb-3">
                <label class="form-label text-success">
                    <i class="fas fa-check-circle me-1"></i>Bonne réponse *
                </label>
                <input type="text" class="form-control" name="questions[${questionIndex}][bonne_reponse]" required placeholder="Saisissez la bonne réponse...">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label text-warning">
                    <i class="fas fa-star me-1"></i>Points *
                </label>
                <input type="number" class="form-control" name="questions[${questionIndex}][points]" value="1" min="1" max="100" required>
            </div>
        </div>
    `;
    
    container.appendChild(questionDiv);
    
    // Ajouter deux choix par défaut
    const choixList = questionDiv.querySelector('.choix-list');
    addChoixToList(choixList, questionIndex);
    addChoixToList(choixList, questionIndex);
    
    questionIndex++;
    updateQuestionCounter();
    
    // Animation de scroll vers la nouvelle question
    questionDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function addChoix(qIndex) {
    const choixList = document.querySelector(`[data-question="${qIndex}"] .choix-list`);
    addChoixToList(choixList, qIndex);
}

function addChoixToList(choixList, qIndex) {
    const choixDiv = document.createElement('div');
    choixDiv.className = 'input-group mb-2';
    choixDiv.innerHTML = `
        <span class="input-group-text"><i class="fas fa-circle text-muted"></i></span>
        <input type="text" class="form-control" name="questions[${qIndex}][choix][]" placeholder="Option de réponse" required>
        <button type="button" class="btn btn-outline-danger" onclick="this.parentNode.remove()">
            <i class="fas fa-minus"></i>
        </button>
    `;
    choixList.appendChild(choixDiv);
}

function removeQuestion(btn) {
    const questionCard = btn.closest('.question-card');
    questionCard.remove();
    updateQuestionCounter();
}

// Événements
document.getElementById('type_devoir').addEventListener('change', function() {
    updateTypeRules();
    updateQuestionCounter();
    setTimeout(addMinimumQuestions, 100);
});

document.getElementById('niveau_id').addEventListener('change', function() {
    var niveauId = this.value;
    var moduleSelect = document.getElementById('module_id');
    Array.from(moduleSelect.options).forEach(function(opt) {
        if (!opt.value) return opt.style.display = '';
        opt.style.display = (opt.getAttribute('data-niveau') === niveauId) ? '' : 'none';
    });
    moduleSelect.value = '';
    updateProgress();
});

// Suggestion de date d'envoi (optionnelle)
document.getElementById('session_id').addEventListener('change', function() {
    suggestEnvoiDate();
});

document.getElementById('semaine').addEventListener('change', function() {
    suggestEnvoiDate();
});

function suggestEnvoiDate() {
    const sessionSelect = document.getElementById('session_id');
    const semaineSelect = document.getElementById('semaine');
    const dateEnvoiInput = document.getElementById('date_envoi');
    
    if (sessionSelect.value && semaineSelect.value) {
        const selectedOption = sessionSelect.options[sessionSelect.selectedIndex];
        const dateDebut = selectedOption.getAttribute('data-debut');
        const semaine = parseInt(semaineSelect.value);
        
        // Calculer le premier dimanche après la date de début
        const debut = new Date(dateDebut);
        const premierDimanche = getNextSunday(debut);
        
        // Ajouter (semaine - 1) * 7 jours pour obtenir le dimanche de la semaine demandée
        const dateEnvoi = new Date(premierDimanche);
        dateEnvoi.setDate(premierDimanche.getDate() + ((semaine - 1) * 7));
        
        // Définir l'heure à 13h00 (dimanche soir)
        dateEnvoi.setHours(13, 0, 0, 0);
        
        // Formater la date pour datetime-local
        const year = dateEnvoi.getFullYear();
        const month = String(dateEnvoi.getMonth() + 1).padStart(2, '0');
        const day = String(dateEnvoi.getDate()).padStart(2, '0');
        const hours = String(dateEnvoi.getHours()).padStart(2, '0');
        const minutes = String(dateEnvoi.getMinutes()).padStart(2, '0');
        
        dateEnvoiInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
        
        // Vérifier si la date est dans le passé
        if (dateEnvoi < new Date()) {
            dateEnvoiInput.style.borderColor = '#dc3545';
            dateEnvoiInput.style.backgroundColor = 'rgba(220, 53, 69, 0.1)';
        } else {
            dateEnvoiInput.style.borderColor = '';
            dateEnvoiInput.style.backgroundColor = '';
        }
    }
}

function getNextSunday(date) {
    const day = date.getDay();
    const diff = date.getDate() + (7 - day); // Dimanche = 0
    return new Date(date.setDate(diff));
}

// Initialisation
window.addEventListener('DOMContentLoaded', function() {
    updateTypeRules();
    updateQuestionCounter();
    updateProgress();
    
    // Initialiser les modules selon le niveau sélectionné
    var niveauId = document.getElementById('niveau_id').value;
    var moduleSelect = document.getElementById('module_id');
    Array.from(moduleSelect.options).forEach(function(opt) {
        if (!opt.value) return opt.style.display = '';
        opt.style.display = (opt.getAttribute('data-niveau') === niveauId) ? '' : 'none';
    });

    // Importation automatique dès qu'un fichier est sélectionné
    const questionFileInput = document.getElementById('questionFile');
    if (questionFileInput) {
        questionFileInput.addEventListener('change', function() {
            console.log('Fichier sélectionné automatiquement');
            if (this.files.length > 0) {
                console.log('Fichier détecté:', this.files[0].name);
                
                // Afficher un message de chargement
                const importSection = this.closest('.import-section');
                if (importSection) {
                    const loadingMsg = document.createElement('div');
                    loadingMsg.className = 'alert alert-info mt-2';
                    loadingMsg.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Importation en cours...';
                    importSection.appendChild(loadingMsg);
                }
                
                parseQuestionsFile(this.files[0], function(questions) {
                    console.log('Importation automatique terminée avec', questions.length, 'questions');
                    setTimeout(() => {
                        for (const q of questions) {
                            console.log('Ajout automatique de question:', q);
                            addImportedQuestion(q);
                        }
                        
                        // Supprimer le message de chargement
                        if (importSection) {
                            const loadingMsg = importSection.querySelector('.alert-info');
                            if (loadingMsg) {
                                loadingMsg.remove();
                            }
                        }
                        
                        // Afficher un message de succès
                        const successMsg = document.createElement('div');
                        successMsg.className = 'alert alert-success mt-2';
                        successMsg.innerHTML = `<i class="fas fa-check-circle me-2"></i>${questions.length} questions importées automatiquement avec succès !`;
                        importSection.appendChild(successMsg);
                        
                        // Supprimer le message de succès après 3 secondes
                        setTimeout(() => {
                            if (successMsg.parentNode) {
                                successMsg.remove();
                            }
                        }, 3000);
                        
                    }, 1000);
                });
            }
        });
    }
});

// Ajouter automatiquement les questions minimum si nécessaire
function addMinimumQuestions() {
    const typeDevoir = document.getElementById('type_devoir').value;
    const minQuestions = getMinQuestions(typeDevoir);
    const currentCount = document.querySelectorAll('#questionsContainer .question-card').length;
    
    if (currentCount < minQuestions) {
        const questionsToAdd = minQuestions - currentCount;
        for (let i = 0; i < questionsToAdd; i++) {
            addQuestion();
        }
    }
}

// Fonction pour parser les fichiers de questions
function parseQuestionsFile(file, callback) {
    console.log('Début du parsing du fichier:', file.name);
    const reader = new FileReader();
    
    reader.onload = function(e) {
        console.log('Fichier lu avec succès');
        let questions = [];
        try {
            if (file.name.endsWith('.json')) {
                console.log('Parsing JSON...');
                questions = JSON.parse(e.target.result);
                console.log('Questions JSON parsées:', questions);
            } else if (file.name.endsWith('.csv') || file.name.endsWith('.txt')) {
                console.log('Parsing CSV/TXT...');
                const lines = e.target.result.split('\n');
                console.log('Lignes trouvées:', lines.length);
                for (const line of lines) {
                    if (!line.trim()) continue;
                    const parts = line.split('|');
                    console.log('Ligne parsée:', parts);
                    if (parts.length >= 4) {
                        const [texte, choix, bonne_reponse, points] = parts;
                        questions.push({
                            texte: texte.trim(),
                            choix: choix.split(';').map(s => s.trim()),
                            bonne_reponse: bonne_reponse.trim(),
                            points: parseInt(points) || 1
                        });
                    }
                }
            } else {
                throw new Error('Format de fichier non supporté');
            }
        } catch (err) {
            console.error('Erreur de parsing:', err);
            alert('Erreur de parsing: ' + err.message);
            return;
        }
        console.log('Questions finales:', questions);
        callback(questions);
    };
    
    reader.onerror = function() {
        console.error('Erreur de lecture du fichier');
        alert('Erreur lors de la lecture du fichier');
    };
    
    reader.readAsText(file);
}

// Fonction pour ajouter une question importée
function addImportedQuestion(questionData) {
    // Créer une nouvelle question
    addQuestion();
    // Récupérer le dernier élément ajouté
    const questions = document.querySelectorAll('#questionsContainer .question-card');
    const lastQuestion = questions[questions.length - 1];
    // Remplir les champs
    lastQuestion.querySelector('input[name^="questions"][name$="[texte]"]').value = questionData.texte || '';
    lastQuestion.querySelector('input[name^="questions"][name$="[bonne_reponse]"]').value = questionData.bonne_reponse || '';
    lastQuestion.querySelector('input[name^="questions"][name$="[points]"]').value = questionData.points || 1;
    // Ajouter les choix
    const choixList = lastQuestion.querySelector('.choix-list');
    choixList.innerHTML = '';
    if (Array.isArray(questionData.choix)) {
        questionData.choix.forEach(choix => {
            const choixDiv = document.createElement('div');
            choixDiv.className = 'input-group mb-2';
            choixDiv.innerHTML = `
                <span class="input-group-text"><i class="fas fa-circle text-muted"></i></span>
                <input type="text" class="form-control" value="${choix}" name="questions[${questionIndex-1}][choix][]" placeholder="Option de réponse" required>
                <button type="button" class="btn btn-outline-danger" onclick="this.parentNode.remove()">
                    <i class="fas fa-minus"></i>
                </button>
            `;
            choixList.appendChild(choixDiv);
        });
    }
}

// Fonction pour confirmer la programmation
function confirmProgrammation() {
    const dateEnvoi = document.getElementById('date_envoi').value;
    const statusDiv = document.getElementById('programmationStatus');
    const successDiv = document.getElementById('programmationSuccess');
    const errorDiv = document.getElementById('programmationError');
    const confirmBtn = document.getElementById('confirmProgrammationBtn');
    
    // Masquer les messages précédents
    statusDiv.style.display = 'none';
    successDiv.style.display = 'none';
    errorDiv.style.display = 'none';
    
    if (!dateEnvoi) {
        // Afficher l'erreur
        statusDiv.style.display = 'block';
        errorDiv.style.display = 'block';
        confirmBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Erreur - Sélectionnez une date et heure';
        confirmBtn.classList.remove('btn-primary');
        confirmBtn.classList.add('btn-danger');
        
        // Remettre le bouton normal après 3 secondes
        setTimeout(() => {
            confirmBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>OK - Confirmer la date et l\'heure d\'envoi';
            confirmBtn.classList.remove('btn-danger');
            confirmBtn.classList.add('btn-primary');
        }, 3000);
        return;
    }
    
    // Vérifier que la date est dans le futur
    const selectedDate = new Date(dateEnvoi);
    const now = new Date();
    
    if (selectedDate <= now) {
        statusDiv.style.display = 'block';
        errorDiv.style.display = 'block';
        errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>La date et l\'heure doivent être dans le futur.';
        confirmBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Erreur - Date dans le passé';
        confirmBtn.classList.remove('btn-primary');
        confirmBtn.classList.add('btn-danger');
        
        setTimeout(() => {
            confirmBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>OK - Confirmer la date et l\'heure d\'envoi';
            confirmBtn.classList.remove('btn-danger');
            confirmBtn.classList.add('btn-primary');
        }, 3000);
        return;
    }
    
    // Succès
    statusDiv.style.display = 'block';
    successDiv.style.display = 'block';
    confirmBtn.innerHTML = '<i class="fas fa-check me-2"></i>Confirmé !';
    confirmBtn.classList.remove('btn-primary');
    confirmBtn.classList.add('btn-success');
    
    // Formater la date pour l'affichage
    const formattedDate = selectedDate.toLocaleString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    successDiv.innerHTML = `<i class="fas fa-check-circle me-2"></i>Date et heure confirmées : ${formattedDate}`;
    
    // Remettre le bouton normal après 5 secondes
    setTimeout(() => {
        confirmBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>OK - Confirmer la date et l\'heure d\'envoi';
        confirmBtn.classList.remove('btn-success');
        confirmBtn.classList.add('btn-primary');
    }, 5000);
    
    // Masquer le message de succès après 8 secondes
    setTimeout(() => {
        statusDiv.style.display = 'none';
    }, 8000);
}
</script>

@endsection 