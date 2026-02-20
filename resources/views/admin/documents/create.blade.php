@extends('admin.layout')
@section('content')
<style>
	* { margin: 0; padding: 0; box-sizing: border-box; }
	body { font-family: 'Poppins', sans-serif; }

	.main-container { max-width: 1200px; margin-left: 280px; padding: 2rem; position: relative; z-index: 10; width: calc(100% - 280px); }
	.form-card { background: rgba(15, 35, 25, 0.95); backdrop-filter: blur(25px); border-radius: 0; box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(76, 175, 80, 0.2), inset 0 1px 0 rgba(76, 175, 80, 0.1); border: 2px solid rgba(76, 175, 80, 0.3); transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1); position: relative; overflow: hidden; }
	.form-card:hover { transform: translateY(-8px); box-shadow: 0 40px 100px rgba(0, 0, 0, 0.5), 0 0 0 2px rgba(76, 175, 80, 0.4), inset 0 1px 0 rgba(76, 175, 80, 0.2); }

	.card-header { background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 25%, #388e3c 50%, #4caf50 75%, #66bb6a 100%); color: white; padding: 1rem 2rem 0.8rem; border-radius: 0; position: relative; overflow: hidden; text-align: center; }
	.card-header::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent); animation: headerShine 6s ease-in-out infinite; }
	@keyframes headerShine { 0%, 100% { left: -100%; } 50% { left: 100%; } }
	.card-header h1 { font-size: 1.6rem; font-weight: 900; text-shadow: 3px 3px 8px rgba(0, 0, 0, 0.5); margin-bottom: 0.3rem; letter-spacing: 2px; background: linear-gradient(45deg, #ffffff, #e8f5e8, #ffffff); background-size: 200% 200%; -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: titleShimmer 3s ease-in-out infinite; }
	.card-header .subtitle { font-size: 0.8rem; font-weight: 400; opacity: 0.95; color: #e8f5e8; }

	.card-body { padding: 1rem; position: relative; }
	.section-title { font-size: 1.05rem; font-weight: 800; color: #66bb6a; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.6rem; padding: 0.6rem; background: linear-gradient(135deg, rgba(76, 175, 80, 0.15), rgba(27, 94, 32, 0.2)); border-radius: 8px; border-left: 6px solid #4caf50; text-shadow: 0 2px 8px rgba(0,0,0,0.5); border: 1px solid rgba(76, 175, 80, 0.2); }
	.form-label { font-weight: 700; color: #81c784; margin-bottom: 0.3rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; text-shadow: 0 2px 6px rgba(0,0,0,0.3); }
	.form-control, .form-select { border: 2px solid rgba(76, 175, 80, 0.4); border-radius: 8px; padding: 0.6rem 1rem; font-size: 0.9rem; font-weight: 600; color: #81c784; background: linear-gradient(135deg, rgba(15, 35, 25, 0.9), rgba(27, 94, 32, 0.3)); backdrop-filter: blur(15px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(76, 175, 80, 0.2); }
	.form-control::placeholder { color: #66bb6a; font-weight: 500; opacity: 0.8; }
	.form-control:focus, .form-select:focus { outline: none; border-color: #4caf50; box-shadow: 0 0 0 8px rgba(76, 175, 80, 0.25), 0 12px 35px rgba(76, 175, 80, 0.3), inset 0 1px 0 rgba(76, 175, 80, 0.3); transform: translateY(-3px) scale(1.02); background: linear-gradient(135deg, rgba(27, 94, 32, 0.4), rgba(76, 175, 80, 0.1)); color: #a5d6a7; }
</style>
<style>
    /* Lisibilité des listes déroulantes */
    select.form-control, select.form-select { color: #000 !important; background-color: #fff !important; }
    select.form-control option, select.form-select option { color: #000 !important; background-color: #fff !important; }
    .form-select { color: #000 !important; background-color: #fff !important; }
    .form-select option { color: #000 !important; background-color: #fff !important; }
</style>

<div class="main-container">
	<div class="form-card">
		<div class="card-header">
			<i class="fas fa-file-upload"></i>
			<h1>Créer un document</h1>
			<p class="subtitle">Interface harmonisée avec la page formateur</p>
		</div>
		<div class="card-body">
			<form id="form-document" method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">
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
								<option value="{{ $module->id }}">
									{{ $module->titre }} @if($module->niveau) (Niveau : {{ $module->niveau->nom }}) @endif
								</option>
							@endforeach
						</select>
						<small class="form-text text-muted">Laisser vide pour un document général du niveau.</small>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-md-12">
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
									@foreach($sessions ?? [] as $session)
										<option value="{{ $session->id }}" data-debut="{{ $session->date_debut }}" data-fin="{{ $session->date_fin }}">
											{{ $session->nom }} ({{ \Carbon\Carbon::parse($session->date_debut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($session->date_fin)->format('d/m/Y') }})
										</option>
									@endforeach
								</select>
								<small class="form-text text-muted">La session définit les dates de début et fin pour calculer les dimanches</small>
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
						<div class="alert alert-info" style="background: linear-gradient(135deg, rgba(76,175,80,0.15), rgba(27,94,32,0.2)); color:#e2f7e4; border: 1px solid rgba(76,175,80,0.35);">
							<h6><i class="fas fa-info-circle me-2"></i>Programmation d'envoi :</h6>
							<ul class="mb-0">
								<li>Envoi automatique à la date et l'heure spécifiées</li>
								<li>Notification email aux apprenants</li>
								<li>Vérification des contenus planifiés chaque heure</li>
							</ul>
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
						<label for="fichier" class="form-label">Document à partager (PDF) *</label>
						<input type="file" class="form-control" id="fichier" name="fichier" accept="application/pdf" required>
					</div>
					<div class="col-md-6">
						<label for="audio" class="form-label">Fichier audio (optionnel, mp3/wav/ogg)</label>
						<input type="file" class="form-control" id="audio" name="audio" accept="audio/mp3,audio/wav,audio/ogg">
						<small class="form-text text-muted">Vous pouvez ajouter un fichier audio en complément du PDF.</small>
					</div>
				</div>

				<div class="alert alert-info" style="background: linear-gradient(135deg, rgba(76,175,80,0.15), rgba(27,94,32,0.2)); color:#e2f7e4; border: 1px solid rgba(76,175,80,0.35);">
					<h6><i class="fas fa-info-circle"></i> Information :</h6>
					<p class="mb-0">Le document sera partagé avec tous les apprenants du niveau et du module sélectionnés. Vous pouvez ajouter un audio en complément du PDF.</p>
				</div>

				<div class="d-flex justify-content-end gap-2 mt-4">
					<a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary">
						<i class="fas fa-times"></i> Annuler
					</a>
					<button type="submit" class="btn btn-success">
						<i class="fas fa-save"></i> Enregistrer
					</button>
				</div>

			</form>
		</div>
	</div>
</div>

<script>
function confirmProgrammation() {
	const sessionId = document.getElementById('session_id').value;
	const dateEnvoi = document.getElementById('date_envoi').value;
	const statusDiv = document.getElementById('programmationStatus');
	const successDiv = document.getElementById('programmationSuccess');
	const errorDiv = document.getElementById('programmationError');
	statusDiv.style.display = 'block';
	successDiv.style.display = 'none';
	errorDiv.style.display = 'none';
	if (!sessionId || !dateEnvoi) { errorDiv.style.display = 'block'; return; }
	const selectedDate = new Date(dateEnvoi);
	if (isNaN(selectedDate.getTime())) { errorDiv.style.display = 'block'; return; }
	successDiv.style.display = 'block';
	const confirmBtn = document.getElementById('confirmProgrammationBtn');
	confirmBtn.innerHTML = '<i class="fas fa-check me-2"></i>Date confirmée !';
	confirmBtn.className = 'btn btn-success btn-lg';
	confirmBtn.disabled = true;
	setTimeout(() => {
		confirmBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>OK - Confirmer la date et l\'heure d\'envoi';
		confirmBtn.className = 'btn btn-success btn-lg';
		confirmBtn.disabled = false;
	}, 3000);
}
</script>

@endsection 