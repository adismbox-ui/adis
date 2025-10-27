<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formateur - ADIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #1a4d3a;
            --secondary-green: #2d6e4e;
            --accent-green: #3d8b64;
            --light-green: #4da674;
            --dark-green: #0f2a1f;
            --text-light: #e8f5e8;
            --text-muted: #b8d4c2;
            --shadow-dark: rgba(15, 42, 31, 0.3);
            --glow-green: rgba(77, 166, 116, 0.6);
        }

        body { 
            background: linear-gradient(135deg, #0f2a1f 0%, #1a4d3a 50%, #2d6e4e 100%);
            background-image: url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-blend-mode: overlay;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(15, 42, 31, 0.9) 0%, rgba(26, 77, 58, 0.8) 50%, rgba(45, 110, 78, 0.7) 100%);
            z-index: -1;
        }

        /* Sidebar moderne avec scroll */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(15, 42, 31, 0.95) 0%, rgba(26, 77, 58, 0.9) 50%, rgba(45, 110, 78, 0.85) 100%);
            background-image: url('https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2074&q=80');
            background-size: cover;
            background-position: center;
            background-blend-mode: overlay;
            color: var(--text-light);
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            z-index: 100;
            box-shadow: 8px 0 32px rgba(15, 42, 31, 0.4);
            border-radius: 0 25px 25px 0;
            overflow: hidden;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(77, 166, 116, 0.2);
            animation: sidebarFloat 6s ease-in-out infinite;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(15, 42, 31, 0.8) 0%, rgba(26, 77, 58, 0.7) 50%, rgba(45, 110, 78, 0.6) 100%);
            z-index: -1;
        }

        .sidebar-content {
            height: 100vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--light-green) transparent;
            padding: 1rem;
        }

        .sidebar-content::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-content::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-content::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--accent-green), var(--light-green));
            border-radius: 3px;
        }

        .sidebar-content::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--light-green), var(--accent-green));
        }

        @keyframes sidebarFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        .sidebar::after {
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

        .sidebar .nav-link {
            color: var(--text-light);
            font-weight: 500;
            border-radius: 15px;
            margin: 8px 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            padding: 12px 20px;
            background: rgba(45, 110, 78, 0.3);
            border: 1px solid rgba(77, 166, 116, 0.2);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s ease;
        }

        .sidebar .nav-link:hover::before {
            left: 100%;
        }

        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: linear-gradient(135deg, var(--accent-green), var(--light-green));
            color: var(--text-light);
            transform: translateX(8px) scale(1.02);
            box-shadow: 0 8px 25px rgba(77, 166, 116, 0.4);
            border-color: var(--light-green);
        }

        .sidebar .nav-link .fa {
            filter: drop-shadow(0 2px 4px rgba(15, 42, 31, 0.3));
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover .fa {
            transform: scale(1.1) rotate(5deg);
        }

        .sidebar .sidebar-header {
            padding: 1.5rem 1.5rem 1rem 1.5rem;
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: 1px;
            background: rgba(15, 42, 31, 0.4);
            border-radius: 15px;
            margin: 15px 0;
            text-align: center;
            border: 1px solid rgba(77, 166, 116, 0.3);
            backdrop-filter: blur(10px);
        }

        .sidebar .sidebar-footer {
            padding: 1.5rem;
            font-size: 0.9rem;
            color: var(--text-muted);
            border-top: 1px solid rgba(77, 166, 116, 0.2);
            background: rgba(15, 42, 31, 0.4);
            backdrop-filter: blur(10px);
            margin-top: 2rem;
        }

        .sidebar-separator {
            border: none;
            border-top: 2px solid rgba(77, 166, 116, 0.3);
            margin: 1rem 0;
            border-radius: 1px;
        }

        .avatar-glow {
            display: inline-block;
            padding: 8px;
            background: linear-gradient(135deg, var(--accent-green), var(--light-green));
            border-radius: 50%;
            box-shadow: 0 0 0 8px rgba(77, 166, 116, 0.2), 0 8px 25px rgba(15, 42, 31, 0.3);
            animation: avatarPulse 3s ease-in-out infinite;
        }

        @keyframes avatarPulse {
            0%, 100% { 
                box-shadow: 0 0 0 8px rgba(77, 166, 116, 0.2), 0 8px 25px rgba(15, 42, 31, 0.3);
            }
            50% { 
                box-shadow: 0 0 0 12px rgba(77, 166, 116, 0.3), 0 12px 35px rgba(15, 42, 31, 0.4);
            }
        }

        .badge-formateur {
            background: linear-gradient(135deg, var(--accent-green), var(--light-green));
            color: var(--text-light);
            font-size: 0.9rem;
            padding: 0.4em 1em;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(77, 166, 116, 0.3);
            letter-spacing: 0.5px;
            font-weight: 600;
            animation: badgeGlow 2s ease-in-out infinite;
        }

        @keyframes badgeGlow {
            0%, 100% { box-shadow: 0 4px 15px rgba(77, 166, 116, 0.3); }
            50% { box-shadow: 0 6px 20px rgba(77, 166, 116, 0.5); }
        }

        .main-content {
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
            position: relative;
        }

        /* Animation d'apparition pour les éléments du sidebar */
        .sidebar .nav-link {
            animation: slideInLeft 0.6s ease-out;
        }

        .sidebar .nav-link:nth-child(1) { animation-delay: 0.1s; }
        .sidebar .nav-link:nth-child(2) { animation-delay: 0.2s; }
        .sidebar .nav-link:nth-child(3) { animation-delay: 0.3s; }
        .sidebar .nav-link:nth-child(4) { animation-delay: 0.4s; }
        .sidebar .nav-link:nth-child(5) { animation-delay: 0.5s; }
        .sidebar .nav-link:nth-child(6) { animation-delay: 0.6s; }
        .sidebar .nav-link:nth-child(7) { animation-delay: 0.7s; }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive design */
        @media (max-width: 991px) {
            .sidebar { 
                width: 80px; 
                border-radius: 0 15px 15px 0;
            }
            .main-content { margin-left: 80px; }
            .sidebar .sidebar-header, .sidebar .sidebar-footer, .sidebar .nav-link span { display: none; }
            .sidebar .nav-link { 
                justify-content: center;
                margin: 5px 0;
                padding: 12px;
            }
            .avatar-glow {
                padding: 6px;
            }
            .avatar-glow img {
                width: 50px !important;
                height: 50px !important;
            }
        }

        /* Effet de hover amélioré */
        .sidebar .nav-link:hover {
            transform: translateX(8px) scale(1.02) rotate(1deg);
        }

        /* Animation pour l'icône active */
        .sidebar .nav-link.active .fa {
            animation: iconBounce 0.6s ease-out;
        }

        @keyframes iconBounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        /* Animations pour le contenu principal */
        .main-content {
            animation: fadeInUp 0.8s ease-out;
        }

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

        /* Particules flottantes */
        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--light-green);
            border-radius: 50%;
            opacity: 0.6;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) translateX(0px);
                opacity: 0.6;
            }
            50% { 
                transform: translateY(-20px) translateX(10px);
                opacity: 1;
            }
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
    </style>
