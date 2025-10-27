<h1>Debug : Documents généraux et formateurs</h1>
<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>Document ID</th>
            <th>Titre</th>
            <th>formateur_id</th>
            <th>utilisateur_id</th>
            <th>Nom du formateur</th>
            <th>Prénom du formateur</th>
        </tr>
    </thead>
    <tbody>
        @foreach($docs as $doc)
            <tr>
                <td>{{ $doc->document_id }}</td>
                <td>{{ $doc->titre }}</td>
                <td>{{ $doc->formateur_id ?? 'NULL' }}</td>
                <td>{{ $doc->utilisateur_id ?? 'NULL' }}</td>
                <td>{{ $doc->nom ?? 'Non renseigné' }}</td>
                <td>{{ $doc->prenom ?? 'Non renseigné' }}</td>
            </tr>
        @endforeach
    </tbody>
</table> 