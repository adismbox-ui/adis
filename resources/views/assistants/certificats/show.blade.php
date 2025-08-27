@extends('assistants.layout')

@section('content')
<div class="container py-4">
    <div class="content-card">
        <div class="card-header-custom">
            <h2 class="card-title">Détail du certificat</h2>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item"><strong>Titre :</strong> {{ $certificat->titre }}</li>
                <li class="list-group-item"><strong>Type :</strong> {{ $certificat->type ?? '-' }}</li>
                <li class="list-group-item"><strong>Apprenant :</strong> {{ $certificat->apprenant->utilisateur->nom ?? '-' }}</li>
                <li class="list-group-item"><strong>Date :</strong> {{ $certificat->created_at ? $certificat->created_at->format('d/m/Y') : '-' }}</li>
            </ul>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('assistant.certificats') }}" class="btn btn-outline-secondary">Retour aux certificats</a>
        </div>
    </div>
</div>
@endsection
