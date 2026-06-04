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
                        <li class="breadcrumb-item active" aria-current="page">Catégories</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addCategorie">
                    Nouvelle Catégorie
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
                                <th>Affiche</th>
                                <th>Nom de la Catégorie</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($categories as $value): ?>
                                <tr>
                                    <td><?= e($i++)?></td>
                                    <td>
                                        <?php if (!empty($value['Image'])): ?>
                                            <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#Image_<?=e($value['id_categorie'])?>">
                                                <img src="<?=base_url('attachments/Categorie/'.$value['Image'])?>" style="width:50px; height:50px; border-radius:50%;">
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Pas d'image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= e($value['nom_categories'])?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
                                                Options
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#update_<?= e($value['id_categorie'])?>">
                                                    Modifier
                                                </a>
                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?= e($value['id_categorie'])?>">
                                                    Supprimer
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Modifier -->
                                <div class="modal fade" id="update_<?= e($value['id_categorie'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier la catégorie</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('Categories/Update') ?>" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-body">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control" name="nom_categories" id="nom_categories_<?= e($value['id_categorie'])?>" value="<?= e($value['nom_categories'])?>" required>
                                                        <label for="nom_categories_<?= e($value['id_categorie'])?>">Nom de la catégorie</label>
                                                    </div>
                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Image</label>
                                                        <input type="hidden" name="HiddenImage" value="<?=e($value['Image'])?>">
                                                        <input type="file" class="form-control" name="Image">
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
                                <div class="modal fade" id="delete_<?= e($value['id_categorie'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmer la suppression</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('Categories/Delete') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-body">
                                                    <p>Voulez-vous vraiment supprimer la catégorie "<?= e($value['nom_categories'])?>" ?</p>
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
                                <th>Affiche</th>
                                <th>Nom de la Catégorie</th>
                                <th>Actions</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter Catégorie -->
        <div class="modal fade" id="addCategorie" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nouvelle catégorie</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?= base_url('Categories/Create') ?>" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="nom_categories" id="nom_categories" required>
                                <label for="nom_categories">Nom de la catégorie</label>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Image</label>
                                <input type="file" class="form-control" name="Image" required>
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