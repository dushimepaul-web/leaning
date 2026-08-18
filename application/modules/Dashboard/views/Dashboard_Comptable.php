<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>

<div class="dashboard-main-body">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h4 class="fw-bold mb-0">Tableau de bord - Comptabilité</h4>
      <p class="text-secondary-light text-sm mb-0">Suivi des recettes et des échéances financières.</p>
    </div>
  </div>

  <!-- KPI CARDS -->
  <div class="row gy-4 mb-24">
    <div class="col-xl-6 col-sm-6">
      <div class="card shadow-sm border-0 radius-8 h-100" style="border-left: 4px solid #25A194 !important;">
        <div class="card-body p-20">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-uppercase text-secondary-light text-xs fw-semibold">Recettes du Jour</span>
              <h4 class="fw-bold mt-4 mb-0"><?= number_format($kpis['recettes_jour'], 0, ',', ' ') ?> FBU</h4>
            </div>
            <div class="w-48-px h-48-px rounded-circle d-flex justify-content-center align-items-center" style="background: rgba(37, 161, 148, 0.1); color: #25A194;">
              <i class="ri-wallet-3-line text-2xl"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-6 col-sm-6">
      <div class="card shadow-sm border-0 radius-8 h-100" style="border-left: 4px solid #10b981 !important;">
        <div class="card-body p-20">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-uppercase text-secondary-light text-xs fw-semibold">Recettes du Mois</span>
              <h4 class="fw-bold mt-4 mb-0"><?= number_format($kpis['recettes_mois'], 0, ',', ' ') ?> FBU</h4>
            </div>
            <div class="w-48-px h-48-px rounded-circle d-flex justify-content-center align-items-center bg-success-50 text-success-600">
              <i class="ri-money-dollar-box-line text-2xl"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- TABLES SECTION -->
  <div class="row gy-4">
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm radius-8 h-100">
        <div class="card-body p-24">
          <h6 class="fw-bold mb-16 text-danger"><i class="ri-time-line me-1"></i> Échéances en Retard / À Relancer</h6>
          <div class="table-responsive">
            <table class="table table-sm table-striped">
              <thead><tr><th>Élève</th><th>Classe</th><th>Montant</th><th>Action</th></tr></thead>
              <tbody>
                <?php if(!empty($echeances_retard)): foreach($echeances_retard as $e): ?>
                <tr>
                  <td><?= htmlspecialchars($e['fullname']) ?></td>
                  <td><?= htmlspecialchars($e['classe']) ?></td>
                  <td class="fw-semibold"><?= number_format($e['montant'], 0, ',', ' ') ?> FBU</td>
                  <td><a href="<?= base_url('Paiements?search='.urlencode($e['fullname'])) ?>" class="btn btn-sm btn-outline-primary py-2 px-10">Payer</a></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-center text-muted">Aucune échéance en retard.</td></tr>
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
          <h6 class="fw-bold mb-16"><i class="ri-receipt-line me-1"></i> Derniers Reçus Émis</h6>
          <div class="table-responsive">
            <table class="table table-sm table-striped">
              <thead><tr><th>N° Reçu</th><th>Élève</th><th>Montant</th><th>Date</th></tr></thead>
              <tbody>
                <?php if(!empty($derniers_recus)): foreach($derniers_recus as $r): ?>
                <tr>
                  <td class="fw-semibold"><?= htmlspecialchars($r['numero_recu']) ?></td>
                  <td><?= htmlspecialchars($r['fullname'] ?? 'N/A') ?></td>
                  <td><?= number_format($r['montant'], 0, ',', ' ') ?> FBU</td>
                  <td><?= $r['date_creation'] ?? '' ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-center text-muted">Aucun reçu récent.</td></tr>
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
