@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            @include('layouts.projets_menu')
        </div>
        <div class="col-md-9">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <h1>Entreprises partenaires</h1>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Site web</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($partenaires as $partenaire)
                    <tr>
                        <td>{{ $partenaire->nom }}</td>
                        <td>{{ $partenaire->description }}</td>
                        <td>{{ $partenaire->email }}</td>
                        <td>{{ $partenaire->telephone }}</td>
                        <td>{{ $partenaire->site_web }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection