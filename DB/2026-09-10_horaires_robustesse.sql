-- À exécuter une seule fois sur la base vip_school après sauvegarde.
-- Les contrôles applicatifs restent nécessaires ; ces index protègent aussi
-- la base contre les écritures concurrentes de créneaux fixes.

ALTER TABLE horaires_fixes ENGINE = InnoDB;

ALTER TABLE horaires_fixes
  ADD UNIQUE KEY uniq_fixe_enseignant (id_annee, id_enseignant, id_jour, id_creneau);

-- Une génération doit pouvoir être retrouvée par année et statut.
ALTER TABLE horaires_generations
  ADD KEY idx_generation_annee_statut (id_annee, statut, deleted_at);
