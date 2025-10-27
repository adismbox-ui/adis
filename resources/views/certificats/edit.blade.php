<h1>Modifier un certificat</h1>
<form method="POST" action="{{ route('certificats.update', $certificat) }}">
    @csrf
    @method('PUT')
    <!-- Ajoute ici les champs du formulaire -->
    <button type="submit">Mettre à jour</button>
</form> 