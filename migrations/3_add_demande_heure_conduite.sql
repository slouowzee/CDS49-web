-- Migration pour créer la table demande_heure_conduite

CREATE TABLE IF NOT EXISTS `demande_heure_conduite` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ideleve` int NOT NULL,
  `commentaire` text,
  `statut` int DEFAULT 0 COMMENT '0: en attente, 1: accepté, -1: refusé',
  `datedemande` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ideleve` (`ideleve`),
  CONSTRAINT `fk_demande_eleve` FOREIGN KEY (`ideleve`) REFERENCES `eleve` (`ideleve`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
