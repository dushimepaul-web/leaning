-- Migration: Tables manquantes (audit 2026-08-14)
-- Rétablit absences, departements, employes (supprimées du dump mais utilisées par le code),
-- enseignements (utilisée par les routes api/enseignements, les horaires et les programmes),
-- et email_logs (journalisation des emails via Cpanel_email).
-- Run: mysql -u root vip_school < migration_003.sql

CREATE TABLE IF NOT EXISTS `absences` (
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

CREATE TABLE IF NOT EXISTS `departements` (
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

CREATE TABLE IF NOT EXISTS `employes` (
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

CREATE TABLE IF NOT EXISTS `enseignements` (
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
  CONSTRAINT `fk_ens_enseignant` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`) ON DELETE CASCADE,
  CONSTRAINT `fk_ens_matiere_classe` FOREIGN KEY (`id_matiere_classe`) REFERENCES `matieres_classes` (`id_matiere_classe`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Propager les enseignements existants de matieres_classes vers enseignements
INSERT INTO `enseignements` (`uuid`, `id_enseignant`, `id_matiere`, `id_classe`, `id_matiere_classe`, `cree_le`)
SELECT UUID(), mc.id_enseignant, mc.id_matiere, mc.id_classe, mc.id_matiere_classe, NOW()
FROM `matieres_classes` mc
WHERE mc.id_enseignant IS NOT NULL
  AND mc.deleted_at IS NULL
  AND NOT EXISTS (
    SELECT 1 FROM `enseignements` en
    WHERE en.id_enseignant = mc.id_enseignant
      AND en.id_matiere_classe = mc.id_matiere_classe
  );

CREATE TABLE IF NOT EXISTS `email_logs` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
