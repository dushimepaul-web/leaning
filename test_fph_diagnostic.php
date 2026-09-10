<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(60);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli('localhost', 'root', '', 'vip_school');
$mysqli->set_charset('utf8mb4');

echo "=== FPH TIMETABLE DIAGNOSTIC ===\n\n";

// 1. Find the matiere with code 'FPH'
echo "=== 1. MATIERE FPH ===\n";
$r = $mysqli->query("SELECT * FROM matieres WHERE code LIKE '%FPH%'");
if ($r->num_rows === 0) {
    echo "  NO matiere found with code containing 'FPH'!\n";
    echo "  Let's check all matieres codes:\n";
    $r2 = $mysqli->query("SELECT id_matiere, code, libelle FROM matieres ORDER BY code");
    while ($row = $r2->fetch_assoc()) {
        echo "    #{$row['id_matiere']} code={$row['code']} libelle={$row['libelle']}\n";
    }
} else {
    while ($row = $r->fetch_assoc()) {
        echo "  id_matiere={$row['id_matiere']} code={$row['code']} libelle={$row['libelle']}\n";
        $fph_id_matiere = $row['id_matiere'];
    }
}

// 2. Find matieres_classes entries for FPH
echo "\n=== 2. MATIERES_CLASSES FOR FPH ===\n";
$r = $mysqli->query("
    SELECT mc.*, m.code, m.libelle, c.libelle as classe_libelle 
    FROM matieres_classes mc 
    JOIN matieres m ON mc.id_matiere = m.id_matiere 
    JOIN classes c ON mc.id_classe = c.id_classe 
    WHERE m.code LIKE '%FPH%' AND mc.deleted_at IS NULL
");
if ($r->num_rows === 0) {
    echo "  NO matieres_classes entries for FPH!\n";
} else {
    while ($row = $r->fetch_assoc()) {
        echo "  id_matiere_classe={$row['id_matiere_classe']}\n";
        echo "    id_classe={$row['id_classe']} ({$row['classe_libelle']})\n";
        echo "    id_matiere={$row['id_matiere']} ({$row['code']} - {$row['libelle']})\n";
        echo "    id_enseignant={$row['id_enseignant']}\n";
        echo "    nb_heures_par_semaine={$row['nb_heures_par_semaine']}\n";
        echo "    nb_heures_par_jour={$row['nb_heures_par_jour']}\n";
        echo "    deleted_at={$row['deleted_at']}\n";
        $fph_id_classe = $row['id_classe'];
        $fph_id_enseignant = $row['id_enseignant'];
        $fph_nb_heures_semaine = $row['nb_heures_par_semaine'];
        $fph_nb_heures_jour = $row['nb_heures_par_jour'];
    }
}

// 3. Find enseignant assigned to FPH
echo "\n=== 3. ENSEIGNANT FOR FPH ===\n";
if (isset($fph_id_enseignant)) {
    $r = $mysqli->query("SELECT * FROM enseignants WHERE id_enseignant = $fph_id_enseignant");
    if ($r->num_rows === 0) {
        echo "  NO enseignant found with id_enseignant=$fph_id_enseignant!\n";
    } else {
        $row = $r->fetch_assoc();
        echo "  id_enseignant={$row['id_enseignant']}\n";
        echo "    fullname={$row['fullname']}\n";
        echo "    first_name={$row['first_name']}\n";
        echo "    last_name={$row['last_name']}\n";
        echo "    email={$row['email']}\n";
        $fph_teacher_name = $row['fullname'];
    }
}

// 4. Check disponibilites_enseignants for that teacher
echo "\n=== 4. DISPONIBILITES ENSEIGNANT (id_enseignant=" . (isset($fph_id_enseignant) ? $fph_id_enseignant : '?') . ") ===\n";
if (isset($fph_id_enseignant)) {
    // Check for indisponible entries
    $r = $mysqli->query("SELECT * FROM disponibilites_enseignants WHERE id_enseignant = $fph_id_enseignant");
    if ($r->num_rows === 0) {
        echo "  NO disponibilites entries at all!\n";
    } else {
        echo "  Total entries: {$r->num_rows}\n";
        while ($row = $r->fetch_assoc()) {
            echo "  id={$row['id_disponibilite']} type={$row['type']} jour={$row['jour']} heure_debut={$row['heure_debut']} heure_fin={$row['heure_fin']}\n";
            if (isset($row['idCreneau'])) echo "    idCreneau={$row['idCreneau']}\n";
            if (isset($row['id_creneau'])) echo "    id_creneau={$row['id_creneau']}\n";
        }
    }

    // Check specifically for indisponible
    $r = $mysqli->query("SELECT * FROM disponibilites_enseignants WHERE id_enseignant = $fph_id_enseignant AND type = 'indisponible'");
    echo "  Indisponible entries: {$r->num_rows}\n";
    while ($row = $r->fetch_assoc()) {
        echo "    id={$row['id_disponibilite']} jour={$row['jour']} heure_debut={$row['heure_debut']} heure_fin={$row['heure_fin']}\n";
    }
}

// 5. Check contraintes_horaires for FPH
echo "\n=== 5. CONTRAINTES HORAIRES FOR FPH ===\n";
if (isset($fph_id_matiere)) {
    $r = $mysqli->query("SELECT * FROM contraintes_horaires WHERE id_matiere = $fph_id_matiere");
    if ($r->num_rows === 0) {
        echo "  NO contraintes_horaires for FPH\n";
    } else {
        while ($row = $r->fetch_assoc()) {
            echo "  " . json_encode($row) . "\n";
        }
    }
}

// Also check active year constraints
echo "\n=== 5b. ALL ACTIVE CONTRAINTES_HORAIRES ===\n";
$r = $mysqli->query("SELECT ch.*, m.code, m.libelle FROM contraintes_horaires ch JOIN matieres m ON ch.id_matiere = m.id_matiere WHERE ch.actif = 1 OR ch.actif IS NULL ORDER BY m.code LIMIT 50");
if ($r->num_rows === 0) {
    echo "  NO active contraintes_horaires\n";
} else {
    while ($row = $r->fetch_assoc()) {
        echo "  id_contrainte={$row['id_contrainte']} matiere={$row['code']} type={$row['type_contrainte']} val=" . json_encode($row) . "\n";
    }
}

// 6. Check horaires for the class
echo "\n=== 6. HORAIRES FOR CLASS " . (isset($fph_id_classe) ? $fph_id_classe : '?') . " ===\n";
if (isset($fph_id_classe)) {
    $r = $mysqli->query("SELECT * FROM horaires WHERE id_classe = $fph_id_classe");
    echo "  Total horaires entries: {$r->num_rows}\n";
    if ($r->num_rows > 0) {
        // Group by matiere
        $by_matiere = [];
        while ($row = $r->fetch_assoc()) {
            $code = isset($row['code_matiere']) ? $row['code_matiere'] : 'unknown';
            if (!isset($by_matiere[$code])) $by_matiere[$code] = 0;
            $by_matiere[$code]++;
        }
        echo "  Slots by matiere:\n";
        foreach ($by_matiere as $code => $count) {
            echo "    $code: $count slots\n";
        }
    }

    // Check if FPH has any horaires
    $r = $mysqli->query("
        SELECT h.*, m.code as code_matiere 
        FROM horaires h 
        LEFT JOIN matieres m ON h.id_matiere = m.id_matiere 
        WHERE h.id_classe = $fph_id_classe AND m.code LIKE '%FPH%'
    ");
    echo "  FPH horaires for this class: {$r->num_rows}\n";
    while ($row = $r->fetch_assoc()) {
        echo "    " . json_encode($row) . "\n";
    }
}

// 7. Check total slots vs occupied for the class
echo "\n=== 7. SLOT CAPACITY ANALYSIS FOR CLASS " . (isset($fph_id_classe) ? $fph_id_classe : '?') . " ===\n";
if (isset($fph_id_classe)) {
    // Check horaires structure
    $r = $mysqli->query("DESCRIBE horaires");
    echo "  horaires table structure:\n";
    while ($row = $r->fetch_assoc()) {
        echo "    {$row['Field']} {$row['Type']} {$row['Null']} {$row['Key']}\n";
    }
    
    // Check how many creneaux exist
    $r = $mysqli->query("SELECT COUNT(*) as cnt FROM creneaux");
    if ($r->num_rows > 0) {
        $row = $r->fetch_assoc();
        echo "  Total creneaux: {$row['cnt']}\n";
    }

    // Check what table holds slots
    $r = $mysqli->query("SHOW TABLES LIKE '%creneau%'");
    echo "  Tables matching creneau:\n";
    while ($row = $r->fetch_assoc()) {
        echo "    {$row[0]}\n";
    }
    $r = $mysqli->query("SHOW TABLES LIKE '%horaire%'");
    echo "  Tables matching horaire:\n";
    while ($row = $r->fetch_assoc()) {
        echo "    {$row[0]}\n";
    }
}

// 8. Check what class FPH belongs to - and show ALL matieres for that class
echo "\n=== 8. ALL MATIERES FOR CLASS " . (isset($fph_id_classe) ? $fph_id_classe : '?') . " ===\n";
if (isset($fph_id_classe)) {
    $r = $mysqli->query("
        SELECT mc.*, m.code, m.libelle, e.fullname as teacher_name
        FROM matieres_classes mc
        JOIN matieres m ON mc.id_matiere = m.id_matiere
        LEFT JOIN enseignants e ON mc.id_enseignant = e.id_enseignant
        WHERE mc.id_classe = $fph_id_classe AND mc.deleted_at IS NULL
        ORDER BY m.code
    ");
    echo "  Total matieres for class: {$r->num_rows}\n";
    $total_heures = 0;
    while ($row = $r->fetch_assoc()) {
        $heures = $row['nb_heures_par_semaine'];
        $total_heures += $heures;
        echo "    {$row['code']} ({$row['libelle']}): {$heures}h/sem, jour={$row['nb_heures_par_jour']}h, prof={$row['teacher_name']}\n";
    }
    echo "  TOTAL hours/week for class: {$total_heures}\n";
}

// 9. Check all matieres and their codes to see if FPH has variant
echo "\n=== 9. ALL MATIERES CODES ===\n";
$r = $mysqli->query("SELECT id_matiere, code, libelle FROM matieres ORDER BY code");
while ($row = $r->fetch_assoc()) {
    echo "  #{$row['id_matiere']} code='{$row['code']}' libelle='{$row['libelle']}'\n";
}

// 10. Check the current school year
echo "\n=== 10. SCHOOL YEARS / ANNEES ===\n";
$r = $mysqli->query("SHOW TABLES LIKE '%annee%'");
while ($row = $r->fetch_assoc()) {
    echo "  Table: {$row[0]}\n";
}
$r = $mysqli->query("SHOW TABLES LIKE '%year%'");
while ($row = $r->fetch_assoc()) {
    echo "  Table: {$row[0]}\n";
}

// Try common year tables
foreach (['annees_scolaires', 'annee_scolaire', 'school_years', 'annee'] as $tbl) {
    $r = $mysqli->query("SELECT * FROM $tbl LIMIT 5");
    if ($r) {
        echo "  Data from $tbl:\n";
        while ($row = $r->fetch_assoc()) {
            echo "    " . json_encode($row) . "\n";
        }
    }
}

// 11. Check for matiere_classe id_matiere = FPH id_matiere: is nb_heures_par_jour consistent?
echo "\n=== 11. FPH SCHEDULE DETAILS ===\n";
if (isset($fph_id_matiere) && isset($fph_id_classe)) {
    echo "  FPH needs: {$fph_nb_heures_semaine}h/sem, {$fph_nb_heures_jour}h/jour\n";
    if ($fph_nb_heures_jour > 0) {
        echo "  That means " . ($fph_nb_heures_semaine / $fph_nb_heures_jour) . " separate sessions needed\n";
    }
    
    // Check if constraint says 1 hour = 1 slot
    echo "  If 1h = 1 slot, need {$fph_nb_heures_semaine} slots total\n";
}

$mysqli->close();
echo "\n=== DIAGNOSTIC COMPLETE ===\n";
