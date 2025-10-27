@extends('admin.layout')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin-create-dark.css') }}" />
@endsection

@section('content')

<!-- Fond animé et particules -->
<div class="bg-animated"></div>
<div class="floating-particles" id="particles"></div>

<!-- Conteneur principal -->
<div class="main-container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card slide-in-up">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-plus"></i>
                        Créer un module
                    </h3>
                </div>
                <div class="card-body">
                    <form id="form-module" method="POST" action="{{ route('modules.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group">
                            <label for="titre" class="form-label">Titre du module</label>
                            <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre') }}" required>
                            @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="discipline" class="form-label">Nom de la discipline</label>
                            <input type="text" class="form-control @error('discipline') is-invalid @enderror" id="discipline" name="discipline" value="{{ old('discipline') }}">
                            @error('discipline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="niveaux_ids" class="form-label">Niveaux</label>
                            <div class="mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tous_niveaux" onclick="toggleTousNiveaux(this)">
                                    <label class="form-check-label" for="tous_niveaux">
                                        <strong>Tous les niveaux</strong>
                                    </label>
                                </div>
                            </div>
                            <select class="form-select @error('niveaux_ids') is-invalid @enderror" id="niveaux_ids" name="niveaux_ids[]" multiple size="5">
                                @foreach($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}" {{ (collect(old('niveaux_ids'))->contains($niveau->id)) ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                                @endforeach
                            </select>
                            @error('niveaux_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Vous pouvez sélectionner plusieurs niveaux en maintenant Ctrl (Windows) ou Cmd (Mac).</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_debut" class="form-label">Date de début</label>
                                    <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut') }}">
                                    @error('date_debut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_fin" class="form-label">Date de fin</label>
                                    <input type="date" class="form-control @error('date_fin') is-invalid @enderror" id="date_fin" name="date_fin" value="{{ old('date_fin') }}">
                                    @error('date_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="formateur_id" class="form-label">Formateur (optionnel)</label>
                            <select class="form-select @error('formateur_id') is-invalid @enderror" id="formateur_id" name="formateur_id">
                                <option value="">Choisir un formateur</option>
                                @foreach($formateurs as $formateur)
                                    <option value="{{ $formateur->id }}" {{ old('formateur_id') == $formateur->id ? 'selected' : '' }}>
                                        {{ $formateur->utilisateur->prenom }} {{ $formateur->utilisateur->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('formateur_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Si non choisi, le formateur défini sur le niveau sera utilisé automatiquement.</small>
                        </div>

                        <div class="form-group">
                            <label for="lien" class="form-label">Lien Google Meet</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('lien') is-invalid @enderror" id="lien" name="lien" value="{{ old('lien') }}">
                                <button type="button" class="btn btn-primary" onclick="genererMeet()">
                                    <i class="fas fa-random"></i> Générer
                                </button>
                            </div>
                            @error('lien')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="support" class="form-label">Support PDF</label>
                            <input type="file" class="form-control @error('support') is-invalid @enderror" id="support" name="support" accept="application/pdf">
                            @error('support')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="audio" class="form-label">Audio du cours</label>
                            <input type="file" class="form-control @error('audio') is-invalid @enderror" id="audio" name="audio" accept="audio/mp3,audio/mpeg,audio/wav,audio/x-wav,audio/m4a">
                            @error('audio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="prix" class="form-label">Prix (FCFA)</label>
                            <input type="number" class="form-control @error('prix') is-invalid @enderror" id="prix" name="prix" value="{{ old('prix') }}">
                            @error('prix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Certificat</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="certificat" id="certificat_oui" value="1" {{ old('certificat') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="certificat_oui">Oui</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="certificat" id="certificat_non" value="0" {{ old('certificat') == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="certificat_non">Non</label>
                                </div>
                            </div>
                            @error('certificat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group text-center mt-5">
                            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i> Enregistrer
                                </button>
                                <button type="button" class="btn btn-success btn-lg" onclick="enregistrerTousNiveaux()">
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
    const container = document.getElementById('particles');
    if (container) {
        for (let i = 0; i < 15; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size = Math.random() * 40 + 20;
            p.style.width = size + 'px';
            p.style.height = size + 'px';
            p.style.left = (Math.random() * 100) + 'vw';
            p.style.top = (Math.random() * 100) + 'vh';
            p.style.animationDelay = (Math.random() * 15) + 's';
            p.style.animationDuration = (Math.random() * 10 + 15) + 's';
            container.appendChild(p);
        }
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
    document.getElementById('form-module').submit();
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