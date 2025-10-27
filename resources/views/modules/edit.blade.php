@extends('admin.layout')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-lg border-0 mt-4">
            <div class="card-header bg-warning text-dark d-flex align-items-center">
                <i class="fas fa-edit fa-lg me-2"></i>
                <h3 class="mb-0">Modifier le module</h3>
            </div>
            <div class="card-body bg-light">
                <form method="POST" action="{{ route('modules.update', $module) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="discipline" class="form-label">Nom de la discipline</label>
                            <input type="text" class="form-control @error('discipline') is-invalid @enderror" id="discipline" name="discipline" value="{{ old('discipline', $module->discipline) }}">
                            @error('discipline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="niveau" class="form-label">Niveau</label>
                            <input type="text" class="form-control @error('niveau') is-invalid @enderror" id="niveau" name="niveau" value="{{ old('niveau', $module->niveau) }}">
                            @error('niveau')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_debut" class="form-label">Date de début</label>
                            <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut', $module->date_debut) }}">
                            @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="date_fin" class="form-label">Date de fin</label>
                            <input type="date" class="form-control @error('date_fin') is-invalid @enderror" id="date_fin" name="date_fin" value="{{ old('date_fin', $module->date_fin) }}">
                            @error('date_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="formateur_id" class="form-label">Formateur</label>
                        <select class="form-select @error('formateur_id') is-invalid @enderror" id="formateur_id" name="formateur_id">
                            <option value="">Choisir un formateur</option>
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->id }}" {{ old('formateur_id', $module->formateur_id) == $formateur->id ? 'selected' : '' }}>
                                    {{ $formateur->utilisateur->prenom }} {{ $formateur->utilisateur->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('formateur_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="lien" class="form-label">Lien Google Meet</label>
                        <div class="input-group">
                            <input type="text" class="form-control @error('lien') is-invalid @enderror" id="lien" name="lien" value="{{ old('lien', $module->lien) }}">
                            <button type="button" class="btn btn-outline-secondary" onclick="genererMeet()"><i class="fas fa-random"></i> Générer</button>
                        </div>
                        @error('lien')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="support" class="form-label">Support PDF</label>
                        <input type="file" class="form-control @error('support') is-invalid @enderror" id="support" name="support" accept="application/pdf">
                        @error('support')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if($module->support)
                            <a href="{{ asset('storage/' . $module->support) }}" target="_blank" class="btn btn-link mt-2"><i class="fas fa-file-pdf"></i> Voir le support actuel</a>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="prix" class="form-label">Prix (FCFA)</label>
                        <input type="number" class="form-control @error('prix') is-invalid @enderror" id="prix" name="prix" value="{{ old('prix', $module->prix) }}">
                        @error('prix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $module->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Certificat</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="certificat" id="certificat_oui" value="1" {{ old('certificat', $module->certificat) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="certificat_oui">Oui</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="certificat" id="certificat_non" value="0" {{ old('certificat', $module->certificat) == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="certificat_non">Non</label>
                            </div>
                        </div>
                        @error('certificat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning btn-lg shadow"><i class="fas fa-save me-1"></i> Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function genererMeet() {
    // Génère un lien Google Meet aléatoire
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