<?php

namespace models;

use models\base\SQL;
use utils\EmailUtils;
use utils\SessionHelpers;

/**
 * Modèle pour gérer les demandes d'accès à l'API
 * 
 * Champs:
 * -
 */
class CompteApiModel extends SQL
{
	public function __construct()
	{
		parent::__construct('compte_api', 'idcompteapi');
	}

	public function connexion(string $email, string $motDePasse, string $token = "")
	{
        $query = "SELECT * FROM compte_api WHERE email = :email LIMIT 1";
        $params = [':email' => $email];

        $result = $this->getPdo()->prepare($query);
        $result->execute($params);
        $compte = $result->fetch();

        if ($compte && password_verify($motDePasse . $_ENV['PEPPER'], $compte['motdepasse'])) {
            SessionHelpers::loginApi($compte);
        } else {
            SessionHelpers::logout();
        }

        if ($token && !empty($compte)) {
            $query = "INSERT INTO token (idcompteapi, token, date_creation) VALUES (:idcompteapi, :token, NOW())";
            $stmt = $this->getPdo()->prepare($query);
            $params = [
                ':idcompteapi' => $compte['idcompteapi'],
                ':token' => $token
            ];
            $stmt->execute($params);
        }


        return SessionHelpers::getConnectedApi();
	}

	public function envoyerDemande(string $email, string $raison, string $nom = "", string $prenom = "")
	{
		$stmt = $this->getPdo()->prepare("SELECT COUNT(*) as count FROM demande_acces_api WHERE emaildemandeur = :email OR ip_demande = :ip_demande;");
		$stmt->execute([':email' => $email, ':ip_demande' => $_SERVER['REMOTE_ADDR']]);
		$result = $stmt->fetch(\PDO::FETCH_OBJ);

		if ($result->count > 0) {
			return false;
		}

		$stmt = $this->getPdo()->prepare("INSERT INTO demande_acces_api (emaildemandeur, date_demande, ip_demande) VALUES (:email, NOW(), :ip_demande);");

		$params = [
			':email' => $email,
			':ip_demande' => $_SERVER['REMOTE_ADDR']
		];

		if ($stmt->execute($params)) {
			$token = bin2hex(random_bytes(32));
			$demande_id = $this->getPdo()->lastInsertId();
			
			$tokenStmt = $this->getPdo()->prepare("UPDATE demande_acces_api SET token_creation = :token WHERE id = :id");
			$tokenStmt->execute([':token' => $token, ':id' => $demande_id]);
			
			EmailUtils::sendEmail(
				$email,
				"Votre demande de compte pour API de CDS49 a ete recu",
				"confirmation_demande_acces_api",
				['token' => $token]
			);
			EmailUtils::sendEmail(
				"contact@localhost.fr",
				"Nouvelle demande de compte pour API CDS49",
				"nouvelle_demande_acces_api",
				[
					'email' => $email,
					'raison' => $raison,
					'nom' => $nom,
					'prenom' => $prenom
				]
			);
			return true;
		} else {
			return false;
		}
	}

	    /**
     * Récupère l'ID de l'élève en fonction de son adresse email.
     * @param string $email
     * @return array|false Retourne un tableau avec l'ID de l'élève si trouvé, false sinon
     */
    public function getByEmail(string $email)
    {
		$query = "SELECT idcompteapi FROM compte_api WHERE email = :email LIMIT 1";

		$stmt = $this->getPdo()->prepare($query);
		$stmt->execute([':email' => $email]);

		$result = $stmt->fetch();

		if ($result) {
			return $result;
		} else {
			return false;
        }
    }

	 public function saveResetToken(int $idcompteapi, string $email,string $token): bool
    {
		$deleteQuery = "DELETE FROM demande_reinitialisation_api WHERE idcompteapi = :idcompteapi";
		$deleteStmt = $this->getPdo()->prepare($deleteQuery);
		$deleteStmt->execute([':idcompteapi' => $idcompteapi]);

		$query = "INSERT INTO demande_reinitialisation_api (idcompteapi, token, date_creation) VALUES (:idcompteapi, :token, NOW())";
		$stmt = $this->getPdo()->prepare($query);
		$params = [
			':idcompteapi' => $idcompteapi,
			':token' => $token
		];

		if ($stmt->execute($params)) {
			EmailUtils::sendEmail(
				$email,
				"Reinitialisation du mot de passe de votre compte API CDS49",
				"reinitialisation_mot_de_passe",
				[
					'token' => $token,
					'email' => $email,
				]
			);
			return true;
		} else {
			return false;
		}
    }

