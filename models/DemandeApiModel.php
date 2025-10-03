<?php

namespace models;

use models\base\SQL;

/**
 * Modèle pour gérer les demandes d'accès à l'API
 * 
 * Champs:
 * - id_demande (int, PK)
 * - email (varchar)
 * - ip_address (varchar)
 * - validation (tinyint, 0=en attente, 1=validée)
 * - date_demande (datetime)
 * - date_validation (datetime, null)
 */
class DemandeApiModel extends SQL
{
    public function __construct()
    {
        parent::__construct('demande_api', 'id_demande');
    }

    /**
     * Enregistre une nouvelle demande d'accès à l'API
     * 
     * @param string $email Email du demandeur
     * @param string $ipAddress Adresse IP du demandeur
     * @return bool True si l'enregistrement a réussi, false sinon
     */
    public function creerDemande(string $email, string $ipAddress): bool
    {
        try {
            $query = "INSERT INTO demande_api (email, ip_address, date_demande) VALUES (?, ?, NOW())";
            $stmt = $this->getPDO()->prepare($query);
            return $stmt->execute([$email, $ipAddress]);
        } catch (\PDOException $e) {
            // Si l'IP existe déjà (contrainte UNIQUE), retourner false
            if ($e->getCode() == '23000') {
                return false;
            }
            throw $e;
        }
    }

    /**
     * Vérifie si une demande existe déjà pour cette adresse IP
     * 
     * @param string $ipAddress Adresse IP à vérifier
     * @return bool True si une demande existe, false sinon
     */
    public function demandeExisteParIP(string $ipAddress): bool
    {
        $query = "SELECT COUNT(*) FROM demande_api WHERE ip_address = ?";
        $stmt = $this->getPDO()->prepare($query);
        $stmt->execute([$ipAddress]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifie si une demande existe déjà pour cet email
     * 
     * @param string $email Email à vérifier
     * @return bool True si une demande existe, false sinon
     */
    public function demandeExiste(string $email): bool
    {
        $query = "SELECT COUNT(*) FROM demande_api WHERE email = ?";
        $stmt = $this->getPDO()->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Récupère une demande par email
     * 
     * @param string $email Email de la demande
     * @return array|null Données de la demande ou null si non trouvée
     */
    public function getByEmail(string $email): ?array
    {
        $query = "SELECT * FROM demande_api WHERE email = ?";
        $stmt = $this->getPDO()->prepare($query);
        $stmt->execute([$email]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Récupère toutes les demandes d'accès API
     * 
     * @return array Liste des demandes
     */
    public function getAllDemandes(): array
    {
        $query = "SELECT * FROM demande_api ORDER BY date_demande DESC";
        $stmt = $this->getPDO()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Valide une demande d'accès API
     * 
     * @param string $email Email de la demande à valider
     * @return bool True si la validation a réussi, false sinon
     */
    public function validerDemande(string $email): bool
    {
        $query = "UPDATE demande_api SET validation = 1, date_validation = NOW() WHERE email = ? AND validation = 0";
        $stmt = $this->getPDO()->prepare($query);
        return $stmt->execute([$email]);
    }

    /**
     * Récupère les demandes nouvellement validées (pour envoi d'email)
     * 
     * @return array Liste des demandes validées depuis la dernière vérification
     */
    public function getDemandesNouveauxValidees(): array
    {
        // Récupère les demandes validées dans les 30 dernières secondes
        $query = "SELECT * FROM demande_api WHERE validation = 1 AND date_validation >= DATE_SUB(NOW(), INTERVAL 30 SECOND)";
        $stmt = $this->getPDO()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Supprime une demande par email (après traitement)
     * 
     * @param string $email Email de la demande à supprimer
     * @return bool True si la suppression a réussi, false sinon
     */
    public function supprimerDemande(string $email): bool
    {
        $query = "DELETE FROM demande_api WHERE email = ?";
        $stmt = $this->getPDO()->prepare($query);
        return $stmt->execute([$email]);
    }

    /**
     * Obtient l'adresse IP réelle du client
     * 
     * @return string Adresse IP du client
     */
    public static function getRealIPAddress(): string
    {
        // Vérifier différentes possibilités d'IP (proxy, load balancer, etc.)
        $ipHeaders = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_FORWARDED_FOR',      // Proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'HTTP_CLIENT_IP',            // Internet service provider
            'REMOTE_ADDR'                // Standard
        ];

        foreach ($ipHeaders as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                
                // Valider que c'est une IP valide
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        // Fallback sur l'IP standard (même si c'est une IP privée)
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}