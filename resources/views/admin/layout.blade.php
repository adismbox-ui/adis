<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - ADIS</title>
    @yield('head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/admin-dark.css') }}" />
    <style>
        body {
    background: linear-gradient(135deg, #222 0%, #2d5016 100%);
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}

.animated-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: -2;
    background: url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80') center/cover no-repeat;
    animation: slowZoom 20s ease-in-out infinite alternate;
}
.animated-background::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background:
        linear-gradient(135deg, rgba(34,34,34,0.75) 0%, rgba(45,80,22,0.55) 60%, rgba(127,176,105,0.25) 100%),
        radial-gradient(ellipse at 60% 40%, rgba(127,176,105,0.18) 0%, rgba(45,80,22,0.12) 60%, transparent 100%);
    animation: greenGlow 8s ease-in-out infinite alternate;
    z-index: 1;
}
@keyframes greenGlow {
    0% { filter: blur(0px) brightness(1) hue-rotate(0deg); }
    50% { filter: blur(2px) brightness(1.08) hue-rotate(-10deg); }
    100% { filter: blur(0px) brightness(1) hue-rotate(0deg); }
}

.floating-particles {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    pointer-events: none;
    z-index: 0;
}
.particle {
    position: absolute;
    background: rgba(74,124,89,0.18);
    border-radius: 50%;
    animation: float 15s infinite linear;
}
@keyframes float {
    0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
}

@keyframes slowZoom {
    0% { transform: scale(1); }
    100% { transform: scale(1.08); }
}

        .sidebar {
    min-height: 100vh;
    /* Ancien style, laissé pour compatibilité */
}
.admin-sidebar {
    min-height: 100vh;
    background: linear-gradient(160deg, #0a0a0a 0%, #1a1a1a 60%, rgba(45,80,22,0.25) 100%);
    color: #fff;
    position: fixed;
    left: 0;
    top: 0;
    width: 260px;
    z-index: 100;
    box-shadow: 4px 0 24px 0 rgba(0,0,0,0.8), 0 1.5px 0 0 rgba(127,176,105,0.3);
    overflow-y: auto;
    max-height: 100vh;
    backdrop-filter: blur(12px) saturate(120%);
    -webkit-backdrop-filter: blur(12px) saturate(120%);
    border-right: 2.5px solid rgba(127,176,105,0.3);
    opacity: 0.98;
    animation: sidebarFadeIn 1.2s cubic-bezier(.32,1.56,.64,1) 0.1s both;
    position: relative;
    overflow: hidden;
}

/* Animation de lumière qui tourne sur le contour */
.admin-sidebar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(127,176,105,0.3), transparent);
    animation: lightRotate 4s linear infinite;
    z-index: 1;
    pointer-events: none;
}

@keyframes lightRotate {
    0% { transform: translateX(-100%) translateY(-100%) rotate(0deg); }
    25% { transform: translateX(100%) translateY(-100%) rotate(90deg); }
    50% { transform: translateX(100%) translateY(100%) rotate(180deg); }
    75% { transform: translateX(-100%) translateY(100%) rotate(270deg); }
    100% { transform: translateX(-100%) translateY(-100%) rotate(360deg); }
}
@keyframes sidebarFadeIn {
    0% { transform: translateX(-60px) scale(0.96); opacity: 0; filter: blur(10px); }
    80% { filter: blur(1.5px); }
    100% { transform: translateX(0) scale(1); opacity: 1; filter: blur(0); }
}
.admin-sidebar-logo img {
    border: 2.5px solid #7fb069;
    box-shadow: 0 0 16px 0 rgba(127,176,105,0.3), 0 4px 16px rgba(0,0,0,0.3);
    transition: box-shadow 0.4s, border 0.3s;
}
.admin-sidebar-logo img:hover {
    box-shadow: 0 0 30px 4px rgba(127,176,105,0.5), 0 8px 24px rgba(0,0,0,0.4);
    border-color: #a7c957;
}
.admin-sidebar-header {
    text-shadow: 0 2px 6px rgba(0,0,0,0.5), 0 1px 0 rgba(0,0,0,0.8);
    position: relative;
    overflow: hidden;
}

