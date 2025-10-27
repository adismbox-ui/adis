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

    /* Styles de base avec image de fond améliorée */
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

    /* Animations globales améliorées */
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

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
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

    @keyframes glow {
        0%, 100% { box-shadow: 0 0 20px var(--glow-green); }
        50% { box-shadow: 0 0 40px var(--glow-green), 0 0 60px var(--glow-green); }
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    /* Carte principale avec effets avancés */
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

    /* En-tête de carte avec animation */
    .card-header-modern {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .header-title {
        color: var(--text-light);
        font-size: 2.5rem;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 2;
        animation: titleGlow 3s ease-in-out infinite;
    }

    @keyframes titleGlow {
        0%, 100% { text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        50% { text-shadow: 2px 2px 8px var(--glow-green); }
    }

    /* Barre de recherche moderne avec animations */
    .search-container {
        background: rgba(15, 42, 31, 0.8);
        border-radius: 15px;
        padding: 1.5rem;
        margin: 2rem 0;
        border: 1px solid rgba(77, 166, 116, 0.3);
        backdrop-filter: blur(10px);
        animation: fadeInUp 0.8s ease-out;
        position: relative;
        overflow: hidden;
    }

    .search-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(77, 166, 116, 0.05), transparent);
        animation: shine 6s ease-in-out infinite;
    }

    .search-input-group {
        position: relative;
        max-width: 600px;
        margin: 0 auto;
    }

    .search-input {
        background: rgba(26, 77, 58, 0.8);
        border: 2px solid rgba(77, 166, 116, 0.3);
        border-radius: 25px;
        color: var(--text-light);
        padding: 1rem 1.5rem 1rem 3rem;
        font-size: 1rem;
        width: 100%;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .search-input:focus {
        background: rgba(45, 110, 78, 0.9);
        border-color: var(--light-green);
        box-shadow: 0 0 25px var(--glow-green);
        color: var(--text-light);
        outline: none;
        animation: inputGlow 2s ease-in-out infinite;
    }

    @keyframes inputGlow {
        0%, 100% { box-shadow: 0 0 25px var(--glow-green); }
        50% { box-shadow: 0 0 35px var(--glow-green), 0 0 45px var(--glow-green); }
    }

    .search-input::placeholder {
        color: var(--text-muted);
        font-style: italic;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--light-green);
        font-size: 1.2rem;
        z-index: 10;
        animation: searchPulse 2s ease-in-out infinite;
    }

    @keyframes searchPulse {
        0%, 100% { opacity: 0.7; transform: translateY(-50%) scale(1); }
        50% { opacity: 1; transform: translateY(-50%) scale(1.1); }
    }

    .search-filters {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .filter-btn {
        background: rgba(45, 110, 78, 0.6);
        border: 1px solid rgba(77, 166, 116, 0.4);
        color: var(--text-light);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .filter-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.5s ease;
    }

    .filter-btn:hover::before {
        left: 100%;
    }

    .filter-btn:hover, .filter-btn.active {
        background: linear-gradient(135deg, var(--accent-green), var(--light-green));
        border-color: var(--light-green);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(77, 166, 116, 0.3);
    }

    /* Navigation par onglets avec animations */
    .nav-tabs-modern {
        border-bottom: 2px solid rgba(77, 166, 116, 0.3);
        margin-bottom: 2rem;
    }

    .nav-tabs-modern .nav-link {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-weight: 600;
        padding: 1rem 2rem;
        border-radius: 10px 10px 0 0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .nav-tabs-modern .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(77, 166, 116, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .nav-tabs-modern .nav-link:hover::before {
        left: 100%;
    }

    .nav-tabs-modern .nav-link:hover {
        color: var(--text-light);
        background: rgba(77, 166, 116, 0.1);
    }

    .nav-tabs-modern .nav-link.active {
        color: var(--text-light);
        background: linear-gradient(135deg, var(--accent-green), var(--light-green));
        border-bottom: 3px solid var(--light-green);
        animation: tabGlow 2s ease-in-out infinite;
    }

    @keyframes tabGlow {
        0%, 100% { box-shadow: 0 0 10px rgba(77, 166, 116, 0.3); }
        50% { box-shadow: 0 0 20px rgba(77, 166, 116, 0.5); }
    }

    /* Cartes de module avec effets avancés */
    .module-card-compact {
        background: rgba(45, 110, 78, 0.9);
        border: 1px solid rgba(77, 166, 116, 0.4);
        border-radius: 15px;
        margin-bottom: 1rem;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        animation: slideIn 0.6s ease-out;
        position: relative;
    }

    .module-card-compact::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(77, 166, 116, 0.05), transparent);
        animation: shine 4s ease-in-out infinite;
    }

    .module-card-compact:hover {
        transform: translateY(-3px) scale(1.01);
        border-color: var(--light-green);
        box-shadow: 0 10px 30px rgba(15, 42, 31, 0.4);
        animation: cardFloat 0.6s ease-out;
    }

    @keyframes cardFloat {
        0% { transform: translateY(0px) scale(1); }
        50% { transform: translateY(-5px) scale(1.02); }
        100% { transform: translateY(-3px) scale(1.01); }
    }

    .module-header-compact {
        background: linear-gradient(135deg, var(--accent-green) 0%, var(--light-green) 100%);
        padding: 1rem;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .module-title-compact {
        color: var(--text-light);
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0;
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .module-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .module-content.expanded {
        max-height: 1000px;
        animation: expandContent 0.3s ease-out;
    }

    @keyframes expandContent {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Badges animés */
    .badge-animated {
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

    .badge-animated::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }

    .badge-animated:hover::before {
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

    /* Tableaux modernes compacts */
    .table-modern-compact {
        background: rgba(15, 42, 31, 0.6);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: inset 0 0 20px rgba(0,0,0,0.2);
        font-size: 0.9rem;
    }

    .table-modern-compact th {
        background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        color: var(--text-light);
        font-weight: 600;
        padding: 0.75rem;
        border: none;
        position: relative;
        font-size: 0.85rem;
    }

    .table-modern-compact td {
        background: rgba(26, 77, 58, 0.8);
        color: var(--text-light);
        padding: 0.75rem;
        border: 1px solid rgba(77, 166, 116, 0.2);
        transition: all 0.3s ease;
    }

    .table-modern-compact tbody tr:hover td {
        background: rgba(45, 110, 78, 0.9);
        transform: scale(1.01);
    }

    /* Boutons interactifs */
    .btn-modern {
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
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

    .btn-success-modern {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white;
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
    }

    .btn-success-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.6);
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, var(--light-green), var(--accent-green));
        color: white;
        box-shadow: 0 4px 15px var(--glow-green);
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px var(--glow-green);
    }

    .btn-outline-modern {
        background: transparent;
        border: 2px solid var(--light-green);
        color: var(--light-green);
    }

    .btn-outline-modern:hover {
        background: var(--light-green);
        color: white;
        transform: scale(1.05);
    }

    /* Formulaires modernes compacts */
    .form-modern-compact {
        background: rgba(15, 42, 31, 0.6);
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid rgba(77, 166, 116, 0.3);
        margin: 1rem 0;
        backdrop-filter: blur(10px);
    }

    .form-control-modern {
        background: rgba(26, 77, 58, 0.8);
        border: 2px solid rgba(77, 166, 116, 0.3);
        border-radius: 8px;
        color: var(--text-light);
        padding: 0.6rem 0.8rem;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .form-control-modern:focus {
        background: rgba(45, 110, 78, 0.9);
        border-color: var(--light-green);
        box-shadow: 0 0 20px var(--glow-green);
        color: var(--text-light);
    }

    .form-control-modern::placeholder {
        color: var(--text-muted);
    }

    .form-label-modern {
        color: var(--text-light);
        font-weight: 600;
        margin-bottom: 0.4rem;
        display: block;
        font-size: 0.9rem;
    }

    /* Sections avec icônes animées */
    .section-title-compact {
        color: var(--text-light);
        font-size: 1.4rem;
        font-weight: 700;
        margin: 1.5rem 0 1rem 0;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .section-icon {
        width: 35px;
        height: 35px;
        background: linear-gradient(135deg, var(--accent-green), var(--light-green));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulse 2s infinite;
        box-shadow: 0 4px 15px var(--glow-green);
    }

    /* Alertes modernes */
    .alert-modern {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 10px;
        color: #fbbf24;
        padding: 1.5rem;
        margin: 1rem 0;
        animation: fadeInUp 0.6s ease-out;
    }

    /* Stats cards avec animations */
    .stats-card {
        background: rgba(45, 110, 78, 0.8);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        border: 1px solid rgba(77, 166, 116, 0.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.5s ease;
    }

    .stats-card:hover::before {
        left: 100%;
    }

    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(15, 42, 31, 0.4);
        animation: statsGlow 2s ease-in-out infinite;
    }

    @keyframes statsGlow {
        0%, 100% { box-shadow: 0 10px 30px rgba(15, 42, 31, 0.4); }
        50% { box-shadow: 0 15px 40px rgba(77, 166, 116, 0.3); }
    }

    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--light-green);
        margin-bottom: 0.5rem;
        animation: numberCount 2s ease-out;
    }

    @keyframes numberCount {
        from { opacity: 0; transform: scale(0.5); }
        to { opacity: 1; transform: scale(1); }
    }

    .stats-label {
        color: var(--text-light);
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .header-title {
            font-size: 2rem;
        }
        
        .main-card {
            margin: 1rem;
        }
        
        .form-modern-compact {
            padding: 1rem;
        }
        
        .nav-tabs-modern .nav-link {
            padding: 0.8rem 1rem;
            font-size: 0.9rem;
        }

        .search-filters {
            flex-direction: column;
            align-items: center;
        }
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

    /* Icône de toggle avec animation */
    .toggle-icon {
        transition: transform 0.3s ease;
    }

    .toggle-icon.rotated {
        transform: rotate(180deg);
        animation: iconRotate 0.3s ease-out;
    }

    @keyframes iconRotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(180deg); }
    }
    
    /* Styles pour les boutons de navigation entre rôles */
    .gap-2 {
        gap: 0.5rem !important;
    }
    
    .btn-info {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        border: none;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
    }
    
    .btn-info:hover {
        background: linear-gradient(135deg, #0284c7, #0369a1);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.5);
        color: white;
    }
    
    .btn-success {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        border: none;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, #16a34a, #15803d);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.5);
        color: white;
    }
</style>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="main-card shadow-lg mb-4">
                <div class="card-header-modern text-center">
                    <h2 class="header-title mb-0">
                        <i class="fas fa-chalkboard-teacher me-3"></i>
                        Tableau de bord Formateur
                    </h2>
                </div>
                <div class="px-4 pt-3 d-flex justify-content-end gap-2">
                    @if(isset($user) && $user && $user->apprenant)
                        <a href="{{ route('apprenants.dashboard') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-exchange-alt me-1"></i>
                            Aller à l'espace Apprenant
                        </a>
                    @endif
                    @if(isset($assistant) && $assistant)
                        <a href="{{ route('assistant.dashboard') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-user-tie me-1"></i>
                            Aller à l'espace Assistant
                        </a>
                    @endif
                </div>
                
                <div class="card-body p-4">
                    <!-- Statistiques rapides -->
                    <div class="row mb-4 animate-on-scroll">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ $niveaux->count() }}</div>
                                <div class="stats-label">Niveaux</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ $niveaux->sum(function($niveau) { return $niveau->modules->sum(function($module) { return $module->inscriptions->count(); }); }) }}</div>
                                <div class="stats-label">Étudiants</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ $niveaux->sum(function($niveau) { return $niveau->modules->sum(function($module) { return $module->documents->count(); }); }) }}</div>
                                <div class="stats-label">Documents</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="stats-card">
                                <div class="stats-number">{{ $niveaux->where('lien_meet', '!=', null)->count() }}</div>
                                <div class="stats-label">Liens Meet</div>
                            </div>
                        </div>
                    </div>

                    <!-- Barre de recherche -->
                    <div class="search-container animate-on-scroll">
                        <div class="text-center mb-3">
                            <h5 class="text-light mb-0">
                                <i class="fas fa-search me-2"></i>
                                Rechercher dans vos niveaux
                            </h5>
                        </div>
                        <div class="search-input-group">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control search-input" 
                                   id="searchModules" 
                                   placeholder="Rechercher par nom de niveau, description, ou contenu..." 
                                   autocomplete="off">
                        </div>
                        <div class="search-filters">
                            <button class="filter-btn active" data-filter="all">
                                <i class="fas fa-list me-1"></i>Tous
                            </button>
                            <button class="filter-btn" data-filter="with-meet">
                                <i class="fas fa-video me-1"></i>Avec Meet
                            </button>
                            <button class="filter-btn" data-filter="with-documents">
                                <i class="fas fa-file-alt me-1"></i>Avec Documents
                            </button>
                            <button class="filter-btn" data-filter="with-students">
                                <i class="fas fa-users me-1"></i>Avec Étudiants
                            </button>
                        </div>
                    </div>

                    <!-- Navigation par onglets -->
                    <ul class="nav nav-tabs nav-tabs-modern" id="dashboardTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="modules-tab" data-bs-toggle="tab" data-bs-target="#modules" type="button" role="tab">
                                <i class="fas fa-layer-group me-2"></i>Mes Niveaux
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab">
                                <i class="fas fa-upload me-2"></i>Upload Général
                            </button>
                        </li>
                    </ul>

                    <!-- Contenu des onglets -->
                    <div class="tab-content" id="dashboardTabContent">
                        <!-- Onglet Modules -->
                        <div class="tab-pane fade show active" id="modules" role="tabpanel">
                    @forelse($niveaux as $niveau)
                                <div class="module-card-compact animate-on-scroll" data-module="{{ strtolower($niveau->nom) }}" data-has-meet="{{ $niveau->lien_meet ? 'true' : 'false' }}" data-has-documents="{{ $niveau->modules->sum(function($module) { return $module->documents->count(); }) > 0 ? 'true' : 'false' }}" data-has-students="{{ $niveau->modules->sum(function($module) { return $module->inscriptions->count(); }) > 0 ? 'true' : 'false' }}">
                                    <div class="module-header-compact" onclick="toggleModule({{ $niveau->id }})">
                                        <div class="module-title-compact">
                                <div>
                                                <strong>{{ $niveau->nom }}</strong>
                                                <div class="mt-1">
