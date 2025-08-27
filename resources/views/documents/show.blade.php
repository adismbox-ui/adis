@extends('admin.layout')

@section('content')
<style>
    body { background-color: #000 !important; }
    /* Zone de détails en fond noir */
    .doc-dark .card { background: #0b0b0b; border-color: #1f2937; }
    .doc-dark .card-body { background: transparent; color: #f1f5f9; }
    .doc-dark .list-group-item { background: transparent; color: #f1f5f9; border-color: #1f2937; }
    .doc-dark h1, .doc-dark h4, .doc-dark strong { color: #f1f5f9 !important; }
    .doc-dark .btn-outline-primary { color: #93c5fd; border-color: #3b82f6; }
    .doc-dark .btn-outline-primary:hover { background: #1e3a8a; color: #fff; }
    .doc-dark .btn-outline-secondary { color: #e5e7eb; border-color: #4b5563; }
</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold text-info"><i class="fas fa-eye me-2"></i>Détail du document</h1>
    <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Retour à la liste</a>
</div>
<div class="row justify-content-center doc-dark">
    <div class="col-lg-8">
        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <h4 class="card-title mb-3"><i class="fas fa-file-alt me-2"></i>{{ $document->titre }}</h4>
                @php
                    $now = \Carbon\Carbon::now();
                    $dateEnvoi = \Carbon\Carbon::parse($document->date_envoi);
                    $isLate = $dateEnvoi < $now && !$document->envoye;
                    $isWaiting = $dateEnvoi > $now && !$document->envoye;
                    $isSent = $document->envoye;
                @endphp
                
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item"><strong>Module :</strong> {{ $document->module->titre ?? 'Non renseigné' }}</li>
                    <li class="list-group-item"><strong>Formateur :</strong> {{ $document->formateur && $document->formateur->utilisateur ? $document->formateur->utilisateur->prenom . ' ' . $document->formateur->utilisateur->nom : 'Non renseigné' }}</li>
                    <li class="list-group-item"><strong>Semaine :</strong> Semaine {{ $document->semaine ?? '-' }}</li>
                    <li class="list-group-item"><strong>Niveau :</strong> {{ $document->niveau->nom ?? '-' }}</li>
                    <li class="list-group-item">
                        <strong>Date d'envoi :</strong> 
                        <span class="badge bg-primary">{{ $dateEnvoi->format('d/m/Y à H:i') }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Statut d'envoi :</strong> 
                        @if($isSent)
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>Envoyé
                            </span>
                        @elseif($isLate)
                            <span class="badge bg-danger">
                                <i class="fas fa-exclamation-triangle me-1"></i>En retard ({{ $dateEnvoi->diffInMinutes($now) }} min)
                            </span>
                        @elseif($isWaiting)
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-clock me-1"></i>En attente (dans {{ $dateEnvoi->diffInMinutes($now) }} min)
                            </span>
                        @else
                            <span class="badge bg-secondary">Non défini</span>
                        @endif
                    </li>
                    <li class="list-group-item"><strong>Fichier :</strong> 
                        @if($document->fichier)
                            <a href="{{ asset('storage/' . $document->fichier) }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-pdf"></i> Voir / Télécharger</a>
                        @else
                            <span class="text-muted">Aucun fichier</span>
                        @endif
                    </li>
                </ul>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.documents.edit', $document) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i>Éditer</a>
                    <form method="POST" action="{{ route('admin.documents.destroy', $document) }}" onsubmit="return confirm('Supprimer ce document ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 