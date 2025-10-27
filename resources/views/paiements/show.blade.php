<h1>Détail paiement</h1>
<p>ID : {{ $paiement->id }}</p>
<a href="{{ route('paiements.edit', $paiement) }}">Modifier</a>
<form method="POST" action="{{ route('paiements.destroy', $paiement) }}">
    @csrf
    @method('DELETE')
    <button type="submit">Supprimer</button>
</form>
<a href="{{ route('paiements.index') }}">Retour à la liste</a> 