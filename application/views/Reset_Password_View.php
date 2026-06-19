<!doctype html>
<html lang="fr" data-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Réinitialiser le mot de passe - <?= $this->Model->get_setting('nom_ecole', 'VIP School') ?></title>
  <?php $fav = $this->Model->get_setting('favicon_ecole', 'assets/images/favicon.png'); ?>
  <link rel="icon" type="image/png" href="<?= base_url($fav) ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/remixicon.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/lib/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
  <div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300"></div>
  <div class="d-lg-flex bg-white" style="min-height:100vh;">
    <div class="w-50 d-lg-flex d-none overflow-hidden">
      <img src="<?= base_url('assets/images/thumbs/login-img.png') ?>" alt="Réinitialisation" class="w-100 h-100 object-fit-cover">
    </div>
    <div class="lg-w-50 px-24 py-32 d-flex justify-content-center align-items-center">
      <div class="max-w-540-px mx-auto w-100">
        <a href="<?= base_url() ?>">
          <?php $logo = $this->Model->get_setting('logo_ecole', 'assets/images/logo.png'); ?>
          <img src="<?= base_url($logo) ?>" alt="Logo">
        </a>
        <div class="mt-32 mb-32">
          <h1 class="h6 fw-bold text-primary-light mb-8">Réinitialiser le mot de passe</h1>
          <p class="text-sm text-secondary-light mb-0">Choisissez un nouveau mot de passe pour votre compte</p>
        </div>
        <?php if (!empty($this->session->flashdata('sms'))) echo $this->session->flashdata('sms'); ?>
        <form action="<?= base_url('Admin/do_reset_password') ?>" method="POST" class="d-flex flex-column gap-32">
          <input type="hidden" name="token" value="<?= isset($token) ? htmlspecialchars($token) : '' ?>">
          <div class="d-flex flex-column gap-16">
            <div>
              <label for="new-password" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                Nouveau mot de passe
                <span class="text-danger-600">*</span>
              </label>
              <div class="position-relative">
                <input type="password" id="new-password" name="password" class="form-control" placeholder="Entrez votre nouveau mot de passe" required>
                <button type="button"
                  class="toggle-password btn p-0 border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light cursor-pointer ri-eye-line"
                  data-toggle="#new-password" aria-label="Afficher/Masquer">
                </button>
              </div>
            </div>
            <div>
              <label for="confirm-password" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                Confirmer le mot de passe
                <span class="text-danger-600">*</span>
              </label>
              <div class="position-relative">
                <input type="password" id="confirm-password" class="form-control" placeholder="Confirmez votre mot de passe" required>
                <button type="button"
                  class="toggle-password btn p-0 border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light cursor-pointer ri-eye-line"
                  data-toggle="#confirm-password" aria-label="Afficher/Masquer">
                </button>
              </div>
            </div>
          </div>
          <div>
            <button type="submit" class="btn btn-primary-600 text-sm btn-sm px-12 py-16 w-100 radius-8">Réinitialiser</button>
          </div>
        </form>
        <div class="mt-32 text-center text-sm">
          <a href="<?= base_url('Admin') ?>" class="text-primary-600 fw-semibold text-decoration-underline">
            <i class="ri-arrow-left-line"></i> Retour à la connexion
          </a>
        </div>
      </div>
    </div>
  </div>
  <script src="<?= base_url('assets/js/lib/jquery-3.7.1.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/lib/bootstrap.bundle.min.js') ?>"></script>
  <script>
    $(document).ready(function() {
      setTimeout(function() { $('#message').fadeOut('slow'); }, 3000);
      $('.toggle-password').on('click', function() {
        const target = $($(this).data('toggle'));
        const icon = $(this);
        if (target.attr('type') === 'password') {
          target.attr('type', 'text');
          icon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
        } else {
          target.attr('type', 'password');
          icon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
        }
      });
    });
  </script>
</body>
</html>
