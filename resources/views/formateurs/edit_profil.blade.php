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
        width: 100px; height: 100px; object-fit:cover; border-radius:50%; border: 4px solid #43cea2; box-shadow: 0 2px 12px #185a9d33;
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
    .form-control:focus {
        border-color: #43cea2;
        box-shadow: 0 0 0 0.16rem rgba(67,206,162,0.13);
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
    .edit-profil-btn:hover, .btn-success:hover {
        background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
        color: #fff;
        transform: translateY(-2px) scale(1.03);
    }
    .btn-success {
        background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
        border: none;
        border-radius: 2rem;
        font-weight: 600;
        transition: background 0.2s, transform 0.15s;
        box-shadow: 0 2px 8px #185a9d22;
    }
    .btn-secondary {
        border-radius: 2rem;
        font-weight: 600;
    }
    .input-group-text {
        background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
        color: #fff;
        border: none;
    }
</style>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="profil-glass p-4 mb-4 text-center">
                <img src="/photo_2025-07-02_10-44-47.jpg" alt="Avatar Formateur" class="avatar-profil">
                <div class="badge-profil mb-2"><i class="fas fa-chalkboard-teacher me-2"></i>Formateur</div>
                <h3 class="fw-bold mb-1">{{ $formateur->utilisateur->prenom ?? '-' }} {{ $formateur->utilisateur->nom ?? '-' }}</h3>
                <div class="mb-3 text-muted">Modification du profil</div>
                <form method="POST" action="{{ route('formateurs.profil.update') }}" class="text-start mt-4">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom', $formateur->utilisateur->nom) }}" placeholder="Nom" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="{{ old('prenom', $formateur->utilisateur->prenom) }}" placeholder="Prénom" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $formateur->utilisateur->email) }}" placeholder="Email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control" id="telephone" name="telephone" value="{{ old('telephone', $formateur->utilisateur->telephone) }}" placeholder="Téléphone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
                                <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="{{ old('date_naissance', $formateur->date_naissance) }}" placeholder="Date de naissance">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" class="form-control" id="adresse" name="adresse" value="{{ old('adresse', $formateur->adresse) }}" placeholder="Adresse">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100 mt-3"><i class="fas fa-save me-1"></i>Enregistrer</button>
                    <a href="{{ route('formateurs.profil') }}" class="btn btn-secondary w-100 mt-2">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
