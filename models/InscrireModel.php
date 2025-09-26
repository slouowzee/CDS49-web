<?php

namespace models;

use models\base\SQL;
use utils\SessionHelpers;

/**
 * Champs:
 * - ideleve (int, PK)
 * - idforfait (int, PK)
 * - dateinscription (date)
 */
class InscrireModel extends SQL
{
    public function __construct()
    {
        parent::__construct('inscrire', 'ideleve');
    }

    /**
     * Récupère les informations d'inscription d'un élève pour un forfait spécifique.
     */
    public function getForfaitEleveConnecte(): object | null | bool
    {
        $idEleve = SessionHelpers::getConnected()['ideleve'] ?? null;

        if (!$idEleve) {
            return null; // Si l'ID de l'élève n'est pas défini, retourner null
        }

        $stmt = $this->getPdo()->prepare("SELECT * FROM inscrire LEFT JOIN forfait ON inscrire.idforfait = forfait.idforfait WHERE inscrire.ideleve = :ideleve;");
        $stmt->execute([':ideleve' => $idEleve]);
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }

    /**
     * Vérifie si un élève a déjà un forfait actif.
     */
    public function eleveAForfaitActif(int $idEleve): bool
    {
        $stmt = $this->getPdo()->prepare("SELECT COUNT(*) as count FROM inscrire WHERE ideleve = :ideleve;");
        $stmt->execute([':ideleve' => $idEleve]);
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        return $result->count > 0;
    }

    /**
     * Inscrit un élève à un forfait.
     */
    public function inscrireEleve(int $idEleve, int $idForfait): bool
    {
        try {
            $stmt = $this->getPdo()->prepare("INSERT INTO inscrire (ideleve, idforfait, dateinscription) VALUES (:ideleve, :idforfait, :dateinscription);");
            return $stmt->execute([
                ':ideleve' => $idEleve,
                ':idforfait' => $idForfait,
                ':dateinscription' => date('Y-m-d')
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }
}
