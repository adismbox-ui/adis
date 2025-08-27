@extends('admin.layout')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold"><i class="fas fa-user-edit me-2"></i>Modifier un formateur</h1>
    <a href="{{ route('formateurs.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <style>
        select.form-control, select.form-select { color:#000 !important; background:#fff !important; }
        select.form-control option, select.form-select option { color:#000 !important; background:#fff !important; }
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }
        
        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            border: 2px solid rgba(76, 175, 80, 0.4);
            background: rgba(15, 35, 25, 0.9);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .form-check-input:checked {
            background: linear-gradient(135deg, #4caf50, #66bb6a);
            border-color: #4caf50;
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.2);
        }
        
        .form-check-input:focus {
            box-shadow: 0 0 0 8px rgba(76, 175, 80, 0.25);
        }
        
        .form-check-label {
            color: #81c784;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .form-check-label:hover {
            color: #a5d6a7;
            transform: translateX(2px);
        }
        
        .text-success {
            color: #4caf50 !important;
        }
        
        .text-danger {
            color: #f44336 !important;
        }
        
        .d-flex {
            display: flex !important;
        }
        
        .gap-3 {
            gap: 1rem !important;
        }
        </style>
        <form method="POST" action="{{ route('formateurs.update', $formateur) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom', $formateur->utilisateur->prenom ?? '') }}" required>
                    @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $formateur->utilisateur->nom ?? '') }}" required>
                    @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $formateur->utilisateur->email ?? '') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone', $formateur->utilisateur->telephone ?? '') }}">
                    @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="mot_de_passe" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                    <input type="password" class="form-control @error('mot_de_passe') is-invalid @enderror" id="mot_de_passe" name="mot_de_passe">
                    @error('mot_de_passe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="mot_de_passe_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password" class="form-control" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation">
                </div>
                <div class="col-md-6">
                    <label for="niveau_id" class="form-label">Niveau (optionnel)</label>
                    <select id="niveau_id" name="niveau_id" class="form-select @error('niveau_id') is-invalid @enderror">
                        <option value="">-- Aucun --</option>
                        @foreach(($niveaux ?? []) as $niveau)
                            <option value="{{ $niveau->id }}" {{ old('niveau_id') == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                        @endforeach
                    </select>
                    @error('niveau_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Devenir assistant</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="devenir_assistant" id="devenir_assistant_oui" value="oui" {{ old('devenir_assistant', $isAssistant ? 'oui' : 'non') == 'oui' ? 'checked' : '' }}>
                            <label class="form-check-label" for="devenir_assistant_oui">
                                <i class="fas fa-check-circle text-success"></i> Oui
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="devenir_assistant" id="devenir_assistant_non" value="non" {{ old('devenir_assistant', $isAssistant ? 'oui' : 'non') == 'non' ? 'checked' : '' }}>
                            <label class="form-check-label" for="devenir_assistant_non">
                                <i class="fas fa-times-circle text-danger"></i> Non
                            </label>
                        </div>
                    </div>
                    <small class="text-muted">Choisissez si ce formateur doit aussi avoir le rôle d'assistant</small>
                    @error('devenir_assistant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection 