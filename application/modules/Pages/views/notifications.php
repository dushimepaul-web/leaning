<?php include VIEWPATH . 'media/Header.php'; ?>
<?php include VIEWPATH . 'media/navbar.php'; ?>

<?php   
$flash_error = $this->session->flashdata('error') ?? '';
$flash_success = $this->session->flashdata('success') ?? '';
$has_message = !empty($flash_error) || !empty($flash_success);
?>

<?php if($has_message): ?>
<!-- Modale de feedback -->
<div class="modal fade" id="feedbackModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header <?= !empty($flash_error) ? 'bg-danger' : 'bg-success' ?> text-white">
                <h5 class="modal-title" id="feedbackModalLabel">
                    <?= !empty($flash_error) ? 'Erreur' : 'Succès' ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if(!empty($flash_error)): ?>
                    <p><?= e($flash_error)?></p>
                <?php else: ?>
                    <p><?= e($flash_success)?></p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="<?= base_url('Pages/Home/') ?>" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Retour Home
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if($has_message): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
    feedbackModal.show();
});
</script>
<?php endif; ?>

<?php include VIEWPATH . 'media/Footer.php'; ?>