.admin-sidebar-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #7fb069, #a7c957);
    animation: shimmer 2s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { transform: translateX(-100%); }
    50% { transform: translateX(100%); }
}
.admin-sidebar-nav .admin-nav-item {
    border-radius: 12px;
    margin-bottom: 7px;
    transition: background 0.25s, box-shadow 0.22s, color 0.18s;
    position: relative;
    overflow: hidden;
}
.admin-sidebar-nav .admin-nav-item.active, .admin-sidebar-nav .admin-nav-item:hover {
    background: linear-gradient(90deg, rgba(127,176,105,0.2) 0%, rgba(45,80,22,0.3) 50%, rgba(127,176,105,0.1) 100%);
    color: #fff;
    box-shadow: 0 0 18px rgba(127,176,105,0.3), 0 2px 8px rgba(0,0,0,0.3);
    border-left: 3px solid #7fb069;
    transform: translateX(8px);
}
.admin-sidebar-nav .admin-nav-item .admin-nav-icon {
    color: #7fb069;
    filter: drop-shadow(0 0 5px rgba(127,176,105,0.3));
    transition: color 0.2s, filter 0.2s;
}
.admin-sidebar-nav .admin-nav-item.active .admin-nav-icon, .admin-sidebar-nav .admin-nav-item:hover .admin-nav-icon {
    color: #a7c957;
    filter: drop-shadow(0 0 12px rgba(127,176,105,0.5));
}
.admin-sidebar-nav .admin-nav-text {
    transition: color 0.2s, text-shadow 0.2s;
    text-shadow: 0 1px 6px rgba(0,0,0,0.5);
}
.admin-sidebar-nav .admin-nav-item.active .admin-nav-text, .admin-sidebar-nav .admin-nav-item:hover .admin-nav-text {
    color: #fff;
    text-shadow: 0 2px 10px rgba(127,176,105,0.5), 0 1px 0 rgba(0,0,0,0.8);
}
.admin-sidebar .btn-outline-light {
    border-color: #7fb069;
    color: #7fb069;
    background: rgba(127,176,105,0.05);
    transition: all 0.23s;
    font-weight: 500;
    box-shadow: 0 0 8px rgba(127,176,105,0.2);
}
.admin-sidebar .btn-outline-light:hover, .admin-sidebar .btn-outline-light:focus {
    background: linear-gradient(90deg, rgba(127,176,105,0.2) 0%, rgba(45,80,22,0.3) 100%);
    color: #fff;
    border-color: #a7c957;
    box-shadow: 0 0 18px rgba(127,176,105,0.4), 0 2px 8px rgba(0,0,0,0.3);
}
.sidebar-section {
    border-top: 1px solid rgba(127,176,105,0.2);
    padding-top: 1.2rem;
    margin-top: 1.2rem;
    animation: fadeInSection 1.2s cubic-bezier(.32,1.56,.64,1) 0.5s both;
}
@keyframes fadeInSection {
    0% { opacity: 0; transform: translateY(30px); }
    100% { opacity: 1; transform: translateY(0); }
}


