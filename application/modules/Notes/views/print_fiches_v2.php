<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($title) ?></title>
<style>
  body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #000; margin: 0; padding: 20px; background: #e5e5e5; }
  .fiche-page { background: #fff; width: 297mm; margin: 0 auto 30px auto; padding: 10mm; box-shadow: 0 4px 12px rgba(0,0,0,0.15); box-sizing: border-box; }
  h3 { text-align: center; text-transform: uppercase; margin-bottom: 2px; font-size: 13pt; }
  .entete { text-align: center; font-size: 9pt; margin-bottom: 10px; }
  table.f-table { border-collapse: collapse; width: 100%; font-size: 7.5pt; }
  table.f-table th, table.f-table td { border: 1px solid #000; padding: 2px 3px; text-align: center; }
  table.f-table th { background: #f0f0f0; font-weight: bold; }
  .text-left { text-align: left !important; }
  .bg-g { background: #f1f3f5; }
  .bg-d { background: #e9ecef; }
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
$mxp = $c['maxima_periode'] ?? [];
$nbPeriodes = count($periodes);
?>

<div class="fiche-page">
  <h3>Fiche de points</h3>
  <div class="entete">
    <strong>Classe :</strong> <?= htmlspecialchars($classe_nom) ?> &nbsp;|&nbsp;
    <strong>Année :</strong> <?= htmlspecialchars($annee_libelle) ?>
    | <strong>Périodes :</strong> <?= htmlspecialchars(implode(' - ', array_column($periodes, 'libelle'))) ?>
  </div>

  <table class="f-table">
    <thead>
      <tr>
        <th rowspan="2">#</th>
        <th rowspan="2" class="text-left" style="min-width:140px">NOM ET PRENOMS</th>
        <th colspan="4" style="background:#e9ecef">MAXIMA</th>
        <?php foreach ($periodes as $pe): ?>
        <th colspan="4" style="background:#e8e8e8"><?= htmlspecialchars($pe['libelle']) ?></th>
        <?php endforeach; ?>
        <th colspan="3" style="background:#d9d9d9">TOTAUX ANNUELS</th>
      </tr>
      <tr>
        <th>TJ</th><th>EX</th><th>TP</th><th>TOT</th>
        <?php foreach ($periodes as $pe): ?>
        <th>TJ</th><th>EX</th><th>TP</th><th>TOT</th>
        <?php endforeach; ?>
        <th>MAX</th><th>TOT</th><th>%</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $ftTj = 0; $ftCmp = 0; $ftRess = 0;
      foreach ($eleves as $idx => $el):
        $annNote = 0; $annMax = 0;
        $pid0 = $periodes[0]['id_periode'] ?? null;
        $pm = $mxp[$pid0] ?? ['tj' => 0, 'comp' => 0, 'ress' => 0, 'tot' => 0];
      ?>
      <tr>
        <td><?= $idx + 1 ?></td>
        <td class="text-left" style="font-weight:600"><?= htmlspecialchars($el['fullname']) ?></td>
        <td><?= $pm['tj'] > 0 ? number_format($pm['tj'], 1) : '-' ?></td>
        <td><?= $pm['comp'] > 0 ? number_format($pm['comp'], 1) : '-' ?></td>
        <td><?= $pm['ress'] > 0 ? number_format($pm['ress'], 1) : '-' ?></td>
        <td><strong><?= $pm['tot'] > 0 ? number_format($pm['tot'], 1) : '-' ?></strong></td>
        <?php foreach ($periodes as $pe):
          $pt = $el['totaux_periodes'][$pe['id_periode']] ?? ['tj' => 0, 'comp' => 0, 'ress' => 0, 'tot' => 0];
        ?>
        <td><?= $pt['tj'] > 0 ? number_format($pt['tj'], 1) : '-' ?></td>
        <td><?= $pt['comp'] > 0 ? number_format($pt['comp'], 1) : '-' ?></td>
        <td><?= $pt['ress'] > 0 ? number_format($pt['ress'], 1) : '-' ?></td>
        <td><strong><?= $pt['tot'] > 0 ? number_format($pt['tot'], 1) : '-' ?></strong></td>
        <?php endforeach; ?>
        <?php
        foreach ($matieres as $mat):
          $mid = $mat['id_matiere'];
          $matEl = null;
          foreach ($el['matieres'] as $m) { if ($m['id_matiere'] == $mid) { $matEl = $m; break; } }
          $annMax += $matEl && $matEl['annuel'] ? $matEl['annuel']['max'] : 0;
          $annNote += $matEl && $matEl['annuel'] ? $matEl['annuel']['note'] : 0;
        endforeach;
        ?>
        <td><strong><?= $annMax > 0 ? number_format($annMax, 1) : '-' ?></strong></td>
        <td><strong><?= $annNote > 0 ? number_format($annNote, 1) : '-' ?></strong></td>
        <td><strong><?= $annMax > 0 ? number_format(($annNote / $annMax) * 100, 2) . '%' : '-' ?></strong></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <?php
      $ftTj = 0; $ftCmp = 0; $ftRess = 0;
      foreach ($eleves as $el):
        foreach ($periodes as $pe):
          $pt = $el['totaux_periodes'][$pe['id_periode']] ?? [];
          $ftTj += $pt['tj'] ?? 0; $ftCmp += $pt['comp'] ?? 0; $ftRess += $pt['ress'] ?? 0;
        endforeach;
      endforeach;
      $ftTot = $ftTj + $ftCmp + $ftRess;
      ?>
      <tr class="bg-g" style="font-weight:bold">
        <td colspan="2" class="text-left">TOTAUX ÉLÈVES</td>
        <td><?= number_format($ftTj, 1) ?></td>
        <td><?= number_format($ftCmp, 1) ?></td>
        <td><?= number_format($ftRess, 1) ?></td>
        <td><strong><?= number_format($ftTot, 1) ?></strong></td>
        <?php foreach ($periodes as $pe):
          $sTj = 0; $sCmp = 0; $sRess = 0;
          foreach ($eleves as $el):
            $pt = $el['totaux_periodes'][$pe['id_periode']] ?? [];
            $sTj += $pt['tj'] ?? 0; $sCmp += $pt['comp'] ?? 0; $sRess += $pt['ress'] ?? 0;
          endforeach;
          $sTot = $sTj + $sCmp + $sRess;
        ?>
        <td><?= number_format($sTj, 1) ?></td>
        <td><?= number_format($sCmp, 1) ?></td>
        <td><?= number_format($sRess, 1) ?></td>
        <td><strong><?= number_format($sTot, 1) ?></strong></td>
        <?php endforeach; ?>
        <td>-</td>
        <td><?= number_format($ftTot, 1) ?></td>
        <td>-</td>
      </tr>
    </tfoot>
  </table>
</div>

</body>
</html>