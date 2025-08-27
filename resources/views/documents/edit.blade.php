@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold text-warning"><i class="fas fa-edit me-2"></i>Modifier le document</h1>
    <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Retour à la liste</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.documents.update', $document) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
                    <div class="mb-3">
                        <label for="module_id" class="form-label">Module</label>
                        <select class="form-select" id="module_id" name="module_id" required>
                            <option value="">Choisir un module</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->id }}" {{ $document->module_id == $module->id ? 'selected' : '' }}>{{ $module->titre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="semaine" class="form-label">Semaine</label>
                        <select class="form-select" id="semaine" name="semaine" required>
                            <option value="">Choisir la semaine</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $document->semaine == $i ? 'selected' : '' }}>Semaine {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="niveau_id" class="form-label">Niveau</label>
                        <select class="form-select" id="niveau_id" name="niveau_id" required>
                            <option value="">Choisir un niveau</option>
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id }}" {{ $document->niveau_id == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre du document</label>
                        <input type="text" class="form-control" id="titre" name="titre" value="{{ old('titre', $document->titre) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="fichier" class="form-label">Document à partager (PDF)</label>
                        @if($document->fichier)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $document->fichier) }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-pdf"></i> Voir le PDF actuel</a>
                            </div>
                        @endif
                        <input type="file" class="form-control" id="fichier" name="fichier" accept="application/pdf">
                        <small class="form-text text-muted">Laisser vide pour conserver le fichier actuel.</small>
                    </div>
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save me-2"></i>Mettre à jour</button>
                    </div>
</form> 
            </div>
        </div>
    </div>
</div>
@endsection 