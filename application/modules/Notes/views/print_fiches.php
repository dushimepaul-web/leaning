<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Fiches de points - <?= $classe_nom ?></title>
<style>
  body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #000; margin: 0; padding: 20px; background: #e5e5e5; }
  .fiche-page { background: #fff; width: 297mm; margin: 0 auto 30px auto; padding: 12mm; box-shadow: 0 4px 12px rgba(0,0,0,0.15); page-break-after: always; box-sizing: border-box; }
  h3 { text-align: center; text-transform: uppercase; margin-bottom: 4px; font-size: 13pt; }
  .entete { text-align: center; font-size: 9pt; margin-bottom: 12px; }
  table.f-table { border-collapse: collapse; width: 100%; font-size: 7.5pt; }
  table.f-table th, table.f-table td { border: 1px solid #000; padding: 2px 4px; text-align: center; }
  table.f-table th { background: #f0f0f0; font-weight: bold; }
  .text-left { text-align: left !important; }
  .bg-g { background: #f1f3f5; }
  .bg-d { background: #e9ecef; }
  @media print {
    body { background: #fff; padding: 0; }
    .fiche-page { box-shadow: none; margin: 0; width: 100%; page-break-after: always; }
    .no-print { display: none; }
  }
</style>
</head>
<body>

<div class="no-print" style="text-align: right; margin-bottom: 20px; max-width: 297mm; margin-left: auto; margin-right: auto;">
  <button onclick="window.print()" style="background: #217346; color: #fff; border: none; padding: 10px 20px; font-size: 11pt; font-weight: bold; cursor: pointer; border-radius: 4px;">Imprimer les fiches</button>
</div>

<div class="fiche-page">
  <h3>Fiches de points</h3>
  <div class="entete">
    <strong>Classe :</strong> <?= htmlspecialchars($classe_nom) ?> &nbsp;|&nbsp;
    <strong>Année :</strong> <?= htmlspecialchars($annee_libelle) ?>
  </div>

  <?php foreach ($periodes as $periode):
    $pid = $periode['id_periode'];
    $cats = ['tj', 'comp', 'ress'];
    $catLabels = ['tj' => 'TJ', 'comp' => 'COMP', 'ress' => 'RESS'];
  ?>
  <div style="margin-top: 20px;">
    <h4 style="margin:0 0 6px 0; font-size:11pt; background:#e9ecef; padding:3px 8px;"><?= htmlspecialchars($periode['libelle']) ?></h4>
    <table class="f-table">
      <thead>
        <tr>
          <th rowspan="2">#</th>
          <th rowspan="2" class="text-left">Élève</th>
          <th rowspan="2">Mat.</th>
          <?php foreach ($matieres as $mat): ?>
          <th colspan="4"><?= htmlspecialchars($mat['libelle']) ?></th>
          <?php endforeach; ?>
          <th colspan="4">TOTAUX</th>
        </tr>
        <tr>
          <?php foreach ($matieres as $mat): ?>
          <th>TJ</th><th>CMP</th><th>RSS</th><th>TOT</th>
          <?php endforeach; ?>
          <th>TJ</th><th>CMP</th><th>RSS</th><th>TOT</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $colTotals = ['tj' => 0, 'comp' => 0, 'ress' => 0, 'tot' => 0];
        $colMax = ['tj' => 0, 'comp' => 0, 'ress' => 0, 'tot' => 0];

        foreach ($students as $idx => $stu):
            $eid = $stu['id_etudiant'];
            $s_tj = 0; $s_comp = 0; $s_ress = 0;

            foreach ($matieres as $mat):
                $mid = $mat['id_matiere'];
                $notes = $note_map[$eid][$mid][$pid] ?? [];
                $s_tj += floatval($notes['tj'] ?? 0);
                $s_comp += floatval($notes['comp'] ?? 0);
                $s_ress += floatval($notes['ress'] ?? 0);
            endforeach;

            $s_tot = $s_tj + $s_comp + $s_ress;
            $colTotals['tj'] += $s_tj; $colTotals['comp'] += $s_comp;
            $colTotals['ress'] += $s_ress; $colTotals['tot'] += $s_tot;

            // Student max totals
            $m_tj = 0; $m_comp = 0; $m_ress = 0;
            foreach ($matieres as $mat):
                $mid = $mat['id_matiere'];
                $m_tj += floatval($max_map[$mid][$pid]['tj'] ?? 0);
                $m_comp += floatval($max_map[$mid][$pid]['comp'] ?? 0);
                $m_ress += floatval($max_map[$mid][$pid]['ress'] ?? 0);
            endforeach;
            $m_tot = $m_tj + $m_comp + $m_ress;
            if ($idx === 0) { $colMax['tj'] = $m_tj; $colMax['comp'] = $m_comp; $colMax['ress'] = $m_ress; $colMax['tot'] = $m_tot; }
        ?>
        <tr>
          <td><?= $idx + 1 ?></td>
          <td class="text-left"><?= htmlspecialchars($stu['nom']) ?></td>
          <td><?= htmlspecialchars($stu['matricule']) ?></td>
          <?php foreach ($matieres as $mat):
              $mid = $mat['id_matiere'];
              $notes = $note_map[$eid][$mid][$pid] ?? [];
              $v_tj = floatval($notes['tj'] ?? 0);
              $v_comp = floatval($notes['comp'] ?? 0);
              $v_ress = floatval($notes['ress'] ?? 0);
              $v_tot = $v_tj + $v_comp + $v_ress;
          ?>
          <td><?= $v_tj > 0 ? number_format($v_tj, 1) : '-' ?></td>
          <td><?= $v_comp > 0 ? number_format($v_comp, 1) : '-' ?></td>
          <td><?= $v_ress > 0 ? number_format($v_ress, 1) : '-' ?></td>
          <td><strong><?= $v_tot > 0 ? number_format($v_tot, 1) : '-' ?></strong></td>
          <?php endforeach; ?>
          <td><?= number_format($s_tj, 1) ?></td>
          <td><?= number_format($s_comp, 1) ?></td>
          <td><?= number_format($s_ress, 1) ?></td>
          <td><strong><?= number_format($s_tot, 1) ?></strong></td>
        </tr>
        <?php endforeach; ?>
        <tr class="bg-g" style="font-weight:bold;">
          <td colspan="3" class="text-left">TOTAUX ÉLÈVES</td>
          <?php foreach ($matieres as $mat):
              $mid = $mat['id_matiere'];
              $sum_tj = 0; $sum_comp = 0; $sum_ress = 0;
              foreach ($students as $stu):
                  $notes = $note_map[$stu['id_etudiant']][$mid][$pid] ?? [];
                  $sum_tj += floatval($notes['tj'] ?? 0);
                  $sum_comp += floatval($notes['comp'] ?? 0);
                  $sum_ress += floatval($notes['ress'] ?? 0);
              endforeach;
              $sum_tot = $sum_tj + $sum_comp + $sum_ress;
          ?>
          <td><?= number_format($sum_tj, 1) ?></td>
          <td><?= number_format($sum_comp, 1) ?></td>
          <td><?= number_format($sum_ress, 1) ?></td>
          <td><strong><?= number_format($sum_tot, 1) ?></strong></td>
          <?php endforeach; ?>
          <td><?= number_format($colTotals['tj'], 1) ?></td>
          <td><?= number_format($colTotals['comp'], 1) ?></td>
          <td><?= number_format($colTotals['ress'], 1) ?></td>
          <td><strong><?= number_format($colTotals['tot'], 1) ?></strong></td>
        </tr>
        <tr class="bg-d" style="font-weight:bold;">
          <td colspan="3" class="text-left">MAXIMA / MOYENNE</td>
          <?php foreach ($matieres as $mat):
              $mid = $mat['id_matiere'];
              $mxtj = $max_map[$mid][$pid]['tj'] ?? 0;
              $mxcomp = $max_map[$mid][$pid]['comp'] ?? 0;
              $mxress = $max_map[$mid][$pid]['ress'] ?? 0;
              $mxtot = $mxtj + $mxcomp + $mxress;
          ?>
          <td><?= $mxtj > 0 ? '/' . number_format($mxtj, 1) : '-' ?></td>
          <td><?= $mxcomp > 0 ? '/' . number_format($mxcomp, 1) : '-' ?></td>
          <td><?= $mxress > 0 ? '/' . number_format($mxress, 1) : '-' ?></td>
          <td><strong><?= $mxtot > 0 ? '/' . number_format($mxtot, 1) : '-' ?></strong></td>
          <?php endforeach; ?>
          <td><?= '/' . number_format($colMax['tj'], 1) ?></td>
          <td><?= '/' . number_format($colMax['comp'], 1) ?></td>
          <td><?= '/' . number_format($colMax['ress'], 1) ?></td>
          <td><strong><?= '/' . number_format($colMax['tot'], 1) ?>
            <br><small><?= $colMax['tot'] > 0 ? number_format(($colTotals['tot'] / count($students)) / $colMax['tot'] * 20, 2) . '/20' : '-' ?></small>
          </strong></td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php endforeach; ?>
</div>

</body>
</html>
