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
    private InscrireModel $inscrireModel;

    function __construct()
    {
        $this->eleveModel = new EleveModel();
        $this->forfaitModel = new ForfaitModel();
        $this->inscrireModel = new InscrireModel();
    }

    /**
     * Affiche le formulaire de création de compte et gère la soumission du formulaire.
     *
     * @return string
     */
    public function creerCompte(): string
    {
        if (SessionHelpers::isLogin()) {
            $this->redirect('/mon-compte/');
        }

        if ($this->isPost()) {
            $nom = $_POST['nom'] ?? null;
            $prenom = $_POST['prenom'] ?? null;
	    	$telephone = $_POST['telephone'] ?? null;
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;
            $confirmPassword = $_POST['confirm_password'] ?? null;
            $dateNaissance = $_POST['date_naissance'] ?? null;

            if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($confirmPassword) || empty($dateNaissance)) {
                SessionHelpers::setFlashMessage('error', 'Tous les champs sont requis sauf le numéro de téléphone.');
                $this->redirect('/creer-compte.html');
            }

            if (!empty($telephone) && !SessionHelpers::isValidFrenchPhone($telephone)) {
                SessionHelpers::setFlashMessage('error', 'Le numéro de téléphone doit contenir 10 chiffres ou être vide.');
                $this->redirect('/creer-compte.html');
            }

            if (!SessionHelpers::validatePassword($password)) {
                $errorMessage = SessionHelpers::getPasswordValidationError($password);
                SessionHelpers::setFlashMessage('error', $errorMessage);
                $this->redirect('/creer-compte.html');
            }

            if ($password !== $confirmPassword) {
                SessionHelpers::setFlashMessage('error', 'Les mots de passe ne correspondent pas.');
            } else {
                $cleanPhone = SessionHelpers::cleanPhoneForStorage($telephone);
                $cleanPhone = $cleanPhone ?? '';
                
                $success = $this->eleveModel->creer_eleve($nom, $prenom, $cleanPhone, $email, $password, $dateNaissance);

                if ($success) {
                    if (isset($_SESSION['forfait_selectionne'])) {
                        unset($_SESSION['FLASH']['info']);
                        SessionHelpers::setFlashMessage('success', 'Compte créé avec succès ! Vous pouvez maintenant confirmer votre abonnement au forfait.');
                        $this->redirect("/confirmer-abonnement.html");
                    } else {
                        $this->redirect('/mon-compte/');
                    }
                } else {
                    SessionHelpers::setFlashMessage('error', "L'adresse email est déjà utilisée ou une erreur est survenue.");
                }
            }
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
        if (SessionHelpers::isLogin()) {
            $this->redirect('/mon-compte/');
        }

        if ($this->isPost()) {
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;

            if (empty($email) || empty($password)) {
                SessionHelpers::setFlashMessage('error', 'Tous les champs sont requis.');
                $this->redirect('/connexion.html');
            }

            $eleve = $this->eleveModel->connexion($email, $password);

            if ($eleve) {
                if (isset($_SESSION['forfait_selectionne'])) {
                    unset($_SESSION['FLASH']['info']);
                    
                    $idEleve = $eleve['ideleve'];
                    if ($this->inscrireModel->eleveAForfaitActif($idEleve)) {
                        unset($_SESSION['forfait_selectionne']);
                        SessionHelpers::setFlashMessage('info', 'Vous avez déjà un forfait actif. Vous ne pouvez pas souscrire à un autre forfait.');
                        $this->redirect('/mon-compte/');
                    } else {
                        $this->redirect("/confirmer-abonnement.html");
                    }
                } else {
                    $this->redirect('/mon-compte/');
                }
            } else {
                SessionHelpers::setFlashMessage('error', 'Identifiants incorrects.');
                $this->redirect('/connexion.html');
            }
        }

        return Template::render("views/utilisateur/connexion.php", [
            'titre' => 'Connexion',
            'error' => SessionHelpers::getFlashMessage('error')
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
		$eleve = $this->eleveModel->getByEmail($resendEmail);
		if ($eleve) {
		    $newToken = bin2hex(random_bytes(3));
		    $this->eleveModel->saveResetToken($eleve['ideleve'], $resendEmail, $newToken);
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
			$eleve = $this->eleveModel->validateResetToken($token);
			if (!$eleve) {
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idforfait'])) {
            $idForfait = $_POST['idforfait'];
            
            if (empty($idForfait) || !is_numeric($idForfait)) {
                SessionHelpers::setFlashMessage('error', 'Forfait non valide.');
                $this->redirect('/forfaits.html');
            }

            $forfait = $this->forfaitModel->getById((int)$idForfait);
            if (!$forfait) {
                SessionHelpers::setFlashMessage('error', 'Forfait introuvable.');
                $this->redirect('/forfaits.html');
            }

            $_SESSION['forfait_selectionne'] = $idForfait;
            
            $this->redirect('/activer-offre.html');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_choice']) && !SessionHelpers::isLogin()) {
            SessionHelpers::setFlashMessage('info', 'Votre forfait sera activé après votre authentification.');
            $this->redirect('/connexion.html');
        }

        $idForfait = $_SESSION['forfait_selectionne'] ?? null;

        if (empty($idForfait)) {
            SessionHelpers::setFlashMessage('error', 'Aucun forfait sélectionné.');
            $this->redirect('/forfaits.html');
        }

        $forfait = $this->forfaitModel->getById((int)$idForfait);
        if (!$forfait) {
            SessionHelpers::setFlashMessage('error', 'Forfait introuvable.');
            $this->redirect('/forfaits.html');
        }

        if (!SessionHelpers::isLogin()) {
            
            return Template::render(
                "views/utilisateur/activer-offre.php",
                [
                    'titre' => 'Confirmer le choix du forfait',
                    'forfait' => $forfait,
                    'step' => 'confirm_choice_not_connected'
                ]
            );
        }

        $eleveConnecte = SessionHelpers::getConnected();
        $idEleve = $eleveConnecte['ideleve'];

        if ($this->inscrireModel->eleveAForfaitActif($idEleve)) {
            SessionHelpers::setFlashMessage('error', 'Vous avez déjà un forfait actif. Vous ne pouvez pas vous inscrire à un nouveau forfait.');
            return Template::render(
                "views/utilisateur/activer-offre.php",
                [
                    'titre' => 'Erreur d\'activation',
                    'forfait' => $forfait,
                    'step' => 'error',
                    'error' => SessionHelpers::getFlashMessage('error')
                ]
            );
        }

        if ($this->isPost()) {
            $success = $this->inscrireModel->inscrireEleve($idEleve, (int)$idForfait);

            if ($success) {
                unset($_SESSION['forfait_selectionne']);
                
                SessionHelpers::setFlashMessage('success', 'Félicitations ! Votre forfait "' . htmlspecialchars($forfait->libelleforfait) . '" a été activé avec succès.');
                
                return Template::render(
                    "views/utilisateur/activer-offre.php",
                    [
                        'titre' => 'Forfait Activé',
                        'forfait' => $forfait,
                        'step' => 'success',
                        'success' => SessionHelpers::getFlashMessage('success')
                    ]
                );
            } else {
                SessionHelpers::setFlashMessage('error', 'Une erreur est survenue lors de l\'activation de votre forfait.');
                return Template::render(
                    "views/utilisateur/activer-offre.php",
                    [
                        'titre' => 'Erreur d\'activation',
                        'forfait' => $forfait,
                        'step' => 'error',
                        'error' => SessionHelpers::getFlashMessage('error')
                    ]
                );
            }
        }

        return Template::render(
            "views/utilisateur/activer-offre.php",
            [
                'titre' => 'Confirmer l\'activation du forfait',
                'forfait' => $forfait,
                'step' => 'confirm_activation_connected'
            ]
        );
    }

    /**
     * Confirme l'abonnement après connexion/inscription
     */
    public function confirmerAbonnement(): string
    {
        if (!SessionHelpers::isLogin()) {
            $this->redirect('/connexion.html');
        }

        $idForfait = $_SESSION['forfait_selectionne'] ?? null;

        if (empty($idForfait)) {
            SessionHelpers::setFlashMessage('error', 'Aucun forfait sélectionné.');
            $this->redirect('/forfaits.html');
        }

        $forfait = $this->forfaitModel->getById((int)$idForfait);
        if (!$forfait) {
            SessionHelpers::setFlashMessage('error', 'Forfait introuvable.');
            $this->redirect('/forfaits.html');
        }

        $eleveConnecte = SessionHelpers::getConnected();
        $idEleve = $eleveConnecte['ideleve'];

        if ($this->inscrireModel->eleveAForfaitActif($idEleve)) {
            SessionHelpers::setFlashMessage('error', 'Vous avez déjà un forfait actif. Vous ne pouvez pas vous inscrire à un nouveau forfait.');
            return Template::render(
                "views/utilisateur/confirmer-abonnement.php",
                [
                    'titre' => 'Erreur d\'activation',
                    'forfait' => $forfait,
                    'step' => 'error',
                    'error' => SessionHelpers::getFlashMessage('error')
                ]
            );
        }

        if ($this->isPost()) {
            $success = $this->inscrireModel->inscrireEleve($idEleve, (int)$idForfait);

            if ($success) {
                unset($_SESSION['forfait_selectionne']);
                
                SessionHelpers::setFlashMessage('success', 'Félicitations ! Votre forfait "' . htmlspecialchars($forfait->libelleforfait) . '" a été activé avec succès.');
                
                return Template::render(
                    "views/utilisateur/confirmer-abonnement.php",
                    [
                        'titre' => 'Forfait Activé',
                        'forfait' => $forfait,
                        'step' => 'success',
                        'success' => SessionHelpers::getFlashMessage('success')
                    ]
                );
            } else {
                SessionHelpers::setFlashMessage('error', 'Une erreur est survenue lors de l\'activation de votre forfait.');
                return Template::render(
                    "views/utilisateur/confirmer-abonnement.php",
                    [
                        'titre' => 'Erreur d\'activation',
                        'forfait' => $forfait,
                        'step' => 'error',
                        'error' => SessionHelpers::getFlashMessage('error')
                    ]
                );
            }
        }

        return Template::render(
            "views/utilisateur/confirmer-abonnement.php",
            [
                'titre' => 'Confirmer votre abonnement',
                'forfait' => $forfait,
                'step' => 'confirm'
            ]
        );
    }
}
