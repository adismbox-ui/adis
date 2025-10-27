@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg mt-4">
                <div class="card-header bg-warning text-dark text-center">
                    <h3><i class="fas fa-clock me-2"></i>Inscriptions en attente de validation</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-warning">
                                <tr>
                                    <th>Apprenant</th>
                                    <th>Module</th>
                                    <th>Date d'inscription</th>
                                    <th>Numéro Mobile Money</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inscriptions as $insc)
                                    <tr>
                                        <td>{{ $insc->apprenant->utilisateur->prenom ?? '' }} {{ $insc->apprenant->utilisateur->nom ?? '' }}</td>
                                        <td>{{ $insc->module->titre ?? '' }}</td>
                                        <td>{{ $insc->date_inscription }}</td>
                                        <td>{{ $insc->mobile_money ?? '-' }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.inscriptions.valider', $insc->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Valider</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Aucune inscription en attente</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 