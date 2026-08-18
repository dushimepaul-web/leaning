-- Migration: Points de conduite par élève et par trimestre
-- Crée les tables points_conduite et sanctions_conduite
-- Run: mysql -u root vip_school < migration_002.sql

CREATE TABLE IF NOT EXISTS `points_conduite` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sanctions_conduite` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
