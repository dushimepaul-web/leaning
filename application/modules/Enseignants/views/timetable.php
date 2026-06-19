<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Teacher Timetable</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <a href="<?= base_url('Enseignants') ?>" class="text-secondary-light hover-text-primary hover-underline"> / Teacher</a>
        <a href="<?= base_url('Enseignants/details/' . $teacher['uuid']) ?>" class="text-secondary-light hover-text-primary hover-underline"> / <?= htmlspecialchars($teacher['nom'] . ' ' . $teacher['prenom']) ?></a>
        <span class="text-secondary-light"> / Timetable</span>
      </div>
    </div>
  </div>
  <div class="mt-24">
    <div class="card h-100">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
        <h5 class="fw-semibold mb-0"><?= htmlspecialchars($teacher['nom'] . ' ' . ($teacher['postnom'] ?? '') . ' ' . $teacher['prenom']) ?> - Timetable</h5>
      </div>
      <?php
      $horaires = [];
      if ($teacher['id_enseignant']) {
        $this->db->select('h.*, c.libelle as classe_libelle, m.libelle as matiere_libelle, cr.libelle as creneau_libelle, cr.heure_debut, cr.heure_fin, cr.ordre, j.libelle as jour_libelle');
        $this->db->from('horaires h');
        $this->db->join('classes c', 'c.id_classe = h.id_classe', 'left');
        $this->db->join('matieres m', 'm.id_matiere = (SELECT ens.id_matiere FROM enseignements ens WHERE ens.id_enseignement = h.id_enseignement LIMIT 1)', 'left');
        $this->db->join('creneaux cr', 'cr.id_creneau = h.id_creneau');
        $this->db->join('jours_semaine j', 'j.id_jour = h.id_jour');
        $this->db->where('h.id_enseignant', $teacher['id_enseignant']);
        $this->db->where('h.deleted_at', null);
        $this->db->order_by('h.id_jour, cr.ordre');
        $horaires = $this->db->get()->result_array();
      }
      $by_jour = [];
      foreach ($horaires as $h) {
        $by_jour[$h['id_jour']][] = $h;
      }
      $title_colors = [
        ['bg' => 'bg-warning-100', 'text' => 'text-warning-600'],
        ['bg' => 'bg-info-100', 'text' => 'text-info-600'],
        ['bg' => 'bg-success-100', 'text' => 'text-success-600'],
        ['bg' => 'bg-danger-100', 'text' => 'text-danger-600'],
        ['bg' => 'bg-primary-100', 'text' => 'text-primary-600'],
      ];
      ?>
      <div class="card-body p-20 d-flex flex-column gap-20">
        <div class="overflow-x-auto d-flex scroll-sm pb-8">
          <div class="d-flex gap-16 flex-shrink-0 flex-grow-1">
            <?php foreach ($jours as $j): ?>
              <div class="flex-grow-1" style="min-width:220px;">
                <h6 class="text-md mb-8"><?= $j['libelle'] ?></h6>
                <div class="d-flex flex-column gap-16">
                  <?php $entries = $by_jour[$j['id_jour']] ?? []; ?>
                  <?php if (empty($entries)): ?>
                    <div class="text-center text-secondary-light py-20 radius-8 border" style="background:#f8f9fa;">
                      <span>Aucun cours</span>
                    </div>
                  <?php else: ?>
                    <?php foreach ($entries as $idx => $h):
                      $c = $title_colors[$idx % count($title_colors)];
                    ?>
                      <div class="attendance-card border radius-8 overflow-hidden">
                        <h6 class="text-sm <?= $c['bg'] ?> <?= $c['text'] ?> fw-semibold py-10 px-16 text-center mb-0 card-title">
                          <?= htmlspecialchars($h['classe_libelle'] ?? 'N/A') ?>
                        </h6>
                        <div class="px-10 py-16 d-flex flex-column gap-10">
                          <div class="d-flex align-items-center gap-8">
                            <span class="d-flex line-height-1 text-secondary-light text-lg">
                              <i class="ri-book-open-line"></i>
                            </span>
                            <div class="text-primary-light text-sm d-flex">
                              <span class="w-64-px flex-shrink-0"> Subject </span>
                              <span class="flex-grow-1">: <?= htmlspecialchars($h['matiere_libelle'] ?? $h['creneau_libelle']) ?></span>
                            </div>
                          </div>
                          <div class="d-flex align-items-center gap-8">
                            <span class="d-flex line-height-1 text-secondary-light text-lg">
                              <i class="ri-building-4-line"></i>
                            </span>
                            <div class="text-primary-light text-sm d-flex">
                              <span class="w-64-px flex-shrink-0"> Room No </span>
                              <span class="flex-grow-1">: -</span>
                            </div>
                          </div>
                          <div class="d-flex align-items-center gap-8">
                            <span class="d-flex line-height-1 text-secondary-light text-lg">
                              <i class="ri-time-line"></i>
                            </span>
                            <div class="text-primary-light text-sm d-flex">
                              <span class="flex-grow-1"><?= date('h:i A', strtotime($h['heure_debut'])) ?> - <?= date('h:i A', strtotime($h['heure_fin'])) ?></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
