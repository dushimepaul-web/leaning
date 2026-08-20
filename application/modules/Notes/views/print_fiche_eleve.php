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
  table.f-table th, table.f-table td { border: 1px solid #000; padding: 3px 4px; text-align: center; }
  table.f-table th { background: #D9D9D9; font-weight: bold; font-size: 9pt; }
  table.f-table td.branche { background: #fff; }
  table.f-table td.gris { background: #D9D9D9; }
  table.f-table td.bareme { background: #f1f3f5; font-weight: bold; }
  table.f-table td.mat { text-align: left; font-weight: bold; padding-left: 6px; }
  .text-left { text-align: left !important; }
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
$groups = $groups ?? [];
$students = $students ?? [];
$notes = $notes ?? [];
$nf = function($v){ return ($v !== null && $v > 0) ? number_format($v, 1) : '-'; };
$sumType = function($st, $g, $types) use ($notes) {
    $s = 0;
    foreach ($g['items'] as $e) {
        if (in_array($e['type'], $types)) {
            $n = isset($notes[$st['id_etudiant']][$e['id_evaluation']]) ? $notes[$st['id_etudiant']][$e['id_evaluation']] : null;
            if ($n !== null) $s += $n;
        }
    }
    return $s;
};
$cellsOf = function($st, $g) use ($sumType) {
    $tj = $sumType($st, $g, ['interrogation','devoir']);
    $comp = $sumType($st, $g, ['competance']);
    $ress = $sumType($st, $g, ['ressource']);
    return [$tj, $comp, $ress, $tj + $comp + $ress];
};
$maxCells = function($g) {
    $tj = 0; $comp = 0; $ress = 0;
    foreach ($g['items'] as $e) {
        $v = floatval($e['ponderee_sur'] ?: 0);
        if (in_array($e['type'], ['interrogation','devoir'])) $tj += $v;
        elseif ($e['type'] === 'competance') $comp += $v;
        elseif ($e['type'] === 'ressource') $ress += $v;
    }
    return [$tj, $comp, $ress, $tj + $comp + $ress];
};
$maxTa = 0;
foreach ($groups as $g) { $m = $maxCells($g); $maxTa += $m[3]; }
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

  <table class="f-table">
    <thead>
      <tr>
        <th class="branche" rowspan="3" style="width: 26px;">N°</th>
        <th class="branche" rowspan="3" style="width: 200px;">ÉLÈVES</th>
        <?php foreach ($groups as $g): ?>
        <th colspan="4"><?= htmlspecialchars($g['libelle']) ?></th>
        <?php endforeach; ?>
        <th colspan="2" rowspan="2">TOTAUX</th>
      </tr>
      <tr>
        <?php foreach ($groups as $g): ?>
        <th rowspan="2">TJ</th><th colspan="2">EXAMEN</th><th rowspan="2">TOT</th>
        <?php endforeach; ?>
      </tr>
      <tr>
        <?php foreach ($groups as $g): ?>
        <th>COMP</th><th>RESS</th>
        <?php endforeach; ?>
        <th>T.A</th><th>%</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="bareme" colspan="2"></td>
        <?php foreach ($groups as $g): $m = $maxCells($g); ?>
        <td class="bareme"><?= $nf($m[0]) ?></td>
        <td class="bareme"><?= $nf($m[1]) ?></td>
        <td class="bareme"><?= $nf($m[2]) ?></td>
        <td class="bareme gris"><?= $nf($m[3]) ?></td>
        <?php endforeach; ?>
        <td class="bareme"><?= $nf($maxTa) ?></td>
        <td class="bareme">100%</td>
      </tr>
      <?php $i = 1; foreach ($students as $st): $ta = 0; foreach ($groups as $g) { $c = $cellsOf($st, $g); $ta += $c[3]; } ?>
      <tr>
        <td><?= $i++ ?></td>
        <td class="mat"><?= htmlspecialchars($st['nom']) ?></td>
        <?php foreach ($groups as $g): $c = $cellsOf($st, $g); ?>
        <td><?= $nf($c[0]) ?></td>
        <td><?= $nf($c[1]) ?></td>
        <td><?= $nf($c[2]) ?></td>
        <td class="gris"><?= $nf($c[3]) ?></td>
        <?php endforeach; ?>
        <td><?= $nf($ta) ?></td>
        <td><?= $maxTa > 0 ? number_format($ta / $maxTa * 100, 2).'%' : '-' ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</body>
</html>