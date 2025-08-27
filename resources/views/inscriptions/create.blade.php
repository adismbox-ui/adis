<h1>Créer une inscription</h1>
<form method="POST" action="{{ route('inscriptions.store') }}">
    @csrf
    <!-- Ajoute ici les champs du formulaire -->
    <button type="submit">Enregistrer</button>
</form> 