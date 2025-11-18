<?php

namespace models;

use models\base\SQL;
use utils\EmailUtils;
use utils\SessionHelpers;

/**
 * Champs:
 * - ideleve (int, PK)
 * - nomeleve (varchar)
 * - prenomeleve (varchar)
 * - teleleve (varchar, optionnel)
 * - emaileleve (varchar)
 * - motpasseeleve (varchar)
 * - datenaissanceeleve (date)
 */
class EleveModel extends SQL
{
    public function __construct()
    {
        parent::__construct('eleve', 'ideleve');
    }

    /**
     * Récupère les informations de l'élève connecté.
     * Si l'utilisateur n'est pas connecté, retourne null.
     * @return array|null
     */
    public function getMe()
    {
        if (!SessionHelpers::isLogin()) {
            return null;
        }

        $eleve = SessionHelpers::getConnected();
        $ideleve = $eleve["ideleve"];

        $query = "SELECT * FROM eleve WHERE ideleve = :ideleve LIMIT 1";
        $stmt = $this->getPdo()->prepare($query);
        $stmt->execute([':ideleve' => $ideleve]);

        return $stmt->fetch();
    }

    /**
     * Méthode pour créer un nouvel élève.
     * Vérifie si l'email existe déjà avant de créer un compte.
     * 
     * @param string $nom
     * @param string $prenom
     */
    public function creer_eleve(string $nom, string $prenom, string $telephone, string $email, string $motDePasse, string $dateNaissance): bool
    {
        $pdo = $this->getPdo();

        $query = "SELECT * FROM eleve WHERE emaileleve = :email LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':email' => $email]);
        $existingEleve = $stmt->fetch();

        if ($existingEleve) {
            return false;
        }

        $query = "INSERT INTO eleve (nomeleve, prenomeleve, teleleve, emaileleve, motpasseeleve, datenaissanceeleve) 
                  VALUES (:nom, :prenom, :telephone, :email, :motDePasse, :dateNaissance)";
        $stmt = $pdo->prepare($query);

		$motDePasseHash = $motDePasse. $_ENV['PEPPER'];
		$motDePasseHash= password_hash($motDePasseHash, PASSWORD_DEFAULT);

        $params = [
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':telephone' => $telephone,
            ':email' => $email,
            ':motDePasse' => $motDePasseHash,
            ':dateNaissance' => $dateNaissance
        ];

        if ($stmt->execute($params)) {
            $this->connexion($email, $motDePasse);

            EmailUtils::sendEmail(
                $email,
                "Bienvenue chez CDS 49",
                "confirmation_creation_compte",
                [
                    'nomeleve' => $nom,
                    'prenomeleve' => $prenom
                ]
            );

            return true;
        } else {
            return false;
        }
    }

    /**
     * Méthode pour vérifier si un élève existe avec l'email et le mot de passe donnés.
     *
     * @param string $email
     * @param string $motDePasse
     * @param string $token (optionnel) Token à générer et sauvegarder dans la base de données (pour utilisattion via mobile)
     * @return array
     */
    public function connexion(string $email, string $motDePasse, string $token = ""): array
    {
        $query = "SELECT * FROM eleve WHERE emaileleve = :email LIMIT 1";
        $params = [':email' => $email];

        $result = $this->getPdo()->prepare($query);
        $result->execute($params);
        $eleve = $result->fetch(\PDO::FETCH_ASSOC);

        if ($eleve && password_verify($motDePasse . $_ENV['PEPPER'], $eleve['motpasseeleve'])) {
            SessionHelpers::login($eleve);
        } else {
            SessionHelpers::logout();
        }

        if ($token && !empty($eleve)) {
			$query = "SELECT * FROM token WHERE ideleve = :ideleve LIMIT 1";
			$stmt = $this->getPdo()->prepare($query);
			if ($stmt->execute([':ideleve' => $eleve['ideleve']]) && $stmt->rowCount() > 0) {
				$updateQuery = "UPDATE token SET token = :token, date_creation = NOW() WHERE ideleve = :ideleve";
				$updateStmt = $this->getPdo()->prepare($updateQuery);
				$params = [
					':ideleve' => $eleve['ideleve'],
					':token' => $token
				];
				$updateStmt->execute($params);
			} else {
				$insertQuery = "INSERT INTO token (ideleve, token, date_creation) VALUES (:ideleve, :token, NOW())";
				$insertStmt = $this->getPdo()->prepare($insertQuery);
				$params = [
					':ideleve' => $eleve['ideleve'],
					':token' => $token
				];
				$insertStmt->execute($params);
			}
        }


        return SessionHelpers::getConnected();
    }

