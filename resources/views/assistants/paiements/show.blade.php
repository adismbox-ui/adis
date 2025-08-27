@extends('assistants.layout')

@section('content')
<div class="container py-4">
    <div class="content-card">
        <div class="card-header-custom">
            <h2 class="card-title">Détail du paiement</h2>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item"><strong>Montant :</strong> {{ $paiement->montant }} FCFA</li>
                <li class="list-group-item"><strong>Apprenant :</strong> {{ $paiement->apprenant->utilisateur->nom ?? '-' }}</li>
                <li class="list-group-item"><strong>Module :</strong> {{ $paiement->module->titre ?? '-' }}</li>
                <li class="list-group-item"><strong>Date :</strong> {{ $paiement->created_at ? $paiement->created_at->format('d/m/Y') : '-' }}</li>
            </ul>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('assistant.paiements') }}" class="btn btn-outline-secondary">Retour aux paiements</a>
        </div>
    </div>
</div>
@endsection
