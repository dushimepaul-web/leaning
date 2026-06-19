<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h6 class="fw-semibold mb-0">Tableau de bord</h6>
      <p class="text-neutral-600 mt-4 mb-0">École — Gérez votre école, suivez les présences, dépenses et soldes.</p>
    </div>
  </div>
  <div class="row gy-4">
    <div class="col-xxl-8">
      <div class="row gy-4">
        <div class="col-xxl-4 col-sm-6">
          <div class="card shadow-1 radius-8 gradient-bg-end-1 h-100">
            <div class="card-body p-20">
              <div class="d-flex align-items-center gap-3 mb-16">
                <div class="w-44-px h-44-px bg-warning-600 rounded-circle d-flex justify-content-center align-items-center">
                  <i class="ri-graduation-cap-line text-white text-xl"></i>
                </div>
                <p class="fw-medium text-primary-light mb-1">Total Étudiants</p>
              </div>
              <h6 class="mb-0"><?= $total_etudiants ?? 0 ?></h6>
            </div>
          </div>
        </div>
        <div class="col-xxl-4 col-sm-6">
          <div class="card shadow-1 radius-8 gradient-bg-end-2 h-100">
            <div class="card-body p-20">
              <div class="d-flex align-items-center gap-3 mb-16">
                <div class="w-44-px h-44-px bg-blue-600 rounded-circle d-flex justify-content-center align-items-center">
                  <i class="ri-user-follow-line text-white text-xl"></i>
                </div>
                <p class="fw-medium text-primary-light mb-1">Total Enseignants</p>
              </div>
              <h6 class="mb-0"><?= $total_enseignants ?? 0 ?></h6>
            </div>
          </div>
        </div>
        <div class="col-xxl-4 col-sm-6">
          <div class="card shadow-1 radius-8 gradient-bg-end-3 h-100">
            <div class="card-body p-20">
              <div class="d-flex align-items-center gap-3 mb-16">
                <div class="w-44-px h-44-px bg-purple-600 rounded-circle d-flex justify-content-center align-items-center">
                  <i class="ri-list-view text-white text-xl"></i>
                </div>
                <p class="fw-medium text-primary-light mb-1">Total Classes</p>
              </div>
              <h6 class="mb-0"><?= $total_classes ?? 0 ?></h6>
            </div>
          </div>
        </div>
        <div class="col-xxl-4 col-sm-6">
          <div class="card shadow-1 radius-8 gradient-bg-end-4 h-100">
            <div class="card-body p-20">
              <div class="d-flex align-items-center gap-3 mb-16">
                <div class="w-44-px h-44-px bg-primary-600 rounded-circle d-flex justify-content-center align-items-center">
                  <i class="ri-money-dollar-circle-line text-white text-xl"></i>
                </div>
                <p class="fw-medium text-primary-light mb-1">Paiements</p>
              </div>
              <h6 class="mb-0"><?= $total_paiements ?? 0 ?></h6>
            </div>
          </div>
        </div>
        <div class="col-xxl-4 col-sm-6">
          <div class="card shadow-1 radius-8 gradient-bg-end-5 h-100">
            <div class="card-body p-20">
              <div class="d-flex align-items-center gap-3 mb-16">
                <div class="w-44-px h-44-px bg-success-600 rounded-circle d-flex justify-content-center align-items-center">
                  <i class="ri-user-add-line text-white text-xl"></i>
                </div>
                <p class="fw-medium text-primary-light mb-1">Inscriptions</p>
              </div>
              <h6 class="mb-0"><?= $total_inscriptions ?? 0 ?></h6>
            </div>
          </div>
        </div>
        <div class="col-xxl-4 col-sm-6">
          <div class="card shadow-1 radius-8 gradient-bg-end-6 h-100">
            <div class="card-body p-20">
              <div class="d-flex align-items-center gap-3 mb-16">
                <div class="w-44-px h-44-px bg-cyan-600 rounded-circle d-flex justify-content-center align-items-center">
                  <i class="ri-team-line text-white text-xl"></i>
                </div>
                <p class="fw-medium text-primary-light mb-1">Personnel</p>
              </div>
              <h6 class="mb-0">—</h6>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
