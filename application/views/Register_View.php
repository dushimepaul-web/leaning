<!doctype html>
<html lang="fr" data-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inscription - <?= $this->Model->get_setting('nom_ecole', 'VIP School') ?></title>
  <?php $fav = $this->Model->get_setting('favicon_ecole', 'assets/images/favicon.png'); ?>
  <link rel="icon" type="image/png" href="<?= base_url($fav) ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/remixicon.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/lib/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
  <div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300"></div>
  <div class="d-lg-flex bg-white" style="min-height:100vh;">
    <div class="w-50 d-lg-block d-none overflow-hidden d-flex">
      <img src="<?= base_url('assets/images/thumbs/login-img.png') ?>" alt="Inscription" class="w-100 h-100 object-fit-cover">
    </div>
    <div class="lg-w-50 px-24 py-40 d-flex justify-content-center align-items-center">
      <div class="max-w-540-px mx-auto w-100">
        <a href="<?= base_url() ?>">
          <?php $logo = $this->Model->get_setting('logo_ecole', 'assets/images/logo.png'); ?>
          <img src="<?= base_url($logo) ?>" alt="Logo">
        </a>
        <div class="mt-48 mb-32">
          <h1 class="h6 fw-bold text-primary-light">Creer votre compte</h1>
          <p class="text-sm text-secondary-light">Remplissez les informations pour commencer</p>
        </div>
        <?php if (!empty($this->session->flashdata('sms'))) echo $this->session->flashdata('sms'); ?>
        <form action="<?= base_url('Admin/do_register') ?>" method="POST" class="d-flex flex-column gap-24">
          <div>
            <label class="text-sm fw-semibold text-primary-light mb-8">
              Nom complet <span class="text-danger-600">*</span>
            </label>
            <input type="text" name="nom_complet" class="form-control" placeholder="Entrez votre nom complet" required>
          </div>
          <div>
            <label class="text-sm fw-semibold text-primary-light mb-8">
              Adresse Email <span class="text-danger-600">*</span>
            </label>
            <input type="email" name="email" class="form-control" placeholder="Entrez votre email" required>
          </div>
          <div>
            <label for="reg-password" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
              Mot de passe
              <span class="text-danger-600">*</span>
            </label>
            <div class="position-relative">
              <input type="password" id="reg-password" name="password" class="form-control" placeholder="Entrez votre mot de passe" required>
              <button type="button"
                class="toggle-password btn p-0 border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light cursor-pointer ri-eye-line"
                data-toggle="#reg-password" aria-label="Afficher/Masquer le mot de passe">
              </button>
            </div>
          </div>
          <div>
            <label for="reg-confirm" class="text-sm fw-semibold text-primary-light d-inline-block mb-8">
              Confirmer le mot de passe
              <span class="text-danger-600">*</span>
            </label>
            <div class="position-relative">
              <input type="password" id="reg-confirm" name="confirm_password" class="form-control" placeholder="Confirmez votre mot de passe" required>
              <button type="button"
                class="toggle-password btn p-0 border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light cursor-pointer ri-eye-line"
                data-toggle="#reg-confirm" aria-label="Afficher/Masquer le mot de passe">
              </button>
            </div>
          </div>
          <div>
            <label class="text-sm fw-semibold text-primary-light mb-8">Selectionner un role</label>
            <select name="role" class="form-select" required>
              <option value="">Choisissez votre role</option>
              <option value="eleve">Eleve</option>
              <option value="enseignant">Enseignant</option>
              <option value="parent">Parent</option>
            </select>
          </div>
          <div class="form-check style-check d-flex align-items-center">
            <input class="form-check-input border border-neutral-400" type="checkbox" id="terms" required>
            <label class="form-check-label" for="terms">J'accepte les <a href="#" class="text-primary-600 text-decoration-underline">Conditions d'utilisation</a></label>
          </div>
          <button type="submit" class="btn btn-primary-600 w-100 py-16 radius-8 text-sm fw-semibold">Creer un compte</button>
        </form>
        <div class="mt-24 text-center text-sm">
          Vous avez deja un compte ?
          <a href="<?= base_url('Admin') ?>" class="text-primary-600 fw-semibold text-decoration-underline">Se connecter</a>
        </div>
      </div>
    </div>
  </div>
  <script src="<?= base_url('assets/js/lib/jquery-3.7.1.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/lib/bootstrap.bundle.min.js') ?>"></script>
  <script>
    $(document).ready(function() {
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
