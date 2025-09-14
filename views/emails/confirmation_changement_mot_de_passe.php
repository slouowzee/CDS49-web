<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de changement de mot de passe - CDS 49</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .header img {
            max-width: 150px;
        }

        .content {
            padding: 20px 0;
        }

        .content h1 {
            color: #333;
        }

        .content p {
            line-height: 1.6;
        }

        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 0.9em;
            color: #777;
        }

        .success-box {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #155724;
        }

        .security-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="content">
            <h1>Confirmation de changement de mot de passe</h1>
            <p>Bonjour <?= htmlspecialchars($prenomeleve ?? 'Utilisateur') ?>,</p>
            
            <div class="success-box">
                <strong>✓ Votre mot de passe a été modifié avec succès !</strong>
            </div>
	                
            <div class="security-notice">
                <strong>Important pour votre sécurité :</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Si vous n'êtes pas à l'origine de ce changement, contactez-nous immédiatement</li>
                    <li>Assurez-vous de garder votre mot de passe confidentiel</li>
                    <li>Utilisez un mot de passe unique pour votre compte CDS 49</li>
                </ul>
            </div>
            
            <p>Vous pouvez maintenant vous connecter à votre compte avec votre nouveau mot de passe.</p>
            
            <br>
            
            <p>Si vous avez des questions concernant votre compte, n'hésitez pas à nous contacter.</p>
            <p>L'équipe CDS 49</p>
        </div>
        <div class="footer">
            <p>&copy; <?= date('Y') ?> CDS 49. Tous droits réservés.</p>
            <p>Ceci est un email automatique, merci de ne pas y répondre.</p>
            <p>Si vous n'avez pas demandé ce changement, contactez-nous immédiatement.</p>
        </div>
    </div>
</body>

</html>
