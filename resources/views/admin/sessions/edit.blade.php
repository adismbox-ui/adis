@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i> Modifier la Session
        </h1>
        <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Modifier la session de formation</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sessions.update', $session) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom de la session *</label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $session->nom) }}" required>
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="niveau_id" class="form-label">Niveau *</label>
                            <select class="form-select @error('niveau_id') is-invalid @enderror" id="niveau_id" name="niveau_id" required>
                                <option value="">Choisir un niveau...</option>
                                @foreach($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}" {{ old('niveau_id', $session->niveau_id) == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                                @endforeach
                            </select>
                            @error('niveau_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $session->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_debut" class="form-label">Date de début (dimanche uniquement) *</label>
                            <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut', $session->date_debut ? $session->date_debut->format('Y-m-d') : '') }}" min="{{ date('Y-m-d') }}" required>
                            @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_fin" class="form-label">Date de fin (dimanche uniquement) *</label>
                            <input type="date" class="form-control @error('date_fin') is-invalid @enderror" id="date_fin" name="date_fin" value="{{ old('date_fin', $session->date_fin ? $session->date_fin->format('Y-m-d') : '') }}" required>
                            @error('date_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="duree_seance_minutes" class="form-label">Durée séance (minutes) *</label>
                            <input type="number" class="form-control @error('duree_seance_minutes') is-invalid @enderror" id="duree_seance_minutes" name="duree_seance_minutes" value="{{ old('duree_seance_minutes', $session->duree_seance_minutes) }}" min="15" max="480" required>
                            @error('duree_seance_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="nombre_seances" class="form-label">Nombre de séances *</label>
                            <input type="number" class="form-control @error('nombre_seances') is-invalid @enderror" id="nombre_seances" name="nombre_seances" value="{{ old('nombre_seances', $session->nombre_seances) }}" min="1" required>
                            @error('nombre_seances')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="places_max" class="form-label">Places maximum</label>
                            <input type="number" class="form-control @error('places_max') is-invalid @enderror" id="places_max" name="places_max" value="{{ old('places_max', $session->places_max) }}" min="1">
                            @error('places_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">Laissez vide pour illimité</small>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Modules inclus dans la session</label>
                <div class="row">
                        @foreach($modules as $module)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="modules[]" id="module_{{ $module->id }}" value="{{ $module->id }}" {{ (collect(old('modules', $session->modules->pluck('id')))->contains($module->id)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="module_{{ $module->id }}">
                                        {{ $module->titre }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <small class="form-text text-muted">Cochez un ou plusieurs modules à inclure dans la session.</small>
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
                        <i class="fas fa-save"></i> Enregistrer les modifications
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

    // Désactive tous les jours sauf dimanche dans le sélecteur
    dateDebut.addEventListener('input', function() {
        if (this.value && !isSunday(this.value)) {
            alert('La date de début doit être un dimanche.');
            this.value = '';
            dateFin.value = '';
        }
    });
    
    dateDebut.addEventListener('change', function() {
        if (!isSunday(this.value)) {
            this.value = '';
            dateFin.value = '';
            return;
        }
        // Calculer le dimanche suivant (7 jours après)
        const debut = new Date(this.value);
        const fin = new Date(debut);
        fin.setDate(debut.getDate() + 7);
        // Formater la date YYYY-MM-DD
        const yyyy = fin.getFullYear();
        const mm = String(fin.getMonth() + 1).padStart(2, '0');
        const dd = String(fin.getDate()).padStart(2, '0');
        dateFin.value = `${yyyy}-${mm}-${dd}`;
    });
});
</script>
@endpush
@endsection 