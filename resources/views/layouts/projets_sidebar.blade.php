<div class="sidebar sidebar-apprenant border-end">
    <div class="p-3">
        <h4 class="text-primary mb-4">
            <i class="fas fa-project-diagram me-2"></i>
            PROJETS
        </h4>
        
        <div class="mb-4">
            <p class="text-muted small">
                Nos Projets, Votre Impact
            </p>
            <p class="text-muted small">
                Chez ADIS, nous croyons en la force de la solidarité et de l'action collective. 
                Le menu PROJETS vous donne un aperçu des initiatives communautaires en cours, 
                des projets réalisés grâce à votre soutien, et de ceux qui attendent encore vos contributions.
            </p>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('projets.index') ? 'active bg-primary text-white' : 'text-dark' }}" 
               href="{{ route('projets.index') }}">
                <i class="fas fa-play-circle me-2"></i>
                Projets en cours
            </a>
            
            <a class="nav-link {{ request()->routeIs('projets.realises') ? 'active bg-success text-white' : 'text-dark' }}" 
               href="{{ route('projets.realises') }}">
                <i class="fas fa-check-circle me-2"></i>
                Projets réalisés
            </a>
            
            <a class="nav-link {{ request()->routeIs('projets.financer') ? 'active bg-warning text-dark' : 'text-dark' }}" 
               href="{{ route('projets.financer') }}">
                <i class="fas fa-hand-holding-usd me-2"></i>
                Projets à financer
            </a>
            
            <a class="nav-link {{ request()->routeIs('projets.don') ? 'active bg-info text-white' : 'text-dark' }}" 
               href="{{ route('projets.don') }}">
                <i class="fas fa-heart me-2"></i>
                Faire un don
            </a>
            
            <hr class="my-3">
            
            <a class="nav-link {{ request()->routeIs('appel-a-projets.*') ? 'active bg-secondary text-white' : 'text-dark' }}" 
               href="{{ route('appel-a-projets.index') }}">
                <i class="fas fa-bullhorn me-2"></i>
                Appels à projets
            </a>
            
            <a class="nav-link {{ request()->routeIs('partenaires.index') ? 'active bg-dark text-white' : 'text-dark' }}" 
               href="{{ route('partenaires.index') }}">
                <i class="fas fa-handshake me-2"></i>
                Entreprises partenaires
            </a>
            
            <a class="nav-link {{ request()->routeIs('rapports.index') ? 'active bg-info text-white' : 'text-dark' }}" 
               href="{{ route('rapports.index') }}">
                <i class="fas fa-chart-bar me-2"></i>
                Rapports et bilans
            </a>
            
            <a class="nav-link {{ request()->routeIs('galeries.index') ? 'active bg-primary text-white' : 'text-dark' }}" 
               href="{{ route('galeries.index') }}">
                <i class="fas fa-images me-2"></i>
                Galerie photos & vidéos
            </a>
            
            <a class="nav-link {{ request()->routeIs('partenaires.create') ? 'active bg-success text-white' : 'text-dark' }}" 
               href="{{ route('partenaires.create') }}">
                <i class="fas fa-building me-2"></i>
                Enregistrer mon entreprise
            </a>
        </nav>
    </div>
</div>

<style>
/* Sidebar Apprenant sombre et animé */
.sidebar-apprenant {
    min-height: 100vh;
    position: relative;
    color: #ffffff;
    background: #0b0f14;
    overflow: hidden;
}

.sidebar-apprenant::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: url('{{ asset('ad.jpg') }}');
    background-size: cover;
    background-position: center;
    filter: brightness(0.6) saturate(1.1);
    transform: scale(1.1);
}

.sidebar-apprenant::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.55) 0%, rgba(0, 0, 0, 0.8) 100%);
    backdrop-filter: blur(2px);
}

.sidebar-apprenant .p-3 {
    position: relative;
    z-index: 2;
}

.sidebar-apprenant h4 {
    color: #e8fff4;
    letter-spacing: 1px;
    text-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
}

.sidebar-apprenant .text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
}

@keyframes slideInLeftSidebar {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}

.sidebar-apprenant .nav .nav-link {
    color: #e2f7ef !important;
    border-radius: 12px;
    margin-bottom: 8px;
    padding: 0.6rem 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    transition: transform 0.25s ease, background 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    position: relative;
    overflow: hidden;
    animation: slideInLeftSidebar 0.45s ease both;
}

.sidebar-apprenant .nav .nav-link::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #34d399, #059669);
    opacity: 0;
    transition: opacity 0.25s ease, width 0.25s ease;
}

.sidebar-apprenant .nav .nav-link:hover {
    transform: translateX(6px);
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(52, 211, 153, 0.35);
    box-shadow: 0 8px 24px rgba(52, 211, 153, 0.2);
}

.sidebar-apprenant .nav .nav-link:hover::before {
    opacity: 1;
}

.sidebar-apprenant .nav .nav-link i {
    width: 22px;
    text-align: center;
    color: #34d399;
    transition: transform 0.25s ease, color 0.25s ease;
}

.sidebar-apprenant .nav .nav-link:hover i {
    transform: translateX(2px) scale(1.05);
    color: #a7f3d0;
}

.sidebar-apprenant .nav .nav-link.active {
    color: #ffffff !important;
    background: linear-gradient(135deg, rgba(52, 211, 153, 0.25), rgba(5, 150, 105, 0.25)) !important;
    border-color: rgba(52, 211, 153, 0.55);
    box-shadow: 0 10px 30px rgba(16, 185, 129, 0.25), inset 0 0 20px rgba(16, 185, 129, 0.15);
}

.sidebar-apprenant .nav .nav-link.active::before {
    opacity: 1;
    width: 4px;
}

.sidebar-apprenant hr {
    border-color: rgba(255, 255, 255, 0.15);
}
</style> 