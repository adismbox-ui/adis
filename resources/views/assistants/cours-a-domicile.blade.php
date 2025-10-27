@extends('assistants.layout')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-success"><i class="fas fa-home me-2"></i> Cours à domicile</h1>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <i class="fas fa-list me-2"></i>Liste des cours à domicile à valider
        </div>
        <div class="card-body">
            @if($cours->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>Apprenant</th>
                            <th>Module</th>
                            <th>Niveau</th>
                            <th>Formateur</th>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cours as $coursMaison)
                        <tr>
                            <td>{{ $coursMaison->apprenant ? $coursMaison->apprenant->nom_complet : 'Inconnu' }}</td>
                            <td>{{ $coursMaison->module ? $coursMaison->module->titre : '-' }}</td>
                            <td>{{ $coursMaison->module && $coursMaison->module->niveau ? $coursMaison->module->niveau->nom : '-' }}</td>
                            <td>{{ $coursMaison->formateur ? $coursMaison->formateur->utilisateur->prenom.' '.$coursMaison->formateur->utilisateur->nom : '-' }}</td>
                            <td>{{ $coursMaison->date ? \Carbon\Carbon::parse($coursMaison->date)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $coursMaison->heure ?? '-' }}</td>
                            <td>
                                @if($coursMaison->statut === 'valide')
                                    <span class="badge bg-success">Validé</span>
                                @elseif($coursMaison->statut === 'refuse')
                                    <span class="badge bg-danger">Refusé</span>
                                @else
                                    <span class="badge bg-warning text-dark">En attente</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('assistant.cours-a-domicile.show', $coursMaison->id) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                                <!-- Ajouter ici d'autres actions si besoin -->
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-home fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucun cours à domicile à afficher pour le moment.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
