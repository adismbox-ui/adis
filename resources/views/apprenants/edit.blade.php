@extends('admin.layout')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-edit"></i> Modifier l'apprenant</h1>
        <a href="{{ route('apprenants.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edition du profil</h6>
        </div>
        <div class="card-body">
            <style>
            select.form-control, select.form-select { color:#000 !important; background-color:#fff !important; }
            select.form-control option, select.form-select option { color:#000 !important; background-color:#fff !important; }
            </style>

            <form method="POST" action="{{ route('apprenants.update', $apprenant) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom" class="form-control @error('prenom') is-invalid @enderror" value="{{ old('prenom', $apprenant->utilisateur->prenom ?? '') }}" required>
                        @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $apprenant->utilisateur->nom ?? '') }}" required>
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $apprenant->utilisateur->email ?? '') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mot de passe (optionnel)</label>
                        <input type="password" name="mot_de_passe" class="form-control @error('mot_de_passe') is-invalid @enderror" placeholder="Laisser vide pour ne pas changer">
                        @error('mot_de_passe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Sexe</label>
                        <select name="sexe" class="form-select @error('sexe') is-invalid @enderror" required>
                            <option value="">Choisir...</option>
                            <option value="Homme" {{ old('sexe', $apprenant->utilisateur->sexe ?? '') == 'Homme' ? 'selected' : '' }}>Homme</option>
                            <option value="Femme" {{ old('sexe', $apprenant->utilisateur->sexe ?? '') == 'Femme' ? 'selected' : '' }}>Femme</option>
                        </select>
                        @error('sexe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Catégorie</label>
                        <select name="categorie" class="form-select @error('categorie') is-invalid @enderror" required>
                            <option value="">Choisir...</option>
                            <option value="Enfant" {{ old('categorie', $apprenant->utilisateur->categorie ?? '') == 'Enfant' ? 'selected' : '' }}>Enfant</option>
                            <option value="Etudiant" {{ old('categorie', $apprenant->utilisateur->categorie ?? '') == 'Etudiant' ? 'selected' : '' }}>Étudiant</option>
                            <option value="Professionnel" {{ old('categorie', $apprenant->utilisateur->categorie ?? '') == 'Professionnel' ? 'selected' : '' }}>Professionnel</option>
                        </select>
                        @error('categorie')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Niveau</label>
                        <select name="niveau_id" class="form-select @error('niveau_id') is-invalid @enderror">
                            <option value="">Aucun</option>
                            @foreach(($niveaux ?? []) as $niveau)
                                <option value="{{ $niveau->id }}" {{ old('niveau_id', $apprenant->niveau_id) == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                            @endforeach
                        </select>
                        @error('niveau_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('apprenants.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection