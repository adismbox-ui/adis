<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Certificat de Niveau</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #f9f9f9;
            color: #222;
        }
        .certificat-container {
            border: 8px double #185a9d;
            padding: 40px 30px;
            margin: 30px auto;
            background: #fff;
            max-width: 700px;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(24,90,157,0.12);
            text-align: center;
        }
        .certificat-title {
            font-size: 2.5em;
            color: #185a9d;
            margin-bottom: 0.5em;
            font-weight: bold;
        }
        .certificat-niveau {
            font-size: 1.5em;
            color: #43cea2;
            margin-bottom: 1em;
        }
        .certificat-nom {
            font-size: 1.3em;
            margin-bottom: 1em;
        }
        .certificat-date {
            margin-top: 2em;
            font-size: 1.1em;
            color: #888;
        }
        .certificat-signature {
            margin-top: 3em;
            font-size: 1.1em;
            color: #185a9d;
        }
        .logo {
            width: 90px;
            margin-bottom: 1em;
        }
    </style>
</head>
<body>
    <div class="certificat-container">
        <img src="{{ public_path('ad.jpg') }}" class="logo" alt="Logo" />
        <div class="certificat-title">Certificat de Réussite</div>
        <div class="certificat-niveau">Niveau : <strong>{{ $niveau->nom }}</strong></div>
        <div class="certificat-nom">
            Délivré à : <strong>{{ $apprenant->utilisateur->prenom }} {{ $apprenant->utilisateur->nom }}</strong>
        </div>
        <div>
            Pour avoir validé avec succès tous les critères du niveau <strong>{{ $niveau->nom }}</strong>.<br>
            Nous félicitons {{ $apprenant->utilisateur->prenom }} pour son engagement et sa réussite.
        </div>
        <div class="certificat-date">
            Délivré le {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>
        <div class="certificat-signature">
            <em>Signature de l'administration ADIS</em>
        </div>
    </div>
</body>
</html> 