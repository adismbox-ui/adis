@extends('assistants.layout')

@section('content')
<div class="container py-4">
    <div class="content-card">
        <div class="card-header-custom">
            <h2 class="card-title">Détail du questionnaire</h2>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item"><strong>Titre :</strong> {{ $questionnaire->titre }}</li>
                <li class="list-group-item"><strong>Description :</strong> {{ $questionnaire->description ?? '-' }}</li>
                <li class="list-group-item"><strong>Date de création :</strong> {{ $questionnaire->created_at ? $questionnaire->created_at->format('d/m/Y') : '-' }}</li>
            </ul>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('assistant.questionnaires') }}" class="btn btn-outline-secondary">Retour aux questionnaires</a>
        </div>
    </div>
</div>
@endsection
