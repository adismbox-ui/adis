@extends('formateurs.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11 col-md-12">
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="fas fa-history me-2"></i> Historique de mes demandes à domicile</h4>
                    <a href="{{ route('validation_cours_domicile.index') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-arrow-left"></i> Retour aux demandes à traiter</a>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <button class="btn btn-outline-secondary btn-sm me-2 filter-btn" onclick="filterRows('all', this)">Toutes</button>
                        <button class="btn btn-outline-success btn-sm me-2 filter-btn" onclick="filterRows('acceptee_formateur', this)">Acceptées</button>
                        <button class="btn btn-outline-danger btn-sm me-2 filter-btn" onclick="filterRows('refusee_formateur', this)">Refusées</button>
                        <button class="btn btn-outline-warning btn-sm filter-btn" onclick="filterRows('en_attente', this)">En attente</button>
                    </div>
                    @if($demandes->isEmpty())
                        <div class="alert alert-info mb-0">Aucune demande trouvée dans votre historique.</div>
                    @else
                        <div class="table-responsive">
                        <table class="table table-hover align-middle" id="historique-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Demandeur</th>
                                    <th>Module</th>
                                    <th>Enfants</th>
                                    <th>Adresse</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Message</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($demandes as $demande)
                                <tr data-statut="{{ $demande->statut }}">
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
                                        @if($demande->statut === 'acceptee_formateur')
                                            <span class="badge bg-success">Acceptée</span>
                                        @elseif($demande->statut === 'refusee_formateur')
                                            <span class="badge bg-danger">Refusée</span>
                                        @elseif($demande->statut === 'validee' || $demande->statut === 'en_attente_formateur')
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $demande->statut }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $demande->message }}</td>
                                </tr>
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
<script>
function filterRows(statut, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const rows = document.querySelectorAll('#historique-table tbody tr');
    rows.forEach(row => {
        if (statut === 'all' || row.getAttribute('data-statut') === statut || (statut === 'en_attente' && (row.getAttribute('data-statut') === 'validee' || row.getAttribute('data-statut') === 'en_attente_formateur'))) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endsection 