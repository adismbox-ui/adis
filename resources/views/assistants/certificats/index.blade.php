@extends('assistants.layout')

@section('content')
<style>
    /* Styles spécifiques pour la page certificats - Design sombre */
    .certificat-card {
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

    .certificat-card::before {
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

    .certificat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(127, 176, 105, 0.2);
        background: rgba(255, 255, 255, 0.15);
    }

    .certificat-header {
        background: linear-gradient(135deg, rgba(127, 176, 105, 0.2) 0%, rgba(167, 201, 87, 0.2) 100%);
        padding: 1.5rem;
        border-radius: 20px 20px 0 0;
        border-bottom: 1px solid rgba(127, 176, 105, 0.3);
    }

    .certificat-body {
        padding: 1.5rem;
    }

    .certificat-title {
        color: var(--accent-green);
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
    }

    .certificat-info {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .certificat-icon {
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

    .certificat-icon:hover {
        transform: scale(1.1) rotate(10deg);
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
        .certificat-card {
            margin-bottom: 1rem;
        }
        
        .certificat-icon {
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
                    <i class="fas fa-certificate me-2"></i>Gestion des Certificats
                </h1>
                <p class="text-muted mb-0">Consultez et gérez tous les certificats</p>
            </div>
            <a href="{{ route('assistant.certificats.create') }}" class="btn btn-gradient">
                <i class="fas fa-plus me-2"></i> Nouveau certificat
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
    @if($certificats->isEmpty())
        <div class="empty-state animate-on-scroll">
            <i class="fas fa-certificate empty-icon"></i>
            <h3 style="color: var(--accent-green);">Aucun certificat trouvé</h3>
            <p class="text-muted mb-4">Aucun certificat n'a été trouvé dans le système.</p>
            <a href="{{ route('assistant.certificats.create') }}" class="btn btn-gradient">
                <i class="fas fa-plus me-2"></i> Créer un certificat
            </a>
        </div>
    @else
        <div class="row">
            @foreach($certificats as $certificat)
                <div class="col-lg-6 col-xl-4 animate-on-scroll">
                    <div class="certificat-card">
                        <div class="certificat-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="certificat-title">
                                        <i class="fas fa-certificate me-2"></i>{{ $certificat->titre }}
                                    </h4>
                                    <p class="certificat-info">
                                        {{ $certificat->created_at ? $certificat->created_at->format('d/m/Y H:i') : '-' }}
                                    </p>
                                </div>
                                <div class="certificat-icon">
                                    <i class="fas fa-certificate"></i>
                                </div>
                            </div>
                        </div>
                        <div class="certificat-body">
                            <!-- Informations du certificat -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="info-box">
                                        <div class="info-label">Date</div>
                                        <div class="info-value">
                                            {{ $certificat->created_at ? $certificat->created_at->format('d/m/Y') : '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-box">
                                        <div class="info-label">Heure</div>
                                        <div class="info-value">
                                            {{ $certificat->created_at ? $certificat->created_at->format('H:i') : '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Statut -->
                            <div class="mb-3">
                                <strong style="color: var(--accent-green);">Statut :</strong>
                                <div class="mt-2">
                                    <span class="badge-custom badge-success">
                                        <i class="fas fa-check-circle me-1"></i>Validé
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="action-buttons">
                                    <a href="{{ route('assistant.certificats.show', $certificat->id) }}" class="btn btn-action btn-view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('assistant.certificats.edit', $certificat->id) }}" class="btn btn-action btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $certificat->created_at->format('d/m/Y') }}
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
    document.querySelectorAll('.certificat-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Animation des icônes de certificat
    document.querySelectorAll('.certificat-icon').forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(10deg)';
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
});
</script>
@endsection 