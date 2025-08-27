@extends('admin.layout')

@section('content')
<div class="animated-background"></div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-eye me-2"></i>Détails de la Candidature
                    </h3>
                    <a href="{{ route('admin.candidatures.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Informations de l'entreprise</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Raison sociale</th>
                                    <td>{{ $candidature->raison_sociale }}</td>
                                </tr>
                                <tr>
                                    <th>Responsable</th>
                                    <td>{{ $candidature->nom_responsable }}</td>
                                </tr>
                                <tr>
                                    <th>Statut juridique</th>
                                    <td>{{ $candidature->statut_juridique }}</td>
                                </tr>
                                <tr>
                                    <th>RCCM</th>
                                    <td>{{ $candidature->rccm }}</td>
                                </tr>
                                <tr>
                                    <th>Contact</th>
                                    <td>{{ $candidature->contact }}</td>
                                </tr>
                                @if($candidature->site_web)
                                <tr>
                                    <th>Site web</th>
                                    <td><a href="{{ $candidature->site_web }}" target="_blank">{{ $candidature->site_web }}</a></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="mb-3">Informations de la candidature</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Appel à projet</th>
                                    <td>{{ $candidature->reference_appel }}</td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        <span class="badge {{ $candidature->statut_badge_class }}">
                                            {{ $candidature->statut_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Date de soumission</th>
                                    <td>{{ $candidature->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @if($candidature->updated_at != $candidature->created_at)
                                <tr>
                                    <th>Dernière modification</th>
                                    <td>{{ $candidature->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                                @if($candidature->notes_admin)
                                <tr>
                                    <th>Notes admin</th>
                                    <td>{{ $candidature->notes_admin }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3">Documents soumis</h5>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                            <h6>Offre technique</h6>
                                            <a href="{{ route('admin.candidatures.download', ['candidature' => $candidature, 'fileType' => 'offre_technique']) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-download me-1"></i>Télécharger
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                            <h6>Offre financière</h6>
                                            <a href="{{ route('admin.candidatures.download', ['candidature' => $candidature, 'fileType' => 'offre_financiere']) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-download me-1"></i>Télécharger
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                            <h6>Justificatif paiement</h6>
                                            <a href="{{ route('admin.candidatures.download', ['candidature' => $candidature, 'fileType' => 'justificatif_paiement']) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-download me-1"></i>Télécharger
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($candidature->references_path)
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                            <h6>Références</h6>
                                            <a href="{{ route('admin.candidatures.download', ['candidature' => $candidature, 'fileType' => 'references']) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-download me-1"></i>Télécharger
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($candidature->statut === 'en_attente')
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3">Actions</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#accepterModal">
                                    <i class="fas fa-check me-2"></i>Accepter
                                </button>
                                <button type="button" class="btn btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#refuserModal">
                                    <i class="fas fa-times me-2"></i>Refuser
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($candidature->statut === 'en_attente')
<!-- Modal pour accepter -->
<div class="modal fade" id="accepterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Accepter la candidature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.candidatures.update', $candidature) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p><strong>Entreprise :</strong> {{ $candidature->raison_sociale }}</p>
                    <p><strong>Appel à projet :</strong> {{ $candidature->reference_appel }}</p>
                    <div class="mb-3">
                        <label class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control" name="notes_admin" rows="3">{{ $candidature->notes_admin }}</textarea>
                    </div>
                    <input type="hidden" name="statut" value="acceptee">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Accepter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour refuser -->
<div class="modal fade" id="refuserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Refuser la candidature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.candidatures.update', $candidature) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p><strong>Entreprise :</strong> {{ $candidature->raison_sociale }}</p>
                    <p><strong>Appel à projet :</strong> {{ $candidature->reference_appel }}</p>
                    <div class="mb-3">
                        <label class="form-label">Motif du refus *</label>
                        <textarea class="form-control" name="notes_admin" rows="3" required>{{ $candidature->notes_admin }}</textarea>
                    </div>
                    <input type="hidden" name="statut" value="refusee">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Refuser</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection 