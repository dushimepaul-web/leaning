<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h6 class="fw-semibold mb-0">Tableau de bord</h6>
      <p class="text-neutral-600 mt-4 mb-0"><?= $annee_active_name ?> — Période : <?= $periode_active_name ?></p>
    </div>
  </div>
  <div class="row gy-4">
    <div class="col-xl-4 col-sm-6">
      <div class="card shadow-1 radius-8 gradient-bg-end-1 h-100">
        <div class="card-body p-24">
          <div class="d-flex align-items-center gap-3 mb-16">
            <div class="w-48-px h-48-px bg-warning-600 rounded-circle d-flex justify-content-center align-items-center">
              <i class="ri-graduation-cap-line text-white text-xl"></i>
            </div>
            <p class="fw-medium text-primary-light mb-1">Total Élèves</p>
          </div>
          <h4 class="mb-0"><?= $total_etudiants ?? 0 ?></h4>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-sm-6">
      <div class="card shadow-1 radius-8 gradient-bg-end-2 h-100">
        <div class="card-body p-24">
          <div class="d-flex align-items-center gap-3 mb-16">
            <div class="w-48-px h-48-px bg-blue-600 rounded-circle d-flex justify-content-center align-items-center">
              <i class="ri-user-follow-line text-white text-xl"></i>
            </div>
            <p class="fw-medium text-primary-light mb-1">Enseignants</p>
          </div>
          <h4 class="mb-0"><?= $total_enseignants ?? 0 ?></h4>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-sm-6">
      <div class="card shadow-1 radius-8 gradient-bg-end-3 h-100">
        <div class="card-body p-24">
          <div class="d-flex align-items-center gap-3 mb-16">
            <div class="w-48-px h-48-px bg-purple-600 rounded-circle d-flex justify-content-center align-items-center">
              <i class="ri-list-view text-white text-xl"></i>
            </div>
            <p class="fw-medium text-primary-light mb-1">Classes</p>
          </div>
          <h4 class="mb-0"><?= $total_classes ?? 0 ?></h4>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-sm-6">
      <div class="card shadow-1 radius-8 gradient-bg-end-4 h-100">
        <div class="card-body p-24">
          <div class="d-flex align-items-center gap-3 mb-16">
            <div class="w-48-px h-48-px bg-primary-600 rounded-circle d-flex justify-content-center align-items-center">
              <i class="ri-money-dollar-circle-line text-white text-xl"></i>
            </div>
            <p class="fw-medium text-primary-light mb-1">Paiements</p>
          </div>
          <h4 class="mb-0"><?= $total_paiements ?? 0 ?></h4>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-sm-6">
      <div class="card shadow-1 radius-8 gradient-bg-end-5 h-100">
        <div class="card-body p-24">
          <div class="d-flex align-items-center gap-3 mb-16">
            <div class="w-48-px h-48-px bg-success-600 rounded-circle d-flex justify-content-center align-items-center">
              <i class="ri-stack-line text-white text-xl"></i>
            </div>
            <p class="fw-medium text-primary-light mb-1">Stocks</p>
          </div>
          <h4 class="mb-0"><?= $total_produits ?? 0 ?></h4>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-sm-6">
      <div class="card shadow-1 radius-8 gradient-bg-end-6 h-100">
        <div class="card-body p-24">
          <div class="d-flex align-items-center gap-3 mb-16">
            <div class="w-48-px h-48-px bg-cyan-600 rounded-circle d-flex justify-content-center align-items-center">
              <i class="ri-shopping-cart-line text-white text-xl"></i>
            </div>
            <p class="fw-medium text-primary-light mb-1">Mouvements</p>
          </div>
          <h4 class="mb-0"><?= $total_mouvements ?? 0 ?></h4>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
