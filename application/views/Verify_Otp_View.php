<!doctype html>
<html lang="fr" data-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vérification OTP - <?= $this->Model->get_setting('nom_ecole', 'VIP School') ?></title>
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
      <?php $login_img = $this->Model->get_setting('login_img', 'assets/images/thumbs/login-img.png'); ?>
      <img src="<?= base_url($login_img) ?>" alt="Vérification" class="w-100 h-100 object-fit-cover" onerror="this.style.display='none';this.parentElement.style.background='linear-gradient(135deg,#25A194 0%,#1C7F73 100%)'">
    </div>
    <div class="lg-w-50 px-24 py-32 d-flex justify-content-center align-items-center">
      <div class="max-w-540-px mx-auto w-100">
        <a href="<?= base_url() ?>">
          <?php $logo = $this->Model->get_setting('logo_ecole', 'assets/images/logo.png'); ?>
          <img src="<?= base_url($logo) ?>" alt="Logo">
        </a>
        <div class="mt-32 mb-32">
          <h1 class="h6 fw-bold text-primary-light mb-8">Code de vérification</h1>
          <p class="text-sm text-secondary-light mb-0">Entrez le code à 6 chiffres envoyé à votre adresse email</p>
        </div>
        <?php if (!empty($this->session->flashdata('sms'))) echo $this->session->flashdata('sms'); ?>
        <form action="<?= base_url('Admin/do_verify_otp') ?>" method="POST" class="d-flex flex-column gap-32">
          <input type="hidden" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
          <div>
            <label for="otp" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
              Code OTP
              <span class="text-danger-600">*</span>
            </label>
            <input type="text" id="otp" name="code" class="form-control text-center" placeholder="Entrez le code à 6 chiffres" maxlength="6" required style="font-size:1.5rem;letter-spacing:8px;">
          </div>
          <div>
            <button type="submit" class="btn btn-primary-600 text-sm btn-sm px-12 py-16 w-100 radius-8">Vérifier</button>
          </div>
        </form>
        <div class="mt-32 text-center text-sm">
          <a href="<?= base_url('Admin/forgot_password') ?>" class="text-primary-600 fw-semibold text-decoration-underline">
            <i class="ri-arrow-left-line"></i> Retour
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
    });
  </script>
</body>
</html>
