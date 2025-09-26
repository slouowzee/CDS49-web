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

            // Validation des champs obligatoires (téléphone peut être vide)
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
                
                // Création de l'élève dans la base de données (en utilisant le modèle EleveModel).
                $success = $this->eleveModel->creer_eleve($nom, $prenom, $cleanPhone, $email, $password, $dateNaissance);

                if ($success) {
                    if (isset($_SESSION['forfait_selectionne'])) {
                        $this->redirect("/confirmer-abonnement.html");
                    } else {
                        $this->redirect('/mon-compte/');
                    }
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
                if (isset($_SESSION['forfait_selectionne'])) {
                    $this->redirect("/confirmer-abonnement.html");
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
			} elseif (!SessionHelpers::validatePassword($nouveauMotDePasse)) {
			    $errorMessage = SessionHelpers::getPasswordValidationError($nouveauMotDePasse);
			    SessionHelpers::setFlashMessage('error', $errorMessage);
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
        // Si on reçoit un POST avec l'ID du forfait, on le stocke en session
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idforfait'])) {
            $idForfait = $_POST['idforfait'];
            
            // Vérifier que l'ID forfait est valide
            if (empty($idForfait) || !is_numeric($idForfait)) {
                SessionHelpers::setFlashMessage('error', 'Forfait non valide.');
                $this->redirect('/forfaits.html');
            }

            // Récupérer les informations du forfait pour vérifier qu'il existe
            $forfait = $this->forfaitModel->getById((int)$idForfait);
            if (!$forfait) {
                SessionHelpers::setFlashMessage('error', 'Forfait introuvable.');
                $this->redirect('/forfaits.html');
            }

            // Stocker le forfait en session
            $_SESSION['forfait_selectionne'] = $idForfait;
            
            // Rediriger vers cette même page en GET pour afficher la confirmation
            $this->redirect('/activer-offre.html');
        }

        // Si on reçoit un POST avec confirmation pour utilisateur non connecté
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_choice']) && !SessionHelpers::isLogin()) {
            // Rediriger vers la page de connexion
            SessionHelpers::setFlashMessage('info', 'Votre forfait sera activé après votre authentification.');
            $this->redirect('/connexion.html');
        }

        // Récupérer l'ID du forfait depuis la session
        $idForfait = $_SESSION['forfait_selectionne'] ?? null;

        // Vérifier qu'un forfait est sélectionné
        if (empty($idForfait)) {
            SessionHelpers::setFlashMessage('error', 'Aucun forfait sélectionné.');
            $this->redirect('/forfaits.html');
        }

        // Récupérer les informations du forfait
        $forfait = $this->forfaitModel->getById((int)$idForfait);
        if (!$forfait) {
            SessionHelpers::setFlashMessage('error', 'Forfait introuvable.');
            $this->redirect('/forfaits.html');
        }

        // Si l'utilisateur n'est pas connecté
        if (!SessionHelpers::isLogin()) {
            
            // Afficher la première confirmation (pour utilisateur non connecté)
            return Template::render(
                "views/utilisateur/activer-offre.php",
                [
                    'titre' => 'Confirmer le choix du forfait',
                    'forfait' => $forfait,
                    'step' => 'confirm_choice_not_connected'
                ]
            );
        }

        // L'utilisateur est connecté
        $eleveConnecte = SessionHelpers::getConnected();
        $idEleve = $eleveConnecte['ideleve'];

        // Vérifier si l'élève a déjà un forfait actif
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

        // Si c'est la confirmation finale (POST pour utilisateur connecté)
        if ($this->isPost()) {
            // Activer le forfait
            $success = $this->inscrireModel->inscrireEleve($idEleve, (int)$idForfait);

            if ($success) {
                // Nettoyer la session
                unset($_SESSION['forfait_selectionne']);
                
                // Message de succès
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

        // Afficher la confirmation finale (pour utilisateur connecté)
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
        // Vérifier que l'utilisateur est connecté
        if (!SessionHelpers::isLogin()) {
            $this->redirect('/connexion.html');
        }

        // Récupérer l'ID du forfait depuis la session
        $idForfait = $_SESSION['forfait_selectionne'] ?? null;

        // Vérifier qu'un forfait est sélectionné
        if (empty($idForfait)) {
            SessionHelpers::setFlashMessage('error', 'Aucun forfait sélectionné.');
            $this->redirect('/forfaits.html');
        }

        // Récupérer les informations du forfait
        $forfait = $this->forfaitModel->getById((int)$idForfait);
        if (!$forfait) {
            SessionHelpers::setFlashMessage('error', 'Forfait introuvable.');
            $this->redirect('/forfaits.html');
        }

        $eleveConnecte = SessionHelpers::getConnected();
        $idEleve = $eleveConnecte['ideleve'];

        // Vérifier si l'élève a déjà un forfait actif
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

        // Si c'est une confirmation (POST)
        if ($this->isPost()) {
            // Activer le forfait
            $success = $this->inscrireModel->inscrireEleve($idEleve, (int)$idForfait);

            if ($success) {
                // Nettoyer la session
                unset($_SESSION['forfait_selectionne']);
                
                // Message de succès
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

        // Afficher la confirmation finale
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
