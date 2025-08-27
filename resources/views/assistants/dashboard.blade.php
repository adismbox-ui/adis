@extends('assistants.layout')

@section('content')
<style>
    :root {
        --primary-green: #2d5016;
        --secondary-green: #4a7c59;
        --accent-green: #7fb069;
        --light-green: #a7c957;
        --bg-green: #bc4749;
        --white-glass: rgba(255, 255, 255, 0.95);
        --green-glass: rgba(127, 176, 105, 0.2);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #2d5016 0%, #4a7c59 50%, #7fb069 100%);
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }
    .animated-background {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2;
        background: url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80') center/cover no-repeat;
        animation: slowZoom 20s ease-in-out infinite alternate;
    }
    .animated-background::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(135deg, rgba(45,80,22,0.45) 0%, rgba(74,124,89,0.28) 50%, rgba(127,176,105,0.17) 100%);
        z-index: 1;
    }
    @keyframes slowZoom { 0% { transform: scale(1); } 100% { transform: scale(1.1); } }
    .floating-particles { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: -1; }
    .particle { position: absolute; background: rgba(167, 201, 87, 0.6); border-radius: 50%; animation: float 15s infinite linear; }
    @keyframes float { 0% { transform: translateY(100vh) rotate(0deg); opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { transform: translateY(-100px) rotate(360deg); opacity: 0; } }
    .hero-section { background: linear-gradient(135deg, rgba(45,80,22,0.9) 0%, rgba(74,124,89,0.8) 100%); backdrop-filter: blur(15px); border-bottom: 1px solid rgba(167, 201, 87, 0.3); padding: 4rem 0; margin-bottom: 2rem; position: relative; overflow: hidden; }
    .hero-section::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(167, 201, 87, 0.1) 0%, transparent 70%); animation: rotate 20s linear infinite; }
    @keyframes rotate { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .hero-content { position: relative; z-index: 2; }
    .hero-title { font-size: 3.5rem; font-weight: 700; color: #fff; text-shadow: 0 4px 20px rgba(0,0,0,0.5); margin-bottom: 1rem; animation: fadeInDown 1s ease-out; }
    .hero-subtitle { font-size: 1.3rem; color: rgba(255,255,255,0.9); margin-bottom: 0.5rem; animation: fadeInUp 1s ease-out 0.3s both; }
    .hero-info { color: rgba(255,255,255,0.7); animation: fadeInUp 1s ease-out 0.6s both; }
    .stats-container { margin-bottom: 3rem; }
    .stat-card { background: var(--white-glass); backdrop-filter: blur(20px); border: 1px solid rgba(167, 201, 87, 0.3); border-radius: 20px; padding: 2rem; margin-bottom: 2rem; position: relative; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); overflow: hidden; cursor: pointer; animation: slideInUp 0.8s ease-out; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(167, 201, 87, 0.4), transparent); transition: left 0.5s; }
    .stat-card:hover::before { left: 100%; }
    .stat-card:hover { transform: translateY(-15px) scale(1.05); box-shadow: 0 25px 50px rgba(45, 80, 22, 0.3); border-color: var(--accent-green); }
    .stat-icon { font-size: 3rem; color: var(--primary-green); margin-bottom: 1rem; transition: all 0.3s ease; animation: bounce 2s infinite; }
    .stat-card:hover .stat-icon { color: var(--accent-green); transform: scale(1.2) rotate(10deg); }
    .stat-number { font-size: 2.5rem; font-weight: bold; color: var(--primary-green); margin-bottom: 0.5rem; }
    .stat-label { color: var(--secondary-green); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
    .content-card { background: var(--white-glass); backdrop-filter: blur(20px); border: 1px solid rgba(167, 201, 87, 0.3); border-radius: 20px; margin-bottom: 2rem; overflow: hidden; transition: all 0.3s ease; animation: fadeInUp 1s ease-out; }
    .content-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(45, 80, 22, 0.2); }
    .card-header-custom { background: linear-gradient(135deg, var(--primary-green), var(--secondary-green)); color: white; padding: 1.5rem; border: none; position: relative; overflow: hidden; }
    .card-header-custom::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: left 0.8s; }
    .content-card:hover .card-header-custom::before { left: 100%; }
    .card-title { font-size: 1.2rem; font-weight: 700; margin: 0; }
    .table-custom { margin: 0; }
    .table-custom thead th { background: rgba(167, 201, 87, 0.2); color: var(--primary-green); font-weight: 700; border: none; padding: 1rem; }
    .table-custom tbody tr { transition: all 0.3s ease; border: none; }
    .table-custom tbody tr:hover { background: rgba(167, 201, 87, 0.1); transform: scale(1.02); }
    .table-custom td { padding: 1rem; border: none; vertical-align: middle; }
    .avatar { width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-green), var(--light-green)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem; margin-right: 1rem; border: 3px solid rgba(167, 201, 87, 0.5); transition: all 0.3s ease; animation: pulse 2s infinite; }
    .avatar:hover { transform: scale(1.1); border-color: var(--accent-green); }
    .badge-custom { background: linear-gradient(135deg, var(--accent-green), var(--light-green)); color: white; padding: 0.5rem 1rem; border-radius: 25px; font-weight: 600; box-shadow: 0 4px 15px rgba(127, 176, 105, 0.4); transition: all 0.3s ease; animation: slideInRight 0.8s ease-out; }
    .badge-custom:hover { transform: scale(1.1); box-shadow: 0 6px 20px rgba(127, 176, 105, 0.6); }
    .session-item { border-left: 4px solid var(--accent-green); background: rgba(167, 201, 87, 0.1); padding: 1.5rem; margin-bottom: 1rem; border-radius: 0 15px 15px 0; transition: all 0.3s ease; position: relative; overflow: hidden; }
    .session-item::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: linear-gradient(to bottom, var(--accent-green), var(--light-green)); transition: width 0.3s ease; }
    .session-item:hover::before { width: 100%; opacity: 0.1; }
    .session-item:hover { transform: translateX(10px); box-shadow: 0 10px 25px rgba(45, 80, 22, 0.2); }
    .session-date { background: linear-gradient(135deg, var(--primary-green), var(--secondary-green)); color: white; padding: 1rem; border-radius: 15px; text-align: center; min-width: 80px; box-shadow: 0 8px 20px rgba(45, 80, 22, 0.3); }
    .student-card { background: var(--white-glass); backdrop-filter: blur(15px); border: 1px solid rgba(167, 201, 87, 0.3); border-radius: 15px; padding: 1.5rem; margin-bottom: 1rem; transition: all 0.3s ease; position: relative; overflow: hidden; }
    .student-card::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(167, 201, 87, 0.1) 0%, transparent 50%); opacity: 0; transition: opacity 0.3s ease; }
    .student-card:hover::before { opacity: 1; }
    .student-card:hover { transform: translateY(-5px) rotate(1deg); box-shadow: 0 15px 30px rgba(45, 80, 22, 0.2); border-color: var(--accent-green); }
    @keyframes fadeInDown { from { opacity: 0; transform: translateY(-50px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideInUp { from { opacity: 0; transform: translateY(100px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideInRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(167, 201, 87, 0.7); } 50% { box-shadow: 0 0 0 10px rgba(167, 201, 87, 0); } }
    @keyframes bounce { 0%, 20%, 50%, 80%, 100% { transform: translateY(0); } 40% { transform: translateY(-10px); } 60% { transform: translateY(-5px); } }
    .empty-state { text-align: center; padding: 3rem; color: var(--secondary-green); }
    .empty-state i { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; animation: float 3s ease-in-out infinite; }
    @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
    @media (max-width: 768px) { .hero-title { font-size: 2.5rem; } .stat-card { padding: 1.5rem; } .hero-section { padding: 2rem 0; } }
    .fade-in-section { opacity: 0; transform: translateY(50px); transition: all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .fade-in-section.is-visible { opacity: 1; transform: translateY(0); }
</style>

<!-- Fond animé -->
<div class="animated-background"></div>
<!-- Particules flottantes -->
<div class="floating-particles" id="particles"></div>
<!-- Section Hero -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content text-center text-white">
            <h1 class="hero-title">
                <i class="fas fa-leaf me-3"></i>Dashboard Assistant
                    </h1>
            <p class="hero-subtitle">Bienvenue, {{ Auth::user()->prenom }} {{ Auth::user()->nom }}</p>
            <small class="hero-info">Dernière connexion : {{ now()->format('d/m/Y à H:i') }}</small>
        </div>
    </div>
</section>

<div class="container">
    <!-- Statistiques -->
    <section class="stats-container fade-in-section">
        <div class="row g-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card text-center" style="animation-delay: 0.1s">
                    <i class="fas fa-users stat-icon"></i>
                    <div class="stat-number">{{ $totalApprenants }}</div>
                    <div class="stat-label">Total Apprenants</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card text-center" style="animation-delay: 0.2s">
                    <i class="fas fa-chalkboard-teacher stat-icon"></i>
                    <div class="stat-number">{{ $totalFormateurs }}</div>
                    <div class="stat-label">Total Formateurs</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card text-center" style="animation-delay: 0.3s">
                    <i class="fas fa-book stat-icon"></i>
                    <div class="stat-number">{{ $totalModules }}</div>
                    <div class="stat-label">Total Modules</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card text-center" style="animation-delay: 0.4s">
                    <i class="fas fa-clipboard-list stat-icon"></i>
                    <div class="stat-number">{{ $totalInscriptions }}</div>
                    <div class="stat-label">Total Inscriptions</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenu principal -->
    <div class="row g-4">
        <!-- Inscriptions récentes -->
        <div class="col-lg-6">
            <div class="content-card fade-in-section">
                <div class="card-header-custom">
                    <h6 class="card-title">
                        <i class="fas fa-user-plus me-2"></i>Inscriptions récentes (7 derniers jours)
                    </h6>
                </div>
                <div class="card-body p-0">
                        <div class="table-responsive">
                        <table class="table table-custom">
                                <thead>
                                    <tr>
                                        <th>Apprenant</th>
                                        <th>Module</th>
                                    <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($inscriptionsRecentes as $inscription)
                                <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                            <div class="avatar">
                                                        {{ isset($inscription->apprenant) ? (substr($inscription->apprenant->prenom ?? 'I', 0, 1) . substr($inscription->apprenant->nom ?? '', 0, 1)) : 'IN' }}
                                                </div>
                                                <div>
                                                @php $util = isset($inscription->apprenant) ? $inscription->apprenant->utilisateur ?? null : null; @endphp
                                                <div class="fw-bold">{{ $util ? ($util->prenom . ' ' . $util->nom) : 'Inconnu' }}</div>
    <small class="text-muted">{{ $util->email ?? '' }}</small>
                                            </div>
                                            </div>
                                        </td>
                                        <td>
                                        <span class="badge-custom">{{ $inscription->module->nom ?? ($inscription->module->titre ?? 'Inconnu') }}</span>
                                        </td>
                                        <td>
                                            <small>{{ $inscription->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                    </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center empty-state">
                                        <i class="fas fa-inbox"></i><br>Aucune inscription récente
                                    </td>
                                </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>
        </div>

        <!-- Sessions à venir -->
        <div class="col-lg-6">
            <div class="content-card fade-in-section">
                <div class="card-header-custom">
                    <h6 class="card-title">
                        <i class="fas fa-calendar-alt me-2"></i>Prochaines Sessions
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($sessionsAVenir as $session)
                    <div class="session-item">
                        <div class="d-flex align-items-center">
                            <div class="session-date me-3">
                                <div class="fw-bold fs-4">{{ $session->date_debut->format('d') }}</div>
                                <small>{{ strtoupper($session->date_debut->format('M')) }}</small>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    @if($session->modules->count() > 0)
                                        @foreach($session->modules as $module)
                                            {{ $module->nom ?? $module->titre }}@if(!$loop->last), @endif
                                        @endforeach
                                    @else
                                        <span class="text-muted">Aucun module</span>
                                    @endif
                                </h6>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-user me-1"></i>
                                    @if($session->formateur)
                                        {{ $session->formateur->prenom }} {{ $session->formateur->nom }}
                                    @else
                                        <span class="text-muted">Aucun formateur</span>
                                    @endif
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $session->date_debut->format('H:i') }} - {{ $session->date_fin->format('H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i><br>Aucune session programmée
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Nouveaux apprenants -->
    <section class="fade-in-section">
        <div class="content-card">
            <div class="card-header-custom">
                <h6 class="card-title">
                        <i class="fas fa-user-graduate me-2"></i>Nouveaux Apprenants
                    </h6>
                </div>
                <div class="card-body">
                <div class="row g-3">
                    @forelse($apprenantsRecents as $apprenant)
                    <div class="col-md-6 col-lg-4">
                        <div class="student-card">
                                        <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                                    {{ substr($apprenant->prenom, 0, 1) }}{{ substr($apprenant->nom, 0, 1) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $apprenant->prenom }} {{ $apprenant->nom }}</h6>
                                                <p class="text-muted mb-1 small">{{ $apprenant->email }}</p>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>{{ $apprenant->created_at->format('d/m/Y') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    @empty
                    <div class="col-12 empty-state">
                        <i class="fas fa-user-slash"></i><br>Aucun nouvel apprenant
                            </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>

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
