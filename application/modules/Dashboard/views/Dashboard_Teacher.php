<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h6 class="fw-semibold mb-0">Tableau de bord</h6>
      <p class="text-neutral-600 mt-4 mb-0">Enseignant — Gérez vos cours, élèves et devoirs depuis un tableau centralisé.</p>
    </div>
  </div>
  <div class="row gy-4">
    <div class="col-xxl-7">
      <div class="card radius-12 border-0 h-100">
        <div class="card-body p-24">
          <div class="row g-3">
            <div class="col-md-5">
              <div class="radius-8 overflow-hidden position-relative z-1 h-100 py-32 px-20 text-center d-flex justify-content-center align-items-center">
                <img src="<?= base_url() ?>assets/images/bg/edit-profile-bg.png" alt="BG" class="position-absolute start-0 top-0 w-100 h-100 object-fit-cover z-n1">
                <div>
                  <span class="mb-12">
                    <img src="<?= base_url() ?>assets/images/thumbs/edit-profile-img.png" alt="Profile" class="rounded-circle object-fit-cover">
                  </span>
                  <h6 class="text-white"><?= $this->session->userdata('nom_complet') ?></h6>
                  <span class="text-white text-lg d-block">Enseignant</span>
                </div>
              </div>
            </div>
            <div class="col-md-7">
              <div class="row g-3">
                <div class="col-sm-6">
                  <div class="radius-8 py-24 px-16 text-center bg-purple-100">
                    <span class="w-48-px h-48-px d-inline-flex justify-content-center align-items-center rounded-circle border border-purple-300 bg-purple-200">
                      <i class="ri-graduation-cap-line text-purple-600 text-xl"></i>
                    </span>
                    <span class="text-secondary-light fw-medium d-block mt-12">Total Étudiants</span>
                    <h5 class="text-primary-light"><?= $total_etudiants ?? 0 ?></h5>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="radius-8 py-24 px-16 text-center bg-success-100">
                    <span class="w-48-px h-48-px d-inline-flex justify-content-center align-items-center rounded-circle border border-success-300 bg-success-200">
                      <i class="ri-book-open-line text-success-600 text-xl"></i>
                    </span>
                    <span class="text-secondary-light fw-medium d-block mt-12">Matières</span>
                    <h5 class="text-primary-light">—</h5>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="radius-8 py-24 px-16 text-center bg-primary-100">
                    <span class="w-48-px h-48-px d-inline-flex justify-content-center align-items-center rounded-circle border border-primary-300 bg-primary-200">
                      <i class="ri-time-line text-primary-600 text-xl"></i>
                    </span>
                    <span class="text-secondary-light fw-medium d-block mt-12">Cours/Semaine</span>
                    <h5 class="text-primary-light">—</h5>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="radius-8 py-24 px-16 text-center bg-danger-100">
                    <span class="w-48-px h-48-px d-inline-flex justify-content-center align-items-center rounded-circle border border-danger-300 bg-danger-200">
                      <i class="ri-calendar-check-line text-danger-600 text-xl"></i>
                    </span>
                    <span class="text-secondary-light fw-medium d-block mt-12">Présences</span>
                    <h5 class="text-primary-light">—</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
