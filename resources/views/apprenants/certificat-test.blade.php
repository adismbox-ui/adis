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

    .card-header h3 {
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

    .certificate-item {
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

    .certificate-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(34, 197, 94, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .certificate-item:hover::before {
        left: 100%;
    }

    .certificate-item:hover {
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

    .btn-download {
        background: linear-gradient(135deg, var(--grass-green), var(--dark-green));
        border: none;
        border-radius: 25px;
        padding: 10px 20px;
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-download::after {
        content: '📄';
        position: absolute;
        top: 50%;
        left: -30px;
        transform: translateY(-50%);
        transition: left 0.3s ease;
    }

    .btn-download:hover::after {
        left: calc(100% - 25px);
    }

    .btn-download:hover {
        transform: translateX(10px);
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

    .certificate-item:nth-child(1) { animation-delay: 0.1s; }
    .certificate-item:nth-child(2) { animation-delay: 0.2s; }
    .certificate-item:nth-child(3) { animation-delay: 0.3s; }
    .certificate-item:nth-child(4) { animation-delay: 0.4s; }
    .certificate-item:nth-child(5) { animation-delay: 0.5s; }

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

<div class="container py-4">
    <a href="{{ url()->previous() }}" class="btn btn-back mb-4">
        <i class="fas fa-arrow-left me-2"></i>Retour
    </a>
    
    <div class="card main-card shadow-lg border-0 mb-4">
        <div class="card-header">
            <h3 class="mb-0">
                <i class="fas fa-certificate me-3"></i>Mes Certificats
            </h3>
        </div>
        <div class="card-body p-4">
            @if($modulesValidés->count() > 0)
                <h5 class="mb-3 text-success">
                    <i class="fas fa-trophy me-2"></i>Modules validés :
                </h5>
                <div class="certificates-list">
                    @foreach($modulesValidés as $inscription)
                        <div class="certificate-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2">
                                        <i class="fas fa-certificate text-success me-2"></i>
                                        {{ $inscription->module->titre ?? 'Module inconnu' }}
                                    </h6>
                                    <span class="badge-animated">
                                        <i class="fas fa-check-circle me-1"></i>Validé
                                    </span>
                                </div>
                                @php
                                    $certificat = $certificats->where('module_id', $inscription->module_id)->first();
                                @endphp
                                @if($certificat)
                                    <a href="{{ route('apprenants.certificats.test', ['download'=>1, 'module_id'=>$inscription->module_id]) }}" 
                                       class="btn btn-download">
                                        <i class="fas fa-download me-2"></i>
                                        Télécharger
                                    </a>
                                @else
                                    <span class="text-muted">Certificat non généré</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-custom">
                    <i class="fas fa-info-circle fa-2x mb-3 text-success"></i>
                    <h5>Vous n'avez validé aucun module</h5>
                    <p class="mb-0">Commencez par valider vos modules pour obtenir des certificats.</p>
                </div>
            @endif

            @if(isset($niveauCertificat) && $niveauCertificat)
                <div class="alert alert-custom mt-4">
                    <i class="fas fa-graduation-cap fa-2x mb-3 text-success"></i>
                    <h5>🎓 Certificat de niveau obtenu :</h5>
                    <p>Vous avez validé le niveau <strong>{{ $niveauCertificat->nom }}</strong> !</p>
                    @if(isset($certificatNiveau) && $certificatNiveau)
                        <a href="{{ route('certificats.generator.readonly', $certificatNiveau) }}" 
                           class="btn btn-download">
                            <i class="fas fa-eye me-2"></i>
                            Ouvrir le générateur du certificat de niveau
                        </a>
                    @else
                        <span class="text-muted">Certificat de niveau non disponible</span>
                    @endif
                </div>
            @endif

            <h5 class="mt-4 text-success">
                <i class="fas fa-list me-2"></i>Certificats obtenus :
            </h5>
            <div class="certificates-list">
                @forelse($certificats as $certificat)
                    <div class="certificate-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-2">
                                    <i class="fas fa-certificate text-success me-2"></i>
                                    {{ $certificat->module->titre ?? 'Module inconnu' }}
                                </h6>
                                <span class="badge-animated">
                                    <i class="fas fa-calendar me-1"></i>
                                    Délivré le {{ $certificat->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="certificate-item">
                        <div class="text-center text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            Aucun certificat disponible.
                        </div>
                    </div>
                @endforelse
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

    const certificateItems = document.querySelectorAll('.certificate-item');
    certificateItems.forEach((item, index) => {
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