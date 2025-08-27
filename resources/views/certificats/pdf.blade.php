<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat de Formation - ADIS</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        
        body {
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
        }
        
        .certificate-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .certificate-border {
            border: 8px solid #2d5f4f;
            margin: 20px;
            padding: 60px 50px;
            position: relative;
            min-height: 600px;
        }
        
        /* Décorations d'angle */
        .corner-decoration {
            position: absolute;
            width: 200px;
            height: 200px;
            opacity: 0.8;
        }
        
        .corner-decoration.top-right {
            top: -20px;
            right: -20px;
            background: conic-gradient(from 0deg, #28a745, #ffc107, #28a745, #ffc107);
            clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
            transform: rotate(45deg);
        }
        
        .corner-decoration.bottom-left {
            bottom: -20px;
            left: -20px;
            background: conic-gradient(from 180deg, #28a745, #ffc107, #28a745, #ffc107);
            clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
            transform: rotate(45deg);
        }
        
        /* Rubans décoratifs */
        .ribbon {
            position: absolute;
            height: 60px;
        }
        
        .ribbon-green-1 {
            background: linear-gradient(45deg, #28a745, #20c997);
            width: 300px;
            top: 20px;
            right: -50px;
            transform: rotate(15deg);
            border-radius: 30px;
        }
        
        .ribbon-gold-1 {
            background: linear-gradient(45deg, #ffc107, #fd7e14);
            width: 250px;
            top: 60px;
            right: -30px;
            transform: rotate(-10deg);
            border-radius: 30px;
        }
        
        .ribbon-green-2 {
            background: linear-gradient(-45deg, #28a745, #20c997);
            width: 280px;
            bottom: 100px;
            left: -60px;
            transform: rotate(-20deg);
            border-radius: 30px;
        }
        
        .ribbon-gold-2 {
            background: linear-gradient(-45deg, #ffc107, #fd7e14);
            width: 200px;
            bottom: 140px;
            left: -40px;
            transform: rotate(25deg);
            border-radius: 30px;
        }
        
        /* Logo ADIS */
        .logo-container {
            position: absolute;
            top: 30px;
            left: 30px;
        }
        
        .logo {
            width: 120px;
            height: 120px;
            border: 3px solid #2d5f4f;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            position: relative;
        }
        
        .logo-text {
            font-weight: bold;
            color: #2d5f4f;
            text-align: center;
        }
        
        .logo-adis {
            font-size: 24px;
            letter-spacing: 2px;
        }
        
        .logo-arabic {
            font-size: 12px;
            margin-top: 5px;
        }
        
        .logo-tagline {
            font-size: 8px;
            color: #666;
            margin-top: 2px;
        }
        
        .logo-icon {
            position: absolute;
            top: 20px;
            width: 40px;
            height: 20px;
            background: #28a745;
            clip-path: polygon(0% 100%, 50% 0%, 100% 100%);
        }
        
        .logo-icon::after {
            content: '';
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 2px;
            background: #ffc107;
        }
        
        /* Médaille de certification */
        .certification-badge {
            position: absolute;
            top: 30px;
            right: 30px;
            width: 100px;
            height: 100px;
        }
        
        .badge-outer {
            width: 100px;
            height: 100px;
            background: conic-gradient(from 0deg, #ffc107, #28a745, #ffc107, #28a745, #ffc107);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .badge-inner {
            width: 70px;
            height: 70px;
            background: #2d5f4f;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .badge-star {
            color: #ffc107;
            font-size: 30px;
        }
        
        .badge-ribbon {
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 25px;
            background: #dc3545;
            clip-path: polygon(0% 0%, 100% 0%, 100% 70%, 50% 100%, 0% 70%);
        }
        
        /* Contenu principal */
        .main-content {
            text-align: center;
            margin-top: 40px;
            position: relative;
            z-index: 10;
        }
        
        .certificate-title {
            font-size: 4rem;
            font-weight: bold;
            color: #2d5f4f;
            letter-spacing: 3px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .certificate-subtitle {
            font-size: 2rem;
            color: #666;
            font-weight: 300;
            margin-bottom: 40px;
            letter-spacing: 1px;
        }
        
        .certificate-text {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 20px;
        }
        
        .certificate-description {
            font-size: 1rem;
            color: #555;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto 40px auto;
        }
        
        .details-section {
            display: flex;
            justify-content: space-between;
            margin: 40px 0;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .detail-item {
            text-align: left;
        }
        
        .detail-label {
            font-weight: bold;
            color: #2d5f4f;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        
        .detail-line {
            width: 200px;
            height: 2px;
            background: #ffc107;
            margin-bottom: 20px;
        }
        
        /* Section signature */
        .signature-section {
            text-align: right;
            margin-top: 60px;
            position: relative;
        }
        
        .signature-location {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 40px;
        }
        
        .signature-name {
            font-size: 1.3rem;
            font-weight: bold;
            color: #2d5f4f;
            margin-bottom: 5px;
        }
        
        .signature-title {
            font-size: 1rem;
            color: #666;
            margin-bottom: 20px;
        }
        
        .signature-line {
            width: 200px;
            height: 1px;
            background: #333;
            margin-left: auto;
            margin-bottom: 10px;
        }
        
        /* Ligne décorative */
        .decorative-line {
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #ffc107, #28a745, #ffc107);
            margin: 40px 0;
            border-radius: 2px;
        }
        
        /* Ligne pour le nom avec contenu dynamique */
        .name-line {
            height: 60px;
            border-bottom: 2px solid #333;
            margin: 20px auto;
            width: 400px;
            position: relative;
        }
        
        .name-line::after {
            content: "{{ $apprenant->prenom }} {{ $apprenant->nom }}";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 0 20px;
            font-size: 18px;
            font-weight: bold;
            color: #2d5f4f;
        }
        
        /* Valeurs des détails */
        .detail-value {
            font-size: 1rem;
            color: #2d5f4f;
            font-weight: 600;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <!-- Décorations d'arrière-plan -->
            <div class="ribbon ribbon-green-1"></div>
            <div class="ribbon ribbon-gold-1"></div>
            <div class="ribbon ribbon-green-2"></div>
            <div class="ribbon ribbon-gold-2"></div>
            
            <!-- Logo ADIS -->
            <div class="logo-container">
                <div class="logo">
                    <div class="logo-icon"></div>
                    <div class="logo-text">
                        <div class="logo-adis">ADIS</div>
                        <div class="logo-arabic">أكاديمية لنشر الإسلام وتعليم</div>
                        <div class="logo-tagline">La Diffusion de l'Islam et Sciences</div>
                    </div>
                </div>
            </div>
            
            <!-- Badge de certification -->
            <div class="certification-badge">
                <div class="badge-outer">
                    <div class="badge-inner">
                        <div class="badge-star">★</div>
                    </div>
                    <div class="badge-ribbon"></div>
                </div>
            </div>
            
            <!-- Contenu principal -->
            <div class="main-content">
                <h1 class="certificate-title">CERTIFICAT</h1>
                <h2 class="certificate-subtitle">DE FORMATION</h2>
                
                <div class="decorative-line"></div>
                
                <p class="certificate-text">Ce certificat est décerné à:</p>
                
                <div class="name-line"></div>
                
                <div class="certificate-description">
                    Pour sa participation avec succès au programme de formation en langue arabe de l'académie virtuelle ADIS.
                </div>
                
                <div class="details-section">
                    <div class="detail-item">
                        <div class="detail-label">Niveau:</div>
                        <div class="detail-line"></div>
                        <div class="detail-value">
                            @if($module && $module->niveau)
                                {{ $module->niveau->nom }}
                            @else
                                {{ $certificat->titre }}
                            @endif
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Période:</div>
                        <div class="detail-line"></div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($certificat->date_obtention)->format('d/m/Y') }}</div>
                    </div>
                </div>
                
                <div class="signature-section">
                    <div class="signature-location">Fait à Abidjan,</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">Moustapha BAKARE</div>
                    <div class="signature-title">Superviseur général</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 