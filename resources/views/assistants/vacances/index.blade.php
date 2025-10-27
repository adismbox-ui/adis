@extends('assistants.layout')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-umbrella-beach"></i> Gestion des Vacances
        </h1>
        <a href="{{ route('assistant.vacances.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle Vacance
        </a>
    </div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Périodes de Vacances</h6>
        </div>
        <div class="card-body">
            @if($vacances->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Période</th>
                                <th>Durée</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vacances as $vacance)
                                <tr>
                                    <td>
                                        <strong>{{ $vacance->nom }}</strong>
                                    </td>
                                    <td>
                                        {{ Str::limit($vacance->description, 100) ?: 'Aucune description' }}
                                    </td>
                                    <td>
                                        <div>
                                            <strong>Du :</strong> {{ $vacance->date_debut ? \Carbon\Carbon::parse($vacance->date_debut)->format('d/m/Y') : '-' }}
                                        </div>
                                        <div>
                                            <strong>Au :</strong> {{ $vacance->date_fin ? \Carbon\Carbon::parse($vacance->date_fin)->format('d/m/Y') : '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $duree = $vacance->date_debut && $vacance->date_fin ? \Carbon\Carbon::parse($vacance->date_debut)->diffInDays(\Carbon\Carbon::parse($vacance->date_fin)) + 1 : '-';
                                        @endphp
                                        <span class="badge bg-info">{{ $duree }} jour(s)</span>
                                    </td>
                                    <td>
                                        @if($vacance->actif)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('assistant.vacances.edit', $vacance) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('assistant.vacances.destroy', $vacance) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette période de vacances ?')"
                                                  style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-umbrella-beach fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune période de vacances créée pour le moment.</p>
                    <a href="{{ route('assistant.vacances.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Créer la première période
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection