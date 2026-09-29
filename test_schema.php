<?php
define('BASEPATH', 'true');
$mysqli = new mysqli('localhost', 'root', '', 'vip_school');
if ($mysqli->connect_error) { die($mysqli->connect_error); }

echo "=== 1. Limites journalieres vs heures/semaine ===\n";
$r = $mysqli->query("SELECT mc.id_matiere_classe, c.libelle classe, m.code, mc.nb_heures_par_semaine w, mc.nb_heures_par_jour d
 FROM matieres_classes mc JOIN classes c ON c.id_classe=mc.id_classe JOIN matieres m ON m.id_matiere=mc.id_matiere
 WHERE mc.deleted_at IS NULL AND mc.nb_heures_par_semaine > 0");
$bad = 0;
while ($row = $r->fetch_assoc()) {
    $w = (int)$row['w']; $d = (int)$row['d'];
    $max = 5 * $d;
    if ($w > $max) { echo "  INFEASIBLE: {$row['classe']} {$row['code']} {$w}h/semaine > 5 x {$d} = {$max}\n"; $bad++; }
}
if (!$bad) echo "  OK - toutes les matieres tiennent dans la semaine avec leur limite journaliere\n";

echo "\n=== 2. Marges enseignants (capacite - charge) ===\n";
$r = $mysqli->query("SELECT e.id_enseignant, e.fullname,
  COALESCE((SELECT SUM(nb_heures_par_semaine) FROM matieres_classes mc WHERE mc.id_enseignant=e.id_enseignant AND mc.deleted_at IS NULL),0) charge
 FROM enseignants e WHERE e.deleted_at IS NULL");
$teachers = [];
while ($row = $r->fetch_assoc()) $teachers[] = $row;

$r = $mysqli->query("SELECT id_enseignant, id_jour, id_creneau FROM disponibilites_enseignants WHERE type='indisponible' AND deleted_at IS NULL");
$indispo = [];
while ($row = $r->fetch_assoc()) $indispo[(int)$row['id_enseignant']][(int)$row['id_jour']][(int)$row['id_creneau']] = true;

$jours = [1,2,3,4,5]; $creneaux = [1,2,3,4,5,6,7,8];
$tight = 0;
foreach ($teachers as $t) {
    $charge = (int)$t['charge'];
    if ($charge <= 0) continue;
    $cap = 0;
    foreach ($jours as $j) foreach ($creneaux as $c) if (!isset($indispo[(int)$t['id_enseignant']][$j][$c])) $cap++;
    $marge = $cap - $charge;
    $flag = $marge < 0 ? 'INFEASIBLE' : ($marge === 0 ? 'SERRURE' : '');
    if ($marge <= 2) { printf("  %-28s charge=%-3d cap=%-3d marge=%-3d %s\n", $t['fullname'], $charge, $cap, $marge, $flag); $tight++; }
}
echo "  (enseignants avec marge <=2 : $tight)\n";

echo "\n=== 3. Charge des classes vs creneaux ===\n";
$r = $mysqli->query("SELECT c.libelle, SUM(mc.nb_heures_par_semaine) h, COUNT(DISTINCT mc.id_matiere) nmat
 FROM classes c JOIN matieres_classes mc ON mc.id_classe=c.id_classe AND mc.deleted_at IS NULL
 WHERE c.deleted_at IS NULL GROUP BY c.id_classe ORDER BY c.ordre");
while ($row = $r->fetch_assoc()) printf("  %-12s %sh (matieres: %d) / 40 creneaux %s\n", $row['libelle'], $row['h'], $row['nmat'], ((int)$row['h'] <= 40) ? '' : 'DEPASSE');

echo "\n=== 4. Enseignants presents sur combien de jours ===\n";
$r = $mysqli->query("SELECT e.fullname, COUNT(DISTINCT d.id_jour) jours
 FROM disponibilites_enseignants d JOIN enseignants e ON e.id_enseignant=d.id_enseignant
 WHERE d.type='indisponible' AND d.deleted_at IS NULL GROUP BY d.id_enseignant HAVING jours>0");
while ($row = $r->fetch_assoc()) printf("  %-28s indispo sur %d/5 jours\n", $row['fullname'], 5 - $row['jours'] > 0 ? $row['jours'] : $row['jours']);

$mysqli->close();
?>
