<h1>Modifier une inscription</h1>
<form method="POST" action="{{ route('inscriptions.update', $inscription) }}">
    @csrf
    @method('PUT')
    <!-- Ajoute ici les champs du formulaire -->
    <button type="submit">Mettre à jour</button>
</form> 