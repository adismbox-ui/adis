@extends('admin.layout')

@section('content')
<div class="container-fluid">
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i> Modifier le Niveau
        </h1>
        <a href="{{ route('admin.niveaux.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Modifier le niveau : {{ $niveau->nom }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.niveaux.update', $niveau) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du niveau *</label>
                            <input type="text" 
                                   class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" 
                                   name="nom" 
                                   value="{{ old('nom', $niveau->nom) }}" 
                                   required>
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
                                   value="{{ old('ordre', $niveau->ordre) }}" 
                                   min="0" 
                                   required>
                            @error('ordre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Plus le nombre est petit, plus le niveau apparaîtra en premier</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="4">{{ old('description', $niveau->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-video me-1"></i> Lien Google Meet (optionnel)
                    </div>
                    <div class="card-body">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-8">
                                <label for="lien_meet" class="form-label">Lien</label>
                                <input type="text" class="form-control @error('lien_meet') is-invalid @enderror" id="lien_meet" name="lien_meet" value="{{ old('lien_meet', $niveau->lien_meet) }}" placeholder="https://meet.google.com/...">
                                @error('lien_meet')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 d-flex gap-2 mt-3 mt-md-0">
                                <button type="button" class="btn btn-primary w-50" onclick="regenMeet()"><i class="fas fa-sync-alt me-1"></i> Régénérer</button>
                                <button type="button" class="btn btn-secondary w-50" onclick="copyMeet()"><i class="fas fa-copy me-1"></i> Copier</button>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">Vous pouvez modifier manuellement, régénérer, ou laisser vide pour auto-génération à l'enregistrement.</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="formateur_id" class="form-label">Formateur responsable (optionnel)</label>
                    <select class="form-control @error('formateur_id') is-invalid @enderror" id="formateur_id" name="formateur_id">
                        <option value="">-- Aucun --</option>
                        @isset($formateurs)
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->id }}" {{ old('formateur_id', $niveau->formateur_id) == $formateur->id ? 'selected' : '' }}>
                                    {{ $formateur->utilisateur->prenom ?? '' }} {{ $formateur->utilisateur->nom ?? '' }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                    @error('formateur_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Si défini, ce formateur sera lié au niveau et pourra être utilisé comme responsable.</small>
                </div>

                <div class="mb-3">
                    <label for="session_id" class="form-label">Session (optionnel)</label>
                    <select class="form-control @error('session_id') is-invalid @enderror" id="session_id" name="session_id">
                        <option value="">-- Aucune --</option>
                        @isset($sessions)
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}" {{ old('session_id', $niveau->session_id) == $s->id ? 'selected' : '' }}>
                                    {{ $s->nom }} ({{ optional($s->date_debut)->format('d/m/Y') }} - {{ optional($s->date_fin)->format('d/m/Y') }})
                                </option>
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
                               {{ old('actif', $niveau->actif) ? 'checked' : '' }}>
                        <label class="form-check-label" for="actif">
                            Niveau actif
                        </label>
                    </div>
                    <small class="form-text text-muted">Un niveau inactif ne sera pas visible lors de la création de sessions</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.niveaux.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function regenMeet(){
    const chars = 'abcdefghijklmnopqrstuvwxyz';
    const rand = n => Array.from({length:n}, () => chars[Math.floor(Math.random()*chars.length)]).join('');
    const lien = `https://meet.google.com/${rand(3)}-${rand(4)}-${rand(3)}`;
    const input = document.getElementById('lien_meet');
    if (input) input.value = lien;
}
function copyMeet(){
    const input = document.getElementById('lien_meet');
    if (!input || !input.value) return;
    navigator.clipboard.writeText(input.value).then(() => {
        input.classList.add('is-valid');
        setTimeout(()=> input.classList.remove('is-valid'), 1000);
    });
}
</script>
@endsection 