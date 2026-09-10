<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(60);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli('localhost', 'root', '', 'vip_school');
$mysqli->set_charset('utf8mb4');

echo "=== FPH TIMETABLE DIAGNOSTIC PART 3 ===\n\n";

// 1. Check creneaux table
echo "=== CRENEAUX TABLE STRUCTURE ===\n";
$r = $mysqli->query("DESCRIBE creneaux");
while ($row = $r->fetch_assoc()) {
    echo "  {$row['Field']} {$row['Type']} {$row['Null']} {$row['Key']}\n";
}
echo "\n=== ALL CRENEAUX ===\n";
$r = $mysqli->query("SELECT * FROM creneaux ORDER BY id_creneau");
while ($row = $r->fetch_assoc()) {
    echo "  id={$row['id_creneau']}";
    foreach ($row as $k => $v) {
        if ($k !== 'id_creneau') echo " $k=$v";
    }
    echo "\n";
}

// 2. Check jours table
echo "\n=== JOURS TABLE ===\n";
$r = $mysqli->query("SHOW TABLES LIKE '%jour%'");
while ($row = $r->fetch_assoc()) {
    echo "  Table: {$row[0]}\n";
}
$r = $mysqli->query("DESCRIBE jours");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        echo "  {$row['Field']} {$row['Type']}\n";
    }
}
$r = $mysqli->query("SELECT * FROM jours");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        echo "  " . json_encode($row) . "\n";
    }
}

// 3. Now properly query horaires for each class with FPH
echo "\n=== HORAIRES FOR EACH CLASS WITH FPH ===\n";
$classes = [
    14 => '2PF Sc Maths-phys',
    15 => '1ere IG',
    16 => '2eme IG',
    17 => '3eme IG',
    18 => '1ere BA',
    19 => '2eme BA',
    20 => '3eme BA'
];

foreach ($classes as $cls_id => $cls_name) {
    $r = $mysqli->query("
        SELECT h.id_horaire, h.id_creneau, h.id_jour, h.id_matiere, h.id_enseignant, h.id_enseignement,
               c.heure_debut, c.heure_fin, j.nom as jour_nom, m.code as code_matiere, e.fullname as teacher
        FROM horaires h
        JOIN creneaux c ON h.id_creneau = c.id_creneau
        JOIN jours j ON h.id_jour = j.id_jour
        LEFT JOIN matieres m ON h.id_matiere = m.id_matiere
        LEFT JOIN enseignants e ON h.id_enseignant = e.id_enseignant
        WHERE h.id_classe = $cls_id AND h.deleted_at IS NULL
        ORDER BY j.id_jour, c.heure_debut
    ");
    echo "\n  CLASS $cls_id ($cls_name): {$r->num_rows} total slots\n";
    
    $fph_count = 0;
    $by_matiere = [];
    while ($row = $r->fetch_assoc()) {
        $code = $row['code_matiere'];
        if (!isset($by_matiere[$code])) $by_matiere[$code] = 0;
        $by_matiere[$code]++;
        
        if (strpos($row['code_matiere'], 'FPH') !== false) {
            $fph_count++;
            echo "    FPH: {$row['jour_nom']} {$row['heure_debut']}-{$row['heure_fin']} teacher={$row['teacher']}\n";
        }
    }
    echo "    FPH slots placed: $fph_count\n";
    echo "    Slots by matiere:\n";
    foreach ($by_matiere as $code => $cnt) {
        $marker = (strpos($code, 'FPH') !== false) ? ' <-- FPH' : '';
        echo "      $code: $cnt slots$marker\n";
    }
}

// 4. Check teacher 48 conflicts - all horaires for teacher 48
echo "\n=== TEACHER 48 (KARORERO) ALL SLOTS ===\n";
$r = $mysqli->query("
    SELECT h.*, c.heure_debut, c.heure_fin, j.nom as jour_nom, m.code as code_matiere, cl.libelle as classe_libelle
    FROM horaires h
    JOIN creneaux c ON h.id_creneau = c.id_creneau
    JOIN jours j ON h.id_jour = j.id_jour
    LEFT JOIN matieres m ON h.id_matiere = m.id_matiere
    LEFT JOIN classes cl ON h.id_classe = cl.id_classe
    WHERE h.id_enseignant = 48 AND h.deleted_at IS NULL
    ORDER BY j.id_jour, c.heure_debut
");
echo "  Total slots: {$r->num_rows}\n";
$by_day_creneau = [];
while ($row = $r->fetch_assoc()) {
    $key = "J{$row['id_jour']}_C{$row['id_creneau']}";
    $by_day_creneau[$key] = $row;
    echo "    {$row['jour_nom']} (j={$row['id_jour']}) {$row['heure_debut']}-{$row['heure_fin']}: {$row['code_matiere']} @ {$row['classe_libelle']}\n";
}

// 5. Check each class's capacity (total creneaux * total jours vs total horaires)
echo "\n=== CAPACITY ANALYSIS ===\n";
$r_jours = $mysqli->query("SELECT COUNT(*) as cnt FROM jours");
$total_jours = $r_jours->fetch_assoc()['cnt'];
$r_creneaux = $mysqli->query("SELECT COUNT(*) as cnt FROM creneaux");
$total_creneaux = $r_creneaux->fetch_assoc()['cnt'];
$max_slots = $total_jours * $total_creneaux;
echo "  Total jours: $total_jours\n";
echo "  Total creneaux: $total_creneaux\n";
echo "  Max slots per class: $max_slots\n\n";

foreach ($classes as $cls_id => $cls_name) {
    $r = $mysqli->query("SELECT COUNT(*) as cnt FROM horaires WHERE id_classe = $cls_id AND deleted_at IS NULL");
    $used = $r->fetch_assoc()['cnt'];
    $free = $max_slots - $used;
    $pct = round($used / $max_slots * 100, 1);
    echo "  Class $cls_id ($cls_name): $used/$max_slots used ($pct%) - $free free slots\n";
}

// 6. Check enseignements table (id_enseignement referenced in horaires)
echo "\n=== ENSEIGNEMENTS TABLE ===\n";
$r = $mysqli->query("SHOW TABLES LIKE '%enseignement%'");
while ($row = $r->fetch_assoc()) {
    echo "  {$row[0]}\n";
}
$r = $mysqli->query("DESCRIBE enseignements");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        echo "  {$row['Field']} {$row['Type']}\n";
    }
}

