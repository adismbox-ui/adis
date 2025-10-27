@extends('formateurs.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center mb-3">
        <div class="col-lg-10 col-md-12 text-end">
            <a href="{{ route('validation_cours_domicile.historique') }}" class="btn btn-outline-dark btn-sm">
                <i class="fas fa-history"></i> Voir l'historique de mes demandes
            </a>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <h4 class="mb-0"><i class="fas fa-home me-2"></i> Mes cours à domicile validés et assignés</h4>
                </div>
                <div class="card-body">
                    @if($demandes->isEmpty())
                        <div class="alert alert-info mb-0">Aucune demande validée et assignée par l'administrateur ne vous concerne actuellement.</div>
                    @else
                        <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Demandeur</th>
                                    <th>Module</th>
                                    <th>Enfants</th>
                                    <th>Adresse</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($demandes as $demande)
                                <tr>
                                    <td>{{ $demande->id }}</td>
                                    <td>{{ $demande->user->prenom ?? '' }} {{ $demande->user->nom ?? '' }}</td>
                                    <td>{{ $demande->module }}</td>
                                    <td>{{ $demande->nombre_enfants }}</td>
                                    <td>
                                        {{ $demande->ville }}, {{ $demande->commune }}, {{ $demande->quartier }}<br>
                                        N° {{ $demande->numero }}
                                    </td>
                                    <td>{{ $demande->created_at ? $demande->created_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalDemande{{ $demande->id }}">
                                            <i class="fas fa-eye"></i> Voir
                                        </button>
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
                                            <li class="list-group-item"><strong>Module :</strong> {{ $demande->module }}</li>
                                            <li class="list-group-item"><strong>Nombre d'enfants :</strong> {{ $demande->nombre_enfants }}</li>
                                            <li class="list-group-item"><strong>Ville :</strong> {{ $demande->ville }}</li>
                                            <li class="list-group-item"><strong>Commune :</strong> {{ $demande->commune }}</li>
                                            <li class="list-group-item"><strong>Quartier :</strong> {{ $demande->quartier }}</li>
                                            <li class="list-group-item"><strong>Téléphone :</strong> {{ $demande->numero }}</li>
                                            <li class="list-group-item"><strong>Date de demande :</strong> {{ $demande->created_at ? $demande->created_at->format('d/m/Y H:i') : '-' }}</li>
                                            <li class="list-group-item"><strong>Message :</strong> {{ $demande->message }}</li>
                                        </ul>
                                        <div class="text-center">
                                            @if($demande->statut === 'validee' || $demande->statut === 'en_attente_formateur')
                                                <form action="{{ route('validation_cours_domicile.accepter', $demande->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success me-2"><i class="fas fa-check"></i> Accepter</button>
                                                </form>
                                                <form action="{{ route('validation_cours_domicile.refuser', $demande->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> Refuser</button>
                                                </form>
                                            @elseif($demande->statut === 'acceptee_formateur')
                                                <span class="badge bg-success fs-5">Acceptée</span>
                                            @elseif($demande->statut === 'refusee_formateur')
                                                <span class="badge bg-danger fs-5">Refusée</span>
                                            @endif
                                        </div>
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
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .modal-header {
        background: #007bff;
        color: #fff;
    }
    .modal-footer .btn {
        min-width: 120px;
    }
</style>
@endpush
