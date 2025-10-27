@extends('assistants.layout')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-edit me-2"></i>Modifier le module</h1>
        <a href="{{ route('assistant.modules') }}" class="btn btn-secondary">Retour</a>
    </div>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('assistant.modules.update', $module->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nom" class="form-label">Nom du module</label>
            <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom', $module->nom) }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $module->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="niveau_id" class="form-label">Niveau</label>
            <select class="form-select" id="niveau_id" name="niveau_id">
                <option value="">-- Choisir --</option>
                @foreach($niveaux as $niveau)
                    <option value="{{ $niveau->id }}" {{ old('niveau_id', $module->niveau_id) == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="lien" class="form-label">Lien Google Meet</label>
            <input type="url" class="form-control" id="lien" name="lien" value="{{ old('lien', $module->lien) }}">
        </div>
        <button type="submit" class="btn btn-success">Enregistrer</button>
    </form>
</div>
@endsection
