@extends('apprenants.layout')
@section('content')
    <div class="container py-4">
        <div class="alert alert-success">
            <h2>La page personnalisée fonctionne !</h2>
            <p>Bienvenue {{ $user->prenom ?? $user->name ?? 'apprenant' }}.</p>
        </div>
    </div>
@endsection 