.sidebar .nav-link {
    color: #fff;
    font-weight: 500;
    border-radius: 0 30px 30px 0;
    margin-bottom: 8px;
    transition: background 0.2s;
}
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .sidebar .sidebar-header {
            padding: 2rem 1.5rem 1rem 1.5rem;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 1px;
            background: rgba(0,0,0,0.08);
        }
        .sidebar .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 1rem 1.5rem;
            font-size: 0.95rem;
            color: #e0e0e0;
        }
        .sidebar-section {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1rem;
        }
        .sidebar-section:first-child {
            border-top: none;
            padding-top: 0;
        }
        .sidebar-section small {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .main-content {
            margin-left: 280px;
            padding: 2rem 2rem 2rem 2rem;
            min-height: 100vh;
            width: calc(100% - 280px);
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 30%, rgba(45,80,22,0.1) 100%);
            color: #fff;
        }

        /* Styles sombres pour les cartes et composants */
        .card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(127, 176, 105, 0.2);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            color: #fff;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(127, 176, 105, 0.2);
            border-color: rgba(127, 176, 105, 0.4);
        }

        .card-header {
            background: linear-gradient(135deg, rgba(127, 176, 105, 0.1) 0%, rgba(45, 80, 22, 0.2) 100%);
            border-bottom: 1px solid rgba(127, 176, 105, 0.2);
            color: #fff;
            font-weight: 600;
        }

        .card-body {
            color: #fff;
        }

        /* Styles pour les tableaux */
        .table {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 15px;
            overflow: hidden;
            border-collapse: separate;
            border-spacing: 0;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(127, 176, 105, 0.3);
        }

        .table thead th {
            background: linear-gradient(135deg, rgba(127, 176, 105, 0.4) 0%, rgba(45, 80, 22, 0.6) 100%);
            color: #ffffff !important;
            border-bottom: 3px solid rgba(127, 176, 105, 0.5);
            font-weight: 700;
            padding: 1.2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(127, 176, 105, 0.2);
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(127, 176, 105, 0.3);
        }

        .table tbody td {
            border-bottom: 2px solid rgba(127, 176, 105, 0.2);
            color: #ffffff !important;
            padding: 1.2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
            font-weight: 600;
            font-size: 1rem;
        }

        /* Styles spécifiques pour les liens dans les tableaux */
        .table a {
            color: #a7c957 !important;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            text-shadow: 0 1px 3px rgba(127, 176, 105, 0.5);
        }

        .table a:hover {
            color: #ffffff !important;
            text-shadow: 0 2px 6px rgba(127, 176, 105, 0.8);
            transform: scale(1.05);
        }

        /* Styles pour les badges dans les tableaux */
        .table .badge {
            color: #ffffff !important;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 0.5rem 0.8rem;
            border-radius: 8px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Styles pour les boutons dans les tableaux */
        .table .btn {
            color: #ffffff !important;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Styles pour les icônes dans les tableaux */
        .table .fas, .table .far, .table .fab {
            color: #a7c957 !important;
            font-size: 1.2rem;
            text-shadow: 0 1px 3px rgba(127, 176, 105, 0.5);
        }

        .table .fas:hover, .table .far:hover, .table .fab:hover {
            color: #ffffff !important;
            text-shadow: 0 2px 6px rgba(127, 176, 105, 0.8);
            transform: scale(1.2);
        }

        /* Styles pour les boutons */
        .btn-primary {
            background: linear-gradient(135deg, #7fb069 0%, #4a7c59 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(127, 176, 105, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #a7c957 0%, #7fb069 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(127, 176, 105, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #4a7c59 0%, #2d5016 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(74, 124, 89, 0.3);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #7fb069 0%, #4a7c59 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(127, 176, 105, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #e74c3c 0%, #dc3545 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        }

        /* Styles pour les formulaires */
        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(127, 176, 105, 0.3);
            border-radius: 8px;
            color: #fff;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #7fb069;
            box-shadow: 0 0 0 0.2rem rgba(127, 176, 105, 0.25);
            color: #fff;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-label {
            color: #fff;
            font-weight: 600;
        }

        /* Styles pour les alertes */
        .alert {
            border-radius: 10px;
            border: none;
            backdrop-filter: blur(10px);
        }

        .alert-success {
            background: rgba(127, 176, 105, 0.2);
            color: #fff;
            border-left: 4px solid #7fb069;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            color: #fff;
            border-left: 4px solid #dc3545;
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.2);
            color: #fff;
            border-left: 4px solid #ffc107;
        }

        .alert-info {
            background: rgba(23, 162, 184, 0.2);
            color: #fff;
            border-left: 4px solid #17a2b8;
        }

        /* Styles pour les badges */
        .badge {
            border-radius: 6px;
            font-weight: 600;
        }

        .badge-success {
            background: linear-gradient(135deg, #7fb069 0%, #4a7c59 100%);
            color: #fff;
        }

        .badge-primary {
            background: linear-gradient(135deg, #4a7c59 0%, #2d5016 100%);
            color: #fff;
        }

        /* Styles pour les modales */
        .modal-content {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border: 1px solid rgba(127, 176, 105, 0.3);
            border-radius: 15px;
            color: #fff;
        }

        .modal-header {
            background: linear-gradient(135deg, rgba(127, 176, 105, 0.1) 0%, rgba(45, 80, 22, 0.2) 100%);
            border-bottom: 1px solid rgba(127, 176, 105, 0.2);
        }

        .modal-footer {
            background: rgba(255, 255, 255, 0.02);
            border-top: 1px solid rgba(127, 176, 105, 0.2);
        }

        /* Styles pour les paginations */
        .pagination .page-link {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(127, 176, 105, 0.3);
            color: #fff;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background: rgba(127, 176, 105, 0.2);
            border-color: #7fb069;
            color: #fff;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #7fb069 0%, #4a7c59 100%);
            border-color: #7fb069;
            color: #fff;
        }

        /* Styles pour les titres */
        h1, h2, h3, h4, h5, h6 {
            color: #ffffff !important;
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.8);
            font-weight: 700;
        }

        /* Styles pour les éléments Bootstrap */
        .row, .col, .col-md, .col-lg, .col-xl {
            color: #fff !important;
        }

        .container, .container-fluid {
            color: #fff !important;
        }

        /* Styles pour les éléments de navigation */
        .nav-link {
            color: #fff !important;
        }

        .nav-link:hover {
            color: #7fb069 !important;
        }

        .nav-link.active {
            color: #a7c957 !important;
        }

        /* Styles pour les liens */
        a {
            color: #a7c957;
            transition: all 0.3s ease;
            text-shadow: 0 1px 3px rgba(127, 176, 105, 0.5);
            font-weight: 600;
        }

        a:hover {
            color: #ffffff;
            text-shadow: 0 2px 6px rgba(127, 176, 105, 0.8);
            transform: scale(1.05);
        }

        /* Styles pour les listes */
        ul, ol {
            color: #ffffff !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        ul li, ol li {
            color: #ffffff !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
            font-weight: 500;
        }

        /* Styles pour les descriptions */
        .text-muted {
            color: rgba(255, 255, 255, 0.9) !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .text-secondary {
            color: rgba(255, 255, 255, 0.95) !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Styles pour les textes de succès, danger, etc. */
        .text-success {
            color: #7fb069 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .text-info {
            color: #17a2b8 !important;
        }

        /* Styles pour les icônes */
        .fas, .far, .fab {
            color: #a7c957;
            transition: all 0.3s ease;
            text-shadow: 0 1px 3px rgba(127, 176, 105, 0.5);
            font-size: 1.1rem;
        }

        .fas:hover, .far:hover, .fab:hover {
            color: #ffffff;
            text-shadow: 0 2px 6px rgba(127, 176, 105, 0.8);
            transform: scale(1.2);
        }

        /* Styles pour tous les éléments de texte */
        p, span, div, label, small, strong, em {
            color: #ffffff !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Styles pour les textes dans les cartes */
        .card p, .card span, .card div, .card label, .card small, .card strong, .card em {
            color: #ffffff !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Styles pour les textes dans les modales */
        .modal p, .modal span, .modal div, .modal label, .modal small, .modal strong, .modal em {
            color: #ffffff !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Styles pour les textes dans les formulaires */
        .form-group p, .form-group span, .form-group div, .form-group label, .form-group small, .form-group strong, .form-group em {
            color: #ffffff !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Styles pour les sections spéciales */
        .hero-section {
            background: linear-gradient(135deg, rgba(127, 176, 105, 0.1) 0%, rgba(45, 80, 22, 0.2) 100%);
            border-radius: 20px;
            padding: 3rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(127, 176, 105, 0.3);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #7fb069, #a7c957);
            animation: shimmer 3s ease-in-out infinite;
        }

        /* Styles pour les statistiques */
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(127, 176, 105, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(127, 176, 105, 0.2), transparent);
            transition: left 0.5s;
        }

        .stat-card:hover::before {
            left: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(127, 176, 105, 0.3);
            border-color: rgba(127, 176, 105, 0.4);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #7fb069;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .stat-label {
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media (max-width: 991px) {
            .admin-sidebar { width: 100px; }
            .main-content { margin-left: 100px; width: calc(100% - 100px); }
            .admin-sidebar .admin-sidebar-header, .admin-sidebar .admin-sidebar-footer, .admin-nav-text { display: none; }
            .admin-nav-item { justify-content: center; }
        }
    </style>
<script>
// Génération dynamique des bulles vert sombre pour le fond admin
window.addEventListener('DOMContentLoaded', function() {
    const particleColors = [
        'rgba(74,124,89,0.18)', // vert sombre principal
        'rgba(45,80,22,0.22)',  // vert foncé accent
        'rgba(127,176,105,0.12)', // vert clair très doux
    ];
    const particleCount = 18;
    const minSize = 28;
    const maxSize = 70;
    const minDuration = 13;
    const maxDuration = 22;
    const container = document.querySelector('.floating-particles');
    if(container) {
        for(let i=0; i<particleCount; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size = Math.random()*(maxSize-minSize)+minSize;
            p.style.width = size+'px';
            p.style.height = size+'px';
            p.style.left = (Math.random()*100)+'vw';
            p.style.top = (Math.random()*100)+'vh';
            p.style.background = particleColors[Math.floor(Math.random()*particleColors.length)];
            p.style.animationDuration = (Math.random()*(maxDuration-minDuration)+minDuration)+'s';
            p.style.opacity = (Math.random()*0.35+0.18).toFixed(2);
            container.appendChild(p);
        }
    }
});
</script>
</head>
<body>
    <div class="animated-background"></div>
    <div class="floating-particles"></div>
    <!-- SIDEBAR ADMIN -->
    <div class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-logo" style="text-align:center; padding-top:1.2rem;">
    @php
        $adminLogo = null;
        try {
            $adminLogo = \App\Models\Setting::first()?->admin_logo;
        } catch (\Throwable $e) { $adminLogo = null; }
    @endphp
    <a href="/" title="Aller à l'accueil" style="display:inline-block;">
        <img src="{{ $adminLogo ? asset('storage/'.$adminLogo) : '/photo_2025-07-02_10-44-47.jpg' }}" alt="Logo ADIS" style="max-width:80px; border-radius:14px; box-shadow:0 2px 8px #0003; margin-bottom:0.7rem;">
    </a>
<a href="{{ route('admin.logo.edit') }}" class="btn btn-outline-light btn-sm mt-2 w-100" style="border-radius:8px;">
    <i class="fas fa-image me-1"></i> Changer le logo
</a>
</div>
        <div class="admin-sidebar-header">
            <h2 class="admin-sidebar-title">Admin Panel</h2>
            <p class="admin-sidebar-subtitle">Gestion Nature Vivante</p>
        </div>
        
        <!-- Animated background overlay for sidebar -->
        <div class="sidebar-bg-overlay"></div>
        <div class="sidebar-particles" id="sidebarParticles"></div>
        <nav class="admin-sidebar-nav">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 13h1v7c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-7h1a1 1 0 0 0 .707-1.707l-9-9a.999.999 0 0 0-1.414 0l-9 9A1 1 0 0 0 3 13z"/>
                </svg>
                <span class="admin-nav-text">Tableau de bord</span>
            </a>
            <!-- GESTION UTILISATEURS -->
            <div class="sidebar-section mt-3">
                <small class="text-muted px-3 mb-2 d-block">GESTION UTILISATEURS</small>
                <a href="{{ route('admin.utilisateurs') }}" class="admin-nav-item {{ request()->is('admin/utilisateurs*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.579 2 2 6.579 2 12s4.579 10 10 10 10-4.579 10-10S17.421 2 12 2zm0 5c1.727 0 3 1.272 3 3s-1.273 3-3 3c-1.726 0-3-1.272-3-3s1.274-3 3-3zm-5.106 9.772c.897-1.32 2.393-2.2 4.106-2.2h2c1.714 0 3.209.88 4.106 2.2C15.828 18.14 14.015 19 12 19s-3.828-.86-5.106-2.228z"/>
                    </svg>
                    <span class="admin-nav-text">Tous les Utilisateurs</span>
                </a>
                <a href="{{ route('formateurs.index') }}" class="admin-nav-item {{ request()->is('admin/formateurs*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <span class="admin-nav-text">Formateurs</span>
                </a>
                <a href="{{ route('apprenants.index') }}" class="admin-nav-item {{ request()->is('admin/apprenants*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <span class="admin-nav-text">Apprenants</span>
                </a>
                <a href="{{ route('admin.assistants') }}" class="admin-nav-item {{ request()->is('admin/assistants*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5s-3 1.34-3 3 1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13zm8 0c-.29 0-.62.02-.97.05C15.64 14.1 17 15.28 17 16.5V19h7v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                    <span class="admin-nav-text">Assistants</span>
                </a>
            </div>
            <!-- GESTION FORMATIONS -->
            <div class="sidebar-section mt-3">
                <small class="text-muted px-3 mb-2 d-block">GESTION FORMATIONS</small>
                <a href="{{ route('admin.sessions.index') }}" class="admin-nav-item {{ request()->is('admin/sessions*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 18H5V8h14v13zm0-15H5V5h14v1z"/>
                    </svg>
                    <span class="admin-nav-text">Sessions</span>
                </a>
                <a href="{{ route('admin.niveaux.index') }}" class="admin-nav-item {{ request()->is('admin/niveaux*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 8h14v-2H7v2zm0-4h14v-2H7v2zm0-6v2h14V7H7z"/>
                    </svg>
                    <span class="admin-nav-text">Niveaux</span>
                </a>
                <a href="{{ route('admin.modules') }}" class="admin-nav-item {{ request()->is('admin/modules*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zM5 19V5h14l.002 14H5z"/>
                        <path d="M7 7h10v2H7zm0 4h10v2H7zm0 4h6v2H7z"/>
                    </svg>
                    <span class="admin-nav-text">Modules</span>
                </a>
                <a href="{{ route('admin.vacances.index') }}" class="admin-nav-item {{ request()->is('admin/vacances*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                    </svg>
                    <span class="admin-nav-text">Vacances</span>
                </a>
                <a href="{{ route('admin.sessions.calendrier') }}" class="admin-nav-item {{ request()->is('admin/sessions/calendrier*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 10h5v5H7z"/>
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 18H5V8h14v13zm0-15H5V5h14v1z"/>
                    </svg>
                    <span class="admin-nav-text">Calendrier</span>
                </a>
                <a href="{{ route('admin.sessions.calendrier') }}" class="admin-nav-item d-none"></a>
            </div>
            <!-- COURS À DOMICILE -->
            <div class="sidebar-section mt-3">
                <small class="text-muted px-3 mb-2 d-block">COURS À DOMICILE</small>
                <a href="/valider-cours" class="admin-nav-item {{ request()->is('valider-cours*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                    <span class="admin-nav-text">Cours à domicile</span>
                </a>
            </div>
            <!-- ADMINISTRATION -->
            <div class="sidebar-section mt-3">
                <small class="text-muted px-3 mb-2 d-block">ADMINISTRATION</small>
                <a href="{{ route('admin.inscriptions') }}" class="admin-nav-item {{ request()->is('admin/inscriptions*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/>
                    </svg>
                    <span class="admin-nav-text">Préinscriptions</span>
                </a>
                <a href="{{ route('admin.paiements') }}" class="admin-nav-item {{ request()->is('admin/paiements*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 7H3c-1.1 0-2 .9-2 2v6c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zm0 8H3V9h18v6z"/>
                    </svg>
                    <span class="admin-nav-text">Paiements</span>
                </a>
                <a href="{{ route('admin.presence.index') }}" class="admin-nav-item {{ request()->is('admin/presence*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <span class="admin-nav-text">Présence</span>
                </a>
                <a href="{{ route('admin.certificats.index') }}" class="admin-nav-item {{ request()->is('admin/certificats*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h10zm0 2H7v14h10V5zm-5 2a2 2 0 1 1 0 4 2 2 0 0 1 0-4zm0 6c2.67 0 8 1.34 8 4v2H4v-2c0-2.66 5.33-4 8-4z"/>
                    </svg>
                    <span class="admin-nav-text">Certificats</span>
                </a>
                <a href="{{ route('admin.documents.index') }}" class="admin-nav-item {{ request()->is('admin/documents*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6H6zm7 7V3.5L18.5 9H13z"/>
                    </svg>
                    <span class="admin-nav-text">Documents</span>
                </a>
                <a href="{{ route('questionnaires.index') }}" class="admin-nav-item {{ request()->is('admin/questionnaires*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 6v18h18V6H3zm16 16H5V8h14v14zm-7-7h2v2h-2zm0-4h2v2h-2z"/>
                    </svg>
                    <span class="admin-nav-text">Questionnaires</span>
                </a>
            </div>
            
            <!-- GESTION PROJETS -->
            <div class="sidebar-section mt-3">
                <small class="text-muted px-3 mb-2 d-block">GESTION PROJETS</small>
                <a href="{{ route('admin.projets.index') }}" class="admin-nav-item {{ request()->is('admin/projets*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    <span class="admin-nav-text">Tous les Projets</span>
                </a>
                <a href="{{ route('admin.projets.create') }}" class="admin-nav-item {{ request()->is('admin/projets/create*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    <span class="admin-nav-text">Ajouter un Projet</span>
                </a>
                <a href="{{ route('admin.appels-a-projets.index') }}" class="admin-nav-item {{ request()->is('admin/appels-a-projets*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <span class="admin-nav-text">Appels à Projets</span>
                </a>
                <a href="{{ route('admin.candidatures.index') }}" class="admin-nav-item {{ request()->is('admin/candidatures*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                    </svg>
                    <span class="admin-nav-text">Candidatures</span>
                </a>
            </div>

            <!-- GALERIE -->
            <div class="sidebar-section mt-3">
                <small class="text-muted px-3 mb-2 d-block">GALERIE</small>
                <a href="{{ route('galeries.index') }}" class="admin-nav-item {{ request()->is('projets/galeries') || request()->is('projets/galeries/*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 19V5a2 2 0 0 0-2-2H5c-1.103 0-2 .897-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2zM8.5 11.5l2.5 3.01L14.5 11l4.5 6H5l3.5-5.5zM7 8a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/>
                    </svg>
                    <span class="admin-nav-text">Galerie</span>
                </a>
                <a href="{{ route('galeries.create') }}" class="admin-nav-item {{ request()->is('projets/galeries/create') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6z"/>
                    </svg>
                    <span class="admin-nav-text">Ajouter à la galerie</span>
                </a>
            </div>

            <!-- ENTREPRISES -->
            <div class="sidebar-section mt-3">
                <small class="text-muted px-3 mb-2 d-block">ENTREPRISES</small>
                <a href="{{ route('partenaires.index') }}" class="admin-nav-item {{ request()->is('projets/partenaires') || request()->is('projets/partenaires/*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 13h18v8H3z"/>
                        <path d="M7 9h10v4H7z"/>
                        <path d="M9 3h6v6H9z"/>
                    </svg>
                    <span class="admin-nav-text">Entreprises partenaires</span>
                </a>
                <a href="{{ route('partenaires.create') }}" class="admin-nav-item {{ request()->is('projets/partenaires/create') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6z"/>
                    </svg>
                    <span class="admin-nav-text">Ajouter une entreprise</span>
                </a>
            </div>
            
            <!-- GESTION DONS -->
            <div class="sidebar-section mt-3">
                <small class="text-muted px-3 mb-2 d-block">GESTION DONS</small>
                <a href="{{ route('admin.dons.index') }}" class="admin-nav-item {{ request()->is('admin/dons*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span class="admin-nav-text">Tous les Dons</span>
                </a>
                <a href="{{ route('admin.dons.create') }}" class="admin-nav-item {{ request()->is('admin/dons/create*') ? 'active' : '' }}">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    <span class="admin-nav-text">Ajouter un Don</span>
                </a>
            </div>
            
            <!-- NOTIFICATION MODULE -->
            <div class="notification-module" id="notificationModule">
                <div class="notification-header" onclick="toggleNotifications()">
                    <div class="notification-icon-wrapper">
                        <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                        </svg>
                        <span class="notification-badge" id="notificationBadge">3</span>
                    </div>
                    <span class="notification-title">Notifications</span>
                    <svg class="notification-arrow" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                </div>
                
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-list">
                        <!-- Sample notifications - these will be populated dynamically -->
                        <div class="notification-item unread">
                            <div class="notification-dot"></div>
                            <div class="notification-content">
                                <div class="notification-text">Nouvelle inscription reçue</div>
                                <div class="notification-time">Il y a 2 minutes</div>
                            </div>
                        </div>
                        
                        <div class="notification-item unread">
                            <div class="notification-dot"></div>
                            <div class="notification-content">
                                <div class="notification-text">Paiement en attente de validation</div>
                                <div class="notification-time">Il y a 15 minutes</div>
                            </div>
                        </div>
                        
                        <div class="notification-item">
                            <div class="notification-dot"></div>
                            <div class="notification-content">
                                <div class="notification-text">Session de formation créée</div>
                                <div class="notification-time">Il y a 1 heure</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="notification-footer">
                        <a href="{{ route('admin.notifications.index') }}" class="notification-action">Voir toutes les notifications</a>
                        <a href="#" class="notification-action" onclick="markAllAsRead()">Marquer comme lues</a>
                    </div>
                </div>
            </div>
            
            <!-- Déconnexion -->
            <div class="sidebar-section mt-3">
                <a href="{{ route('logout') }}" class="admin-logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16 13v-2H7V8l-5 4 5 4v-3z"/>
                        <path d="M20 3h-9c-1.103 0-2 .897-2 2v4h2V5h9v14h-9v-4H9v4c0 1.103.897 2 2 2h9c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2z"/>
                    </svg>
                    <span class="admin-nav-text">Déconnexion</span>
                </a>
            </div>
        </nav>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
    </div>
    <style>
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.8);
            border-right: 1px solid rgba(127, 176, 105, 0.3);
            overflow-y: auto;
            max-height: 100vh;
            background: linear-gradient(160deg, #0a0a0a 0%, #1a1a1a 60%, rgba(45,80,22,0.25) 100%);
        }
        .sidebar-bg-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=600&q=80') center/cover no-repeat;
            opacity: 0.18;
            z-index: 1;
            pointer-events: none;
            animation: sidebarBgPulse 10s ease-in-out infinite;
        }
        @keyframes sidebarBgPulse {
            0%,100% { opacity: 0.18; }
            50% { opacity: 0.28; }
        }
        .sidebar-particles {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 2;
            pointer-events: none;
        }
        .sidebar-particle {
            position: absolute;
            width: 7px; height: 7px;
            background: radial-gradient(circle, #7fb069, #4a7c59 80%);
            border-radius: 50%;
            opacity: 0.7;
            animation: sidebarParticleFloat 14s linear infinite;
            box-shadow: 0 0 10px rgba(127,176,105,0.4);
        }
        @keyframes sidebarParticleFloat {
            0% { transform: translateY(100vh) scale(0.7) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100vh) scale(1.1) rotate(360deg); opacity: 0; }
        }
        .admin-sidebar-logo { z-index: 3; position: relative; }
        .admin-sidebar-header { z-index: 3; position: relative; padding: 1.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1); background: rgba(0, 0, 0, 0.08); }
        .admin-sidebar-title { color: #f1f5f9; font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3); }
        .admin-sidebar-subtitle { color: #94a3b8; font-size: 0.9rem; opacity: 0.8; }
        .admin-sidebar-nav { padding: 1rem 0; z-index: 3; position: relative; }
        .admin-nav-item {
    display: flex;
    align-items: center;
    padding: 0.85rem 1.7rem;
    color: #fff;
    text-shadow: 0 2px 12px rgba(0,0,0,0.5), 0 0px 2px rgba(0,0,0,0.8);
    font-weight: 700;
    font-size: 1.08rem;
    letter-spacing: 0.5px;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4,0,0.2,1);
    border-left: 3px solid transparent;
    border-radius: 10px;
    margin-bottom: 10px;
    box-shadow: 0 0 6px 0 rgba(0,0,0,0.3);
    background: rgba(255,255,255,0.03);
    position: relative;
    overflow: hidden;
}

.admin-nav-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(127,176,105,0.3), transparent);
    transition: left 0.5s;
}

.admin-nav-item:hover::before {
    left: 100%;
}

        .admin-nav-item:hover {
    background: rgba(127, 176, 105, 0.1);
    color: #fff;
    border-left-color: #7fb069;
    box-shadow: 0 6px 24px rgba(127,176,105,0.3);
    transform: translateX(8px) scale(1.045);
    text-shadow: 0 4px 24px rgba(127,176,105,0.5), 0 0px 4px rgba(0,0,0,0.8);
}
        .admin-nav-item.active {
    background: linear-gradient(90deg, rgba(127,176,105,0.2) 0%, rgba(45,80,22,0.3) 50%, rgba(127,176,105,0.1) 100%);
    color: #fff;
    border-left-color: #a7c957;
    box-shadow: 0 8px 32px rgba(127,176,105,0.4);
    text-shadow: 0 6px 32px rgba(127,176,105,0.5), 0 2px 8px rgba(0,0,0,0.8);
    font-weight: 900;
    letter-spacing: 1px;
}
        .admin-nav-icon { width: 20px; height: 20px; margin-right: 0.75rem; opacity: 0.85; }
        .admin-nav-text {
    font-size: 1.13rem;
    font-weight: 900;
    letter-spacing: 0.5px;
    color: #fff;
    text-shadow: 0 2px 12px rgba(0,0,0,0.5), 0 0px 2px rgba(0,0,0,0.8);
}
        .sidebar-section { border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1rem; }
        .sidebar-section:first-child { border-top: none; padding-top: 0; }
        .sidebar-section small { font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px; color: #7fb069; text-shadow: 0 1px 4px rgba(0,0,0,0.25); }
        .admin-sidebar-footer { position: sticky; bottom: 0; left: 0; right: 0; padding: 1rem 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1); background: rgba(0, 0, 0, 0.08); z-index: 3; }
        .admin-logout-btn { display: flex; align-items: center; width: 100%; padding: 0.75rem; background: rgba(127, 176, 105, 0.05); color: #7fb069; border: 1px solid rgba(127, 176, 105, 0.2); border-radius: 6px; text-decoration: none; transition: all 0.2s cubic-bezier(0.4,0,0.2,1); font-size: 0.9rem; margin-top: 8px; }
        .admin-logout-btn:hover { background: rgba(127, 176, 105, 0.15); color: #a7c957; box-shadow: 0 2px 12px rgba(127,176,105,0.3); transform: scale(1.03); }
        
        /* NOTIFICATION MODULE STYLES */
        .notification-module {
            margin: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            z-index: 5;
            position: relative;
        }
        
        .notification-header {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border-radius: 12px;
        }
        
        .notification-header:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .notification-icon-wrapper {
            position: relative;
            margin-right: 0.75rem;
        }
        
        .notification-icon {
            width: 20px;
            height: 20px;
            color: #fff;
            opacity: 0.9;
        }
        
        .notification-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ef4444;
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .notification-title {
            flex: 1;
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .notification-arrow {
            width: 16px;
            height: 16px;
            color: #fff;
            opacity: 0.7;
            transition: transform 0.2s ease;
        }
        
        .notification-module.open .notification-arrow {
            transform: rotate(180deg);
        }
        
        .notification-dropdown {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(0, 0, 0, 0.15);
            border-radius: 0 0 12px 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .notification-module.open .notification-dropdown {
            max-height: 400px;
        }
        
        .notification-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .notification-item {
            display: flex;
            align-items: flex-start;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: background 0.2s ease;
            cursor: pointer;
        }
        
        .notification-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .notification-item:last-child {
            border-bottom: none;
        }
        
        .notification-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            margin-right: 0.75rem;
            margin-top: 0.25rem;
            flex-shrink: 0;
        }
        
        .notification-item.unread .notification-dot {
            background: #7fb069;
            box-shadow: 0 0 8px rgba(127, 176, 105, 0.5);
        }
        
        .notification-content {
            flex: 1;
        }
        
        .notification-text {
            color: #fff;
            font-size: 0.85rem;
            font-weight: 500;
            line-height: 1.3;
            margin-bottom: 0.25rem;
        }
        
        .notification-item.unread .notification-text {
            font-weight: 600;
        }
        
        .notification-time {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            font-weight: 400;
        }
        
        .notification-footer {
            padding: 0.75rem 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            justify-content: space-between;
            gap: 0.5rem;
        }
        
        .notification-action {
            color: #7fb069;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }
        
        .notification-action:hover {
            color: #a7c957;
            background: rgba(127, 176, 105, 0.1);
            text-shadow: 0 1px 3px rgba(127, 176, 105, 0.3);
        }
        
        @media (max-width: 768px) { 
            .admin-sidebar { width: 100%; left: 0; } 
            .main-content { margin-left: 0; width: 100%; }
        }
    </style>
    <script>
        // Animated floating particles in sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('sidebarParticles');
            if (container) {
                for (let i = 0; i < 12; i++) {
                    const p = document.createElement('div');
                    p.className = 'sidebar-particle';
                    p.style.left = Math.random() * 100 + '%';
                    p.style.animationDelay = (Math.random() * 12) + 's';
                    p.style.animationDuration = (Math.random() * 8 + 10) + 's';
                    container.appendChild(p);
                }
            }
        });
        
        // Notification module functionality
        function toggleNotifications() {
            const module = document.getElementById('notificationModule');
            const dropdown = document.getElementById('notificationDropdown');
            
            if (module.classList.contains('open')) {
                module.classList.remove('open');
            } else {
                module.classList.add('open');
            }
        }
        
        function markAllAsRead() {
            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const unreadItems = document.querySelectorAll('.notification-item.unread');
                    const badge = document.getElementById('notificationBadge');
                    
                    unreadItems.forEach(item => {
                        item.classList.remove('unread');
                    });
                    
                    // Update badge count
                    badge.textContent = '0';
                    badge.style.display = 'none';
                    
                    console.log('Toutes les notifications ont été marquées comme lues');
                }
            })
            .catch(error => {
                console.error('Erreur lors du marquage des notifications:', error);
            });
        }
        
        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const module = document.getElementById('notificationModule');
            if (module && !module.contains(event.target)) {
                module.classList.remove('open');
            }
        });
        
        // Function to fetch notifications from backend
        function fetchNotifications() {
            fetch('/admin/notifications/ajax')
            .then(response => response.json())
            .then(data => {
                updateNotificationsList(data.notifications);
                updateNotificationBadge(data.unread_count);
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des notifications:', error);
            });
        }
        
        // Function to update notifications list in sidebar
        function updateNotificationsList(notifications) {
            const notificationList = document.querySelector('.notification-list');
            if (!notificationList) return;
            
            notificationList.innerHTML = '';
            
            if (notifications.length === 0) {
                notificationList.innerHTML = '<div class="no-notifications"><p>Aucune nouvelle notification</p></div>';
                return;
            }
            
            notifications.forEach(notification => {
                const notificationItem = document.createElement('div');
                notificationItem.className = `notification-item ${!notification.is_read ? 'unread' : ''}`;
                notificationItem.innerHTML = `
                    <div class="notification-dot"></div>
                    <div class="notification-content">
                        <div class="notification-text">${notification.title}</div>
                        <div class="notification-time">${formatTime(notification.created_at)}</div>
                    </div>
                `;
                notificationList.appendChild(notificationItem);
            });
        }
        
        // Function to update notification badge
        function updateNotificationBadge(count) {
            const badge = document.getElementById('notificationBadge');
            if (badge) {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'block' : 'none';
            }
        }
        
        // Function to format time (simple implementation)
        function formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInMinutes = Math.floor((now - date) / (1000 * 60));
            
            if (diffInMinutes < 1) return 'À l\'instant';
            if (diffInMinutes < 60) return `Il y a ${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''}`;
            
            const diffInHours = Math.floor(diffInMinutes / 60);
            if (diffInHours < 24) return `Il y a ${diffInHours} heure${diffInHours > 1 ? 's' : ''}`;
            
            const diffInDays = Math.floor(diffInHours / 24);
            return `Il y a ${diffInDays} jour${diffInDays > 1 ? 's' : ''}`;
        }
        
        // Auto-refresh notifications every 30 seconds
        setInterval(function() {
            fetchNotifications();
        }, 30000);
        
        // Initial load of notifications
        document.addEventListener('DOMContentLoaded', function() {
            // Delay initial fetch to ensure page is fully loaded
            setTimeout(fetchNotifications, 1000);
        });
    </script>
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(
            isset(
                $errors
            ) && $errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 