@extends('apprenants.layout')

@section('content')
<style>
    /* Image de fond avec overlay */
    .profile-page {
        position: relative;
        min-height: 100vh;
        background: url('https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2074&q=80') center center/cover no-repeat;
    }

    .profile-page::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(67, 234, 74, 0.3) 0%, rgba(46, 204, 64, 0.4) 100%);
        z-index: 1;
    }

    .profile-container {
        position: relative;
        z-index: 2;
        padding: 2rem 0;
    }

    /* Animations et effets modernes */
    .profile-header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 25px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        animation: slideInDown 0.8s ease-out;
        position: relative;
        overflow: hidden;
    }

    .profile-header::before {
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

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .profile-avatar {
        width: 120px !important;
        height: 120px !important;
        background: linear-gradient(135deg, #43ea4a 0%, #2ecc40 100%) !important;
        border-radius: 50% !important;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(67, 234, 74, 0.3) !important;
        border: 5px solid #fff !important;
        animation: pulse 2s ease-in-out infinite;
        position: relative;
        overflow: hidden;
    }

    .profile-avatar::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
        animation: rotate 10s linear infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .profile-avatar i {
        position: relative;
        z-index: 2;
        color: white !important;
        font-size: 3rem !important;
    }

    /* Cards avec effet glassmorphism */
    .profile-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        animation: slideInUp 0.8s ease-out;
        position: relative;
        overflow: hidden;
    }

    .profile-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #43ea4a, #2ecc40, #27ae60);
        animation: shimmer 3s ease-in-out infinite;
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

    .profile-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(67, 234, 74, 0.2);
    }

    /* Formulaires avec animations */
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 16px;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
    }

    .form-control:focus {
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

    .form-group:focus-within .form-label {
        color: #27ae60 !important;
        transform: translateY(-2px);
    }

    /* Boutons avec animations */
    .btn-gradient-primary {
        background: linear-gradient(135deg, #43ea4a 0%, #2ecc40 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-gradient-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }

    .btn-gradient-primary:hover::before {
        left: 100%;
    }

    .btn-gradient-primary:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 10px 25px rgba(67, 234, 74, 0.4);
    }

    .btn-gradient-secondary {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc40 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-gradient-secondary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }

    .btn-gradient-secondary:hover::before {
        left: 100%;
    }

    .btn-gradient-secondary:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 10px 25px rgba(39, 174, 96, 0.4);
    }

    /* Badges stylisés */
    .badge {
        border-radius: 20px;
        padding: 8px 16px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Alertes avec animations */
    .alert {
        border-radius: 15px;
        border: none;
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

    /* Responsive */
    @media (max-width: 768px) {
        .profile-container {
            padding: 1rem 0;
        }
        
        .profile-header {
            margin: 0 1rem;
        }
        
        .profile-card {
            margin: 0 1rem 1rem 1rem;
        }
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

<div class="profile-page">
    <div class="profile-container">
        <div class="container">
    <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">
                    <!-- Header du profil -->
                    <div class="profile-header text-center mb-5 p-5 animate-on-scroll">
                        <div class="profile-avatar mx-auto mb-4">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h2 class="fw-bold mb-3" style="color: #2ecc40; letter-spacing: 1px;">
                            {{ $user->prenom ?? '' }} {{ $user->nom ?? '' }}
                        </h2>
                        <div class="mb-3">
                            <span class="badge bg-light text-dark fs-6" style="background: rgba(67, 234, 74, 0.1) !important; color: #2ecc40 !important;">
                                <i class="fas fa-envelope me-2"></i> {{ $user->email }}
                            </span>
                </div>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <span class="badge" style="background: linear-gradient(135deg, #43ea4a, #2ecc40); color: white;">
                                <i class="fas fa-phone-alt me-2"></i> {{ $user->telephone ?? 'Non renseigné' }}
                            </span>
                            <span class="badge" style="background: linear-gradient(135deg, #27ae60, #2ecc40); color: white;">
                                <i class="fas fa-user-graduate me-2"></i> Apprenant
                            </span>
                </div>
            </div>

                    <!-- Contenu principal -->
            <div class="row g-4">
                        <!-- Formulaire d'informations -->
                        <div class="col-lg-6">
                            <div class="profile-card p-4 animate-on-scroll">
                                <h4 class="mb-4" style="color: #2ecc40;">
                                    <i class="fas fa-user-edit me-2"></i> Modifier mes informations
                                </h4>
                                
                    @if(session('success'))
                                    <div class="alert alert-success text-center animate__animated animate__fadeInDown">
                                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                    </div>
                    @endif
                                
                    <form method="POST" action="{{ route('apprenants.prifil_test.update') }}" class="needs-validation" novalidate>
                        @csrf
                                    
                                    <div class="form-group mb-3">
                                        <label for="prenom" class="form-label">
                                            <i class="fas fa-user me-2"></i>Prénom
                                        </label>
                            <input type="text" class="form-control" id="prenom" name="prenom" value="{{ old('prenom', $user->prenom ?? '') }}" required>
                        </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="nom" class="form-label">
                                            <i class="fas fa-user me-2"></i>Nom
                                        </label>
                            <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom', $user->nom ?? '') }}" required>
                        </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope me-2"></i>Email
                                        </label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
                        </div>
                                    
                                    <div class="form-group mb-4">
                                        <label for="telephone" class="form-label">
                                            <i class="fas fa-phone me-2"></i>Téléphone
                                        </label>
                            <input type="text" class="form-control" id="telephone" name="telephone" value="{{ old('telephone', $user->telephone ?? '') }}">
                        </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-gradient-primary btn-lg">
                                            <i class="fas fa-save me-2"></i>Mettre à jour
                                        </button>
                                </div>
                            </form>
                        </div>
                    </div>

                        <!-- Formulaire de mot de passe -->
                        <div class="col-lg-6">
                            <div class="profile-card p-4 animate-on-scroll">
                                <h4 class="mb-4" style="color: #27ae60;">
                                    <i class="fas fa-key me-2"></i> Modifier le mot de passe
                                </h4>
                                
                                @if(session('password_success'))
                                    <div class="alert alert-success text-center animate__animated animate__fadeInDown">
                                        <i class="fas fa-check-circle me-2"></i>{{ session('password_success') }}
                </div>
                            @endif
                                
                            @if(session('password_error'))
                                    <div class="alert alert-danger text-center animate__animated animate__shakeX">
                                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('password_error') }}
                                    </div>
                            @endif
                                
                            <form method="POST" action="{{ route('apprenants.prifil_test.update_password') }}" class="needs-validation" novalidate>
                                @csrf
                                    
                                    <div class="form-group mb-3">
                                        <label for="current_password" class="form-label">
                                            <i class="fas fa-lock me-2"></i>Mot de passe actuel
                                        </label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="new_password" class="form-label">
                                            <i class="fas fa-key me-2"></i>Nouveau mot de passe
                                        </label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                                </div>
                                    
                                    <div class="form-group mb-4">
                                        <label for="new_password_confirmation" class="form-label">
                                            <i class="fas fa-check-circle me-2"></i>Confirmer le nouveau mot de passe
                                        </label>
                                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required minlength="6">
                                </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-gradient-secondary btn-lg">
                                            <i class="fas fa-key me-2"></i>Changer le mot de passe
                                        </button>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
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
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });

    // Animation des badges
    document.querySelectorAll('.badge').forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.05)';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
</script>
@endsection
