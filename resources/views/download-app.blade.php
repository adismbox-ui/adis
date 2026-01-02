<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Télécharger l'application ADIS</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4caf50 0%, #388e3c 50%, #256029 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .download-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }
        .app-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #4caf50, #388e3c);
            border-radius: 25px;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(76,175,80,0.3);
        }
        .app-icon i {
            font-size: 60px;
            color: white;
        }
        h1 {
            color: #256029;
            margin-bottom: 10px;
            font-size: 28px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .download-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .btn-download {
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 16px;
            text-decoration: none;
            border: none;
            background: linear-gradient(135deg, #4caf50, #388e3c);
            color: white;
            box-shadow: 0 4px 15px rgba(76,175,80,0.3);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-download:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(76,175,80,0.4);
        }
        .btn-store {
            background: linear-gradient(135deg, #256029, #4caf50);
        }
        .btn-back {
            margin-top: 20px;
            color: #4caf50;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        .btn-back:hover {
            color: #256029;
            transform: translateX(-5px);
        }
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="download-container">
        <div class="app-icon">
            <i class="fas fa-mobile-alt"></i>
        </div>
        <h1>Application ADIS</h1>
        <p>Téléchargez l'application mobile ADIS pour accéder à vos formations, documents et bien plus encore, où que vous soyez.</p>
        
        @if(session('error'))
            <div class="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="download-buttons">
            <a href="{{ route('mobile-app.download') }}" class="btn-download">
                <i class="fas fa-download"></i>
                Télécharger l'APK
            </a>
            <a href="{{ route('mobile-app.store') }}" class="btn-download btn-store">
                <i class="fab fa-google-play"></i>
                Disponible sur Google Play
            </a>
        </div>

        <a href="/" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            Retour à l'accueil
        </a>
    </div>
</body>
</html>

