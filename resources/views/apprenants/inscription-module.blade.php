@extends('apprenants.layout')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg mb-4 border-0">
                <div class="card-header bg-gradient-success text-white text-center">
                    <h2 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Inscription à un module</h2>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('apprenants.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label for="module_id" class="form-label">Module</label>
                            <select class="form-select @error('module_id') is-invalid @enderror" id="module_id" name="module_id" required>
                                <option value="">Choisir un module...</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module->id }}" {{ (old('module_id', $selectedModuleId) == $module->id) ? 'selected' : '' }}>
                                        {{ $module->titre }} ({{ $module->niveau->nom ?? 'Niveau inconnu' }}, {{ $module->prix ? number_format($module->prix, 0, ',', ' ') . ' F CFA' : 'Gratuit' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('module_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="niveau_id" class="form-label">Niveau</label>
                            <select class="form-select @error('niveau_id') is-invalid @enderror" id="niveau_id" name="niveau_id">
                                <option value="">Choisir un niveau...</option>
                                @foreach($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}" {{ (old('niveau_id', $apprenant->niveau_id ?? null) == $niveau->id) ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                                @endforeach
                            </select>
                            @error('niveau_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="mobile_money" class="form-label">Numéro Mobile Money</label>
                            <input type="text" class="form-control @error('mobile_money') is-invalid @enderror" id="mobile_money" name="mobile_money" value="{{ old('mobile_money', $user->telephone ?? '') }}" required>
                            @error('mobile_money')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="moyen_paiement" class="form-label">Moyen de paiement</label>
                            <select class="form-select @error('moyen_paiement') is-invalid @enderror" id="moyen_paiement" name="moyen_paiement">
                                <option value="">Choisir...</option>
                                <option value="Orange Money" {{ old('moyen_paiement') == 'Orange Money' ? 'selected' : '' }}>Orange Money</option>
                                <option value="MTN Mobile Money" {{ old('moyen_paiement') == 'MTN Mobile Money' ? 'selected' : '' }}>MTN Mobile Money</option>
                                <option value="Moov Money" {{ old('moyen_paiement') == 'Moov Money' ? 'selected' : '' }}>Moov Money</option>
                                <option value="Autre" {{ old('moyen_paiement') == 'Autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('moyen_paiement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 text-center mt-4">
                            <a href="{{ route('achat') }}" class="btn btn-success btn-lg px-5"><i class="fas fa-check-circle me-2"></i>Découvrez nos formations</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 