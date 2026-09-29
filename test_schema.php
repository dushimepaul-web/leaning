<?php
define('BASEPATH', 'true');
$mysqli = new mysqli('localhost', 'root', '', 'vip_school');
if ($mysqli->connect_error) { die($mysqli->connect_error); }

$r = $mysqli->query("SELECT MAX(id_generation) g FROM horaires");
$g = $r->fetch_assoc()['g'];
echo "generation active: $g\n";

$r = $mysqli->query("SELECT type, COUNT(*) c FROM horaires WHERE deleted_at IS NULL AND id_generation=$g GROUP BY type");
while ($row = $r->fetch_assoc()) echo " type {$row['type']}: {$row['c']}\n";

$r = $mysqli->query("SELECT COUNT(*) c FROM horaires WHERE deleted_at IS NULL");
echo "total actifs: " . $r->fetch_assoc()['c'] . "\n";

// Heures attendues vs placées par classe
$r = $mysqli->query("SELECT c.libelle, COALESCE(h.placed,0) placed, COALESCE(s.total,0) attendu
 FROM classes c
 LEFT JOIN (SELECT id_classe, COUNT(*) placed FROM horaires WHERE deleted_at IS NULL AND id_generation=$g GROUP BY id_classe) h ON h.id_classe=c.id_classe
 LEFT JOIN (SELECT id_classe, SUM(nb_heures_par_semaine) total FROM matieres_classes WHERE deleted_at IS NULL GROUP BY id_classe) s ON s.id_classe=c.id_classe
 WHERE c.deleted_at IS NULL ORDER BY c.ordre");
while ($row = $r->fetch_assoc()) {
    printf(" %-12s place=%-4s attendu=%-6s %s\n", $row['libelle'], $row['placed'], $row['attendu'],
        ((int)$row['placed'] === (int)$row['attendu']) ? 'OK' : 'ECART');
}

// Conflits DB (integrite)
echo "\n=== verifications integrite ===\n";
$checks = [
 'classe double-booked' => "SELECT COUNT(*) c FROM (SELECT id_classe, id_jour, id_creneau, COUNT(*) n FROM horaires WHERE deleted_at IS NULL AND id_generation=$g GROUP BY 1,2,3 HAVING n>1) t",
 'prof double-booked' => "SELECT COUNT(*) c FROM (SELECT id_enseignant, id_jour, id_creneau, COUNT(*) n FROM horaires WHERE deleted_at IS NULL AND id_generation=$g GROUP BY 1,2,3 HAVING n>1) t",
 'prof indispo violee' => "SELECT COUNT(*) c FROM horaires h JOIN disponibilites_enseignants d ON d.id_enseignant=h.id_enseignant AND d.id_jour=h.id_jour AND d.id_creneau=h.id_creneau AND d.type='indisponible' AND d.deleted_at IS NULL WHERE h.deleted_at IS NULL AND h.id_generation=$g",
 'cours le week-end/creneau invalide' => "SELECT COUNT(*) c FROM horaires h LEFT JOIN jours_semaine j ON j.id_jour=h.id_jour AND j.deleted_at IS NULL AND j.actif=1 WHERE h.deleted_at IS NULL AND h.id_generation=$g AND j.id_jour IS NULL",
];
foreach ($checks as $label => $sql) {
    $r = $mysqli->query($sql);
    $row = $r ? $r->fetch_assoc() : null;
    printf(" - %-40s : %s\n", $label, $row ? $row['c'] : 'ERR');
}

// Max heures meme matiere par jour
$r = $mysqli->query("SELECT MAX(n) m FROM (SELECT id_classe, id_matiere, id_jour, COUNT(*) n FROM horaires WHERE deleted_at IS NULL AND id_generation=$g GROUP BY 1,2,3) t");
echo " - max memes cours/jour/classe : " . $r->fetch_assoc()['m'] . "\n";

$mysqli->close();
?>
