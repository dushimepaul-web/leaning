-- MIGRATIONS POUR CORRIGER LES ANOMALIES DE SCHÉMA

-- 1. FK password_resets (nécessite InnoDB)
ALTER TABLE `password_resets` ENGINE = InnoDB;
ALTER TABLE `password_resets`
ADD CONSTRAINT `fk_password_resets_utilisateur`
FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`)
ON DELETE CASCADE ON UPDATE CASCADE;

-- 2. menus.uuid NOT NULL
UPDATE `menus` SET `uuid` = UUID() WHERE `uuid` IS NULL OR `uuid` = '';
ALTER TABLE `menus` MODIFY `uuid` CHAR(36) NOT NULL;

-- 3. frais.id_classe NOT NULL (supprimer d'abord les paiements liés)
DELETE FROM `paiements` WHERE `id_frais` IN (SELECT `id_frais` FROM `frais` WHERE `id_classe` IS NULL);
DELETE FROM `frais` WHERE `id_classe` IS NULL;
ALTER TABLE `frais` MODIFY `id_classe` INT NOT NULL;

-- 4. paiements.id_frais NOT NULL
DELETE FROM `paiements` WHERE `id_frais` IS NULL;
ALTER TABLE `paiements` MODIFY `id_frais` INT NOT NULL;

-- 5. Index performance
CREATE INDEX IF NOT EXISTS `idx_frais_id_type_frais` ON `frais` (`id_type_frais`);
CREATE INDEX IF NOT EXISTS `idx_paiements_id_frais` ON `paiements` (`id_frais`);
CREATE INDEX IF NOT EXISTS `idx_paiements_id_etudiant` ON `paiements` (`id_etudiant`);
CREATE INDEX IF NOT EXISTS `idx_paiements_date` ON `paiements` (`date_paiement`);
CREATE INDEX IF NOT EXISTS `idx_notes_id_evaluation` ON `notes` (`id_evaluation`);
CREATE INDEX IF NOT EXISTS `idx_notes_id_etudiant` ON `notes` (`id_etudiant`);
