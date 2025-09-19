<?php

namespace models;

use models\base\SQL;

/**
 * Champs:
 * - idvehicule (int, PK)
 * - nbpassagers (int)
 * - immatriculation (varchar)
 * - designation (varchar)
 * - manuel (tinyint)
 */
class VehiculeModel extends SQL
{
    public function __construct()
    {
        parent::__construct('vehicule', 'idvehicule');
    }

	public function getAllVehicules(): array
	{
		$query = "SELECT * FROM vehicule";
		$stmt = $this->getPdo()->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll();
	}
}
