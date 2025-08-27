@extends('admin.layout')

@section('content')
<style>
    /* Background avec image et overlay */
    body {
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(22, 101, 52, 0.9)), 
                    url('https://images.unsplash.com/photo-1518837695005-2083093ee35b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
        background-size: cover;
        background-attachment: fixed;
        background-position: center;
        min-height: 100vh;
        margin: 0;
        padding: 20px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Alert personnalisé */
    .alert {
        border: none;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        animation: slideInDown 0.6s ease-out;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.9), rgba(22, 163, 74, 0.9));
        color: white;
        border-left: 5px solid #10b981;
    }

    /* Titre principal */
    h1 {
        color: #ffffff;
        font-size: 3rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 40px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        background: linear-gradient(135deg, #10b981, #34d399);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: glow 2s ease-in-out infinite alternate;
    }

    @keyframes glow {
        from { filter: drop-shadow(0 0 5px rgba(16, 185, 129, 0.5)); }
        to { filter: drop-shadow(0 0 20px rgba(16, 185, 129, 0.8)); }
    }

    /* Container principal */
    .main-container {
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        animation: fadeInUp 0.8s ease-out;
    }

    /* Formulaire */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        color: #10b981;
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        background: rgba(30, 41, 59, 0.8);
        border: 2px solid rgba(16, 185, 129, 0.3);
        border-radius: 10px;
        padding: 12px 16px;
        color: #ffffff;
        font-size: 1rem;
        width: 100%;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
        background: rgba(30, 41, 59, 0.9);
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .form-select {
        background: rgba(30, 41, 59, 0.8);
        border: 2px solid rgba(16, 185, 129, 0.3);
        border-radius: 10px;
        padding: 12px 16px;
        color: #ffffff;
        font-size: 1rem;
        width: 100%;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        outline: none;
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
        background: rgba(30, 41, 59, 0.9);
    }

    .form-select option {
        background: rgba(30, 41, 59, 0.9);
        color: #ffffff;
    }

    /* Boutons */
    .btn {
        border: none;
        border-radius: 10px;
        padding: 12px 24px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        margin: 5px;
        cursor: pointer;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #0284c7, #0369a1);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
        color: white;
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #d97706, #b45309);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        color: white;
    }

    /* Messages d'erreur */
    .invalid-feedback {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 5px;
        display: block;
    }

    .is-invalid {
        border-color: #ef4444 !important;
    }

    /* Animations */
    @keyframes slideInDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes fadeInUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h1>
        <i class="fas fa-edit me-3"></i>
        Modifier l'Assistant
    </h1>

    <div class="main-container">
        <form method="POST" action="{{ route('assistants.update', $assistant->id) }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="prenom" class="form-label">
                    <i class="fas fa-user me-2"></i>Prénom
                </label>
                <input type="text" 
                       class="form-control @error('prenom') is-invalid @enderror" 
                       id="prenom" 
                       name="prenom" 
                       value="{{ old('prenom', $assistant->prenom) }}" 
                       required>
                @error('prenom')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="nom" class="form-label">
                    <i class="fas fa-id-card me-2"></i>Nom
                </label>
                <input type="text" 
                       class="form-control @error('nom') is-invalid @enderror" 
                       id="nom" 
                       name="nom" 
                       value="{{ old('nom', $assistant->nom) }}" 
                       required>
                @error('nom')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-2"></i>Email
                </label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $assistant->email) }}" 
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="telephone" class="form-label">
                    <i class="fas fa-phone me-2"></i>Téléphone
                </label>
                <input type="text" 
                       class="form-control @error('telephone') is-invalid @enderror" 
                       id="telephone" 
                       name="telephone" 
                       value="{{ old('telephone', $assistant->telephone) }}">
                @error('telephone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="formateur_id" class="form-label">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Formateur responsable (optionnel)
                </label>
                <select class="form-select @error('formateur_id') is-invalid @enderror" 
                        id="formateur_id" 
                        name="formateur_id">
                    <option value="">Aucun formateur assigné</option>
                    @foreach($formateurs as $formateur)
                        <option value="{{ $formateur->id }}" 
                                {{ old('formateur_id', $assistant->assistant->formateur_id ?? '') == $formateur->id ? 'selected' : '' }}>
                            {{ $formateur->utilisateur->prenom ?? 'N/A' }} {{ $formateur->utilisateur->nom ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                @error('formateur_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('admin.assistants') }}" class="btn btn-warning">
                    <i class="fas fa-arrow-left me-2"></i>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
