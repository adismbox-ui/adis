<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Actualités | ADIS</title>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/css/app.css">
	<style>
		body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f9fafb; color: #222; }
		.container { max-width: 1100px; margin: 0 auto; padding: 2rem 1rem; }
		section { background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(16,185,129,0.08); padding: 2.2rem 2rem; margin-bottom: 2rem; }
		h1, h2, h3 { color: #388e3c; margin-bottom: 1.2rem; }
		.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
		.card { background: #ffffff; border: 1px solid #eef2f7; border-radius: 14px; padding: 1.1rem; box-shadow: 0 4px 12px rgba(0,0,0,.04); }
		.card h3 { margin: 0 0 .5rem; font-size: 1.05rem; color: #256029; }
		.card p { margin: 0; color: #475569; font-size: .95rem; }
		.card .meta { margin-top: .6rem; font-size: .85rem; color: #6b7280; }
		.badge { display: inline-block; font-size: .8rem; padding: .25rem .6rem; border-radius: 999px; background: #e8f5e9; color: #14532d; border: 1px solid rgba(20,83,45,.2); }
		.hero { text-align: center; }
		.hero p { max-width: 800px; margin: 0 auto; color: #334155; }
		@media (max-width: 600px) { section { padding: 1.2rem 0.8rem; } }
	</style>
</head>
<body>
	<header style="background: linear-gradient(135deg, #4caf50, #388e3c); padding: 1.2rem 0; box-shadow: 0 4px 24px rgba(16,185,129,0.10); display: flex; align-items: center; justify-content: center; position: relative;">
		<div style="position: absolute; left: 32px; display: flex; align-items: center; gap: 12px;">
			<a href="/" style="display: inline-flex; align-items: center; gap: 12px; color: #fff; text-decoration: none;">
				<img src="/photo_2025-07-02_10-44-47.jpg" alt="Logo ADIS" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
				<span style="color: #fff; font-size: 1.5rem; font-weight: bold; letter-spacing: 2px;">ADIS</span>
			</a>
		</div>
		<h1 style="color: #fff; font-size: 2.2rem; font-weight: bold; letter-spacing: 2px; margin: 0;">ACTUALITÉS</h1>
	</header>

	<div class="container">
		<section class="hero">
			<p>Suivez les dernières nouvelles d’ADIS : annonces importantes, lancements de sessions, projets, initiatives communautaires et plus encore.</p>
		</section>

		<section>
			<h2>À la une</h2>
			<div class="grid">
				<div class="card">
					<span class="badge"><i class="fas fa-bolt"></i> Nouveau</span>
					<h3>Ouverture des inscriptions — Session prochaine</h3>
					<p>Les inscriptions pour la prochaine session (12 semaines) sont ouvertes. Réservez votre place dès maintenant.</p>
					<div class="meta"><i class="far fa-calendar-alt"></i> 01/08/2025</div>
				</div>
				<div class="card">
					<span class="badge"><i class="fas fa-info-circle"></i> Info</span>
					<h3>Lancement de la Marketplace ADIS</h3>
					<p>Présentez vos produits et services à la communauté. Inscriptions annuelles à 25 000 FCFA.</p>
					<div class="meta"><i class="far fa-calendar-alt"></i> 29/07/2025</div>
				</div>
				<div class="card">
					<span class="badge"><i class="fas fa-users"></i> Communauté</span>
					<h3>Vie associative — Événements à venir</h3>
					<p>Ateliers, conférences et rencontres pour dynamiser notre réseau. Consultez l’agenda.</p>
					<div class="meta"><i class="far fa-calendar-alt"></i> 25/07/2025</div>
				</div>
			</div>
		</section>

		<section>
			<h2>Toutes les actualités</h2>
			<div class="grid">
				<div class="card">
					<h3>Résultats de fin de session — Publication</h3>
					<p>Les attestations et certificats sont disponibles dans votre espace membre.</p>
					<div class="meta">23/07/2025</div>
				</div>
				<div class="card">
					<h3>Nouveaux modules — Sciences religieuses</h3>
					<p>Ajout de modules en fiqh et aqida avec un parcours progressif.</p>
					<div class="meta">18/07/2025</div>
				</div>
				<div class="card">
					<h3>Ressources — Bibliothèque enrichie</h3>
					<p>Des PDF, audios et vidéos pédagogiques sont disponibles pour révision.</p>
					<div class="meta">12/07/2025</div>
				</div>
			</div>
		</section>

		<section>
			<h2>Contact rédaction</h2>
			<p>Une information à partager ? Écrivez-nous : <a href="mailto:adis.mbox@gmail.com">adis.mbox@gmail.com</a></p>
		</section>
	</div>
</body>
</html>
