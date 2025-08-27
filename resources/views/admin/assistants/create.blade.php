@extends('admin.layout')

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

.card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #7fb069, #a7c957);
    animation: shimmer 2s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { transform: translateX(-100%); }
    50% { transform: translateX(100%); }
}

.card-header h3 {
    color: #ffffff;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    font-weight: 700;
    margin: 0;
}

.card-header i {
    color: #a7c957;
    text-shadow: 0 1px 3px rgba(127, 176, 105, 0.5);
}

/* Corps de carte */
.card-body {
    background: rgba(255, 255, 255, 0.03);
    color: #ffffff;
    padding: 2rem;
}

/* Labels */
.form-label {
    color: #ffffff;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    margin-bottom: 0.5rem;
}

/* Champs de formulaire */
.form-control {
    background: rgba(255, 255, 255, 0.08);
    border: 2px solid rgba(127, 176, 105, 0.3);
    border-radius: 10px;
    color: #ffffff;
    transition: all 0.3s ease;
    font-weight: 500;
}

.form-control:focus {
    background: rgba(255, 255, 255, 0.12);
    border-color: #a7c957;
    box-shadow: 0 0 0 0.2rem rgba(127, 176, 105, 0.25);
    color: #ffffff;
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

/* Boutons */
.btn {
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, #7fb069 0%, #4a7c59 100%);
    border: none;
    color: #ffffff;
    box-shadow: 0 4px 15px rgba(127, 176, 105, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #a7c957 0%, #7fb069 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(127, 176, 105, 0.4);
}

/* Messages d'erreur */
.invalid-feedback {
    color: #ff6b6b;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

/* Animations d'entrée pour les sections */
.form-group {
    animation: slideInUp 0.8s ease-out;
}

.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.2s; }
.form-group:nth-child(3) { animation-delay: 0.3s; }
.form-group:nth-child(4) { animation-delay: 0.4s; }
.form-group:nth-child(5) { animation-delay: 0.5s; }
.form-group:nth-child(6) { animation-delay: 0.6s; }
.form-group:nth-child(7) { animation-delay: 0.7s; }
.form-group:nth-child(8) { animation-delay: 0.8s; }

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

/* Effet de lueur sur les icônes */
.fas, .far, .fab {
    color: #a7c957;
    text-shadow: 0 1px 3px rgba(127, 176, 105, 0.5);
    transition: all 0.3s ease;
}

.fas:hover, .far:hover, .fab:hover {
    color: #ffffff;
    text-shadow: 0 2px 6px rgba(127, 176, 105, 0.8);
    transform: scale(1.2);
}

/* Titre principal */
.page-title {
    color: #ffffff;
    text-shadow: 0 3px 6px rgba(0, 0, 0, 0.8);
    font-weight: 900;
    font-size: 2.5rem;
    text-align: center;
    margin-bottom: 2rem;
    animation: glow 2s ease-in-out infinite alternate;
}

@keyframes glow {
    from { text-shadow: 0 3px 6px rgba(0, 0, 0, 0.8); }
    to { text-shadow: 0 3px 6px rgba(127, 176, 105, 0.5); }
}
</style>

<!-- Particules flottantes -->
<div class="particles" id="particles"></div>

<div class="main-container">
    <h1 class="page-title">
        <i class="fas fa-user-plus me-3"></i>
        Créer un assistant
    </h1>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-user-plus fa-lg me-3"></i>
                    <h3>Créer un nouvel assistant</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.assistants.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group mb-4">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required placeholder="Entrez le nom">
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom') }}" required placeholder="Entrez le prénom">
                            @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="exemple@email.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="+225 0123456789">
                            @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Entrez le mot de passe">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required placeholder="Confirmez le mot de passe">
                            @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="photo" class="form-label">Photo de profil</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                            @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="bio" class="form-label">Biographie</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4" placeholder="Décrivez brièvement l'assistant...">{{ old('bio') }}</textarea>
                            @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group text-center mt-5">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-save me-2"></i> Créer l'assistant
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Génération des particules
document.addEventListener('DOMContentLoaded', function() {
    const particlesContainer = document.getElementById('particles');
    for (let i = 0; i < 20; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 15 + 's';
        particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
        particlesContainer.appendChild(particle);
    }
});
</script>
@endsection 