    /**
     * Méthode pour mettre à jour les informations d'un élève.
     * @param int $ideleve
     * @param string $nom
     * @param string $prenom
     * @param string|null $telephone
     * @param string $email
     * @param string $motDePasse (optionnel) Mot de passe à mettre à jour, si fourni.
     * @return bool
     */
    public function update(string $ideleve, string $nom, string $prenom, ?string $telephone, string $email, string $datenaissanceeleve, ?string $motDePasse = null): bool
    {
        $pdo = $this->getPdo();

        $query = "SELECT * FROM eleve WHERE emaileleve = :email AND ideleve != :ideleve LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':email' => $email, ':ideleve' => $ideleve]);
        $existingEleve = $stmt->fetch();

        if ($existingEleve) {
            return false;
        }

        $query = "UPDATE eleve SET nomeleve = :nom, prenomeleve = :prenom, teleleve= :telephone, emaileleve = :email, datenaissanceeleve = :datenaissanceeleve";

        if ($motDePasse !== null) {
            $query .= ", motpasseeleve = :motDePasse";
        }

        $query .= " WHERE ideleve = :ideleve";

        $params = [
            ':nom' => $nom,
            ':prenom' => $prenom,
	    ':telephone' => $telephone,
            ':email' => $email,
            ':datenaissanceeleve' => $datenaissanceeleve,
            ':ideleve' => $ideleve
        ];

        if ($motDePasse !== null) {
	    $motDePasse .= $_ENV['PEPPER'];
	    $motDePasse= password_hash($motDePasse, PASSWORD_DEFAULT);
            $params[':motDePasse'] = $motDePasse;
        }

        $stmt = $pdo->prepare($query);

        $result = $stmt->execute($params);

        if ($result) {
            SessionHelpers::login([
                'ideleve' => $ideleve,
                'nomeleve' => $nom,
                'prenomeleve' => $prenom,
                'teleleve' => $telephone,
                'emaileleve' => $email,
                'datenaissanceeleve' => $datenaissanceeleve
            ]);

            return true;
        } else {
            return false;
        }
    }

