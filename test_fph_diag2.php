<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(60);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli('localhost', 'root', '', 'vip_school');
$mysqli->set_charset('utf8mb4');

echo "=== FPH TIMETABLE DIAGNOSTIC PART 2 ===\n\n";

// 1. contraintes_horaires structure
echo "=== CONTRAINTES_HORAIRES STRUCTURE ===\n";
$r = $mysqli->query("DESCRIBE contraintes_horaires");
while ($row = $r->fetch_assoc()) {
    echo "  {$row['Field']} {$row['Type']} {$row['Null']} {$row['Key']}\n";
}

echo "\n=== ALL CONTRAINTES_HORAIRES ===\n";
$r = $mysqli->query("SELECT * FROM contraintes_horaires");
while ($row = $r->fetch_assoc()) {
    echo "  " . json_encode($row) . "\n";
}

// 2. disponibilites_enseignants structure
echo "\n=== DISPONIBILITES_ENSEIGNANTS STRUCTURE ===\n";
$r = $mysqli->query("DESCRIBE disponibilites_enseignants");
while ($row = $r->fetch_assoc()) {
    echo "  {$row['Field']} {$row['Type']} {$row['Null']} {$row['Key']}\n";
}

// 3. Check ALL disponibilites for teacher 48
echo "\n=== ALL DISPONIBILITES FOR ENSEIGNANT 48 ===\n";
$r = $mysqli->query("SELECT * FROM disponibilites_enseignants WHERE id_enseignant = 48");
echo "  Rows: {$r->num_rows}\n";
while ($row = $r->fetch_assoc()) {
    echo "  " . json_encode($row) . "\n";
}

// 4. Check all teacher 48's entries across ALL tables
echo "\n=== DISPO TABLES ===\n";
$r = $mysqli->query("SHOW TABLES LIKE '%dispo%'");
while ($row = $r->fetch_assoc()) {
    echo "  {$row[0]}\n";
}

// 5. horaires table structure
echo "\n=== HORAIRES TABLE STRUCTURE ===\n";
$r = $mysqli->query("DESCRIBE horaires");
while ($row = $r->fetch_assoc()) {
    echo "  {$row['Field']} {$row['Type']} {$row['Null']} {$row['Key']}\n";
}

