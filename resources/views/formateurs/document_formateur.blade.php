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

    /* Cartes de module */
    .module-card {
        background: rgba(45, 110, 78, 0.8);
        border: 1px solid rgba(77, 166, 116, 0.4);
        border-radius: 15px;
        margin-bottom: 2rem;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        animation: slideIn 0.6s ease-out;
        position: relative;
        backdrop-filter: blur(10px);
    }

    .module-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(77, 166, 116, 0.05), transparent);
        animation: shine 4s ease-in-out infinite;
    }

    .module-card:hover {
        transform: translateY(-5px) scale(1.02);
        border-color: var(--light-green);
        box-shadow: 0 15px 40px rgba(15, 42, 31, 0.4);
    }

    .module-header {
        background: linear-gradient(135deg, var(--accent-green) 0%, var(--light-green) 100%);
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .module-title {
        color: var(--text-light);
        font-size: 1.4rem;
        font-weight: 600;
        margin: 0;
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .module-badge {
        background: rgba(255, 255, 255, 0.2);
        color: var(--text-light);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Cartes de document */
    .document-card {
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

    .document-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.5s ease;
    }

    .document-card:hover::before {
        left: 100%;
    }

    .document-card:hover {
        transform: translateY(-3px);
        border-color: var(--light-green);
        box-shadow: 0 10px 30px rgba(15, 42, 31, 0.4);
    }

    .document-title {
        color: var(--text-light);
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .document-info {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 1rem;
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

    .btn-primary-modern {
        background: linear-gradient(135deg, var(--light-green), var(--accent-green));
        color: white;
        box-shadow: 0 4px 15px var(--glow-green);
    }

    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px var(--glow-green);
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

    /* Audio player moderne */
    .audio-player-modern {
        background: rgba(15, 42, 31, 0.6);
        border-radius: 10px;
        padding: 1rem;
        margin: 1rem 0;
        border: 1px solid rgba(77, 166, 116, 0.3);
        backdrop-filter: blur(10px);
    }

    .audio-player-modern audio {
        width: 100%;
        border-radius: 8px;
        background: rgba(26, 77, 58, 0.8);
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

    /* Section headers */
    .section-header {
        color: var(--text-light);
        font-size: 1.3rem;
        font-weight: 600;
        margin: 2rem 0 1rem 0;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .section-icon {
        width: 35px;
        height: 35px;
        background: linear-gradient(135deg, var(--accent-green), var(--light-green));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulse 2s infinite;
        box-shadow: 0 4px 15px var(--glow-green);
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
        
        .module-card {
            margin: 1rem 0;
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
                            <i class="fas fa-file-alt text-white"></i>
                        </div>
                        Mes documents de module
                    </h1>
                </div>
                
                <div class="card-body p-4">
    @if($modules->isEmpty())
                        <div class="alert-modern alert-info-modern animate-on-scroll">
                            <i class="fas fa-info-circle me-2"></i>
                            Aucun module trouvé.
                        </div>
    @else
        @foreach($modules as $module)
                            <div class="module-card animate-on-scroll">
                                <div class="module-header">
                                    <div class="module-title">
                                        <div>
                                            <i class="fas fa-book-open me-2"></i>
                                            {{ $module->titre ?? $module->nom ?? 'Module' }}
                                        </div>
                                        <span class="module-badge">
                                            <i class="fas fa-file-alt me-1"></i>
                                            {{ $module->documents->count() }} documents
                                        </span>
                                    </div>
                </div>
                                
                                <div class="p-4">
                    @if($module->documents->isEmpty())
                                        <div class="alert-modern alert-warning-modern">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Aucun document pour ce module.
                                        </div>
                    @else
                        <!-- Section Audios du module -->
                        @php
                            $moduleAudios = $module->documents->whereNotNull('audio');
                        @endphp
                        @if($moduleAudios->isNotEmpty())
                            <div class="mb-4">
                                                <div class="section-header">
                                                    <div class="section-icon">
                                                        <i class="fas fa-headphones text-white"></i>
                                                    </div>
                                                    Audios de ce module
                                                </div>
                                                <div class="row g-3">
                                                    @foreach($moduleAudios as $audioDoc)
                                                        <div class="col-md-6 col-lg-4">
                                                            <div class="document-card">
                                                                <div class="document-title">
                                                                    <i class="fas fa-music text-success me-2"></i>
                                                                    {{ $audioDoc->titre }}
                                                                </div>
                                                                
                                                <div class="audio-player-modern">
                                                    <audio controls>
                                                        <source src="{{ asset('storage/' . $audioDoc->audio) }}" type="audio/mpeg">
                                                        <source src="{{ asset('storage/' . $audioDoc->audio) }}" type="audio/mp3">
                                                        <source src="{{ asset('storage/' . $audioDoc->audio) }}" type="audio/wav">
                                                        Votre navigateur ne supporte pas la lecture audio.
                                                    </audio>
                                                </div>
                                                
                                                    @if(!empty($audioDoc->fichier))
                                                    <a href="{{ asset('storage/' . $audioDoc->fichier) }}" target="_blank" class="btn btn-outline-modern btn-modern w-100 mb-2">
                                                            <i class="fas fa-file-pdf me-1"></i> Voir / Télécharger le PDF
                                                        </a>
                                                    @endif
                                                
                                                <div class="document-info">
                                                    <small>
                                                        <i class="fas fa-calendar me-1"></i>
                                                            Semaine: {{ $audioDoc->semaine ?? 'N/A' }} | 
                                                        <i class="fas fa-tag me-1"></i>
                                                            Type: {{ $audioDoc->type ?? 'Audio' }}
                                                        </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- Section Documents du module -->
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                            Documents de ce module
                        </div>
                        <div class="row g-3">
                            @foreach($module->documents->whereNull('audio') as $document)
                                <div class="col-md-4">
                                    <div class="document-card">
                                        <div class="document-title">
                                            <i class="fas fa-file me-2"></i>
                                            {{ $document->titre }}
                                        </div>
                                        
                                        <div class="document-info">
                                            <div class="mb-2">
                                                <i class="fas fa-tag me-1"></i>
                                                Type : <strong>{{ $document->type }}</strong>
                                            </div>
                                            <div>
                                                <i class="fas fa-calendar me-1"></i>
                                                Semaine : <strong>{{ $document->semaine ?? '-' }}</strong>
                                            </div>
                                        </div>
                                            
                                            @php
                                                $extension = pathinfo($document->fichier, PATHINFO_EXTENSION);
                                                $isPdf = in_array(strtolower($extension), ['pdf']);
                                                $isWord = in_array(strtolower($extension), ['doc', 'docx']);
                                            @endphp
                                        
                                            @if($isPdf)
                                            <button type="button" class="btn btn-primary-modern btn-modern w-100 mb-2" data-bs-toggle="modal" data-bs-target="#pdfModal_{{ $document->id }}">
                                                    <i class="fas fa-eye me-1"></i> Voir le PDF
                                                </button>
                                                <!-- Modal PDF -->
                                                <div class="modal fade" id="pdfModal_{{ $document->id }}" tabindex="-1" aria-labelledby="pdfModalLabel_{{ $document->id }}" aria-hidden="true">
                                                  <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                      <div class="modal-header">
                                                        <h5 class="modal-title" id="pdfModalLabel_{{ $document->id }}">{{ $document->titre }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                      </div>
                                                      <div class="modal-body" style="height:80vh;">
                                                        <iframe src="{{ asset('storage/' . $document->fichier) }}#toolbar=0" width="100%" height="100%" style="border:none;"></iframe>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                            @elseif($isWord)
                                            <a href="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode(asset('storage/' . $document->fichier)) }}" target="_blank" class="btn btn-success-modern btn-modern w-100 mb-2">
                                                    <i class="fas fa-eye me-1"></i> Voir le Word
                                                </a>
                                            @else
                                            <a href="{{ asset('storage/' . $document->fichier) }}" target="_blank" class="btn btn-outline-modern btn-modern w-100 mb-2">
                                                    <i class="fas fa-download me-1"></i> Télécharger / Ouvrir
                                                </a>
                                            @endif
                                        
                                        <div class="text-end">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                Ajouté le {{ $document->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
</div>
</div>

<!-- Scripts pour les animations -->
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
});
</script>

<!-- FontAwesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

@endsection
