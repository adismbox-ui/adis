@extends('formateurs.layout')

@section('content')
<div class="container py-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h2 class="mb-0">Feuille de présence</h2>
		<button class="btn btn-outline-secondary" onclick="window.print()"><i class="fas fa-print me-1"></i> Imprimer</button>
	</div>

	<div class="card mb-3">
		<div class="card-body">
			<div class="row g-3">
				<div class="col-md-4">
					<div><strong>Séance:</strong> {{ $presence->nom ?? ('Séance #'.$presence->id) }}</div>
				</div>
				<div class="col-md-4">
					<div><strong>Statut:</strong> {{ $presence->is_open ? 'Ouverte' : 'Fermée' }}</div>
				</div>
				<div class="col-md-4">
					<div><strong>Générée le:</strong> {{ $generatedAt->format('d/m/Y H:i') }}</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card">
		<div class="card-body p-0">
			<table class="table table-bordered mb-0">
				<thead>
					<tr>
						<th style="width: 40%">Nom de l'apprenant</th>
						<th style="width: 30%">Signature</th>
						<th style="width: 30%">Heure</th>
					</tr>
				</thead>
				<tbody>
				@php($marks = $presence->marks->sortBy('present_at')->values())
				@foreach($marks as $mark)
					@php($apprenant = $mark->apprenant)
					@php($nom = $apprenant ? trim(($apprenant->nom ?? '').' '.($apprenant->prenom ?? '')) : '')
					@if($nom === '' && $apprenant && $apprenant->utilisateur)
						@php($nom = trim(($apprenant->utilisateur->nom ?? '').' '.($apprenant->utilisateur->prenom ?? '')))
					@endif
					@php($heure = $mark->present_at ? $mark->present_at->format('H:i') : '')
					<tr>
						<td>{{ $nom !== '' ? $nom : 'Nom indisponible' }}</td>
						<td></td>
						<td>{{ $heure }}</td>
					</tr>
				@endforeach
				@php($remaining = max(0, 20 - $marks->count()))
				@for($i=0; $i<$remaining; $i++)
					<tr>
						<td style="height: 40px;"></td>
						<td></td>
						<td></td>
					</tr>
				@endfor
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection