@extends('assistants.layout')

@section('content')
<div class="container py-4">
    <div class="content-card">
        <div class="card-header-custom">
            <h2 class="card-title">Détail de la session</h2>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item"><strong>Nom :</strong> {{ $session->nom }}</li>
                <li class="list-group-item"><strong>Niveau :</strong> {{ $session->niveau->nom ?? '-' }}</li>
                <li class="list-group-item"><strong>Module :</strong> {{ $session->module->titre ?? '-' }}</li>
                <li class="list-group-item"><strong>Formateur :</strong> {{ $session->formateur->utilisateur->nom ?? '-' }}</li>
                <li class="list-group-item"><strong>Date début :</strong> {{ $session->date_debut ? date('d/m/Y', strtotime($session->date_debut)) : '-' }}</li>
                <li class="list-group-item"><strong>Date fin :</strong> {{ $session->date_fin ? date('d/m/Y', strtotime($session->date_fin)) : '-' }}</li>
                <li class="list-group-item"><strong>Places max :</strong> {{ $session->places_max ?? '-' }}</li>
            </ul>
            <h5>Inscriptions :</h5>
            <ul class="list-group">
                @forelse($session->inscriptions as $inscr)
                    <li class="list-group-item">
                        {{ $inscr->apprenant->utilisateur->nom ?? 'Inconnu' }}
                        <span class="badge bg-success ms-2">Inscrit</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Aucune inscription pour cette session.</li>
                @endforelse
            </ul>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('assistant.sessions') }}" class="btn btn-outline-secondary">Retour aux sessions</a>
        </div>
    </div>
</div>
@endsection
