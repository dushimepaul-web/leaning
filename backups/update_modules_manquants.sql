-- ============================================================
-- SCRIPT DE MISE À JOUR - Modules manquants du contrat
-- Exécuter ce script sur la BDD vip_school
-- ============================================================

-- 1. Ajout de la catégorie TOILETTES dans categories_produits
INSERT IGNORE INTO `categories_produits` (`uuid`, `code`, `libelle`, `cree_le`) VALUES
('795422d4-6369-11f1-9d55-9c7bef735b1f', 'TOILETTES', 'Matériels de toilettes', NOW());

-- 2. Ajout du menu "Matériels Scolaires" (enfant de Scolarité, parent_id=6)
INSERT IGNORE INTO `menus` (`uuid`, `code`, `libelle`, `icon`, `parent_id`, `ordre`, `route`, `cree_le`, `modifie_le`) VALUES
('a1b2c3d4-0001-4000-8000-000000000001', 'scolarite_materiels', 'Matériels Scolaires', 'school', 6, 35, 'Materiels', NOW(), NOW());

-- 3. Mise à jour du menu Toilettes existant pour pointer vers le nouveau module
UPDATE `menus` SET `route` = 'Toilettes', `icon` = 'school' WHERE `code` = 'scolarite_toilettes' AND `route` IS NULL;

-- 4. Ajout des permissions admin (id_role=1) pour les nouveaux menus
-- Toilettes (menu id du menu scolarite_toilettes)
INSERT IGNORE INTO `roles_menus` (`uuid`, `id_role`, `id_menu`, `can_view`, `can_add`, `can_edit`, `can_delete`, `can_export`, `can_imprimer`, `cree_le`)
SELECT UUID(), 1, m.id_menu, 1, 1, 1, 1, 1, 1, NOW()
FROM `menus` m WHERE m.code = 'scolarite_toilettes';

-- Matériels Scolaires
INSERT IGNORE INTO `roles_menus` (`uuid`, `id_role`, `id_menu`, `can_view`, `can_add`, `can_edit`, `can_delete`, `can_export`, `can_imprimer`, `cree_le`)
SELECT UUID(), 1, m.id_menu, 1, 1, 1, 1, 1, 1, NOW()
FROM `menus` m WHERE m.code = 'scolarite_materiels';

-- 5. Création de la table recus si elle n'existe pas déjà (vérification structure)
-- La table recus existe déjà, mais vérifions qu'elle a bien id_utilisateur comme FK optionnelle
-- ALTER TABLE `recus` MODIFY `id_utilisateur` int(11) DEFAULT NULL;
