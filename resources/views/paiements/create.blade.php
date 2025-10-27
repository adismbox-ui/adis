<h1>Créer un paiement</h1>
<form method="POST" action="{{ route('paiements.store') }}">
    @csrf
    <!-- Ajoute ici les champs du formulaire -->
    <button type="submit">Enregistrer</button>
</form> 