@extends('assistants.layout')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-layer-group me-2"></i>Créer un module</h1>
    <form method="POST" action="{{ route('assistant.modules.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="titre" class="form-label">Titre du module *</label>
            <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre') }}" required placeholder="Ex: Introduction à la programmation">
            @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="prix" class="form-label">Prix (optionnel)</label>
                    <input type="number" step="0.01" class="form-control @error('prix') is-invalid @enderror" id="prix" name="prix" value="{{ old('prix') }}">
                    @error('prix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="niveaux_ids" class="form-label">Niveaux concernés</label>
            <div class="mb-2">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="tous_niveaux" onclick="toggleTousNiveaux(this)">
                    <label class="form-check-label" for="tous_niveaux">Tous les niveaux</label>
                </div>
            </div>
            <select multiple class="form-control @error('niveaux_ids') is-invalid @enderror" id="niveaux_ids" name="niveaux_ids[]" size="6" style="min-height: 150px;">
                @foreach($niveaux as $niveau)
                    <option value="{{ $niveau->id }}" {{ (collect(old('niveaux_ids'))->contains($niveau->id)) ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                @endforeach
            </select>
            @error('niveaux_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">Maintenez Ctrl (Windows) ou Cmd (Mac) pour sélectionner plusieurs niveaux.</small>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Enregistrer</button>
            <button type="button" class="btn btn-success" onclick="enregistrerTousNiveaux()"><i class="fas fa-layer-group me-1"></i> Enregistrer dans tous les Niveaux</button>
        </div>
    </form>
</div>
<script>
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
</script>
@endsection