@extends('formateurs.layout')

@section('content')
<div class="container py-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h2 class="mb-0">Présence - Formateur</h2>
		<a href="{{ route('formateurs.present.format') }}" class="btn btn-outline-secondary">
			<i class="fas fa-file-alt me-1"></i> Format vierge
		</a>
	</div>

	@if(session('success'))
		<div class="alert alert-success">{{ session('success') }}</div>
	@endif
	@if($errors->any())
		<div class="alert alert-danger">{{ $errors->first() }}</div>
	@endif

	<div class="card mb-4">
		<div class="card-body">
			<form method="POST" action="{{ route('formateurs.presence.open') }}">
				@csrf
				<div class="row g-2 mb-2">
					<div class="col-md-4">
						<input type="text" name="nom" class="form-control" placeholder="Nom de la séance (optionnel)">
					</div>
					<div class="col-md-4">
						<input type="text" name="nom_formateur" class="form-control" placeholder="Nom du formateur (si aucun lié)">
					</div>
					<div class="col-md-4">
						<input type="text" name="module" class="form-control" placeholder="Nom du module enseigné (optionnel)">
					</div>
				</div>
				<div class="row g-2">
					<div class="col-md-10">
						<input type="text" name="commentaire" class="form-control" placeholder="Commentaire (optionnel)">
					</div>
					<div class="col-md-2 d-grid">
						<button class="btn btn-primary" type="submit">Ouvrir présence</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="card">
		<div class="card-header">Historique récent</div>
		<div class="card-body p-0">
			<table class="table mb-0">
				<thead>
					<tr>
						<th>Nom</th>
						<th>Ouverte</th>
						<th>Présents</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				@forelse($requests as $req)
					<tr>
						<td>{{ $req->nom ?? ('Séance #' . $req->id) }}</td>
						<td>
							@if($req->is_open)
								<span class="badge bg-success">Ouverte</span>
							@else
								<span class="badge bg-secondary">Fermée</span>
							@endif
						</td>
						<td>{{ $req->marks()->count() }}</td>
						<td>
							@if($req->is_open)
							<form method="POST" action="{{ route('formateurs.presence.close', $req) }}">
								@csrf
								<button class="btn btn-sm btn-outline-danger">Fermer</button>
							</form>
							@endif
							<a href="{{ route('formateurs.presence.sheet', $req) }}" class="btn btn-sm btn-outline-secondary ms-1">
								Feuille
							</a>
						</td>
					</tr>
				@empty
					<tr><td colspan="4" class="text-center py-3">Aucune présence encore.</td></tr>
				@endforelse
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection

