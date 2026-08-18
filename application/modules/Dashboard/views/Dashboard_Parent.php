<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h6 class="fw-semibold mb-0">Tableau de bord</h6>
      <p class="text-neutral-600 mt-4 mb-0">Parent — Suivez les progrès, présences et performances de vos enfants.</p>
    </div>
  </div>
  <div class="row gy-4">
    <div class="col-xxl-3 col-sm-6">
      <div class="card p-3 shadow-2 radius-8 h-100 border-0 gradient-bg-end-12">
        <div class="card-body p-0">
          <div class="d-flex align-items-center gap-3">
            <span class="mb-0 w-48-px h-48-px bg-warning-600 text-white d-flex justify-content-center align-items-center rounded-circle">
              <i class="ri-money-dollar-circle-line text-white text-xl"></i>
            </span>
            <div>
              <span class="fw-medium text-primary-light text-md">Frais Impayés</span>
            </div>
          </div>
          <div class="mt-16">
            <h6 class="fw-semibold mb-0">—</h6>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-sm-6">
      <div class="card p-3 shadow-2 radius-8 h-100 border-0 gradient-bg-end-13">
        <div class="card-body p-0">
          <div class="d-flex align-items-center gap-3">
            <span class="mb-0 w-48-px h-48-px bg-blue-600 text-white d-flex justify-content-center align-items-center rounded-circle">
              <i class="ri-graduation-cap-line text-white text-xl"></i>
            </span>
            <div>
              <span class="fw-medium text-primary-light text-md">Moyenne Générale</span>
            </div>
          </div>
          <div class="mt-16">
            <h6 class="fw-semibold mb-0">—</h6>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-sm-6">
      <div class="card p-3 shadow-2 radius-8 h-100 border-0 gradient-bg-end-14">
        <div class="card-body p-0">
          <div class="d-flex align-items-center gap-3">
            <span class="mb-0 w-48-px h-48-px bg-purple-600 text-white d-flex justify-content-center align-items-center rounded-circle">
              <i class="ri-calendar-check-line text-white text-xl"></i>
            </span>
            <div>
              <span class="fw-medium text-primary-light text-md">Présences</span>
            </div>
          </div>
          <div class="mt-16">
            <h6 class="fw-semibold mb-0">—%</h6>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-3 col-sm-6">
      <div class="card p-3 shadow-2 radius-8 h-100 border-0 gradient-bg-end-15">
        <div class="card-body p-0">
          <div class="d-flex align-items-center gap-3">
            <span class="mb-0 w-48-px h-48-px bg-success-600 text-white d-flex justify-content-center align-items-center rounded-circle">
              <i class="ri-notification-3-line text-white text-xl"></i>
            </span>
            <div>
              <span class="fw-medium text-primary-light text-md">Notifications</span>
            </div>
          </div>
          <div class="mt-16">
            <h6 class="fw-semibold mb-0">—</h6>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
