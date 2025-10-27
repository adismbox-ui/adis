@extends('formateurs.layout')

@section('content')
<div class="container py-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h2 class="mb-0">Présence - Format vierge</h2>
		<button class="btn btn-outline-secondary" onclick="window.print()"><i class="fas fa-print me-1"></i> Imprimer</button>
	</div>

	<div class="card mb-3">
		<div class="card-body">
			<div class="row g-3">
				<div class="col-md-6">
					<div><strong>Date:</strong> {{ $generatedAt->format('d/m/Y') }}</div>
				</div>
				<div class="col-md-6">
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
				@for($i=0; $i<20; $i++)
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