</head>
<body>
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

    <div class="sidebar">
        <div class="sidebar-content">
            <div class="text-center mt-4 mb-3">
                <div class="avatar-glow mx-auto mb-3">
                    @php
                    $user = Auth::user();
                    $sidebarLogo = $user && $user->sidebar_logo ? asset('storage/' . $user->sidebar_logo) : '/photo_2025-07-02_10-44-47.jpg';
                    @endphp
                    <img src="{{ $sidebarLogo }}" alt="Logo ADIS" class="rounded-circle border border-3 border-light shadow" style="width:90px; height:90px; object-fit:cover;">
                    <form action="{{ route('utilisateur.logo.upload') }}" method="POST" enctype="multipart/form-data" style="margin-top:0.8rem;">
                        @csrf
                        <label for="logo-upload" class="btn btn-sm btn-outline-light" style="font-weight:600;cursor:pointer;border-radius:20px;padding:0.4rem 1rem;">
                            <i class="fas fa-upload me-1"></i> Changer le logo
                        </label>
                        <input id="logo-upload" type="file" name="logo" accept="image/*" style="display:none;" onchange="this.form.submit()">
                    </form>
                </div>
                <div class="fw-bold mt-3" style="font-size:1.2rem; letter-spacing:0.5px;color:var(--text-light);">
                    Bienvenue, {{ Auth::user()->prenom ?? 'Formateur' }} !
                </div>
                <span class="badge badge-formateur mt-2">Formateur</span>
            </div>
            <hr class="sidebar-separator my-3">
            <div class="sidebar-header text-center mb-3">
                <i class="fas fa-chalkboard-teacher me-2"></i>ADIS
            </div>
            <nav class="nav flex-column px-3 mt-2">
                <a class="nav-link {{ request()->is('formateurs/dashboard') ? 'active' : '' }}" href="{{ route('formateurs.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-3"></i> <span>Dashboard</span>
                </a>
                <a class="nav-link" href="{{ route('modules.index') }}">
                    <i class="fas fa-layer-group me-3"></i> <span>Mes niveaux</span>
                </a>
                <a class="nav-link {{ request()->is('formateurs/documents') ? 'active' : '' }}" href="{{ route('formateurs.documents') }}">
                    <i class="fas fa-file-alt me-3"></i> <span>Documents</span>
                </a>
                <a class="nav-link {{ request()->is('formateurs/questionnaires') ? 'active' : '' }}" href="{{ route('formateurs.questionnaires') }}">
                    <i class="fas fa-question-circle me-3"></i> <span>Questionnaires</span>
                </a>
                <a class="nav-link {{ request()->is('formateurs/presence*') ? 'active' : '' }}" href="{{ route('formateurs.presence.index') }}">
                    <i class="fas fa-user-check me-3"></i> <span>Présence</span>
                </a>
                <a class="nav-link {{ request()->is('formateurs/apprenants*') ? 'active' : '' }}" href="{{ route('formateurs.apprenants.index') }}">
                    <i class="fas fa-users me-3"></i> <span>Apprenants</span>
                </a>
                <a class="nav-link {{ request()->is('validation-cours-domicile*') ? 'active' : '' }}" href="{{ route('validation_cours_domicile.index') }}">
                    <i class="fas fa-home me-3"></i> <span>Cours à domicile</span>
                </a>
                <a class="nav-link {{ request()->is('formateurs/profil*') ? 'active' : '' }}" href="{{ route('formateurs.profil') }}">
                    <i class="fas fa-user me-3"></i> <span>Profil</span>
                </a>
                <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-3"></i> <span>Déconnexion</span>
                </a>
            </nav>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
            <div class="sidebar-footer">
                <div class="text-center">
                    <small>© 2024 ADIS - Tous droits réservés</small>
                </div>
            </div>
        </div>
    </div>
    <div class="main-content">
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 