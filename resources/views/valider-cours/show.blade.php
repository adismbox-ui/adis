@extends('admin.layout')

@section('content')
<div class="container mt-5">
    <style>
        /* Rendre les informations des formateurs bien visibles (texte noir) */
        #formateursTable tbody td {
            color: #000 !important;
            background: #fff !important;
        }
        #formateursTable thead th {
            color: #000 !important;
            background: #f1f5f9 !important;
        }
    </style>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-info text-white text-center">
                    <h3 class="mb-0"><i class="fas fa-eye me-2"></i>Détail de la demande</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group mb-4" style="background:#000; color:#fff;">
                        <li class="list-group-item" style="background:#000; color:#fff;"><strong>Demandeur :</strong> {{ $demande->user ? $demande->user->prenom . ' ' . $demande->user->nom : '-' }}</li>
                        <li class="list-group-item" style="background:#000; color:#fff;"><strong>Module :</strong> {{ $demande->module }}</li>
                        <li class="list-group-item" style="background:#000; color:#fff;"><strong>Nombre d'enfants :</strong> {{ $demande->nombre_enfants }}</li>
                        <li class="list-group-item" style="background:#000; color:#fff;"><strong>Ville :</strong> {{ $demande->ville }}</li>
                        <li class="list-group-item" style="background:#000; color:#fff;"><strong>Commune :</strong> {{ $demande->commune }}</li>
                        <li class="list-group-item" style="background:#000; color:#fff;"><strong>Quartier :</strong> {{ $demande->quartier }}</li>
                        <li class="list-group-item" style="background:#000; color:#fff;"><strong>Téléphone :</strong> {{ $demande->numero }}</li>
                        <li class="list-group-item" style="background:#000; color:#fff;"><strong>Message :</strong> {{ $demande->message }}</li>
                        <li class="list-group-item" style="background:#000; color:#fff;">
                            <strong>Statut :</strong> 
                            @if($demande->statut == 'validee')
                                <span class="badge bg-success">Validée</span>
                            @elseif($demande->statut == 'refusee')
                                <span class="badge bg-danger">Refusée</span>
                            @elseif($demande->statut == 'acceptee_formateur')
                                <span class="badge bg-warning">Acceptée par formateur</span>
                            @else
                                <span class="badge bg-secondary">En attente</span>
                            @endif
                        </li>
                        @if($demande->formateur_id)
                            <li class="list-group-item">
                                <strong>Formateur assigné :</strong> 
                                @php
                                    $formateur = \App\Models\Formateur::with('utilisateur')->find($demande->formateur_id);
                                @endphp
                                @if($formateur)
                                    {{ $formateur->utilisateur->prenom }} {{ $formateur->utilisateur->nom }}
                                @else
                                    Formateur non trouvé
                                @endif
                            </li>
                        @endif
                    </ul>

                    @if($demande->statut == 'acceptee_formateur')
                        <!-- Demande acceptée par le formateur - Admin peut valider définitivement -->
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Cette demande a été acceptée par le formateur. Vous pouvez maintenant la valider définitivement.
                        </div>
                        <form action="{{ route('valider.cours.valider', $demande->id) }}" method="POST" class="mb-3">
                            @csrf
                            <input type="hidden" name="formateur_id" value="{{ $demande->formateur_id }}">
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-check"></i> Valider définitivement
                            </button>
                        </form>
                        <form action="{{ route('valider.cours.refuser', $demande->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-times"></i> Refuser la demande
                            </button>
                        </form>
                    @elseif($demande->statut == 'en_attente')
                        <!-- Demande en attente - Admin doit assigner un formateur -->
                        <form action="{{ route('valider.cours.valider', $demande->id) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Formateurs enseignant ce module</label>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm me-2" onclick="sortTable('ville')">Trier par ville</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm me-2" onclick="sortTable('commune')">Trier par commune</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="sortTable('quartier')">Trier par quartier</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle" id="formateursTable">
                                        <thead class="table-info">
                                            <tr>
                                                <th>Nom</th>
                                                <th>Ville</th>
                                                <th>Commune</th>
                                                <th>Quartier</th>
                                                <th>Module</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($formateurs as $formateur)
                                                <tr>
                                                    <td>{{ $formateur->utilisateur->prenom }} {{ $formateur->utilisateur->nom }}</td>
                                                    <td>{{ $formateur->ville ?? '-' }}</td>
                                                    <td>{{ $formateur->commune ?? '-' }}</td>
                                                    <td>{{ $formateur->quartier ?? '-' }}</td>
                                                    <td>
                                                        @php
                                                            $module = $formateur->modules->first();
                                                        @endphp
                                                        {{ $module ? $module->titre : '-' }}
                                                    </td>
                                                    <td>
                                                        <form action="{{ route('valider.cours.valider', $demande->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="formateur_id" value="{{ $formateur->id }}">
                                                            <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Assigner</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <script>
                            function sortTable(col) {
                                const colMap = { ville: 1, commune: 2, quartier: 3 };
                                const table = document.getElementById('formateursTable');
                                const tbody = table.tBodies[0];
                                const rows = Array.from(tbody.rows);
                                const idx = colMap[col];
                                rows.sort((a, b) => {
                                    return a.cells[idx].innerText.localeCompare(b.cells[idx].innerText, 'fr', { sensitivity: 'base' });
                                });
                                rows.forEach(row => tbody.appendChild(row));
                            }
                            </script>
                            
                        </form>
                        <form action="{{ route('valider.cours.refuser', $demande->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100"><i class="fas fa-times"></i> Refuser la demande</button>
                        </form>
                    @elseif($demande->statut == 'validee')
                        <div class="alert alert-success text-center">Cette demande a déjà été validée.</div>
                    @elseif($demande->statut == 'refusee')
                        <div class="alert alert-danger text-center">Cette demande a été refusée.</div>
                    @endif
                    <a href="{{ route('valider.cours.index') }}" class="btn btn-outline-secondary w-100 mt-3">Retour à la liste</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
