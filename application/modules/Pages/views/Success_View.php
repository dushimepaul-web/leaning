<?php include VIEWPATH . 'media/Header.php'; ?>
<?php include VIEWPATH . 'media/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                
                <!-- Header -->
                <div class="bg-success text-white text-center py-4">
                    <div class="display-3 mb-2">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <h3 class="fw-bold">Inscription confirmée !</h3>
                    <p class="mb-0 opacity-75">Votre demande a été enregistrée avec succès</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    
                    <!-- Message de félicitations -->
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-dark">Félicitations, <?= htmlspecialchars($fullname); ?> !</h4>
                        <p class="text-muted">
                            Vous êtes sur le point de rejoindre la formation :
                        </p>
                        <span class="badge bg-primary fs-6 px-4 py-2 rounded-pill">
                            <?= htmlspecialchars($course_name ?? 'Non spécifiée'); ?>
                        </span>
                    </div>

                    <!-- Récapitulatif de l'inscription -->
                    <div class="bg-light rounded-3 p-4 mb-4">
                        <h5 class="fw-bold text-dark mb-3">
                            <i class="bi bi-receipt-cutoff me-2"></i> Récapitulatif
                        </h5>
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted ps-0">Formation</td>
                                <td class="fw-semibold text-end"><?= htmlspecialchars($course_name ?? 'Non spécifiée') ?></td>
                            </tr>
                            <?php if (!empty($date_debut)): ?>
                            <tr>
                                <td class="text-muted ps-0 border-top">Période</td>
                                <td class="fw-semibold text-end border-top">
                                    du <?= date('d/m/Y', strtotime($date_debut)) ?> 
                                    au <?= date('d/m/Y', strtotime($date_defin)) ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($attendance)): ?>
                            <tr>
                                <td class="text-muted ps-0 border-top">Mode</td>
                                <td class="fw-semibold text-end border-top"><?= htmlspecialchars($attendance) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($payement)): ?>
                            <tr>
                                <td class="text-muted ps-0 border-top">Paiement</td>
                                <td class="fw-semibold text-end border-top"><?= htmlspecialchars($payement) ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>

                    <!-- Prochaines étapes -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-3">
                            <i class="bi bi-list-check me-2"></i> Prochaines étapes
                        </h5>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="card border-0 bg-light text-center p-3 h-100">
                                    <div class="fs-2 text-success">📧</div>
                                    <small class="fw-bold">1. Vérifiez vos emails</small>
                                    <small class="text-muted">Email envoyé à <span class="fw-semibold"><?= htmlspecialchars($email) ?></span></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-light text-center p-3 h-100">
                                    <div class="fs-2 text-warning">🔗</div>
                                    <small class="fw-bold">2. Cliquez sur le lien</small>
                                    <small class="text-muted">Confirmez votre adresse email</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-light text-center p-3 h-100">
                                    <div class="fs-2 text-info">✅</div>
                                    <small class="fw-bold">3. Inscription validée</small>
                                    <small class="text-muted">Place réservée automatiquement</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alerte vérification email -->
                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-start p-4" role="alert">
                        <div class="fs-1 me-3">
                            <i class="bi bi-envelope-exclamation-fill"></i>
                        </div>
                        <div>
                            <h6 class="alert-heading fw-bold mb-2">
                                <i class="bi bi-info-circle"></i> Action requise
                            </h6>
                            <p class="mb-1 small">
                                Un email de confirmation a été envoyé à 
                                <span class="fw-bold text-decoration-underline"><?= htmlspecialchars($email); ?></span>.
                            </p>
                            <p class="mb-0 small fw-semibold">
                                Cliquez sur le lien dans cet email pour valider définitivement votre inscription.
                            </p>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
                        <a href="https://mail.google.com" target="_blank" class="btn btn-primary btn-lg rounded-pill shadow px-4">
                            <i class="bi bi-mailbox me-2"></i> Accéder à ma messagerie
                        </a>
                        <a href="<?= base_url('Pages/Home') ?>" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
                            <i class="bi bi-house-door me-2"></i> Retour à l'accueil
                        </a>
                    </div>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="bi bi-exclamation-triangle"></i> 
                            Vous n'avez rien reçu ? Vérifiez vos <span class="fw-bold text-danger">courriers indésirables (Spams)</span>.
                        </small>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .bg-success { background: linear-gradient(135deg, #198754, #157347) !important; }
    .card { border: none; }
</style>

<?php include VIEWPATH . 'media/Footer.php'; ?>
