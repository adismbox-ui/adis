@extends('assistants.layout')

@section('content')
<div class="container py-4">
    <div class="content-card">
        <div class="card-header-custom">
            <h2 class="card-title">Détail du document</h2>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item"><strong>Titre :</strong> {{ $document->titre }}</li>
                <li class="list-group-item"><strong>Auteur :</strong> {{ $document->auteur->nom ?? '-' }}</li>
                <li class="list-group-item"><strong>Date :</strong> {{ $document->created_at ? $document->created_at->format('d/m/Y') : '-' }}</li>
                <li class="list-group-item"><strong>Description :</strong> {{ $document->description ?? '-' }}</li>
            </ul>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('assistant.documents') }}" class="btn btn-outline-secondary">Retour aux documents</a>
        </div>
    </div>
</div>
@endsection
