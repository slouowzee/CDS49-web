<?php

namespace controllers;

use utils\Template;
use models\EleveModel;
use models\InscrireModel;
use utils\SessionHelpers;
use controllers\base\WebController;
use models\ConduireModel;

class CompteController extends WebController
{
    private EleveModel $eleveModel;
    private InscrireModel $inscrireModel;
    private ConduireModel $conduireModel;

    public function __construct()
    {
        $this->eleveModel = new EleveModel();
        $this->inscrireModel = new InscrireModel();
        $this->conduireModel = new ConduireModel();
    }

    /**
     * Affiche le compte utilisateur.
     *
     * @return string
     */
    public function monCompte(): string
    {
        $this->redirect('/mon-compte/planning.html');
    }

    public function mesInformations(): string
    {
        if ($this->isPost()) {
            // Traitement de la mise à jour des informations de l'utilisateur
            $nom = $_POST['nom'] ?? null;
            $prenom = $_POST['prenom'] ?? null;
            $telephone = $_POST['telephone'] ?? null;
            $email = $_POST['email'] ?? null;
            $dateNaissance = $_POST['datenaissance'] ?? null;

            if (empty($nom) || empty($prenom) || empty($telephone) || empty($email) || empty($dateNaissance)) {
                SessionHelpers::setFlashMessage('error', 'Tous les champs sont requis.');
                $this->redirect('/mon-compte/profil.html');
            }

            // Mise à jour des informations de l'utilisateur dans la base de données
            $success = $this->eleveModel->update(
                SessionHelpers::getConnected()['ideleve'],
                $nom,
                $prenom,
		$telephone,
                $email,
                $dateNaissance
            );

            if ($success) {
                SessionHelpers::setFlashMessage('success', 'Vos informations ont été mises à jour avec succès.');
            } else {
                SessionHelpers::setFlashMessage('error', "Une erreur est survenue lors de la mise à jour de vos informations.");
            }
        }

        return template::render(
            "views/utilisateur/compte/mes-informations.php",
            [
                'titre' => 'Mes informations',
                'error' => SessionHelpers::getFlashMessage('error'),
                'success' => SessionHelpers::getFlashMessage('success'),
            ] + $this->eleveModel->getMe() // Ajoute l'ensemble des informations de l'utilisateur connecté (concatène les données de l'utilisateur connecté + les données de la vue, résultat un tableau associatif)
        );
    }

    /**
     * Affiche le planning de l'utilisateur connecté.
     */
    public function planning(): string
    {
        // Récupération du forfait de l'utilisateur connecté
        $forfait = $this->inscrireModel->getForfaitEleveConnecte();

        // Récupération du planning de l'utilisateur connecté (modèle conduire)
        $planning = $this->conduireModel->getLessonsByEleve();

        return Template::render(
            "views/utilisateur/compte/planning.php",
            [
                'titre' => 'Mon planning',
                'forfait' => $forfait,
                'planning' => $planning,
                'eleve' => SessionHelpers::getConnected(),
                'error' => SessionHelpers::getFlashMessage('error'),
                'success' => SessionHelpers::getFlashMessage('success')
            ]
        );
    }

    /**
     * Déconnecte l'utilisateur.
     *
     * @return void
     */
    public function deconnexion(): void
    {
        SessionHelpers::logout();
        $this->redirect('/');
    }
}
