<h1>Créer un certificat</h1>
<form method="POST" action="{{ route('certificats.store') }}">
    @csrf
    <!-- Ajoute ici les champs du formulaire -->
    <button type="submit">Enregistrer</button>
</form> 