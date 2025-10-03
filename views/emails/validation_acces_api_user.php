<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre accès API a été validé</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 20px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0;">🎉 Accès API Validé !</h1>
        <p style="color: rgba(255,255,255,0.9); margin: 5px 0 0 0;">CDS 49 - Auto-École</p>
    </div>
    
    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;">
        <h2 style="color: #495057; border-bottom: 2px solid #28a745; padding-bottom: 10px;">
            Félicitations ! Votre accès a été approuvé
        </h2>
        
        <div style="background-color: white; padding: 20px; border-radius: 8px; border-left: 4px solid #28a745; margin: 20px 0;">
            <p style="color: #666; margin: 0; line-height: 1.6; font-size: 16px;">
                <strong>Bonne nouvelle !</strong> Votre demande d'accès à notre API documentaire a été approuvée par notre équipe.
            </p>
        </div>
        
        <div style="background-color: white; padding: 20px; border-radius: 8px; border-left: 4px solid #007bff; margin: 20px 0;">
            <h3 style="color: #495057; margin-top: 0;">Prochaine étape :</h3>
            <p style="color: #666; margin: 0 0 15px 0;">
                Créez maintenant votre compte API pour accéder à la documentation complète :
            </p>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="<?= htmlspecialchars($lien_creation_compte) ?>" 
               style="display: inline-block; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); 
                      color: white; text-decoration: none; padding: 15px 30px; 
                      border-radius: 25px; font-weight: bold; font-size: 16px; 
                      box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
                🚀 Créer mon compte API
            </a>
        </div>
        
        <div style="background-color: #d1ecf1; border: 1px solid #bee5eb; border-radius: 8px; padding: 15px; margin: 20px 0;">
            <p style="color: #0c5460; margin: 0; font-size: 14px;">
                <strong>Important :</strong> Ce lien est personnel et sécurisé. Gardez-le précieusement pour créer votre compte API.
            </p>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <p style="color: #6c757d; font-size: 14px; margin: 0;">
                Une fois votre compte créé, vous aurez accès à toute notre documentation API.
            </p>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 20px; padding: 20px; background-color: #f8f9fa; border-radius: 10px;">
        <p style="color: #6c757d; font-size: 12px; margin: 0;">
            Merci de votre patience et bienvenue dans l'écosystème API CDS 49 !<br>
            L'équipe CDS 49
        </p>
    </div>
</body>
</html>