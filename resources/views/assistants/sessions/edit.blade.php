@extends('assistants.layout')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-edit me-2"></i>Éditer la session</h1>
        <a href="{{ route('assistant.sessions') }}" class="btn btn-secondary">Retour</a>
    </div>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('assistant.sessions.update', $session) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom', $session->nom) }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $session->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="niveau_id" class="form-label">Niveau</label>
            <select class="form-select" id="niveau_id" name="niveau_id">
                <option value="">-- Choisir --</option>
                @foreach($niveaux as $niveau)
                    <option value="{{ $niveau->id }}" {{ old('niveau_id', $session->niveau_id) == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="date_debut" class="form-label">Date début</label>
            <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ old('date_debut', optional($session->date_debut)->format('Y-m-d')) }}">
        </div>
        <div class="mb-3">
            <label for="date_fin" class="form-label">Date fin</label>
            <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ old('date_fin', optional($session->date_fin)->format('Y-m-d')) }}">
        </div>
        <div class="mb-3">
            <label for="places_max" class="form-label">Places max</label>
            <input type="number" min="1" class="form-control" id="places_max" name="places_max" value="{{ old('places_max', $session->places_max) }}">
        </div>
        <button type="submit" class="btn btn-success">Enregistrer</button>
    </form>
</div>
@endsection
