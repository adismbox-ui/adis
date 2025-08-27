@extends('admin.layout')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-check me-2"></i> Détail de l'Inscription
        </h1>
        <a href="{{ route('admin.inscriptions') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white"><i class="fas fa-user-graduate me-2"></i> Apprenant</div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-3" style="width:48px;height:48px;font-size:1.3rem;font-weight:bold;">
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
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-warning text-dark"><i class="fas fa-info-circle me-2"></i> Détails Inscription</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>ID :</strong> {{ $inscription->id }}</li>
                        <li class="list-group-item"><strong>Date d'inscription :</strong> {{ $inscription->date_inscription ? \Carbon\Carbon::parse($inscription->date_inscription)->format('d/m/Y') : '-' }}</li>
                        <li class="list-group-item"><strong>Statut :</strong> <span class="badge bg-{{ $inscription->statut == 'valide' ? 'success' : ($inscription->statut == 'en_attente' ? 'warning text-dark' : 'danger') }}">{{ ucfirst($inscription->statut) }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <a href="{{ route('admin.inscriptions.edit', $inscription) }}" class="btn btn-primary me-2"><i class="fas fa-edit"></i> Modifier</a>
        <form action="{{ route('admin.inscriptions.destroy', $inscription) }}" method="POST" style="display:inline" onsubmit="return confirm('Supprimer cette inscription ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Supprimer</button>
        </form>
    </div>
</div>
@endsection 