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

    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid rgba(34, 197, 94, 0.3);
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(34, 197, 94, 0.25);
        background: rgba(255, 255, 255, 0.95);
    }

    .form-control:disabled {
        background: rgba(240, 255, 244, 0.8);
        color: var(--dark-green);
    }

    .btn-save {
        background: linear-gradient(135deg, var(--grass-green), var(--dark-green));
        border: none;
        border-radius: 25px;
        padding: 12px 30px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-save:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 10px 30px rgba(34, 197, 94, 0.4);
        color: white;
    }

    .setting-item {
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

    .setting-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(34, 197, 94, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .setting-item:hover::before {
        left: 100%;
    }

    .setting-item:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 15px 35px rgba(34, 197, 94, 0.2);
        border-color: var(--primary-green);
    }

    .form-switch .form-check-input {
        width: 2.5em;
        height: 1.3em;
        background-color: rgba(34, 197, 94, 0.3);
        border-color: var(--primary-green);
        transition: all 0.3s ease;
    }

    .form-switch .form-check-input:checked {
        background-color: var(--primary-green);
        border-color: var(--dark-green);
    }

    .form-switch .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(34, 197, 94, 0.25);
    }

    .section-title {
        color: var(--dark-green);
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--light-green);
    }

    .setting-item:nth-child(1) { animation-delay: 0.1s; }
    .setting-item:nth-child(2) { animation-delay: 0.2s; }
    .setting-item:nth-child(3) { animation-delay: 0.3s; }
    .setting-item:nth-child(4) { animation-delay: 0.4s; }
    .setting-item:nth-child(5) { animation-delay: 0.5s; }

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
        <div class="col-lg-8 col-md-11">
            <div class="card main-card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-header text-center">
                    <h2 class="mb-0">
                        <i class="fas fa-cog me-3"></i>Mes Paramètres
                    </h2>
                </div>
                <div class="card-body p-5">
                    <form method="POST" action="#" class="needs-validation" novalidate>
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-user me-2 text-success"></i>Nom
                                    </label>
                                    <input type="text" class="form-control" value="{{ $user->nom ?? 'Nom Test' }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-envelope me-2 text-success"></i>Email
                                    </label>
                                    <input type="email" class="form-control" value="{{ $user->email ?? 'test@email.com' }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-phone-alt me-2 text-success"></i>Téléphone
                                    </label>
                                    <input type="text" class="form-control" value="{{ $user->telephone ?? 'Non renseigné' }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-palette me-2 text-success"></i>Thème de couleur
                                    </label>
                                    <select class="form-select" id="theme-select">
                                        <option value="clair">Clair</option>
                                        <option value="sombre">Sombre</option>
                                        <option value="custom">Personnalisé</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-bell me-2 text-success"></i>Notifications
                                    </label>
                                    <select class="form-select">
                                        <option>Activées</option>
                                        <option>Désactivées</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-language me-2 text-success"></i>Langue
                                    </label>
                                    <select class="form-select">
                                        <option>Français</option>
                                        <option>Anglais</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-save btn-lg rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                    <hr class="my-5">
                    <h4 class="section-title">
                        <i class="fas fa-user-graduate me-2"></i>Paramètres Apprenant
                    </h4>
                    <div class="settings-list">
                        <div class="setting-item d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-question-circle me-2 text-info"></i>
                                Recevoir des rappels de questionnaires
                            </span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </div>
                        <div class="setting-item d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-calendar-alt me-2 text-warning"></i>
                                Afficher le calendrier des cours
                            </span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </div>
                        <div class="setting-item d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-language me-2 text-success"></i>
                                Notifications en français
                            </span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const settingItems = document.querySelectorAll('.setting-item');
    settingItems.forEach((item, index) => {
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

// Changement de thème fictif
const themeSelect = document.getElementById('theme-select');
themeSelect.addEventListener('change', function() {
    if(this.value === 'sombre') {
        document.body.style.background = '#23272f';
        document.body.style.color = '#fff';
    } else if(this.value === 'clair') {
        document.body.style.background = '#f8fafc';
        document.body.style.color = '#222';
    } else {
        document.body.style.background = 'linear-gradient(120deg, #43cea2 0%, #185a9d 100%)';
        document.body.style.color = '#fff';
    }
});
</script>
@endsection
