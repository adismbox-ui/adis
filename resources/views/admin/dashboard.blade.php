@extends('admin.layout')

@section('content')
<!-- Styles sombres pour le dashboard -->
<style>
    .gradient-text {
        background: linear-gradient(90deg, #7fb069 0%, #a7c957 60%, #fff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-fill-color: transparent;
        text-shadow: 0 2px 10px rgba(0,0,0,0.10), 0 1px 2px rgba(60,60,60,0.09);
    }
    .gradient-title {
        letter-spacing: 1.5px;
        margin-bottom: 0.5rem;
    }
    .gradient-desc {
        margin-bottom: 1.5rem;
    }
    
    .dashboard-hero {
        background: linear-gradient(135deg, rgba(127, 176, 105, 0.1) 0%, rgba(45, 80, 22, 0.2) 100%);
        border-radius: 20px;
        padding: 3rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(127, 176, 105, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #7fb069, #a7c957);
        animation: shimmer 3s ease-in-out infinite;
    }
    
    .hero-title {
        font-weight: 900;
        font-size: 3rem;
        color: #fff;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .hero-desc {
        font-weight: 700;
        font-size: 1.35rem;
        color: #fff;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }
    
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
        cursor: pointer;
        animation: slideInUp 0.8s ease-out;
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
    
    .stat-icon {
        font-size: 3rem;
        color: #7fb069;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        animation: bounce 2s infinite;
    }
    
    .stat-card:hover .stat-icon {
        color: #a7c957;
        transform: scale(1.2) rotate(10deg);
    }
    
    .stat-number, .stat-value {
        font-size: 2.5rem;
        font-weight: bold;
        color: #7fb069;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .stat-label {
        color: #fff;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .content-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(127, 176, 105, 0.2);
        border-radius: 15px;
        margin-bottom: 2rem;
        overflow: hidden;
        transition: all 0.3s ease;
        animation: fadeInUp 1s ease-out;
    }
    
    .card-header-custom {
        background: linear-gradient(135deg, rgba(127, 176, 105, 0.2) 0%, rgba(45, 80, 22, 0.3) 100%);
        color: #fff;
        border-radius: 15px 15px 0 0;
        border: none;
        padding: 1.5rem 2rem 1.2rem 2rem;
        margin-bottom: 0;
        position: relative;
        overflow: hidden;
    }
    
    .card-header-custom::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #7fb069, #a7c957);
        animation: shimmer 2s ease-in-out infinite;
    }
    
    .fade-in-section {
        opacity: 0;
        transform: translateY(50px);
        transition: all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .fade-in-section.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(50px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(100px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    @keyframes shimmer {
        0%, 100% { transform: translateX(-100%); }
        50% { transform: translateX(100%); }
    }
</style>
<!-- Fond animé et particules -->
<div class="animated-background"></div>
<div class="floating-particles" id="particles"></div>
</style>
<!-- Image de fond herbe verte avec Bootstrap -->
<!-- Overlay de fond animé -->
<div class="background-overlay"></div>
<!-- Début du contenu principal -->
<div style="position:relative; z-index:1;">

<!-- Floating Particles -->
<div class="floating-particles" id="particles"></div>

<div class="container-fluid px-4" style="margin-top:0 !important; padding-top:0 !important;">
    <!-- Hero Section -->
    <div class="dashboard-hero">
        <div class="hero-content text-center">
            <h1 class="hero-title">
                <i class="fas fa-shield-alt me-3" style="color: #7fb069;"></i>
                <span class="gradient-text">Tableau de Bord ADIS</span>
            </h1>
            <p class="hero-desc">
                <span class="gradient-text">Gestion intelligente et moderne de votre plateforme éducative</span>
            </p>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
            <i class="fas fa-check-circle me-2"></i>
            <span id="successMessage">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @else
        <div class="alert alert-success alert-dismissible fade show d-none" role="alert" id="successAlert">
            <i class="fas fa-check-circle me-2"></i>
            <span id="successMessage"></span>
            <button type="button" class="btn-close" onclick="this.parentElement.classList.add('d-none')"></button>
        </div>
    @endif

    <!-- Statistiques Utilisateurs -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card primary" onclick="animateCard(this)">
                <div class="text-center">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value">{{ $totalUtilisateurs }}</div>
                    <div class="stat-label">Utilisateurs</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card success" onclick="animateCard(this)">
                <div class="text-center">
                    <div class="stat-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-value">{{ $totalApprenants }}</div>
                    <div class="stat-label">Apprenants</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card info" onclick="animateCard(this)">
                <div class="text-center">
                    <div class="stat-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stat-value">{{ $totalFormateurs }}</div>
                    <div class="stat-label">Formateurs</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card warning" onclick="animateCard(this)">
                <div class="text-center">
                    <div class="stat-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-value">{{ $totalAssistants }}</div>
                    <div class="stat-label">Assistants</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Statistiques Formation -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card primary" onclick="animateCard(this)">
                <div class="text-center">
                    <div class="stat-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="stat-value">{{ $totalNiveaux }}</div>
                    <div class="stat-label">Niveaux</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card success" onclick="animateCard(this)">
                <div class="text-center">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-value">{{ $totalSessions }}</div>
                    <div class="stat-label">Sessions</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card info" onclick="animateCard(this)">
                <div class="text-center">
                    <div class="stat-icon">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="stat-value">{{ $sessionsEnCours }}</div>
                    <div class="stat-label">Sessions Actives</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card danger" onclick="animateCard(this)">
                <div class="text-center">
                    <div class="stat-icon">
                        <i class="fas fa-umbrella-beach"></i>
                    </div>
                    <div class="stat-value">{{ $totalVacances }}</div>
                    <div class="stat-label">Vacances</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Actions rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="action-card">
                <h5 class="text-white mb-4">
                    <i class="fas fa-bolt me-2"></i>Actions Rapides
                </h5>
                <div class="row justify-content-center mb-3">
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.sessions.create') }}" class="action-btn btn btn-primary btn-lg w-100" style="font-size:1.1rem;" onclick="showNotification('Nouvelle session programmée...')">
                            <i class="fas fa-calendar-plus me-2"></i> Nouvelle Session
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.vacances.create') }}" class="action-btn btn btn-warning btn-lg w-100" style="font-size:1.1rem;" onclick="showNotification('Période de vacances ajoutée...')">
                            <i class="fas fa-umbrella-beach me-2"></i> Nouvelle Vacance
                        </a>
                    </div>
                </div>
                <div class="row justify-content-center mb-3">
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.niveaux.create') }}" class="action-btn btn btn-success btn-lg w-100" style="font-size:1.1rem;" onclick="showNotification('Nouveau niveau en cours de création...')">
                            <i class="fas fa-plus me-2"></i> Nouveau Niveau
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.sessions.calendrier') }}" class="action-btn btn btn-info btn-lg w-100" style="font-size:1.1rem;" onclick="showNotification('Ouverture du calendrier...')">
                            <i class="fas fa-calendar me-2"></i> Voir Calendrier
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Cards -->
    <div class="row">
        <!-- Formateurs à valider -->
        <div class="col-lg-6">
            <div class="content-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Formateurs à valider ({{ $formateursAValider->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($formateursAValider as $formateur)
                                    <tr>
                                        <td>{{ $formateur->utilisateur->nom ?? '-' }} {{ $formateur->utilisateur->prenom ?? '-' }}</td>
                                        <td>{{ $formateur->utilisateur->email ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.formateur.show', $formateur->id) }}" class="btn btn-info btn-sm btn-3d me-1" onclick="showNotification('Profil consulté')">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.formateur.valider', $formateur->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm btn-3d" onclick="showNotification('Formateur validé avec succès !'); return confirm('Valider ce formateur ?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center">Aucun formateur à valider</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Demandes de paiement -->
        <div class="col-lg-6">
            <div class="content-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        Demandes de paiement ({{ $demandesPaiementEnAttente->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th>Apprenant</th>
                                    <th>Module</th>
                                    <th>Montant</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($demandesPaiementEnAttente as $paiement)
                                    <tr>
                                        <td>{{ $paiement->apprenant->utilisateur->nom ?? '-' }} {{ $paiement->apprenant->utilisateur->prenom ?? '-' }}</td>
                                        <td>{{ $paiement->module->titre ?? '-' }}</td>
                                        <td>{{ number_format($paiement->montant, 0, ',', ' ') }} F</td>
                                        <td>
                                            <form action="{{ route('admin.paiements.valider', $paiement->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm btn-3d me-1" onclick="showNotification('Paiement validé avec succès !'); return confirm('Valider ce paiement ?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.paiements.refuser', $paiement->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm btn-3d" onclick="showNotification('Paiement refusé !'); return confirm('Refuser ce paiement ?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">Aucune demande de paiement</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts modernes du dashboard -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script>
// Particules flottantes dynamiques
(function() {
    const particlesContainer = document.getElementById('particles');
    if (!particlesContainer) return;
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
// Animation fade-in au scroll
const faders = document.querySelectorAll('.fade-in-section');
const appearOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
const appearOnScroll = new IntersectionObserver(function(entries, observer) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        }
    });
}, appearOptions);
faders.forEach(fader => { appearOnScroll.observe(fader); });
</script>
@endsection
<!-- Fin du contenu principal -->
</div> 