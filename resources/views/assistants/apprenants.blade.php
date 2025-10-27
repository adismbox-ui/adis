@extends('assistants.layout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-user-graduate me-2"></i>Liste des apprenants</h1>
    @if($apprenants->isEmpty())
        <div class="alert alert-info">Aucun apprenant trouvé.</div>
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
            @foreach($apprenants as $apprenant)
                <tr>
                    <td>{{ $apprenant->utilisateur->prenom ?? '-' }}</td>
                    <td>{{ $apprenant->utilisateur->nom ?? '-' }}</td>
                    <td>{{ $apprenant->utilisateur->email ?? '-' }}</td>
                    <td>{{ $apprenant->utilisateur->telephone ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection 