@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-success text-white py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">Inscription</h5>
                            <small class="text-white-50">Créez votre compte en quelques étapes</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 p-md-5">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Veuillez corriger les erreurs ci-dessous.</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div id="step-indicator" class="mb-4 text-center d-none">
                        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge step-badge bg-success" id="badge-step-1">1</span>
                                <span class="fw-semibold">Informations générales</span>
                            </div>
                            <span class="text-muted">→</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge step-badge bg-secondary" id="badge-step-2">2</span>
                                <span class="fw-semibold" id="step2-label">Complément</span>
                            </div>
                            <span class="text-muted">→</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge step-badge bg-secondary" id="badge-step-3">3</span>
                                <span class="fw-semibold">Confirmation</span>
                            </div>
                        </div>
                    </div>
                    <form id="register-form" method="POST" action="{{ secure_url('/register') }}" enctype="multipart/form-data">
                        @csrf
                        <!-- ÉTAPE 1 : Informations générales -->
                        <div class="step" id="step-1">
                        <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="prenom" class="form-label">Prénom(s)</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" value="{{ old('prenom') }}" autocomplete="given-name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom') }}" autocomplete="family-name" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label for="sexe" class="form-label">Sexe</label>
                                    <select class="form-select" id="sexe" name="sexe" autocomplete="sex" required>
                                        <option value="">Choisir...</option>
                                        <option value="Homme" {{ old('sexe')==='Homme' ? 'selected' : '' }}>Homme</option>
                                        <option value="Femme" {{ old('sexe')==='Femme' ? 'selected' : '' }}>Femme</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label for="telephone" class="form-label">Téléphone (WhatsApp)</label>
                                    <input type="text" class="form-control" id="telephone" name="telephone" value="{{ old('telephone') }}" autocomplete="tel" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                                <div class="form-text">Nous enverrons un lien de vérification à cette adresse.</div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label for="ville" class="form-label">Ville</label>
                                    <input type="text" class="form-control" id="ville" name="ville" value="{{ old('ville') }}" autocomplete="address-level2" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="commune" class="form-label">Commune</label>
                                    <input type="text" class="form-control" id="commune" name="commune" value="{{ old('commune') }}" autocomplete="address-level3" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="quartier" class="form-label">Quartier</label>
                                    <input type="text" class="form-control" id="quartier" name="quartier" value="{{ old('quartier') }}" autocomplete="address-line2" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" id="password" name="password" autocomplete="new-password" required>
                                    <div class="form-text">Plus de 8 caractères</div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="password_confirmation" class="form-label">Confirmation du mot de passe</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="type_compte" class="form-label">Type de compte</label>
                                    <select class="form-select @error('type_compte') is-invalid @enderror" id="type_compte" name="type_compte" required>
                                        <option value="">Choisir...</option>
                                        @php($allowed = $allowedTypes ?? null)
                                        @if(!$allowed || in_array('admin', $allowed))
                                            @unless(isset($adminExists) && $adminExists)
                                                <option value="admin" {{ old('type_compte')==='admin' ? 'selected' : '' }}>Administrateur</option>
                                            @endunless
                                        @endif
                                        @if(!$allowed || in_array('assistant', $allowed))
                                            @unless(isset($assistantExists) && $assistantExists)
                                                <option value="assistant" {{ old('type_compte')==='assistant' ? 'selected' : '' }}>Assistant</option>
                                            @endunless
                                        @endif
                                        @if(!$allowed || in_array('formateur', $allowed))
                                            <option value="formateur" {{ old('type_compte')==='formateur' ? 'selected' : '' }}>Professeur / Formateur</option>
                                        @endif
                                        @if(!$allowed || in_array('apprenant', $allowed))
                                            <option value="apprenant" {{ old('type_compte')==='apprenant' ? 'selected' : '' }}>Apprenant</option>
                                        @endif
                                    </select>
                                    @error('type_compte')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="categorie" class="form-label">Catégorie</label>
                                    <select class="form-select" id="categorie" name="categorie" required>
                                        <option value="">Choisir...</option>
                                        <option value="Enfant" {{ old('categorie')==='Enfant' ? 'selected' : '' }}>Enfant</option>
                                        <option value="Etudiant" {{ old('categorie')==='Etudiant' ? 'selected' : '' }}>Etudiant</option>
                                        <option value="Professionnel" {{ old('categorie')==='Professionnel' ? 'selected' : '' }}>Professionnel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end" id="step1-next-group">
                                <button type="button" class="btn btn-success px-4" id="next-1">Suivant</button>
                            </div>
                            <div class="d-flex justify-content-end d-none" id="step1-submit-group">
                                <button type="submit" class="btn btn-success px-4">S'inscrire</button>
                            </div>
                        </div>
                        <!-- ÉTAPE 2 : Dynamique -->
                        <div class="step d-none" id="step-2">
                            <!-- Contenu dynamique selon le type de compte -->
                            <div id="step2-apprenant" class="d-none">
                                <h5 class="mb-3">Nous prenons vos besoins en formation</h5>
                                <div class="mb-3">
                                    <label class="form-label">Comment avez-vous connu ADIS ?</label>
                                    <select class="form-select" name="connaissance_adis_apprenant">
                                        <option value="">Choisir...</option>
                                        <option>Réseaux sociaux</option>
                                        <option>Publicité en ligne</option>
                                        <option>Bouche à oreille</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Avez-vous déjà participé aux formations ADIS en langue arabe ?</label>
                                    <select class="form-select" name="formation_adis_apprenant">
                                        <option value="">Choisir...</option>
                                        <option>Oui</option>
                                        <option>Non</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Avez-vous déjà participé à une formation en langue arabe avec un autre organisme ?</label>
                                    <select class="form-select" name="formation_autre_apprenant">
                                        <option value="">Choisir...</option>
                                        <option>Oui</option>
                                        <option>Non</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Votre niveau de lecture coranique</label>
                                    <select class="form-select" name="niveau_coranique_apprenant">
                                        <option value="">Choisir...</option>
                                        <option>Débutant</option>
                                        <option>Intermédiaire</option>
                                        <option>Avancé</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Votre niveau en langue arabe</label>
                                    <select class="form-select" name="niveau_arabe_apprenant">
                                        <option value="">Choisir...</option>
                                        <option>Débutant</option>
                                        <option>Intermédiaire</option>
                                        <option>Avancé</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Avez-vous déjà entendu parler des tomes de Médine ?</label>
                                    <select class="form-select" name="tomes_medine_apprenant">
                                        <option value="">Choisir...</option>
                                        <option>Oui</option>
                                        <option>Non</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Lequel des tomes de Médine avez-vous étudié ?</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="tomes_etudies_apprenant[]" value="Tome 1"> <label class="form-check-label">Tome 1</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="tomes_etudies_apprenant[]" value="Tome 2"> <label class="form-check-label">Tome 2</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="tomes_etudies_apprenant[]" value="Tome 3"> <label class="form-check-label">Tome 3</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="tomes_etudies_apprenant[]" value="Tome 4"> <label class="form-check-label">Tome 4</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="tomes_etudies_apprenant[]" value="Aucun"> <label class="form-check-label">Aucun</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Souhaiteriez-vous apprendre d'autres disciplines ?</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="disciplines_apprenant[]" value="Tajwîd"> <label class="form-check-label">Le Tajwîd</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="disciplines_apprenant[]" value="Tahfîz"> <label class="form-check-label">Le Tahfîz</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="disciplines_apprenant[]" value="Tafsîr"> <label class="form-check-label">Le Tafsîr</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="disciplines_apprenant[]" value="Hadith"> <label class="form-check-label">Le hadith</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="disciplines_apprenant[]" value="As-Sîra"> <label class="form-check-label">As-Sîra</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="disciplines_apprenant[]" value="Fiqh"> <label class="form-check-label">Le Fiqh</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="disciplines_apprenant[]" value="Anglais"> <label class="form-check-label">Anglais</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Quelles sont vos attentes à l'issue de ce parcours ?</label><br>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="attentes_apprenant[]" value="Lire parfaitement le coran"> <label class="form-check-label">Lire parfaitement le coran</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="attentes_apprenant[]" value="Lire en langue arabe sans les accents"> <label class="form-check-label">Lire en langue arabe sans les accents</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="attentes_apprenant[]" value="Parler l'arabe"> <label class="form-check-label">Parler l'arabe</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="attentes_apprenant[]" value="Avoir un bon niveau en sciences religieuses"> <label class="form-check-label">Avoir un bon niveau en sciences religieuses</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="attentes_apprenant[]" value="Parler l'anglais"> <label class="form-check-label">Parler l'anglais</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Souhaiteriez-vous avoir un formateur ADIS à domicile pour vos enfants ?</label>
                                    <select class="form-select" name="formateur_domicile_apprenant">
                                        <option value="">Choisir...</option>
                                        <option>Oui</option>
                                        <option>Non</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="niveau-apprenant-group" style="display:none;">
                                    <label class="form-label">Niveau souhaité</label>
                                    <select class="form-select" name="niveau_id" id="niveau_id_apprenant">
                                        <option value="">Choisir...</option>
                                        @if(isset($niveaux))
                                            @foreach($niveaux as $niveau)
                                                <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div id="step2-formateur" class="d-none">
                                <h5 class="mb-3">Vos compétences et expérience pédagogique</h5>
                                <div class="mb-3">
                                    <label class="form-label">Quelles disciplines pouvez-vous enseigner ?</label><br>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="disciplines_formateur[]" value="Langue arabe"> <label class="form-check-label">Langue arabe</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="disciplines_formateur[]" value="Lecture coranique"> <label class="form-check-label">Lecture coranique</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="disciplines_formateur[]" value="Fiqh"> <label class="form-check-label">Fiqh</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="disciplines_formateur[]" value="Anglais"> <label class="form-check-label">Anglais</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Quel est votre niveau en langue arabe ?</label>
                                    <select class="form-select" name="niveau_arabe_formateur">
                                        <option value="">Choisir...</option>
                                        <option>Natif</option>
                                        <option>Courant</option>
                                        <option>Avancé</option>
                                        <option>Intermédiaire</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Quel est votre niveau en langue française ?</label>
                                    <select class="form-select" name="niveau_francais_formateur">
                                        <option value="">Choisir...</option>
                                        <option>Natif</option>
                                        <option>Courant</option>
                                        <option>Avancé</option>
                                        <option>Intermédiaire</option>
                                        <option>Débutant</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Avez-vous déjà enseigné avec ADIS ?</label>
                                    <select class="form-select" name="deja_enseigne_adis_formateur">
                                        <option value="">Choisir...</option>
                                        <option>Oui</option>
                                        <option>Non</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Souhaitez-vous enseigner à domicile ?</label>
                                    <select class="form-select" name="enseignement_domicile_formateur">
                                        <option value="">Choisir...</option>
                                        <option>Oui</option>
                                        <option>Non</option>
                                        <option>Peut-être</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Dernier diplôme en sciences religieuses</label>
                                    <select class="form-select" name="diplome_religieux_formateur" id="diplome_religieux_formateur">
                                        <option value="">Choisir...</option>
                                        <option>CEPE</option>
                                        <option>BEPC</option>
                                        <option>BAC</option>
                                        <option>LICENCE</option>
                                        <option>MASTER</option>
                                        <option>DOCTORAT</option>
                                        <option>AUTRE</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="diplome_religieux_autre_group" style="display: none;">
                                    <label class="form-label">Précisez votre diplôme en sciences religieuses</label>
                                    <input type="text" class="form-control" name="diplome_religieux_autre_formateur" placeholder="Entrez votre diplôme">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Dernier diplôme en sciences générales</label>
                                    <select class="form-select" name="diplome_general_formateur" id="diplome_general_formateur">
                                        <option value="">Choisir...</option>
                                        <option>CEPE</option>
                                        <option>BEPC</option>
                                        <option>BAC</option>
                                        <option>LICENCE</option>
                                        <option>MASTER</option>
                                        <option>INGÉNIEUR</option>
                                        <option>DOCTORAT</option>
                                        <option>AUTRE</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="diplome_general_autre_group" style="display: none;">
                                    <label class="form-label">Précisez votre diplôme en sciences générales</label>
                                    <input type="text" class="form-control" name="diplome_general_autre_formateur" placeholder="Entrez votre diplôme">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" id="prev-2">Précédent</button>
                                <button type="button" class="btn btn-success px-4" id="next-2">Suivant</button>
                            </div>
                        </div>
                        <!-- ÉTAPE 3 : Confirmation -->
                        <div class="step d-none" id="step-3">
                            <div id="confirmation-apprenant" class="d-none">
                                <h5>Confirmez et activez la création de votre compte</h5>
                                <p>Nous vous remercions d'avoir fait confiance à ADIS. Un e-mail de confirmation avec un lien à cliquer vous a été envoyé à l'adresse électronique fournie pour valider et compléter votre inscription.<br><br>L'équipe ADIS vous remercie.</p>
                            </div>
                            <div id="confirmation-formateur" class="d-none">
                                <h5>Activation de votre compte formateur</h5>
                                <p>Nous vous remercions d'avoir fait confiance à ADIS. L'équipe ADIS analyse votre candidature. Un e-mail de confirmation avec un lien à cliquer vous sera envoyé dans un délai de 48H à l'adresse électronique fournie pour valider, compléter ou décliner votre offre.<br><br>L'équipe ADIS vous remercie.</p>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" id="prev-3">Précédent</button>
                                <button type="submit" class="btn btn-success px-4">Valider l'inscription</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