// 7. Check FPH in enseignements
echo "\n=== ENSEIGNEMENTS FOR FPH ===\n";
$r = $mysqli->query("
    SELECT en.*, m.code, c.libelle as classe_libelle, e.fullname as teacher
    FROM enseignements en
    JOIN matieres m ON en.id_matiere = m.id_matiere
    JOIN classes c ON en.id_classe = c.id_classe
    JOIN enseignants e ON en.id_enseignant = e.id_enseignant
    WHERE m.code LIKE '%FPH%'
");
while ($row = $r->fetch_assoc()) {
    echo "  id_enseignement={$row['id_enseignement']} class={$row['classe_libelle']} teacher={$row['teacher']} nbh={$row['nb_heures']}\n";
}

// 8. Check the generation result
echo "\n=== GENERATIONS ===\n";
$r = $mysqli->query("SHOW TABLES LIKE '%generation%'");
while ($row = $r->fetch_assoc()) {
    echo "  {$row[0]}\n";
}
$r = $mysqli->query("SELECT * FROM generations ORDER BY id_generation DESC LIMIT 3");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        echo "  " . json_encode($row) . "\n";
    }
}

// 9. Check generation_details or errors table
echo "\n=== ERROR/DETAIL TABLES ===\n";
$r = $mysqli->query("SHOW TABLES LIKE '%error%'");
while ($row = $r->fetch_assoc()) echo "  {$row[0]}\n";
$r = $mysqli->query("SHOW TABLES LIKE '%detail%'");
while ($row = $r->fetch_assoc()) echo "  {$row[0]}\n";
$r = $mysqli->query("SHOW TABLES LIKE '%result%'");
while ($row = $r->fetch_assoc()) echo "  {$row[0]}\n";
$r = $mysqli->query("SHOW TABLES LIKE '%log%'");
while ($row = $r->fetch_assoc()) echo "  {$row[0]}\n";

$mysqli->close();
echo "\n=== PART 3 COMPLETE ===\n";
