@extends('assistants.layout')

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

.card-header::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Corps de carte */
.card-body {
    padding: 2rem;
    color: #ffffff;
}

/* Titre principal */
h1 {
    color: #ffffff;
    font-size: 2.5rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 2rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    background: linear-gradient(135deg, #7fb069, #a7c957);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: glow 2s ease-in-out infinite alternate;
}

@keyframes glow {
    from { filter: drop-shadow(0 0 5px rgba(127, 176, 105, 0.5)); }
    to { filter: drop-shadow(0 0 20px rgba(127, 176, 105, 0.8)); }
}

/* Labels */
label {
    color: #7fb069;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    display: block;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

/* Champs de formulaire */
.form-control, .form-select {
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(127, 176, 105, 0.3);
    border-radius: 15px;
    color: #ffffff;
    font-size: 1rem;
    padding: 12px 20px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.form-control:focus, .form-select:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #7fb069;
    box-shadow: 0 0 20px rgba(127, 176, 105, 0.3);
    color: #ffffff;
    outline: none;
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

/* Options des selects */
.form-select option {
    background: #1a1a1a;
    color: #ffffff;
}

/* Checkbox personnalisé */
.form-check-input {
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(127, 176, 105, 0.3);
    border-radius: 5px;
    width: 20px;
    height: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-check-input:checked {
    background: #7fb069;
    border-color: #7fb069;
    box-shadow: 0 0 10px rgba(127, 176, 105, 0.5);
}

.form-check-label {
    color: #ffffff;
    font-weight: 500;
    margin-left: 0.5rem;
    cursor: pointer;
}

/* Boutons */
.btn {
    border: none;
    border-radius: 25px;
    padding: 12px 30px;
    font-weight: 600;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    cursor: pointer;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}

.btn-success {
    background: linear-gradient(135deg, #7fb069, #a7c957);
    color: white;
    box-shadow: 0 8px 25px rgba(127, 176, 105, 0.4);
}

.btn-success:hover {
    background: linear-gradient(135deg, #a7c957, #7fb069);
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(127, 176, 105, 0.6);
    color: white;
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: white;
    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #495057, #343a40);
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(108, 117, 125, 0.6);
    color: white;
}

/* Messages d'erreur */
.invalid-feedback {
    color: #ff6b6b;
    font-size: 0.9rem;
    margin-top: 0.25rem;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.is-invalid {
    border-color: #ff6b6b !important;
    box-shadow: 0 0 10px rgba(255, 107, 107, 0.3) !important;
}

/* Responsive */
@media (max-width: 768px) {
    .main-container {
        padding: 1rem;
    }
    
    h1 {
        font-size: 2rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .btn {
        padding: 10px 20px;
        font-size: 0.9rem;
    }
}

/* Animation d'entrée pour les champs */
.form-group {
    animation: slideInUp 0.6s ease-out;
    animation-fill-mode: both;
}

.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.2s; }
.form-group:nth-child(3) { animation-delay: 0.3s; }
.form-group:nth-child(4) { animation-delay: 0.4s; }
.form-group:nth-child(5) { animation-delay: 0.5s; }
.form-group:nth-child(6) { animation-delay: 0.6s; }
.form-group:nth-child(7) { animation-delay: 0.7s; }
.form-group:nth-child(8) { animation-delay: 0.8s; }
.form-group:nth-child(9) { animation-delay: 0.9s; }
.form-group:nth-child(10) { animation-delay: 1.0s; }

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<!-- Particules animées -->
<div class="particles">
    <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
    <div class="particle" style="left: 20%; animation-delay: 2s;"></div>
    <div class="particle" style="left: 30%; animation-delay: 4s;"></div>
    <div class="particle" style="left: 40%; animation-delay: 6s;"></div>
    <div class="particle" style="left: 50%; animation-delay: 8s;"></div>
    <div class="particle" style="left: 60%; animation-delay: 10s;"></div>
    <div class="particle" style="left: 70%; animation-delay: 12s;"></div>
    <div class="particle" style="left: 80%; animation-delay: 14s;"></div>
    <div class="particle" style="left: 90%; animation-delay: 16s;"></div>
</div>

<div class="main-container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h1>✏️ Modifier la formation</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('assistant.formations.update', $formation) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nom">
                                        <i class="fas fa-graduation-cap me-2"></i>Nom de la formation
                                    </label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" name="nom" value="{{ old('nom', $formation->nom) }}" required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="niveau_id">
                                        <i class="fas fa-layer-group me-2"></i>Niveau
                                    </label>
                                    <select class="form-select @error('niveau_id') is-invalid @enderror" 
                                            id="niveau_id" name="niveau_id" required>
                                        <option value="">Sélectionner un niveau</option>
                                        @foreach($niveaux as $niveau)
                                            <option value="{{ $niveau->id }}" 
                                                    {{ old('niveau_id', $formation->niveau_id) == $niveau->id ? 'selected' : '' }}>
                                                {{ $niveau->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('niveau_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">
                                <i class="fas fa-align-left me-2"></i>Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $formation->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="date_debut">
                                        <i class="fas fa-calendar-plus me-2"></i>Date de début
                                    </label>
                                    <input type="date" class="form-control @error('date_debut') is-invalid @enderror" 
                                           id="date_debut" name="date_debut" 
                                           value="{{ old('date_debut', $formation->date_debut) }}" required>
                                    @error('date_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="date_fin">
                                        <i class="fas fa-calendar-check me-2"></i>Date de fin
                                    </label>
                                    <input type="date" class="form-control @error('date_fin') is-invalid @enderror" 
                                           id="date_fin" name="date_fin" 
                                           value="{{ old('date_fin', $formation->date_fin) }}" required>
                                    @error('date_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="heure_debut">
                                        <i class="fas fa-clock me-2"></i>Heure de début
                                    </label>
                                    <input type="time" class="form-control @error('heure_debut') is-invalid @enderror" 
                                           id="heure_debut" name="heure_debut" 
                                           value="{{ old('heure_debut', $formation->heure_debut) }}">
                                    @error('heure_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="heure_fin">
                                        <i class="fas fa-clock me-2"></i>Heure de fin
                                    </label>
                                    <input type="time" class="form-control @error('heure_fin') is-invalid @enderror" 
                                           id="heure_fin" name="heure_fin" 
                                           value="{{ old('heure_fin', $formation->heure_fin) }}">
                                    @error('heure_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="duree_seance_minutes">
                                        <i class="fas fa-hourglass-half me-2"></i>Durée (minutes)
                                    </label>
                                    <input type="number" class="form-control @error('duree_seance_minutes') is-invalid @enderror" 
                                           id="duree_seance_minutes" name="duree_seance_minutes" 
                                           value="{{ old('duree_seance_minutes', $formation->duree_seance_minutes) }}" 
                                           min="15" max="480" required>
                                    @error('duree_seance_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="prix">
                                        <i class="fas fa-euro-sign me-2"></i>Prix (€)
                                    </label>
                                    <input type="number" step="0.01" class="form-control @error('prix') is-invalid @enderror" 
                                           id="prix" name="prix" 
                                           value="{{ old('prix', $formation->prix) }}" min="0">
                                    @error('prix')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="places_max">
                                        <i class="fas fa-users me-2"></i>Places maximum
                                    </label>
                                    <input type="number" class="form-control @error('places_max') is-invalid @enderror" 
                                           id="places_max" name="places_max" 
                                           value="{{ old('places_max', $formation->places_max) }}" min="1">
                                    @error('places_max')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="jour_semaine">
                                        <i class="fas fa-calendar-day me-2"></i>Jour de la semaine
                                    </label>
                                    <select class="form-select @error('jour_semaine') is-invalid @enderror" 
                                            id="jour_semaine" name="jour_semaine">
                                        <option value="">Tous les jours</option>
                                        <option value="Lundi" {{ old('jour_semaine', $formation->jour_semaine) == 'Lundi' ? 'selected' : '' }}>Lundi</option>
                                        <option value="Mardi" {{ old('jour_semaine', $formation->jour_semaine) == 'Mardi' ? 'selected' : '' }}>Mardi</option>
                                        <option value="Mercredi" {{ old('jour_semaine', $formation->jour_semaine) == 'Mercredi' ? 'selected' : '' }}>Mercredi</option>
                                        <option value="Jeudi" {{ old('jour_semaine', $formation->jour_semaine) == 'Jeudi' ? 'selected' : '' }}>Jeudi</option>
                                        <option value="Vendredi" {{ old('jour_semaine', $formation->jour_semaine) == 'Vendredi' ? 'selected' : '' }}>Vendredi</option>
                                        <option value="Samedi" {{ old('jour_semaine', $formation->jour_semaine) == 'Samedi' ? 'selected' : '' }}>Samedi</option>
                                        <option value="Dimanche" {{ old('jour_semaine', $formation->jour_semaine) == 'Dimanche' ? 'selected' : '' }}>Dimanche</option>
                                    </select>
                                    @error('jour_semaine')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="modules">
                                <i class="fas fa-book me-2"></i>Modules associés
                            </label>
                            <select class="form-select @error('modules') is-invalid @enderror" 
                                    id="modules" name="modules[]" multiple>
                                @foreach($modules as $module)
                                    <option value="{{ $module->id }}" 
                                            {{ in_array($module->id, old('modules', $formation->modules->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $module->titre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('modules')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1" 
                                       {{ old('actif', $formation->actif) ? 'checked' : '' }}>
                                <label class="form-check-label" for="actif">
                                    <i class="fas fa-toggle-on me-2"></i>Formation active
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('assistant.formations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation en temps réel des dates
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    
    dateDebut.addEventListener('change', function() {
        if (dateFin.value && this.value > dateFin.value) {
            dateFin.value = this.value;
        }
    });
    
    dateFin.addEventListener('change', function() {
        if (dateDebut.value && this.value < dateDebut.value) {
            alert('La date de fin doit être postérieure à la date de début');
            this.value = dateDebut.value;
        }
    });
    
    // Validation des heures
    const heureDebut = document.getElementById('heure_debut');
    const heureFin = document.getElementById('heure_fin');
    
    heureDebut.addEventListener('change', function() {
        if (heureFin.value && this.value >= heureFin.value) {
            alert('L\'heure de fin doit être postérieure à l\'heure de début');
            this.value = '';
        }
    });
    
    heureFin.addEventListener('change', function() {
        if (heureDebut.value && this.value <= heureDebut.value) {
            alert('L\'heure de fin doit être postérieure à l\'heure de début');
            this.value = '';
        }
    });
});
</script>

@endsection
