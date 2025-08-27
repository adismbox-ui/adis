@extends('apprenants.layout')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center"><i class="fas fa-shopping-cart me-2"></i>Modules à acheter pour accéder à votre niveau</h2>

    @if(isset($modules) && count($modules))
        <div class="row justify-content-center">
            <div class="col-md-10">
                <table class="table table-bordered table-hover bg-white">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Discipline</th>
                            <th>Formateur</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Prix (FCFA)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $module)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $module->discipline }}</td>
                            <td>{{ optional($module->formateur->utilisateur)->prenom }} {{ optional($module->formateur->utilisateur)->nom }}</td>
                            <td>{{ $module->date_debut }}</td>
                            <td>{{ $module->date_fin }}</td>
                            <td>{{ $module->prix }}</td>
                            <td>
                                @if($module->is_paye)
                                    <span class="badge bg-success">Payé</span>
                                @else
                                    <form method="POST" action="{{ route('apprenants.payer-module', $module->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-credit-card me-1"></i>Payer
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Vous devez payer <b>tous les modules</b> de votre niveau pour débloquer les questionnaires et les cours associés.
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning text-center">
            Aucun module à acheter pour votre niveau actuellement.
        </div>
    @endif
</div>
@endsection
