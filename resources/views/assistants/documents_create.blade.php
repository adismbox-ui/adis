@extends('assistants.layout')

@section('content')
<style>
    /* Lisibilité des listes déroulantes (comme admin) */
    select.form-control, select.form-select { color: #000 !important; background-color: #fff !important; }
    select.form-control option, select.form-select option { color: #000 !important; background-color: #fff !important; }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-primary"><i class="fas fa-file-upload me-2"></i>Créer un document</h1>
        <a href="{{ route('assistant.documents') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Retour</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4">
                    <form id="form-document" method="POST" action="{{ route('assistant.documents.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="niveau_id" class="form-label">Niveau *</label>
                                <select class="form-select" id="niveau_id" name="niveau_id" required>
                                    <option value="">Choisir un niveau</option>
                                    @foreach($niveaux as $niveau)
                                        <option value="{{ $niveau->id }}">{{ $niveau->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="module_id" class="form-label">Module</label>
                                <select class="form-select" id="module_id" name="module_id">
                                    <option value="">Choisir un module (optionnel)</option>
                                    @foreach($modules as $module)
                                        <option value="{{ $module->id }}">{{ $module->titre }} @if($module->niveau) (Niveau : {{ $module->niveau->nom }}) @endif</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Laisser vide pour un document général du niveau.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="semaine" class="form-label">Semaine *</label>
                                <select class="form-select @error('semaine') is-invalid @enderror" id="semaine" name="semaine" required>
                                    <option value="">-- Choisir la semaine --</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('semaine') == $i ? 'selected' : '' }}>Semaine {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('semaine')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="titre" class="form-label">Titre du document *</label>
                                <input type="text" class="form-control" id="titre" name="titre" required>
                            </div>
                        </div>

                        <div class="card mb-4" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(76,175,80,0.3);">
                            <div class="card-header" style="background: linear-gradient(135deg, #2e7d32, #1b5e20); border: none; color: #fff; text-align: left;">
                                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Programmation automatique</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><i class="fas fa-calendar-alt me-1"></i>Session de formation *</label>
                                        <select class="form-select" id="session_id" name="session_id" required>
                                            <option value="">-- Choisir une session --</option>
                                            @foreach(($sessions ?? []) as $session)
                                                <option value="{{ $session->id }}" data-debut="{{ $session->date_debut }}" data-fin="{{ $session->date_fin }}">
                                                    {{ $session->nom }} ({{ \Carbon\Carbon::parse($session->date_debut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($session->date_fin)->format('d/m/Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">La session définit les dates pour la programmation.</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><i class="fas fa-calendar-check me-1"></i>Date et heure d'envoi</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                            <input type="datetime-local" class="form-control" id="date_envoi" name="date_envoi" required>
                                        </div>
                                        <small class="form-text text-muted">Définissez manuellement la date et l'heure d'envoi</small>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-success btn-lg" id="confirmProgrammationBtn" onclick="confirmProgrammation()">
                                        <i class="fas fa-check-circle me-2"></i>OK - Confirmer la date et l'heure d'envoi
                                    </button>
                                    <div id="programmationStatus" class="mt-2" style="display: none;">
                                        <div class="alert alert-success" id="programmationSuccess" style="display: none;">
                                            <i class="fas fa-check-circle me-2"></i>Date et heure confirmées avec succès !
                                        </div>
                                        <div class="alert alert-danger" id="programmationError" style="display: none;">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Veuillez sélectionner une date et une heure valides.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="fichier" class="form-label">Document à partager (PDF) *</nlabel>
                                <input type="file" class="form-control" id="fichier" name="fichier" accept="application/pdf" required>
                            </div>
                            <div class="col-md-6">
                                <label for="audio" class="form-label">Fichier audio (optionnel, mp3/wav/ogg)</label>
                                <input type="file" class="form-control" id="audio" name="audio" accept="audio/mp3,audio/wav,audio/ogg">
                                <small class="form-text text-muted">Vous pouvez ajouter un fichier audio en complément du PDF.</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('assistant.documents') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i> Annuler</a>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
function confirmProgrammation() {
    const sessionId = document.getElementById('session_id')?.value;
    const dateEnvoi = document.getElementById('date_envoi')?.value;
    const statusDiv = document.getElementById('programmationStatus');
    const successDiv = document.getElementById('programmationSuccess');
    const errorDiv = document.getElementById('programmationError');
    if (!statusDiv || !successDiv || !errorDiv) return;
    statusDiv.style.display = 'block';
    successDiv.style.display = 'none';
    errorDiv.style.display = 'none';
    if (!sessionId || !dateEnvoi) { errorDiv.style.display = 'block'; return; }
    const selectedDate = new Date(dateEnvoi);
    if (isNaN(selectedDate.getTime())) { errorDiv.style.display = 'block'; return; }
    successDiv.style.display = 'block';
    const btn = document.getElementById('confirmProgrammationBtn');
    if (btn) {
        btn.innerHTML = '<i class="fas fa-check me-2"></i>Date confirmée !';
        btn.className = 'btn btn-success btn-lg';
        btn.disabled = true;
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>OK - Confirmer la date et l\'heure d\'envoi';
            btn.className = 'btn btn-success btn-lg';
            btn.disabled = false;
        }, 3000);
    }
}
</script>