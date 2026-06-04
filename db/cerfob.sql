-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 04 juin 2026 à 22:23
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `id_categorie` int NOT NULL,
  `id_teacher` int NOT NULL,
  `date_insertion` datetime NOT NULL,
  `description` text,
  PRIMARY KEY (`id_course`)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
