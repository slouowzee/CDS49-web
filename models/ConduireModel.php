<?php

namespace models;

use PDO;
use models\base\SQL;
use models\InscrireModel;
use utils\SessionHelpers;

/**
 * Champs:
 * - idlecon (int, PK, AUTO_INCREMENT)
 * - ideleve (int)
 * - idvehicule (int)
 * - idmoniteur (int)
 * - heuredebut (datetime)
 * - lieurdv (varchar)
 */
class ConduireModel extends SQL
{
    public function __construct()
    {
        parent::__construct('conduire', 'ideleve');
    }

    /**
     * Retourne les leçons de conduite pour un élève spécifique.
     * Au format: 
     * [
     *  planning => [{start: "2023-10-01 10:00", end: "2023-10-01 11:00", title: "Leçon de conduite"}],
     *  nbLeconsRestantes => 5,
     *  prochainRdv => "2023-10-05 14:00"
     * ]
     */
    public function getLessonsByEleve(): array
    {
        $idEleve = SessionHelpers::getConnected()['ideleve'] ?? null;

        $stmt = $this->getPdo()->prepare("SELECT * FROM conduire WHERE ideleve = :ideleve ORDER BY heuredebut DESC");
        $stmt->execute([':ideleve' => $idEleve]);
        $lessons = $stmt->fetchAll(PDO::FETCH_OBJ);
        $planning = [];

        // On Compte le nombre de leçons plannifiées
        foreach ($lessons as $lesson) {
            $planning[] = [
                'idlecon' => $lesson->idlecon,
                'start' => $lesson->heuredebut,
                'end' => date('Y-m-d H:i', strtotime($lesson->heuredebut) + 3600), // Durée de 1 heure
                'title' => "Leçon de conduite",
                'heuredebut' => $lesson->heuredebut,
                'idvehicule' => $lesson->idvehicule,
                'idmoniteur' => $lesson->idmoniteur,
                'lieurdv' => $lesson->lieurdv
            ];
        }

        // Le prochain rendez-vous est le plus proche dans le futur par rapport à la date actuelle
        $prochainRdv = null;
        $stmt = $this->getPdo()->prepare("SELECT heuredebut FROM conduire WHERE ideleve = :ideleve AND heuredebut > NOW() ORDER BY heuredebut ASC LIMIT 1");
        $stmt->execute([':ideleve' => $idEleve]);
        $nextLesson = $stmt->fetch(PDO::FETCH_OBJ);
        if ($nextLesson) {
            $prochainRdv = $nextLesson->heuredebut;
        }

        // On récupère le forfait de l'élève pour savoir combien de leçons il lui reste
        $inscrireModel = new InscrireModel();
        $forfaitEleve = $inscrireModel->getForfaitEleveConnecte();

        $nbLeconsRestantes = 0;
        if ($forfaitEleve) {
            // On suppose que le forfait contient un nombre de leçons
            $nbLeconsRestantes = $forfaitEleve->nbheures - count($lessons);
        }

        // Créer la structure de retour (planning, nbLeconsRestantes, prochainRdv)
        return [
            'planning' => $planning,
            'nbLeconsRestantes' => $nbLeconsRestantes,
            'prochainRdv' => $prochainRdv
        ];
    }

    /**
     * Récupère les détails d'une leçon spécifique par ID de leçon
     */
    public function getLeconDetails(int $idlecon): ?object
    {
        $idEleve = SessionHelpers::getConnected()['ideleve'] ?? null;
        
        if (!$idEleve) {
            return null;
        }

        $stmt = $this->getPdo()->prepare("
            SELECT c.*, m.nommoniteur, m.prenommoniteur, v.designation, v.immatriculation, v.manuel, v.nbpassagers
            FROM conduire c
            LEFT JOIN moniteur m ON c.idmoniteur = m.idmoniteur
            LEFT JOIN vehicule v ON c.idvehicule = v.idvehicule
            WHERE c.ideleve = :ideleve AND c.idlecon = :idlecon
        ");
        
        $stmt->execute([
            ':ideleve' => $idEleve,
            ':idlecon' => $idlecon
        ]);
        
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    /**
     * Supprime une leçon de conduite
     */
    public function deleteLecon(int $idlecon): bool
    {
        $idEleve = SessionHelpers::getConnected()['ideleve'] ?? null;
        
        if (!$idEleve) {
            return false;
        }

        $stmt = $this->getPdo()->prepare("
            DELETE FROM conduire 
            WHERE idlecon = :idlecon AND ideleve = :ideleve
        ");
        
        return $stmt->execute([
            ':idlecon' => $idlecon,
            ':ideleve' => $idEleve
        ]);
    }

    /**
     * Envoie un email de confirmation d'annulation de leçon
     */
    public function sendCancellationEmail(array $eleve, object $lecon): bool
    {
        $emailUtils = new \utils\EmailUtils();
        
        $subject = "Confirmation annulation de lecon - CDS 49";
        
        $data = [
            'prenom' => $eleve['prenomeleve'],
            'dateLecon' => date('d/m/Y à H:i', strtotime($lecon->heuredebut)),
            'moniteur' => $lecon->prenommoniteur . ' ' . $lecon->nommoniteur,
            'lieu' => $lecon->lieurdv ?? 'Non spécifié'
        ];
        
        return $emailUtils->sendEmail(
            $eleve['emaileleve'],
            $subject,
            'confirmation_annulation_lecon',
            $data
        );
    }

    /**
     * Crée une demande d'heures supplémentaires pour l'élève connecté
     * @param string|null $commentaire Commentaire optionnel (ex: disponibilités)
     * @return bool
     */
    public function creerDemandeHeureSupplementaire(?string $commentaire = null): bool
    {
        $idEleve = SessionHelpers::getConnected()['ideleve'] ?? null;
        
        if (!$idEleve) {
            return false;
        }

        $stmt = $this->getPdo()->prepare("
            INSERT INTO demande_heure_conduite (ideleve, commentaire, statut, datedemande)
            VALUES (:ideleve, :commentaire, 0, NOW())
        ");
        
        return $stmt->execute([
            ':ideleve' => $idEleve,
            ':commentaire' => $commentaire
        ]);
    }
}
