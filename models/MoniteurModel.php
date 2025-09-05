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
}
