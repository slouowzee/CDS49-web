<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe - CDS 49</title>
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

        .button {
            display: inline-block;
            background-color: #dc3545;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }

        .button:hover {
            background-color: #c82333;
        }

        .token-box {
            background-color: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
            font-family: monospace;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #495057;
        }

        .warning {
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
            <h1>Réinitialisation de votre mot de passe</h1>
            <p>Bonjour <?= htmlspecialchars($prenomeleve ?? 'Utilisateur') ?>,</p>
            
            <p>Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte CDS 49.</p>
            
            <p>Voici votre code de réinitialisation :</p>
            
            <div class="token-box">
                <?= htmlspecialchars($token ?? 'TOKEN_NON_DISPONIBLE') ?>
            </div>
            
            <div class="warning">
                <strong>Important :</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Ce code est valable pendant 15 minutes uniquement</li>
                    <li>Ne partagez jamais ce code avec quelqu'un d'autre</li>
                    <li>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email</li>
                </ul>
            </div>
            
            <p>Pour terminer la réinitialisation de votre mot de passe, cliquez sur le lien ci-dessous et utilisez votre code :</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="<?= $_ENV['BASE_URL'] ?? 'http://192.168.119.3' ?>/mot-de-passe-oublie.html?email=<?= urlencode($emaileleve ?? '') ?>" class="button">
                    Réinitialiser mon mot de passe
                </a>
            </div>
            
            <br>
            
            <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
            <p>L'équipe CDS 49</p>
        </div>
        <div class="footer">
            <p>&copy; <?= date('Y') ?> CDS 49. Tous droits réservés.</p>
            <p>Ceci est un email automatique, merci de ne pas y répondre.</p>
            <p>Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email en toute sécurité.</p>
        </div>
    </div>
</body>

</html>
