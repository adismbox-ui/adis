@extends('admin.layout')
@section('head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
@endsection
@section('content')
<style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; }

        .main-container { max-width: 1200px; margin-left: 280px; padding: 2rem; position: relative; z-index: 10; width: calc(100% - 280px); }
        .form-card { background: rgba(15, 35, 25, 0.95); backdrop-filter: blur(25px); border-radius: 0; box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(76, 175, 80, 0.2), inset 0 1px 0 rgba(76, 175, 80, 0.1); border: 2px solid rgba(76, 175, 80, 0.3); transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1); position: relative; overflow: hidden; }
        .form-card:hover { transform: translateY(-10px); box-shadow: 0 40px 100px rgba(0, 0, 0, 0.5), 0 0 0 2px rgba(76, 175, 80, 0.4), inset 0 1px 0 rgba(76, 175, 80, 0.2); }

        .card-header { background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 25%, #388e3c 50%, #4caf50 75%, #66bb6a 100%); color: white; padding: 1rem 2rem 0.8rem; border-radius: 0; position: relative; overflow: hidden; text-align: center; }
        .card-header::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent); animation: headerShine 6s ease-in-out infinite; }
        @keyframes headerShine { 0%, 100% { left: -100%; } 50% { left: 100%; } }
        .card-header h1 { font-size: 1.8rem; font-weight: 900; text-shadow: 3px 3px 8px rgba(0, 0, 0, 0.5); margin-bottom: 0.3rem; letter-spacing: 2px; background: linear-gradient(45deg, #ffffff, #e8f5e8, #ffffff); background-size: 200% 200%; background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: titleShimmer 3s ease-in-out infinite; }
        @keyframes titleShimmer { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
        .card-header .subtitle { font-size: 0.8rem; font-weight: 400; opacity: 0.95; color: #e8f5e8; }
        .card-header i { font-size: 2rem; margin-bottom: 0.5rem; display: block; color: #e8f5e8; }

        .card-body { padding: 1rem; position: relative; }
        .section-title { font-size: 1.1rem; font-weight: 800; color: #66bb6a; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.6rem; padding: 0.6rem; background: linear-gradient(135deg, rgba(76, 175, 80, 0.15), rgba(27, 94, 32, 0.2)); border-radius: 8px; border-left: 6px solid #4caf50; text-shadow: 0 2px 8px rgba(0,0,0,0.5); border: 1px solid rgba(76, 175, 80, 0.2); }
        .form-label { font-weight: 700; color: #81c784; margin-bottom: 0.3rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; text-shadow: 0 2px 6px rgba(0,0,0,0.3); }
        .form-control, .form-select { border: 2px solid rgba(76, 175, 80, 0.4); border-radius: 8px; padding: 0.6rem 1rem; font-size: 0.9rem; font-weight: 600; color: #81c784; background: linear-gradient(135deg, rgba(15, 35, 25, 0.9), rgba(27, 94, 32, 0.3)); backdrop-filter: blur(15px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(76, 175, 80, 0.2); }
        .form-control::placeholder { color: #66bb6a; font-weight: 500; opacity: 0.8; }
        .form-control:focus, .form-select:focus { outline: none; border-color: #4caf50; box-shadow: 0 0 0 8px rgba(76, 175, 80, 0.25), 0 12px 35px rgba(76, 175, 80, 0.3), inset 0 1px 0 rgba(76, 175, 80, 0.3); transform: translateY(-3px) scale(1.02); background: linear-gradient(135deg, rgba(27, 94, 32, 0.4), rgba(76, 175, 80, 0.1)); color: #a5d6a7; }
        
        /* Styles pour les boutons radio */
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }
        
        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            border: 2px solid rgba(76, 175, 80, 0.4);
            background: rgba(15, 35, 25, 0.9);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .form-check-input:checked {
            background: linear-gradient(135deg, #4caf50, #66bb6a);
            border-color: #4caf50;
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.2);
        }
        
        .form-check-input:focus {
            box-shadow: 0 0 0 8px rgba(76, 175, 80, 0.25);
        }
        
        .form-check-label {
            color: #81c784;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .form-check-label:hover {
            color: #a5d6a7;
            transform: translateX(2px);
        }
        
        .text-success {
            color: #4caf50 !important;
        }
        
        .text-danger {
            color: #f44336 !important;
        }
        
        .d-flex {
            display: flex !important;
        }
        
        .gap-3 {
            gap: 1rem !important;
        }

        .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; }

        .btn-submit { background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 25%, #4caf50 50%, #66bb6a 75%, #81c784 100%); border: none; color: white; font-weight: 900; font-size: 1rem; padding: 1rem 3rem; border-radius: 8px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; text-transform: uppercase; letter-spacing: 2px; box-shadow: 0 20px 45px rgba(27, 94, 32, 0.5), 0 0 0 2px rgba(76, 175, 80, 0.3); cursor: pointer; }

        .progress-bar { position: fixed; top: 0; left: 0; height: 8px; background: linear-gradient(90deg, #1b5e20, #4caf50, #66bb6a, #81c784); transition: width 0.5s ease; z-index: 1000; box-shadow: 0 0 20px rgba(76, 175, 80, 1); }
    </style>
    <style>
        /* Lisibilité des listes déroulantes */
        select.form-control, select.form-select { color: #000 !important; background-color: #fff !important; }
        select.form-control option, select.form-select option { color: #000 !important; background-color: #fff !important; }
    </style>

    <div class="progress-bar" id="progressBar"></div>

    <div class="main-container">
        <div class="form-card">
            <div class="card-header">
                <i class="fas fa-chalkboard-teacher"></i>
                <h1>Créer un Formateur</h1>
                <p class="subtitle">Ajoutez un enseignant avec style – même design que les apprenants</p>
</div>

    <div class="card-body">
                <form id="formateurForm" method="POST" action="{{ route('formateurs.store') }}">
            @csrf

                    <div class="form-group">
                        <h3 class="section-title"><i class="fas fa-user"></i> Informations personnelles</h3>
                        <div class="grid-2">
                            <div>
                                <label for="prenom" class="form-label"><i class="fas fa-user"></i> Prénom</label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                                @error('prenom')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label for="nom" class="form-label"><i class="fas fa-user"></i> Nom</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required>
                                @error('nom')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="grid-2">
                            <div>
                                <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label for="telephone" class="form-label"><i class="fas fa-phone"></i> Téléphone</label>
                                <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone') }}">
                                @error('telephone')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="grid-2">
                            <div>
                                <label for="mot_de_passe" class="form-label"><i class="fas fa-lock"></i> Mot de passe</label>
                                <input type="password" class="form-control @error('mot_de_passe') is-invalid @enderror" id="mot_de_passe" name="mot_de_passe" required>
                                @error('mot_de_passe')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label for="mot_de_passe_confirmation" class="form-label"><i class="fas fa-lock"></i> Confirmer le mot de passe</label>
                                <input type="password" class="form-control" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation" required>
                            </div>
                        </div>
                </div>

                    <div class="section-divider"></div>

                    <div class="form-group">
                        <h3 class="section-title"><i class="fas fa-map-marker-alt"></i> Adresse</h3>
                        <div class="grid-2">
                            <div>
                                <label for="ville" class="form-label"><i class="fas fa-city"></i> Ville</label>
                                <input type="text" class="form-control @error('ville') is-invalid @enderror" id="ville" name="ville" value="{{ old('ville') }}">
                                @error('ville')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label for="commune" class="form-label"><i class="fas fa-map-marker-alt"></i> Commune</label>
                                <input type="text" class="form-control @error('commune') is-invalid @enderror" id="commune" name="commune" value="{{ old('commune') }}">
                                @error('commune')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="grid-2">
                            <div>
                                <label for="quartier" class="form-label"><i class="fas fa-location-arrow"></i> Quartier</label>
                                <input type="text" class="form-control @error('quartier') is-invalid @enderror" id="quartier" name="quartier" value="{{ old('quartier') }}">
                                @error('quartier')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                        </div>
                </div>

                    <div class="section-divider"></div>

                    <div class="form-group">
                        <h3 class="section-title"><i class="fas fa-project-diagram"></i> Affectations</h3>
                        <div class="grid-2">
                            <div>
                                <label for="niveau_id" class="form-label"><i class="fas fa-layer-group"></i> Assigner un niveau (optionnel)</label>
                                <select class="form-select @error('niveau_id') is-invalid @enderror" id="niveau_id" name="niveau_id">
                                    <option value="">-- Aucun niveau --</option>
                                    @foreach(($niveaux ?? []) as $niveau)
                                        <option value="{{ $niveau->id }}" {{ old('niveau_id') == $niveau->id ? 'selected' : '' }}>{{ $niveau->nom }}</option>
                                    @endforeach
                                </select>
                                @error('niveau_id')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="form-label"><i class="fas fa-user-tie"></i> Devenir assistant</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="devenir_assistant" id="devenir_assistant_oui" value="oui" {{ old('devenir_assistant') == 'oui' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="devenir_assistant_oui">
                                            <i class="fas fa-check-circle text-success"></i> Oui
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="devenir_assistant" id="devenir_assistant_non" value="non" {{ old('devenir_assistant') == 'non' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="devenir_assistant_non">
                                            <i class="fas fa-times-circle text-danger"></i> Non
                                        </label>
                                    </div>
                                </div>
                                <small class="text-muted">Choisissez si ce nouveau formateur doit aussi avoir le rôle d'assistant</small>
                                @error('devenir_assistant')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ route('formateurs.index') }}" class="btn btn-outline-secondary me-2"><i class="fas fa-arrow-left"></i> Retour</a>
                        <button type="submit" class="btn-submit"><i class="fas fa-save me-2"></i> Enregistrer le formateur</button>
            </div>
        </form>
    </div>
</div>
    </div>

    <script>
        function updateProgressBar() {
            const form = document.getElementById('formateurForm');
            const inputs = form.querySelectorAll('input[required], select[required]');
            let filledInputs = 0;
            inputs.forEach(input => { if (input.value && input.value.toString().trim() !== '') { filledInputs++; } });
            const progress = inputs.length > 0 ? (filledInputs / inputs.length) * 100 : 100;
            document.getElementById('progressBar').style.width = progress + '%';
        }
        document.querySelectorAll('#formateurForm input, #formateurForm select').forEach(input => {
            input.addEventListener('input', updateProgressBar);
            input.addEventListener('change', updateProgressBar);
        });
        updateProgressBar();
    </script>
@endsection 