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

    .card-header h2 {
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

    .questionnaire-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(240, 255, 244, 0.9));
        border: 1px solid rgba(34, 197, 94, 0.2);
        border-radius: 15px;
        margin-bottom: 15px;
        padding: 20px;
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(5px);
        height: 100%;
    }

    .questionnaire-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(34, 197, 94, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .questionnaire-card:hover::before {
        left: 100%;
    }

    .questionnaire-card:hover {
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

    .btn-respond {
        background: linear-gradient(135deg, var(--grass-green), var(--dark-green));
        border: none;
        border-radius: 25px;
        padding: 8px 16px;
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-respond:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 20px rgba(21, 128, 61, 0.4);
        color: white;
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

    .correction-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 248, 220, 0.9));
        border: 1px solid rgba(220, 53, 69, 0.3);
        border-radius: 15px;
        margin-bottom: 15px;
        padding: 20px;
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(5px);
    }

    .correction-item {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(248, 215, 218, 0.9));
        border: 1px solid rgba(220, 53, 69, 0.2);
        border-radius: 10px;
        margin-bottom: 10px;
        padding: 15px;
        transition: all 0.3s ease;
    }

    .correction-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.2);
    }

    .questionnaire-card:nth-child(1) { animation-delay: 0.1s; }
    .questionnaire-card:nth-child(2) { animation-delay: 0.2s; }
    .questionnaire-card:nth-child(3) { animation-delay: 0.3s; }
    .questionnaire-card:nth-child(4) { animation-delay: 0.4s; }
    .questionnaire-card:nth-child(5) { animation-delay: 0.5s; }

    @media (max-width: 768px) {
        .main-card {
            margin: 10px;
            border-radius: 15px;
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

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-11">
            <!-- Messages de résultat -->
            @if(session('success'))
                <div class="alert alert-custom animate__animated animate__fadeIn mb-4">
                    <i class="fas fa-check-circle me-2 text-success"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger animate__animated animate__fadeIn mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                </div>
                @if(session('incorrects'))
                    <div class="card correction-card mb-4 animate__animated animate__fadeIn">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-times-circle me-2"></i>Corrections
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="corrections-list">
                                @foreach(session('incorrects') as $inc)
                                    <div class="correction-item">
                                        <strong class="text-dark">{{ $inc['texte'] }}</strong><br>
                                        <span class="text-success">
                                            <i class="fas fa-check me-1"></i>Bonne réponse : {{ $inc['bonne_reponse'] }}
                                        </span><br>
                                        <span class="text-danger">
                                            <i class="fas fa-times me-1"></i>Votre réponse : {{ $inc['votre_reponse'] ?? 'Aucune' }}
                                        </span><br>
                                        <span class="text-warning">
                                            <i class="fas fa-star me-1"></i>{{ $inc['points'] ?? 1 }} points perdus
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="card main-card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header text-center">
                    <h2 class="mb-0">
                        <i class="fas fa-question-circle me-3"></i>Mes Questionnaires
                    </h2>
                </div>
                <div class="card-body p-5">
                    @if($questionnaires && count($questionnaires) > 0)
                        <div class="row g-4">
                            @foreach($questionnaires as $q)
                                <div class="col-md-6">
                                    <div class="questionnaire-card">
                                        <div class="d-flex align-items-center mb-3 gap-2">
                                            <span class="badge-animated">
                                                <i class="fas fa-layer-group me-1"></i>
                                                {{ $q->module->titre ?? '-' }}
                                            </span>
                                            <span class="badge-animated">
                                                <i class="fas fa-graduation-cap me-1"></i>
                                                {{ $q->module->niveau->nom ?? '-' }}
                                            </span>
                                        </div>
                                        <h5 class="fw-bold mb-3 text-success">{{ $q->titre }}</h5>
                                        <div class="mb-3 text-muted">{{ $q->description }}</div>
                                        <div class="d-flex justify-content-end">
                                            <a href="{{ route('apprenants.questionnaires.show', $q->id) }}" 
                                               class="btn btn-respond btn-sm rounded-pill">
                                                <i class="fas fa-edit me-1"></i>Répondre
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-custom text-center">
                            <i class="fas fa-info-circle fa-2x mb-3 text-success"></i>
                            <h5>Aucun questionnaire disponible</h5>
                            <p class="mb-0">Aucun questionnaire à afficher pour votre niveau ou vos modules.</p>
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

    const questionnaireCards = document.querySelectorAll('.questionnaire-card');
    questionnaireCards.forEach((card, index) => {
        card.style.animationName = 'slideInUp';
        card.style.animationDuration = '0.8s';
        card.style.animationDelay = (index * 0.1) + 's';
        card.style.animationFillMode = 'both';
    });

    const correctionItems = document.querySelectorAll('.correction-item');
    correctionItems.forEach((item, index) => {
        item.style.animationName = 'slideInUp';
        item.style.animationDuration = '0.8s';
        item.style.animationDelay = (index * 0.1) + 's';
        item.style.animationFillMode = 'both';
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
