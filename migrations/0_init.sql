-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : db:3306
-- Généré le : ven. 17 oct. 2025 à 13:31
-- Version du serveur : 8.4.1
-- Version de PHP : 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ap3_les-supers-nanas`
--

-- --------------------------------------------------------

--
-- Structure de la table `categorie_question`
--

CREATE TABLE `categorie_question` (
  `idcategorie` int NOT NULL,
  `libcategorie` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categorie_question`
--

INSERT INTO `categorie_question` (`idcategorie`, `libcategorie`) VALUES
(1, 'Aléatoire '),
(2, 'Code de la route '),
(3, 'Signalisation '),
(4, 'Priorités '),
(5, 'Stationnement');

-- --------------------------------------------------------

--
-- Structure de la table `compte_admin`
--

CREATE TABLE `compte_admin` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `motdepasse` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `compte_api`
--

CREATE TABLE `compte_api` (
  `idcompteapi` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `motdepasse` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `conduire`
--

CREATE TABLE `conduire` (
  `ideleve` int NOT NULL,
  `idvehicule` int NOT NULL,
  `idmoniteur` int NOT NULL,
  `heuredebut` datetime NOT NULL,
  `lieurdv` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- --------------------------------------------------------

--
-- Structure de la table `demande_acces_api`
--

CREATE TABLE `demande_acces_api` (
  `id` int NOT NULL,
  `emaildemandeur` varchar(255) NOT NULL,
  `date_demande` datetime NOT NULL,
  `ip_demande` varchar(255) NOT NULL,
  `token_creation` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demande_reinitialisation`
--

CREATE TABLE `demande_reinitialisation` (
  `token` varchar(255) NOT NULL,
  `ideleve` int NOT NULL,
  `date_creation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demande_reinitialisation_api`
--

CREATE TABLE `demande_reinitialisation_api` (
  `token` varchar(255) NOT NULL,
  `idcompteapi` int NOT NULL,
  `date_creation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `eleve`
--

CREATE TABLE `eleve` (
  `ideleve` int NOT NULL,
  `nomeleve` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `prenomeleve` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `teleleve` varchar(255) COLLATE utf8mb3_bin DEFAULT NULL,
  `emaileleve` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `motpasseeleve` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `datenaissanceeleve` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- --------------------------------------------------------

--
-- Structure de la table `forfait`
--

CREATE TABLE `forfait` (
  `idforfait` int NOT NULL,
  `libelleforfait` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `descriptionforfait` text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `contenuforfait` text CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `prixforfait` decimal(10,2) DEFAULT NULL,
  `nbheures` bigint DEFAULT NULL,
  `prixhoraire` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

--
-- Déchargement des données de la table `forfait`
--

INSERT INTO `forfait` (`idforfait`, `libelleforfait`, `descriptionforfait`, `contenuforfait`, `prixforfait`, `nbheures`, `prixhoraire`) VALUES
(1, 'Forfait 20 Heures', 'Idéal pour commencer votre apprentissage de la conduite avec une base solide.', 'Cours de code illimités;20 heures de conduite;Accompagnement examen code;Accompagnement examen conduite', 1200.00, 20, NULL),
(2, 'Forfait 25 Heures', 'Plus d\'heures pour plus de confiance et une meilleure préparation à l\'examen.', 'Cours de code illimités;25 heures de conduite;Accompagnement examen code;Accompagnement examen conduite;1 Rendez-vous pédagogique inclus', 1450.00, 25, NULL),
(3, 'Leçon de Perfectionnement', 'Pour ceux qui ont déjà le code et souhaitent reprendre confiance ou améliorer leur conduite.', 'Conduite en ville;Manœuvres spécifiques;Conduite sur autoroute;Adapté à vos besoins', 45.00, 1, 45.00);

-- --------------------------------------------------------

--
-- Structure de la table `inscrire`
--

CREATE TABLE `inscrire` (
  `ideleve` int NOT NULL,
  `idforfait` int NOT NULL,
  `dateinscription` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- --------------------------------------------------------

--
-- Structure de la table `moniteur`
--

CREATE TABLE `moniteur` (
  `idmoniteur` int NOT NULL,
  `nommoniteur` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `prenommoniteur` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `emailmoniteur` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

--
-- Déchargement des données de la table `moniteur`
--

INSERT INTO `moniteur` (`idmoniteur`, `nommoniteur`, `prenommoniteur`, `emailmoniteur`) VALUES
(1, 'John', 'Doe', 'jdoe@proton.me'),
(2, 'Sarrazin', 'Paul', 'paul.Sarr4@orange.fr'),
(3, 'Manin', 'Lydie', 'lmanin@gmail.com'),
(4, 'Duperray', 'Valentina', 'dv1999@gmail.com'),
(5, 'Pilet', 'Gaël', 'gael.pilet1@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `question`
--

CREATE TABLE `question` (
  `idquestion` int NOT NULL,
  `libellequestion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `imagequestion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `idcategorie` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

--
-- Déchargement des données de la table `question`
--

INSERT INTO `question` (`idquestion`, `libellequestion`, `imagequestion`, `idcategorie`) VALUES
(1, 'Ce panneau annonce :', 'image41.jpg', 3),
(2, 'Je peux dépasser le véhicule blanc par la droite.', 'image42.jpg', 2),
(3, 'En cas de contrôle par les forces de l\'ordre, je dois présenter :', 'image43.jpg', 2),
(4, 'Sur autoroute, la bande d\'arrêt d\'urgence est réservée :', 'image44.jpg', 2),
(5, 'Ce panneau m\'indique :', 'image45.jpg', 3),
(6, 'Dans cette intersection sans signalisation, je dois céder le passage :', 'image46.jpg', 4),
(7, 'L\'utilisation du téléphone tenu en main en conduisant est sanctionnée par :', 'image47.jpg', 2),
(8, 'La distance de sécurité à 90 km/h sur sol sec est d\'environ :', 'image48.jpg', 2),
(9, 'Ce voyant rouge signifie :', 'image49.jpg', 2),
(10, 'Le stationnement est considéré comme abusif après une durée ininterrompue de :', 'image50.jpg', 5),
(11, 'Ce signal lumineux m\'oblige à :', 'image51.jpg', 3),
(12, 'Un oubli du clignotant est passible :', 'image52.jpg', 2),
(13, 'En présence d\'un animal sur la chaussée, mon premier réflexe est de :', 'image53.jpg', 2),
(14, 'Le marquage au sol de couleur bleue indique :', 'image54.jpg', 3),
(15, 'Sur une route pour automobiles, la vitesse maximale autorisée est de :', 'image55.jpg', 2),
(16, 'Ce panneau interdit l\'accès :', 'image56.jpg', 3),
(17, 'Le \"régulateur de vitesse adaptatif\" permet de :', 'image57.jpg', 2),
(18, 'Circuler sur une voie réservée aux bus est :', 'image58.jpg', 2),
(19, 'En cas de crevaison sur autoroute, je dois :', 'image59.jpg', 2),
(20, 'Le feu est vert, mais l\'intersection est encombrée. Je dois :', 'image60.jpg', 4),
(21, 'Ce panneau indique :', 'image61.jpg', 3),
(22, 'La conduite accompagnée (AAC) est possible à partir de :', 'image62.jpg', 2),
(23, 'Par temps de brouillard épais, je peux utiliser :', 'image63.jpg', 2),
(24, 'La pression des pneus doit être vérifiée :', 'image64.jpg', 2),
(25, 'Ce panneau met fin à :', 'image65.jpg', 3),
(26, 'Je m\'apprête à dépasser un cycliste. Je klaxonne pour l\'avertir.', 'image66.jpg', 2),
(27, 'En agglomération, de nuit, sur une chaussée non éclairée, j\'utilise :', 'image67.jpg', 2),
(28, 'La durée de la période probatoire pour un conducteur ayant suivi la formation traditionnelle est de :', 'image68.jpg', 2),
(29, 'Ce panneau signale :', 'image69.jpg', 3),
(30, 'Un \"aquaplaning\" (ou aquaplanage) se produit lorsque :', 'image70.jpg', 2),
(31, 'La signalisation m\'autorise à faire demi-tour.', 'image71.jpg', 3),
(32, 'Le taux légal d\'alcoolémie à ne pas atteindre est de 0,5 g/L de sang, ce qui équivaut à :', 'image72.jpg', 2),
(33, 'Ce panneau indique que le stationnement est :', 'image73.jpg', 5),
(34, 'Lors d\'un contrôle technique, une défaillance \"critique\" entraîne :', 'image74.jpg', 2),
(35, 'Je tracte une caravane dont le PTAC est de 800 kg. Mon permis B est suffisant.', 'image75.jpg', 2),
(36, 'Cette balise de virage est de couleur :', 'image76.jpg', 3),
(37, 'Refuser une priorité est sanctionné par :', 'image77.jpg', 4),
(38, 'Je sors d\'un chemin de terre. Je dois céder le passage.', 'image78.jpg', 4),
(39, 'Ce panneau signifie :', 'image79.jpg', 3),
(40, 'Pour limiter la pollution, je peux :', 'image80.jpg', 2);

-- --------------------------------------------------------

--
-- Structure de la table `reponse`
--

CREATE TABLE `reponse` (
  `idquestion` int NOT NULL,
  `numreponse` int NOT NULL,
  `libellereponse` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `valide` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- --------------------------------------------------------

--
-- Structure de la table `resultat`
--

CREATE TABLE `resultat` (
  `idresultat` int NOT NULL,
  `ideleve` int NOT NULL,
  `dateresultat` datetime NOT NULL,
  `score` bigint NOT NULL,
  `nbquestions` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- --------------------------------------------------------

--
-- Structure de la table `token`
--

CREATE TABLE `token` (
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ideleve` int NOT NULL,
  `date_creation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vehicule`
--

CREATE TABLE `vehicule` (
  `idvehicule` int NOT NULL,
  `nbpassagers` int DEFAULT NULL,
  `immatriculation` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `designation` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `manuel` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

--
-- Déchargement des données de la table `vehicule`
--

INSERT INTO `vehicule` (`idvehicule`, `nbpassagers`, `immatriculation`, `designation`, `manuel`) VALUES
(1, 5, 'FA-828-NE', 'Tesla', 1),
(2, 2, 'BZ-666-FF', 'Porshe 911', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categorie_question`
--
ALTER TABLE `categorie_question`
  ADD PRIMARY KEY (`idcategorie`);

--
-- Index pour la table `compte_admin`
--
ALTER TABLE `compte_admin`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `compte_api`
--
ALTER TABLE `compte_api`
  ADD PRIMARY KEY (`idcompteapi`) USING BTREE;

--
-- Index pour la table `conduire`
--
ALTER TABLE `conduire`
  ADD PRIMARY KEY (`ideleve`,`idvehicule`,`idmoniteur`,`heuredebut`) USING BTREE,
  ADD KEY `i_fk_conduire_eleve1` (`ideleve`),
  ADD KEY `i_fk_conduire_vehicule1` (`idvehicule`),
  ADD KEY `i_fk_conduire_moniteur1` (`idmoniteur`);

--
-- Index pour la table `demande_acces_api`
--
ALTER TABLE `demande_acces_api`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `demande_reinitialisation`
--
ALTER TABLE `demande_reinitialisation`
  ADD PRIMARY KEY (`token`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `ideleve` (`ideleve`);

--
-- Index pour la table `demande_reinitialisation_api`
--
ALTER TABLE `demande_reinitialisation_api`
  ADD PRIMARY KEY (`token`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idcompteapi` (`idcompteapi`);

--
-- Index pour la table `eleve`
--
ALTER TABLE `eleve`
  ADD PRIMARY KEY (`ideleve`);

--
-- Index pour la table `forfait`
--
ALTER TABLE `forfait`
  ADD PRIMARY KEY (`idforfait`);

--
-- Index pour la table `inscrire`
--
ALTER TABLE `inscrire`
  ADD PRIMARY KEY (`ideleve`,`idforfait`,`dateinscription`),
  ADD KEY `i_fk_inscrire_eleve1` (`ideleve`),
  ADD KEY `i_fk_inscrire_forfait1` (`idforfait`);

--
-- Index pour la table `moniteur`
--
ALTER TABLE `moniteur`
  ADD PRIMARY KEY (`idmoniteur`);

--
-- Index pour la table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`idquestion`),
  ADD KEY `idcategorie` (`idcategorie`);

--
-- Index pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD PRIMARY KEY (`idquestion`,`numreponse`),
  ADD KEY `i_fk_reponse_question1` (`idquestion`);

--
-- Index pour la table `resultat`
--
ALTER TABLE `resultat`
  ADD PRIMARY KEY (`idresultat`),
  ADD KEY `i_fk_resultat_eleve1` (`ideleve`);

--
-- Index pour la table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`token`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `token_ibfk_1` (`ideleve`);

--
-- Index pour la table `vehicule`
--
ALTER TABLE `vehicule`
  ADD PRIMARY KEY (`idvehicule`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categorie_question`
--
ALTER TABLE `categorie_question`
  MODIFY `idcategorie` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `compte_admin`
--
ALTER TABLE `compte_admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `compte_api`
--
ALTER TABLE `compte_api`
  MODIFY `idcompteapi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `demande_acces_api`
--
ALTER TABLE `demande_acces_api`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `eleve`
--
ALTER TABLE `eleve`
  MODIFY `ideleve` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `forfait`
--
ALTER TABLE `forfait`
  MODIFY `idforfait` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `moniteur`
--
ALTER TABLE `moniteur`
  MODIFY `idmoniteur` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `question`
--
ALTER TABLE `question`
  MODIFY `idquestion` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `resultat`
--
ALTER TABLE `resultat`
  MODIFY `idresultat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `vehicule`
--
ALTER TABLE `vehicule`
  MODIFY `idvehicule` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `conduire`
--
ALTER TABLE `conduire`
  ADD CONSTRAINT `conduire_ibfk_1` FOREIGN KEY (`ideleve`) REFERENCES `eleve` (`ideleve`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `conduire_ibfk_2` FOREIGN KEY (`idvehicule`) REFERENCES `vehicule` (`idvehicule`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `conduire_ibfk_3` FOREIGN KEY (`idmoniteur`) REFERENCES `moniteur` (`idmoniteur`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `demande_reinitialisation`
--
ALTER TABLE `demande_reinitialisation`
  ADD CONSTRAINT `demande_reinitialisation_ibfk_1` FOREIGN KEY (`ideleve`) REFERENCES `eleve` (`ideleve`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `demande_reinitialisation_api`
--
ALTER TABLE `demande_reinitialisation_api`
  ADD CONSTRAINT `demande_reinitialisation_api_ibfk_1` FOREIGN KEY (`idcompteapi`) REFERENCES `compte_api` (`idcompteapi`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `inscrire`
--
ALTER TABLE `inscrire`
  ADD CONSTRAINT `inscrire_ibfk_1` FOREIGN KEY (`ideleve`) REFERENCES `eleve` (`ideleve`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inscrire_ibfk_2` FOREIGN KEY (`idforfait`) REFERENCES `forfait` (`idforfait`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`idcategorie`) REFERENCES `categorie_question` (`idcategorie`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD CONSTRAINT `reponse_ibfk_1` FOREIGN KEY (`idquestion`) REFERENCES `question` (`idquestion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `resultat`
--
ALTER TABLE `resultat`
  ADD CONSTRAINT `resultat_ibfk_1` FOREIGN KEY (`ideleve`) REFERENCES `eleve` (`ideleve`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `token_ibfk_1` FOREIGN KEY (`ideleve`) REFERENCES `eleve` (`ideleve`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Évènements
--
DROP EVENT IF EXISTS `delete_old_tokens`$$
CREATE DEFINER=`ap3_les-supers-nanas-1`@`%` EVENT `delete_old_tokens` ON SCHEDULE EVERY 30 SECOND STARTS '2025-09-14 14:51:18' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM demande_reinitialisation WHERE date_creation < NOW() - INTERVAL 15 MINUTE$$

DROP EVENT IF EXISTS `delete_old_tokens_api`$$
CREATE DEFINER=`ap3_les-supers-nanas-1`@`%` EVENT `delete_old_tokens_api` ON SCHEDULE EVERY 30 SECOND STARTS '2025-10-17 12:19:52' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM demande_reinitialisation_api
  WHERE date_creation < NOW() - INTERVAL 15 MINUTE$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
