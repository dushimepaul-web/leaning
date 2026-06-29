-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 29 juin 2026 à 13:08
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
-- Base de données : `vip_school`
--

-- --------------------------------------------------------

--
-- Structure de la table `annees_scolaires`
--

DROP TABLE IF EXISTS `annees_scolaires`;
CREATE TABLE IF NOT EXISTS `annees_scolaires` (
  `id_annee` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `debut` date NOT NULL,
  `fin` date NOT NULL,
  `est_en_cours` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_annee`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `annees_scolaires`
--

INSERT INTO `annees_scolaires` (`id_annee`, `uuid`, `libelle`, `debut`, `fin`, `est_en_cours`, `deleted_at`, `cree_le`, `modifie_le`) VALUES
(1, '19bd43cf-ebc6-43b9-95ac-48992c3eace5', '2025-2026', '2025-09-09', '2026-07-12', 1, NULL, '2026-06-09 01:06:14', '2026-06-29 13:35:22');

-- --------------------------------------------------------

--
-- Structure de la table `assurances`
--

DROP TABLE IF EXISTS `assurances`;
CREATE TABLE IF NOT EXISTS `assurances` (
  `id_assurance` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_etudiant` int NOT NULL,
  `police` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `compagnie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `statut` enum('active','expiree','resiliee') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_assurance`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `police` (`police`),
  KEY `id_etudiant` (`id_etudiant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id_log` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_utilisateur` int DEFAULT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_concernee` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_enregistrement` int DEFAULT NULL,
  `anciennes_valeurs` json DEFAULT NULL,
  `nouvelles_valeurs` json DEFAULT NULL,
  `adresse_ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_action` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `idx_audit_table` (`table_concernee`),
  KEY `idx_audit_date` (`date_action`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `audit_logs`
--

INSERT INTO `audit_logs` (`id_log`, `uuid`, `id_utilisateur`, `action`, `table_concernee`, `id_enregistrement`, `anciennes_valeurs`, `nouvelles_valeurs`, `adresse_ip`, `date_action`, `cree_le`) VALUES
(1, '51ade7a6-ae99-442e-b604-6c1632619250', NULL, 'update', 'roles_menus', 1, NULL, NULL, '::1', '2026-06-29 12:07:26', '2026-06-29 12:07:26');

-- --------------------------------------------------------

--
-- Structure de la table `bulletins`
--

DROP TABLE IF EXISTS `bulletins`;
CREATE TABLE IF NOT EXISTS `bulletins` (
  `id_bulletin` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_etudiant` int NOT NULL,
  `id_classe` int NOT NULL,
  `id_annee` int NOT NULL,
  `id_periode` int NOT NULL,
  `moyenne` decimal(5,2) DEFAULT NULL,
  `rang` int DEFAULT NULL,
  `decision` enum('admis','ajourne','echoue') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'admis',
  `date_edition` date NOT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_bulletin`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_bulletin` (`id_etudiant`,`id_annee`,`id_periode`),
  KEY `id_classe` (`id_classe`),
  KEY `id_annee` (`id_annee`),
  KEY `idx_bulletins_periode` (`id_periode`),
  KEY `idx_bulletins_decision` (`decision`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bulletins`
--

INSERT INTO `bulletins` (`id_bulletin`, `uuid`, `id_etudiant`, `id_classe`, `id_annee`, `id_periode`, `moyenne`, `rang`, `decision`, `date_edition`, `cree_le`, `modifie_le`, `deleted_at`) VALUES
(1, 'ff661c98-a537-4a4f-ba85-6079195bb0b9', 2, 7, 1, 1, NULL, NULL, 'ajourne', '2026-06-18', '2026-06-18 15:30:42', '2026-06-18 15:30:42', NULL),
(2, 'f2170613-4d5c-4a55-b207-cca0cf999766', 1, 1, 1, 3, 28.75, 1, 'admis', '2026-06-19', '2026-06-19 13:05:18', '2026-06-19 13:10:34', NULL),
(3, '5cfc4237-9732-47ea-b057-4808207669a8', 2, 1, 1, 3, 22.00, 2, 'admis', '2026-06-19', '2026-06-19 13:05:18', '2026-06-19 13:10:34', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `categories_produits`
--

DROP TABLE IF EXISTS `categories_produits`;
CREATE TABLE IF NOT EXISTS `categories_produits` (
  `id_categorie` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_categorie`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories_produits`
--

INSERT INTO `categories_produits` (`id_categorie`, `uuid`, `code`, `libelle`, `cree_le`, `deleted_at`) VALUES
(1, '79541d51-6369-11f1-9d55-9c7bef735b1f', 'UNIFORME', 'Uniformes', '2026-06-08 20:40:08', NULL),
(2, '79542150-6369-11f1-9d55-9c7bef735b1f', 'LIVRE', 'Livres et manuels', '2026-06-08 20:40:08', NULL),
(3, '795422d3-6369-11f1-9d55-9c7bef735b1f', 'MATERIEL', 'Matériels scolaires', '2026-06-08 20:40:08', NULL),
(4, 'c10f846a-6fcb-11f1-bfbc-9c7bef735b1f', 'FOURNITURE', 'Fournitures diverses', '2026-06-24 14:53:53', NULL),
(5, 'c10f932c-6fcb-11f1-bfbc-9c7bef735b1f', 'TOILETTE', 'Produits de toilette', '2026-06-24 14:53:53', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id_classe` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_section` int NOT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `niveau` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_classe`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`),
  KEY `id_section` (`id_section`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `classes`
--

INSERT INTO `classes` (`id_classe`, `uuid`, `id_section`, `code`, `libelle`, `niveau`, `deleted_at`, `cree_le`, `modifie_le`) VALUES
(1, 'CLS-9d12ba2f52436b1d5c1bbaa969ed', 1, '', '1ère Primaire', NULL, NULL, '2026-06-09 22:34:08', '2026-06-09 22:34:08'),
(7, 'CLS-f081c492fe715d6d45cd534266e9', 1, '2P', '2ème Primaire', NULL, NULL, '2026-06-09 22:37:07', '2026-06-09 22:37:07'),
(8, 'CLS-7aea4e0269878dae578f62e5aa06', 2, '3S', '3ème Secondaire', NULL, NULL, '2026-06-09 22:37:07', '2026-06-09 22:37:07');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `id_commande` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_etudiant` int NOT NULL,
  `date_commande` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('en_attente','prete','distribuee','annulee') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_commande`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `idx_commandes_etudiant` (`id_etudiant`),
  KEY `idx_commandes_statut` (`statut`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id_commande`, `uuid`, `id_etudiant`, `date_commande`, `statut`, `total`, `cree_le`, `modifie_le`, `deleted_at`) VALUES
(1, 'a7506e81-42e2-4b8a-a848-880066556497', 2, '2026-06-18 00:00:00', 'prete', 23.00, '2026-06-18 15:22:07', '2026-06-18 15:22:46', NULL),
(2, '14c19a47-2009-4d04-a66d-21607584fc83', 2, '2026-06-29 00:00:00', 'en_attente', 23.00, '2026-06-29 12:00:24', '2026-06-29 12:00:49', '2026-06-29 10:00:49');

-- --------------------------------------------------------

--
-- Structure de la table `commandes_details`
--

DROP TABLE IF EXISTS `commandes_details`;
CREATE TABLE IF NOT EXISTS `commandes_details` (
  `id_detail` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_commande` int NOT NULL,
  `id_produit` int NOT NULL,
  `quantite` int NOT NULL DEFAULT '1',
  `prix_unitaire` decimal(12,2) NOT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_detail`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_commande` (`id_commande`),
  KEY `id_produit` (`id_produit`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commandes_details`
--

INSERT INTO `commandes_details` (`id_detail`, `uuid`, `id_commande`, `id_produit`, `quantite`, `prix_unitaire`, `cree_le`) VALUES
(1, '066c7634-77a1-4456-8346-136722925f1c', 1, 1, 1, 23.00, '2026-06-18 15:22:08'),
(2, 'b0d80885-6611-473a-9c1b-56bee2da9b88', 2, 1, 1, 23.00, '2026-06-29 12:00:24');

-- --------------------------------------------------------

--
-- Structure de la table `contraintes_horaires`
--

DROP TABLE IF EXISTS `contraintes_horaires`;
CREATE TABLE IF NOT EXISTS `contraintes_horaires` (
  `id_contrainte` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_annee` int NOT NULL,
  `type` enum('matiere','classe','enseignant','global') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_concerne` int DEFAULT NULL COMMENT 'id_matiere / id_classe / id_enseignant / NULL pour global',
  `id_jour` int DEFAULT NULL,
  `id_creneau_debut` int DEFAULT NULL,
  `id_creneau_fin` int DEFAULT NULL,
  `regle` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'interdit, preferer_matin, max_consecutifs, seulement_creneau',
  `valeur` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_contrainte`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_annee` (`id_annee`),
  KEY `id_jour` (`id_jour`),
  KEY `id_creneau_debut` (`id_creneau_debut`),
  KEY `id_creneau_fin` (`id_creneau_fin`),
  KEY `idx_contraintes_type` (`type`),
  KEY `idx_contraintes_concerne` (`id_concerne`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `creneaux`
--

DROP TABLE IF EXISTS `creneaux`;
CREATE TABLE IF NOT EXISTS `creneaux` (
  `id_creneau` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `ordre` int NOT NULL DEFAULT '0',
  `type_creneau` enum('cours','recreation','pause','accueil','sortie') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cours',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_creneau`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `idx_creneaux_ordre` (`ordre`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `creneaux`
--

INSERT INTO `creneaux` (`id_creneau`, `uuid`, `libelle`, `heure_debut`, `heure_fin`, `ordre`, `type_creneau`, `cree_le`, `modifie_le`) VALUES
(6, '44445bcc-636b-11f1-9d55-9c7bef735b1f', '6ème heure', '11:25:00', '12:20:00', 6, 'cours', '2026-06-08 20:52:58', '2026-06-08 23:55:22'),
(7, '44445caa-636b-11f1-9d55-9c7bef735b1f', '5eme Heure', '10:40:00', '11:25:00', 7, 'cours', '2026-06-08 20:52:58', '2026-06-08 23:52:20'),
(8, '44445d70-636b-11f1-9d55-9c7bef735b1f', 'Pause', '10:30:00', '10:40:00', 8, 'pause', '2026-06-08 20:52:58', '2026-06-08 23:51:24'),
(9, '44445e3b-636b-11f1-9d55-9c7bef735b1f', '4ème heure', '09:45:00', '10:30:00', 9, 'cours', '2026-06-08 20:52:58', '2026-06-08 23:50:11'),
(10, '44445ef8-636b-11f1-9d55-9c7bef735b1f', '3eme heure', '09:00:00', '09:45:00', 10, 'cours', '2026-06-08 20:52:58', '2026-06-08 23:49:48'),
(11, '44445fb5-636b-11f1-9d55-9c7bef735b1f', '2ème heure', '08:15:00', '09:00:00', 11, 'cours', '2026-06-08 20:52:58', '2026-06-08 23:48:58'),
(12, '4444606f-636b-11f1-9d55-9c7bef735b1f', '1ème heure', '07:30:00', '08:15:00', 12, 'cours', '2026-06-08 20:52:58', '2026-06-08 23:48:19'),
(13, '12b36adf-60e8-4091-af5e-8995d2dddc98', '8ème heure', '13:05:00', '13:50:00', 4, 'cours', '2026-06-08 23:58:08', '2026-06-08 23:58:08'),
(14, '5f6c125f-3592-4009-8144-72ec8e82fe1c', '7ème heure', '12:55:00', '13:05:00', 5, 'cours', '2026-06-08 23:59:01', '2026-06-08 23:59:01');

-- --------------------------------------------------------

--
-- Structure de la table `disponibilites_enseignants`
--

DROP TABLE IF EXISTS `disponibilites_enseignants`;
CREATE TABLE IF NOT EXISTS `disponibilites_enseignants` (
  `id_disponibilite` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_enseignant` int NOT NULL,
  `id_creneau` int NOT NULL,
  `id_jour` int NOT NULL,
  `type` enum('disponible','indisponible') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponible',
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_disponibilite`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_dispo_ens` (`id_enseignant`,`id_creneau`,`id_jour`),
  KEY `id_creneau` (`id_creneau`),
  KEY `idx_dispos_enseignant` (`id_enseignant`),
  KEY `idx_dispos_jour` (`id_jour`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `disponibilites_enseignants`
--

INSERT INTO `disponibilites_enseignants` (`id_disponibilite`, `uuid`, `id_enseignant`, `id_creneau`, `id_jour`, `type`, `deleted_at`, `cree_le`, `modifie_le`) VALUES
(2, 'ee734c78-40b1-450f-a747-7b289885747d', 1, 9, 4, 'disponible', NULL, '2026-06-18 16:31:51', '2026-06-18 16:31:51'),
(3, 'b3caa762-80d2-484f-82a1-6e1fad3cacf8', 2, 10, 5, 'disponible', NULL, '2026-06-24 17:15:06', '2026-06-24 17:15:06');

-- --------------------------------------------------------

--
-- Structure de la table `echeances`
--

DROP TABLE IF EXISTS `echeances`;
CREATE TABLE IF NOT EXISTS `echeances` (
  `id_echeance` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_frais` int NOT NULL,
  `id_etudiant` int NOT NULL,
  `date_echeance` date NOT NULL,
  `montant` decimal(12,2) NOT NULL,
  `statut` enum('impaye','partiel','paye','annule') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'impaye',
  `rappel_envoye` tinyint(1) NOT NULL DEFAULT '0',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_echeance`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_etudiant` (`id_etudiant`),
  KEY `idx_echeances_statut` (`statut`),
  KEY `idx_echeances_date` (`date_echeance`),
  KEY `fk_echeances_frais` (`id_frais`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

DROP TABLE IF EXISTS `enseignants`;
CREATE TABLE IF NOT EXISTS `enseignants` (
  `id_enseignant` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricule` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `postnom` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prenom` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe` enum('M','F') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `telephone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `specialite` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qualification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `experience` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `id_utilisateur` int DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_enseignant`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `matricule` (`matricule`),
  KEY `idx_enseignants_actif` (`actif`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`id_enseignant`, `uuid`, `matricule`, `nom`, `postnom`, `prenom`, `sexe`, `date_naissance`, `telephone`, `email`, `adresse`, `specialite`, `qualification`, `experience`, `date_embauche`, `actif`, `id_utilisateur`, `cree_le`, `modifie_le`, `deleted_at`) VALUES
(1, '0b27cb51-2f5b-4c53-9619-2be99f02c877', 'TCH-001', 'Dupont', NULL, 'Jean', 'M', NULL, '+243800000001', 'jean.dupont@school.cd', NULL, 'CHIMIE', NULL, NULL, NULL, 1, NULL, '2026-06-09 23:21:13', '2026-06-09 23:21:13', NULL),
(2, '3a12292d-18a6-40dc-b752-1286dd9d6ebc', 'TCH-002', 'Smith', NULL, 'Alice', 'F', NULL, '+243800000002', 'alice@school.cd', NULL, NULL, NULL, NULL, NULL, 1, NULL, '2026-06-09 23:21:22', '2026-06-09 23:21:22', NULL),
(3, '767b76f7-787d-4872-a97e-59d5a167688e', 'TCH-003', 'Kabongo', 'Pierre', 'Paul', 'M', '0000-00-00', '+243800000003', 'paul@school.cd', '', 'CHIMIE', 'Master en Histoire', '10 ans', '2026-06-18', 1, NULL, '2026-06-09 23:21:22', '2026-06-16 14:56:30', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `enseignements`
--

DROP TABLE IF EXISTS `enseignements`;
CREATE TABLE IF NOT EXISTS `enseignements` (
  `id_enseignement` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_enseignant` int NOT NULL,
  `id_matiere` int NOT NULL,
  `id_classe` int NOT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_enseignement`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_enseignement` (`id_enseignant`,`id_matiere`,`id_classe`),
  KEY `id_matiere` (`id_matiere`),
  KEY `id_classe` (`id_classe`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `enseignements`
--

INSERT INTO `enseignements` (`id_enseignement`, `uuid`, `id_enseignant`, `id_matiere`, `id_classe`, `cree_le`, `deleted_at`, `modifie_le`) VALUES
(10, 'bc0739c9-ec1d-4f04-90d8-990b36ab0916', 1, 2, 1, '2026-06-19 10:32:33', NULL, '2026-06-19 10:32:33'),
(11, '943fc8cb-8440-4920-9f01-d42fa9a33e33', 3, 1, 1, '2026-06-29 12:32:37', NULL, '2026-06-29 12:32:37');

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

DROP TABLE IF EXISTS `etudiants`;
CREATE TABLE IF NOT EXISTS `etudiants` (
  `id_etudiant` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricule` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_ordre` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `postnom` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prenom` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe` enum('M','F') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `adresse_permanente` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `telephone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_nom` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_telephone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_profession` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_adresse` text COLLATE utf8mb4_unicode_ci,
  `tuteur_nom` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tuteur_telephone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_utilisateur` int DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_etudiant`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `matricule` (`matricule`),
  KEY `idx_etudiants_nom` (`nom`,`postnom`,`prenom`),
  KEY `idx_etudiants_date_naiss` (`date_naissance`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`id_etudiant`, `uuid`, `matricule`, `numero_ordre`, `nom`, `postnom`, `prenom`, `date_naissance`, `lieu_naissance`, `sexe`, `adresse`, `adresse_permanente`, `telephone`, `email`, `parent_nom`, `parent_telephone`, `parent_profession`, `parent_adresse`, `tuteur_nom`, `tuteur_telephone`, `photo`, `id_utilisateur`, `actif`, `cree_le`, `modifie_le`, `deleted_at`) VALUES
(1, 'ETU-fbf5c8199f0399b95f70fc3f1dc6', 'MAT-0001', '2', 'Dupont', 'Dupont', 'Jean', '2005-01-15', NULL, 'M', 'Kinshasa, Commune de la Gombe', NULL, '+243811000001', 'jean.dupont@email.com', 'Dupont Alphonse', '+243811000011', 'Ingenieur', NULL, NULL, NULL, '', NULL, 1, '2026-06-09 22:34:08', '2026-06-24 15:22:48', NULL),
(2, 'ETU-507ae6d117cf82f9949f527d98ce', 'MAT-0002', '3', 'Mukendi', 'Mukendi', 'Marie', '2006-02-15', NULL, 'F', 'Kinshasa, Commune de la Gombe', NULL, '+243811000002', 'marie.mukendi@email.com', 'Mukendi Joseph', '+243811000021', 'Commercant', NULL, NULL, NULL, '', NULL, 1, '2026-06-09 22:34:08', '2026-06-24 15:22:48', NULL),
(3, 'ETU-58cfcd3f88c68f0b7ace58662ac6', 'MAT-0003', '2', 'Kabongo', 'Kabongo', 'Pierre', '2007-03-15', NULL, 'M', 'Kinshasa, Commune de la Gombe', NULL, '+243811000003', 'pierre.kabongo@email.com', 'Kabongo Michel', '+243811000033', 'Avocat', '', NULL, NULL, '', NULL, 1, '2026-06-09 22:34:08', '2026-06-24 15:22:48', NULL),
(4, 'ETU-80c808283af8dcb819ea0c92849c1e8e', 'MAT-0004', '1', 'Ilunga', 'Ilunga', 'Esther', '2008-04-15', NULL, 'F', 'Kinshasa, Ngaliema', '', '+243811000004', 'esther.ilunga@email.com', 'Ilunga Paul', '+243811000043', 'Banquier', '', NULL, NULL, '', NULL, 1, '2026-06-09 22:37:07', '2026-06-24 15:22:48', NULL),
(5, 'ETU-6e995fb45570811845ae886a608c975f', 'MAT-0005', '5', 'Tshimanga', 'Tshimanga', 'David', '2009-05-15', NULL, 'M', 'Kinshasa, Lemba', NULL, '+243811000005', 'david.tshimanga@email.com', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, '2026-06-09 22:37:08', '2026-06-09 22:50:57', '2026-06-09 20:50:57'),
(6, '0879edec-9903-4c9c-a0b2-d1bfd72a8d80', '26/0001', '1', 'Administateur', 'Ilunga', 'Esther', '2003-06-24', NULL, 'M', 'NBN', NULL, '+257 79 123 456', 'paul@gmail.com', 'Ilunga Paul', '+243811000043', 'Banquier', 'MNKJ', NULL, NULL, 'assets/uploads/students/a6711edd7dd3a057907abadfca630abe.png', 2, 1, '2026-06-24 15:07:56', '2026-06-24 15:22:48', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `evaluations`
--

DROP TABLE IF EXISTS `evaluations`;
CREATE TABLE IF NOT EXISTS `evaluations` (
  `id_evaluation` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_periode` int NOT NULL,
  `libelle` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_classe` int NOT NULL,
  `id_matiere` int NOT NULL,
  `id_annee` int NOT NULL,
  `date_eval` date NOT NULL,
  `type` enum('interrogation','devoir','controle','composition','examen','tp','projet','participation','autre') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'devoir',
  `coefficient` decimal(3,1) NOT NULL DEFAULT '1.0',
  `sur` decimal(5,1) NOT NULL DEFAULT '20.0',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_evaluation`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_matiere` (`id_matiere`),
  KEY `id_annee` (`id_annee`),
  KEY `idx_evaluations_classe` (`id_classe`),
  KEY `idx_evaluations_periode` (`id_periode`),
  KEY `idx_evaluations_type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `evaluations`
--

INSERT INTO `evaluations` (`id_evaluation`, `uuid`, `id_periode`, `libelle`, `id_classe`, `id_matiere`, `id_annee`, `date_eval`, `type`, `coefficient`, `sur`, `cree_le`, `modifie_le`, `deleted_at`) VALUES
(3, 'a4b8fcf7-7bef-41f5-a087-3c4dd7c55421', 1, 'EYW', 7, 1, 1, '2026-06-18', 'devoir', 1.0, 20.0, '2026-06-18 15:41:45', '2026-06-18 15:41:45', NULL),
(4, '740eca57-b3f3-48b5-91bf-0a9a34f7480e', 3, 'interro1', 1, 1, 1, '2026-06-19', 'interrogation', 1.0, 20.0, '2026-06-19 10:48:36', '2026-06-19 10:48:36', NULL),
(5, '6a499668-078a-43b9-a2d6-2494904e5c52', 3, 'interro 2', 1, 1, 1, '2026-06-19', 'interrogation', 1.0, 20.0, '2026-06-19 10:49:18', '2026-06-19 10:49:18', NULL),
(6, '1f79880e-4f22-4d82-8805-be3d8ea0fdec', 3, 'interro 2', 1, 1, 1, '2026-06-19', 'interrogation', 1.0, 20.0, '2026-06-19 10:49:19', '2026-06-19 10:50:56', '2026-06-19 08:50:56'),
(7, '9a0f2e79-d241-4318-b406-682e9b2f38ee', 3, 'intero3', 1, 1, 1, '2026-06-19', 'interrogation', 1.0, 20.0, '2026-06-19 10:51:26', '2026-06-19 10:51:26', NULL),
(8, '33f9cf2d-8abf-4a9f-b167-eb6507fc0e14', 3, 'interro 4', 1, 1, 1, '2026-06-19', 'interrogation', 1.0, 60.0, '2026-06-19 11:06:30', '2026-06-19 11:06:30', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `evenements`
--

DROP TABLE IF EXISTS `evenements`;
CREATE TABLE IF NOT EXISTS `evenements` (
  `id_evenement` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `titre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime DEFAULT NULL,
  `lieu` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('scolaire','sportif','culturel','reunion','autre') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scolaire',
  `couleur` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#25A194',
  `statut` enum('planifie','en_cours','termine','annule') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planifie',
  `id_utilisateur_createur` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_evenement`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `date_debut` (`date_debut`),
  KEY `idx_utilisateur_createur` (`id_utilisateur_createur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `frais`
--

DROP TABLE IF EXISTS `frais`;
CREATE TABLE IF NOT EXISTS `frais` (
  `id_frais` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_type_frais` int NOT NULL,
  `id_classe` int NOT NULL,
  `id_annee` int NOT NULL,
  `montant` decimal(12,2) NOT NULL,
  `echeance` date DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_frais`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_frais` (`id_type_frais`,`id_classe`,`id_annee`),
  KEY `id_classe` (`id_classe`),
  KEY `id_annee` (`id_annee`),
  KEY `idx_frais_id_type_frais` (`id_type_frais`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `frais`
--

INSERT INTO `frais` (`id_frais`, `uuid`, `id_type_frais`, `id_classe`, `id_annee`, `montant`, `echeance`, `cree_le`, `modifie_le`, `deleted_at`) VALUES
(4, 'f5b85aea-7e0f-4fb9-8bb9-1f7b619e8e9e', 4, 7, 1, 65478.00, NULL, '2026-06-24 16:53:31', '2026-06-24 16:53:31', NULL),
(5, '17902c8a-4ac9-489c-b4a2-5e05743aa6ea', 2, 1, 1, 38983.00, NULL, '2026-06-24 16:58:24', '2026-06-24 16:58:24', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `horaires`
--

DROP TABLE IF EXISTS `horaires`;
CREATE TABLE IF NOT EXISTS `horaires` (
  `id_horaire` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_generation` int NOT NULL,
  `id_enseignement` int NOT NULL,
  `id_matiere` int DEFAULT NULL,
  `id_enseignant` int NOT NULL,
  `id_classe` int NOT NULL,
  `id_creneau` int NOT NULL,
  `id_jour` int NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_horaire`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_horaire_ens` (`id_generation`,`id_creneau`,`id_jour`,`id_enseignement`),
  UNIQUE KEY `uniq_horaire_prof` (`id_generation`,`id_creneau`,`id_jour`,`id_enseignant`),
  UNIQUE KEY `uniq_horaire_classe` (`id_generation`,`id_creneau`,`id_jour`,`id_classe`),
  KEY `id_enseignement` (`id_enseignement`),
  KEY `id_creneau` (`id_creneau`),
  KEY `idx_horaires_generation` (`id_generation`),
  KEY `idx_horaires_enseignant` (`id_enseignant`),
  KEY `idx_horaires_classe` (`id_classe`),
  KEY `idx_horaires_jour` (`id_jour`),
  KEY `idx_matiere` (`id_matiere`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `horaires_generations`
--

DROP TABLE IF EXISTS `horaires_generations`;
CREATE TABLE IF NOT EXISTS `horaires_generations` (
  `id_generation` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_annee` int NOT NULL,
  `date_generation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('brouillon','publie','archive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brouillon',
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_generation`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_annee` (`id_annee`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `horaires_generations`
--

INSERT INTO `horaires_generations` (`id_generation`, `uuid`, `libelle`, `id_annee`, `date_generation`, `statut`, `deleted_at`, `cree_le`, `modifie_le`) VALUES
(1, '7879a769-e247-49d2-ab6d-4c911f8a419f', 'Emploi du temps 2026', 1, '2026-06-18 15:51:55', 'brouillon', NULL, '2026-06-18 15:51:55', '2026-06-18 15:51:55');

-- --------------------------------------------------------

--
-- Structure de la table `inscriptions`
--

DROP TABLE IF EXISTS `inscriptions`;
CREATE TABLE IF NOT EXISTS `inscriptions` (
  `id_inscription` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_etudiant` int NOT NULL,
  `id_classe` int NOT NULL,
  `id_section` int DEFAULT NULL,
  `id_annee` int NOT NULL,
  `date_inscription` date NOT NULL,
  `statut` enum('inscrit','actif','suspendu','exclu','termine') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_inscription`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_inscription` (`id_etudiant`,`id_annee`),
  KEY `idx_inscriptions_classe` (`id_classe`),
  KEY `idx_inscriptions_annee` (`id_annee`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `inscriptions`
--

INSERT INTO `inscriptions` (`id_inscription`, `uuid`, `id_etudiant`, `id_classe`, `id_section`, `id_annee`, `date_inscription`, `statut`, `cree_le`, `modifie_le`, `deleted_at`) VALUES
(1, 'INS-9581bdfdbd88841ab41936625e78', 1, 1, 1, 1, '2026-06-09', 'actif', '2026-06-09 22:34:08', '2026-06-09 22:34:08', NULL),
(2, 'INS-aceffa5c260958ff581754ee7a30', 2, 1, 1, 1, '2026-06-09', 'actif', '2026-06-09 22:34:08', '2026-06-09 22:34:08', NULL),
(3, 'INS-ea83387dfef3e4d786590dc602f414b8', 3, 7, 1, 1, '2026-06-09', 'actif', '2026-06-09 22:37:07', '2026-06-09 22:37:07', NULL),
(4, 'INS-ea21717fdfd51db18b2d6cea11d97ec6', 4, 7, 1, 1, '2026-06-09', 'actif', '2026-06-09 22:37:07', '2026-06-09 22:37:07', NULL),
(5, 'INS-2f918a2a08905616d850f35d4d18a2fa', 5, 8, 2, 1, '2026-06-09', 'actif', '2026-06-09 22:37:08', '2026-06-09 22:50:57', '2026-06-09 20:50:57'),
(6, '1907c269-424f-4c9a-8a3c-c08acc086b18', 6, 1, 1, 1, '2026-06-24', 'actif', '2026-06-24 15:07:56', '2026-06-24 15:07:56', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `jours_semaine`
--

DROP TABLE IF EXISTS `jours_semaine`;
CREATE TABLE IF NOT EXISTS `jours_semaine` (
  `id_jour` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `ordre` int NOT NULL DEFAULT '0',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_jour`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `jours_semaine`
--

INSERT INTO `jours_semaine` (`id_jour`, `uuid`, `code`, `libelle`, `actif`, `ordre`, `cree_le`) VALUES
(1, '4434f68b-636b-11f1-9d55-9c7bef735b1f', 'lundi', 'Lundi', 1, 1, '2026-06-08 20:52:58'),
(2, '4434facd-636b-11f1-9d55-9c7bef735b1f', 'mardi', 'Mardi', 1, 2, '2026-06-08 20:52:58'),
(3, '4434fd20-636b-11f1-9d55-9c7bef735b1f', 'mercredi', 'Mercredi', 1, 3, '2026-06-08 20:52:58'),
(4, '4434fe86-636b-11f1-9d55-9c7bef735b1f', 'jeudi', 'Jeudi', 1, 4, '2026-06-08 20:52:58'),
(5, '4434ffbe-636b-11f1-9d55-9c7bef735b1f', 'vendredi', 'Vendredi', 1, 5, '2026-06-08 20:52:58'),
(6, '443500f8-636b-11f1-9d55-9c7bef735b1f', 'samedi', 'Samedi', 1, 6, '2026-06-08 20:52:58');

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

DROP TABLE IF EXISTS `matieres`;
CREATE TABLE IF NOT EXISTS `matieres` (
  `id_matiere` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_matiere`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `matieres`
--

INSERT INTO `matieres` (`id_matiere`, `uuid`, `code`, `libelle`, `cree_le`, `modifie_le`, `deleted_at`) VALUES
(1, '35b5ec42-a82e-4904-9404-c059b7c4762e', 'CHIMIE', 'CHIMIE', '2026-06-09 01:18:38', '2026-06-09 01:18:38', NULL),
(2, '73e09416-fd81-4dd2-9f6d-9f04f0d5c5ec', 'MATHEMATIQUE', 'MATHS', '2026-06-16 14:57:50', '2026-06-16 14:57:50', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `matieres_classes`
--

DROP TABLE IF EXISTS `matieres_classes`;
CREATE TABLE IF NOT EXISTS `matieres_classes` (
  `id_matiere_classe` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_matiere` int NOT NULL,
  `id_classe` int NOT NULL,
  `id_enseignant` int DEFAULT NULL,
  `coefficient` decimal(3,1) NOT NULL DEFAULT '1.0',
  `nb_heures_par_jour` decimal(4,1) NOT NULL DEFAULT '0.0',
  `nb_heures_par_semaine` decimal(4,1) NOT NULL DEFAULT '0.0',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_matiere_classe`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_matiere_classe` (`id_matiere`,`id_classe`),
  KEY `id_classe` (`id_classe`),
  KEY `id_enseignant` (`id_enseignant`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `matieres_classes`
--

INSERT INTO `matieres_classes` (`id_matiere_classe`, `uuid`, `id_matiere`, `id_classe`, `id_enseignant`, `coefficient`, `nb_heures_par_jour`, `nb_heures_par_semaine`, `cree_le`, `deleted_at`) VALUES
(1, 'd369b225-4d02-4551-adf7-b76476334f97', 1, 1, 3, 20.0, 3.0, 5.0, '2026-06-15 20:28:02', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `menus`
--

DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `id_menu` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `ordre` int NOT NULL DEFAULT '0',
  `route` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_menu`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `menus`
--

INSERT INTO `menus` (`id_menu`, `uuid`, `code`, `libelle`, `icon`, `parent_id`, `ordre`, `route`, `cree_le`, `modifie_le`) VALUES
(1, '7949fad0-6369-11f1-9d55-9c7bef735b1f', 'dashboard', 'Tableau de bord', 'dashboard', NULL, 1, 'dashboard.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(2, '7949ff93-6369-11f1-9d55-9c7bef735b1f', 'Eleves', 'Eleves', 'people', NULL, 2, 'etudiants.php', '2026-06-08 20:40:08', '2026-06-17 07:06:10'),
(3, '794a0193-6369-11f1-9d55-9c7bef735b1f', 'inscriptions', 'Inscriptions', 'how_to_reg', NULL, 3, 'inscriptions.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(4, '794a032d-6369-11f1-9d55-9c7bef735b1f', 'sections', 'Sections & Classes', 'layers', NULL, 4, 'sections.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(5, '794a04c1-6369-11f1-9d55-9c7bef735b1f', 'enseignants', 'Enseignants', 'school', NULL, 5, 'enseignants.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(6, '794a0648-6369-11f1-9d55-9c7bef735b1f', 'scolarite', 'Scolarit??', 'payments', NULL, 6, 'scolarite.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(7, '794a07d7-6369-11f1-9d55-9c7bef735b1f', 'scolarite_minerval', 'Minerval', NULL, 6, 1, 'minerval.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(8, '794a09e9-6369-11f1-9d55-9c7bef735b1f', 'scolarite_assurance', 'Assurance', NULL, 6, 2, 'assurance.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(9, '794a0bc4-6369-11f1-9d55-9c7bef735b1f', 'scolarite_toilettes', 'Toilettes', NULL, 6, 3, 'toilettes.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(10, '794a0d79-6369-11f1-9d55-9c7bef735b1f', 'produits', 'Produits', 'inventory', NULL, 7, 'produits.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(11, '794a0efa-6369-11f1-9d55-9c7bef735b1f', 'produits_uniformes', 'Uniformes', NULL, 10, 1, 'uniformes.php', '2026-06-08 20:40:08', '2026-06-09 22:09:18'),
(12, '794a10af-6369-11f1-9d55-9c7bef735b1f', 'produits_livres', 'Livres', NULL, 10, 2, 'livres.php', '2026-06-08 20:40:08', '2026-06-09 22:09:18'),
(13, '794a1279-6369-11f1-9d55-9c7bef735b1f', 'produits_materiel', 'Mat. scolaires', NULL, 10, 3, 'materiels.php', '2026-06-08 20:40:08', '2026-06-09 22:09:18'),
(14, '794a1432-6369-11f1-9d55-9c7bef735b1f', 'stock', 'Stock', 'warehouse', NULL, 8, 'stock.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(15, '794a15b9-6369-11f1-9d55-9c7bef735b1f', 'points', 'Notes & Points', 'grade', NULL, 9, 'points.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(16, '794a1749-6369-11f1-9d55-9c7bef735b1f', 'bulletins', 'Bulletins', 'description', NULL, 10, 'bulletins.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(17, '794a18db-6369-11f1-9d55-9c7bef735b1f', 'paiements', 'Paiements', 'account_balance', NULL, 11, 'paiements.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(18, '794a1a5c-6369-11f1-9d55-9c7bef735b1f', 'recus', 'Re??us', 'receipt', NULL, 12, 'recus.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(19, '794a1be8-6369-11f1-9d55-9c7bef735b1f', 'echeances', '??ch??anciers', 'calendar_month', NULL, 13, 'echeances.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(20, '794a1d5a-6369-11f1-9d55-9c7bef735b1f', 'rapports', 'Rapports', 'assessment', NULL, 14, 'rapports.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(21, '794a1ed8-6369-11f1-9d55-9c7bef735b1f', 'parametres', 'Param??tres', 'settings', NULL, 15, 'parametres.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(22, '794a2048-6369-11f1-9d55-9c7bef735b1f', 'utilisateurs', 'Utilisateurs', 'admin_panel_settings', NULL, 16, 'utilisateurs.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(23, '794a21c8-6369-11f1-9d55-9c7bef735b1f', 'audit', 'Journal d\'audit', 'history', NULL, 17, 'audit.php', '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(24, '6bec1849-636a-11f1-9d55-9c7bef735b1f', 'horaires', 'Horaires', 'calendar_view_week', NULL, 18, 'horaires.php', '2026-06-08 20:46:55', '2026-06-08 20:46:55'),
(25, '6becf21b-636a-11f1-9d55-9c7bef735b1f', 'horaires_creneaux', 'Cr??neaux', NULL, 24, 1, 'creneaux.php', '2026-06-08 20:46:55', '2026-06-08 20:46:55'),
(26, '6becf766-636a-11f1-9d55-9c7bef735b1f', 'horaires_volumes', 'Volumes horaires', NULL, 24, 2, 'volumes.php', '2026-06-08 20:46:55', '2026-06-08 20:46:55'),
(27, '6becf93e-636a-11f1-9d55-9c7bef735b1f', 'horaires_dispos', 'Disponibilit??s', NULL, 24, 3, 'disponibilites.php', '2026-06-08 20:46:55', '2026-06-08 20:46:55'),
(28, '6becfb8f-636a-11f1-9d55-9c7bef735b1f', 'horaires_generer', 'G??n??rer', NULL, 24, 4, 'generer.php', '2026-06-08 20:46:55', '2026-06-08 20:46:55'),
(29, '6becfd94-636a-11f1-9d55-9c7bef735b1f', 'horaires_consulter', 'Consulter', NULL, 24, 5, 'consulter.php', '2026-06-08 20:46:55', '2026-06-08 20:46:55'),
(35, '18e6df5c-638e-11f1-9d55-9c7bef735b1f', 'classes', 'Classes', NULL, NULL, 19, NULL, '2026-06-09 01:02:17', '2026-06-09 22:10:58'),
(36, '18e6e644-638e-11f1-9d55-9c7bef735b1f', 'periodes', 'Périodes', NULL, NULL, 20, NULL, '2026-06-09 01:02:17', '2026-06-09 22:10:58'),
(37, '18e6e76a-638e-11f1-9d55-9c7bef735b1f', 'annees_scolaires', 'Années scolaires', NULL, NULL, 21, NULL, '2026-06-09 01:02:17', '2026-06-09 22:10:58'),
(38, '18e6e868-638e-11f1-9d55-9c7bef735b1f', 'matieres', 'Matières', NULL, NULL, 22, NULL, '2026-06-09 01:02:17', '2026-06-09 22:10:58'),
(39, '18e6e934-638e-11f1-9d55-9c7bef735b1f', 'enseignements', 'Enseignements', NULL, NULL, 23, NULL, '2026-06-09 01:02:17', '2026-06-09 22:10:58'),
(40, '6f9a95ae-682d-11f1-9e11-9c7bef735b1f', 'mes_enfants', 'Mes enfants', 'family', NULL, 24, 'Parents/MesEnfants', '2026-06-14 22:12:58', '2026-06-14 22:12:58');

-- --------------------------------------------------------

--
-- Structure de la table `mouvements_stock`
--

DROP TABLE IF EXISTS `mouvements_stock`;
CREATE TABLE IF NOT EXISTS `mouvements_stock` (
  `id_mouvement` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_produit` int NOT NULL,
  `type` enum('entree','sortie') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantite` int NOT NULL,
  `prix_unitaire` decimal(12,2) DEFAULT NULL,
  `motif` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_mvt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_utilisateur` int DEFAULT NULL,
  `id_etudiant` int DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mouvement`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `idx_mouvements_produit` (`id_produit`),
  KEY `idx_mouvements_date` (`date_mvt`),
  KEY `idx_mvt_etudiant` (`id_etudiant`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `mouvements_stock`
--

INSERT INTO `mouvements_stock` (`id_mouvement`, `uuid`, `id_produit`, `type`, `quantite`, `prix_unitaire`, `motif`, `date_mvt`, `modifie_le`, `id_utilisateur`, `id_etudiant`, `cree_le`) VALUES
(1, 'c908b305-1eba-405f-a6fa-0ffcdd37b32c', 1, 'sortie', 23, 23.00, 'Ajustement manuel', '2026-06-24 14:51:39', '2026-06-24 14:51:39', 1, NULL, '2026-06-24 14:51:39'),
(2, '41257d52-4f71-4b14-ad20-e0cf8e82402c', 1, 'sortie', 1, 23.00, 'Vente à Mukendi Marie', '2026-06-24 16:27:26', '2026-06-24 16:27:26', 1, 2, '2026-06-24 16:27:26'),
(3, '0d814ee8-e7f0-44dc-b367-d4cc54326843', 1, 'sortie', 1, 23.00, 'Vente à Mukendi Marie', '2026-06-24 16:27:27', '2026-06-24 16:27:27', 1, 2, '2026-06-24 16:27:27'),
(4, '5717c519-e43a-4ada-8805-18f6356cc0b0', 1, 'sortie', 1, 23.00, 'Commande #2', '2026-06-29 12:00:24', '2026-06-29 12:00:24', 1, NULL, '2026-06-29 12:00:24');

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

DROP TABLE IF EXISTS `notes`;
CREATE TABLE IF NOT EXISTS `notes` (
  `id_note` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_evaluation` int NOT NULL,
  `id_etudiant` int NOT NULL,
  `note` decimal(5,1) NOT NULL,
  `appreciation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_note`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_note` (`id_evaluation`,`id_etudiant`),
  KEY `idx_notes_etudiant` (`id_etudiant`),
  KEY `idx_notes_id_evaluation` (`id_evaluation`),
  KEY `idx_notes_id_etudiant` (`id_etudiant`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notes`
--

INSERT INTO `notes` (`id_note`, `uuid`, `id_evaluation`, `id_etudiant`, `note`, `appreciation`, `cree_le`, `modifie_le`, `deleted_at`) VALUES
(1, '451081bd-403f-4efd-b077-7c73bf9cba89', 3, 1, 12.0, '32', '2026-06-18 15:42:17', '2026-06-18 15:42:17', NULL),
(2, 'e175e185-0964-460d-bff1-c5885094c009', 4, 1, 23.0, NULL, '2026-06-19 10:50:19', '2026-06-19 10:50:19', NULL),
(3, '7980a8b6-0dcc-438c-b7b2-6de38b027968', 5, 1, 12.0, NULL, '2026-06-19 10:50:19', '2026-06-19 10:50:19', NULL),
(4, 'd74d6331-8164-4a07-90b9-77e473a9cd9c', 6, 1, 16.0, NULL, '2026-06-19 10:50:19', '2026-06-19 10:50:19', NULL),
(5, 'a76c2a58-4bf7-4ff4-b756-c5cad88dbe61', 4, 2, 23.0, NULL, '2026-06-19 10:50:19', '2026-06-19 10:50:19', NULL),
(6, '20cb947b-2099-47bb-8b13-fcbb97ff856b', 5, 2, 23.0, NULL, '2026-06-19 10:50:19', '2026-06-19 10:50:19', NULL),
(7, 'e549757d-4af5-4995-8587-ecc9f4eebd1e', 6, 2, 43.0, NULL, '2026-06-19 10:50:19', '2026-06-19 10:50:19', NULL),
(8, '874eee4e-dbfe-410b-9f8c-f6496f3a3c61', 7, 1, 20.0, NULL, '2026-06-19 11:07:06', '2026-06-19 11:07:06', NULL),
(9, '46d0c400-76ab-423f-a1fb-758cbaa61871', 8, 1, 60.0, NULL, '2026-06-19 11:07:06', '2026-06-19 11:07:06', NULL),
(10, 'd7e1c613-7873-4a13-b734-3b1fc152a026', 7, 2, 20.0, NULL, '2026-06-19 11:07:06', '2026-06-19 11:07:06', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id_notification` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_utilisateur` int DEFAULT NULL,
  `titre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('info','succes','avertissement','erreur') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `lien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT '0',
  `date_lu` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notification`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `lu` (`lu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
CREATE TABLE IF NOT EXISTS `paiements` (
  `id_paiement` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_etudiant` int NOT NULL,
  `id_frais` int NOT NULL,
  `id_annee` int NOT NULL,
  `montant` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Montant vers?? dans ce paiement',
  `date_paiement` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mode_paiement` enum('especes','banque','mobile_money','cheque') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'especes',
  `reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'N?? de transaction bancaire/mobile',
  `id_utilisateur` int DEFAULT NULL,
  `statut` enum('partiel','solde','annule') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'partiel',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_paiement`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_frais` (`id_frais`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `idx_paiements_etudiant` (`id_etudiant`),
  KEY `idx_paiements_date` (`date_paiement`),
  KEY `idx_paiements_annee` (`id_annee`),
  KEY `idx_paiements_mode` (`mode_paiement`),
  KEY `idx_paiements_statut` (`statut`),
  KEY `idx_paiements_id_frais` (`id_frais`),
  KEY `idx_paiements_id_etudiant` (`id_etudiant`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id_paiement`, `uuid`, `id_etudiant`, `id_frais`, `id_annee`, `montant`, `date_paiement`, `mode_paiement`, `reference`, `id_utilisateur`, `statut`, `notes`, `cree_le`, `modifie_le`, `deleted_at`) VALUES
(4, '048ed206-a551-4fcc-8d0f-35e564bc58d9', 4, 4, 1, 65478.00, '2026-06-24 00:00:00', 'especes', '', 1, 'solde', '', '2026-06-24 16:53:31', '2026-06-24 16:53:31', NULL),
(5, '91033758-d4d3-4f84-94b9-775a4924a4b4', 6, 5, 1, 38983.00, '2026-06-24 00:00:00', 'banque', '', 1, 'solde', '', '2026-06-24 16:58:24', '2026-06-24 16:58:24', NULL),
(6, '22ce2fe9-37e0-48e3-8ca9-e4f9accd71e6', 1, 5, 1, 430943.00, '2026-06-24 00:00:00', 'especes', '', 1, 'solde', '', '2026-06-24 17:02:44', '2026-06-24 17:02:44', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `paiements_recus`
--

DROP TABLE IF EXISTS `paiements_recus`;
CREATE TABLE IF NOT EXISTS `paiements_recus` (
  `id_paiement_recu` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_recu` int NOT NULL,
  `id_paiement` int NOT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_paiement_recu`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_recu_paiement` (`id_recu`,`id_paiement`),
  KEY `id_paiement` (`id_paiement`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `paiements_recus`
--

INSERT INTO `paiements_recus` (`id_paiement_recu`, `uuid`, `id_recu`, `id_paiement`, `cree_le`) VALUES
(2, '246e6b1b-f834-445e-8c6e-394fda7a8ebc', 3, 4, '2026-06-24 16:53:31'),
(3, 'a7a2bd0f-54d0-40d2-9b2d-3918299d3675', 4, 5, '2026-06-24 16:58:24'),
(4, '1b129568-5239-4fc6-a4f6-08092bb1be46', 5, 6, '2026-06-24 17:02:44');

-- --------------------------------------------------------

--
-- Structure de la table `parametres`
--

DROP TABLE IF EXISTS `parametres`;
CREATE TABLE IF NOT EXISTS `parametres` (
  `id_parametre` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `clef` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_parametre`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `clef` (`clef`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `parametres`
--

INSERT INTO `parametres` (`id_parametre`, `uuid`, `clef`, `valeur`, `deleted_at`, `cree_le`, `modifie_le`) VALUES
(1, '7956029e-6369-11f1-9d55-9c7bef735b1f', 'nom_ecole', 'FUTURE VIP SCHOOL', NULL, '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(2, '79560766-6369-11f1-9d55-9c7bef735b1f', 'adresse_ecole', '', NULL, '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(3, '795609cd-6369-11f1-9d55-9c7bef735b1f', 'telephone_ecole', '', NULL, '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(4, '79560b2e-6369-11f1-9d55-9c7bef735b1f', 'email_ecole', 'admin@vip-school.com', NULL, '2026-06-08 20:40:08', '2026-06-19 10:08:23'),
(5, '79560cbf-6369-11f1-9d55-9c7bef735b1f', 'logo_ecole', 'assets/uploads/logo/4ec78211cd49cc56c8d9bd9b5f82e66b.jpeg', NULL, '2026-06-08 20:40:08', '2026-06-16 14:17:40'),
(8, '79561787-6369-11f1-9d55-9c7bef735b1f', 'prochain_num_recu', '1', NULL, '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(9, '79561b46-6369-11f1-9d55-9c7bef735b1f', 'annee_active', '1', NULL, '2026-06-08 20:40:08', '2026-06-19 09:51:35'),
(10, '4c171c97-a8ed-46f9-8ed9-f602791a09ef', 'favicon_ecole', 'assets/uploads/logo/favicon_b09bd5153d626a3e38114583e962780f.png', NULL, '2026-06-16 14:18:02', '2026-06-16 14:18:02'),
(11, 'ce8f5dc5-6b24-11f1-8bfe-9c7bef735b1f', 'seuil_excellent', '18', NULL, '2026-06-18 16:48:45', '2026-06-18 16:48:45'),
(12, 'ce8f6a8b-6b24-11f1-8bfe-9c7bef735b1f', 'seuil_tres_bien', '16', NULL, '2026-06-18 16:48:45', '2026-06-18 16:48:45'),
(13, 'ce8f6dfa-6b24-11f1-8bfe-9c7bef735b1f', 'seuil_bien', '14', NULL, '2026-06-18 16:48:45', '2026-06-18 16:48:45'),
(14, 'ce8f706b-6b24-11f1-8bfe-9c7bef735b1f', 'seuil_assez_bien', '12', NULL, '2026-06-18 16:48:45', '2026-06-18 16:48:45'),
(15, 'ce8f721f-6b24-11f1-8bfe-9c7bef735b1f', 'seuil_passable', '10', NULL, '2026-06-18 16:48:45', '2026-06-18 16:48:45'),
(16, 'ce8f7406-6b24-11f1-8bfe-9c7bef735b1f', 'appreciation_excellent', 'Excellent', NULL, '2026-06-18 16:48:45', '2026-06-18 16:48:45'),
(17, 'ce94719c-6b24-11f1-8bfe-9c7bef735b1f', 'appreciation_tres_bien', 'Très Bien', NULL, '2026-06-18 16:48:45', '2026-06-18 16:48:45'),
(18, 'ce94741f-6b24-11f1-8bfe-9c7bef735b1f', 'appreciation_bien', 'Bien', NULL, '2026-06-18 16:48:45', '2026-06-18 16:48:45'),
(19, 'ce9475e2-6b24-11f1-8bfe-9c7bef735b1f', 'appreciation_assez_bien', 'Assez Bien', NULL, '2026-06-18 16:48:45', '2026-06-18 16:48:45'),
(20, 'ce9477c8-6b24-11f1-8bfe-9c7bef735b1f', 'appreciation_passable', 'Passable', NULL, '2026-06-18 16:48:45', '2026-06-18 16:48:45'),
(21, 'ce947a84-6b24-11f1-8bfe-9c7bef735b1f', 'appreciation_insuffisant', 'Insuffisant', NULL, '2026-06-18 16:48:45', '2026-06-18 16:48:45'),
(22, 'ce947cc8-6b24-11f1-8bfe-9c7bef735b1f', 'regle_admis', 'moyenne>=12', NULL, '2026-06-18 16:48:45', '2026-06-18 16:48:45'),
(23, 'ce947f59-6b24-11f1-8bfe-9c7bef735b1f', 'regle_ajourne', 'moyenne>=10 AND moyenne<12', NULL, '2026-06-18 16:48:45', '2026-06-18 16:48:45'),
(24, 'ce9480f5-6b24-11f1-8bfe-9c7bef735b1f', 'regle_echoue', 'moyenne<10', NULL, '2026-06-18 16:48:45', '2026-06-18 16:48:45'),
(25, '07c49e0b-9864-424a-b7bc-b9ce3bda4647', 'devise', '', NULL, '2026-06-19 09:51:35', '2026-06-19 09:51:35'),
(26, '18b83392-2c74-454b-a863-cfec752abe1c', 'tva', '0', NULL, '2026-06-19 09:51:35', '2026-06-19 09:51:35'),
(27, 'c1bd459d-5ca7-4f25-8ac3-af468ae48a5a', 'periode_active', '3', NULL, '2026-06-19 09:51:35', '2026-06-19 10:00:09'),
(28, 'c31c5955-36b9-41ef-9929-ce32260931c8', 'email_protocol', 'mail', NULL, '2026-06-19 09:51:35', '2026-06-19 09:51:35'),
(29, '8026b261-459e-469d-9102-11b6c8e4d2d6', 'email_smtp_host', '', NULL, '2026-06-19 09:51:35', '2026-06-19 09:51:35'),
(30, '1008e00a-3d50-4727-b07f-7f87623c849f', 'email_smtp_user', 'admin@vip-school.com', NULL, '2026-06-19 09:51:35', '2026-06-19 09:51:35'),
(31, '463a6168-4937-4d60-a274-320798faae73', 'email_smtp_pass', 'admin123', NULL, '2026-06-19 09:51:35', '2026-06-19 09:51:35'),
(32, '1ff4901f-7494-4ca2-b29d-6baa7fd9198b', 'email_smtp_port', '587', NULL, '2026-06-19 09:51:35', '2026-06-19 09:51:35'),
(33, '2eaecedc-22cf-4cf8-9aef-099bd7917ade', 'email_smtp_crypto', 'tls', NULL, '2026-06-19 09:51:35', '2026-06-19 09:51:35'),
(34, 'c12c095d-d5a1-4887-b0ba-4612fc17c444', 'email_sendmail_path', '', NULL, '2026-06-19 09:51:35', '2026-06-19 09:51:35');

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id_reset` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_utilisateur` int NOT NULL,
  `code` varchar(10) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `utilise` tinyint(1) NOT NULL DEFAULT '0',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_reset`),
  KEY `fk_password_resets_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `periodes`
--

DROP TABLE IF EXISTS `periodes`;
CREATE TABLE IF NOT EXISTS `periodes` (
  `id_periode` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_annee` int NOT NULL,
  `libelle` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `est_en_cours` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_periode`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_periode_annee` (`libelle`,`id_annee`),
  KEY `id_annee` (`id_annee`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `periodes`
--

INSERT INTO `periodes` (`id_periode`, `uuid`, `id_annee`, `libelle`, `date_debut`, `date_fin`, `est_en_cours`, `deleted_at`, `cree_le`, `modifie_le`) VALUES
(1, '3462e3ee-2069-4ddd-b819-89871773c8d2', 1, '1ERE TRIMESTRE', '2025-09-09', '2025-12-25', 0, NULL, '2026-06-09 01:14:51', '2026-06-19 10:05:38'),
(2, '7e29b0b7-d5e8-4f7e-9264-1fd7d9baea4b', 1, '2EME TRIMESTRE', '2026-01-01', '2026-03-27', 0, NULL, '2026-06-09 01:16:06', '2026-06-09 01:16:06'),
(3, 'bc828fb9-c30f-4fff-960b-4406ad7c4dec', 1, '3EME TRIMESTRE', '2026-03-27', '2026-07-12', 1, NULL, '2026-06-09 01:16:46', '2026-06-19 10:05:38');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

DROP TABLE IF EXISTS `produits`;
CREATE TABLE IF NOT EXISTS `produits` (
  `id_produit` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_categorie` int NOT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `taille` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `editeur` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `annee_edition` year DEFAULT NULL,
  `id_matiere` int DEFAULT NULL,
  `id_classe` int DEFAULT NULL,
  `prix_unitaire` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stock_mini` int NOT NULL DEFAULT '0',
  `stock_actuel` int NOT NULL DEFAULT '0',
  `unite` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pi??ce',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_produit`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_produits_categorie` (`id_categorie`),
  KEY `idx_matiere` (`id_matiere`),
  KEY `idx_classe` (`id_classe`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id_produit`, `uuid`, `id_categorie`, `code`, `libelle`, `description`, `taille`, `editeur`, `annee_edition`, `id_matiere`, `id_classe`, `prix_unitaire`, `stock_mini`, `stock_actuel`, `unite`, `cree_le`, `modifie_le`, `deleted_at`) VALUES
(1, '59625ff8-a2f2-4874-9769-e5c407b73b9f', 3, 'siue', 'ddsilfjiow', NULL, NULL, NULL, NULL, NULL, NULL, 23.00, 5, 20, 'pi??ce', '2026-06-18 15:18:19', '2026-06-29 12:00:24', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `recus`
--

DROP TABLE IF EXISTS `recus`;
CREATE TABLE IF NOT EXISTS `recus` (
  `id_recu` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_recu` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_etudiant` int NOT NULL,
  `id_annee` int NOT NULL,
  `date_edition` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `montant_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_utilisateur` int DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_recu`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `numero_recu` (`numero_recu`),
  KEY `id_etudiant` (`id_etudiant`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `idx_recus_annee` (`id_annee`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `recus`
--

INSERT INTO `recus` (`id_recu`, `uuid`, `numero_recu`, `id_etudiant`, `id_annee`, `date_edition`, `montant_total`, `modifie_le`, `id_utilisateur`, `cree_le`, `deleted_at`) VALUES
(1, '46908d4d-cb75-4245-9dde-3080c8a4fdf8', 'JDSK', 2, 1, '2026-06-18 18:25:30', 23.00, '2026-06-18 18:25:39', NULL, '2026-06-18 18:25:30', '2026-06-18 16:25:39'),
(2, '7fd1cba1-66fc-49fb-a476-2233521fc32a', 'RECU-20260624-0001', 2, 1, '2026-06-24 14:47:37', 10000.00, '2026-06-24 16:47:37', 1, '2026-06-24 16:47:37', NULL),
(3, 'd1169923-c240-42b3-96c1-23de8e6cca8a', 'RECU-20260624-0002', 4, 1, '2026-06-24 14:53:31', 65478.00, '2026-06-24 16:53:31', 1, '2026-06-24 16:53:31', NULL),
(4, '0667d7b1-0f2b-4d14-a1ea-132a7f8564d6', 'RECU-20260624-0003', 6, 1, '2026-06-24 14:58:24', 38983.00, '2026-06-24 16:58:24', 1, '2026-06-24 16:58:24', NULL),
(5, 'cc0a0f11-4702-4791-b5cc-a095123f47f5', 'RECU-20260624-0004', 1, 1, '2026-06-24 15:02:44', 430943.00, '2026-06-24 17:02:44', 1, '2026-06-24 17:02:44', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id_role` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hierarchie` int NOT NULL DEFAULT '0',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id_role`, `uuid`, `code`, `libelle`, `hierarchie`, `cree_le`, `modifie_le`, `deleted_at`) VALUES
(1, '7948b1f8-6369-11f1-9d55-9c7bef735b1f', 'admin', 'Administrateur', 10, '2026-06-08 20:40:08', '2026-06-08 20:40:08', NULL),
(2, '7948b668-6369-11f1-9d55-9c7bef735b1f', 'direction', 'Direction', 8, '2026-06-08 20:40:08', '2026-06-08 20:40:08', NULL),
(3, '7948b9df-6369-11f1-9d55-9c7bef735b1f', 'comptable', 'Comptable', 5, '2026-06-08 20:40:08', '2026-06-08 20:40:08', NULL),
(4, '7948bb51-6369-11f1-9d55-9c7bef735b1f', 'secretaire', 'Secretaire', 3, '2026-06-08 20:40:08', '2026-06-08 23:44:59', NULL),
(5, '7948bc8d-6369-11f1-9d55-9c7bef735b1f', 'lecture', 'Lecture seule', 1, '2026-06-08 20:40:08', '2026-06-08 20:40:08', NULL),
(6, '65582d92-682c-11f1-9e11-9c7bef735b1f', 'enseignant', 'Enseignant', 4, '2026-06-14 22:05:30', '2026-06-14 22:05:30', NULL),
(7, '65586e62-682c-11f1-9e11-9c7bef735b1f', 'eleve', 'Eleve', 2, '2026-06-14 22:05:30', '2026-06-14 22:05:30', NULL),
(8, '65587180-682c-11f1-9e11-9c7bef735b1f', 'parent', 'Parent', 1, '2026-06-14 22:05:30', '2026-06-29 12:05:23', '2026-06-29 10:05:23');

-- --------------------------------------------------------

--
-- Structure de la table `roles_menus`
--

DROP TABLE IF EXISTS `roles_menus`;
CREATE TABLE IF NOT EXISTS `roles_menus` (
  `id_role_menu` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_role` int NOT NULL,
  `id_menu` int NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT '0',
  `can_add` tinyint(1) NOT NULL DEFAULT '0',
  `can_edit` tinyint(1) NOT NULL DEFAULT '0',
  `can_delete` tinyint(1) NOT NULL DEFAULT '0',
  `can_export` tinyint(1) NOT NULL DEFAULT '0',
  `can_imprimer` tinyint(1) NOT NULL DEFAULT '0',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_role_menu`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_role_menu` (`id_role`,`id_menu`),
  KEY `id_menu` (`id_menu`)
) ENGINE=InnoDB AUTO_INCREMENT=230 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles_menus`
--

INSERT INTO `roles_menus` (`id_role_menu`, `uuid`, `id_role`, `id_menu`, `can_view`, `can_add`, `can_edit`, `can_delete`, `can_export`, `can_imprimer`, `cree_le`) VALUES
(32, '794d6189-6369-11f1-9d55-9c7bef735b1f', 2, 1, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(33, '794d6a83-6369-11f1-9d55-9c7bef735b1f', 2, 2, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(34, '794d6dcb-6369-11f1-9d55-9c7bef735b1f', 2, 3, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(35, '794d700b-6369-11f1-9d55-9c7bef735b1f', 2, 4, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(36, '794d720f-6369-11f1-9d55-9c7bef735b1f', 2, 5, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(37, '794d7559-6369-11f1-9d55-9c7bef735b1f', 2, 6, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(38, '794d777e-6369-11f1-9d55-9c7bef735b1f', 2, 10, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(39, '794d7ab6-6369-11f1-9d55-9c7bef735b1f', 2, 14, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(40, '794d7dad-6369-11f1-9d55-9c7bef735b1f', 2, 15, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(41, '794d8348-6369-11f1-9d55-9c7bef735b1f', 2, 16, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(42, '794d8783-6369-11f1-9d55-9c7bef735b1f', 2, 17, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(43, '794d8ec0-6369-11f1-9d55-9c7bef735b1f', 2, 18, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(44, '794d9428-6369-11f1-9d55-9c7bef735b1f', 2, 19, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(45, '794d9ade-6369-11f1-9d55-9c7bef735b1f', 2, 20, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(46, '794da008-6369-11f1-9d55-9c7bef735b1f', 2, 21, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(47, '794da49d-6369-11f1-9d55-9c7bef735b1f', 2, 22, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(48, '794da712-6369-11f1-9d55-9c7bef735b1f', 2, 23, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(49, '794da93a-6369-11f1-9d55-9c7bef735b1f', 2, 7, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(50, '794dab41-6369-11f1-9d55-9c7bef735b1f', 2, 8, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(51, '794dad2f-6369-11f1-9d55-9c7bef735b1f', 2, 9, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(52, '794daf1e-6369-11f1-9d55-9c7bef735b1f', 2, 11, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(53, '794db10d-6369-11f1-9d55-9c7bef735b1f', 2, 12, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(54, '794db300-6369-11f1-9d55-9c7bef735b1f', 2, 13, 1, 0, 0, 0, 1, 1, '2026-06-08 20:40:08'),
(63, '794ef43c-6369-11f1-9d55-9c7bef735b1f', 3, 1, 1, 1, 1, 0, 1, 1, '2026-06-08 20:40:08'),
(64, '794ef9f7-6369-11f1-9d55-9c7bef735b1f', 3, 19, 1, 1, 1, 0, 1, 1, '2026-06-08 20:40:08'),
(65, '794efccb-6369-11f1-9d55-9c7bef735b1f', 3, 17, 1, 1, 1, 0, 1, 1, '2026-06-08 20:40:08'),
(66, '794eff2e-6369-11f1-9d55-9c7bef735b1f', 3, 10, 1, 1, 1, 0, 1, 1, '2026-06-08 20:40:08'),
(67, '794f01b9-6369-11f1-9d55-9c7bef735b1f', 3, 12, 1, 1, 1, 0, 1, 1, '2026-06-08 20:40:08'),
(68, '794f041a-6369-11f1-9d55-9c7bef735b1f', 3, 13, 1, 1, 1, 0, 1, 1, '2026-06-08 20:40:08'),
(69, '794f0689-6369-11f1-9d55-9c7bef735b1f', 3, 11, 1, 1, 1, 0, 1, 1, '2026-06-08 20:40:08'),
(70, '794f08c0-6369-11f1-9d55-9c7bef735b1f', 3, 20, 1, 1, 1, 0, 1, 1, '2026-06-08 20:40:08'),
(71, '794f0b0c-6369-11f1-9d55-9c7bef735b1f', 3, 18, 1, 1, 1, 0, 1, 1, '2026-06-08 20:40:08'),
(72, '794f0d4f-6369-11f1-9d55-9c7bef735b1f', 3, 6, 1, 1, 1, 0, 1, 1, '2026-06-08 20:40:08'),
(73, '794f0faf-6369-11f1-9d55-9c7bef735b1f', 3, 8, 1, 1, 1, 0, 1, 1, '2026-06-08 20:40:08'),
(74, '794f135a-6369-11f1-9d55-9c7bef735b1f', 3, 7, 1, 1, 1, 0, 1, 1, '2026-06-08 20:40:08'),
(75, '794f15d5-6369-11f1-9d55-9c7bef735b1f', 3, 9, 1, 1, 1, 0, 1, 1, '2026-06-08 20:40:08'),
(76, '794f1811-6369-11f1-9d55-9c7bef735b1f', 3, 14, 1, 1, 1, 0, 1, 1, '2026-06-08 20:40:08'),
(100, '6bef39dd-636a-11f1-9d55-9c7bef735b1f', 2, 24, 1, 0, 0, 0, 1, 1, '2026-06-08 20:46:55'),
(101, '6bef3e61-636a-11f1-9d55-9c7bef735b1f', 2, 29, 1, 0, 0, 0, 1, 1, '2026-06-08 20:46:55'),
(102, '6bef4116-636a-11f1-9d55-9c7bef735b1f', 2, 25, 1, 0, 0, 0, 1, 1, '2026-06-08 20:46:55'),
(103, '6bef44f7-636a-11f1-9d55-9c7bef735b1f', 2, 27, 1, 0, 0, 0, 1, 1, '2026-06-08 20:46:55'),
(104, '6bef473b-636a-11f1-9d55-9c7bef735b1f', 2, 28, 1, 0, 0, 0, 1, 1, '2026-06-08 20:46:55'),
(105, '6bef4953-636a-11f1-9d55-9c7bef735b1f', 2, 26, 1, 0, 0, 0, 1, 1, '2026-06-08 20:46:55'),
(114, 'ffd035c6-319f-4941-9297-3bb7247c419b', 1, 1, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(115, '3df919c9-aff4-47bd-8b69-cf13f0829745', 1, 2, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(116, 'a27bad6f-7f7b-4d20-964a-9166f656c48b', 1, 3, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(117, '31a88d4a-1c75-4c00-82b6-846c7a519c7f', 1, 4, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(118, '854b3a38-475c-48d3-af6f-b435aebc3e65', 1, 5, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(119, '0dfb46b2-3bb0-4c5f-b022-f056bc15599f', 1, 6, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(120, '9b9fcb23-d610-4ea0-b0e8-41495681c4ca', 1, 7, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(121, '4bbe93db-612a-40f8-ba69-af38ded4aa81', 1, 8, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(122, 'c5c2c0c9-071e-415b-8e82-fcb116250855', 1, 9, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(123, 'c88aee37-ddb5-4c98-8253-66011d54ac60', 1, 10, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(124, 'e54e145a-d2ad-46a5-967f-740026c52f7b', 1, 14, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(125, '0300b42d-3488-4743-83fe-5a48e3ad41ba', 1, 15, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(126, '79e18a4a-4808-4423-8d93-df3aa3776811', 1, 16, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(127, '36505a61-b609-43de-bc5e-1ef20d15da83', 1, 17, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(128, '43fc56e0-9f22-482b-b4b9-a64286c1f00e', 1, 18, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(129, '946b87b6-809d-4cb9-a31f-dbe8d9e03b83', 1, 19, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(130, 'f5de4026-bbd9-420b-ad7b-4cb9f11f73f0', 1, 20, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(131, '3f61c340-fb83-4585-a166-34ea6963ffec', 1, 21, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(132, '9406cdfc-227a-42f5-9d28-5d11a4213856', 1, 22, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(133, '1a0399f8-16f5-4873-8d9c-8ef7020e7896', 1, 23, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(134, 'a5b74411-a62c-4990-992e-3b0ce7e73b60', 1, 24, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(135, '5316e0dc-a89e-419f-a071-bb7f8c7735be', 1, 25, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(136, 'dbaf9ab2-958d-48be-829d-bdc83b7134c7', 1, 26, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(137, '95f77cb1-4549-47bf-97a5-bf532a9c3fed', 1, 27, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(138, '886c5e3a-2e4c-43e2-be68-fa43c3198c7a', 1, 28, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(139, '8db4ae6c-fb5f-4c56-9038-11b35a60ee33', 1, 29, 1, 1, 1, 1, 1, 1, '2026-06-08 23:43:48'),
(140, '835fee0d-abee-4cdf-9916-1c61718fcbe6', 4, 1, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(141, 'dd0d04f2-b37f-450e-a43c-1867201cf953', 4, 2, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(142, '66279e0e-c906-4f54-80fa-8a674af8a241', 4, 3, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(143, '14904535-0c55-4de5-9253-19ba14826e66', 4, 4, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(144, '94ad9bbf-293c-420c-8f3f-4e8c638685d5', 4, 5, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(145, 'dd270777-f043-40ff-82a9-65c5286a4cff', 4, 6, 1, 1, 0, 0, 0, 0, '2026-06-08 23:44:17'),
(146, '989440a4-f042-4d0a-9dfe-d003126e4185', 4, 7, 0, 0, 0, 0, 0, 0, '2026-06-08 23:44:17'),
(147, 'eb91dab7-84ae-4abd-9f73-c100395dc963', 4, 8, 0, 0, 0, 0, 0, 0, '2026-06-08 23:44:17'),
(148, '09f01565-6742-45ad-a122-fabc04aec9e2', 4, 9, 0, 0, 0, 0, 0, 0, '2026-06-08 23:44:17'),
(149, '6a25d6a7-4a6e-4123-ad0b-8f642a8bc917', 4, 10, 0, 0, 0, 0, 0, 0, '2026-06-08 23:44:17'),
(150, '6893b302-1ac3-469b-9379-659105aacf2d', 4, 14, 0, 0, 0, 0, 0, 0, '2026-06-08 23:44:17'),
(151, '417ea998-371d-4c2c-bd40-ffc6a1767f88', 4, 15, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(152, '648bf17d-7b07-4ac4-b93f-f6be4956931c', 4, 16, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(153, 'e6500a90-1157-400f-a66b-1c492a0d6663', 4, 17, 0, 0, 0, 0, 0, 0, '2026-06-08 23:44:17'),
(154, 'a690fee4-529d-4a52-a148-2c2bb5c1d563', 4, 18, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(155, 'a75a1108-5c6a-4ec5-88fd-259a8649473c', 4, 19, 0, 0, 0, 0, 0, 0, '2026-06-08 23:44:17'),
(156, '314b2ef0-c889-41c1-a266-82a6a537ff64', 4, 20, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(157, '8c775cee-b17f-4ed3-ae86-a03aee692a8a', 4, 21, 0, 0, 0, 0, 0, 0, '2026-06-08 23:44:17'),
(158, '5912d372-9d57-4356-adb4-9b931e637bde', 4, 22, 0, 0, 0, 0, 0, 0, '2026-06-08 23:44:17'),
(159, 'e00e6075-a948-40fe-af66-f24ee9d18af8', 4, 23, 0, 0, 0, 0, 0, 0, '2026-06-08 23:44:17'),
(160, 'b28ded31-9b33-40f9-be8f-08f420bc23e7', 4, 24, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(161, '48552413-0ac2-4592-814a-714308f8d7f5', 4, 25, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(162, '89b0aaef-f3f3-4baf-a7de-38961d9c7d22', 4, 26, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(163, 'e63862fa-3380-4496-a4c9-4bb966c493dc', 4, 27, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(164, '3ed125a1-8d8a-4c1c-b0f0-2010573fe816', 4, 28, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(165, '939ce9da-d1bc-44b4-b317-39c74dd94f26', 4, 29, 1, 1, 1, 0, 1, 1, '2026-06-08 23:44:17'),
(166, '3c576799-638e-11f1-9d55-9c7bef735b1f', 1, 35, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:17'),
(167, '3c5782c8-638e-11f1-9d55-9c7bef735b1f', 1, 36, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:17'),
(168, '3c5787fe-638e-11f1-9d55-9c7bef735b1f', 1, 37, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:17'),
(169, '3c578d88-638e-11f1-9d55-9c7bef735b1f', 1, 38, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:17'),
(170, '3c57949f-638e-11f1-9d55-9c7bef735b1f', 1, 39, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:17'),
(171, '3ec701ac-638e-11f1-9d55-9c7bef735b1f', 4, 35, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(172, '3ec709dd-638e-11f1-9d55-9c7bef735b1f', 2, 35, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(173, '3ec70c61-638e-11f1-9d55-9c7bef735b1f', 3, 35, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(174, '3ec70f63-638e-11f1-9d55-9c7bef735b1f', 4, 36, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(175, '3ec711c7-638e-11f1-9d55-9c7bef735b1f', 2, 36, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(176, '3ec713cd-638e-11f1-9d55-9c7bef735b1f', 3, 36, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(177, '3ec715f2-638e-11f1-9d55-9c7bef735b1f', 4, 37, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(178, '3ec71830-638e-11f1-9d55-9c7bef735b1f', 2, 37, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(179, '3ec71a15-638e-11f1-9d55-9c7bef735b1f', 3, 37, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(180, '3ec71d33-638e-11f1-9d55-9c7bef735b1f', 4, 38, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(181, '3ec720b9-638e-11f1-9d55-9c7bef735b1f', 2, 38, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(182, '3ec72323-638e-11f1-9d55-9c7bef735b1f', 3, 38, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(183, '3ec72618-638e-11f1-9d55-9c7bef735b1f', 4, 39, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(184, '3ec728ac-638e-11f1-9d55-9c7bef735b1f', 2, 39, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(185, '3ec72b66-638e-11f1-9d55-9c7bef735b1f', 3, 39, 1, 1, 1, 1, 0, 0, '2026-06-09 01:03:21'),
(186, '9fac4e09-d038-404c-a94e-56251245233e', 1, 11, 1, 1, 1, 1, 1, 1, '2026-06-09 22:09:18'),
(187, '2ddc02c1-67fa-46df-a82e-8112d1782a17', 1, 12, 1, 1, 1, 1, 1, 1, '2026-06-09 22:09:18'),
(188, '163176cd-b6d6-44c6-b784-90ff093c4dc8', 1, 13, 1, 1, 1, 1, 1, 1, '2026-06-09 22:09:18'),
(189, '3828acc2-c8fb-4428-965f-f5ccf5a4932e', 5, 1, 1, 0, 0, 0, 0, 0, '2026-06-09 22:09:18'),
(190, 'ec579135-a62b-462d-8b71-bfe563ca4354', 5, 2, 1, 0, 0, 0, 0, 0, '2026-06-09 22:09:18'),
(191, 'be948634-54c7-4431-9ce1-daef58778721', 5, 3, 1, 0, 0, 0, 0, 0, '2026-06-09 22:09:18'),
(192, 'a856bc8c-e9c8-427a-a81b-90225dc5aece', 5, 4, 1, 0, 0, 0, 0, 0, '2026-06-09 22:09:18'),
(193, '6c13b88d-fb9a-4237-858a-1a0e3d6a9e85', 5, 5, 1, 0, 0, 0, 0, 0, '2026-06-09 22:09:18'),
(194, '80170e7f-a3ac-4dc6-92f7-996cb4c5e08c', 5, 15, 1, 0, 0, 0, 0, 0, '2026-06-09 22:09:18'),
(195, '2b4ab0db-e8be-46a3-ab99-ba7e9e9c7ebc', 5, 16, 1, 0, 0, 0, 0, 0, '2026-06-09 22:09:18'),
(196, 'd6e5cfbc-5c0f-4b9a-9627-f39d91e256f8', 5, 17, 1, 0, 0, 0, 0, 0, '2026-06-09 22:09:18'),
(197, 'a9dc4945-d34e-41cb-90a8-cf745eccc00b', 5, 21, 1, 0, 0, 0, 0, 0, '2026-06-09 22:09:18'),
(198, '71805375-fdb6-4b32-aabf-6d1e7a3b71f9', 5, 22, 1, 0, 0, 0, 0, 0, '2026-06-09 22:09:18'),
(199, '565717b2-682d-11f1-9e11-9c7bef735b1f', 6, 16, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(200, '56594469-682d-11f1-9e11-9c7bef735b1f', 6, 35, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(201, '5659503e-682d-11f1-9e11-9c7bef735b1f', 6, 1, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(202, '56595b4f-682d-11f1-9e11-9c7bef735b1f', 6, 5, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(203, '56596669-682d-11f1-9e11-9c7bef735b1f', 6, 39, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(204, '56597231-682d-11f1-9e11-9c7bef735b1f', 6, 2, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(205, '56597a97-682d-11f1-9e11-9c7bef735b1f', 6, 24, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(206, '56598590-682d-11f1-9e11-9c7bef735b1f', 6, 29, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(207, '56598de9-682d-11f1-9e11-9c7bef735b1f', 6, 3, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(208, '56599808-682d-11f1-9e11-9c7bef735b1f', 6, 38, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(209, '5659a004-682d-11f1-9e11-9c7bef735b1f', 6, 4, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(214, '565bc370-682d-11f1-9e11-9c7bef735b1f', 6, 15, 1, 1, 1, 0, 0, 0, '2026-06-14 22:12:15'),
(215, '565cfcf7-682d-11f1-9e11-9c7bef735b1f', 7, 16, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(216, '565d114a-682d-11f1-9e11-9c7bef735b1f', 7, 1, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(217, '565d197e-682d-11f1-9e11-9c7bef735b1f', 7, 17, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(218, '565d1e7e-682d-11f1-9e11-9c7bef735b1f', 7, 18, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(222, '565e9078-682d-11f1-9e11-9c7bef735b1f', 8, 16, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(223, '565ea3d7-682d-11f1-9e11-9c7bef735b1f', 8, 1, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(224, '565eac33-682d-11f1-9e11-9c7bef735b1f', 8, 17, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(225, '565eb3aa-682d-11f1-9e11-9c7bef735b1f', 8, 18, 1, 0, 0, 0, 0, 0, '2026-06-14 22:12:15'),
(229, '7416a7f4-682d-11f1-9e11-9c7bef735b1f', 8, 40, 1, 0, 0, 0, 0, 0, '2026-06-14 22:13:05');

-- --------------------------------------------------------

--
-- Structure de la table `sections`
--

DROP TABLE IF EXISTS `sections`;
CREATE TABLE IF NOT EXISTS `sections` (
  `id_section` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_section`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sections`
--

INSERT INTO `sections` (`id_section`, `uuid`, `code`, `libelle`, `actif`, `deleted_at`, `cree_le`, `modifie_le`) VALUES
(1, '6b0d10b0-c337-4acf-80ed-52119b3ac260', 'ECONOMIQUE', 'ECO', 1, NULL, '2026-06-09 01:17:28', '2026-06-09 01:17:28'),
(2, '0440a63a-8fac-4707-ba27-df78355ab4dd', 'PEDAGOGIQUE', 'PEDA', 1, NULL, '2026-06-09 01:17:50', '2026-06-09 01:17:50');

-- --------------------------------------------------------

--
-- Structure de la table `types_frais`
--

DROP TABLE IF EXISTS `types_frais`;
CREATE TABLE IF NOT EXISTS `types_frais` (
  `id_type_frais` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_type_frais`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `types_frais`
--

INSERT INTO `types_frais` (`id_type_frais`, `uuid`, `code`, `libelle`, `description`, `deleted_at`, `cree_le`, `modifie_le`) VALUES
(1, '7952efa0-6369-11f1-9d55-9c7bef735b1f', 'MINERVAL', 'Minerval / Frais de scolarité', '', NULL, '2026-06-08 20:40:08', '2026-06-24 14:51:03'),
(2, '7952f58e-6369-11f1-9d55-9c7bef735b1f', 'ASSURANCE', 'Assurance scolaire', NULL, NULL, '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(3, '7952f933-6369-11f1-9d55-9c7bef735b1f', 'UNIFORME', 'Uniformes scolaires', NULL, NULL, '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(4, '7952fbde-6369-11f1-9d55-9c7bef735b1f', 'LIVRE', 'Manuels et livres scolaires', NULL, NULL, '2026-06-08 20:40:08', '2026-06-08 20:40:08'),
(5, '7952fe28-6369-11f1-9d55-9c7bef735b1f', 'MATERIEL', 'Materiels scolaires', '', NULL, '2026-06-08 20:40:08', '2026-06-09 00:43:04'),
(6, '7953006c-6369-11f1-9d55-9c7bef735b1f', 'TOILETTES', 'Materiels de toilettes', '', NULL, '2026-06-08 20:40:08', '2026-06-24 14:50:45');

-- --------------------------------------------------------

--
-- Structure de la table `uniformes`
--

DROP TABLE IF EXISTS `uniformes`;
CREATE TABLE IF NOT EXISTS `uniformes` (
  `id_uniforme` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `taille` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_actuel` int NOT NULL DEFAULT '0',
  `stock_minimum` int NOT NULL DEFAULT '5',
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_uniforme`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_role` int NOT NULL,
  `nom_complet` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mot_de_passe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `email` (`email`),
  KEY `id_role` (`id_role`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `uuid`, `id_role`, `nom_complet`, `email`, `photo`, `telephone`, `mot_de_passe`, `actif`, `cree_le`, `modifie_le`, `deleted_at`) VALUES
(1, '795185c3-6369-11f1-9d55-9c7bef735b1f', 1, 'Administrateur', 'admin@vip-school.com', NULL, NULL, '$2y$10$WYhZEBBp3mQBwVB0csJhIuiYzX15/lPRIiN7uR286PQUAJMSKVIaC', 1, '2026-06-08 20:40:08', '2026-06-09 21:11:44', NULL),
(2, 'dfdd5a4e-58ca-4b6e-b003-7d4d50a3cbae', 7, 'Administateur Ilunga Esther', 'paul@gmail.com', NULL, NULL, '$2y$10$XVG0U4t5jjKv3rzEpQqC3uPy69n4M7tGyEnASRPZp8bRaaMNiCo1i', 1, '2026-06-24 15:07:56', '2026-06-24 15:07:56', NULL);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `assurances`
--
ALTER TABLE `assurances`
  ADD CONSTRAINT `assurances_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE;

--
-- Contraintes pour la table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `bulletins`
--
ALTER TABLE `bulletins`
  ADD CONSTRAINT `bulletins_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`),
  ADD CONSTRAINT `bulletins_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE,
  ADD CONSTRAINT `bulletins_ibfk_4` FOREIGN KEY (`id_periode`) REFERENCES `periodes` (`id_periode`),
  ADD CONSTRAINT `fk_bulletins_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE;

--
-- Contraintes pour la table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`id_section`) REFERENCES `sections` (`id_section`);

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `fk_commandes_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE;

--
-- Contraintes pour la table `commandes_details`
--
ALTER TABLE `commandes_details`
  ADD CONSTRAINT `commandes_details_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE,
  ADD CONSTRAINT `commandes_details_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`);

--
-- Contraintes pour la table `contraintes_horaires`
--
ALTER TABLE `contraintes_horaires`
  ADD CONSTRAINT `contraintes_horaires_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE,
  ADD CONSTRAINT `contraintes_horaires_ibfk_2` FOREIGN KEY (`id_jour`) REFERENCES `jours_semaine` (`id_jour`) ON DELETE CASCADE,
  ADD CONSTRAINT `contraintes_horaires_ibfk_3` FOREIGN KEY (`id_creneau_debut`) REFERENCES `creneaux` (`id_creneau`) ON DELETE CASCADE,
  ADD CONSTRAINT `contraintes_horaires_ibfk_4` FOREIGN KEY (`id_creneau_fin`) REFERENCES `creneaux` (`id_creneau`) ON DELETE CASCADE;

--
-- Contraintes pour la table `disponibilites_enseignants`
--
ALTER TABLE `disponibilites_enseignants`
  ADD CONSTRAINT `disponibilites_enseignants_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`) ON DELETE CASCADE,
  ADD CONSTRAINT `disponibilites_enseignants_ibfk_2` FOREIGN KEY (`id_creneau`) REFERENCES `creneaux` (`id_creneau`) ON DELETE CASCADE,
  ADD CONSTRAINT `disponibilites_enseignants_ibfk_3` FOREIGN KEY (`id_jour`) REFERENCES `jours_semaine` (`id_jour`) ON DELETE CASCADE;

--
-- Contraintes pour la table `echeances`
--
ALTER TABLE `echeances`
  ADD CONSTRAINT `echeances_ibfk_2` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_echeances_frais` FOREIGN KEY (`id_frais`) REFERENCES `frais` (`id_frais`) ON DELETE CASCADE;

--
-- Contraintes pour la table `enseignants`
--
ALTER TABLE `enseignants`
  ADD CONSTRAINT `fk_enseignants_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `enseignements`
--
ALTER TABLE `enseignements`
  ADD CONSTRAINT `enseignements_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`) ON DELETE CASCADE,
  ADD CONSTRAINT `enseignements_ibfk_2` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`) ON DELETE CASCADE,
  ADD CONSTRAINT `enseignements_ibfk_3` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`) ON DELETE CASCADE;

--
-- Contraintes pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD CONSTRAINT `fk_etudiants_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`id_periode`) REFERENCES `periodes` (`id_periode`),
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`),
  ADD CONSTRAINT `evaluations_ibfk_3` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`),
  ADD CONSTRAINT `evaluations_ibfk_4` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`);

--
-- Contraintes pour la table `evenements`
--
ALTER TABLE `evenements`
  ADD CONSTRAINT `fk_evenements_utilisateur` FOREIGN KEY (`id_utilisateur_createur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `frais`
--
ALTER TABLE `frais`
  ADD CONSTRAINT `frais_ibfk_1` FOREIGN KEY (`id_type_frais`) REFERENCES `types_frais` (`id_type_frais`),
  ADD CONSTRAINT `frais_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`),
  ADD CONSTRAINT `frais_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE;

--
-- Contraintes pour la table `horaires`
--
ALTER TABLE `horaires`
  ADD CONSTRAINT `fk_horaires_matiere` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `horaires_ibfk_1` FOREIGN KEY (`id_generation`) REFERENCES `horaires_generations` (`id_generation`) ON DELETE CASCADE,
  ADD CONSTRAINT `horaires_ibfk_2` FOREIGN KEY (`id_enseignement`) REFERENCES `enseignements` (`id_enseignement`) ON DELETE CASCADE,
  ADD CONSTRAINT `horaires_ibfk_3` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `horaires_ibfk_4` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`),
  ADD CONSTRAINT `horaires_ibfk_5` FOREIGN KEY (`id_creneau`) REFERENCES `creneaux` (`id_creneau`),
  ADD CONSTRAINT `horaires_ibfk_6` FOREIGN KEY (`id_jour`) REFERENCES `jours_semaine` (`id_jour`);

--
-- Contraintes pour la table `horaires_generations`
--
ALTER TABLE `horaires_generations`
  ADD CONSTRAINT `horaires_generations_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE;

--
-- Contraintes pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD CONSTRAINT `fk_inscriptions_classe` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscriptions_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscriptions_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE;

--
-- Contraintes pour la table `matieres_classes`
--
ALTER TABLE `matieres_classes`
  ADD CONSTRAINT `matieres_classes_ibfk_1` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`) ON DELETE CASCADE,
  ADD CONSTRAINT `matieres_classes_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`) ON DELETE CASCADE,
  ADD CONSTRAINT `matieres_classes_ibfk_3` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`) ON DELETE SET NULL;

--
-- Contraintes pour la table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id_menu`) ON DELETE CASCADE;

--
-- Contraintes pour la table `mouvements_stock`
--
ALTER TABLE `mouvements_stock`
  ADD CONSTRAINT `fk_mouvements_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `mouvements_stock_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE,
  ADD CONSTRAINT `mouvements_stock_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `fk_notes_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`id_evaluation`) REFERENCES `evaluations` (`id_evaluation`) ON DELETE CASCADE;

--
-- Contraintes pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `fk_paiements_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  ADD CONSTRAINT `paiements_ibfk_2` FOREIGN KEY (`id_frais`) REFERENCES `frais` (`id_frais`),
  ADD CONSTRAINT `paiements_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE,
  ADD CONSTRAINT `paiements_ibfk_4` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `paiements_recus`
--
ALTER TABLE `paiements_recus`
  ADD CONSTRAINT `paiements_recus_ibfk_1` FOREIGN KEY (`id_recu`) REFERENCES `recus` (`id_recu`) ON DELETE CASCADE,
  ADD CONSTRAINT `paiements_recus_ibfk_2` FOREIGN KEY (`id_paiement`) REFERENCES `paiements` (`id_paiement`) ON DELETE CASCADE;

--
-- Contraintes pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_resets_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `periodes`
--
ALTER TABLE `periodes`
  ADD CONSTRAINT `periodes_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE;

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `produits_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categories_produits` (`id_categorie`);

--
-- Contraintes pour la table `recus`
--
ALTER TABLE `recus`
  ADD CONSTRAINT `recus_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`),
  ADD CONSTRAINT `recus_ibfk_2` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE,
  ADD CONSTRAINT `recus_ibfk_3` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `roles_menus`
--
ALTER TABLE `roles_menus`
  ADD CONSTRAINT `roles_menus_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_menus_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `menus` (`id_menu`) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
