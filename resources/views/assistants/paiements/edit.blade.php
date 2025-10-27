@extends('assistants.layout')

@section('content')
<div class="container py-4">
    <div class="content-card">
        <div class="card-header-custom">
            <h2 class="card-title">Modifier le paiement</h2>
        </div>
        <form method="POST" action="#">
            @csrf
            <div class="mb-3">
                <label class="form-label">Montant</label>
                <input type="text" class="form-control" value="{{ $paiement->montant }}" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Apprenant</label>
                <input type="text" class="form-control" value="{{ $paiement->apprenant->utilisateur->nom ?? '-' }}" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Module</label>
                <input type="text" class="form-control" value="{{ $paiement->module->titre ?? '-' }}" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="text" class="form-control" value="{{ $paiement->created_at ? $paiement->created_at->format('d/m/Y') : '-' }}" readonly>
            </div>
            <div class="text-end">
                <a href="{{ route('assistant.paiements') }}" class="btn btn-outline-secondary">Annuler</a>
                <button type="submit" class="btn btn-success" disabled>Enregistrer (démo)</button>
            </div>
        </form>
    </div>
</div>
@endsection
