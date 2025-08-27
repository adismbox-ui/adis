@extends('apprenants.layout')

@section('content')
<style>
    :root {
        --primary-green: #22c55e;
        --dark-green: #16a34a;
        --light-green: #86efac;
        --grass-green: #15803d;
        --bg-overlay: rgba(0, 0, 0, 0.7);
    }

    body {
        background: linear-gradient(135deg, #1e3a8a 0%, #16a34a 50%, #22c55e 100%);
        background-attachment: fixed;
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
        font-family: 'Poppins', sans-serif;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: 
            linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
            radial-gradient(circle at 25% 25%, rgba(34, 197, 94, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 75% 75%, rgba(22, 163, 74, 0.3) 0%, transparent 50%),
            url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        animation: backgroundFlow 20s ease-in-out infinite;
        z-index: -1;
    }

    @keyframes backgroundFlow {
        0%, 100% { transform: translateX(0) translateY(0); }
        25% { transform: translateX(-20px) translateY(-10px); }
        50% { transform: translateX(20px) translateY(-20px); }
        75% { transform: translateX(-10px) translateY(10px); }
    }

    .container {
        position: relative;
        z-index: 1;
    }

    .floating-particles {
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
        background: var(--light-green);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
        opacity: 0.6;
    }

    .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
    .particle:nth-child(2) { left: 20%; animation-delay: 1s; }
    .particle:nth-child(3) { left: 30%; animation-delay: 2s; }
    .particle:nth-child(4) { left: 40%; animation-delay: 3s; }
    .particle:nth-child(5) { left: 50%; animation-delay: 4s; }
    .particle:nth-child(6) { left: 60%; animation-delay: 5s; }
    .particle:nth-child(7) { left: 70%; animation-delay: 0.5s; }
    .particle:nth-child(8) { left: 80%; animation-delay: 1.5s; }
    .particle:nth-child(9) { left: 90%; animation-delay: 2.5s; }

    @keyframes float {
        0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
        10% { opacity: 0.6; }
        90% { opacity: 0.6; }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .btn-back {
        background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
        border: none;
        color: white;
        padding: 12px 24px;
        border-radius: 50px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
        animation: slideInLeft 0.8s ease-out;
    }

    .btn-back:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
        color: white;
    }

    @keyframes slideInLeft {
        from { transform: translateX(-100px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    .main-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        animation: slideInUp 1s ease-out;
        overflow: hidden;
        position: relative;
    }

    .main-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--primary-green), transparent);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    @keyframes slideInUp {
        from { transform: translateY(50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .card-header {
        background: linear-gradient(135deg, var(--primary-green), var(--dark-green)) !important;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }

    .card-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: conic-gradient(transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: rotate 4s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .card-header h1, .card-header h4 {
        position: relative;
        z-index: 2;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        animation: textGlow 2s ease-in-out infinite alternate;
    }

    @keyframes textGlow {
        from { text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); }
        to { text-shadow: 2px 2px 8px rgba(255, 255, 255, 0.5); }
    }

    .alert-custom {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(134, 239, 172, 0.2));
        border: 1px solid var(--primary-green);
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }

    .module-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(240, 255, 244, 0.9));
        border: 1px solid rgba(34, 197, 94, 0.2);
        border-radius: 15px;
        margin-bottom: 15px;
        padding: 20px;
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(5px);
    }

    .module-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(34, 197, 94, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .module-card:hover::before {
        left: 100%;
    }

    .module-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 15px 35px rgba(34, 197, 94, 0.2);
        border-color: var(--primary-green);
    }

    .badge-animated {
        background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8em;
        margin: 2px;
        display: inline-block;
        animation: bounceIn 0.8s ease-out;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(34, 197, 94, 0.3);
    }

    .badge-animated:hover {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.5);
    }

    @keyframes bounceIn {
        0% { transform: scale(0.3); opacity: 0; }
        50% { transform: scale(1.05); }
        70% { transform: scale(0.9); }
        100% { transform: scale(1); opacity: 1; }
    }

    .btn-pay {
        background: linear-gradient(135deg, var(--grass-green), var(--dark-green));
        border: none;
        border-radius: 25px;
        padding: 15px 30px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    .btn-pay:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 10px 30px rgba(34, 197, 94, 0.4);
        color: white;
    }

    .btn-pay:disabled {
        background: #6c757d;
        transform: none;
        box-shadow: none;
    }

    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid rgba(34, 197, 94, 0.3);
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(34, 197, 94, 0.25);
    }

    .payment-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .payment-card .card-header {
        background: linear-gradient(135deg, var(--grass-green), var(--dark-green)) !important;
        border-radius: 15px 15px 0 0;
    }

    @media (max-width: 768px) {
        .main-card {
            margin: 10px;
            border-radius: 15px;
        }
        
        .btn-back {
            padding: 10px 20px;
            font-size: 0.9em;
        }
    }
</style>

<div class="floating-particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>

<a href="javascript:history.back()" class="btn btn-back mb-3">
    <i class="fas fa-arrow-left me-2"></i>Retour
