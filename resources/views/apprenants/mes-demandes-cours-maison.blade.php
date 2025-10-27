@extends('apprenants.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient text-white text-center" style="background:linear-gradient(90deg,#43e97b,#38f9d7,#228B22);">
    <h2 class="mb-0" style="font-weight:900;letter-spacing:1.5px;text-shadow:0 2px 18px #0008,0 1px 0 #43e97b; color:#fff !important; font-size:2.4rem;">
        <i class="fas fa-home me-2"></i>Mes demandes de cours à domicile
    </h2>
</div>
<div class="card-body">
    <div class="p-4 mb-4 rounded-4 text-center" style="background: linear-gradient(90deg,#43e97b 0%,#228B22 100%); box-shadow:0 4px 24px #43e97b33;">
    <span style="font-size:1.35rem;font-weight:600;color:#fff !important;text-shadow:0 2px 10px #000a,0 1px 0 #43e97b;letter-spacing:0.5px;">
        Retrouvez ici toutes vos <span style="color:#fff !important;font-weight:700;text-decoration:underline dotted #fff;">demandes passées</span> et <span style="color:#fff !important;font-weight:700;text-decoration:underline wavy #fff;">en cours</span>.<br>
        <span style="color:#fff !important;font-weight:700;font-style:italic;">Suivez leur statut en temps réel&nbsp;!</span>
    </span>
</div>
                    <!-- Liste des demandes de cours à domicile -->
                    @if(isset($demandes) && count($demandes))
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Niveau</th>
                                        <th>Nombre d'enfants</th>
                                        <th>Ville</th>
                                        <th>Commune</th>
                                        <th>Quartier</th>
                                        <th>Téléphone</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($demandes as $demande)
                                        <tr>
                                            <td>{{ $demande->niveau->nom ?? '-' }}</td>
                                            <td>{{ $demande->nombre_enfants }}</td>
                                            <td>{{ $demande->ville }}</td>
                                            <td>{{ $demande->commune }}</td>
                                            <td>{{ $demande->quartier }}</td>
                                            <td>{{ $demande->numero }}</td>
                                            <td>{{ $demande->statut ?? 'en_attente' }}</td>
                                            <td>
                                                <form action="{{ route('demandes.cours.maison.destroy', $demande->id) }}" method="POST" onsubmit="return confirm('Supprimer cette demande ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center mt-4">
                            <i class="fas fa-info-circle me-1"></i> Vous n'avez pas encore fait de demande.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
