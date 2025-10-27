@extends('assistants.layout')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-home me-2"></i>Cours à domicile</h1>
        <a href="{{ route('assistant.cours_domicile.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Nouveau cours à domicile</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($cours->isEmpty())
        <div class="alert alert-info">Aucune demande de cours à domicile trouvée.</div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Date</th>
                    <th>Demandeur</th>
                    <th>Module</th>
                    <th>Niveau</th>
                    <th>Ville</th>
                    <th>Commune</th>
                    <th>Quartier</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($cours as $demande)
                <tr>
                    <td>{{ $demande->created_at ? $demande->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $demande->user->prenom ?? '' }} {{ $demande->user->nom ?? '' }}</td>
                    <td>{{ $demande->module ?? '-' }}</td>
                    <td>{{ $demande->niveau ? $demande->niveau->nom : '-' }}</td>
                    <td>{{ $demande->ville ?? '-' }}</td>
                    <td>{{ $demande->commune ?? '-' }}</td>
                    <td>{{ $demande->quartier ?? '-' }}</td>
                    <td>
                        @if($demande->statut === 'validee')
                            <span class="badge bg-success">Validée</span>
                        @elseif($demande->statut === 'en_attente' || $demande->statut === null)
                            <span class="badge bg-warning">En attente</span>
                        @elseif($demande->statut === 'refusee')
                            <span class="badge bg-danger">Refusée</span>
                        @else
                            <span class="badge bg-secondary">{{ $demande->statut }}</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalDemande{{ $demande->id }}">
                            <i class="fas fa-eye"></i> Voir
                        </button>
                        @if($demande->statut === 'en_attente' || $demande->statut === null)
                        <form action="{{ route('assistant.cours_domicile.valider', $demande->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Valider cette demande ?');">
                                <i class="fas fa-check"></i> Valider
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                <!-- Modal -->
                <div class="modal fade" id="modalDemande{{ $demande->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $demande->id }}" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel{{ $demande->id }}">Détail de la demande #{{ $demande->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                      </div>
                      <div class="modal-body">
                        <ul class="list-group mb-3">
                            <li class="list-group-item"><strong>Demandeur :</strong> {{ $demande->user ? $demande->user->prenom . ' ' . $demande->user->nom : '-' }}</li>
                            <li class="list-group-item"><strong>Module :</strong> {{ $demande->module ?? '-' }}</li>
                            <li class="list-group-item"><strong>Niveau :</strong> {{ $demande->niveau ? $demande->niveau->nom : '-' }}</li>
                            <li class="list-group-item"><strong>Ville :</strong> {{ $demande->ville ?? '-' }}</li>
                            <li class="list-group-item"><strong>Commune :</strong> {{ $demande->commune ?? '-' }}</li>
                            <li class="list-group-item"><strong>Quartier :</strong> {{ $demande->quartier ?? '-' }}</li>
                            <li class="list-group-item"><strong>Téléphone :</strong> {{ $demande->numero ?? '-' }}</li>
                            <li class="list-group-item"><strong>Date de demande :</strong> {{ $demande->created_at ? $demande->created_at->format('d/m/Y H:i') : '-' }}</li>
                            <li class="list-group-item"><strong>Message :</strong> {{ $demande->message ?? '-' }}</li>
                        </ul>
                        @if($demande->statut === 'en_attente' || $demande->statut === null)
                        <div class="d-flex gap-2">
                            <form action="{{ route('assistant.cours_domicile.valider', $demande->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Valider</button>
                            </form>
                            <form action="{{ route('assistant.cours_domicile.refuser', $demande->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Refuser cette demande ?');"><i class="fas fa-times"></i> Refuser</button>
                            </form>
                        </div>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection 