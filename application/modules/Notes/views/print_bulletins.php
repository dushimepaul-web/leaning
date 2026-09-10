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

$ress_active = isset($ressources_active) ? intval($ressources_active) !== 0 : false;
$comp_active = isset($competences_active) ? intval($competences_active) !== 0 : false;
$ex_active = isset($examen_active) ? intval($examen_active) !== 0 : false;
$mode_b = $ress_active || $comp_active;
$mode_a = $ex_active && !$mode_b;
$pct_comp = isset($competences_pourcentage) ? floatval($competences_pourcentage) : 40;
$pct_ress = isset($ressources_pourcentage) ? floatval($ressources_pourcentage) : 60;
$facteur_points = isset($facteur_points_heure) ? floatval($facteur_points_heure) : floatval(get_setting('facteur_points_heure', 15));
$col_span = $mode_b ? 4 : 3;
$sub_head = $mode_b ? '<th>TJ</th><th>COMP</th><th>RESS</th><th>TOT</th>' : '<th>TJ</th><th>EX</th><th>TOT</th>';
$cat_active_ress = $ress_active && !$comp_active;
$cat_active_comp = $comp_active && !$ress_active;

$conduiteVal = isset($conduite_val) ? $conduite_val : 60;
$perTots = isset($per_tots) ? $per_tots : [];
$maximaMap = isset($maxima_map) ? $maxima_map : [];
$rangsPeriode = isset($rangs_periode) ? $rangs_periode : [];
$matieresInactifs = isset($subjects_inactifs) ? $subjects_inactifs : [];

foreach ($eleves as $eleve_id => $eleve):
    $stud_notes = $notes_map[$eleve_id] ?? [];
    $nbPeriodes = count($periodes) ?: 3;
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
      <?php if ($mode_b): ?>
      <tr>
        <th class="branches-header" rowspan="3"></th>
        <th colspan="4">MAXIMA</th>
        <th colspan="4">1er TRIMESTRE</th>
        <th colspan="4">2e TRIMESTRE</th>
        <th colspan="4">3e TRIMESTRE</th>
        <th colspan="3" rowspan="2">TOTAUX ANNUELS</th>
      </tr>
      <tr>
        <th rowspan="2">TJ</th><th colspan="2">EXAMEN</th><th rowspan="2">TOT</th>
        <th rowspan="2">TJ</th><th colspan="2">EXAMEN</th><th rowspan="2">TOT</th>
        <th rowspan="2">TJ</th><th colspan="2">EXAMEN</th><th rowspan="2">TOT</th>
        <th rowspan="2">TJ</th><th colspan="2">EXAMEN</th><th rowspan="2">TOT</th>
      </tr>
      <tr>
        <th>COMP</th><th>RESS</th>
        <th>COMP</th><th>RESS</th>
        <th>COMP</th><th>RESS</th>
        <th>COMP</th><th>RESS</th>
        <th>MAX</th><th>TOT</th><th>%</th>
      </tr>
      <?php else: ?>
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
      <?php endif; ?>
    </thead>
    <tbody>
<?php
$generaux = [];
$techniques = [];
foreach ($subjects as $subj) {
    if (isset($subj['est_general']) && intval($subj['est_general']) === 1) {
        $generaux[] = $subj;
    } else {
        $techniques[] = $subj;
    }
}

$aAnnMax = 0; $aAnnNote = 0;
$gen_per_max = [1 => ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0,'tot'=>0], 2 => ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0,'tot'=>0], 3 => ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0,'tot'=>0]];
$tech_per_max = [1 => ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0,'tot'=>0], 2 => ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0,'tot'=>0], 3 => ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0,'tot'=>0]];

$gen_per_tot = [1 => ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0,'tot'=>0], 2 => ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0,'tot'=>0], 3 => ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0,'tot'=>0]];
$tech_per_tot = [1 => ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0,'tot'=>0], 2 => ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0,'tot'=>0], 3 => ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0,'tot'=>0]];

