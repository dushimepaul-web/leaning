-- Script pour supprimer la contrainte de clé étrangère sur la table horaires
ALTER TABLE `horaires` DROP FOREIGN KEY `horaires_ibfk_5`;
ALTER TABLE `horaires` DROP INDEX `id_creneau`;
