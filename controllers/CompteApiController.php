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

	    /**
     * Affiche le formulaire de mot de passe oublié.
     *
     * @return string
     */
    public function motDePasseOublie(): string
    {
        $emailFromUrl = $_GET['email'] ?? null;
        if ($emailFromUrl && !$this->isPost()) {
            SessionHelpers::setFlashMessage('show_token_form', true);
            SessionHelpers::setFlashMessage('email_used', $emailFromUrl);
            SessionHelpers::setFlashMessage('success', 'Veuillez entrer le code de réinitialisation reçu par email.');
        }

        if ($this->isPost()) {
	    $email = $_POST['email'] ?? null;
	    $token = $_POST['token'] ?? null;
	    $nouveauMotDePasse = $_POST['nouveau_mot_de_passe'] ?? null;
	    $confirmMotDePasse = $_POST['confirm_mot_de_passe'] ?? null;
	    $resendEmail = $_POST['resend_email'] ?? null;

	    if (!empty($resendEmail)) {
		$compteApi = $this->compteApiModel->getByEmail($resendEmail);
		if ($compteApi) {
		    $newToken = bin2hex(random_bytes(3));
		    $this->compteApiModel->saveResetToken($compteApi['idcompteapi'], $resendEmail, $newToken);
		    SessionHelpers::setFlashMessage('success', 'Un nouveau jeton de réinitialisation a été envoyé à votre adresse email.');
		    SessionHelpers::setFlashMessage('show_token_form', true);
		    SessionHelpers::setFlashMessage('email_used', $resendEmail);
		} else {
		    SessionHelpers::setFlashMessage('error', 'Aucun compte trouvé avec cette adresse email.');
		    SessionHelpers::setFlashMessage('show_token_form', true);
		}
	    } elseif (!isset($_POST['token'])) {
		if (empty($email)) {
		    SessionHelpers::setFlashMessage('error', 'L\'adresse email est requise.');
		    $this->redirect('/mot-de-passe-oublie-api.html');
		}

		$compteApi = $this->compteApiModel->getByEmail($email);

		if (!$compteApi) {
		    SessionHelpers::setFlashMessage('error', 'Aucun compte trouvé avec cette adresse email.');
		    $this->redirect('/mot-de-passe-oublie-api.html');
		} else {
		    $token = bin2hex(random_bytes(3));
		    $this->compteApiModel->saveResetToken($compteApi['idcompteapi'], $email, $token);

		    SessionHelpers::setFlashMessage('token', $token);
		    SessionHelpers::setFlashMessage('email_used', $email);
		    SessionHelpers::setFlashMessage('success', 'Un jeton de réinitialisation a été envoyé à votre adresse email. Il expire dans 15 minutes.');
		    $this->redirect('/mot-de-passe-oublie-api.html');
		}
	    } else {
			if (empty($token)) {
				SessionHelpers::setFlashMessage('error', 'Le jeton de réinitialisation est requis.');
				SessionHelpers::setFlashMessage('show_token_form', true);
			} else {
				if (!empty($nouveauMotDePasse)) {
					if (empty($confirmMotDePasse)) {
						SessionHelpers::setFlashMessage('error', 'La confirmation du mot de passe est requise.');
						SessionHelpers::setFlashMessage('token_valide', $token);
					} elseif ($nouveauMotDePasse !== $confirmMotDePasse) {
						SessionHelpers::setFlashMessage('error', 'Les mots de passe ne correspondent pas.');
						SessionHelpers::setFlashMessage('token_valide', $token);
					} elseif (!SessionHelpers::validatePassword($nouveauMotDePasse)) {
						$errorMessage = SessionHelpers::getPasswordValidationError($nouveauMotDePasse);
						SessionHelpers::setFlashMessage('error', $errorMessage);
						SessionHelpers::setFlashMessage('token_valide', $token);
					} else {
						$success = $this->compteApiModel->resetPasswordWithToken($token, $nouveauMotDePasse);

						if ($success) {
						SessionHelpers::setFlashMessage('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
						$this->redirect('/connexion-api.html');
						} else {
						SessionHelpers::setFlashMessage('error', 'Le jeton de réinitialisation est invalide ou a expiré. Veuillez faire une nouvelle demande.');
						$this->redirect('/mot-de-passe-oublie-api.html');
						}
					}
				} else {
					$compteApi = $this->compteApiModel->validateResetToken($token);
					if (!$compteApi) {
						SessionHelpers::setFlashMessage('error', 'Le jeton de réinitialisation est invalide ou a expiré. Veuillez faire une nouvelle demande.');
						SessionHelpers::setFlashMessage('show_token_form', true);
					} else {
						SessionHelpers::setFlashMessage('token_valide', $token);
						SessionHelpers::setFlashMessage('success', 'Jeton validé. Veuillez entrer votre nouveau mot de passe.');
					}
				}
			}
	    }
	}
        return Template::render(
            "views/utilisateur/document-api/mot-de-passe-oublie-api.php",
            [
                'titre' => 'Mot de passe oublié',
                'error' => SessionHelpers::getFlashMessage('error'),
                'success' => SessionHelpers::getFlashMessage('success'),
                'token' => SessionHelpers::getFlashMessage('token'),
                'token_valide' => SessionHelpers::getFlashMessage('token_valide'),
                'show_token_form' => SessionHelpers::getFlashMessage('show_token_form'),
                'email_used' => SessionHelpers::getFlashMessage('email_used')
            ]
        );
    }
}