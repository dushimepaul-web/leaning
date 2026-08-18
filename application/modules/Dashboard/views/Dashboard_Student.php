<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>

<div class="dashboard-main-body">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h4 class="fw-bold mb-0">Espace Élève / Parent</h4>
      <p class="text-secondary-light text-sm mb-0">Suivi académique et résultats récents.</p>
    </div>
  </div>

  <div class="row gy-4">
    <div class="col-xl-12">
      <div class="card border-0 shadow-sm radius-8">
        <div class="card-body p-24">
          <h6 class="fw-bold mb-16"><i class="ri-file-list-3-line me-1"></i> Dernier Bulletin Disponible</h6>
          <?php if(!empty($dernier_bulletin)): ?>
            <div class="row">
              <div class="col-md-3"><strong>Période :</strong> <?= htmlspecialchars($dernier_bulletin['periode']) ?></div>
              <div class="col-md-3"><strong>Moyenne :</strong> <span class="badge bg-success"><?= $dernier_bulletin['moyenne'] ?> / 20</span></div>
              <div class="col-md-3"><strong>Rang :</strong> <?= $dernier_bulletin['rang'] ?? '-' ?></div>
              <div class="col-md-3"><strong>Décision :</strong> <?= ucfirst($dernier_bulletin['decision'] ?? '-') ?></div>
            </div>
            <div class="mt-16">
              <a href="<?= base_url('Notes/bulletins') ?>" class="btn btn-sm btn-primary">Voir tous les bulletins</a>
            </div>
          <?php else: ?>
            <p class="text-muted mb-0">Aucun bulletin publié pour le moment.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include VIEWPATH.'includes/Footer.php'; ?>
