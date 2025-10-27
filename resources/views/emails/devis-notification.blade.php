<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis - ADIS</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .header {
            background: linear-gradient(135deg, #4caf50, #388e3c);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
        }
        .content {
            background: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .devis-info {
            background: #e8f5e8;
            border: 1px solid #4caf50;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .devis-info h3 {
            color: #2e7d32;
            margin-top: 0;
        }
        .devis-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .devis-details h4 {
            color: #495057;
            margin-top: 0;
        }
        .devis-details ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .devis-details li {
            margin-bottom: 8px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #4caf50, #388e3c);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .contact-info {
            background: #f1f3f4;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .contact-info h4 {
            color: #495057;
            margin-top: 0;
        }
        .highlight {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .highlight strong {
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 DEVIS PERSONNALISÉ</h1>
        <p>Académie pour la Diffusion de l'Islam et de la Science</p>
    </div>
    
    <div class="content">
        <p>Bonjour <strong>{{ $donateur->nom_donateur }}</strong>,</p>
        
        <p>Nous vous remercions pour votre intérêt envers nos projets communautaires. Suite à votre demande, voici votre devis personnalisé :</p>
        
        <div class="devis-info">
            <h3>📊 Informations de votre demande</h3>
            <p><strong>Date de la demande :</strong> {{ now()->format('d/m/Y') }}</p>
            <p><strong>Projet sélectionné :</strong> 
                @if($don->projet_id == 'fonds_general')
                    Fonds général (pour tous nos projets)
                @else
                    {{ $projet->intitule ?? 'Projet spécifique' }}
                @endif
            </p>
            <p><strong>Montant du don :</strong> {{ number_format($don->montant, 0, ',', ' ') }} F CFA</p>
            <p><strong>Type de don :</strong> {{ ucfirst($don->type_don) }}</p>
        </div>
        
        <div class="devis-details">
            <h4>🎯 Détails du projet</h4>
            @if($don->projet_id != 'fonds_general' && isset($projet))
                <ul>
                    <li><strong>Intitulé :</strong> {{ $projet->intitule }}</li>
                    <li><strong>Description :</strong> {{ $projet->description ?? 'Description non disponible' }}</li>
                    <li><strong>Montant total du projet :</strong> {{ number_format($projet->montant_total, 0, ',', ' ') }} F CFA</li>
                    <li><strong>Montant déjà collecté :</strong> {{ number_format($projet->montant_collecte, 0, ',', ' ') }} F CFA</li>
                    <li><strong>Reste à financer :</strong> {{ number_format($projet->montant_total - $projet->montant_collecte, 0, ',', ' ') }} F CFA</li>
                </ul>
            @else
                <p>Votre don sera utilisé pour soutenir tous nos projets selon les besoins prioritaires identifiés par notre équipe.</p>
            @endif
        </div>
        
        <div class="highlight">
            <strong>💡 Impact de votre don :</strong><br>
            Votre contribution de <strong>{{ number_format($don->montant, 0, ',', ' ') }} F CFA</strong> permettra de :
            <ul>
                <li>Financer l'achat de matériel éducatif</li>
                <li>Former des bénéficiaires</li>
                <li>Développer nos programmes communautaires</li>
                <li>Améliorer la qualité de nos services</li>
            </ul>
        </div>
        
        <div class="devis-details">
            <h4>📋 Prochaines étapes</h4>
            <ol>
                <li><strong>Validation :</strong> Confirmez votre intention de faire ce don</li>
                <li><strong>Paiement :</strong> Effectuez le paiement selon le mode choisi</li>
                <li><strong>Confirmation :</strong> Recevez une confirmation de votre don</li>
                <li><strong>Suivi :</strong> Suivez l'impact de votre contribution</li>
            </ol>
        </div>
        
        <div class="contact-info">
            <h4>📞 Besoin d'aide ?</h4>
            <p>Notre équipe est disponible pour répondre à toutes vos questions :</p>
            <p><strong>Email :</strong> <a href="mailto:adis.mbox@gmail.com">adis.mbox@gmail.com</a></p>
            <p><strong>Téléphone :</strong> <a href="tel:+2250704830462">+225 0704830462</a></p>
            <p><strong>Site web :</strong> <a href="https://www.adis.org">www.adis.org</a></p>
        </div>
        
        <p><strong>Ce devis est valable 30 jours à compter de sa date d'émission.</strong></p>
        
        <p>Nous vous remercions de votre confiance et de votre générosité.</p>
        
        <p>Cordialement,<br>
        <strong>L'équipe ADIS</strong></p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} ADIS - Académie pour la Diffusion de l'Islam et de la Science</p>
        <p>Ce message a été envoyé à {{ $donateur->email_donateur }}</p>
        <p>Si vous n'êtes pas à l'origine de cette demande, veuillez nous contacter immédiatement.</p>
    </div>
</body>
</html> 