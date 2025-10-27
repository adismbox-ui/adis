@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold text-primary"><i class="fas fa-file-upload me-2"></i>Créer un document</h1>
    <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Retour à la liste</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">
    @csrf
                    <div class="mb-3">
                        <label for="niveau_id" class="form-label">Niveau</label>
                        <select class="form-select" id="niveau_id" name="niveau_id" required>
                            <option value="">Choisir un niveau</option>
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
    <label for="module_id" class="form-label">Module</label>
    <select class="form-select" id="module_id" name="module_id">
        <option value="">Choisir un module (optionnel)</option>
        @foreach($modules as $module)
            <option value="{{ $module->id }}">
                {{ $module->titre }} @if($module->niveau) (Niveau : {{ $module->niveau->nom }}) @endif
            </option>
        @endforeach
    </select>
    <small class="form-text text-muted">Laisser vide pour un document général du niveau.</small>
</div>
<div class="mb-3">
    <label for="semaine" class="form-label">Semaine <span class="text-danger">*</span></label>
    <select class="form-select @error('semaine') is-invalid @enderror" id="semaine" name="semaine" required>
        <option value="">-- Choisir la semaine --</option>
        @for($i = 1; $i <= 12; $i++)
            <option value="{{ $i }}" {{ old('semaine') == $i ? 'selected' : '' }}>Semaine {{ $i }}</option>
        @endfor
    </select>
    @error('semaine')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre du document</label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                    </div>
                    <div class="mb-3">
    <label for="fichier" class="form-label">Document à partager (PDF)</label>
    <input type="file" class="form-control" id="fichier" name="fichier" accept="application/pdf" required>
</div>
<div class="mb-3">
    <label for="audio" class="form-label">Fichier audio (optionnel, mp3/wav/ogg)</label>
    <input type="file" class="form-control" id="audio" name="audio" accept="audio/mp3,audio/wav,audio/ogg">
    <small class="form-text text-muted">Vous pouvez ajouter un fichier audio en complément du PDF.</small>
</div>
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i>Enregistrer</button>
                    </div>
</form> 
            </div>
        </div>
    </div>
</div>
@endsection 
