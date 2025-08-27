<h1>Modifier un paiement</h1>
<form method="POST" action="{{ route('paiements.update', $paiement) }}">
    @csrf
    @method('PUT')
    <!-- Ajoute ici les champs du formulaire -->
    <button type="submit">Mettre à jour</button>
</form> 