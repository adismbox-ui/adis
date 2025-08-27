@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-hand-holding-heart me-2"></i>Créer un Nouveau Don
                    </h4>
                    <a href="{{ route('admin.dons.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.dons.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Informations du donateur -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Informations du Donateur
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="nom_donateur" class="form-label">Nom complet *</label>
                                    <input type="text" class="form-control @error('nom_donateur') is-invalid @enderror" 
                                           id="nom_donateur" name="nom_donateur" value="{{ old('nom_donateur') }}" required>
                                    @error('nom_donateur')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email_donateur" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('email_donateur') is-invalid @enderror" 
                                           id="email_donateur" name="email_donateur" value="{{ old('email_donateur') }}" required>
                                    @error('email_donateur')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="telephone" class="form-label">Téléphone</label>
                                    <input type="text" class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" name="telephone" value="{{ old('telephone') }}">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Informations du don -->
                            <div class="col-md-6">
                                <h5 class="text-success mb-3">
                                    <i class="fas fa-money-bill me-2"></i>Informations du Don
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="montant" class="form-label">Montant (F CFA) *</label>
                                    <input type="number" class="form-control @error('montant') is-invalid @enderror" 
                                           id="montant" name="montant" value="{{ old('montant') }}" min="100" step="100" required>
                                    @error('montant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="type_don" class="form-label">Type de don *</label>
                                    <select class="form-select @error('type_don') is-invalid @enderror" id="type_don" name="type_don" required>
                                        <option value="">Sélectionner un type</option>
                                        <option value="ponctuel" {{ old('type_don') == 'ponctuel' ? 'selected' : '' }}>Ponctuel</option>
                                        <option value="mensuel" {{ old('type_don') == 'mensuel' ? 'selected' : '' }}>Mensuel</option>
                                    </select>
                                    @error('type_don')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="mode_paiement" class="form-label">Mode de paiement *</label>
                                    <select class="form-select @error('mode_paiement') is-invalid @enderror" id="mode_paiement" name="mode_paiement" required>
                                        <option value="">Sélectionner un mode</option>
                                        <option value="carte" {{ old('mode_paiement') == 'carte' ? 'selected' : '' }}>Carte bancaire</option>
                                        <option value="virement" {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>Virement bancaire</option>
                                        <option value="mobile" {{ old('mode_paiement') == 'mobile' ? 'selected' : '' }}>Mobile money</option>
                                    </select>
                                    @error('mode_paiement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-info mb-3">
                                    <i class="fas fa-project-diagram me-2"></i>Projet Associé
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="projet_id" class="form-label">Projet (optionnel)</label>
                                    <select class="form-select @error('projet_id') is-invalid @enderror" id="projet_id" name="projet_id">
                                        <option value="">Fonds général</option>
                                        @foreach($projets as $projet)
                                            <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>
                                                {{ $projet->intitule }} - {{ number_format($projet->montant_total, 0, ',', ' ') }} F CFA
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('projet_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label">Message du donateur</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" 
                                              id="message" name="message" rows="3" placeholder="Message optionnel du donateur...">{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input @error('recu_demande') is-invalid @enderror" 
                                               type="checkbox" id="recu_demande" name="recu_demande" value="1" 
                                               {{ old('recu_demande') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="recu_demande">
                                            Demander un reçu électronique
                                        </label>
                                        @error('recu_demande')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Enregistrer le Don
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        border-radius: 15px;
    }
    
    .card-header {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white;
        border-radius: 15px 15px 0 0 !important;
        border: none;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 0.2rem rgba(34, 197, 94, 0.25);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        border: none;
        border-radius: 8px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(34, 197, 94, 0.3);
    }
    
    .alert {
        border-radius: 10px;
        border: none;
    }
    
    h5 {
        color: #374151;
        font-weight: 600;
    }
</style>

@endsection 