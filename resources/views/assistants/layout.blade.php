<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant - ADIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        :root {
            --primary-green: #2d5016;
            --secondary-green: #4a7c59;
            --accent-green: #7fb069;
            --light-green: #a7c957;
            --bg-green: #bc4749;
            --white-glass: rgba(255, 255, 255, 0.95);
            --green-glass: rgba(127, 176, 105, 0.2);
            --dark-bg: #1a1a1a;
            --darker-bg: #0f0f0f;
            --card-bg: rgba(255, 255, 255, 0.1);
            --text-light: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.7);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2d5016 0%, #4a7c59 50%, #7fb069 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Fond animé */
        .animated-background {
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            z-index: -2;
            background: url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80') center/cover no-repeat;
            animation: slowZoom 20s ease-in-out infinite alternate;
        }
        .animated-background::before {
            content: '';
            position: absolute; 
            top: 0; 
            left: 0; 
            right: 0; 
            bottom: 0;
            background: linear-gradient(135deg, rgba(45,80,22,0.8) 0%, rgba(74,124,89,0.7) 50%, rgba(127,176,105,0.6) 100%);
            z-index: 1;
        }
        @keyframes slowZoom { 
            0% { transform: scale(1); } 
            100% { transform: scale(1.1); } 
        }
        
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
            background: rgba(167, 201, 87, 0.6); 
            border-radius: 50%; 
            animation: float 15s infinite linear; 
        }
        @keyframes float { 
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; } 
            10% { opacity: 1; } 
            90% { opacity: 1; } 
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; } 
        }

        /* Sidebar sombre et moderne */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(160deg, #111 0%, #1a1a1a 60%, rgba(45,80,22,0.25) 100%);
            color: var(--text-light);
            position: fixed;
            left: 0;
            top: 0;
            width: 220px;
            z-index: 1000;
            box-shadow: 2px 0 24px rgba(0, 0, 0, 0.4);
            overflow-y: auto;
            max-height: 100vh;
            animation: slideInLeft 0.8s ease-out;
            border-right: 1px solid rgba(127, 176, 105, 0.3);
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .sidebar .nav-link {
            color: var(--text-light);
            font-weight: 500;
            border-radius: 0 30px 30px 0;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            padding: 12px 18px;
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(127, 176, 105, 0.3), transparent);
            transition: left 0.5s;
        }

        .sidebar .nav-link:hover::before {
            left: 100%;
        }

        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: rgba(127, 176, 105, 0.2);
            color: var(--text-light);
            transform: translateX(8px);
            box-shadow: 0 4px 12px rgba(127, 176, 105, 0.3);
            border-left: 3px solid var(--accent-green);
        }

        .sidebar .sidebar-header {
            padding: 2rem 1.5rem 1rem 1.5rem;
            font-size: 1.3rem;
            font-weight: bold;
            letter-spacing: 1px;
            background: rgba(0,0,0,0.3);
            border-bottom: 1px solid rgba(127, 176, 105, 0.3);
            position: relative;
            overflow: hidden;
        }

        .sidebar .sidebar-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-green), var(--light-green));
            animation: shimmer 2s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { transform: translateX(-100%); }
            50% { transform: translateX(100%); }
        }

        .sidebar-section {
            border-top: 1px solid rgba(127, 176, 105, 0.2);
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .sidebar-section:first-child {
            border-top: none;
            padding-top: 0;
            margin-top: 0;
        }

        .sidebar-section small {
            color: var(--accent-green) !important;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 1px 4px rgba(0,0,0,0.25);
            font-size: 0.85rem;
            padding-left: 1rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        /* Main content avec glassmorphism sombre */
        .main-content {
            margin-left: 220px;
            padding: 2rem;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* Cards avec effet glassmorphism sombre */
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            color: var(--text-light);
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-green), var(--light-green));
            animation: shimmer 3s ease-in-out infinite;
        }

        .glass-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(127, 176, 105, 0.2);
            background: rgba(255, 255, 255, 0.15);
        }

        /* Boutons avec animations */
        .btn-gradient {
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

        .btn-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-gradient:hover::before {
            left: 100%;
        }

        .btn-gradient:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 25px rgba(127, 176, 105, 0.4);
        }

        /* Animations d'entrée */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 991px) {
            .sidebar { 
                width: 80px; 
                transition: width 0.3s ease;
            }
            .main-content { 
                margin-left: 80px; 
                padding: 1rem;
            }
            .sidebar .sidebar-header, .sidebar .nav-link span { 
                display: none; 
            }
            .sidebar .nav-link { 
                justify-content: center; 
                padding: 12px;
            }
            .sidebar .nav-link i {
                font-size: 1.2rem;
            }
        }

        /* Scroll personnalisé pour le sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(127, 176, 105, 0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(127, 176, 105, 0.5);
        }

        /* Styles communs pour toutes les pages assistant */
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

        .btn-secondary-gradient {
            background: linear-gradient(135deg, var(--secondary-green) 0%, var(--accent-green) 100%);
            color: var(--text-light);
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-secondary-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-secondary-gradient:hover::before {
            left: 100%;
        }

        .btn-secondary-gradient:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 25px rgba(74, 124, 89, 0.4);
        }

        .status-badge {
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

        .alert {
            border-radius: 15px;
            border: none;
            animation: fadeInSlide 0.8s ease-out;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            color: var(--text-light);
        }

        /* Tableaux modernes sombres */
        .table-modern {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .table-modern thead th {
            background: linear-gradient(135deg, rgba(127, 176, 105, 0.2) 0%, rgba(167, 201, 87, 0.2) 100%);
            color: var(--accent-green);
            font-weight: 600;
            border: none;
            padding: 1rem;
        }

        .table-modern tbody tr {
            transition: all 0.3s ease;
            color: var(--text-light);
        }

        .table-modern tbody tr:hover {
            background: linear-gradient(135deg, rgba(127, 176, 105, 0.1) 0%, rgba(167, 201, 87, 0.1) 100%);
            transform: scale(1.01);
        }

        .table-modern tbody td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
            color: var(--text-light);
        }

        /* Styles pour les tableaux modernes avec animations */
        .modern-table {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            animation: slideInUp 0.8s ease-out;
        }

        .modern-table thead th {
            background: linear-gradient(135deg, rgba(127, 176, 105, 0.2) 0%, rgba(167, 201, 87, 0.2) 100%);
            color: var(--accent-green);
            font-weight: 600;
            border: none;
            padding: 1.2rem 1rem;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(127, 176, 105, 0.2);
            color: var(--text-light);
        }

        .modern-table tbody tr:last-child {
            border-bottom: none;
        }

        .modern-table tbody tr:hover {
            background: linear-gradient(135deg, rgba(127, 176, 105, 0.1) 0%, rgba(167, 201, 87, 0.1) 100%);
            transform: scale(1.01);
            box-shadow: 0 4px 15px rgba(127, 176, 105, 0.2);
        }

        .modern-table tbody td {
            border: none;
            padding: 1.2rem 1rem;
            vertical-align: middle;
            color: var(--text-light);
        }

        /* Boutons d'action modernes */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn-action::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-action:hover::before {
            left: 100%;
        }

        .btn-edit {
            background: linear-gradient(135deg, var(--accent-green) 0%, var(--light-green) 100%);
            color: white;
        }

        .btn-edit:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 20px rgba(127, 176, 105, 0.3);
        }

        .btn-delete {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
        }

        .btn-delete:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
        }

        .btn-view {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }

        .btn-view:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
        }

        /* Badges modernes */
        .badge-modern {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .badge-modern:hover {
            transform: translateY(-2px) scale(1.05);
        }

        .badge-success {
            background: linear-gradient(135deg, var(--accent-green), var(--light-green));
            color: white;
        }

        .badge-warning {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }

        .badge-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .badge-info {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        /* Animations pour les éléments de tableau */
        .table-row-animate {
            animation: slideInRight 0.6s ease-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Responsive pour les tableaux */
        @media (max-width: 768px) {
            .modern-table {
                font-size: 0.9rem;
            }
            
            .modern-table thead th,
            .modern-table tbody td {
                padding: 0.8rem 0.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 0.3rem;
            }
            
            .btn-action {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
        }

        /* Textes et couleurs sombres */
        .text-dark {
            color: var(--text-light) !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            color: var(--text-light);
        }

        .card-header {
            background: rgba(127, 176, 105, 0.1);
            border-bottom: 1px solid rgba(127, 176, 105, 0.2);
            color: var(--text-light);
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-light);
            backdrop-filter: blur(10px);
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--accent-green);
            color: var(--text-light);
            box-shadow: 0 0 0 0.2rem rgba(127, 176, 105, 0.25);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <!-- Fond animé -->
    <div class="animated-background"></div>
    <!-- Particules flottantes -->
    <div class="floating-particles" id="particles"></div>

    <!-- SIDEBAR ASSISTANT -->
    <div class="sidebar" id="assistantSidebar">
        <div class="sidebar-header text-center">
            @php
                $user = Auth::user();
                $sidebarLogo = $user && $user->sidebar_logo ? asset('storage/' . $user->sidebar_logo) : '/photo_2025-07-02_10-44-47.jpg';
            @endphp
            <img src="{{ $sidebarLogo }}" alt="Logo ADIS" style="max-width:60px; border-radius:14px; box-shadow:0 4px 12px rgba(127, 176, 105, 0.3); margin-bottom:0.7rem;">
            <form action="{{ route('utilisateur.logo.upload') }}" method="POST" enctype="multipart/form-data" style="margin-top:0.5rem;">
                @csrf
                <label for="logo-upload" class="btn btn-sm btn-outline-light" style="font-weight:bold;cursor:pointer;">
                    <i class="fas fa-upload"></i> Changer le logo
                </label>
                <input id="logo-upload" type="file" name="logo" accept="image/*" style="display:none;" onchange="this.form.submit()">
            </form>
            <div>Assistant Panel</div>
            <small>Gestion Assistant</small>
        </div>
        <nav class="nav flex-column px-2 mt-4">
            <!-- Dashboard -->
            <a href="{{ route('assistant.dashboard') }}" class="nav-link {{ request()->is('assistant/dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt me-2"></i>
                <span>Tableau de bord</span>
            </a>
            <!-- GESTION FORMATIONS (sans entrée Formations) -->
            <div class="sidebar-section mt-3">
                <small class="text-muted px-3 mb-2 d-block">GESTION FORMATIONS</small>
                <a href="{{ route('assistant.niveaux') }}" class="nav-link {{ request()->is('assistant/niveaux*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group me-2"></i>
                    <span>Niveaux</span>
                </a>
                <a href="{{ route('assistant.sessions') }}" class="nav-link {{ request()->is('assistant/sessions*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <span>Sessions</span>
                </a>
                <a href="{{ route('assistant.calendrier') }}" class="nav-link {{ request()->is('assistant/calendrier*') ? 'active' : '' }}">
                    <i class="fas fa-calendar me-2"></i>
                    <span>Calendrier</span>
                </a>
                <a href="{{ route('assistant.vacances') }}" class="nav-link {{ request()->is('assistant/vacances*') ? 'active' : '' }}">
                    <i class="fas fa-umbrella-beach me-2"></i>
                    <span>Vacances</span>
                </a>
                <a href="{{ route('assistant.modules') }}" class="nav-link {{ request()->is('assistant/modules*') ? 'active' : '' }}">
                    <i class="fas fa-book me-2"></i>
                    <span>Modules</span>
                </a>
            </div>
            <!-- GESTION APPRENANTS -->
            <div class="sidebar-section">
                <small class="text-muted px-3 mb-2 d-block">GESTION APPRENANTS</small>
                <a href="{{ route('assistant.cours_domicile') }}" class="nav-link {{ request()->is('assistant/cours-a-domicile*') ? 'active' : '' }}">
                    <i class="fas fa-home me-2"></i>
                    <span>Cours à domicile</span>
                </a>
                <a href="{{ route('assistant.inscriptions') }}" class="nav-link {{ request()->is('assistant/inscriptions*') ? 'active' : '' }}">
                    <i class="fas fa-user-plus me-2"></i>
                    <span>Inscriptions</span>
                </a>
                <a href="{{ route('assistant.paiements') }}" class="nav-link {{ request()->is('assistant/paiements*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    <span>Paiements</span>
                </a>
                <a href="{{ route('assistant.certificats') }}" class="nav-link {{ request()->is('assistant/certificats*') ? 'active' : '' }}">
                    <i class="fas fa-certificate me-2"></i>
                    <span>Certificats</span>
                </a>
                <a href="{{ route('assistant.documents') }}" class="nav-link {{ request()->is('assistant/documents*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt me-2"></i>
                    <span>Documents</span>
                </a>
                <a href="{{ route('assistant.questionnaires') }}" class="nav-link {{ request()->is('assistant/questionnaires*') ? 'active' : '' }}">
                    <i class="fas fa-question-circle me-2"></i>
                    <span>Questionnaires</span>
                </a>
            </div>
            <!-- DÉCONNEXION -->
            <div class="sidebar-section mt-auto">
                <a href="{{ route('logout') }}" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    <span>Déconnexion</span>
                </a>
            </div>
        </nav>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Particules flottantes dynamiques
        (function() {
            const particlesContainer = document.getElementById('particles');
            for (let i = 0; i < 18; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                p.style.left = Math.random() * 100 + '%';
                p.style.width = p.style.height = (8 + Math.random()*16) + 'px';
                p.style.animationDelay = Math.random() * 10 + 's';
                p.style.animationDuration = (Math.random() * 8 + 8) + 's';
                particlesContainer.appendChild(p);
            }
        })();

        // Animation au scroll
        document.addEventListener('DOMContentLoaded', function() {
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

            // Animation des liens du sidebar
            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(8px) scale(1.05)';
                });
                
                link.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html> 