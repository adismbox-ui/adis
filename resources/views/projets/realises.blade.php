@extends('layouts.app')

@section('content')
<!-- Particules animées -->
<div class="particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>
<div class="container-fluid main-container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 p-0">
            <div class="sidebar">
                <div class="p-4">
                    <h3 class="text-center mb-4" style="color: var(--accent-green);">
                        <i class="fas fa-project-diagram me-2"></i>Navigation
                    </h3>
                    @include('layouts.projets_sidebar')
                </div>
            </div>
        </div>
        <!-- Contenu principal -->
        <div class="col-md-9">
            <div class="content-area card-3d">
                <h1 class="main-title">
                    <i class="fas fa-check-circle me-3"></i>PROJETS REALISÉS
                </h1>
                <div class="description">
                    <p class="lead mb-0">
                        <i class="fas fa-star text-warning me-2"></i>
                        Grâce à la générosité de nos donateurs et à l'engagement précieux de nos partenaires, nous avons pu mener à bien ces projets qui ont transformé des vies et renforcé notre communauté. Nous remercions chaleureusement tous ceux qui ont contribué à ces réussites. Ensemble, continuons à bâtir un futur prometteur !
                        <i class="fas fa-heart text-danger ms-2"></i>
                    </p>
                </div>
                <div class="modern-table">
                    <div class="table-responsive">
                        <table class="table table-dark mb-0">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-clipboard-list me-2"></i>Intitulé du projet</th>
                                    <th><i class="fas fa-users me-2"></i>Bénéficiaires</th>
                                    <th><i class="fas fa-bullseye me-2"></i>Objectif atteint</th>
                                    <th><i class="fas fa-calendar-alt me-2"></i>Début</th>
                                    <th><i class="fas fa-calendar-check me-2"></i>Fin effective</th>
                                    <th><i class="fas fa-money-bill-wave me-2"></i>Montant</th>
                                    <th><i class="fas fa-user-tie me-2"></i>Responsable</th>
                                    <th><i class="fas fa-file-alt me-2"></i>Rapport</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($projets->count() > 0)
                                    @foreach($projets as $projet)
                                        <tr>
                                            <td><strong>{{ $projet->intitule }}</strong></td>
                                            <td>{{ $projet->beneficiaires }}</td>
                                            <td>{{ $projet->objectif }}</td>
                                            <td>{{ $projet->debut }}</td>
                                            <td>{{ $projet->fin_prevue }}</td>
                                            <td>{{ number_format($projet->montant_total, 0, ',', ' ') }} F CFA</td>
                                            <td>{{ $projet->responsable }}</td>
                                            <td>
                                                <a href="#" class="btn btn-primary btn-sm">Télécharger</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            Aucun projet réalisé pour le moment.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Styles et scripts personnalisés -->
