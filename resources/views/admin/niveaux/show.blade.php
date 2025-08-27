@extends('admin.layout')

@section('content')
<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1 class="h3 mb-0 text-gray-800">
			<i class="fas fa-eye"></i> Détails du Niveau
		</h1>
		<a href="{{ route('admin.niveaux.index') }}" class="btn btn-secondary">
			<i class="fas fa-arrow-left"></i> Retour
		</a>
	</div>

	<div class="card shadow">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">{{ $niveau->nom }}</h6>
		</div>
		<div class="card-body">
			<div class="row mb-3">
				<div class="col-md-6">
					<label class="form-label">Nom</label>
					<p class="form-control-plaintext">{{ $niveau->nom }}</p>
				</div>
				<div class="col-md-6">
					<label class="form-label">Ordre</label>
					<p class="form-control-plaintext">{{ $niveau->ordre }}</p>
				</div>
			</div>
			<div class="mb-3">
				<label class="form-label">Description</label>
				<p class="form-control-plaintext">{{ $niveau->description ?? 'Aucune description' }}</p>
			</div>

			<div class="mb-3">
				<label class="form-label"><i class="fas fa-video me-1"></i> Lien Google Meet</label>
				<p class="form-control-plaintext">
					@if(!empty($niveau->lien_meet))
						<a href="{{ $niveau->lien_meet }}" target="_blank">{{ $niveau->lien_meet }}</a>
					@else
						<span class="text-muted">Aucun</span>
					@endif
				</p>
			</div>

			<div class="mb-3">
				<label class="form-label"><i class="fas fa-calendar-alt me-1"></i> Session</label>
				<p class="form-control-plaintext">
					@if($niveau->session_id && ($s = \App\Models\SessionFormation::find($niveau->session_id)))
						{{ $s->nom }} ({{ optional($s->date_debut)->format('d/m/Y') }} - {{ optional($s->date_fin)->format('d/m/Y') }})
					@else
						<span class="text-muted">Aucune</span>
					@endif
				</p>
			</div>

			<div class="d-flex gap-2">
				<a href="{{ route('admin.niveaux.edit', $niveau) }}" class="btn btn-primary">
					<i class="fas fa-edit"></i> Modifier
				</a>
				<form action="{{ route('admin.niveaux.destroy', $niveau) }}" method="POST" onsubmit="return confirm('Supprimer ce niveau ?');">
					@csrf
					@method('DELETE')
					<button type="submit" class="btn btn-danger">
						<i class="fas fa-trash"></i> Supprimer
					</button>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

