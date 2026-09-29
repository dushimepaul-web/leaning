<?php
$m = new mysqli('localhost', 'root', '', 'vip_school');
if ($m->connect_error) die($m->connect_error);

function q($m, $sql) {
    $r = $m->query($sql);
    if (!$r) { echo "SQL ERROR: " . $m->error . "\n"; return []; }
    $out = [];
    while ($row = $r->fetch_assoc()) $out[] = $row;
    return $out;
}

echo "=== Derniere generation ===\n";
foreach (q($m, "SELECT * FROM horaires_generations ORDER BY id_generation DESC LIMIT 1") as $g) {
    printf("  id=%s date=%s sessions=%s\n", $g['id_generation'], $g['cree_le'] ?? '', $g['nb_sessions'] ?? '');
}

echo "\n=== KARORERO Charles (id=48) : ses heures placees ===\n";
$sql = "SELECT h.id_jour, h.id_creneau, c.libelle classe, m.code, h.type
 FROM horaires h JOIN classes c ON c.id_classe=h.id_classe
 LEFT JOIN matieres m ON m.id_matiere=h.id_matiere
 WHERE h.id_enseignant=48 AND h.id_generation=95 AND h.deleted_at IS NULL
 ORDER BY h.id_jour, h.id_creneau";
$slots = [];
foreach (q($m, $sql) as $r) {
    printf("  J%s C%-2s %-10s %-10s %s\n", $r['id_jour'], $r['id_creneau'], $r['classe'], $r['code'], $r['type']);
    $slots[$r['id_jour'] . '-' . $r['id_creneau']] = true;
}
echo "  places: " . count($slots) . "/16 disponibles (J3+J4)\n";
$free = [];
foreach ([3,4] as $j) foreach ([1,2,3,4,5,6,7,8] as $c) if (!isset($slots["$j-$c"])) $free[] = "J{$j}C{$c}";
echo "  libres: " . implode(', ', $free) . "\n";

echo "\n=== 3eme IG : emploi du temps ===\n";
foreach (q($m, "SELECT h.id_jour, h.id_creneau, m.code, e.fullname ens, h.type FROM horaires h
 JOIN classes c ON c.id_classe=h.id_classe
 LEFT JOIN matieres m ON m.id_matiere=h.id_matiere
 LEFT JOIN enseignants e ON e.id_enseignant=h.id_enseignant
 WHERE c.libelle='3Ã¨me IG' AND h.deleted_at IS NULL
 ORDER BY h.id_jour, h.id_creneau") as $r) {
    printf("  J%s C%-2s %-10s %-24s %s\n", $r['id_jour'], $r['id_creneau'], $r['code'], $r['ens'], $r['type']);
}

echo "\n=== Taux de remplissage par classe ===\n";
foreach (q($m, "SELECT c.libelle, COUNT(*) n FROM horaires h JOIN classes c ON c.id_classe=h.id_classe WHERE h.deleted_at IS NULL GROUP BY h.id_classe ORDER BY c.ordre") as $r) {
    printf("  %-12s %d/40\n", $r['libelle'], $r['n']);
}
?>

