<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10"><?= $title ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php if (empty($enfants)): ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ri-user-search-line" style="font-size: 48px; color: #ccc;"></i>
                    <p class="mt-2 text-muted">Aucun enfant trouvé</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($enfants as $e): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <?php if (!empty($e['photo'])): ?>
                                    <img src="<?= base_url($e['photo']) ?>" alt="" class="rounded-circle" width="60" height="60" style="object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px;">
                                        <?= strtoupper(substr($e['prenom'] ?? $e['nom'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1"><?= htmlspecialchars(trim($e['nom'] . ' ' . ($e['postnom'] ?? '') . ' ' . ($e['prenom'] ?? ''))) ?></h6>
                                <p class="mb-0 text-muted small"><?= htmlspecialchars($e['matricule'] ?? '') ?></p>
                                <p class="mb-0 text-muted small"><?= htmlspecialchars($e['classe_libelle'] ?? '') ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-around">
                            <a href="<?= base_url('Bulletins/etudiant/' . $e['uuid']) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="ri-file-list-3-line"></i> Bulletins
                            </a>
                            <a href="<?= base_url('Paiements/etudiant/' . $e['uuid']) ?>" class="btn btn-sm btn-outline-success">
                                <i class="ri-bank-card-line"></i> Paiements
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
