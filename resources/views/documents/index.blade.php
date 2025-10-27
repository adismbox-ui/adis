@extends('admin.layout')

@section('content')
<style>
    .table tbody tr td {
        color: #22c55e !important;
        font-weight: 500;
        font-size: 14px;
        background-color: rgba(255, 255, 255, 0.9) !important;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }
    .table thead tr th {
        color: #15803d !important;
        font-weight: 600;
        background-color: rgba(255, 255, 255, 0.95) !important;
    }
    .table td, .table td * {
        color: #22c55e !important;
        background-color: rgba(255, 255, 255, 0.9) !important;
    }
    .table {
        background-color: rgba(255, 255, 255, 0.8) !important;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold text-primary"><i class="fas fa-file-alt me-2"></i>Liste des documents</h1>
    <a href="{{ route('admin.documents.create') }}" class="btn btn-success btn-lg shadow"><i class="fas fa-plus me-2"></i>Créer un document</a>
</div>
@if(session('auto_send_message'))
    <div class="alert alert-success">
        <i class="fas fa-bolt me-1"></i> {{ session('auto_send_message') }}
    </div>
@endif
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Titre</th>
                        <th>Module</th>
                        <th>Semaine</th>
                        <th>Niveau</th>
                        <th>Date d'envoi</th>
                        <th>Statut</th>
                        <th>Document</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $document)
                        @php
                            $now = \Carbon\Carbon::now();
                            $dateEnvoi = \Carbon\Carbon::parse($document->date_envoi);
                            $isLate = $dateEnvoi < $now && !$document->envoye;
                            $isWaiting = $dateEnvoi > $now && !$document->envoye;
                            $isSent = $document->envoye;
                        @endphp
                        <tr>
                            <td>{{ $document->id }}</td>
                            <td>
                                <strong>{{ $document->titre }}</strong>
                            </td>
                            <td>{{ $document->module->titre ?? 'Non renseigné' }}</td>
                            <td>
                                @if($document->semaine)
                                    <span class="badge bg-info text-dark">Semaine {{ $document->semaine }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $document->niveau->nom ?? '-' }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <small class="text-muted">{{ $dateEnvoi->format('d/m/Y') }}</small>
                                    <strong>{{ $dateEnvoi->format('H:i') }}</strong>
                                </div>
                            </td>
                            <td>
                                @if($isSent)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Envoyé
                                    </span>
                                @elseif($isLate)
                                    <span class="badge bg-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>En retard
                                    </span>
                                    <br><small class="text-muted">{{ $dateEnvoi->diffInMinutes($now) }} min</small>
                                @elseif($isWaiting)
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-clock me-1"></i>En attente
                                    </span>
                                    <br><small class="text-muted">{{ $dateEnvoi->diffInMinutes($now) }} min</small>
                                @else
                                    <span class="badge bg-secondary">Non défini</span>
                                @endif
                            </td>
                            <td>
                                @if($document->fichier)
                                    <a href="{{ asset('storage/' . $document->fichier) }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-pdf"></i> Voir</a>
                                @else
                                    <span class="text-muted">Aucun</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.documents.show', $document) }}" class="btn btn-info btn-sm me-1" title="Voir"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.documents.edit', $document) }}" class="btn btn-warning btn-sm me-1" title="Modifier"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.documents.destroy', $document) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce document ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Supprimer"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Aucun document trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 