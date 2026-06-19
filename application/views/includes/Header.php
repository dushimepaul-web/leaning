<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">


<!-- Mirrored from edudash-php.theme.picode.in/index.php by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 09 Apr 2026 00:39:52 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description"
    content="Modern Education Admin Dashboard for schools, colleges, universities, and eLearning platforms. Includes student and course management, attendance, exams, payments, analytics, and a fully responsive clean UI—ideal for LMS, coaching centers, and academic admin systems.">
  <meta name="keywords"
    content="Education Admin Dashboard, School Admin Panel, College Dashboard, University Dashboard, LMS Dashboard, eLearning Admin Template, Student Management System, Course Management, Education Template, Study Dashboard, Online Learning Dashboard, Academic Admin Panel, Bootstrap Dashboard, React Education Dashboard, Next.js Education Template">
  <meta name="robots" content="INDEX,FOLLOW">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Title -->
  <title><?= $this->Model->get_setting('nom_ecole', 'VIP School') ?> - Tableau de bord</title>
  <?php $fav = $this->Model->get_setting('favicon_ecole', 'assets/images/favicon.png'); ?>
  <link rel="icon" type="image/png" href="<?= base_url($fav) ?>" sizes="16x16">
  <!-- remix icon font css  -->
  <link rel="stylesheet" href="<?=base_url()?>assets/css/remixicon.css">
  <!-- BootStrap css -->
  <link rel="stylesheet" href="<?=base_url()?>assets/css/lib/bootstrap.min.css">
  <!-- Apex Chart css -->
  <link rel="stylesheet" href="<?=base_url()?>assets/css/lib/apexcharts.css">
  <!-- Data Table css -->
  <link rel="stylesheet" href="<?=base_url()?>assets/css/lib/dataTables.min.css">
  <!-- Date picker css -->
  <link rel="stylesheet" href="<?=base_url()?>assets/css/lib/flatpickr.min.css">
  <!-- Calendar css -->
  <link rel="stylesheet" href="<?=base_url()?>assets/css/lib/full-calendar.css">
  <!-- calendar -->
  <link rel="stylesheet" href="<?=base_url()?>assets/css/lib/calendar.css">
  <!-- main css -->
  <link rel="stylesheet" href="<?=base_url()?>assets/css/style.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?=base_url()?>assets/css/sweetalert2.min.css">
  <script src="<?=base_url()?>assets/js/lib/sweetalert2.min.js"></script>
  <style>
    .swal2-popup { font-family: "Inter", sans-serif; border-radius: 0.75rem; background: #fff; box-shadow: 0 10px 40px rgba(0,0,0,0.15); padding: 2rem; border: 1px solid #e2e8f0; }
    .swal2-title { font-weight: 600; font-size: 1.25rem; color: #273142; }
    .swal2-html-container { font-size: 0.9375rem; color: #5A6877; }
    .swal2-icon { border-width: 3px; }
    .swal2-icon.swal2-warning { border-color: #FF7A2C; color: #FF7A2C; }
    .swal2-icon.swal2-error { border-color: #DC2626; color: #DC2626; }
    .swal2-icon.swal2-success { border-color: #00C31D; color: #00C31D; }
    .swal2-icon.swal2-question { border-color: #25A194; color: #25A194; }
    .swal2-icon.swal2-info { border-color: #04B4FF; color: #04B4FF; }
    .swal2-styled.swal2-confirm { border-radius: 0.5rem; font-weight: 500; font-size: 1rem; padding: 0.5625rem 1.5rem; }
    .swal2-styled.swal2-confirm.swal2-styled--primary { background-color: #25A194; }
    .swal2-styled.swal2-confirm.swal2-styled--primary:hover { background-color: #1C7F73; }
    .swal2-styled.swal2-confirm.swal2-styled--danger { background-color: #DC2626; }
    .swal2-styled.swal2-confirm.swal2-styled--danger:hover { background-color: #B91C1C; }
    .swal2-styled.swal2-cancel { border-radius: 0.5rem; font-weight: 500; font-size: 1rem; padding: 0.5625rem 1.5rem; background-color: #EEF2F6; color: #273142; border: 1px solid #d1d5db80; }
    .swal2-styled.swal2-cancel:hover { background-color: #e2e8f0; }
    .swal2-timer-progress-bar { background: #25A194; }
    .swal2-toast { border-radius: 0.5rem !important; box-shadow: 0 4px 16px rgba(0,0,0,0.1) !important; }
    .swal2-toast .swal2-title { font-size: 0.875rem !important; }
    .swal2-toast .swal2-timer-progress-bar { background: #25A194; }
    .navbar-search.dt-search { position: relative; display: inline-block; }
    .navbar-search.dt-search .dt-input { width: 24.25rem; height: 2.5rem; background-color: #EEF2F6; border: 1px solid #d1d5db80; border-radius: 0.5rem; padding-block: 0.3125rem; padding-inline-start: 2.625rem; padding-inline-end: 1.25rem; color: #273142; outline: none; }
    .navbar-search.dt-search .dt-input:focus { border-color: #25A194; }
    .navbar-search.dt-search .icon { position: absolute; top: 50%; transform: translateY(-50%); inset-inline-start: 0.9375rem; font-size: 1.125rem; color: #273142; pointer-events: none; }
    table.dataTable { margin-top: 0 !important; }
    div.dt-scroll { overflow-x: auto; }
  </style>
</head>

<body>

  <!-- Theme Customization Structure Start -->
<div class="body-overlay"></div>

<button type="button"
    class="theme-customization__button w-48-px h-48-px bg-primary-600 text-white rounded-circle d-flex justify-content-center align-items-center position-fixed end-0 bottom-0 mb-40 me-40 text-2xxl bg-hover-primary-700" aria-label="Theme Customization Button">
    <i class="ri-settings-3-line animate-spin"></i>
</button>
<div class="theme-customization-sidebar w-100 bg-base h-100vh overflow-y-auto position-fixed end-0 top-0">
    <div class="d-flex align-items-center gap-3 py-16 px-24 justify-content-between border-bottom">
        <div>
            <h6 class="text-sm dark:text-white">Theme Settings</h6>
            <p class="text-xs mb-0 text-neutral-500 dark:text-neutral-200">Customize and preview instantly</p>
        </div>
        <button data-slot="button"
            class="theme-customization-sidebar__close text-neutral-900 bg-transparent text-hover-primary-600 d-flex text-xl">
            <i class="ri-close-fill"></i>
        </button>
    </div>

    <div class="d-flex flex-column gap-48 p-24 overflow-y-auto flex-grow-1">

        <div class="theme-setting-item">
            <h6 class="fw-medium text-primary-light text-md mb-3">Theme Mode</h6>
            <div class="d-grid grid-cols-3 gap-3 dark-light-mode">
                <button type="button"
                    class="theme-btn theme-setting-item__btn d-flex align-items-center justify-content-center h-64-px rounded-3 text-xl active"
                    data-theme="light" aria-label="light">
                    <i class="ri-sun-line"></i>
                </button>
                <button type="button"
                    class="theme-btn theme-setting-item__btn d-flex align-items-center justify-content-center h-64-px rounded-3 text-xl"
                    data-theme="dark" aria-label="dark">
                    <i class="ri-moon-line"></i>
                </button>
                <button type="button"
                    class="theme-btn theme-setting-item__btn d-flex align-items-center justify-content-center h-64-px rounded-3 text-xl"
                    data-theme="system" aria-label="system">
                    <i class="ri-computer-line"></i>
                </button>
            </div>
        </div>

        <div class="theme-setting-item">
            <h6 class="fw-medium text-primary-light text-md mb-3">Page Direction</h6>
            <div class="d-grid grid-cols-2 gap-3">
                <button type="button"
                    class="theme-setting-item__btn ltr-mode-btn d-flex align-items-center justify-content-center gap-2 h-56-px rounded-3 text-xl" aria-label="LTR">
                    <span><i class="ri-align-item-left-line"></i></span>
                    <span class="h6 text-sm font-medium mb-0">LTR</span>
                </button>

                <button type="button"
                    class="theme-setting-item__btn rtl-mode-btn d-flex align-items-center justify-content-center gap-2 h-56-px rounded-3 text-xl" aria-label="RTL">
                    <span class="h6 text-sm font-medium mb-0">RTL</span>
                    <span><i class="ri-align-item-right-line"></i></span>
                </button>
            </div>
        </div>

        <div class="theme-setting-item">
            <h6 class="fw-medium text-primary-light text-md mb-3">Color Schema</h6>
            <div class="d-grid grid-cols-3 gap-3">
                <button type="button"
                    class="color-picker-btn d-flex flex-column justify-content-center align-items-center"
                    data-color="base" aria-label="Base">
                    <span class="color-picker-btn__box h-40-px w-100 rounded-3"
                        style="background-color: #25A194;"></span>
                    <span class="fw-medium mt-1" style="color: #25A194;">Base</span>
                </button>
                <button type="button"
                    class="color-picker-btn d-flex flex-column justify-content-center align-items-center"
                    data-color="red" aria-label="Red">
                    <span class="color-picker-btn__box h-40-px w-100 rounded-3"
                        style="background-color: #dc2626;"></span>
                    <span class="fw-medium mt-1" style="color: #dc2626;">Red</span>
                </button>
                <button type="button"
                    class="color-picker-btn d-flex flex-column justify-content-center align-items-center"
                    data-color="blue" aria-label="Blue">
                    <span class="color-picker-btn__box h-40-px w-100 rounded-3"
                        style="background-color: #2563eb;"></span>
                    <span class="fw-medium mt-1" style="color: #2563eb;">Blue</span>
                </button>
                <button type="button"
                    class="color-picker-btn d-flex flex-column justify-content-center align-items-center"
                    data-color="yellow" aria-label="Yellow">
                    <span class="color-picker-btn__box h-40-px w-100 rounded-3"
                        style="background-color: #ff9f29;"></span>
                    <span class="fw-medium mt-1" style="color: #ff9f29;">Yellow</span>
                </button>
                <button type="button"
                    class="color-picker-btn d-flex flex-column justify-content-center align-items-center"
                    data-color="cyan" aria-label="Cyan">
                    <span class="color-picker-btn__box h-40-px w-100 rounded-3"
                        style="background-color: #00b8f2;"></span>
                    <span class="fw-medium mt-1" style="color: #00b8f2;">Cyan</span>
                </button>
                <button type="button"
                    class="color-picker-btn d-flex flex-column justify-content-center align-items-center"
                    data-color="violet" aria-label="Violet">
                    <span class="color-picker-btn__box h-40-px w-100 rounded-3"
                        style="background-color: #7c3aed;"></span>
                    <span class="fw-medium mt-1" style="color: #7c3aed;">Violet</span>
                </button>
            </div>
        </div>

    </div>
</div>
<!-- Theme Customization Structure End -->









  <div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300">
  </div><aside class="sidebar">
  <button type="button" class="sidebar-close-btn">
    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
  </button>
  <div class="">
    <div class="sidebar-logo d-flex align-items-center justify-content-between">
      <a href="<?= base_url('Dashboard') ?>" class="">
        <?php $l = $this->Model->get_setting('logo_ecole', 'assets/images/logo.png'); ?>
        <img src="<?= base_url($l) ?>" alt="site logo" class="light-logo">
        <img src="<?= base_url($l) ?>" alt="site logo" class="dark-logo">
        <img src="<?= base_url($l) ?>" alt="site logo" class="logo-icon">
      </a>
      <button type="button" class="text-xxl d-xl-flex d-none line-height-1 sidebar-toggle text-neutral-500"
        aria-label="Collapse Sidebar">
        <i class="ri-contract-left-line"></i>
      </button>
    </div>
  </div>
  <!-- User Info start -->
  <div class="mx-16 py-12">
    <div class="dropdown profile-dropdown">
      <button type="button"
        class="profile-dropdown__button d-flex align-items-center justify-content-between p-10 w-100 overflow-hidden bg-neutral-50 radius-12 "
        data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
        <span class="d-flex align-items-start gap-10">
          <?php $photo = $this->session->userdata('photo'); ?>
          <?php if (!empty($photo)): ?>
          <img src="<?=base_url($photo)?>" alt="Photo" class="w-40-px h-40-px rounded-circle object-fit-cover flex-shrink-0">
          <?php else: ?>
          <span class="w-40-px h-40-px rounded-circle bg-primary-100 d-flex align-items-center justify-content-center flex-shrink-0">
            <iconify-icon icon="solar:user-bold" class="text-primary-600 text-xl"></iconify-icon>
          </span>
          <?php endif; ?>
          <span class="profile-dropdown__contents">
            <span class="h6 mb-0 text-md d-block text-primary-light"><?= $this->session->userdata('nom_complet') ?? 'Administrateur' ?></span>
            <span class="text-secondary-light text-sm mb-0 d-block"><?= $this->session->userdata('role_libelle') ?? 'Admin' ?></span>
          </span>
        </span>
        <span class="profile-dropdown__icon pe-8 text-xl d-flex line-height-1">
          <i class="ri-arrow-right-s-line"></i>
        </span>
      </button>
      <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
        <li>
          <a href="<?= base_url('Profile') ?>"
            class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 py-6">
            <i class="ri-user-3-line"></i>
            Mon Profil
          </a>
        </li>
        <li>
          <a href="<?= base_url('Parametres') ?>"
            class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 py-6">
            <i class="ri-settings-3-line"></i>
            Paramètres
          </a>
        </li>
        <li>
          <a href="<?= base_url('Logout') ?>"
            class="dropdown-item rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-2 py-6">
            <i class="ri-shut-down-line"></i>
            Déconnexion
          </a>
        </li>
      </ul>
    </div>
  </div>

































