<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($title) ?></title>
<style>
  body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #000; margin: 0; padding: 20px; background: #e5e5e5; }
  .fiche-page { background: #fff; width: 297mm; margin: 0 auto 30px auto; padding: 10mm; box-sizing: border-box; }
  .h-row { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 10pt; font-weight: bold; }
  .titre-cours { text-align: center; font-size: 12pt; font-weight: bold; text-transform: uppercase; margin: 12px 0; }
  table.f-table { border-collapse: collapse; width: 100%; font-size: 8pt; margin-top: 10px; table-layout: fixed; }
  table.f-table th, table.f-table td { border: 1px solid #000; padding: 4px 5px; text-align: center; }
  table.f-table th { background: #D9D9D9; font-weight: bold; font-size: 9pt; }
  .text-left { text-align: left !important; }
  .bg-g { background: #f1f3f5; }
  @media print {
    body { background: #fff; padding: 0; }
    .fiche-page { box-shadow: none; margin: 0; width: 100%; }
    .no-print { display: none; }
  }
</style>
</head>
<body>

<div class="no-print" style="text-align: right; margin-bottom: 20px; max-width: 297mm; margin-left: auto; margin-right: auto;">
  <button onclick="window.print()" style="background: #217346; color: #fff; border: none; padding: 10px 20px; font-size: 11pt; font-weight: bold; cursor: pointer; border-radius: 4px;">Imprimer la fiche</button>
</div>

<?php
$c = $complet;
$periodes = $c['periodes'] ?? [];
$matieres = $c['matieres'] ?? [];
$eleves = $c['eleves'] ?? [];
$maxima = $c['maxima'] ?? [];
$pids = array_column($periodes, 'id_periode');
$ress_active = isset($c['ressources_active']) ? intval($c['ressources_active']) !== 0 : true;
$comp_active = isset($c['competences_active']) ? intval($c['competences_active']) !== 0 : true;
$both_active = $ress_active && $comp_active;
$col_span = $both_active ? 4 : 3;
$sub_head = $both_active ? '<th>TJ</th><th>RESS</th><th>COMP</th><th>TOT</th>' : '<th>TJ</th><th>EX</th><th>TOT</th>';
function fcells($t) { global $both_active;
  $fmt = function($v){ return $v > 0 ? number_format($v, 1) : '-'; };
  if ($both_active) return [$fmt($t['tj']), $fmt($t['ress']), $fmt($t['comp']), '<strong>'.$fmt($t['tot']).'</strong>'];
  return [$fmt($t['tj']), $fmt(($t['comp'] ?? 0) + ($t['ress'] ?? 0)), '<strong>'.$fmt($t['tot']).'</strong>'];
}
?>

<div class="fiche-page">
  <div class="h-row">
    <span>SECTION: <?= htmlspecialchars($section_nom) ?></span>
    <span>ANNEE SCOLAIRE : <?= htmlspecialchars($annee_libelle) ?></span>
  </div>
  <div class="h-row">
    <span>Classe : <?= htmlspecialchars($classe_nom) ?></span>
    <span>Nombre d'Eleves : <?= $nb_eleves ?></span>
  </div>
  <div class="titre-cours">
    FICHE DE POINTS — <?= htmlspecialchars($matiere_nom) ?>
  </div>
  <div style="text-align: right; font-weight: bold; font-size: 10pt; margin-bottom: 8px;">
    <?= htmlspecialchars($periode_nom) ?>
  </div>

  <table class="f-table">
    <thead>
      <tr>
        <th rowspan="2" class="text-left" style="width: 180px;">BRANCHE</th>
        <th colspan="<?= $col_span ?>">MAXIMA</th>
        <?php foreach ($periodes as $pe): ?>
        <th colspan="<?= $col_span ?>"><?= htmlspecialchars($pe['libelle']) ?></th>
        <?php endforeach; ?>
        <th colspan="3">TOTAUX ANNUELS</th>
      </tr>
      <tr>
        <?= $sub_head ?>
        <?php foreach ($periodes as $pe): ?>
        <?= $sub_head ?>
        <?php endforeach; ?>
        <th>MAX</th><th>TOT</th><th>%</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Totaux maxima classe
      $mTot = ['tj' => 0, 'comp' => 0, 'ress' => 0, 'tot' => 0];
      foreach ($matieres as $m) {
          $mid = $m['id_matiere'];
          foreach ($pids as $pid) {
              $mm = $maxima[$mid][$pid] ?? [];
              $mTot['tj'] += $mm['tj'] ?? 0;
              $mTot['comp'] += $mm['comp'] ?? 0;
              $mTot['ress'] += $mm['ress'] ?? 0;
          }
      }
      $mTot['tot'] = $mTot['tj'] + $mTot['comp'] + $mTot['ress'];
      ?>
      <!-- Ligne MAXIMA -->
      <tr class="bg-g" style="font-weight:700">
        <td class="text-left">MAXIMA</td>
        <?= implode('', array_map(function($c){ return '<td>'.$c.'</td>'; }, fcells($mTot))) ?>
        <?php foreach ($periodes as $pe): ?>
        <?= str_repeat('<td>-</td>', $col_span) ?>
        <?php endforeach; ?>
        <td><strong><?= number_format($mTot['tot'], 1) ?></strong></td>
        <td><strong><?= number_format($mTot['tot'], 1) ?></strong></td>
        <td>100%</td>
      </tr>

      <?php foreach ($matieres as $mat):
        $mid = $mat['id_matiere'];
        // Maxima cumulés pour cette matière
        $matMaxTj = 0; $matMaxComp = 0; $matMaxRess = 0;
        foreach ($pids as $pid) {
            $mm = $maxima[$mid][$pid] ?? [];
            $matMaxTj += $mm['tj'] ?? 0;
            $matMaxComp += $mm['comp'] ?? 0;
            $matMaxRess += $mm['ress'] ?? 0;
        }
        $matMaxTot = $matMaxTj + $matMaxComp + $matMaxRess;
      ?>
      <tr>
        <td class="text-left" style="font-weight:700"><?= htmlspecialchars($mat['libelle']) ?></td>
        <?= implode('', array_map(function($c){ return '<td>'.$c.'</td>'; }, fcells(['tj' => $matMaxTj, 'comp' => $matMaxComp, 'ress' => $matMaxRess, 'tot' => $matMaxTot]))) ?>

        <?php 
        $annTj = 0; $annComp = 0; $annRess = 0;
        foreach ($periodes as $pe):
          $pid = $pe['id_periode'];
          $tTj = 0; $tComp = 0; $tRess = 0;
          foreach ($eleves as $el) {
              $mEl = null;
              foreach ($el['matieres'] as $m) { if ($m['id_matiere'] == $mid) { $mEl = $m; break; } }
              $per = $mEl && isset($mEl['periodes'][$pid]) ? $mEl['periodes'][$pid] : ['tj' => 0, 'comp' => 0, 'ress' => 0];
              $tTj += $per['tj'] ?? 0;
              $tComp += $per['comp'] ?? 0;
              $tRess += $per['ress'] ?? 0;
          }
          $tTot = $tTj + $tComp + $tRess;
          $annTj += $tTj; $annComp += $tComp; $annRess += $tRess;
        ?>
        <?= implode('', array_map(function($c){ return '<td>'.$c.'</td>'; }, fcells(['tj' => $tTj, 'comp' => $tComp, 'ress' => $tRess, 'tot' => $tTot]))) ?>
        <?php endforeach; ?>
        <?php $annTot = $annTj + $annComp + $annRess; ?>
        <td><strong><?= number_format($matMaxTot, 1) ?></strong></td>
        <td><strong><?= number_format($annTot, 1) ?></strong></td>
        <td><strong><?= $matMaxTot > 0 ? number_format(($annTot / $matMaxTot) * 100, 2) . '%' : '-' ?></strong></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <?php
      $gTot = 0;
      foreach ($eleves as $el) {
          foreach ($matieres as $mat) {
              $mid = $mat['id_matiere'];
              $mEl = null;
              foreach ($el['matieres'] as $m) { if ($m['id_matiere'] == $mid) { $mEl = $m; break; } }
              $gTot += $mEl && isset($mEl['annuel']) ? ($mEl['annuel']['note'] ?? 0) : 0;
          }
      }
      ?>
      <tr class="bg-g" style="font-weight:bold">
        <td class="text-left">TOTAUX ÉLÈVES</td>
        <?= implode('', array_map(function($c){ return '<td>'.$c.'</td>'; }, fcells($mTot))) ?>
        <?php foreach ($periodes as $pe):
          $pid = $pe['id_periode'];
          $sTj = 0; $sComp = 0; $sRess = 0;
          foreach ($eleves as $el) {
              foreach ($matieres as $mat) {
                  $mid = $mat['id_matiere'];
                  $mEl = null;
                  foreach ($el['matieres'] as $m) { if ($m['id_matiere'] == $mid) { $mEl = $m; break; } }
                  $per = $mEl && isset($mEl['periodes'][$pid]) ? $mEl['periodes'][$pid] : ['tj' => 0, 'comp' => 0, 'ress' => 0];
                  $sTj += $per['tj'] ?? 0;
                  $sComp += $per['comp'] ?? 0;
                  $sRess += $per['ress'] ?? 0;
              }
          }
          $sTot = $sTj + $sComp + $sRess;
        ?>
        <?= implode('', array_map(function($c){ return '<td>'.$c.'</td>'; }, fcells(['tj' => $sTj, 'comp' => $sComp, 'ress' => $sRess, 'tot' => $sTot]))) ?>
        <?php endforeach; ?>
        <td><strong><?= number_format($mTot['tot'], 1) ?></strong></td>
        <td><strong><?= number_format($gTot, 1) ?></strong></td>
        <td><strong><?= $mTot['tot'] > 0 ? number_format(($gTot / $mTot['tot']) * 100, 2) . '%' : '-' ?></strong></td>
      </tr>
    </tfoot>
  </table>
</div>

</body>
</html>
