<?php include VIEWPATH.'media/Header.php' ;?>
<?php include VIEWPATH.'media/navbar.php' ;?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Gestion des Contenus Institutionnels</h2>
                <a href="<?= base_url('Pages/About_us/add') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajouter un Contenu
                </a>
            </div>

            <!-- Filter by Type -->
            <div class="mb-4">
                <div class="btn-group" role="group">
                    <?php foreach($types as $type_key => $type_label) { ?>
                    <a href="<?= base_url('Pages/About_us/manage?type='.$type_key) ?>"
                       class="btn btn-outline-primary <?= ($type == $type_key) ? 'active' : '' ?>">
                        <?= $type_label ?>
                    </a>
                    <?php } ?>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if($this->session->flashdata('success')) { ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $this->session->flashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php } ?>
            <?php if($this->session->flashdata('error')) { ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $this->session->flashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php } ?>

            <!-- Contents Table -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th>Créé le</th>
                            <th>Modifié le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($contents)) { ?>
                            <?php foreach($contents as $content) { ?>
                            <tr>
                                <td><?= e($content['Title']) ?></td>
                                <td>
                                    <span class="badge bg-info"><?= isset($types[$content['Type']]) ? $types[$content['Type']] : $content['Type'] ?></span>
                                </td>
                                <td>
                                    <span class="badge <?= ($content['Status'] == 'Active') ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $content['Status'] ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($content['CreatedAt'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($content['UpdatedAt'])) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('Pages/About_us/edit/'.$content['IdContent']) ?>"
                                           class="btn btn-warning" title="Éditer">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('Pages/About_us/toggle_status/'.$content['IdContent']) ?>"
                                           class="btn btn-info" title="Changer le statut">
                                            <i class="fas fa-toggle-on"></i>
                                        </a>
                                        <a href="<?= base_url('Pages/About_us/delete/'.$content['IdContent']) ?>"
                                           class="btn btn-danger" title="Supprimer"
                                           onclick="return confirm('Êtes-vous sûr?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Aucun contenu trouvé pour ce type
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.table th {
    font-weight: 600;
    vertical-align: middle;
}

.table tbody tr {
    vertical-align: middle;
}
</style>

<?php include VIEWPATH.'media/Footer.php' ;?>
