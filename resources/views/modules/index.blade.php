@extends('formateurs.layout')

@section('content')
<style>
    /* Variables CSS pour la cohérence des couleurs */
    :root {
        --primary-green: #1a4d3a;
        --secondary-green: #2d6e4e;
        --accent-green: #3d8b64;
        --light-green: #4da674;
        --bg-green: #0f2a1f;
        --text-light: #e8f5e8;
        --text-muted: #b8d4c2;
        --shadow-dark: rgba(15, 42, 31, 0.3);
        --glow-green: rgba(77, 166, 116, 0.6);
    }

    /* Styles de base avec image de fond */
    body {
        background: linear-gradient(135deg, #0f2a1f 0%, #1a4d3a 50%, #2d6e4e 100%);
        background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-blend-mode: multiply;
        min-height: 100vh;
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }

    /* Overlay sombre pour le contenu */
    .container::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(15, 42, 31, 0.9) 0%, rgba(26, 77, 58, 0.8) 50%, rgba(45, 110, 78, 0.7) 100%);
        z-index: -1;
    }

    /* Animations globales */
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

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    /* Carte principale */
    .main-card {
        background: rgba(15, 42, 31, 0.9);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(77, 166, 116, 0.3);
        border-radius: 20px;
        overflow: hidden;
        animation: fadeInUp 0.8s ease-out;
        box-shadow: 0 20px 60px var(--shadow-dark);
        transition: all 0.3s ease;
        position: relative;
    }

    .main-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(77, 166, 116, 0.05), transparent);
        animation: shine 8s ease-in-out infinite;
    }

    @keyframes shine {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    .main-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 80px var(--shadow-dark);
    }

    /* En-tête de page */
    .page-header {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        transform: rotate(45deg);
        animation: shine 3s ease-in-out infinite;
    }

    .page-title {
        color: var(--text-light);
        font-size: 2.5rem;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 2;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .page-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--accent-green), var(--light-green));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulse 2s infinite;
        box-shadow: 0 4px 15px var(--glow-green);
    }

    /* Tableau moderne */
    .table-modern {
        background: rgba(15, 42, 31, 0.6);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: inset 0 0 20px rgba(0,0,0,0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(77, 166, 116, 0.3);
    }

    .table-modern th {
        background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        color: var(--text-light);
        font-weight: 600;
        padding: 1.2rem 1rem;
        border: none;
        position: relative;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-modern th::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .table-modern th:hover::before {
        opacity: 1;
    }

    .table-modern td {
        background: rgba(26, 77, 58, 0.8);
        color: var(--text-light);
        padding: 1rem;
        border: 1px solid rgba(77, 166, 116, 0.2);
        transition: all 0.3s ease;
        vertical-align: middle;
    }

    .table-modern tbody tr {
        transition: all 0.3s ease;
    }

    .table-modern tbody tr:hover {
        background: rgba(45, 110, 78, 0.9);
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(15, 42, 31, 0.3);
    }

    .table-modern tbody tr:hover td {
        border-color: var(--light-green);
    }

    /* Badges et statuts */
    .badge-modern {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        margin: 0.2rem;
        animation: pulse 2s infinite;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .badge-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }

    .badge-modern:hover::before {
        left: 100%;
    }

    .badge-success {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white;
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
    }

    .badge-info {
        background: linear-gradient(135deg, var(--light-green), var(--accent-green));
        color: white;
        box-shadow: 0 4px 15px var(--glow-green);
    }

    .badge-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .badge-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }

    /* Boutons modernes */
    .btn-modern {
        padding: 0.6rem 1.2rem;
        border-radius: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: none;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .btn-modern::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.3s ease, height 0.3s ease;
    }

    .btn-modern:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-info-modern {
        background: linear-gradient(135deg, var(--light-green), var(--accent-green));
        color: white;
        box-shadow: 0 4px 15px var(--glow-green);
    }

    .btn-info-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px var(--glow-green);
    }

    /* Alertes modernes */
    .alert-modern {
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 10px;
        color: #22c55e;
        padding: 1.5rem;
        margin: 1rem 0;
        animation: fadeInUp 0.6s ease-out;
        backdrop-filter: blur(10px);
    }

    /* Animation d'apparition progressive */
    .animate-on-scroll {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease-out;
    }

    .animate-on-scroll.visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2rem;
        }
        
        .main-card {
            margin: 1rem;
        }
        
        .table-modern {
            font-size: 0.9rem;
        }
        
        .table-modern th,
        .table-modern td {
            padding: 0.8rem 0.5rem;
        }
    }
</style>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="main-card shadow-lg mb-4">
                <!-- En-tête de page -->
                <div class="page-header text-center">
                    <h1 class="page-title">
                        <div class="page-icon">
                            <i class="fas fa-layer-group text-white"></i>
                        </div>
                        Mes niveaux
                    </h1>
</div>
                
                <div class="card-body p-4">
@if(session('success'))
                        <div class="alert-modern animate-on-scroll">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
@endif

                    <!-- Tableau moderne -->
                    <div class="table-responsive animate-on-scroll">
                        <table class="table table-modern align-middle">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag me-2"></i>#</th>
                                    <th><i class="fas fa-layer-group me-2"></i>Nom du niveau</th>
                                    <th><i class="fas fa-calendar me-2"></i>Session</th>
                                    <th><i class="fas fa-book me-2"></i>Modules</th>
                                    <th><i class="fas fa-video me-2"></i>Lien Meet</th>
                                    <th><i class="fas fa-cogs me-2"></i>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($niveaux as $niveau)
                                <tr class="animate-on-scroll">
                                    <td>
                                        <span class="badge-modern badge-info">
                                            {{ $niveau->id }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $niveau->nom }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $niveau->description }}</small>
                                    </td>
            <td>
    @if($niveau->sessionFormation)
                                            <span class="badge-modern badge-success">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $niveau->sessionFormation->nom }}
                                            </span>
                                            <br>
        <small class="text-muted">{{ \Carbon\Carbon::parse($niveau->sessionFormation->date_debut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($niveau->sessionFormation->date_fin)->format('d/m/Y') }}</small>
    @else
                                            <span class="badge-modern badge-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Aucune session
                                            </span>
    @endif
</td>
                                    <td>
                                        <span class="badge-modern badge-info">
                                            <i class="fas fa-book me-1"></i>
                                            {{ $niveau->modules->count() }} module(s)
                                        </span>
                                    </td>
                                    <td>
                                        @if($niveau->lien_meet)
                                            <span class="badge-modern badge-success">
                                                <i class="fas fa-video me-1"></i>
                                                Disponible
                                            </span>
                                            <br>
                                            <a href="{{ $niveau->lien_meet }}" target="_blank" class="btn btn-info-modern btn-modern btn-sm mt-1">
                                                <i class="fas fa-external-link-alt me-1"></i>Rejoindre
                                            </a>
                                        @else
                                            <span class="badge-modern badge-warning">
                                                <i class="fas fa-video-slash me-1"></i>
                                                Non défini
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.niveaux.show', $niveau) }}" class="btn btn-info-modern btn-modern">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </a>
            </td>
        </tr>
        @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-folder-open me-2"></i>
                                        Aucun niveau attribué pour le moment.
                                    </td>
                                </tr>
        @endforelse
    </tbody>
</table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts pour les animations -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation d'apparition au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    // Observer tous les éléments avec la classe animate-on-scroll
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });

    // Animation des badges au hover
    document.querySelectorAll('.badge-modern').forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(2deg)';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });
    });

    // Effet de ripple sur les boutons
    document.querySelectorAll('.btn-modern').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                position: absolute;
                border-radius: 50%;
                background: rgba(255,255,255,0.3);
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Animation CSS pour le ripple
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
});
</script>

<!-- FontAwesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

@endsection 