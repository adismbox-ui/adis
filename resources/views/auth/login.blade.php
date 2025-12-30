<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Interface Premium</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.9), rgba(6, 95, 70, 0.8)), 
                        url('{{ asset('IMAGE ARRIERE PLAN CONNEXION.jpg') }}');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            position: relative;
        }

        /* Particules 3D animées */
        .particles-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: linear-gradient(45deg, #10b981, #34d399);
            border-radius: 50%;
            animation: float3d 8s infinite ease-in-out;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
        }

        @keyframes float3d {
            0%, 100% { 
                transform: translateY(0px) rotateX(0deg) rotateY(0deg) scale(1);
                opacity: 0.7;
            }
            25% { 
                transform: translateY(-30px) rotateX(90deg) rotateY(45deg) scale(1.2);
                opacity: 1;
            }
            50% { 
                transform: translateY(-60px) rotateX(180deg) rotateY(90deg) scale(0.8);
                opacity: 0.5;
            }
            75% { 
                transform: translateY(-30px) rotateX(270deg) rotateY(135deg) scale(1.1);
                opacity: 0.8;
            }
        }

        /* Effets de lumière dynamiques */
        .light-rays {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 30%, rgba(16, 185, 129, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(34, 211, 149, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(6, 182, 212, 0.1) 0%, transparent 70%);
            animation: lightPulse 4s ease-in-out infinite;
        }

        @keyframes lightPulse {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.05); }
        }

        /* Container principal */
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            perspective: 1000px;
            position: relative;
            z-index: 10;
        }

        /* Carte de connexion 3D */
        .login-card {
            background: linear-gradient(135deg, #4caf50, #388e3c) !important;
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 50px;
            width: 450px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(16, 185, 129, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            transform-style: preserve-3d;
            animation: cardEntrance 1s ease-out, cardFloat 6s ease-in-out infinite 1s;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            display: none;
        }

        @keyframes borderGlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes cardEntrance {
            0% {
                transform: translateY(100px) rotateX(-15deg) scale(0.8);
                opacity: 0;
            }
            100% {
                transform: translateY(0) rotateX(0deg) scale(1);
                opacity: 1;
            }
        }

        @keyframes cardFloat {
            0%, 100% { transform: translateY(0px) rotateY(0deg); }
            25% { transform: translateY(-5px) rotateY(1deg); }
            50% { transform: translateY(-10px) rotateY(0deg); }
            75% { transform: translateY(-5px) rotateY(-1deg); }
        }

        /* En-tête avec logo animé */
        .login-header {
            text-align: center;
            margin-bottom: 40px;
            transform: translateZ(20px);
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
            animation: logoSpin 4s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }
        
        .logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 50%;
            animation: logoImageSpin 4s ease-in-out infinite;
        }

        .logo::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            border: 2px solid rgba(16, 185, 129, 0.3);
            border-radius: 50%;
            animation: logoRing 3s linear infinite;
        }

        @keyframes logoSpin {
            0%, 100% { transform: rotateY(0deg) scale(1); }
            50% { transform: rotateY(180deg) scale(1.1); }
        }
        
        @keyframes logoImageSpin {
            0%, 100% { transform: rotateY(0deg) scale(1); }
            50% { transform: rotateY(-180deg) scale(1.1); }
        }

        @keyframes logoRing {
            0% { transform: scale(1) rotate(0deg); opacity: 1; }
            100% { transform: scale(1.5) rotate(360deg); opacity: 0; }
        }

        .logo i {
            font-size: 35px;
            color: white;
            animation: iconPulse 2s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        h1 {
            color: #ffffff;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #10b981, #34d399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: textGlow 2s ease-in-out infinite alternate;
        }

        @keyframes textGlow {
            from { filter: drop-shadow(0 0 5px rgba(16, 185, 129, 0.5)); }
            to { filter: drop-shadow(0 0 15px rgba(16, 185, 129, 0.8)); }
        }

        .subtitle {
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 300;
            margin-bottom: 30px;
            text-shadow: 0 0 8px rgba(255,255,255,0.7), 0 0 16px rgba(255,255,255,0.45);
        }

        /* Badges décoratifs */
        .badges-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .badge {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(6, 182, 212, 0.2));
            color: #10b981;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid rgba(16, 185, 129, 0.3);
            animation: badgeFloat 3s ease-in-out infinite;
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.2);
        }

        .badge:nth-child(2) { animation-delay: 1s; }
        .badge:nth-child(3) { animation-delay: 2s; }

        @keyframes badgeFloat {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-5px) scale(1.05); }
        }

        /* Formulaire */
        .form-group {
            margin-bottom: 25px;
            position: relative;
            transform: translateZ(10px);
        }

        .input-container {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
        }

        .input-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.4), transparent);
            transition: left 0.6s;
        }

        .input-container:focus-within::before {
            left: 100%;
        }

        .form-input {
            width: 100%;
            padding: 18px 50px 18px 20px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 15px;
            color: #111827;
            font-size: 16px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input::placeholder {
            color: #64748b;
        }

        .form-input:focus {
            border-color: #10b981;
            background: #ffffff;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.25);
            transform: scale(1.01);
        }

        .form-input.error-input {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .form-input.error-input:focus {
            border-color: #ef4444;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.25);
        }

        .input-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #10b981;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .form-input:focus + .input-icon {
            color: #34d399;
            animation: iconBounce 0.6s ease;
        }

        @keyframes iconBounce {
            0%, 20%, 60%, 100% { transform: translateY(-50%) scale(1); }
            40% { transform: translateY(-50%) scale(1.2); }
            80% { transform: translateY(-50%) scale(1.1); }
        }

        /* Bouton de connexion 3D */
        .login-button {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            border-radius: 15px;
            color: white;
            font-size: 18px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
            transform: translateZ(15px);
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateZ(15px) translateY(-3px) scale(1.02);
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.6);
        }

        .login-button:active {
            transform: translateZ(15px) translateY(-1px) scale(0.98);
        }

        /* Options supplémentaires */
        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
            transform: translateZ(5px);
        }

        .remember-me {
            display: flex;
            align-items: center;
            color: #94a3b8;
            font-size: 14px;
        }

        .custom-checkbox {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            border: 2px solid #10b981;
            border-radius: 5px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .custom-checkbox.checked {
            background: #10b981;
            animation: checkPulse 0.3s ease;
        }

        .custom-checkbox.checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        @keyframes checkPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .forgot-password {
            color: #10b981;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .forgot-password:hover {
            color: #34d399;
            text-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
        }

        /* Séparateur social */
        .social-divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            color: #64748b;
            font-size: 14px;
        }

        .social-divider::before,
        .social-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #10b981, transparent);
        }

        .social-divider span {
            padding: 0 20px;
            background: transparent;
            color: #e2e8f0;
        }

        /* Boutons sociaux */
        .social-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .social-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.4s ease;
            transform: translate(-50%, -50%);
        }

        .social-btn:hover::before {
            width: 100px;
            height: 100px;
        }

        .google { background: linear-gradient(135deg, #ea4335, #fbbc05); }
        .facebook { background: linear-gradient(135deg, #4267b2, #1877f2); }
        .twitter { background: linear-gradient(135deg, #1da1f2, #0d8bd9); }

        .social-btn:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 30px;
            color: #64748b;
            font-size: 14px;
        }

        .login-footer a {
            color: #10b981;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .login-footer a:hover {
            color: #34d399;
            text-shadow: 0 0 5px rgba(16, 185, 129, 0.5);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-card {
                width: 90%;
                padding: 30px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .badges-container {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
            
            .main-container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Particules 3D -->
    <div class="particles-container" id="particles"></div>
    
    <!-- Effets de lumière -->
    <div class="light-rays"></div>

    <div class="main-container">
        <div class="login-card">
            <div class="login-header">
                <a href="{{ url('/') }}" style="text-decoration: none; display: block;">
                    <div class="logo">
                        <img src="{{ asset('photo_2025-07-02_10-44-47.jpg') }}" alt="Logo ADIS">
                    </div>
                </a>
                <h1>Connexion</h1>
                <p class="subtitle">Accédez à votre espace personnel</p>
            </div>

            <!-- Badges décoratifs supprimés -->

            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Affichage des erreurs -->
                @if ($errors->any())
                    <div class="error-message" style="background: #ef4444; color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-size: 14px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                        <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="success-message" style="background: #10b981; color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-size: 14px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                        <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="form-group">
                    <div class="input-container">
                        <input type="email" name="email" class="form-input @error('email') error-input @enderror" 
                               placeholder="Adresse email" value="{{ old('email') }}" required>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                    @error('email')
                        <div class="error-text" style="color: #ef4444; font-size: 12px; margin-top: 5px; margin-left: 5px;">
                            <i class="fas fa-exclamation-circle" style="margin-right: 4px;"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="input-container">
                        <input type="password" name="password" class="form-input @error('password') error-input @enderror" 
                               placeholder="Mot de passe" required>
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                    @error('password')
                        <div class="error-text" style="color: #ef4444; font-size: 12px; margin-top: 5px; margin-left: 5px;">
                            <i class="fas fa-exclamation-circle" style="margin-right: 4px;"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="login-options">
                    <label class="remember-me">
                        <div class="custom-checkbox" onclick="toggleCheckbox(this)"></div>
                        Se souvenir de moi
                        <input type="checkbox" name="remember" id="remember" style="display:none">
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password">Mot de passe oublié ?</a>
                    @endif
                </div>

                <button type="submit" class="login-button">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <div class="social-divider">
                <span>Ou connectez-vous avec</span>
            </div>

            <div class="social-buttons">
                <button class="social-btn google">
                    <i class="fab fa-google"></i>
                </button>
                <button class="social-btn facebook">
                    <i class="fab fa-facebook-f"></i>
                </button>
                <button class="social-btn twitter">
                    <i class="fab fa-twitter"></i>
                </button>
            </div>

            <div class="login-footer">
                Pas encore de compte ? 
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Créer un compte</a>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Création des particules 3D
        function createParticles() {
            const container = document.getElementById('particles');
            const particleCount = 25;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                const size = Math.random() * 8 + 4;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 8 + 's';
                particle.style.animationDuration = (Math.random() * 6 + 6) + 's';

                container.appendChild(particle);
            }
        }

        // Toggle checkbox personnalisé
        function toggleCheckbox(element) {
            element.classList.toggle('checked');
            const checkbox = document.getElementById('remember');
            if (checkbox) checkbox.checked = element.classList.contains('checked');
        }

        // Animation des inputs
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Gestion du formulaire
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const button = this.querySelector('.login-button');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Connexion...';
            button.style.background = 'linear-gradient(135deg, #059669, #047857)';
            
            // Le formulaire sera soumis normalement pour Laravel
        });

        // Effet de parallaxe sur le mouvement de la souris
        document.addEventListener('mousemove', function(e) {
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;
            
            const loginCard = document.querySelector('.login-card');
            const rotateY = (mouseX - 0.5) * 10;
            const rotateX = (mouseY - 0.5) * -10;
            
            loginCard.style.transform = `rotateY(${rotateY}deg) rotateX(${rotateX}deg)`;
        });

        // Animation d'entrée au chargement
        window.addEventListener('load', function() {
            createParticles();
            
            // Animation séquentielle des éléments
            const elements = document.querySelectorAll('.form-group, .login-options, .social-divider, .social-buttons, .login-footer');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(30px)';
                    el.style.transition = 'all 0.6s ease';
                    
                    setTimeout(() => {
                        el.style.opacity = '1';
                        el.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 200);
            });
        });

        // Effet sonore de clic (simulation)
        document.querySelectorAll('button, .custom-checkbox, a').forEach(element => {
            element.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 100);
            });
        });
    </script>
</body>
</html>