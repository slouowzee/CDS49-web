<?php

namespace models;

use models\base\SQL;
use utils\SessionHelpers;

/**
 * Champs:
 * - idresultat (int, PK)
 * - ideleve (int)
 * - dateresultat (datetime)
 * - score (bigint)
 * - nbquestions (bigint)
 */
class ResultatModel extends SQL
{
    public function __construct()
    {
        parent::__construct('resultat', 'idresultat');
    }

    /**
     * Sauvegarde le score d'un élève par son ID.
     * @param int $idEleve L'ID de l'élève.
     */
    public function saveScoreById(int $idEleve, int $score, int $nbquestions): bool
    {
        // Préparer la requête pour insérer le score
        $query = "INSERT INTO resultat (ideleve, dateresultat, score, nbquestions) VALUES (:ideleve, NOW(), :score, :nbquestions)";
        $stmt = $this->getPdo()->prepare($query);

        return $stmt->execute([
            ':ideleve' => $idEleve,
            ':score' => $score,
            ':nbquestions' => $nbquestions
        ]);
    }

    /**
     * Sauvegarde du score par un token d'élève.
     */
    public function saveScoreByToken(string $token, int $score, int $nbquestions): bool
    {
        // Récupération de l'élève par son token
        $eleveModel = new EleveModel();
        $eleve = $eleveModel->getByToken($token);

        if (!$eleve) {
            return false; // Si l'élève n'existe pas, retourner false
        }

        // Préparer la requête pour insérer le score
        $query = "INSERT INTO resultat (ideleve, dateresultat, score, nbquestions) VALUES (:ideleve, NOW(), :score, :nbquestions)";
        $stmt = $this->getPdo()->prepare($query);

        return $stmt->execute([
            ':ideleve' => $eleve['ideleve'],
            ':score' => $score,
            ':nbquestions' => $nbquestions
        ]);
    }

	public function getResults() {
		$idEleve = SessionHelpers::getConnected()['ideleve'] ?? null;

        if (!$idEleve) {
            return null;
        }

		$query = "SELECT dateresultat, score, idcategorie FROM resultat WHERE ideleve = :ideleve";
		$stmt = $this->getPdo()->prepare($query);

		return $stmt->execute([
            ':ideleve' => $idEleve
        ]);
	}
}