    /**
     * Valide un token de réinitialisation
     * @param string $token
     * @return array|false Retourne les données de l'élève si le token est valide, false sinon
     */
    public function validateResetToken(string $token)
    {
        $query = "SELECT e.*, dra.date_creation 
                  FROM demande_reinitialisation_api dra
                  JOIN compte_api e ON dra.idcompteapi = e.idcompteapi
                  WHERE dra.token = :token
                  AND dra.date_creation >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)
                  LIMIT 1";
        
        $stmt = $this->getPdo()->prepare($query);
        $stmt->execute([':token' => $token]);
        
        return $stmt->fetch();
    }

    /**
     * Met à jour le mot de passe d'un élève avec un token de réinitialisation valide
     * @param string $token
     * @param string $nouveauMotDePasse
     * @return bool
     */
    public function resetPasswordWithToken(string $token, string $nouveauMotDePasse): bool
    {
        $compte = $this->validateResetToken($token);
        
        if (!$compte) {
            return false;
        }
        
        $motDePasseHash = $nouveauMotDePasse . $_ENV['PEPPER'];
        $motDePasseHash = password_hash($motDePasseHash, PASSWORD_DEFAULT);

        $query = "UPDATE compte_api SET motdepasse = :motdepasse WHERE idcompteapi = :idcompteapi";
        $stmt = $this->getPdo()->prepare($query);
        
        $success = $stmt->execute([
            ':motdepasse' => $motDePasseHash,
            ':idcompteapi' => $compte['idcompteapi']
        ]);
        
        if ($success) {
            $deleteQuery = "DELETE FROM demande_reinitialisation_api WHERE token = :token";
            $deleteStmt = $this->getPdo()->prepare($deleteQuery);
            $deleteStmt->execute([':token' => $token]);
            
            EmailUtils::sendEmail(
                $compte['email'],
                "Confirmation de changement du mot de passe de votre compte API CDS49",
                "confirmation_changement_mot_de_passe",
                []
            );
        }
        
        return $success;
    }

	/**
	 * Récupère une demande d'accès par token de création
	 * @param string $token
	 * @return array|false
	 */
	public function getDemandeByToken(string $token)
	{
		$query = "SELECT * FROM demande_acces_api 
				  WHERE token_creation = :token 
				  AND token_creation IS NOT NULL
				  LIMIT 1";
		
		$stmt = $this->getPdo()->prepare($query);
		$stmt->execute([':token' => $token]);
		
		return $stmt->fetch();
	}

	/**
	 * Crée un compte API à partir d'un token de demande valide
	 * @param string $token
	 * @param string $motDePasse
	 * @return bool
	 */
	public function creerCompteAvecToken(string $token, string $motDePasse): bool
	{
		$demande = $this->getDemandeByToken($token);
		
		if (!$demande) {
			return false;
		}

		$stmt = $this->getPdo()->prepare("SELECT COUNT(*) as count FROM compte_api WHERE email = :email;");
		$stmt->execute([':email' => $demande['emaildemandeur']]);
		$result = $stmt->fetch(\PDO::FETCH_OBJ);

		if ($result->count > 0) {
			return false;
		}

		$hashedPassword = password_hash($motDePasse . $_ENV['PEPPER'], PASSWORD_BCRYPT);

		$stmt = $this->getPdo()->prepare("INSERT INTO compte_api (email, motdepasse) VALUES (:email, :motdepasse);");

		if ($stmt->execute([':email' => $demande['emaildemandeur'], ':motdepasse' => $hashedPassword])) {
			$deleteStmt = $this->getPdo()->prepare("DELETE FROM demande_acces_api WHERE id = :id");
			$deleteStmt->execute([':id' => $demande['id']]);
			
			return true;
		} else {
			return false;
		}
	}
}