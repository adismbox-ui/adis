@extends('formateurs.layout')

@section('content')
<style>
    .profil-glass {
        background: rgba(255,255,255,0.60);
        border-radius: 2rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
        backdrop-filter: blur(6px);
        border: 1.5px solid rgba(255,255,255,0.22);
        transition: box-shadow 0.25s;
    }
    .profil-glass:hover {
        box-shadow: 0 12px 36px 0 rgba(67,206,162,0.22), 0 4px 16px 0 rgba(30,60,114,0.13);
    }
    .avatar-profil {
        width: 110px; height: 110px; object-fit:cover; border-radius:50%; border: 4px solid #43cea2; box-shadow: 0 2px 12px #185a9d33;
        margin-bottom: 1rem;
        background: #fff;
    }
    .badge-profil {
        background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
        color: #fff;
        font-size: 1.02rem;
        padding: 0.35em 1.2em;
        border-radius: 14px;
        box-shadow: 0 2px 8px #185a9d22;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
    }
    .edit-profil-btn {
        background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
        color: #fff;
        border: none;
        border-radius: 2rem;
        padding: 0.55em 2em;
        font-weight: 600;
        transition: background 0.2s, transform 0.15s;
        box-shadow: 0 2px 8px #185a9d22;
    }
    .edit-profil-btn:hover {
        background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
        color: #fff;
        transform: translateY(-2px) scale(1.03);
    }
    .timeline {
        border-left: 3px solid #43cea2;
        margin-left: 2rem;
        padding-left: 1.5rem;
        margin-top: 2rem;
    }
    .timeline-event {
        position: relative;
        margin-bottom: 1.8rem;
    }
    .timeline-event:before {
        content: '';
        position: absolute;
        left: -2.2rem;
        top: 0.2rem;
        width: 1.1rem;
        height: 1.1rem;
        background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
        border-radius: 50%;
        box-shadow: 0 2px 8px #185a9d22;
    }
</style>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="profil-glass p-4 mb-4 text-center">
                <img src="/photo_2025-07-02_10-44-47.jpg" alt="Avatar Formateur" class="avatar-profil">
                <div class="badge-profil mb-2"><i class="fas fa-chalkboard-teacher me-2"></i>Formateur</div>
                <h3 class="fw-bold mb-1">{{ $formateur->utilisateur->prenom ?? '-' }} {{ $formateur->utilisateur->nom ?? '-' }}</h3>
                <div class="mb-3 text-muted">{{ $formateur->utilisateur->email ?? '-' }}</div>
                <div class="mb-3">
                    <span class="me-3"><i class="fas fa-phone-alt me-1"></i> {{ $formateur->utilisateur->telephone ?? '-' }}</span>
                    <span class="me-3"><i class="fas fa-map-marker-alt me-1"></i> {{ $formateur->adresse ?? '-' }}</span>
                    <span><i class="fas fa-birthday-cake me-1"></i> {{ $formateur->date_naissance ?? '-' }}</span>
                </div>
                <a href="{{ route('formateurs.profil.edit') }}" class="edit-profil-btn"><i class="fas fa-edit me-1"></i> Modifier mon profil</a>
            </div>
            <div class="timeline">
                <div class="timeline-event">
                    <div><i class="fas fa-user-check text-success me-2"></i> Dernière connexion : <strong>{{ now()->format('d/m/Y H:i') }}</strong></div>
                </div>
                <div class="timeline-event">
                    <div><i class="fas fa-layer-group text-primary me-2"></i> Niveaux enseignés : <strong>{{ $formateur->niveaux->count() ?? 0 }}</strong></div>
                </div>
                <div class="timeline-event">
                    <div><i class="fas fa-users text-info me-2"></i> Apprenants suivis : <strong>{{ $formateur->niveaux->flatMap(function($niveau) { return $niveau->modules->flatMap->inscriptions->pluck('apprenant_id'); })->unique()->count() ?? 0 }}</strong></div>
                </div>
                <div class="timeline-event">
                    <div><i class="fas fa-award text-warning me-2"></i> Membre depuis : <strong>{{ $formateur->created_at ? $formateur->created_at->format('d/m/Y') : '-' }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
