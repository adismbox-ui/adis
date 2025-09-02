<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apprenant - ADIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { background: #f8f9fa; }
        .main-content {
            padding: 2rem 2rem 2rem 2rem;
            margin-left: 220px;
            min-height: 100vh;
            width: calc(100% - 220px);
        }
        /* Sidebar sombre avec image de fond et overlays animés (style proche admin) */
        .sidebar {
            min-width:220px;
            height:100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            overflow-x: hidden;
            border-right: 1px solid rgba(127,176,105,0.28);
            box-shadow: 4px 0 18px rgba(0,0,0,0.55);
            background: linear-gradient(160deg, #0a0a0a 0%, #161616 60%, rgba(45,80,22,0.22) 100%);
            animation: sidebar-slide-in 0.8s cubic-bezier(.77,0,.18,1) 0s 1;
            backdrop-filter: blur(10px) saturate(120%);
            -webkit-backdrop-filter: blur(10px) saturate(120%);
            z-index: 1000;
        }
        .sidebar .sidebar-bg-overlay{
            position:absolute; inset:0;
            background: url('{{ asset('ad.jpg') }}') center/cover no-repeat;
            opacity: 0.22;
            z-index: 0;
            animation: sidebarBgPulse 10s ease-in-out infinite;
            pointer-events: none;
        }
        .sidebar .sidebar-overlay-dark{
            position:absolute; inset:0;
            background: radial-gradient(1200px 600px at -10% -10%, rgba(45,80,22,0.25), transparent 50%),
                        linear-gradient(160deg, rgba(0,0,0,0.65), rgba(0,0,0,0.75));
            z-index: 1;
            pointer-events: none;
        }
        .sidebar .sidebar-particles{ position:absolute; inset:0; z-index: 2; pointer-events:none; }
        @keyframes sidebarBgPulse { 0%,100%{opacity:.22} 50%{opacity:.3} }
        .sidebar > * { position: relative; z-index: 2; }
        .sidebar .nav-link {
            color: #e8f5e9;
            font-weight: 500;
            border-radius: 12px;
            margin-bottom: 8px;
            transition: background 0.3s, color 0.2s, box-shadow 0.2s, transform 0.2s;
            display: flex;
            align-items: center;
            padding: 12px 18px;
            font-size: 1.06rem;
            border: 1px solid rgba(127,176,105,0.18);
            background: rgba(255,255,255,0.04);
            box-shadow: 0 2px 10px rgba(0,0,0,0.25);
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(127,176,105,0.18), rgba(80,120,50,0.28));
            color: #fff !important;
            border-color: rgba(127,176,105,0.38);
            box-shadow: 0 8px 22px rgba(127,176,105,0.18);
            transform: translateX(6px) scale(1.04);
        }
        .sidebar .nav-link i {
            font-size: 1.35em;
            margin-right: 14px;
            transition: color 0.2s, transform 0.3s cubic-bezier(.77,0,.18,1);
            filter: drop-shadow(0 1px 2px #6366f155);
        }
        .sidebar .nav-link:hover i, .sidebar .nav-link.active i {
            color: #fff;
            animation: icon-bounce 0.5s;
        }
        @keyframes icon-bounce {
            0% { transform: scale(1) rotate(0deg); }
            30% { transform: scale(1.25) rotate(-10deg); }
            60% { transform: scale(0.95) rotate(8deg); }
            100% { transform: scale(1) rotate(0deg); }
        }
        .sidebar .nav-link.text-danger {
            color: #f87171 !important;
        }
        .sidebar .nav-link.text-danger:hover {
            background: linear-gradient(90deg, #dc3545 0%, #f87171 100%);
            color: #fff !important;
        }
        .sidebar .nav-link .badge {
            margin-left: auto;
        }
        .sidebar .nav-link:focus {
            outline: 2px solid #6366f1;
        }
        .sidebar .nav-link svg {
            margin-right: 8px;
        }
        .sidebar .nav-link .fa {
            min-width: 22px;
            text-align: center;
        }
        .sidebar .nav-link.active {
            font-weight: bold;
            box-shadow: 0 8px 24px rgba(99,102,241,0.18);
        }
        .sidebar .text-center img {
            filter: drop-shadow(0 2px 8px #6366f1aa);
            border: 3px solid #fff;
            background: rgba(255,255,255,0.7);
        }
        .particles { display: none; }

        /* Styles pour le scroll du sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(67, 234, 74, 0.5);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(67, 234, 74, 0.8);
        }

        /* Responsive pour mobile */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -220px;
                transition: left 0.3s ease;
                z-index: 1001;
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
            }

            /* Bouton pour ouvrir/fermer le sidebar sur mobile */
            .sidebar-toggle {
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1002;
                background: #43ea4a;
                border: none;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                color: white;
                font-size: 1.2rem;
                box-shadow: 0 4px 12px rgba(67, 234, 74, 0.3);
                transition: all 0.3s ease;
            }

            .sidebar-toggle:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 16px rgba(67, 234, 74, 0.4);
            }
        }
    </style>
    <script>
        // Ajoute la classe active sur le lien du menu correspondant à l'URL
        document.addEventListener('DOMContentLoaded', function() {
            var links = document.querySelectorAll('.sidebar .nav-link');
            var path = window.location.pathname;
            links.forEach(function(link) {
                if(link.getAttribute('href') === path) {
                    link.classList.add('active');
                }
            });
            // Animation particules flottantes
            const particles = document.getElementById('particles');
            if(particles) {
                for(let i=0; i<18; i++) {
                    let p = document.createElement('div');
                    p.style.position = 'absolute';
                    p.style.width = p.style.height = (8 + Math.random()*16) + 'px';
                    p.style.borderRadius = '50%';
                    p.style.background = `linear-gradient(135deg, #6366f1, #60a5fa, #f472b6, #fbbf24, #34d399)`;
                    p.style.opacity = 0.13 + Math.random()*0.18;
                    p.style.left = (Math.random()*100) + 'vw';
                    p.style.top = (Math.random()*100) + 'vh';
                    p.style.filter = 'blur(1.5px)';
                    p.style.zIndex = 0;
                    p.animate([
                        { transform: `translateY(0px) scale(1)` },
                        { transform: `translateY(${40+Math.random()*80}px) scale(${0.8+Math.random()*0.5})` }
                    ], {
                        duration: 8000 + Math.random()*6000,
                        direction: 'alternate',
                        iterations: Infinity,
                        easing: 'ease-in-out'
                    });
                    particles.appendChild(p);
                }
            }

            // Particules discrètes dans le sidebar
            const sidebarParticles = document.getElementById('sidebarParticles');
            if (sidebarParticles) {
                for (let i = 0; i < 14; i++) {
                    const dot = document.createElement('div');
                    dot.className = 'sidebar-particle';
                    dot.style.width = dot.style.height = (2 + Math.random()*3) + 'px';
                    dot.style.borderRadius = '50%';
                    dot.style.background = 'rgba(127,176,105,0.85)';
                    dot.style.boxShadow = '0 0 8px rgba(127,176,105,0.6)';
                    dot.style.position = 'absolute';
                    dot.style.left = (Math.random()*100) + '%';
                    dot.style.top = (Math.random()*100) + '%';
                    dot.animate([
                        { transform: 'translate(0,0)', opacity: 0.9 },
                        { transform: `translate(${(Math.random()*30-15)}px, ${(Math.random()*30-15)}px)`, opacity: 0.4 }
                    ], { duration: 6000 + Math.random()*6000, direction: 'alternate', iterations: Infinity, easing: 'ease-in-out' });
                    sidebarParticles.appendChild(dot);
                }
            }
        });

        // Gestion du sidebar sur mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });

                // Fermer le sidebar en cliquant à l'extérieur
                document.addEventListener('click', function(e) {
                    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        sidebar.classList.remove('show');
                    }
                });
            }
        });
    </script>
</head>
<body>
    <!-- Particules flottantes pour dashboard moderne -->
    <div class="particles" id="particles"></div>
    
    <!-- Bouton toggle pour mobile -->
    <button class="sidebar-toggle d-md-none" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar border-end p-3">
            <div class="sidebar-bg-overlay"></div>
            <div class="sidebar-overlay-dark"></div>
            <div class="sidebar-particles" id="sidebarParticles"></div>
            <div class="text-center mb-4">
                @php
    $user = Auth::user();
    $sidebarLogo = $user && $user->sidebar_logo ? asset('storage/' . $user->sidebar_logo) : '/photo_2025-07-02_10-44-47.jpg';
@endphp
<img src="{{ $sidebarLogo }}" alt="Logo ADIS" style="max-width:90px; border-radius:12px; box-shadow:0 2px 8px #6366f1aa;">
<form action="{{ route('utilisateur.logo.upload') }}" method="POST" enctype="multipart/form-data" style="margin-top:0.5rem;">
    @csrf
    <label for="logo-upload" class="btn btn-sm btn-outline-primary" style="font-weight:bold;cursor:pointer;">
        <i class="fas fa-upload"></i> Changer le logo
    </label>
    <input id="logo-upload" type="file" name="logo" accept="image/*" style="display:none;" onchange="this.form.submit()">
</form>
<h5 class="mt-2">Espace Apprenant</h5>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item mb-2"><a class="nav-link" href="{{ route('apprenants.dashboard') }}"><i class="fas fa-home me-2"></i>Dashboard</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="/document-test"><i class="fas fa-file-alt me-2"></i>Documents</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="/certificat-test"><i class="fas fa-certificate me-2"></i>Certificats</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="/module-test"><i class="fas fa-layer-group me-2"></i>Modules</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="/mes-demandes-cours-maison"><i class="fas fa-home me-2"></i>Cours à domicile</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="{{ route('achat') }}"><i class="fas fa-shopping-cart me-2"></i>Achat de module</a></li>
<li class="nav-item mb-2"><a class="nav-link" href="{{ route('paiement.page') }}"><i class="fas fa-money-bill-wave me-2"></i>Paiement</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="{{ route('apprenants.presence.index') }}"><i class="fas fa-user-check me-2"></i>Présence</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="{{ route('apprenants.prifil_test') }}"><i class="fas fa-user me-2"></i>Profil</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="{{ route('apprenants.notification_test') }}"><i class="fas fa-bell me-2"></i>Notifications</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="{{ route('apprenants.parametre_test') }}"><i class="fas fa-cog me-2"></i>Paramètres du compte</a></li>
                <li class="nav-item mb-2">
                    
                    <li class="nav-item mb-2"><a class="nav-link" href="{{ route('apprenants.questionnaire_test') }}"><i class="fas fa-question-circle me-2"></i>Questionnaire</a></li>
                </li>
                <li class="nav-item mt-4"><a class="nav-link text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
            </ul>
            <script>
                // Affichage dynamique du sous-menu
                document.addEventListener('DOMContentLoaded', function() {
                    var currentPath = window.location.pathname;
                    var questionnairesLink = document.querySelector('[href="#submenu-questionnaires"]');
                    var submenu = document.getElementById('submenu-questionnaires');
                    if(currentPath.includes('questionnaires')) {
                        submenu.classList.add('show');
                    }
                });
            </script>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
        </nav>
        <!-- Main Content -->
        <div class="main-content">
            @yield('content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>