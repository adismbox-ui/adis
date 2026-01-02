<!DOCTYPE html>
<html lang="fr">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADIS - Académie pour la Diffusion de l'Islam et de la Science</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }

        /* Enhanced Color Palette */
        :root {
            --primary-green: #4caf50;
            --secondary-green: #388e3c;
            --dark-green: #256029;
            --light-green: #81c784;
            --mint-green: #a5d6a7;
            --forest-green: #256029;
            --lime-green: #cddc39;
            --emerald-green: #4caf50;
            --teal-green: #26a69a;
            --sage-green: #66bb6a;
            --gradient-primary: linear-gradient(135deg, var(--primary-green), var(--secondary-green), var(--teal-green));
            --shadow-light: 0 4px 20px rgba(76, 175, 80, 0.1);
            --shadow-medium: 0 8px 30px rgba(76, 175, 80, 0.2);
            --shadow-heavy: 0 20px 40px rgba(76, 175, 80, 0.3);
            --transition-smooth: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-bounce: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        /* 3D Animation Keyframes */
        @keyframes float3D {
            0%, 100% { 
                transform: translateY(0px) rotateX(0deg) rotateY(0deg);
            }
            25% { 
                transform: translateY(-10px) rotateX(5deg) rotateY(5deg);
            }
            50% { 
                transform: translateY(-20px) rotateX(0deg) rotateY(10deg);
            }
            75% { 
                transform: translateY(-10px) rotateX(-5deg) rotateY(5deg);
            }
        }

        @keyframes rotate3D {
            0% { transform: rotateY(0deg); }
            100% { transform: rotateY(360deg); }
        }

        @keyframes pulse3D {
            0%, 100% { 
                transform: scale(1) rotateZ(0deg);
                box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
            }
            50% { 
                transform: scale(1.05) rotateZ(2deg);
                box-shadow: 0 0 40px rgba(16, 185, 129, 0.6);
            }
        }

        @keyframes morphing {
            0%, 100% { border-radius: 20px; }
            25% { border-radius: 50px 20px 50px 20px; }
            50% { border-radius: 50px; }
            75% { border-radius: 20px 50px 20px 50px; }
        }

        /* Enhanced Header with 3D Effects */
        .adis-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(76, 175, 80, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: none !important;
            margin-bottom: 0 !important;
            box-shadow: none !important;
            transition: var(--transition-smooth);
            padding: 1rem 0;
            box-shadow: 0 4px 24px rgba(16,185,129,0.10);
        }

        .adis-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .adis-logo img {
            width: 48px; height: 48px; border-radius: 50%; object-fit: cover;
        }

        .adis-logo span {
            color: #fff;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .adis-menu {
            display: flex;
            gap: 0.2rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .adis-menu > li {
            list-style: none;
            position: relative;
        }

        .adis-menu > li > a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            padding: 7px 13px;
            border-radius: 18px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-size: 0.89rem;
            display: flex;
            align-items: center;
            gap: 6px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(145deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.13);
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            min-width: unset;
            cursor: pointer;
        }

        .adis-menu > li > a::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, var(--primary-green) 0%, var(--mint-green) 100%);
            opacity: 0.18;
            transform: translate(-50%, -50%);
            transition: width 0.4s cubic-bezier(0.4,0,0.2,1), height 0.4s cubic-bezier(0.4,0,0.2,1);
            z-index: 0;
        }
        .adis-menu > li > a:hover::before, .adis-menu > li > a:focus::before {
            width: 220%;
            height: 500%;
        }
        .adis-menu > li > a:hover, .adis-menu > li > a.active {
            background: linear-gradient(90deg, var(--primary-green), var(--mint-green));
            color: #fff;
            transform: translateY(-3px) scale(1.07) rotateZ(-1deg);
            box-shadow: 0 6px 24px rgba(76,175,80,0.18), 0 1.5px 8px rgba(0,0,0,0.10);
            animation: menuGlow 1.2s alternate infinite;
        }
        @keyframes menuGlow {
            0% { box-shadow: 0 6px 24px rgba(76,175,80,0.18), 0 1.5px 8px rgba(0,0,0,0.10); }
            100% { box-shadow: 0 12px 36px rgba(76,175,80,0.28), 0 3px 16px rgba(0,0,0,0.13); }
        }
        .adis-menu > li > a:active {
            animation: menuBounce 0.3s;
        }
        @keyframes menuBounce {
            0% { transform: scale(1.07) rotateZ(-1deg); }
            50% { transform: scale(0.95) rotateZ(1deg); }
            100% { transform: scale(1.07) rotateZ(-1deg); }
        }
        /* Effet ripple au clic */
        .adis-menu > li > a .ripple {
            position: absolute;
            border-radius: 50%;
            transform: scale(0);
            animation: ripple-effect 0.6s linear;
            background: rgba(255,255,255,0.4);
            pointer-events: none;
            z-index: 1;
        }
        @keyframes ripple-effect {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        .adis-menu .has-submenu:hover .adis-submenu {
            display: block;
        }

        .adis-submenu {
            display: none;
            position: absolute;
            left: 0; top: 110%;
            background: #fff;
            min-width: 200px;
            box-shadow: 0 8px 32px rgba(16,185,129,0.13);
            border-radius: 16px;
            z-index: 1001;
            opacity: 0;
            transform: translateY(20px) scale(0.98);
            pointer-events: none;
            transition: opacity 0.35s cubic-bezier(0.4,0,0.2,1), transform 0.35s cubic-bezier(0.4,0,0.2,1);
        }
        .adis-menu .has-submenu:hover .adis-submenu,
        .adis-menu .has-submenu:focus-within .adis-submenu {
            display: block;
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }
        .adis-submenu li {
            list-style: none;
        }
        .adis-submenu a {
            display: block;
            color: var(--secondary-green);
            padding: 0.85rem 1.4rem;
            text-decoration: none;
            font-weight: 500;
            border-radius: 10px;
            margin: 0.15rem 0.5rem;
            background: linear-gradient(90deg, rgba(76,175,80,0.04), rgba(165,214,167,0.07));
            box-shadow: 0 1.5px 8px rgba(76,175,80,0.06);
            transition: background 0.25s, color 0.25s, transform 0.25s, box-shadow 0.25s;
            position: relative;
            overflow: hidden;
        }
        .adis-submenu a::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, var(--primary-green) 0%, var(--mint-green) 100%);
            opacity: 0.13;
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
            z-index: 0;
        }
        .adis-submenu a:hover, .adis-submenu a:focus {
            background: linear-gradient(90deg, var(--primary-green), var(--mint-green));
            color: #fff;
            transform: translateX(8px) scale(1.04) skewX(-3deg);
            box-shadow: 0 4px 18px rgba(76,175,80,0.13);
        }
        .adis-submenu a:hover::after, .adis-submenu a:focus::after {
            width: 220%;
            height: 500%;
        }
        /* Animation d'apparition des liens du sous-menu */
        .adis-menu .has-submenu:hover .adis-submenu a,
        .adis-menu .has-submenu:focus-within .adis-submenu a {
            animation: submenuFadeIn 0.5s cubic-bezier(0.4,0,0.2,1) both;
        }
        @keyframes submenuFadeIn {
            0% { opacity: 0; transform: translateY(20px) scale(0.98); }
            100% { opacity: 1; transform: translateY(0) scale(1.04); }
        }

        .adis-search {
            position: relative;
            display: flex;
            align-items: center;
            background: none;
            border-radius: 30px;
            padding: 0;
            border: none;
            box-shadow: none;
        }
        .adis-search input {
            width: 0;
            opacity: 0;
            border: none;
            outline: none;
            font-size: 1rem;
            background: rgba(255,255,255,0.95);
            color: #333;
            border-radius: 30px;
            padding: 8px 0;
            margin-left: 0;
            transition: width 0.4s, opacity 0.4s, margin-left 0.4s;
            box-shadow: 0 2px 8px rgba(76,175,80,0.08);
        }
        .adis-search.active input {
            width: 180px;
            opacity: 1;
            margin-left: 10px;
            padding: 8px 18px;
        }
        .adis-search button {
            background: #fff;
            border: none;
            color: var(--primary-green);
            font-size: 1.3rem;
            cursor: pointer;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(76,175,80,0.10);
            transition: background 0.3s, color 0.3s, box-shadow 0.3s;
        }
        .adis-search button:hover {
            background: var(--primary-green);
            color: #fff;
            box-shadow: 0 4px 16px rgba(76,175,80,0.18);
        }
        @media (max-width: 768px) {
            .adis-search.active input {
                width: 120px;
            }
        }

        /* HERO */
        .adis-hero {
            margin-top: 0 !important;
            padding-top: 0 !important;
            min-height: 70vh;
            background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%), url('/ad.jpg') center/cover no-repeat;
            background-blend-mode: overlay;
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .adis-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(16,185,129,0.45);
        }

        .adis-hero-content {
            position: relative;
            z-index: 1000;
        }

        .adis-hero-content h1 {
            position: relative;
            z-index: 1100;
            font-size: 3.2rem;
            font-weight: 900;
            margin-bottom: 1.2rem;
            padding: 0.7em 1.2em;
            color: #fff !important;
            background: none !important;
            -webkit-background-clip: unset !important;
            -webkit-text-fill-color: unset !important;
            background-clip: unset !important;
            text-shadow: 0 8px 32px rgba(0,0,0,0.85), 0 2px 8px rgba(16,185,129,0.18);
            box-shadow: none;
            animation: none !important;
        }
        @keyframes textGlow {
            0% { text-shadow: 0 8px 32px rgba(0,0,0,0.65), 0 2px 8px rgba(16,185,129,0.18); }
            100% { text-shadow: 0 12px 48px rgba(0,0,0,0.75), 0 2px 8px rgba(16,185,129,0.18); }
        }

        .adis-hero-content p {
            font-size: 1.3rem;
            font-weight: 400;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 8px rgba(0,0,0,0.13);
        }

        /* PRESENTATION */
        .adis-presentation {
            background: #fff;
            margin: 0 auto;
            max-width: 700px;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(16,185,129,0.08);
            padding: 2.5rem 2rem 2rem 2rem;
            margin-top: -60px;
            position: relative;
            z-index: 3;
            text-align: center;
        }

        .adis-presentation p {
            font-size: 1.15rem;
            margin-bottom: 2rem;
            color: #222;
        }

        .adis-actions {
            display: flex;
            justify-content: center;
            gap: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .adis-actions a, .btn-voir-plus, .btn-cta, .btn-don-premium {
            padding: 0.95rem 2.4rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.08rem;
            text-decoration: none;
            border: none;
            background: linear-gradient(100deg, var(--primary-green), var(--mint-green), var(--teal-green));
            color: #fff !important;
            box-shadow: 0 4px 18px rgba(76,175,80,0.18), 0 1.5px 8px rgba(0,0,0,0.10);
            transition: all 0.3s cubic-bezier(.4,2,.3,1), box-shadow 0.3s cubic-bezier(.4,2,.3,1);
            position: relative;
            overflow: hidden;
            perspective: 600px;
            cursor: pointer;
        }
        .adis-actions a.btn-don, .btn-don-premium {
            background: linear-gradient(90deg, #fbbf24, var(--primary-green), var(--mint-green));
            color: #fff !important;
        }
        .adis-actions a.btn-mobile {
            background: linear-gradient(90deg, #667eea, #764ba2, var(--primary-green));
            color: #fff !important;
            animation: pulse-mobile 2s ease-in-out infinite;
        }
        @keyframes pulse-mobile {
            0%, 100% {
                box-shadow: 0 4px 18px rgba(102, 126, 234, 0.18), 0 1.5px 8px rgba(0,0,0,0.10);
            }
            50% {
                box-shadow: 0 8px 28px rgba(102, 126, 234, 0.35), 0 3px 12px rgba(0,0,0,0.15);
            }
        }
        .adis-actions a.btn-mobile:hover, .adis-actions a.btn-mobile:focus {
            background: linear-gradient(90deg, #764ba2, #667eea, var(--mint-green));
            animation: none;
        }
        .adis-actions a:hover, .btn-voir-plus:hover, .btn-cta:hover, .btn-don-premium:hover, .adis-actions a:focus, .btn-voir-plus:focus, .btn-cta:focus, .btn-don-premium:focus {
            background: linear-gradient(100deg, var(--mint-green), var(--primary-green), var(--teal-green));
            color: #fff;
            transform: translateY(-4px) scale(1.07) rotateX(8deg) skewY(-2deg);
            box-shadow: 0 12px 36px rgba(76,175,80,0.28), 0 3px 16px rgba(0,0,0,0.13);
            filter: brightness(1.08) saturate(1.2);
        }
        .adis-actions a:active, .btn-voir-plus:active, .btn-cta:active, .btn-don-premium:active {
            transform: scale(0.97) rotateX(-6deg) skewY(2deg);
            box-shadow: 0 2px 8px rgba(76,175,80,0.13);
        }
        .adis-actions a::before, .btn-voir-plus::before, .btn-cta::before, .btn-don-premium::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, var(--primary-green) 0%, var(--mint-green) 100%);
            opacity: 0.13;
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
            z-index: 0;
        }
        .adis-actions a:hover::before, .btn-voir-plus:hover::before, .btn-cta:hover::before, .btn-don-premium:hover::before, .adis-actions a:focus::before, .btn-voir-plus:focus::before, .btn-cta:focus::before, .btn-don-premium:focus::before {
            width: 220%;
            height: 500%;
        }
        /* Effet ripple au clic pour tous les boutons principaux */
        .adis-actions a .ripple, .btn-voir-plus .ripple, .btn-cta .ripple, .btn-don-premium .ripple {
            position: absolute;
            border-radius: 50%;
            transform: scale(0);
            animation: ripple-effect 0.6s linear;
            background: rgba(255,255,255,0.4);
            pointer-events: none;
            z-index: 1;
        }
        @keyframes ripple-effect {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        /* JS pour effet ripple sur tous les boutons principaux */
        document.querySelectorAll('.adis-actions a, .btn-voir-plus, .btn-cta, .btn-don-premium').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const circle = document.createElement('span');
                circle.classList.add('ripple');
                const diameter = Math.max(btn.clientWidth, btn.clientHeight);
                circle.style.width = circle.style.height = `${diameter}px`;
                circle.style.left = `${e.clientX - btn.getBoundingClientRect().left - diameter/2}px`;
                circle.style.top = `${e.clientY - btn.getBoundingClientRect().top - diameter/2}px`;
                btn.appendChild(circle);
                circle.addEventListener('animationend', () => circle.remove());
            });
        });

        @media (max-width: 900px) {
            .adis-header { flex-direction: column; gap: 0.7rem; }
            .adis-menu { flex-wrap: wrap; }
            .adis-presentation { margin-top: 0; }
        }

        @media (max-width: 600px) {
            .adis-header { flex-direction: column; padding: 0.5rem 0.5rem; }
            .adis-menu { flex-direction: column; gap: 0.2rem; }
            .adis-hero-content h1 { font-size: 1.5rem; }
            .adis-presentation { padding: 1.2rem 0.5rem; }
            .adis-actions { flex-direction: column; gap: 0.7rem; }
        }

        /* Enhanced Hero Section with 3D Elements */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 25%, var(--teal-green) 50%, var(--dark-green) 75%, var(--forest-green) 100%);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding-top: 120px;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255,255,255,0.05) 0%, transparent 50%);
            animation: float3D 8s ease-in-out infinite;
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .hero-content h1 {
            font-size: 4rem;
            font-weight: 900;
            color: white;
            margin-bottom: 2rem;
            line-height: 1.2;
            text-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            position: relative;
            transform-style: preserve-3d;
            background: rgba(0,0,0,0.35);
            border-radius: 18px;
            padding: 18px 32px;
            display: inline-block;
        }

        .hero-content p {
            font-size: 1.4rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 3rem;
            line-height: 1.8;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 4rem;
        }

        .stat-item {
            text-align: center;
            color: white;
            padding: 2rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            border-radius: 25px;
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.4s ease;
            transform-style: preserve-3d;
            position: relative;
            overflow: hidden;
        }

        .stat-item::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: rotate3D 4s linear infinite;
        }

        .stat-item:hover {
            transform: translateY(-10px) rotateX(10deg) rotateY(5deg);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 900;
            display: block;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            z-index: 2;
        }

        /* Enhanced Floating Cards with 3D Effects */
        .hero-visual {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        .floating-cards {
            position: relative;
            width: 100%;
            height: 600px;
            transform-style: preserve-3d;
        }

        .floating-card {
            position: absolute;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
            border-radius: 25px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            animation: float3D 6s ease-in-out infinite;
            transition: all 0.4s ease;
            transform-style: preserve-3d;
        }

        .floating-card:hover {
            transform: translateY(-20px) rotateX(10deg) rotateY(10deg) scale(1.05);
            box-shadow: 0 35px 70px rgba(0, 0, 0, 0.3);
        }

        .floating-card:nth-child(1) {
            top: 10%;
            left: 5%;
            animation-delay: 0s;
            background: linear-gradient(135deg, var(--mint-green), rgba(255, 255, 255, 0.9));
        }

        .floating-card:nth-child(2) {
            top: 40%;
            right: 5%;
            animation-delay: 2s;
            background: linear-gradient(135deg, var(--light-green), rgba(255, 255, 255, 0.9));
        }

        .floating-card:nth-child(3) {
            bottom: 10%;
            left: 15%;
            animation-delay: 4s;
            background: linear-gradient(135deg, var(--lime-green), rgba(255, 255, 255, 0.9));
        }

        .card-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green), var(--teal-green));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            margin-bottom: 1.5rem;
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
            animation: morphing 4s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .card-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: rotate(45deg);
            animation: shine 2s linear infinite;
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: bold;
            color: var(--secondary-green);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card-text {
            font-size: 1rem;
            color: #555;
            line-height: 1.6;
        }

        /* Enhanced Carousel Section with Images */
        .carousel-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5, #d1fae5);
            position: relative;
            overflow: hidden;
        }

        .carousel-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
            animation: float3D 10s ease-in-out infinite;
        }

        .section-title {
            text-align: center;
            margin-bottom: 5rem;
            position: relative;
            z-index: 2;
        }

        .section-title h2 {
            font-size: 3.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green), var(--teal-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 5px;
            background: linear-gradient(90deg, var(--mint-green), var(--light-green));
            border-radius: 3px;
            animation: pulse3D 2s ease-in-out infinite;
        }

        .section-title p {
            font-size: 1.3rem;
            color: #666;
            max-width: 700px;
            margin: 0 auto;
        }

        .carousel-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 2;
        }

        .carousel {
            overflow: hidden;
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(5, 150, 105, 0.3);
            position: relative;
            transform-style: preserve-3d;
        }

        .carousel-track {
            display: flex;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .carousel-slide {
            min-width: 100%;
            height: 500px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 4rem;
            overflow: hidden;
        }

        /* Individual slide backgrounds with images */
        .carousel-slide:nth-child(1) {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.9), rgba(5, 150, 105, 0.9)), 
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%2310b981" width="400" height="300"/><circle cx="100" cy="100" r="50" fill="%23059669" opacity="0.5"/><circle cx="300" cy="200" r="60" fill="%2334d399" opacity="0.3"/><path d="M50 150 Q200 50 350 150 Q200 250 50 150" fill="%23ffffff" opacity="0.1"/></svg>');
            background-size: cover;
            background-position: center;
        }

        .carousel-slide:nth-child(2) {
            background: linear-gradient(135deg, rgba(20, 184, 166, 0.9), rgba(13, 148, 136, 0.9)), 
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%2314b8a6" width="400" height="300"/><polygon points="200,50 250,150 150,150" fill="%23ffffff" opacity="0.2"/><circle cx="200" cy="200" r="40" fill="%236ee7b7" opacity="0.4"/><rect x="50" y="50" width="60" height="60" fill="%23ffffff" opacity="0.1" rx="10"/></svg>');
            background-size: cover;
            background-position: center;
        }

        .carousel-slide:nth-child(3) {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.9), rgba(21, 128, 61, 0.9)), 
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%2322c55e" width="400" height="300"/><ellipse cx="200" cy="150" rx="100" ry="50" fill="%23ffffff" opacity="0.2"/><path d="M100 100 L300 100 L200 200 Z" fill="%2384cc16" opacity="0.3"/><circle cx="80" cy="80" r="20" fill="%23ffffff" opacity="0.3"/></svg>');
            background-size: cover;
            background-position: center;
        }

        .carousel-slide:nth-child(4) {
            background: linear-gradient(135deg, rgba(132, 204, 22, 0.9), rgba(101, 163, 13, 0.9)), 
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%2384cc16" width="400" height="300"/><path d="M0 150 Q100 50 200 150 Q300 250 400 150 L400 300 L0 300 Z" fill="%23ffffff" opacity="0.1"/><circle cx="350" cy="100" r="30" fill="%23ffffff" opacity="0.2"/><rect x="50" y="200" width="80" height="20" fill="%23ffffff" opacity="0.3" rx="10"/></svg>');
            background-size: cover;
            background-position: center;
        }

        .slide-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            transform-style: preserve-3d;
        }

        .slide-content h3 {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 2rem;
            text-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .slide-content p {
            font-size: 1.3rem;
            opacity: 0.95;
            max-width: 700px;
            margin: 0 auto 2rem;
            line-height: 1.8;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
            border: none;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 24px;
            color: var(--secondary-green);
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            z-index: 3;
        }

        .carousel-nav:hover {
            background: linear-gradient(135deg, white, var(--mint-green));
            transform: translateY(-50%) scale(1.1) rotateZ(10deg);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .carousel-prev {
            left: 30px;
        }

        .carousel-next {
            right: 30px;
        }

        .carousel-dots {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 3rem;
        }

        .carousel-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #d1d5db;
            cursor: pointer;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .carousel-dot::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            border-radius: 50%;
            transform: scale(0);
            transition: transform 0.4s ease;
        }

        .carousel-dot.active::before {
            transform: scale(1);
        }

        .carousel-dot.active {
            transform: scale(1.3);
        }

        /* Enhanced Features Section */
        .features-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, white, #f9fafb, #f3f4f6);
            position: relative;
        }

        .features-grid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 3rem;
        }

        .feature-card {
            background: linear-gradient(135deg, #ffffff, #f0fdf4, #ecfdf5);
            padding: 3rem 2.5rem;
            border-radius: 30px;
            text-align: center;
            transition: all 0.5s ease;
            border: 2px solid rgba(16, 185, 129, 0.1);
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(16, 185, 129, 0.1), transparent);
            animation: rotate3D 8s linear infinite;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-card:hover {
            transform: translateY(-15px) rotateX(5deg) rotateY(5deg);
            box-shadow: 0 30px 60px rgba(16, 185, 129, 0.3);
            border-color: var(--primary-green);
        }

        .feature-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green), var(--teal-green));
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
            margin: 0 auto 2rem;
            box-shadow: 0 15px 40px rgba(16, 185, 129, 0.4);
            position: relative;
            overflow: hidden;
            animation: morphing 6s ease-in-out infinite;
        }

        .feature-icon::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: rotate(45deg);
            animation: shine 3s linear infinite;
        }

        .feature-title {
            font-size: 1.6rem;
            font-weight: bold;
            color: var(--secondary-green);
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .feature-text {
            color: #666;
            line-height: 1.8;
            font-size: 1.1rem;
        }

        /* Enhanced CTA Section */
        .cta-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, var(--secondary-green), var(--dark-green), var(--forest-green));
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: rotate3D 20s linear infinite;
        }

        @keyframes rotate3D {
            0% { transform: rotateY(0deg); }
            100% { transform: rotateY(360deg); }
        }

        .cta-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 2;
        }

        .cta-content h2 {
            font-size: 3rem;
            font-weight: bold;
            color: white;
            margin-bottom: 1.5rem;
        }

        .cta-content p {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 3rem;
        }

        .cta-buttons {
            display: flex;
            gap: 2rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-cta {
            padding: 18px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-cta-primary {
            background: white;
            color: #059669;
        }

        .btn-cta-primary:hover {
            background: #f0fdf4;
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(255, 255, 255, 0.3);
        }

        .btn-cta-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-cta-secondary:hover {
            background: white;
            color: #059669;
            transform: translateY(-3px);
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #1f2937, #374151, #4caf50 80%);
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
        }

        .footer-section h3 {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: var(--primary-green);
        }

        .footer-section p,
        .footer-section a {
            color: #d1d5db;
            text-decoration: none;
            line-height: 1.8;
        }

        .footer-section a:hover {
            color: var(--primary-green);
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            background: rgba(76, 175, 80, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #d1d5db;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: var(--primary-green);
            border-color: var(--primary-green);
            color: white;
            transform: translateY(-2px);
        }

        .footer-bottom {
            border-top: 1px solid #374151;
            margin-top: 2rem;
            padding-top: 1rem;
            text-align: center;
            color: #9ca3af;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .adis-header { flex-direction: column; gap: 0.7rem; }
            .adis-menu { flex-wrap: wrap; }
            .adis-presentation { margin-top: 0; }
        }

        /* Ajout d'un style premium pour le bouton Faire un don */
        .btn-don-premium {
            background: linear-gradient(90deg, #fbbf24, #10b981, #34d399);
            color: #fff !important;
            font-size: 1.2rem;
            font-weight: bold;
            border: none;
            border-radius: 50px;
            box-shadow: 0 6px 24px rgba(16,185,129,0.25);
            padding: 16px 44px;
            transition: all 0.3s cubic-bezier(.4,2,.3,1);
            position: relative;
            overflow: hidden;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 18px;
            z-index: 2;
        }
        .btn-don-premium i {
            margin-right: 10px;
            font-size: 1.3em;
        }
        .btn-don-premium:hover, .btn-don-premium:focus {
            background: linear-gradient(90deg, #10b981, #fbbf24, #34d399);
            color: #fff;
            transform: scale(1.07) translateY(-3px) rotateZ(-2deg);
            box-shadow: 0 12px 32px rgba(251,191,36,0.25), 0 2px 12px rgba(16,185,129,0.18);
        }

        .trait-hero {
            width: 120px;
            height: 5px;
            background: linear-gradient(90deg, var(--mint-green), var(--light-green), var(--lime-green));
            border-radius: 3px;
            margin: 18px auto 28px auto;
            box-shadow: 0 2px 12px rgba(16,185,129,0.25);
            animation: pulse3D 2s ease-in-out infinite;
        }

        .mega-banner-nav {
            width: 100%;
            padding: 0;
            margin: 0;
            border-bottom: 2px solid rgba(255,255,255,0.08);
            animation: float3D 8s ease-in-out infinite;
            margin-top: 120px;
            min-height: 48px;
            overflow-x: auto;
        }
        .mega-nav-list {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            list-style: none;
            margin: 0;
            padding: 0.2rem 0.2rem;
            position: relative;
            flex-wrap: nowrap;
        }
        .mega-nav-btn {
            color: #fff;
            font-size: 0.92rem;
            font-weight: 600;
            padding: 0.45rem 1.1rem;
            border-radius: 0;
            background: rgba(255,255,255,0.07);
            text-decoration: none;
            transition: all 0.25s cubic-bezier(.4,2,.3,1);
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            box-shadow: none;
            min-width: 120px;
            justify-content: center;
        }
        .mega-nav-btn.active, .mega-nav-btn:hover {
            background: linear-gradient(90deg, var(--mint-green), var(--light-green), var(--lime-green));
            color: var(--secondary-green);
            transform: scale(1.07) translateY(-2px);
            box-shadow: 0 6px 24px rgba(16,185,129,0.15);
        }
        .has-submenu {
            position: relative;
        }
        .submenu-arrow {
            font-size: 0.8em;
            margin-left: 0.3em;
        }
        .mega-submenu {
            display: none;
            position: absolute;
            left: 0;
            top: 110%;
            background: linear-gradient(135deg, #fff, var(--mint-green), var(--light-green));
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(16,185,129,0.13);
            min-width: 210px;
            padding: 0.7rem 0;
            z-index: 1001;
            animation: float3D 8s ease-in-out infinite;
        }
        .has-submenu:hover .mega-submenu, .has-submenu:focus-within .mega-submenu {
            display: block;
        }
        .mega-submenu li {
            list-style: none;
        }
        .mega-submenu a {
            display: block;
            color: var(--secondary-green);
            padding: 0.7rem 1.7rem;
            text-decoration: none;
            font-weight: 500;
            border-radius: 12px;
            transition: background 0.2s, color 0.2s;
        }
        .mega-submenu a:hover {
            background: var(--primary-green);
            color: #fff;
        }
        @media (max-width: 900px) {
            .mega-nav-list {
                gap: 0.2rem;
            }
            .mega-nav-btn {
                font-size: 0.95rem;
                padding: 0.35rem 0.7rem;
            }
            .mega-submenu {
                min-width: 150px;
            }
        }
        @media (max-width: 600px) {
            .mega-nav-list {
                flex-direction: row;
                gap: 0.1rem;
            }
            .mega-banner-nav {
                padding-bottom: 0.5rem;
                min-height: 40px;
            }
        }
        .mega-nav-list-secondary {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            list-style: none;
            margin: 0;
            padding: 0.2rem 0.2rem 0.2rem 0.2rem;
            position: relative;
            flex-wrap: nowrap;
            width: 100%;
        }

        /* Améliorations Hero Section */
        .adis-hero {
            position: relative;
            perspective: 1000px;
            transform-style: preserve-3d;
        }
        .adis-hero-content {
            animation: heroFloat 8s ease-in-out infinite;
            transform-style: preserve-3d;
        }
        @keyframes heroFloat {
            0%, 100% { 
                transform: translateY(0px) rotateX(0deg);
            }
            50% { 
                transform: translateY(-20px) rotateX(2deg);
            }
        }
        /* Particules flottantes améliorées */
        .hero-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: linear-gradient(45deg, rgba(255,255,255,0.8), rgba(16,185,129,0.6));
            border-radius: 50%;
            animation: particleFloat 15s linear infinite;
        }
        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) rotate(0deg) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
                transform: scale(1);
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg) scale(0);
                opacity: 0;
            }
        }
        /* Texte avec effet 3D */
        .adis-hero-content h1 {
            background: linear-gradient(45deg, #fff, #f0f9ff, #e0f2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
            animation: textGlow 4s ease-in-out infinite alternate;
        }
        @keyframes textGlow {
            0% {
                filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3)) drop-shadow(0 0 20px rgba(255,255,255,0.1));
            }
            100% {
                filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3)) drop-shadow(0 0 30px rgba(255,255,255,0.3));
            }
        }
        /* Trait décoratif 3D */
        .trait-hero {
            position: relative;
            overflow: hidden;
        }
        .trait-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shimmer 2s infinite;
        }
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        .whatsapp-float {
            position: fixed;
            right: 28px;
            bottom: 32px;
            z-index: 2000;
            background: #4caf50;
            color: #fff;
            width: 62px;
            height: 62px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(76,175,80,0.25);
            font-size: 2.2rem;
            cursor: pointer;
            transition: box-shadow 0.3s, transform 0.3s;
            animation: whatsappYoyo 2.5s ease-in-out infinite alternate;
        }
        .whatsapp-float:hover {
            box-shadow: 0 16px 48px rgba(76,175,80,0.35);
            transform: scale(1.08) rotate(-8deg);
            background: #388e3c;
        }
        @keyframes whatsappYoyo {
            0% { bottom: 32px; }
            50% { bottom: 52px; }
            100% { bottom: 32px; }
        }
    </style>
    </head>

