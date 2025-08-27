@extends('apprenants.layout')

@section('content')
<!-- Fond image via Bootstrap utilities avec même image que certificat-test -->
<div class="position-fixed top-0 start-0 w-100 h-100 z-n1">
    <div class="w-100 h-100" style="background: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') center/cover no-repeat; animation: backgroundFloat 28s ease-in-out infinite;"></div>
    <!-- Voile sombre comme sur la page admin -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(34,34,34,0.75) 0%, rgba(45,80,22,0.55) 60%, rgba(127,176,105,0.25) 100%), radial-gradient(ellipse at 60% 40%, rgba(127,176,105,0.18) 0%, rgba(45,80,22,0.12) 60%, transparent 100%); animation: greenGlow 8s ease-in-out infinite alternate;"></div>
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(1200px 600px at -10% -10%, rgba(45,80,22,0.18), transparent 50%), radial-gradient(900px 500px at 110% 110%, rgba(45,80,22,0.16), transparent 40%);"></div>
</div>

<!-- Bandeau d'information en vert avec animation -->
<div class="container-fluid pt-3">
    <div class="row">
        <div class="col-12">
            <div class="p-3 p-md-4 mb-3 mb-md-4 text-center fw-semibold"
                 style="background: linear-gradient(135deg, #1b5e20, #2e7d32); color:#eaffea; border:1px solid rgba(127,176,105,0.45); border-radius:16px; box-shadow:0 10px 28px rgba(67, 160, 71, 0.25); letter-spacing:.2px; animation: pulse 2.8s ease-in-out infinite;">
                <i class="fas fa-seedling me-2"></i>
                Pour passer au niveau suivant, il faut au moins 60% de moyenne sur l'ensemble des modules
            </div>
        </div>
    </div>
    
