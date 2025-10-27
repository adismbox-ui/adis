@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-hand-holding-heart me-2"></i>Détails du Don
                    </h4>
                    <div>
                        <a href="{{ route('admin.dons.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <a href="{{ route('admin.dons.edit', $don) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Modifier
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-user me-2"></i>Informations du Donateur
                            </h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Nom complet :</td>
                                    <td>{{ $don->nom_donateur }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Email :</td>
                                    <td>{{ $don->email_donateur }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Téléphone :</td>
                                    <td>{{ $don->telephone ?? 'Non renseigné' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-success mb-3">
                                <i class="fas fa-money-bill me-2"></i>Informations du Don
                            </h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Référence :</td>
                                    <td><span class="badge bg-secondary">{{ $don->numero_reference }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Montant :</td>
                                    <td><span class="fw-bold text-success fs-5">{{ number_format($don->montant, 0, ',', ' ') }} F CFA</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Type de don :</td>
                                    <td><span class="badge bg-primary">{{ $don->type_don_label }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Mode de paiement :</td>
                                    <td><span class="badge bg-info">{{ $don->mode_paiement_label }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Statut :</td>
                                    <td><span class="badge bg-{{ $don->statut_color }}">{{ $don->statut_label }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Date du don :</td>
                                    <td>{{ $don->date_don->format('d/m/Y à H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-info mb-3">
                                <i class="fas fa-project-diagram me-2"></i>Projet Associé
                            </h5>
                            @if($don->projet)
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $don->projet->intitule }}</h6>
                                        <p class="card-text">{{ Str::limit($don->projet->description, 150) }}</p>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">Montant total : {{ number_format($don->projet->montant_total, 0, ',', ' ') }} F CFA</small>
                                            <small class="text-muted">Collecté : {{ number_format($don->projet->montant_collecte, 0, ',', ' ') }} F CFA</small>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Ce don est destiné au fonds général (tous les projets)
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-warning mb-3">
                                <i class="fas fa-comment me-2"></i>Message du Donateur
                            </h5>
                            @if($don->message)
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <p class="card-text">{{ $don->message }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Aucun message laissé par le donateur
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($don->statut === 'en_attente')
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-danger mb-3">
                                    <i class="fas fa-cogs me-2"></i>Actions Administratives
                                </h5>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.dons.confirmer', $don) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Confirmer ce don ?')">
                                            <i class="fas fa-check me-2"></i>Confirmer le Don
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.dons.annuler', $don) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Annuler ce don ?')">
                                            <i class="fas fa-times me-2"></i>Annuler le Don
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($don->notes_admin)
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-secondary mb-3">
                                    <i class="fas fa-sticky-note me-2"></i>Notes Administratives
                                </h5>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <p class="card-text">{{ $don->notes_admin }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 