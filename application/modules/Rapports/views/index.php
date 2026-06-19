<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Rapports</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Rapports</span>
      </div>
    </div>
  </div>
  <div class="row g-3">
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body text-center py-32 px-24">
          <span class="mb-16 fs-1 line-height-1 text-primary d-block"><i class="ri-group-line"></i></span>
          <h5 class="fw-semibold text-primary-light mb-8">Élèves</h5>
          <p class="text-sm text-secondary-light mb-24">Effectifs, répartition par classe, etc.</p>
          <a href="<?= base_url('Etudiants') ?>" class="btn btn-primary-600">Voir</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body text-center py-32 px-24">
          <span class="mb-16 fs-1 line-height-1 text-primary d-block"><i class="ri-money-dollar-circle-line"></i></span>
          <h5 class="fw-semibold text-primary-light mb-8">Finances</h5>
          <p class="text-sm text-secondary-light mb-24">Revenus, dépenses, soldes, etc.</p>
          <a href="<?= base_url('Comptabilite') ?>" class="btn btn-primary-600">Voir</a>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-body text-center py-32 px-24">
          <span class="mb-16 fs-1 line-height-1 text-primary d-block"><i class="ri-bar-chart-line"></i></span>
          <h5 class="fw-semibold text-primary-light mb-8">Statistiques</h5>
          <p class="text-sm text-secondary-light mb-24">Présences, notes, tendances, etc.</p>
          <span class="text-sm text-secondary-light">(Fonctionnalité à venir)</span>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
