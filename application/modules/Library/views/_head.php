<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light"><?= html_escape($page_title ?? ($title ?? 'Librairie')) ?></h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / </span>
        <a href="<?= base_url('Library') ?>" class="text-secondary-light hover-text-primary hover-underline">Librairie</a>
        <span class="text-secondary-light"> / <?= html_escape($page_subtitle ?? '') ?></span>
      </div>
    </div>
    <div class="d-flex align-items-center gap-2"><?= $page_actions ?? '' ?></div>
  </div>
<?php $libVer = file_exists(FCPATH.'assets/js/library.js') ? filemtime(FCPATH.'assets/js/library.js') : time(); ?>
<script src="<?= base_url() ?>assets/js/library.js?v=<?= $libVer ?>"></script>
