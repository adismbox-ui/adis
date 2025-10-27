@extends('assistants.layout')

@section('content')
<style>
/* Fond sombre animé */
body {
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 30%, rgba(45,80,22,0.1) 100%);
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}

/* Particules flottantes */
.particles {
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
    background: rgba(127, 176, 105, 0.6);
    border-radius: 50%;
    animation: float 15s infinite linear;
}

@keyframes float {
    0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
}

/* Conteneur principal */
.main-container {
    position: relative;
    z-index: 1;
    padding: 2rem;
    animation: fadeInUp 1s ease-out;
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

/* Carte principale */
.card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(127, 176, 105, 0.3);
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    position: relative;
    transition: all 0.3s ease;
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(127, 176, 105, 0.2), transparent);
    transition: left 0.5s;
}

.card:hover::before {
    left: 100%;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 50px rgba(127, 176, 105, 0.3);
    border-color: rgba(127, 176, 105, 0.5);
}

/* En-tête de carte */
.card-header {
    background: linear-gradient(135deg, rgba(127, 176, 105, 0.2) 0%, rgba(45, 80, 22, 0.3) 100%);
    border-bottom: 2px solid rgba(127, 176, 105, 0.3);
    color: #ffffff;
    font-weight: 700;
    position: relative;
    overflow: hidden;
}

.card-header::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Corps de carte */
.card-body {
    padding: 2rem;
    color: #ffffff;
}

/* Titre principal */
h1 {
    color: #ffffff;
    font-size: 2.5rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 2rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    background: linear-gradient(135deg, #7fb069, #a7c957);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: glow 2s ease-in-out infinite alternate;
}

@keyframes glow {
    from { filter: drop-shadow(0 0 5px rgba(127, 176, 105, 0.5)); }
    to { filter: drop-shadow(0 0 20px rgba(127, 176, 105, 0.8)); }
}

/* Tableau */
.table {
    background: transparent;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    border: none;
    margin: 0;
}

/* En-têtes du tableau */
.table thead tr th {
    background: linear-gradient(135deg, #4a7c59, #2d5016);
    color: #ffffff;
    font-weight: 600;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 20px 15px;
    border: none;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    position: relative;
    border-bottom: 3px solid #7fb069;
}

.table thead tr th::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #7fb069, transparent);
}

/* Cellules du tableau */
.table tbody tr td {
    background: rgba(30, 41, 59, 0.8);
    color: #e2e8f0;
    font-weight: 500;
    font-size: 15px;
    padding: 18px 15px;
    border: 1px solid rgba(127, 176, 105, 0.1);
    transition: all 0.3s ease;
    vertical-align: middle;
}

/* Effet hover sur les lignes */
.table tbody tr {
    transition: all 0.3s ease;
    cursor: pointer;
}

.table tbody tr:hover {
    background: rgba(127, 176, 105, 0.1);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(127, 176, 105, 0.2);
}

.table tbody tr:hover td {
    background: rgba(127, 176, 105, 0.15);
    color: #ffffff;
    text-shadow: 0 0 10px rgba(127, 176, 105, 0.5);
}

/* Lignes de sessions */
.table tbody tr.session-row {
    background: rgba(127, 176, 105, 0.1);
    border-left: 4px solid #7fb069;
}

.table tbody tr.session-row:hover {
    background: rgba(127, 176, 105, 0.2);
}

/* Lignes de vacances */
.table tbody tr.vacance-row {
    background: rgba(245, 158, 11, 0.1);
    border-left: 4px solid #f59e0b;
}

.table tbody tr.vacance-row:hover {
    background: rgba(245, 158, 11, 0.2);
}

/* Boutons */
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

.btn-success {
    background: linear-gradient(135deg, #7fb069, #a7c957);
    color: white;
    box-shadow: 0 4px 15px rgba(127, 176, 105, 0.4);
}

.btn-success:hover {
    background: linear-gradient(135deg, #a7c957, #7fb069);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(127, 176, 105, 0.6);
    color: white;
}

.btn-outline-primary {
    background: transparent;
    border: 2px solid #7fb069;
    color: #7fb069;
    box-shadow: 0 4px 15px rgba(127, 176, 105, 0.2);
}

.btn-outline-primary:hover {
    background: #7fb069;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(127, 176, 105, 0.4);
}

.btn-outline-warning {
    background: transparent;
    border: 2px solid #f59e0b;
    color: #f59e0b;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);
}

.btn-outline-warning:hover {
    background: #f59e0b;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
}

/* Badges */
.badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: white;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.badge-success {
    background: linear-gradient(135deg, #7fb069, #a7c957);
}

.badge-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

/* Responsive */
@media (max-width: 768px) {
    .main-container {
        padding: 1rem;
    }
    
    h1 {
        font-size: 2rem;
    }
    
    .card-body {
        padding: 1.5rem;
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

/* Animation d'entrée pour les lignes */
.table tbody tr {
    animation: slideInUp 0.6s ease-out;
    animation-fill-mode: both;
}

.table tbody tr:nth-child(1) { animation-delay: 0.1s; }
.table tbody tr:nth-child(2) { animation-delay: 0.2s; }
.table tbody tr:nth-child(3) { animation-delay: 0.3s; }
.table tbody tr:nth-child(4) { animation-delay: 0.4s; }
.table tbody tr:nth-child(5) { animation-delay: 0.5s; }
.table tbody tr:nth-child(6) { animation-delay: 0.6s; }
.table tbody tr:nth-child(7) { animation-delay: 0.7s; }
.table tbody tr:nth-child(8) { animation-delay: 0.8s; }
.table tbody tr:nth-child(9) { animation-delay: 0.9s; }
.table tbody tr:nth-child(10) { animation-delay: 1.0s; }

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
</style>

<!-- Particules animées -->
<div class="particles">
    <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
    <div class="particle" style="left: 20%; animation-delay: 2s;"></div>
    <div class="particle" style="left: 30%; animation-delay: 4s;"></div>
    <div class="particle" style="left: 40%; animation-delay: 6s;"></div>
    <div class="particle" style="left: 50%; animation-delay: 8s;"></div>
    <div class="particle" style="left: 60%; animation-delay: 10s;"></div>
    <div class="particle" style="left: 70%; animation-delay: 12s;"></div>
    <div class="particle" style="left: 80%; animation-delay: 14s;"></div>
    <div class="particle" style="left: 90%; animation-delay: 16s;"></div>
</div>

<div class="main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-calendar-alt me-2"></i> Calendrier des Formations
        </h1>
        <div>
            <a href="{{ route('assistant.formations.index') }}" class="btn btn-info me-2">
                <i class="fas fa-list me-1"></i> Liste
            </a>
            <a href="{{ route('assistant.formations.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Nouvelle Formation
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-calendar me-2"></i> Calendrier des Formations et Vacances
                    </h6>
                </div>
                <div class="card-body">
                    @if($sessions->count() > 0 || $vacances->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-tag me-2"></i>Type</th>
                                        <th><i class="fas fa-graduation-cap me-2"></i>Nom</th>
                                        <th><i class="fas fa-calendar-range me-2"></i>Période</th>
                                        <th><i class="fas fa-shield-alt me-2"></i>Statut</th>
                                        <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sessions as $session)
                                        <tr class="session-row">
                                            <td>
                                                <i class="fas fa-calendar-alt text-success me-2"></i>
                                                <strong>Formation</strong>
                                            </td>
                                            <td>
                                                <strong>{{ $session->nom }}</strong>
                                                @if($session->description)
                                                    <br><small class="text-muted">{{ Str::limit($session->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="fas fa-play me-1"></i>{{ $session->date_debut->format('d/m/Y') }} 
                                                    <i class="fas fa-stop me-1"></i>{{ $session->date_fin->format('d/m/Y') }}
                                                </div>
                                                @if($session->heure_debut && $session->heure_fin)
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>{{ $session->heure_debut->format('H:i') }} - {{ $session->heure_fin->format('H:i') }}
                                                    </small>
                                                @endif
                                                @if($session->jour_semaine)
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-calendar-day me-1"></i>{{ $session->jour_semaine }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($session->actif)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle me-1"></i>Active
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('assistant.formations.edit', $session) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    @foreach($vacances as $vacance)
                                        <tr class="vacance-row">
                                            <td>
                                                <i class="fas fa-umbrella-beach text-warning me-2"></i>
                                                <strong>Vacances</strong>
                                            </td>
                                            <td>
                                                <strong>{{ $vacance->nom }}</strong>
                                                @if($vacance->description)
                                                    <br><small class="text-muted">{{ Str::limit($vacance->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="fas fa-play me-1"></i>{{ $vacance->date_debut->format('d/m/Y') }} 
                                                    <i class="fas fa-stop me-1"></i>{{ $vacance->date_fin->format('d/m/Y') }}
                                                </div>
                                                @php
                                                    $duree = $vacance->date_debut->diffInDays($vacance->date_fin) + 1;
                                                @endphp
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-day me-1"></i>{{ $duree }} jour(s)
                                                </small>
                                            </td>
                                            <td>
                                                @if($vacance->actif)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle me-1"></i>Active
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('assistant.vacances.edit', $vacance) }}" class="btn btn-outline-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Aucune formation ou vacance programmée</h4>
                            <p class="text-muted">Commencez par créer une formation ou des vacances pour voir le calendrier.</p>
                            <div class="mt-3">
                                <a href="{{ route('assistant.formations.create') }}" class="btn btn-success me-2">
                                    <i class="fas fa-plus me-1"></i>Créer une formation
                                </a>
                                <a href="{{ route('assistant.vacances.create') }}" class="btn btn-info">
                                    <i class="fas fa-plus me-1"></i>Créer des vacances
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
