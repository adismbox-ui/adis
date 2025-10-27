@extends('assistants.layout')

@section('content')
<style>
    /* Styles spécifiques pour la page calendrier */
    .calendar-header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        animation: slideInDown 0.8s ease-out;
        position: relative;
        overflow: hidden;
    }

    .calendar-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #43ea4a, #2ecc40, #27ae60);
        animation: shimmer 2s ease-in-out infinite;
    }

    .calendar-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        animation: slideInUp 0.8s ease-out;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .calendar-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #43ea4a, #2ecc40, #27ae60);
        animation: shimmer 3s ease-in-out infinite;
    }

    .calendar-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(67, 234, 74, 0.2);
    }

    .session-item {
        background: linear-gradient(135deg, rgba(67, 234, 74, 0.1) 0%, rgba(46, 204, 64, 0.1) 100%);
        border: 1px solid rgba(67, 234, 74, 0.2);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .session-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #43ea4a, #2ecc40);
    }

    .session-item:hover {
        transform: translateX(8px);
        box-shadow: 0 8px 25px rgba(67, 234, 74, 0.2);
    }

    .vacance-item {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 152, 0, 0.1) 100%);
        border: 1px solid rgba(255, 193, 7, 0.2);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .vacance-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #ffc107, #ff9800);
    }

    .vacance-item:hover {
        transform: translateX(8px);
        box-shadow: 0 8px 25px rgba(255, 193, 7, 0.2);
    }

    .item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .item-type {
        display: flex;
        align-items: center;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .item-type.session {
        color: #2ecc40;
    }

    .item-type.vacance {
        color: #ff9800;
    }

    .item-status {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .status-active {
        background: linear-gradient(135deg, #27ae60, #2ecc40);
        color: white;
    }

    .status-inactive {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
    }

    .item-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .detail-item {
        text-align: center;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 12px;
        backdrop-filter: blur(10px);
    }

    .detail-label {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 0.5rem;
    }

    .detail-value {
        font-weight: 600;
        color: #2ecc40;
    }

    .legend-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        animation: slideInRight 0.8s ease-out;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        padding: 0.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .legend-item:hover {
        background: rgba(67, 234, 74, 0.1);
        transform: translateX(5px);
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        margin-right: 1rem;
    }

    .legend-color.session {
        background: linear-gradient(135deg, #43ea4a, #2ecc40);
    }

    .legend-color.vacance {
        background: linear-gradient(135deg, #ffc107, #ff9800);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        animation: slideInUp 0.8s ease-out;
    }

    .empty-icon {
        font-size: 4rem;
        color: #43ea4a;
        margin-bottom: 1rem;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .item-details {
            grid-template-columns: 1fr;
        }
        
        .calendar-header {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
    }
</style>

<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="calendar-header animate-on-scroll">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-2" style="color: #2ecc40;">
                    <i class="fas fa-calendar me-2"></i>Calendrier des Sessions
                </h1>
                <p class="text-muted mb-0">Gérez les sessions de formation et les périodes de vacances</p>
            </div>
            <a href="{{ route('assistant.calendrier.create') }}" class="btn btn-gradient">
                <i class="fas fa-plus me-2"></i> Nouvelle Session
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Contenu principal -->
        <div class="col-lg-8">
            <div class="calendar-card p-4 animate-on-scroll">
                <h4 class="mb-4" style="color: #2ecc40;">
                    <i class="fas fa-calendar-alt me-2"></i>Sessions et Vacances
                </h4>

                @if(($sessions->count() ?? 0) > 0 || ($vacances->count() ?? 0) > 0)
                    <!-- Sessions -->
                    @foreach($sessions as $session)
                        <div class="session-item animate-on-scroll">
                            <div class="item-header">
                                <div class="item-type session">
                                    <i class="fas fa-calendar-alt me-2"></i>Session
                                </div>
                                <span class="item-status {{ ($session->actif ?? true) ? 'status-active' : 'status-inactive' }}">
                                    <i class="fas {{ ($session->actif ?? true) ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                    {{ ($session->actif ?? true) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            
                            <h5 class="mb-3" style="color: #2ecc40;">{{ $session->nom }}</h5>
                            @if($session->niveau)
                                <p class="text-muted mb-3">
                                    <i class="fas fa-layer-group me-1"></i>{{ $session->niveau->nom }}
                                </p>
                            @endif
                            
                            <div class="item-details">
                                <div class="detail-item">
                                    <div class="detail-label">Date de début</div>
                                    <div class="detail-value">
                                        {{ $session->date_debut ? \Carbon\Carbon::parse($session->date_debut)->format('d/m/Y') : '-' }}
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Date de fin</div>
                                    <div class="detail-value">
                                        {{ $session->date_fin ? \Carbon\Carbon::parse($session->date_fin)->format('d/m/Y') : '-' }}
                                    </div>
                                </div>
                                @if($session->heure_debut && $session->heure_fin)
                                    <div class="detail-item">
                                        <div class="detail-label">Heure début</div>
                                        <div class="detail-value">
                                            {{ \Carbon\Carbon::parse($session->heure_debut)->format('H:i') }}
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Heure fin</div>
                                        <div class="detail-value">
                                            {{ \Carbon\Carbon::parse($session->heure_fin)->format('H:i') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('assistant.calendrier.edit', $session) }}" class="btn btn-secondary-gradient btn-sm">
                                    <i class="fas fa-edit me-1"></i> Modifier
                                </a>
                            </div>
                        </div>
                    @endforeach

                    <!-- Vacances -->
                    @foreach($vacances as $vacance)
                        <div class="vacance-item animate-on-scroll">
                            <div class="item-header">
                                <div class="item-type vacance">
                                    <i class="fas fa-umbrella-beach me-2"></i>Vacances
                                </div>
                                <span class="item-status {{ ($vacance->actif ?? true) ? 'status-active' : 'status-inactive' }}">
                                    <i class="fas {{ ($vacance->actif ?? true) ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                    {{ ($vacance->actif ?? true) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            
                            <h5 class="mb-3" style="color: #ff9800;">{{ $vacance->nom }}</h5>
                            
                            <div class="item-details">
                                <div class="detail-item">
                                    <div class="detail-label">Date de début</div>
                                    <div class="detail-value">
                                        {{ $vacance->date_debut ? \Carbon\Carbon::parse($vacance->date_debut)->format('d/m/Y') : '-' }}
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Date de fin</div>
                                    <div class="detail-value">
                                        {{ $vacance->date_fin ? \Carbon\Carbon::parse($vacance->date_fin)->format('d/m/Y') : '-' }}
                                    </div>
                                </div>
                                @php 
                                    $duree = $vacance->date_debut && $vacance->date_fin ? 
                                        \Carbon\Carbon::parse($vacance->date_debut)->diffInDays(\Carbon\Carbon::parse($vacance->date_fin)) + 1 : null; 
                                @endphp
                                @if($duree)
                                    <div class="detail-item">
                                        <div class="detail-label">Durée</div>
                                        <div class="detail-value">{{ $duree }} jour(s)</div>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('assistant.vacances.edit', $vacance) }}" class="btn btn-secondary-gradient btn-sm">
                                    <i class="fas fa-edit me-1"></i> Modifier
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state animate-on-scroll">
                        <i class="fas fa-calendar empty-icon"></i>
                        <h3 style="color: #2ecc40;">Aucune session ou vacance programmée</h3>
                        <p class="text-muted mb-4">Commencez par créer votre première session ou période de vacances.</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('assistant.calendrier.create') }}" class="btn btn-gradient">
                                <i class="fas fa-plus me-2"></i> Créer une session
                            </a>
                            <a href="{{ route('assistant.vacances.create') }}" class="btn btn-secondary-gradient">
                                <i class="fas fa-umbrella-beach me-2"></i> Créer des vacances
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar avec légende et actions rapides -->
        <div class="col-lg-4">
            <div class="legend-card p-4 animate-on-scroll">
                <h4 class="mb-4" style="color: #2ecc40;">
                    <i class="fas fa-info-circle me-2"></i>Légende
                </h4>
                
                <div class="legend-item">
                    <div class="legend-color session"></div>
                    <span>Sessions de formation</span>
                </div>
                
                <div class="legend-item">
                    <div class="legend-color vacance"></div>
                    <span>Périodes de vacances</span>
                </div>
                
                <hr class="my-4">
                
                <h5 class="mb-3" style="color: #2ecc40;">Actions rapides</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('assistant.calendrier.create') }}" class="btn btn-gradient btn-sm">
                        <i class="fas fa-plus me-1"></i> Nouvelle Session
                    </a>
                    <a href="{{ route('assistant.vacances.create') }}" class="btn btn-secondary-gradient btn-sm">
                        <i class="fas fa-umbrella-beach me-1"></i> Nouvelle Vacance
                    </a>
                    <a href="{{ route('assistant.niveaux') }}" class="btn btn-secondary-gradient btn-sm">
                        <i class="fas fa-layer-group me-1"></i> Gérer les Niveaux
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation au scroll
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

    // Animation des cartes au hover
    document.querySelectorAll('.calendar-card, .session-item, .vacance-item').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Animation des boutons
    document.querySelectorAll('.btn-gradient, .btn-secondary-gradient').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.02)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Animation des éléments de légende
    document.querySelectorAll('.legend-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
});
</script>
@endsection