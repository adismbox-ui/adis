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

.card-header h3, .card-header h6 {
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

.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    border: none;
    color: #ffffff;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #495057 0%, #343a40 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
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
.mb-3 {
    animation: slideInUp 0.8s ease-out;
}

.mb-3:nth-child(1) { animation-delay: 0.1s; }
.mb-3:nth-child(2) { animation-delay: 0.2s; }
.mb-3:nth-child(3) { animation-delay: 0.3s; }
.mb-3:nth-child(4) { animation-delay: 0.4s; }

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

/* En-tête de page */
.page-header {
    background: linear-gradient(135deg, rgba(127, 176, 105, 0.1) 0%, rgba(45, 80, 22, 0.2) 100%);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(127, 176, 105, 0.3);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #7fb069, #a7c957);
    animation: shimmer 3s ease-in-out infinite;
}

.page-header h1 {
    color: #ffffff;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    font-weight: 700;
    margin: 0;
}
</style>

<style>
/* Lisibilité des listes déroulantes (texte en noir, fond clair) */
select.form-control,
select.form-select {
    color: #000 !important;
    background-color: #fff !important;
}
select.form-control option,
select.form-select option {
    color: #000 !important;
    background-color: #fff !important;
}
</style>

<!-- Particules flottantes -->
<div class="particles" id="particles"></div>

<div class="main-container">
    <h1 class="page-title">
        <i class="fas fa-layer-group me-3"></i>
        Créer un niveau
    </h1>
    
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">
                <i class="fas fa-plus"></i> Nouveau Niveau
            </h1>
            <a href="{{ route('admin.niveaux.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold">Créer un nouveau niveau</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.niveaux.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du niveau *</label>
                            <input type="text" 
                                   class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" 
                                   name="nom" 
                                   value="{{ old('nom') }}" 
                                   required
                                   placeholder="Ex: Débutant, Intermédiaire, Avancé">
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="ordre" class="form-label">Ordre d'affichage *</label>
                            <input type="number" 
                                   class="form-control @error('ordre') is-invalid @enderror" 
                                   id="ordre" 
                                   name="ordre" 
                                   value="{{ old('ordre', 0) }}" 
                                   min="0" 
                                   required
                                   placeholder="0, 1, 2, 3...">
                            @error('ordre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Plus le nombre est petit, plus le niveau apparaîtra en premier</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="4"
                              placeholder="Décrivez brièvement ce niveau...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="formateur_id" class="form-label">Formateur responsable (optionnel)</label>
                    <select class="form-control @error('formateur_id') is-invalid @enderror" id="formateur_id" name="formateur_id">
                        <option value="">-- Aucun --</option>
                        @isset($formateurs)
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->id }}" {{ old('formateur_id') == $formateur->id ? 'selected' : '' }}>
                                    {{ $formateur->utilisateur->prenom ?? '' }} {{ $formateur->utilisateur->nom ?? '' }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                    @error('formateur_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Si défini, ce formateur sera lié au niveau et pourra être utilisé comme responsable.</small>
                </div>

                <div class="mb-3">
                    <label for="session_id" class="form-label">Session (optionnel)</label>
                    <select class="form-control @error('session_id') is-invalid @enderror" id="session_id" name="session_id">
                        <option value="">-- Aucune --</option>
                        @isset($sessions)
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}">{{ $s->nom }} ({{ optional($s->date_debut)->format('d/m/Y') }} - {{ optional($s->date_fin)->format('d/m/Y') }})</option>
                            @endforeach
                        @endisset
                    </select>
                    @error('session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="actif" 
                               name="actif" 
                               value="1" 
                               {{ old('actif', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="actif">
                            Niveau actif
                        </label>
                    </div>
                    <small class="text-muted">Un niveau inactif ne sera pas visible lors de la création de sessions</small>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-video me-2"></i>Générer un lien Google Meet (optionnel)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-8">
                                <label for="lien_meet" class="form-label">Lien Google Meet</label>
                                <input type="text" id="lien_meet" name="lien_meet" class="form-control" placeholder="Aucun" readonly>
                            </div>
                            <div class="col-md-4 d-flex gap-2 mt-3 mt-md-0">
                                <button type="button" class="btn btn-primary w-50" onclick="genererMeetNiveau()">
                                    <i class="fas fa-random me-1"></i> Générer
                                </button>
                                <button type="button" class="btn btn-secondary w-50" onclick="copierMeetNiveau()">
                                    <i class="fas fa-copy me-1"></i> Copier
                                </button>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">Astuce: cliquez sur Générer pour créer un lien de réunion; ce lien n'est pas enregistré avec le niveau.</small>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.niveaux.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer le niveau
                    </button>
                </div>
            </form>
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

function genererMeetNiveau() {
    const chars = 'abcdefghijklmnopqrstuvwxyz';
    const rand = (n) => Array.from({length:n}, () => chars[Math.floor(Math.random()*chars.length)]).join('');
    const lien = `https://meet.google.com/${rand(3)}-${rand(4)}-${rand(3)}`;
    const input = document.getElementById('lien_meet');
    if (input) input.value = lien;
}

function copierMeetNiveau() {
    const input = document.getElementById('lien_meet');
    if (!input || !input.value) return;
    navigator.clipboard.writeText(input.value).then(() => {
        const original = input.value;
        input.classList.add('is-valid');
        setTimeout(() => input.classList.remove('is-valid'), 1200);
    });
}
</script>
@endsection 