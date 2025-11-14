-- Migration pour ajouter la catégorie aux résultats de quiz

-- Ajouter la colonne idcategorie à la table resultat
ALTER TABLE `resultat` ADD COLUMN `idcategorie` INT NOT NULL AFTER `nbquestions`;

-- Ajouter une clé étrangère vers la table categorie_question
ALTER TABLE `resultat` ADD INDEX `idx_idcategorie` (`idcategorie`);
ALTER TABLE `resultat` ADD CONSTRAINT `fk_resultat_categorie` 
    FOREIGN KEY (`idcategorie`) REFERENCES `categorie_question`(`idcategorie`) 
    ON DELETE RESTRICT ON UPDATE CASCADE;
