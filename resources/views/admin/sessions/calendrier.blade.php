@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar"></i> Calendrier des Sessions
        </h1>
        <div>
            <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-list"></i> Liste
            </a>
            <a href="{{ route('admin.sessions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Session
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Calendrier des Sessions et Vacances</h6>
                </div>
                <div class="card-body">
                    @if($sessions->count() > 0 || $vacances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Type</th>
                                        <th>Nom</th>
                                        <th>Période</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sessions as $session)
                                        <tr class="table-primary">
                                            <td>
                                                <i class="fas fa-calendar-alt text-primary"></i>
                                                <strong>Session</strong>
                                            </td>
                                            <td>
                                                <strong>{{ $session->nom }}</strong>
                                                
                                            </td>
                                            <td>
                                                <div>{{ $session->date_debut->format('d/m/Y') }} - {{ $session->date_fin->format('d/m/Y') }}</div>
                                                @if($session->heure_debut && $session->heure_fin)
                                                    <small class="text-muted">{{ $session->heure_debut->format('H:i') }} - {{ $session->heure_fin->format('H:i') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($session->actif)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.sessions.edit', $session) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    @foreach($vacances as $vacance)
                                        <tr class="table-warning">
                                            <td>
                                                <i class="fas fa-umbrella-beach text-warning"></i>
                                                <strong>Vacances</strong>
                                            </td>
                                            <td>
                                                <strong>{{ $vacance->nom }}</strong>
                                            </td>
                                            <td>
                                                <div>{{ $vacance->date_debut->format('d/m/Y') }} - {{ $vacance->date_fin->format('d/m/Y') }}</div>
                                                @php
                                                    $duree = $vacance->date_debut->diffInDays($vacance->date_fin) + 1;
                                                @endphp
                                                <small class="text-muted">{{ $duree }} jour(s)</small>
                                            </td>
                                            <td>
                                                @if($vacance->actif)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.vacances.edit', $vacance) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune session ou période de vacances programmée.</p>
                            <div>
                                <a href="{{ route('admin.sessions.create') }}" class="btn btn-primary me-2">
                                    <i class="fas fa-plus"></i> Créer une session
                                </a>
                                <a href="{{ route('admin.vacances.create') }}" class="btn btn-warning">
                                    <i class="fas fa-umbrella-beach"></i> Créer des vacances
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Légende</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-primary" style="width: 20px; height: 20px; border-radius: 3px;"></div>
                        <span class="ms-2">Sessions de formation</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-warning" style="width: 20px; height: 20px; border-radius: 3px;"></div>
                        <span class="ms-2">Périodes de vacances</span>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">Actions rapides</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.sessions.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nouvelle Session
                        </a>
                        <a href="{{ route('admin.vacances.create') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-umbrella-beach"></i> Nouvelle Vacance
                        </a>
                        <a href="{{ route('admin.niveaux.index') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-layer-group"></i> Gérer les Niveaux
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 