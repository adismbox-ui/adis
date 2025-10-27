@extends('assistants.layout')

@section('content')
<style>
    /* Styles spécifiques pour la page questionnaires - Design sombre */
    .questionnaire-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
        animation: slideInUp 0.8s ease-out;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
        color: var(--text-light);
    }

    .questionnaire-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--accent-green), var(--light-green));
        animation: shimmer 3s ease-in-out infinite;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .questionnaire-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(127, 176, 105, 0.2);
        background: rgba(255, 255, 255, 0.15);
    }

    .questionnaire-header {
        background: linear-gradient(135deg, rgba(127, 176, 105, 0.2) 0%, rgba(167, 201, 87, 0.2) 100%);
        padding: 1.5rem;
        border-radius: 20px 20px 0 0;
        border-bottom: 1px solid rgba(127, 176, 105, 0.3);
    }

    .questionnaire-body {
        padding: 1.5rem;
    }

    .questionnaire-title {
        color: var(--accent-green);
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
    }

    .questionnaire-info {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .questionnaire-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent-green), var(--light-green));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        border: 3px solid rgba(127, 176, 105, 0.5);
        transition: all 0.3s ease;
        animation: pulse 2s infinite;
    }

    .questionnaire-icon:hover {
        transform: scale(1.1) rotate(5deg);
        border-color: var(--accent-green);
    }

    .info-box {
        background: rgba(127, 176, 105, 0.1);
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        border: 1px solid rgba(127, 176, 105, 0.2);
    }

    .info-label {
        color: var(--accent-green);
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .info-value {
        color: var(--text-light);
        font-size: 0.95rem;
    }

    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }

    .badge-custom:hover {
        transform: translateY(-2px) scale(1.05);
    }

    .badge-success {
        background: linear-gradient(135deg, var(--accent-green), var(--light-green));
        color: white;
    }

    .badge-warning {
        background: linear-gradient(135deg, #f39c12, #e67e22);
        color: white;
    }

    .badge-danger {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
    }

    .badge-info {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
    }

    .badge-primary {
        background: linear-gradient(135deg, #9b59b6, #8e44ad);
        color: white;
    }

    /* Bouton supprimer */
    .btn-action.btn-delete {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: #fff;
        border: none;
    }

    .search-box {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        padding: 1rem;
        margin-bottom: 2rem;
        color: var(--text-light);
    }

    .search-input {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        color: var(--text-light);
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }

    .search-input:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: var(--accent-green);
        color: var(--text-light);
        box-shadow: 0 0 0 0.2rem rgba(127, 176, 105, 0.25);
    }

    .search-input::placeholder {
        color: var(--text-muted);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        animation: slideInUp 0.8s ease-out;
        color: var(--text-light);
    }

    .empty-icon {
        font-size: 4rem;
        color: var(--accent-green);
        margin-bottom: 1rem;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .alert {
        border-radius: 15px;
        border: none;
        animation: fadeInSlide 0.8s ease-out;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        color: var(--text-light);
    }

    @keyframes fadeInSlide {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .questionnaire-card {
            margin-bottom: 1rem;
        }
        
        .questionnaire-icon {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }
    }
</style>

<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="page-header animate-on-scroll">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-2" style="color: var(--accent-green);">
                    <i class="fas fa-question-circle me-2"></i>Gestion des Questionnaires
                </h1>
                <p class="text-muted mb-0">Consultez et gérez tous les questionnaires</p>
            </div>
            <a href="{{ route('assistant.questionnaires.create') }}" class="btn btn-gradient">
                <i class="fas fa-plus me-2"></i> Nouveau questionnaire
            </a>
        </div>
    </div>

    <!-- Messages de succès -->
    @if(session('success'))
        <div class="alert alert-success animate-on-scroll">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Barre de recherche -->
    <div class="search-box animate-on-scroll">
        <input type="text" id="searchInput" class="form-control search-input" placeholder="Rechercher par titre, module ou type...">
    </div>

    <!-- Contenu principal -->
        @if($questionnaires->isEmpty())
        <div class="empty-state animate-on-scroll">
            <i class="fas fa-question-circle empty-icon"></i>
            <h3 style="color: var(--accent-green);">Aucun questionnaire trouvé</h3>
            <p class="text-muted mb-4">Aucun questionnaire n'a été trouvé dans le système.</p>
            <a href="{{ route('assistant.questionnaires.create') }}" class="btn btn-gradient">
                <i class="fas fa-plus me-2"></i> Créer un questionnaire
            </a>
        </div>
        @else
        <div class="row" id="questionnaireContainer">
                    @foreach($questionnaires as $questionnaire)
                <div class="col-lg-6 col-xl-4 animate-on-scroll questionnaire-item">
                    <div class="questionnaire-card">
                        <div class="questionnaire-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="questionnaire-title">
                                        <i class="fas fa-question-circle me-2"></i>{{ $questionnaire->titre ?? '(Sans titre)' }}
                                    </h4>
                                    <p class="questionnaire-info">
                                        ID: {{ $questionnaire->id }} | {{ $questionnaire->created_at ? $questionnaire->created_at->format('d/m/Y H:i') : '-' }}
                                    </p>
                                </div>
                                <div class="questionnaire-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="questionnaire-body">
                            <!-- Type de questionnaire -->
                            <div class="mb-3">
                                <strong style="color: var(--accent-green);">Type :</strong>
                                <div class="mt-2">
                            @switch($questionnaire->type_devoir)
                                @case('hebdomadaire')
                                            <span class="badge-custom badge-warning">
                                        <i class="fas fa-calendar-week me-1"></i> Hebdomadaire
                                    </span>
                                    @break
                                @case('mensuel')
                                            <span class="badge-custom badge-info">
                                        <i class="fas fa-calendar-alt me-1"></i> Mensuel
                                    </span>
                                    @break
                                @case('final')
                                            <span class="badge-custom badge-danger">
                                        <i class="fas fa-trophy me-1"></i> Final
                                    </span>
                                    @break
                                @default
                                            <span class="badge-custom badge-secondary">Non défini</span>
                            @endswitch
                                </div>
                            </div>

                            <!-- Informations du questionnaire -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="info-box">
                                        <div class="info-label">Semaine</div>
                                        <div class="info-value">
                                            {{ $questionnaire->semaine ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-box">
                                        <div class="info-label">Temps</div>
                                        <div class="info-value">
                                            {{ $questionnaire->minutes ?? '-' }} min
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Module et Niveau -->
                            <div class="mb-3">
                                <strong style="color: var(--accent-green);">Module :</strong>
                                <div class="mt-2">
                                    <span class="badge-custom badge-primary">
                                        <i class="fas fa-book me-1"></i>{{ $questionnaire->module->titre ?? '-' }}
                            </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong style="color: var(--accent-green);">Niveau :</strong>
                                <div class="mt-2">
                                    <span class="badge-custom badge-info">
                                        <i class="fas fa-layer-group me-1"></i>{{ $questionnaire->module->niveau->nom ?? '-' }}
                            </span>
                                </div>
                            </div>

                            <!-- Nombre de questions -->
                            <div class="mb-3">
                                <strong style="color: var(--accent-green);">Questions :</strong>
                                <div class="mt-2">
                                    <span class="badge-custom badge-success">
                                <i class="fas fa-list me-1"></i> {{ $questionnaire->questions->count() }}
                            </span>
                                </div>
                            </div>

                            <!-- Statut d'envoi -->
                            <div class="mb-3">
                                <strong style="color: var(--accent-green);">Statut :</strong>
                                <div class="mt-2">
                                    @if($questionnaire->envoye)
                                        <span class="badge-custom badge-success">
                                            <i class="fas fa-paper-plane me-1"></i> Envoyé
                                        </span>
                                    @else
                                        <span class="badge-custom badge-warning">
                                            <i class="fas fa-clock me-1"></i> En attente
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="action-buttons">
                                    <a href="{{ route('assistant.questionnaires.show', $questionnaire) }}" class="btn btn-action btn-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('assistant.questionnaires.edit', $questionnaire) }}" class="btn btn-action btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('assistant.questionnaires.destroy', $questionnaire) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce questionnaire ? Cette action est irréversible.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-action btn-delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $questionnaire->created_at->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                    @endforeach
        </div>
        @endif
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });

    // Animation des cartes au hover
    document.querySelectorAll('.questionnaire-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Animation des icônes de questionnaire
    document.querySelectorAll('.questionnaire-icon').forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(5deg)';
        });
        
        icon.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });
    });

    // Animation des badges
    document.querySelectorAll('.badge-custom').forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.05)';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Animation des boutons
    document.querySelectorAll('.btn-gradient').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.02)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

// Filtrage instantané
const searchInput = document.getElementById('searchInput');
    const questionnaireItems = document.querySelectorAll('.questionnaire-item');
    
    if(searchInput && questionnaireItems.length > 0) {
    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
            questionnaireItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if(text.includes(filter)) {
                    item.style.display = 'block';
                    item.style.animation = 'slideInUp 0.5s ease-out';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection 