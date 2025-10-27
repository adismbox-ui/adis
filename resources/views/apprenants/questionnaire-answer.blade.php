@extends('apprenants.layout')
@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0 animate__animated animate__fadeIn">
        <div class="card-header bg-gradient bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-question-circle fa-lg me-2"></i>
                <h3 class="mb-0">{{ $questionnaire->titre }}</h3>
            </div>
            <div class="d-flex align-items-center">
                <!-- Timer -->
                <div class="me-3">
                    <div class="timer-display bg-warning text-dark px-3 py-2 rounded shadow">
                        <i class="fas fa-clock me-2"></i>
                        <span id="timer" class="fw-bold fs-5">{{ $questionnaire->minutes }}:00</span>
                    </div>
                </div>
                <a href="{{ route('apprenants.questionnaire_test') }}" class="btn btn-outline-light btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir quitter ? Votre progression sera perdue.')">
                    <i class="fas fa-times me-1"></i> Quitter
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Informations du questionnaire -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <p class="text-muted fs-5">{{ $questionnaire->description }}</p>
                </div>
                <div class="col-md-4">
                    <div class="d-flex flex-column gap-2">
                        <span class="badge bg-gradient bg-primary text-white fs-6 p-2">
                            <i class="fas fa-book me-1"></i> {{ $questionnaire->module->titre ?? 'Module' }}
                        </span>
                        <span class="badge bg-gradient bg-info text-white fs-6 p-2">
                            <i class="fas fa-layer-group me-1"></i> {{ $questionnaire->module->niveau->nom ?? 'Niveau' }}
                        </span>
                        <span class="badge bg-gradient bg-success text-white fs-6 p-2">
                            <i class="fas fa-list me-1"></i> {{ $questionnaire->questions->count() }} questions
                        </span>
                        @if($questionnaire->type_devoir)
                        <span class="badge bg-gradient bg-warning text-dark fs-6 p-2">
                            <i class="fas fa-calendar me-1"></i> {{ ucfirst($questionnaire->type_devoir) }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Progress bar -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Progression</span>
                    <span class="text-muted" id="progress-text">0/{{ $questionnaire->questions->count() }}</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" id="progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <!-- Formulaire de réponse -->
            <form method="POST" action="{{ route('apprenants.questionnaires.repondre', $questionnaire) }}" id="questionnaire-form">
                @csrf
                @if($questionnaire->questions->isEmpty())
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-info-circle fa-3x mb-3 text-info"></i>
                        <h5>Aucune question disponible pour ce questionnaire.</h5>
                    </div>
                @else
                    @foreach($questionnaire->questions as $index => $question)
                        <div class="card mb-4 border-0 shadow-sm question-card" data-question="{{ $index + 1 }}">
                            <div class="card-header bg-gradient bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 text-primary">
                                        <span class="badge bg-primary me-2 fs-6">{{ $index + 1 }}</span>
                                        {{ $question->texte }}
                                    </h5>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-star me-1"></i>{{ $question->points ?? 1 }} pts
                                        </span>
                                        <span class="badge bg-secondary question-status" id="status-{{ $index + 1 }}">
                                            <i class="fas fa-clock me-1"></i> En attente
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    @if(is_array($question->choix) && count($question->choix) > 0)
                                        @foreach($question->choix as $choix)
                                            <label class="list-group-item border-0 bg-transparent choice-item">
                                                <input type="radio" name="reponses[{{ $question->id }}]" value="{{ $choix }}" class="form-check-input me-3" required>
                                                <span class="ms-2 choice-text">{{ $choix }}</span>
                                            </label>
                                        @endforeach
                                    @else
                                        <div class="alert alert-danger">
                                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Erreur : les choix de la question ne sont pas valides.</h6>
                                            <small class="text-muted">
                                                Type de données : {{ gettype($question->choix) }}<br>
                                                Contenu : {{ is_string($question->choix) ? $question->choix : json_encode($question->choix) }}<br>
                                                Nombre d'éléments : {{ is_array($question->choix) ? count($question->choix) : 'N/A' }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Bouton de soumission -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            <span id="completion-status">Toutes les questions sont obligatoires</span>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg shadow" id="submit-btn" disabled>
                            <i class="fas fa-paper-plane me-2"></i>Envoyer mes réponses
                        </button>
                    </div>
                @endif
            </form>

            <!-- Messages de résultat -->
            @if(session('success'))
                <div class="alert alert-success mt-4 animate__animated animate__fadeIn">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger mt-4 animate__animated animate__fadeIn">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                </div>
                @if(session('incorrects'))
                    <div class="mt-3">
                        <h5 class="text-danger">Correction :</h5>
                        <ul class="list-group">
                            @foreach(session('incorrects') as $inc)
                                <li class="list-group-item">
                                    <strong>{{ $inc['texte'] }}</strong><br>
                                    <span class="text-success">Bonne réponse : {{ $inc['bonne_reponse'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
.timer-display {
    font-family: 'Courier New', monospace;
    min-width: 120px;
    text-align: center;
    transition: all 0.3s ease;
}
.timer-warning {
    background-color: #ffc107 !important;
    animation: pulse 1s infinite;
}
.timer-danger {
    background-color: #dc3545 !important;
    color: white !important;
    animation: pulse 0.5s infinite;
}
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}
.question-card {
    transition: all 0.3s ease;
    border-left: 4px solid #dee2e6;
}
.question-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}
.question-card.answered {
    border-left-color: #28a745;
}
.question-card.current {
    border-left-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25) !important;
}
.choice-item {
    transition: all 0.2s ease;
    border-radius: 8px;
    margin-bottom: 8px;
}
.choice-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}
.form-check-input:checked + .choice-text {
    font-weight: bold;
    color: #0d6efd;
}
.choice-item:has(.form-check-input:checked) {
    background-color: #e3f2fd;
    border-left: 3px solid #0d6efd;
}
.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}
.progress-bar {
    border-radius: 10px;
    transition: width 0.3s ease;
}
.question-status.answered {
    background-color: #28a745 !important;
}
.question-status.current {
    background-color: #007bff !important;
}
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.animate__fadeIn {
    animation-duration: 0.8s;
}
</style>

