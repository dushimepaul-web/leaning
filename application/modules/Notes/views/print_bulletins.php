<?php defined('BASEPATH') OR exit('No script direct access allowed');
function nf($v){return $v>0?number_format($v,1):'-';}
function nf2($v){return $v>0?number_format($v,2):'-';}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Bulletins - <?= htmlspecialchars($classe_nom) ?></title>
<style>
  body { font-family: Arial, Calibri, sans-serif; font-size: 11pt; color: #000; margin: 0; padding: 20px; background: #e5e5e5; }
  .bulletin-page { background: #fff; width: 297mm; min-height: 210mm; margin: 0 auto 30px auto; padding: 10mm 8mm; box-shadow: 0 4px 12px rgba(0,0,0,0.15); page-break-after: always; box-sizing: border-box; }
  .h-row { display: flex; justify-content: space-between; font-size: 12pt; font-weight: 700; line-height: 1.7; padding: 0 2px; }
  .h-row .h-right { text-align: right; }
  table.bul-table { border-collapse: collapse; width: 100%; font-size: 8.5pt; margin-top: 8px; table-layout: fixed; }
  table.bul-table th, table.bul-table td { border: 1px solid #000; padding: 3px 4px; text-align: center; font-weight: 400; }
  table.bul-table thead th { background: #D9D9D9; font-weight: 700; font-size: 8pt; }
  table.bul-table thead th.branches-header { background: #fff; width: 130px; min-width: 130px; }
  .text-left { text-align: left !important; padding-left: 6px !important; }
  .branches { text-align: left; font-weight: 700; padding-left: 6px; }
  .matiere { color: #000; font-weight: 400; }
  .gris { background: #D9D9D9; }
  .no-print { display: none; }
  @media print {
    body { background: #fff; padding: 0; }
    .bulletin-page { box-shadow: none; margin: 0; width: 100%; page-break-after: always; }
  }
</style>
</head>
<body>

<?php
$notes_map = [];
foreach ($aggregated_data as $row) {
    $notes_map[$row['inscription_id']][$row['subject_id']] = $row;
}

$ress_active = isset($ressources_active) ? intval($ressources_active) !== 0 : true;
$comp_active = isset($competences_active) ? intval($competences_active) !== 0 : true;
$both_active = $ress_active && $comp_active;
$pct_comp = isset($competences_pourcentage) ? floatval($competences_pourcentage) : 40;
$pct_ress = isset($ressources_pourcentage) ? floatval($ressources_pourcentage) : 60;
$facteur_points = isset($facteur_points_heure) ? floatval($facteur_points_heure) : floatval(get_setting('facteur_points_heure', 15));
$col_span = $both_active ? 4 : 3;
$sub_head = $both_active ? '<th>TJ</th><th>RESS</th><th>COMP</th><th>TOT</th>' : '<th>TJ</th><th>EX</th><th>TOT</th>';
// Un seul actif : la catégorie active absorbe tout l'EX (EX = TJ)
$cat_active_ress = $ress_active && !$comp_active; // seule RESS active
$cat_active_comp = $comp_active && !$ress_active; // seule COMP active

$conduiteVal = 60;
$relTj = 10; $relEx = 10; $relTot = 20;

foreach ($eleves as $eleve_id => $eleve):
    $stud_notes = $notes_map[$eleve_id] ?? [];
    $nbPeriodes = 3;
    $cd = isset($conduite_map) && isset($conduite_map[$eleve_id]) ? $conduite_map[$eleve_id] : [];
    $cd1 = $cd[1] ?? $conduiteVal; $cd2 = $cd[2] ?? $conduiteVal; $cd3 = $cd[3] ?? $conduiteVal;
    $cdAnn = $cd1 + $cd2 + $cd3;
?>

<div class="bulletin-page">
  <div class="h-row">
    <span>SECTION: <?= htmlspecialchars($classe_nom) ?></span>
    <span class="h-right">ANNEE SCOLAIRE : <?= htmlspecialchars($annee_scolaire) ?></span>
  </div>
  <div class="h-row">
    <span>NOM ET PRENOM : <?= htmlspecialchars($eleve['fullname']) ?></span>
    <span class="h-right">Nombre d'Eleves : <?= count($eleves) ?></span>
  </div>
  <div class="h-row">
    <span>N&deg; d'ordre : <?= htmlspecialchars($eleve['matricule'] ?? '.................') ?></span>
  </div>
  <div class="h-row">
    <span>Classe : <?= htmlspecialchars($classe_nom) ?></span>
  </div>

  <table class="bul-table">
    <thead>
      <tr>
        <th class="branches-header" rowspan="2"></th>
        <th colspan="<?= $col_span ?>">MAXIMA</th>
        <th colspan="<?= $col_span ?>">1er TRIMESTRE</th>
        <th colspan="<?= $col_span ?>">2e TRIMESTRE</th>
        <th colspan="<?= $col_span ?>">3e TRIMESTRE</th>
        <th colspan="3">TOTAUX ANNUELS</th>
      </tr>
      <tr>
        <?= $sub_head ?>
        <?= $sub_head ?>
        <?= $sub_head ?>
        <?= $sub_head ?>
        <th>MAX</th><th>TOT</th><th>%</th>
      </tr>
    </thead>
    <tbody>
<?php
$perTot = [
    1 => ['tj'=>0,'ex'=>0,'tp'=>0,'tot'=>0],
    2 => ['tj'=>0,'ex'=>0,'tp'=>0,'tot'=>0],
    3 => ['tj'=>0,'ex'=>0,'tp'=>0,'tot'=>0],
];
$aAnnMax = 0; $aAnnNote = 0;

foreach ($subjects as $subj):
    $s_data = $stud_notes[$subj['id']] ?? null;
    $heures = isset($subj['nb_heures_par_semaine']) ? (float)$subj['nb_heures_par_semaine'] : 0;

    // Maxima : TJ = coefficient calculé (heures × facteur) ; EX = TJ ; RESS/COMP selon les pourcentages (ou 100% si seul actif)
    $max_tj = $heures * $facteur_points;
    if ($both_active) {
        $max_comp = round($max_tj * $pct_comp / 100, 1);
        $max_ress = round($max_tj * $pct_ress / 100, 1);
    } elseif ($cat_active_ress) {
        $max_comp = 0;
        $max_ress = $max_tj;
    } else {
        $max_comp = $max_tj;
        $max_ress = 0;
    }
    $max_tot = $max_tj + $max_comp + $max_ress;
    $max_sub_annuel = $max_tot * 3; // 3 periods

    // Actual notes from evaluations (catégorie inactive = 0)
    $t1_tj = $s_data ? (float)$s_data['note_t1_tj'] : 0;
    $t1_comp = $s_data ? (float)$s_data['note_t1_comp'] : 0;
    $t1_ress = $s_data ? (float)$s_data['note_t1_ress'] : 0;
    if (!$both_active) { if ($cat_active_ress) $t1_comp = 0; else $t1_ress = 0; }
    $t1_tot = $t1_tj + $t1_comp + $t1_ress;
    $t2_tj = $s_data ? (float)$s_data['note_t2_tj'] : 0;
    $t2_comp = $s_data ? (float)$s_data['note_t2_comp'] : 0;
    $t2_ress = $s_data ? (float)$s_data['note_t2_ress'] : 0;
    if (!$both_active) { if ($cat_active_ress) $t2_comp = 0; else $t2_ress = 0; }
    $t2_tot = $t2_tj + $t2_comp + $t2_ress;
    $t3_tj = $s_data ? (float)$s_data['note_t3_tj'] : 0;
    $t3_comp = $s_data ? (float)$s_data['note_t3_comp'] : 0;
    $t3_ress = $s_data ? (float)$s_data['note_t3_ress'] : 0;
    if (!$both_active) { if ($cat_active_ress) $t3_comp = 0; else $t3_ress = 0; }
    $t3_tot = $t3_tj + $t3_comp + $t3_ress;

    $tot_sub_annuel = $t1_tot + $t2_tot + $t3_tot;
    $pct_sub = $max_sub_annuel > 0 ? round(($tot_sub_annuel / $max_sub_annuel) * 100, 2) : 0;

    $perTot[1]['tj'] += $t1_tj; $perTot[1]['ex'] += $t1_comp; $perTot[1]['tp'] += $t1_ress; $perTot[1]['tot'] += $t1_tot;
    $perTot[2]['tj'] += $t2_tj; $perTot[2]['ex'] += $t2_comp; $perTot[2]['tp'] += $t2_ress; $perTot[2]['tot'] += $t2_tot;
    $perTot[3]['tj'] += $t3_tj; $perTot[3]['ex'] += $t3_comp; $perTot[3]['tp'] += $t3_ress; $perTot[3]['tot'] += $t3_tot;
    $aAnnMax += $max_sub_annuel;
    $aAnnNote += $tot_sub_annuel;
?>
      <tr>
        <td class="text-left matiere"><?= htmlspecialchars($subj['name']) ?></td>
        <?php if ($both_active): ?>
        <td><?= nf($max_tj) ?></td>
        <td><?= nf($max_ress) ?></td>
        <td><?= nf($max_comp) ?></td>
        <td><strong><?= nf($max_tot) ?></strong></td>
        <?php else: ?>
        <td><?= nf($max_tj) ?></td>
        <td><?= nf($max_tj) ?></td>
        <td><strong><?= nf($max_tot) ?></strong></td>
        <?php endif; ?>

        <?php if ($both_active): ?>
        <td><?= nf($t1_tj) ?></td>
        <td><?= nf($t1_ress) ?></td>
        <td><?= nf($t1_comp) ?></td>
        <td><strong><?= nf($t1_tot) ?></strong></td>
        <?php else: ?>
        <td><?= nf($t1_tj) ?></td>
        <td><?= nf($t1_comp + $t1_ress) ?></td>
        <td><strong><?= nf($t1_tot) ?></strong></td>
        <?php endif; ?>

        <?php if ($both_active): ?>
        <td><?= nf($t2_tj) ?></td>
        <td><?= nf($t2_ress) ?></td>
        <td><?= nf($t2_comp) ?></td>
        <td><strong><?= nf($t2_tot) ?></strong></td>
        <?php else: ?>
        <td><?= nf($t2_tj) ?></td>
        <td><?= nf($t2_comp + $t2_ress) ?></td>
        <td><strong><?= nf($t2_tot) ?></strong></td>
        <?php endif; ?>

        <?php if ($both_active): ?>
        <td><?= nf($t3_tj) ?></td>
        <td><?= nf($t3_ress) ?></td>
        <td><?= nf($t3_comp) ?></td>
        <td><strong><?= nf($t3_tot) ?></strong></td>
        <?php else: ?>
        <td><?= nf($t3_tj) ?></td>
        <td><?= nf($t3_comp + $t3_ress) ?></td>
        <td><strong><?= nf($t3_tot) ?></strong></td>
        <?php endif; ?>

        <td><strong><?= nf($max_sub_annuel) ?></strong></td>
        <td><strong><?= nf($tot_sub_annuel) ?></strong></td>
        <td><strong><?= $pct_sub ?>%</strong></td>
      </tr>
<?php endforeach; ?>

      <!-- B: Sous-Tot -->
      <tr>
        <td class="text-left branches">Sous-Tot</td>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span-1) ?><td><strong><?= nf($perTot[1]['tot']) ?></strong></td>
        <?= str_repeat('<td></td>', $col_span-1) ?><td><strong><?= nf($perTot[2]['tot']) ?></strong></td>
        <?= str_repeat('<td></td>', $col_span-1) ?><td><strong><?= nf($perTot[3]['tot']) ?></strong></td>
        <td><strong><?= nf($aAnnMax) ?></strong></td>
        <td><strong><?= nf($aAnnNote) ?></strong></td>
        <td><strong><?= $aAnnMax>0?number_format($aAnnNote/$aAnnMax*100,2):'-' ?>%</strong></td>
      </tr>

      <!-- C: Conduite -->
      <tr>
        <td class="text-left branches">Conduite</td>
        <td class="gris"><?= $conduiteVal ?></td><?= str_repeat('<td></td>', $col_span-2) ?><td><?= $conduiteVal ?></td>
        <?= str_repeat('<td></td>', $col_span-1) ?><td><?= $cd1 ?></td>
        <?= str_repeat('<td></td>', $col_span-1) ?><td><?= $cd2 ?></td>
        <?= str_repeat('<td></td>', $col_span-1) ?><td><?= $cd3 ?></td>
        <td><strong><?= $conduiteVal * $nbPeriodes ?></strong></td>
        <td><strong><?= $cdAnn ?></strong></td>
        <td><strong><?= $cdAnn>0?number_format($cdAnn/($conduiteVal*$nbPeriodes)*100,2):'-' ?>%</strong></td>
      </tr>

      <!-- D: Totaux -->
      <tr>
        <td class="text-left branches">Totaux</td>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span-1) ?><td><strong><?= nf($perTot[1]['tot']+$cd1) ?></strong></td>
        <?= str_repeat('<td></td>', $col_span-1) ?><td><strong><?= nf($perTot[2]['tot']+$cd2) ?></strong></td>
        <?= str_repeat('<td></td>', $col_span-1) ?><td><strong><?= nf($perTot[3]['tot']+$cd3) ?></strong></td>
        <td><strong><?= nf($aAnnMax+$conduiteVal*$nbPeriodes) ?></strong></td>
        <td><strong><?= nf($aAnnNote+$cdAnn) ?></strong></td>
        <td><strong><?= ($aAnnMax+$conduiteVal*$nbPeriodes)>0?number_format(($aAnnNote+$cdAnn)/($aAnnMax+$conduiteVal*$nbPeriodes)*100,2):'-' ?>%</strong></td>
      </tr>

      <!-- E: Pourcentage -->
      <tr>
        <td class="text-left branches">Pourcentage</td>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <td></td><td></td><td></td>
      </tr>

      <!-- E2: Mention (dynamique depuis Paramètres) -->
      <tr>
        <td class="text-left branches">Mention</td>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <td></td><td></td><td><strong><?= htmlspecialchars(get_mention($aAnnMax>0?($aAnnNote/$aAnnMax)*20:0, 20)) ?></strong></td>
      </tr>

      <!-- F: Place -->
      <tr>
        <td class="text-left branches">Place</td>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <td></td><td></td><td></td>
      </tr>

      <!-- G: Religion -->
      <tr>
        <td class="text-left branches">Religion</td>
        <?php if ($both_active): ?>
        <td><?= $relTj ?></td><td><?= $relEx ?></td><td></td><td><?= $relTot ?></td>
        <td><?= $relTj ?></td><td><?= $relEx ?></td><td></td><td><?= $relTot ?></td>
        <td><?= $relTj ?></td><td><?= $relEx ?></td><td></td><td><?= $relTot ?></td>
        <td><?= $relTj ?></td><td><?= $relEx ?></td><td></td><td><?= $relTot ?></td>
        <?php else: ?>
        <td><?= $relTj ?></td><td><?= $relEx ?></td><td><?= $relTot ?></td>
        <td><?= $relTj ?></td><td><?= $relEx ?></td><td><?= $relTot ?></td>
        <td><?= $relTj ?></td><td><?= $relEx ?></td><td><?= $relTot ?></td>
        <td><?= $relTj ?></td><td><?= $relEx ?></td><td><?= $relTot ?></td>
        <?php endif; ?>
        <td><strong><?= $relTot * $nbPeriodes ?></strong></td>
        <td></td><td></td>
      </tr>

      <!-- H: Signatures -->
      <tr>
        <td class="text-left branches" rowspan="2">Signatures</td>
        <td>PARENTS</td><?= str_repeat('<td></td>', $col_span-1) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <td></td><td></td><td></td>
      </tr>
      <tr>
        <td>TITULAIRE</td><?= str_repeat('<td></td>', $col_span-1) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <?= str_repeat('<td></td>', $col_span) ?>
        <td></td><td></td><td></td>
      </tr>
    </tbody>
  </table>
</div>
<?php endforeach; ?>

</body>
</html>
