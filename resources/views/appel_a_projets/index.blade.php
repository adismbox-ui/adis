@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            @include('layouts.projets_menu')
        </div>
        <div class="col-md-9">
            <h1>Appels à projets</h1>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Intitulé</th>
                        <th>Domaine</th>
                        <th>Date limite</th>
                        <th>Etat</th>
                        <th>Détails</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($appel_a_projets as $appel)
                    <tr>
                        <td>{{ $appel->reference }}</td>
                        <td>{{ $appel->intitule }}</td>
                        <td>{{ $appel->domaine }}</td>
                        <td>{{ $appel->date_limite_soumission }}</td>
                        <td>{{ $appel->etat }}</td>
                        <td><a href="{{ route('appel-a-projets.show', $appel) }}" class="btn btn-info btn-sm">Voir</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection