<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Vie associative | ADIS</title>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/css/app.css">
	<style>
		body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f9fafb; color: #222; }
		.container { max-width: 1100px; margin: 0 auto; padding: 2rem 1rem; }
		section { background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(16,185,129,0.08); padding: 2.2rem 2rem; margin-bottom: 2rem; }
		h1, h2, h3 { color: #388e3c; margin-bottom: 1.2rem; }
		.lead { font-size: 1.05rem; line-height: 1.6; }
		.table { width: 100%; border-collapse: collapse; }
		.table th, .table td { padding: .75rem; border-bottom: 1px solid #eef2f7; text-align: left; font-size: .95rem; }
		.table th { background: #f5faf7; color: #256029; }
		.badge-info { display: inline-block; background: #e8f5e9; color: #256029; border: 1px solid rgba(37,96,41,.15); border-radius: 10px; padding: .35rem .7rem; font-size: .9rem; }
		.gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 14px; }
		.gallery .item { background: #eff6ff; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 1.2rem; text-align: center; color: #64748b; }
		@media (max-width: 600px) { section { padding: 1.2rem 0.8rem; } .table { display: block; overflow-x: auto; } }
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
		<h1 style="color: #fff; font-size: 2.2rem; font-weight: bold; letter-spacing: 2px; margin: 0;">VIE ASSOCIATIVE</h1>
	</header>

	<div class="container">
		<section>
			<p class="lead">Suivez la vie de notre communauté à travers les activités et événements à venir, découvrez notre magazine numérique et revivez les meilleurs moments grâce à notre galerie photos et vidéos.</p>
		</section>

		<section>
			<h2>PRÉSENTATION</h2>
			<p>Bienvenue dans l’espace dédié à la vie associative d’ADIS. Découvrez notre mission, nos valeurs et les engagements qui animent notre communauté pour renforcer la solidarité et le développement mutuel.</p>
		</section>

		<section>
			<h2>ACTIVITÉS ET ÉVÉNEMENTS À VENIR</h2>
			<p>Ne manquez aucune occasion de participer à nos activités enrichissantes ! Retrouvez ici le calendrier des événements, ateliers, conférences et rencontres organisés pour dynamiser notre réseau et favoriser les échanges.</p>
			<table class="table">
				<thead>
					<tr>
						<th>Date</th>
						<th>Titre de l’évènement</th>
						<th>Description courte</th>
						<th>Lieu</th>
						<th>Heure</th>
						<th>Contact</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="6" style="text-align:center; color:#6b7280;">(À venir)</td>
					</tr>
				</tbody>
			</table>
		</section>

		<section>
			<h2>LE MAGAZINE « ADIS »</h2>
			<p>Plongez dans notre magazine numérique bimensuel, source d’informations, d’interviews exclusives, et d’articles inspirants sur la vie associative et les initiatives de nos membres. Un rendez-vous incontournable pour rester connecté.</p>
		</section>

		<section>
			<h2>GALERIE ASSOCIATIVE</h2>
			<p>Revivez les moments forts de nos événements grâce à cette galerie multimédia. Images et vidéos vous permettront de partager l’ambiance et l’énergie qui animent notre communauté.</p>
			<div class="gallery">
				<div class="item">Image/Vidéo à venir</div>
				<div class="item">Image/Vidéo à venir</div>
				<div class="item">Image/Vidéo à venir</div>
			</div>
		</section>
	</div>
</body>
</html>
