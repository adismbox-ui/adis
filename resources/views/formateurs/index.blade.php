@extends('admin.layout')

@section('content')
<style>
    /* Background avec image et overlay */
    body {
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(22, 101, 52, 0.9)), 
                    url('https://images.unsplash.com/photo-1518837695005-2083093ee35b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
        background-size: cover;
        background-attachment: fixed;
        background-position: center;
        min-height: 100vh;
        margin: 0;
        padding: 20px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Alert personnalisé */
    .alert {
        border: none;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        animation: slideInDown 0.6s ease-out;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.9), rgba(22, 163, 74, 0.9));
        color: white;
        border-left: 5px solid #10b981;
    }

    /* Titre principal */
    h1 {
        color: #ffffff;
        font-size: 3.5rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 40px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        background: linear-gradient(135deg, #10b981, #34d399);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: glow 2s ease-in-out infinite alternate;
    }

    @keyframes glow {
        from { filter: drop-shadow(0 0 5px rgba(16, 185, 129, 0.5)); }
        to { filter: drop-shadow(0 0 20px rgba(16, 185, 129, 0.8)); }
    }

    /* Container du tableau */
    .table-container {
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        animation: fadeInUp 0.8s ease-out;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Table principale */
    .table {
        background: transparent;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        border: none;
        margin: 0;
        min-width: 1000px;
    }

    /* En-têtes du tableau */
    .table thead tr th {
        background: linear-gradient(135deg, #065f46, #047857);
        color: #ffffff;
        font-weight: 600;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 20px 15px;
        border: none;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        position: relative;
        border-bottom: 3px solid #10b981;
    }

    .table thead tr th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #10b981, transparent);
    }

    /* Cellules du tableau */
    .table tbody tr td {
        background: rgba(30, 41, 59, 0.8);
        color: #e2e8f0;
        font-weight: 500;
        font-size: 15px;
        padding: 18px 15px;
        border: 1px solid rgba(16, 185, 129, 0.1);
        transition: all 0.3s ease;
        vertical-align: middle;
    }

    /* Effet hover sur les lignes */
    .table tbody tr {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .table tbody tr:hover {
        background: rgba(16, 185, 129, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2);
    }

    .table tbody tr:hover td {
        background: rgba(16, 185, 129, 0.15);
        color: #ffffff;
        text-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }

    /* Boutons d'action */
    .btn {
        border: none;
        border-radius: 25px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0 3px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-info {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        color: white;
        box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
    }

    .btn-info:hover {
        background: linear-gradient(135deg, #0284c7, #0369a1);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.6);
        color: white;
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #d97706, #b45309);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.6);
        color: white;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.6);
        color: white;
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.6);
        color: white;
    }

    /* Badge pour le statut */
    .badge-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    }

    /* Boutons d'action sans fond */
    .btn-action {
        background: none;
        border: none;
        color: #10b981;
        font-size: 1.2rem;
        padding: 8px;
        margin: 0 3px;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        min-height: 40px;
    }

    .btn-action:hover {
        color: #059669;
        transform: scale(1.1);
        background: rgba(16, 185, 129, 0.1);
    }

    .btn-action:active {
        transform: scale(0.95);
    }

    /* Animations */
    @keyframes slideInDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes fadeInUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        h1 {
            font-size: 2.5rem;
        }
        
        .table-container {
            padding: 15px;
            margin: 10px;
        }
        
        .table {
            font-size: 14px;
        }
        
        .btn {
            padding: 6px 12px;
            font-size: 11px;
            margin: 2px;
        }
    }

    /* Effet de particules */
    .particles {
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
        width: 4px;
        height: 4px;
        background: #10b981;
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
        opacity: 0.6;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }
</style>

<!-- Particules animées -->
<div class="particles">
    <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
    <div class="particle" style="left: 20%; animation-delay: 1s;"></div>
    <div class="particle" style="left: 30%; animation-delay: 2s;"></div>
    <div class="particle" style="left: 40%; animation-delay: 3s;"></div>
    <div class="particle" style="left: 50%; animation-delay: 4s;"></div>
    <div class="particle" style="left: 60%; animation-delay: 5s;"></div>
    <div class="particle" style="left: 70%; animation-delay: 6s;"></div>
    <div class="particle" style="left: 80%; animation-delay: 7s;"></div>
    <div class="particle" style="left: 90%; animation-delay: 8s;"></div>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">👨‍🏫 Gestion des Formateurs</h1>
        <a href="{{ route('formateurs.create') }}" class="btn btn-success">
            <i class="fas fa-user-plus me-1"></i> Nouveau formateur
        </a>
    </div>
    <form method="GET" action="{{ route('formateurs.index') }}" class="mb-3 d-flex" role="search">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control me-2" placeholder="Rechercher par nom, email, téléphone, ID...">
        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
    </form>

    <div class="table-container">
        <table class="table">
    <thead>
        <tr>
                    <th><i class="fas fa-user me-2"></i>Prénom</th>
                    <th><i class="fas fa-id-card me-2"></i>Nom</th>
                    <th><i class="fas fa-envelope me-2"></i>Email</th>
                    <th><i class="fas fa-phone me-2"></i>Téléphone</th>
                    <th><i class="fas fa-shield-alt me-2"></i>Statut</th>
                    <th><i class="fas fa-layer-group me-2"></i>Niveaux</th>
                    <th><i class="fas fa-cogs me-2"></i>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($formateurs as $formateur)
            <tr>
                        <td>
                            <strong>{{ $formateur->utilisateur->prenom ?? 'N/A' }}</strong>
                        </td>
                        <td>
                            <strong>{{ $formateur->utilisateur->nom ?? 'N/A' }}</strong>
                        </td>
                        <td>
                            <i class="fas fa-at me-1"></i>{{ $formateur->utilisateur->email ?? 'N/A' }}
                        </td>
                        <td>
                            <i class="fas fa-phone me-1"></i>{{ $formateur->utilisateur->telephone ?? 'N/A' }}
                        </td>
                        <td>
                            @php($actif = $formateur->utilisateur->actif ?? true)
                            <span class="badge-status">
                                <i class="fas fa-{{ $actif ? 'check-circle' : 'ban' }} me-1"></i>{{ $actif ? 'Actif' : 'Désactivé' }}
                            </span>
                        </td>
                        <td>
                            @if($formateur->niveaux && $formateur->niveaux->count())
                                @foreach($formateur->niveaux as $niveau)
                                    <span class="badge bg-secondary me-1 mb-1"><i class="fas fa-layer-group me-1"></i>{{ $niveau->nom }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Aucun</span>
                            @endif
                        </td>
                <td>
                            <a href="{{ route('formateurs.show', $formateur) }}" class="btn-action" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('formateurs.edit', $formateur) }}" class="btn-action" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                    <form action="{{ route('formateurs.toggle-activation', $formateur) }}" method="POST" style="display:inline-block"
                          onsubmit="return confirm('Confirmer la bascule d\'activation pour ce formateur ?')">
                        @csrf
                        <button type="submit" class="btn-action" title="{{ $actif ? 'Désactiver' : 'Activer' }}">
                            <i class="fas fa-{{ $actif ? 'user-slash' : 'user-check' }}"></i>
                        </button>
                    </form>
                            <form action="{{ route('formateurs.destroy', $formateur) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                                <button type="submit" class="btn-action" 
                                        onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce formateur ?')" title="Supprimer">
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

<!-- Script pour les effets interactifs -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Effet de clignotement pour les nouvelles notifications
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.animation = 'slideInDown 0.6s ease-out';
    });

    // Effet de survol amélioré pour les lignes du tableau
    const tableRows = document.querySelectorAll('.table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.01)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Animation des boutons
    const buttons = document.querySelectorAll('.btn');
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