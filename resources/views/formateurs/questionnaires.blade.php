@extends('formateurs.layout')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card shadow mb-4" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(77, 166, 116, 0.25); border-radius: 16px;">
        <div class="card-header" style="background: rgba(26,77,58,0.4); color:#e8f5e8; border-bottom: 1px solid rgba(77,166,116,0.25);">
            <h4 class="mb-0"><i class="fas fa-question-circle me-2"></i> Questionnaires de mes niveaux</h4>
        </div>
        <div class="card-body">
            @if($questionnaires->isEmpty())
                <div class="text-center text-light py-5">
                    <i class="fas fa-inbox fa-3x mb-3" style="color:#4da674;"></i>
                    <p class="mb-0">Aucun questionnaire n'est disponible pour vos niveaux.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-dark table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Niveau</th>
                                <th>Module</th>
                                <th>Titre</th>
                                <th>Questions</th>
                                <th>Envoi</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questionnaires as $q)
                                <tr>
                                    <td>{{ $q['niveau']?->nom ?? '-' }}</td>
                                    <td>{{ $q['module']?->titre ?? '-' }}</td>
                                    <td>{{ $q['titre'] }}</td>
                                    <td>{{ $q['questions_count'] }}</td>
                                    <td>
                                        @if(!empty($q['date_envoi']))
                                            {{ \Carbon\Carbon::parse($q['date_envoi'])->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($q['envoye'])
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Envoyé</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Planifié</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('assistant.questionnaires.show', $q['id']) }}" class="btn btn-sm btn-outline-light">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection





