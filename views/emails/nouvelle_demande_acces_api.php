
<?php
// Vue pour l'envoi d'un e-mail signalant une nouvelle demande d'accès à l'API (views/emails/nouvelle_demande_acces_api.php)
?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Nouvelle demande d'accès API</title>
	<style>
		body {
			font-family: sans-serif;
			line-height: 1.6;
			color: #333;
		}

		.container {
			width: 80%;
			margin: 20px auto;
			padding: 20px;
			border: 1px solid #ddd;
			border-radius: 5px;
		}

		h1 {
			color: #555;
		}

		p {
			margin-bottom: 10px;
		}

		strong {
			color: #000;
		}
	</style>
</head>

<body>
	<div class="container">
		<h1>Nouvelle demande d'accès à l'API</h1>
		<p>Une nouvelle demande d'accès à l'API a été soumise via le formulaire.</p>
		<p><strong>Email :</strong> <?php echo htmlspecialchars($email ?? 'Non fourni'); ?></p>
		<p><strong>Nom :</strong> <?php echo htmlspecialchars($nom ?? 'Non fourni'); ?></p>
		<p><strong>Prénom :</strong> <?php echo htmlspecialchars($prenom ?? 'Non fourni'); ?></p>
		<p><strong>Raison :</strong></p>
		<p><?php echo nl2br(htmlspecialchars($raison ?? 'Aucune raison fournie')); ?></p>
		<hr>
		<p><em>Ceci est un message automatique.</em></p>
	</div>
</body>

</html>
