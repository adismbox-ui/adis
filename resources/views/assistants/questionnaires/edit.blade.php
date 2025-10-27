@extends('assistants.layout')

@section('content')
<div class="container py-4">
    <div class="content-card">
        <div class="card-header-custom">
            <h2 class="card-title">Modifier le questionnaire</h2>
        </div>
        <form method="POST" action="#">
            @csrf
            <div class="mb-3">
                <label class="form-label">Titre</label>
                <input type="text" class="form-control" value="{{ $questionnaire->titre }}" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" rows="3" readonly>{{ $questionnaire->description ?? '-' }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Date de création</label>
                <input type="text" class="form-control" value="{{ $questionnaire->created_at ? $questionnaire->created_at->format('d/m/Y') : '-' }}" readonly>
            </div>
            <div class="text-end">
                <a href="{{ route('assistant.questionnaires') }}" class="btn btn-outline-secondary">Annuler</a>
                <button type="submit" class="btn btn-success" disabled>Enregistrer (démo)</button>
            </div>
        </form>
    </div>
</div>
@endsection
