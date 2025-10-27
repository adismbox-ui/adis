@extends('admin.layout')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-credit-card me-2"></i> Détail du Paiement
        </h1>
        <a href="{{ route('admin.paiements') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item"><strong>ID :</strong> {{ $paiement->id }}</li>
                <li class="list-group-item"><strong>Apprenant :</strong> {{ $paiement->apprenant->utilisateur->prenom ?? '-' }} {{ $paiement->apprenant->utilisateur->nom ?? '-' }}</li>
                <li class="list-group-item"><strong>Module :</strong> {{ $paiement->module->titre ?? '-' }}</li>
                <li class="list-group-item"><strong>Montant :</strong> {{ number_format($paiement->montant, 0, ',', ' ') }} F CFA</li>
                <li class="list-group-item"><strong>Date :</strong> {{ $paiement->created_at ? $paiement->created_at->format('d/m/Y H:i') : '-' }}</li>
                <li class="list-group-item"><strong>Référence :</strong> {{ $paiement->reference ?? '-' }}</li>
                <li class="list-group-item"><strong>Méthode :</strong> {{ $paiement->methode ?? '-' }}</li>
                <li class="list-group-item"><strong>Statut :</strong> <span class="badge bg-{{ $paiement->statut == 'valide' ? 'success' : ($paiement->statut == 'en_attente' ? 'warning text-dark' : 'danger') }}">{{ ucfirst($paiement->statut) }}</span></li>
                <li class="list-group-item"><strong>Notes :</strong> {{ $paiement->notes ?? '-' }}</li>
            </ul>
            <div class="mt-4">
                <a href="{{ route('admin.paiements.edit', $paiement) }}" class="btn btn-primary me-2"><i class="fas fa-edit"></i> Modifier</a>
                <form action="{{ route('admin.paiements.destroy', $paiement) }}" method="POST" style="display:inline" onsubmit="return confirm('Supprimer ce paiement ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 