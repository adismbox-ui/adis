@extends('assistants.layout')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold text-primary"><i class="fas fa-book me-2"></i>Liste des modules</h1>
    <a href="{{ route('assistant.modules.create') }}" class="btn btn-success shadow"><i class="fas fa-plus me-1"></i> Nouveau module</a>
</div>
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="table-responsive">
<table class="table table-hover align-middle rounded bg-white shadow-sm">
    <thead class="table-primary">
        <tr>
            <th>#</th>
            <th>Discipline</th>
            <th>Niveau</th>
            <th>Formateur</th>
            <th>Date début</th>
            <th>Date fin</th>
            <th>Prix (FCFA)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($modules as $module)
        <tr>
            <td class="fw-bold">{{ $module->id }}</td>
            <td>{{ $module->discipline }}</td>
            <td>
                @if($module->niveau)
                    <span class="badge bg-info text-dark">{{ $module->niveau->nom }}</span>
                @else
                    <span class="text-danger">Aucun</span>
                @endif
            </td>
            <td>
                @if($module->formateur && $module->formateur->utilisateur)
                    {{ $module->formateur->utilisateur->prenom }} {{ $module->formateur->utilisateur->nom }}
                @else
                    <span class="text-danger">Aucun formateur</span>
                @endif
            </td>
            <td>{{ $module->date_debut }}</td>
            <td>{{ $module->date_fin }}</td>
            <td><span class="badge bg-success">{{ $module->prix }}</span></td>
            <td>
                <a href="{{ route('assistant.modules.show', $module) }}" class="btn btn-sm btn-info me-1" title="Voir"><i class="fas fa-eye"></i></a>
                <a href="{{ route('assistant.modules.edit', $module) }}" class="btn btn-sm btn-warning me-1" title="Modifier"><i class="fas fa-edit"></i></a>
                <form action="{{ route('assistant.modules.destroy', $module) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Voulez-vous vraiment supprimer ce module ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer"><i class="fas fa-trash-alt"></i></button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endsection