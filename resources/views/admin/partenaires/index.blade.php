@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Entreprises partenaires (Admin)</h1>
    <a href="{{ route('admin.partenaires.create') }}" class="btn btn-success">
        <i class="fas fa-plus-circle me-2"></i>Ajouter une entreprise
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Site web</th>
                </tr>
            </thead>
            <tbody>
                @forelse($partenaires as $p)
                    <tr>
                        <td>{{ $p->nom }}</td>
                        <td>{{ $p->email }}</td>
                        <td>{{ $p->telephone }}</td>
                        <td>{{ $p->site_web }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">Aucune entreprise enregistrée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
  </div>
@endsection