</a>

<div class="container mt-5">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card main-card shadow-lg border-0">
                <div class="card-header text-center">
                    <h1 class="mb-0">
                        <i class="fas fa-book me-3"></i>Demande d'Accès aux Modules
                    </h1>
                    <p class="mb-0 mt-2">Sélectionnez les modules et contactez l'administrateur pour l'accès</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-custom alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Sélection du module -->
        <div class="col-lg-8 mb-4">
            <div class="card main-card shadow-lg border-0">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-book me-3"></i>Formations Disponibles
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if($modules->count() > 0)
                        <div class="alert alert-custom mb-4 text-center">
                            <i class="fas fa-info-circle me-2 text-success"></i>
                            Modules disponibles à l'achat pour votre niveau :
                        </div>
                        <div class="row">
                            @foreach($modules as $module)
                                <div class="col-md-6 mb-3">
                                    <div class="module-card">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title text-success mb-1 fw-bold">
                                                <i class="fas fa-book me-2"></i>{{ $module->titre }}
                                            </h5>
                                            <span class="badge-animated">
                                                <i class="fas fa-layer-group me-1"></i>
                                                {{ $module->niveau->nom ?? 'Niveau non défini' }}
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <strong class="text-success">
                                                <i class="fas fa-money-bill me-1"></i>
                                                {{ $module->prix ? number_format($module->prix, 0, ',', ' ') . ' F CFA' : 'Gratuit' }}
                                            </strong>
                                        </div>
                                        @if($module->description)
                                            <p class="card-text text-muted small mb-2">{{ Str::limit($module->description, 80) }}</p>
                                        @endif
                                        @if($module->formateur)
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-user-tie text-success me-1"></i>
                                                <small class="text-muted">{{ $module->formateur->utilisateur->prenom ?? '' }} {{ $module->formateur->utilisateur->nom ?? 'Formateur non assigné' }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-custom text-center">
                            <i class="fas fa-info-circle me-2 text-success"></i>
                            Aucun module à acheter pour votre niveau actuellement.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Section Contact Admin -->
        <div class="col-lg-4 mb-4">
            <div class="card payment-card shadow-lg border-0 sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-user-shield me-3"></i>Accès aux Modules
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if($modules->count() > 0)
                        <div class="alert alert-info text-center mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Pour accéder aux modules, contactez l'administrateur</strong>
                        </div>
                        
                        <div class="text-center mb-4">
                            <div class="h4 text-success mb-3">
                                <i class="fas fa-money-bill me-2"></i>
                                Montant total : {{ number_format($modules->sum('prix'), 0, ',', ' ') }} F CFA
                            </div>
                            <p class="text-muted">Prix des modules sélectionnés</p>
                        </div>

                        <div class="alert alert-warning">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Procédure d'accès
                            </h6>
                            <p class="mb-2">Pour obtenir l'accès aux modules de formation :</p>
                            <ol class="mb-0">
                                <li>Contactez l'administrateur par téléphone ou email</li>
                                <li>Présentez votre demande d'accès</li>
                                <li>Effectuez le paiement selon les modalités définies</li>
                                <li>L'admin vous activera l'accès aux modules</li>
                            </ol>
                        </div>

                        <div class="card border-primary mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-phone me-2"></i>Contact Administrateur
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Téléphone :</strong> 
                                    <a href="tel:+22500000000" class="text-primary">+225 00 00 00 00</a>
                                </div>
                                <div class="mb-2">
                                    <strong>Email :</strong> 
                                    <a href="mailto:admin@adis-formation.com" class="text-primary">admin@adis-formation.com</a>
                                </div>
                                <div class="mb-0">
                                    <strong>Horaires :</strong> Lundi - Vendredi, 8h - 18h
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <form action="{{ route('achat.envoyer_demande') }}" method="POST" onsubmit="return confirm('Envoyer une demande d\'accès aux modules à l\'administrateur ?')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer une demande
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-custom text-center">
                            <i class="fas fa-info-circle me-2 text-success"></i>
                            Aucun module disponible pour l'achat.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const badges = document.querySelectorAll('.badge-animated');
    badges.forEach((badge, index) => {
        badge.style.animationDelay = (index * 0.1) + 's';
        
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(5deg)';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });
    });

    const moduleCards = document.querySelectorAll('.module-card');
    moduleCards.forEach((card, index) => {
        card.style.animationName = 'slideInUp';
        card.style.animationDuration = '0.8s';
        card.style.animationDelay = (index * 0.1) + 's';
        card.style.animationFillMode = 'both';
    });

    let ticking = false;
    function updateParticles() {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelectorAll('.particle');
        const speed = scrolled * 0.5;

        parallax.forEach((particle, index) => {
            const yPos = -(speed / (index + 1));
            particle.style.transform = 'translateY(' + yPos + 'px)';
        });
        ticking = false;
    }

    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateParticles);
            ticking = true;
        }
    }

    window.addEventListener('scroll', requestTick);
});
</script>
@endsection
