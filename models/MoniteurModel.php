<?php

namespace models;

use models\base\SQL;

/**
 * Champs:
 * - idmoniteur (int, PK)
 * - nommoniteur (varchar)
 * - prenommoniteur (varchar)
 * - emailmoniteur (varchar)
 */
class MoniteurModel extends SQL
{
    public function __construct()
    {
        parent::__construct('moniteur', 'idmoniteur');
    }

	public function getAllMoniteurs(): array
	{
		$query = "SELECT * FROM moniteur";
        $stmt = $this->getPdo()->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
	}
}
