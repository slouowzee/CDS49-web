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
     * path: /api/questions/{n}?idcategorie=2
     * method: GET
     * Retourne N questions et leur réponse associée.
     * Paramètres:
     * - n: nombre de questions (défaut: 40)
     * - idcategorie: ID de la catégorie (optionnel, si absent ou =1 => aléatoire)
     */
    function getQuestions(int $n = 40)
    {
        if ($this->isPost()) {
            return $this->errorResponse('Méthode non autorisée', 405);
        }

        if ($n <= 0) {
            return $this->errorResponse('Le nombre de questions doit être supérieur à zéro', 400);
        }

        $idcategorie = isset($_GET['idcategorie']) ? (int)$_GET['idcategorie'] : null;

        $questions = [];
        $output = [];

        // Si idcategorie est null ou =1 (Aléatoire), on récupère des questions aléatoires toutes catégories
        if ($idcategorie === null || $idcategorie === 1) {
            $questions = $this->questionModel->getRandomQuestions($n);
        } else {
            // Sinon, on récupère les questions de la catégorie spécifique
            $questions = $this->questionModel->getQuestionsByCategorie($n, $idcategorie);
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

	function getCategorie() {
		if ($this->isPost()) {
            return $this->errorResponse('Méthode non autorisée', 405);
        }

		$categorie = $this->questionModel->getCategorie();

		return $this->successResponse('', $categorie);
	}

    /**
     * path: /api/scores/set
     * method: POST
     * Sauvegarde du score de l'élève.
     */
    function setScores() {
		$data = $this->getJsonData();

		if (empty($data['token'])) {
			return $this->errorResponse('Token requis', 401);
		}
		if (!$this->isPost()) {
			return $this->errorResponse('Méthode non autorisée', 405);
		}

		$id = $data['id'] ?? null;
		$score = $data['score'] ?? null;
		$nbquestions = $data['nbquestions'] ?? null;
		$idcategorie = $data['idcategorie'] ?? 1;

		if (!is_numeric($score) || !is_numeric($nbquestions) || (int)$score < 0 || (int)$score > 40 || (int)$nbquestions <= 0 || (int)$nbquestions > 40) {
			return $this->errorResponse('Erreur dans la requête.', 400);
		}

$success = $this->resultatModel->saveScoreByToken($data['token'], (int)$score, (int)$nbquestions, (int)$idcategorie);
		if ($success) {
			return $this->successResponse('Score sauvegardé avec succès');
		} else {
			return $this->errorResponse('Échec de la sauvegarde du score', 500);
		}
	}


    /**
     * path: /api/scores
     * method: GET
     * Récupère tous les scores d'un utilisateur avec statistiques.
     * Paramètres optionnels: ?date_debut=2024-01-01 00:00:00&date_fin=2024-12-31 23:59:59
     */
    function getScores()
    {
        if ($this->isPost()) {
            return $this->errorResponse('Méthode non autorisée', 405);
        }

        $token = $this->getAuthToken();

        if (empty($token)) {
            return $this->errorResponse('Token requis', 401);
        }

        $dateDebut = $_GET['date_debut'] ?? null;
        $dateFin = $_GET['date_fin'] ?? null;

        if ($dateDebut && !$this->isValidDateTime($dateDebut)) {
            return $this->errorResponse('Format de date_debut invalide. Utilisez: YYYY-MM-DD HH:MM:SS', 400);
        }
        if ($dateFin && !$this->isValidDateTime($dateFin)) {
            return $this->errorResponse('Format de date_fin invalide. Utilisez: YYYY-MM-DD HH:MM:SS', 400);
        }

        $result = $this->resultatModel->getScoresWithStatistics($token, $dateDebut, $dateFin);

        return $this->successResponse('Scores récupérés avec succès', $result);
    }

    

    

    /**
     * Valide le format de date/heure.
     * @param string $dateTime
     * @return bool
     */
    private function isValidDateTime(string $dateTime): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        return $d && $d->format('Y-m-d H:i:s') === $dateTime;
    }
}
