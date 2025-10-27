@extends('assistants.layout')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-umbrella-beach me-2"></i>Modifier la période de vacances</h1>
        <a href="{{ route('assistant.vacances') }}" class="btn btn-secondary">Retour</a>
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
    <form action="{{ route('assistant.vacances.update', $vacance) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom', $vacance->nom) }}" required>
        </div>
        <div class="mb-3">
            <label for="date_debut" class="form-label">Date début</label>
            <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ old('date_debut', optional($vacance->date_debut)->format('Y-m-d')) }}">
        </div>
        <div class="mb-3">
            <label for="date_fin" class="form-label">Date fin</label>
            <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ old('date_fin', optional($vacance->date_fin)->format('Y-m-d')) }}">
        </div>
        <div class="mb-3">
            <label for="actif" class="form-label">Statut</label>
            <select class="form-select" id="actif" name="actif">
                <option value="1" {{ old('actif', $vacance->actif) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('actif', $vacance->actif) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Enregistrer</button>
    </form>
</div>
@endsection
