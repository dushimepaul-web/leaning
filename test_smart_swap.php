<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(60);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli('localhost', 'root', '', 'vip_school');
$mysqli->set_charset('utf8mb4');

function get_setting_db($mysqli, $key, $default = null) {
    $stmt = $mysqli->prepare('SELECT valeur FROM parametres WHERE clef = ?');
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $r = $stmt->get_result();
    $row = $r->fetch_assoc();
    return $row ? $row['valeur'] : $default;
}

$heure_debut = get_setting_db($mysqli, 'heure_debut_journee', '07:30');
$duree_cours = max(1, (int)get_setting_db($mysqli, 'duree_cours', 45));
$duree_pause = max(0, (int)get_setting_db($mysqli, 'duree_pause', 20));
$duree_vigie = max(0, (int)get_setting_db($mysqli, 'duree_vigie', 10));
$nb_creneaux = max(1, (int)get_setting_db($mysqli, 'nb_creneaux_jour', 8));

$parts = explode(':', $heure_debut);
$h = isset($parts[0]) ? (int)$parts[0] : 7;
$m = isset($parts[1]) ? (int)$parts[1] : 30;
$current_minutes = $h * 60 + $m;

$creneaux = [];
if ($duree_vigie > 0) {
    $creneaux[] = ['id_creneau' => 'vigile', 'type_creneau' => 'vigile'];
    $current_minutes += $duree_vigie;
}
$milieu = (int)ceil($nb_creneaux / 2);
for ($i = 1; $i <= $nb_creneaux; $i++) {
    $creneaux[] = ['id_creneau' => $i, 'type_creneau' => 'cours'];
    $current_minutes += $duree_cours;
    if ($i == $milieu && $i < $nb_creneaux) {
        $creneaux[] = ['id_creneau' => 'pause' . $i, 'type_creneau' => 'pause'];
        $current_minutes += $duree_pause;
    }
}
$coursOnlyIds = array_column(array_filter($creneaux, fn($c) => $c['type_creneau'] === 'cours'), 'id_creneau');

$jours = [];
$r = $mysqli->query('SELECT id_jour, libelle FROM jours_semaine WHERE actif = 1 ORDER BY ordre ASC');
while ($row = $r->fetch_assoc()) $jours[] = $row;
$jourIds = array_column($jours, 'id_jour');

$maxAnnee = $mysqli->query('SELECT MAX(id_annee) as m FROM annees_scolaires')->fetch_assoc()['m'];

$matieresClasses = [];
$r = $mysqli->query("SELECT mc.*, m.code as matiere_code, m.libelle as matiere_libelle
FROM matieres_classes mc
JOIN matieres m ON mc.id_matiere = m.id_matiere
WHERE mc.deleted_at IS NULL AND mc.nb_heures_par_semaine > 0 AND mc.id_enseignant IS NOT NULL
ORDER BY mc.nb_heures_par_semaine DESC");
while ($row = $r->fetch_assoc()) $matieresClasses[] = $row;

$mapMC2Ens = [];
$r = $mysqli->query("SELECT id_matiere_classe, id_enseignant FROM enseignements WHERE deleted_at IS NULL");
while ($row = $r->fetch_assoc()) $mapMC2Ens[(int)$row['id_matiere_classe']] = (int)$row['id_enseignant'];

$indisponible = [];
$r = $mysqli->query("SELECT id_enseignant, id_jour, id_creneau FROM disponibilites_enseignants WHERE type = 'indisponible' AND deleted_at IS NULL");
while ($row = $r->fetch_assoc()) {
    $indisponible[(int)$row['id_enseignant']][(int)$row['id_jour']][$row['id_creneau']] = true;
}

$contraintes = [];
$r = $mysqli->query("SELECT * FROM contraintes_horaires WHERE id_annee = $maxAnnee AND deleted_at IS NULL");
while ($row = $r->fetch_assoc()) $contraintes[] = $row;
$contraintesIndex = [];
foreach ($contraintes as $ct) $contraintesIndex[$ct['type']][$ct['id_concerne']][] = $ct;

$blocs = [];
$blocId = 0;
foreach ($matieresClasses as $mc) {
    $nbHeuresSemaine = (int)$mc['nb_heures_par_semaine'];
    if ($nbHeuresSemaine <= 0) continue;
    $idEns = $mapMC2Ens[(int)$mc['id_matiere_classe']] ?? null;
    if (!$idEns) continue;
    $idProf = (int)$mc['id_enseignant'];
    $idCl = (int)$mc['id_classe'];
    $nbParJour = isset($mc['nb_heures_par_jour']) ? (int)$mc['nb_heures_par_jour'] : 0;
    if ($nbParJour <= 0) $nbParJour = 1;

    $totalBlocsSize = 0;
    while ($totalBlocsSize < $nbHeuresSemaine) {
        $remaining = $nbHeuresSemaine - $totalBlocsSize;
        $tailleBloc = min($remaining, $nbParJour);
        $blocs[] = [
            'id' => $blocId++,
            'id_matiere_classe' => (int)$mc['id_matiere_classe'],
            'id_matiere' => (int)$mc['id_matiere'],
            'id_enseignant' => $idProf,
            'id_enseignement' => (int)$idEns,
            'id_classe' => $idCl,
            'matiere_libelle' => $mc['matiere_libelle'] ?? '',
            'matiere_code' => $mc['matiere_code'] ?? '',
            'taille' => $tailleBloc,
            'placed' => false,
            'jour' => null,
            'creneaux' => [],
            'difficulty' => 0,
        ];
        $totalBlocsSize += $tailleBloc;
    }
}

usort($blocs, function($a, $b) {
    if ($b['taille'] !== $a['taille']) return $b['taille'] - $a['taille'];
    return $b['difficulty'] - $a['difficulty'];
});

$grille = [];
$occupationProf = [];
$heuresParJourMC = [];
$created = 0;

$estLibre = function($idProf, $idClasse, $idMatiere, $idJour, $idCreneau) use (&$grille, &$occupationProf, &$indisponible, &$contraintesIndex) {
    if (isset($grille[$idClasse . '_' . $idJour . '_' . $idCreneau])) return false;
    if (isset($occupationProf[$idProf . '_' . $idJour . '_' . $idCreneau])) return false;
    if (isset($indisponible[$idProf][$idJour][$idCreneau])) return false;
    if (isset($contraintesIndex['matiere'][$idMatiere])) {
        foreach ($contraintesIndex['matiere'][$idMatiere] as $ct) {
            if ($ct['regle'] === 'interdit' && $ct['id_jour'] == $idJour) {
                if (!$ct['id_creneau_debut'] || ($idCreneau >= $ct['id_creneau_debut'] && $idCreneau <= $ct['id_creneau_fin'])) return false;
            }
        }
    }
    return true;
};

$placerSeance = function(&$seance) use (&$grille, &$occupationProf, &$heuresParJourMC, &$created) {
    $gkey = $seance['id_classe'] . '_' . $seance['id_jour'] . '_' . $seance['id_creneau'];
    $pkey = $seance['id_enseignant'] . '_' . $seance['id_jour'] . '_' . $seance['id_creneau'];
    $grille[$gkey] = [
        'id_enseignement' => (int)$seance['id_enseignement'],
        'id_matiere' => (int)$seance['id_matiere'],
        'id_matiere_classe' => (int)$seance['id_matiere_classe'],
        'id_enseignant' => (int)$seance['id_enseignant'],
        'id_classe' => (int)$seance['id_classe'],
        'id_jour' => (int)$seance['id_jour'],
        'id_creneau' => $seance['id_creneau'],
    ];
    $occupationProf[$pkey] = true;
    $hkey = $seance['id_matiere_classe'] . '_' . $seance['id_jour'];
    $heuresParJourMC[$hkey] = ($heuresParJourMC[$hkey] ?? 0) + 1;
    $seance['placed'] = true;
    $created++;
};

$retirerSeanceByInfo = function($seance) use (&$grille, &$occupationProf, &$heuresParJourMC, &$created) {
    $gkey = $seance['id_classe'] . '_' . $seance['id_jour'] . '_' . $seance['id_creneau'];
    $pkey = $seance['id_enseignant'] . '_' . $seance['id_jour'] . '_' . $seance['id_creneau'];
    unset($grille[$gkey]);
    unset($occupationProf[$pkey]);
    $hkey = $seance['id_matiere_classe'] . '_' . $seance['id_jour'];
    if (isset($heuresParJourMC[$hkey]) && $heuresParJourMC[$hkey] > 0) $heuresParJourMC[$hkey]--;
    if ($created > 0) $created--;
};

$verifierContraintesProfInterdit = function($idProf, $idMatiere, $idJour, $idCreneau) use (&$indisponible, &$contraintesIndex) {
    if (isset($indisponible[$idProf][$idJour][$idCreneau])) return false;
    if (isset($contraintesIndex['matiere'][$idMatiere])) {
        foreach ($contraintesIndex['matiere'][$idMatiere] as $ct) {
            if ($ct['regle'] === 'interdit' && $ct['id_jour'] == $idJour) {
                if (!$ct['id_creneau_debut'] || ($idCreneau >= $ct['id_creneau_debut'] && $idCreneau <= $ct['id_creneau_fin'])) return false;
            }
        }
    }
    return true;
};

$chercherFenetre = function($taille, $idJour, $idProf, $idClasse, $idMatiere, &$heuresParJourMC) use ($coursOnlyIds, $estLibre) {
    $nbCours = count($coursOnlyIds);
    if ($taille <= 0 || $taille > $nbCours) return null;
    $best = null; $bestScore = -99999;
    for ($start = 0; $start <= $nbCours - $taille; $start++) {
        $fenetreIds = array_slice($coursOnlyIds, $start, $taille);
        $toutesLibres = true;
        foreach ($fenetreIds as $cid) {
            if (!$estLibre($idProf, $idClasse, $idMatiere, $idJour, $cid)) { $toutesLibres = false; break; }
        }
        if (!$toutesLibres) continue;
        $score = 100;
        $totalChargeJour = 0;
        foreach ($heuresParJourMC as $hk => $hv) {
            if (substr($hk, -strlen('_' . $idJour)) === '_' . $idJour) $totalChargeJour += $hv;
        }
        $score -= $totalChargeJour * 5;
        if ($score > $bestScore) { $bestScore = $score; $best = $fenetreIds; }
    }
    return $best;
};

// PASS 1
foreach ($blocs as &$bloc) {
    if ($bloc['placed']) continue;
    foreach ($jourIds as $idJ) {
        $fenetre = $chercherFenetre($bloc['taille'], $idJ, $bloc['id_enseignant'], $bloc['id_classe'], $bloc['id_matiere'], $heuresParJourMC);
        if ($fenetre !== null) {
            $bloc['jour'] = $idJ; $bloc['creneaux'] = $fenetre; $bloc['placed'] = true;
            foreach ($fenetre as $cid) {
                $seancePl = ['id_enseignement'=>$bloc['id_enseignement'],'id_matiere'=>$bloc['id_matiere'],'id_matiere_classe'=>$bloc['id_matiere_classe'],'id_enseignant'=>$bloc['id_enseignant'],'id_classe'=>$bloc['id_classe'],'id_jour'=>$idJ,'id_creneau'=>$cid,'placed'=>true];
                $placerSeance($seancePl);
            }
            break;
        }
    }
}
unset($bloc);

// PASS 4 test
echo "Running PASS 4 simulation...\n";
$placementsForces = [];
$coursDeplacesSet = [];

foreach ($blocs as &$bloc) {
    if ($bloc['placed']) continue;
    $targetProf = $bloc['id_enseignant'];
    $targetClasse = $bloc['id_classe'];
    $targetMatiere = $bloc['id_matiere'];

    $slotsProfLibre = [];
    $slotsClasseLibre = [];
    foreach ($jourIds as $jid) {
        foreach ($coursOnlyIds as $cid) {
            if (!isset($occupationProf[$targetProf . '_' . $jid . '_' . $cid]) &&
                !isset($indisponible[$targetProf][$jid][$cid]) &&
                $verifierContraintesProfInterdit($targetProf, $targetMatiere, $jid, $cid, $contraintesIndex)) {
                $slotsProfLibre[$jid . '_' . $cid] = ['jour' => $jid, 'creneau' => $cid];
            }
            if (!isset($grille[$targetClasse . '_' . $jid . '_' . $cid])) {
                $slotsClasseLibre[$jid . '_' . $cid] = ['jour' => $jid, 'creneau' => $cid];
            }
        }
    }

    $swapEffectue = false;
    $candidatsSwaps = [];
    foreach ($slotsClasseLibre as $mKey => $m) {
        $gkey = $targetClasse . '_' . $m['jour'] . '_' . $m['creneau'];
        if (!isset($grille[$gkey])) continue;
        $coursX = $grille[$gkey];
        $xMatiereClasse = $coursX['id_matiere_classe'];
        $xProf = $coursX['id_enseignant'];
        $xKey = $xMatiereClasse . '_' . $m['jour'] . '_' . $m['creneau'];
        if (isset($coursDeplacesSet[$xKey])) continue;

        foreach ($slotsProfLibre as $lKey => $l) {
            if ($l['jour'] === $m['jour'] && $l['creneau'] === $m['creneau']) continue;
            if (isset($grille[$targetClasse . '_' . $l['jour'] . '_' . $l['creneau']])) continue;
            if (isset($occupationProf[$xProf . '_' . $l['jour'] . '_' . $l['creneau']]) &&
                ($xProf . '_' . $l['jour'] . '_' . $l['creneau'] !== $targetProf . '_' . $m['jour'] . '_' . $m['creneau'])) continue;
            if (isset($indisponible[$xProf][$l['jour']][$l['creneau']])) continue;
            if (!$verifierContraintesProfInterdit($xProf, $coursX['id_matiere'], $l['jour'], $l['creneau'], $contraintesIndex)) continue;

            $candidatsSwaps[] = ['score' => 60, 'coursX' => $coursX, 'm' => $m, 'l' => $l, 'xKey' => $xKey];
        }
    }

    if (!empty($candidatsSwaps)) {
        usort($candidatsSwaps, fn($a, $b) => $b['score'] - $a['score']);
        $meilleurSwap = $candidatsSwaps[0];
        $coursX = $meilleurSwap['coursX'];
        $m = $meilleurSwap['m'];
        $l = $meilleurSwap['l'];

        $retirerSeanceByInfo($coursX);
        $coursX['id_jour'] = $l['jour'];
        $coursX['id_creneau'] = $l['creneau'];
        $placerSeance($coursX);

        $seanceB = [
            'id_enseignement' => $bloc['id_enseignement'],
            'id_matiere' => $bloc['id_matiere'],
            'id_matiere_classe' => $bloc['id_matiere_classe'],
            'id_enseignant' => $targetProf,
            'id_classe' => $targetClasse,
            'id_jour' => $m['jour'],
            'id_creneau' => $m['creneau'],
            'placed' => true,
        ];
        $placerSeance($seanceB);

        $bloc['placed'] = true;
        $coursDeplacesSet[$meilleurSwap['xKey']] = true;
        $swapEffectue = true;
        echo "SWAP SUCCESSFUL for {$bloc['matiere_code']} at J{$m['jour']} C{$m['creneau']} (moved X to J{$l['jour']} C{$l['creneau']})\n";
    }

    if ($swapEffectue) continue;

    // Fallback isolated
    $heuresRestantes = $bloc['taille'];
    while ($heuresRestantes > 0) {
        $placeHeure = false;
        foreach ($jourIds as $idJ) {
            foreach ($coursOnlyIds as $cid) {
                if ($estLibre($bloc['id_enseignant'], $bloc['id_classe'], $bloc['id_matiere'], $idJ, $cid)) {
                    $seancePl = [
                        'id_enseignement' => $bloc['id_enseignement'],
                        'id_matiere' => $bloc['id_matiere'],
                        'id_matiere_classe' => $bloc['id_matiere_classe'],
                        'id_enseignant' => $bloc['id_enseignant'],
                        'id_classe' => $bloc['id_classe'],
                        'id_jour' => $idJ,
                        'id_creneau' => $cid,
                        'placed' => true,
                    ];
                    $placerSeance($seancePl);
                    $heuresRestantes--;
                    $placeHeure = true;
                    break 2;
                }
            }
        }
        if (!$placeHeure) break;
    }
    if ($heuresRestantes === 0) $bloc['placed'] = true;
}
unset($bloc);

$unplaced = 0;
foreach ($blocs as $b) {
    if (!$b['placed']) $unplaced++;
}
echo "Total unplaced after PASS 4: $unplaced\n";

$mysqli->close();
