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
	 * @param int $score Le score obtenu.
	 * @param int $nbquestions Le nombre de questions.
	 * @param int $idcategorie L'ID de la catégorie.
	 * @param string $token Le token de l'élève.
     */
    public function saveScoreById(int $idEleve, int $score, int $nbquestions, int $idcategorie, string $token): bool
    {
		$eleveModel = new EleveModel();
		$eleve = $eleveModel->getByToken($token);
		if (!$eleve) {
			error_log("ResultatModel: Élève non trouvé pour le token");
			return false;
		}

        $query = "INSERT INTO resultat (ideleve, dateresultat, score, nbquestions, idcategorie) VALUES (:ideleve, NOW(), :score, :nbquestions, :idcategorie)";
        $stmt = $this->getPdo()->prepare($query);

        return $stmt->execute([
            ':ideleve' => $idEleve,
            ':score' => $score,
            ':nbquestions' => $nbquestions,
            ':idcategorie' => $idcategorie
        ]);
    }

    /**
     * Sauvegarde du score par un token d'élève.
     */
    public function saveScoreByToken(string $token, int $score, int $nbquestions, int $idcategorie ): bool
{
    // Récupération de l'élève par son token
    $eleveModel = new EleveModel();
    $eleve = $eleveModel->getByToken($token);

    if (!$eleve) {
        error_log("ResultatModel: Élève non trouvé pour le token");
        return false;
    }

    // Conversion en tableau si c'est un objet
    if (is_object($eleve)) {
        $eleve = (array) $eleve;
    }

    // Vérifier que l'ID existe
    $idEleve = $eleve['ideleve'] ?? null;
    if (!$idEleve) {
        error_log("ResultatModel: ideleve introuvable dans l'élève");
        return false;
    }

    // Préparer la requête pour insérer le score
    $query = "INSERT INTO resultat (ideleve, dateresultat, score, nbquestions,idcategorie) VALUES (:ideleve, NOW(), :score, :nbquestions,:idcategorie)";
    $stmt = $this->getPdo()->prepare($query);

    try {
        $result = $stmt->execute([
            ':ideleve' => $idEleve,
            ':score' => $score,
            ':nbquestions' => $nbquestions
            ,':idcategorie' => $idcategorie
        ]);
        
        if ($result) {
            error_log("ResultatModel: Score sauvegardé avec succès (ideleve=$idEleve, score=$score/$nbquestions)");
        }
        
        return $result;
    } catch (\PDOException $e) {
        error_log("ResultatModel ERROR: " . $e->getMessage());
        return false;
    }
}

	public function getResults() {
		$idEleve = SessionHelpers::getConnected()['ideleve'] ?? null;

        if (!$idEleve) {
            return [];
        }

		$query = "SELECT dateresultat, score, nbquestions FROM resultat WHERE ideleve = :ideleve ORDER BY dateresultat DESC";
		$stmt = $this->getPdo()->prepare($query);
		$stmt->execute([
            ':ideleve' => $idEleve
        ]);

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

    /**
     * Récupère tous les scores d'un élève par son token.
     * @param string $token Le token de l'élève.
     * @param string|null $dateDebut Date de début pour le filtrage (format: Y-m-d H:i:s)
     * @param string|null $dateFin Date de fin pour le filtrage (format: Y-m-d H:i:s)
     * @return array Liste des résultats
     */
    public function getScoresByToken(string $token, ?string $dateDebut = null, ?string $dateFin = null): array
{
    $eleveModel = new EleveModel();
    $eleve = $eleveModel->getByToken($token);

    if (!$eleve) {
        return [];
    }

    // Conversion en tableau si c'est un objet
    if (is_object($eleve)) {
        $eleve = (array) $eleve;
    }

    $idEleve = $eleve['ideleve'] ?? null;
    if (!$idEleve) {
        error_log("ResultatModel getScores: ideleve introuvable");
        return [];
    }

    $query = "SELECT idresultat, dateresultat, score, nbquestions 
              FROM resultat 
              WHERE ideleve = :ideleve";

    $params = [':ideleve' => $idEleve];

    // Ajouter le filtre de période si fourni
    if ($dateDebut !== null && $dateFin !== null) {
        $query .= " AND dateresultat BETWEEN :dateDebut AND :dateFin";
        $params[':dateDebut'] = $dateDebut;
        $params[':dateFin'] = $dateFin;
    }

    $query .= " ORDER BY dateresultat DESC";

    $stmt = $this->getPdo()->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

    /**
     * Calcule les statistiques des scores d'un élève.
     * @param string $token Le token de l'élève.
     * @param string|null $dateDebut Date de début pour le filtrage
     * @param string|null $dateFin Date de fin pour le filtrage
     * @return array Statistiques : moyenne, top3, total
     */
    public function getStatisticsByToken(string $token, ?string $dateDebut = null, ?string $dateFin = null): array
    {
        $scores = $this->getScoresByToken($token, $dateDebut, $dateFin);

        if (empty($scores)) {
            return [
                'moyenne' => 0,
                'top3' => [],
                'total' => 0
            ];
        }

        // Calculer la moyenne sur 40 points
        $totalScore = 0;
        foreach ($scores as $score) {
            $totalScore += $score['score'];
        }
        $moyenne = round($totalScore / count($scores), 2);

        // Trier par score décroissant et prendre le top 3
        usort($scores, function($a, $b) {
            return $b['score'] - $a['score'];
        });
        $top3 = array_slice($scores, 0, 3);

        return [
            'moyenne' => $moyenne,
            'top3' => $top3,
            'total' => count($scores)
        ];
    }

    /**
     * Récupère tous les scores avec statistiques (combiné).
     * @param string $token Le token de l'élève.
     * @param string|null $dateDebut Date de début
     * @param string|null $dateFin Date de fin
     * @return array Scores + statistiques
     */
    public function getScoresWithStatistics(string $token, ?string $dateDebut = null, ?string $dateFin = null): array
    {
        $scores = $this->getScoresByToken($token, $dateDebut, $dateFin);
        $statistics = $this->getStatisticsByToken($token, $dateDebut, $dateFin);

        return [
            'scores' => $scores,
            'statistics' => $statistics
        ];
    }
}
