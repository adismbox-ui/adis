@extends('apprenants.layout')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-white text-center">
                    <h3 class="mb-0"><i class="fas fa-edit me-2"></i>Modifier ma demande</h3>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('demandes.cours.maison.update', $demande->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="niveau_id" class="form-label fw-bold">Niveau à enseigner</label>
                            <select name="niveau_id" id="niveau_id" class="form-select" required>
                                <option value="">-- Sélectionnez un niveau --</option>
                                @if(isset($niveaux))
                                    @foreach($niveaux as $niveau)
                                        <option value="{{ $niveau->id }}" {{ old('niveau_id', $demande->niveau_id) == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nombre_enfants" class="form-label fw-bold">Nombre d'enfants</label>
                            <input type="number" name="nombre_enfants" id="nombre_enfants" class="form-control" value="{{ old('nombre_enfants', $demande->nombre_enfants) }}" required min="1" max="20">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label fw-bold">Message</label>
                            <textarea name="message" id="message" class="form-control" rows="4" required minlength="10" maxlength="2000">{{ old('message', $demande->message) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100"><i class="fas fa-save me-2"></i>Enregistrer les modifications</button>
                        <form action="{{ route('demandes.cours.maison.destroy', $demande->id) }}" method="POST" class="mt-2" onsubmit="return confirm('Supprimer cette demande ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100"><i class="fas fa-trash me-2"></i>Supprimer</button>
                        </form>
                        <a href="{{ route('demandes.cours.maison.index') }}" class="btn btn-outline-secondary w-100 mt-2">Retour à la liste</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
