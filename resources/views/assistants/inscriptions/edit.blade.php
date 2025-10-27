@extends('assistants.layout')

@section('content')
<div class="container py-4">
    <div class="content-card">
        <div class="card-header-custom">
            <h2 class="card-title">Modifier l'inscription</h2>
        </div>
        <form method="POST" action="#">
            @csrf
            <div class="mb-3">
                <label class="form-label">Apprenant</label>
                <input type="text" class="form-control" value="{{ $inscription->apprenant->utilisateur->nom ?? '-' }}" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Module</label>
                <input type="text" class="form-control" value="{{ $inscription->module->titre ?? '-' }}" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Session</label>
                <input type="text" class="form-control" value="{{ $inscription->sessionFormation->nom ?? '-' }}" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Date d'inscription</label>
                <input type="text" class="form-control" value="{{ $inscription->created_at ? $inscription->created_at->format('d/m/Y') : '-' }}" readonly>
            </div>
            <div class="text-end">
                <a href="{{ route('assistant.inscriptions') }}" class="btn btn-outline-secondary">Annuler</a>
                <button type="submit" class="btn btn-success" disabled>Enregistrer (démo)</button>
            </div>
        </form>
    </div>
</div>
@endsection
