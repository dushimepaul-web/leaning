<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(60);

$mysqli = new mysqli('localhost', 'root', '', 'vip_school');
$mysqli->set_charset('utf8mb4');

$max_gen = 3;

echo "=== FPH ROOT CAUSE ANALYSIS ===\n\n";

// 1. Check which class is missing 1 slot (3ème IG = id 17)
echo "=== 1. 3EME IG (id=17) HORAIRES IN GEN $max_gen ===\n";
$r = $mysqli->query("
    SELECT h.*, m.code as code_matiere, e.fullname as teacher, js.libelle as jour_nom
    FROM horaires h
    JOIN jours_semaine js ON h.id_jour = js.id_jour
    LEFT JOIN matieres m ON h.id_matiere = m.id_matiere
    LEFT JOIN enseignants e ON h.id_enseignant = e.id_enseignant
    WHERE h.id_classe = 17 AND h.id_generation = $max_gen AND h.deleted_at IS NULL
    ORDER BY h.id_jour, h.id_creneau
");
echo "  Total: {$r->num_rows}\n";
$placed_slots = [];
while ($row = $r->fetch_assoc()) {
    $key = "J{$row['id_jour']}_C{$row['id_creneau']}";
    $placed_slots[$key] = $row;
    echo "  {$row['jour_nom']} (j={$row['id_jour']}) creneau={$row['id_creneau']}: {$row['code_matiere']} ({$row['teacher']})\n";
}

// 2. Find which slot is FREE in 3ème IG
echo "\n=== 2. FREE SLOT IN 3EME IG ===\n";
for ($j = 1; $j <= 5; $j++) {
    for ($c = 1; $c <= 8; $c++) {
        $key = "J{$j}_C{$c}";
        if (!isset($placed_slots[$key])) {
            // Get jour name
            $r2 = $mysqli->query("SELECT libelle FROM jours_semaine WHERE id_jour = $j");
            $jour = $r2->fetch_assoc()['libelle'];
            echo "  FREE SLOT: $jour (j=$j) creneau=$c\n";
            $free_jour = $j;
            $free_creneau = $c;
        }
    }
}
if (!isset($free_jour)) {
    echo "  NO FREE SLOT FOUND - all 40 slots occupied!\n";
}

// 3. Check teacher 48 at that free slot
echo "\n=== 3. TEACHER 48 (KARORERO) AT FREE SLOT ===\n";
if (isset($free_jour)) {
    $r = $mysqli->query("
        SELECT h.*, m.code as code_matiere, cl.libelle as classe_libelle
        FROM horaires h
        LEFT JOIN matieres m ON h.id_matiere = m.id_matiere
        LEFT JOIN classes cl ON h.id_classe = cl.id_classe
        WHERE h.id_enseignant = 48 AND h.id_jour = $free_jour AND h.id_creneau = $free_creneau AND h.id_generation = $max_gen AND h.deleted_at IS NULL
    ");
    if ($r->num_rows > 0) {
        echo "  CONFLICT! Teacher 48 is BUSY at this slot:\n";
        while ($row = $r->fetch_assoc()) {
            echo "    {$row['code_matiere']} @ {$row['classe_libelle']}\n";
        }
    } else {
        echo "  Teacher 48 is FREE at this slot!\n";
        echo "  -> This means the constraint is NOT teacher availability\n";
    }
    
    // Also check what's in the class at this slot in other gens
    $r2 = $mysqli->query("
        SELECT h.id_generation, m.code as code_matiere, cl.libelle as classe_libelle, e.fullname as teacher
        FROM horaires h
        LEFT JOIN matieres m ON h.id_matiere = m.id_matiere
        LEFT JOIN classes cl ON h.id_classe = cl.id_classe
        LEFT JOIN enseignants e ON h.id_enseignant = e.id_enseignant
        WHERE h.id_classe = 17 AND h.id_jour = $free_jour AND h.id_creneau = $free_creneau AND h.deleted_at IS NULL
    ");
    echo "  Other entries at same class+slot across all gens: {$r2->num_rows}\n";
}

// 4. Check all teacher 48 occupied slots for easy cross-reference
echo "\n=== 4. TEACHER 48 ALL OCCUPIED SLOTS (GEN $max_gen) ===\n";
$r = $mysqli->query("
    SELECT h.id_jour, h.id_creneau, m.code as code_matiere, cl.libelle as classe_libelle, js.libelle as jour_nom
    FROM horaires h
    JOIN jours_semaine js ON h.id_jour = js.id_jour
    LEFT JOIN matieres m ON h.id_matiere = m.id_matiere
    LEFT JOIN classes cl ON h.id_classe = cl.id_classe
    WHERE h.id_enseignant = 48 AND h.id_generation = $max_gen AND h.deleted_at IS NULL
    ORDER BY h.id_jour, h.id_creneau
");
$teacher48_occupied = [];
while ($row = $r->fetch_assoc()) {
    $key = "J{$row['id_jour']}_C{$row['id_creneau']}";
    $teacher48_occupied[$key] = $row;
    echo "  {$row['jour_nom']} (j={$row['id_jour']}) creneau={$row['id_creneau']}: {$row['code_matiere']} @ {$row['classe_libelle']}\n";
}

// 5. Check 3ème IG's ALL matieres_classes
echo "\n=== 5. ALL MATIERES FOR 3EME IG ===\n";
$r = $mysqli->query("
    SELECT mc.*, m.code, m.libelle, e.fullname as teacher
    FROM matieres_classes mc
    JOIN matieres m ON mc.id_matiere = m.id_matiere
    LEFT JOIN enseignants e ON mc.id_enseignant = e.id_enseignant
    WHERE mc.id_classe = 17 AND mc.deleted_at IS NULL
    ORDER BY m.code
");
$total_h = 0;
$fph_h = 0;
while ($row = $r->fetch_assoc()) {
    $h = $row['nb_heures_par_semaine'];
    $total_h += $h;
    if ($row['code'] === 'FPH') $fph_h = $h;
    echo "  {$row['code']} ({$row['libelle']}): {$h}h/sem, jour={$row['nb_heures_par_jour']}h, prof={$row['teacher']}\n";
}
echo "  TOTAL: {$total_h}h/sem\n";
echo "  FPH needs: {$fph_h}h\n";

// 6. Count placed hours per matiere for 3ème IG
echo "\n=== 6. PLACED HOURS PER MATIERE FOR 3EME IG ===\n";
$r = $mysqli->query("
    SELECT m.code, COUNT(*) as placed
    FROM horaires h
    JOIN matieres m ON h.id_matiere = m.id_matiere
    WHERE h.id_classe = 17 AND h.id_generation = $max_gen AND h.deleted_at IS NULL
    GROUP BY m.code
    ORDER BY m.code
");
while ($row = $r->fetch_assoc()) {
    $marker = ($row['code'] === 'FPH') ? ' <-- FPH' : '';
    echo "  {$row['code']}: {$row['placed']} slots placed$marker\n";
}

// 7. Check if jour_special (mardi) has any impact
echo "\n=== 7. JOUR SPECIAL CHECK ===\n";
$r = $mysqli->query("SELECT * FROM parametres WHERE clef IN ('jour_special', 'jour_special_actif')");
while ($row = $r->fetch_assoc()) {
    echo "  {$row['clef']} = {$row['valeur']}\n";
}

// Check all horaires on Tuesday (j=2) for 3ème IG
echo "\n  Tuesday (j=2) horaires for 3ème IG:\n";
$r = $mysqli->query("
    SELECT h.id_creneau, m.code as code_matiere, e.fullname as teacher
    FROM horaires h
    LEFT JOIN matieres m ON h.id_matiere = m.id_matiere
    LEFT JOIN enseignants e ON h.id_enseignant = e.id_enseignant
    WHERE h.id_classe = 17 AND h.id_jour = 2 AND h.id_generation = $max_gen AND h.deleted_at IS NULL
    ORDER BY h.id_creneau
");
$tuesday_count = 0;
$tuesday_slots = [];
while ($row = $r->fetch_assoc()) {
    $tuesday_slots[] = $row['id_creneau'];
    $tuesday_count++;
    echo "    creneau={$row['id_creneau']}: {$row['code_matiere']} ({$row['teacher']})\n";
}
echo "  Tuesday total: $tuesday_count slots placed\n";
echo "  Tuesday free: " . (8 - $tuesday_count) . " slots\n";

// 8. Check if the algorithm file can be found
echo "\n=== 8. SEARCHING FOR TIMETABLE GENERATION CODE ===\n";

$mysqli->close();
