<?php include VIEWPATH . 'media/Header.php'; ?>
<?php include VIEWPATH . 'media/navbar.php'; ?>

<div class="modal fade" id="successModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="inscriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title d-flex align-items-center" id="inscriptionModalLabel">
                    <i class="bi bi-patch-check-fill me-2 fs-4"></i> Inscription confirmée !
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer" onclick="window.location='<?= base_url('Pages/Home'); ?>'"></button>
            </div>

            <div class="modal-body p-0">
                <div class="card border-0">
                    <div class="card-body p-4 p-md-5">
                        
                        <div class="text-center mb-4">
                            <div class="display-4 text-success mb-2">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <h3 class="fw-bold text-dark">Félicitations, <?= htmlspecialchars($fullname); ?> !</h3>
                            <p class="text-muted fs-5">
                                Votre demande pour la formation <span class="text-primary fw-semibold">"<?= htmlspecialchars($course_name ?? 'Non spécifiée'); ?>"</span> a été bien enregistrée.
                            </p>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm d-flex align-items-start p-4" role="alert">
                            <div class="fs-1 me-3">
                                <i class="bi bi-envelope-exclamation-fill"></i>
                            </div>
                            <div>
                                <h4 class="alert-heading h6 fw-bold mb-2">Action requise : Vérifiez votre boîte mail</h4>
                                <p class="mb-2 small text-dark opacity-75">
                                    Un email de confirmation vient d'être envoyé à : <span class="fw-bold text-decoration-underline"><?= htmlspecialchars($email); ?></span>.
                                </p>
                                <p class="mb-0 fw-semibold small">
                                    <i class="bi bi-info-circle"></i> Vous devez cliquer sur le lien dans cet email pour valider définitivement votre inscription.
                                </p>
                            </div>
                        </div>


                        <div class="text-center mt-5">
                            <a href="https://mail.google.com" target="_blank" class="btn btn-primary btn-lg px-5 rounded-pill shadow">
                                <i class="bi bi-mailbox me-2"></i> Accéder à ma messagerie
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light border-0 justify-content-center py-3">
                <p class="text-muted small mb-0">
                    Vous n'avez rien reçu ? Vérifiez vos <span class="fw-bold text-danger">courriers indésirables (Spams)</span>.
                </p>
            </div>
            <div class="p-3 text-center border-top">
                <a href="<?= base_url('Pages/Home') ?>" class="btn btn-outline-secondary btn-sm border-0">
                   <i class="bi bi-house-door"></i> Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation de la modale Bootstrap 5
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    });
</script>

<style>
    .modal-content { border-radius: 15px !important; }
    .line-height-lg { line-height: 1.8; }
    .btn-primary { background-color: #0d6efd; border: none; }
    .alert-info { background-color: #e7f1ff; color: #084298; }
</style>

<?php include VIEWPATH . 'media/Footer.php'; ?>