<body>
    <!-- HEADER -->
    <header class="adis-header">
        <div class="adis-logo">
            <img src="/photo_2025-07-02_10-44-47.jpg" alt="Logo ADIS" />
            <span>ADIS</span>
        </div>
        <ul class="adis-menu" id="menu">
            <li><a href="/" class="active"><i class="fas fa-home"></i> Accueil</a></li>
            <li>
                <a href="/qui-sommes-nous"><i class="fas fa-users"></i> Qui sommes-nous ?</a>
            </li>
            <li class="has-submenu">
                <a href="/formations"><i class="fas fa-graduation-cap"></i> Formation <i class="fas fa-chevron-down"></i></a>
                <ul class="adis-submenu">
                    <li><a href="/formations#nos-formations">Nos formations</a></li>
                    <li><a href="/formations#formation-domicile">Formation à domicile</a></li>
                    <li><a href="/formations#ressources">Ressources</a></li>
                </ul>
            </li>
            <li><a href="/projets"><i class="fas fa-briefcase"></i> Business</a></li>
            <li><a href="/marketplace"><i class="fas fa-store"></i> Market place</a></li>
            <li><a href="/vie-associative"><i class="fas fa-hands-helping"></i> Vie associative</a></li>
            <li><a href="/actualites"><i class="fas fa-newspaper"></i> Actualités</a></li>
        </ul>
        <div class="adis-search" id="adis-search">
            <button type="button" id="search-toggle" aria-label="Rechercher"><i class="fas fa-search"></i></button>
            <input type="text" placeholder="Rechercher..." id="search-input" />
        </div>
    </header>

    <!-- Hero Section -->
    <section class="adis-hero" id="hero">
        <div class="hero-particles" id="particles"></div>
        <div class="adis-hero-content" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 45vh;">
            <h1>Bienvenue chez ADIS</h1>
        </div>
    </section>

    <!-- PRESENTATION + BOUTONS -->
    <section class="adis-presentation scroll-reveal" id="presentation">
        <p>Nous sommes très ravis de vous accueillir parmi nous et aussi disponible à vous accompagner dans votre développement personnel par nos formations, opportunités d'affaires et actions de bienfaisance.

