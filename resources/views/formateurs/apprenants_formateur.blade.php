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
        --gold: #ffd700;
        --silver: #c0c0c0;
        --bronze: #cd7f32;
    }

    /* Styles de base avec image de fond */
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

    /* Animations globales */
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

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes shine {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    @keyframes bounce {
        0%, 20%, 53%, 80%, 100% { transform: translateY(0); }
        40%, 43% { transform: translateY(-30px); }
        70% { transform: translateY(-15px); }
        90% { transform: translateY(-4px); }
    }

    /* Carte principale */
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

    .main-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 80px var(--shadow-dark);
    }

    /* En-tête de page */
    .page-header {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        transform: rotate(45deg);
        animation: shine 3s ease-in-out infinite;
    }

    .page-title {
        color: var(--text-light);
        font-size: 2.5rem;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 2;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .page-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--accent-green), var(--light-green));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulse 2s infinite;
        box-shadow: 0 4px 15px var(--glow-green);
    }

    /* Contrôles de tri */
    .controls-section {
        background: rgba(26, 77, 58, 0.8);
        border: 1px solid rgba(77, 166, 116, 0.3);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        backdrop-filter: blur(10px);
    }

    .control-group {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .control-label {
        color: var(--text-light);
        font-weight: 600;
        margin: 0;
    }

    .control-select {
        background: rgba(15, 42, 31, 0.8);
        border: 1px solid rgba(77, 166, 116, 0.5);
        color: var(--text-light);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        backdrop-filter: blur(10px);
    }

    .control-select:focus {
        outline: none;
        border-color: var(--light-green);
        box-shadow: 0 0 0 2px rgba(77, 166, 116, 0.3);
    }

    /* Cartes d'apprenant */
    .apprenant-card {
        background: rgba(26, 77, 58, 0.8);
        border: 1px solid rgba(77, 166, 116, 0.3);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .apprenant-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.5s ease;
    }

    .apprenant-card:hover::before {
        left: 100%;
    }

    .apprenant-card:hover {
        transform: translateY(-3px);
        border-color: var(--light-green);
        box-shadow: 0 10px 30px rgba(15, 42, 31, 0.4);
    }

    .apprenant-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .apprenant-title {
        color: var(--text-light);
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .apprenant-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--accent-green), var(--light-green));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 15px var(--glow-green);
    }

    .ranking-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.9rem;
        animation: bounce 2s infinite;
    }

    .ranking-1 { background: var(--gold); color: #000; }
    .ranking-2 { background: var(--silver); color: #000; }
    .ranking-3 { background: var(--bronze); color: #000; }
    .ranking-other { background: rgba(77, 166, 116, 0.8); color: white; }

    .apprenant-info {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .progress-section {
        margin: 1rem 0;
    }

    .progress-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .progress-label {
        color: var(--text-light);
        font-size: 0.85rem;
        font-weight: 500;
    }

    .progress-bar-container {
        width: 60%;
        height: 8px;
        background: rgba(15, 42, 31, 0.5);
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .progress-bar-niveau { background: linear-gradient(90deg, var(--accent-green), var(--light-green)); }
    .progress-bar-points { background: linear-gradient(90deg, #ffd700, #ffed4e); }

    .progress-value {
        color: var(--text-light);
        font-size: 0.8rem;
        font-weight: 600;
        min-width: 40px;
        text-align: right;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
        margin: 1rem 0;
    }

    .stat-item {
        text-align: center;
        padding: 0.5rem;
        background: rgba(15, 42, 31, 0.5);
        border-radius: 8px;
        border: 1px solid rgba(77, 166, 116, 0.3);
    }

    .stat-value {
        color: var(--light-green);
        font-size: 1.2rem;
        font-weight: 700;
        display: block;
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Boutons modernes */
    .btn-modern {
        padding: 0.6rem 1.2rem;
        border-radius: 10px;
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

    /* Alertes modernes */
    .alert-modern {
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 10px;
        color: #22c55e;
        padding: 1.5rem;
        margin: 1rem 0;
        animation: fadeInUp 0.6s ease-out;
        backdrop-filter: blur(10px);
    }

    .alert-warning-modern {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #fbbf24;
    }

    .alert-info-modern {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.3);
        color: #3b82f6;
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

    /* Responsive design */
    @media (max-width: 768px) {
        .page-title {
            font-size: 2rem;
        }
        
        .main-card {
            margin: 1rem;
        }
        
        .control-group {
            flex-direction: column;
            align-items: stretch;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="main-card shadow-lg mb-4">
                <!-- En-tête de page -->
                <div class="page-header text-center">
                    <h1 class="page-title">
                        <div class="page-icon">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        Mes apprenants (niveaux)
                    </h1>
                </div>
                
                <div class="card-body p-4">
                    <!-- Section des contrôles de tri -->
                    <div class="controls-section animate-on-scroll">
                        <div class="control-group">
                            <div>
                                <label class="control-label">Trier par :</label>
                                <select id="sortBy" class="control-select">
                                    <option value="niveau">Niveau</option>
                                    <option value="points">Points</option>
                                    <option value="nom">Nom</option>
                                    <option value="progression">Progression</option>
                                </select>
                            </div>
                            <div>
                                <label class="control-label">Ordre :</label>
                                <select id="sortOrder" class="control-select">
                                    <option value="desc">Décroissant</option>
                                    <option value="asc">Croissant</option>
                                </select>
                            </div>
                            <div>
                                <label class="control-label">Filtrer par niveau :</label>
                                <select id="filterNiveau" class="control-select">
                                    <option value="">Tous les niveaux</option>
                                    @foreach(($niveaux ?? []) as $niveau)
                                        <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des apprenants -->
                    @if($apprenants->isEmpty())
                        <div class="alert-modern alert-info-modern animate-on-scroll">
                            <i class="fas fa-info-circle me-2"></i>
                            Aucun apprenant trouvé.
                        </div>
                    @else
                        <div class="row g-4" id="apprenantsContainer">
                            @foreach($apprenants as $index => $apprenant)
                                @php
                                    $points = $apprenant->points ?? 0;
                                    $niveauOrdre = $apprenant->niveau->ordre ?? 1;
                                    $progression = ($points / 1000) * 100;
                                    $niveauProgression = $apprenant->niveau_progression ?? (($niveauOrdre / 4) * 100);
                                @endphp
                                <div class="col-md-6 col-lg-4 apprenant-item" 
                                     data-niveau-id="{{ $apprenant->niveau->id ?? '' }}"
                                     data-niveau="{{ $apprenant->niveau->nom ?? 'Niveau inconnu' }}"
                                     data-points="{{ $points }}"
                                     data-nom="{{ strtolower($apprenant->utilisateur->nom ?? '') }}"
                                     data-progression="{{ $progression }}">
                                    <div class="apprenant-card animate-on-scroll">
                                        <!-- Badge de classement -->
                                        <div class="ranking-badge ranking-{{ $index < 3 ? $index + 1 : 'other' }}">
                                            {{ $index + 1 }}
                                        </div>
                                        
                                        <div class="apprenant-header">
                                            <div class="apprenant-title">
                                                <div class="apprenant-avatar">
                                                    {{ strtoupper(substr($apprenant->utilisateur->prenom ?? 'A', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $apprenant->utilisateur->nom ?? '-' }} {{ $apprenant->utilisateur->prenom ?? '' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="apprenant-info">
                                            <div class="mb-2">
                                                <i class="fas fa-envelope me-1"></i>
                                                <strong>Email :</strong> {{ $apprenant->utilisateur->email ?? '-' }}
                                            </div>
                                            <div class="mb-2">
                                                <i class="fas fa-phone me-1"></i>
                                                <strong>Téléphone :</strong> {{ $apprenant->utilisateur->telephone ?? '-' }}
                                            </div>
                                            <div class="mb-2">
                                                <i class="fas fa-layer-group me-1"></i>
                                                <strong>Niveau :</strong> 
                                                <span class="badge bg-info">{{ $apprenant->niveau->nom ?? 'Non défini' }}</span>
                                            </div>
                                        </div>

                                        <!-- Section de progression -->
                                        <div class="progress-section">
                                            <div class="progress-item">
                                                <span class="progress-label">Progression niveau</span>
                                                <div class="progress-bar-container">
                                                    <div class="progress-bar progress-bar-niveau" style="width: {{ $niveauProgression }}%"></div>
                                                </div>
                                                <span class="progress-value">{{ number_format($niveauProgression, 0) }}%</span>
                                            </div>
                                            <div class="progress-item">
                                                <span class="progress-label">Points acquis</span>
                                                <div class="progress-bar-container">
                                                    <div class="progress-bar progress-bar-points" style="width: {{ $progression }}%"></div>
                                                </div>
                                                <span class="progress-value">{{ $points }}</span>
                                            </div>
                                        </div>

                                        <!-- Grille de statistiques -->
                                        <div class="stats-grid">
                                            <div class="stat-item">
                                                <span class="stat-value">{{ $points }}</span>
                                                <span class="stat-label">Points</span>
                                            </div>
                                            <div class="stat-item">
                                                <span class="stat-value">{{ $apprenant->modules_completes ?? rand(1, 5) }}</span>
                                                <span class="stat-label">Modules</span>
                                            </div>
                                            <div class="stat-item">
                                                <span class="stat-value">{{ $apprenant->quiz_reussis ?? rand(3, 12) }}</span>
                                                <span class="stat-label">Quiz</span>
                                            </div>
                                        </div>
                                        
                                        <a href="{{ route('formateurs.apprenants.show', $apprenant->id) }}" class="btn btn-outline-modern btn-modern w-100">
                                            <i class="fas fa-eye me-1"></i> Voir détails
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts pour les animations et le tri -->
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

    // Système de tri et filtrage
    const sortBy = document.getElementById('sortBy');
    const sortOrder = document.getElementById('sortOrder');
    const filterNiveau = document.getElementById('filterNiveau');
    const apprenantsContainer = document.getElementById('apprenantsContainer');

    function sortAndFilterApprenants() {
        const apprenantItems = Array.from(document.querySelectorAll('.apprenant-item'));
        const selectedNiveauId = filterNiveau.value;
        const sortField = sortBy.value;
        const sortDirection = sortOrder.value;

        // Filtrer par niveau
        let filteredItems = apprenantItems;
        if (selectedNiveauId) {
            filteredItems = apprenantItems.filter(item => (item.dataset.niveauId || '') === selectedNiveauId);
        }

        // Trier les éléments
        filteredItems.sort((a, b) => {
            let aValue, bValue;

            switch (sortField) {
                case 'niveau':
                    aValue = a.dataset.niveau;
                    bValue = b.dataset.niveau;
                    break;
                case 'points':
                    aValue = parseInt(a.dataset.points);
                    bValue = parseInt(b.dataset.points);
                    break;
                case 'nom':
                    aValue = a.dataset.nom;
                    bValue = b.dataset.nom;
                    break;
                case 'progression':
                    aValue = parseFloat(a.dataset.progression);
                    bValue = parseFloat(b.dataset.progression);
                    break;
                default:
                    return 0;
            }

            if (sortDirection === 'asc') {
                return aValue > bValue ? 1 : -1;
            } else {
                return aValue < bValue ? 1 : -1;
            }
        });

        // Réorganiser les éléments dans le DOM
        filteredItems.forEach((item, index) => {
            // Mettre à jour le badge de classement
            const rankingBadge = item.querySelector('.ranking-badge');
            if (rankingBadge) {
                rankingBadge.className = `ranking-badge ranking-${index < 3 ? index + 1 : 'other'}`;
                rankingBadge.textContent = index + 1;
            }
            
            // Réorganiser dans le conteneur
            apprenantsContainer.appendChild(item);
        });

        // Animation de réorganisation
        filteredItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.1}s`;
            item.classList.remove('visible');
            item.classList.add('animate-on-scroll');
            
            // Réobserver l'élément
            observer.observe(item);
        });
    }

    // Événements de changement
    sortBy.addEventListener('change', sortAndFilterApprenants);
    sortOrder.addEventListener('change', sortAndFilterApprenants);
    filterNiveau.addEventListener('change', sortAndFilterApprenants);

    // Tri initial par points décroissant
    sortBy.value = 'points';
    sortOrder.value = 'desc';
    sortAndFilterApprenants();
});
</script>

<!-- FontAwesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

@endsection
