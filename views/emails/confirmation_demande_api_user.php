<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande d'accès API reçue</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0;">CDS 49</h1>
        <p style="color: rgba(255,255,255,0.9); margin: 5px 0 0 0;">Auto-École</p>
    </div>
    
    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;">
        <h2 style="color: #495057; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
            Demande d'accès API reçue
        </h2>
        
        <p style="color: #495057; font-size: 16px;">
            Bonjour <?= htmlspecialchars($nom_complet) ?>,
        </p>
        
        <div style="background-color: white; padding: 20px; border-radius: 8px; border-left: 4px solid #28a745; margin: 20px 0;">
            <p style="color: #666; margin: 0; line-height: 1.6;">
                Nous avons bien reçu votre demande d'accès à notre API documentaire. 
                Notre équipe va examiner votre demande et vous recontactera prochainement.
            </p>
        </div>
        
        <div style="background-color: white; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; margin: 20px 0;">
            <h3 style="color: #495057; margin-top: 0;">Prochaines étapes :</h3>
            <ol style="color: #666; padding-left: 20px;">
                <li>Examen de votre demande par notre équipe</li>
                <li>Validation de votre accès</li>
                <li>Création de votre compte API</li>
            </ol>
            
            <p style="color: #666; margin: 15px 0 0 0;">
                <strong>Une fois votre demande approuvée</strong>, vous pourrez créer votre compte API en cliquant sur le lien suivant :
            </p>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="<?= htmlspecialchars($lien_creation_compte) ?>" 
               style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                      color: white; text-decoration: none; padding: 15px 30px; 
                      border-radius: 25px; font-weight: bold; font-size: 16px;">
                Créer mon compte API
            </a>
        </div>
        
        <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin: 20px 0;">
            <p style="color: #856404; margin: 0; font-size: 14px;">
                <strong>Important :</strong> Conservez ce lien précieusement. Vous en aurez besoin pour créer votre compte API.
            </p>
        </div>
        
        <p style="color: #6c757d; font-size: 14px; margin-top: 30px;">
            Si vous avez des questions, vous pouvez nous contacter à tout moment.
        </p>
    </div>
    
    <div style="text-align: center; margin-top: 20px; padding: 20px; background-color: #f8f9fa; border-radius: 10px;">
        <p style="color: #6c757d; font-size: 12px; margin: 0;">
            Merci de votre confiance,<br>
            L'équipe CDS 49
        </p>
    </div>
</body>
</html>