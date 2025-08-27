@extends('admin.layout')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-credit-card me-2"></i> Modifier le Paiement
        </h1>
        <a href="{{ route('admin.paiements') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.paiements.update', $paiement) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="montant" class="form-label">Montant</label>
                    <input type="number" class="form-control" id="montant" name="montant" value="{{ old('montant', $paiement->montant) }}" required>
                </div>
                <div class="mb-3">
                    <label for="statut" class="form-label">Statut</label>
                    <select class="form-select" id="statut" name="statut" required>
                        <option value="en_attente" @if(old('statut', $paiement->statut) == 'en_attente') selected @endif>En attente</option>
                        <option value="valide" @if(old('statut', $paiement->statut) == 'valide') selected @endif>Validé</option>
                        <option value="refuse" @if(old('statut', $paiement->statut) == 'refuse') selected @endif>Refusé</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="methode" class="form-label">Méthode</label>
                    <input type="text" class="form-control" id="methode" name="methode" value="{{ old('methode', $paiement->methode) }}">
                </div>
                <div class="mb-3">
                    <label for="reference" class="form-label">Référence</label>
                    <input type="text" class="form-control" id="reference" name="reference" value="{{ old('reference', $paiement->reference) }}">
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes">{{ old('notes', $paiement->notes) }}</textarea>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Enregistrer</button>
                <a href="{{ route('admin.paiements') }}" class="btn btn-secondary ms-2">Annuler</a>
            </form>
        </div>
    </div>
</div>
@endsection 