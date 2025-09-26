<?php


namespace utils;


class SessionHelpers
{
    public function __construct()
    {
        SessionHelpers::init();
    }

    static function setFlashMessage(string $key, mixed $value): void
    {
        $_SESSION['FLASH'][$key] = $value;
    }

    static function getFlashMessage(string $key): mixed
    {
        if (isset($_SESSION['FLASH'][$key])) {
            $value = $_SESSION['FLASH'][$key];
            unset($_SESSION['FLASH'][$key]); // Supprimer le message après l'avoir récupéré
            return $value;
        }

        return null; // Retourne null si le message n'existe pas
    }

    static function init(): void
    {
        session_start();
    }

    static function login(mixed $equipe): void
    {
        $_SESSION['LOGIN'] = $equipe;
    }

    static function logout(): void
    {
        unset($_SESSION['LOGIN']);
    }

    static function getConnected(): mixed
    {
        if (SessionHelpers::isLogin()) {
            return $_SESSION['LOGIN'];
        } else {
            return array();
        }
    }

    static function isLogin(): bool
    {
        return isset($_SESSION['LOGIN']);
    }

    static function saveSelectedForfait(int $idForfait): void
    {
        $_SESSION['SELECTED_FORFAIT'] = $idForfait;
    }

    static function getSelectedForfait(): ?int
    {
        $selectedForfait = $_SESSION['SELECTED_FORFAIT'] ?? null;
        unset($_SESSION['SELECTED_FORFAIT']); // Suppression du forfait sélectionné après récupération
        return $selectedForfait;
    }

    static function hasSelectedForfait(): bool
    {
        return isset($_SESSION['SELECTED_FORFAIT']);
    }

    /**
     * Formate un numéro de téléphone français.
     * Accepte les formats : 0123456789, +33123456789, 01.23.45.67.89, etc.
     * Retourne le format : 01 23 45 67 89
     * 
     * @param string|null $phone Le numéro de téléphone à formater
     * @return string Le numéro formaté ou le numéro original si impossible à formater
     */
    static function formatFrenchPhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // Convertir en string si c'est un int (cas où c'est stocké comme int en BDD)
        $phone = (string) $phone;

        // Si c'est déjà formaté avec des espaces, le retourner tel quel
        if (preg_match('/^\d{2} \d{2} \d{2} \d{2} \d{2}$/', $phone)) {
            return $phone;
        }

        // Supprimer tous les caractères non numériques sauf le +
        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);

        // Gérer le format international +33
        if (strpos($cleanPhone, '+33') === 0) {
            $cleanPhone = '0' . substr($cleanPhone, 3);
        }

        // Vérifier que le numéro fait bien 10 chiffres et commence par 0
        if (strlen($cleanPhone) === 10 && substr($cleanPhone, 0, 1) === '0') {
            // Formater au format français : XX XX XX XX XX
            return substr($cleanPhone, 0, 2) . ' ' . 
                   substr($cleanPhone, 2, 2) . ' ' . 
                   substr($cleanPhone, 4, 2) . ' ' . 
                   substr($cleanPhone, 6, 2) . ' ' . 
                   substr($cleanPhone, 8, 2);
        }

        // Si le numéro fait 10 chiffres mais ne commence pas par 0, essayer quand même de le formater
        if (strlen($cleanPhone) === 10) {
            return substr($cleanPhone, 0, 2) . ' ' . 
                   substr($cleanPhone, 2, 2) . ' ' . 
                   substr($cleanPhone, 4, 2) . ' ' . 
                   substr($cleanPhone, 6, 2) . ' ' . 
                   substr($cleanPhone, 8, 2);
        }

        // Si impossible à formater correctement, retourner le numéro original
        return $phone;
    }

    /**
     * Valide un numéro de téléphone français.
     * 
     * @param string|null $phone Le numéro de téléphone à valider
     * @return bool True si le numéro est valide ou vide, false sinon
     */
    static function isValidFrenchPhone(?string $phone): bool
    {
        if (empty($phone)) {
            return true; // Un numéro vide est autorisé
        }

        // Convertir en string si c'est un int
        $phone = (string) $phone;

        // Si c'est déjà formaté avec des espaces, c'est valide
        if (preg_match('/^\d{2} \d{2} \d{2} \d{2} \d{2}$/', $phone)) {
            return true;
        }

        // Supprimer tous les caractères non numériques sauf le +
        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);

        // Gérer le format international +33
        if (strpos($cleanPhone, '+33') === 0) {
            $cleanPhone = '0' . substr($cleanPhone, 3);
        }

        // Accepter tout numéro de 10 chiffres (plus permissif)
        return strlen($cleanPhone) === 10;
    }

    /**
     * Nettoie un numéro de téléphone pour le stockage en base.
     * Retourne uniquement les chiffres.
     * 
     * @param string|null $phone Le numéro de téléphone à nettoyer
     * @return string|null Le numéro nettoyé ou null si vide
     */
    static function cleanPhoneForStorage(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Convertir en string si c'est un int
        $phone = (string) $phone;

        // Supprimer tous les caractères non numériques sauf le +
        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);

        // Gérer le format international +33
        if (strpos($cleanPhone, '+33') === 0) {
            $cleanPhone = '0' . substr($cleanPhone, 3);
        }

        // Si on a 10 chiffres, c'est bon
        if (strlen($cleanPhone) === 10) {
            return $cleanPhone;
        }

        // Si pas assez de chiffres, retourner null
        return null;
    }

    /**
     * Valide un mot de passe selon les critères de sécurité.
     * - Au moins 8 caractères
     * - Au moins 1 majuscule
     * - Au moins 1 minuscule
     * - Au moins 1 chiffre
     * - Au moins 1 caractère spécial (tout type compris)
     * 
     * @param string|null $password Le mot de passe à valider
     * @return bool True si le mot de passe est valide, false sinon
     */
    static function validatePassword(?string $password): bool
    {
        if (empty($password)) {
            return false;
        }

        // Au moins 8 caractères
        if (strlen($password) < 8) {
            return false;
        }

        // Au moins 1 majuscule
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        // Au moins 1 minuscule
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        // Au moins 1 chiffre
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        // Au moins 1 caractère spécial (tout ce qui n'est pas lettre ou chiffre)
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            return false;
        }

        return true;
    }

    /**
     * Retourne un message d'erreur détaillé pour les critères de mot de passe non respectés.
     * 
     * @param string|null $password Le mot de passe à analyser
     * @return string Message d'erreur détaillé ou chaîne vide si valide
     */
    static function getPasswordValidationError(?string $password): string
    {
        if (empty($password)) {
            return 'Le mot de passe est requis.';
        }

        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'au moins 8 caractères';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'au moins 1 majuscule';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'au moins 1 minuscule';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'au moins 1 chiffre';
        }

        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = 'au moins 1 caractère spécial';
        }

        if (empty($errors)) {
            return '';
        }

        return 'Le mot de passe doit contenir : ' . implode(', ', $errors) . '.';
    }
}
