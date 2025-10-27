@extends('admin.layout')

@push('styles')
    <style>
        /* Nature Vivante Styles */
        body.nature-vivante {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #2d5016 0%, #4a7c59 25%, #68b684 50%, #7bc96f 75%, #8fdd8f 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        .background-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: url('https://images.unsplash.com/photo-1441974231531-c6227db76b6e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80') center/cover;
            opacity: 0.3;
            animation: backgroundPulse 8s ease-in-out infinite;
            z-index: 0;
        }
        .color-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: radial-gradient(circle at 50% 50%, rgba(34, 139, 34, 0.4) 0%, rgba(0, 100, 0, 0.6) 100%);
            z-index: 1;
            animation: colorShift 12s ease-in-out infinite;
        }
        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 2;
            pointer-events: none;
        }
        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: radial-gradient(circle, #90EE90, #32CD32);
            border-radius: 50%;
            animation: float 15s infinite linear;
            box-shadow: 0 0 10px rgba(144, 238, 144, 0.8);
        }
        .nature-container {
            position: relative;
            z-index: 3;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .nature-header {
            text-align: center;
            margin-bottom: 3rem;
            animation: slideDown 1.5s ease-out;
        }
        .nature-title {
            font-size: 4rem;
            font-weight: bold;
            background: linear-gradient(45deg, #228B22, #7CFC00, #ADFF2F, #32CD32);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 3s ease-in-out infinite;
            text-shadow: 0 0 30px rgba(124, 252, 0, 0.5);
            margin-bottom: 1rem;
        }
        .nature-subtitle {
            font-size: 1.5rem;
            color: #E6FFE6;
            opacity: 0.9;
            animation: fadeInUp 2s ease-out 0.5s both;
        }
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .nature-card {
            background: rgba(34, 139, 34, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(144, 238, 144, 0.3);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.3s ease;
            animation: cardFloat 6s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }
        .nature-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(124, 252, 0, 0.1), transparent);
            animation: cardShine 4s ease-in-out infinite;
        }
        .nature-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(34, 139, 34, 0.4);
            border-color: rgba(124, 252, 0, 0.6);
        }
        .nature-card h3 {
            color: #7CFC00;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            text-shadow: 0 0 10px rgba(124, 252, 0, 0.5);
        }
        .nature-card p {
            color: #E6FFE6;
            line-height: 1.6;
            opacity: 0.9;
        }
        .nature-action-button {
            display: inline-block;
            background: linear-gradient(45deg, #228B22, #32CD32, #7CFC00);
            background-size: 200% 200%;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            animation: buttonPulse 2s ease-in-out infinite;
            text-decoration: none;
            text-align: center;
            margin: 2rem auto;
            display: block;
            width: fit-content;
            box-shadow: 0 10px 30px rgba(34, 139, 34, 0.4);
            position: relative;
            overflow: hidden;
        }
        .nature-action-button:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(34, 139, 34, 0.6);
            animation: none;
        }
        @keyframes backgroundPulse {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.5; }
        }
        @keyframes colorShift {
            0% { background: radial-gradient(circle at 50% 50%, rgba(34, 139, 34, 0.4) 0%, rgba(0, 100, 0, 0.6) 100%); }
            25% { background: radial-gradient(circle at 30% 70%, rgba(50, 205, 50, 0.4) 0%, rgba(34, 139, 34, 0.6) 100%); }
            50% { background: radial-gradient(circle at 70% 30%, rgba(124, 252, 0, 0.3) 0%, rgba(50, 205, 50, 0.6) 100%); }
            75% { background: radial-gradient(circle at 20% 80%, rgba(173, 255, 47, 0.3) 0%, rgba(124, 252, 0, 0.5) 100%); }
            100% { background: radial-gradient(circle at 50% 50%, rgba(34, 139, 34, 0.4) 0%, rgba(0, 100, 0, 0.6) 100%); }
        }
        @keyframes float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
        }
        @keyframes slideDown {
            0% { transform: translateY(-50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        @keyframes fadeInUp {
            0% { transform: translateY(30px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes cardFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        @keyframes cardShine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(30deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(30deg); }
        }
        @keyframes buttonPulse {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes rippleEffect {
            0% { transform: scale(0); opacity: 1; }
            100% { transform: scale(1); opacity: 0; }
        }
        @media (max-width: 768px) {
            .nature-title { font-size: 2.5rem; }
            .nature-subtitle { font-size: 1.2rem; }
            .nature-container { padding: 1rem; }
            .cards-container { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    <div class="background-overlay"></div>
    <div class="color-overlay"></div>
    <div class="floating-particles" id="particles"></div>
    <div class="nature-container">
        <header class="nature-header">
            <h1 class="nature-title">Nature Vivante</h1>
            <p class="nature-subtitle">Découvrez la beauté de la nature avec des animations époustouflantes</p>
        </header>
        <div class="cards-container">
            <div class="nature-card">
                <h3>🌿 Écosystème</h3>
                <p>Plongez dans un monde verdoyant où chaque élément s'anime avec grâce. Les dégradés de vert créent une atmosphère apaisante et naturelle.</p>
            </div>
            <div class="nature-card">
                <h3>🌱 Croissance</h3>
                <p>Observez les animations fluides qui donnent vie à cette page. Chaque mouvement est pensé pour créer une expérience immersive unique.</p>
            </div>
            <div class="nature-card">
                <h3>🍃 Harmonie</h3>
                <p>Les variations de couleurs vertes s'entremêlent dans un ballet visuel captivant, créant une symphonie de teintes naturelles.</p>
            </div>
        </div>
        <button class="nature-action-button" onclick="createRipple(event)">
            Explorez la Nature
        </button>
    </div>
@endsection

@push('scripts')
    <script>
        // Appliquer la classe body spécifique à cette page
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('nature-vivante');
        });
        // Création des particules flottantes
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            for (let i = 0; i < 15; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                particlesContainer.appendChild(particle);
            }
        }
        // Effet de ripple sur le bouton
        function createRipple(event) {
            const button = event.target;
            const ripple = document.createElement('span');
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = event.clientX - rect.left - size / 2;
            const y = event.clientY - rect.top - size / 2;
            ripple.style.position = 'absolute';
            ripple.style.width = size + 'px';
            ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
            ripple.style.borderRadius = '50%';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'rippleEffect 0.6s ease-out';
            ripple.style.pointerEvents = 'none';
            button.appendChild(ripple);
            setTimeout(() => {
                ripple.remove();
            }, 600);
        }
        // Animation des cartes au scroll
        function animateOnScroll() {
            const cards = document.querySelectorAll('.nature-card');
            cards.forEach((card, index) => {
                const rect = card.getBoundingClientRect();
                if (rect.top < window.innerHeight && rect.bottom > 0) {
                    card.style.animation = `cardFloat 6s ease-in-out infinite ${index * 0.2}s`;
                }
            });
        }
        // Effet de parallaxe sur la couleur overlay
        function parallaxEffect() {
            const scrolled = window.pageYOffset;
            const overlay = document.querySelector('.color-overlay');
            overlay.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            animateOnScroll();
            window.addEventListener('scroll', () => {
                animateOnScroll();
                parallaxEffect();
            });
            setInterval(() => {
                const particles = document.querySelectorAll('.particle');
                particles.forEach(particle => {
                    if (Math.random() < 0.1) {
                        particle.style.animationDelay = '0s';
                    }
                });
            }, 20000);
        });
        // Interaction avec la souris
        document.addEventListener('mousemove', (e) => {
            const overlay = document.querySelector('.color-overlay');
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;
            overlay.style.background = `radial-gradient(circle at ${x * 100}% ${y * 100}%, rgba(34, 139, 34, 0.4) 0%, rgba(0, 100, 0, 0.6) 100%)`;
        });
    </script>
@endpush
