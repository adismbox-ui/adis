<h1>Détail inscription</h1>
<p>ID : {{ $inscription->id }}</p>
<a href="{{ route('inscriptions.edit', $inscription) }}">Modifier</a>
<form method="POST" action="{{ route('inscriptions.destroy', $inscription) }}">
    @csrf
    @method('DELETE')
    <button type="submit">Supprimer</button>
</form>
<a href="{{ route('inscriptions.index') }}">Retour à la liste</a> 