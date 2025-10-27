@extends('admin.layout')
@section('content')
<h1>Détail utilisateur</h1>
<div class="card p-4 mb-3">
    <p><strong>Nom :</strong> {{ $utilisateur->nom }}</p>
    <p><strong>Prénom :</strong> {{ $utilisateur->prenom }}</p>
    <p><strong>Email :</strong> {{ $utilisateur->email }}</p>
    <p><strong>Type de compte :</strong> {{ $utilisateur->type_compte }}</p>
    <p><strong>Catégorie :</strong> {{ $utilisateur->categorie }}</p>
    <p><strong>Sexe :</strong> {{ $utilisateur->sexe }}</p>
    <p><strong>Téléphone :</strong> {{ $utilisateur->telephone }}</p>
</div>
<a href="{{ route('utilisateurs.edit', $utilisateur) }}" class="btn btn-warning">Modifier</a>
<form method="POST" action="{{ route('utilisateurs.destroy', $utilisateur) }}" style="display:inline-block">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</button>
</form>
<a href="{{ route('utilisateurs.index') }}" class="btn btn-secondary">Retour à la liste</a>
@endsection 