<?php

namespace controllers;

use utils\Template;
use models\EleveModel;
use models\InscrireModel;
use utils\SessionHelpers;
use controllers\base\WebController;
use models\ForfaitModel;

class UtilisateurController extends WebController
{
    private EleveModel $eleveModel;
    private ForfaitModel $forfaitModel;

    function __construct()
    {
        $this->eleveModel = new EleveModel();
        $this->forfaitModel = new ForfaitModel();
    }

    /**
     * Affiche le formulaire de création de compte et gère la soumission du formulaire.
     *
     * @return string
     */
    public function creerCompte(): string
    {
        // Si l'utilisateur est déjà connecté, redirige vers la page de compte.
        if (SessionHelpers::isLogin()) {
            $this->redirect('/mon-compte/');
        }

        // Si la requête est de type POST, traite la soumission du formulaire.
        if ($this->isPost()) {
            $nom = $_POST['nom'] ?? null;
            $prenom = $_POST['prenom'] ?? null;
	    $telephone = $_POST['telephone'] ?? null;
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;
            $confirmPassword = $_POST['confirm_password'] ?? null;
            $dateNaissance = $_POST['date_naissance'] ?? null;

            if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($confirmPassword) || empty($dateNaissance)) {
                SessionHelpers::setFlashMessage('error', 'Tous les champs sont requis.');
                $this->redirect('/creer-compte.html');
            }

            if ($password !== $confirmPassword) {
                SessionHelpers::setFlashMessage('error', 'Les mots de passe ne correspondent pas.');
            } else {
                // Création de l'élève dans la base de données (en utilisant le modèle EleveModel).
                $success = $this->eleveModel->creer_eleve($nom, $prenom, $telephone, $email, $password, $dateNaissance);

                if ($success) {
                    $this->redirect('/mon-compte/');
                } else {
                    SessionHelpers::setFlashMessage('error', "L'adresse email est déjà utilisée ou une erreur est survenue.");
                }
            }
            // Rediriger vers la page de création de compte pour afficher le message d'erreur ou si la création a échoué
            $this->redirect('/creer-compte.html');
        }

