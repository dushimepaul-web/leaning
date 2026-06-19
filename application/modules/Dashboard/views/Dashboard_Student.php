<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h6 class="fw-semibold mb-0">Tableau de bord</h6>
      <p class="text-neutral-600 mt-4 mb-0">Étudiant — Suivez vos cours, notes et performances.</p>
    </div>
  </div>
  <div class="row gy-4">
    <div class="col-xxl-4">
      <div class="card radius-12 border-0 h-100">
        <div class="card-body p-24 d-flex gap-16 flex-sm-nowrap flex-wrap">
          <div class="radius-8 overflow-hidden position-relative z-1 py-32 px-20 text-center d-flex justify-content-center align-items-center flex-grow-1">
            <img src="<?= base_url() ?>assets/images/bg/edit-profile-bg.png" alt="BG" class="position-absolute start-0 top-0 w-100 h-100 z-n1">
            <div>
              <span class="mb-12">
                <img src="<?= base_url() ?>assets/images/thumbs/studnt-edit-profile-img.png" alt="Profile" class="rounded-circle object-fit-cover">
              </span>
              <h6 class="text-white"><?= $this->session->userdata('nom_complet') ?></h6>
              <span class="text-white text-lg d-block">Bienvenue</span>
            </div>
          </div>
          <div class="d-flex flex-column gap-20 flex-grow-1 justify-content-between">
            <div class="radius-8 py-24 px-16 text-start d-flex align-items-center gap-12 bg-purple-100">
              <span class="w-48-px h-48-px d-inline-flex justify-content-center align-items-center rounded-circle border border-purple-300 bg-purple-200">
                <i class="ri-calendar-event-line text-purple-600 text-xl"></i>
              </span>
              <div>
                <span class="text-secondary-light fw-medium d-block">Événements</span>
                <h5 class="text-primary-light">—</h5>
              </div>
            </div>
            <div class="radius-8 py-24 px-16 text-start d-flex align-items-center gap-12 bg-success-100">
              <span class="w-48-px h-48-px d-inline-flex justify-content-center align-items-center rounded-circle border border-success-300 bg-success-200">
                <i class="ri-notification-3-line text-success-600 text-xl"></i>
              </span>
              <div>
                <span class="text-secondary-light fw-medium d-block">Notifications</span>
                <h5 class="text-primary-light">—</h5>
              </div>
            </div>
            <div class="radius-8 py-24 px-16 text-start d-flex align-items-center gap-12 bg-primary-100">
              <span class="w-48-px h-48-px d-inline-flex justify-content-center align-items-center rounded-circle border border-primary-300 bg-primary-200">
                <i class="ri-calendar-check-line text-primary-600 text-xl"></i>
              </span>
              <div>
                <span class="text-secondary-light fw-medium d-block">Présences</span>
                <h5 class="text-primary-light">—</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xxl-8">
      <div class="row gy-4">
        <div class="col-12">
          <div class="card h-100">
            <div class="card-body p-0">
              <div class="d-flex flex-wrap align-items-center justify-content-between px-20 py-16 border-bottom border-neutral-200">
                <h6 class="text-lg mb-0">Mes Notes Récentes</h6>
              </div>
              <div class="p-20">
                <p class="text-secondary-light">Aucune note disponible pour le moment.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
