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

    .btn-new {
        background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
        border: none;
        border-radius: 25px;
        padding: 12px 24px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-new:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 10px 30px rgba(34, 197, 94, 0.4);
        color: white;
    }

    .table-modern {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .table-modern thead {
        background: linear-gradient(135deg, #2d5016, #4a7c59);
        color: white;
    }

    .table-modern tbody tr {
        transition: all 0.3s ease;
        border: none;
    }

    .table-modern tbody tr:hover {
        background: rgba(34, 139, 34, 0.1);
        transform: scale(1.02);
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

    .btn-edit {
        background: linear-gradient(135deg, var(--grass-green), var(--dark-green));
        border: none;
        border-radius: 25px;
        padding: 8px 16px;
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-edit:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 20px rgba(21, 128, 61, 0.4);
        color: white;
    }

    .fade-in-row { 
        animation: fadeInUp 0.7s; 
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
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

<div class="container mt-4">
    <div class="row justify-content-center mb-3">
        <div class="col-md-10">
            <div class="alert alert-custom d-flex align-items-center shadow-sm mb-2" role="alert">
                <i class="fas fa-list fa-2x me-3 text-success"></i>
                <div>
                    <strong>Mes demandes de cours à domicile</strong><br>
                    Retrouvez ici toutes vos demandes passées et en cours. Suivez leur statut en temps réel !
                </div>
            </div>
            <a href="{{ route('cours.maison') }}" class="btn btn-new mb-3">
                <i class="fas fa-plus me-2"></i>Nouvelle demande
            </a>
            <div class="card main-card shadow border-0">
                <div class="card-header text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-list me-3"></i>Mes demandes de cours à domicile
                    </h3>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-custom animate__animated animate__fadeIn">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if($demandes->count())
                        <div class="table-responsive">
                            <table class="table table-modern align-middle" id="demandes-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Module à enseigner</th>
                                        <th>Nombre d'enfants</th>
                                        <th>Message</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($demandes as $demande)
                                        <tr class="fade-in-row">
                                            <td>
                                                <span class="badge-animated">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $demande->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-animated">
                                                    <i class="fas fa-book me-1"></i>
                                                    {{ $demande->module }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-animated">
                                                    <i class="fas fa-users me-1"></i>
                                                    {{ $demande->nombre_enfants }}
                                                </span>
                                            </td>
                                            <td>{{ Str::limit($demande->message, 80) }}</td>
                                            <td>
                                                @if($demande->statut == 'validee')
                                                    <span class="badge-animated bg-success">Validée</span>
                                                @elseif($demande->statut == 'refusee')
                                                    <span class="badge-animated bg-danger">Refusée</span>
                                                @elseif($demande->statut == 'acceptee_formateur')
                                                    <span class="badge-animated bg-info">Acceptée par le formateur</span>
                                                @elseif($demande->statut == 'refusee_formateur')
                                                    <span class="badge-animated bg-danger">Refusée par le formateur</span>
                                                @elseif($demande->statut == 'en_attente_formateur')
                                                    <span class="badge-animated bg-warning">En attente formateur</span>
                                                @else
                                                    <span class="badge-animated bg-warning">En attente</span>
                                                @endif
                                                @if(in_array($demande->statut, ['acceptee_formateur','en_attente_formateur','validee']) && $demande->formateur && $demande->formateur->utilisateur)
                                                    <div class="mt-2 small">
                                                        <i class="fas fa-user-tie me-1"></i><strong>Formateur:</strong>
                                                        {{ $demande->formateur->utilisateur->prenom }} {{ $demande->formateur->utilisateur->nom }}
                                                        <br>
                                                        <i class="fas fa-envelope me-1"></i>{{ $demande->formateur->utilisateur->email }}
                                                        <br>
                                                        <i class="fas fa-phone me-1"></i>{{ $demande->formateur->utilisateur->telephone ?? '—' }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('demandes.cours.maison.edit', $demande->id) }}" 
                                                   class="btn btn-edit btn-sm">
                                                    <i class="fas fa-edit me-1"></i>Modifier
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-custom text-center animate__animated animate__fadeIn">
                            <i class="fas fa-inbox fa-2x mb-3 text-success"></i><br>
                            <strong>Aucune demande trouvée.</strong><br>
                            Cliquez sur <span class="badge-animated">Nouvelle demande</span> pour en créer une !
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

    const tableRows = document.querySelectorAll('.fade-in-row');
    tableRows.forEach((row, index) => {
        row.style.animationDelay = (index * 0.1) + 's';
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
