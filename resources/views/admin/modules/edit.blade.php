@extends('admin.layout')
@section('content')
<style>
body {
    background: linear-gradient(135deg, #062a17 0%, #0e4024 100%) !important;
    background-size: 200% 200%;
    animation: bg-shift 12s ease-in-out infinite alternate;
    min-height: 100vh;
}
@keyframes bg-shift { from { background-position: 0% 50%; } to { background-position: 100% 50%; } }
.bg-green-overlay { display: none; }
.main-container, .card-3d, .card-header-3d {
    position: relative;
    z-index: 1;
}
.card-header-3d {
    background: linear-gradient(90deg, #228B22 0%, #43e97b 50%, #38f9d7 100%);
    color: #fff;
    border-radius: 20px 20px 0 0;
    box-shadow: 0 4px 24px rgba(34,139,34,0.18);
    border: none;
    padding: 2rem 2rem 1.5rem 2rem;
    margin-bottom: 0;
}
.card-3d, .card, .alert-info, .import-section {
    background: rgba(5, 20, 12, 0.85);
    border: 1.5px solid rgba(67, 233, 123, 0.25);
    box-shadow: 0 6px 28px rgba(0, 0, 0, 0.35);
    backdrop-filter: blur(6px);
}
.card-3d {
    border-radius: 18px;
}
.card-header.bg-primary, .card-header.bg-info, .card-header.bg-success {
    background: linear-gradient(90deg, #228B22 0%, #43e97b 100%) !important;
    color: #fff !important;
    border-radius: 18px 18px 0 0 !important;
    border: none;
}
.alert-info {
    color: #155724;
    background: linear-gradient(90deg, #d4f5e9 0%, #b2f7b8 100%);
    border-color: #43e97b;
}
.btn-success, .btn-success-3d, .btn-primary-3d {
    background: linear-gradient(135deg, #43e97b, #228B22, #006400);
    color: #fff;
    border: none;
}
.btn-success:hover, .btn-success-3d:hover, .btn-primary-3d:hover {
    background: linear-gradient(135deg, #006400, #228B22, #43e97b);
    color: #fff;
}
</style>
<div class="bg-green-overlay"></div>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-lg border-0 mt-4">
            <div class="card-header bg-primary text-white d-flex align-items-center" style="background: linear-gradient(90deg, #0f5c31 0%, #17a063 100%) !important; box-shadow: 0 6px 20px rgba(0,0,0,0.4);">
                <i class="fas fa-edit fa-lg me-2"></i>
                <h3 class="mb-0">Modifier le module</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.modules.update', $module) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="titre" class="form-label" style="color:#e9ffe9; text-shadow: 0 2px 6px rgba(0,0,0,0.6);">Titre du module *</label>
                        <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre', $module->titre) }}" required>
                        @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="prix" class="form-label" style="color:#e9ffe9; text-shadow: 0 2px 6px rgba(0,0,0,0.6);">Prix (optionnel)</label>
                        <input type="number" class="form-control @error('prix') is-invalid @enderror" id="prix" name="prix" value="{{ old('prix', $module->prix) }}">
                        @error('prix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label" style="color:#e9ffe9; text-shadow: 0 2px 6px rgba(0,0,0,0.6);">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $module->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color:#e9ffe9; text-shadow: 0 2px 6px rgba(0,0,0,0.6);">Niveau</label>
                        <select class="form-select" name="niveau_id" style="color:#0a2616; background:#caffe1; border-color:#1aa368;">
                            <option value="">Aucun</option>
                            @foreach(($niveaux ?? []) as $niveau)
                                <option value="{{ $niveau->id }}" {{ old('niveau_id', $module->niveau_id) == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 text-center">
                        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-3 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg shadow px-5 mb-2 mb-md-0"><i class="fas fa-save me-1"></i> Enregistrer</button>
                            <a href="{{ route('admin.modules') }}" class="btn btn-secondary btn-lg ms-2">Annuler</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 