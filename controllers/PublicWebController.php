<?php

namespace controllers;

use utils\Template;
use models\ForfaitModel;
use models\MoniteurModel;
use models\VehiculeModel;
use models\InscrireModel;
use controllers\base\WebController;
use utils\SessionHelpers;

class PublicWebController extends WebController
{
    private ForfaitModel $forfaitModel;
    private MoniteurModel $moniteurModel;
    private VehiculeModel $vehiculeModel;
    private InscrireModel $inscrireModel;

    public function __construct()
    {
        $this->forfaitModel = new ForfaitModel();
        $this->moniteurModel = new MoniteurModel();
        $this->vehiculeModel = new VehiculeModel();
        $this->inscrireModel = new InscrireModel();
    }

    function home(): string
    {
        return Template::render("views/global/home.php");
    }

    function forfait(): string
    {
        // Récupération de tous les forfaits depuis la base de données
        $forfaits = $this->forfaitModel->getAllForfaits();
        
        $hasForfaitActif = false;
        $errorMessage = null;
        
        // Vérifier si l'utilisateur connecté a déjà un forfait actif
        if (SessionHelpers::isLogin()) {
            $eleveConnecte = SessionHelpers::getConnected();
            $idEleve = $eleveConnecte['ideleve'];
            
            if ($this->inscrireModel->eleveAForfaitActif($idEleve)) {
                $hasForfaitActif = true;
                $errorMessage = 'Vous avez déjà un forfait actif. Pour toute modification de votre forfait, veuillez vous rendre dans nos bureaux.';
            }
        }

        return Template::render("views/global/forfaits.php", [
            'forfaits' => $forfaits,
            'hasForfaitActif' => $hasForfaitActif,
            'error' => $errorMessage,
            'flash_error' => SessionHelpers::getFlashMessage('error'),
            'flash_success' => SessionHelpers::getFlashMessage('success')
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
