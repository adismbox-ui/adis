@extends('admin.layout')

@section('content')
<style>
    /* Forcer le style sur le body */
    body {
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(22, 101, 52, 0.9)), 
                    url('https://images.unsplash.com/photo-1518837695005-2083093ee35b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') !important;
        background-size: cover !important;
        background-attachment: fixed !important;
        background-position: center !important;
        min-height: 100vh !important;
        margin: 0 !important;
        padding: 20px !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
    }

    /* Masquer les éléments du layout admin */
    .animated-background,
    .floating-particles {
        display: none !important;
    }

    /* Container principal */
    .main-content {
        max-width: 1200px !important;
        margin: 0 auto !important;
        padding: 20px !important;
        position: relative !important;
        z-index: 10 !important;
    }

    /* Alert personnalisé */
    .alert {
        border: none !important;
        border-radius: 15px !important;
        padding: 20px !important;
        margin-bottom: 30px !important;
        backdrop-filter: blur(10px) !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
        animation: slideInDown 0.6s ease-out !important;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.9), rgba(22, 163, 74, 0.9)) !important;
        color: white !important;
        border-left: 5px solid #10b981 !important;
    }

    /* Titre principal */
    .page-title {
        color: #ffffff !important;
        font-size: 3.5rem !important;
        font-weight: 700 !important;
        text-align: center !important;
        margin-bottom: 40px !important;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5) !important;
        background: linear-gradient(135deg, #10b981, #34d399) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        background-clip: text !important;
        animation: glow 2s ease-in-out infinite alternate !important;
    }

    @keyframes glow {
        from { filter: drop-shadow(0 0 5px rgba(16, 185, 129, 0.5)) !important; }
        to { filter: drop-shadow(0 0 20px rgba(16, 185, 129, 0.8)) !important; }
    }

    /* Container du tableau */
    .table-container {
        background: rgba(15, 23, 42, 0.95) !important;
        backdrop-filter: blur(20px) !important;
        border-radius: 20px !important;
        padding: 30px !important;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
        border: 1px solid rgba(16, 185, 129, 0.2) !important;
        animation: fadeInUp 0.8s ease-out !important;
    }

    /* Table principale */
    .modern-table {
        background: transparent !important;
        border-radius: 15px !important;
        overflow: hidden !important;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
        border: none !important;
        margin: 0 !important;
        width: 100% !important;
    }

    /* En-têtes du tableau */
    .modern-table thead tr th {
        background: linear-gradient(135deg, #065f46, #047857) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        font-size: 16px !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        padding: 20px 15px !important;
        border: none !important;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3) !important;
        position: relative !important;
        border-bottom: 3px solid #10b981 !important;
    }

    .modern-table thead tr th::after {
        content: '' !important;
        position: absolute !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 2px !important;
        background: linear-gradient(90deg, transparent, #10b981, transparent) !important;
    }

    /* Cellules du tableau */
    .modern-table tbody tr td {
        background: rgba(30, 41, 59, 0.8) !important;
        color: #e2e8f0 !important;
        font-weight: 500 !important;
        font-size: 15px !important;
        padding: 18px 15px !important;
        border: 1px solid rgba(16, 185, 129, 0.1) !important;
        transition: all 0.3s ease !important;
        vertical-align: middle !important;
    }

    /* Effet hover sur les lignes */
    .modern-table tbody tr {
        transition: all 0.3s ease !important;
        cursor: pointer !important;
    }

    .modern-table tbody tr:hover {
        background: rgba(16, 185, 129, 0.1) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2) !important;
    }

    .modern-table tbody tr:hover td {
        background: rgba(16, 185, 129, 0.15) !important;
        color: #ffffff !important;
        text-shadow: 0 0 10px rgba(16, 185, 129, 0.5) !important;
    }

    /* Boutons d'action */
    .modern-btn {
        border: none !important;
        border-radius: 25px !important;
        padding: 8px 16px !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        margin: 0 3px !important;
        transition: all 0.3s ease !important;
        position: relative !important;
        overflow: hidden !important;
        text-decoration: none !important;
        display: inline-block !important;
    }

    .modern-btn::before {
        content: '' !important;
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        width: 0 !important;
        height: 0 !important;
        background: rgba(255, 255, 255, 0.2) !important;
        border-radius: 50% !important;
        transform: translate(-50%, -50%) !important;
        transition: width 0.6s, height 0.6s !important;
    }

    .modern-btn:hover::before {
        width: 300px !important;
        height: 300px !important;
    }

    .modern-btn-info {
        background: linear-gradient(135deg, #0ea5e9, #0284c7) !important;
        color: white !important;
        box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4) !important;
    }

    .modern-btn-info:hover {
        background: linear-gradient(135deg, #0284c7, #0369a1) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.6) !important;
        color: white !important;
    }

    .modern-btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        color: white !important;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4) !important;
    }

    .modern-btn-warning:hover {
        background: linear-gradient(135deg, #d97706, #b45309) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.6) !important;
        color: white !important;
    }

    .modern-btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: white !important;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4) !important;
    }

    .modern-btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.6) !important;
        color: white !important;
    }

    /* Badge pour le statut */
    .modern-badge {
        display: inline-block !important;
        padding: 6px 12px !important;
        border-radius: 20px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        background: linear-gradient(135deg, #10b981, #059669) !important;
        color: white !important;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3) !important;
    }

    /* Boutons d'action sans fond */
    .btn-action {
        background: none !important;
        border: none !important;
        color: #10b981 !important;
        font-size: 1.2rem !important;
        padding: 8px !important;
        margin: 0 3px !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        border-radius: 50% !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 40px !important;
        min-height: 40px !important;
    }

    .btn-action:hover {
        color: #059669 !important;
        transform: scale(1.1) !important;
        background: rgba(16, 185, 129, 0.1) !important;
    }

    .btn-action:active {
        transform: scale(0.95) !important;
    }

    /* Animations */
    @keyframes slideInDown {
        from {
            transform: translateY(-100%) !important;
            opacity: 0 !important;
        }
        to {
            transform: translateY(0) !important;
            opacity: 1 !important;
        }
    }

    @keyframes fadeInUp {
        from {
            transform: translateY(30px) !important;
            opacity: 0 !important;
        }
        to {
            transform: translateY(0) !important;
            opacity: 1 !important;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2.5rem !important;
        }
        
        .table-container {
            padding: 15px !important;
            margin: 10px !important;
        }
        
        .modern-table {
            font-size: 14px !important;
        }
        
        .modern-btn {
            padding: 6px 12px !important;
            font-size: 11px !important;
            margin: 2px !important;
        }
    }

    /* Effet de particules */
    .modern-particles {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        pointer-events: none !important;
        z-index: -1 !important;
    }

    .modern-particle {
        position: absolute !important;
        width: 4px !important;
        height: 4px !important;
        background: #10b981 !important;
        border-radius: 50% !important;
        animation: float 6s ease-in-out infinite !important;
        opacity: 0.6 !important;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg) !important; }
        50% { transform: translateY(-20px) rotate(180deg) !important; }
    }
