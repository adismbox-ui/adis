<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de don - ADIS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
        .highlight {
            background-color: #e8f5e8;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Merci pour votre don !</h1>
    </div>
    
    <div class="content">
        <p>Bonjour !</p>
        
        <p>Nous avons bien reçu votre don et nous vous en remercions sincèrement !</p>
        
        <div class="highlight">
            <p><strong>Votre générosité fait la différence !</strong></p>
            <p>Chaque don, quel que soit le montant, contribue directement à la réalisation de nos projets communautaires et à l'amélioration de la vie des personnes que nous aidons.</p>
        </div>
        
        <p>Notre équipe va traiter votre don et vous contactera si nécessaire pour finaliser le processus.</p>
        
        <p>Vous recevrez également un reçu détaillé par email dans les prochaines heures.</p>
        
        <p>Cordialement,<br>
        <strong>L'équipe ADIS</strong></p>
    </div>
    
    <div class="footer">
        <p>Cet email a été envoyé à : {{ $email }}</p>
        <p>© {{ date('Y') }} ADIS - Tous droits réservés</p>
    </div>
</body>
</html> 