$render_matiere_rows = function($matiere_list, $stud_notes, $mode_b, $pct_comp, $pct_ress, $facteur_points, &$group_per_tot, &$group_per_max, &$group_ann_max, &$group_ann_note) use ($col_span, $mode_a) {
    foreach ($matiere_list as $subj):
        $s_data = $stud_notes[$subj['id']] ?? null;
        $max_tj = isset($subj['note_max_matiere']) ? (float)$subj['note_max_matiere'] : 15;
        if ($mode_b) {
            $max_comp = round($max_tj * $pct_comp / 100, 1);
            $max_ress = round($max_tj * $pct_ress / 100, 1);
            $max_ex = 0;
        } elseif ($mode_a) {
            $max_comp = 0;
            $max_ress = 0;
            $max_ex = $max_tj;
        } else {
            $max_comp = 0;
            $max_ress = 0;
            $max_ex = 0;
        }
        $max_tot = $max_tj + $max_comp + $max_ress + $max_ex;
        $max_sub_annuel = $max_tot * 3;

        // Somme des maxima par période (fixe par période, pas de cumul d'addition des trimestres)
        foreach ([1, 2, 3] as $pid) {
            $group_per_max[$pid]['tj'] += $max_tj;
            $group_per_max[$pid]['comp'] += $max_comp;
            $group_per_max[$pid]['ress'] += $max_ress;
            $group_per_max[$pid]['ex'] += $max_ex;
            $group_per_max[$pid]['tot'] += $max_tot;
        }

        $t1_tj = $s_data ? (float)$s_data['note_t1_tj'] : 0;
        $t1_comp = $s_data ? (float)$s_data['note_t1_comp'] : 0;
        $t1_ress = $s_data ? (float)$s_data['note_t1_ress'] : 0;
        $t1_ex = $s_data ? (float)$s_data['note_t1_ex'] : 0;
        if ($mode_b) { $t1_ex = 0; }
        elseif ($mode_a) { $t1_comp = 0; $t1_ress = 0; }
        else { $t1_comp = 0; $t1_ress = 0; $t1_ex = 0; }
        $t1_tot = $t1_tj + $t1_comp + $t1_ress + $t1_ex;

        $t2_tj = $s_data ? (float)$s_data['note_t2_tj'] : 0;
        $t2_comp = $s_data ? (float)$s_data['note_t2_comp'] : 0;
        $t2_ress = $s_data ? (float)$s_data['note_t2_ress'] : 0;
        $t2_ex = $s_data ? (float)$s_data['note_t2_ex'] : 0;
        if ($mode_b) { $t2_ex = 0; }
        elseif ($mode_a) { $t2_comp = 0; $t2_ress = 0; }
        else { $t2_comp = 0; $t2_ress = 0; $t2_ex = 0; }
        $t2_tot = $t2_tj + $t2_comp + $t2_ress + $t2_ex;

        $t3_tj = $s_data ? (float)$s_data['note_t3_tj'] : 0;
        $t3_comp = $s_data ? (float)$s_data['note_t3_comp'] : 0;
        $t3_ress = $s_data ? (float)$s_data['note_t3_ress'] : 0;
        $t3_ex = $s_data ? (float)$s_data['note_t3_ex'] : 0;
        if ($mode_b) { $t3_ex = 0; }
        elseif ($mode_a) { $t3_comp = 0; $t3_ress = 0; }
        else { $t3_comp = 0; $t3_ress = 0; $t3_ex = 0; }
        $t3_tot = $t3_tj + $t3_comp + $t3_ress + $t3_ex;

        $tot_sub_annuel = $t1_tot + $t2_tot + $t3_tot;
        $pct_sub = $max_sub_annuel > 0 ? round(($tot_sub_annuel / $max_sub_annuel) * 100, 2) : 0;

        $group_per_tot[1]['tj'] += $t1_tj; $group_per_tot[1]['comp'] += $t1_comp; $group_per_tot[1]['ress'] += $t1_ress; $group_per_tot[1]['ex'] += $t1_ex; $group_per_tot[1]['tot'] += $t1_tot;
        $group_per_tot[2]['tj'] += $t2_tj; $group_per_tot[2]['comp'] += $t2_comp; $group_per_tot[2]['ress'] += $t2_ress; $group_per_tot[2]['ex'] += $t2_ex; $group_per_tot[2]['tot'] += $t2_tot;
        $group_per_tot[3]['tj'] += $t3_tj; $group_per_tot[3]['comp'] += $t3_comp; $group_per_tot[3]['ress'] += $t3_ress; $group_per_tot[3]['ex'] += $t3_ex; $group_per_tot[3]['tot'] += $t3_tot;

        $group_ann_max += $max_sub_annuel;
        $group_ann_note += $tot_sub_annuel;
?>
      <tr>
        <td class="text-left matiere"><?= htmlspecialchars($subj['name']) ?></td>
        <?php if ($mode_b): ?>
        <td><?= nf($max_tj) ?></td>
        <td><?= nf($max_comp) ?></td>
        <td><?= nf($max_ress) ?></td>
        <td><strong><?= nf($max_tot) ?></strong></td>
        <?php else: ?>
        <td><?= nf($max_tj) ?></td>
        <td><?= nf($max_ex) ?></td>
        <td><strong><?= nf($max_tot) ?></strong></td>
        <?php endif; ?>

        <?php if ($mode_b): ?>
        <td><?= nf($t1_tj) ?></td>
        <td><?= nf($t1_comp) ?></td>
        <td><?= nf($t1_ress) ?></td>
        <td><strong><?= nf($t1_tot) ?></strong></td>
        <?php else: ?>
        <td><?= nf($t1_tj) ?></td>
        <td><?= nf($t1_ex) ?></td>
        <td><strong><?= nf($t1_tot) ?></strong></td>
        <?php endif; ?>

        <?php if ($mode_b): ?>
        <td><?= nf($t2_tj) ?></td>
        <td><?= nf($t2_comp) ?></td>
        <td><?= nf($t2_ress) ?></td>
        <td><strong><?= nf($t2_tot) ?></strong></td>
        <?php else: ?>
        <td><?= nf($t2_tj) ?></td>
        <td><?= nf($t2_ex) ?></td>
        <td><strong><?= nf($t2_tot) ?></strong></td>
        <?php endif; ?>

        <?php if ($mode_b): ?>
        <td><?= nf($t3_tj) ?></td>
        <td><?= nf($t3_comp) ?></td>
        <td><?= nf($t3_ress) ?></td>
        <td><strong><?= nf($t3_tot) ?></strong></td>
        <?php else: ?>
        <td><?= nf($t3_tj) ?></td>
        <td><?= nf($t3_ex) ?></td>
        <td><strong><?= nf($t3_tot) ?></strong></td>
        <?php endif; ?>

        <td><strong><?= nf($max_sub_annuel) ?></strong></td>
        <td><strong><?= nf($tot_sub_annuel) ?></strong></td>
        <td><strong><?= $pct_sub ?>%</strong></td>
      </tr>
<?php
    endforeach;
};
?>

<!-- 1. COURS GENERAUX -->
<tr>
  <td class="text-left branches" style="background:#e8e8e8; font-weight:bold;" colspan="<?= $col_span + 16 ?>">COURS GENERAUX ET LANGUES</td>
</tr>
<?php
$gen_ann_max = 0; $gen_ann_note = 0;
if (!empty($generaux)) {
    $render_matiere_rows($generaux, $stud_notes, $mode_b, $pct_comp, $pct_ress, $facteur_points, $gen_per_tot, $gen_per_max, $gen_ann_max, $gen_ann_note);
}
?>
<tr>
  <td class="text-left branches" style="font-weight:bold;">SOUS-TOT1</td>
  <?php
  $gen_ann_max_tj = $gen_per_max[1]['tj'];
  $gen_ann_max_comp = $gen_per_max[1]['comp'];
  $gen_ann_max_ress = $gen_per_max[1]['ress'];
  $gen_ann_max_ex = $gen_per_max[1]['ex'];
  ?>
  <?php if ($mode_b): ?>
  <td><?= nf($gen_ann_max_tj) ?></td><td><?= nf($gen_ann_max_comp) ?></td><td><?= nf($gen_ann_max_ress) ?></td><td><strong><?= nf($gen_ann_max) ?></strong></td>
  <?php else: ?>
  <td><?= nf($gen_ann_max_tj) ?></td><td><?= nf($gen_ann_max_ex) ?></td><td><strong><?= nf($gen_ann_max) ?></strong></td>
  <?php endif; ?>
  <?php foreach ([1, 2, 3] as $pn): ?>
  <?php if ($mode_b): ?>
  <td><?= nf($gen_per_tot[$pn]['tj']) ?></td><td><?= nf($gen_per_tot[$pn]['comp']) ?></td><td><?= nf($gen_per_tot[$pn]['ress']) ?></td><td><strong><?= nf($gen_per_tot[$pn]['tot']) ?></strong></td>
  <?php else: ?>
  <td><?= nf($gen_per_tot[$pn]['tj']) ?></td><td><?= nf($gen_per_tot[$pn]['ex']) ?></td><td><strong><?= nf($gen_per_tot[$pn]['tot']) ?></strong></td>
  <?php endif; ?>
  <?php endforeach; ?>
  <td><strong><?= nf($gen_ann_max) ?></strong></td>
  <td><strong><?= nf($gen_ann_note) ?></strong></td>
  <td><strong><?= $gen_ann_max > 0 ? number_format($gen_ann_note / $gen_ann_max * 100, 2) : '-' ?>%</strong></td>
</tr>


<!-- 2. COURS TECHNIQUE -->
<tr>
  <td class="text-left branches" style="background:#e8e8e8; font-weight:bold;" colspan="<?= $col_span + 16 ?>">COURS TECHNIQUE</td>
</tr>
<?php
$tech_ann_max = 0; $tech_ann_note = 0;
if (!empty($techniques)) {
    $render_matiere_rows($techniques, $stud_notes, $mode_b, $pct_comp, $pct_ress, $facteur_points, $tech_per_tot, $tech_per_max, $tech_ann_max, $tech_ann_note);
}
?>
<tr>
  <td class="text-left branches" style="font-weight:bold;">SOUS-TOT2</td>
  <?php
  $tech_ann_max_tj = $tech_per_max[1]['tj'];
  $tech_ann_max_comp = $tech_per_max[1]['comp'];
  $tech_ann_max_ress = $tech_per_max[1]['ress'];
  $tech_ann_max_ex = $tech_per_max[1]['ex'];
  ?>
  <?php if ($mode_b): ?>
  <td><?= nf($tech_ann_max_tj) ?></td><td><?= nf($tech_ann_max_comp) ?></td><td><?= nf($tech_ann_max_ress) ?></td><td><strong><?= nf($tech_ann_max) ?></strong></td>
  <?php else: ?>
  <td><?= nf($tech_ann_max_tj) ?></td><td><?= nf($tech_ann_max_ex) ?></td><td><strong><?= nf($tech_ann_max) ?></strong></td>
  <?php endif; ?>
  <?php foreach ([1, 2, 3] as $pn): ?>
  <?php if ($mode_b): ?>
  <td><?= nf($tech_per_tot[$pn]['tj']) ?></td><td><?= nf($tech_per_tot[$pn]['comp']) ?></td><td><?= nf($tech_per_tot[$pn]['ress']) ?></td><td><strong><?= nf($tech_per_tot[$pn]['tot']) ?></strong></td>
  <?php else: ?>
  <td><?= nf($tech_per_tot[$pn]['tj']) ?></td><td><?= nf($tech_per_tot[$pn]['ex']) ?></td><td><strong><?= nf($tech_per_tot[$pn]['tot']) ?></strong></td>
  <?php endif; ?>
  <?php endforeach; ?>
  <td><strong><?= nf($tech_ann_max) ?></strong></td>
  <td><strong><?= nf($tech_ann_note) ?></strong></td>
  <td><strong><?= $tech_ann_max > 0 ? number_format($tech_ann_note / $tech_ann_max * 100, 2) : '-' ?>%</strong></td>
</tr>

<?php
$aAnnMax = $gen_ann_max + $tech_ann_max;
$aAnnNote = $gen_ann_note + $tech_ann_note;
$t1_tot_cours = $gen_per_tot[1]['tot'] + $tech_per_tot[1]['tot'];
$t2_tot_cours = $gen_per_tot[2]['tot'] + $tech_per_tot[2]['tot'];
$t3_tot_cours = $gen_per_tot[3]['tot'] + $tech_per_tot[3]['tot'];

$t1_max_cours = $gen_per_max[1]['tot'] + $tech_per_max[1]['tot'];
$t2_max_cours = $gen_per_max[2]['tot'] + $tech_per_max[2]['tot'];
$t3_max_cours = $gen_per_max[3]['tot'] + $tech_per_max[3]['tot'];
?>

      <!-- C: Conduite -->
      <tr>
        <td class="text-left branches">Conduite</td>
        <td class="gris"><?= $conduiteVal ?></td><?= str_repeat('<td></td>', $col_span-2) ?><td><?= $conduiteVal ?></td>
        <?= str_repeat('<td></td>', $col_span-1) ?><td><?= $cd1 ?></td>
        <?= str_repeat('<td></td>', $col_span-1) ?><td><?= $cd2 ?></td>
        <?= str_repeat('<td></td>', $col_span-1) ?><td><?= $cd3 ?></td>
        <td><strong><?= $conduiteVal * $nbPeriodes ?></strong></td>
        <td><strong><?= $cdAnn ?></strong></td>
        <td><strong><?= $conduiteVal > 0 ? number_format($cdAnn / ($conduiteVal * $nbPeriodes) * 100, 2) : '-' ?>%</strong></td>
      </tr>

      <!-- D: Total -->
      <?php
      $t_ann_tj = 0; $t_ann_comp = 0; $t_ann_ress = 0; $t_ann_ex = 0;
      foreach ($subjects as $subj) {
          $mx = $maximaMap[$subj['id']][1] ?? ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0];
          $t_ann_tj += $mx['tj'];
          $t_ann_comp += $mx['comp'];
          $t_ann_ress += $mx['ress'];
          $t_ann_ex += $mx['ex'];
      }
      $t_ann_tot = $aAnnMax + $conduiteVal * $nbPeriodes;
      ?>
      <tr>
        <td class="text-left branches" style="font-weight:bold;">Total</td>
        <?php if ($mode_b): ?>
        <td><?= nf($t_ann_tj) ?></td><td><?= nf($t_ann_comp) ?></td><td><?= nf($t_ann_ress) ?></td><td><strong><?= nf($aAnnMax) ?></strong></td>
        <?php else: ?>
        <td><?= nf($t_ann_tj) ?></td><td><?= nf($t_ann_ex) ?></td><td><strong><?= nf($aAnnMax) ?></strong></td>
        <?php endif; ?>
        <?php foreach ([1,2,3] as $pnum):
            $ptj = ($gen_per_tot[$pnum]['tj'] ?? 0) + ($tech_per_tot[$pnum]['tj'] ?? 0);
            $pcomp = ($gen_per_tot[$pnum]['comp'] ?? 0) + ($tech_per_tot[$pnum]['comp'] ?? 0);
            $press = ($gen_per_tot[$pnum]['ress'] ?? 0) + ($tech_per_tot[$pnum]['ress'] ?? 0);
            $pex = ($gen_per_tot[$pnum]['ex'] ?? 0) + ($tech_per_tot[$pnum]['ex'] ?? 0);
            $ptot = $ptj + $pcomp + $press + $pex + ($pnum == 1 ? $cd1 : ($pnum == 2 ? $cd2 : $cd3));
        ?>
        <?php if ($mode_b): ?>
        <td><?= nf($ptj) ?></td><td><?= nf($pcomp) ?></td><td><?= nf($press) ?></td><td><strong><?= nf($ptot) ?></strong></td>
        <?php else: ?>
        <td><?= nf($ptj) ?></td><td><?= nf($pex) ?></td><td><strong><?= nf($ptot) ?></strong></td>
        <?php endif; ?>
        <?php endforeach; ?>
        <td><strong><?= nf($t_ann_tot) ?></strong></td>
        <td><strong><?= nf($aAnnNote+$cdAnn) ?></strong></td>
        <td><strong><?= $t_ann_tot>0?number_format(($aAnnNote+$cdAnn)/$t_ann_tot*100,2):'-' ?>%</strong></td>
      </tr>

      <!-- E: Pourcentage -->
      <tr>
        <td class="text-left branches">Pourcentage</td>
        <?php if ($mode_b): ?>
        <td></td><td></td><td></td><td></td>
        <?php else: ?>
        <td></td><td></td><td></td>
        <?php endif; ?>
        <?php foreach ([1, 2, 3] as $pnum): ?>
        <?php
            $pTotNote = $perTots[$eleve_id][$pnum]['tot'] ?? 0;
            $pTotMax = 0;
            foreach ($subjects as $subj) {
                $mx = $maximaMap[$subj['id']][$pnum] ?? ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0,'tot'=>0];
                $pTotMax += $mx['tot'];
            }
            $pTotMax += $conduiteVal;
            $pPct = $pTotMax > 0 ? number_format($pTotNote / $pTotMax * 100, 2) . '%' : '-';
        ?>
        <?php if ($mode_b): ?>
        <td></td><td></td><td></td><td><strong><?= $pPct ?></strong></td>
        <?php else: ?>
        <td></td><td></td><td><strong><?= $pPct ?></strong></td>
        <?php endif; ?>
        <?php endforeach; ?>
        <td></td><td></td><td><strong><?= ($aAnnMax+$conduiteVal*$nbPeriodes)>0?number_format(($aAnnNote+$cdAnn)/($aAnnMax+$conduiteVal*$nbPeriodes)*100,2):'-' ?>%</strong></td>
      </tr>

      <!-- F: Place -->
      <tr>
        <td class="text-left branches">Place</td>
        <?php if ($mode_b): ?>
        <td></td><td></td><td></td><td></td>
        <?php else: ?>
        <td></td><td></td><td></td>
        <?php endif; ?>
        <?php foreach ([1, 2, 3] as $pnum): ?>
        <?php $rangP = isset($rangsPeriode[$pnum][$eleve_id]) ? $rangsPeriode[$pnum][$eleve_id] : 0; ?>
        <?php if ($mode_b): ?>
        <td></td><td></td><td></td><td><strong><?= $rangP > 0 ? $rangP . 'e' : '—' ?></strong></td>
        <?php else: ?>
        <td></td><td></td><td><strong><?= $rangP > 0 ? $rangP . 'e' : '—' ?></strong></td>
        <?php endif; ?>
        <?php endforeach; ?>
        <td></td><td></td><td><strong><?= isset($eleve['rang']) && $eleve['rang'] > 0 ? $eleve['rang'] . 'e' : '—' ?></strong></td>
      </tr>

      <!-- Cours Inactifs -->
      <?php foreach ($matieresInactifs as $mat_inactif):
          $mid_inactif = $mat_inactif['id'];
          $s_data_inactif = $notes_map[$eleve_id][$mid_inactif] ?? null;
          $max_tj_inactif = floatval($mat_inactif['note_max_matiere'] ?? 0);
          $max_comp_inactif = 0; $max_ress_inactif = 0; $max_ex_inactif = 0;
          $max_tot_inactif = $max_tj_inactif;
          if ($mode_b) {
              $max_comp_inactif = round($max_tj_inactif * $pct_comp / 100, 1);
              $max_ress_inactif = round($max_tj_inactif * $pct_ress / 100, 1);
              $max_tot_inactif = $max_tj_inactif + $max_comp_inactif + $max_ress_inactif;
          } elseif ($mode_a) {
              $max_ex_inactif = $max_tj_inactif;
              $max_tot_inactif = $max_tj_inactif * 2;
          }
          $ann_note_inactif = 0;
      ?>
      <tr>
        <td class="text-left matiere"><?= htmlspecialchars($mat_inactif['name']) ?></td>
        <?php if ($mode_b): ?>
        <td><?= nf($max_tj_inactif) ?></td><td><?= nf($max_comp_inactif) ?></td><td><?= nf($max_ress_inactif) ?></td><td><strong><?= nf($max_tot_inactif) ?></strong></td>
        <?php else: ?>
        <td><?= nf($max_tj_inactif) ?></td><td><?= nf($max_ex_inactif) ?></td><td><strong><?= nf($max_tot_inactif) ?></strong></td>
        <?php endif; ?>
        <?php foreach ([1, 2, 3] as $pnum):
            $p = $s_data_inactif["note_t{$pnum}_tj"] ?? 0;
            $pc = $s_data_inactif["note_t{$pnum}_comp"] ?? 0;
            $pr = $s_data_inactif["note_t{$pnum}_ress"] ?? 0;
            $pe = $s_data_inactif["note_t{$pnum}_ex"] ?? 0;
            if ($mode_b) $pe = 0;
            elseif ($mode_a) { $pc = 0; $pr = 0; }
            else { $pc = 0; $pr = 0; $pe = 0; }
            $t_tot = $p + $pc + $pr + $pe;
            $ann_note_inactif += $t_tot;
        ?>
        <?php if ($mode_b): ?>
        <td><?= nf($p) ?></td><td><?= nf($pc) ?></td><td><?= nf($pr) ?></td><td><strong><?= nf($t_tot) ?></strong></td>
        <?php else: ?>
        <td><?= nf($p) ?></td><td><?= nf($pe) ?></td><td><strong><?= nf($t_tot) ?></strong></td>
        <?php endif; ?>
        <?php endforeach; ?>
        <td><strong>-</strong></td>
        <td><strong><?= nf($ann_note_inactif) ?></strong></td>
        <td><strong>-</strong></td>
      </tr>
      <?php endforeach; ?>

      <!-- H: Signatures -->
      <tr style="height:40px;">
        <td class="text-left branches" rowspan="2">Signatures</td>
        <td colspan="<?= $col_span ?>">PARENTS</td>
        <td colspan="<?= $col_span ?>"></td>
        <td colspan="<?= $col_span ?>"></td>
        <td colspan="<?= $col_span ?>"></td>
        <td colspan="3" rowspan="2"></td>
      </tr>
      <tr style="height:40px;">
        <td colspan="<?= $col_span ?>">TITULAIRE</td>
        <td colspan="<?= $col_span ?>"></td>
        <td colspan="<?= $col_span ?>"></td>
        <td colspan="<?= $col_span ?>"></td>
      </tr>
    </tbody>
  </table>
</div>
<?php endforeach; ?>

</body>
</html>
