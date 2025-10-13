
<?php
// Vue pour l'envoi d'un e-mail de confirmation de demande d'accès à l'API (views/emails/confirmation_demande_acces_api.php)
?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Confirmation de demande d'accès API</title>
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
		<h1>Confirmation de votre demande d'accès</h1>
		<p>Nous avons bien reçu votre demande d'accès à l'API.</p>
		<p>Votre demande a été approuvée ! Vous pouvez maintenant créer votre compte en cliquant sur le lien ci-dessous :</p>
		<p style="text-align: center; margin: 20px 0;">
			<a href="<?php echo htmlspecialchars("http://192.168.119.3:9000") . '/creer-compte-api.html?token=' . ($token ?? ''); ?>" 
			   style="background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">
				Créer mon compte API
			</a>
		</p>
		<p><em>Ce lien sera valable jusqu'à la création de votre compte.</em></p>
		<hr>
		<p><em>Ceci est un message automatique.</em></p>
	</div>
</body>

</html>
