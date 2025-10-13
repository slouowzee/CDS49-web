<?php

namespace controllers;

use utils\Template;
use utils\SessionHelpers;
use controllers\base\WebController;
use models\CompteApiModel;


class CompteApiController extends WebController
{
	private CompteApiModel $compteApiModel;

	public function __construct()
	{
		$this->compteApiModel = new CompteApiModel();
	}

	/**
	 * Déconnecte l'utilisateur API (vide uniquement les données liées à la connexion API)
	 */
	public function deconnexion(): void
	{
		SessionHelpers::logoutApi();

		$this->redirect('/');
	}

	public function connexion()
	{
		if (SessionHelpers::isLoginApi()) {
			$this->redirect('/documentation-api.html');
		}

        if ($this->isPost()) {
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;

            if (empty($email) || empty($password)) {
                SessionHelpers::setFlashMessage('error', 'Tous les champs sont requis.');
                $this->redirect('/connexion.html');
            }

            $compte = $this->compteApiModel->connexion($email, $password);

            if ($compte) {
                SessionHelpers::loginApi($compte);
				$this->redirect('/documentation-api.html');
            } else {
                SessionHelpers::setFlashMessage('error', 'Identifiants incorrects.');
                $this->redirect('/connexion-api.html');
            }
        }

        return Template::render("views/utilisateur/document-api/connexion-api.php", [
            'titre' => 'Connexion',
            'error' => SessionHelpers::getFlashMessage('error'),
            'success' => SessionHelpers::getFlashMessage('success')
        ]);
	}

	public function demandeAcces()
	{
		if (SessionHelpers::isLoginApi()) {
			$this->redirect('/documentation-api.html');
		}

		if ($this->isPost()) {
			$email = $_POST['email'] ?? null;
			$raison = $_POST['raison'] ?? null;
			$nom = $_POST['nom'] ?? null;
			$prenom = $_POST['prenom'] ?? null;

			if (empty($email) || empty($raison)) {
				SessionHelpers::setFlashMessage('error', 'Tous les champs marqués d\'un * sont requis.');
				$this->redirect('/demande-acces-api.html');
			}

			if ($this->compteApiModel->envoyerDemande($email, $raison, $nom, $prenom)) {
				SessionHelpers::setFlashMessage('success', 'Votre demande a été envoyée avec succès. Nous vous contacterons par email.');
				$this->redirect('/connexion-api.html');
			} else {
				SessionHelpers::setFlashMessage('error', 'Une erreur est survenue lors de l\'envoi de votre demande. Veuillez réessayer plus tard.');
				$this->redirect('/demande-acces-api.html');
			}
		}

		return Template::render("views/utilisateur/document-api/demande-acces-api.php", [
			'titre' => 'Demande d\'accès à l\'API',
			'error' => SessionHelpers::getFlashMessage('error'),
			'success' => SessionHelpers::getFlashMessage('success')
		]);
	}

	public function creerCompteApi()
	{
		if (SessionHelpers::isLoginApi()) {
			$this->redirect('/documentation-api.html');
		}

		$token = $_GET['token'] ?? $_POST['token'] ?? null;

		if (!$token) {
			SessionHelpers::setFlashMessage('error', 'Lien invalide.');
			$this->redirect('/demande-acces-api.html');
		}
		
		$demande = $this->compteApiModel->getDemandeByToken($token);
		
		if (!$demande) {
			SessionHelpers::setFlashMessage('error', 'Ce lien n\'est plus valide ou a déjà été utilisé.');
			$this->redirect('/demande-acces-api.html');
		}

		if ($this->isPost()) {
			$tokenPost = $_POST['token'] ?? null;
			$motDePasse = $_POST['password'] ?? null;
			$confirmMotDePasse = $_POST['confirm_password'] ?? null;

			if (empty($tokenPost) || empty($motDePasse) || empty($confirmMotDePasse)) {
				SessionHelpers::setFlashMessage('error', 'Tous les champs sont requis.');
				$this->redirect('/creer-compte-api.html?token=' . urlencode($tokenPost ?: ''));
			}

			if ($motDePasse !== $confirmMotDePasse) {
				SessionHelpers::setFlashMessage('error', 'Les mots de passe ne correspondent pas.');
				$this->redirect('/creer-compte-api.html?token=' . urlencode($tokenPost));
			}

			if (strlen($motDePasse) < 8) {
				SessionHelpers::setFlashMessage('error', 'Le mot de passe doit contenir au moins 8 caractères.');
				$this->redirect('/creer-compte-api.html?token=' . urlencode($tokenPost));
			}

			if ($this->compteApiModel->creerCompteAvecToken($tokenPost, $motDePasse)) {
				SessionHelpers::setFlashMessage('success', 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.');
				$this->redirect('/connexion-api.html');
			} else {
				SessionHelpers::setFlashMessage('error', 'Une erreur est survenue lors de la création de votre compte. La demande n\'est plus d\'actualité ou un compte existe déjà.');
				$this->redirect('/demande-acces-api.html');
			}
		}

		return Template::render("views/utilisateur/document-api/creer-compte-api.php", [
			'titre' => 'Création de compte API',
			'error' => SessionHelpers::getFlashMessage('error'),
			'success' => SessionHelpers::getFlashMessage('success'),
			'token' => $token,
			'email' => $demande['emaildemandeur']
		]);
	}
}