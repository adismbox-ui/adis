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

/* Alertes */
.alert {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(127, 176, 105, 0.3);
    border-radius: 10px;
    color: #ffffff;
}

.alert-info {
    background: linear-gradient(135deg, rgba(127, 176, 105, 0.1) 0%, rgba(45, 80, 22, 0.2) 100%);
    border-color: rgba(127, 176, 105, 0.5);
}

.alert h6 {
    color: #a7c957;
    font-weight: 700;
}

.alert ul {
    color: rgba(255, 255, 255, 0.9);
}

.alert li {
    color: rgba(255, 255, 255, 0.9);
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
.mb-3:nth-child(5) { animation-delay: 0.5s; }
.mb-3:nth-child(6) { animation-delay: 0.6s; }

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

<!-- Particules flottantes -->
<div class="particles" id="particles"></div>

<div class="main-container">
    <h1 class="page-title">
        <i class="fas fa-calendar-plus me-3"></i>
        Créer une session
    </h1>
    
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">
                <i class="fas fa-plus"></i> Nouvelle Session
            </h1>
            <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold">Créer une nouvelle session de formation</h6>
        </div>
        <div class="card-body">
            <form id="sessionForm" action="{{ route('admin.sessions.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom de la session *</label>
                            <input type="text" 
                                   class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" 
                                   name="nom" 
                                   value="{{ old('nom') }}" 
                                   required
                                   placeholder="Ex: Session Débutant Janvier 2024">
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre de séances (dimanches)</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="number" id="nb_seances" class="form-control" value="0" readonly>
                        <span id="nb_seances_hint" class="text-muted"></span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="3"
                              placeholder="Décrivez brièvement cette session...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_debut" class="form-label">Date de début (dimanche uniquement) *</label>
                            <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut') }}" min="{{ date('Y-m-d') }}" required>
                            @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_fin" class="form-label">Date de fin (dimanche uniquement) *</label>
                            <input type="date" class="form-control @error('date_fin') is-invalid @enderror" id="date_fin" name="date_fin" value="{{ old('date_fin') }}" required>
                            @error('date_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="duree_seance_minutes" class="form-label">Durée séance (minutes) *</label>
                            <input type="number" 
                                   class="form-control @error('duree_seance_minutes') is-invalid @enderror" 
                                   id="duree_seance_minutes" 
                                   name="duree_seance_minutes" 
                                   value="{{ old('duree_seance_minutes', 60) }}" 
                                   min="15" 
                                   max="480" 
                                   required
                                   placeholder="60">
                            @error('duree_seance_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="places_max" class="form-label">Places maximum</label>
                            <input type="number" 
                                   class="form-control @error('places_max') is-invalid @enderror" 
                                   id="places_max" 
                                   name="places_max" 
                                   value="{{ old('places_max') }}" 
                                   min="1"
                                   placeholder="Illimité">
                            @error('places_max')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Laissez vide pour illimité</small>
                        </div>
                    </div>
                </div>

                
                @if($vacances->count() > 0)
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Périodes de vacances programmées :</h6>
                        <ul class="mb-0">
                            @foreach($vacances as $vacance)
                                <li><strong>{{ $vacance->nom }}</strong> : du {{ $vacance->date_debut->format('d/m/Y') }} au {{ $vacance->date_fin->format('d/m/Y') }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer la session
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

function isSunday(dateStr) {
    const date = new Date(dateStr);
    return date.getDay() === 0;
}

document.addEventListener('DOMContentLoaded', function() {
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const nbSeances = document.getElementById('nb_seances');
    const nbSeancesHint = document.getElementById('nb_seances_hint');

    // Désactive tous les jours sauf dimanche dans le sélecteur
    dateDebut.addEventListener('input', function() {
        if (this.value && !isSunday(this.value)) {
            alert('La date de début doit être un dimanche.');
            this.value = '';
            dateFin.value = '';
        }
    });
    
    function updateAutoFinAndCount() {
        if (!dateDebut.value) { dateFin.value = ''; if(nbSeances){nbSeances.value=0; nbSeancesHint.textContent='';} return; }
        if (!isSunday(dateDebut.value)) { dateDebut.value=''; dateFin.value=''; if(nbSeances){nbSeances.value=0; nbSeancesHint.textContent='';} return; }
        if (!isSunday(this.value)) {
            this.value = '';
            dateFin.value = '';
            return;
        }
        // Calculer le dimanche suivant (7 jours après)
        const debut = new Date(dateDebut.value);
        const fin = new Date(debut);
        fin.setDate(debut.getDate() + 7);
        // Formater la date YYYY-MM-DD
        const yyyy = fin.getFullYear();
        const mm = String(fin.getMonth() + 1).padStart(2, '0');
        const dd = String(fin.getDate()).padStart(2, '0');
        dateFin.value = `${yyyy}-${mm}-${dd}`;
        computeSundays();
    }

    function getSundaysCount(startStr, endStr){
        const start = new Date(startStr);
        const end = new Date(endStr);
        if (isNaN(start) || isNaN(end) || end < start) return 0;
        let count = 0; const d = new Date(start);
        while (d <= end) { if (d.getDay() === 0) count++; d.setDate(d.getDate()+1); }
        return count;
    }

    function computeSundays(){
        if (!nbSeances) return;
        if (!dateDebut.value || !dateFin.value) { nbSeances.value = 0; nbSeancesHint.textContent=''; return 0; }
        const count = getSundaysCount(dateDebut.value, dateFin.value);
        nbSeances.value = count;
        nbSeancesHint.textContent = count === 1 ? '(1 dimanche)' : `(${count} dimanches)`;
        return count;
    }

    dateDebut.addEventListener('change', updateAutoFinAndCount);
    dateFin.addEventListener('change', computeSundays);

    // Confirmation à la soumission si != 12
    const form = document.getElementById('sessionForm');
    if (form) {
        form.addEventListener('submit', function(e){
            const count = computeSundays();
            if (typeof count === 'number' && count !== 12) {
                const msg = count < 12
                    ? `Le nombre de séances (${count}) est inférieur à 12. Voulez-vous continuer ?`
                    : `Le nombre de séances (${count}) est supérieur à 12. Voulez-vous continuer ?`;
                if (!confirm(msg)) {
                    e.preventDefault();
                }
            }
        });
    }
});
</script>
@endsection 