</div>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(120deg, #0b0b0b 0%, #121212 60%, #0e160e 100%);
        background-size: 300% 300%;
        animation: gradientShift 18s ease infinite;
        color: #e8f5e9;
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    /* Animation du fond dégradé */
    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Animation du voile vert comme sur la page admin */
    @keyframes greenGlow {
        0% { filter: blur(0px) brightness(1) hue-rotate(0deg); }
        50% { filter: blur(2px) brightness(1.08) hue-rotate(-10deg); }
        100% { filter: blur(0px) brightness(1) hue-rotate(0deg); }
    }

    /* Laisser à zéro pour privilégier le fond via Bootstrap (inséré en HTML) */
    body::before { content: ''; position: fixed; inset: 0; opacity: 0; z-index: -2; }

    @keyframes backgroundFloat {
        0%, 100% { transform: scale(1) translateY(0) rotate(0deg); }
        50% { transform: scale(1.06) translateY(-8px) rotate(0.3deg); }
    }

    /* Overlay géré via Bootstrap en HTML */
    body::after { content: ''; position: fixed; inset: 0; opacity: 0; z-index: -1; }

    /* Particules flottantes */
    .floating-particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
    }

    .particle {
        position: absolute;
        background: radial-gradient(circle, #ffd700, #ffed4e);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
        opacity: 0.6;
    }

    .particle:nth-child(1) { width: 8px; height: 8px; left: 10%; animation-delay: 0s; }
    .particle:nth-child(2) { width: 12px; height: 12px; left: 20%; animation-delay: 1s; }
    .particle:nth-child(3) { width: 6px; height: 6px; left: 30%; animation-delay: 2s; }
    .particle:nth-child(4) { width: 10px; height: 10px; left: 40%; animation-delay: 0.5s; }
    .particle:nth-child(5) { width: 14px; height: 14px; left: 50%; animation-delay: 1.5s; }
    .particle:nth-child(6) { width: 8px; height: 8px; left: 60%; animation-delay: 2.5s; }
    .particle:nth-child(7) { width: 12px; height: 12px; left: 70%; animation-delay: 3s; }
    .particle:nth-child(8) { width: 6px; height: 6px; left: 80%; animation-delay: 0.8s; }
    .particle:nth-child(9) { width: 10px; height: 10px; left: 90%; animation-delay: 1.8s; }

    @keyframes float {
        0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
        10% { opacity: 0.6; }
        90% { opacity: 0.6; }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    /* Cartes avec effet glassmorphism */
    .glass-card {
        background: rgba(10, 14, 10, 0.55);
        backdrop-filter: blur(14px) saturate(120%);
        -webkit-backdrop-filter: blur(14px) saturate(120%);
        border: 1px solid rgba(127, 176, 105, 0.22);
        border-radius: 20px;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.35);
        color: #e8f5e9;
        transition: all 0.3s ease;
        animation: slideInUp 0.8s ease;
    }

    .glass-card:hover {
        transform: translateY(-8px) scale(1.015);
        box-shadow: 0 22px 48px rgba(127, 176, 105, 0.18);
        border-color: rgba(127, 176, 105, 0.38);
        background: rgba(12, 18, 12, 0.62);
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

    /* Badges 3D avec animations */
    .badge-3d {
        background: linear-gradient(135deg, #228b22, #32cd32, #ffd700);
        border: none;
        border-radius: 25px;
        padding: 8px 16px;
        color: white;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        box-shadow: 
            0 4px 8px rgba(0, 0, 0, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.3),
            inset 0 -1px 0 rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        animation: pulse 2s infinite;
        position: relative;
        overflow: hidden;
    }

    .badge-3d::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transform: rotate(45deg);
        transition: all 0.6s;
        opacity: 0;
    }

    .badge-3d:hover::before {
        animation: shine 0.6s ease;
        opacity: 1;
    }

    @keyframes shine {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    /* En-têtes avec dégradé vert-doré */
    .header-gradient {
        background: linear-gradient(135deg, rgba(127, 176, 105, 0.25), rgba(45, 80, 22, 0.35));
        color: #f3fff5;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.45);
        border-radius: 20px 20px 0 0;
        position: relative;
        overflow: hidden;
    }

    .header-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        animation: headerShine 3s ease infinite;
    }

    @keyframes headerShine {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    /* Alerte avec animation */
    .alert-animated {
        background: rgba(34, 139, 34, 0.08);
        border: 2px solid rgba(127,176,105,0.45);
        color: #e8f5e9;
        border-radius: 15px;
        animation: alertBounce 0.6s ease;
        position: relative;
        overflow: hidden;
    }

    .alert-animated::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #228b22, #ffd700, #228b22);
        animation: progressBar 2s linear infinite;
    }

    @keyframes progressBar {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    @keyframes alertBounce {
        0% { transform: scale(0.8); opacity: 0; }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Tableau avec effets */
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

    /* Graphique avec animation */
    .chart-container {
        position: relative;
        animation: chartSpin 0.8s ease;
    }

    @keyframes chartSpin {
        from { transform: rotate(-180deg) scale(0.5); opacity: 0; }
        to { transform: rotate(0deg) scale(1); opacity: 1; }
    }

    /* Boutons avec effets 3D */
    .btn-3d {
        background: linear-gradient(135deg, #228b22, #32cd32);
        border: none;
        border-radius: 25px;
        padding: 12px 24px;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 
            0 6px 12px rgba(34, 139, 34, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-3d:hover {
        transform: translateY(-3px);
        box-shadow: 
            0 10px 20px rgba(34, 139, 34, 0.6),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
        background: linear-gradient(135deg, #32cd32, #228b22);
    }

    .btn-3d:active {
        transform: translateY(0);
        box-shadow: 0 4px 8px rgba(34, 139, 34, 0.4);
    }

    /* Icônes animées */
    .icon-animated {
        animation: iconBounce 2s ease infinite;
    }

    @keyframes iconBounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    /* Liste avec animations */
    .list-group-item {
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(34, 139, 34, 0.2);
        transition: all 0.3s ease;
        border-radius: 10px !important;
        margin-bottom: 5px;
    }

    .list-group-item:hover {
        background: rgba(34, 139, 34, 0.1);
        transform: translateX(10px);
        border-color: #228b22;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .glass-card {
            border-radius: 15px;
            margin: 10px;
        }
        
        .badge-3d {
            font-size: 0.8rem;
            padding: 6px 12px;
        }
    }

    /* Container principal */
    .main-container {
        position: relative;
        z-index: 1;
        padding: 20px 0;
    }

    /* Alerte danger avec animation */
    .alert-danger-animated {
        background: rgba(220, 53, 69, 0.1);
        border: 2px solid #dc3545;
        border-radius: 15px;
        animation: alertBounce 0.6s ease;
        position: relative;
        overflow: hidden;
    }

    .alert-danger-animated::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #dc3545, #ffd700, #dc3545);
        animation: progressBar 2s linear infinite;
    }
</style>

<!-- Particules flottantes -->
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

<div class="main-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-11">
                <!-- Bouton d'achat modules -->
                <div class="text-center mb-4 d-flex justify-content-center gap-2">
                    <a href="{{ route('achat') }}" class="btn btn-3d">
                        <i class="fas fa-shopping-cart me-2 icon-animated"></i>
                        Acheter des modules
                    </a>
                    @if(isset($user) && $user && $user->formateur)
                    <a href="{{ route('formateurs.dashboard') }}" class="btn btn-3d" style="background: linear-gradient(135deg, #1b5e20, #2e7d32);">
                        <i class="fas fa-chalkboard-teacher me-2 icon-animated"></i>
                        Espace Formateur
                    </a>
                    @endif
                </div>

                <!-- Points et progression de niveau -->
                <div class="alert alert-animated d-flex align-items-center mb-4" style="font-size:1.2rem;">
                    <div class="chart-container me-4" style="width:90px; height:90px;">
                        <canvas id="progressChart" width="90" height="90"></canvas>
                    </div>
                    <div>
                        <strong>Moyenne modules :</strong> 
                        <span class="badge badge-3d fs-5">{{ $pourcentage ?? 0 }}%</span>
                        <br>
                        <small class="text-muted">Pour passer au niveau suivant, il faut au moins <b>60% de moyenne</b> sur l'ensemble des modules.</small>
                        @if(isset($nextNiveau) && $nextNiveau)
                            <div class="mt-2">
                                <span class="badge badge-3d fs-6">
                                    <i class="fas fa-trophy me-1"></i>
                                    Félicitations ! Vous avez atteint le niveau <b>{{ $nextNiveau->nom }}</b> grâce à votre moyenne de {{ $pourcentage }}%
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modules non validés -->
                @if(!empty($modulesNonValides))
                    <div class="alert alert-danger-animated mb-4" style="font-size:1.1rem;">
                        <i class="fas fa-exclamation-triangle me-2 icon-animated"></i>
                        <strong>Attention :</strong> Vous n'avez pas encore validé les modules suivants (moins de 60%) :
                        <ul class="mb-0 mt-2">
                            @foreach($modulesNonValides as $modTitre)
                                <li>{{ $modTitre }}</li>
                            @endforeach
                        </ul>
                        <span class="text-danger">Vous devez obtenir au moins 60% dans chaque module pour valider.</span>
                    </div>
                @endif

                <!-- Carte principale -->
                <div class="glass-card mb-4">
                    <div class="card-header header-gradient text-center">
                        <h2 class="mb-0">
                            <i class="fas fa-user-graduate me-2 icon-animated"></i>
                            Tableau de bord Apprenant
                        </h2>
                    </div>
                    <div class="card-body">
                        <!-- Progression par module -->
                        <div class="mb-4">
                            <h5 class="mb-3 text-success">
                                <i class="fas fa-list-alt me-2 icon-animated"></i>
                                Progression par module
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-modern align-middle">
                                    <thead>
                                        <tr>
                                            <th>Module</th>
                                            <th>Pourcentage</th>
                                            <th>Points obtenus</th>
                                            <th>Points possibles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($modulesPourcentages as $mod)
                                            <tr>
                                                <td>{{ $mod['titre'] }}</td>
                                                <td><span class="badge badge-3d">{{ $mod['pourcentage'] }}%</span></td>
                                                <td>{{ $mod['points_obtenus'] }}</td>
                                                <td>{{ $mod['points_possibles'] }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center">Aucun module trouvé pour ce niveau.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Informations personnelles -->
                        <div class="glass-card mb-4">
                            <div class="card-header header-gradient">
                                <h4 class="mb-0">
                                    <i class="fas fa-id-card me-2 icon-animated"></i>
                                    Informations personnelles
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">
                                                <strong><i class="fas fa-user me-2 text-success"></i>Nom :</strong> {{ $user->nom ?? '-' }}
                                            </li>
                                            <li class="list-group-item">
                                                <strong><i class="fas fa-user me-2 text-success"></i>Prénom :</strong> {{ $user->prenom ?? '-' }}
                                            </li>
                                            <li class="list-group-item">
                                                <strong><i class="fas fa-envelope me-2 text-success"></i>Email :</strong> {{ $user->email ?? '-' }}
                                            </li>
                                            <li class="list-group-item">
                                                <strong><i class="fas fa-phone me-2 text-success"></i>Téléphone :</strong> {{ $user->telephone ?? '-' }}
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">
                                                <strong><i class="fas fa-user-tag me-2 text-success"></i>Statut :</strong> 
                                                <span class="badge badge-3d">{{ ucfirst($user->type_compte ?? 'apprenant') }}</span>
                                            </li>
                                            <li class="list-group-item">
                                                <strong><i class="fas fa-level-up-alt me-2 text-success"></i>Niveau :</strong> 
                                                <span class="badge badge-3d">{{ $apprenant && $apprenant->niveau ? (is_object($apprenant->niveau) ? $apprenant->niveau->nom : $apprenant->niveau) : '-' }}</span>
                                            </li>
                                            <li class="list-group-item">
                                                <strong><i class="fas fa-calendar me-2 text-success"></i>Année d'inscription :</strong> {{ $apprenant && $apprenant->created_at ? $apprenant->created_at->format('Y') : '-' }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Partie paiement (ne pas toucher) -->
                        @includeWhen(isset($paiements) && isset($modules), 'apprenants.partials.paiement')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('progressChart').getContext('2d');
        var percent = {{ $pourcentage ?? 0 }};
        var color = percent < 60 ? '#dc3545' : '#228b22';
        var bgColor = 'rgba(255, 255, 255, 0.2)';
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [percent, 100 - percent],
                    backgroundColor: [color, bgColor],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '75%',
                plugins: {
                    tooltip: { enabled: false },
                    legend: { display: false },
                    title: { display: false }
                },
                animation: {
                    animateRotate: true,
                    duration: 2000,
                    easing: 'easeInOutBounce'
                }
            },
            plugins: [{
                id: 'centerText',
                afterDraw: function(chart) {
                    var width = chart.width,
                        height = chart.height,
                        ctx = chart.ctx;
                    ctx.restore();
                    var fontSize = (height / 4.5).toFixed(2);
                    ctx.font = 'bold ' + fontSize + "px Poppins";
                    ctx.textBaseline = "middle";
                    var text = percent + "%",
                        textX = Math.round((width - ctx.measureText(text).width) / 2),
                        textY = height / 2;
                    ctx.fillStyle = color;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
            }]
        });

        // Animation des cartes au scroll
        const cards = document.querySelectorAll('.glass-card');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationDelay = Math.random() * 0.5 + 's';
                    entry.target.classList.add('slideInUp');
                }
            });
        });

        cards.forEach(card => observer.observe(card));

        // Animation des badges au clic
        const badges = document.querySelectorAll('.badge-3d');
        badges.forEach(badge => {
            badge.addEventListener('click', function() {
                this.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            });
        });
    });
</script>
@endsection

