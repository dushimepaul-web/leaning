<?php include VIEWPATH . 'includes/Header.php'; ?>
<?php include VIEWPATH . 'includes/Sidebar.php'; ?>

<div class="page-wrapper">
    <div class="page-content">

        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Admin</div>
            <div class="ps-3">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="#"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Join Us</li>
                </ol>
            </div>
            <div class="ms-auto">
                <a class="btn btn-outline-primary" href="#" data-bs-toggle="modal" data-bs-target="#addJoinUs">
                    Nouvelle Section
                </a>
            </div>
        </div>

        <hr/>

        <div class="card">
            <div class="card-body">

                <div class="table-responsive">
                    <table id="example"class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $i = 1; foreach ($joinus as $value): ?>
                                <tr>
                                    <td><?= e($i++)?></td>
                                    <td><?= e($value['titre'])?></td>
                                    <td>
                                       <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#detail_<?=e($value['id'])?>">View detail</a>
                                     </td>
                                    

                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
                                                Options
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#update_<?= e($value['id'])?>">Modifier</a>
                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?= e($value['id'])?>">Supprimer</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal UPDATE -->
                                <div class="modal fade" id="update_<?= e($value['id'])?>" tabindex="-1">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier Join Us</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <form action="<?= base_url('Join_us/update') ?>" method="POST">

                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">

                                                <div class="modal-body">
                                                    <div class="form-floating mb-3">
                                                        <input type="text" class="form-control" name="titre" value="<?= e($value['titre'])?>" required>
                                                        <label>Titre</label>
                                                    </div>

                                                    <div class="form-floating mb-3">
                                                        <textarea class="form-control ckeditor" name="description" style="height:120px;" required><?= e($value['description'])?></textarea>
                                                        <label>Description</label>
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
                        <div class="modal fade" id="detail_<?=e($value['id'])?>">
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



                                <!-- Modal DELETE -->
                                <div class="modal fade" id="delete_<?= e($value['id'])?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmer suppression</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <form action="<?= base_url('Join_us/delete') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">

                                                <div class="modal-body">
                                                    <p>Voulez-vous vraiment supprimer <strong><?= e($value['titre'])?></strong> ?</p>
                                                </div>

                                                <div class="modal-footer">
                                                    <button class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                                                    <button class="btn btn-info" type="submit">Supprimer</button>
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
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </tfoot>

                    </table>
                </div>

            </div>
        </div>

        <!-- Modal ADD -->
        <div class="modal fade" id="addJoinUs" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Nouvelle section Join Us</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="<?= base_url('Join_us/create') ?>" method="POST">

                        <div class="modal-body">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="titre" required>
                                <label>Titre</label>
                            </div>

                            <div class="form-floating mb-3">
                                <textarea class="form-control ckeditor" name="description" style="height:120px;" required></textarea>
                                <label>Description</label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
                            <button class="btn btn-info" type="submit">Enregistrer</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
</div>


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