-- Migration: Add id_matiere_classe to enseignements
-- Makes matieres_classes the single source of truth for subject-class relationships
-- Run: mysql -u root vip_school < migration_001.sql

-- Step 1: Add column
ALTER TABLE enseignements
  ADD COLUMN id_matiere_classe INT NULL AFTER id_classe,
  ADD KEY id_matiere_classe (id_matiere_classe);

-- Step 2: Create missing matieres_classes entries from enseignements data
INSERT INTO matieres_classes (uuid, id_matiere, id_classe, coefficient, id_enseignant, nb_heures_par_jour, nb_heures_par_semaine)
SELECT UUID(), e.id_matiere, e.id_classe, 1.0, e.id_enseignant, 0.0, 0.0
FROM enseignements e
LEFT JOIN matieres_classes mc ON e.id_matiere = mc.id_matiere AND e.id_classe = mc.id_classe AND mc.deleted_at IS NULL
WHERE mc.id_matiere_classe IS NULL
GROUP BY e.id_matiere, e.id_classe, e.id_enseignant;

-- Step 3: Populate id_matiere_classe in enseignements
UPDATE enseignements e
JOIN matieres_classes mc ON e.id_matiere = mc.id_matiere AND e.id_classe = mc.id_classe AND mc.deleted_at IS NULL
SET e.id_matiere_classe = mc.id_matiere_classe
WHERE e.id_matiere_classe IS NULL;

-- Step 4: Make NOT NULL
ALTER TABLE enseignements MODIFY COLUMN id_matiere_classe INT NOT NULL;

-- Step 5: Add FK to matieres_classes
ALTER TABLE enseignements ADD CONSTRAINT fk_ens_matiere_classe
  FOREIGN KEY (id_matiere_classe) REFERENCES matieres_classes(id_matiere_classe) ON DELETE CASCADE;

-- Step 6: Add unique constraint (teacher + matiere_classe)
ALTER TABLE enseignements ADD UNIQUE INDEX uniq_enseignant_matiere_classe (id_enseignant, id_matiere_classe);
