<?php

namespace routes;

use controllers\CompteController;
use utils\Template;
use routes\base\Route;
use utils\SessionHelpers;
use controllers\PublicWebController;
use controllers\UtilisateurController;
use controllers\CompteApiController;

class Web
{
    function __construct()
    {
        $public = new PublicWebController();
        $utilisateur = new UtilisateurController();
        $compte = new CompteController();
		$compteApi = new CompteApiController();

        // Appel la méthode « home » dans le contrôleur $main.
        Route::Add('/', [$public, 'home']);
        Route::Add('/forfaits.html', [$public, 'forfait']);

		// ! LOT 1 : Ajout de la route "À propos"
		Route::Add('/a-propos.html', [$public, 'aPropos']);
		// ! LOT 2 : Ajout de la route "Notre équipe"
		Route::Add("/notre-equipe.html", [$public, 'notreEquipe']);

        // Gestion utilisateur
        Route::Add('/creer-compte.html', [$utilisateur, 'creerCompte']);
        Route::Add('/connexion.html', [$utilisateur, 'connexion']);
        Route::Add('/mot-de-passe-oublie.html', [$utilisateur, 'motDePasseOublie']);

        // Gestion de l'offre
        Route::Add('/activer-offre.html', [$utilisateur, 'activerOffre']);
        Route::Add('/confirmer-abonnement.html', [$utilisateur, 'confirmerAbonnement']);

        // Documentation API
		Route::Add('/connexion-api.html', [$compteApi, 'connexion']);
		Route::Add('/demande-acces-api.html', [$compteApi, 'demandeAcces']);
		Route::Add('/creer-compte-api.html', [$compteApi, 'creerCompteApi']);
		Route::Add('/mot-de-passe-oublie-api.html', [$compteApi, 'motDePasseOublie']);
		if (SessionHelpers::isLoginApi()) {
			Route::Add('/deconnexion-api.html', [$compteApi, 'deconnexion']);
			Route::Add('/documentation-api.html', function () {
				return Template::render('views/global/documentation-api.php');
			});
		}

        // Si l'utilisateur est connecté, ajoute les routes de déconnexion et de compte.
        if (SessionHelpers::isLogin()) {
            Route::Add('/deconnexion.html', [$compte, 'deconnexion']);
            Route::Add('/mon-compte/planning.html', [$compte, 'planning']);
            Route::Add('/mon-compte/planning/details.html', [$compte, 'detailsLecon']);
            Route::Add('/mon-compte/planning/annuler-lecon.html', [$compte, 'annulerLecon']);
            Route::Add('/mon-compte/demande-heure-supplementaire.html', [$compte, 'demandeHeureSupplementaire']);
			Route::Add('/mon-compte/results.html', [$compte, 'results']);
            Route::Add('/mon-compte/profil.html', [$compte, 'mesInformations']);
            Route::Add('/mon-compte/changer-mot-de-passe.html', [$compte, 'changerMotDePasse']);
            Route::Add('/mon-compte/', [$compte, 'monCompte']);
        }

        // Appel la fonction inline dans le routeur.
        // Utile pour du code très simple, où un tes, l'utilisation d'un contrôleur est préférable.
        /* Route::Add('/about', function () {
            return Template::render('views/global/about.php');
        }); */
    }
}
