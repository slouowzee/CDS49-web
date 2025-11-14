<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation annulation de lecon - CDS 49</title>
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

        .info-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #dc3545;
            margin: 20px 0;
        }

        .info-box p {
            margin: 5px 0;
        }

        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #888;
            font-size: 0.9em;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>CDS 49 - Auto-école</h2>
        </div>

        <div class="content">
            <h1>Annulation de leçon confirmée</h1>
            <p>Bonjour <?= htmlspecialchars($prenom) ?>,</p>
            <p>Nous vous confirmons l'annulation de votre leçon de conduite.</p>

            <div class="info-box">
                <p><strong>Date et heure :</strong> <?= htmlspecialchars($dateLecon) ?></p>
                <p><strong>Moniteur :</strong> <?= htmlspecialchars($moniteur) ?></p>
                <p><strong>Lieu de rendez-vous :</strong> <?= htmlspecialchars($lieu) ?></p>
            </div>

            <p>Si vous souhaitez replanifier une leçon, n'hésitez pas à nous contacter.</p>
        </div>

        <div class="footer">
            <p>Cordialement,<br>L'équipe CDS 49</p>
            <p>
                <a href="mailto:contact@cds49.fr">contact@cds49.fr</a>
            </p>
        </div>
    </div>
</body>

</html>