    public function getByToken(string $token)
    {
        $query = "SELECT *
                  FROM eleve
                  JOIN token ON token.ideleve = eleve.ideleve 
                  WHERE token.token = :token 
                  LIMIT 1";
        $stmt = $this->getPdo()->prepare($query);
        $stmt->execute([':token' => $token]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $result;
    }

    public function updateByToken(string $token, string $nom, string $prenom, string $email, string $datenaissanceeleve, ?string $motDePasse = null): bool
    {
        $result = $this->getByToken($token);

        return $this->update($result['ideleve'], $nom, $prenom, $email, $datenaissanceeleve, $motDePasse);
    }

    /**
     * Méthode pour anonymiser les données d'un élève (soft delete).
     * Un soft delete consiste à supprimer les données de l'élève sans les supprimer physiquement de la base de données.
     * @param int $ideleve
     * @return bool
     */
    public function deleteMe(): bool
    {
        $pdo = $this->getPdo();

        if (!SessionHelpers::isLogin()) {
            return false;
        }

        $eleve = SessionHelpers::getConnected();
        $ideleve = $eleve->ideleve;

        $query = "UPDATE eleve SET nomeleve = 'Anonyme', prenomeleve = 'Anonyme',
                    emaileleve = NULL, motpasseeleve = NULL, datenaissanceeleve = NULL 
                    WHERE ideleve = :ideleve";

        $stmt = $pdo->prepare($query);
        $params = [':ideleve' => $ideleve];

        if ($stmt->execute($params)) {
            EmailUtils::sendEmail(
                $eleve->emaileleve,
                "Confirmation de la suppression de votre compte",
                "confirmation_anonymisation_compte",
                [
                    'nomeleve' => $eleve->nomeleve,
                    'prenomeleve' => $eleve->prenomeleve
                ]
            );

            SessionHelpers::logout();
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
	$query = "SELECT ideleve FROM eleve WHERE emaileleve = :email LIMIT 1";

	$stmt = $this->getPdo()->prepare($query);
	$stmt->execute([':email' => $email]);

	$result = $stmt->fetch();

	if ($result) {
	    return $result;
	} else {
	    return false;
        }
    }

    /**
     * Sauvegarde un token de réinitialisation dans la base de données et envoie un email à l'utilisateur.
     * @param int $ideleve
     * @param string $email
     * @param string $token
     * @return bool
     */
    public function saveResetToken(int $ideleve, string $email,string $token): bool
    {
	$query = "SELECT prenomeleve FROM eleve WHERE ideleve = :ideleve LIMIT 1";
	$stmt = $this->getPdo()->prepare($query);
	$stmt->execute([':ideleve' => $ideleve]);
	$eleve = $stmt->fetch();

	$deleteQuery = "DELETE FROM demande_reinitialisation WHERE ideleve = :ideleve";
	$deleteStmt = $this->getPdo()->prepare($deleteQuery);
	$deleteStmt->execute([':ideleve' => $ideleve]);

	$query = "INSERT INTO demande_reinitialisation (ideleve, token, date_creation) VALUES (:ideleve, :token, NOW())";
	$stmt = $this->getPdo()->prepare($query);
	$params = [
	    ':ideleve' => $ideleve,
	    ':token' => $token
	];

	if ($stmt->execute($params)) {
	    EmailUtils::sendEmail(
		$email,
		"Reinitialisation de votre mot de passe",
		"reinitialisation_mot_de_passe",
		[
		    'token' => $token,
		    'prenomeleve' => $eleve['prenomeleve'] ?? 'Utilisateur',
		    'emaileleve' => $email,
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
        $query = "SELECT e.*, dr.date_creation 
                  FROM demande_reinitialisation dr
                  JOIN eleve e ON dr.ideleve = e.ideleve
                  WHERE dr.token = :token
                  AND dr.date_creation >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)
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
        $eleve = $this->validateResetToken($token);
        
        if (!$eleve) {
            return false;
        }
        
        $motDePasseHash = $nouveauMotDePasse . $_ENV['PEPPER'];
        $motDePasseHash = password_hash($motDePasseHash, PASSWORD_DEFAULT);
        
        $query = "UPDATE eleve SET motpasseeleve = :motDePasse WHERE ideleve = :ideleve";
        $stmt = $this->getPdo()->prepare($query);
        
        $success = $stmt->execute([
            ':motDePasse' => $motDePasseHash,
            ':ideleve' => $eleve['ideleve']
        ]);
        
        if ($success) {
            $deleteQuery = "DELETE FROM demande_reinitialisation WHERE token = :token";
            $deleteStmt = $this->getPdo()->prepare($deleteQuery);
            $deleteStmt->execute([':token' => $token]);
            
            EmailUtils::sendEmail(
                $eleve['emaileleve'],
                "Confirmation de changement de mot de passe",
                "confirmation_changement_mot_de_passe",
                [
                    'prenomeleve' => $eleve['prenomeleve']
                ]
            );
        }
        
        return $success;
    }

    /**
     * Vérifie si l'élève connecté a une demande en attente
     * @return bool
     */
    public function aDemandeEnCours(): bool
    {
        $idEleve = SessionHelpers::getConnected()['ideleve'] ?? null;
        
        if (!$idEleve) {
            return false;
        }

        $stmt = $this->getPdo()->prepare("
            SELECT COUNT(*) as nb
            FROM demande_heure_conduite
            WHERE ideleve = :ideleve AND statut = 0
        ");
        
        $stmt->execute([':ideleve' => $idEleve]);
        $result = $stmt->fetch();
        
        return ($result['nb'] ?? 0) > 0;
    }

    /**
     * Crée une demande d'heures supplémentaires pour l'élève connecté
     * @param string|null $commentaire Commentaire optionnel (ex: disponibilités)
     * @return bool
     */
    public function creerDemandeHeureSupplementaire(?string $commentaire = null): bool
    {
        $idEleve = SessionHelpers::getConnected()['ideleve'] ?? null;
        
        if (!$idEleve) {
            return false;
        }

        $stmt = $this->getPdo()->prepare("
            INSERT INTO demande_heure_conduite (ideleve, commentaire, statut, datedemande)
            VALUES (:ideleve, :commentaire, 0, NOW())
        ");
        
        return $stmt->execute([
            ':ideleve' => $idEleve,
            ':commentaire' => $commentaire
        ]);
    }
}
