@extends('assistants.layout')

@section('content')
<style>
    /* Styles spécifiques pour la page niveaux - Design sombre */
    .page-header {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        animation: slideInDown 0.8s ease-out;
        position: relative;
        overflow: hidden;
        color: var(--text-light);
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--accent-green), var(--light-green));
        animation: shimmer 2s ease-in-out infinite;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .btn-create {
        background: linear-gradient(135deg, var(--accent-green) 0%, var(--light-green) 100%);
        color: var(--text-light);
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-create::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }

    .btn-create:hover::before {
        left: 100%;
    }

    .btn-create:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 10px 25px rgba(127, 176, 105, 0.4);
    }

    .niveau-card {
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

    .niveau-card::before {
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

    .niveau-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(127, 176, 105, 0.2);
        background: rgba(255, 255, 255, 0.15);
    }

    .niveau-header {
        background: linear-gradient(135deg, rgba(127, 176, 105, 0.2) 0%, rgba(167, 201, 87, 0.2) 100%);
        padding: 1.5rem;
        border-radius: 20px 20px 0 0;
        border-bottom: 1px solid rgba(127, 176, 105, 0.3);
    }

    .niveau-body {
        padding: 1.5rem;
    }

    .niveau-title {
        color: var(--accent-green);
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
    }

    .niveau-description {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .niveau-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(127, 176, 105, 0.2);
    }

    .niveau-ordre {
        background: linear-gradient(135deg, var(--accent-green), var(--light-green));
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .niveau-status {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .status-active {
        background: linear-gradient(135deg, var(--accent-green), var(--light-green));
        color: white;
    }

    .status-inactive {
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
        .page-header {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .niveau-card {
            margin-bottom: 1rem;
        }
        
        .niveau-meta {
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-start;
        }
    }
</style>

<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="page-header animate-on-scroll">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-2" style="color: var(--accent-green);">
                    <i class="fas fa-layer-group me-2"></i>Gestion des Niveaux
                </h1>
                <p class="text-muted mb-0">Gérez les niveaux de formation disponibles</p>
            </div>
            <a href="{{ route('assistant.niveaux.create') }}" class="btn btn-create">
                <i class="fas fa-plus me-2"></i> Nouveau niveau
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
    @if($niveaux->isEmpty())
        <div class="empty-state animate-on-scroll">
            <i class="fas fa-layer-group empty-icon"></i>
            <h3 style="color: var(--accent-green);">Aucun niveau trouvé</h3>
            <p class="text-muted mb-4">Commencez par créer votre premier niveau de formation.</p>
            <a href="{{ route('assistant.niveaux.create') }}" class="btn btn-create">
                <i class="fas fa-plus me-2"></i> Créer un niveau
            </a>
        </div>
    @else
        <div class="row">
            @foreach($niveaux as $niveau)
                <div class="col-lg-6 col-xl-4 animate-on-scroll">
                    <div class="niveau-card">
                        <div class="niveau-header">
                            <h3 class="niveau-title">
                                <i class="fas fa-layer-group me-2"></i>{{ $niveau->nom }}
                            </h3>
                        </div>
                        <div class="niveau-body">
                            <p class="niveau-description">
                                {{ $niveau->description ?: 'Aucune description disponible' }}
                            </p>
                            <div class="niveau-meta">
                                <span class="niveau-ordre">
                                    <i class="fas fa-sort-numeric-up me-1"></i>Ordre: {{ $niveau->ordre }}
                                </span>
                                <span class="niveau-status {{ $niveau->actif ? 'status-active' : 'status-inactive' }}">
                                    <i class="fas {{ $niveau->actif ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                    {{ $niveau->actif ? 'Actif' : 'Inactif' }}
                                </span>
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
    document.querySelectorAll('.niveau-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Animation des boutons
    document.querySelectorAll('.btn-create').forEach(btn => {
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