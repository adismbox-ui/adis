@extends('admin.layout')
@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-warning text-dark d-flex align-items-center">
            <i class="fas fa-edit fa-lg me-2"></i>
            <h3 class="mb-0">Modifier le questionnaire</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('questionnaires.update', $questionnaire) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre du questionnaire</label>
                    <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre', $questionnaire->titre) }}" required>
                    @error('titre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $questionnaire->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div id="questions-container">
                    <!-- Questions dynamiques (à remplir par JS) -->
                </div>
                <button type="button" class="btn btn-outline-primary mb-3" onclick="addQuestion()"><i class="fas fa-plus"></i> Ajouter une question</button>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-warning btn-lg shadow"><i class="fas fa-save me-1"></i> Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
let questionIndex = 0;
function addQuestion(question = null) {
    const container = document.getElementById('questions-container');
    const q = document.createElement('div');
    q.className = 'card mb-3';
    let choixHtml = '';
    if (question && question.choix) {
        question.choix.forEach(function(choix) {
            choixHtml += `<div class=\"input-group mb-1\">`
                + `<input type=\"text\" class=\"form-control\" name=\"questions[${questionIndex}][choix][]\" value=\"${choix}\" placeholder=\"Choix\" required>`
                + `<button type=\"button\" class=\"btn btn-outline-danger\" onclick=\"this.parentNode.remove()\"><i class=\"fas fa-minus\"></i></button>`
                + `</div>`;
        });
    } else {
        choixHtml += `<div class=\"input-group mb-1\">`
            + `<input type=\"text\" class=\"form-control\" name=\"questions[${questionIndex}][choix][]\" placeholder=\"Choix\" required>`
            + `<button type=\"button\" class=\"btn btn-outline-danger\" onclick=\"this.parentNode.remove()\"><i class=\"fas fa-minus\"></i></button>`
            + `</div>`;
        choixHtml += `<div class=\"input-group mb-1\">`
            + `<input type=\"text\" class=\"form-control\" name=\"questions[${questionIndex}][choix][]\" placeholder=\"Choix\" required>`
            + `<button type=\"button\" class=\"btn btn-outline-danger\" onclick=\"this.parentNode.remove()\"><i class=\"fas fa-minus\"></i></button>`
            + `</div>`;
    }
    q.innerHTML = `
        <div class=\"card-body\">
            <div class=\"mb-2\">
                <label class=\"form-label\">Question</label>
                <input type=\"text\" class=\"form-control\" name=\"questions[${questionIndex}][texte]\" value=\"${question ? question.texte : ''}\" required>
            </div>
            <div class=\"mb-2 choix-container\">
                <label class=\"form-label\">Choix</label>
                <div class=\"choix-list\">${choixHtml}</div>
                <button type=\"button\" class=\"btn btn-outline-success btn-sm mt-2\" onclick=\"addChoix(this, ${questionIndex})\"><i class=\"fas fa-plus\"></i> Ajouter un choix</button>
            </div>
            <div class=\"mb-2\">
                <label class=\"form-label\">Bonne réponse</label>
                <input type=\"text\" class=\"form-control\" name=\"questions[${questionIndex}][bonne_reponse]\" value=\"${question ? question.bonne_reponse : ''}\" placeholder=\"Recopier la bonne réponse\" required>
            </div>
            <button type=\"button\" class=\"btn btn-danger btn-sm\" onclick=\"this.closest('.card').remove()\"><i class=\"fas fa-trash\"></i> Supprimer la question</button>
        </div>
    `;
    container.appendChild(q);
    questionIndex++;
}
function addChoix(btn, qIndex) {
    const choixList = btn.closest('.choix-container').querySelector('.choix-list');
    addChoixToList(choixList, qIndex);
}
function addChoixToList(choixList, qIndex) {
    const choixDiv = document.createElement('div');
    choixDiv.className = 'input-group mb-1';
    choixDiv.innerHTML = `
        <input type="text" class="form-control" name="questions[${qIndex}][choix][]" placeholder="Choix" required>
        <button type="button" class="btn btn-outline-danger" onclick="this.parentNode.remove()"><i class="fas fa-minus"></i></button>
    `;
    choixList.appendChild(choixDiv);
}
// Pré-remplissage si des questions existent (à faire côté backend plus tard)
</script>
@endsection 