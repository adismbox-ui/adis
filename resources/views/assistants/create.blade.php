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
        .card-header .subtitle { font-size: 0.8rem; font-weight: 400; opacity: 0.95; color: #e8f5e8; }
        .card-header i { font-size: 2rem; margin-bottom: 0.5rem; display: block; color: #e8f5e8; }
        .card-body { padding: 1rem; position: relative; }
        .section-title { font-size: 1.1rem; font-weight: 800; color: #66bb6a; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.6rem; padding: 0.6rem; background: linear-gradient(135deg, rgba(76, 175, 80, 0.15), rgba(27, 94, 32, 0.2)); border-radius: 8px; border-left: 6px solid #4caf50; text-shadow: 0 2px 8px rgba(0,0,0,0.5); border: 1px solid rgba(76, 175, 80, 0.2); }
        .form-label { font-weight: 700; color: #81c784; margin-bottom: 0.3rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; text-shadow: 0 2px 6px rgba(0,0,0,0.3); }
        .form-control, .form-select { border: 2px solid rgba(76, 175, 80, 0.4); border-radius: 8px; padding: 0.6rem 1rem; font-size: 0.9rem; font-weight: 600; color: #81c784; background: linear-gradient(135deg, rgba(15, 35, 25, 0.9), rgba(27, 94, 32, 0.3)); backdrop-filter: blur(15px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(76, 175, 80, 0.2); }
        .form-control::placeholder { color: #66bb6a; font-weight: 500; opacity: 0.8; }
        .form-control:focus, .form-select:focus { outline: none; border-color: #4caf50; box-shadow: 0 0 0 8px rgba(76, 175, 80, 0.25), 0 12px 35px rgba(76, 175, 80, 0.3), inset 0 1px 0 rgba(76, 175, 80, 0.3); transform: translateY(-3px) scale(1.02); background: linear-gradient(135deg, rgba(27, 94, 32, 0.4), rgba(76, 175, 80, 0.1)); color: #a5d6a7; }
        
        /* Styles pour les options de la liste déroulante */
        .form-select option {
            background: rgba(15, 35, 25, 0.95);
            color: #81c784;
            font-weight: 600;
            padding: 10px;
        }
        
        .form-select option:hover {
            background: rgba(76, 175, 80, 0.2);
            color: #a5d6a7;
        }
        
        /* Section divider */
        .section-divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(76, 175, 80, 0.3), transparent);
            margin: 2rem 0;
            border-radius: 1px;
        }
        .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; }
        .btn-submit { background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 25%, #4caf50 50%, #66bb6a 75%, #81c784 100%); border: none; color: white; font-weight: 900; font-size: 1rem; padding: 1rem 3rem; border-radius: 8px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; text-transform: uppercase; letter-spacing: 2px; box-shadow: 0 20px 45px rgba(27, 94, 32, 0.5), 0 0 0 2px rgba(76, 175, 80, 0.3); cursor: pointer; }
        .progress-bar { position: fixed; top: 0; left: 0; height: 8px; background: linear-gradient(90deg, #1b5e20, #4caf50, #66bb6a, #81c784); transition: width 0.5s ease; z-index: 1000; box-shadow: 0 0 20px rgba(76, 175, 80, 1); }
</style>

    <div class="progress-bar" id="progressBar"></div>

<div class="main-container">
        <div class="form-card">
            <div class="card-header">
                <i class="fas fa-user-tie"></i>
                <h1>Créer un Assistant</h1>
                <p class="subtitle">Même design que la création d'apprenant</p>
        </div>

        <div class="card-body">
                <form id="assistantForm" method="POST" action="{{ route('admin.assistants.store') }}">
    @csrf

                    <div class="form-group">
                        <h3 class="section-title"><i class="fas fa-user"></i> Informations personnelles</h3>
                        <div class="grid-2">
                            <div>
                                <label for="prenom" class="form-label"><i class="fas fa-id-badge"></i> Prénom</label>
                                <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                                @error('prenom')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label for="nom" class="form-label"><i class="fas fa-user"></i> Nom</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required>
                                @error('nom')<div class="error-message">{{ $message }}</div>@enderror
    </div>
    </div>
                        <div class="grid-3">
                            <div>
                                <label for="sexe" class="form-label"><i class="fas fa-venus-mars"></i> Sexe</label>
                                <select class="form-select @error('sexe') is-invalid @enderror" id="sexe" name="sexe" required>
            <option value="">Choisir...</option>
            <option value="Homme" {{ old('sexe') == 'Homme' ? 'selected' : '' }}>Homme</option>
            <option value="Femme" {{ old('sexe') == 'Femme' ? 'selected' : '' }}>Femme</option>
        </select>
                                @error('sexe')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label for="telephone" class="form-label"><i class="fas fa-phone"></i> Téléphone</label>
                                <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone') }}" required>
                                @error('telephone')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>



                    <div class="form-group">
                        <h3 class="section-title"><i class="fas fa-key"></i> Accès au compte</h3>
                        <div class="grid-2">
                            <div>
                                <label for="mot_de_passe" class="form-label"><i class="fas fa-lock"></i> Mot de passe</label>
                                <input type="password" class="form-control @error('mot_de_passe') is-invalid @enderror" id="mot_de_passe" name="mot_de_passe" value="adis" required>
                                <small class="text-muted">Mot de passe par défaut: <strong>adis</strong></small>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="show_pwd_checkbox">
                                    <label class="form-check-label" for="show_pwd_checkbox">Afficher le mot de passe</label>
                                </div>
                                @error('mot_de_passe')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ route('admin.assistants') }}" class="btn btn-outline-secondary me-2"><i class="fas fa-arrow-left"></i> Retour</a>
                        <button type="submit" class="btn-submit"><i class="fas fa-save me-2"></i> Enregistrer l'assistant</button>
    </div>
</form>
        </div>
    </div>
</div>

    <script>
        function updateProgressBar() {
            const form = document.getElementById('assistantForm');
            const inputs = form.querySelectorAll('input[required], select[required]');
            let filledInputs = 0;
            inputs.forEach(input => { if (input.value && input.value.toString().trim() !== '') { filledInputs++; } });
            const progress = inputs.length > 0 ? (filledInputs / inputs.length) * 100 : 100;
            document.getElementById('progressBar').style.width = progress + '%';
        }
        document.querySelectorAll('#assistantForm input, #assistantForm select').forEach(input => {
            input.addEventListener('input', updateProgressBar);
            input.addEventListener('change', updateProgressBar);
        });
        updateProgressBar();

        // Afficher / masquer le mot de passe
        const showPwd = document.getElementById('show_pwd_checkbox');
        const pwdInput = document.getElementById('mot_de_passe');
        if (showPwd && pwdInput) {
            showPwd.addEventListener('change', function() {
                pwdInput.type = this.checked ? 'text' : 'password';
            });
        }
    </script>
@endsection 