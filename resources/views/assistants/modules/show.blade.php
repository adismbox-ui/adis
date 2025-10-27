@extends('assistants.layout')
@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="fas fa-book fa-lg me-2"></i>
            <h3 class="mb-0">Détail du module</h3>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Titre</dt>
                <dd class="col-sm-9">{{ $module->titre }}</dd>
                <dt class="col-sm-3">Discipline</dt>
                <dd class="col-sm-9">{{ $module->discipline }}</dd>
                <dt class="col-sm-3">Formateur</dt>
                <dd class="col-sm-9">
                    @if($module->formateur && $module->formateur->utilisateur)
                        {{ $module->formateur->utilisateur->prenom }} {{ $module->formateur->utilisateur->nom }}
                    @else
                        <span class="text-danger">Aucun formateur</span>
                    @endif
                </dd>
                <dt class="col-sm-3">Date début</dt>
                <dd class="col-sm-9">{{ $module->date_debut }}</dd>
                <dt class="col-sm-3">Date fin</dt>
                <dd class="col-sm-9">{{ $module->date_fin }}</dd>
                <dt class="col-sm-3">Prix</dt>
                <dd class="col-sm-9">{{ $module->prix }} FCFA</dd>
                <dt class="col-sm-3">Certificat</dt>
                <dd class="col-sm-9">{!! $module->certificat ? '<span class="badge bg-primary">Oui</span>' : '<span class="badge bg-secondary">Non</span>' !!}</dd>
                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $module->description }}</dd>
                <dt class="col-sm-3">Lien Google Meet</dt>
                <dd class="col-sm-9">@if($module->lien)<a href="{{ $module->lien }}" target="_blank">{{ $module->lien }}</a>@else <span class="text-muted">Aucun</span>@endif</dd>
                <dt class="col-sm-3">Support PDF</dt>
                <dd class="col-sm-9">
                    @if($module->support)
                        <a href="{{ asset('storage/'.$module->support) }}" target="_blank" class="btn btn-outline-info btn-sm">Voir le support</a>
                    @else
                        <span class="text-muted">Aucun</span>
                    @endif
                </dd>
            </dl>
            @if($module->audio)
                <div class="mb-3">
                    <label class="form-label">Audio du cours :</label>
                    <audio controls style="width:100%">
                        <source src="{{ asset('storage/'.$module->audio) }}" type="audio/mpeg">
                        Votre navigateur ne supporte pas la lecture audio.
                    </audio>
                    <a href="{{ asset('storage/'.$module->audio) }}" target="_blank" class="btn btn-outline-info btn-sm mt-2">Télécharger l'audio</a>
                </div>
            @endif
            <div class="d-flex justify-content-end">
                <a href="{{ route('assistant.modules') }}" class="btn btn-secondary ms-2">Retour à la liste</a>
            </div>
        </div>
    </div>
</div>
@endsection
