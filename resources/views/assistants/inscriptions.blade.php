@extends('assistants.layout')

@section('content')
<style>
    /* Styles spécifiques pour la page inscriptions - Design sombre */
    .inscription-card {
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

    .inscription-card::before {
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

    .inscription-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(127, 176, 105, 0.2);
        background: rgba(255, 255, 255, 0.15);
    }

    .inscription-header {
        background: linear-gradient(135deg, rgba(127, 176, 105, 0.2) 0%, rgba(167, 201, 87, 0.2) 100%);
        padding: 1.5rem;
        border-radius: 20px 20px 0 0;
        border-bottom: 1px solid rgba(127, 176, 105, 0.3);
    }

    .inscription-body {
        padding: 1.5rem;
    }

    .inscription-title {
        color: var(--accent-green);
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
    }

    .inscription-info {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent-green), var(--light-green));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
        border: 3px solid rgba(127, 176, 105, 0.5);
        transition: all 0.3s ease;
        animation: pulse 2s infinite;
    }

    .avatar:hover {
        transform: scale(1.1);
        border-color: var(--accent-green);
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
        .inscription-card {
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
                    <i class="fas fa-clipboard-list me-2"></i>Liste des inscriptions
                </h1>
                <p class="text-muted mb-0">Gérez toutes les inscriptions aux modules</p>
            </div>
        </div>
    </div>

    <!-- Messages de succès -->
    @if(session('success'))
        <div class="alert alert-success animate-on-scroll">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Contenu principal -->
    @if($inscriptions->isEmpty())
        <div class="empty-state animate-on-scroll">
            <i class="fas fa-clipboard-list empty-icon"></i>
            <h3 style="color: var(--accent-green);">Aucune inscription trouvée</h3>
            <p class="text-muted mb-4">Aucune inscription n'a été trouvée dans le système.</p>
        </div>
    @else
        <div class="row">
            @foreach($inscriptions as $inscription)
                <div class="col-lg-6 col-xl-4 animate-on-scroll">
                    <div class="inscription-card">
                        <div class="inscription-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="inscription-title">
                                        <i class="fas fa-user-plus me-2"></i>Inscription #{{ $inscription->id }}
                                    </h4>
                                    <p class="inscription-info">
                                        {{ $inscription->created_at ? $inscription->created_at->format('d/m/Y H:i') : '-' }}
                                    </p>
                                </div>
                                <span class="badge-custom {{ $inscription->statut == 'valide' ? 'badge-success' : ($inscription->statut == 'en_attente' ? 'badge-warning' : 'badge-danger') }}">
                                    @if($inscription->statut == 'valide')
                                        <i class="fas fa-check-circle me-1"></i>Validé
                                    @elseif($inscription->statut == 'en_attente')
                                        <i class="fas fa-clock me-1"></i>En attente
                                    @else
                                        <i class="fas fa-times-circle me-1"></i>Refusé
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="inscription-body">
                            <!-- Apprenant -->
                            <div class="mb-3">
                                <strong style="color: var(--accent-green);">Apprenant :</strong>
                                <div class="d-flex align-items-center mt-2">
                                    <div class="avatar me-3">
                                        {{ substr($inscription->apprenant->utilisateur->prenom ?? 'A', 0, 1) }}{{ substr($inscription->apprenant->utilisateur->nom ?? 'P', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">
                                            {{ $inscription->apprenant->utilisateur->prenom ?? '-' }} {{ $inscription->apprenant->utilisateur->nom ?? '' }}
                                        </div>
                                        <small class="text-muted">{{ $inscription->apprenant->utilisateur->email ?? '' }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Module -->
                            <div class="mb-3">
                                <strong style="color: var(--accent-green);">Module :</strong>
                                <div class="mt-2">
                                    <span class="badge-custom badge-success">
                                        <i class="fas fa-book me-1"></i>{{ $inscription->module->titre ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="action-buttons">
                                    <a href="{{ route('assistant.inscriptions.show', $inscription->id) }}" class="btn btn-action btn-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('assistant.inscriptions.edit', $inscription->id) }}" class="btn btn-action btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $inscription->created_at->format('d/m/Y') }}
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
    document.querySelectorAll('.inscription-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
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

    // Animation des badges
    document.querySelectorAll('.badge-custom').forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.05)';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
</script>
@endsection 