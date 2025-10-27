@extends('assistants.layout')
@section('content')
<style>
/* Listes déroulantes en fond noir pour cette page */
select.form-control,
select.form-select {
    background-color: #000 !important;
    color: #fff !important;
    border-color: #333 !important;
}
select.form-control:focus,
select.form-select:focus {
    background-color: #000 !important;
    color: #fff !important;
    box-shadow: 0 0 0 0.2rem rgba(0,0,0,0.25) !important;
}
select.form-control option,
select.form-select option {
    background-color: #000 !important;
    color: #fff !important;
}
</style>
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-layer-group me-2"></i>Créer un niveau</h1>
    <form method="POST" action="{{ route('assistant.niveaux.store') }}">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom du niveau *</label>
                    <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required placeholder="Ex: Débutant, Intermédiaire, Avancé">
                    @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="ordre" class="form-label">Ordre d'affichage *</label>
                    <input type="number" class="form-control @error('ordre') is-invalid @enderror" id="ordre" name="ordre" value="{{ old('ordre', 0) }}" min="0" required placeholder="0, 1, 2, 3...">
                    @error('ordre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Plus le nombre est petit, plus le niveau apparaîtra en premier</small>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Décrivez brièvement ce niveau...">{{ old('description') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="semaine" class="form-label">Semaine</label>
            <select class="form-control" id="semaine" name="semaine">
                <option value="">-- Choisir la semaine --</option>
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ old('semaine') == $i ? 'selected' : '' }}>Semaine {{ $i }}</option>
                @endfor
            </select>
            <small class="text-muted">Optionnel: utilisé pour suggérer une date d'envoi avec la session</small>
        </div>

        <div class="mb-3">
            <label for="formateur_id" class="form-label">Formateur responsable (optionnel)</label>
            <select class="form-control @error('formateur_id') is-invalid @enderror" id="formateur_id" name="formateur_id">
                <option value="">-- Aucun --</option>
                @isset($formateurs)
                    @foreach($formateurs as $formateur)
                        <option value="{{ $formateur->id }}" {{ old('formateur_id') == $formateur->id ? 'selected' : '' }}>
                            {{ $formateur->utilisateur->prenom ?? '' }} {{ $formateur->utilisateur->nom ?? '' }}
                        </option>
                    @endforeach
                @endisset
            </select>
            @error('formateur_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">Si défini, ce formateur sera lié au niveau et pourra être utilisé comme responsable.</small>
        </div>

        <div class="mb-3">
            <label for="session_id" class="form-label">Session (optionnel)</label>
            <select class="form-control @error('session_id') is-invalid @enderror" id="session_id" name="session_id">
                <option value="">-- Aucune --</option>
                @isset($sessions)
                    @foreach($sessions as $s)
                        <option value="{{ $s->id }}">{{ $s->nom }} ({{ optional($s->date_debut)->format('d/m/Y') }} - {{ optional($s->date_fin)->format('d/m/Y') }})</option>
                    @endforeach
                @endisset
            </select>
            @error('session_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-clock me-2"></i>Programmation automatique</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-calendar-check me-1"></i>Date et heure d'envoi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="datetime-local" class="form-control" id="date_envoi" name="date_envoi">
                        </div>
                        <small class="form-text text-muted">Sélectionnez la session et la semaine pour suggérer une date (dimanche 13h).</small>
                    </div>
                </div>
                <div class="text-center">
                    <button type="button" class="btn btn-success" id="confirmProgrammationBtn" onclick="confirmProgrammation()">
                        <i class="fas fa-check-circle me-2"></i>OK - Confirmer la date et l'heure d'envoi
                    </button>
                    <div id="programmationStatus" class="mt-2" style="display:none;">
                        <div class="alert alert-success" id="programmationSuccess" style="display:none;"><i class="fas fa-check-circle me-2"></i>Date et heure confirmées avec succès !</div>
                        <div class="alert alert-danger" id="programmationError" style="display:none;"><i class="fas fa-exclamation-triangle me-2"></i>Veuillez sélectionner une date et une heure valides.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1" {{ old('actif', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="actif">Niveau actif</label>
            </div>
            <small class="text-muted">Un niveau inactif ne sera pas visible lors de la création de sessions</small>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-video me-2"></i>Lien Google Meet (optionnel)</h6>
            </div>
            <div class="card-body">
                <div class="row g-2 align-items-center">
                    <div class="col-md-8">
                        <label for="lien_meet" class="form-label">Lien Google Meet</label>
                        <input type="text" id="lien_meet" name="lien_meet" class="form-control" placeholder="Aucun" readonly>
                    </div>
                    <div class="col-md-4 d-flex gap-2 mt-3 mt-md-0">
                        <button type="button" class="btn btn-primary w-50" onclick="genererMeetNiveau()"><i class="fas fa-random me-1"></i> Générer</button>
                        <button type="button" class="btn btn-secondary w-50" onclick="copierMeetNiveau()"><i class="fas fa-copy me-1"></i> Copier</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('assistant.niveaux') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Créer le niveau</button>
        </div>
    </form>
</div>

<script>
function genererMeetNiveau() {
    const chars = 'abcdefghijklmnopqrstuvwxyz';
    const rand = (n) => Array.from({length:n}, () => chars[Math.floor(Math.random()*chars.length)]).join('');
    const lien = `https://meet.google.com/${rand(3)}-${rand(4)}-${rand(3)}`;
    const input = document.getElementById('lien_meet');
    if (input) input.value = lien;
}
function copierMeetNiveau() {
    const input = document.getElementById('lien_meet');
    if (!input || !input.value) return;
    navigator.clipboard.writeText(input.value);
}
</script>

<script>
// Suggestion automatique de date d'envoi basée sur la session et la semaine (dimanche 13:00)
function getNextSunday(date) {
    const d = new Date(date);
    const day = d.getDay(); // 0 = dimanche
    const diff = (7 - day) % 7; // jours jusqu'au prochain dimanche
    d.setDate(d.getDate() + diff);
    return d;
}

function suggestEnvoiDate() {
    const sessionSelect = document.getElementById('session_id');
    const semaineSelect = document.getElementById('semaine');
    const input = document.getElementById('date_envoi');
    if (!sessionSelect || !semaineSelect || !input) return;
    const sessionOption = sessionSelect.options[sessionSelect.selectedIndex];
    const semaine = parseInt(semaineSelect.value);
    const dateDebut = sessionOption && sessionOption.textContent.includes('/') ? sessionOption.getAttribute('data-debut') : null;
    // Si pas d'attributs data, on tente via liste sessions non enrichie
    // Laisser vide si non disponible
    if (sessionSelect.value && semaine) {
        // fallback: utiliser aujourd'hui comme base si date_debut inconnue
        const base = new Date();
        const firstSunday = getNextSunday(base);
        const dateEnvoi = new Date(firstSunday);
        dateEnvoi.setDate(firstSunday.getDate() + ((semaine - 1) * 7));
        dateEnvoi.setHours(13, 0, 0, 0);
        const year = dateEnvoi.getFullYear();
        const month = String(dateEnvoi.getMonth() + 1).padStart(2, '0');
        const day = String(dateEnvoi.getDate()).padStart(2, '0');
        const hours = String(dateEnvoi.getHours()).padStart(2, '0');
        const minutes = String(dateEnvoi.getMinutes()).padStart(2, '0');
        input.value = `${year}-${month}-${day}T${hours}:${minutes}`;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const sessionSelect = document.getElementById('session_id');
    const semaineSelect = document.getElementById('semaine');
    if (sessionSelect) sessionSelect.addEventListener('change', suggestEnvoiDate);
    if (semaineSelect) semaineSelect.addEventListener('change', suggestEnvoiDate);
});

function confirmProgrammation() {
    const dateEnvoi = document.getElementById('date_envoi')?.value;
    const statusDiv = document.getElementById('programmationStatus');
    const successDiv = document.getElementById('programmationSuccess');
    const errorDiv = document.getElementById('programmationError');
    if (!statusDiv || !successDiv || !errorDiv) return;
    statusDiv.style.display = 'block';
    successDiv.style.display = 'none';
    errorDiv.style.display = 'none';
    if (!dateEnvoi) { errorDiv.style.display = 'block'; return; }
    const selectedDate = new Date(dateEnvoi);
    if (isNaN(selectedDate.getTime())) { errorDiv.style.display = 'block'; return; }
    successDiv.style.display = 'block';
}
</script>
@endsection 