@if($niveau->lien_meet)
    <span class="badge-animated badge-success">
        <i class="fas fa-video me-1"></i>Meet
    </span>
    <a href="{{ $niveau->lien_meet }}" target="_blank" class="btn btn-success-modern btn-sm ms-2" style="vertical-align: middle;">
        <i class="fas fa-external-link-alt me-1"></i>Rejoindre le cours
    </a>
@endif
@if($niveau->sessionFormation)
                                                        <span class="badge-animated badge-info">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            {{ $niveau->sessionFormation->nom }}
                                                        </span>
@else
                                                        <span class="badge-animated badge-danger">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                                            Session non définie
                                                        </span>
@endif
                                </div>
                            </div>
                                            <i class="fas fa-chevron-down toggle-icon" id="toggle-{{ $niveau->id }}"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="module-content" id="content-{{ $niveau->id }}">
                                        <div class="p-3">
                                            <!-- Section Modules du niveau -->
                                            <div class="section-title-compact">
                                                <div class="section-icon">
                                                    <i class="fas fa-book text-white"></i>
                                                </div>
                                                Modules du niveau ({{ $niveau->modules->count() }})
                                            </div>
                                            
                                            @forelse($niveau->modules as $module)
                                                <div class="form-modern-compact mb-3">
                                                    <h6 class="text-light mb-3">
                                                        <i class="fas fa-book me-2"></i>
                                                        {{ $module->titre }}
                                                    </h6>
                                                    
                                                    <!-- Documents du module -->
                                                    <div class="mb-3">
                                                        <h6 class="text-light mb-2">
                                                            <i class="fas fa-file-alt me-2"></i>
                                                            Documents ({{ $module->documents->count() }})
                                                        </h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-modern-compact align-middle">
                                                                <thead>
                                                                    <tr>
                                                                        <th><i class="fas fa-file me-1"></i>Titre</th>
                                                                        <th><i class="fas fa-tag me-1"></i>Type</th>
                                                                        <th><i class="fas fa-download me-1"></i>Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($module->documents as $doc)
                                                                        <tr>
                                                                            <td>{{ $doc->titre }}</td>
                                                                            <td>{{ $doc->type ?? '-' }}</td>
                                                                            <td>
                                                                                <a href="{{ asset('storage/' . $doc->fichier) }}" 
                                                                                   class="btn btn-outline-modern btn-sm" download>
                                                                                    <i class="fas fa-download me-1"></i>Télécharger
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="3" class="text-center text-muted">
                                                                                <i class="fas fa-folder-open me-2"></i>
                                                                                Aucun document proposé
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Étudiants du module -->
                                                    <div class="mb-3">
                                                        <h6 class="text-light mb-2">
                                                            <i class="fas fa-users me-2"></i>
                                                            Étudiants inscrits ({{ $module->inscriptions->count() }})
                                                        </h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-modern-compact align-middle">
                                                                <thead>
                                                                    <tr>
                                                                        <th><i class="fas fa-user me-1"></i>Nom</th>
                                                                        <th><i class="fas fa-user me-1"></i>Prénom</th>
                                                                        <th><i class="fas fa-envelope me-1"></i>Email</th>
                                                                        <th><i class="fas fa-star me-1"></i>Points</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($module->inscriptions as $inscription)
                                                                        <tr>
                                                                            <td>{{ $inscription->apprenant->utilisateur->nom ?? '-' }}</td>
                                                                            <td>{{ $inscription->apprenant->utilisateur->prenom ?? '-' }}</td>
                                                                            <td>{{ $inscription->apprenant->utilisateur->email ?? '-' }}</td>
                                                                            <td>
                                                                                <span class="badge-animated badge-success">
                                                                                    <i class="fas fa-trophy me-1"></i>
                                                                                    {{ $inscription->points }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="4" class="text-center text-muted">
                                                                                <i class="fas fa-user-slash me-2"></i>
                                                                                Aucun étudiant inscrit
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="alert-modern">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Aucun module associé à ce niveau.
                                                </div>
                                            @endforelse
                                            
                                            <!-- Formulaire d'upload pour le niveau -->
                                            <div class="form-modern-compact">
                                                <h6 class="text-light mb-3">
                                                    <i class="fas fa-cloud-upload-alt me-2"></i>
                                                    Ajouter un document au niveau
                                                </h6>
                                                <form action="{{ route('admin.documents.store') }}" method="POST" 
                                                      enctype="multipart/form-data" class="row g-2 align-items-end">
                                                    @csrf
                                                    <input type="hidden" name="niveau_id" value="{{ $niveau->id }}">
                                                    
                                                    <div class="col-md-4">
                                                        <label class="form-label-modern">Titre</label>
                                                        <input type="text" name="titre" class="form-control form-control-modern" 
                                                               placeholder="Titre du document..." required>
                                                    </div>
                                                    
                                                    <div class="col-md-3">
                                                        <label class="form-label-modern">Type</label>
                                                        <input type="text" name="type" class="form-control form-control-modern" 
                                                               placeholder="PDF, Word...">
                                                    </div>
                                                    
                                                    <div class="col-md-3">
                                                        <label class="form-label-modern">Fichier</label>
                                                        <input type="file" name="fichier" class="form-control form-control-modern" required>
                                                    </div>
                                                    
                                                    <div class="col-md-2">
                                                        <button type="submit" class="btn btn-success-modern btn-modern w-100">
                                                            <i class="fas fa-upload me-1"></i>Proposer
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                                <div class="alert-modern">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Vous n'avez aucun niveau attribué pour le moment.
                                </div>
                    @endforelse
                        </div>

                        <!-- Onglet Upload Général -->
                        <div class="tab-pane fade" id="upload" role="tabpanel">
                            <div class="section-title-compact animate-on-scroll">
                                <div class="section-icon">
                                    <i class="fas fa-broadcast-tower text-white"></i>
                                </div>
                                Envoyer un fichier à tous les apprenants
                            </div>
                            
                            <div class="form-modern-compact animate-on-scroll">
                                <form action="{{ route('admin.documents.store') }}" method="POST" 
                                      enctype="multipart/form-data" class="row g-3 align-items-end">
                        @csrf
                        <input type="hidden" name="module_id" value="">
                                    
                        <div class="col-md-4">
                                        <label class="form-label-modern">Titre du document</label>
                                        <input type="text" name="titre" class="form-control form-control-modern" 
                                               placeholder="Document général..." required>
                        </div>
                                    
                        <div class="col-md-3">
                                        <label class="form-label-modern">Type</label>
                                        <input type="text" name="type" class="form-control form-control-modern" 
                                               placeholder="Annonce, Info...">
                        </div>
                                    
                        <div class="col-md-3">
                                        <label class="form-label-modern">Fichier</label>
                                        <input type="file" name="fichier" class="form-control form-control-modern" required>
                        </div>
                                    
                        <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary-modern btn-modern w-100">
                                            <i class="fas fa-paper-plane me-1"></i>Envoyer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
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
    document.querySelectorAll('.badge-animated').forEach(badge => {
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

    // Fonctionnalité de recherche
    const searchInput = document.getElementById('searchModules');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const moduleCards = document.querySelectorAll('.module-card-compact');

    // Recherche en temps réel
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterModules(searchTerm, getActiveFilter());
    });

    // Filtres par boutons
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Retirer la classe active de tous les boutons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Ajouter la classe active au bouton cliqué
            this.classList.add('active');
            
            // Appliquer le filtre
            filterModules(searchInput.value.toLowerCase(), this.dataset.filter);
        });
    });

    function getActiveFilter() {
        const activeButton = document.querySelector('.filter-btn.active');
        return activeButton ? activeButton.dataset.filter : 'all';
    }

    function filterModules(searchTerm, filter) {
        moduleCards.forEach(card => {
            const title = card.dataset.module;
            const hasMeet = card.dataset.hasMeet === 'true';
            const hasDocuments = card.dataset.hasDocuments === 'true';
            const hasStudents = card.dataset.hasStudents === 'true';
            
            let showCard = true;
            
            // Filtre par recherche
            if (searchTerm && !title.includes(searchTerm)) {
                showCard = false;
            }
            
            // Filtre par type
            if (filter === 'with-meet' && !hasMeet) {
                showCard = false;
            } else if (filter === 'with-documents' && !hasDocuments) {
                showCard = false;
            } else if (filter === 'with-students' && !hasStudents) {
                showCard = false;
            }
            
            // Afficher/masquer la carte
            if (showCard) {
                card.style.display = 'block';
                card.style.animation = 'slideIn 0.5s ease-out';
            } else {
                card.style.display = 'none';
            }
        });
    }
});

// Fonction pour toggle les niveaux
function toggleModule(niveauId) {
    const content = document.getElementById(`content-${niveauId}`);
    const icon = document.getElementById(`toggle-${niveauId}`);
    
    if (content.classList.contains('expanded')) {
        content.classList.remove('expanded');
        icon.classList.remove('rotated');
    } else {
        content.classList.add('expanded');
        icon.classList.add('rotated');
    }
}
</script>

<!-- FontAwesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

@endsection 