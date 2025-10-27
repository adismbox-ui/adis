<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reçu de don - ADIS</title>
  <style>
    body{font-family:Segoe UI, Tahoma, Geneva, Verdana, sans-serif; background:#f6f7f9; color:#333; margin:0; padding:20px}
    .card{max-width:700px; margin:0 auto; background:#fff; border-radius:16px; box-shadow:0 12px 28px rgba(0,0,0,.08); overflow:hidden}
    .header{background:linear-gradient(135deg,#10b981,#34d399); color:#fff; padding:28px; text-align:center}
    .header h1{margin:0; font-size:24px}
    .sub{opacity:.95; margin-top:6px}
    .content{padding:28px}
    .row{margin-bottom:10px}
    .label{font-weight:700; color:#065f46}
    .val{color:#111}
    .highlight{background:#ecfdf5; padding:16px; border-radius:10px; border-left:4px solid #10b981; margin:16px 0}
    .footer{background:#f1f5f9; padding:16px; font-size:12px; color:#555; text-align:center}
    .contacts{margin-top:10px; font-size:13px; color:#444}
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <h1>Reçu de votre don</h1>
      <div class="sub">Académie pour la Diffusion de l'Islam et de la Science</div>
    </div>
    <div class="content">
      <div class="highlight">Merci <strong>{{ $don->nom_donateur }}</strong> pour votre générosité !</div>

      <div class="row"><span class="label">Numéro de référence:</span> <span class="val">{{ $don->numero_reference }}</span></div>
      <div class="row"><span class="label">Date:</span> <span class="val">{{ $don->date_don?->format('d/m/Y H:i') }}</span></div>
      <div class="row"><span class="label">Donateur:</span> <span class="val">{{ $don->nom_donateur }}</span></div>
      <div class="row"><span class="label">Email:</span> <span class="val">{{ $don->email_donateur }}</span></div>
      <div class="row"><span class="label">Montant:</span> <span class="val">{{ number_format($don->montant,0,',',' ') }} F CFA</span></div>
      <div class="row"><span class="label">Type de don:</span> <span class="val">{{ ucfirst($don->type_don) }}</span></div>
      <div class="row"><span class="label">Destination:</span> <span class="val">{{ $projetNom }}</span></div>
      <div class="row"><span class="label">Mode de paiement:</span> <span class="val">{{ strtoupper($don->mode_paiement) }}</span></div>

      <div class="highlight">
        <p>Votre contribution soutient concrètement nos projets prioritaires. Nous vous tiendrons informé(e) de l'impact de votre don.</p>
      </div>

      <div class="contacts">
        <div><strong>Contacts ADIS</strong></div>
        <div>Email: <a href="mailto:adis.mbox@gmail.com">adis.mbox@gmail.com</a> | Tél: <a href="tel:+2250704830462">+225 0704830462</a></div>
        <div>Site: <a href="https://www.adis.org">www.adis.org</a></div>
      </div>
    </div>
    <div class="footer">
      © {{ date('Y') }} ADIS — Merci pour votre soutien
    </div>
  </div>
</body>
</html>