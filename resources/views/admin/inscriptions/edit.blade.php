@extends('admin.layout')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-check me-2"></i> Modifier l'Inscription
        </h1>
        <a href="{{ route('admin.inscriptions') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white"><i class="fas fa-user-graduate me-2"></i> Apprenant</div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-3" style="width:40px;height:40px;font-weight:bold;">
                            {{ strtoupper(substr($inscription->apprenant->utilisateur->prenom ?? '?',0,1)) }}{{ strtoupper(substr($inscription->apprenant->utilisateur->nom ?? '?',0,1)) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $inscription->apprenant->utilisateur->prenom ?? '-' }} {{ $inscription->apprenant->utilisateur->nom ?? '-' }}</div>
                            <div class="text-muted small">{{ $inscription->apprenant->utilisateur->email ?? '-' }}</div>
                            <div class="text-muted small">{{ $inscription->apprenant->utilisateur->telephone ?? '-' }}</div>
                            @if($inscription->apprenant->niveau)
                                <span class="badge bg-info text-dark mt-1">Niveau : {{ $inscription->apprenant->niveau->nom }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white"><i class="fas fa-book me-2"></i> Module</div>
                <div class="card-body">
                    <div class="fw-bold mb-1">{{ $inscription->module->titre ?? '-' }}</div>
                    <div class="text-muted small mb-1">Discipline : {{ $inscription->module->discipline ?? '-' }}</div>
                    <div class="text-muted small mb-1">Prix : <span class="badge bg-success">{{ $inscription->module->prix ?? '-' }} FCFA</span></div>
                    <div class="text-muted small">Dates : {{ $inscription->module->date_debut ?? '-' }} au {{ $inscription->module->date_fin ?? '-' }}</div>
                    @if($inscription->module->formateur && $inscription->module->formateur->utilisateur)
                        <div class="text-muted small">Formateur : {{ $inscription->module->formateur->utilisateur->prenom }} {{ $inscription->module->formateur->utilisateur->nom }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.inscriptions.update', $inscription) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="statut" class="form-label">Statut</label>
                    <select class="form-select" id="statut" name="statut" required>
                        <option value="en_attente" @if(old('statut', $inscription->statut) == 'en_attente') selected @endif>En attente</option>
                        <option value="valide" @if(old('statut', $inscription->statut) == 'valide') selected @endif>Validé</option>
                        <option value="refuse" @if(old('statut', $inscription->statut) == 'refuse') selected @endif>Refusé</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="date_inscription" class="form-label">Date d'inscription</label>
                    <input type="date" class="form-control" id="date_inscription" name="date_inscription" value="{{ old('date_inscription', $inscription->date_inscription) }}" required>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Enregistrer</button>
                <a href="{{ route('admin.inscriptions') }}" class="btn btn-secondary ms-2">Annuler</a>
            </form>
        </div>
    </div>
</div>
@endsection 