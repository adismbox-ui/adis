@extends('admin.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h2 class="mb-4 text-primary"><i class="fa-solid fa-certificate"></i> Certificats obtenus par les apprenants</h2>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Apprenant</th>
                                    <th>Module</th>
                                    <th>Date d'obtention</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($certificats as $certificat)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $certificat->id }}</span></td>
                                        <td><i class="fa-solid fa-user-graduate"></i> {{ $certificat->apprenant->utilisateur->prenom ?? '-' }} {{ $certificat->apprenant->utilisateur->nom ?? '' }}</td>
                                        <td><i class="fa-solid fa-book"></i> {{ $certificat->module->titre ?? '-' }}</td>
                                        <td><i class="fa-solid fa-calendar-check"></i> {{ $certificat->created_at ? $certificat->created_at->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.certificats.show', $certificat) }}" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                                            <a href="{{ route('admin.certificats.edit', $certificat) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                            <form action="{{ route('admin.certificats.destroy', $certificat) }}" method="POST" style="display:inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce certificat ?')"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Aucun certificat trouvé.</td></tr>
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