@extends('admin.layout')

@section('content')
<div class="animated-background"></div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="fas fa-plus me-2"></i>Nouvel Appel à Projet
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.appels-a-projets.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Référence *</label>
                                <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                                       name="reference" value="{{ old('reference') }}" required>
                                @error('reference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Intitulé *</label>
                                <input type="text" class="form-control @error('intitule') is-invalid @enderror" 
                                       name="intitule" value="{{ old('intitule') }}" required>
                                @error('intitule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Domaine *</label>
                                <input type="text" class="form-control @error('domaine') is-invalid @enderror" 
                                       name="domaine" value="{{ old('domaine') }}" required>
                                @error('domaine')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date limite de soumission *</label>
                                <input type="date" class="form-control @error('date_limite_soumission') is-invalid @enderror" 
                                       name="date_limite_soumission" value="{{ old('date_limite_soumission') }}" required>
                                @error('date_limite_soumission')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">État *</label>
                                <select class="form-select @error('etat') is-invalid @enderror" name="etat" required>
                                    <option value="">Sélectionner un état</option>
                                    <option value="ouvert" {{ old('etat') == 'ouvert' ? 'selected' : '' }}>Ouvert</option>
                                    <option value="cloture" {{ old('etat') == 'cloture' ? 'selected' : '' }}>Clôturé</option>
                                </select>
                                @error('etat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Montant estimatif (FCFA)</label>
                                <input type="number" class="form-control @error('montant_estimatif') is-invalid @enderror" 
                                       name="montant_estimatif" value="{{ old('montant_estimatif') }}" step="0.01">
                                @error('montant_estimatif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bénéficiaires</label>
                                <input type="text" class="form-control @error('beneficiaires') is-invalid @enderror" 
                                       name="beneficiaires" value="{{ old('beneficiaires') }}">
                                @error('beneficiaires')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de clôture</label>
                                <input type="date" class="form-control @error('date_cloture') is-invalid @enderror" 
                                       name="date_cloture" value="{{ old('date_cloture') }}">
                                @error('date_cloture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Détails de l'offre</label>
                            <textarea class="form-control @error('details_offre') is-invalid @enderror" 
                                      name="details_offre" rows="5">{{ old('details_offre') }}</textarea>
                            @error('details_offre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.appels-a-projets.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Créer l'appel à projet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 