<style>
.step-badge {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 0.95rem;
}
</style>
@endpush
<script>
// Gestion des étapes
let currentStep = 1;
const steps = [
    document.getElementById('step-1'),
    document.getElementById('step-2'),
    document.getElementById('step-3')
];
const badges = [
    document.getElementById('badge-step-1'),
    document.getElementById('badge-step-2'),
    document.getElementById('badge-step-3')
];
const nextGroup = document.getElementById('step1-next-group');
const submitGroup = document.getElementById('step1-submit-group');
function showStep(step) {
    steps.forEach((el, i) => {
        el.classList.toggle('d-none', i !== step-1);
        badges[i].classList.remove('bg-success','bg-secondary');
        badges[i].classList.add(i === step-1 ? 'bg-success' : 'bg-secondary');
    });
}
function updateStep2Content() {
    const type = document.getElementById('type_compte').value;
    document.getElementById('step2-apprenant').classList.toggle('d-none', type !== 'apprenant');
    document.getElementById('step2-formateur').classList.toggle('d-none', type !== 'formateur');
    document.getElementById('step2-label').textContent = (type === 'apprenant') ? 'Besoins' : (type === 'formateur' ? 'Complément' : 'Complément');
    // Afficher le bouton Suivant ou S'inscrire selon le type
    if (type === 'apprenant' || type === 'formateur') {
        nextGroup.classList.remove('d-none');
        submitGroup.classList.add('d-none');
        document.getElementById('step-indicator').classList.remove('d-none');
    } else if (type === 'admin' || type === 'assistant') {
        nextGroup.classList.add('d-none');
        submitGroup.classList.remove('d-none');
        document.getElementById('step-indicator').classList.add('d-none');
    } else {
        nextGroup.classList.remove('d-none');
        submitGroup.classList.add('d-none');
        document.getElementById('step-indicator').classList.add('d-none');
    }
}
// Validation avant d'avancer
let typeError = null;
let validationError = null;
function removeValidationError() {
    if (validationError) { validationError.remove(); validationError = null; }
}
function showValidationError(msg) {
    removeValidationError();
    validationError = document.createElement('div');
    validationError.className = 'alert alert-danger mt-3';
    validationError.innerText = msg;
    validationError.setAttribute('role', 'alert');
    document.getElementById('step-1').appendChild(validationError);
    validationError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
function getStep1Fields() {
    return document.querySelectorAll('#step-1 input[required], #step-1 select[required]');
}
function validateStep1() {
    const fields = getStep1Fields();
    for (const f of fields) {
        if (!f.value || !f.value.trim()) {
            const label = (f.id && document.querySelector('label[for="' + f.id + '"]')) ? document.querySelector('label[for="' + f.id + '"]').innerText : f.name;
            showValidationError('Veuillez remplir le champ "' + (label || 'obligatoire') + '" avant de continuer.');
            f.focus();
            return false;
        }
    }
    if (document.getElementById('password').value.length < 8) {
        showValidationError('Le mot de passe doit contenir au moins 8 caractères.');
        document.getElementById('password').focus();
        return false;
    }
    if (document.getElementById('password').value !== document.getElementById('password_confirmation').value) {
        showValidationError('Les mots de passe ne correspondent pas.');
        document.getElementById('password_confirmation').focus();
        return false;
    }
    removeValidationError();
    return true;
}
document.getElementById('next-1').onclick = function() {
    removeValidationError();
    const type = document.getElementById('type_compte').value;
    if (type !== 'apprenant' && type !== 'formateur') {
        if (!typeError) {
            typeError = document.createElement('div');
            typeError.className = 'alert alert-danger mt-2';
            typeError.innerText = "Veuillez choisir 'Apprenant' ou 'Formateur' pour continuer.";
            document.getElementById('type_compte').parentNode.appendChild(typeError);
        }
        return;
    }
    if (typeError) { typeError.remove(); typeError = null; }
    if (!validateStep1()) return;
    updateStep2Content();
    showStep(2);
    currentStep = 2;
};
document.getElementById('prev-2').onclick = function() {
    removeValidationError();
    showStep(1);
    currentStep = 1;
};
function getStep2RequiredSelects() {
    const type = document.getElementById('type_compte').value;
    const container = type === 'apprenant' ? document.getElementById('step2-apprenant') : document.getElementById('step2-formateur');
    if (!container) return [];
    return container.querySelectorAll('select[name]:not([multiple])');
}
function validateStep2() {
    const selects = getStep2RequiredSelects();
    for (const s of selects) {
        if (!s.value || s.value.trim() === '' || (s.options[s.selectedIndex] && s.options[s.selectedIndex].text.trim() === 'Choisir...')) {
            const label = s.closest('.mb-3')?.querySelector('.form-label');
            const labelText = (label && label.innerText) ? label.innerText.replace(/\s*\*\s*$/, '').trim() : 'obligatoire';
            removeValidationError();
            validationError = document.createElement('div');
            validationError.className = 'alert alert-danger mt-3';
            validationError.innerText = 'Veuillez remplir le champ "' + labelText + '" avant de continuer.';
            validationError.setAttribute('role', 'alert');
            document.getElementById('step-2').appendChild(validationError);
            validationError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            s.focus();
            return false;
        }
    }
    var dr = document.getElementById('diplome_religieux_formateur');
    if (dr && dr.value === 'AUTRE') {
        var inp = document.querySelector('input[name="diplome_religieux_autre_formateur"]');
        if (inp && (!inp.value || !inp.value.trim())) {
            removeValidationError();
            validationError = document.createElement('div');
            validationError.className = 'alert alert-danger mt-3';
            validationError.innerText = 'Veuillez préciser votre diplôme en sciences religieuses.';
            document.getElementById('step-2').appendChild(validationError);
            inp.focus();
            return false;
        }
    }
    var dg = document.getElementById('diplome_general_formateur');
    if (dg && dg.value === 'AUTRE') {
        var inp2 = document.querySelector('input[name="diplome_general_autre_formateur"]');
        if (inp2 && (!inp2.value || !inp2.value.trim())) {
            removeValidationError();
            validationError = document.createElement('div');
            validationError.className = 'alert alert-danger mt-3';
            validationError.innerText = 'Veuillez préciser votre diplôme en sciences générales.';
            document.getElementById('step-2').appendChild(validationError);
            inp2.focus();
            return false;
        }
    }
    removeValidationError();
    return true;
}
document.getElementById('next-2').onclick = function() {
    removeValidationError();
    if (!validateStep2()) return;
    const type = document.getElementById('type_compte').value;
    showStep(3);
    currentStep = 3;
    document.getElementById('confirmation-apprenant').classList.toggle('d-none', type !== 'apprenant');
    document.getElementById('confirmation-formateur').classList.toggle('d-none', type !== 'formateur');
};
document.getElementById('prev-3').onclick = function() {
    removeValidationError();
    showStep(2);
    currentStep = 2;
};
document.getElementById('type_compte').onchange = function() {
    updateStep2Content();
    if (typeError) { typeError.remove(); typeError = null; }
};
// Ajout de la logique pour masquer l'option Enfant si Formateur est choisi
function updateCategorieOptions() {
    const typeCompte = document.getElementById('type_compte').value;
    const categorieSelect = document.getElementById('categorie');
    const enfantOption = Array.from(categorieSelect.options).find(opt => opt.value === 'Enfant');
    if (typeCompte === 'formateur') {
        enfantOption.style.display = 'none';
        // Si Enfant était sélectionné, on reset
        if (categorieSelect.value === 'Enfant') {
            categorieSelect.value = '';
        }
    } else {
        enfantOption.style.display = '';
    }
}
document.getElementById('type_compte').addEventListener('change', function() {
    updateCategorieOptions();
    updateStep2Content(); // garder la logique existante
});
// Initialisation
updateStep2Content();
showStep(1);
updateCategorieOptions(); // Initialiser au chargement

// Si retour avec erreurs, conserver les valeurs et afficher l'étape appropriée
@if ($errors->any())
    (function() {
        var hasTypeError = @json($errors->has('type_compte'));
        var oldType = @json(old('type_compte'));
        var typeSelect = document.getElementById('type_compte');
        if (typeSelect && oldType) typeSelect.value = oldType;
        updateCategorieOptions();
        updateStep2Content();
        // En cas d'erreur type_compte, afficher l'étape 1 pour corriger
        if (hasTypeError) {
            showStep(1);
        } else if (oldType) {
            showStep(2);
        }
    })();
@endif

    // Afficher la liste des niveaux si apprenant
    function toggleNiveauDropdown() {
        var typeCompte = document.getElementById('type_compte').value;
        var niveauGroup = document.getElementById('niveau-apprenant-group');
        if(typeCompte === 'apprenant') {
            niveauGroup.style.display = '';
        } else {
            niveauGroup.style.display = 'none';
        }
    }
    document.getElementById('type_compte').addEventListener('change', toggleNiveauDropdown);
    document.addEventListener('DOMContentLoaded', toggleNiveauDropdown);

    // Gestion des champs "AUTRE" pour les diplômes
    function toggleDiplomeAutreField(selectId, groupId) {
        const select = document.getElementById(selectId);
        const group = document.getElementById(groupId);
        if (select && group) {
            group.style.display = select.value === 'AUTRE' ? 'block' : 'none';
            if (select.value !== 'AUTRE') {
                const input = group.querySelector('input');
                if (input) input.value = '';
            }
        }
    }

    // Initialisation des écouteurs pour les diplômes
    document.addEventListener('DOMContentLoaded', function() {
        const diplomeReligieux = document.getElementById('diplome_religieux_formateur');
        const diplomeGeneral = document.getElementById('diplome_general_formateur');
        
        if (diplomeReligieux) {
            diplomeReligieux.addEventListener('change', function() {
                toggleDiplomeAutreField('diplome_religieux_formateur', 'diplome_religieux_autre_group');
            });
            // Initialiser l'état au chargement
            toggleDiplomeAutreField('diplome_religieux_formateur', 'diplome_religieux_autre_group');
        }
        
        if (diplomeGeneral) {
            diplomeGeneral.addEventListener('change', function() {
                toggleDiplomeAutreField('diplome_general_formateur', 'diplome_general_autre_group');
            });
            // Initialiser l'état au chargement
            toggleDiplomeAutreField('diplome_general_formateur', 'diplome_general_autre_group');
        }
    });
</script>
@endsection 