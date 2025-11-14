-- Migration pour ajouter un identifiant unique aux leçons de conduite

-- Supprimer la clé primaire actuelle
ALTER TABLE `conduire` DROP PRIMARY KEY;

-- Ajouter la colonne idlecon comme identifiant auto-incrémenté
ALTER TABLE `conduire` ADD COLUMN `idlecon` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

-- Recréer les index sur les autres colonnes
ALTER TABLE `conduire` ADD INDEX `idx_ideleve` (`ideleve`);
ALTER TABLE `conduire` ADD INDEX `idx_idvehicule` (`idvehicule`);
ALTER TABLE `conduire` ADD INDEX `idx_idmoniteur` (`idmoniteur`);
ALTER TABLE `conduire` ADD INDEX `idx_heuredebut` (`heuredebut`);
