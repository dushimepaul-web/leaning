<?php include VIEWPATH . 'includes/Header.php'; ?>
<?php include VIEWPATH . 'includes/Sidebar.php'; ?>

<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
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
                        <li class="breadcrumb-item active" aria-current="page">Modes de Paiement</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addModePayement">
                    Nouveau Mode
                </a>
            </div>
        </div>
        <!--end breadcrumb-->
        <hr/>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($mode_payements as $value): ?>
                                <tr>
                                    <td><?= e($i++)?></td>
                                    <td><?= e($value['description'])?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
                                                Options
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#update_<?= e($value['id_mode_payement'])?>">
                                                    Modifier
                                                </a>
                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?= e($value['id_mode_payement'])?>">
                                                    Supprimer
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Modifier -->
                                <div class="modal fade" id="update_<?= e($value['id_mode_payement'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier le mode de paiement</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('Mode_payement/UpdateModePayement') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-body">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="description" id="description_<?= e($value['uuid'])?>" value="<?= e($value['description'])?>" required>
                                                        <label for="description_<?= e($value['id_mode_payement'])?>">Description</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
                                                    <button type="submit" class="btn btn-info">Modifier</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Supprimer -->
                                <div class="modal fade" id="delete_<?= e($value['id_mode_payement'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmer la suppression</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('Mode_payement/DeleteModePayement') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-body">
                                                    <p>Voulez-vous vraiment supprimer le mode de paiement "<?= e($value['description'])?>" ?</p>
                                                    <div class="alert alert-warning mt-2">
                                                        <small>
                                                            <i class="bx bx-info-circle"></i>
                                                            Attention : Ce mode de paiement pourrait être utilisé dans des inscriptions existantes.
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-info">Supprimer</button>
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
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter Mode de Paiement -->
        <div class="modal fade" id="addModePayement" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nouveau mode de paiement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?= base_url('Mode_payement/CreateModePayement') ?>" method="POST">
                        <div class="modal-body">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="description" id="description" placeholder="Ex: Carte bancaire, Espèces, Virement..." required>
                                <label for="description">Description</label>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    Exemples : Carte bancaire, Espèces, Virement bancaire, Chèque, PayPal, etc.
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-info">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<!--end page wrapper -->

<?php include VIEWPATH . 'includes/Footer.php'; ?>