</style>

<!-- Particules animées -->
<div class="modern-particles">
    <div class="modern-particle" style="left: 10%; animation-delay: 0s;"></div>
    <div class="modern-particle" style="left: 20%; animation-delay: 1s;"></div>
    <div class="modern-particle" style="left: 30%; animation-delay: 2s;"></div>
    <div class="modern-particle" style="left: 40%; animation-delay: 3s;"></div>
    <div class="modern-particle" style="left: 50%; animation-delay: 4s;"></div>
    <div class="modern-particle" style="left: 60%; animation-delay: 5s;"></div>
    <div class="modern-particle" style="left: 70%; animation-delay: 6s;"></div>
    <div class="modern-particle" style="left: 80%; animation-delay: 7s;"></div>
    <div class="modern-particle" style="left: 90%; animation-delay: 8s;"></div>
</div>

<div class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('auto_send_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-paper-plane me-2"></i>
            {{ session('auto_send_message') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h1 class="page-title">📄 Gestion des Documents</h1>

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.documents.create') }}" class="modern-btn modern-btn-info">
            <i class="fas fa-plus me-1"></i> Nouveau document
        </a>
    </div>

    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th><i class="fas fa-file me-2"></i>Titre</th>
                    <th><i class="fas fa-book me-2"></i>Module</th>
                    <th><i class="fas fa-layer-group me-2"></i>Niveau</th>
                    <th><i class="fas fa-calendar me-2"></i>Date envoi</th>
                    <th><i class="fas fa-shield-alt me-2"></i>Statut</th>
                    <th><i class="fas fa-cogs me-2"></i>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $document)
                    <tr>
                        <td>
                            <strong>{{ $document->titre }}</strong>
                        </td>
                        <td>
                            <i class="fas fa-book me-1"></i>{{ $document->module->titre ?? 'Document général' }}
                        </td>
                        <td>
                            <i class="fas fa-layer-group me-1"></i>{{ $document->niveau->nom ?? '-' }}
                        </td>
                        <td>
                            <i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($document->date_envoi)->format('d/m/Y') }}
                        </td>
                        <td>
                            <span class="modern-badge">
                                <i class="fas fa-check-circle me-1"></i>{{ $document->envoye ? 'Envoyé' : 'En attente' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.documents.show', $document) }}" class="btn-action" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.documents.edit', $document) }}" class="btn-action" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.documents.destroy', $document) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action" 
                                        onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce document ?')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Effet de clignotement pour les nouvelles notifications
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.animation = 'slideInDown 0.6s ease-out';
    });

    // Effet de survol amélioré pour les lignes du tableau
    const tableRows = document.querySelectorAll('.modern-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.01)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Animation des boutons
    const buttons = document.querySelectorAll('.modern-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            let ripple = document.createElement('span');
            ripple.classList.add('ripple');
            this.appendChild(ripple);
            
            let x = e.clientX - e.target.offsetLeft;
            let y = e.clientY - e.target.offsetTop;
            
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});
</script>

@endsection
