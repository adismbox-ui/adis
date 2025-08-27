@extends('admin.layout')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold"><i class="fas fa-user-tie me-2"></i>Détail du formateur</h1>
    <a href="{{ route('formateurs.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-body" style="background:#000; color:#fff; border-radius:8px;">
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <i class="fas fa-user-tie fa-5x mb-3" style="color:#a7c957;"></i>
                <h4 class="fw-bold">Formateur #{{ $formateur->id }}</h4>
                <p class="text-muted" style="color:#ccc !important;">Utilisateur ID: {{ $formateur->utilisateur_id }}</p>
            </div>
            <div class="col-md-8">
                <ul class="list-group list-group-flush" style="background:transparent;">
                    <li class="list-group-item" style="background:transparent; color:#fff; border-color:rgba(255,255,255,0.1)"><strong>Prénom:</strong> {{ $formateur->utilisateur->prenom ?? '-' }}</li>
                    <li class="list-group-item" style="background:transparent; color:#fff; border-color:rgba(255,255,255,0.1)"><strong>Nom:</strong> {{ $formateur->utilisateur->nom ?? '-' }}</li>
                    <li class="list-group-item" style="background:transparent; color:#fff; border-color:rgba(255,255,255,0.1)"><strong>Email:</strong> {{ $formateur->utilisateur->email ?? '-' }}</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white border-0 d-flex justify-content-end gap-2">
        <a href="{{ route('formateurs.edit', $formateur) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Modifier</a>
        <form method="POST" action="{{ route('formateurs.destroy', $formateur) }}" onsubmit="return confirm('Supprimer ce formateur ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Supprimer</button>
        </form>
    </div>
</div>
@endsection 