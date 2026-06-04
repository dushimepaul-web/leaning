<?php include VIEWPATH . 'includes/Header.php'; ?>
<?php include VIEWPATH . 'includes/Sidebar.php'; ?>

<div class="page-wrapper">
    <div class="page-content">

        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Admin</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="javascript:;">
                                <i class="bx bx-home-alt"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Newsletter</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addEmail">
                    Ajouter un Email
                </a>
            </div>
        </div>
        <hr/>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Email</th>
                                <th>Date d'inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($emails as $email): ?>
                                <tr>
                                    <td><?= e($i++)?></td>
                                    <td><?= e($email['email'])?></td>
                                    <td><?= e($email['date_inscription'])?></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete_<?= e($email['id_newsletter'])?>">Supprimer</button>
                                    </td>
                                </tr>

                                <!-- Modal Supprimer -->
                                <div class="modal fade" id="delete_<?= e($email['id_newsletter'])?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmer la suppression</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('Newsletter/Delete') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($email['uuid'])?>">
                                                <div class="modal-body">
                                                    <p>Voulez-vous vraiment supprimer l'email "<?= e($email['email'])?>" ?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Email</th>
                                <th>Date d'inscription</th>
                                <th>Actions</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter Email -->
        <div class="modal fade" id="addEmail" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?= base_url('Newsletter/Create') ?>" method="POST">
                        <div class="modal-body">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
                                <label for="email">Email</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-info">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include VIEWPATH . 'includes/Footer.php'; ?>