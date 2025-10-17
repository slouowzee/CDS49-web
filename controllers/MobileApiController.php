<?php

namespace controllers;

use controllers\base\ApiController;
use models\EleveModel;
use models\QuestionModel;
use models\ReponseModel;
use models\ResultatModel;
use utils\SessionHelpers;

class MobileApiController extends ApiController
{
    private EleveModel $eleveModel;
    private QuestionModel $questionModel;
    private ReponseModel $reponseModel;
    private ResultatModel $resultatModel;

    function __construct()
    {
        $this->eleveModel = new EleveModel();
        $this->questionModel = new QuestionModel();
        $this->reponseModel = new ReponseModel();
        $this->resultatModel = new ResultatModel();
    }

    function index()
    {
        return $this->redirect('/documentation-api.html');
    }

    /**
     * path: /api/login
     * method: POST
     * Méthode pour gérer la connexion des utilisateurs via l'API mobile.
     */
    function login()
    {
        if (!$this->isPost()) {
            return $this->errorResponse('Méthode non autorisée', 405);
        }

        // Lire les données JSON du corps de la requête
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (empty($email) || empty($password)) {
            return $this->errorResponse('Email et mot de passe requis', 400);
        }
        $token = bin2hex(random_bytes(16));
        $user = $this->eleveModel->connexion($email, $password, $token);

        if ($user) {
            return $this->successResponse('Connexion réussie', ['user' => $user, 'token' => $token]);
        } else {
            return $this->errorResponse('Identifiants invalides', 401);
        }
    }

	function register() {
		if (!$this->isPost()) {
			return $this->errorResponse('Méthode non autorisée', 405);
		}

		// Lire les données JSON du corps de la requête
		$data = $this->getJsonData();

		$nom = $data['nom'] ?? null;
		$prenom = $data['prenom'] ?? null;
		$telephone = $data['telephone'] ?? null;
		$email = $data['email'] ?? null;
		$datenaissance = $data['datenaissance'] ?? null;
		$motDePasse = $data['motdepasse'] ?? null;

		if (empty($nom) || empty($prenom) || empty($telephone) || empty($email) || empty($datenaissance) || empty($motDePasse)) {
			return $this->errorResponse('Tous les champs sont requis', 400);
		}

		$existingUser = $this->eleveModel->getByEmail($email);
		if ($existingUser) {
			return $this->errorResponse('Un utilisateur avec cet email existe déjà', 409);
		}

		$success = $this->eleveModel->creer_eleve($nom, $prenom, $telephone, $email, $motDePasse, $datenaissance);

		if ($success) {
			return $this->successResponse('Inscription réussie');
		} else {
			return $this->errorResponse('Échec de l\'inscription', 500);
		}
	}

    /**
     * path: /api/profile/get
     * method: GET
     * Méthode pour récupérer les informations de l'utilisateur spécifié par le token.
     */
    function getProfile()
    {
        if ($this->isPost()) {
            return $this->errorResponse('Méthode non autorisée', 405);
        }

        // Récupération depuis l'en-tête (Bearer token), on ne garde que le token
        $token = trim(str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION'] ?? ''));

        if (empty($token)) {
            return $this->errorResponse('Token requis', 401);
        }

        $user = $this->eleveModel->getByToken($token);

        if ($user) {
            return $this->successResponse('Informations utilisateur récupérées avec succès', ['user' => $user]);
        } else {
            return $this->errorResponse('Utilisateur non trouvé', 404);
        }
    }

    /**
     * path: /api/profile/update
     * method: POST
     * Méthode pour mettre à jour les informations de l'utilisateur.
     */
    function updateProfile()
    {
        if (!$this->isPost()) {
            return $this->errorResponse('Méthode non autorisée', 405);
        }

        // Lire les données JSON du corps de la requête
        $data = $this->getJsonData();

        // Récupération depuis l'en-tête (Bearer token)
        $token = $this->getAuthToken();

        if (empty($token)) {
            return $this->errorResponse('Token requis', 401);
        }

        $nom = $data['nom'] ?? null;
        $prenom = $data['prenom'] ?? null;
        $email = $data['email'] ?? null;
        $datenaissance = $data['datenaissance'] ?? null;
        $motDePasse = $data['motdepasse'] ?? null;

        if (empty($nom) || empty($prenom) || empty($email) || empty($datenaissance)) {
            return $this->errorResponse('Tous les champs sont requis', 400);
        }

        $success = $this->eleveModel->updateByToken($token, $nom, $prenom, $email, $datenaissance, $motDePasse);

        if ($success) {
            return $this->successResponse('Informations mises à jour avec succès');
        } else {
            return $this->errorResponse('Échec de la mise à jour des informations', 500);
        }
    }

    /**
     * path: /api/questions/{n}?categorie=random
     * method: GET
     * Retourne N questions et leur réponse associée.
     */
    function getQuestions(int $n = 40)
    {
        if ($this->isPost()) {
            return $this->errorResponse('Méthode non autorisée', 405);
        }

        if ($n <= 0) {
            return $this->errorResponse('Le nombre de questions doit être supérieur à zéro', 400);
        }

        $categorie = $_GET['categorie'] ?? null;

        // TODO: Implémenter la logique de filtrage par catégorie si nécessaire
        $questions = [];
        $output = [];


        // Si la catégorie est null ou 'random', on récupère des questions aléatoires
        if ($categorie == null || $categorie == 'random') {
            $questions = $this->questionModel->getRandomQuestions($n);
        } else {
            // TODO: Implémenter la logique pour récupérer les questions par catégorie
        }


        foreach ($questions as $question) {
            $reponses = $this->reponseModel->getByQuestion($question->idquestion);
            $output[] = [
                'question' => $question,
                'reponses' => $reponses
            ];
        }

        return $this->successResponse('', $output);
    }

    /**
     * path: /api/fin-test
     * method: POST
     * Sauvegarde du score de l'élève.
     */
    function saveScore()
    {
        if (!$this->isPost()) {
            return $this->errorResponse('Méthode non autorisée', 405);
        }

        // Récupération depuis l'en-tête (Bearer token), on ne garde que le token
        $token = $this->getAuthToken();
        $success = false;

        /**
         * Data est un JSON qui contient :
         * {score: 1234, nbquestions: 40}
         */
        $data = $this->getJsonData();

        $score = $data['score'] ?? null;
        $nbQuestions = $data['nbquestions'] ?? null;

        if (empty($score) || empty($nbQuestions)) {
            return $this->errorResponse('Score et nombre de questions requis', 400);
        }

        // Récupération du connecté dans la session
        if (!$token && SessionHelpers::getConnected()) {
            // Id de l'élève connecté
            $ideleve = SessionHelpers::getConnected()['ideleve'] ?? null;

            if (empty($ideleve)) {
                return $this->errorResponse('Vous devez être connecté pour sauvegarder un score', 401);
            }

            // Sauvegarde du score dans la base de données par ID d'élève
            $success = $this->resultatModel->saveScoreById($ideleve, $score, $nbQuestions);
        } else {
            if (empty($token)) {
                return $this->errorResponse('Token requis', 401);
            }

            // Sauvegarde du score dans la base de données
            $success = $this->resultatModel->saveScoreByToken($token, $score, $nbQuestions);
        }

        if ($success) {
            return $this->successResponse('Score sauvegardé avec succès');
        } else {
            return $this->errorResponse('Échec de la sauvegarde du score', 500);
        }
    }
}
