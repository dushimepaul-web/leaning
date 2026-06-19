/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.5.2-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: vip_school
-- ------------------------------------------------------
-- Server version	11.5.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `absences`
--

DROP TABLE IF EXISTS `absences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `absences` (
  `id_absence` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `date_absence` date NOT NULL,
  `motif` varchar(255) DEFAULT NULL,
  `justifiee` tinyint(1) NOT NULL DEFAULT 0,
  `type_absence` enum('etudiant','enseignant') NOT NULL DEFAULT 'etudiant',
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL,
  `modifie_le` datetime NOT NULL,
  PRIMARY KEY (`id_absence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `absences`
--

LOCK TABLES `absences` WRITE;
/*!40000 ALTER TABLE `absences` DISABLE KEYS */;
/*!40000 ALTER TABLE `absences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `annees_scolaires`
--

DROP TABLE IF EXISTS `annees_scolaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annees_scolaires` (
  `id_annee` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `libelle` varchar(20) NOT NULL,
  `debut` date NOT NULL,
  `fin` date NOT NULL,
  `est_en_cours` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_annee`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annees_scolaires`
--

LOCK TABLES `annees_scolaires` WRITE;
/*!40000 ALTER TABLE `annees_scolaires` DISABLE KEYS */;
INSERT INTO `annees_scolaires` VALUES
(1,'19bd43cf-ebc6-43b9-95ac-48992c3eace5','2025-2026','2025-09-09','2026-07-12',1,NULL,'2026-06-09 01:06:14','2026-06-09 01:12:33');
/*!40000 ALTER TABLE `annees_scolaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assurances`
--

DROP TABLE IF EXISTS `assurances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assurances` (
  `id_assurance` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `police` varchar(50) NOT NULL,
  `compagnie` varchar(100) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `statut` enum('active','expiree','resiliee') NOT NULL DEFAULT 'active',
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_assurance`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `police` (`police`),
  KEY `id_etudiant` (`id_etudiant`),
  CONSTRAINT `assurances_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assurances`
--

LOCK TABLES `assurances` WRITE;
/*!40000 ALTER TABLE `assurances` DISABLE KEYS */;
/*!40000 ALTER TABLE `assurances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `table_concernee` varchar(50) DEFAULT NULL,
  `id_enregistrement` int(11) DEFAULT NULL,
  `anciennes_valeurs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`anciennes_valeurs`)),
  `nouvelles_valeurs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`nouvelles_valeurs`)),
  `adresse_ip` varchar(45) DEFAULT NULL,
  `date_action` datetime NOT NULL DEFAULT current_timestamp(),
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_log`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `idx_audit_table` (`table_concernee`),
  KEY `idx_audit_date` (`date_action`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES
(1,'',NULL,'update','roles_menus',3,NULL,NULL,'::1','2026-06-15 09:29:31','2026-06-15 09:29:31'),
(12,'9ed5f2bf-e5db-4f1a-a9c0-efabb27758dc',1,'update','roles_menus',2,NULL,NULL,'::1','2026-06-15 09:44:42','2026-06-15 09:44:42'),
(13,'9c7e1f5e-0668-495e-8183-9eddcfe346b3',1,'update','roles_menus',2,NULL,NULL,'::1','2026-06-15 09:45:09','2026-06-15 09:45:09'),
(14,'7ba5aefa-b7c5-4e34-950e-e2b809738730',1,'update','roles_menus',2,NULL,NULL,'::1','2026-06-15 09:47:43','2026-06-15 09:47:43'),
(15,'abb72089-8a42-405a-90e5-a9f71514c7d7',NULL,'update','roles_menus',2,NULL,NULL,'::1','2026-06-15 09:48:36','2026-06-15 09:48:36'),
(16,'66ea8b01-1139-4317-a7bc-4bf6672b693c',NULL,'update','roles_menus',2,NULL,NULL,'::1','2026-06-15 09:48:47','2026-06-15 09:48:47');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bibliotheque_livres`
--

DROP TABLE IF EXISTS `bibliotheque_livres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bibliotheque_livres` (
  `id_livre` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `auteur` varchar(150) DEFAULT NULL,
  `isbn` varchar(30) DEFAULT NULL,
  `editeur` varchar(100) DEFAULT NULL,
  `annee` int(4) DEFAULT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `disponible` int(11) NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL,
  `modifie_le` datetime NOT NULL,
  PRIMARY KEY (`id_livre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bibliotheque_livres`
--

LOCK TABLES `bibliotheque_livres` WRITE;
/*!40000 ALTER TABLE `bibliotheque_livres` DISABLE KEYS */;
/*!40000 ALTER TABLE `bibliotheque_livres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bulletins`
--

DROP TABLE IF EXISTS `bulletins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bulletins` (
  `id_bulletin` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL,
  `id_periode` int(11) NOT NULL,
  `moyenne` decimal(5,2) DEFAULT NULL,
  `rang` int(11) DEFAULT NULL,
  `decision` enum('admis','ajourne','echoue') DEFAULT 'admis',
  `date_edition` date NOT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_bulletin`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_bulletin` (`id_etudiant`,`id_annee`,`id_periode`),
  KEY `id_classe` (`id_classe`),
  KEY `id_annee` (`id_annee`),
  KEY `idx_bulletins_periode` (`id_periode`),
  KEY `idx_bulletins_decision` (`decision`),
  CONSTRAINT `bulletins_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`),
  CONSTRAINT `bulletins_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE,
  CONSTRAINT `bulletins_ibfk_4` FOREIGN KEY (`id_periode`) REFERENCES `periodes` (`id_periode`),
  CONSTRAINT `fk_bulletins_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bulletins`
--

LOCK TABLES `bulletins` WRITE;
/*!40000 ALTER TABLE `bulletins` DISABLE KEYS */;
/*!40000 ALTER TABLE `bulletins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories_produits`
--

DROP TABLE IF EXISTS `categories_produits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories_produits` (
  `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `code` varchar(30) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_categorie`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories_produits`
--

LOCK TABLES `categories_produits` WRITE;
/*!40000 ALTER TABLE `categories_produits` DISABLE KEYS */;
INSERT INTO `categories_produits` VALUES
(1,'79541d51-6369-11f1-9d55-9c7bef735b1f','UNIFORME','Uniformes','2026-06-08 20:40:08'),
(2,'79542150-6369-11f1-9d55-9c7bef735b1f','LIVRE','Livres et manuels','2026-06-08 20:40:08'),
(3,'795422d3-6369-11f1-9d55-9c7bef735b1f','MATERIEL','Mat??riels scolaires','2026-06-08 20:40:08');
/*!40000 ALTER TABLE `categories_produits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certificats`
--

DROP TABLE IF EXISTS `certificats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certificats` (
  `id_certificat` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `numero_certificat` varchar(30) NOT NULL,
  `type_certificat` varchar(50) NOT NULL,
  `motif` text DEFAULT NULL,
  `date_emission` date NOT NULL,
  `statut` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL,
  `modifie_le` datetime NOT NULL,
  PRIMARY KEY (`id_certificat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certificats`
--

LOCK TABLES `certificats` WRITE;
/*!40000 ALTER TABLE `certificats` DISABLE KEYS */;
/*!40000 ALTER TABLE `certificats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classes` (
  `id_classe` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_section` int(11) NOT NULL,
  `code` varchar(30) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `niveau` varchar(50) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_classe`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`),
  KEY `id_section` (`id_section`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`id_section`) REFERENCES `sections` (`id_section`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES
(1,'50726231-6449-11f1-954f-9c7bef735b1f',1,'1P-ECO','1ère Primaire ECO',NULL,NULL,'2026-06-09 23:22:27','2026-06-09 23:22:27'),
(2,'50726544-6449-11f1-954f-9c7bef735b1f',1,'2P-ECO','2ème Primaire ECO',NULL,NULL,'2026-06-09 23:22:27','2026-06-09 23:22:27'),
(3,'5072663c-6449-11f1-954f-9c7bef735b1f',2,'3S-PEDA','3ème Secondaire PEDA',NULL,NULL,'2026-06-09 23:22:27','2026-06-09 23:22:27');
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commandes` (
  `id_commande` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `date_commande` datetime NOT NULL DEFAULT current_timestamp(),
  `statut` enum('en_attente','prete','distribuee','annulee') NOT NULL DEFAULT 'en_attente',
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_commande`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `idx_commandes_etudiant` (`id_etudiant`),
  KEY `idx_commandes_statut` (`statut`),
  CONSTRAINT `fk_commandes_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commandes`
--

LOCK TABLES `commandes` WRITE;
/*!40000 ALTER TABLE `commandes` DISABLE KEYS */;
/*!40000 ALTER TABLE `commandes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commandes_details`
--

DROP TABLE IF EXISTS `commandes_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commandes_details` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `prix_unitaire` decimal(12,2) NOT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_detail`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_commande` (`id_commande`),
  KEY `id_produit` (`id_produit`),
  CONSTRAINT `commandes_details_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE,
  CONSTRAINT `commandes_details_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commandes_details`
--

LOCK TABLES `commandes_details` WRITE;
/*!40000 ALTER TABLE `commandes_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `commandes_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comptabilite_ecritures`
--

DROP TABLE IF EXISTS `comptabilite_ecritures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comptabilite_ecritures` (
  `id_ecriture` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_plan` int(11) NOT NULL,
  `libelle` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `montant` decimal(12,2) NOT NULL,
  `type` enum('debit','credit') NOT NULL,
  `date_ecriture` date NOT NULL,
  `id_etudiant` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL,
  `modifie_le` datetime NOT NULL,
  PRIMARY KEY (`id_ecriture`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comptabilite_ecritures`
--

LOCK TABLES `comptabilite_ecritures` WRITE;
/*!40000 ALTER TABLE `comptabilite_ecritures` DISABLE KEYS */;
/*!40000 ALTER TABLE `comptabilite_ecritures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comptabilite_plans`
--

DROP TABLE IF EXISTS `comptabilite_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comptabilite_plans` (
  `id_plan` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `code_compte` varchar(20) NOT NULL,
  `libelle` varchar(150) NOT NULL,
  `type` enum('actif','passif','charge','produit') NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL,
  `modifie_le` datetime NOT NULL,
  PRIMARY KEY (`id_plan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comptabilite_plans`
--

LOCK TABLES `comptabilite_plans` WRITE;
/*!40000 ALTER TABLE `comptabilite_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `comptabilite_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contraintes_horaires`
--

DROP TABLE IF EXISTS `contraintes_horaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contraintes_horaires` (
  `id_contrainte` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_annee` int(11) NOT NULL,
  `type` enum('matiere','classe','enseignant','global') NOT NULL,
  `id_concerne` int(11) DEFAULT NULL COMMENT 'id_matiere / id_classe / id_enseignant / NULL pour global',
  `id_jour` int(11) DEFAULT NULL,
  `id_creneau_debut` int(11) DEFAULT NULL,
  `id_creneau_fin` int(11) DEFAULT NULL,
  `regle` varchar(50) NOT NULL COMMENT 'interdit, preferer_matin, max_consecutifs, seulement_creneau',
  `valeur` varchar(255) DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_contrainte`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_annee` (`id_annee`),
  KEY `id_jour` (`id_jour`),
  KEY `id_creneau_debut` (`id_creneau_debut`),
  KEY `id_creneau_fin` (`id_creneau_fin`),
  KEY `idx_contraintes_type` (`type`),
  KEY `idx_contraintes_concerne` (`id_concerne`),
  CONSTRAINT `contraintes_horaires_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE,
  CONSTRAINT `contraintes_horaires_ibfk_2` FOREIGN KEY (`id_jour`) REFERENCES `jours_semaine` (`id_jour`) ON DELETE CASCADE,
  CONSTRAINT `contraintes_horaires_ibfk_3` FOREIGN KEY (`id_creneau_debut`) REFERENCES `creneaux` (`id_creneau`) ON DELETE CASCADE,
  CONSTRAINT `contraintes_horaires_ibfk_4` FOREIGN KEY (`id_creneau_fin`) REFERENCES `creneaux` (`id_creneau`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contraintes_horaires`
--

LOCK TABLES `contraintes_horaires` WRITE;
/*!40000 ALTER TABLE `contraintes_horaires` DISABLE KEYS */;
/*!40000 ALTER TABLE `contraintes_horaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `creneaux`
--

DROP TABLE IF EXISTS `creneaux`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `creneaux` (
  `id_creneau` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `libelle` varchar(50) NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `ordre` int(11) NOT NULL DEFAULT 0,
  `type_creneau` enum('cours','recreation','pause','accueil','sortie') NOT NULL DEFAULT 'cours',
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_creneau`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `idx_creneaux_ordre` (`ordre`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `creneaux`
--

LOCK TABLES `creneaux` WRITE;
/*!40000 ALTER TABLE `creneaux` DISABLE KEYS */;
INSERT INTO `creneaux` VALUES
(6,'44445bcc-636b-11f1-9d55-9c7bef735b1f','6?me heure','11:25:00','12:20:00',6,'cours','2026-06-08 20:52:58','2026-06-08 23:55:22'),
(7,'44445caa-636b-11f1-9d55-9c7bef735b1f','5eme Heure','10:40:00','11:25:00',7,'cours','2026-06-08 20:52:58','2026-06-08 23:52:20'),
(8,'44445d70-636b-11f1-9d55-9c7bef735b1f','Pause','10:30:00','10:40:00',8,'pause','2026-06-08 20:52:58','2026-06-08 23:51:24'),
(9,'44445e3b-636b-11f1-9d55-9c7bef735b1f','4?me heure','09:45:00','10:30:00',9,'cours','2026-06-08 20:52:58','2026-06-08 23:50:11'),
(10,'44445ef8-636b-11f1-9d55-9c7bef735b1f','3eme heure','09:00:00','09:45:00',10,'cours','2026-06-08 20:52:58','2026-06-08 23:49:48'),
(11,'44445fb5-636b-11f1-9d55-9c7bef735b1f','2?me heure','08:15:00','09:00:00',11,'cours','2026-06-08 20:52:58','2026-06-08 23:48:58'),
(12,'4444606f-636b-11f1-9d55-9c7bef735b1f','1?me heure','07:30:00','08:15:00',12,'cours','2026-06-08 20:52:58','2026-06-08 23:48:19'),
(13,'12b36adf-60e8-4091-af5e-8995d2dddc98','8?me heure','13:05:00','13:50:00',4,'cours','2026-06-08 23:58:08','2026-06-08 23:58:08'),
(14,'5f6c125f-3592-4009-8144-72ec8e82fe1c','7?me heure','12:55:00','13:05:00',5,'cours','2026-06-08 23:59:01','2026-06-08 23:59:01');
/*!40000 ALTER TABLE `creneaux` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departements`
--

DROP TABLE IF EXISTS `departements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departements` (
  `id_departement` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL,
  `modifie_le` datetime NOT NULL,
  PRIMARY KEY (`id_departement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departements`
--

LOCK TABLES `departements` WRITE;
/*!40000 ALTER TABLE `departements` DISABLE KEYS */;
/*!40000 ALTER TABLE `departements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disponibilites_enseignants`
--

DROP TABLE IF EXISTS `disponibilites_enseignants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disponibilites_enseignants` (
  `id_disponibilite` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_enseignant` int(11) NOT NULL,
  `id_creneau` int(11) NOT NULL,
  `id_jour` int(11) NOT NULL,
  `type` enum('disponible','indisponible') NOT NULL DEFAULT 'disponible',
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_disponibilite`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_dispo_ens` (`id_enseignant`,`id_creneau`,`id_jour`),
  KEY `id_creneau` (`id_creneau`),
  KEY `idx_dispos_enseignant` (`id_enseignant`),
  KEY `idx_dispos_jour` (`id_jour`),
  CONSTRAINT `disponibilites_enseignants_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`) ON DELETE CASCADE,
  CONSTRAINT `disponibilites_enseignants_ibfk_2` FOREIGN KEY (`id_creneau`) REFERENCES `creneaux` (`id_creneau`) ON DELETE CASCADE,
  CONSTRAINT `disponibilites_enseignants_ibfk_3` FOREIGN KEY (`id_jour`) REFERENCES `jours_semaine` (`id_jour`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disponibilites_enseignants`
--

LOCK TABLES `disponibilites_enseignants` WRITE;
/*!40000 ALTER TABLE `disponibilites_enseignants` DISABLE KEYS */;
/*!40000 ALTER TABLE `disponibilites_enseignants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `distributions`
--

DROP TABLE IF EXISTS `distributions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distributions` (
  `id_distribution` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `date_distribution` date NOT NULL,
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utilisateur` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_distribution`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_commande` (`id_commande`),
  KEY `id_etudiant` (`id_etudiant`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `distributions_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE,
  CONSTRAINT `distributions_ibfk_2` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`),
  CONSTRAINT `distributions_ibfk_3` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distributions`
--

LOCK TABLES `distributions` WRITE;
/*!40000 ALTER TABLE `distributions` DISABLE KEYS */;
/*!40000 ALTER TABLE `distributions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `echeances`
--

DROP TABLE IF EXISTS `echeances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `echeances` (
  `id_echeance` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_frais` int(11) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `date_echeance` date NOT NULL,
  `montant` decimal(12,2) NOT NULL,
  `statut` enum('impaye','partiel','paye','annule') NOT NULL DEFAULT 'impaye',
  `rappel_envoye` tinyint(1) NOT NULL DEFAULT 0,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_echeance`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_etudiant` (`id_etudiant`),
  KEY `idx_echeances_statut` (`statut`),
  KEY `idx_echeances_date` (`date_echeance`),
  KEY `fk_echeances_frais` (`id_frais`),
  CONSTRAINT `echeances_ibfk_2` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  CONSTRAINT `fk_echeances_frais` FOREIGN KEY (`id_frais`) REFERENCES `frais` (`id_frais`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `echeances`
--

LOCK TABLES `echeances` WRITE;
/*!40000 ALTER TABLE `echeances` DISABLE KEYS */;
/*!40000 ALTER TABLE `echeances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employes`
--

DROP TABLE IF EXISTS `employes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employes` (
  `id_employe` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `matricule` varchar(30) NOT NULL,
  `nom_complet` varchar(150) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `poste` varchar(100) DEFAULT NULL,
  `id_departement` int(11) DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `salaire` decimal(12,2) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL,
  `modifie_le` datetime NOT NULL,
  PRIMARY KEY (`id_employe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employes`
--

LOCK TABLES `employes` WRITE;
/*!40000 ALTER TABLE `employes` DISABLE KEYS */;
/*!40000 ALTER TABLE `employes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enseignants`
--

DROP TABLE IF EXISTS `enseignants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enseignants` (
  `id_enseignant` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `matricule` varchar(30) NOT NULL,
  `nom` varchar(80) NOT NULL,
  `postnom` varchar(80) DEFAULT NULL,
  `prenom` varchar(80) DEFAULT NULL,
  `sexe` enum('M','F') NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `adresse_permanente` text DEFAULT NULL,
  `statut_matrimonial` enum('Marie','Celibataire') DEFAULT NULL,
  `type_contrat` enum('Contractuel','Horaire') DEFAULT NULL,
  `poste` varchar(100) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `experience` varchar(255) DEFAULT NULL,
  `groupe_sanguin` varchar(5) DEFAULT NULL,
  `taille` decimal(5,2) DEFAULT NULL,
  `poids` decimal(5,2) DEFAULT NULL,
  `pere_nom` varchar(80) DEFAULT NULL,
  `mere_nom` varchar(80) DEFAULT NULL,
  `banque_compte` varchar(50) DEFAULT NULL,
  `banque_nom` varchar(100) DEFAULT NULL,
  `banque_ifsc` varchar(30) DEFAULT NULL,
  `numero_identification` varchar(50) DEFAULT NULL,
  `reseaux_sociaux` text DEFAULT NULL,
  `specialite` varchar(100) DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_enseignant`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `matricule` (`matricule`),
  KEY `idx_enseignants_actif` (`actif`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enseignants`
--

LOCK TABLES `enseignants` WRITE;
/*!40000 ALTER TABLE `enseignants` DISABLE KEYS */;
INSERT INTO `enseignants` VALUES
(1,'5e68a11a-6449-11f1-954f-9c7bef735b1f','TCH-001','Dupont','','Jean','M',NULL,'+243800000001','jean@school.cd',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'CHIMIE',NULL,1,'2026-06-09 23:22:50','2026-06-09 23:49:05','2026-06-09 21:49:05'),
(2,'5e68a617-6449-11f1-954f-9c7bef735b1f','TCH-002','Smith','','Alice','F',NULL,'+243800000002','alice@school.cd',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'CHIMIE',NULL,1,'2026-06-09 23:22:50','2026-06-09 23:22:50',NULL),
(3,'5e68a7e3-6449-11f1-954f-9c7bef735b1f','TCH-003','Kabongo','Pierre','Paul','M',NULL,'+243800000003','paul@school.cd',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'CHIMIE',NULL,1,'2026-06-09 23:22:50','2026-06-09 23:22:50',NULL),
(4,'5d092052-644a-11f1-954f-9c7bef735b1f','TCH-004','Mukendi','Dieudonné','Jean-Pierre','M','1985-03-15','+243810000001','jp.mukendi@school.cd',NULL,'12, Av. Kasaï, Kinshasa','45, Av. Libération, Lubumbashi','Marie','Contractuel','Kinshasa','Doctorat en Mathématiques','15 ans','A+',178.00,75.50,'Mukendi Paul','Mukendi Marie','1234567890','Rawbank','RAWBCD01','ID-CONGO-001',NULL,'Mathématiques','2015-09-01',1,'2026-06-09 23:29:57','2026-06-09 23:29:57',NULL),
(5,'5d092506-644a-11f1-954f-9c7bef735b1f','TCH-005','Lukusa','Albert','Marie-Claire','F','1990-07-22','+243820000001','mc.lukusa@school.cd',NULL,'8, Av. Mwangaza, Goma','BP 123, Bukavu','Celibataire','Horaire','Goma','Master en Français','8 ans','O+',165.00,60.00,'Lukusa Joseph','Lukusa Béatrice','2345678901','Access Bank','ACCDR01','ID-CONGO-002',NULL,'Français','2019-10-15',1,'2026-06-09 23:29:57','2026-06-09 23:29:57',NULL),
(6,'5d092654-644a-11f1-954f-9c7bef735b1f','TCH-006','Tshibangu','Michel','Pascal','M','1982-11-30','+243830000001','pascal.tshibangu@school.cd',NULL,'25, Av. de lÉglise, Mbuji-Mayi','10, Av. Centrale, Kananga','Marie','Contractuel','Mbuji-Mayi','Doctorat en Physique','18 ans','AB+',182.00,80.00,'Tshibangu Pierre','Tshibangu Hélène','3456789012','BIC','BICCD01','ID-CONGO-003',NULL,'Physique','2013-09-01',1,'2026-06-09 23:29:57','2026-06-09 23:29:57',NULL),
(7,'5d09274f-644a-11f1-954f-9c7bef735b1f','TCH-007','Kabasele','Joseph','Benoît','M','1995-05-18','+243840000001','benoit.kabasele@school.cd',NULL,'7, Av. du Commerce, Kolwezi','22, Av. Verte, Likasi','Celibataire','Contractuel','Kolwezi','Master en Anglais','5 ans','B-',175.00,70.00,'Kabasele François','Kabasele Rose','4567890123','FirstBank','FIRBCD01','ID-CONGO-004',NULL,'Anglais','2020-09-01',1,'2026-06-09 23:29:57','2026-06-09 23:29:57',NULL),
(8,'5d0927f9-644a-11f1-954f-9c7bef735b1f','TCH-008','Nzuzi','Katherine','Esther','F','1988-08-25','+243850000001','esther.nzuzi@school.cd',NULL,'15, Av. de la Paix, Matadi','30, Av. du Port, Boma','Marie','Contractuel','Matadi','Master en Histoire','10 ans','A-',168.00,65.00,'Nzuzi Daniel','Nzuzi Sarah','5678901234','TMB','TMBCD01','ID-CONGO-005',NULL,'Histoire','2017-09-01',1,'2026-06-09 23:29:57','2026-06-09 23:29:57',NULL);
/*!40000 ALTER TABLE `enseignants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enseignements`
--

DROP TABLE IF EXISTS `enseignements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enseignements` (
  `id_enseignement` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_enseignant` int(11) NOT NULL,
  `id_matiere` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_enseignement`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_enseignement` (`id_enseignant`,`id_matiere`,`id_classe`),
  KEY `id_matiere` (`id_matiere`),
  KEY `id_classe` (`id_classe`),
  CONSTRAINT `enseignements_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`) ON DELETE CASCADE,
  CONSTRAINT `enseignements_ibfk_2` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`) ON DELETE CASCADE,
  CONSTRAINT `enseignements_ibfk_3` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enseignements`
--

LOCK TABLES `enseignements` WRITE;
/*!40000 ALTER TABLE `enseignements` DISABLE KEYS */;
INSERT INTO `enseignements` VALUES
(4,'6328e8bf-6449-11f1-954f-9c7bef735b1f',1,1,1,'2026-06-09 23:22:58',NULL,'2026-06-09 23:22:58'),
(5,'6328ed5a-6449-11f1-954f-9c7bef735b1f',2,1,2,'2026-06-09 23:22:58',NULL,'2026-06-09 23:22:58'),
(6,'6328ee85-6449-11f1-954f-9c7bef735b1f',3,1,3,'2026-06-09 23:22:58',NULL,'2026-06-09 23:22:58'),
(7,'61db982b-644a-11f1-954f-9c7bef735b1f',4,2,1,'2026-06-09 23:30:05',NULL,'2026-06-09 23:30:05'),
(8,'61db9ac3-644a-11f1-954f-9c7bef735b1f',5,3,2,'2026-06-09 23:30:05',NULL,'2026-06-09 23:30:05'),
(9,'61db9bab-644a-11f1-954f-9c7bef735b1f',6,4,3,'2026-06-09 23:30:05',NULL,'2026-06-09 23:30:05'),
(10,'61db9c36-644a-11f1-954f-9c7bef735b1f',7,5,1,'2026-06-09 23:30:05',NULL,'2026-06-09 23:30:05'),
(11,'61db9cb6-644a-11f1-954f-9c7bef735b1f',8,6,2,'2026-06-09 23:30:05',NULL,'2026-06-09 23:30:05');
/*!40000 ALTER TABLE `enseignements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etudiants`
--

DROP TABLE IF EXISTS `etudiants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etudiants` (
  `id_etudiant` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `matricule` varchar(30) NOT NULL,
  `nom` varchar(80) NOT NULL,
  `postnom` varchar(80) DEFAULT NULL,
  `prenom` varchar(80) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(100) DEFAULT NULL,
  `sexe` enum('M','F') NOT NULL,
  `adresse` text DEFAULT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tuteur_nom` varchar(150) DEFAULT NULL,
  `tuteur_telephone` varchar(30) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_etudiant`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `matricule` (`matricule`),
  KEY `idx_etudiants_nom` (`nom`,`postnom`,`prenom`),
  KEY `idx_etudiants_date_naiss` (`date_naissance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etudiants`
--

LOCK TABLES `etudiants` WRITE;
/*!40000 ALTER TABLE `etudiants` DISABLE KEYS */;
/*!40000 ALTER TABLE `etudiants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluations`
--

DROP TABLE IF EXISTS `evaluations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `evaluations` (
  `id_evaluation` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_periode` int(11) NOT NULL,
  `libelle` varchar(150) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `id_matiere` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL,
  `date_eval` date NOT NULL,
  `type` enum('devoir','examen','composition') NOT NULL DEFAULT 'devoir',
  `coefficient` decimal(3,1) NOT NULL DEFAULT 1.0,
  `sur` decimal(5,1) NOT NULL DEFAULT 20.0,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_evaluation`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_matiere` (`id_matiere`),
  KEY `id_annee` (`id_annee`),
  KEY `idx_evaluations_classe` (`id_classe`),
  KEY `idx_evaluations_periode` (`id_periode`),
  KEY `idx_evaluations_type` (`type`),
  CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`id_periode`) REFERENCES `periodes` (`id_periode`),
  CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`),
  CONSTRAINT `evaluations_ibfk_3` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`),
  CONSTRAINT `evaluations_ibfk_4` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluations`
--

LOCK TABLES `evaluations` WRITE;
/*!40000 ALTER TABLE `evaluations` DISABLE KEYS */;
/*!40000 ALTER TABLE `evaluations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evenements`
--

DROP TABLE IF EXISTS `evenements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `evenements` (
  `id_evenement` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime DEFAULT NULL,
  `lieu` varchar(200) DEFAULT NULL,
  `type` enum('scolaire','sportif','culturel','reunion','autre') NOT NULL DEFAULT 'scolaire',
  `couleur` varchar(7) DEFAULT '#25A194',
  `statut` enum('planifie','en_cours','termine','annule') NOT NULL DEFAULT 'planifie',
  `id_utilisateur_createur` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_evenement`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `date_debut` (`date_debut`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evenements`
--

LOCK TABLES `evenements` WRITE;
/*!40000 ALTER TABLE `evenements` DISABLE KEYS */;
INSERT INTO `evenements` VALUES
(1,'1f7f4c91-ad99-4ae2-a905-3b29784bffe6','test event',NULL,'2026-06-20 10:00:00',NULL,NULL,'scolaire','#25A194','planifie',NULL,NULL,'2026-06-09 23:59:29','2026-06-09 23:59:29');
/*!40000 ALTER TABLE `evenements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `frais`
--

DROP TABLE IF EXISTS `frais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `frais` (
  `id_frais` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_type_frais` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL,
  `montant` decimal(12,2) NOT NULL,
  `echeance` date DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_frais`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_frais` (`id_type_frais`,`id_classe`,`id_annee`),
  KEY `id_classe` (`id_classe`),
  KEY `id_annee` (`id_annee`),
  CONSTRAINT `frais_ibfk_1` FOREIGN KEY (`id_type_frais`) REFERENCES `types_frais` (`id_type_frais`),
  CONSTRAINT `frais_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`),
  CONSTRAINT `frais_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frais`
--

LOCK TABLES `frais` WRITE;
/*!40000 ALTER TABLE `frais` DISABLE KEYS */;
/*!40000 ALTER TABLE `frais` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horaires`
--

DROP TABLE IF EXISTS `horaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `horaires` (
  `id_horaire` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_generation` int(11) NOT NULL,
  `id_enseignement` int(11) NOT NULL,
  `id_enseignant` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `id_creneau` int(11) NOT NULL,
  `id_jour` int(11) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
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
  CONSTRAINT `horaires_ibfk_1` FOREIGN KEY (`id_generation`) REFERENCES `horaires_generations` (`id_generation`) ON DELETE CASCADE,
  CONSTRAINT `horaires_ibfk_2` FOREIGN KEY (`id_enseignement`) REFERENCES `enseignements` (`id_enseignement`) ON DELETE CASCADE,
  CONSTRAINT `horaires_ibfk_3` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  CONSTRAINT `horaires_ibfk_4` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`),
  CONSTRAINT `horaires_ibfk_5` FOREIGN KEY (`id_creneau`) REFERENCES `creneaux` (`id_creneau`),
  CONSTRAINT `horaires_ibfk_6` FOREIGN KEY (`id_jour`) REFERENCES `jours_semaine` (`id_jour`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horaires`
--

LOCK TABLES `horaires` WRITE;
/*!40000 ALTER TABLE `horaires` DISABLE KEYS */;
/*!40000 ALTER TABLE `horaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horaires_generations`
--

DROP TABLE IF EXISTS `horaires_generations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `horaires_generations` (
  `id_generation` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `id_annee` int(11) NOT NULL,
  `date_generation` datetime NOT NULL DEFAULT current_timestamp(),
  `statut` enum('brouillon','publie','archive') NOT NULL DEFAULT 'brouillon',
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_generation`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_annee` (`id_annee`),
  CONSTRAINT `horaires_generations_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horaires_generations`
--

LOCK TABLES `horaires_generations` WRITE;
/*!40000 ALTER TABLE `horaires_generations` DISABLE KEYS */;
/*!40000 ALTER TABLE `horaires_generations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inscriptions`
--

DROP TABLE IF EXISTS `inscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inscriptions` (
  `id_inscription` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL,
  `date_inscription` date NOT NULL,
  `statut` enum('inscrit','actif','suspendu','exclu','termine') NOT NULL DEFAULT 'actif',
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_inscription`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_inscription` (`id_etudiant`,`id_annee`),
  KEY `idx_inscriptions_classe` (`id_classe`),
  KEY `idx_inscriptions_annee` (`id_annee`),
  CONSTRAINT `fk_inscriptions_classe` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`) ON DELETE CASCADE,
  CONSTRAINT `inscriptions_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  CONSTRAINT `inscriptions_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inscriptions`
--

LOCK TABLES `inscriptions` WRITE;
/*!40000 ALTER TABLE `inscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `inscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jours_semaine`
--

DROP TABLE IF EXISTS `jours_semaine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jours_semaine` (
  `id_jour` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `code` varchar(10) NOT NULL,
  `libelle` varchar(20) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `ordre` int(11) NOT NULL DEFAULT 0,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_jour`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jours_semaine`
--

LOCK TABLES `jours_semaine` WRITE;
/*!40000 ALTER TABLE `jours_semaine` DISABLE KEYS */;
INSERT INTO `jours_semaine` VALUES
(1,'4434f68b-636b-11f1-9d55-9c7bef735b1f','lundi','Lundi',1,1,'2026-06-08 20:52:58'),
(2,'4434facd-636b-11f1-9d55-9c7bef735b1f','mardi','Mardi',1,2,'2026-06-08 20:52:58'),
(3,'4434fd20-636b-11f1-9d55-9c7bef735b1f','mercredi','Mercredi',1,3,'2026-06-08 20:52:58'),
(4,'4434fe86-636b-11f1-9d55-9c7bef735b1f','jeudi','Jeudi',1,4,'2026-06-08 20:52:58'),
(5,'4434ffbe-636b-11f1-9d55-9c7bef735b1f','vendredi','Vendredi',1,5,'2026-06-08 20:52:58'),
(6,'443500f8-636b-11f1-9d55-9c7bef735b1f','samedi','Samedi',1,6,'2026-06-08 20:52:58');
/*!40000 ALTER TABLE `jours_semaine` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matieres`
--

DROP TABLE IF EXISTS `matieres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matieres` (
  `id_matiere` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `code` varchar(20) NOT NULL,
  `libelle` varchar(120) NOT NULL,
  `coefficient` decimal(3,1) NOT NULL DEFAULT 1.0,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_matiere`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matieres`
--

LOCK TABLES `matieres` WRITE;
/*!40000 ALTER TABLE `matieres` DISABLE KEYS */;
INSERT INTO `matieres` VALUES
(1,'35b5ec42-a82e-4904-9404-c059b7c4762e','CHIMIE','CHIMIE',99.9,'2026-06-09 01:18:38','2026-06-09 01:18:38',NULL),
(2,'61cd2b55-644a-11f1-954f-9c7bef735b1f','MATH','Mathématiques',1.0,'2026-06-09 23:30:05','2026-06-09 23:30:05',NULL),
(3,'61cd3118-644a-11f1-954f-9c7bef735b1f','FRAN','Français',1.0,'2026-06-09 23:30:05','2026-06-09 23:30:05',NULL),
(4,'61cd31f0-644a-11f1-954f-9c7bef735b1f','PHYS','Physique',1.0,'2026-06-09 23:30:05','2026-06-10 09:40:55','2026-06-10 07:40:55'),
(5,'61cd3280-644a-11f1-954f-9c7bef735b1f','ANGL','Anglais',1.0,'2026-06-09 23:30:05','2026-06-09 23:30:05',NULL),
(6,'61cd330a-644a-11f1-954f-9c7bef735b1f','HIST','Histoire',1.0,'2026-06-09 23:30:05','2026-06-09 23:30:05',NULL);
/*!40000 ALTER TABLE `matieres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matieres_classes`
--

DROP TABLE IF EXISTS `matieres_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matieres_classes` (
  `id_matiere_classe` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_matiere` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `id_enseignant` int(11) DEFAULT NULL,
  `coefficient` decimal(3,1) NOT NULL DEFAULT 1.0,
  `nb_heures_par_jour` decimal(4,1) NOT NULL DEFAULT 0.0,
  `nb_heures_par_semaine` decimal(4,1) NOT NULL DEFAULT 0.0,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_matiere_classe`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_matiere_classe` (`id_matiere`,`id_classe`),
  KEY `id_classe` (`id_classe`),
  KEY `id_enseignant` (`id_enseignant`),
  CONSTRAINT `matieres_classes_ibfk_1` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`) ON DELETE CASCADE,
  CONSTRAINT `matieres_classes_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`) ON DELETE CASCADE,
  CONSTRAINT `matieres_classes_ibfk_3` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matieres_classes`
--

LOCK TABLES `matieres_classes` WRITE;
/*!40000 ALTER TABLE `matieres_classes` DISABLE KEYS */;
/*!40000 ALTER TABLE `matieres_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menus` (
  `id_menu` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) DEFAULT uuid(),
  `code` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `ordre` int(11) NOT NULL DEFAULT 0,
  `route` varchar(100) DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_menu`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id_menu`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES
(1,'7949fad0-6369-11f1-9d55-9c7bef735b1f','dashboard','Tableau de bord','dashboard',NULL,1,'Dashboard','2026-06-08 20:40:08','2026-06-10 01:15:10'),
(2,'7949ff93-6369-11f1-9d55-9c7bef735b1f','etudiants','??tudiants','people',NULL,2,'etudiants.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),
(3,'794a0193-6369-11f1-9d55-9c7bef735b1f','inscriptions','Inscriptions','how_to_reg',NULL,3,'inscriptions.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),
(4,'794a032d-6369-11f1-9d55-9c7bef735b1f','sections','Sections & Classes','layers',NULL,4,'Classes','2026-06-08 20:40:08','2026-06-10 01:15:10'),
(5,'794a04c1-6369-11f1-9d55-9c7bef735b1f','enseignants','Enseignants','school',NULL,5,'Enseignants','2026-06-08 20:40:08','2026-06-10 01:15:10'),
(6,'794a0648-6369-11f1-9d55-9c7bef735b1f','scolarite','Scolarit??','payments',NULL,6,'Frais','2026-06-08 20:40:08','2026-06-10 01:15:10'),
(7,'794a07d7-6369-11f1-9d55-9c7bef735b1f','scolarite_minerval','Minerval',NULL,6,1,'minerval.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),
(8,'794a09e9-6369-11f1-9d55-9c7bef735b1f','scolarite_assurance','Assurance',NULL,6,2,'assurance.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),
(9,'794a0bc4-6369-11f1-9d55-9c7bef735b1f','scolarite_toilettes','Toilettes',NULL,6,3,'toilettes.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),
(10,'794a0d79-6369-11f1-9d55-9c7bef735b1f','produits','Produits','inventory',NULL,7,'produits.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),
(11,'794a0efa-6369-11f1-9d55-9c7bef735b1f','produits_uniformes','Uniformes',NULL,10,1,'uniformes.php','2026-06-08 20:40:08','2026-06-10 01:15:10'),
(12,'794a10af-6369-11f1-9d55-9c7bef735b1f','produits_livres','Livres',NULL,10,2,'livres.php','2026-06-08 20:40:08','2026-06-10 01:15:10'),
(13,'794a1279-6369-11f1-9d55-9c7bef735b1f','produits_materiel','Mat. scolaires',NULL,10,3,'materiels.php','2026-06-08 20:40:08','2026-06-10 01:15:10'),
(14,'794a1432-6369-11f1-9d55-9c7bef735b1f','stock','Stock','warehouse',NULL,8,'stock.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),
(15,'794a15b9-6369-11f1-9d55-9c7bef735b1f','points','Notes & Points','grade',NULL,9,'Notes','2026-06-08 20:40:08','2026-06-10 01:15:10'),
(16,'794a1749-6369-11f1-9d55-9c7bef735b1f','bulletins','Bulletins','description',NULL,10,'bulletins.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),
(17,'794a18db-6369-11f1-9d55-9c7bef735b1f','paiements','Paiements','account_balance',NULL,11,'paiements.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),
(18,'794a1a5c-6369-11f1-9d55-9c7bef735b1f','recus','Re??us','receipt',NULL,12,'recus.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),
(19,'794a1be8-6369-11f1-9d55-9c7bef735b1f','echeances','??ch??anciers','calendar_month',NULL,13,'echeances.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),
(20,'794a1d5a-6369-11f1-9d55-9c7bef735b1f','rapports','Rapports','assessment',NULL,14,'rapports.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),
(21,'794a1ed8-6369-11f1-9d55-9c7bef735b1f','parametres','Param??tres','settings',50,1,'Parametres','2026-06-08 20:40:08','2026-06-15 09:24:39'),
(22,'794a2048-6369-11f1-9d55-9c7bef735b1f','utilisateurs','Utilisateurs','admin_panel_settings',50,2,'Utilisateurs','2026-06-08 20:40:08','2026-06-15 09:24:39'),
(23,'794a21c8-6369-11f1-9d55-9c7bef735b1f','audit','Journal d\'audit','history',50,5,'audit.php','2026-06-08 20:40:08','2026-06-15 09:24:39'),
(24,'6bec1849-636a-11f1-9d55-9c7bef735b1f','horaires','Horaires','calendar_view_week',NULL,18,'Horaires','2026-06-08 20:46:55','2026-06-10 01:15:10'),
(25,'6becf21b-636a-11f1-9d55-9c7bef735b1f','horaires_creneaux','Cr??neaux',NULL,24,1,'creneaux.php','2026-06-08 20:46:55','2026-06-08 20:46:55'),
(26,'6becf766-636a-11f1-9d55-9c7bef735b1f','horaires_volumes','Volumes horaires',NULL,24,2,'volumes.php','2026-06-08 20:46:55','2026-06-08 20:46:55'),
(27,'6becf93e-636a-11f1-9d55-9c7bef735b1f','horaires_dispos','Disponibilit??s',NULL,24,3,'disponibilites.php','2026-06-08 20:46:55','2026-06-08 20:46:55'),
(28,'6becfb8f-636a-11f1-9d55-9c7bef735b1f','horaires_generer','G??n??rer',NULL,24,4,'generer.php','2026-06-08 20:46:55','2026-06-08 20:46:55'),
(29,'6becfd94-636a-11f1-9d55-9c7bef735b1f','horaires_consulter','Consulter',NULL,24,5,'consulter.php','2026-06-08 20:46:55','2026-06-08 20:46:55'),
(35,'18e6df5c-638e-11f1-9d55-9c7bef735b1f','classes','Classes','ri-list-view',4,1,'Classes','2026-06-09 01:02:17','2026-06-10 01:15:10'),
(36,'18e6e644-638e-11f1-9d55-9c7bef735b1f','periodes','P?riodes','ri-calendar-line',4,2,'Classes','2026-06-09 01:02:17','2026-06-10 01:14:47'),
(37,'18e6e76a-638e-11f1-9d55-9c7bef735b1f','annees_scolaires','Ann?es scolaires','ri-calendar-2-line',4,3,'Classes','2026-06-09 01:02:17','2026-06-10 01:14:47'),
(38,'18e6e868-638e-11f1-9d55-9c7bef735b1f','matieres','Mati?res','ri-book-2-line',4,4,'Classes','2026-06-09 01:02:17','2026-06-10 01:14:47'),
(39,'18e6e934-638e-11f1-9d55-9c7bef735b1f','enseignements','Enseignements','ri-book-open-line',4,5,'Classes','2026-06-09 01:02:17','2026-06-10 01:14:47'),
(40,'bbfa970d-644d-11f1-954f-9c7bef735b1f','evenements','Evenements','calendar_check',NULL,19,'Evenements','2026-06-09 23:54:05','2026-06-10 01:15:10'),
(41,'0fa3328d-6459-11f1-954f-9c7bef735b1f','commandes','Commandes','ri-shopping-cart-line',NULL,20,'Commandes','2026-06-10 01:15:10','2026-06-10 01:15:10'),
(42,'0fa3d07b-6459-11f1-954f-9c7bef735b1f','absences','Absences','ri-user-unfollow-line',NULL,21,'Absences','2026-06-10 01:15:10','2026-06-10 01:15:10'),
(43,'0fa469c8-6459-11f1-954f-9c7bef735b1f','bibliotheque','BibliothÃ¨que','ri-book-shelf-line',NULL,22,'Bibliotheque','2026-06-10 01:15:10','2026-06-10 01:15:10'),
(44,'0fa57c61-6459-11f1-954f-9c7bef735b1f','certificats','Certificats','ri-award-line',NULL,23,'Certificats','2026-06-10 01:15:10','2026-06-10 01:15:10'),
(45,'0fa6b9ef-6459-11f1-954f-9c7bef735b1f','comptabilite','ComptabilitÃ©','ri-calculator-line',NULL,24,'Comptabilite','2026-06-10 01:15:10','2026-06-10 01:15:10'),
(46,'0fa8190a-6459-11f1-954f-9c7bef735b1f','employes','EmployÃ©s','ri-user-heart-line',NULL,25,'Employes','2026-06-10 01:15:10','2026-06-10 01:15:10'),
(47,'0fa974ee-6459-11f1-954f-9c7bef735b1f','paie','Paie','ri-money-euro-circle-line',NULL,26,'Paie','2026-06-10 01:15:10','2026-06-10 01:15:10'),
(48,'0faac93d-6459-11f1-954f-9c7bef735b1f','uniformes','Uniformes','ri-shirt-line',NULL,27,'Uniformes','2026-06-10 01:15:10','2026-06-10 01:15:10'),
(49,'0fac2c2d-6459-11f1-954f-9c7bef735b1f','messages','Messages','ri-message-line',NULL,28,'Messages','2026-06-10 01:15:10','2026-06-10 01:15:10'),
(50,'4518fae7-688b-11f1-b4e2-9c7bef735b1f','administration','Administration','ri-shield-user-line',NULL,29,'Administration','2026-06-15 09:24:39','2026-06-15 09:24:39'),
(51,'451bd714-688b-11f1-b4e2-9c7bef735b1f','roles','Rôles',NULL,50,3,'Administration/Roles','2026-06-15 09:24:39','2026-06-15 09:24:39'),
(52,'451c9bc9-688b-11f1-b4e2-9c7bef735b1f','permissions','Permissions',NULL,50,4,'Administration/Permissions','2026-06-15 09:24:39','2026-06-15 09:24:39'),
(53,'35e84231-38ea-454b-b1b2-a9ddf21e8cc5','menus','Menus','ri-menu-line',50,6,'Administration/Menus','2026-06-15 09:46:40','2026-06-15 09:46:40'),
(54,'2ec9fadc-827e-4b39-b0fc-a9597efd263a','operations','OpÃ©rations groupÃ©es','ri-import-export-line',50,7,'Administration/Operations','2026-06-15 09:50:55','2026-06-15 09:50:55'),
(55,'d50bfcc0-0561-40a2-adb3-6e748a21dfbf','sauvegardes','Sauvegardes','ri-database-2-line',50,8,'Administration/Sauvegardes','2026-06-15 09:50:55','2026-06-15 09:50:55');
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_expediteur` int(11) NOT NULL,
  `id_destinataire` int(11) DEFAULT NULL,
  `destinataire_type` enum('utilisateur','groupe','tous') NOT NULL DEFAULT 'utilisateur',
  `sujet` varchar(200) DEFAULT NULL,
  `corps` text NOT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL,
  `modifie_le` datetime NOT NULL,
  PRIMARY KEY (`id_message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mouvements_stock`
--

DROP TABLE IF EXISTS `mouvements_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mouvements_stock` (
  `id_mouvement` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `type` enum('entree','sortie') NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` decimal(12,2) DEFAULT NULL,
  `motif` varchar(255) DEFAULT NULL,
  `date_mvt` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utilisateur` int(11) DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_mouvement`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `idx_mouvements_produit` (`id_produit`),
  KEY `idx_mouvements_date` (`date_mvt`),
  CONSTRAINT `mouvements_stock_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE,
  CONSTRAINT `mouvements_stock_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mouvements_stock`
--

LOCK TABLES `mouvements_stock` WRITE;
/*!40000 ALTER TABLE `mouvements_stock` DISABLE KEYS */;
/*!40000 ALTER TABLE `mouvements_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notes` (
  `id_note` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_evaluation` int(11) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `note` decimal(5,1) NOT NULL,
  `appreciation` varchar(255) DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_note`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_note` (`id_evaluation`,`id_etudiant`),
  KEY `idx_notes_etudiant` (`id_etudiant`),
  CONSTRAINT `fk_notes_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`id_evaluation`) REFERENCES `evaluations` (`id_evaluation`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paie_bulletins`
--

DROP TABLE IF EXISTS `paie_bulletins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paie_bulletins` (
  `id_bulletin_paie` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_contrat` int(11) NOT NULL,
  `mois` int(2) NOT NULL,
  `annee` int(4) NOT NULL,
  `salaire_brut` decimal(12,2) NOT NULL,
  `deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `salaire_net` decimal(12,2) NOT NULL,
  `date_edition` date NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL,
  `modifie_le` datetime NOT NULL,
  PRIMARY KEY (`id_bulletin_paie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paie_bulletins`
--

LOCK TABLES `paie_bulletins` WRITE;
/*!40000 ALTER TABLE `paie_bulletins` DISABLE KEYS */;
/*!40000 ALTER TABLE `paie_bulletins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paie_contrats`
--

DROP TABLE IF EXISTS `paie_contrats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paie_contrats` (
  `id_contrat` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_employe` int(11) NOT NULL,
  `type_contrat` enum('cdi','cdd','stage') NOT NULL DEFAULT 'cdi',
  `salaire_base` decimal(12,2) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL,
  `modifie_le` datetime NOT NULL,
  PRIMARY KEY (`id_contrat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paie_contrats`
--

LOCK TABLES `paie_contrats` WRITE;
/*!40000 ALTER TABLE `paie_contrats` DISABLE KEYS */;
/*!40000 ALTER TABLE `paie_contrats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paiements` (
  `id_paiement` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `id_frais` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL,
  `montant` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Montant vers?? dans ce paiement',
  `date_paiement` datetime NOT NULL DEFAULT current_timestamp(),
  `mode_paiement` enum('especes','banque','mobile_money','cheque') NOT NULL DEFAULT 'especes',
  `reference` varchar(100) DEFAULT NULL COMMENT 'N?? de transaction bancaire/mobile',
  `id_utilisateur` int(11) DEFAULT NULL,
  `statut` enum('partiel','solde','annule') NOT NULL DEFAULT 'partiel',
  `notes` text DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
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
  CONSTRAINT `fk_paiements_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  CONSTRAINT `paiements_ibfk_2` FOREIGN KEY (`id_frais`) REFERENCES `frais` (`id_frais`),
  CONSTRAINT `paiements_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE,
  CONSTRAINT `paiements_ibfk_4` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paiements`
--

LOCK TABLES `paiements` WRITE;
/*!40000 ALTER TABLE `paiements` DISABLE KEYS */;
/*!40000 ALTER TABLE `paiements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paiements_recus`
--

DROP TABLE IF EXISTS `paiements_recus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paiements_recus` (
  `id_paiement_recu` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_recu` int(11) NOT NULL,
  `id_paiement` int(11) NOT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_paiement_recu`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_recu_paiement` (`id_recu`,`id_paiement`),
  KEY `id_paiement` (`id_paiement`),
  CONSTRAINT `paiements_recus_ibfk_1` FOREIGN KEY (`id_recu`) REFERENCES `recus` (`id_recu`) ON DELETE CASCADE,
  CONSTRAINT `paiements_recus_ibfk_2` FOREIGN KEY (`id_paiement`) REFERENCES `paiements` (`id_paiement`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paiements_recus`
--

LOCK TABLES `paiements_recus` WRITE;
/*!40000 ALTER TABLE `paiements_recus` DISABLE KEYS */;
/*!40000 ALTER TABLE `paiements_recus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parametres`
--

DROP TABLE IF EXISTS `parametres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parametres` (
  `id_parametre` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `clef` varchar(50) NOT NULL,
  `valeur` text NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_parametre`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `clef` (`clef`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parametres`
--

LOCK TABLES `parametres` WRITE;
/*!40000 ALTER TABLE `parametres` DISABLE KEYS */;
INSERT INTO `parametres` VALUES
(1,'7956029e-6369-11f1-9d55-9c7bef735b1f','nom_ecole','FUTURE VIP SCHOOL',NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),
(2,'79560766-6369-11f1-9d55-9c7bef735b1f','adresse_ecole','',NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),
(3,'795609cd-6369-11f1-9d55-9c7bef735b1f','telephone_ecole','',NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),
(4,'79560b2e-6369-11f1-9d55-9c7bef735b1f','email_ecole','',NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),
(5,'79560cbf-6369-11f1-9d55-9c7bef735b1f','logo_ecole','assets/uploads/logo/a9cc41beef232042ccf9f8a51eacf41e.jpeg',NULL,'2026-06-08 20:40:08','2026-06-15 09:26:27'),
(6,'795610e9-6369-11f1-9d55-9c7bef735b1f','devise','BIF',NULL,'2026-06-08 20:40:08','2026-06-09 00:06:45'),
(7,'7956147e-6369-11f1-9d55-9c7bef735b1f','tva','0',NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),
(8,'79561787-6369-11f1-9d55-9c7bef735b1f','prochain_num_recu','1',NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),
(9,'79561b46-6369-11f1-9d55-9c7bef735b1f','annee_active','1',NULL,'2026-06-08 20:40:08','2026-06-10 00:12:06'),
(10,'ad458b22-d590-4f2f-8a42-db3366458090','favicon_ecole','assets/uploads/logo/favicon_b9816ac09024ef5fc9fb82ef60d1e104.png',NULL,'2026-06-10 00:34:12','2026-06-15 09:28:15'),
(11,'bd209b26-6889-11f1-b4e2-9c7bef735b1f','email_protocol','mail',NULL,'2026-06-15 09:13:41','2026-06-15 09:13:41'),
(12,'bd20a396-6889-11f1-b4e2-9c7bef735b1f','email_smtp_host','',NULL,'2026-06-15 09:13:41','2026-06-15 09:13:41'),
(13,'bd20a420-6889-11f1-b4e2-9c7bef735b1f','email_smtp_user','',NULL,'2026-06-15 09:13:41','2026-06-15 09:13:41'),
(14,'bd20a4e8-6889-11f1-b4e2-9c7bef735b1f','email_smtp_pass','',NULL,'2026-06-15 09:13:41','2026-06-15 09:13:41'),
(15,'bd20a536-6889-11f1-b4e2-9c7bef735b1f','email_smtp_port','587',NULL,'2026-06-15 09:13:41','2026-06-15 09:13:41'),
(16,'bd20a57b-6889-11f1-b4e2-9c7bef735b1f','email_smtp_crypto','tls',NULL,'2026-06-15 09:13:41','2026-06-15 09:13:41'),
(17,'bd20a5c9-6889-11f1-b4e2-9c7bef735b1f','email_sendmail_path','/usr/sbin/sendmail',NULL,'2026-06-15 09:13:41','2026-06-15 09:13:41');
/*!40000 ALTER TABLE `parametres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id_reset` int(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(11) NOT NULL,
  `code` varchar(6) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `utilise` tinyint(1) NOT NULL DEFAULT 0,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_reset`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `token` (`token`),
  KEY `code` (`code`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `periodes`
--

DROP TABLE IF EXISTS `periodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periodes` (
  `id_periode` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_annee` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `est_en_cours` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_periode`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_periode_annee` (`libelle`,`id_annee`),
  KEY `id_annee` (`id_annee`),
  CONSTRAINT `periodes_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `periodes`
--

LOCK TABLES `periodes` WRITE;
/*!40000 ALTER TABLE `periodes` DISABLE KEYS */;
INSERT INTO `periodes` VALUES
(1,'3462e3ee-2069-4ddd-b819-89871773c8d2',1,'1ERE TRIMESTRE','2025-09-09','2025-12-25',1,NULL,'2026-06-09 01:14:51','2026-06-10 09:25:14'),
(2,'7e29b0b7-d5e8-4f7e-9264-1fd7d9baea4b',1,'2EME TRIMESTRE','2026-01-01','2026-03-27',0,NULL,'2026-06-09 01:16:06','2026-06-10 09:25:14'),
(3,'bc828fb9-c30f-4fff-960b-4406ad7c4dec',1,'3EME TRIMESTRE','2026-06-01','2026-07-12',0,NULL,'2026-06-09 01:16:46','2026-06-10 09:25:01');
/*!40000 ALTER TABLE `periodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produits`
--

DROP TABLE IF EXISTS `produits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produits` (
  `id_produit` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_categorie` int(11) NOT NULL,
  `code` varchar(30) NOT NULL,
  `libelle` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `prix_unitaire` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock_mini` int(11) NOT NULL DEFAULT 0,
  `stock_actuel` int(11) NOT NULL DEFAULT 0,
  `unite` varchar(20) DEFAULT 'pi??ce',
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_produit`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_produits_categorie` (`id_categorie`),
  CONSTRAINT `produits_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categories_produits` (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produits`
--

LOCK TABLES `produits` WRITE;
/*!40000 ALTER TABLE `produits` DISABLE KEYS */;
/*!40000 ALTER TABLE `produits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recus`
--

DROP TABLE IF EXISTS `recus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recus` (
  `id_recu` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `numero_recu` varchar(30) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL,
  `date_edition` datetime NOT NULL DEFAULT current_timestamp(),
  `montant_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utilisateur` int(11) DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_recu`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `numero_recu` (`numero_recu`),
  KEY `id_etudiant` (`id_etudiant`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `idx_recus_annee` (`id_annee`),
  CONSTRAINT `recus_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`),
  CONSTRAINT `recus_ibfk_2` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE,
  CONSTRAINT `recus_ibfk_3` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recus`
--

LOCK TABLES `recus` WRITE;
/*!40000 ALTER TABLE `recus` DISABLE KEYS */;
/*!40000 ALTER TABLE `recus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `code` varchar(30) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `hierarchie` int(11) NOT NULL DEFAULT 0,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'7948b1f8-6369-11f1-9d55-9c7bef735b1f','admin','Administrateur',10,'2026-06-08 20:40:08','2026-06-08 20:40:08',NULL),
(2,'7948b668-6369-11f1-9d55-9c7bef735b1f','direction','Direction',8,'2026-06-08 20:40:08','2026-06-08 20:40:08',NULL),
(3,'7948b9df-6369-11f1-9d55-9c7bef735b1f','comptable','Comptable',5,'2026-06-08 20:40:08','2026-06-08 20:40:08',NULL),
(4,'7948bb51-6369-11f1-9d55-9c7bef735b1f','secretaire','Secretaire',3,'2026-06-08 20:40:08','2026-06-08 23:44:59',NULL),
(5,'7948bc8d-6369-11f1-9d55-9c7bef735b1f','lecture','Lecture seule',1,'2026-06-08 20:40:08','2026-06-08 20:40:08',NULL),
(6,'669f34a6-3931-47eb-ad62-01fcda2e4c7a','Eleve','Eleve',6,'2026-06-15 09:34:17','2026-06-15 09:34:17',NULL),
(7,'2da7015f-1cd8-4f2e-9b55-bb3074f48f9d','Parent','parent',9,'2026-06-15 09:34:50','2026-06-15 09:34:50',NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_menus`
--

DROP TABLE IF EXISTS `roles_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles_menus` (
  `id_role_menu` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_role` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT 0,
  `can_add` tinyint(1) NOT NULL DEFAULT 0,
  `can_edit` tinyint(1) NOT NULL DEFAULT 0,
  `can_delete` tinyint(1) NOT NULL DEFAULT 0,
  `can_export` tinyint(1) NOT NULL DEFAULT 0,
  `can_imprimer` tinyint(1) NOT NULL DEFAULT 0,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_role_menu`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_role_menu` (`id_role`,`id_menu`),
  KEY `id_menu` (`id_menu`),
  CONSTRAINT `roles_menus_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE,
  CONSTRAINT `roles_menus_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `menus` (`id_menu`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=265 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_menus`
--

LOCK TABLES `roles_menus` WRITE;
/*!40000 ALTER TABLE `roles_menus` DISABLE KEYS */;
INSERT INTO `roles_menus` VALUES
(32,'794d6189-6369-11f1-9d55-9c7bef735b1f',2,1,1,1,0,0,1,1,'2026-06-08 20:40:08'),
(33,'794d6a83-6369-11f1-9d55-9c7bef735b1f',2,2,1,1,0,0,1,1,'2026-06-08 20:40:08'),
(34,'794d6dcb-6369-11f1-9d55-9c7bef735b1f',2,3,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(35,'794d700b-6369-11f1-9d55-9c7bef735b1f',2,4,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(36,'794d720f-6369-11f1-9d55-9c7bef735b1f',2,5,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(37,'794d7559-6369-11f1-9d55-9c7bef735b1f',2,6,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(38,'794d777e-6369-11f1-9d55-9c7bef735b1f',2,10,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(39,'794d7ab6-6369-11f1-9d55-9c7bef735b1f',2,14,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(40,'794d7dad-6369-11f1-9d55-9c7bef735b1f',2,15,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(41,'794d8348-6369-11f1-9d55-9c7bef735b1f',2,16,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(42,'794d8783-6369-11f1-9d55-9c7bef735b1f',2,17,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(43,'794d8ec0-6369-11f1-9d55-9c7bef735b1f',2,18,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(44,'794d9428-6369-11f1-9d55-9c7bef735b1f',2,19,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(45,'794d9ade-6369-11f1-9d55-9c7bef735b1f',2,20,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(46,'794da008-6369-11f1-9d55-9c7bef735b1f',2,21,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(47,'794da49d-6369-11f1-9d55-9c7bef735b1f',2,22,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(48,'794da712-6369-11f1-9d55-9c7bef735b1f',2,23,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(49,'794da93a-6369-11f1-9d55-9c7bef735b1f',2,7,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(50,'794dab41-6369-11f1-9d55-9c7bef735b1f',2,8,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(51,'794dad2f-6369-11f1-9d55-9c7bef735b1f',2,9,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(52,'794daf1e-6369-11f1-9d55-9c7bef735b1f',2,11,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(53,'794db10d-6369-11f1-9d55-9c7bef735b1f',2,12,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(54,'794db300-6369-11f1-9d55-9c7bef735b1f',2,13,1,0,0,0,1,1,'2026-06-08 20:40:08'),
(63,'794ef43c-6369-11f1-9d55-9c7bef735b1f',3,1,1,1,1,0,1,1,'2026-06-08 20:40:08'),
(64,'794ef9f7-6369-11f1-9d55-9c7bef735b1f',3,19,1,1,1,0,1,1,'2026-06-08 20:40:08'),
(65,'794efccb-6369-11f1-9d55-9c7bef735b1f',3,17,1,1,1,0,1,1,'2026-06-08 20:40:08'),
(66,'794eff2e-6369-11f1-9d55-9c7bef735b1f',3,10,1,1,1,0,1,1,'2026-06-08 20:40:08'),
(67,'794f01b9-6369-11f1-9d55-9c7bef735b1f',3,12,1,1,1,0,1,1,'2026-06-08 20:40:08'),
(68,'794f041a-6369-11f1-9d55-9c7bef735b1f',3,13,1,1,1,0,1,1,'2026-06-08 20:40:08'),
(69,'794f0689-6369-11f1-9d55-9c7bef735b1f',3,11,1,1,1,0,1,1,'2026-06-08 20:40:08'),
(70,'794f08c0-6369-11f1-9d55-9c7bef735b1f',3,20,1,1,1,0,1,1,'2026-06-08 20:40:08'),
(71,'794f0b0c-6369-11f1-9d55-9c7bef735b1f',3,18,1,1,1,0,1,1,'2026-06-08 20:40:08'),
(72,'794f0d4f-6369-11f1-9d55-9c7bef735b1f',3,6,1,1,1,0,1,1,'2026-06-08 20:40:08'),
(73,'794f0faf-6369-11f1-9d55-9c7bef735b1f',3,8,1,1,1,0,1,1,'2026-06-08 20:40:08'),
(74,'794f135a-6369-11f1-9d55-9c7bef735b1f',3,7,1,1,1,0,1,1,'2026-06-08 20:40:08'),
(75,'794f15d5-6369-11f1-9d55-9c7bef735b1f',3,9,1,1,1,0,1,1,'2026-06-08 20:40:08'),
(76,'794f1811-6369-11f1-9d55-9c7bef735b1f',3,14,1,1,1,0,1,1,'2026-06-08 20:40:08'),
(100,'6bef39dd-636a-11f1-9d55-9c7bef735b1f',2,24,1,0,0,0,1,1,'2026-06-08 20:46:55'),
(101,'6bef3e61-636a-11f1-9d55-9c7bef735b1f',2,29,1,0,0,0,1,1,'2026-06-08 20:46:55'),
(102,'6bef4116-636a-11f1-9d55-9c7bef735b1f',2,25,1,0,0,0,1,1,'2026-06-08 20:46:55'),
(103,'6bef44f7-636a-11f1-9d55-9c7bef735b1f',2,27,1,0,0,0,1,1,'2026-06-08 20:46:55'),
(104,'6bef473b-636a-11f1-9d55-9c7bef735b1f',2,28,1,0,0,0,1,1,'2026-06-08 20:46:55'),
(105,'6bef4953-636a-11f1-9d55-9c7bef735b1f',2,26,1,0,0,0,1,1,'2026-06-08 20:46:55'),
(114,'ffd035c6-319f-4941-9297-3bb7247c419b',1,1,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(115,'3df919c9-aff4-47bd-8b69-cf13f0829745',1,2,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(116,'a27bad6f-7f7b-4d20-964a-9166f656c48b',1,3,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(117,'31a88d4a-1c75-4c00-82b6-846c7a519c7f',1,4,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(118,'854b3a38-475c-48d3-af6f-b435aebc3e65',1,5,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(119,'0dfb46b2-3bb0-4c5f-b022-f056bc15599f',1,6,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(120,'9b9fcb23-d610-4ea0-b0e8-41495681c4ca',1,7,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(121,'4bbe93db-612a-40f8-ba69-af38ded4aa81',1,8,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(122,'c5c2c0c9-071e-415b-8e82-fcb116250855',1,9,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(123,'c88aee37-ddb5-4c98-8253-66011d54ac60',1,10,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(124,'e54e145a-d2ad-46a5-967f-740026c52f7b',1,14,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(125,'0300b42d-3488-4743-83fe-5a48e3ad41ba',1,15,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(126,'79e18a4a-4808-4423-8d93-df3aa3776811',1,16,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(127,'36505a61-b609-43de-bc5e-1ef20d15da83',1,17,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(128,'43fc56e0-9f22-482b-b4b9-a64286c1f00e',1,18,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(129,'946b87b6-809d-4cb9-a31f-dbe8d9e03b83',1,19,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(130,'f5de4026-bbd9-420b-ad7b-4cb9f11f73f0',1,20,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(131,'3f61c340-fb83-4585-a166-34ea6963ffec',1,21,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(132,'9406cdfc-227a-42f5-9d28-5d11a4213856',1,22,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(133,'1a0399f8-16f5-4873-8d9c-8ef7020e7896',1,23,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(134,'a5b74411-a62c-4990-992e-3b0ce7e73b60',1,24,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(135,'5316e0dc-a89e-419f-a071-bb7f8c7735be',1,25,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(136,'dbaf9ab2-958d-48be-829d-bdc83b7134c7',1,26,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(137,'95f77cb1-4549-47bf-97a5-bf532a9c3fed',1,27,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(138,'886c5e3a-2e4c-43e2-be68-fa43c3198c7a',1,28,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(139,'8db4ae6c-fb5f-4c56-9038-11b35a60ee33',1,29,1,1,1,1,1,1,'2026-06-08 23:43:48'),
(140,'835fee0d-abee-4cdf-9916-1c61718fcbe6',4,1,1,1,1,0,0,1,'2026-06-08 23:44:17'),
(141,'dd0d04f2-b37f-450e-a43c-1867201cf953',4,2,1,1,1,0,1,1,'2026-06-08 23:44:17'),
(142,'66279e0e-c906-4f54-80fa-8a674af8a241',4,3,1,1,1,0,1,1,'2026-06-08 23:44:17'),
(143,'14904535-0c55-4de5-9253-19ba14826e66',4,4,1,1,1,0,1,1,'2026-06-08 23:44:17'),
(144,'94ad9bbf-293c-420c-8f3f-4e8c638685d5',4,5,1,1,1,0,1,1,'2026-06-08 23:44:17'),
(145,'dd270777-f043-40ff-82a9-65c5286a4cff',4,6,1,1,0,0,0,0,'2026-06-08 23:44:17'),
(146,'989440a4-f042-4d0a-9dfe-d003126e4185',4,7,0,0,0,0,0,0,'2026-06-08 23:44:17'),
(147,'eb91dab7-84ae-4abd-9f73-c100395dc963',4,8,0,0,0,0,0,0,'2026-06-08 23:44:17'),
(148,'09f01565-6742-45ad-a122-fabc04aec9e2',4,9,0,0,0,0,0,0,'2026-06-08 23:44:17'),
(149,'6a25d6a7-4a6e-4123-ad0b-8f642a8bc917',4,10,0,0,0,0,0,0,'2026-06-08 23:44:17'),
(150,'6893b302-1ac3-469b-9379-659105aacf2d',4,14,0,0,0,0,0,0,'2026-06-08 23:44:17'),
(151,'417ea998-371d-4c2c-bd40-ffc6a1767f88',4,15,1,1,1,0,1,1,'2026-06-08 23:44:17'),
(152,'648bf17d-7b07-4ac4-b93f-f6be4956931c',4,16,1,1,1,0,1,1,'2026-06-08 23:44:17'),
(153,'e6500a90-1157-400f-a66b-1c492a0d6663',4,17,0,0,0,0,0,0,'2026-06-08 23:44:17'),
(154,'a690fee4-529d-4a52-a148-2c2bb5c1d563',4,18,1,1,1,0,1,1,'2026-06-08 23:44:17'),
(155,'a75a1108-5c6a-4ec5-88fd-259a8649473c',4,19,0,0,0,0,0,0,'2026-06-08 23:44:17'),
(156,'314b2ef0-c889-41c1-a266-82a6a537ff64',4,20,1,1,1,0,1,1,'2026-06-08 23:44:17'),
(157,'8c775cee-b17f-4ed3-ae86-a03aee692a8a',4,21,0,0,0,0,0,0,'2026-06-08 23:44:17'),
(158,'5912d372-9d57-4356-adb4-9b931e637bde',4,22,0,0,0,0,0,0,'2026-06-08 23:44:17'),
(159,'e00e6075-a948-40fe-af66-f24ee9d18af8',4,23,0,0,0,0,0,0,'2026-06-08 23:44:17'),
(160,'b28ded31-9b33-40f9-be8f-08f420bc23e7',4,24,1,1,1,0,1,1,'2026-06-08 23:44:17'),
(161,'48552413-0ac2-4592-814a-714308f8d7f5',4,25,1,1,1,0,1,1,'2026-06-08 23:44:17'),
(162,'89b0aaef-f3f3-4baf-a7de-38961d9c7d22',4,26,1,1,1,0,1,1,'2026-06-08 23:44:17'),
(163,'e63862fa-3380-4496-a4c9-4bb966c493dc',4,27,1,1,1,0,1,1,'2026-06-08 23:44:17'),
(164,'3ed125a1-8d8a-4c1c-b0f0-2010573fe816',4,28,1,1,1,0,1,1,'2026-06-08 23:44:17'),
(165,'939ce9da-d1bc-44b4-b317-39c74dd94f26',4,29,1,1,1,0,1,1,'2026-06-08 23:44:17'),
(166,'3c576799-638e-11f1-9d55-9c7bef735b1f',1,35,1,1,1,1,0,0,'2026-06-09 01:03:17'),
(167,'3c5782c8-638e-11f1-9d55-9c7bef735b1f',1,36,1,1,1,1,0,0,'2026-06-09 01:03:17'),
(168,'3c5787fe-638e-11f1-9d55-9c7bef735b1f',1,37,1,1,1,1,0,0,'2026-06-09 01:03:17'),
(169,'3c578d88-638e-11f1-9d55-9c7bef735b1f',1,38,1,1,1,1,0,0,'2026-06-09 01:03:17'),
(170,'3c57949f-638e-11f1-9d55-9c7bef735b1f',1,39,1,1,1,1,0,0,'2026-06-09 01:03:17'),
(171,'3ec701ac-638e-11f1-9d55-9c7bef735b1f',4,35,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(172,'3ec709dd-638e-11f1-9d55-9c7bef735b1f',2,35,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(173,'3ec70c61-638e-11f1-9d55-9c7bef735b1f',3,35,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(174,'3ec70f63-638e-11f1-9d55-9c7bef735b1f',4,36,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(175,'3ec711c7-638e-11f1-9d55-9c7bef735b1f',2,36,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(176,'3ec713cd-638e-11f1-9d55-9c7bef735b1f',3,36,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(177,'3ec715f2-638e-11f1-9d55-9c7bef735b1f',4,37,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(178,'3ec71830-638e-11f1-9d55-9c7bef735b1f',2,37,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(179,'3ec71a15-638e-11f1-9d55-9c7bef735b1f',3,37,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(180,'3ec71d33-638e-11f1-9d55-9c7bef735b1f',4,38,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(181,'3ec720b9-638e-11f1-9d55-9c7bef735b1f',2,38,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(182,'3ec72323-638e-11f1-9d55-9c7bef735b1f',3,38,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(183,'3ec72618-638e-11f1-9d55-9c7bef735b1f',4,39,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(184,'3ec728ac-638e-11f1-9d55-9c7bef735b1f',2,39,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(185,'3ec72b66-638e-11f1-9d55-9c7bef735b1f',3,39,1,1,1,1,0,0,'2026-06-09 01:03:21'),
(186,'bbfbaee0-644d-11f1-954f-9c7bef735b1f',1,40,1,1,1,1,1,1,'2026-06-09 23:54:05'),
(187,'022a4d47-6459-11f1-954f-9c7bef735b1f',1,11,1,1,1,1,1,1,'2026-06-10 01:14:47'),
(188,'022b0d79-6459-11f1-954f-9c7bef735b1f',1,12,1,1,1,1,1,1,'2026-06-10 01:14:47'),
(189,'022ba81a-6459-11f1-954f-9c7bef735b1f',1,13,1,1,1,1,1,1,'2026-06-10 01:14:47'),
(190,'022c8067-6459-11f1-954f-9c7bef735b1f',2,40,1,0,0,0,1,1,'2026-06-10 01:14:47'),
(191,'0fb4a5f5-6459-11f1-954f-9c7bef735b1f',1,42,1,1,1,1,1,1,'2026-06-10 01:15:10'),
(192,'0fb53e41-6459-11f1-954f-9c7bef735b1f',1,43,1,1,1,1,1,1,'2026-06-10 01:15:10'),
(193,'0fb5f0a5-6459-11f1-954f-9c7bef735b1f',1,44,1,1,1,1,1,1,'2026-06-10 01:15:10'),
(194,'0fb68697-6459-11f1-954f-9c7bef735b1f',1,41,1,1,1,1,1,1,'2026-06-10 01:15:10'),
(195,'0fb738a2-6459-11f1-954f-9c7bef735b1f',1,45,1,1,1,1,1,1,'2026-06-10 01:15:10'),
(196,'0fb7e021-6459-11f1-954f-9c7bef735b1f',1,46,1,1,1,1,1,1,'2026-06-10 01:15:10'),
(197,'0fb87d7a-6459-11f1-954f-9c7bef735b1f',1,49,1,1,1,1,1,1,'2026-06-10 01:15:10'),
(198,'0fb91954-6459-11f1-954f-9c7bef735b1f',1,47,1,1,1,1,1,1,'2026-06-10 01:15:10'),
(199,'0fb9a6f9-6459-11f1-954f-9c7bef735b1f',1,48,1,1,1,1,1,1,'2026-06-10 01:15:10'),
(200,'0fba5815-6459-11f1-954f-9c7bef735b1f',2,42,1,0,0,0,1,1,'2026-06-10 01:15:10'),
(201,'0fbaf5ae-6459-11f1-954f-9c7bef735b1f',2,43,1,0,0,0,1,1,'2026-06-10 01:15:10'),
(202,'0fbb9ce7-6459-11f1-954f-9c7bef735b1f',2,44,1,0,0,0,1,1,'2026-06-10 01:15:10'),
(203,'0fbc3cc0-6459-11f1-954f-9c7bef735b1f',2,41,1,0,0,0,1,1,'2026-06-10 01:15:10'),
(204,'0fbcde0f-6459-11f1-954f-9c7bef735b1f',2,45,1,0,0,0,1,1,'2026-06-10 01:15:10'),
(205,'0fbd7ec3-6459-11f1-954f-9c7bef735b1f',2,46,1,0,0,0,1,1,'2026-06-10 01:15:10'),
(206,'0fbe2497-6459-11f1-954f-9c7bef735b1f',2,49,1,0,0,0,1,1,'2026-06-10 01:15:10'),
(207,'0fbec877-6459-11f1-954f-9c7bef735b1f',2,47,1,0,0,0,1,1,'2026-06-10 01:15:10'),
(208,'0fbf555e-6459-11f1-954f-9c7bef735b1f',2,48,1,0,0,0,1,1,'2026-06-10 01:15:10'),
(209,'4ce687da-688b-11f1-b4e2-9c7bef735b1f',1,50,1,1,1,1,1,1,'2026-06-15 09:24:52'),
(210,'4ce68c38-688b-11f1-b4e2-9c7bef735b1f',1,52,1,1,1,1,1,1,'2026-06-15 09:24:52'),
(211,'4ce68cfa-688b-11f1-b4e2-9c7bef735b1f',1,51,1,1,1,1,1,1,'2026-06-15 09:24:52'),
(212,'4cfec4f6-688b-11f1-b4e2-9c7bef735b1f',2,50,1,1,1,0,1,1,'2026-06-15 09:24:52'),
(213,'4cfeca82-688b-11f1-b4e2-9c7bef735b1f',2,52,1,1,1,0,1,1,'2026-06-15 09:24:52'),
(214,'4cfecc67-688b-11f1-b4e2-9c7bef735b1f',2,51,1,1,1,0,1,1,'2026-06-15 09:24:52'),
(215,'e687d1cd-551c-4941-85a3-c93086364d0b',3,2,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(216,'94ebc921-53aa-48b9-95ae-cc69a5b4980e',3,3,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(217,'0f0d42eb-60fe-40d3-939e-8ffb26375b0a',3,4,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(218,'a69693d0-2482-4345-878f-164c48aadc88',3,5,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(219,'bd835931-1bac-4a0a-bc3e-3711a2ba6684',3,15,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(220,'9fc0b0cf-7832-49de-96b4-52363b539b4f',3,16,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(221,'099ae799-3643-49d9-8f36-b574cdce7ce3',3,21,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(222,'3da4259a-e8fe-44c1-a181-d35a36d64224',3,22,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(223,'27950402-5660-47c2-8048-6cd3850252a5',3,23,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(224,'9b72aa8c-9a04-4952-971e-9130f29e30d0',3,24,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(225,'7b451c4c-ff78-42dd-81d4-eac841fd8e81',3,25,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(226,'3d04143f-f9e2-4f88-ac37-6731b293d6f8',3,26,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(227,'1c6cfe32-49c1-455a-8733-99bef453638b',3,27,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(228,'cc4f71cc-caef-4ad1-848b-2aa53469d6d5',3,28,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(229,'39800ec6-243c-4919-a8bb-0a0f75e97b3f',3,29,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(230,'9d3f93cc-1b2b-456e-a284-276f552c11ce',3,40,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(231,'104ade1d-6688-4840-8821-0bf3be39577d',3,41,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(232,'6fd4848f-e6dd-4ca3-bb77-3f64faed9add',3,42,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(233,'c1127199-d79b-4ea7-9d95-51bc008348fa',3,43,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(234,'683aac75-b31a-4e72-8cbf-60fa83440b35',3,44,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(235,'f7ff4a42-a644-4e17-a38c-99281da480c7',3,45,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(236,'8c854324-672b-4502-a5d5-9dd5af5631ea',3,46,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(237,'fb5bf6f6-d27b-4cac-bd2b-24012fbea209',3,47,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(238,'f06b61a0-2829-40ab-86d1-4e2409251e45',3,48,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(239,'03ad15af-633b-4ff2-8c20-d80f264e2a0e',3,49,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(240,'3ab6d69b-4e9f-4686-8956-828e307c9e36',3,50,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(241,'e6c7dc6e-5add-469e-8747-67f6b6cec89a',3,51,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(242,'515fc7a3-187f-4c78-8a87-d2aa67359969',3,52,0,0,0,0,0,0,'2026-06-15 09:29:31'),
(243,'c597b60b-3e06-4e51-9eb0-48af7a541fc0',4,11,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(244,'d04ce750-5c88-4e06-8424-1310cebe6e17',4,12,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(245,'97ed31ac-bbb8-4809-9b9e-00ddc415c049',4,13,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(246,'26e7e7b7-b90f-4076-9366-5a523ac0ce01',4,40,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(247,'b51ad27e-7578-400b-a973-74bf4055a97e',4,41,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(248,'c5677ab9-ed32-4c39-bef2-4e9613a5c806',4,42,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(249,'bfa51be6-ef5f-4e64-b9c9-169bdb4e8020',4,43,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(250,'ef33cf5c-1a0f-4567-9c4a-c119e22bb792',4,44,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(251,'4d7191f4-be00-465d-89f8-07c4c91ace57',4,45,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(252,'7ac5928f-fd6c-4c9a-8fdb-73e31fa739ac',4,46,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(253,'1d141a22-ef4b-48a8-8799-68d79747f49f',4,47,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(254,'6999860e-f50c-4572-a7dd-a07059fadedd',4,48,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(255,'ecb23396-c5de-4956-bc41-fbe665f2dd85',4,49,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(256,'6a0138df-6516-4a03-be12-c7f360cf3036',4,50,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(257,'c7737caf-ba6e-45f9-97bc-7719ec66d127',4,51,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(258,'6e1ba21c-0c74-4f5c-bd49-396ed72c1a72',4,52,0,0,0,0,0,0,'2026-06-15 09:35:30'),
(259,'5d7743b0-494e-47fd-b020-7f9d67f2c7c8',1,53,1,1,1,1,1,1,'2026-06-15 09:46:40'),
(260,'637cf246-e323-4580-b89f-1b888945a122',2,53,1,1,1,1,1,1,'2026-06-15 09:46:40'),
(261,'bf236520-a51b-434f-9caf-57d39e65abe2',1,54,1,1,1,1,1,1,'2026-06-15 09:50:55'),
(262,'6d5f3c03-6c46-489b-ae53-e79d002a7de4',1,55,1,1,1,1,1,1,'2026-06-15 09:50:55'),
(263,'e1ec847c-127e-4235-a0a3-041411530ea1',2,54,1,1,1,1,1,1,'2026-06-15 09:50:55'),
(264,'6c55fc23-ed1d-4ffc-b6d6-00e5a9688b09',2,55,1,1,1,1,1,1,'2026-06-15 09:50:55');
/*!40000 ALTER TABLE `roles_menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `id_section` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `code` varchar(20) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_section`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES
(1,'6b0d10b0-c337-4acf-80ed-52119b3ac260','ECONOMIQUE','ECO',NULL,'2026-06-09 01:17:28','2026-06-09 01:17:28'),
(2,'0440a63a-8fac-4707-ba27-df78355ab4dd','PEDAGOGIQUE','PEDA',NULL,'2026-06-09 01:17:50','2026-06-10 09:36:23');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id_session` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `date_expiration` datetime NOT NULL,
  `adresse_ip` varchar(45) DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_session`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `token` (`token`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `idx_sessions_token` (`token`),
  KEY `idx_sessions_expiration` (`date_expiration`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `types_frais`
--

DROP TABLE IF EXISTS `types_frais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `types_frais` (
  `id_type_frais` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `code` varchar(30) NOT NULL,
  `libelle` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_type_frais`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `types_frais`
--

LOCK TABLES `types_frais` WRITE;
/*!40000 ALTER TABLE `types_frais` DISABLE KEYS */;
INSERT INTO `types_frais` VALUES
(1,'7952efa0-6369-11f1-9d55-9c7bef735b1f','MINERVAL','Minerval / Frais de scolarit??',NULL,NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),
(2,'7952f58e-6369-11f1-9d55-9c7bef735b1f','ASSURANCE','Assurance scolaire',NULL,NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),
(3,'7952f933-6369-11f1-9d55-9c7bef735b1f','UNIFORME','Uniformes scolaires',NULL,NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),
(4,'7952fbde-6369-11f1-9d55-9c7bef735b1f','LIVRE','Manuels et livres scolaires',NULL,NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),
(5,'7952fe28-6369-11f1-9d55-9c7bef735b1f','MATERIEL','Materiels scolaires','',NULL,'2026-06-08 20:40:08','2026-06-09 00:43:04'),
(6,'7953006c-6369-11f1-9d55-9c7bef735b1f','TOILETTES','Mat??riels de toilettes',NULL,NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08');
/*!40000 ALTER TABLE `types_frais` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uniformes`
--

DROP TABLE IF EXISTS `uniformes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uniformes` (
  `id_uniforme` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `libelle` varchar(150) NOT NULL,
  `taille` varchar(20) DEFAULT NULL,
  `prix_unitaire` decimal(12,2) NOT NULL,
  `quantite_stock` int(11) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL,
  `modifie_le` datetime NOT NULL,
  PRIMARY KEY (`id_uniforme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uniformes`
--

LOCK TABLES `uniformes` WRITE;
/*!40000 ALTER TABLE `uniformes` DISABLE KEYS */;
/*!40000 ALTER TABLE `uniformes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateurs` (
  `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_role` int(11) NOT NULL,
  `nom_complet` varchar(150) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `email` (`email`),
  KEY `id_role` (`id_role`),
  CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurs`
--

LOCK TABLES `utilisateurs` WRITE;
/*!40000 ALTER TABLE `utilisateurs` DISABLE KEYS */;
INSERT INTO `utilisateurs` VALUES
(1,'795185c3-6369-11f1-9d55-9c7bef735b1f',1,'Administrateur','admin@vip-school.com','$2y$10$R/nOaYuEh/8kAeVt1Py/wehoLg7.k3C2Y/kOULNQSwPwJBNLr8kie',1,'2026-06-08 20:40:08','2026-06-09 23:42:35',NULL);
/*!40000 ALTER TABLE `utilisateurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `volumes_horaires`
--

DROP TABLE IF EXISTS `volumes_horaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `volumes_horaires` (
  `id_volume` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_enseignement` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL,
  `nb_heures_semaine` decimal(4,1) NOT NULL DEFAULT 1.0,
  `max_heures_jour` int(11) NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT current_timestamp(),
  `modifie_le` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_volume`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_volume_ens` (`id_enseignement`,`id_annee`),
  KEY `id_annee` (`id_annee`),
  KEY `idx_volumes_ens` (`id_enseignement`),
  CONSTRAINT `volumes_horaires_ibfk_1` FOREIGN KEY (`id_enseignement`) REFERENCES `enseignements` (`id_enseignement`) ON DELETE CASCADE,
  CONSTRAINT `volumes_horaires_ibfk_2` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `volumes_horaires`
--

LOCK TABLES `volumes_horaires` WRITE;
/*!40000 ALTER TABLE `volumes_horaires` DISABLE KEYS */;
/*!40000 ALTER TABLE `volumes_horaires` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-06-15  9:57:20
