<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(60);

$mysqli = new mysqli('localhost', 'root', '', 'vip_school');
$mysqli->set_charset('utf8mb4');

$max_gen = 3;

echo "=== DEEP ANALYSIS: WHY PASS 5 SWAP FAILS FOR FPH @ 3EME IG ===\n\n";

// The only free slot for 3ème IG is Wed (j=3) creneau=1
// Teacher 48 is busy at Wed C1 with FRA TECH @ 1ere BA
// Pass 5 tries two strategies:

// STRATEGY A: Find another teacher for FRA TECH @ 1ere BA at Wed C1
echo "=== STRATEGY A: SUBSTITUTE teacher for FRA TECH @ 1ere BA ===\n";
$r = $mysqli->query("
    SELECT en.id_enseignement, en.id_enseignant, e.fullname
    FROM enseignements en
    JOIN matieres_classes mc ON en.id_matiere_classe = mc.id_matiere_classe
    JOIN enseignants e ON en.id_enseignant = e.id_enseignant
    WHERE mc.id_classe = 18 AND mc.id_matiere = (SELECT id_matiere FROM matieres WHERE code = 'FRA TECH')
    AND en.id_enseignant != 48 AND en.deleted_at IS NULL AND mc.deleted_at IS NULL
");
if ($r->num_rows === 0) {
    echo "  NO other teacher found for FRA TECH in 1ere BA!\n";
    echo "  -> Strategy A fails: no substitute available\n";
} else {
    while ($row = $r->fetch_assoc()) {
        echo "  Candidate: teacher {$row['id_enseignant']} ({$row['fullname']})\n";
        // Check if they are free at Wed C1
        $tid = $row['id_enseignant'];
        $r2 = $mysqli->query("
            SELECT * FROM horaires 
            WHERE id_enseignant = $tid AND id_jour = 3 AND id_creneau = 1 AND id_generation = $max_gen AND deleted_at IS NULL
        ");
        if ($r2->num_rows > 0) {
            echo "    -> BUSY at Wed C1! Cannot substitute.\n";
        } else {
            echo "    -> FREE at Wed C1! Could substitute.\n";
        }
    }
}

// STRATEGY B: Move FRA TECH from 1ere BA (Wed C1) to another slot
echo "\n=== STRATEGY B: MOVE FRA TECH @ 1ere BA to different slot ===\n";
echo "  Teacher 48 must be free at the new slot AND 1ere BA must be free there.\n\n";

// Check all slots for 1ere BA
echo "  1ere BA schedule:\n";
$r = $mysqli->query("
    SELECT h.id_jour, h.id_creneau, m.code as code_matiere, e.fullname as teacher, js.libelle as jour_nom
    FROM horaires h
    JOIN jours_semaine js ON h.id_jour = js.id_jour
    LEFT JOIN matieres m ON h.id_matiere = m.id_matiere
    LEFT JOIN enseignants e ON h.id_enseignant = e.id_enseignant
    WHERE h.id_classe = 18 AND h.id_generation = $max_gen AND h.deleted_at IS NULL
    ORDER BY h.id_jour, h.id_creneau
");
$ba18_slots = [];
echo "  Total: {$r->num_rows}/40\n";
while ($row = $r->fetch_assoc()) {
    $key = "J{$row['id_jour']}_C{$row['id_creneau']}";
    $ba18_slots[$key] = $row;
    echo "    {$row['jour_nom']} (j={$row['id_jour']}) C{$row['id_creneau']}: {$row['code_matiere']} ({$row['teacher']})\n";
}

// Find free slots in 1ere BA where teacher 48 is also free
echo "\n  Free slots in 1ere BA where teacher 48 is also free:\n";
$found = false;
for ($j = 1; $j <= 5; $j++) {
    for ($c = 1; $c <= 8; $c++) {
        $class_key = "J{$j}_C{$c}";
        $prof_key = "48_{$j}_{$c}";
        if (!isset($ba18_slots[$class_key])) {
            // Class is free at this slot
            // Check if teacher 48 is occupied at this slot
            $r2 = $mysqli->query("
                SELECT COUNT(*) as cnt FROM horaires 
                WHERE id_enseignant = 48 AND id_jour = $j AND id_creneau = $c AND id_generation = $max_gen AND deleted_at IS NULL
            ");
            $cnt = $r2->fetch_assoc()['cnt'];
            if ($cnt == 0) {
                $r3 = $mysqli->query("SELECT libelle FROM jours_semaine WHERE id_jour = $j");
                $jour = $r3->fetch_assoc()['libelle'];
                echo "    $jour (j=$j) C$c: BOTH class and teacher FREE\n";
                $found = true;
            }
        }
    }
}
if (!$found) {
    echo "    NONE found!\n";
}

// Also check: does 1ere BA have any free slots at all?
echo "\n  Free slots in 1ere BA:\n";
$free_count = 0;
for ($j = 1; $j <= 5; $j++) {
    for ($c = 1; $c <= 8; $c++) {
        $class_key = "J{$j}_C{$c}";
        if (!isset($ba18_slots[$class_key])) {
            $r3 = $mysqli->query("SELECT libelle FROM jours_semaine WHERE id_jour = $j");
            $jour = $r3->fetch_assoc()['libelle'];
            echo "    $jour (j=$j) C$c: class FREE\n";
            $free_count++;
        }
    }
}
echo "  Total free in 1ere BA: $free_count\n";

// Check 3ème IG total hours vs placed
echo "\n=== SUMMARY: 3EME IG HOURS ===\n";
$r = $mysqli->query("SELECT SUM(nb_heures_par_semaine) as total FROM matieres_classes WHERE id_classe = 17 AND deleted_at IS NULL");
$total_required = $r->fetch_assoc()['total'];
$r = $mysqli->query("SELECT COUNT(*) as placed FROM horaires WHERE id_classe = 17 AND id_generation = $max_gen AND deleted_at IS NULL");
$total_placed = $r->fetch_assoc()['placed'];
echo "  Required: {$total_required}h, Placed: {$total_placed}h, Missing: " . ($total_required - $total_placed) . "h\n";

// Show exactly which matiere is missing
echo "\n  Per-matiere required vs placed:\n";
$r1 = $mysqli->query("
    SELECT mc.id_matiere, m.code, SUM(mc.nb_heures_par_semaine) as required
    FROM matieres_classes mc
    JOIN matieres m ON mc.id_matiere = m.id_matiere
    WHERE mc.id_classe = 17 AND mc.deleted_at IS NULL
    GROUP BY mc.id_matiere, m.code
");
while ($row = $r1->fetch_assoc()) {
    $r2 = $mysqli->query("
        SELECT COUNT(*) as placed FROM horaires h
        WHERE h.id_classe = 17 AND h.id_matiere = {$row['id_matiere']} AND h.id_generation = $max_gen AND h.deleted_at IS NULL
    ");
    $placed = $r2->fetch_assoc()['placed'];
    $diff = $row['required'] - $placed;
    $marker = ($diff > 0) ? ' <-- SHORT' : '';
    echo "    {$row['code']}: required={$row['required']}h, placed={$placed}h, diff={$diff}$marker\n";
}

$mysqli->close();
