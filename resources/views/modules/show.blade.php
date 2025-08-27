@extends('formateurs.layout')
@section('content')
<div class="container py-4">
    <h2 class="mb-4">Détail du module</h2>
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h4 class="card-title mb-3">{{ $module->titre }}</h4>
            <p><strong>Discipline :</strong> {{ $module->discipline }}</p>
            <p><strong>Niveau :</strong> 
                @if($module->niveau)
                    <span class="fw-bold">{{ $module->niveau->nom }}</span> <small class="text-muted">{{ $module->niveau->description }}</small>
                @else
                    <span class="text-danger">Non défini</span>
                @endif
            </p>
            <p><strong>Formateur :</strong> {{ optional($module->formateur->utilisateur)->prenom }} {{ optional($module->formateur->utilisateur)->nom }}</p>
            <p><strong>Date début :</strong> {{ $module->date_debut }}</p>
            <p><strong>Date fin :</strong> {{ $module->date_fin }}</p>
            <p><strong>Description :</strong> {{ $module->description }}</p>
        </div>
    </div>
    <a href="{{ route('modules.index') }}" class="btn btn-secondary">Retour à la liste</a>
</div>
@endsection