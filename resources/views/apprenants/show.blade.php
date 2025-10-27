@extends('admin.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-user-graduate me-2"></i> Détail de l'apprenant</h4>
                    <div>
                        <a href="{{ route('apprenants.edit', $apprenant) }}" class="btn btn-light btn-sm me-2"><i class="fas fa-edit"></i> Modifier</a>
                        <form method="POST" action="{{ route('apprenants.destroy', $apprenant) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet apprenant ?');">
    @csrf
    @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Supprimer</button>
</form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-auto">
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; font-size: 2.5rem;">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h3 class="mb-0">{{ $apprenant->utilisateur->prenom ?? '' }} {{ $apprenant->utilisateur->nom ?? '' }}</h3>
                            <p class="mb-1 text-muted"><i class="fas fa-envelope me-1"></i> {{ $apprenant->utilisateur->email ?? 'Email inconnu' }}</p>
                            <span class="badge bg-info">ID #{{ $apprenant->id }}</span>
                            @if($apprenant->niveau)
                                <div class="mt-2"><span class="badge bg-info text-dark">Niveau : {{ is_object($apprenant->niveau) ? $apprenant->niveau->nom : $apprenant->niveau }}</span></div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6 mb-2">
                            <strong>Téléphone :</strong> {{ $apprenant->utilisateur->telephone ?? 'Non renseigné' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Sexe :</strong> {{ $apprenant->utilisateur->sexe ?? 'Non renseigné' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Catégorie :</strong> {{ $apprenant->utilisateur->categorie ?? 'Non renseigné' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Compte actif :</strong> {!! $apprenant->utilisateur->actif ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-danger">Non</span>' !!}
                        </div>
                        <div class="col-md-12 mb-2">
                            <strong>Mot de passe initial :</strong> <span class="badge bg-secondary">adis</span>
                            <small class="text-muted ms-2">(par défaut à la création, peut être changé ensuite)</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-graduation-cap me-2"></i> Historique des Formations
                </div>
                <div class="card-body">
                    @if($apprenant->inscriptions && $apprenant->inscriptions->count() > 0)
                        <ul class="list-group">
                            @foreach($apprenant->inscriptions as $inscription)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-book-open text-primary me-2"></i>
                                        <strong>{{ $inscription->module->nom ?? 'Module inconnu' }}</strong>
                                        <br>
                                        <small class="text-muted">Inscrit le {{ $inscription->created_at ? $inscription->created_at->format('d/m/Y') : 'N/A' }}</small>
                                    </div>
                                    <span class="badge bg-primary">{{ $inscription->module->categorie ?? '' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-info mb-0">Aucune formation suivie.</div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-certificate me-2"></i> Certificats Obtenus
                </div>
                <div class="card-body">
                    @if($apprenant->certificats && $apprenant->certificats->count() > 0)
                        <ul class="list-group">
                            @foreach($apprenant->certificats as $certificat)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-award text-success me-2"></i>
                                        <strong>{{ $certificat->module->nom ?? 'Module inconnu' }}</strong>
                                        <br>
                                        <small class="text-muted">Délivré le {{ $certificat->created_at ? $certificat->created_at->format('d/m/Y') : 'N/A' }}</small>
                                    </div>
                                    <span class="badge bg-success">Certificat</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-info mb-0">Aucun certificat obtenu.</div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-credit-card me-2"></i> Paiements
                </div>
                <div class="card-body">
                    @if($apprenant->paiements && $apprenant->paiements->count() > 0)
                        <ul class="list-group">
                            @foreach($apprenant->paiements as $paiement)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-euro-sign text-warning me-2"></i>
                                        <strong>{{ $paiement->module->nom ?? 'Module inconnu' }}</strong>
                                        <br>
                                        <small class="text-muted">Payé le {{ $paiement->created_at ? $paiement->created_at->format('d/m/Y') : 'N/A' }}</small>
                                    </div>
                                    <span class="badge bg-warning text-dark">{{ $paiement->montant ?? 'Montant inconnu' }} €</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-info mb-0">Aucun paiement enregistré.</div>
                    @endif
                </div>
            </div>

            <a href="{{ route('apprenants.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
        </div>
    </div>
</div>
@endsection 