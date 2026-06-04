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
                        <li class="breadcrumb-item active" aria-current="page">Cours</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addCourse">
                    Nouveau Cours
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
                                <th>Nom du Cours</th>
                                <th>Catégorie</th>
                                <th>Enseignant</th>
                                <th>Description</th>
                                <th>Date d'insertion</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($courses as $value): ?>
                                <tr>
                                    <td><?= e($i++)?></td>
                                    <td><?= e($value['nom_course'])?></td>
                                    <td><?= e($value['nom_categories'])?></td>
                                    <td><?= e($value['nom_teacher'] . ' ' . $value['prenom_teacher'])?></td>
                                    <td>
                                <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#afficher_<?=e($value['id_course'])?>">View detail</a>
                            </td>
                                    <td><?= date('d/m/Y H:i', strtotime($value['date_insertion'])); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
                                                Options
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#update_<?= e($value['id_course'])?>">
                                                    Modifier
                                                </a>
                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?= e($value['id_course'])?>">
                                                    Supprimer
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Modifier -->
                                <div class="modal fade" id="update_<?= e($value['id_course'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier le cours</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('Courses/UpdateCourse') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" name="nom_course" value="<?= e($value['nom_course'])?>" required>
                                                                <label>Nom du cours</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-control" name="id_categorie" required>
                                                                    <option value="">Sélectionner une catégorie</option>
                                                                    <?php foreach ($categories as $categorie): ?>
                                                                        <option value="<?= e($categorie['id_categorie'])?>" <?= e($categorie['id_categorie'] == $value['id_categorie'] ? 'selected' : '')?>>
                                                                            <?= e($categorie['nom_categories'])?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <label>Catégorie</label>
                                                            </div>
                                                        </div>
                                                    
                                                        <div class="col-md-4">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-control" name="id_teacher" required>
                                                                    <option value="">Sélectionner un enseignant</option>
                                                                    <?php foreach ($teachers as $teacher): ?>
                                                                        <option value="<?= e($teacher['id_teacher'])?>" <?= e($teacher['id_teacher'] == $value['id_teacher'] ? 'selected' : '')?>>
                                                                            <?= e($teacher['nom'] . ' ' . $teacher['prenom'])?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <label>Enseignant</label>
                                                            </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-floating mb-3">
                                                                <textarea class="form-control ckeditor" name="description" style="height: 100px;" required><?= e($value['description'])?></textarea>
                                                                <label>Description</label>
                                                            </div>
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

                                 <!-- ========= MODAL DETAIL ========= -->
                        <div class="modal fade" id="afficher_<?=e($value['id_course'])?>">
                            <div class="modal-dialog modal-fullscreen">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Détail</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <?=e($value['description'])?>
                                    </div>

                                    <div class="modal-footer">
                                   <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
                                    </div>

                                </div>
                            </div>
                        </div>


                                <!-- Modal Supprimer -->
                                <div class="modal fade" id="delete_<?= e($value['id_course'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmer la suppression</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('Courses/DeleteCourse') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-body">
                                                    <p>Voulez-vous vraiment supprimer le cours "<?= e($value['nom_course'])?>" ?</p>
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
                                <th>Nom du Cours</th>
                                <th>Catégorie</th>
                                <th>Enseignant</th>
                                <th>Description</th>
                                <th>Date d'insertion</th>
                                <th>Actions</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter Cours -->
        <div class="modal fade" id="addCourse" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nouveau cours</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?= base_url('Courses/CreateCourse') ?>" method="POST">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="nom_course" required>
                                        <label>Nom du cours</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <select class="form-control" name="id_categorie" required>
                                            <option value="">Sélectionner une catégorie</option>
                                            <?php foreach ($categories as $categorie): ?>
                                                <option value="<?= e($categorie['id_categorie'])?>"><?= e($categorie['nom_categories'])?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label>Catégorie</label>
                                    </div>
                                
                            </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <select class="form-control" name="id_teacher" required>
                                            <option value="">Sélectionner un enseignant</option>
                                            <?php foreach ($teachers as $teacher): ?>
                                                <option value="<?= e($teacher['id_teacher'])?>"><?= e($teacher['nom'] . ' ' . $teacher['prenom'])?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label>Enseignant</label>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control ckeditor" name="description" style="height: 100px;" required></textarea>
                                        <label>Description</label>
                                    </div>
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

<!-- ========= CKEDITOR + UPLOAD ========= -->
<script src="<?= base_url("assets/cdn/ckeditor/ckeditor.js") ?>"></script>

<script>
$(document).ready(function () {
    $('.ckeditor').each(function () {
        CKEDITOR.replace($(this).attr('name'), {
            filebrowserUploadUrl: "<?php echo base_url('Carousel/uploadImage'); ?>",
            filebrowserUploadMethod: "form"
        });
    });
});
</script>

<?php include VIEWPATH.'includes/Footer.php'; ?>