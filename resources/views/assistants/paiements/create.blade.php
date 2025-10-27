@extends('assistants.layout')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-credit-card me-2"></i>Créer un paiement</h1>
    <form method="POST" action="{{ route('assistant.paiements.store') }}">
        @csrf
        <div class="mb-3">
            <label for="montant" class="form-label">Montant *</label>
            <input type="number" class="form-control" id="montant" name="montant" required min="0">
        </div>
        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
</div>
@endsection 