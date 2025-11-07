<?php

use routes\base\Router;
use utils\SessionHelpers;

include("autoload.php"); // Pour les classes internes (controllers, utils, routes, etc.)
include("vendor/autoload.php"); // Pour les librairies externes. (PHPMailer, etc.)

// Set le fuseau horaire par défaut
date_default_timezone_set('Europe/Paris');

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->safeLoad();

// Configuration des erreurs PHP pour éviter l'affichage HTML dans les réponses API
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/php_errors.log');

/*
 * Permet l'utilisation du serveur PHP interne et l'affichage des contenus static.
 */
if (php_sapi_name() == 'cli-server') {
    if (str_starts_with($_SERVER["REQUEST_URI"], '/public/')) {
        return false;
    }
}

class EntryPoint
{
    private Router $router;
    private SessionHelpers $sessionHelpers;

    function __construct()
    {
        $this->sessionHelpers = new SessionHelpers();

        $this->router = new Router();
        $this->router->LoadRequestedPath();
    }
}

new EntryPoint();
