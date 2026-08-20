-- MySQL dump 10.13  Distrib 9.1.0, for Win64 (x86_64)
--
-- Host: localhost    Database: vip_school
-- ------------------------------------------------------
-- Server version	9.1.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `absences`
--

DROP TABLE IF EXISTS `absences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `absences` (
  `id_absence` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_etudiant` int NOT NULL,
  `date_absence` date NOT NULL,
  `motif` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `justifiee` tinyint(1) NOT NULL DEFAULT '0',
  `type_absence` enum('etudiant','enseignant') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'etudiant',
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_absence`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_etudiant` (`id_etudiant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `annees_scolaires` (
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annees_scolaires`
--

LOCK TABLES `annees_scolaires` WRITE;
/*!40000 ALTER TABLE `annees_scolaires` DISABLE KEYS */;
INSERT INTO `annees_scolaires` VALUES (1,'19bd43cf-ebc6-43b9-95ac-48992c3eace5','2025-2026','2025-09-09','2026-07-12',1,NULL,'2026-06-09 01:06:14','2026-08-20 02:40:37'),(3,'23dd5299-ab73-4674-8d40-f93fd983c843','2019-2020','2019-09-01','2020-07-01',0,NULL,'2026-08-19 18:51:01','2026-08-20 02:40:37');
/*!40000 ALTER TABLE `annees_scolaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assurances`
--

DROP TABLE IF EXISTS `assurances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assurances` (
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
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
  KEY `idx_audit_date` (`date_action`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,'51ade7a6-ae99-442e-b604-6c1632619250',NULL,'update','roles_menus',1,NULL,NULL,'::1','2026-06-29 12:07:26','2026-06-29 12:07:26'),(2,'21eb870b-37dc-4186-abd7-b1338242db18',NULL,'create','sanctions_conduite',1,NULL,'{\"motif\": \"Retard (3 fois)\", \"points_retires\": 5}','::1','2026-08-13 14:52:18','2026-08-13 14:52:18'),(3,'7dd1c383-e2c2-4f57-8fa1-3bdeb6a96caa',NULL,'create','sanctions_conduite',2,NULL,'{\"motif\": \"Test indiscipline\", \"points_retires\": 5}','::1','2026-08-13 14:58:36','2026-08-13 14:58:36'),(4,'bf142927-ceb4-484a-bf81-10296c3b979f',1,'create','sanctions_conduite',3,NULL,'{\"motif\": \"DERANGEMENT\", \"points_retires\": 5}','::1','2026-08-13 15:14:17','2026-08-13 15:14:17'),(5,'6567581f-8829-4b69-b78d-c947c8fa11eb',1,'create','sanctions_conduite',4,NULL,'{\"motif\": \"M,ZXOFI DFWEO CWEOIUFWF\", \"points_retires\": 10}','::1','2026-08-13 15:15:08','2026-08-13 15:15:08'),(6,'b1db142d-f8a0-4014-8eba-bf4c11b2c75f',1,'create','sanctions_conduite',5,NULL,'{\"motif\": \"EEYYTTYTY JUT87 785 87 769\", \"points_retires\": 3}','::1','2026-08-13 15:18:49','2026-08-13 15:18:49'),(7,'dd594cc2-deb9-4a17-a518-dd39e828941f',1,'create','sanctions_conduite',6,NULL,'{\"motif\": \"zxmcnxcm\", \"points_retires\": 5}','::1','2026-08-13 15:59:40','2026-08-13 15:59:40'),(8,'f3bec915-bf22-4ed8-b743-65f1c8ef185d',1,'create','sanctions_conduite',7,NULL,'{\"motif\": \"zm,mz,mx\", \"points_retires\": 5}','::1','2026-08-13 16:00:07','2026-08-13 16:00:07'),(9,'d26f61b7-f6cd-4d55-a0c8-cee118134ca0',1,'create','sanctions_conduite',8,NULL,'{\"motif\": \"mcxcjk\", \"points_retires\": 5}','::1','2026-08-13 16:00:36','2026-08-13 16:00:36'),(10,'0413d762-99ec-4b50-9168-7cde8d0650f7',1,'create','sanctions_conduite',9,NULL,'{\"motif\": \"xmcxjkch\", \"points_retires\": 5}','::1','2026-08-13 16:03:56','2026-08-13 16:03:56'),(11,'0ae397af-6817-470e-8ac4-28a41a481166',1,'create','utilisateurs',12,NULL,'{\"actif\": 1, \"email\": \"zztest@vip.local\", \"id_role\": 6, \"nom_complet\": \"Test ZZT User\", \"mot_de_passe\": \"$2y$10$BwJWIMWeV0hboknzX7VqjuLUu/r1GsWs5Wu3EIVEa37BKxCoteaGC\"}','::1','2026-08-20 10:47:08','2026-08-20 10:47:08'),(12,'b540aedb-f8c3-4724-884d-7ce98d7818c2',1,'create','sanctions_conduite',10,NULL,'{\"motif\": \"TEST ZZT\", \"points_retires\": 1}','::1','2026-08-20 10:47:21','2026-08-20 10:47:21'),(13,'9031709b-167f-425b-b363-1c7f430c4341',1,'create','sanctions_conduite',11,NULL,'{\"motif\": \"TEST ZZT\", \"points_retires\": 1}','::1','2026-08-20 10:47:22','2026-08-20 10:47:22'),(14,'a8d39da2-6951-461d-99b0-f53b4904a030',1,'create','utilisateurs',13,NULL,'{\"actif\": 1, \"email\": \"zztest2@vip.local\", \"id_role\": 6, \"nom_complet\": \"Test ZZT User\", \"mot_de_passe\": \"$2y$10$ilR1Zkp0/MSV9Z.HMx1npuPe9bqWWIKO/gCn7MHNCHC.WIpnnFMpG\"}','::1','2026-08-20 10:54:44','2026-08-20 10:54:44'),(15,'24edb1bf-40b1-469b-8754-a206fda65800',1,'create','sanctions_conduite',12,NULL,'{\"motif\": \"TEST ZZT\", \"points_retires\": 1}','::1','2026-08-20 10:54:50','2026-08-20 10:54:50');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bulletins`
--

DROP TABLE IF EXISTS `bulletins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bulletins` (
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
  KEY `idx_bulletins_decision` (`decision`),
  CONSTRAINT `bulletins_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`),
  CONSTRAINT `bulletins_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE,
  CONSTRAINT `bulletins_ibfk_4` FOREIGN KEY (`id_periode`) REFERENCES `periodes` (`id_periode`),
  CONSTRAINT `fk_bulletins_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bulletins`
--

LOCK TABLES `bulletins` WRITE;
/*!40000 ALTER TABLE `bulletins` DISABLE KEYS */;
INSERT INTO `bulletins` VALUES (1,'ff661c98-a537-4a4f-ba85-6079195bb0b9',2,7,1,1,NULL,NULL,'ajourne','2026-06-18','2026-06-18 15:30:42','2026-06-18 15:30:42',NULL),(2,'f2170613-4d5c-4a55-b207-cca0cf999766',1,1,1,3,28.75,2,'admis','2026-08-13','2026-06-19 13:05:18','2026-08-13 16:18:32',NULL),(3,'5cfc4237-9732-47ea-b057-4808207669a8',2,1,1,3,22.00,3,'admis','2026-08-13','2026-06-19 13:05:18','2026-08-13 16:18:32',NULL),(4,'afbcdc29-0a2a-4f8e-8870-f76ce16451d5',6,1,1,3,78.00,1,'admis','2026-08-13','2026-07-30 17:35:17','2026-08-13 16:18:32',NULL);
/*!40000 ALTER TABLE `bulletins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories_produits`
--

DROP TABLE IF EXISTS `categories_produits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories_produits` (
  `id_categorie` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_categorie`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories_produits`
--

LOCK TABLES `categories_produits` WRITE;
/*!40000 ALTER TABLE `categories_produits` DISABLE KEYS */;
INSERT INTO `categories_produits` VALUES (1,'79541d51-6369-11f1-9d55-9c7bef735b1f','UNIFORME','Uniformes','2026-06-08 20:40:08',NULL),(2,'79542150-6369-11f1-9d55-9c7bef735b1f','LIVRE','Livres et manuels','2026-06-08 20:40:08',NULL),(3,'795422d3-6369-11f1-9d55-9c7bef735b1f','MATERIEL','Matériels scolaires','2026-06-08 20:40:08',NULL),(4,'c10f846a-6fcb-11f1-bfbc-9c7bef735b1f','FOURNITURE','Fournitures diverses','2026-06-24 14:53:53',NULL),(5,'c10f932c-6fcb-11f1-bfbc-9c7bef735b1f','TOILETTE','Produits de toilette','2026-06-24 14:53:53',NULL),(7,'04506720-ecfa-4307-afae-440f0291fa8b','DF','DF','2026-07-30 22:34:58','2026-07-30 20:35:28'),(9,'3c9340d3-e41d-4d60-b921-c9615783045f','SDD','EWE','2026-07-30 22:35:15','2026-07-30 20:35:33'),(12,'fa7300dc-c68e-4848-9767-c819acc6366e','CZZT','Categorie ZZT','2026-08-20 10:54:33',NULL);
/*!40000 ALTER TABLE `categories_produits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ci_sessions`
--

LOCK TABLES `ci_sessions` WRITE;
/*!40000 ALTER TABLE `ci_sessions` DISABLE KEYS */;
INSERT INTO `ci_sessions` VALUES ('ba8ratiu9q59e4nqpp4no4cihhppkhnu','::1',1787212408,_binary '__ci_last_regenerate|i:1787212408;logged_in|b:0;'),('a8qrpvohmc5nqfmghg3gbg2iad8851mh','::1',1787212433,_binary '__ci_last_regenerate|i:1787212433;logged_in|b:0;'),('5pvvbpufu7gor3jk39jbkr2q8jfrvcf2','::1',1787212446,_binary '__ci_last_regenerate|i:1787212446;logged_in|b:0;'),('j1h1ac7cgq63vcu0khu5pgchih8jk6tm','::1',1787212491,_binary '__ci_last_regenerate|i:1787212489;logged_in|b:0;login_attempts_::1|a:2:{s:5:\"count\";i:1;s:4:\"time\";i:1787212491;}sms|s:154:\"<div id=\"message\" class=\"alert alert-danger text-center\">\n                <strong>Oups!</strong> Email incorrect ou compte désactivé.\n            </div>\";__ci_vars|a:1:{s:3:\"sms\";s:3:\"new\";}'),('51dfur6rmri19ie1rbi5v7udhmm1o7sc','::1',1787212506,_binary '__ci_last_regenerate|i:1787212505;logged_in|b:0;login_attempts_::1|a:2:{s:5:\"count\";i:1;s:4:\"time\";i:1787212506;}sms|s:154:\"<div id=\"message\" class=\"alert alert-danger text-center\">\n                <strong>Oups!</strong> Email incorrect ou compte désactivé.\n            </div>\";__ci_vars|a:1:{s:3:\"sms\";s:3:\"new\";}'),('u1pd60pmbrnc7q2oitus16d9b8q3tieh','::1',1787212523,_binary '__ci_last_regenerate|i:1787212523;logged_in|b:0;login_attempts_::1|a:2:{s:5:\"count\";i:1;s:4:\"time\";i:1787212523;}sms|s:154:\"<div id=\"message\" class=\"alert alert-danger text-center\">\n                <strong>Oups!</strong> Email incorrect ou compte désactivé.\n            </div>\";__ci_vars|a:1:{s:3:\"sms\";s:3:\"old\";}'),('p26bpi9vvnmm2l706l16uo4l89ab8r10','::1',1787212537,_binary '__ci_last_regenerate|i:1787212537;logged_in|b:0;'),('pcup1v753th54jbiiji9bjgjbb8sldr8','::1',1787212579,_binary '__ci_last_regenerate|i:1787212578;logged_in|b:0;'),('ff7po3h1o9rpi3hhrqjdm1jobara8kip','::1',1787212597,_binary '__ci_last_regenerate|i:1787212597;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";'),('mvbuqtiesfef1ddit58pb79u8nq6a81c','::1',1787212616,_binary '__ci_last_regenerate|i:1787212615;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('phjr57vrjcpu2kbbsnvc7dp8ru0ah64s','::1',1787212650,_binary '__ci_last_regenerate|i:1787212650;logged_in|b:0;'),('dhj37o5vmhf0epsmf48ao9v66t1ll88a','::1',1787212661,_binary '__ci_last_regenerate|i:1787212659;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('3k9dbcedhv024kcdsnulnfrm6cfj19b9','::1',1787212793,_binary '__ci_last_regenerate|i:1787212790;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('s25ks0sad2c6rcjlponf6qkfglblc652','::1',1787212857,_binary '__ci_last_regenerate|i:1787212855;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('u9fr26dv870k0jdv1rku4mq6754an8n8','::1',1787212924,_binary '__ci_last_regenerate|i:1787212923;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('11p1gm03dpjlgia95mfhtdqvn3g5kvks','::1',1787212962,_binary '__ci_last_regenerate|i:1787212962;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('8lq4q43b926a5hfhiljt7s1o0n71tpl6','::1',1787212995,_binary '__ci_last_regenerate|i:1787212993;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('0hnk5taqim72mnmmh7sj0mjob6r8pk1g','::1',1787213244,_binary '__ci_last_regenerate|i:1787213244;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";'),('hqtp56potmh43q72v94577ao1geopk1n','::1',1787213302,_binary '__ci_last_regenerate|i:1787213265;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('hf4lf22j7lp55vsrm7vkaho89lo5alog','::1',1787213323,_binary '__ci_last_regenerate|i:1787213317;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('rp6tq9iun6dn1nkfb0tlaedn0ougi7av','::1',1787213387,_binary '__ci_last_regenerate|i:1787213386;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('7pfpsfn3f9gnvgbtgla3ndhs3lnc30p2','::1',1787213527,_binary '__ci_last_regenerate|i:1787213525;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('sq4lud3ijs9uqlk11m0a7orct5murqaq','::1',1787213547,_binary '__ci_last_regenerate|i:1787213543;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('jivsesk6ho3gts9enls3g2u1l00p1dsb','::1',1787213636,_binary '__ci_last_regenerate|i:1787213602;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('dcsg9cj6q67mj9cr3n2ssuf5dctsf64c','::1',1787213665,_binary '__ci_last_regenerate|i:1787213663;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('69qfihm2fc815ajs51tdl1dk3hbn3tfs','::1',1787213795,_binary '__ci_last_regenerate|i:1787213792;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('phoi8ql4g7u9eregd4mg8qjlem7j31gs','::1',1787214494,_binary '__ci_last_regenerate|i:1787214493;logged_in|b:0;login_attempts_::1|a:2:{s:5:\"count\";i:1;s:4:\"time\";i:1787214494;}sms|s:169:\"<div id=\"message\" class=\"alert alert-danger text-center\">\n                    <strong>Oups!</strong> Mot de passe incorrect ou compte non activé.\n                </div>\";__ci_vars|a:1:{s:3:\"sms\";s:3:\"new\";}'),('3stpm5tnrs996tefnii2e9uaqbhgq7me','::1',1787214634,_binary '__ci_last_regenerate|i:1787214494;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('r12artmn46amdafnemfgih66j612l2pc','::1',1787214503,_binary '__ci_last_regenerate|i:1787214501;logged_in|b:0;login_attempts_::1|a:2:{s:5:\"count\";i:1;s:4:\"time\";i:1787214502;}'),('4vp93u1fn95jctrhvaiub38hfghmp3mv','::1',1787214503,_binary '__ci_last_regenerate|i:1787214503;logged_in|b:0;'),('ctnmpef4kg206t7fmfc1m6jpfbg0nm7p','::1',1787214503,_binary '__ci_last_regenerate|i:1787214503;logged_in|b:0;'),('vd5qt8cjq2nit0t5oec42322o0hnjjth','::1',1787214506,_binary '__ci_last_regenerate|i:1787214503;logged_in|b:0;reset_email|s:20:\"admin@vip-school.com\";sms|s:246:\"<div id=\"message\" class=\"alert alert-warning text-center\">\n                <strong>Attention!</strong> Le code a été généré mais l\'envoi par email a échoué.\n                Code: <strong>607115</strong> (valable 15 min).\n            </div>\";__ci_vars|a:1:{s:3:\"sms\";s:3:\"new\";}'),('eh90val2eabujmiq3tjic2o3ib8um3bq','::1',1787215402,_binary '__ci_last_regenerate|i:1787215401;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|s:76:\"uploads/profiles/profile_795185c3-6369-11f1-9d55-9c7bef735b1f_1787215402.png\";'),('2d3o5d4otmilpuoaae3rqhpucqqcpm2h','::1',1787215601,_binary '__ci_last_regenerate|i:1787215599;logged_in|b:0;login_attempts_::1|a:2:{s:5:\"count\";i:1;s:4:\"time\";i:1787215600;}sms|s:169:\"<div id=\"message\" class=\"alert alert-danger text-center\">\n                    <strong>Oups!</strong> Mot de passe incorrect ou compte non activé.\n                </div>\";__ci_vars|a:1:{s:3:\"sms\";s:3:\"old\";}'),('688l59fisu1c3f1f9f2lnbj0kcstmep2','::1',1787215704,_binary '__ci_last_regenerate|i:1787215602;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('tu4hbq654jvqvt6ijvrr99okthj4i232','::1',1787215646,_binary '__ci_last_regenerate|i:1787215645;logged_in|b:1;id_utilisateur|s:2:\"12\";uuid|s:36:\"52e257fd-b4f1-406f-9178-9edc81523b43\";email|s:16:\"zztest@vip.local\";nom_complet|s:13:\"Test ZZT User\";user|s:13:\"Test ZZT User\";id_role|s:1:\"6\";role_code|s:10:\"enseignant\";role_libelle|s:10:\"Enseignant\";photo|N;sms|s:151:\"<div id=\"message\" class=\"alert alert-danger text-center\"><strong>Accès refusé!</strong> Vous n\'avez pas la permission d\'accéder à cette page.</div>\";__ci_vars|a:1:{s:3:\"sms\";s:3:\"new\";}'),('hcgau75q5jml9diptmcqpaor7c5qtc3k','::1',1787215646,_binary '__ci_last_regenerate|i:1787215646;logged_in|b:0;login_attempts_::1|a:2:{s:5:\"count\";i:1;s:4:\"time\";i:1787214507;}sms|s:154:\"<div id=\"message\" class=\"alert alert-danger text-center\">\n                <strong>Oups!</strong> Email incorrect ou compte désactivé.\n            </div>\";__ci_vars|a:1:{s:3:\"sms\";s:3:\"old\";}'),('vvr1vpn15bl4vhv7rhtb9ulel7sf9cut','::1',1787215647,_binary '__ci_last_regenerate|i:1787215646;logged_in|b:0;'),('2mt75vs0ljvto81ud17hog83sj27sdc6','::1',1787215651,_binary '__ci_last_regenerate|i:1787215648;logged_in|b:0;'),('me7cjou9686ctljnj3vjs92pc2tfuqe6','::1',1787216067,_binary '__ci_last_regenerate|i:1787216064;logged_in|b:0;login_attempts_::1|a:2:{s:5:\"count\";i:1;s:4:\"time\";i:1787216066;}sms|s:169:\"<div id=\"message\" class=\"alert alert-danger text-center\">\n                    <strong>Oups!</strong> Mot de passe incorrect ou compte non activé.\n                </div>\";__ci_vars|a:1:{s:3:\"sms\";s:3:\"old\";}'),('df0e627gf39odohd2b62c2lid7u3f9vj','::1',1787216100,_binary '__ci_last_regenerate|i:1787216067;logged_in|b:1;id_utilisateur|s:1:\"1\";uuid|s:36:\"795185c3-6369-11f1-9d55-9c7bef735b1f\";email|s:20:\"admin@vip-school.com\";nom_complet|s:14:\"Administrateur\";user|s:14:\"Administrateur\";id_role|s:1:\"1\";role_code|s:5:\"admin\";role_libelle|s:14:\"Administrateur\";photo|N;'),('7dnj3e7j1dm7419sm8q1v2pt7augtgkt','::1',1787216093,_binary '__ci_last_regenerate|i:1787216091;logged_in|b:1;id_utilisateur|s:2:\"13\";uuid|s:36:\"b6b84390-f7ef-4f64-8d74-f8d21453087d\";email|s:17:\"zztest2@vip.local\";nom_complet|s:13:\"Test ZZT User\";user|s:13:\"Test ZZT User\";id_role|s:1:\"6\";role_code|s:10:\"enseignant\";role_libelle|s:10:\"Enseignant\";photo|N;sms|s:151:\"<div id=\"message\" class=\"alert alert-danger text-center\"><strong>Accès refusé!</strong> Vous n\'avez pas la permission d\'accéder à cette page.</div>\";__ci_vars|a:1:{s:3:\"sms\";s:3:\"new\";}'),('l9esm17n5o3ki17obdrd01mcl7sr3tvl','::1',1787216095,_binary '__ci_last_regenerate|i:1787216094;logged_in|b:0;login_attempts_::1|a:2:{s:5:\"count\";i:1;s:4:\"time\";i:1787215652;}'),('vbgm9ihgr9sv1fob6lqp7sk0c70fpos8','::1',1787216096,_binary '__ci_last_regenerate|i:1787216095;logged_in|b:0;'),('oq9pjejf1ubeurvkmmnba0u1l8749a9q','::1',1787216100,_binary '__ci_last_regenerate|i:1787216097;logged_in|b:0;');
/*!40000 ALTER TABLE `ci_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classes` (
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
  KEY `id_section` (`id_section`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`id_section`) REFERENCES `sections` (`id_section`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES (1,'CLS-9d12ba2f52436b1d5c1bbaa969ed',1,'1ECO','1ère ECONOMIQUE','',NULL,'2026-06-09 22:34:08','2026-07-30 11:58:24'),(7,'CLS-f081c492fe715d6d45cd534266e9',1,'2ECO','2ème ECONOMIQUE','',NULL,'2026-06-09 22:37:07','2026-07-30 11:58:02'),(8,'CLS-7aea4e0269878dae578f62e5aa06',2,'3PEDA','3ème PEDAGOGIQUE','',NULL,'2026-06-09 22:37:07','2026-07-30 11:57:34'),(9,'aa6b4b6c-ff2e-4ca4-a34f-ac465d792fc0',2,'1PEDA','1??re PEDAGOGIQUE','',NULL,'2026-07-30 11:59:12','2026-08-20 09:43:39'),(10,'a30d7837-443b-4ced-bbd0-ef05edd0115f',2,'2PEDA','2ème PEDAGOGIQUE','',NULL,'2026-07-30 11:59:42','2026-07-30 11:59:42'),(11,'0bd9b215-7b40-40de-9599-5480c07d9364',2,'4PEDA','4ème PEDAGOGIQUE','',NULL,'2026-07-30 12:00:17','2026-07-30 12:00:17'),(12,'6c9241f6-55ce-45c6-bdab-4d9dcca14cc6',1,'3ECO','3ème ECONOMIQUE','',NULL,'2026-07-30 12:00:54','2026-07-30 12:00:54'),(13,'dfe1f1e0-fbbc-4d3e-80a1-6ae97daea4d5',1,'ECONOMIQUE','6ème heure','','2026-07-30 10:01:58','2026-07-30 12:01:52','2026-07-30 12:01:58'),(14,'663de1e6-a0a7-4cef-b8af-47cc5a84aff5',6,'2M-P','2PF Sc Maths-phys','seconde',NULL,'2026-08-19 19:07:15','2026-08-19 19:07:15');
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commandes` (
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
  KEY `idx_commandes_statut` (`statut`),
  CONSTRAINT `fk_commandes_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commandes`
--

LOCK TABLES `commandes` WRITE;
/*!40000 ALTER TABLE `commandes` DISABLE KEYS */;
INSERT INTO `commandes` VALUES (1,'a7506e81-42e2-4b8a-a848-880066556497',2,'2026-06-18 00:00:00','prete',23.00,'2026-06-18 15:22:07','2026-07-30 22:07:33',NULL),(2,'14c19a47-2009-4d04-a66d-21607584fc83',2,'2026-06-29 00:00:00','en_attente',23.00,'2026-06-29 12:00:24','2026-06-29 12:00:49','2026-06-29 10:00:49'),(3,'ebd2a0a8-8da4-4edb-ae63-04e3fdb11ffb',1,'2026-07-30 00:00:00','distribuee',500.00,'2026-07-31 00:06:55','2026-07-30 22:07:52',NULL);
/*!40000 ALTER TABLE `commandes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commandes_details`
--

DROP TABLE IF EXISTS `commandes_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commandes_details` (
  `id_detail` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_commande` int NOT NULL,
  `id_produit` int NOT NULL,
  `quantite` int NOT NULL DEFAULT '1',
  `prix_unitaire` decimal(12,2) NOT NULL,
  `prix_achat` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_detail`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_commande` (`id_commande`),
  KEY `id_produit` (`id_produit`),
  CONSTRAINT `commandes_details_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE,
  CONSTRAINT `commandes_details_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commandes_details`
--

LOCK TABLES `commandes_details` WRITE;
/*!40000 ALTER TABLE `commandes_details` DISABLE KEYS */;
INSERT INTO `commandes_details` VALUES (1,'066c7634-77a1-4456-8346-136722925f1c',1,1,1,23.00,0.00,'2026-06-18 15:22:08'),(2,'b0d80885-6611-473a-9c1b-56bee2da9b88',2,1,1,23.00,0.00,'2026-06-29 12:00:24'),(3,'74e52530-0306-4fe2-8039-dee1de0a3601',3,2,1,500.00,1000.00,'2026-07-31 00:06:55');
/*!40000 ALTER TABLE `commandes_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contraintes_horaires`
--

DROP TABLE IF EXISTS `contraintes_horaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contraintes_horaires` (
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
  KEY `idx_contraintes_concerne` (`id_concerne`),
  CONSTRAINT `contraintes_horaires_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE,
  CONSTRAINT `contraintes_horaires_ibfk_2` FOREIGN KEY (`id_jour`) REFERENCES `jours_semaine` (`id_jour`) ON DELETE CASCADE
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
-- Table structure for table `departements`
--

DROP TABLE IF EXISTS `departements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departements` (
  `id_departement` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_departement`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `disponibilites_enseignants` (
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
  KEY `idx_dispos_jour` (`id_jour`),
  CONSTRAINT `disponibilites_enseignants_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`) ON DELETE CASCADE,
  CONSTRAINT `disponibilites_enseignants_ibfk_3` FOREIGN KEY (`id_jour`) REFERENCES `jours_semaine` (`id_jour`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disponibilites_enseignants`
--

LOCK TABLES `disponibilites_enseignants` WRITE;
/*!40000 ALTER TABLE `disponibilites_enseignants` DISABLE KEYS */;
INSERT INTO `disponibilites_enseignants` VALUES (2,'ee734c78-40b1-450f-a747-7b289885747d',1,9,4,'disponible',NULL,'2026-06-18 16:31:51','2026-06-18 16:31:51'),(3,'b3caa762-80d2-484f-82a1-6e1fad3cacf8',2,10,5,'disponible',NULL,'2026-06-24 17:15:06','2026-06-24 17:15:06'),(5,'b114761c-6345-41dc-b5cc-e6522c5371a1',3,2,1,'disponible',NULL,'2026-08-20 10:54:42','2026-08-20 10:54:42');
/*!40000 ALTER TABLE `disponibilites_enseignants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `echeances`
--

DROP TABLE IF EXISTS `echeances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `echeances` (
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
-- Table structure for table `email_logs`
--

DROP TABLE IF EXISTS `email_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_logs` (
  `id_email_log` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'succes',
  `error_msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `duration` decimal(10,4) DEFAULT '0.0000',
  `sent_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_email_log`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `status` (`status`),
  KEY `sent_at` (`sent_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_logs`
--

LOCK TABLES `email_logs` WRITE;
/*!40000 ALTER TABLE `email_logs` DISABLE KEYS */;
INSERT INTO `email_logs` VALUES (1,'','admin@vip-school.com','Code de verification - FUTURE VIP SCHOOL','echec','Unable to send email using PHP mail(). Your server might not be configured to send mail using this method.<br /><pre>Date: Thu, 20 Aug 2026 08:28:23 +0000\r\nFrom: &quot;FUTURE VIP SCHOOL&quot; &lt;admin@vip-school.com&gt;\r\nReturn-Path: &lt;admin@vip-school.com&gt;\r\nReply-To: &lt;admin@vip-school.com&gt;\r\nUser-Agent: CodeIgniter\r\nX-Sender: admin@vip-school.com\r\nX-Mailer: CodeIgniter\r\nX-Priority: 3 (Normal)\r\nMessage-ID: &lt;6a86baa7f3a54@vip-school.com&gt;\r\nMime-Version: 1.0\r\nContent-Type: multipar',2.0509,'2026-08-20 08:28:26');
/*!40000 ALTER TABLE `email_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employes`
--

DROP TABLE IF EXISTS `employes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employes` (
  `id_employe` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricule` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_complet` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `poste` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_departement` int DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `salaire` decimal(12,2) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_employe`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_departement` (`id_departement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enseignants` (
  `id_enseignant` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricule` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fullname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `fk_enseignants_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enseignants`
--

LOCK TABLES `enseignants` WRITE;
/*!40000 ALTER TABLE `enseignants` DISABLE KEYS */;
INSERT INTO `enseignants` VALUES (1,'0b27cb51-2f5b-4c53-9619-2be99f02c877','TCH-001','Dupont Jean','M',NULL,'+243800000001','jean.dupont@school.cd',NULL,'CHIMIE',NULL,NULL,NULL,1,NULL,'2026-06-09 23:21:13','2026-07-30 11:08:57',NULL),(2,'3a12292d-18a6-40dc-b752-1286dd9d6ebc','TCH-002','Smith Alice','F',NULL,'+243800000002','alice@school.cd',NULL,NULL,NULL,NULL,NULL,1,NULL,'2026-06-09 23:21:22','2026-07-30 11:08:57',NULL),(3,'767b76f7-787d-4872-a97e-59d5a167688e','TCH-003','Kabongo Pierre Paul','M','0000-00-00','+243800000003','paul@school.cd','','MATHS','Master en Histoire','10 ans','2026-06-18',1,NULL,'2026-06-09 23:21:22','2026-07-30 15:32:40',NULL),(4,'7a4b0718-130c-4c64-969e-1a2699840195','MAT-0004','NDUWAYESU BELYSE','M','2000-03-23','+243800000003','dushimeyesupaulin@gmail.com','Bujumbura','','Master en Histoire','10 ans','2026-07-30',1,5,'2026-07-30 15:07:41','2026-07-30 15:34:33',NULL),(5,'85e973f8-8c20-11f1-bf99-9c7bef735b1f','ENS-001','Jean Dupont','M','1985-04-12','+25761000001','jean.dupont@school.bi','Bujumbura','CHIMIE','Licence en Chimie','8 ans','2026-01-10',1,NULL,'2026-07-30 16:11:13','2026-07-30 16:11:13',NULL),(6,'85e97d08-8c20-11f1-bf99-9c7bef735b1f','ENS-002','Alice Smith','F','1988-07-18','+25761000002','alice.smith@school.bi','Bujumbura','MATHEMATIQUES','Licence en Mathématiques','10 ans','2026-01-10',1,NULL,'2026-07-30 16:11:13','2026-07-30 16:11:13',NULL),(7,'85e97e68-8c20-11f1-bf99-9c7bef735b1f','ENS-003','Pierre Kabongo','M','1983-11-20','+25761000003','pierre.kabongo@school.bi','Bujumbura','FRANCAIS','Master en Lettres','12 ans','2026-01-10',1,NULL,'2026-07-30 16:11:13','2026-07-30 16:11:13',NULL),(8,'85e97f3f-8c20-11f1-bf99-9c7bef735b1f','ENS-004','Nduwayesu Belyse','F','1990-03-23','+25761000004','belyse@school.bi','Bujumbura','KIRUNDI','Licence en Langues','7 ans','2026-01-10',1,NULL,'2026-07-30 16:11:13','2026-07-30 16:11:13',NULL),(9,'85e98025-8c20-11f1-bf99-9c7bef735b1f','ENS-005','Emmanuel Niyonzima','M','1986-05-30','+25761000005','emmanuel@school.bi','Bujumbura','PHYSIQUE','Licence en Physique','9 ans','2026-01-10',1,NULL,'2026-07-30 16:11:13','2026-07-30 16:11:13',NULL),(10,'85e980e3-8c20-11f1-bf99-9c7bef735b1f','ENS-006','Diane Uwimana','F','1991-02-14','+25761000006','diane@school.bi','Bujumbura','BIOLOGIE','Licence en Biologie','6 ans','2026-01-10',1,NULL,'2026-07-30 16:11:13','2026-07-30 16:11:13',NULL),(11,'85e9819a-8c20-11f1-bf99-9c7bef735b1f','ENS-007','Patrick Ntawukuriryayo','M','1984-09-05','+25761000007','patrick@school.bi','Bujumbura','HISTOIRE-GEOGRAPHIE','Master en Histoire','15 ans','2026-01-10',1,NULL,'2026-07-30 16:11:13','2026-07-30 16:11:13',NULL),(12,'85e982a7-8c20-11f1-bf99-9c7bef735b1f','ENS-008','Chantal Mukamana','F','1987-12-11','+25761000008','chantal@school.bi','Bujumbura','ECONOMIE-DROIT','Master en Economie','11 ans','2026-01-10',1,NULL,'2026-07-30 16:11:13','2026-07-30 16:11:13',NULL),(13,'85e9835b-8c20-11f1-bf99-9c7bef735b1f','ENS-009','Eric Nsabimana','M','1982-08-21','+25761000009','eric@school.bi','Bujumbura','COMPTABILITE','Expert Comptable','14 ans','2026-01-10',1,NULL,'2026-07-30 16:11:13','2026-07-30 16:11:13',NULL),(14,'85e9840b-8c20-11f1-bf99-9c7bef735b1f','ENS-010','Claudine Nshimirimana','F','1993-06-16','+25761000010','claudine@school.bi','Bujumbura','INFORMATIQUE-TIC','Ingénieur Informatique','5 ans','2026-01-10',1,NULL,'2026-07-30 16:11:13','2026-07-30 16:11:13',NULL),(15,'85e984b5-8c20-11f1-bf99-9c7bef735b1f','ENS-011','Samuel Ndayizeye','M','1981-10-03','+25761000011','samuel@school.bi','Bujumbura','PHILOSOPHIE-PSYCHOLOGIE','Master en Philosophie','16 ans','2026-01-10',1,NULL,'2026-07-30 16:11:13','2026-07-30 16:11:13',NULL),(16,'85e9855a-8c20-11f1-bf99-9c7bef735b1f','ENS-012','Beatrice Hakizimana','F','1992-01-28','+25761000012','beatrice@school.bi','Bujumbura','ANGLAIS-RELIGION-EPS','Licence en Anglais','7 ans','2026-01-10',1,NULL,'2026-07-30 16:11:13','2026-07-30 16:11:13',NULL);
/*!40000 ALTER TABLE `enseignants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enseignements`
--

DROP TABLE IF EXISTS `enseignements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enseignements` (
  `id_enseignement` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_enseignant` int NOT NULL,
  `id_matiere` int NOT NULL,
  `id_classe` int NOT NULL,
  `id_matiere_classe` int NOT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_enseignement`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_enseignement` (`id_enseignant`,`id_matiere`,`id_classe`),
  UNIQUE KEY `uniq_enseignant_matiere_classe` (`id_enseignant`,`id_matiere_classe`),
  KEY `id_matiere` (`id_matiere`),
  KEY `id_classe` (`id_classe`),
  KEY `fk_ens_matiere_classe` (`id_matiere_classe`),
  CONSTRAINT `fk_ens_enseignant` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`) ON DELETE CASCADE,
  CONSTRAINT `fk_ens_matiere_classe` FOREIGN KEY (`id_matiere_classe`) REFERENCES `matieres_classes` (`id_matiere_classe`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enseignements`
--

LOCK TABLES `enseignements` WRITE;
/*!40000 ALTER TABLE `enseignements` DISABLE KEYS */;
INSERT INTO `enseignements` VALUES (1,'db62a1fb-97d4-11f1-b680-9c7bef735b1f',1,1,1,1,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(2,'db62a2e6-97d4-11f1-b680-9c7bef735b1f',2,2,1,2,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(3,'db62a348-97d4-11f1-b680-9c7bef735b1f',4,3,9,3,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(4,'db62a376-97d4-11f1-b680-9c7bef735b1f',1,1,9,4,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(5,'db62a43e-97d4-11f1-b680-9c7bef735b1f',12,5,11,12,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(6,'db62a466-97d4-11f1-b680-9c7bef735b1f',12,5,10,13,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(7,'db62a490-97d4-11f1-b680-9c7bef735b1f',12,5,9,14,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(8,'db62a4b9-97d4-11f1-b680-9c7bef735b1f',12,5,8,15,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(9,'db62a4e0-97d4-11f1-b680-9c7bef735b1f',12,5,12,16,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(10,'db62a508-97d4-11f1-b680-9c7bef735b1f',12,5,7,17,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(11,'db62a52e-97d4-11f1-b680-9c7bef735b1f',12,5,1,18,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(12,'db62a557-97d4-11f1-b680-9c7bef735b1f',6,7,11,19,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(13,'db62a57f-97d4-11f1-b680-9c7bef735b1f',6,7,10,20,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(14,'db62a5a4-97d4-11f1-b680-9c7bef735b1f',6,7,9,21,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(15,'db62a5cb-97d4-11f1-b680-9c7bef735b1f',6,7,8,22,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(16,'db62a5f3-97d4-11f1-b680-9c7bef735b1f',6,7,12,23,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(17,'db62a61c-97d4-11f1-b680-9c7bef735b1f',6,7,7,24,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(18,'db62a644-97d4-11f1-b680-9c7bef735b1f',6,7,1,25,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(19,'db62a66a-97d4-11f1-b680-9c7bef735b1f',1,1,11,26,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(20,'db62a692-97d4-11f1-b680-9c7bef735b1f',1,1,10,27,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(21,'db62a6dd-97d4-11f1-b680-9c7bef735b1f',1,1,8,28,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(22,'db62a705-97d4-11f1-b680-9c7bef735b1f',1,1,12,29,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(23,'db62a72c-97d4-11f1-b680-9c7bef735b1f',1,1,7,30,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(24,'db62a8f7-97d4-11f1-b680-9c7bef735b1f',8,12,11,52,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(25,'db62a91f-97d4-11f1-b680-9c7bef735b1f',8,12,10,53,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(26,'db62a946-97d4-11f1-b680-9c7bef735b1f',8,12,9,54,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(27,'db62a96d-97d4-11f1-b680-9c7bef735b1f',8,12,8,55,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(28,'db62a994-97d4-11f1-b680-9c7bef735b1f',8,12,12,56,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(29,'db62a9bb-97d4-11f1-b680-9c7bef735b1f',8,12,7,57,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(30,'db62a9e1-97d4-11f1-b680-9c7bef735b1f',8,12,1,58,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(31,'db62aa92-97d4-11f1-b680-9c7bef735b1f',8,11,11,66,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(32,'db62aab8-97d4-11f1-b680-9c7bef735b1f',8,11,10,67,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(33,'db62aade-97d4-11f1-b680-9c7bef735b1f',8,11,9,68,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(34,'db62ab04-97d4-11f1-b680-9c7bef735b1f',8,11,8,69,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(35,'db62ab2a-97d4-11f1-b680-9c7bef735b1f',8,11,12,70,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(36,'db62ab50-97d4-11f1-b680-9c7bef735b1f',8,11,7,71,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(37,'db62ab77-97d4-11f1-b680-9c7bef735b1f',8,11,1,72,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(38,'db62ac29-97d4-11f1-b680-9c7bef735b1f',12,20,11,80,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(39,'db62ac4f-97d4-11f1-b680-9c7bef735b1f',12,20,10,81,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(40,'db62ac77-97d4-11f1-b680-9c7bef735b1f',12,20,9,82,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(41,'db62ac9d-97d4-11f1-b680-9c7bef735b1f',12,20,8,83,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(42,'db62acc4-97d4-11f1-b680-9c7bef735b1f',12,20,12,84,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(43,'db62ace9-97d4-11f1-b680-9c7bef735b1f',12,20,7,85,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(44,'db62ad0f-97d4-11f1-b680-9c7bef735b1f',12,20,1,86,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(45,'db62ad34-97d4-11f1-b680-9c7bef735b1f',3,4,11,87,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(46,'db62ad5b-97d4-11f1-b680-9c7bef735b1f',3,4,10,88,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(47,'db62ad81-97d4-11f1-b680-9c7bef735b1f',3,4,9,89,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(48,'db62adce-97d4-11f1-b680-9c7bef735b1f',3,4,8,90,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(49,'db62adf6-97d4-11f1-b680-9c7bef735b1f',3,4,12,91,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(50,'db62ae1d-97d4-11f1-b680-9c7bef735b1f',3,4,7,92,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(51,'db62ae43-97d4-11f1-b680-9c7bef735b1f',3,4,1,93,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(52,'db62ae68-97d4-11f1-b680-9c7bef735b1f',7,10,11,94,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(53,'db62ae8e-97d4-11f1-b680-9c7bef735b1f',7,10,10,95,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(54,'db62aeb5-97d4-11f1-b680-9c7bef735b1f',7,10,9,96,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(55,'db62aedb-97d4-11f1-b680-9c7bef735b1f',7,10,8,97,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(56,'db62b138-97d4-11f1-b680-9c7bef735b1f',7,10,12,98,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(57,'db62b182-97d4-11f1-b680-9c7bef735b1f',7,10,7,99,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(58,'db62b1ab-97d4-11f1-b680-9c7bef735b1f',7,10,1,100,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(59,'db62b261-97d4-11f1-b680-9c7bef735b1f',7,9,11,108,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(60,'db62b287-97d4-11f1-b680-9c7bef735b1f',7,9,10,109,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(61,'db62b2ad-97d4-11f1-b680-9c7bef735b1f',7,9,9,110,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(62,'db62b2d2-97d4-11f1-b680-9c7bef735b1f',7,9,8,111,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(63,'db62b2f7-97d4-11f1-b680-9c7bef735b1f',7,9,12,112,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(64,'db62b31c-97d4-11f1-b680-9c7bef735b1f',7,9,7,113,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(65,'db62b341-97d4-11f1-b680-9c7bef735b1f',7,9,1,114,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(66,'db62b38e-97d4-11f1-b680-9c7bef735b1f',10,16,11,115,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(67,'db62b3b5-97d4-11f1-b680-9c7bef735b1f',10,16,10,116,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(68,'db62b3dc-97d4-11f1-b680-9c7bef735b1f',10,16,9,117,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(69,'db62b401-97d4-11f1-b680-9c7bef735b1f',10,16,8,118,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(70,'db62b441-97d4-11f1-b680-9c7bef735b1f',10,16,12,119,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(71,'db62b469-97d4-11f1-b680-9c7bef735b1f',10,16,7,120,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(72,'db62b48f-97d4-11f1-b680-9c7bef735b1f',10,16,1,121,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(73,'db62b4b4-97d4-11f1-b680-9c7bef735b1f',4,3,11,122,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(74,'db62b4d9-97d4-11f1-b680-9c7bef735b1f',4,3,10,123,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(75,'db62b534-97d4-11f1-b680-9c7bef735b1f',4,3,8,124,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(76,'db62b55c-97d4-11f1-b680-9c7bef735b1f',4,3,12,125,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(77,'db62b582-97d4-11f1-b680-9c7bef735b1f',4,3,7,126,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(78,'db62b5a8-97d4-11f1-b680-9c7bef735b1f',4,3,1,127,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(79,'db62b65a-97d4-11f1-b680-9c7bef735b1f',2,2,11,135,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(80,'db62b680-97d4-11f1-b680-9c7bef735b1f',2,2,10,136,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(81,'db62b6a7-97d4-11f1-b680-9c7bef735b1f',2,2,9,137,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(82,'db62b6cc-97d4-11f1-b680-9c7bef735b1f',2,2,8,138,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(83,'db62b6f1-97d4-11f1-b680-9c7bef735b1f',2,2,12,139,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(84,'db62b717-97d4-11f1-b680-9c7bef735b1f',2,2,7,140,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(85,'db62b73c-97d4-11f1-b680-9c7bef735b1f',11,17,11,141,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(86,'db62b761-97d4-11f1-b680-9c7bef735b1f',11,17,10,142,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(87,'db62b786-97d4-11f1-b680-9c7bef735b1f',11,17,9,143,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(88,'db62b7ab-97d4-11f1-b680-9c7bef735b1f',11,17,8,144,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(89,'db62b7d0-97d4-11f1-b680-9c7bef735b1f',11,17,12,145,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(90,'db62b7f5-97d4-11f1-b680-9c7bef735b1f',11,17,7,146,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(91,'db62b819-97d4-11f1-b680-9c7bef735b1f',11,17,1,147,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(92,'db62b83e-97d4-11f1-b680-9c7bef735b1f',5,6,11,148,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(93,'db62b863-97d4-11f1-b680-9c7bef735b1f',5,6,10,149,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(94,'db62b8af-97d4-11f1-b680-9c7bef735b1f',5,6,9,150,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(95,'db62b8d6-97d4-11f1-b680-9c7bef735b1f',5,6,8,151,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(96,'db62b8fe-97d4-11f1-b680-9c7bef735b1f',5,6,12,152,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(97,'db62b924-97d4-11f1-b680-9c7bef735b1f',5,6,7,153,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(98,'db62b949-97d4-11f1-b680-9c7bef735b1f',5,6,1,154,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(99,'db62b96f-97d4-11f1-b680-9c7bef735b1f',11,18,11,155,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(100,'db62b995-97d4-11f1-b680-9c7bef735b1f',11,18,10,156,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(101,'db62b9bb-97d4-11f1-b680-9c7bef735b1f',11,18,9,157,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(102,'db62ba02-97d4-11f1-b680-9c7bef735b1f',11,18,8,158,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(103,'db62ba2a-97d4-11f1-b680-9c7bef735b1f',11,18,12,159,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(104,'db62ba51-97d4-11f1-b680-9c7bef735b1f',11,18,7,160,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(105,'db62ba76-97d4-11f1-b680-9c7bef735b1f',11,18,1,161,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(106,'db62ba9a-97d4-11f1-b680-9c7bef735b1f',12,19,11,162,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(107,'db62babf-97d4-11f1-b680-9c7bef735b1f',12,19,10,163,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(108,'db62bae4-97d4-11f1-b680-9c7bef735b1f',12,19,9,164,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(109,'db62bb09-97d4-11f1-b680-9c7bef735b1f',12,19,8,165,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(110,'db62bb2e-97d4-11f1-b680-9c7bef735b1f',12,19,12,166,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(111,'db62bb52-97d4-11f1-b680-9c7bef735b1f',12,19,7,167,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(112,'db62bb77-97d4-11f1-b680-9c7bef735b1f',12,19,1,168,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(113,'db62bd32-97d4-11f1-b680-9c7bef735b1f',10,23,11,190,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(114,'db62bd57-97d4-11f1-b680-9c7bef735b1f',10,23,10,191,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(115,'db62bd7d-97d4-11f1-b680-9c7bef735b1f',10,23,9,192,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(116,'db62bda3-97d4-11f1-b680-9c7bef735b1f',10,23,8,193,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(117,'db62bdca-97d4-11f1-b680-9c7bef735b1f',10,23,12,194,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(118,'db62bdef-97d4-11f1-b680-9c7bef735b1f',10,23,7,195,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(119,'db62be13-97d4-11f1-b680-9c7bef735b1f',10,23,1,196,'2026-08-14 13:39:49',NULL,'2026-08-14 13:39:49'),(121,'56f3043c-d66d-41e8-897f-1ae589c29f6c',3,1,1,1,'2026-08-20 10:54:41',NULL,'2026-08-20 10:54:41');
/*!40000 ALTER TABLE `enseignements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etudiants`
--

DROP TABLE IF EXISTS `etudiants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `etudiants` (
  `id_etudiant` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricule` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fullname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_ordre` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `pere_nom` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pere_telephone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pere_profession` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pere_adresse` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mere_nom` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mere_telephone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mere_profession` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mere_adresse` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  KEY `idx_etudiants_date_naiss` (`date_naissance`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `fk_etudiants_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etudiants`
--

LOCK TABLES `etudiants` WRITE;
/*!40000 ALTER TABLE `etudiants` DISABLE KEYS */;
INSERT INTO `etudiants` VALUES (1,'ETU-fbf5c8199f0399b95f70fc3f1dc6','MAT-0001','Dupont Jean','2','2005-01-15',NULL,'M','Kinshasa, Commune de la Gombe',NULL,'+243811000001','jean.dupont@email.com','Dupont Alphonse','+243811000011','Ingenieur',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,1,'2026-06-09 22:34:08','2026-07-30 11:20:22',NULL),(2,'ETU-507ae6d117cf82f9949f527d98ce','MAT-0002','Mukendi Marie','3','2006-02-15',NULL,'F','Kinshasa, Commune de la Gombe',NULL,'+243811000002','marie.mukendi@email.com','Mukendi Joseph','+243811000021','Commercant',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,1,'2026-06-09 22:34:08','2026-07-30 11:20:22',NULL),(3,'ETU-58cfcd3f88c68f0b7ace58662ac6','MAT-0003','Kabongo Pierre','2','2007-03-15',NULL,'M','Kinshasa, Commune de la Gombe',NULL,'+243811000003','pierre.kabongo@email.com','Kabongo Michel','+243811000033','Avocat','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,1,'2026-06-09 22:34:08','2026-07-30 11:20:22',NULL),(4,'ETU-80c808283af8dcb819ea0c92849c1e8e','MAT-0004','Ilunga Esther','1','2008-04-15',NULL,'F','Kinshasa, Ngaliema','','+243811000004','esther.ilunga@email.com','Ilunga Paul','+243811000043','Banquier','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,1,'2026-06-09 22:37:07','2026-07-30 11:20:22',NULL),(5,'ETU-6e995fb45570811845ae886a608c975f','MAT-0005','Tshimanga David','5','2009-05-15',NULL,'M','Kinshasa, Lemba',NULL,'+243811000005','david.tshimanga@email.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,1,'2026-06-09 22:37:08','2026-07-30 11:20:22','2026-06-09 20:50:57'),(6,'0879edec-9903-4c9c-a0b2-d1bfd72a8d80','26/0001','Administateur Esther','1','2003-06-24',NULL,'M','NBN',NULL,'+257 79 123 456','paul@gmail.com','Ilunga Paul','+243811000043','Banquier','MNKJ','','','','','','','','',NULL,NULL,'assets/uploads/students/a6711edd7dd3a057907abadfca630abe.png',2,1,'2026-06-24 15:07:56','2026-08-14 11:36:31',NULL),(7,'88c351c2-951f-4af4-809f-b36536d7173c','26/0002','NDUWAYESU BELYSE Niyondiko',NULL,NULL,NULL,'M','Bujumbura',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'NDUWAYESU BELYSE','+243811000051','Agriculteur','Bujumbura','NDUWAYESU BELYSE','+243811000042','Menagere','Bujumbura',NULL,NULL,'assets/uploads/students/000a9614c7b0874b703c47af753c2fba.png',3,1,'2026-07-27 22:52:09','2026-07-30 11:20:22','2026-07-27 20:56:29'),(8,'c5788d42-ee44-40ce-97a4-8776c797055b','26/0003','NDUWAYESU BELYSE','1','2000-03-21',NULL,'F','Bujumbura',NULL,'+243811000004','adhavuga@gmail.com',NULL,NULL,NULL,NULL,'NDUWAYESU BELYSE','+243811000051','Medecin','Bujumbura','NDUWAYESU BELYSE','+243811000032','Infirmiere','Bujumbura',NULL,NULL,'assets/uploads/students/6b0f38642ce5b190bae9c6f7af837bc0.png',4,1,'2026-07-30 12:09:53','2026-07-30 12:09:53',NULL);
/*!40000 ALTER TABLE `etudiants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluations`
--

DROP TABLE IF EXISTS `evaluations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `evaluations` (
  `id_evaluation` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_periode` int NOT NULL,
  `libelle` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_classe` int NOT NULL,
  `id_matiere` int NOT NULL,
  `id_annee` int NOT NULL,
  `date_eval` date NOT NULL,
  `type` enum('interrogation','devoir','controle','composition','examen','tp','projet','participation','autre') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'devoir',
  `coefficient` decimal(10,2) NOT NULL DEFAULT '1.00',
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
  KEY `idx_evaluations_type` (`type`),
  CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`id_periode`) REFERENCES `periodes` (`id_periode`),
  CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`),
  CONSTRAINT `evaluations_ibfk_3` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`),
  CONSTRAINT `evaluations_ibfk_4` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluations`
--

LOCK TABLES `evaluations` WRITE;
/*!40000 ALTER TABLE `evaluations` DISABLE KEYS */;
INSERT INTO `evaluations` VALUES (3,'a4b8fcf7-7bef-41f5-a087-3c4dd7c55421',1,'EYW',7,1,1,'2026-06-18','devoir',1.00,20.0,'2026-06-18 15:41:45','2026-07-27 23:46:53','2026-07-27 21:46:53'),(4,'740eca57-b3f3-48b5-91bf-0a9a34f7480e',3,'interro1',1,1,1,'2026-06-19','interrogation',1.00,20.0,'2026-06-19 10:48:36','2026-06-19 10:48:36',NULL),(5,'6a499668-078a-43b9-a2d6-2494904e5c52',3,'interro 2',1,1,1,'2026-06-19','interrogation',1.00,20.0,'2026-06-19 10:49:18','2026-06-19 10:49:18',NULL),(6,'1f79880e-4f22-4d82-8805-be3d8ea0fdec',3,'interro 2',1,1,1,'2026-06-19','interrogation',1.00,20.0,'2026-06-19 10:49:19','2026-06-19 10:50:56','2026-06-19 08:50:56'),(7,'9a0f2e79-d241-4318-b406-682e9b2f38ee',3,'intero3',1,1,1,'2026-06-19','interrogation',1.00,20.0,'2026-06-19 10:51:26','2026-06-19 10:51:26',NULL),(8,'33f9cf2d-8abf-4a9f-b167-eb6507fc0e14',3,'interro 4',1,1,1,'2026-06-19','interrogation',1.00,60.0,'2026-06-19 11:06:30','2026-06-19 11:06:30',NULL),(11,'b37ee013-2e3e-4f82-a872-09743e8fd327',3,'interro1',9,5,1,'2026-07-30','devoir',1.00,20.0,'2026-07-30 23:27:42','2026-07-30 23:27:42',NULL),(12,'5209b84c-f4b5-49c5-82fe-9d5c43ae55e6',3,'interro',9,5,1,'2026-07-30','devoir',1.00,20.0,'2026-07-30 23:37:25','2026-07-30 23:37:25',NULL);
/*!40000 ALTER TABLE `evaluations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evenements`
--

DROP TABLE IF EXISTS `evenements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `evenements` (
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
  KEY `idx_utilisateur_createur` (`id_utilisateur_createur`),
  CONSTRAINT `fk_evenements_utilisateur` FOREIGN KEY (`id_utilisateur_createur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evenements`
--

LOCK TABLES `evenements` WRITE;
/*!40000 ALTER TABLE `evenements` DISABLE KEYS */;
INSERT INTO `evenements` VALUES (3,'ea086783-4c58-431b-9581-bf4e0d9872ef','Evenement ZZT',NULL,'2026-09-01 08:00:00',NULL,NULL,'scolaire','#25A194','planifie',1,NULL,'2026-08-20 10:54:38','2026-08-20 10:54:38');
/*!40000 ALTER TABLE `evenements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `frais`
--

DROP TABLE IF EXISTS `frais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `frais` (
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
  KEY `idx_frais_id_type_frais` (`id_type_frais`),
  CONSTRAINT `frais_ibfk_1` FOREIGN KEY (`id_type_frais`) REFERENCES `types_frais` (`id_type_frais`),
  CONSTRAINT `frais_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`),
  CONSTRAINT `frais_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frais`
--

LOCK TABLES `frais` WRITE;
/*!40000 ALTER TABLE `frais` DISABLE KEYS */;
INSERT INTO `frais` VALUES (4,'f5b85aea-7e0f-4fb9-8bb9-1f7b619e8e9e',4,7,1,65478.00,NULL,'2026-06-24 16:53:31','2026-06-24 16:53:31',NULL),(5,'17902c8a-4ac9-489c-b4a2-5e05743aa6ea',2,1,1,38983.00,NULL,'2026-06-24 16:58:24','2026-06-24 16:58:24',NULL),(7,'35e1f47a-18e4-41bd-8522-036a2f0bd852',1,1,1,120.00,NULL,'2026-08-20 10:54:41','2026-08-20 10:54:41',NULL);
/*!40000 ALTER TABLE `frais` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horaires`
--

DROP TABLE IF EXISTS `horaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horaires` (
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
  KEY `idx_horaires_generation` (`id_generation`),
  KEY `idx_horaires_enseignant` (`id_enseignant`),
  KEY `idx_horaires_classe` (`id_classe`),
  KEY `idx_horaires_jour` (`id_jour`),
  KEY `idx_matiere` (`id_matiere`),
  CONSTRAINT `fk_horaires_matiere` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `horaires_ibfk_1` FOREIGN KEY (`id_generation`) REFERENCES `horaires_generations` (`id_generation`) ON DELETE CASCADE,
  CONSTRAINT `horaires_ibfk_3` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  CONSTRAINT `horaires_ibfk_4` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`),
  CONSTRAINT `horaires_ibfk_6` FOREIGN KEY (`id_jour`) REFERENCES `jours_semaine` (`id_jour`)
) ENGINE=InnoDB AUTO_INCREMENT=917 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horaires`
--

LOCK TABLES `horaires` WRITE;
/*!40000 ALTER TABLE `horaires` DISABLE KEYS */;
INSERT INTO `horaires` VALUES (882,'d87ef01f-64f2-4efe-a3df-1dd2047d0232',1,4,1,1,9,1,1,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(883,'01b8e6b4-5803-4f6f-aa91-27e7f75daa6b',1,4,1,1,9,2,1,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(884,'1511c1bd-9682-4a38-bacd-9d97e6d75c7d',1,4,1,1,9,3,1,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(885,'c9ad926e-92dd-4c3b-8a4f-9c3d1cac267c',1,4,1,1,9,4,1,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(886,'17774423-3e3f-4ad5-9682-e9d35bce6e12',1,4,1,1,9,1,2,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(887,'846f15ee-a506-403a-9852-8e044376b05b',1,4,1,1,9,2,2,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(888,'530e1eeb-2623-47fe-a34c-85a13acc35b4',1,4,1,1,9,3,2,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(889,'39064fb5-7994-462c-8a76-6bb8711ecbb6',1,4,1,1,9,4,2,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(890,'6fc9de24-eb7f-4f1e-a75c-b18685d43be4',1,4,1,1,9,1,3,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(891,'82157917-48b2-466c-a573-d478b9889a3e',1,4,1,1,9,2,3,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(892,'5562c87f-93ea-4918-9d35-99434590ee7f',1,4,1,1,9,3,3,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(893,'8d205555-cda8-4505-b45e-b8863112cf0c',1,4,1,1,9,4,3,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(894,'8fd75527-d5ba-4cfc-b0e9-de73ce9bda3f',1,2,2,2,1,1,1,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(895,'ad8897fd-27ee-4e56-913f-e95559da2581',1,2,2,2,1,2,1,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(896,'ff82aa22-a941-4b72-a771-2341cb2d8dd5',1,2,2,2,1,3,1,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(897,'9baf8f5f-f817-4da7-9054-3c9d03b86986',1,2,2,2,1,1,2,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(898,'757c182f-c90a-46bf-8e5e-235b6d1f1b93',1,2,2,2,1,2,2,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(899,'b6b61357-f818-4ca9-ae64-bd5b38fa345d',1,2,2,2,1,3,2,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(900,'cc950280-3873-4e9f-9f89-bb887f1503a5',1,2,2,2,1,1,3,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(901,'8207537e-7b9e-4ae1-be57-958f0bc17ac2',1,2,2,2,1,2,3,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(902,'3f4046d1-d335-4c0b-9c01-c4e92eb28118',1,2,2,2,1,3,3,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(903,'0494d4c0-ef8c-4640-a991-34eeba150476',1,3,3,4,9,5,1,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(904,'1a0a27cc-4a13-406b-bfce-3950482ed76b',1,3,3,4,9,6,1,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(905,'ff0028b8-e776-4a36-96fc-30379cb18824',1,3,3,4,9,7,1,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(906,'57e583f3-d237-4f62-a260-a77d4ddea425',1,3,3,4,9,5,2,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(907,'2796b5a6-a799-4fc5-9a99-7929e33df465',1,3,3,4,9,6,2,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(908,'7ab288e5-e41f-418d-98ca-75bc657c948b',1,3,3,4,9,7,2,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(909,'b40041a1-d6a9-472f-94bc-e1157b470122',1,3,3,4,9,5,3,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(910,'afa54f76-9347-4f8b-9062-4d0d78e784af',1,3,3,4,9,6,3,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(911,'a4788d90-c6b1-48fe-847a-5232bb5506ee',1,3,3,4,9,7,3,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(912,'3185f31c-3935-40ce-94c6-369867f585d2',1,1,1,1,1,5,1,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(913,'57d7c3a9-2899-4e93-8fbc-4e136ab39e6c',1,1,1,1,1,6,1,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(914,'2b3819c4-94ee-4e6a-971e-70c66dee604b',1,1,1,1,1,7,1,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(915,'f83bcde5-cead-4f2d-834c-b954fc0bcb73',1,1,1,1,1,5,2,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46'),(916,'0c92dc43-47f0-4c31-809b-860e8ce9baf1',1,1,1,1,1,6,2,NULL,'2026-08-20 10:54:46','2026-08-20 10:54:46');
/*!40000 ALTER TABLE `horaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horaires_generations`
--

DROP TABLE IF EXISTS `horaires_generations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horaires_generations` (
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
  KEY `id_annee` (`id_annee`),
  CONSTRAINT `horaires_generations_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horaires_generations`
--

LOCK TABLES `horaires_generations` WRITE;
/*!40000 ALTER TABLE `horaires_generations` DISABLE KEYS */;
INSERT INTO `horaires_generations` VALUES (1,'7879a769-e247-49d2-ab6d-4c911f8a419f','Emploi du temps 2026',1,'2026-06-18 15:51:55','brouillon',NULL,'2026-06-18 15:51:55','2026-06-18 15:51:55');
/*!40000 ALTER TABLE `horaires_generations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inscriptions`
--

DROP TABLE IF EXISTS `inscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inscriptions` (
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
  KEY `idx_inscriptions_annee` (`id_annee`),
  CONSTRAINT `fk_inscriptions_classe` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`) ON DELETE CASCADE,
  CONSTRAINT `inscriptions_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  CONSTRAINT `inscriptions_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inscriptions`
--

LOCK TABLES `inscriptions` WRITE;
/*!40000 ALTER TABLE `inscriptions` DISABLE KEYS */;
INSERT INTO `inscriptions` VALUES (1,'INS-9581bdfdbd88841ab41936625e78',1,1,1,1,'2026-06-09','actif','2026-06-09 22:34:08','2026-06-09 22:34:08',NULL),(2,'INS-aceffa5c260958ff581754ee7a30',2,1,1,1,'2026-06-09','actif','2026-06-09 22:34:08','2026-06-09 22:34:08',NULL),(3,'INS-ea83387dfef3e4d786590dc602f414b8',3,7,1,1,'2026-06-09','actif','2026-06-09 22:37:07','2026-06-09 22:37:07',NULL),(4,'INS-ea21717fdfd51db18b2d6cea11d97ec6',4,7,1,1,'2026-06-09','actif','2026-06-09 22:37:07','2026-06-09 22:37:07',NULL),(5,'INS-2f918a2a08905616d850f35d4d18a2fa',5,8,2,1,'2026-06-09','actif','2026-06-09 22:37:08','2026-06-09 22:50:57','2026-06-09 20:50:57'),(6,'1907c269-424f-4c9a-8a3c-c08acc086b18',6,1,1,1,'2026-06-24','actif','2026-06-24 15:07:56','2026-06-24 15:07:56',NULL),(7,'2e08759d-2487-4b2c-9218-5ef0edfb6b2d',8,9,2,1,'2026-07-30','actif','2026-07-30 12:09:53','2026-07-30 12:09:53',NULL);
/*!40000 ALTER TABLE `inscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jours_semaine`
--

DROP TABLE IF EXISTS `jours_semaine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jours_semaine` (
  `id_jour` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `ordre` int NOT NULL DEFAULT '0',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_jour`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jours_semaine`
--

LOCK TABLES `jours_semaine` WRITE;
/*!40000 ALTER TABLE `jours_semaine` DISABLE KEYS */;
INSERT INTO `jours_semaine` VALUES (1,'4434f68b-636b-11f1-9d55-9c7bef735b1f','lundi','Lundi',1,1,'2026-06-08 20:52:58',NULL),(2,'4434facd-636b-11f1-9d55-9c7bef735b1f','mardi','Mardi',1,2,'2026-06-08 20:52:58',NULL),(3,'4434fd20-636b-11f1-9d55-9c7bef735b1f','mercredi','Mercredi',1,3,'2026-06-08 20:52:58',NULL),(4,'4434fe86-636b-11f1-9d55-9c7bef735b1f','jeudi','Jeudi',1,4,'2026-06-08 20:52:58',NULL),(5,'4434ffbe-636b-11f1-9d55-9c7bef735b1f','vendredi','Vendredi',1,5,'2026-06-08 20:52:58',NULL),(6,'443500f8-636b-11f1-9d55-9c7bef735b1f','samedi','Samedi',1,6,'2026-06-08 20:52:58',NULL),(16,'76dfb19e-26a5-4b50-81b1-b75644bcc57f','JZZT','Jour ZZT',1,90,'2026-08-20 10:54:34',NULL);
/*!40000 ALTER TABLE `jours_semaine` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matieres`
--

DROP TABLE IF EXISTS `matieres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matieres` (
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
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matieres`
--

LOCK TABLES `matieres` WRITE;
/*!40000 ALTER TABLE `matieres` DISABLE KEYS */;
INSERT INTO `matieres` VALUES (1,'35b5ec42-a82e-4904-9404-c059b7c4762e','CHIM','CHIMIE','2026-06-09 01:18:38','2026-07-30 16:27:55',NULL),(2,'73e09416-fd81-4dd2-9f6d-9f04f0d5c5ec','MATH','MATHS','2026-06-16 14:57:50','2026-07-30 16:27:55',NULL),(3,'91201221-cd0d-4f99-86af-1649672f91ad','KIR','KIR','2026-07-30 14:05:11','2026-07-30 16:27:55',NULL),(4,'c173d647-8c1f-11f1-bf99-9c7bef735b1f','FRA','FRANCAIS','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(5,'c173df97-8c1f-11f1-bf99-9c7bef735b1f','ANG','ANGLAIS','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(6,'c173e0d0-8c1f-11f1-bf99-9c7bef735b1f','PHYS','PHYSIQUE','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(7,'c173e1d0-8c1f-11f1-bf99-9c7bef735b1f','BIO','BIOLOGIE','2026-07-30 16:05:44','2026-07-30 16:26:26',NULL),(8,'c173e29a-8c1f-11f1-bf99-9c7bef735b1f','SC.T','SCIENCES DE LA TERRE','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(9,'c1740a39-8c1f-11f1-bf99-9c7bef735b1f','HIST','HISTOIRE','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(10,'c1740b69-8c1f-11f1-bf99-9c7bef735b1f','GEO','GEOGRAPHIE','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(11,'c1740c1c-8c1f-11f1-bf99-9c7bef735b1f','ECO','ECONOMIE','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(12,'c1740cc5-8c1f-11f1-bf99-9c7bef735b1f','DROI','DROIT','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(13,'c1740d94-8c1f-11f1-bf99-9c7bef735b1f','COGE','COMPTABILITE GENERALE','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(14,'c1740fae-8c1f-11f1-bf99-9c7bef735b1f','COMA','COMPTABILITE ANALYTIQUE','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(15,'c1741072-8c1f-11f1-bf99-9c7bef735b1f','DSC','DSC','2026-07-30 16:05:44','2026-08-19 19:15:13','2026-08-19 17:15:13'),(16,'c1741146-8c1f-11f1-bf99-9c7bef735b1f','INFO','INFORMATIQUE','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(17,'c17411f6-8c1f-11f1-bf99-9c7bef735b1f','PHILO','PHILOSOPHIE','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(18,'c17412c4-8c1f-11f1-bf99-9c7bef735b1f','PSY','PSYCHOLOGIE','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(19,'c174139b-8c1f-11f1-bf99-9c7bef735b1f','REL','RELIGION','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(20,'c1741453-8c1f-11f1-bf99-9c7bef735b1f','EPS','EDUCATION PHYSIQUE ET SPORTIVE','2026-07-30 16:05:44','2026-07-30 16:05:44',NULL),(21,'c1741501-8c1f-11f1-bf99-9c7bef735b1f','DDE','D.D.E','2026-07-30 16:05:44','2026-07-30 16:05:44',NULL),(22,'c17415ae-8c1f-11f1-bf99-9c7bef735b1f','ENV','ENVIRONNEMENT','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(23,'c1741660-8c1f-11f1-bf99-9c7bef735b1f','TIC','TECHNOLOGIES DE L\'INFORMATION ET DE LA COMMUNICATION','2026-07-30 16:05:44','2026-07-30 16:05:44',NULL),(24,'c174170e-8c1f-11f1-bf99-9c7bef735b1f','LAT','LATIN','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(25,'c17417b6-8c1f-11f1-bf99-9c7bef735b1f','GRE','GREC','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(26,'c174193c-8c1f-11f1-bf99-9c7bef735b1f','SOC','SOCIOLOGIE','2026-07-30 16:05:44','2026-07-30 16:27:55',NULL),(27,'c1741a10-8c1f-11f1-bf99-9c7bef735b1f','STAGE','STAGE','2026-07-30 16:05:44','2026-07-30 16:05:44',NULL),(28,'c1741ab2-8c1f-11f1-bf99-9c7bef735b1f','AGRI','AGRICULTURE','2026-07-30 16:05:44','2026-07-30 16:26:10',NULL),(34,'044a570b-0b9f-43f8-aefb-f1468043a125','FPH','FPH','2026-08-19 19:10:37','2026-08-19 19:10:37',NULL),(35,'43286aa4-f491-44cb-acfd-e9bf6cabaeed','BIO-CHI','BIO-CHIMIE','2026-08-19 19:12:38','2026-08-19 19:12:38',NULL),(36,'44030532-818d-4ab4-98a5-6ed0f2535fe7','SPS','SPORT ET SANTE','2026-08-19 19:13:26','2026-08-19 19:13:26',NULL),(37,'34e7d4e8-8878-4387-974f-7e84daf9b3cb','D.SC','DESSIN SCINTIFIQUE','2026-08-19 19:14:33','2026-08-19 19:14:33',NULL),(38,'c68049a7-5c1b-478e-a6ba-417b381df5cd','KISW','KISWAHILI','2026-08-19 19:57:10','2026-08-19 19:57:10',NULL),(41,'6a61118c-c6e3-458e-99da-42335e1d40c3','MZZT','Matiere ZZT','2026-08-20 10:54:33','2026-08-20 10:54:33',NULL);
/*!40000 ALTER TABLE `matieres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matieres_classes`
--

DROP TABLE IF EXISTS `matieres_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matieres_classes` (
  `id_matiere_classe` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_matiere` int NOT NULL,
  `id_classe` int NOT NULL,
  `id_enseignant` int DEFAULT NULL,
  `coefficient` decimal(10,1) NOT NULL DEFAULT '1.0',
  `nb_heures_par_jour` decimal(4,1) NOT NULL DEFAULT '0.0',
  `nb_heures_par_semaine` decimal(4,1) NOT NULL DEFAULT '0.0',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_matiere_classe`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_matiere_classe` (`id_matiere`,`id_classe`),
  KEY `id_classe` (`id_classe`),
  KEY `id_enseignant` (`id_enseignant`),
  CONSTRAINT `matieres_classes_ibfk_1` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`) ON DELETE CASCADE,
  CONSTRAINT `matieres_classes_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id_classe`) ON DELETE CASCADE,
  CONSTRAINT `matieres_classes_ibfk_3` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=209 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matieres_classes`
--

LOCK TABLES `matieres_classes` WRITE;
/*!40000 ALTER TABLE `matieres_classes` DISABLE KEYS */;
INSERT INTO `matieres_classes` VALUES (1,'d369b225-4d02-4551-adf7-b76476334f97',1,1,1,20.0,3.0,5.0,'2026-06-15 20:28:02',NULL),(2,'754f2e15-8c02-11f1-bf99-9c7bef735b1f',2,1,2,20.0,3.0,9.0,'2026-07-30 12:36:01',NULL),(3,'f23031c9-c149-44c0-80c9-a596e1bbf069',3,9,4,40.0,3.0,9.0,'2026-07-30 14:05:41',NULL),(4,'941ab995-1e55-490b-8d3b-b6cb87c10594',1,9,1,23.0,4.0,12.0,'2026-07-30 14:19:03',NULL),(5,'0cba3608-8c20-11f1-bf99-9c7bef735b1f',28,11,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(6,'0cba3e83-8c20-11f1-bf99-9c7bef735b1f',28,10,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(7,'0cba3faf-8c20-11f1-bf99-9c7bef735b1f',28,9,NULL,60.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(8,'0cba40a6-8c20-11f1-bf99-9c7bef735b1f',28,8,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(9,'0cba41bf-8c20-11f1-bf99-9c7bef735b1f',28,12,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(10,'0cba4289-8c20-11f1-bf99-9c7bef735b1f',28,7,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(11,'0cba434f-8c20-11f1-bf99-9c7bef735b1f',28,1,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(12,'0cba442c-8c20-11f1-bf99-9c7bef735b1f',5,11,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(13,'0cba44f0-8c20-11f1-bf99-9c7bef735b1f',5,10,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(14,'0cba45a6-8c20-11f1-bf99-9c7bef735b1f',5,9,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(15,'0cba4650-8c20-11f1-bf99-9c7bef735b1f',5,8,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(16,'0cba4742-8c20-11f1-bf99-9c7bef735b1f',5,12,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(17,'0cba4a60-8c20-11f1-bf99-9c7bef735b1f',5,7,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(18,'0cba4b24-8c20-11f1-bf99-9c7bef735b1f',5,1,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(19,'0cba4c03-8c20-11f1-bf99-9c7bef735b1f',7,11,6,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(20,'0cba4dad-8c20-11f1-bf99-9c7bef735b1f',7,10,6,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(21,'0cba4e8e-8c20-11f1-bf99-9c7bef735b1f',7,9,6,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(22,'0cba4fff-8c20-11f1-bf99-9c7bef735b1f',7,8,6,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(23,'0cba50f3-8c20-11f1-bf99-9c7bef735b1f',7,12,6,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(24,'0cba51b1-8c20-11f1-bf99-9c7bef735b1f',7,7,6,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(25,'0cba5276-8c20-11f1-bf99-9c7bef735b1f',7,1,6,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(26,'0cba534a-8c20-11f1-bf99-9c7bef735b1f',1,11,1,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(27,'0cba541f-8c20-11f1-bf99-9c7bef735b1f',1,10,1,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(28,'0cbaaf8d-8c20-11f1-bf99-9c7bef735b1f',1,8,1,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(29,'0cbab149-8c20-11f1-bf99-9c7bef735b1f',1,12,1,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(30,'0cbab234-8c20-11f1-bf99-9c7bef735b1f',1,7,1,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(31,'0cbab66c-8c20-11f1-bf99-9c7bef735b1f',14,11,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(32,'0cbab823-8c20-11f1-bf99-9c7bef735b1f',14,10,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(33,'0cbab9dd-8c20-11f1-bf99-9c7bef735b1f',14,9,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(34,'0cbabc38-8c20-11f1-bf99-9c7bef735b1f',14,8,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(35,'0cbabe2c-8c20-11f1-bf99-9c7bef735b1f',14,12,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(36,'0cbabf9a-8c20-11f1-bf99-9c7bef735b1f',14,7,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(37,'0cbac12a-8c20-11f1-bf99-9c7bef735b1f',14,1,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(38,'0cbac342-8c20-11f1-bf99-9c7bef735b1f',13,11,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(39,'0cbac49c-8c20-11f1-bf99-9c7bef735b1f',13,10,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(40,'0cbac56b-8c20-11f1-bf99-9c7bef735b1f',13,9,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(41,'0cbac634-8c20-11f1-bf99-9c7bef735b1f',13,8,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(42,'0cbac6eb-8c20-11f1-bf99-9c7bef735b1f',13,12,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(43,'0cbac795-8c20-11f1-bf99-9c7bef735b1f',13,7,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(44,'0cbac861-8c20-11f1-bf99-9c7bef735b1f',13,1,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(45,'0cbac935-8c20-11f1-bf99-9c7bef735b1f',21,11,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(46,'0cbaca78-8c20-11f1-bf99-9c7bef735b1f',21,10,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(47,'0cbaccc3-8c20-11f1-bf99-9c7bef735b1f',21,9,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(48,'0cbacde0-8c20-11f1-bf99-9c7bef735b1f',21,8,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(49,'0cbacf21-8c20-11f1-bf99-9c7bef735b1f',21,12,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(50,'0cbacfeb-8c20-11f1-bf99-9c7bef735b1f',21,7,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(51,'0cbad08f-8c20-11f1-bf99-9c7bef735b1f',21,1,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(52,'0cbad14f-8c20-11f1-bf99-9c7bef735b1f',12,11,8,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(53,'0cbad20b-8c20-11f1-bf99-9c7bef735b1f',12,10,8,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(54,'0cbad2ae-8c20-11f1-bf99-9c7bef735b1f',12,9,8,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(55,'0cbad35a-8c20-11f1-bf99-9c7bef735b1f',12,8,8,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(56,'0cbad409-8c20-11f1-bf99-9c7bef735b1f',12,12,8,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(57,'0cbad4ab-8c20-11f1-bf99-9c7bef735b1f',12,7,8,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(58,'0cbad54a-8c20-11f1-bf99-9c7bef735b1f',12,1,8,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(59,'0cbad605-8c20-11f1-bf99-9c7bef735b1f',15,11,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(60,'0cbad6b8-8c20-11f1-bf99-9c7bef735b1f',15,10,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(61,'0cbad75b-8c20-11f1-bf99-9c7bef735b1f',15,9,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(62,'0cbad7fc-8c20-11f1-bf99-9c7bef735b1f',15,8,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(63,'0cbad89f-8c20-11f1-bf99-9c7bef735b1f',15,12,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(64,'0cbad959-8c20-11f1-bf99-9c7bef735b1f',15,7,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(65,'0cbad9ff-8c20-11f1-bf99-9c7bef735b1f',15,1,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(66,'0cbadab3-8c20-11f1-bf99-9c7bef735b1f',11,11,8,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(67,'0cbadb66-8c20-11f1-bf99-9c7bef735b1f',11,10,8,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(68,'0cbadc12-8c20-11f1-bf99-9c7bef735b1f',11,9,8,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(69,'0cbadd83-8c20-11f1-bf99-9c7bef735b1f',11,8,8,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(70,'0cbade36-8c20-11f1-bf99-9c7bef735b1f',11,12,8,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(71,'0cbadee3-8c20-11f1-bf99-9c7bef735b1f',11,7,8,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(72,'0cbae01a-8c20-11f1-bf99-9c7bef735b1f',11,1,8,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(73,'0cbae2bf-8c20-11f1-bf99-9c7bef735b1f',22,11,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(74,'0cbae391-8c20-11f1-bf99-9c7bef735b1f',22,10,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(75,'0cbae4d8-8c20-11f1-bf99-9c7bef735b1f',22,9,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(76,'0cbae6f7-8c20-11f1-bf99-9c7bef735b1f',22,8,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(77,'0cbae7d1-8c20-11f1-bf99-9c7bef735b1f',22,12,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(78,'0cbae887-8c20-11f1-bf99-9c7bef735b1f',22,7,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(79,'0cbaeb8d-8c20-11f1-bf99-9c7bef735b1f',22,1,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(80,'0cbaec86-8c20-11f1-bf99-9c7bef735b1f',20,11,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(81,'0cbaedca-8c20-11f1-bf99-9c7bef735b1f',20,10,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(82,'0cbaee6c-8c20-11f1-bf99-9c7bef735b1f',20,9,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(83,'0cbaef0f-8c20-11f1-bf99-9c7bef735b1f',20,8,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(84,'0cbaefb9-8c20-11f1-bf99-9c7bef735b1f',20,12,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(85,'0cbaf06a-8c20-11f1-bf99-9c7bef735b1f',20,7,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(86,'0cbaf209-8c20-11f1-bf99-9c7bef735b1f',20,1,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(87,'0cbaf2d0-8c20-11f1-bf99-9c7bef735b1f',4,11,3,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(88,'0cbaf3a6-8c20-11f1-bf99-9c7bef735b1f',4,10,3,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(89,'0cbaf4d4-8c20-11f1-bf99-9c7bef735b1f',4,9,3,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(90,'0cbaf621-8c20-11f1-bf99-9c7bef735b1f',4,8,3,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(91,'0cbaf745-8c20-11f1-bf99-9c7bef735b1f',4,12,3,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(92,'0cbaf89b-8c20-11f1-bf99-9c7bef735b1f',4,7,3,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(93,'0cbaf9c0-8c20-11f1-bf99-9c7bef735b1f',4,1,3,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(94,'0cbafb0d-8c20-11f1-bf99-9c7bef735b1f',10,11,7,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(95,'0cbafc2e-8c20-11f1-bf99-9c7bef735b1f',10,10,7,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(96,'0cbafdd6-8c20-11f1-bf99-9c7bef735b1f',10,9,7,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(97,'0cbafec6-8c20-11f1-bf99-9c7bef735b1f',10,8,7,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(98,'0cbaffc3-8c20-11f1-bf99-9c7bef735b1f',10,12,7,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(99,'0cbb0075-8c20-11f1-bf99-9c7bef735b1f',10,7,7,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(100,'0cbb0138-8c20-11f1-bf99-9c7bef735b1f',10,1,7,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(101,'0cbb021f-8c20-11f1-bf99-9c7bef735b1f',25,11,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(102,'0cbb02d7-8c20-11f1-bf99-9c7bef735b1f',25,10,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(103,'0cbb03bb-8c20-11f1-bf99-9c7bef735b1f',25,9,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(104,'0cbb04d2-8c20-11f1-bf99-9c7bef735b1f',25,8,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(105,'0cbb063c-8c20-11f1-bf99-9c7bef735b1f',25,12,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(106,'0cbb0710-8c20-11f1-bf99-9c7bef735b1f',25,7,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(107,'0cbb07c2-8c20-11f1-bf99-9c7bef735b1f',25,1,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(108,'0cbb0887-8c20-11f1-bf99-9c7bef735b1f',9,11,7,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(109,'0cbb0941-8c20-11f1-bf99-9c7bef735b1f',9,10,7,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(110,'0cbb09ff-8c20-11f1-bf99-9c7bef735b1f',9,9,7,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(111,'0cbb0ab0-8c20-11f1-bf99-9c7bef735b1f',9,8,7,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(112,'0cbb0b6b-8c20-11f1-bf99-9c7bef735b1f',9,12,7,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(113,'0cbb0c1b-8c20-11f1-bf99-9c7bef735b1f',9,7,7,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(114,'0cbb0cd1-8c20-11f1-bf99-9c7bef735b1f',9,1,7,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(115,'0cbb0d9f-8c20-11f1-bf99-9c7bef735b1f',16,11,10,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(116,'0cbb0ecb-8c20-11f1-bf99-9c7bef735b1f',16,10,10,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(117,'0cbb0f77-8c20-11f1-bf99-9c7bef735b1f',16,9,10,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(118,'0cbb1021-8c20-11f1-bf99-9c7bef735b1f',16,8,10,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(119,'0cbb10c4-8c20-11f1-bf99-9c7bef735b1f',16,12,10,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(120,'0cbb1163-8c20-11f1-bf99-9c7bef735b1f',16,7,10,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(121,'0cbb1203-8c20-11f1-bf99-9c7bef735b1f',16,1,10,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(122,'0cbb12b6-8c20-11f1-bf99-9c7bef735b1f',3,11,4,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(123,'0cbb135e-8c20-11f1-bf99-9c7bef735b1f',3,10,4,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(124,'0cbb1616-8c20-11f1-bf99-9c7bef735b1f',3,8,4,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(125,'0cbb16ee-8c20-11f1-bf99-9c7bef735b1f',3,12,4,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(126,'0cbb17a5-8c20-11f1-bf99-9c7bef735b1f',3,7,4,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(127,'0cbb1881-8c20-11f1-bf99-9c7bef735b1f',3,1,4,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(128,'0cbb1947-8c20-11f1-bf99-9c7bef735b1f',24,11,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(129,'0cbb1a0e-8c20-11f1-bf99-9c7bef735b1f',24,10,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(130,'0cbb1ab7-8c20-11f1-bf99-9c7bef735b1f',24,9,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(131,'0cbb1cfb-8c20-11f1-bf99-9c7bef735b1f',24,8,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(132,'0cbb1dda-8c20-11f1-bf99-9c7bef735b1f',24,12,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(133,'0cbb1e99-8c20-11f1-bf99-9c7bef735b1f',24,7,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(134,'0cbb1f42-8c20-11f1-bf99-9c7bef735b1f',24,1,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(135,'0cbb1fff-8c20-11f1-bf99-9c7bef735b1f',2,11,2,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(136,'0cbb20cc-8c20-11f1-bf99-9c7bef735b1f',2,10,2,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(137,'0cbb216e-8c20-11f1-bf99-9c7bef735b1f',2,9,2,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(138,'0cbb2215-8c20-11f1-bf99-9c7bef735b1f',2,8,2,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(139,'0cbb22c1-8c20-11f1-bf99-9c7bef735b1f',2,12,2,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(140,'0cbb242b-8c20-11f1-bf99-9c7bef735b1f',2,7,2,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(141,'0cbb26c0-8c20-11f1-bf99-9c7bef735b1f',17,11,11,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(142,'0cbb2787-8c20-11f1-bf99-9c7bef735b1f',17,10,11,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(143,'0cbb2869-8c20-11f1-bf99-9c7bef735b1f',17,9,11,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(144,'0cbb2924-8c20-11f1-bf99-9c7bef735b1f',17,8,11,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(145,'0cbb29d2-8c20-11f1-bf99-9c7bef735b1f',17,12,11,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(146,'0cbb2b29-8c20-11f1-bf99-9c7bef735b1f',17,7,11,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(147,'0cbb2bdb-8c20-11f1-bf99-9c7bef735b1f',17,1,11,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(148,'0cbb2e36-8c20-11f1-bf99-9c7bef735b1f',6,11,5,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(149,'0cbb2f7e-8c20-11f1-bf99-9c7bef735b1f',6,10,5,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(150,'0cbb3037-8c20-11f1-bf99-9c7bef735b1f',6,9,5,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(151,'0cbb30e2-8c20-11f1-bf99-9c7bef735b1f',6,8,5,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(152,'0cbb319a-8c20-11f1-bf99-9c7bef735b1f',6,12,5,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(153,'0cbb323f-8c20-11f1-bf99-9c7bef735b1f',6,7,5,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(154,'0cbb32db-8c20-11f1-bf99-9c7bef735b1f',6,1,5,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(155,'0cbb338c-8c20-11f1-bf99-9c7bef735b1f',18,11,11,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(156,'0cbb3431-8c20-11f1-bf99-9c7bef735b1f',18,10,11,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(157,'0cbb34d7-8c20-11f1-bf99-9c7bef735b1f',18,9,11,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(158,'0cbb35fc-8c20-11f1-bf99-9c7bef735b1f',18,8,11,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(159,'0cbb36c2-8c20-11f1-bf99-9c7bef735b1f',18,12,11,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(160,'0cbb3761-8c20-11f1-bf99-9c7bef735b1f',18,7,11,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(161,'0cbb37fd-8c20-11f1-bf99-9c7bef735b1f',18,1,11,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(162,'0cbb38b3-8c20-11f1-bf99-9c7bef735b1f',19,11,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(163,'0cbb39a1-8c20-11f1-bf99-9c7bef735b1f',19,10,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(164,'0cbb3a41-8c20-11f1-bf99-9c7bef735b1f',19,9,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(165,'0cbb3ade-8c20-11f1-bf99-9c7bef735b1f',19,8,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(166,'0cbb3b79-8c20-11f1-bf99-9c7bef735b1f',19,12,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(167,'0cbb3c0f-8c20-11f1-bf99-9c7bef735b1f',19,7,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(168,'0cbb3cca-8c20-11f1-bf99-9c7bef735b1f',19,1,12,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(169,'0cbb3d80-8c20-11f1-bf99-9c7bef735b1f',8,11,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(170,'0cbb3e48-8c20-11f1-bf99-9c7bef735b1f',8,10,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(171,'0cbb3eed-8c20-11f1-bf99-9c7bef735b1f',8,9,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(172,'0cbb3f95-8c20-11f1-bf99-9c7bef735b1f',8,8,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(173,'0cbb4035-8c20-11f1-bf99-9c7bef735b1f',8,12,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(174,'0cbb40ea-8c20-11f1-bf99-9c7bef735b1f',8,7,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(175,'0cbb4199-8c20-11f1-bf99-9c7bef735b1f',8,1,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(176,'0cbb4247-8c20-11f1-bf99-9c7bef735b1f',26,11,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(177,'0cbb4303-8c20-11f1-bf99-9c7bef735b1f',26,10,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(178,'0cbb43ab-8c20-11f1-bf99-9c7bef735b1f',26,9,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(179,'0cbb4448-8c20-11f1-bf99-9c7bef735b1f',26,8,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(180,'0cbb44e3-8c20-11f1-bf99-9c7bef735b1f',26,12,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(181,'0cbb4582-8c20-11f1-bf99-9c7bef735b1f',26,7,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(182,'0cbb4637-8c20-11f1-bf99-9c7bef735b1f',26,1,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(183,'0cbb46ee-8c20-11f1-bf99-9c7bef735b1f',27,11,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(184,'0cbb4792-8c20-11f1-bf99-9c7bef735b1f',27,10,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(185,'0cbb482d-8c20-11f1-bf99-9c7bef735b1f',27,9,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(186,'0cbb48c7-8c20-11f1-bf99-9c7bef735b1f',27,8,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(187,'0cbb4987-8c20-11f1-bf99-9c7bef735b1f',27,12,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(188,'0cbcc6ba-8c20-11f1-bf99-9c7bef735b1f',27,7,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(189,'0cbcc8f7-8c20-11f1-bf99-9c7bef735b1f',27,1,NULL,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(190,'0cbcca75-8c20-11f1-bf99-9c7bef735b1f',23,11,10,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(191,'0cbccbc3-8c20-11f1-bf99-9c7bef735b1f',23,10,10,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(192,'0cbcccf7-8c20-11f1-bf99-9c7bef735b1f',23,9,10,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(193,'0cbccdde-8c20-11f1-bf99-9c7bef735b1f',23,8,10,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(194,'0cbcce96-8c20-11f1-bf99-9c7bef735b1f',23,12,10,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(195,'0cbccf4a-8c20-11f1-bf99-9c7bef735b1f',23,7,10,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(196,'0cbccffd-8c20-11f1-bf99-9c7bef735b1f',23,1,10,1.0,0.0,0.0,'2026-07-30 16:07:50',NULL),(197,'8b02acda-1f59-4df6-a1b4-4eb745f6dac9',5,14,NULL,15.0,1.0,1.0,'2026-08-19 19:28:10',NULL),(198,'25a41591-36df-482a-92f8-bbea277b031a',37,14,NULL,75.0,3.0,5.0,'2026-08-19 19:33:44',NULL),(199,'5c189044-dae2-4576-9d28-9c36ff714f4b',3,14,NULL,15.0,1.0,1.0,'2026-08-19 19:35:01',NULL),(200,'a71c345b-dd9c-4ac8-a7d7-e98810e8a2d9',19,14,NULL,15.0,1.0,1.0,'2026-08-19 19:36:44',NULL),(201,'5a11317c-75fd-4172-8993-fd057b89da11',6,14,NULL,120.0,4.0,8.0,'2026-08-19 19:38:26',NULL),(202,'6f5fda5a-187a-4af0-ae33-24aab4405b63',16,14,NULL,75.0,3.0,5.0,'2026-08-19 19:38:57',NULL),(203,'082cbb6e-cf3b-4a51-942e-8e59dacf6f66',34,14,NULL,30.0,2.0,2.0,'2026-08-19 19:39:57',NULL),(204,'909bde5a-f7da-4e19-88f6-4a0167c4f3af',35,14,NULL,60.0,2.0,4.0,'2026-08-19 19:46:17',NULL),(205,'d869047b-4538-4ea9-bea3-d89232d145d9',36,14,NULL,15.0,1.0,1.0,'2026-08-19 19:49:09',NULL),(206,'5de05656-b216-4553-aeaa-e46a0927f277',2,14,NULL,150.0,4.0,10.0,'2026-08-19 19:50:08',NULL),(207,'6fdb236f-b085-4b84-8d4f-22df1275c619',4,14,NULL,15.0,1.0,1.0,'2026-08-19 19:54:16',NULL),(208,'7e62cb1b-5b6b-48ce-aca8-5902312f0edc',38,14,NULL,15.0,1.0,1.0,'2026-08-19 19:57:31',NULL);
/*!40000 ALTER TABLE `matieres_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menus` (
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
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id_menu`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES (1,'7949fad0-6369-11f1-9d55-9c7bef735b1f','dashboard','Tableau de bord','dashboard',NULL,1,'dashboard.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(2,'7949ff93-6369-11f1-9d55-9c7bef735b1f','Eleves','Eleves','people',NULL,2,'etudiants.php','2026-06-08 20:40:08','2026-06-17 07:06:10'),(3,'794a0193-6369-11f1-9d55-9c7bef735b1f','inscriptions','Inscriptions','how_to_reg',NULL,3,'inscriptions.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(4,'794a032d-6369-11f1-9d55-9c7bef735b1f','sections','Sections & Classes','layers',NULL,4,'sections.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(5,'794a04c1-6369-11f1-9d55-9c7bef735b1f','enseignants','Enseignants','school',NULL,5,'enseignants.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(6,'794a0648-6369-11f1-9d55-9c7bef735b1f','scolarite','Scolarit??','payments',NULL,6,'scolarite.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(7,'794a07d7-6369-11f1-9d55-9c7bef735b1f','scolarite_minerval','Minerval',NULL,6,1,'frais.php','2026-06-08 20:40:08','2026-08-20 09:43:39'),(10,'794a0d79-6369-11f1-9d55-9c7bef735b1f','produits','Produits','inventory',NULL,7,'produits.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(11,'794a0efa-6369-11f1-9d55-9c7bef735b1f','produits_uniformes','Uniformes',NULL,10,1,'uniformes.php','2026-06-08 20:40:08','2026-06-09 22:09:18'),(12,'794a10af-6369-11f1-9d55-9c7bef735b1f','produits_livres','Livres',NULL,10,2,'livres.php','2026-06-08 20:40:08','2026-06-09 22:09:18'),(13,'794a1279-6369-11f1-9d55-9c7bef735b1f','produits_materiel','Mat. scolaires',NULL,10,3,'materiels.php','2026-06-08 20:40:08','2026-06-09 22:09:18'),(14,'794a1432-6369-11f1-9d55-9c7bef735b1f','stock','Stock','warehouse',NULL,8,'stock.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(15,'794a15b9-6369-11f1-9d55-9c7bef735b1f','points','Notes & Points','grade',NULL,9,'points.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(16,'794a1749-6369-11f1-9d55-9c7bef735b1f','bulletins','Bulletins','description',NULL,10,'bulletins.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(17,'794a18db-6369-11f1-9d55-9c7bef735b1f','paiements','Paiements','account_balance',NULL,11,'paiements.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(18,'794a1a5c-6369-11f1-9d55-9c7bef735b1f','recus','Re??us','receipt',NULL,12,'recus.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(19,'794a1be8-6369-11f1-9d55-9c7bef735b1f','echeances','??ch??anciers','calendar_month',NULL,13,'echeances.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(20,'794a1d5a-6369-11f1-9d55-9c7bef735b1f','rapports','Rapports','assessment',NULL,14,'rapports.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(21,'794a1ed8-6369-11f1-9d55-9c7bef735b1f','parametres','Param??tres','settings',NULL,15,'parametres.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(22,'794a2048-6369-11f1-9d55-9c7bef735b1f','utilisateurs','Utilisateurs','admin_panel_settings',NULL,16,'utilisateurs.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(23,'794a21c8-6369-11f1-9d55-9c7bef735b1f','audit','Journal d\'audit','history',NULL,17,'audit.php','2026-06-08 20:40:08','2026-06-08 20:40:08'),(24,'6bec1849-636a-11f1-9d55-9c7bef735b1f','horaires','Horaires','calendar_view_week',NULL,18,'horaires.php','2026-06-08 20:46:55','2026-06-08 20:46:55'),(26,'6becf766-636a-11f1-9d55-9c7bef735b1f','horaires_volumes','Volumes horaires',NULL,24,2,'volumes.php','2026-06-08 20:46:55','2026-06-08 20:46:55'),(27,'6becf93e-636a-11f1-9d55-9c7bef735b1f','horaires_dispos','Disponibilit??s',NULL,24,3,'disponibilites.php','2026-06-08 20:46:55','2026-06-08 20:46:55'),(28,'6becfb8f-636a-11f1-9d55-9c7bef735b1f','horaires_generer','G??n??rer',NULL,24,4,'generer.php','2026-06-08 20:46:55','2026-06-08 20:46:55'),(29,'6becfd94-636a-11f1-9d55-9c7bef735b1f','horaires_consulter','Consulter',NULL,24,5,'consulter.php','2026-06-08 20:46:55','2026-06-08 20:46:55'),(35,'18e6df5c-638e-11f1-9d55-9c7bef735b1f','classes','Classes',NULL,NULL,19,'classes.php','2026-06-09 01:02:17','2026-08-20 09:43:39'),(36,'18e6e644-638e-11f1-9d55-9c7bef735b1f','periodes','Périodes',NULL,NULL,20,'periodes.php','2026-06-09 01:02:17','2026-08-20 09:43:39'),(37,'18e6e76a-638e-11f1-9d55-9c7bef735b1f','annees_scolaires','Années scolaires',NULL,NULL,21,'annees_scolaires.php','2026-06-09 01:02:17','2026-08-20 09:43:39'),(38,'18e6e868-638e-11f1-9d55-9c7bef735b1f','matieres','Matières',NULL,NULL,22,'matieres.php','2026-06-09 01:02:17','2026-08-20 09:43:39'),(39,'18e6e934-638e-11f1-9d55-9c7bef735b1f','enseignements','Enseignements',NULL,NULL,23,'enseignements.php','2026-06-09 01:02:17','2026-08-20 09:43:39');
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mouvements_stock`
--

DROP TABLE IF EXISTS `mouvements_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mouvements_stock` (
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
  KEY `idx_mvt_etudiant` (`id_etudiant`),
  CONSTRAINT `fk_mouvements_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `mouvements_stock_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE,
  CONSTRAINT `mouvements_stock_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mouvements_stock`
--

LOCK TABLES `mouvements_stock` WRITE;
/*!40000 ALTER TABLE `mouvements_stock` DISABLE KEYS */;
INSERT INTO `mouvements_stock` VALUES (1,'c908b305-1eba-405f-a6fa-0ffcdd37b32c',1,'sortie',23,23.00,'Ajustement manuel','2026-06-24 14:51:39','2026-06-24 14:51:39',1,NULL,'2026-06-24 14:51:39'),(2,'41257d52-4f71-4b14-ad20-e0cf8e82402c',1,'sortie',1,23.00,'Vente à Mukendi Marie','2026-06-24 16:27:26','2026-06-24 16:27:26',1,2,'2026-06-24 16:27:26'),(3,'0d814ee8-e7f0-44dc-b367-d4cc54326843',1,'sortie',1,23.00,'Vente à Mukendi Marie','2026-06-24 16:27:27','2026-06-24 16:27:27',1,2,'2026-06-24 16:27:27'),(4,'5717c519-e43a-4ada-8805-18f6356cc0b0',1,'sortie',1,23.00,'Commande #2','2026-06-29 12:00:24','2026-06-29 12:00:24',1,NULL,'2026-06-29 12:00:24'),(5,'12bdb759-489b-49d2-9bb3-ee1aa73405b2',10,'entree',46,NULL,'Stock initial','2026-07-30 18:30:56','2026-07-30 20:30:56',NULL,NULL,'2026-07-30 20:30:56'),(6,'9b03b25b-dae4-4b81-8f6e-1e256e9dbbbf',8,'entree',1,1200.00,'Approvisionnement','2026-07-30 21:00:57','2026-07-30 23:00:57',1,NULL,'2026-07-30 21:00:57'),(7,'67f4dd5b-7111-43d7-8eac-31c2d42c6f36',8,'entree',2,1200.00,'Approvisionnement','2026-07-30 21:01:22','2026-07-30 23:01:22',1,NULL,'2026-07-30 21:01:22'),(8,'4bd615c7-470b-4c11-8854-13bd2cad6822',8,'entree',1,1000.00,'Approvisionnement','2026-07-30 21:01:44','2026-07-30 23:01:44',1,NULL,'2026-07-30 21:01:44'),(9,'6a81d596-b75c-4393-b45b-98433650fe82',8,'entree',1,1000.00,'Approvisionnement','2026-07-30 21:06:13','2026-07-30 23:06:13',1,NULL,'2026-07-30 21:06:13'),(10,'b56c247b-9a8c-4f5d-95d7-8f45ea6ad3a1',2,'entree',1,1000.00,'Approvisionnement','2026-07-30 21:17:08','2026-07-30 23:17:08',1,NULL,'2026-07-30 21:17:08'),(11,'fef353fc-d49d-4143-86a5-06abf8156331',2,'sortie',1,500.00,'Commande #3','2026-07-31 00:06:55','2026-07-31 00:06:55',1,1,'2026-07-31 00:06:55'),(12,'ba1050a7-9ad8-48ae-8b03-ce7d5da4c218',2,'entree',1,1000.00,'Annulation commande #3','2026-07-31 00:07:46','2026-07-31 00:07:46',1,NULL,'2026-07-31 00:07:46'),(13,'afbcc444-f355-4e98-b43f-a47146b3709d',2,'sortie',1,500.00,'Réactivation commande #3','2026-07-31 00:07:52','2026-07-31 00:07:52',1,NULL,'2026-07-31 00:07:52'),(14,'cba5b62a-64ae-4e5e-b784-ff13ca9338e2',8,'sortie',1,1500.00,'Vente à Dupont Jean','2026-07-31 00:09:15','2026-07-31 00:09:15',1,1,'2026-07-31 00:09:15'),(15,'91434d06-0692-4a58-87a1-d43931454961',8,'sortie',1,1500.00,'Vente à Dupont Jean','2026-07-31 00:10:35','2026-07-31 00:10:35',1,1,'2026-07-31 00:10:35'),(18,'5f4bc656-afc5-41ad-af26-8c607aa1a57d',13,'entree',1,100.00,'Stock initial','2026-08-20 10:54:41','2026-08-20 10:54:41',1,NULL,'2026-08-20 10:54:41');
/*!40000 ALTER TABLE `mouvements_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notes` (
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
  KEY `idx_notes_id_etudiant` (`id_etudiant`),
  CONSTRAINT `fk_notes_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`id_evaluation`) REFERENCES `evaluations` (`id_evaluation`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
INSERT INTO `notes` VALUES (1,'451081bd-403f-4efd-b077-7c73bf9cba89',3,1,12.0,'32','2026-06-18 15:42:17','2026-06-18 15:42:17',NULL),(2,'e175e185-0964-460d-bff1-c5885094c009',4,1,20.0,NULL,'2026-06-19 10:50:19','2026-08-20 09:43:39',NULL),(3,'7980a8b6-0dcc-438c-b7b2-6de38b027968',5,1,12.0,NULL,'2026-06-19 10:50:19','2026-06-19 10:50:19',NULL),(4,'d74d6331-8164-4a07-90b9-77e473a9cd9c',6,1,16.0,NULL,'2026-06-19 10:50:19','2026-06-19 10:50:19',NULL),(5,'a76c2a58-4bf7-4ff4-b756-c5cad88dbe61',4,2,20.0,NULL,'2026-06-19 10:50:19','2026-08-20 09:43:39',NULL),(6,'20cb947b-2099-47bb-8b13-fcbb97ff856b',5,2,20.0,NULL,'2026-06-19 10:50:19','2026-08-20 09:43:39',NULL),(7,'e549757d-4af5-4995-8587-ecc9f4eebd1e',6,2,20.0,NULL,'2026-06-19 10:50:19','2026-08-20 09:43:39',NULL),(8,'874eee4e-dbfe-410b-9f8c-f6496f3a3c61',7,1,20.0,NULL,'2026-06-19 11:07:06','2026-06-19 11:07:06',NULL),(9,'46d0c400-76ab-423f-a1fb-758cbaa61871',8,1,60.0,NULL,'2026-06-19 11:07:06','2026-06-19 11:07:06',NULL),(10,'d7e1c613-7873-4a13-b734-3b1fc152a026',7,2,20.0,NULL,'2026-06-19 11:07:06','2026-06-19 11:07:06',NULL),(11,'15ab361a-b0eb-4408-9e9d-93f8962ee402',5,6,20.0,NULL,'2026-07-27 22:30:22','2026-08-20 09:43:39',NULL);
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paiements` (
  `id_paiement` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_etudiant` int NOT NULL,
  `id_frais` int NOT NULL,
  `id_annee` int NOT NULL,
  `montant` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Montant vers?? dans ce paiement',
  `date_paiement` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mode_paiement` enum('especes','banque','mobile_money','cheque') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'especes',
  `reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'N?? de transaction bancaire/mobile',
  `preuve_paiement` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  KEY `idx_paiements_id_etudiant` (`id_etudiant`),
  CONSTRAINT `fk_paiements_etudiant` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`) ON DELETE CASCADE,
  CONSTRAINT `paiements_ibfk_2` FOREIGN KEY (`id_frais`) REFERENCES `frais` (`id_frais`),
  CONSTRAINT `paiements_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE,
  CONSTRAINT `paiements_ibfk_4` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paiements`
--

LOCK TABLES `paiements` WRITE;
/*!40000 ALTER TABLE `paiements` DISABLE KEYS */;
INSERT INTO `paiements` VALUES (4,'048ed206-a551-4fcc-8d0f-35e564bc58d9',4,4,1,65478.00,'2026-06-24 00:00:00','especes','',NULL,1,'solde','','2026-06-24 16:53:31','2026-06-24 16:53:31',NULL),(5,'91033758-d4d3-4f84-94b9-775a4924a4b4',6,5,1,38983.00,'2026-06-24 00:00:00','banque','',NULL,1,'solde','','2026-06-24 16:58:24','2026-06-24 16:58:24',NULL),(6,'22ce2fe9-37e0-48e3-8ca9-e4f9accd71e6',1,5,1,430943.00,'2026-06-24 00:00:00','especes','',NULL,1,'solde','','2026-06-24 17:02:44','2026-06-24 17:02:44',NULL),(7,'97611e3e-8b26-4d5e-9ee4-55cb5e7214a8',2,4,1,434.00,'2026-07-30 00:00:00','banque','8979',NULL,1,'solde','','2026-07-30 20:05:57','2026-07-30 20:05:57',NULL);
/*!40000 ALTER TABLE `paiements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paiements_recus`
--

DROP TABLE IF EXISTS `paiements_recus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paiements_recus` (
  `id_paiement_recu` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_recu` int NOT NULL,
  `id_paiement` int NOT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_paiement_recu`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_recu_paiement` (`id_recu`,`id_paiement`),
  KEY `id_paiement` (`id_paiement`),
  CONSTRAINT `paiements_recus_ibfk_1` FOREIGN KEY (`id_recu`) REFERENCES `recus` (`id_recu`) ON DELETE CASCADE,
  CONSTRAINT `paiements_recus_ibfk_2` FOREIGN KEY (`id_paiement`) REFERENCES `paiements` (`id_paiement`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paiements_recus`
--

LOCK TABLES `paiements_recus` WRITE;
/*!40000 ALTER TABLE `paiements_recus` DISABLE KEYS */;
INSERT INTO `paiements_recus` VALUES (2,'246e6b1b-f834-445e-8c6e-394fda7a8ebc',3,4,'2026-06-24 16:53:31'),(4,'1b129568-5239-4fc6-a4f6-08092bb1be46',5,6,'2026-06-24 17:02:44'),(5,'60723d21-361d-406f-b41a-3aa189830233',6,7,'2026-07-30 20:05:57');
/*!40000 ALTER TABLE `paiements_recus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parametres`
--

DROP TABLE IF EXISTS `parametres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parametres` (
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
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parametres`
--

LOCK TABLES `parametres` WRITE;
/*!40000 ALTER TABLE `parametres` DISABLE KEYS */;
INSERT INTO `parametres` VALUES (1,'7956029e-6369-11f1-9d55-9c7bef735b1f','nom_ecole','FUTURE VIP SCHOOL',NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),(2,'79560766-6369-11f1-9d55-9c7bef735b1f','adresse_ecole','',NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),(3,'795609cd-6369-11f1-9d55-9c7bef735b1f','telephone_ecole','',NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),(4,'79560b2e-6369-11f1-9d55-9c7bef735b1f','email_ecole','admin@vip-school.com',NULL,'2026-06-08 20:40:08','2026-06-19 10:08:23'),(5,'79560cbf-6369-11f1-9d55-9c7bef735b1f','logo_ecole','assets/uploads/logo/248a512276ac48a993983372fa906304.jpg',NULL,'2026-06-08 20:40:08','2026-07-27 23:12:04'),(8,'79561787-6369-11f1-9d55-9c7bef735b1f','prochain_num_recu','1',NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),(9,'79561b46-6369-11f1-9d55-9c7bef735b1f','annee_active','1',NULL,'2026-06-08 20:40:08','2026-06-19 09:51:35'),(10,'4c171c97-a8ed-46f9-8ed9-f602791a09ef','favicon_ecole','assets/uploads/logo/favicon_dffdbc90dadb2228515298036cb58989.png',NULL,'2026-06-16 14:18:02','2026-07-27 23:13:29'),(11,'ce8f5dc5-6b24-11f1-8bfe-9c7bef735b1f','seuil_excellent','18',NULL,'2026-06-18 16:48:45','2026-06-18 16:48:45'),(12,'ce8f6a8b-6b24-11f1-8bfe-9c7bef735b1f','seuil_tres_bien','16',NULL,'2026-06-18 16:48:45','2026-06-18 16:48:45'),(13,'ce8f6dfa-6b24-11f1-8bfe-9c7bef735b1f','seuil_bien','14',NULL,'2026-06-18 16:48:45','2026-06-18 16:48:45'),(14,'ce8f706b-6b24-11f1-8bfe-9c7bef735b1f','seuil_assez_bien','12',NULL,'2026-06-18 16:48:45','2026-06-18 16:48:45'),(15,'ce8f721f-6b24-11f1-8bfe-9c7bef735b1f','seuil_passable','10',NULL,'2026-06-18 16:48:45','2026-06-18 16:48:45'),(16,'ce8f7406-6b24-11f1-8bfe-9c7bef735b1f','appreciation_excellent','Excellent',NULL,'2026-06-18 16:48:45','2026-06-18 16:48:45'),(17,'ce94719c-6b24-11f1-8bfe-9c7bef735b1f','appreciation_tres_bien','Très Bien',NULL,'2026-06-18 16:48:45','2026-06-18 16:48:45'),(18,'ce94741f-6b24-11f1-8bfe-9c7bef735b1f','appreciation_bien','Bien',NULL,'2026-06-18 16:48:45','2026-06-18 16:48:45'),(19,'ce9475e2-6b24-11f1-8bfe-9c7bef735b1f','appreciation_assez_bien','Assez Bien',NULL,'2026-06-18 16:48:45','2026-06-18 16:48:45'),(20,'ce9477c8-6b24-11f1-8bfe-9c7bef735b1f','appreciation_passable','Passable',NULL,'2026-06-18 16:48:45','2026-06-18 16:48:45'),(21,'ce947a84-6b24-11f1-8bfe-9c7bef735b1f','appreciation_insuffisant','Insuffisant',NULL,'2026-06-18 16:48:45','2026-06-18 16:48:45'),(22,'ce947cc8-6b24-11f1-8bfe-9c7bef735b1f','regle_admis','moyenne>=12',NULL,'2026-06-18 16:48:45','2026-06-18 16:48:45'),(23,'ce947f59-6b24-11f1-8bfe-9c7bef735b1f','regle_ajourne','moyenne>=10 AND moyenne<12',NULL,'2026-06-18 16:48:45','2026-06-18 16:48:45'),(24,'ce9480f5-6b24-11f1-8bfe-9c7bef735b1f','regle_echoue','moyenne<10',NULL,'2026-06-18 16:48:45','2026-06-18 16:48:45'),(25,'07c49e0b-9864-424a-b7bc-b9ce3bda4647','devise','BIF',NULL,'2026-06-19 09:51:35','2026-07-27 22:57:05'),(26,'18b83392-2c74-454b-a863-cfec752abe1c','tva','0',NULL,'2026-06-19 09:51:35','2026-06-19 09:51:35'),(27,'c1bd459d-5ca7-4f25-8ac3-af468ae48a5a','periode_active','1',NULL,'2026-06-19 09:51:35','2026-08-20 02:40:37'),(28,'c31c5955-36b9-41ef-9929-ce32260931c8','email_protocol','mail',NULL,'2026-06-19 09:51:35','2026-06-19 09:51:35'),(29,'8026b261-459e-469d-9102-11b6c8e4d2d6','email_smtp_host','',NULL,'2026-06-19 09:51:35','2026-06-19 09:51:35'),(30,'1008e00a-3d50-4727-b07f-7f87623c849f','email_smtp_user','admin@vip-school.com',NULL,'2026-06-19 09:51:35','2026-06-19 09:51:35'),(31,'463a6168-4937-4d60-a274-320798faae73','email_smtp_pass','admin123',NULL,'2026-06-19 09:51:35','2026-06-19 09:51:35'),(32,'1ff4901f-7494-4ca2-b29d-6baa7fd9198b','email_smtp_port','587',NULL,'2026-06-19 09:51:35','2026-06-19 09:51:35'),(33,'2eaecedc-22cf-4cf8-9aef-099bd7917ade','email_smtp_crypto','tls',NULL,'2026-06-19 09:51:35','2026-06-19 09:51:35'),(34,'c12c095d-d5a1-4887-b0ba-4612fc17c444','email_sendmail_path','',NULL,'2026-06-19 09:51:35','2026-06-19 09:51:35'),(42,'274485ac-971b-11f1-a300-9c7bef735b1f','points_conduite_defaut','60',NULL,'2026-08-13 15:30:30','2026-08-13 15:39:48'),(43,'fc1c8f62-97b6-11f1-b680-9c7bef735b1f','mention_excellent','90',NULL,'2026-08-14 10:05:59','2026-08-14 10:05:59'),(44,'fc1c9da0-97b6-11f1-b680-9c7bef735b1f','mention_excellent_libelle','Excellent',NULL,'2026-08-14 10:05:59','2026-08-14 10:05:59'),(45,'fc1c9eaa-97b6-11f1-b680-9c7bef735b1f','mention_tres_bien','80',NULL,'2026-08-14 10:05:59','2026-08-14 10:05:59'),(46,'fc1c9f2e-97b6-11f1-b680-9c7bef735b1f','mention_tres_bien_libelle','Très Bien',NULL,'2026-08-14 10:05:59','2026-08-14 10:05:59'),(47,'fc1c9fbd-97b6-11f1-b680-9c7bef735b1f','mention_bien','70',NULL,'2026-08-14 10:05:59','2026-08-14 10:05:59'),(48,'fc1ca074-97b6-11f1-b680-9c7bef735b1f','mention_bien_libelle','Bien',NULL,'2026-08-14 10:05:59','2026-08-14 10:05:59'),(49,'fc1ca136-97b6-11f1-b680-9c7bef735b1f','mention_assez_bien','60',NULL,'2026-08-14 10:05:59','2026-08-14 10:05:59'),(50,'fc1ca1e6-97b6-11f1-b680-9c7bef735b1f','mention_assez_bien_libelle','Assez Bien',NULL,'2026-08-14 10:05:59','2026-08-14 10:05:59'),(51,'fc1ca2a2-97b6-11f1-b680-9c7bef735b1f','mention_passable','40',NULL,'2026-08-14 10:05:59','2026-08-14 10:05:59'),(52,'fc1ca35d-97b6-11f1-b680-9c7bef735b1f','mention_passable_libelle','Passable',NULL,'2026-08-14 10:05:59','2026-08-14 10:05:59'),(53,'fc1ca414-97b6-11f1-b680-9c7bef735b1f','mention_insuffisant','0',NULL,'2026-08-14 10:05:59','2026-08-14 10:05:59'),(54,'fc1ca4c0-97b6-11f1-b680-9c7bef735b1f','mention_insuffisant_libelle','Insuffisant',NULL,'2026-08-14 10:05:59','2026-08-14 10:05:59'),(57,'c6903bd2-97d7-11f1-b680-9c7bef735b1f','echelle_notes','100',NULL,'2026-08-14 14:00:43','2026-08-14 14:04:07'),(58,'d1516ca7-9c63-11f1-b725-9c7bef735b1f','heure_debut_journee','07:30',NULL,'2026-08-20 08:53:14','2026-08-20 08:53:14'),(59,'d1518360-9c63-11f1-b725-9c7bef735b1f','duree_cours','45',NULL,'2026-08-20 08:53:14','2026-08-20 08:53:14'),(60,'d1518443-9c63-11f1-b725-9c7bef735b1f','duree_pause','20',NULL,'2026-08-20 08:53:14','2026-08-20 08:53:14'),(61,'d1518542-9c63-11f1-b725-9c7bef735b1f','duree_vigie','10',NULL,'2026-08-20 08:53:14','2026-08-20 08:53:14'),(62,'d151873d-9c63-11f1-b725-9c7bef735b1f','nb_creneaux_jour','8',NULL,'2026-08-20 08:53:14','2026-08-20 08:53:14');
/*!40000 ALTER TABLE `parametres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `id_reset` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `id_utilisateur` int NOT NULL,
  `code` varchar(10) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `utilise` tinyint(1) NOT NULL DEFAULT '0',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_reset`),
  KEY `fk_password_resets_utilisateur` (`id_utilisateur`),
  CONSTRAINT `fk_password_resets_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES (2,'4479c09f-f31c-4fcb-a2b7-812da3acf173',1,'607115','b7fbf798880ebb1b41d6470bb265900f1a02f7f0d3f005ee3e3fed2efe95cb85','2026-08-20 08:43:23',0,'2026-08-20 10:28:23'),(3,'19eac3c5-327b-41cd-8f98-ed05fd9a17c1',1,'083448','6231952181084b36e21d86fcde35c49fef2fa3b483ea23f4aeb9054d342ffd41','2026-08-20 09:02:28',0,'2026-08-20 10:47:28'),(4,'cfba38db-e084-4a74-a60e-35ecbe6675d0',1,'614178','15c11238d88abd18143ef2009713eea767ea80fb351e17654561deb4120d13df','2026-08-20 09:09:58',0,'2026-08-20 10:54:58');
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `periodes`
--

DROP TABLE IF EXISTS `periodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `periodes` (
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
  KEY `id_annee` (`id_annee`),
  CONSTRAINT `periodes_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `periodes`
--

LOCK TABLES `periodes` WRITE;
/*!40000 ALTER TABLE `periodes` DISABLE KEYS */;
INSERT INTO `periodes` VALUES (1,'3462e3ee-2069-4ddd-b819-89871773c8d2',1,'1ERE TRIMESTRE','2025-09-09','2025-12-25',1,NULL,'2026-06-09 01:14:51','2026-08-19 18:56:52'),(2,'7e29b0b7-d5e8-4f7e-9264-1fd7d9baea4b',1,'2EME TRIMESTRE','2026-01-01','2026-03-27',0,NULL,'2026-06-09 01:16:06','2026-06-09 01:16:06'),(3,'bc828fb9-c30f-4fff-960b-4406ad7c4dec',1,'3EME TRIMESTRE','2026-03-27','2026-07-12',0,NULL,'2026-06-09 01:16:46','2026-08-19 18:56:52'),(4,'376ed018-5393-4936-b1a1-9063343a95fc',3,'1ERE TRIMESTRE','2019-09-01','2019-12-31',0,NULL,'2026-08-19 18:59:36','2026-08-19 18:59:36'),(5,'5ff7691a-dd28-44e8-ba72-119ce194cb0c',3,'2EME TRIMESTRE','2020-01-01','2020-03-31',0,NULL,'2026-08-19 19:00:24','2026-08-19 19:00:24'),(6,'3e35379a-b117-4a4a-9935-529220bedffc',3,'3EME TRIMESTRE','2020-04-01','2020-07-01',0,NULL,'2026-08-19 19:01:11','2026-08-19 19:01:11'),(9,'75f4d52a-ed34-42a1-b0bc-035314a740dd',1,'Periode ZZT','2026-09-01','2026-10-31',0,NULL,'2026-08-20 10:54:39','2026-08-20 10:54:39');
/*!40000 ALTER TABLE `periodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `points_conduite`
--

DROP TABLE IF EXISTS `points_conduite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `points_conduite` (
  `id_point_conduite` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_etudiant` int NOT NULL,
  `id_annee` int NOT NULL,
  `id_periode` int NOT NULL,
  `points_initial` decimal(5,2) NOT NULL DEFAULT '60.00',
  `points_retires` decimal(5,2) NOT NULL DEFAULT '0.00',
  `observation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_point_conduite`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uniq_conduite` (`id_etudiant`,`id_annee`,`id_periode`),
  KEY `id_annee` (`id_annee`),
  KEY `id_periode` (`id_periode`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `points_conduite`
--

LOCK TABLES `points_conduite` WRITE;
/*!40000 ALTER TABLE `points_conduite` DISABLE KEYS */;
INSERT INTO `points_conduite` VALUES (20,'29737590-9c0f-4cb9-bbfe-d3348ce75e42',8,1,3,60.00,20.00,NULL,NULL,'2026-08-13 15:13:51','2026-08-20 09:43:39'),(21,'d7425171-98e7-4be4-8703-d9d9bf3b8e8f',1,1,1,80.00,0.00,'test ZZT',NULL,'2026-08-13 15:24:59','2026-08-20 10:54:50'),(22,'b05b202d-650f-455b-9445-ff2b933b43f6',2,1,1,80.00,0.00,NULL,NULL,'2026-08-13 15:24:59','2026-08-13 15:24:59'),(23,'f9c32e4b-30df-42fa-bcad-966d88aa1123',6,1,1,80.00,0.00,NULL,NULL,'2026-08-13 15:24:59','2026-08-13 15:24:59'),(24,'05b1c686-01ef-4d4e-8080-84e51c8bc2bf',3,1,2,50.00,0.00,NULL,NULL,'2026-08-13 15:25:11','2026-08-13 15:25:11'),(25,'b776a726-54aa-480e-8fac-69f7a4c6fc69',4,1,2,50.00,0.00,NULL,NULL,'2026-08-13 15:25:11','2026-08-13 15:25:11'),(29,'6c5924fc-6e5b-4781-84e4-fab488e77359',8,1,1,75.00,0.00,NULL,NULL,'2026-08-13 15:30:50','2026-08-13 15:30:50'),(30,'743bed50-aa38-406a-b497-d463b1a4b75a',1,1,3,60.00,10.00,NULL,NULL,'2026-08-13 15:59:34','2026-08-13 16:03:56'),(31,'995b4414-d324-4cd5-ba35-79db2ffaf3fc',6,1,3,60.00,5.00,NULL,NULL,'2026-08-13 15:59:58','2026-08-13 16:00:07'),(32,'c096f735-62f4-49f4-9229-624929d9af45',6,1,2,60.00,0.00,NULL,NULL,'2026-08-14 11:36:31','2026-08-14 11:36:31');
/*!40000 ALTER TABLE `points_conduite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produits`
--

DROP TABLE IF EXISTS `produits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produits` (
  `id_produit` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_categorie` int NOT NULL,
  `libelle` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `taille` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prix_achat` decimal(12,2) NOT NULL DEFAULT '0.00',
  `editeur` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `annee_edition` year DEFAULT NULL,
  `prix_unitaire` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stock_mini` int NOT NULL DEFAULT '0',
  `stock_actuel` int NOT NULL DEFAULT '0',
  `unite` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pièce',
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifie_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_produit`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `idx_produits_categorie` (`id_categorie`),
  CONSTRAINT `produits_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categories_produits` (`id_categorie`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produits`
--

LOCK TABLES `produits` WRITE;
/*!40000 ALTER TABLE `produits` DISABLE KEYS */;
INSERT INTO `produits` VALUES (1,'59625ff8-a2f2-4874-9769-e5c407b73b9f',3,'ddsilfjiow',NULL,NULL,0.00,NULL,NULL,23.00,5,20,'pi??ce','2026-06-18 15:18:19','2026-07-30 18:29:59','2026-07-30 18:29:59'),(2,'ed06542e-98ec-4b1c-befe-3400b87617ee',3,'Crayons','',NULL,500.00,'',0000,500.00,0,0,'pièce','2026-07-30 18:30:08','2026-08-20 09:43:39',NULL),(3,'7491733f-2065-4a17-9dbd-cc935e9f867d',3,'Taille-crayons',NULL,NULL,0.00,NULL,NULL,300.00,0,0,'pièce','2026-07-30 18:30:08','2026-07-30 20:18:26','2026-07-30 20:18:26'),(4,'40e1653e-964f-4df4-b275-289e5ba623c2',3,'Rame de papiers',NULL,NULL,0.00,NULL,NULL,15000.00,0,0,'pièce','2026-07-30 18:30:08','2026-07-30 18:30:08',NULL),(5,'1e42df32-9c78-4eaa-b03c-2ab1d1e0bcbf',3,'Crayons de couleurs',NULL,NULL,0.00,NULL,NULL,2500.00,0,0,'pièce','2026-07-30 18:30:08','2026-07-30 18:30:08',NULL),(6,'1ca5ff61-b4ab-4b1a-ac1d-ff9c54e5b055',3,'Gommes',NULL,NULL,0.00,NULL,NULL,200.00,0,0,'pièce','2026-07-30 18:30:08','2026-07-30 18:30:08',NULL),(7,'73bf19cb-f81c-4cbc-8526-ca74af7c2edc',3,'Stylos',NULL,NULL,0.00,NULL,NULL,400.00,0,0,'pièce','2026-07-30 18:30:08','2026-07-30 18:30:08',NULL),(8,'ef167e73-92f0-4598-a15f-137b84c0e769',2,'Cahiers','',NULL,1054.55,'',0000,1500.00,0,9,'pièce','2026-07-30 18:30:08','2026-07-31 00:10:35',NULL),(9,'756c9f29-da39-45a7-8fb1-ccaf6f8e2f96',3,'Règles',NULL,NULL,0.00,NULL,NULL,500.00,0,0,'pièce','2026-07-30 18:30:08','2026-07-30 18:30:08',NULL),(10,'e1769796-2e67-4ed9-b6af-4b21b70a6f66',2,'we034930','',NULL,0.00,'sdui384',0000,6454.00,0,46,'pièce','2026-07-30 18:30:56','2026-07-30 18:31:11','2026-07-30 18:31:11'),(13,'e69c6cb4-4807-474a-8ee1-a2e432116aa4',1,'Produit ZZT',NULL,NULL,100.00,NULL,NULL,150.00,0,1,'pièce','2026-08-20 10:54:40','2026-08-20 10:54:40',NULL);
/*!40000 ALTER TABLE `produits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recus`
--

DROP TABLE IF EXISTS `recus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recus` (
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
  KEY `idx_recus_annee` (`id_annee`),
  CONSTRAINT `recus_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`),
  CONSTRAINT `recus_ibfk_2` FOREIGN KEY (`id_annee`) REFERENCES `annees_scolaires` (`id_annee`) ON DELETE CASCADE,
  CONSTRAINT `recus_ibfk_3` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recus`
--

LOCK TABLES `recus` WRITE;
/*!40000 ALTER TABLE `recus` DISABLE KEYS */;
INSERT INTO `recus` VALUES (1,'46908d4d-cb75-4245-9dde-3080c8a4fdf8','JDSK',2,1,'2026-06-18 18:25:30',23.00,'2026-06-18 18:25:39',NULL,'2026-06-18 18:25:30','2026-06-18 16:25:39'),(2,'7fd1cba1-66fc-49fb-a476-2233521fc32a','RECU-20260624-0001',2,1,'2026-06-24 14:47:37',10000.00,'2026-06-24 16:47:37',1,'2026-06-24 16:47:37',NULL),(3,'d1169923-c240-42b3-96c1-23de8e6cca8a','RECU-20260624-0002',4,1,'2026-06-24 14:53:31',65478.00,'2026-06-24 16:53:31',1,'2026-06-24 16:53:31',NULL),(4,'0667d7b1-0f2b-4d14-a1ea-132a7f8564d6','RECU-20260624-0003',6,1,'2026-06-24 14:58:24',38983.00,'2026-06-24 16:58:24',1,'2026-06-24 16:58:24',NULL),(5,'cc0a0f11-4702-4791-b5cc-a095123f47f5','RECU-20260624-0004',1,1,'2026-06-24 15:02:44',430943.00,'2026-06-24 17:02:44',1,'2026-06-24 17:02:44',NULL),(6,'f4c3904c-3fed-4266-b654-bc2f7b653025','RECU-20260730-0001',2,1,'2026-07-30 18:05:57',434.00,'2026-07-30 20:05:57',1,'2026-07-30 20:05:57',NULL);
/*!40000 ALTER TABLE `recus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'7948b1f8-6369-11f1-9d55-9c7bef735b1f','admin','Administrateur',10,'2026-06-08 20:40:08','2026-06-08 20:40:08',NULL),(2,'7948b668-6369-11f1-9d55-9c7bef735b1f','direction','Direction',8,'2026-06-08 20:40:08','2026-06-08 20:40:08',NULL),(3,'7948b9df-6369-11f1-9d55-9c7bef735b1f','comptable','Comptable',5,'2026-06-08 20:40:08','2026-06-08 20:40:08',NULL),(4,'7948bb51-6369-11f1-9d55-9c7bef735b1f','secretaire','Secretaire',3,'2026-06-08 20:40:08','2026-06-08 23:44:59',NULL),(5,'7948bc8d-6369-11f1-9d55-9c7bef735b1f','lecture','Lecture seule',1,'2026-06-08 20:40:08','2026-06-08 20:40:08',NULL),(6,'65582d92-682c-11f1-9e11-9c7bef735b1f','enseignant','Enseignant',4,'2026-06-14 22:05:30','2026-06-14 22:05:30',NULL),(7,'65586e62-682c-11f1-9e11-9c7bef735b1f','eleve','Eleve',2,'2026-06-14 22:05:30','2026-06-14 22:05:30',NULL),(8,'65587180-682c-11f1-9e11-9c7bef735b1f','parent','Parent',1,'2026-06-14 22:05:30','2026-06-29 12:05:23','2026-06-29 10:05:23');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_menus`
--

DROP TABLE IF EXISTS `roles_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles_menus` (
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
  KEY `id_menu` (`id_menu`),
  CONSTRAINT `roles_menus_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE,
  CONSTRAINT `roles_menus_ibfk_2` FOREIGN KEY (`id_menu`) REFERENCES `menus` (`id_menu`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=230 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_menus`
--

LOCK TABLES `roles_menus` WRITE;
/*!40000 ALTER TABLE `roles_menus` DISABLE KEYS */;
INSERT INTO `roles_menus` VALUES (32,'794d6189-6369-11f1-9d55-9c7bef735b1f',2,1,1,0,0,0,1,1,'2026-06-08 20:40:08'),(33,'794d6a83-6369-11f1-9d55-9c7bef735b1f',2,2,1,0,0,0,1,1,'2026-06-08 20:40:08'),(34,'794d6dcb-6369-11f1-9d55-9c7bef735b1f',2,3,1,0,0,0,1,1,'2026-06-08 20:40:08'),(35,'794d700b-6369-11f1-9d55-9c7bef735b1f',2,4,1,0,0,0,1,1,'2026-06-08 20:40:08'),(36,'794d720f-6369-11f1-9d55-9c7bef735b1f',2,5,1,0,0,0,1,1,'2026-06-08 20:40:08'),(37,'794d7559-6369-11f1-9d55-9c7bef735b1f',2,6,1,0,0,0,1,1,'2026-06-08 20:40:08'),(38,'794d777e-6369-11f1-9d55-9c7bef735b1f',2,10,1,0,0,0,1,1,'2026-06-08 20:40:08'),(39,'794d7ab6-6369-11f1-9d55-9c7bef735b1f',2,14,1,0,0,0,1,1,'2026-06-08 20:40:08'),(40,'794d7dad-6369-11f1-9d55-9c7bef735b1f',2,15,1,0,0,0,1,1,'2026-06-08 20:40:08'),(41,'794d8348-6369-11f1-9d55-9c7bef735b1f',2,16,1,0,0,0,1,1,'2026-06-08 20:40:08'),(42,'794d8783-6369-11f1-9d55-9c7bef735b1f',2,17,1,0,0,0,1,1,'2026-06-08 20:40:08'),(43,'794d8ec0-6369-11f1-9d55-9c7bef735b1f',2,18,1,0,0,0,1,1,'2026-06-08 20:40:08'),(44,'794d9428-6369-11f1-9d55-9c7bef735b1f',2,19,1,0,0,0,1,1,'2026-06-08 20:40:08'),(45,'794d9ade-6369-11f1-9d55-9c7bef735b1f',2,20,1,0,0,0,1,1,'2026-06-08 20:40:08'),(46,'794da008-6369-11f1-9d55-9c7bef735b1f',2,21,1,0,0,0,1,1,'2026-06-08 20:40:08'),(47,'794da49d-6369-11f1-9d55-9c7bef735b1f',2,22,1,0,0,0,1,1,'2026-06-08 20:40:08'),(48,'794da712-6369-11f1-9d55-9c7bef735b1f',2,23,1,0,0,0,1,1,'2026-06-08 20:40:08'),(52,'794daf1e-6369-11f1-9d55-9c7bef735b1f',2,11,1,0,0,0,1,1,'2026-06-08 20:40:08'),(53,'794db10d-6369-11f1-9d55-9c7bef735b1f',2,12,1,0,0,0,1,1,'2026-06-08 20:40:08'),(54,'794db300-6369-11f1-9d55-9c7bef735b1f',2,13,1,0,0,0,1,1,'2026-06-08 20:40:08'),(63,'794ef43c-6369-11f1-9d55-9c7bef735b1f',3,1,1,1,1,0,1,1,'2026-06-08 20:40:08'),(64,'794ef9f7-6369-11f1-9d55-9c7bef735b1f',3,19,1,1,1,0,1,1,'2026-06-08 20:40:08'),(65,'794efccb-6369-11f1-9d55-9c7bef735b1f',3,17,1,1,1,0,1,1,'2026-06-08 20:40:08'),(66,'794eff2e-6369-11f1-9d55-9c7bef735b1f',3,10,1,1,1,0,1,1,'2026-06-08 20:40:08'),(67,'794f01b9-6369-11f1-9d55-9c7bef735b1f',3,12,1,1,1,0,1,1,'2026-06-08 20:40:08'),(68,'794f041a-6369-11f1-9d55-9c7bef735b1f',3,13,1,1,1,0,1,1,'2026-06-08 20:40:08'),(69,'794f0689-6369-11f1-9d55-9c7bef735b1f',3,11,1,1,1,0,1,1,'2026-06-08 20:40:08'),(70,'794f08c0-6369-11f1-9d55-9c7bef735b1f',3,20,1,1,1,0,1,1,'2026-06-08 20:40:08'),(71,'794f0b0c-6369-11f1-9d55-9c7bef735b1f',3,18,1,1,1,0,1,1,'2026-06-08 20:40:08'),(72,'794f0d4f-6369-11f1-9d55-9c7bef735b1f',3,6,1,1,1,0,1,1,'2026-06-08 20:40:08'),(76,'794f1811-6369-11f1-9d55-9c7bef735b1f',3,14,1,1,1,0,1,1,'2026-06-08 20:40:08'),(100,'6bef39dd-636a-11f1-9d55-9c7bef735b1f',2,24,1,0,0,0,1,1,'2026-06-08 20:46:55'),(101,'6bef3e61-636a-11f1-9d55-9c7bef735b1f',2,29,1,0,0,0,1,1,'2026-06-08 20:46:55'),(103,'6bef44f7-636a-11f1-9d55-9c7bef735b1f',2,27,1,0,0,0,1,1,'2026-06-08 20:46:55'),(104,'6bef473b-636a-11f1-9d55-9c7bef735b1f',2,28,1,0,0,0,1,1,'2026-06-08 20:46:55'),(105,'6bef4953-636a-11f1-9d55-9c7bef735b1f',2,26,1,0,0,0,1,1,'2026-06-08 20:46:55'),(114,'ffd035c6-319f-4941-9297-3bb7247c419b',1,1,1,1,1,1,1,1,'2026-06-08 23:43:48'),(115,'3df919c9-aff4-47bd-8b69-cf13f0829745',1,2,1,1,1,1,1,1,'2026-06-08 23:43:48'),(116,'a27bad6f-7f7b-4d20-964a-9166f656c48b',1,3,1,1,1,1,1,1,'2026-06-08 23:43:48'),(117,'31a88d4a-1c75-4c00-82b6-846c7a519c7f',1,4,1,1,1,1,1,1,'2026-06-08 23:43:48'),(118,'854b3a38-475c-48d3-af6f-b435aebc3e65',1,5,1,1,1,1,1,1,'2026-06-08 23:43:48'),(119,'0dfb46b2-3bb0-4c5f-b022-f056bc15599f',1,6,1,1,1,1,1,1,'2026-06-08 23:43:48'),(123,'c88aee37-ddb5-4c98-8253-66011d54ac60',1,10,1,1,1,1,1,1,'2026-06-08 23:43:48'),(124,'e54e145a-d2ad-46a5-967f-740026c52f7b',1,14,1,1,1,1,1,1,'2026-06-08 23:43:48'),(125,'0300b42d-3488-4743-83fe-5a48e3ad41ba',1,15,1,1,1,1,1,1,'2026-06-08 23:43:48'),(126,'79e18a4a-4808-4423-8d93-df3aa3776811',1,16,1,1,1,1,1,1,'2026-06-08 23:43:48'),(127,'36505a61-b609-43de-bc5e-1ef20d15da83',1,17,1,1,1,1,1,1,'2026-06-08 23:43:48'),(128,'43fc56e0-9f22-482b-b4b9-a64286c1f00e',1,18,1,1,1,1,1,1,'2026-06-08 23:43:48'),(129,'946b87b6-809d-4cb9-a31f-dbe8d9e03b83',1,19,1,1,1,1,1,1,'2026-06-08 23:43:48'),(130,'f5de4026-bbd9-420b-ad7b-4cb9f11f73f0',1,20,1,1,1,1,1,1,'2026-06-08 23:43:48'),(131,'3f61c340-fb83-4585-a166-34ea6963ffec',1,21,1,1,1,1,1,1,'2026-06-08 23:43:48'),(132,'9406cdfc-227a-42f5-9d28-5d11a4213856',1,22,1,1,1,1,1,1,'2026-06-08 23:43:48'),(133,'1a0399f8-16f5-4873-8d9c-8ef7020e7896',1,23,1,1,1,1,1,1,'2026-06-08 23:43:48'),(134,'a5b74411-a62c-4990-992e-3b0ce7e73b60',1,24,1,1,1,1,1,1,'2026-06-08 23:43:48'),(136,'dbaf9ab2-958d-48be-829d-bdc83b7134c7',1,26,1,1,1,1,1,1,'2026-06-08 23:43:48'),(137,'95f77cb1-4549-47bf-97a5-bf532a9c3fed',1,27,1,1,1,1,1,1,'2026-06-08 23:43:48'),(138,'886c5e3a-2e4c-43e2-be68-fa43c3198c7a',1,28,1,1,1,1,1,1,'2026-06-08 23:43:48'),(139,'8db4ae6c-fb5f-4c56-9038-11b35a60ee33',1,29,1,1,1,1,1,1,'2026-06-08 23:43:48'),(140,'835fee0d-abee-4cdf-9916-1c61718fcbe6',4,1,1,1,1,0,1,1,'2026-06-08 23:44:17'),(141,'dd0d04f2-b37f-450e-a43c-1867201cf953',4,2,1,1,1,0,1,1,'2026-06-08 23:44:17'),(142,'66279e0e-c906-4f54-80fa-8a674af8a241',4,3,1,1,1,0,1,1,'2026-06-08 23:44:17'),(143,'14904535-0c55-4de5-9253-19ba14826e66',4,4,1,1,1,0,1,1,'2026-06-08 23:44:17'),(144,'94ad9bbf-293c-420c-8f3f-4e8c638685d5',4,5,1,1,1,0,1,1,'2026-06-08 23:44:17'),(145,'dd270777-f043-40ff-82a9-65c5286a4cff',4,6,1,1,0,0,0,0,'2026-06-08 23:44:17'),(149,'6a25d6a7-4a6e-4123-ad0b-8f642a8bc917',4,10,0,0,0,0,0,0,'2026-06-08 23:44:17'),(150,'6893b302-1ac3-469b-9379-659105aacf2d',4,14,0,0,0,0,0,0,'2026-06-08 23:44:17'),(151,'417ea998-371d-4c2c-bd40-ffc6a1767f88',4,15,1,1,1,0,1,1,'2026-06-08 23:44:17'),(152,'648bf17d-7b07-4ac4-b93f-f6be4956931c',4,16,1,1,1,0,1,1,'2026-06-08 23:44:17'),(153,'e6500a90-1157-400f-a66b-1c492a0d6663',4,17,0,0,0,0,0,0,'2026-06-08 23:44:17'),(154,'a690fee4-529d-4a52-a148-2c2bb5c1d563',4,18,1,1,1,0,1,1,'2026-06-08 23:44:17'),(155,'a75a1108-5c6a-4ec5-88fd-259a8649473c',4,19,0,0,0,0,0,0,'2026-06-08 23:44:17'),(156,'314b2ef0-c889-41c1-a266-82a6a537ff64',4,20,1,1,1,0,1,1,'2026-06-08 23:44:17'),(157,'8c775cee-b17f-4ed3-ae86-a03aee692a8a',4,21,0,0,0,0,0,0,'2026-06-08 23:44:17'),(158,'5912d372-9d57-4356-adb4-9b931e637bde',4,22,0,0,0,0,0,0,'2026-06-08 23:44:17'),(159,'e00e6075-a948-40fe-af66-f24ee9d18af8',4,23,0,0,0,0,0,0,'2026-06-08 23:44:17'),(160,'b28ded31-9b33-40f9-be8f-08f420bc23e7',4,24,1,1,1,0,1,1,'2026-06-08 23:44:17'),(162,'89b0aaef-f3f3-4baf-a7de-38961d9c7d22',4,26,1,1,1,0,1,1,'2026-06-08 23:44:17'),(163,'e63862fa-3380-4496-a4c9-4bb966c493dc',4,27,1,1,1,0,1,1,'2026-06-08 23:44:17'),(164,'3ed125a1-8d8a-4c1c-b0f0-2010573fe816',4,28,1,1,1,0,1,1,'2026-06-08 23:44:17'),(165,'939ce9da-d1bc-44b4-b317-39c74dd94f26',4,29,1,1,1,0,1,1,'2026-06-08 23:44:17'),(166,'3c576799-638e-11f1-9d55-9c7bef735b1f',1,35,1,1,1,1,0,0,'2026-06-09 01:03:17'),(167,'3c5782c8-638e-11f1-9d55-9c7bef735b1f',1,36,1,1,1,1,0,0,'2026-06-09 01:03:17'),(168,'3c5787fe-638e-11f1-9d55-9c7bef735b1f',1,37,1,1,1,1,0,0,'2026-06-09 01:03:17'),(169,'3c578d88-638e-11f1-9d55-9c7bef735b1f',1,38,1,1,1,1,0,0,'2026-06-09 01:03:17'),(170,'3c57949f-638e-11f1-9d55-9c7bef735b1f',1,39,1,1,1,1,0,0,'2026-06-09 01:03:17'),(171,'3ec701ac-638e-11f1-9d55-9c7bef735b1f',4,35,1,1,1,1,0,0,'2026-06-09 01:03:21'),(172,'3ec709dd-638e-11f1-9d55-9c7bef735b1f',2,35,1,1,1,1,0,0,'2026-06-09 01:03:21'),(173,'3ec70c61-638e-11f1-9d55-9c7bef735b1f',3,35,1,1,1,1,0,0,'2026-06-09 01:03:21'),(174,'3ec70f63-638e-11f1-9d55-9c7bef735b1f',4,36,1,1,1,1,0,0,'2026-06-09 01:03:21'),(175,'3ec711c7-638e-11f1-9d55-9c7bef735b1f',2,36,1,1,1,1,0,0,'2026-06-09 01:03:21'),(176,'3ec713cd-638e-11f1-9d55-9c7bef735b1f',3,36,1,1,1,1,0,0,'2026-06-09 01:03:21'),(177,'3ec715f2-638e-11f1-9d55-9c7bef735b1f',4,37,1,1,1,1,0,0,'2026-06-09 01:03:21'),(178,'3ec71830-638e-11f1-9d55-9c7bef735b1f',2,37,1,1,1,1,0,0,'2026-06-09 01:03:21'),(179,'3ec71a15-638e-11f1-9d55-9c7bef735b1f',3,37,1,1,1,1,0,0,'2026-06-09 01:03:21'),(180,'3ec71d33-638e-11f1-9d55-9c7bef735b1f',4,38,1,1,1,1,0,0,'2026-06-09 01:03:21'),(181,'3ec720b9-638e-11f1-9d55-9c7bef735b1f',2,38,1,1,1,1,0,0,'2026-06-09 01:03:21'),(182,'3ec72323-638e-11f1-9d55-9c7bef735b1f',3,38,1,1,1,1,0,0,'2026-06-09 01:03:21'),(183,'3ec72618-638e-11f1-9d55-9c7bef735b1f',4,39,1,1,1,1,0,0,'2026-06-09 01:03:21'),(184,'3ec728ac-638e-11f1-9d55-9c7bef735b1f',2,39,1,1,1,1,0,0,'2026-06-09 01:03:21'),(185,'3ec72b66-638e-11f1-9d55-9c7bef735b1f',3,39,1,1,1,1,0,0,'2026-06-09 01:03:21'),(186,'9fac4e09-d038-404c-a94e-56251245233e',1,11,1,1,1,1,1,1,'2026-06-09 22:09:18'),(187,'2ddc02c1-67fa-46df-a82e-8112d1782a17',1,12,1,1,1,1,1,1,'2026-06-09 22:09:18'),(188,'163176cd-b6d6-44c6-b784-90ff093c4dc8',1,13,1,1,1,1,1,1,'2026-06-09 22:09:18'),(189,'3828acc2-c8fb-4428-965f-f5ccf5a4932e',5,1,1,0,0,0,0,0,'2026-06-09 22:09:18'),(190,'ec579135-a62b-462d-8b71-bfe563ca4354',5,2,1,0,0,0,0,0,'2026-06-09 22:09:18'),(191,'be948634-54c7-4431-9ce1-daef58778721',5,3,1,0,0,0,0,0,'2026-06-09 22:09:18'),(192,'a856bc8c-e9c8-427a-a81b-90225dc5aece',5,4,1,0,0,0,0,0,'2026-06-09 22:09:18'),(193,'6c13b88d-fb9a-4237-858a-1a0e3d6a9e85',5,5,1,0,0,0,0,0,'2026-06-09 22:09:18'),(194,'80170e7f-a3ac-4dc6-92f7-996cb4c5e08c',5,15,1,0,0,0,0,0,'2026-06-09 22:09:18'),(195,'2b4ab0db-e8be-46a3-ab99-ba7e9e9c7ebc',5,16,1,0,0,0,0,0,'2026-06-09 22:09:18'),(196,'d6e5cfbc-5c0f-4b9a-9627-f39d91e256f8',5,17,1,0,0,0,0,0,'2026-06-09 22:09:18'),(197,'a9dc4945-d34e-41cb-90a8-cf745eccc00b',5,21,1,0,0,0,0,0,'2026-06-09 22:09:18'),(198,'71805375-fdb6-4b32-aabf-6d1e7a3b71f9',5,22,1,0,0,0,0,0,'2026-06-09 22:09:18'),(199,'565717b2-682d-11f1-9e11-9c7bef735b1f',6,16,1,0,0,0,0,0,'2026-06-14 22:12:15'),(200,'56594469-682d-11f1-9e11-9c7bef735b1f',6,35,1,0,0,0,0,0,'2026-06-14 22:12:15'),(201,'5659503e-682d-11f1-9e11-9c7bef735b1f',6,1,1,0,0,0,0,0,'2026-06-14 22:12:15'),(202,'56595b4f-682d-11f1-9e11-9c7bef735b1f',6,5,1,0,0,0,0,0,'2026-06-14 22:12:15'),(203,'56596669-682d-11f1-9e11-9c7bef735b1f',6,39,1,0,0,0,0,0,'2026-06-14 22:12:15'),(204,'56597231-682d-11f1-9e11-9c7bef735b1f',6,2,1,0,0,0,0,0,'2026-06-14 22:12:15'),(205,'56597a97-682d-11f1-9e11-9c7bef735b1f',6,24,1,0,0,0,0,0,'2026-06-14 22:12:15'),(206,'56598590-682d-11f1-9e11-9c7bef735b1f',6,29,1,0,0,0,0,0,'2026-06-14 22:12:15'),(207,'56598de9-682d-11f1-9e11-9c7bef735b1f',6,3,1,0,0,0,0,0,'2026-06-14 22:12:15'),(208,'56599808-682d-11f1-9e11-9c7bef735b1f',6,38,1,0,0,0,0,0,'2026-06-14 22:12:15'),(209,'5659a004-682d-11f1-9e11-9c7bef735b1f',6,4,1,0,0,0,0,0,'2026-06-14 22:12:15'),(214,'565bc370-682d-11f1-9e11-9c7bef735b1f',6,15,1,1,1,0,0,0,'2026-06-14 22:12:15'),(215,'565cfcf7-682d-11f1-9e11-9c7bef735b1f',7,16,1,0,0,0,0,0,'2026-06-14 22:12:15'),(216,'565d114a-682d-11f1-9e11-9c7bef735b1f',7,1,1,0,0,0,0,0,'2026-06-14 22:12:15'),(217,'565d197e-682d-11f1-9e11-9c7bef735b1f',7,17,1,0,0,0,0,0,'2026-06-14 22:12:15'),(218,'565d1e7e-682d-11f1-9e11-9c7bef735b1f',7,18,1,0,0,0,0,0,'2026-06-14 22:12:15'),(222,'565e9078-682d-11f1-9e11-9c7bef735b1f',8,16,1,0,0,0,0,0,'2026-06-14 22:12:15'),(223,'565ea3d7-682d-11f1-9e11-9c7bef735b1f',8,1,1,0,0,0,0,0,'2026-06-14 22:12:15'),(224,'565eac33-682d-11f1-9e11-9c7bef735b1f',8,17,1,0,0,0,0,0,'2026-06-14 22:12:15'),(225,'565eb3aa-682d-11f1-9e11-9c7bef735b1f',8,18,1,0,0,0,0,0,'2026-06-14 22:12:15');
/*!40000 ALTER TABLE `roles_menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sanctions_conduite`
--

DROP TABLE IF EXISTS `sanctions_conduite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sanctions_conduite` (
  `id_sanction` int NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_point_conduite` int NOT NULL,
  `motif` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `points_retires` decimal(5,2) NOT NULL DEFAULT '0.00',
  `date_sanction` date NOT NULL,
  `id_utilisateur` int DEFAULT NULL,
  `cree_le` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sanction`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `id_point_conduite` (`id_point_conduite`),
  CONSTRAINT `fk_sanction_point_conduite` FOREIGN KEY (`id_point_conduite`) REFERENCES `points_conduite` (`id_point_conduite`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sanctions_conduite`
--

LOCK TABLES `sanctions_conduite` WRITE;
/*!40000 ALTER TABLE `sanctions_conduite` DISABLE KEYS */;
INSERT INTO `sanctions_conduite` VALUES (3,'26f75dda-7861-48c1-9933-f1258b3d63c6',20,'DERANGEMENT',5.00,'2026-08-13',1,'2026-08-13 15:14:16'),(4,'6b630745-e898-41e1-902a-ae6e0dff6075',20,'M,ZXOFI DFWEO CWEOIUFWF',10.00,'2026-08-13',1,'2026-08-13 15:15:07'),(6,'ce7bebd6-b723-40ed-949b-8e05f1311b58',30,'zxmcnxcm',5.00,'2026-08-13',1,'2026-08-13 15:59:40'),(7,'8fb7fcd4-64d5-4e0c-ba41-c49cb720dc1b',31,'zm,mz,mx',5.00,'2026-08-13',1,'2026-08-13 16:00:06'),(8,'cfa1a2f1-4f05-4bd0-9df2-ea67f7abbcb5',20,'mcxcjk',5.00,'2026-08-13',1,'2026-08-13 16:00:36'),(9,'96d4a03e-bddd-4f8e-8d6a-d738755d791e',30,'xmcxjkch',5.00,'2026-08-13',1,'2026-08-13 16:03:55');
/*!40000 ALTER TABLE `sanctions_conduite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sections` (
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES (1,'6b0d10b0-c337-4acf-80ed-52119b3ac260','ECO','ECONOMIQUE',1,NULL,'2026-06-09 01:17:28','2026-07-30 16:25:21'),(2,'0440a63a-8fac-4707-ba27-df78355ab4dd','PEDA','PEDAGOGIQUE',1,NULL,'2026-06-09 01:17:50','2026-07-30 16:25:38'),(6,'1d6a4f3d-04a4-4f74-baa1-cb75153730fa','M-P','MATHS-PHYS',1,NULL,'2026-08-19 19:04:59','2026-08-19 19:04:59'),(10,'7b132201-3f12-45d2-800d-c0792bfcd018','SZZT','Section ZZT',1,NULL,'2026-08-20 10:54:31','2026-08-20 10:54:31');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `types_frais`
--

DROP TABLE IF EXISTS `types_frais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `types_frais` (
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `types_frais`
--

LOCK TABLES `types_frais` WRITE;
/*!40000 ALTER TABLE `types_frais` DISABLE KEYS */;
INSERT INTO `types_frais` VALUES (1,'7952efa0-6369-11f1-9d55-9c7bef735b1f','MINERVAL','Minerval / Frais de scolarité','',NULL,'2026-06-08 20:40:08','2026-06-24 14:51:03'),(2,'7952f58e-6369-11f1-9d55-9c7bef735b1f','ASSURANCE','Assurance scolaire',NULL,NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),(3,'7952f933-6369-11f1-9d55-9c7bef735b1f','UNIFORME','Uniformes scolaires',NULL,NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),(4,'7952fbde-6369-11f1-9d55-9c7bef735b1f','LIVRE','Manuels et livres scolaires',NULL,NULL,'2026-06-08 20:40:08','2026-06-08 20:40:08'),(5,'7952fe28-6369-11f1-9d55-9c7bef735b1f','MATERIEL','Materiels scolaires','',NULL,'2026-06-08 20:40:08','2026-06-09 00:43:04'),(6,'7953006c-6369-11f1-9d55-9c7bef735b1f','TOILETTES','Materiels de toilettes','',NULL,'2026-06-08 20:40:08','2026-06-24 14:50:45'),(8,'c0b38981-5535-47dd-a0a8-b42e344d4c79','TOILsd','Materiels de toilettes','','2026-07-30 18:06:48','2026-07-30 20:06:42','2026-07-30 20:06:48'),(12,'08dfece4-19ec-46fd-8642-34ad85059d71','TZZT','Type Frais ZZT',NULL,NULL,'2026-08-20 10:54:36','2026-08-20 10:54:36');
/*!40000 ALTER TABLE `types_frais` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uniformes`
--

DROP TABLE IF EXISTS `uniformes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `uniformes` (
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uniformes`
--

LOCK TABLES `uniformes` WRITE;
/*!40000 ALTER TABLE `uniformes` DISABLE KEYS */;
INSERT INTO `uniformes` VALUES (3,'0d1c5635-788f-4aed-906e-517b492b216c','Uniforme ZZT',NULL,100.00,1,5,NULL,'2026-08-20 08:54:37','2026-08-20 08:54:37');
/*!40000 ALTER TABLE `uniformes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilisateurs` (
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
  KEY `id_role` (`id_role`),
  CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurs`
--

LOCK TABLES `utilisateurs` WRITE;
/*!40000 ALTER TABLE `utilisateurs` DISABLE KEYS */;
INSERT INTO `utilisateurs` VALUES (1,'795185c3-6369-11f1-9d55-9c7bef735b1f',1,'Administrateur','admin@vip-school.com',NULL,NULL,'$2y$10$WYhZEBBp3mQBwVB0csJhIuiYzX15/lPRIiN7uR286PQUAJMSKVIaC',1,'2026-06-08 20:40:08','2026-08-20 10:43:39',NULL),(2,'dfdd5a4e-58ca-4b6e-b003-7d4d50a3cbae',7,'Administateur Esther','paul@gmail.com',NULL,NULL,'$2y$10$XVG0U4t5jjKv3rzEpQqC3uPy69n4M7tGyEnASRPZp8bRaaMNiCo1i',1,'2026-06-24 15:07:56','2026-08-14 11:36:31',NULL),(3,'db446ef0-925b-401c-a6bd-a52442374468',7,'NDUWAYESU BELYSE  Niyondiko','',NULL,NULL,'$2y$10$nlvxMdBUXxD6/XkGT4uWuOKHXQVIU3WVeWYxt4o6/KRU5u8uyGnne',0,'2026-07-27 22:52:09','2026-07-27 22:56:30','2026-07-27 20:56:30'),(4,'8bc7e72b-936c-41d1-978b-dbe12e53d031',7,'NDUWAYESU BELYSE','adhavuga@gmail.com',NULL,NULL,'$2y$10$uanOPhNSeAE5YmEo5usfpet3uiFBJ5CrKwnpoLHVrz1LhtLw7m1.G',1,'2026-07-30 12:09:53','2026-07-30 12:09:53',NULL),(5,'98e22c90-3eba-4184-9e5f-048a026626bf',6,'NDUWAYESU BELYSE','dushimeyesupaulin@gmail.com',NULL,NULL,'$2y$10$ocIkM5gBj20xNy/9CiP6iegx7NP/4vuMrXfLrSJuNzQ0i830peLIG',1,'2026-07-30 15:07:41','2026-07-30 15:07:41',NULL),(13,'b6b84390-f7ef-4f64-8d74-f8d21453087d',6,'Test ZZT User','zztest2@vip.local',NULL,NULL,'$2y$10$ilR1Zkp0/MSV9Z.HMx1npuPe9bqWWIKO/gCn7MHNCHC.WIpnnFMpG',1,'2026-08-20 10:54:43','2026-08-20 10:54:43',NULL);
/*!40000 ALTER TABLE `utilisateurs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-20 10:55:02
