@extends('admin.layout')
@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0 animate__animated animate__fadeIn">
        <div class="card-header bg-gradient bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
            <i class="fas fa-question-circle fa-lg me-2"></i>
                <h3 class="mb-0">Détails du questionnaire</h3>
            </div>
            <div>
                <a href="{{ route('questionnaires.index') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
                <a href="{{ route('questionnaires.edit', $questionnaire) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Informations générales -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="fw-bold text-primary mb-2">{{ $questionnaire->titre ?? 'Sans titre' }}</h2>
                    <p class="text-muted fs-5">{{ $questionnaire->description ?? 'Aucune description' }}</p>
                </div>
                <div class="col-md-4">
                    <div class="d-flex flex-column gap-2">
                        <span class="badge bg-gradient bg-primary text-white fs-6 p-2">
                            <i class="fas fa-book me-1"></i> Module : {{ $questionnaire->module->titre ?? 'Non assigné' }}
                        </span>
                        <span class="badge bg-gradient bg-info text-dark fs-6 p-2">
                            <i class="fas fa-layer-group me-1"></i> Niveau : {{ $questionnaire->module->niveau->nom ?? 'Non défini' }}
                        </span>
                        <span class="badge bg-gradient bg-success text-white fs-6 p-2">
                            <i class="fas fa-list me-1"></i> {{ $questionnaire->questions->count() }} question(s)
                        </span>
                        @if($questionnaire->minutes)
                        <span class="badge bg-gradient bg-warning text-dark fs-6 p-2">
                            <i class="fas fa-stopwatch me-1"></i> {{ $questionnaire->minutes }} minutes
                        </span>
                        @endif
                        @if($questionnaire->semaine)
                        <span class="badge bg-gradient bg-info text-white fs-6 p-2">
                            <i class="fas fa-calendar me-1"></i> Semaine {{ $questionnaire->semaine }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Liste des questions -->
                @if($questionnaire->questions->isEmpty())
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-info-circle fa-2x mb-3 text-info"></i>
                    <h5>Aucune question enregistrée pour ce questionnaire.</h5>
                    <p class="mb-0">Ajoutez des questions en modifiant ce questionnaire.</p>
                </div>
                @else
                <h4 class="fw-bold text-dark mb-4">
                    <i class="fas fa-list-ol me-2 text-primary"></i>Questions du questionnaire
                </h4>
                
                <div class="row">
                    @foreach($questionnaire->questions as $index => $question)
                        <div class="col-12 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 text-primary">
                                            <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                            {{ $question->texte }}
                                        </h5>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Bonne réponse disponible
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="text-muted mb-3">
                                                <i class="fas fa-list me-1"></i>Choix proposés :
                                            </h6>
                                            <div class="list-group list-group-flush">
                                                @foreach($question->choix as $choix)
                                                    <div class="list-group-item border-0 bg-transparent">
                                                        <i class="fas fa-circle text-muted me-2"></i>
                                            {{ $choix }}
                                                    </div>
                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-light border-0">
                                                <div class="card-body text-center">
                                                    <h6 class="text-success mb-2">
                                                        <i class="fas fa-star me-1"></i>Bonne réponse
                                                    </h6>
                                                    <div class="badge bg-success text-white fs-6 p-2">
                                                        {{ $question->bonne_reponse }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Statistiques -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card bg-gradient bg-light border-0">
                            <div class="card-body text-center">
                                <h5 class="text-primary mb-2">
                                    <i class="fas fa-chart-bar me-2"></i>Statistiques du questionnaire
                                </h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="border-end">
                                            <h3 class="text-primary mb-1">{{ $questionnaire->questions->count() }}</h3>
                                            <p class="text-muted mb-0">Questions</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="border-end">
                                            <h3 class="text-info mb-1">{{ $questionnaire->module->titre ? 'Oui' : 'Non' }}</h3>
                                            <p class="text-muted mb-0">Module assigné</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="border-end">
                                            <h3 class="text-success mb-1">{{ $questionnaire->module->niveau->nom ? 'Oui' : 'Non' }}</h3>
                                            <p class="text-muted mb-0">Niveau défini</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <h3 class="text-warning mb-1">{{ $questionnaire->minutes ?? 'Non' }}</h3>
                                        <p class="text-muted mb-0">Minutes configurées</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
            @endif
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s ease-in-out;
}
.card:hover {
    transform: translateY(-2px);
}
.badge {
    font-weight: 500;
}
.list-group-item {
    padding: 0.5rem 0;
}
</style>
@endsection 