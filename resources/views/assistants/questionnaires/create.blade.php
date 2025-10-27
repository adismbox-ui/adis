@extends('assistants.layout')
@section('content')
<style>
    /* Harmonisation avec assistants/niveaux/create: style sobre */
    body { background: none !important; min-height: auto !important; }
    .container, .container-fluid { padding: 1.5rem !important; }
    .main-card, .card { background: rgba(255, 255, 255, 0.08) !important; border: 1px solid rgba(229, 231, 235, 0.25) !important; box-shadow: none !important; border-radius: 8px !important; }
    .card-header { background: rgba(248, 250, 252, 0.2) !important; color: #111827 !important; border-bottom: 1px solid rgba(229, 231, 235, 0.25) !important; border-radius: 8px 8px 0 0 !important; padding: 0.75rem 1rem !important; }
    .card-body { background: transparent !important; }
    .card-header h3, .card-header h2, .card-header h5 { color: #111827 !important; text-shadow: none !important; }
    .form-label { color: #111827 !important; font-weight: 600 !important; }
    .form-control, .form-select { color: #111827 !important; background: #fff !important; border: 1px solid #e5e7eb !important; box-shadow: none !important; }
    .btn-primary, .btn-success, .btn-secondary { border-radius: 6px !important; box-shadow: none !important; }
    /* Listes déroulantes fond noir */
    select.form-control, select.form-select { background-color: #000 !important; color: #fff !important; }
    select.form-control option, select.form-select option { background-color: #000 !important; color: #fff !important; }
</style>
</style>

 

<div class="container-fluid py-4">
    <div class="main-card shadow-lg border-0 animate__animated animate__fadeInUp">
        <div class="card-header text-white d-flex align-items-center">
            <i class="fas fa-plus fa-2x me-3 pulse-animation"></i>
            <h3 class="mb-0">Créer un questionnaire</h3>
            <div class="ms-auto">
                <i class="fas fa-clipboard-list fa-2x opacity-50"></i>
            </div>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('assistant.questionnaires.store') }}" id="questionnaireForm">
                @csrf
                
                <!-- Informations de base -->
                <div class="row fade-in">
                    <div class="col-12">
                        <div class="mb-4">
                            <label for="titre" class="form-label">
                                <i class="fas fa-heading me-2 text-success"></i>Titre du questionnaire
                            </label>
                            <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre') }}" required>
                            @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row fade-in">
                    <div class="col-12">
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-2 text-success"></i>Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row fade-in">
                    <div class="col-md-6 mb-4">
                        <label for="type_devoir" class="form-label">
                            <i class="fas fa-tasks me-2 text-success"></i>Type de devoir <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('type_devoir') is-invalid @enderror" id="type_devoir" name="type_devoir" required>
                            <option value="">-- Choisir le type --</option>
                            <option value="hebdomadaire" {{ old('type_devoir') == 'hebdomadaire' ? 'selected' : '' }}>📅 Devoir hebdomadaire (min. 2 questions)</option>
                            <option value="mensuel" {{ old('type_devoir') == 'mensuel' ? 'selected' : '' }}>📆 Devoir mensuel (min. 8 questions)</option>
                            <option value="final" {{ old('type_devoir') == 'final' ? 'selected' : '' }}>🎯 Devoir final (min. 66 questions)</option>
                        </select>
                        @error('type_devoir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="semaine" class="form-label">
                            <i class="fas fa-calendar-week me-2 text-success"></i>Semaine <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('semaine') is-invalid @enderror" id="semaine" name="semaine" required>
                            <option value="">-- Choisir la semaine --</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('semaine') == $i ? 'selected' : '' }}>Semaine {{ $i }}</option>
                            @endfor
                        </select>
                        @error('semaine')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row fade-in">
                    <div class="col-md-6 mb-4">
                        <label for="minutes" class="form-label">
                            <i class="fas fa-clock me-2 text-success"></i>Temps limite (minutes) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-stopwatch"></i></span>
                            <input type="number" class="form-control @error('minutes') is-invalid @enderror" id="minutes" name="minutes" value="{{ old('minutes') }}" min="1" max="180" required>
                        </div>
                        @error('minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="form-text text-muted">Durée recommandée selon le type : Hebdomadaire (15-30 min), Mensuel (45-60 min), Final (90-120 min)</small>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="alert alert-info h-100 d-flex align-items-center">
                            <div>
                                <i class="fas fa-info-circle me-2 fa-lg"></i>
                                <strong>Règles par type :</strong><br>
                                <div class="mt-2">
                                    <span class="badge bg-success me-1">Hebdomadaire</span> 2-10 questions, 15-30 min<br>
                                    <span class="badge bg-primary me-1">Mensuel</span> 8-20 questions, 45-60 min<br>
                                    <span class="badge bg-warning me-1">Final</span> 66+ questions, 90-120 min
                                </div>
                            </div>
                        </div>
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
                                    <i class="fas fa-calendar-alt me-1"></i>Session de formation <span class="text-danger">*</span>
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
                        <div class="text-center mt-2">
                            <button type="button" class="btn btn-success btn-sm" id="confirmProgrammationBtn" onclick="confirmProgrammation()">
                                <i class="fas fa-check-circle me-1"></i>OK - Confirmer la date et l'heure d'envoi
                            </button>
                            <div id="programmationStatus" class="mt-2" style="display: none;">
                                <div class="alert alert-success py-2 px-3" id="programmationSuccess" style="display: none;">
                                    <i class="fas fa-check-circle me-1"></i>Date et heure confirmées avec succès !
                                </div>
                                <div class="alert alert-danger py-2 px-3" id="programmationError" style="display: none;">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Veuillez sélectionner une date et une heure valides.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row fade-in">
                    <div class="col-md-6 mb-4">
                        <label for="niveau_id" class="form-label">
                            <i class="fas fa-layer-group me-2 text-success"></i>Niveau concerné <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('niveau_id') is-invalid @enderror" id="niveau_id" name="niveau_id" required>
                            <option value="">-- Choisir un niveau --</option>
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id }}" {{ old('niveau_id') == $niveau->id ? 'selected' : '' }}>🎓 {{ $niveau->nom }}</option>
                            @endforeach
                        </select>
                        @error('niveau_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="module_id" class="form-label">
                            <i class="fas fa-book me-2 text-success"></i>Module concerné <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('module_id') is-invalid @enderror" id="module_id" name="module_id" required>
                            <option value="">-- Choisir un module --</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->id }}" data-niveau="{{ $module->niveau_id }}" style="display:none;">
                                    📚 {{ $module->titre }} @if($module->niveau) (Niveau : {{ $module->niveau->nom }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('module_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Section d'importation -->
                <div class="import-section p-4 mb-4 fade-in">
                    <div class="text-center mb-3">
                        <i class="fas fa-file-import fa-3x text-success mb-3"></i>
                        <h5 class="text-success"><i class="fas fa-cloud-upload-alt me-2"></i>Importer des questions depuis un fichier</h5>
                    </div>
                    <div class="mb-3">
                        <label for="questionFile" class="form-label">Fichier de questions (JSON, CSV ou TXT)</label>
                        <input type="file" class="form-control" id="questionFile" accept=".json,.csv,.txt">
                        <div class="mt-2">
                            <small class="text-muted">
                                <strong>Formats acceptés :</strong><br>
                                <code class="bg-light p-1 rounded">JSON:</code> [{"texte":"Question?", "choix":["option1","option2"], "bonne_reponse":"option1", "points":1}]<br>
                                <code class="bg-light p-1 rounded">TXT:</code> Question?|option1;option2;option3|bonne_reponse|points<br>
                                <strong>💡 L'importation se fait automatiquement dès qu'un fichier est sélectionné !</strong>
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-info" id="importQuestionsBtn" style="display: none;">
                        <i class="fas fa-file-import me-2"></i>Importer les questions (optionnel)
                    </button>
                </div>

                <!-- Container pour les questions -->
                <div id="questionsContainer" class="mb-4"></div>

                <div class="text-center mb-4">
                    <button type="button" class="btn btn-secondary btn-lg" id="addQuestionBtn">
                        <i class="fas fa-plus-circle me-2"></i>Ajouter une question
                    </button>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Enregistrer le questionnaire
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Création des particules flottantes
    function createParticles() {
        const particles = document.getElementById('particles');
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle');
            particle.style.left = Math.random() * 100 + '%';
            particle.style.width = particle.style.height = Math.random() * 10 + 5 + 'px';
            particle.style.animationDelay = Math.random() * 15 + 's';
            particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
            particles.appendChild(particle);
        }
    }

    // Animation des éléments au scroll
    function animateOnScroll() {
        const elements = document.querySelectorAll('.fade-in');
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            if (elementTop < windowHeight - 100) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    }

    // Gestion dynamique des modules selon le niveau
    const niveauSelect = document.getElementById('niveau_id');
    const moduleSelect = document.getElementById('module_id');
    
    if(niveauSelect && moduleSelect) {
        niveauSelect.addEventListener('change', function() {
            const niveauId = this.value;
            Array.from(moduleSelect.options).forEach(opt => {
                if (!opt.value) {
                    opt.style.display = '';
                    return;
                }
                opt.style.display = (opt.getAttribute('data-niveau') === niveauId) ? '' : 'none';
            });
            moduleSelect.value = '';
            
            // Animation de sélection
            moduleSelect.style.transform = 'scale(1.05)';
            setTimeout(() => {
                moduleSelect.style.transform = 'scale(1)';
            }, 200);
        });
    }

    // Gestion des questions dynamiques
    let questionIndex = 0;

    function addQuestion(q = null) {
        const container = document.getElementById('questionsContainer');
        const div = document.createElement('div');
        div.className = 'question-card card mb-3';
        div.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-success mb-0">
                        <i class="fas fa-question-circle me-2"></i>Question ${questionIndex + 1}
                    </h6>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion(this)">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-edit me-2 text-success"></i>Texte de la question
                    </label>
                    <input type="text" name="questions[${questionIndex}][texte]" class="form-control" value="${q ? q.texte : ''}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-list me-2 text-success"></i>Choix (séparés par un point-virgule ;)
                    </label>
                    <input type="text" name="questions[${questionIndex}][choix]" class="form-control" value="${q ? (Array.isArray(q.choix) ? q.choix.join(';') : q.choix) : ''}" required>
                    <small class="text-muted">Exemple: Option 1;Option 2;Option 3;Option 4</small>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-check-circle me-2 text-success"></i>Bonne réponse
                            </label>
                            <input type="text" name="questions[${questionIndex}][bonne_reponse]" class="form-control" value="${q ? q.bonne_reponse : ''}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-star me-2 text-warning"></i>Points
                            </label>
                            <input type="number" name="questions[${questionIndex}][points]" class="form-control" value="${q ? q.points : 1}" min="1" max="100" required>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(div);
        questionIndex++;
        
        // Animation d'apparition
        div.style.opacity = '0';
        div.style.transform = 'translateY(30px)';
        setTimeout(() => {
            div.style.opacity = '1';
            div.style.transform = 'translateY(0)';
        }, 100);
    }

    function removeQuestion(button) {
        const card = button.closest('.question-card');
        card.style.transform = 'translateX(100%)';
        card.style.opacity = '0';
        setTimeout(() => {
            card.remove();
        }, 300);
    }

    // Importation de fichiers
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

    // Event listeners
    document.getElementById('addQuestionBtn').onclick = () => addQuestion();

    // Importation automatique dès qu'un fichier est sélectionné
    document.getElementById('questionFile').addEventListener('change', function() {
        console.log('Fichier sélectionné automatiquement');
        if (this.files.length > 0) {
            console.log('Fichier détecté:', this.files[0].name);
            
            const loadingSpinner = document.querySelector('.loading-spinner');
            if (loadingSpinner) {
                loadingSpinner.style.display = 'block';
            }
            
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
                        addQuestion(q);
                    }
                    if (loadingSpinner) {
                        loadingSpinner.style.display = 'none';
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

    document.getElementById('importQuestionsBtn').onclick = function() {
        console.log('Bouton import cliqué');
        const fileInput = document.getElementById('questionFile');
        console.log('File input trouvé:', fileInput);
        
        if (!fileInput) {
            console.error('File input non trouvé');
            alert('Erreur: Élément de fichier non trouvé');
            return;
        }
        
        if (!fileInput.files.length) {
            console.log('Aucun fichier sélectionné');
            alert('Veuillez sélectionner un fichier.');
            return;
        }
        
        console.log('Fichier sélectionné:', fileInput.files[0].name);
        
        const loadingSpinner = document.querySelector('.loading-spinner');
        if (loadingSpinner) {
            loadingSpinner.style.display = 'block';
        }
        
        parseQuestionsFile(fileInput.files[0], function(questions) {
            console.log('Callback appelé avec', questions.length, 'questions');
            setTimeout(() => {
                for (const q of questions) {
                    console.log('Ajout de question:', q);
                    addQuestion(q);
                }
                if (loadingSpinner) {
                    loadingSpinner.style.display = 'none';
                }
                alert(`${questions.length} questions importées avec succès !`);
            }, 1000);
        });
    };

    // Validation du formulaire
    document.getElementById('questionnaireForm').addEventListener('submit', function(e) {
        const questions = document.querySelectorAll('.question-card');
        if (questions.length === 0) {
            e.preventDefault();
            alert('Veuillez ajouter au moins une question.');
            return;
        }
        
        document.querySelector('.loading-spinner').style.display = 'block';
    });

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        createParticles();
        
        // Animation des éléments fade-in
        const fadeElements = document.querySelectorAll('.fade-in');
        fadeElements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';
            element.style.transition = 'all 0.6s ease';
            
            setTimeout(() => {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, index * 200);
        });
        
        window.addEventListener('scroll', animateOnScroll);

        // Gérer l'affichage du bouton d'importation
        const questionFileInput = document.getElementById('questionFile');
        const importQuestionsBtn = document.getElementById('importQuestionsBtn');

        if (questionFileInput) {
            questionFileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    importQuestionsBtn.style.display = 'block';
                } else {
                    importQuestionsBtn.style.display = 'none';
                }
            });
        }
    });

    // Effets hover sur les inputs
    document.querySelectorAll('.form-control, .form-select').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateY(-2px)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'translateY(0)';
        });
    });
</script>
@endsection 