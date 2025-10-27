@extends('admin.layout')
@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="fas fa-certificate fa-lg me-2"></i>
            <h3 class="mb-0">Détail du certificat</h3>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $certificat->id }}</dd>
                <dt class="col-sm-3">Apprenant</dt>
                <dd class="col-sm-9">{{ $certificat->apprenant->utilisateur->prenom ?? '-' }} {{ $certificat->apprenant->utilisateur->nom ?? '-' }}</dd>
                <dt class="col-sm-3">Module</dt>
                <dd class="col-sm-9">{{ $certificat->module->titre ?? '-' }}</dd>
                <dt class="col-sm-3">Titre</dt>
                <dd class="col-sm-9">{{ $certificat->titre ?? '-' }}</dd>
                <dt class="col-sm-3">Date d'obtention</dt>
                <dd class="col-sm-9">{{ $certificat->date_obtention ? \Carbon\Carbon::parse($certificat->date_obtention)->format('d/m/Y') : '-' }}</dd>
            </dl>
            <a href="{{ route('admin.certificats.edit', $certificat) }}" class="btn btn-primary me-2"><i class="fas fa-edit"></i> Modifier</a>
            <a href="{{ route('admin.certificats.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
        </div>
    </div>
</div>
@endsection 