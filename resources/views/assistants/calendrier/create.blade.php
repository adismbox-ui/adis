@extends('assistants.layout')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-calendar me-2"></i>Créer un événement</h1>
    <form method="POST" action="{{ route('assistant.calendrier.store') }}">
        @csrf
        <div class="mb-3">
            <label for="titre" class="form-label">Titre *</label>
            <input type="text" class="form-control" id="titre" name="titre" required>
        </div>
        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
</div>
@endsection 