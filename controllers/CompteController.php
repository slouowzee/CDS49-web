<?php

namespace controllers;

use utils\Template;
use models\EleveModel;
use models\InscrireModel;
use utils\SessionHelpers;
use controllers\base\WebController;
use models\ConduireModel;
use models\ResultatModel;

class CompteController extends WebController
{
    private EleveModel $eleveModel;
    private InscrireModel $inscrireModel;
    private ConduireModel $conduireModel;
	private ResultatModel $resultatModel;

    public function __construct()
    {
        $this->eleveModel = new EleveModel();
        $this->inscrireModel = new InscrireModel();
        $this->conduireModel = new ConduireModel();
		$this->resultatModel = new ResultatModel();
    }

    /**
     * Affiche le compte utilisateur.
     *
     * @return void
     */
    public function monCompte(): void
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

            // Validation des champs obligatoires (téléphone peut être vide)
            if (empty($nom) || empty($prenom) || empty($email) || empty($dateNaissance)) {
                SessionHelpers::setFlashMessage('error', 'Tous les champs sont requis sauf le numéro de téléphone.');
                $this->redirect('/mon-compte/profil.html');
            }

            // Validation du format du téléphone (plus permissive)
            if (!empty($telephone) && !SessionHelpers::isValidFrenchPhone($telephone)) {
                SessionHelpers::setFlashMessage('error', 'Le numéro de téléphone doit contenir 10 chiffres ou être vide.');
                $this->redirect('/mon-compte/profil.html');
            }

            // Nettoyer le téléphone pour le stockage
            $cleanPhone = SessionHelpers::cleanPhoneForStorage($telephone);
            // Si le téléphone est null, utiliser une chaîne vide
            $cleanPhone = $cleanPhone ?? '';

            // Mise à jour des informations de l'utilisateur dans la base de données
            $success = $this->eleveModel->update(
                SessionHelpers::getConnected()['ideleve'],
                $nom,
                $prenom,
                $cleanPhone,
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
     * Permet à l'utilisateur de changer son mot de passe.
     */
    public function changerMotDePasse(): string
    {
        if ($this->isPost()) {
            $motDePasseActuel = $_POST['current_password'] ?? null;
            $nouveauMotDePasse = $_POST['new_password'] ?? null;
            $confirmMotDePasse = $_POST['confirm_password'] ?? null;

            // Validation des champs
            if (empty($motDePasseActuel) || empty($nouveauMotDePasse) || empty($confirmMotDePasse)) {
                SessionHelpers::setFlashMessage('error', 'Tous les champs sont requis.');
                $this->redirect('/mon-compte/changer-mot-de-passe.html');
            }

            // Vérifier que les nouveaux mots de passe correspondent
            if ($nouveauMotDePasse !== $confirmMotDePasse) {
                SessionHelpers::setFlashMessage('error', 'Les nouveaux mots de passe ne correspondent pas.');
                $this->redirect('/mon-compte/changer-mot-de-passe.html');
            }

            // Validation du nouveau mot de passe
            if (!SessionHelpers::validatePassword($nouveauMotDePasse)) {
                $errorMessage = SessionHelpers::getPasswordValidationError($nouveauMotDePasse);
                SessionHelpers::setFlashMessage('error', $errorMessage);
                $this->redirect('/mon-compte/changer-mot-de-passe.html');
            }

            // Vérifier le mot de passe actuel
            $eleveConnecte = SessionHelpers::getConnected();
            $eleve = $this->eleveModel->getMe();
            
            if (!$eleve || !password_verify($motDePasseActuel . $_ENV['PEPPER'], $eleve['motpasseeleve'])) {
                SessionHelpers::setFlashMessage('error', 'Le mot de passe actuel est incorrect.');
                $this->redirect('/mon-compte/changer-mot-de-passe.html');
            }

            // Mettre à jour le mot de passe
            $success = $this->eleveModel->update(
                $eleveConnecte['ideleve'],
                $eleve['nomeleve'],
                $eleve['prenomeleve'],
                $eleve['teleleve'],
                $eleve['emaileleve'],
                $eleve['datenaissanceeleve'],
                $nouveauMotDePasse
            );

            if ($success) {
                SessionHelpers::setFlashMessage('success', 'Votre mot de passe a été modifié avec succès.');
            } else {
                SessionHelpers::setFlashMessage('error', 'Une erreur est survenue lors de la modification du mot de passe.');
            }

            $this->redirect('/mon-compte/changer-mot-de-passe.html');
        }

        return Template::render(
            "views/utilisateur/compte/changer-mot-de-passe.php",
            [
                'titre' => 'Changer mon mot de passe',
                'error' => SessionHelpers::getFlashMessage('error'),
                'success' => SessionHelpers::getFlashMessage('success')
            ]
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
                'success' => SessionHelpers::getFlashMessage('success'),
                'info' => SessionHelpers::getFlashMessage('info')
            ]
        );
    }

	public function results() {
		$results = $this->resultatModel->getResults();

		return Template::render(
			"views/utilisateur/compte/results.php",
			[
				'titre' => 'Mes résultats',
				'results' => $results,
				'eleve' => SessionHelpers::getConnected(),
                'error' => SessionHelpers::getFlashMessage('error'),
                'success' => SessionHelpers::getFlashMessage('success'),
                'info' => SessionHelpers::getFlashMessage('info')
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
