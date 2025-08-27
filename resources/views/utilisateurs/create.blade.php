@extends('admin.layout')
@section('content')
<h1>Créer un utilisateur</h1>
<form method="POST" action="{{ route('utilisateurs.store') }}" class="card p-4">
    @csrf
    <div class="mb-3">
        <label for="prenom" class="form-label">Prénom</label>
        <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom') }}">
        @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="nom" class="form-label">Nom</label>
        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}">
        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="sexe" class="form-label">Sexe</label>
        <select class="form-select @error('sexe') is-invalid @enderror" id="sexe" name="sexe">
            <option value="">Choisir...</option>
            <option value="Homme" {{ old('sexe') == 'Homme' ? 'selected' : '' }}>Homme</option>
            <option value="Femme" {{ old('sexe') == 'Femme' ? 'selected' : '' }}>Femme</option>
        </select>
        @error('sexe')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="categorie" class="form-label">Catégorie</label>
        <select class="form-select @error('categorie') is-invalid @enderror" id="categorie" name="categorie">
            <option value="">Choisir...</option>
            <option value="Enfant" {{ old('categorie') == 'Enfant' ? 'selected' : '' }}>Enfant</option>
            <option value="Etudiant" {{ old('categorie') == 'Etudiant' ? 'selected' : '' }}>Etudiant</option>
            <option value="Professionnel" {{ old('categorie') == 'Professionnel' ? 'selected' : '' }}>Professionnel</option>
            <option value="Enseignant" {{ old('categorie') == 'Enseignant' ? 'selected' : '' }}>Enseignant</option>
        </select>
        @error('categorie')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="telephone" class="form-label">Téléphone</label>
        <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone') }}">
        @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="mot_de_passe" class="form-label">Mot de passe</label>
        <input type="password" class="form-control @error('mot_de_passe') is-invalid @enderror" id="mot_de_passe" name="mot_de_passe">
        @error('mot_de_passe')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="type_compte" class="form-label">Type de compte</label>
        <select class="form-select @error('type_compte') is-invalid @enderror" id="type_compte" name="type_compte">
            <option value="">Choisir...</option>
            @php($user = auth()->user())
            @if(!$user || $user->type_compte === 'admin')
                <option value="formateur" {{ old('type_compte') == 'formateur' ? 'selected' : '' }}>Formateur</option>
                <option value="apprenant" {{ old('type_compte') == 'apprenant' ? 'selected' : '' }}>Apprenant</option>
            @elseif($user->type_compte === 'formateur')
                <option value="assistant" {{ old('type_compte') == 'assistant' ? 'selected' : '' }}>Assistant</option>
                <option value="apprenant" {{ old('type_compte') == 'apprenant' ? 'selected' : '' }}>Apprenant</option>
            @endif
        </select>
        @error('type_compte')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-primary">Enregistrer</button>
</form>
@endsection 