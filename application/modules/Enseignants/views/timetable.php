<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Emploi du temps - Enseignant</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <a href="<?= base_url('Enseignants') ?>" class="text-secondary-light hover-text-primary hover-underline"> / Enseignants</a>
        <a href="<?= base_url('Enseignants/details/' . $teacher['uuid']) ?>" class="text-secondary-light hover-text-primary hover-underline"> / <?= htmlspecialchars($teacher['fullname'] ?? '') ?></a>
        <span class="text-secondary-light"> / Emploi du temps</span>
      </div>
    </div>
  </div>

  <div class="mt-24">
    <div class="card">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
        <div class="d-flex align-items-center gap-12">
          <a href="<?= base_url('Enseignants') ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-6">
            <i class="ri-arrow-left-line"></i> Retour
          </a>
          <h5 class="fw-semibold mb-0">
            <i class="ri-user-line text-primary"></i>
            <?= htmlspecialchars($teacher['fullname'] ?? '') ?>
          </h5>
        </div>
        <span class="badge bg-primary-100 text-primary-600 px-12 py-4 radius-4 fw-500 text-sm">
          <?= count($horaires ?? []) ?> cours / semaine
        </span>
      </div>

      <?php
      $classes = [];
      foreach ($horaires ?? [] as $h) {
          $cl = $h['classe_libelle'] ?? 'N/A';
          $classes[$cl][] = $h;
      }
      ksort($classes);

      $class_colors = [
        ['header' => 'bg-primary-100 text-primary-700', 'border' => 'border-primary-300'],
        ['header' => 'bg-success-100 text-success-700', 'border' => 'border-success-300'],
        ['header' => 'bg-warning-100 text-warning-700', 'border' => 'border-warning-300'],
        ['header' => 'bg-info-100 text-info-700', 'border' => 'border-info-300'],
        ['header' => 'bg-danger-100 text-danger-700', 'border' => 'border-danger-300'],
        ['header' => 'bg-purple-100 text-purple-700', 'border' => 'border-purple-300'],
      ];
      $ci = 0;
      ?>

      <div class="card-body p-20">
        <?php if (empty($classes)): ?>
          <div class="text-center text-secondary-light py-40">
            <i class="ri-calendar-line text-lg mb-8 d-block"></i>
            Aucun cours programmé
          </div>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="table table-bordered mb-0" style="min-width:1100px;">
              <thead>
                <tr>
                  <th style="width:130px;" class="bg-dark text-white fw-600">Horaire</th>
                  <?php foreach ($jours as $j): ?>
                    <th class="bg-dark text-white fw-600 text-center"><?= htmlspecialchars($j['libelle']) ?></th>
                  <?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php
                $creneaux_all = [];
                foreach ($horaires ?? [] as $h) {
                    $key = $h['id_creneau'];
                    if (!isset($creneaux_all[$key])) {
                        $creneaux_all[$key] = [
                            'id' => $key,
                            'debut' => $h['heure_debut'] ?? '',
                            'fin' => $h['heure_fin'] ?? '',
                            'libelle' => $h['creneau_libelle'] ?? "Cours $key"
                        ];
                    }
                }
                uasort($creneaux_all, function($a, $b) { return strcmp($a['debut'], $b['debut']); });
                ?>
                <?php foreach ($creneaux_all as $cr): ?>
                  <tr>
                    <td class="fw-600 text-dark bg-light" style="font-size:12px; white-space:nowrap;">
                      <?php
                      $de = !empty($cr['debut']) ? date('H:i', strtotime($cr['debut'])) : '?';
                      $fi = !empty($cr['fin']) ? date('H:i', strtotime($cr['fin'])) : '?';
                      echo $de . '<br>' . $fi;
                      ?>
                    </td>
                    <?php foreach ($jours as $j):
                      $jid = $j['id_jour'];
                      $found = null;
                      foreach ($horaires ?? [] as $h) {
                          if ($h['id_jour'] == $jid && $h['id_creneau'] == $cr['id']) {
                              $found = $h;
                              break;
                          }
                      }
                      $cc = $class_colors[$ci % count($class_colors)];
                    ?>
                      <td class="text-center align-middle p-6" style="min-width:140px;">
                        <?php if ($found): ?>
                          <div class="radius-6 overflow-hidden border <?= $cc['border'] ?>" style="font-size:12px;">
                            <div class="<?= $cc['header'] ?> fw-600 py-4 px-6">
                              <?= htmlspecialchars($found['classe_libelle']) ?>
                            </div>
                            <div class="py-4 px-6 bg-white">
                              <div class="fw-500 text-dark" style="font-size:11px;">
                                <?= htmlspecialchars($found['matiere_libelle']) ?>
                              </div>
                            </div>
                          </div>
                        <?php else: ?>
                          <span class="text-secondary-light" style="font-size:11px;">-</span>
                        <?php endif; ?>
                      </td>
                    <?php endforeach; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <?php $ci++; ?>
          <div class="mt-20 pt-16 border-top border-neutral-200">
            <div class="d-flex flex-wrap gap-12">
              <?php foreach ($classes as $clName => $clSessions): ?>
                <div class="d-flex align-items-center gap-6">
                  <div class="w-12-px h-12-px radius-2 <?= $class_colors[$ci % count($class_colors)]['header'] ?>"></div>
                  <span class="text-sm text-dark fw-500"><?= htmlspecialchars($clName) ?></span>
                  <span class="text-xs text-secondary-light">(<?= count($clSessions) ?>h)</span>
                </div>
                <?php $ci++; ?>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