// 6. horaires entries for the class (id_classe=14, since the original query targeted the first FPH entry)
// Actually, let's check ALL classes that have FPH
$classes_with_fph = [14, 15, 16, 17, 18, 19, 20];
foreach ($classes_with_fph as $cls_id) {
    $r = $mysqli->query("SELECT libelle FROM classes WHERE id_classe = $cls_id");
    $cls_name = $r->fetch_assoc()['libelle'];
    
    $r = $mysqli->query("SELECT COUNT(*) as cnt FROM horaires WHERE id_classe = $cls_id");
    $total = $r->fetch_assoc()['cnt'];
    
    $r = $mysqli->query("
        SELECT h.*, m.code as code_matiere 
        FROM horaires h 
        LEFT JOIN matieres m ON h.id_matiere = m.id_matiere 
        WHERE h.id_classe = $cls_id
    ");
    echo "\n=== CLASS $cls_id ($cls_name): $total horaires ===\n";
    
    $fph_count = 0;
    while ($row = $r->fetch_assoc()) {
        if (strpos($row['code_matiere'], 'FPH') !== false) {
            $fph_count++;
            echo "  FPH: jour={$row['jour']} heure_debut={$row['heure_debut']} heure_fin={$row['heure_fin']} sem={$row['id_semaine']}\n";
        }
    }
    echo "  FPH slots placed: $fph_count\n";
    
    // Check which teacher occupies which slots
    $r2 = $mysqli->query("
        SELECT h.jour, h.heure_debut, h.heure_fin, m.code as code_matiere, e.fullname as teacher
        FROM horaires h 
        LEFT JOIN matieres m ON h.id_matiere = m.id_matiere 
        LEFT JOIN enseignants e ON h.id_enseignant = e.id_enseignant
        WHERE h.id_classe = $cls_id
        ORDER BY h.jour, h.heure_debut
    ");
    echo "  All slots:\n";
    while ($row = $r2->fetch_assoc()) {
        echo "    J{$row['jour']} {$row['heure_debut']}-{$row['heure_fin']}: {$row['code_matiere']} ({$row['teacher']})\n";
    }
}

// 7. Check teacher 48's ALL assignments
echo "\n=== TEACHER 48 (KARORERO) ALL MATIERES_CLASSES ===\n";
$r = $mysqli->query("
    SELECT mc.*, m.code, m.libelle, c.libelle as classe_libelle
    FROM matieres_classes mc
    JOIN matieres m ON mc.id_matiere = m.id_matiere
    JOIN classes c ON mc.id_classe = c.id_classe
    WHERE mc.id_enseignant = 48 AND mc.deleted_at IS NULL
    ORDER BY c.libelle
");
while ($row = $r->fetch_assoc()) {
    echo "  class={$row['classe_libelle']} matiere={$row['code']} sem={$row['nb_heures_par_semaine']}h jour={$row['nb_heures_par_jour']}h\n";
}

// 8. Check ALL horaires for teacher 48
echo "\n=== TEACHER 48 ALL HORAIRES ===\n";
$r = $mysqli->query("
    SELECT h.*, m.code as code_matiere, c.libelle as classe_libelle
    FROM horaires h
    LEFT JOIN matieres m ON h.id_matiere = m.id_matiere
    LEFT JOIN classes c ON h.id_classe = c.id_classe
    WHERE h.id_enseignant = 48
    ORDER BY h.jour, h.heure_debut
");
echo "  Total slots: {$r->num_rows}\n";
$by_class = [];
while ($row = $r->fetch_assoc()) {
    $key = $row['classe_libelle'];
    if (!isset($by_class[$key])) $by_class[$key] = [];
    $by_class[$key][] = $row;
}
foreach ($by_class as $cls => $slots) {
    echo "  Class $cls:\n";
    foreach ($slots as $s) {
        echo "    J{$s['jour']} {$s['heure_debut']}-{$s['heure_fin']}: {$s['code_matiere']}\n";
    }
}

// 9. Check total timetable slots capacity
echo "\n=== ALL CLASSES ===\n";
$r = $mysqli->query("SELECT * FROM classes ORDER BY id_classe");
while ($row = $r->fetch_assoc()) {
    echo "  #{$row['id_classe']} {$row['libelle']}\n";
}

// 10. Check what weeks are being used
echo "\n=== SEMAINES ===\n";
$r = $mysqli->query("SHOW TABLES LIKE '%semaine%'");
while ($row = $r->fetch_assoc()) {
    echo "  {$row[0]}\n";
}
$r = $mysqli->query("SELECT * FROM semaines LIMIT 5");
while ($row = $r->fetch_assoc()) {
    echo "  " . json_encode($row) . "\n";
}

// 11. Check algorithm code to understand how it decides
echo "\n=== CHECKING FOR ALGORITHM FILE ===\n";
$algorithm_files = glob("C:\\wamp64\\www\\leaning\\application\\**\\*timetable*");
$algorithm_files = array_merge($algorithm_files, glob("C:\\wamp64\\www\\leaning\\application\\**\\*emploi*"));
$algorithm_files = array_merge($algorithm_files, glob("C:\\wamp64\\www\\leaning\\application\\**\\*schedule*"));
$algorithm_files = array_merge($algorithm_files, glob("C:\\wamp64\\www\\leaning\\application\\**\\*edt*"));
foreach ($algorithm_files as $f) {
    echo "  $f\n";
}

$mysqli->close();
echo "\n=== PART 2 COMPLETE ===\n";
