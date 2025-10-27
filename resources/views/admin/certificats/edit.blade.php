@extends('admin.layout')
@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="fas fa-edit fa-lg me-2"></i>
            <h3 class="mb-0">Modifier le certificat</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.certificats.update', $certificat) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre</label>
                    <input type="text" class="form-control" id="titre" name="titre" value="{{ old('titre', $certificat->titre) }}" required>
                </div>
                <div class="mb-3">
                    <label for="date_obtention" class="form-label">Date d'obtention</label>
                    <input type="date" class="form-control" id="date_obtention" name="date_obtention" value="{{ old('date_obtention', $certificat->date_obtention) }}" required>
                </div>
                <div class="mb-3">
                    <label for="module_id" class="form-label">Module (optionnel)</label>
                    <select class="form-select" id="module_id" name="module_id">
                        <option value="">Aucun (certificat de niveau)</option>
                        @foreach(\App\Models\Module::all() as $module)
                            <option value="{{ $module->id }}" {{ old('module_id', $certificat->module_id) == $module->id ? 'selected' : '' }}>{{ $module->titre }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Mettre à jour</button>
                <a href="{{ route('admin.certificats.index') }}" class="btn btn-secondary ms-2"><i class="fas fa-arrow-left"></i> Retour</a>
            </form>
        </div>
    </div>
</div>
@endsection 