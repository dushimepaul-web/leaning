<!doctype html>
<html lang="fr" data-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion - <?= $this->Model->get_setting('nom_ecole', 'VIP School') ?></title>
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
      <img src="<?= base_url('assets/images/thumbs/login-img.png') ?>" alt="Login Image" class="w-100 h-100 object-fit-cover">
    </div>
    <div class="lg-w-50 px-24 py-32 d-flex justify-content-center align-items-center">
      <div class="max-w-540-px mx-auto w-100">
        <a href="<?= base_url() ?>">
          <?php $logo = $this->Model->get_setting('logo_ecole', 'assets/images/logo.png'); ?>
          <img src="<?= base_url($logo) ?>" alt="Logo">
        </a>
        <div class="mt-32 mb-32">
          <h1 class="h6 fw-bold text-primary-light mb-8">Bienvenue !</h1>
          <p class="text-sm text-secondary-light mb-0">Connectez-vous a votre compte pour continuer</p>
        </div>
        <?php if (!empty($this->session->flashdata('sms'))) echo $this->session->flashdata('sms'); ?>
        <form action="<?= base_url('Admin/do_login') ?>" method="POST" class="d-flex flex-column gap-32 submit-form">
          <div class="d-flex flex-column gap-16">
            <div>
              <label for="email" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                Adresse Email
                <span class="text-danger-600">*</span>
              </label>
              <input type="email" id="email" name="email" class="email-field form-control" placeholder="Entrez votre email" required>
            </div>
            <div>
              <label for="password" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
                Mot de passe
                <span class="text-danger-600">*</span>
              </label>
              <div class="position-relative">
                <input type="password" id="password" name="password" class="password-field form-control" placeholder="Entrez votre mot de passe" required>
                <button type="button"
                  class="toggle-password btn p-0 border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light cursor-pointer ri-eye-line"
                  data-toggle="#password" aria-label="Afficher/Masquer le mot de passe">
                </button>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between gap-2">
            <div class="form-check style-check d-flex align-items-center">
              <input class="form-check-input border border-neutral-400" type="checkbox" id="remember">
              <label class="form-check-label" for="remember">Se souvenir de moi</label>
            </div>
            <a href="<?= base_url('Admin/forgot_password') ?>" class="text-primary-600 fw-medium text-decoration-underline">Mot de passe oublie ?</a>
          </div>
          <div>
            <button type="submit" class="btn btn-primary-600 text-sm btn-sm px-12 py-16 w-100 radius-8">Se connecter</button>
          </div>
          <div class="text-center text-sm text-secondary-light">ou connectez-vous en tant que</div>

          


        </form>
        <div class="mt-32 text-center text-sm">
          Vous n'avez pas de compte ?
          <a href="<?= base_url('Admin/register') ?>" class="text-primary-600 fw-semibold text-decoration-underline">Creer un compte</a>
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
