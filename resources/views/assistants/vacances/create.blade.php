@extends('assistants.layout')
@section('content')
<style>
.card-3d, .card, .alert-info, label, h1, h2, h3, h4, h5, h6, p, span, .form-label, .form-control, .alert, .info-text, .text-info, .text-dark, .form-check-label, .form-text, .form-group, .form-check, .form-check-input, .form-check-inline, .form-select, .form-control, .custom-control-label {
    color: #111 !important;
}

body {
    background: #14532d !important; /* vert sombre */
    color: #111 !important; /* texte noir */
    min-height: 100vh;
    position: relative;
    animation: none;
}
@keyframes bg-zoom {
    0% { background-size: 100% 100%; }
    100% { background-size: 110% 110%; }
}
.bg-green-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(120deg, rgba(34,139,34,0.55) 0%, rgba(144,238,144,0.35) 100%, rgba(0,100,0,0.25) 100%);
    z-index: 0;
    pointer-events: none;
    animation: overlay-fade 8s ease-in-out infinite alternate;
}
@keyframes overlay-fade {
    0% { opacity: 0.7; }
    100% { opacity: 0.9; }
}
.card-3d, .card, .alert-info {
    background: linear-gradient(120deg, #e6ffe6 0%, #b2f7b8 100%);
    border: 1.5px solid #43e97b;
    box-shadow: 0 4px 24px rgba(34,139,34,0.10);
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
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus"></i> Nouvelle Période de Vacances
        </h1>
        <a href="{{ route('assistant.vacances') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Créer une nouvelle période de vacances</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('assistant.vacances.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom de la période *</label>
                            <input type="text" 
                                   class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" 
                                   name="nom" 
                                   value="{{ old('nom') }}" 
                                   placeholder="Ex: Vacances d'été, Vacances de Noël..."
                                   required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="actif" 
                                       name="actif" 
                                       value="1" 
                                       {{ old('actif', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="actif">
                                    Période active
                                </label>
                            </div>
                            <small class="form-text text-muted">Une période inactive n'empêchera pas la création de sessions</small>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="3" 
                              placeholder="Description optionnelle de cette période de vacances...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_debut" class="form-label">Date de début *</label>
                            <input type="date" 
                                   class="form-control @error('date_debut') is-invalid @enderror" 
                                   id="date_debut" 
                                   name="date_debut" 
                                   value="{{ old('date_debut') }}" 
                                   required>
                            @error('date_debut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_fin" class="form-label">Date de fin *</label>
                            <input type="date" 
                                   class="form-control @error('date_fin') is-invalid @enderror" 
                                   id="date_fin" 
                                   name="date_fin" 
                                   value="{{ old('date_fin') }}" 
                                   required>
                            @error('date_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Information :</h6>
                    <p class="mb-0">Les sessions créées pendant cette période de vacances seront automatiquement bloquées. Assurez-vous de bien planifier vos sessions en dehors de ces périodes.</p>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('assistant.vacances') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer la période
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    dateDebut.addEventListener('change', function() {
        dateFin.min = this.value;
        if (dateFin.value && dateFin.value < this.value) {
            dateFin.value = this.value;
        }
    });
});
</script>
@endsection