-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 05 juin 2026 à 15:02
-- Version du serveur : 9.1.0
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `cerfop`
--

-- --------------------------------------------------------

--
-- Structure de la table `about_us`
--

DROP TABLE IF EXISTS `about_us`;
CREATE TABLE IF NOT EXISTS `about_us` (
  `id_about_us` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `title` varchar(200) NOT NULL,
  `details` text,
  `date_insertion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_about_us`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `about_us`
--

INSERT INTO `about_us` (`id_about_us`, `uuid`, `title`, `details`, `date_insertion`) VALUES
(1, '99943f17-5f8a-4f90-aff9-450dcf43a401', 'PLAN GLOBAL DE L\'OFFRE DE FORMATION ET DE RECHERCHE', '<h2>Centre de Recherche et de la Formation Professionnelle</h2>\n<p><em><strong>Motto :</strong> &laquo; Lib&eacute;rer les performances et les comp&eacute;tences &raquo;</em></p>\n\n<h3>Valeurs fondamentales</h3>\n<ul>\n<li><strong>Excellence</strong> : qualit&eacute; et rigueur dans toutes les formations et &eacute;tudes</li>\n<li><strong>Innovation</strong> : cr&eacute;ativit&eacute; et recherche de solutions nouvelles</li>\n<li><strong>Int&eacute;grit&eacute;</strong> : &eacute;thique, transparence et professionnalisme</li>\n<li><strong>Impact</strong> : r&eacute;sultats mesurables et pertinents</li>\n<li><strong>Adaptabilit&eacute;</strong> : formations et services ajust&eacute;s aux besoins des participants et organisations et aux standards de qualit&eacute;</li>\n<li><strong>Collaboration</strong> : synergies avec entreprises, institutions et experts</li>\n</ul>\n\n<h3>Axes strat&eacute;giques de formation</h3>\n\n<h4>P&ocirc;le 1 : Leadership, Management &amp; Gouvernance</h4>\n<p><strong>Objectif</strong> : Contribuer au renforcement des comp&eacute;tences strat&eacute;giques des responsables d&rsquo;entreprises et d&rsquo;organisations sans but lucratif, et &agrave; l&rsquo;am&eacute;lioration de leur performance globale.</p>\n<ul>\n<li>Management et leadership transformationnel</li>\n<li>Gestion socio-&eacute;conomique des entreprises et organisations sans but lucratif</li>\n<li>Gestion des ressources humaines et performance organisationnelle : recrutement, int&eacute;gration et fid&eacute;lisation du personnel, gestion des comp&eacute;tences et d&eacute;veloppement des talents, &eacute;valuation de la performance individuelle et collective, motivation et engagement du personnel, culture organisationnelle et travail d&rsquo;&eacute;quipe, outils num&eacute;riques pour la GRH et tableaux de bord RH, conformit&eacute; l&eacute;gale et s&eacute;curit&eacute; au travail</li>\n<li>Pilotage strat&eacute;gique et planification</li>\n<li>&Eacute;laboration et suivi d&rsquo;un plan strat&eacute;gique</li>\n<li>D&eacute;veloppement et suivi d&rsquo;outils op&eacute;rationnels : manuels de proc&eacute;dures administratives, comptables et financi&egrave;res</li>\n</ul>\n\n<h4>P&ocirc;le 2 : Comp&eacute;tences Techniques &amp; M&eacute;tiers</h4>\n<ul>\n<li><strong>Data Analytics</strong> : Excel (D&eacute;butant &rarr; Avanc&eacute;), Tableau pour la visualisation des donn&eacute;es</li>\n<li><strong>SIG / GIS</strong> : analyse spatiale et cartographie</li>\n<li><strong>Comptabilit&eacute; num&eacute;rique</strong> : Entreprises avec Excel, Organisations sans but lucratif avec Excel, Entreprises avec QuickBooks, Organisations sans but lucratif avec QuickBooks</li>\n<li><strong>Suivi et &Eacute;valuation (S&amp;E) des projets</strong> : fondamentaux, collecte et analyse de donn&eacute;es, indicateurs, reporting</li>\n</ul>\n\n<h4>P&ocirc;le 3 : D&eacute;veloppement Personnel &amp; Soft Skills</h4>\n<ul>\n<li>Communication efficace</li>\n<li>Gestion du stress et intelligence &eacute;motionnelle</li>\n<li>Travail d&rsquo;&eacute;quipe et r&eacute;solution de conflits</li>\n</ul>\n\n<h4>P&ocirc;le 4 : Entrepreneuriat &amp; Innovation</h4>\n<ul>\n<li>Lancement et structuration d&rsquo;entreprise</li>\n<li>Cr&eacute;ation de business plan et marketing digital</li>\n<li>Finance entrepreneuriale : bilan de d&eacute;part, financement, rentabilit&eacute; et efficience</li>\n<li>Suivi et &Eacute;valuation des projets de d&eacute;veloppement</li>\n</ul>\n\n<h4>P&ocirc;le 5 : Recherche, Analyse &amp; Production de Connaissances</h4>\n<ul>\n<li>&Eacute;tudes de faisabilit&eacute; de projets et programmes</li>\n<li>&Eacute;valuation de projets : ex-ante, en cours et ex-post, indicateurs quantitatifs et qualitatifs</li>\n<li>Programmes de d&eacute;veloppement : conception, suivi, impact et recommandations</li>\n<li>&Eacute;tudes de G&eacute;nie Civil : conception, planification, analyses techniques et suivi de chantier</li>\n<li>Analyses socio-&eacute;conomiques : mesure de l&rsquo;impact des projets et programmes sur les communaut&eacute;s et les organisations</li>\n</ul>\n\n<h3>Mod&egrave;le p&eacute;dagogique</h3>\n<ul>\n<li>Approche pratique : ateliers, &eacute;tudes de cas et simulations</li>\n<li>Formation modulable : d&eacute;butant, interm&eacute;diaire, avanc&eacute;</li>\n<li>Apprentissage par comp&eacute;tences (APC) et &eacute;valuation continue</li>\n<li>Certifications professionnelles et accompagnement post-formation</li>\n</ul>\n\n<h3>Partenariats strat&eacute;giques</h3>\n<ul>\n<li>Universit&eacute;s et &eacute;coles techniques</li>\n<li>Institutions publiques</li>\n<li>Entreprises et experts certifi&eacute;s</li>\n<li>Organisations sans but lucratif, telles que les ONG locales et internationales</li>\n</ul>', '2026-06-05 16:41:49');

-- --------------------------------------------------------

--
-- Structure de la table `attendace_course_mode`
--

DROP TABLE IF EXISTS `attendace_course_mode`;
CREATE TABLE IF NOT EXISTS `attendace_course_mode` (
  `id_attendance` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `nom_attendance` varchar(200) NOT NULL,
  PRIMARY KEY (`id_attendance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `carousels`
--

DROP TABLE IF EXISTS `carousels`;
CREATE TABLE IF NOT EXISTS `carousels` (
  `IdCarousel` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `Image` varchar(250) NOT NULL,
  `Description` text NOT NULL,
  `Detail` text NOT NULL,
  `IsActive` tinyint(1) NOT NULL,
  `Title` varchar(200) NOT NULL,
  `IdProductType` int DEFAULT NULL,
  `date_insertion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdCarousel`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `carousels`
--

INSERT INTO `carousels` (`IdCarousel`, `uuid`, `Image`, `Description`, `Detail`, `IsActive`, `Title`, `IdProductType`, `date_insertion`) VALUES
(2, 'cf5ec000-60cc-11f1-a098-040e3cd4deb3', 'hhhhr.jfif', 'Des formations de qualite pour tous', 'DÚtail qualitÚ', 1, 'Qualite et Excellence', NULL, '2026-06-05 12:53:39'),
(3, 'cf5ec187-60cc-11f1-a098-040e3cd4deb3', 'hiuij.jfif', 'DÚveloppez vos compÚtences avec nous', 'DÚtail compÚtences', 1, 'Developpement des competences', NULL, '2026-06-05 12:53:39'),
(5, 'cf5e61ff-60cc-11f1-a098-040e3cd4deb3', 'formation_encourss.jfif', 'Formations professionnelles adaptees a vos besoins', 'Detail formation', 1, 'Formation en cours', NULL, '2026-06-05 12:53:39');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id_categorie` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `nom_categories` varchar(200) NOT NULL,
  `Image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id_categorie`, `uuid`, `nom_categories`, `Image`) VALUES
(1, 'b123d202-60c0-11f1-a098-040e3cd4deb3', 'Suivi et Evaluation', '202606051349386a22d3f257368.png');

-- --------------------------------------------------------

--
-- Structure de la table `contact_us`
--

DROP TABLE IF EXISTS `contact_us`;
CREATE TABLE IF NOT EXISTS `contact_us` (
  `IdContact` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `FullName` varchar(250) NOT NULL,
  `Email` varchar(250) NOT NULL,
  `Subject` varchar(250) NOT NULL,
  `Message` text NOT NULL,
  `PhoneNumber` varchar(12) NOT NULL,
  `Date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Location` varchar(200) NOT NULL,
  PRIMARY KEY (`IdContact`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id_course` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `nom_course` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `id_categorie` int NOT NULL,
  `id_teacher` int NOT NULL,
  `date_insertion` datetime NOT NULL,
  `description` text,
  PRIMARY KEY (`id_course`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `courses`
--

INSERT INTO `courses` (`id_course`, `uuid`, `nom_course`, `image`, `id_categorie`, `id_teacher`, `date_insertion`, `description`) VALUES
(1, '000532d2-60c4-11f1-a098-040e3cd4deb3', 'Concepts de base du Suivi et Evaluation', '20260605144330_6a22e0924d03e.png', 1, 1, '2026-06-05 09:50:35', ''),
(2, '002e9727-60c4-11f1-a098-040e3cd4deb3', 'Theorie du changement et logique d intervention', '20260605145046_6a22e2460bd0c.png', 1, 1, '2026-06-05 09:50:35', ''),
(3, '0045df3b-60c4-11f1-a098-040e3cd4deb3', 'Cadre logique (Logframe)', '20260605145339_6a22e2f35832c.png', 1, 1, '2026-06-05 09:50:35', ''),
(4, '005ba9db-60c4-11f1-a098-040e3cd4deb3', 'Indicateurs de S&E', '20260605145529_6a22e36132d15.png', 1, 1, '2026-06-05 09:50:36', ''),
(5, '00716eba-60c4-11f1-a098-040e3cd4deb3', 'Collecte des donnees', '20260605145747_6a22e3ebe2734.png', 1, 1, '2026-06-05 09:50:36', ''),
(6, '0087c270-60c4-11f1-a098-040e3cd4deb3', 'Qualite des donnees du S&E', '20260605143317_6a22de2d3b621.png', 1, 1, '2026-06-05 09:50:36', ''),
(7, '0097a969-60c4-11f1-a098-040e3cd4deb3', 'Analyse des donnees du S&E', '20260605145515_6a22e353916d8.png', 1, 1, '2026-06-05 09:50:36', ''),
(8, '00a7a530-60c4-11f1-a098-040e3cd4deb3', 'Evaluations de projets', '20260605141934_6a22daf65614d.png', 1, 1, '2026-06-05 09:50:36', ''),
(9, '00b8a083-60c4-11f1-a098-040e3cd4deb3', 'Plan de S&E', '20260605145757_6a22e3f51379e.png', 1, 1, '2026-06-05 09:50:36', ''),
(10, '00cf39ef-60c4-11f1-a098-040e3cd4deb3', 'Etude de cas et Test Fin de cours', '20260605141921_6a22dae9e1581.png', 1, 1, '2026-06-05 09:50:36', '');

-- --------------------------------------------------------

--
-- Structure de la table `email_logs`
--

DROP TABLE IF EXISTS `email_logs`;
CREATE TABLE IF NOT EXISTS `email_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `recipient` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `status` enum('succès','échec') DEFAULT NULL,
  `error_msg` text,
  `duration` decimal(10,4) DEFAULT '0.0000',
  `sent_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `sent_at` (`sent_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `titre` varchar(255) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `mois` varchar(20) NOT NULL,
  `annee` int DEFAULT '2025',
  `est_en_ligne` tinyint(1) DEFAULT '0',
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IsActive` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gallery`
--

DROP TABLE IF EXISTS `gallery`;
CREATE TABLE IF NOT EXISTS `gallery` (
  `IdGallery` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `TypeMedia` enum('image','video','link') NOT NULL,
  `Media` varchar(255) NOT NULL,
  `Description` text,
  `Created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdGallery`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `idGroup` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `group_name` varchar(255) NOT NULL,
  `permission` text NOT NULL,
  PRIMARY KEY (`idGroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Structure de la table `inscriptions`
--

DROP TABLE IF EXISTS `inscriptions`;
CREATE TABLE IF NOT EXISTS `inscriptions` (
  `id_inscription` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `id_course` int NOT NULL,
  `id_timetable_course` int NOT NULL,
  `id_attendance` int NOT NULL,
  `id_mode_payement` int NOT NULL,
  `id_student` int NOT NULL,
  `your_country` varchar(200) NOT NULL,
  `invoice_type` enum('individual','company') NOT NULL DEFAULT 'individual',
  `status_payement` enum('pending','paid','failed') NOT NULL DEFAULT 'pending',
  `email_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `email_confirmation_token` varchar(255) DEFAULT NULL,
  `email_confirmed_at` datetime DEFAULT NULL,
  `token_expired_at` datetime DEFAULT NULL,
  `status_started_course` tinyint(1) NOT NULL DEFAULT '0',
  `status_ended_course` tinyint(1) NOT NULL DEFAULT '0',
  `reminder_sent` tinyint(1) NOT NULL DEFAULT '0',
  `date_insertion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_inscription`),
  KEY `fk_inscription_course` (`id_course`),
  KEY `fk_inscription_timetable` (`id_timetable_course`),
  KEY `fk_inscription_attendance` (`id_attendance`),
  KEY `fk_inscription_payment_mode` (`id_mode_payement`),
  KEY `fk_inscription_student` (`id_student`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `join_us`
--

DROP TABLE IF EXISTS `join_us`;
CREATE TABLE IF NOT EXISTS `join_us` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `menu`
--

DROP TABLE IF EXISTS `menu`;
CREATE TABLE IF NOT EXISTS `menu` (
  `IdMenu` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `Menu` varchar(50) NOT NULL,
  `HasCreate` tinyint(1) NOT NULL,
  `HasRead` tinyint(1) NOT NULL,
  `HasUpdate` tinyint(1) NOT NULL,
  `HasDelete` tinyint(1) NOT NULL,
  `Code` varchar(100) NOT NULL,
  PRIMARY KEY (`IdMenu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mission`
--

DROP TABLE IF EXISTS `mission`;
CREATE TABLE IF NOT EXISTS `mission` (
  `id_mission` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mission`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `mission`
--

INSERT INTO `mission` (`id_mission`, `uuid`, `content`, `date_creation`) VALUES
(1, 'cdb606df-03cc-4874-996c-1bb6cbfb46f1', '<p><strong>Mission</strong></p>\r\n\r\n<p>D&eacute;velopper des comp&eacute;tences techniques avanc&eacute;es et des capacit&eacute;s de pilotage strat&eacute;gique <strong>chez les apprenants</strong>, afin qu&rsquo;ils puissent :</p>\r\n\r\n<ul>\r\n  <li><strong>Prendre des d&eacute;cisions bas&eacute;es sur des donn&eacute;es fiables et pertinentes</strong>, pour r&eacute;soudre les probl&egrave;mes et atteindre les objectifs de leurs organisations.</li>\r\n  <li><strong>Suivre et mesurer la performance</strong> en temps r&eacute;el &agrave; l&rsquo;aide de tableaux de bord interactifs, pour assurer la redevabilit&eacute; et l&rsquo;efficacit&eacute; des actions.</li>\r\n  <li><strong>Optimiser les processus op&eacute;rationnels</strong> de leurs structures afin d&rsquo;am&eacute;liorer la performance globale.</li>\r\n  <li><strong>&Eacute;valuer et piloter des projets et programmes</strong> afin de maximiser leur impact et leur apprentissage, tout en utilisant efficacement les ressources disponibles.</li>\r\n</ul>\r\n', '2026-06-05 16:27:28');

-- --------------------------------------------------------

--
-- Structure de la table `mode_payement`
--

DROP TABLE IF EXISTS `mode_payement`;
CREATE TABLE IF NOT EXISTS `mode_payement` (
  `id_mode_payement` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`id_mode_payement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `newsletter`
--

DROP TABLE IF EXISTS `newsletter`;
CREATE TABLE IF NOT EXISTS `newsletter` (
  `id_newsletter` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_newsletter`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `news_media`
--

DROP TABLE IF EXISTS `news_media`;
CREATE TABLE IF NOT EXISTS `news_media` (
  `id_news_media` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `title` varchar(200) NOT NULL,
  `image` varchar(200) NOT NULL,
  `date_insertion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `details` text NOT NULL,
  PRIMARY KEY (`id_news_media`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `partener`
--

DROP TABLE IF EXISTS `partener`;
CREATE TABLE IF NOT EXISTS `partener` (
  `id_partner` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `logo` text,
  `link` varchar(200) NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_partner`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `IdSetting` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `KeyValue` varchar(250) NOT NULL,
  `TitlePage` varchar(200) DEFAULT NULL,
  `Value` text,
  `IsFile` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdSetting`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `settings`
--

INSERT INTO `settings` (`IdSetting`, `uuid`, `KeyValue`, `TitlePage`, `Value`, `IsFile`) VALUES
(2, '2d8de14a-6032-11f1-b822-9c7bef735b1f', 'site_name', 'Nom du site', 'AbelaB Formation', 0),
(3, '2d8de7df-6032-11f1-b822-9c7bef735b1f', 'site_logo', 'Logo du site', '202512102012516939c633775d7.png', 1),
(4, '2d8de97c-6032-11f1-b822-9c7bef735b1f', 'site_favicon', 'Favicon du site', '202512101906456939b6b5d5d23.jpg', 1),
(5, '2d8deab0-6032-11f1-b822-9c7bef735b1f', 'site_email', 'Email contact', 'info@cerfop.bi', 0),
(6, '2d8dec19-6032-11f1-b822-9c7bef735b1f', 'site_phone', 'Téléphone contact', '+257 68 86 39 45', 0),
(7, '2d8ded38-6032-11f1-b822-9c7bef735b1f', 'site_address', 'Adresse du siège', 'Bujumbura, Burundi', 0),
(8, '2d8dee56-6032-11f1-b822-9c7bef735b1f', 'site_country', 'Pays du site', 'Burundi', 0),
(9, '2d8def73-6032-11f1-b822-9c7bef735b1f', 'maintenance_mode', 'Mode maintenance', '0', 0),
(10, '2d8df0b2-6032-11f1-b822-9c7bef735b1f', 'timezone', 'Fuseau horaire', 'Africa/Bujumbura', 0),
(11, '2d8df1d5-6032-11f1-b822-9c7bef735b1f', 'currency', 'Devise', 'BIF', 0),
(12, '2d8df2e8-6032-11f1-b822-9c7bef735b1f', 'language', 'Langue par défaut', 'fr', 0),
(13, '2d8df420-6032-11f1-b822-9c7bef735b1f', 'payment_paypal', 'PayPal activé', '1', 0),
(14, '2d8df539-6032-11f1-b822-9c7bef735b1f', 'payment_stripe', 'Stripe activé', '1', 0),
(15, '2d8df648-6032-11f1-b822-9c7bef735b1f', 'payment_bank_transfer', 'Virement bancaire activé', '1', 0),
(16, '2d8df759-6032-11f1-b822-9c7bef735b1f', 'default_invoice_type', 'Type de facturation par défaut', 'Entreprise', 0),
(17, '2d8df869-6032-11f1-b822-9c7bef735b1f', 'default_attendance_mode', 'Mode de présence par défaut', 'En ligne', 0),
(18, '2d8df983-6032-11f1-b822-9c7bef735b1f', 'error_course_not_found', 'Erreur Cours', 'Le cours que vous essayez d\'accéder n\'existe pas.', 0),
(19, '2d8dfab3-6032-11f1-b822-9c7bef735b1f', 'error_already_registered', 'Erreur Inscription', 'Vous êtes déjà inscrit dans ce cours.', 0),
(20, '2d8dfbf1-6032-11f1-b822-9c7bef735b1f', 'error_payment_failed', 'Erreur Paiement', 'Le paiement n\'a pas pu être effectué. Veuillez réessayer.', 0),
(21, '2d8dfd6a-6032-11f1-b822-9c7bef735b1f', 'error_form_incomplete', 'Erreur Formulaire', 'Tous les champs obligatoires doivent être remplis.', 0),
(22, '2d8dfe8e-6032-11f1-b822-9c7bef735b1f', 'error_server', 'Erreur Serveur', 'Une erreur interne est survenue, contactez l\'administrateur.', 0),
(24, '2d8dffd6-6032-11f1-b822-9c7bef735b1f', 'site_description', 'description dans le footer', 'créativité est au cœur de l\'innovation. AbeLab est votre laboratoire de formation dédié aux technologies.', 0),
(25, 'b2310716-462f-430a-a8c7-f0ff5ab326dd', 'login_cover', 'Image couverture connexion', 'assets/admin/images/login-images/login-cover.svg', 1),
(26, '2892aa82-864c-4c83-8e84-b8a62b131982', 'site_og_image', 'Image partage réseaux (OG)', 'assets/admin/images/og-image.jpg', 1),
(27, '57db3656-1ad6-43da-9486-e4d150da5697', 'site_twitter_image', 'Image partage Twitter', 'assets/admin/images/twitter-image.jpg', 1),
(28, '5ff00e84-08f3-4746-84b4-bc3e2acfc4b6', 'default_avatar', 'Avatar par défaut', 'assets/admin/images/user.png', 1),
(29, '56d8e196-0c80-431a-a810-80aee1b3d522', 'default_course_image', 'Image cours par défaut', 'assets/images/abelab.png', 1);

-- --------------------------------------------------------

--
-- Structure de la table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `id_student` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `fullname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(200) NOT NULL,
  `address` varchar(200) NOT NULL,
  PRIMARY KEY (`id_student`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
CREATE TABLE IF NOT EXISTS `teachers` (
  `id_teacher` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `nom` varchar(200) NOT NULL,
  `prenom` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(200) NOT NULL,
  `specialite` varchar(200) NOT NULL,
  `experience` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date_insertion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_teacher`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `teachers`
--

INSERT INTO `teachers` (`id_teacher`, `uuid`, `nom`, `prenom`, `email`, `phone`, `specialite`, `experience`, `status`, `date_insertion`) VALUES
(1, 'b12b637e-60c0-11f1-a098-040e3cd4deb3', 'FABO', 'Issa', 'issa.fabo@cerfop.bi', '+257 68 86 39 45', 'Suivi et Evaluation', 10, 1, '2026-06-05 09:26:54');

-- --------------------------------------------------------

--
-- Structure de la table `testimonies`
--

DROP TABLE IF EXISTS `testimonies`;
CREATE TABLE IF NOT EXISTS `testimonies` (
  `IdTestimony` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `Testifier` varchar(250) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `rating` tinyint DEFAULT NULL,
  `Image` varchar(250) DEFAULT NULL,
  `Poste` varchar(250) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `Details` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdTestimony`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `timetable`
--

DROP TABLE IF EXISTS `timetable`;
CREATE TABLE IF NOT EXISTS `timetable` (
  `id_timetable` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `date_debut` datetime NOT NULL,
  `date_defin` datetime NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_teacher` int NOT NULL,
  `date_insertion` datetime NOT NULL,
  PRIMARY KEY (`id_timetable`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `timetable`
--

INSERT INTO `timetable` (`id_timetable`, `uuid`, `date_debut`, `date_defin`, `time`, `id_teacher`, `date_insertion`) VALUES
(1, '0014ea57-60c4-11f1-a098-040e3cd4deb3', '2026-06-05 18:00:00', '2026-06-05 20:00:00', '2026-06-05 07:50:35', 1, '2026-06-05 09:50:35'),
(2, '001cc9c8-60c4-11f1-a098-040e3cd4deb3', '2026-06-06 09:00:00', '2026-06-06 11:00:00', '2026-06-05 07:50:35', 1, '2026-06-05 09:50:35'),
(3, '00318272-60c4-11f1-a098-040e3cd4deb3', '2026-06-12 18:00:00', '2026-06-12 20:00:00', '2026-06-05 07:50:35', 1, '2026-06-05 09:50:35'),
(4, '003d1e96-60c4-11f1-a098-040e3cd4deb3', '2026-06-13 09:00:00', '2026-06-13 11:00:00', '2026-06-05 07:50:35', 1, '2026-06-05 09:50:35'),
(5, '0049749f-60c4-11f1-a098-040e3cd4deb3', '2026-06-19 18:00:00', '2026-06-19 20:00:00', '2026-06-05 07:50:35', 1, '2026-06-05 09:50:35'),
(6, '0054817c-60c4-11f1-a098-040e3cd4deb3', '2026-06-20 09:00:00', '2026-06-20 11:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36'),
(7, '00617fda-60c4-11f1-a098-040e3cd4deb3', '2026-06-26 18:00:00', '2026-06-26 20:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36'),
(8, '0069eb8b-60c4-11f1-a098-040e3cd4deb3', '2026-06-27 09:00:00', '2026-06-27 11:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36'),
(9, '00749356-60c4-11f1-a098-040e3cd4deb3', '2026-07-03 18:00:00', '2026-07-03 20:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36'),
(10, '007e7d37-60c4-11f1-a098-040e3cd4deb3', '2026-07-04 09:00:00', '2026-07-04 11:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36'),
(11, '008a9859-60c4-11f1-a098-040e3cd4deb3', '2026-07-10 18:00:00', '2026-07-10 20:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36'),
(12, '0091afcd-60c4-11f1-a098-040e3cd4deb3', '2026-07-11 09:00:00', '2026-07-11 11:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36'),
(13, '009b4ae1-60c4-11f1-a098-040e3cd4deb3', '2026-07-17 18:00:00', '2026-07-17 20:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36'),
(14, '00a14dca-60c4-11f1-a098-040e3cd4deb3', '2026-07-18 09:00:00', '2026-07-18 11:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36'),
(15, '00ab212a-60c4-11f1-a098-040e3cd4deb3', '2026-07-24 18:00:00', '2026-07-24 20:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36'),
(16, '00b1f663-60c4-11f1-a098-040e3cd4deb3', '2026-07-25 09:00:00', '2026-07-25 11:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36'),
(17, '00bbdf22-60c4-11f1-a098-040e3cd4deb3', '2026-07-31 18:00:00', '2026-07-31 20:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36'),
(18, '00c2d896-60c4-11f1-a098-040e3cd4deb3', '2026-08-01 09:00:00', '2026-08-01 11:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36'),
(19, '00d51184-60c4-11f1-a098-040e3cd4deb3', '2026-08-07 18:00:00', '2026-08-07 20:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36'),
(20, '00dc043a-60c4-11f1-a098-040e3cd4deb3', '2026-08-08 09:00:00', '2026-08-08 11:00:00', '2026-06-05 07:50:36', 1, '2026-06-05 09:50:36');

-- --------------------------------------------------------

--
-- Structure de la table `timetable_courses`
--

DROP TABLE IF EXISTS `timetable_courses`;
CREATE TABLE IF NOT EXISTS `timetable_courses` (
  `id_timetable_course` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `id_course` int NOT NULL,
  `id_timetable` int NOT NULL,
  `date_insertion` datetime NOT NULL,
  `localisation` varchar(200) NOT NULL,
  `price` varchar(200) NOT NULL,
  PRIMARY KEY (`id_timetable_course`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `timetable_courses`
--

INSERT INTO `timetable_courses` (`id_timetable_course`, `uuid`, `id_course`, `id_timetable`, `date_insertion`, `localisation`, `price`) VALUES
(1, '0018b115-60c4-11f1-a098-040e3cd4deb3', 1, 1, '2026-06-05 09:50:35', 'Siege', ''),
(2, '0025d84c-60c4-11f1-a098-040e3cd4deb3', 1, 2, '2026-06-05 09:50:35', 'Siege', ''),
(3, '003a16ad-60c4-11f1-a098-040e3cd4deb3', 2, 3, '2026-06-05 09:50:35', 'Siege', ''),
(4, '00412889-60c4-11f1-a098-040e3cd4deb3', 2, 4, '2026-06-05 09:50:35', 'Siege', ''),
(5, '0050c88b-60c4-11f1-a098-040e3cd4deb3', 3, 5, '2026-06-05 09:50:36', 'Siege', ''),
(6, '0058c323-60c4-11f1-a098-040e3cd4deb3', 3, 6, '2026-06-05 09:50:36', 'Siege', ''),
(7, '0066965d-60c4-11f1-a098-040e3cd4deb3', 4, 7, '2026-06-05 09:50:36', 'Siege', ''),
(8, '006d17b2-60c4-11f1-a098-040e3cd4deb3', 4, 8, '2026-06-05 09:50:36', 'Siege', ''),
(9, '0077a924-60c4-11f1-a098-040e3cd4deb3', 5, 9, '2026-06-05 09:50:36', 'Siege', ''),
(10, '00851383-60c4-11f1-a098-040e3cd4deb3', 5, 10, '2026-06-05 09:50:36', 'Siege', ''),
(11, '008ec959-60c4-11f1-a098-040e3cd4deb3', 6, 11, '2026-06-05 09:50:36', 'Siege', ''),
(12, '0094abfd-60c4-11f1-a098-040e3cd4deb3', 6, 12, '2026-06-05 09:50:36', 'Siege', ''),
(13, '009e5878-60c4-11f1-a098-040e3cd4deb3', 7, 13, '2026-06-05 09:50:36', 'Siege', ''),
(14, '00a48d04-60c4-11f1-a098-040e3cd4deb3', 7, 14, '2026-06-05 09:50:36', 'Siege', ''),
(15, '00aea32f-60c4-11f1-a098-040e3cd4deb3', 8, 15, '2026-06-05 09:50:36', 'Siege', ''),
(16, '00b55258-60c4-11f1-a098-040e3cd4deb3', 8, 16, '2026-06-05 09:50:36', 'Siege', ''),
(17, '00bf3f55-60c4-11f1-a098-040e3cd4deb3', 9, 17, '2026-06-05 09:50:36', 'Siege', ''),
(18, '00c61463-60c4-11f1-a098-040e3cd4deb3', 9, 18, '2026-06-05 09:50:36', 'Siege', ''),
(19, '00d8c66b-60c4-11f1-a098-040e3cd4deb3', 10, 19, '2026-06-05 09:50:36', 'Siege', ''),
(20, '00def2b4-60c4-11f1-a098-040e3cd4deb3', 10, 20, '2026-06-05 09:50:36', 'Siege', '');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `idUser` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `firstName` varchar(200) NOT NULL,
  `lastName` varchar(200) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `telephone` varchar(100) NOT NULL,
  `idGroup` int NOT NULL,
  `image` varchar(200) NOT NULL,
  `dateinsertion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status_user` int NOT NULL DEFAULT '1' COMMENT '1:actif;2:suspendus:3:sortis',
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`idUser`, `uuid`, `firstName`, `lastName`, `username`, `email`, `telephone`, `idGroup`, `image`, `dateinsertion`, `status_user`) VALUES
(1, '2d96aa70-6032-11f1-b822-9c7bef735b1f', 'admina', 'admina', 'admina', 'admina@gmail.com', '3678e465789', 1, '202606042214326a21f8c8d45d1.jpeg', '2024-06-07 16:51:06', 1);

-- --------------------------------------------------------

--
-- Structure de la table `user_group`
--

DROP TABLE IF EXISTS `user_group`;
CREATE TABLE IF NOT EXISTS `user_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `idUser` int NOT NULL,
  `idGroup` int NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` varchar(500) NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `user_group`
--

INSERT INTO `user_group` (`id`, `uuid`, `idUser`, `idGroup`, `username`, `password`, `isActive`) VALUES
(1, '2d95ef62-6032-11f1-b822-9c7bef735b1f', 1, 1, 'admina', '25d55ad283aa400af464c76d713c07ad', 1);

-- --------------------------------------------------------

--
-- Structure de la table `vision`
--

DROP TABLE IF EXISTS `vision`;
CREATE TABLE IF NOT EXISTS `vision` (
  `id_vision` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL DEFAULT '',
  `content` varchar(200) NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_vision`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `vision`
--

INSERT INTO `vision` (`id_vision`, `uuid`, `content`, `date_creation`) VALUES
(1, 'ac118bca-9d36-431d-be4a-ee364d56b0f4', '<p>&Ecirc;tre un centre de r&eacute;f&eacute;rence en d&eacute;veloppement des comp&eacute;tences, recherche appliqu&eacute;e et innovation professionnelle, capable de transformer individus, organisat', '2026-06-05 16:27:07');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD CONSTRAINT `fk_inscription_attendance` FOREIGN KEY (`id_attendance`) REFERENCES `attendace_course_mode` (`id_attendance`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscription_course` FOREIGN KEY (`id_course`) REFERENCES `courses` (`id_course`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscription_payment_mode` FOREIGN KEY (`id_mode_payement`) REFERENCES `mode_payement` (`id_mode_payement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscription_student` FOREIGN KEY (`id_student`) REFERENCES `students` (`id_student`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscription_timetable` FOREIGN KEY (`id_timetable_course`) REFERENCES `timetable_courses` (`id_timetable_course`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