Préparez-vous à acquérir de nouvelles compétences et ainsi atteindre vos objectifs avec nous.
</p>
<p>
N'hésitez pas à nous contacter au besoin pour plus d'informations.

</p>
@if(session('app-download-info'))
    <div style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 1rem; border-radius: 10px; margin: 1rem 0; text-align: center; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
        <i class="fas fa-info-circle"></i> {{ session('app-download-info') }}
    </div>
@endif
        <div class="adis-actions">
            <a href="/register" class="btn-inscription">
                <i class="fas fa-user-plus"></i>
                S'inscrire
            </a>
            <a href="/login" class="btn-connexion">
                <i class="fas fa-sign-in-alt"></i>
                Se connecter
            </a>
            <a href="/download-app" class="btn-mobile" id="btn-download-app">
                <i class="fas fa-mobile-alt"></i>
                Télécharger l'app
            </a>
            <a href="/projets/don" class="btn-don">
                <i class="fas fa-donate"></i>
                Faire un don
            </a>
        </div>
    </section>

    <!-- FOOTER (conservé) -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>ADIS</h3>
                <p>Académie pour la Diffusion de l'Islam et de la Science. Votre partenaire pour un apprentissage de qualité.</p>
                <div class="social-links">
                    <a href="https://www.facebook.com/groups/adis.org/" class="social-link">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://wa.me/message/URJVLNOBZL43D1" class="social-link">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Nos actions</h3>
                <a href="/formations">Formation en ligne</a><br>
                <a href="/cours-domicile">Cours à domicile</a><br>
                <a href="/traduction">Traduction</a><br>
                <a href="/interpretariat">Interprétariat</a><br>
                <a href="/edition">Édition d'ouvrages</a><br>
                <a href="/projets">Mise en relations d'affaires</a>
            </div>
            
            <div class="footer-section">
                <h3>Nos formations</h3>
                <a href="/formations/langue-arabe">Langue Arabe</a><br>
                <a href="/formations/tajwid">Lecture et mémorisation du coran</a><br>
                <a href="/formations/tahfiz">Science du Hadith</a><br>
                <a href="/formations/tafsir">Jurisprudence</a><br>
                <a href="/formations/hadith">Education islamique</a><br>
                <a href="/formations/fiqh">Finance islamique</a>
            </div>
            
            <div class="footer-section">
                <h3>Contact</h3>
                <p><i class="fas fa-envelope"></i> <a href="mailto:adis.mbox@gmail.com">adis.mbox@gmail.com</a></p>
                <p><i class="fas fa-phone"></i> <a href="tel:+2250704830462">+225 0704830462</a></p>
                <p><i class="fas fa-globe"></i> <a href="https://www.adis.org" target="_blank">www.adis.org</a></p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2024 ADIS - Académie pour la Diffusion de l'Islam et de la Science. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Curseur personnalisé -->
    <div class="cursor"></div>
    <!-- WhatsApp flottant -->
    <a href="https://wa.me/message/URJVLNOBZL43D1" class="whatsapp-float" target="_blank" title="Contactez-nous sur WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Carousel functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const totalSlides = slides.length;
        const track = document.getElementById('carousel-track');
        const dotsContainer = document.getElementById('carousel-dots');

        // Create dots
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('div');
            dot.classList.add('carousel-dot');
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(i));
            dotsContainer.appendChild(dot);
        }

        const dots = document.querySelectorAll('.carousel-dot');

        function updateCarousel() {
            track.style.transform = `translateX(-${currentSlide * 100}%)`;
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateCarousel();
        }

        function goToSlide(index) {
            currentSlide = index;
            updateCarousel();
        }

        // Auto-play carousel
        setInterval(nextSlide, 5000);

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add hover effects to buttons
        document.querySelectorAll('.btn, .btn-cta').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.02)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Mobile menu toggle (if needed)
        const mobileBreakpoint = 768;
        
        function handleResize() {
            if (window.innerWidth <= mobileBreakpoint) {
                // Mobile-specific adjustments
                document.querySelectorAll('.floating-card').forEach((card, index) => {
                    card.style.position = 'relative';
                    card.style.margin = '1rem auto';
                    card.style.display = index === 0 ? 'block' : 'none';
                });
            } else {
                // Desktop view
                document.querySelectorAll('.floating-card').forEach(card => {
                    card.style.position = 'absolute';
                    card.style.display = 'block';
                });
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize(); // Call on load

        // Add loading animation
        window.addEventListener('load', function() {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });

        // Counter animation for stats
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            const speed = 200;

            counters.forEach(counter => {
                const updateCount = () => {
                    const target = parseInt(counter.getAttribute('data-target') || counter.innerText);
                    const count = parseInt(counter.innerText.replace('+', ''));
                    const inc = Math.ceil(target / speed);

                    if (count < target) {
                        counter.innerText = (count + inc) + '+';
                        setTimeout(updateCount, 1);
                    } else {
                        counter.innerText = target + '+';
                    }
                };

                // Set data-target if not set
                if (!counter.getAttribute('data-target')) {
                    counter.setAttribute('data-target', counter.innerText.replace('+', ''));
                    counter.innerText = '0+';
                }

                updateCount();
            });
        }

        // Trigger counter animation when stats section is visible
        const statsSection = document.querySelector('.hero-stats');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        });

        if (statsSection) {
            observer.observe(statsSection);
        }

        // Add parallax effect to hero section
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const hero = document.getElementById('hero');
            const rate = scrolled * -0.5;
        });

        // Add dynamic text animation
        const heroTitle = document.querySelector('.adis-hero-content h1');
        if (heroTitle) {
            const text = heroTitle.innerText;
            heroTitle.innerHTML = '';
            
            for (let i = 0; i < text.length; i++) {
                const span = document.createElement('span');
                span.innerText = text[i] === ' ' ? '\u00A0' : text[i];
                span.style.animationDelay = `${i * 0.1}s`;
                span.style.animation = 'fadeInUp 0.6s ease forwards';
                span.style.opacity = '0';
                heroTitle.appendChild(span);
            }
        }

        // Add CSS for text animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);

        // Particules Hero (effet 3D)
        document.addEventListener('DOMContentLoaded', function() {
            const particles = document.getElementById('particles');
            if (particles) {
                for (let i = 0; i < 30; i++) {
                    const p = document.createElement('div');
                    p.className = 'particle';
                    p.style.top = Math.random() * 100 + '%';
                    p.style.left = Math.random() * 100 + '%';
                    p.style.animationDuration = (6 + Math.random() * 6) + 's';
                    p.style.opacity = 0.3 + Math.random() * 0.5;
                    particles.appendChild(p);
                }
            }
        });

        // Scroll reveal
        function revealOnScroll() {
            document.querySelectorAll('.scroll-reveal').forEach(function(el) {
                const rect = el.getBoundingClientRect();
                if (rect.top < window.innerHeight - 60) {
                    el.classList.add('revealed');
                }
            });
        }
        window.addEventListener('scroll', revealOnScroll);
        window.addEventListener('DOMContentLoaded', revealOnScroll);

        // Menu mobile (optionnel)
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            document.getElementById('menu').classList.toggle('active');
        });

        // Curseur personnalisé (optionnel)
        const cursor = document.querySelector('.cursor');
        if (cursor) {
            document.addEventListener('mousemove', e => {
                cursor.style.left = e.clientX + 'px';
                cursor.style.top = e.clientY + 'px';
            });
            document.querySelectorAll('a, button').forEach(el => {
                el.addEventListener('mouseenter', () => cursor.classList.add('hover'));
                el.addEventListener('mouseleave', () => cursor.classList.remove('hover'));
            });
        }

        // Création dynamique des particules
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            if (!particlesContainer) return;
            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (10 + Math.random() * 10) + 's';
                particlesContainer.appendChild(particle);
            }
        }
        // Appeler la fonction au chargement
        document.addEventListener('DOMContentLoaded', createParticles);

        // Animation recherche : toggle input
        document.addEventListener('DOMContentLoaded', function() {
            const search = document.getElementById('adis-search');
            const toggle = document.getElementById('search-toggle');
            const input = document.getElementById('search-input');
            toggle.addEventListener('click', function(e) {
                search.classList.toggle('active');
                if (search.classList.contains('active')) {
                    input.focus();
                } else {
                    input.value = '';
                }
            });
            // Fermer la recherche si on clique ailleurs
            document.addEventListener('click', function(e) {
                if (!search.contains(e.target)) {
                    search.classList.remove('active');
                    input.value = '';
                }
            });
        });

        // Effet ripple sur les boutons du menu principal
        document.querySelectorAll('.adis-menu > li > a').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const circle = document.createElement('span');
                circle.classList.add('ripple');
                const diameter = Math.max(btn.clientWidth, btn.clientHeight);
                circle.style.width = circle.style.height = `${diameter}px`;
                circle.style.left = `${e.clientX - btn.getBoundingClientRect().left - diameter/2}px`;
                circle.style.top = `${e.clientY - btn.getBoundingClientRect().top - diameter/2}px`;
                btn.appendChild(circle);
                circle.addEventListener('animationend', () => circle.remove());
            });
        });
    </script>
    </body>
</html>