@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-green: #0d4f3a;
            --secondary-green: #1a6b4f;
            --accent-green: #26d0ce;
            --light-green: #34d399;
            --dark-bg: #0a0a0a;
            --card-bg: rgba(13, 79, 58, 0.15);
            --glass-bg: rgba(255, 255, 255, 0.05);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: var(--dark-bg); color: #ffffff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; position: relative; }
        body::before { content: ''; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, rgba(10, 10, 10, 0.9) 0%, rgba(13, 79, 58, 0.3) 50%, rgba(10, 10, 10, 0.9) 100%), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse"><path d="M 50 0 L 0 0 0 50" fill="none" stroke="rgba(52, 211, 153, 0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>'); z-index: -2; animation: backgroundShift 20s ease-in-out infinite; }
        @keyframes backgroundShift { 0%, 100% { transform: scale(1) rotate(0deg); } 50% { transform: scale(1.05) rotate(1deg); } }
        .particles { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; pointer-events: none; }
        .particle { position: absolute; background: var(--accent-green); border-radius: 50%; opacity: 0.6; animation: float 6s ease-in-out infinite; }
        .particle:nth-child(1) { width: 4px; height: 4px; top: 20%; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 6px; height: 6px; top: 60%; left: 80%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 3px; height: 3px; top: 80%; left: 20%; animation-delay: 4s; }
        .particle:nth-child(4) { width: 5px; height: 5px; top: 40%; left: 60%; animation-delay: 1s; }
        .particle:nth-child(5) { width: 4px; height: 4px; top: 10%; left: 70%; animation-delay: 3s; }
        @keyframes float { 0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.6; } 50% { transform: translateY(-20px) rotate(180deg); opacity: 1; } }
        .main-container { position: relative; z-index: 1; min-height: 100vh; backdrop-filter: blur(10px); }
        .sidebar { background: linear-gradient(145deg, var(--card-bg), rgba(26, 107, 79, 0.2)); backdrop-filter: blur(15px); border-right: 1px solid rgba(52, 211, 153, 0.3); min-height: 100vh; box-shadow: 10px 0 30px rgba(0, 0, 0, 0.3); position: relative; }
        .sidebar::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(45deg, transparent 40%, rgba(52, 211, 153, 0.1) 50%, transparent 60%); animation: shimmer 3s ease-in-out infinite; }
        @keyframes shimmer { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
        .content-area { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); border-radius: 20px; margin: 20px; padding: 40px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4); border: 1px solid rgba(52, 211, 153, 0.2); position: relative; overflow: hidden; }
        .content-area::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: conic-gradient(from 0deg, transparent, rgba(52, 211, 153, 0.1), transparent); animation: rotate 10s linear infinite; z-index: -1; }
        @keyframes rotate { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .main-title { font-size: 3rem; font-weight: 800; background: linear-gradient(135deg, var(--light-green), var(--accent-green)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-align: center; margin-bottom: 30px; text-shadow: 0 0 30px rgba(52, 211, 153, 0.3); animation: titleGlow 2s ease-in-out infinite alternate; }
        @keyframes titleGlow { 0% { text-shadow: 0 0 30px rgba(52, 211, 153, 0.3); } 100% { text-shadow: 0 0 50px rgba(52, 211, 153, 0.6); } }
        .description { background: var(--glass-bg); backdrop-filter: blur(10px); border-radius: 15px; padding: 25px; margin-bottom: 40px; border: 1px solid rgba(52, 211, 153, 0.3); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); animation: slideInUp 1s ease-out; }
        @keyframes slideInUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modern-table { background: var(--glass-bg); backdrop-filter: blur(15px); border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3); border: 1px solid rgba(52, 211, 153, 0.3); animation: fadeIn 1.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .table-dark { background: linear-gradient(135deg, var(--primary-green), var(--secondary-green)); border: none; }
        .table-dark th { border: none; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; padding: 20px 15px; position: relative; }
        .table-dark th::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 2px; background: linear-gradient(90deg, transparent, var(--accent-green), transparent); }
        .table tbody tr { background: rgba(255, 255, 255, 0.03); border: none; transition: all 0.3s ease; position: relative; }
        .table tbody tr:hover { background: rgba(52, 211, 153, 0.1); transform: translateY(-2px); box-shadow: 0 10px 25px rgba(52, 211, 153, 0.2); }
        .table tbody tr::before { content: ''; position: absolute; left: 0; top: 0; width: 0; height: 100%; background: linear-gradient(90deg, var(--accent-green), transparent); transition: width 0.3s ease; }
        .table tbody tr:hover::before { width: 4px; }
        .table td { border: none; padding: 20px 15px; vertical-align: middle; border-bottom: 1px solid rgba(52, 211, 153, 0.1); }
        .progress { height: 12px; background: rgba(0, 0, 0, 0.3); border-radius: 10px; overflow: hidden; box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3); }
        .progress-bar { background: linear-gradient(135deg, var(--secondary-green), var(--accent-green)); border-radius: 10px; position: relative; overflow: hidden; animation: progressShine 2s ease-in-out infinite; }
        .progress-bar::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent); animation: progressMove 2s ease-in-out infinite; }
        @keyframes progressMove { 0% { left: -100%; } 100% { left: 100%; } }
        @keyframes progressShine { 0%, 100% { box-shadow: 0 0 10px rgba(52, 211, 153, 0.3); } 50% { box-shadow: 0 0 20px rgba(52, 211, 153, 0.6); } }
        .badge { padding: 8px 16px; border-radius: 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; position: relative; overflow: hidden; transition: all 0.3s ease; }
        .badge::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent); transition: left 0.5s ease; }
        .badge:hover::before { left: 100%; }
        .bg-primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4); }
        .bg-success { background: linear-gradient(135deg, var(--light-green), var(--secondary-green)) !important; box-shadow: 0 4px 15px rgba(52, 211, 153, 0.4); }
        .bg-warning { background: linear-gradient(135deg, #f59e0b, #d97706) !important; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4); }
        .card-3d { transform-style: preserve-3d; transition: transform 0.6s ease; }
        .card-3d:hover { transform: rotateY(10deg) rotateX(5deg) scale(1.02); }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.3); }
        ::-webkit-scrollbar-thumb { background: linear-gradient(135deg, var(--secondary-green), var(--accent-green)); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(135deg, var(--accent-green), var(--light-green)); }
        @media (max-width: 768px) { .main-title { font-size: 2rem; } .content-area { margin: 10px; padding: 20px; } .table-responsive { border-radius: 15px; } }
        .table tbody tr { animation: slideInFromRight 0.8s ease-out forwards; opacity: 0; }
        .table tbody tr:nth-child(1) { animation-delay: 0.1s; }
        .table tbody tr:nth-child(2) { animation-delay: 0.2s; }
        .table tbody tr:nth-child(3) { animation-delay: 0.3s; }
        .table tbody tr:nth-child(4) { animation-delay: 0.4s; }
        .table tbody tr:nth-child(5) { animation-delay: 0.5s; }
        @keyframes slideInFromRight { from { transform: translateX(50px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
@endpush
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animation au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, observerOptions);
        document.querySelectorAll('.table tbody tr').forEach(el => {
            observer.observe(el);
        });
        // Animation des particules au survol
        document.addEventListener('mousemove', (e) => {
            const particles = document.querySelectorAll('.particle');
            particles.forEach((particle, index) => {
                const speed = (index + 1) * 0.0001;
                const x = e.clientX * speed;
                const y = e.clientY * speed;
                setTimeout(() => {
                    particle.style.transform = `translate(${x}px, ${y}px)`;
                }, index * 50);
            });
        });
        // Effet de pulsation sur les badges
        setInterval(() => {
            const badges = document.querySelectorAll('.badge');
            badges.forEach(badge => {
                badge.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    badge.style.transform = 'scale(1)';
                }, 200);
            });
        }, 3000);
    </script>
@endpush
@endsection