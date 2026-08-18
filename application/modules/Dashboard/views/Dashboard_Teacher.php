<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>

<div class="dashboard-main-body">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h4 class="fw-bold mb-0">Tableau de bord - Enseignant</h4>
      <p class="text-secondary-light text-sm mb-0">Vos classes, matières et emploi du temps du jour.</p>
    </div>
  </div>

  <div class="row gy-4">
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm radius-8 h-100">
        <div class="card-body p-24">
          <h6 class="fw-bold mb-16"><i class="ri-book-open-line me-1"></i> Mes Classes & Matières</h6>
          <div class="table-responsive">
            <table class="table table-sm table-striped">
              <thead><tr><th>Classe</th><th>Matière</th></tr></thead>
              <tbody>
                <?php if(!empty($classes_matieres)): foreach($classes_matieres as $cm): ?>
                <tr>
                  <td class="fw-semibold"><?= htmlspecialchars($cm['classe']) ?></td>
                  <td><?= htmlspecialchars($cm['matiere']) ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="2" class="text-center text-muted">Aucune affectation trouvée.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm radius-8 h-100">
        <div class="card-body p-24">
          <h6 class="fw-bold mb-16"><i class="ri-calendar-schedule-line me-1"></i> Emploi du temps (Aujourd'hui)</h6>
          <div class="table-responsive">
            <table class="table table-sm table-striped">
              <thead><tr><th>Heure</th><th>Classe</th><th>Matière</th><th>Salle</th></tr></thead>
              <tbody>
                <?php if(!empty($emploi_du_temps)): foreach($emploi_du_temps as $ed): ?>
                <tr>
                  <td><?= $ed['heure_debut'] ?> - <?= $ed['heure_fin'] ?></td>
                  <td class="fw-semibold"><?= htmlspecialchars($ed['classe']) ?></td>
                  <td><?= htmlspecialchars($ed['matiere']) ?></td>
                  <td><?= htmlspecialchars($ed['salle'] ?? '-') ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-center text-muted">Aucun cours prévu aujourd'hui.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include VIEWPATH.'includes/Footer.php'; ?>
