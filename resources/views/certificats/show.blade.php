<h1>Détail certificat</h1>
<p>ID : {{ $certificat->id }}</p>
<a href="{{ route('certificats.edit', $certificat) }}">Modifier</a>
<form method="POST" action="{{ route('certificats.destroy', $certificat) }}">
    @csrf
    @method('DELETE')
    <button type="submit">Supprimer</button>
</form>
<a href="{{ route('admin.certificats.index') }}">Retour à la liste</a> 