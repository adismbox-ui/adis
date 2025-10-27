@extends('admin.layout')

@section('content')
<div class="container mt-4">
    <h2>Détail du Formateur</h2>
    @if(isset($formateur))
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $formateur->nom }} {{ $formateur->prenom }}</h5>
                <p class="card-text"><strong>Email :</strong> {{ $formateur->email }}</p>
                <p class="card-text"><strong>Téléphone :</strong> {{ $formateur->telephone ?? '-' }}</p>
                <!-- Ajoutez d'autres champs si besoin -->
            </div>
        </div>
        <form method="POST" action="{{ route('admin.formateur.refuser', $formateur->id) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir refuser ce formateur ?');">
            @csrf
            <button type="submit" class="btn btn-danger">Refuser</button>
        </form>
    @else
        <div class="alert alert-danger">Aucun formateur trouvé.</div>
    @endif
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mt-3">Retour à l'accueil admin</a>
</div>
@endsection
