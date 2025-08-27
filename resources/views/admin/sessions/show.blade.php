@extends('admin.layout')

@section('content')
<div class="container">
    <h1>Détail de la session</h1>
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">{{ $session->nom }}</h5>
            <p class="card-text"><strong>Description :</strong> {{ $session->description }}</p>
            
            <p class="card-text"><strong>Date début :</strong> {{ $session->date_debut }}</p>
            <p class="card-text"><strong>Date fin :</strong> {{ $session->date_fin }}</p>
            <p class="card-text"><strong>Durée séance (min) :</strong> {{ $session->duree_seance_minutes }}</p>
            <p class="card-text"><strong>Nombre de séances :</strong> {{ $session->nombre_seances }}</p>
            <p class="card-text"><strong>Places max :</strong> {{ $session->places_max }}</p>
            <p class="card-text"><strong>Active :</strong> {{ $session->actif ? 'Oui' : 'Non' }}</p>

            <hr>
            <div style="background-color: #000; padding: 20px; border-radius: 10px; margin: 20px 0;">
                <h6 class="mt-3" style="color: #fff; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Niveaux associés</h6>
                @php($niveaux = \App\Models\Niveau::where('session_id', $session->id)->orderBy('ordre')->get())
                @if($niveaux->count() > 0)
                    <ul class="list-group">
                        @foreach($niveaux as $n)
                            <li class="list-group-item d-flex justify-content-between align-items-center" style="background-color: #fff; border-radius: 8px; margin-bottom: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <span style="color: #000; font-weight: bold; background-color: #28a745; padding: 8px 12px; border-radius: 6px; display: inline-block;">{{ $n->nom }}</span>
                                <a href="{{ route('admin.niveaux.show', $n) }}" class="btn btn-sm btn-outline-primary" style="border: 2px solid #007bff; color: #007bff; background-color: #fff; border-radius: 6px;">Voir</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted" style="color: #ccc;">Aucun niveau associé.</p>
                @endif
            </div>
            <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary mt-3">Retour à la liste</a>
        </div>
    </div>
</div>
@endsection 