@extends('assistants.layout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-chalkboard-teacher me-2"></i>Liste des formateurs</h1>
    @if($formateurs->isEmpty())
        <div class="alert alert-info">Aucun formateur trouvé.</div>
    @else
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
            </tr>
        </thead>
        <tbody>
            @foreach($formateurs as $formateur)
                <tr>
                    <td>{{ $formateur->utilisateur->prenom ?? '-' }}</td>
                    <td>{{ $formateur->utilisateur->nom ?? '-' }}</td>
                    <td>{{ $formateur->utilisateur->email ?? '-' }}</td>
                    <td>{{ $formateur->utilisateur->telephone ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection 