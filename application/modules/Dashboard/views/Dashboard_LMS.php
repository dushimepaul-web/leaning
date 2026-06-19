<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h6 class="fw-semibold mb-0">Tableau de bord</h6>
      <p class="text-neutral-600 mt-4 mb-0">LMS — Gérez les cours, étudiants, devoirs et métriques de performance.</p>
    </div>
  </div>
  <div class="row gy-4">
    <div class="col-xxl-6">
      <div class="bg-base rounded-3 p-20">
        <div class="row g-3">
          <div class="col-sm-6">
            <div class="border rounded-3">
              <div class="px-16 py-12 d-flex gap-8 align-items-center">
                <span class="d-flex align-items-center justify-content-center w-32-px h-32-px rounded-circle bg-primary-100">
                  <i class="ri-book-open-line text-primary-600"></i>
                </span>
                <span class="text-primary-light fw-medium">Cours Actifs</span>
              </div>
              <div class="px-16 py-12">
                <h6 class="mb-0">—</h6>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="border rounded-3">
              <div class="px-16 py-12 d-flex gap-8 align-items-center">
                <span class="d-flex align-items-center justify-content-center w-32-px h-32-px rounded-circle bg-purple-100">
                  <i class="ri-graduation-cap-line text-purple-600"></i>
                </span>
                <span class="text-primary-light fw-medium">Total Étudiants</span>
              </div>
              <div class="px-16 py-12">
                <h6 class="mb-0"><?= $total_etudiants ?? 0 ?></h6>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="border rounded-3">
              <div class="px-16 py-12 d-flex gap-8 align-items-center">
                <span class="d-flex align-items-center justify-content-center w-32-px h-32-px rounded-circle bg-warning-100">
                  <i class="ri-list-view text-warning-600"></i>
                </span>
                <span class="text-primary-light fw-medium">Total Cours</span>
              </div>
              <div class="px-16 py-12">
                <h6 class="mb-0">—</h6>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="border rounded-3">
              <div class="px-16 py-12 d-flex gap-8 align-items-center">
                <span class="d-flex align-items-center justify-content-center w-32-px h-32-px rounded-circle bg-success-100">
                  <i class="ri-user-follow-line text-success-600"></i>
                </span>
                <span class="text-primary-light fw-medium">Total Enseignants</span>
              </div>
              <div class="px-16 py-12">
                <h6 class="mb-0"><?= $total_enseignants ?? 0 ?></h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
