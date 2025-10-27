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

.card-header h3, .card-header h5 {
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
.form-control, .form-select {
    background: rgba(255, 255, 255, 0.08);
    border: 2px solid rgba(127, 176, 105, 0.3);
    border-radius: 10px;
    color: #ffffff;
    transition: all 0.3s ease;
    font-weight: 500;
}

.form-control:focus, .form-select:focus {
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

.btn-success {
    background: linear-gradient(135deg, #4a7c59 0%, #2d5016 100%);
    border: none;
    color: #ffffff;
    box-shadow: 0 4px 15px rgba(74, 124, 89, 0.3);
}

.btn-success:hover {
    background: linear-gradient(135deg, #7fb069 0%, #4a7c59 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(127, 176, 105, 0.4);
}

/* Checkbox et radio */
.form-check-input {
    background-color: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(127, 176, 105, 0.3);
}

.form-check-input:checked {
    background-color: #7fb069;
    border-color: #7fb069;
}

.form-check-label {
    color: #ffffff;
    font-weight: 500;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

/* Messages d'erreur */
.invalid-feedback {
    color: #ff6b6b;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

/* Textes d'aide */
.text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

/* Animations d'entrée pour les sections */
.card {
    animation: slideInUp 0.8s ease-out;
}

.card:nth-child(1) { animation-delay: 0.1s; }
.card:nth-child(2) { animation-delay: 0.2s; }
.card:nth-child(3) { animation-delay: 0.3s; }

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
        <i class="fas fa-plus-circle me-3"></i>
        Créer un module
    </h1>
    
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-plus fa-lg me-3"></i>
                    <h3>Créer un nouveau module</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('modules.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Section 1: Informations de base -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations de base</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="titre" class="form-label">Titre du module *</label>
                                        <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre') }}" required placeholder="Ex: Introduction à la programmation">
                                        @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="prix" class="form-label">Prix (optionnel)</label>
                                        <input type="number" step="0.01" class="form-control @error('prix') is-invalid @enderror" id="prix" name="prix" value="{{ old('prix') }}">
                                        @error('prix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Attribution et niveaux -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Attribution et niveaux</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="niveaux_ids" class="form-label">Niveaux concernés</label>
                                    <div class="mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="tous_niveaux" onclick="toggleTousNiveaux(this)">
                                            <label class="form-check-label" for="tous_niveaux">Tous les niveaux</label>
                                        </div>
                                    </div>
                                    <select class="form-select @error('niveaux_ids') is-invalid @enderror" id="niveaux_ids" name="niveaux_ids[]" multiple size="5">
                                        @foreach($niveaux as $niveau)
                                            <option value="{{ $niveau->id }}" {{ (collect(old('niveaux_ids'))->contains($niveau->id)) ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                                        @endforeach
                                    </select>
                                    @error('niveaux_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Maintenez Ctrl (Windows) ou Cmd (Mac) pour sélectionner plusieurs niveaux.</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Boutons d'action -->
                        <div class="text-center">
                            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-save me-2"></i> Enregistrer
                                </button>
                                <button type="button" class="btn btn-success btn-lg px-5" onclick="enregistrerTousNiveaux()">
                                    <i class="fas fa-layer-group me-2"></i> Enregistrer dans tous les Niveaux
                                </button>
                            </div>
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

function toggleTousNiveaux(checkbox) {
    const select = document.getElementById('niveaux_ids');
    for (let i = 0; i < select.options.length; i++) {
        select.options[i].selected = checkbox.checked;
    }
}

function enregistrerTousNiveaux() {
    document.getElementById('tous_niveaux').checked = true;
    toggleTousNiveaux(document.getElementById('tous_niveaux'));
    document.querySelector('form').submit();
}

function genererMeet() {
    const chars = 'abcdefghijklmnopqrstuvwxyz';
    function randomPart(len) {
        let str = '';
        for (let i = 0; i < len; i++) str += chars[Math.floor(Math.random() * chars.length)];
        return str;
    }
    const lien = `https://meet.google.com/${randomPart(3)}-${randomPart(4)}-${randomPart(3)}`;
    document.getElementById('lien').value = lien;
}
</script>
@endsection 