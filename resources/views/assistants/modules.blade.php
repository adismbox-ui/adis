@extends('assistants.layout')

@section('content')
<style>
    /* Styles spécifiques pour la page modules - Design sombre */
    .module-card {
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

    .module-card::before {
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

    .module-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(127, 176, 105, 0.2);
        background: rgba(255, 255, 255, 0.15);
    }

    .module-header {
        background: linear-gradient(135deg, rgba(127, 176, 105, 0.2) 0%, rgba(167, 201, 87, 0.2) 100%);
        padding: 1.5rem;
        border-radius: 20px 20px 0 0;
        border-bottom: 1px solid rgba(127, 176, 105, 0.3);
    }

    .module-body {
        padding: 1.5rem;
    }

    .module-title {
        color: var(--accent-green);
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
    }

    .module-description {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.5;
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
        background: linear-gradient(135deg, var(--accent-green), var(--light-green));
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent-green), var(--light-green));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1rem;
        border: 2px solid rgba(127, 176, 105, 0.5);
        transition: all 0.3s ease;
    }

    .avatar:hover {
        transform: scale(1.1);
        border-color: var(--accent-green);
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
        .module-card {
            margin-bottom: 1rem;
        }
        
        .info-box {
            margin-bottom: 1rem;
        }
    }
</style>

<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="page-header animate-on-scroll">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-2" style="color: var(--accent-green);">
                    <i class="fas fa-book me-2"></i>Gestion des Modules
                </h1>
                <p class="text-muted mb-0">Consultez et gérez tous les modules de formation</p>
            </div>
            <a href="{{ route('assistant.modules.create') }}" class="btn btn-gradient">
                <i class="fas fa-plus me-2"></i> Nouveau module
            </a>
        </div>
    </div>

    <!-- Messages de succès -->
    @if(session('success'))
        <div class="alert alert-success animate-on-scroll">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Contenu principal -->
    @if($modules->isEmpty())
        <div class="empty-state animate-on-scroll">
            <i class="fas fa-book empty-icon"></i>
            <h3 style="color: var(--accent-green);">Aucun module trouvé</h3>
            <p class="text-muted mb-4">Commencez par créer votre premier module de formation.</p>
            <a href="{{ route('assistant.modules.create') }}" class="btn btn-gradient">
                <i class="fas fa-plus me-2"></i> Créer un module
            </a>
        </div>
    @else
        <div class="row">
            @foreach($modules as $module)
                <div class="col-lg-6 col-xl-4 animate-on-scroll">
                    <div class="module-card">
                        <div class="module-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="module-title">
                                        <i class="fas fa-book me-2"></i>{{ $module->titre ?? 'Sans titre' }}
                                    </h4>
                                    @if($module->niveau)
                                        <span class="badge-custom">
                                            <i class="fas fa-layer-group me-1"></i>{{ $module->niveau->nom }}
                                        </span>
                                    @endif
                                </div>
                                <span class="badge-custom">{{ $module->id }}</span>
                            </div>
                        </div>
                        <div class="module-body">
                            <!-- Informations du module -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="info-box">
                                        <div class="info-label">Début</div>
                                        <div class="info-value">
                                            {{ $module->date_debut ?? 'Non défini' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-box">
                                        <div class="info-label">Fin</div>
                                        <div class="info-value">
                                            {{ $module->date_fin ?? 'Non défini' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Formateur -->
                            <div class="mb-3">
                                <strong style="color: var(--accent-green);">Formateur :</strong>
                                @if($module->formateur && $module->formateur->utilisateur)
                                    <div class="d-flex align-items-center mt-2">
                                        <div class="avatar me-3">
                                            {{ substr($module->formateur->utilisateur->prenom ?? '', 0, 1) }}{{ substr($module->formateur->utilisateur->nom ?? '', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">
                                                {{ $module->formateur->utilisateur->prenom ?? '' }} {{ $module->formateur->utilisateur->nom ?? '' }}
                                            </div>
                                            <small class="text-muted">{{ $module->formateur->utilisateur->email ?? '' }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Aucun formateur assigné</span>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="action-buttons">
                                    <a href="{{ route('assistant.modules.show', $module->id) }}" class="btn btn-action btn-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('assistant.modules.edit', $module->id) }}" class="btn btn-action btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $module->created_at->format('d/m/Y') }}
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
    document.querySelectorAll('.module-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
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

    // Animation des avatars
    document.querySelectorAll('.avatar').forEach(avatar => {
        avatar.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });
        
        avatar.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
@endsection 