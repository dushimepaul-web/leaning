<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(60);

$mysqli = new mysqli('localhost', 'root', '', 'vip_school');
$mysqli->set_charset('utf8mb4');

echo "=== KEY TABLE STRUCTURES ===\n\n";

// 1. horaires structure
echo "=== horaires ===\n";
$r = $mysqli->query("DESCRIBE horaires");
while ($row = $r->fetch_row()) echo "  $row[0] $row[1]\n";

// 2. jours_semaine
echo "\n=== jours_semaine ===\n";
$r = $mysqli->query("DESCRIBE jours_semaine");
while ($row = $r->fetch_row()) echo "  $row[0] $row[1]\n";
$r = $mysqli->query("SELECT * FROM jours_semaine");
while ($row = $r->fetch_assoc()) echo "  " . json_encode($row) . "\n";

// 3. parametres (may hold slots info)
echo "\n=== parametres ===\n";
$r = $mysqli->query("DESCRIBE parametres");
while ($row = $r->fetch_row()) echo "  $row[0] $row[1]\n";
$r = $mysqli->query("SELECT * FROM parametres");
while ($row = $r->fetch_assoc()) echo "  " . json_encode($row) . "\n";

// 4. horaires_generations
echo "\n=== horaires_generations ===\n";
$r = $mysqli->query("DESCRIBE horaires_generations");
while ($row = $r->fetch_row()) echo "  $row[0] $row[1]\n";
$r = $mysqli->query("SELECT * FROM horaires_generations ORDER BY id_generation DESC LIMIT 3");
while ($row = $r->fetch_assoc()) echo "  " . json_encode($row) . "\n";

// 5. Check what matiere 34 FPH looks like in the most recent generation
$r = $mysqli->query("SELECT MAX(id_generation) as max_gen FROM horaires_generations");
$max_gen = $r->fetch_assoc()['max_gen'];
echo "\n=== MAX GENERATION: $max_gen ===\n";

echo "\n=== ALL HORAIRES FOR FPH (matiere=34) IN GEN $max_gen ===\n";
$r = $mysqli->query("
    SELECT h.*, cl.libelle as classe_libelle, e.fullname as teacher
    FROM horaires h
    LEFT JOIN classes cl ON h.id_classe = cl.id_classe
    LEFT JOIN enseignants e ON h.id_enseignant = e.id_enseignant
    WHERE h.id_matiere = 34 AND h.id_generation = $max_gen AND h.deleted_at IS NULL
");
echo "  Count: {$r->num_rows}\n";
while ($row = $r->fetch_assoc()) {
    echo "  id_horaire={$row['id_horaire']} class={$row['classe_libelle']} jour_id={$row['id_jour']} creneau_id={$row['id_creneau']} teacher={$row['teacher']}\n";
}

// 6. Check teacher 48 ALL horaires in latest gen
echo "\n=== TEACHER 48 ALL HORAIRES IN GEN $max_gen ===\n";
$r = $mysqli->query("
    SELECT h.*, cl.libelle as classe_libelle, m.code as code_matiere
    FROM horaires h
    LEFT JOIN classes cl ON h.id_classe = cl.id_classe
    LEFT JOIN matieres m ON h.id_matiere = m.id_matiere
    WHERE h.id_enseignant = 48 AND h.id_generation = $max_gen AND h.deleted_at IS NULL
");
echo "  Count: {$r->num_rows}\n";
while ($row = $r->fetch_assoc()) {
    echo "  id_horaire={$row['id_horaire']} class={$row['classe_libelle']} matiere={$row['code_matiere']} jour_id={$row['id_jour']} creneau_id={$row['id_creneau']}\n";
}

// 7. How is timetable structured - check if there are time slots stored anywhere
// The horaires table has id_creneau but no creneaux table - check if it's inline
echo "\n=== DISTINCT id_creneau VALUES IN horaires ===\n";
$r = $mysqli->query("SELECT DISTINCT id_creneau FROM horaires ORDER BY id_creneau");
while ($row = $r->fetch_row()) echo "  $row[0]\n";

echo "\n=== DISTINCT id_jour VALUES IN horaires ===\n";
$r = $mysqli->query("SELECT DISTINCT id_jour FROM horaires ORDER BY id_jour");
while ($row = $r->fetch_row()) echo "  $row[0]\n";

// 8. Check all classes and count of required matieres_classes
echo "\n=== MATIERES_CLASSES FOR EACH CLASS ===\n";
$r = $mysqli->query("
    SELECT c.id_classe, c.libelle as classe_libelle, 
           COUNT(*) as nb_matieres, SUM(mc.nb_heures_par_semaine) as total_heures
    FROM classes c
    JOIN matieres_classes mc ON c.id_classe = mc.id_classe
    WHERE mc.deleted_at IS NULL
    GROUP BY c.id_classe, c.libelle
    ORDER BY c.id_classe
");
while ($row = $r->fetch_assoc()) {
    echo "  {$row['classe_libelle']}: {$row['nb_matieres']} matieres, {$row['total_heures']}h/sem total\n";
}

// 9. Check the actual algorithm/controller
echo "\n=== ENSEIGNEMENTS TABLE STRUCTURE ===\n";
$r = $mysqli->query("DESCRIBE enseignements");
while ($row = $r->fetch_row()) echo "  $row[0] $row[1]\n";

echo "\n=== ENSEIGNEMENTS FOR FPH ===\n";
$r = $mysqli->query("
    SELECT en.*, m.code, cl.libelle as classe_libelle, e.fullname
    FROM enseignements en
    JOIN matieres m ON en.id_matiere = m.id_matiere
    JOIN classes cl ON en.id_classe = cl.id_classe
    JOIN enseignants e ON en.id_enseignant = e.id_enseignant
    WHERE m.code LIKE '%FPH%'
");
while ($row = $r->fetch_assoc()) echo "  " . json_encode($row) . "\n";

// 10. Check how many horaires per class in latest gen
echo "\n=== HORAIRES COUNT PER CLASS IN GEN $max_gen ===\n";
$r = $mysqli->query("
    SELECT c.id_classe, c.libelle, COUNT(*) as cnt
    FROM horaires h
    JOIN classes c ON h.id_classe = c.id_classe
    WHERE h.id_generation = $max_gen AND h.deleted_at IS NULL
    GROUP BY c.id_classe, c.libelle
    ORDER BY c.id_classe
");
while ($row = $r->fetch_assoc()) {
    echo "  {$row['libelle']}: {$row['cnt']} slots placed\n";
}

$mysqli->close();
