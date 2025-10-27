@extends('admin.layout')
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
                
                <dt class="col-sm-3">Prix</dt>
                <dd class="col-sm-9">{{ $module->prix }} FCFA</dd>
                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $module->description }}</dd>
                
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
                <a href="{{ route('admin.modules.edit', $module) }}" class="btn btn-warning me-2"><i class="fas fa-edit"></i> Modifier</a>
                <form action="{{ route('admin.modules.destroy', $module) }}" method="POST" onsubmit="return confirm('Supprimer ce module ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Supprimer</button>
                </form>
                <a href="{{ route('admin.modules') }}" class="btn btn-secondary ms-2">Retour à la liste</a>
            </div>
        </div>
    </div>
</div>
@endsection 