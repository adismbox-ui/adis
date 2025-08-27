@extends('apprenants.layout')

@section('content')
<style>
    /* Image de fond avec overlay vert herbe */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') center center/cover no-repeat;
        z-index: -2;
        opacity: 0.4;
    }
    
    body::after {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(67, 234, 74, 0.4) 0%, rgba(46, 204, 64, 0.5) 100%);
        z-index: -1;
    }

    /* Animations et styles modernes */
    .page-container {
        min-height: 100vh;
        padding: 2rem 0;
        position: relative;
        overflow: hidden;
    }

    /* Particules flottantes */
    .floating-particles {
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
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.3; }
        50% { transform: translateY(-20px) rotate(180deg); opacity: 0.8; }
    }

    /* Card principale avec effet glassmorphism */
    .main-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        animation: slideInUp 0.8s ease-out;
        position: relative;
        overflow: hidden;
    }

    .main-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #43ea4a, #2ecc40, #27ae60);
        animation: shimmer 2s ease-in-out infinite;
    }

    @keyframes shimmer {
        0%, 100% { transform: translateX(-100%); }
        50% { transform: translateX(100%); }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Header avec dégradé vert herbe */
    .card-header {
        background: linear-gradient(135deg, #43ea4a 0%, #2ecc40 50%, #27ae60 100%) !important;
        border: none;
        border-radius: 20px 20px 0 0 !important;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .card-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 10s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .card-header h2 {
        position: relative;
        z-index: 2;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    /* Formulaires avec animations */
    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 16px;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
    }

    .form-control:focus, .form-select:focus {
        border-color: #43ea4a;
        box-shadow: 0 0 0 0.2rem rgba(67, 234, 74, 0.25);
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 1);
    }

    .form-label {
        color: #2ecc40 !important;
        font-weight: 600;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }

    .form-control:focus + .form-label,
    .form-select:focus + .form-label {
        color: #27ae60 !important;
        transform: translateY(-2px);
    }

    /* Boutons avec animations */
    .btn-primary {
        background: linear-gradient(135deg, #43ea4a 0%, #2ecc40 100%);
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 10px 25px rgba(67, 234, 74, 0.4);
    }

    .btn-outline-secondary {
        border: 2px solid #43ea4a;
        color: #43ea4a;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-secondary:hover {
        background: #43ea4a;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(67, 234, 74, 0.3);
    }

    /* Alert avec animation */
    .alert-primary {
        background: linear-gradient(135deg, rgba(67, 234, 74, 0.1) 0%, rgba(46, 204, 64, 0.1) 100%);
        border: 1px solid rgba(67, 234, 74, 0.3);
        border-radius: 15px;
        animation: fadeInSlide 0.8s ease-out;
    }

    @keyframes fadeInSlide {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Icônes animées */
    .icon-illu {
        filter: drop-shadow(0 4px 12px rgba(67, 234, 74, 0.4));
        animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-container {
            padding: 1rem 0;
        }
        
        .main-card {
            margin: 0 1rem;
        }
    }

    /* Effet de focus amélioré */
    .form-group {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .form-group:focus-within .form-label {
        color: #27ae60 !important;
        transform: translateY(-5px) scale(1.02);
    }

    /* Animation d'entrée pour les éléments */
    .animate-on-scroll {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }

    .animate-on-scroll.visible {
        opacity: 1;
        transform: translateY(0);
    }
</style>

<div class="page-container">
    <!-- Particules flottantes -->
    <div class="floating-particles" id="particles"></div>
    
    <div class="container">
    <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <!-- Alert d'introduction -->
                <div class="alert alert-primary d-flex align-items-center shadow-lg mb-4 animate-on-scroll" role="alert">
                    <i class="fas fa-home fa-2x me-3" style="color: #43ea4a;"></i>
                <div>
                        <strong style="color: #1a5f1a; font-size: 1.1rem;">Demandez un cours à domicile !</strong><br>
                        <span style="color: #2d5a2d; font-weight: 500;">Remplissez le formulaire ci-dessous pour faire votre demande personnalisée.</span>
                    </div>
                </div>

                <!-- Card principale -->
                <div class="card main-card shadow-lg border-0 animate-on-scroll">
                    <div class="card-header text-white text-center">
                        <h2 class="mb-0">
                            <i class="fas fa-home me-2"></i>Cours Maison
                        </h2>
            </div>
                    
                    <div class="card-body p-4">
                        <p class="lead text-center mb-4 animate-on-scroll">
                            Bienvenue sur la page <strong style="color: #2ecc40;">Cours Maison</strong> !<br>
                        Ici, vous pouvez demander un cours à domicile à l'administrateur.
                    </p>
                        
                    @if(session('success'))
                            <div class="alert alert-success text-center animate__animated animate__fadeIn">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            </div>
                    @endif
                        
                    <form action="{{ route('demande.cours.maison') }}" method="POST" class="mb-4">
                        @csrf
                            
                            <div class="form-group animate-on-scroll">
                                <label for="module" class="form-label">
                                    <i class="fas fa-layer-group me-2"></i>Niveau à enseigner
                                </label>
                            <select name="niveau_id" id="niveau_id" class="form-select" required>
                                <option value="">-- Sélectionner un niveau --</option>
                                @foreach($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                            
                            <div class="form-group animate-on-scroll">
                                <label for="nombre_enfants" class="form-label">
                                    <i class="fas fa-users me-2"></i>Nombre d'enfants
                                </label>
                            <input type="number" name="nombre_enfants" id="nombre_enfants" class="form-control" required min="1" max="20" placeholder="Ex: 2">
                        </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group animate-on-scroll">
                                        <label for="ville" class="form-label">
                                            <i class="fas fa-city me-2"></i>Ville
                                        </label>
                            <input type="text" name="ville" id="ville" class="form-control" required maxlength="100" placeholder="Votre ville">
                        </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group animate-on-scroll">
                                        <label for="commune" class="form-label">
                                            <i class="fas fa-map-marker-alt me-2"></i>Commune
                                        </label>
                            <input type="text" name="commune" id="commune" class="form-control" required maxlength="100" placeholder="Votre commune">
                        </div>
                                </div>
                            </div>
                            
                            <div class="form-group animate-on-scroll">
                                <label for="quartier" class="form-label">
                                    <i class="fas fa-home me-2"></i>Quartier
                                </label>
                            <input type="text" name="quartier" id="quartier" class="form-control" required maxlength="100" placeholder="Votre quartier">
                        </div>
                            
                            <div class="form-group animate-on-scroll">
                                <label for="numero" class="form-label">
                                    <i class="fas fa-phone me-2"></i>Numéro de téléphone
                                </label>
                            <input type="tel" name="numero" id="numero" class="form-control" required maxlength="20" placeholder="Ex: 0700000000">
                        </div>
                            
                            <div class="form-group animate-on-scroll">
                                <label for="message" class="form-label">
                                    <i class="fas fa-comment me-2"></i>Message
                                </label>
                            <textarea name="message" id="message" class="form-control" rows="4" required minlength="10" maxlength="2000" placeholder="Décrivez votre besoin de cours à domicile..."></textarea>
                            </div>
                            
                            <div class="d-grid gap-3 animate-on-scroll">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer la demande
                                </button>
                                <a href="{{ route('demandes.cours.maison.index') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-list me-2"></i>Voir mes demandes
                                </a>
                            </div>
                        </form>
                        
                        <div class="text-center mt-5 animate-on-scroll">
                            <i class="fas fa-book-open fa-3x mb-3 icon-illu" style="color: #43ea4a;"></i>
                            <p class="text-muted">
                                Aucun contenu n'est disponible pour le moment.<br>
                                Revenez plus tard ou contactez votre formateur.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Création des particules flottantes
    const particlesContainer = document.getElementById('particles');
    for (let i = 0; i < 15; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        particle.style.width = particle.style.height = (Math.random() * 10 + 5) + 'px';
        particle.style.animationDelay = Math.random() * 6 + 's';
        particle.style.animationDuration = (Math.random() * 3 + 3) + 's';
        particlesContainer.appendChild(particle);
    }

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

    // Animation des boutons au hover
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.02)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Animation des champs de formulaire
    document.querySelectorAll('.form-control, .form-select').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});
</script>
@endsection
