<?php

namespace models\base;


class Database
{
    static function connect()
    {
        $config = include("configs.php");
        
        try {
            $pdo = new \PDO ($config["DB_DSN"], $config["DB_USER"], $config["DB_PASSWORD"]);
            
            if ($pdo && $config["DEBUG"]) {
                // ACTIVER LE DEBUG DES REQUÊTES
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            }
            
            return $pdo;
        } catch (\PDOException $e) {
            // Log l'erreur au lieu de l'afficher en HTML
            error_log("Erreur de connexion à la base de données : " . $e->getMessage());
            
            // En mode DEBUG, relancer l'exception pour avoir plus de détails
            if ($config["DEBUG"]) {
                throw new \Exception("Erreur de connexion à la base de données. Vérifiez vos identifiants et que la base de données existe.");
            }
            
            // En production, retourner null ou une exception générique
            throw new \Exception("Erreur de connexion à la base de données.");
        }
    }
}