<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Erreur base de données</title>
<link href="<?= base_url('assets/css/lib/bootstrap.min.css') ?>" rel="stylesheet">
<link href="<?= base_url('assets/css/remixicon.css') ?>" rel="stylesheet">
<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<div class="min-vh-100 d-flex align-items-center justify-content-center">
  <div class="text-center">
    <h1 class="display-1 fw-bold text-warning">DB</h1>
    <h4 class="mb-3">Erreur de connexion à la base de données</h4>
    <p class="text-secondary-light mb-4">Impossible de se connecter à la base de données. Veuillez réessayer plus tard.</p>
    <a href="<?= base_url('Admin') ?>" class="btn btn-primary">
      <i class="ri-home-4-line me-1"></i> Retour à l'accueil
    </a>
  </div>
</div>
</body>
</html>
