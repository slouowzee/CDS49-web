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

--
-- Déchargement des données de la table `reponse`
--

INSERT INTO `reponse` (`idquestion`, `numreponse`, `libellereponse`, `valide`) VALUES
(1, 1, 'Une succession de virages dont le premier est à droite.', 1),
(1, 2, 'Une chaussée particulièrement glissante.', 0),
(1, 3, 'Une interdiction de tourner à droite.', 0),
(2, 1, 'Oui, car il est sur la voie de gauche.', 0),
(2, 2, 'Non, le dépassement par la droite est interdit.', 1),
(3, 1, 'Le permis de conduire.', 1),
(3, 2, 'Le certificat d\'immatriculation (carte grise).', 1),
(3, 3, 'L\'attestation d\'assurance.', 1),
(4, 1, 'Aux véhicules lents.', 0),
(4, 2, 'À l\'arrêt ou au stationnement.', 0),
(4, 3, 'À l\'arrêt en cas de panne ou de malaise.', 1),
(5, 1, 'L\'entrée dans une zone où la vitesse est limitée à 30 km/h.', 1),
(5, 2, 'Une aire piétonne.', 0),
(5, 3, 'Une route prioritaire.', 0),
(6, 1, 'Aux véhicules venant de ma droite.', 1),
(6, 2, 'Aux véhicules venant de ma gauche.', 0),
(6, 3, 'À tous les véhicules.', 0),
(7, 1, 'Un retrait de 3 points sur le permis.', 1),
(7, 2, 'Une amende forfaitaire de 135 €.', 1),
(7, 3, 'Une simple amende de 35 €.', 0),
(8, 1, '25 mètres.', 0),
(8, 2, '50 mètres.', 1),
(8, 3, '90 mètres.', 0),
(9, 1, 'Une surchauffe du moteur.', 1),
(9, 2, 'Une pression d\'huile insuffisante.', 0),
(9, 3, 'Une batterie déchargée.', 0),
(10, 1, '24 heures.', 0),
(10, 2, '48 heures.', 0),
(10, 3, '7 jours.', 1),
(11, 1, 'M\'arrêter, sauf si mon arrêt crée un danger.', 1),
(11, 2, 'Accélérer pour passer avant le rouge.', 0),
(11, 3, 'Ralentir et passer avec prudence.', 0),
(12, 1, 'D\'un simple avertissement.', 0),
(12, 2, 'D\'une amende et d\'un retrait de 3 points.', 1),
(12, 3, 'D\'une suspension de permis.', 0),
(13, 1, 'Ralentir et me préparer à m\'arrêter.', 1),
(13, 2, 'Klaxonner fortement.', 0),
(13, 3, 'Faire un écart brusque.', 0),
(14, 1, 'Une zone de stationnement à durée limitée (zone bleue).', 1),
(14, 2, 'Une voie réservée aux vélos.', 0),
(14, 3, 'Un emplacement pour personnes handicapées.', 0),
(15, 1, '90 km/h.', 0),
(15, 2, '110 km/h.', 1),
(15, 3, '130 km/h.', 0),
(16, 1, 'Aux motocyclettes.', 1),
(16, 2, 'Aux piétons.', 0),
(16, 3, 'Aux cyclomoteurs.', 1),
(17, 1, 'Maintenir une vitesse constante.', 0),
(17, 2, 'Maintenir une distance de sécurité avec le véhicule qui précède.', 1),
(17, 3, 'Freiner automatiquement en cas d\'obstacle.', 1),
(18, 1, 'Autorisé si je dois tourner à droite.', 0),
(18, 2, 'Interdit et sanctionné par une amende.', 1),
(18, 3, 'Toléré en cas de fort trafic.', 0),
(19, 1, 'Mettre mes passagers en sécurité derrière la glissière.', 1),
(19, 2, 'Changer la roue sur la bande d\'arrêt d\'urgence.', 0),
(19, 3, 'Appeler les secours depuis la borne d\'appel d\'urgence.', 1),
(20, 1, 'Passer lentement.', 0),
(20, 2, 'M\'arrêter avant l\'intersection et attendre qu\'elle se libère.', 1),
(20, 3, 'Klaxonner pour que les autres avancent.', 0),
(21, 1, 'Une obligation d\'aller tout droit à la prochaine intersection.', 1),
(21, 2, 'Une route à sens unique.', 0),
(21, 3, 'Une direction conseillée.', 0),
(22, 1, '15 ans.', 1),
(22, 2, '16 ans.', 0),
(22, 3, '17 ans.', 0),
(23, 1, 'Les feux de croisement.', 1),
(23, 2, 'Les feux de brouillard avant.', 1),
(23, 3, 'Les feux de brouillard arrière.', 1),
(24, 1, 'Une fois par an.', 0),
(24, 2, 'Une fois par mois et avant chaque long trajet.', 1),
(24, 3, 'Uniquement lorsque le voyant s\'allume.', 0),
(25, 1, 'À toutes les interdictions précédemment signalées.', 1),
(25, 2, 'Uniquement à l\'interdiction de dépasser.', 0),
(25, 3, 'Uniquement à la limitation de vitesse.', 0),
(26, 1, 'Oui, c\'est plus prudent.', 0),
(26, 2, 'Non, l\'usage du klaxon est interdit en agglomération sauf danger immédiat.', 1),
(27, 1, 'Les feux de position seuls.', 0),
(27, 2, 'Les feux de croisement.', 1),
(27, 3, 'Les feux de route, si je ne gêne personne.', 1),
(28, 1, '2 ans.', 0),
(28, 2, '3 ans.', 1),
(29, 1, 'Un passage à niveau avec barrières.', 1),
(29, 2, 'Un passage à niveau sans barrières.', 0),
(29, 3, 'Un pont mobile.', 0),
(30, 1, 'Les pneus n\'adhèrent plus à la route à cause d\'une pellicule d\'eau.', 1),
(30, 2, 'Le moteur surchauffe à cause de la pluie.', 0),
(30, 3, 'Les freins sont moins efficaces à cause de l\'eau.', 0),
(31, 1, 'Oui, la ligne discontinue me le permet.', 0),
(31, 2, 'Non, la flèche de rabattement m\'en empêche.', 1),
(32, 1, '0,25 mg par litre d\'air expiré.', 1),
(32, 2, '0,50 mg par litre d\'air expiré.', 0),
(32, 3, '0,80 mg par litre d\'air expiré.', 0),
(33, 1, 'Payant.', 1),
(33, 2, 'Contrôlé par disque.', 0),
(33, 3, 'Gratuit.', 0),
(34, 1, 'Une obligation de réparation sous 2 mois.', 0),
(34, 2, 'Une immobilisation du véhicule le jour même à minuit.', 1),
(34, 3, 'Une simple mention sur le certificat d\'immatriculation.', 0),
(35, 1, 'Oui, si la somme des PTAC (voiture + caravane) ne dépasse pas 3500 kg.', 1),
(35, 2, 'Non, il faut le permis BE dans tous les cas.', 0),
(36, 1, 'Bleue et blanche.', 1),
(36, 2, 'Rouge et blanche.', 0),
(36, 3, 'Verte et blanche.', 0),
(37, 1, 'Une amende de 135 €.', 1),
(37, 2, 'Un retrait de 4 points.', 1),
(37, 3, 'Une peine de suspension du permis.', 1),
(38, 1, 'Oui, à tous les usagers.', 1),
(38, 2, 'Non, la priorité à droite s\'applique.', 0),
(39, 1, 'Débouché sur un quai ou une berge.', 1),
(39, 2, 'Risque de chute de pierres.', 0),
(39, 3, 'Pont mobile.', 0),
(40, 1, 'Adopter une conduite souple.', 1),
(40, 2, 'Vérifier régulièrement la pression des pneus.', 1),
(40, 3, 'Utiliser le régulateur de vitesse.', 1);


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
