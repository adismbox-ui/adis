@extends('formateurs.layout')

@section('content')
<div class="container py-4">
    <a href="{{ url()->previous() }}" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left me-1"></i> Retour</a>
    <div class="card shadow-lg border-0">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Détail de l'apprenant</h4>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Nom :</strong> {{ $apprenant->utilisateur->nom ?? '-' }}</li>
                <li class="list-group-item"><strong>Prénom :</strong> {{ $apprenant->utilisateur->prenom ?? '-' }}</li>
                <li class="list-group-item"><strong>Email :</strong> {{ $apprenant->utilisateur->email ?? '-' }}</li>
                <li class="list-group-item"><strong>Téléphone :</strong> {{ $apprenant->utilisateur->telephone ?? '-' }}</li>
                <li class="list-group-item"><strong>Date de naissance :</strong> {{ $apprenant->date_naissance ?? '-' }}</li>
                <li class="list-group-item"><strong>Adresse :</strong> {{ $apprenant->adresse ?? '-' }}</li>
                <li class="list-group-item"><strong>Niveau :</strong> {{ $apprenant->niveau->nom ?? '-' }}</li>
                <!-- Ajoutez ici d'autres informations utiles sur l'apprenant si besoin -->
            </ul>
        </div>
    </div>
</div>
@endsection
