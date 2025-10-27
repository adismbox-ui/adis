@extends('assistants.layout')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus"></i> Nouvelle Session
        </h1>
        <a href="{{ route('assistant.sessions') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Créer une nouvelle session de formation</h6>
        </div>
        <div class="card-body">
            <form id="sessionForm" action="{{ route('assistant.sessions.store') }}" method="POST">
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
                                   required>
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
                              rows="3">{{ old('description') }}</textarea>
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
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="duree_seance_minutes" class="form-label">Durée séance (minutes) *</label>
                            <input type="number" 
                                   class="form-control @error('duree_seance_minutes') is-invalid @enderror" 
                                   id="duree_seance_minutes" 
                                   name="duree_seance_minutes" 
                                   value="{{ old('duree_seance_minutes', 60) }}" 
                                   min="15" 
                                   max="480" 
                                   required>
                            @error('duree_seance_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="places_max" class="form-label">Places maximum</label>
                            <input type="number" 
                                   class="form-control @error('places_max') is-invalid @enderror" 
                                   id="places_max" 
                                   name="places_max" 
                                   value="{{ old('places_max') }}" 
                                   min="1">
                            @error('places_max')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Laissez vide pour illimité</small>
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
                    <a href="{{ route('assistant.sessions') }}" class="btn btn-secondary">
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

@push('scripts')
<script>
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
    
    function getSundaysCount(startStr, endStr){
        const start = new Date(startStr);
        const end = new Date(endStr);
        if (isNaN(start) || isNaN(end) || end < start) return 0;
        let count = 0; const d = new Date(start);
        while (d <= end) { if (d.getDay() === 0) count++; d.setDate(d.getDate()+1); }
        return count;
    }

    function computeSundays(){
        if (!nbSeances) return 0;
        if (!dateDebut.value || !dateFin.value) { nbSeances.value = 0; if(nbSeancesHint){nbSeancesHint.textContent='';} return 0; }
        const count = getSundaysCount(dateDebut.value, dateFin.value);
        nbSeances.value = count;
        if (nbSeancesHint) nbSeancesHint.textContent = count === 1 ? '(1 dimanche)' : `(${count} dimanches)`;
        return count;
    }

    dateDebut.addEventListener('change', function() {
        if (!isSunday(this.value)) {
            this.value = '';
            dateFin.value = '';
            if (nbSeances) { nbSeances.value = 0; if(nbSeancesHint){nbSeancesHint.textContent='';} }
            return;
        }
        const debut = new Date(this.value);
        const fin = new Date(debut);
        fin.setDate(debut.getDate() + 7);
        const yyyy = fin.getFullYear();
        const mm = String(fin.getMonth() + 1).padStart(2, '0');
        const dd = String(fin.getDate()).padStart(2, '0');
        dateFin.value = `${yyyy}-${mm}-${dd}`;
        computeSundays();
    });

    dateFin.addEventListener('change', computeSundays);

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
@endpush
@endsection 