<script>
// Timer functionality
let totalSeconds = {{ $questionnaire->minutes * 60 }};
let timerInterval;
let hasSubmitted = false;

function updateTimer() {
    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;
    const timerDisplay = document.getElementById('timer');
    const timerContainer = timerDisplay.parentElement;
    
    timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    // Change color based on remaining time
    if (totalSeconds <= 60) { // Last minute
        timerContainer.className = 'timer-display bg-danger text-white px-3 py-2 rounded shadow';
    } else if (totalSeconds <= 300) { // Last 5 minutes
        timerContainer.className = 'timer-display bg-warning text-dark px-3 py-2 rounded shadow';
    }
    
    if (totalSeconds <= 0) {
        clearInterval(timerInterval);
        if (!hasSubmitted) {
            hasSubmitted = true;
            document.getElementById('questionnaire-form').submit();
        }
        return;
    }
    
    totalSeconds--;
}

function updateProgress() {
    const totalQuestions = {{ $questionnaire->questions->count() }};
    const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const completionStatus = document.getElementById('completion-status');
    const submitBtn = document.getElementById('submit-btn');
    
    const percentage = (answeredQuestions / totalQuestions) * 100;
    progressBar.style.width = percentage + '%';
    progressText.textContent = `${answeredQuestions}/${totalQuestions}`;
    
    // Update completion status
    if (answeredQuestions === totalQuestions) {
        completionStatus.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i>Toutes les questions sont répondues !';
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Envoyer mes réponses';
    } else {
        completionStatus.innerHTML = `<i class="fas fa-info-circle me-1"></i>Il reste ${totalQuestions - answeredQuestions} question(s) à répondre`;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Répondez à toutes les questions';
    }
    
    // Update question status
    document.querySelectorAll('.question-card').forEach((card, index) => {
        const questionNumber = index + 1;
        const statusBadge = document.getElementById(`status-${questionNumber}`);
        const hasAnswer = card.querySelector('input[type="radio"]:checked');
        
        if (hasAnswer) {
            card.classList.add('answered');
            card.classList.remove('current');
            statusBadge.className = 'badge bg-success question-status answered';
            statusBadge.innerHTML = '<i class="fas fa-check me-1"></i>Répondu';
        } else {
            card.classList.remove('answered');
            if (answeredQuestions === index) {
                card.classList.add('current');
                statusBadge.className = 'badge bg-primary question-status current';
                statusBadge.innerHTML = '<i class="fas fa-play me-1"></i>En cours';
            } else {
                card.classList.remove('current');
                statusBadge.className = 'badge bg-secondary question-status';
                statusBadge.innerHTML = '<i class="fas fa-clock me-1"></i>En attente';
            }
        }
    });
}

// Start timer when page loads
document.addEventListener('DOMContentLoaded', function() {
    timerInterval = setInterval(updateTimer, 1000);
    
    // Update progress on radio button changes
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', updateProgress);
    });
    
    // Initial progress update
    updateProgress();
    
    // Warn user before leaving page
    window.addEventListener('beforeunload', function(e) {
        if (totalSeconds > 0 && !hasSubmitted) {
            e.preventDefault();
            e.returnValue = 'Êtes-vous sûr de vouloir quitter ? Votre progression sera perdue.';
        }
    });
    
    // Form submission handling
    document.getElementById('questionnaire-form').addEventListener('submit', function(e) {
        if (hasSubmitted) {
            e.preventDefault();
            alert('Ce questionnaire a déjà été soumis !');
            return false;
        }
        
        if (totalSeconds <= 0) {
            e.preventDefault();
            alert('Le temps est écoulé ! Le questionnaire sera soumis automatiquement.');
            return false;
        }
        
        const totalQuestions = {{ $questionnaire->questions->count() }};
        const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
        
        if (answeredQuestions < totalQuestions) {
            e.preventDefault();
            alert(`Veuillez répondre à toutes les questions (${totalQuestions - answeredQuestions} restante(s))`);
            return false;
        }
        
        hasSubmitted = true;
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
    });
});

// Pause timer when user switches tabs
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        console.log('Page cachée - attention au temps !');
    }
});
</script>
@endsection 