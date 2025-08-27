@extends('assistants.layout')

@section('content')
<div class="container py-4">
    <div class="content-card">
        <div class="card-header-custom">
            <h2 class="card-title">Détail de l'inscription</h2>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item"><strong>Apprenant :</strong> {{ $inscription->apprenant->utilisateur->nom ?? '-' }}</li>
                <li class="list-group-item"><strong>Module :</strong> {{ $inscription->module->titre ?? '-' }}</li>
                <li class="list-group-item"><strong>Session :</strong> {{ $inscription->sessionFormation->nom ?? '-' }}</li>
                <li class="list-group-item"><strong>Date d'inscription :</strong> {{ $inscription->created_at ? $inscription->created_at->format('d/m/Y') : '-' }}</li>
            </ul>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('assistant.inscriptions') }}" class="btn btn-outline-secondary">Retour aux inscriptions</a>
        </div>
    </div>
</div>
@endsection