        return Template::render(
            "views/utilisateur/creer-compte.php",
            [
                'titre' => 'Créer un compte',
                'error' => SessionHelpers::getFlashMessage('error'),
                'success' => SessionHelpers::getFlashMessage('success')
            ]
        );
    }

    /**
     * Affiche le formulaire de connexion.
     *
     * @return string
     */
    public function connexion(): string
    {
        // Si l'utilisateur est déjà connecté, redirige vers la page de compte.
        if (SessionHelpers::isLogin()) {
            $this->redirect('/mon-compte/');
        }

        // Si la requête est de type POST, traite la soumission du formulaire.
        if ($this->isPost()) {
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;

            if (empty($email) || empty($password)) {
                SessionHelpers::setFlashMessage('error', 'Tous les champs sont requis.');
                $this->redirect('/connexion.html');
            }

            // Vérification des identifiants de l'utilisateur (en utilisant le modèle EleveModel).
            $eleve = $this->eleveModel->connexion($email, $password);

            if ($eleve) {
                $this->redirect('/mon-compte/');
            } else {
                SessionHelpers::setFlashMessage('error', 'Identifiants incorrects.');
                $this->redirect('/connexion.html');
            }
        }

        return Template::render("views/utilisateur/connexion.php", [
            'titre' => 'Connexion',
            'error' => SessionHelpers::getFlashMessage('error'),
            'success' => SessionHelpers::getFlashMessage('success')
        ]);
    }

    /**
     * Affiche le formulaire de mot de passe oublié.
     *
     * @return string
     */
    public function motDePasseOublie(): string
    {
        // Vérifier si un email est passé en paramètre GET pour afficher directement le formulaire de token
        $emailFromUrl = $_GET['email'] ?? null;
        if ($emailFromUrl && !$this->isPost()) {
            // Afficher directement le formulaire de saisie du token
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

	    // Gestion du renvoi d'email
	    if (!empty($resendEmail)) {
		$eleve = $this->eleveModel->getByEmail($resendEmail);
		if ($eleve) {
		    $newToken = bin2hex(random_bytes(3));
		    $this->eleveModel->saveResetToken($eleve['ideleve'], $resendEmail, $newToken);
		    SessionHelpers::setFlashMessage('success', 'Un nouveau jeton de réinitialisation a été envoyé à votre adresse email.');
		    SessionHelpers::setFlashMessage('show_token_form', true);
		    SessionHelpers::setFlashMessage('email_used', $resendEmail); // Conserver l'email pour les prochains renvois
		} else {
		    SessionHelpers::setFlashMessage('error', 'Aucun compte trouvé avec cette adresse email.');
		    SessionHelpers::setFlashMessage('show_token_form', true);
		}
	    } elseif (!isset($_POST['token'])) {
		// Étape 1 : Demande de réinitialisation par email
		if (empty($email)) {
		    SessionHelpers::setFlashMessage('error', 'L\'adresse email est requise.');
		    $this->redirect('/mot-de-passe-oublie.html');
		}

		$eleve = $this->eleveModel->getByEmail($email);

		if (!$eleve) {
		    SessionHelpers::setFlashMessage('error', 'Aucun compte trouvé avec cette adresse email.');
		    $this->redirect('/mot-de-passe-oublie.html');
		} else {
		    $token = bin2hex(random_bytes(3));
		    $this->eleveModel->saveResetToken($eleve['ideleve'], $email, $token);

		    SessionHelpers::setFlashMessage('token', $token);
		    SessionHelpers::setFlashMessage('email_used', $email);
		    SessionHelpers::setFlashMessage('success', 'Un jeton de réinitialisation a été envoyé à votre adresse email. Il expire dans 15 minutes.');
		    $this->redirect('/mot-de-passe-oublie.html');
		}
	    } else {
		// Étape 2 : Validation du token et nouveau mot de passe
		if (empty($token)) {
		    SessionHelpers::setFlashMessage('error', 'Le jeton de réinitialisation est requis.');
		    SessionHelpers::setFlashMessage('show_token_form', true);
		} else {
		    // Si un nouveau mot de passe est fourni, procéder à la réinitialisation
		    if (!empty($nouveauMotDePasse)) {
			if (empty($confirmMotDePasse)) {
			    SessionHelpers::setFlashMessage('error', 'La confirmation du mot de passe est requise.');
			    SessionHelpers::setFlashMessage('token_valide', $token);
			} elseif ($nouveauMotDePasse !== $confirmMotDePasse) {
			    SessionHelpers::setFlashMessage('error', 'Les mots de passe ne correspondent pas.');
			    SessionHelpers::setFlashMessage('token_valide', $token);
			} elseif (strlen($nouveauMotDePasse) < 6) {
			    SessionHelpers::setFlashMessage('error', 'Le mot de passe doit contenir au moins 6 caractères.');
			    SessionHelpers::setFlashMessage('token_valide', $token);
			} else {
			    // Tenter de réinitialiser le mot de passe
			    $success = $this->eleveModel->resetPasswordWithToken($token, $nouveauMotDePasse);

			    if ($success) {
				SessionHelpers::setFlashMessage('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
				$this->redirect('/connexion.html');
			    } else {
				SessionHelpers::setFlashMessage('error', 'Le jeton de réinitialisation est invalide ou a expiré. Veuillez faire une nouvelle demande.');
				$this->redirect('/mot-de-passe-oublie.html');
			    }
			}
		    } else {
			// Vérifier si le token est valide avant d'afficher le formulaire de nouveau mot de passe
			$eleve = $this->eleveModel->validateResetToken($token);
			if (!$eleve) {
			    SessionHelpers::setFlashMessage('error', 'Le jeton de réinitialisation est invalide ou a expiré. Veuillez faire une nouvelle demande.');
			    // Rester sur l'étape de saisie du token pour permettre un nouvel essai
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
            "views/utilisateur/mot-de-passe-oublie.php",
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

    /**
     * Code d'activation d'une offre.
     * Cette méthode permet à l'utilisateur d'activer l'offre choisi (passage de paramètre dans l'URL).
     */
    public function activerOffre(): string
    {
        $idForfait = $_GET['idforfait'] ?? null;

        $forfait = $this->forfaitModel->getById($idForfait);

        if (!$forfait) {
            $this->redirect('/forfaits.html');
        }

        return Template::render(
            "views/utilisateur/activer-offre.php",
            [
                'titre' => 'Activer une offre',
                'error' => SessionHelpers::getFlashMessage('error'),
                'success' => SessionHelpers::getFlashMessage('success')
            ]
        );
    }
}
