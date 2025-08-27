<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau questionnaire disponible</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #1a4d3a 0%, #2d6e4e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #1a4d3a;
        }
        .message {
            background-color: #f8f9fa;
            border-left: 4px solid #1a4d3a;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .questionnaire-info {
            background-color: #e8f5e8;
            border: 1px solid #1a4d3a;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .questionnaire-info h3 {
            color: #1a4d3a;
            margin-top: 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #1a4d3a 0%, #2d6e4e 100%);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 77, 58, 0.3);
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">📝</div>
            <h1>Nouveau questionnaire disponible</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Bonjour {{ $apprenant->utilisateur->prenom }} {{ $apprenant->utilisateur->nom }},
            </div>
            
            <div class="message">
                Un nouveau questionnaire a été mis à votre disposition. Veuillez le compléter dans les délais impartis.
            </div>
            
            <div class="questionnaire-info">
                <h3>📋 Détails du questionnaire</h3>
                <p><strong>Titre :</strong> {{ $questionnaire->titre }}</p>
                <p><strong>Type :</strong> {{ ucfirst($questionnaire->type_devoir) }}</p>
                <p><strong>Semaine :</strong> {{ $questionnaire->semaine }}</p>
                @if($questionnaire->description)
                    <p><strong>Description :</strong> {{ $questionnaire->description }}</p>
                @endif
                <p><strong>Temps limite :</strong> {{ $questionnaire->minutes }} minutes</p>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $url }}" class="btn">
                    🚀 Commencer le questionnaire
                </a>
            </div>
            
            <div style="margin-top: 30px; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px;">
                <p style="margin: 0; color: #856404;">
                    <strong>⚠️ Important :</strong> 
                    Ce questionnaire a un temps limite de {{ $questionnaire->minutes }} minutes. 
                    Assurez-vous d'avoir suffisamment de temps avant de commencer.
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>Cet email a été envoyé automatiquement par le système ADIS.</p>
            <p>Si vous avez des questions, contactez votre formateur.</p>
        </div>
    </div>
</body>
</html> 