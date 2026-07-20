<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exception non capturée</title>
<link href="<?= base_url('assets/css/lib/bootstrap.min.css') ?>" rel="stylesheet">
<link href="<?= base_url('assets/css/remixicon.css') ?>" rel="stylesheet">
<link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<div class="min-vh-100 d-flex align-items-center justify-content-center">
  <div class="text-center">
    <h1 class="display-1 fw-bold text-danger">Exception</h1>
    <h4 class="mb-3">Une exception non capturée s'est produite</h4>
    <div class="alert alert-danger text-start mx-auto mt-3" style="max-width:600px;font-size:13px;">
      <strong>Type :</strong> <?= htmlspecialchars(get_class($exception), ENT_QUOTES, 'UTF-8') ?><br>
      <strong>Message :</strong> <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?><br>
      <strong>Fichier :</strong> <?= htmlspecialchars($exception->getFile(), ENT_QUOTES, 'UTF-8') ?><br>
      <strong>Ligne :</strong> <?= htmlspecialchars($exception->getLine(), ENT_QUOTES, 'UTF-8') ?><br>
      <strong>Code :</strong> <?= htmlspecialchars($exception->getCode(), ENT_QUOTES, 'UTF-8') ?>
    </div>
    <a href="<?= base_url('Admin') ?>" class="btn btn-primary">
      <i class="ri-home-4-line me-1"></i> Retour à l'accueil
    </a>
  </div>
</div>
</body>
</html>
