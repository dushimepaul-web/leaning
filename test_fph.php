<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(30);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli('localhost', 'root', '', 'vip_school');
$mysqli->set_charset('utf8mb4');

$r = $mysqli->query("SELECT mc.*, m.code, m.libelle, c.libelle as classe_libelle 
FROM matieres_classes mc 
JOIN matieres m ON mc.id_matiere = m.id_matiere 
JOIN classes c ON mc.id_classe = c.id_classe 
WHERE m.code LIKE '%FPH%' AND mc.deleted_at IS NULL");
while ($row = $r->fetch_assoc()) {
    echo "FPH in class {$row['classe_libelle']} (id_classe={$row['id_classe']}): sem={$row['nb_heures_par_semaine']}h, jour={$row['nb_heures_par_jour']}h, prof#{$row['id_enseignant']}\n";
}

$mysqli->close();
