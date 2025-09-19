<?php

namespace controllers;

use utils\Template;
use models\ForfaitModel;
use models\MoniteurModel;
use models\VehiculeModel;
use controllers\base\WebController;

class PublicWebController extends WebController
{
    private ForfaitModel $forfaitModel;
    private MoniteurModel $moniteurModel;
    private VehiculeModel $vehiculeModel;

    public function __construct()
    {
        $this->forfaitModel = new ForfaitModel();
        $this->moniteurModel = new MoniteurModel();
        $this->vehiculeModel = new VehiculeModel();
    }

    function home(): string
    {
        return Template::render("views/global/home.php");
    }

    function forfait(): string
    {
        // Récupération des forfaits depuis la base de données
        $forfaits = $this->forfaitModel->getByPrice();

        return Template::render("views/global/forfaits.php", [
            'forfaits' => $forfaits
        ]);
    }

    // ! LOT 1 : Ajout de la méthode "aPropos"
    function aPropos(): string
    {
	return Template::render("views/global/a-propos.php");
    }

	// ! LOT 2 : Ajout de la méthode "notreEquipe"
    function notreEquipe(): string
    {
        // Récupération des moniteurs et véhicules depuis la base de données
        $moniteurs = $this->moniteurModel->getAllMoniteurs();
        $vehicules = $this->vehiculeModel->getAllVehicules();

        return Template::render("views/global/notre-equipe.php", [
            'moniteurs' => $moniteurs,
            'vehicules' => $vehicules
        ]);
    }
}
