@extends('assistants.layout')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-home me-2"></i>Créer un cours à domicile</h1>
    <form method="POST" action="{{ route('assistant.cours_domicile.store') }}">
        @csrf
        <div class="mb-3">
            <label for="titre" class="form-label">Titre *</label>
            <input type="text" class="form-control" id="titre" name="titre" required>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date *</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="mb-3">
            <label for="niveau_id" class="form-label">Niveau *</label>
            <select class="form-control" id="niveau_id" name="niveau_id" required>
                <option value="">Sélectionner un niveau</option>
                @foreach($niveaux as $niveau)
                    <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
</div>
@endsection 