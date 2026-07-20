<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Erreur PHP</title>
<link href="<?= base_url('assets/css/lib/bootstrap.min.css') ?>" rel="stylesheet">
<link href="<?= base_url('assets/css/remixicon.css') ?>" rel="stylesheet">
<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<div class="min-vh-100 d-flex align-items-center justify-content-center">
  <div class="text-center">
    <h1 class="display-1 fw-bold text-danger">Erreur</h1>
    <h4 class="mb-3">Une erreur PHP s'est produite</h4>
    <p class="text-secondary-light mb-4">Veuillez contacter l'administrateur du site.</p>
    <?php if (isset($message) && $message): ?>
    <div class="alert alert-danger text-start mx-auto mt-3" style="max-width:600px;font-size:13px;">
      <strong>Message :</strong> <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?><br>
      <strong>Fichier :</strong> <?= htmlspecialchars($filepath ?? '', ENT_QUOTES, 'UTF-8') ?><br>
      <strong>Ligne :</strong> <?= htmlspecialchars($line ?? '', ENT_QUOTES, 'UTF-8') ?><br>
      <strong>Type :</strong> <?= htmlspecialchars($severity ?? '', ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>
    <a href="<?= base_url('Admin') ?>" class="btn btn-primary">
      <i class="ri-home-4-line me-1"></i> Retour à l'accueil
    </a>
  </div>
</div>
</body>
</html>
