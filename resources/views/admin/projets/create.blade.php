@extends('admin.layout')
@section('content')

<style>
body {
    background: linear-gradient(135deg, #222 0%, #2d5016 100%);
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}

.animated-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: -2;
    background: url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80') center/cover no-repeat;
    animation: slowZoom 20s ease-in-out infinite alternate;
}

.card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(127, 176, 105, 0.3);
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
}

.card-header {
    background: linear-gradient(135deg, rgba(127, 176, 105, 0.2) 0%, rgba(45, 80, 22, 0.3) 100%);
    border-bottom: 2px solid rgba(127, 176, 105, 0.3);
    color: #ffffff;
    font-weight: 700;
}

.form-control, .form-select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(127, 176, 105, 0.3);
    color: #fff;
}

.form-control:focus, .form-select:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #32CD32;
    box-shadow: 0 0 0 0.2rem rgba(50, 205, 50, 0.25);
    color: #fff;
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.form-label {
    color: #fff;
    font-weight: 600;
}

.btn-success {
    background: linear-gradient(135deg, #32CD32, #228B22, #006400);
    color: #fff;
    border: none;
}

.btn-success:hover {
    background: linear-gradient(135deg, #006400, #228B22, #32CD32);
    color: #fff;
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: #fff;
    border: none;
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #495057, #6c757d);
    color: #fff;
}

.alert {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(127, 176, 105, 0.3);
    color: #fff;
}
</style>

<div class="animated-background"></div>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Ajouter un Nouveau Projet
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.projets.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="intitule" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Intitulé du projet *
                                </label>
                                <input type="text" class="form-control @error('intitule') is-invalid @enderror" 
                                       id="intitule" name="intitule" value="{{ old('intitule') }}" required>
                                @error('intitule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="responsable" class="form-label">
                                    <i class="fas fa-user me-1"></i>Responsable *
                                </label>
                                <input type="text" class="form-control @error('responsable') is-invalid @enderror" 
                                       id="responsable" name="responsable" value="{{ old('responsable') }}" required>
                                @error('responsable')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="beneficiaires" class="form-label">
                                    <i class="fas fa-users me-1"></i>Bénéficiaires *
                                </label>
                                <textarea class="form-control @error('beneficiaires') is-invalid @enderror" 
                                          id="beneficiaires" name="beneficiaires" rows="3" required>{{ old('beneficiaires') }}</textarea>
                                @error('beneficiaires')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="objectif" class="form-label">
                                    <i class="fas fa-bullseye me-1"></i>Objectif du projet *
                                </label>
                                <textarea class="form-control @error('objectif') is-invalid @enderror" 
                                          id="objectif" name="objectif" rows="3" required>{{ old('objectif') }}</textarea>
                                @error('objectif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="debut" class="form-label">
                                    <i class="fas fa-calendar-plus me-1"></i>Date de début *
                                </label>
                                <input type="date" class="form-control @error('debut') is-invalid @enderror" 
                                       id="debut" name="debut" value="{{ old('debut') }}" required>
                                @error('debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="fin_prevue" class="form-label">
                                    <i class="fas fa-calendar-check me-1"></i>Date de fin prévue *
                                </label>
                                <input type="date" class="form-control @error('fin_prevue') is-invalid @enderror" 
                                       id="fin_prevue" name="fin_prevue" value="{{ old('fin_prevue') }}" required>
                                @error('fin_prevue')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="taux_avancement" class="form-label">
                                    <i class="fas fa-percentage me-1"></i>Taux d'avancement (%) *
                                </label>
                                <input type="number" class="form-control @error('taux_avancement') is-invalid @enderror" 
                                       id="taux_avancement" name="taux_avancement" value="{{ old('taux_avancement', 0) }}" 
                                       min="0" max="100" required>
                                @error('taux_avancement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="montant_total" class="form-label">
                                    <i class="fas fa-money-bill-wave me-1"></i>Montant total (FCFA)
                                </label>
                                <input type="number" class="form-control @error('montant_total') is-invalid @enderror" 
                                       id="montant_total" name="montant_total" value="{{ old('montant_total') }}" 
                                       min="0" step="1000">
                                @error('montant_total')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="montant_collecte" class="form-label">
                                    <i class="fas fa-hand-holding-usd me-1"></i>Montant collecté (FCFA)
                                </label>
                                <input type="number" class="form-control @error('montant_collecte') is-invalid @enderror" 
                                       id="montant_collecte" name="montant_collecte" value="{{ old('montant_collecte', 0) }}" 
                                       min="0" step="1000">
                                @error('montant_collecte')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">
                                    <i class="fas fa-info-circle me-1"></i>Statut *
                                </label>
                                <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                    <option value="">-- Choisir un statut --</option>
                                    <option value="en_cours" {{ old('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="realise" {{ old('statut') == 'realise' ? 'selected' : '' }}>Réalisé</option>
                                    <option value="a_financer" {{ old('statut') == 'a_financer' ? 'selected' : '' }}>À financer</option>
                                    <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>Description (optionnel)
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Informations importantes :</h6>
                            <ul class="mb-0">
                                <li>Les champs marqués d'un * sont obligatoires</li>
                                <li>Le taux d'avancement doit être entre 0 et 100%</li>
                                <li>La date de fin doit être postérieure à la date de début</li>
                                <li>Le montant collecté ne peut pas dépasser le montant total</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.projets.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Enregistrer le Projet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validation côté client pour les montants
document.getElementById('montant_collecte').addEventListener('input', function() {
    const montantTotal = parseFloat(document.getElementById('montant_total').value) || 0;
    const montantCollecte = parseFloat(this.value) || 0;
    
    if (montantCollecte > montantTotal) {
        this.setCustomValidity('Le montant collecté ne peut pas dépasser le montant total');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('montant_total').addEventListener('input', function() {
    const montantTotal = parseFloat(this.value) || 0;
    const montantCollecte = parseFloat(document.getElementById('montant_collecte').value) || 0;
    
    if (montantCollecte > montantTotal) {
        document.getElementById('montant_collecte').setCustomValidity('Le montant collecté ne peut pas dépasser le montant total');
    } else {
        document.getElementById('montant_collecte').setCustomValidity('');
    }
});
</script>

@endsection 