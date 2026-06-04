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
                <a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addModal">
                    Nouvelle evenements
                </a>
            </div>
        </div>
        <!--end breadcrumb-->
        <hr/>



<div class="card">
<div class="card-body table-responsive">

<table id="example" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Titre</th>
            <th>Dates</th>
            <th>Lieu</th>
            <th>MOIS</th>
            <th>ANNE</th>
            <th>description</th>
            <th>STATUS</th>
            <th>En ligne ?</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>

    <?php $i = 1; foreach ($events as $ev): ?>
        <tr>
            <td><?= e($i++)?></td>

            <td>
                <?php if ($ev['image']): ?>
                    <img src="<?= base_url('attachments/events/'.$ev['image']) ?>" style="width:50px; height:50px; border-radius:50%;">
                <?php endif; ?>
            </td>

            <td><?=substr($ev['titre'], 0,20)?>...</td>

            <td>
                <?= e($ev['date_debut'])?><b> - </b> <?= e($ev['date_fin'])?>
            </td>

            <td><?= e($ev['lieu'])?></td>
            <td><?= e($ev['mois'])?></td>
            <td><?= e($ev['annee'])?></td>
            <td>
                <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#detail_<?=e($ev['id'])?>">View detail</a>
                            </td>
            <td>
               <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#Status_<?=e($ev['id'])?>">
                <?=e($ev['IsActive']==1?'Activated':'Disactivated')?>
                                </a>
                            </td>
            <td>
                <?= e($ev['est_en_ligne'] ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-danger">Non</span>')?>
            </td>
             
             <td>
               <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Options</button>
                <div class="dropdown-menu">
                     <a class="dropdown-item text-info" href="javascript:void()" data-bs-toggle="modal" data-bs-target="#update_<?=e($ev['id'])?>">Modifier</a>
                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?=e($ev['id'])?>">Supprimer</a>
                 </div>
             </td>
        </tr>


         <!-- ========= MODAL DETAIL ========= -->
                        <div class="modal fade" id="detail_<?=e($ev['id'])?>">
                            <div class="modal-dialog modal-fullscreen">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Détail</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <?=e($ev['description'])?>
                                    </div>

                                    <div class="modal-footer">
                                   <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
                                    </div>

                                </div>
                            </div>
                        </div>


        <!-- UPDATE MODAL -->
        <div class="modal fade" id="update_<?= e($ev['id'])?>">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form method="POST" action="<?= base_url('Events/update') ?>" enctype="multipart/form-data">

                        <input type="hidden" name="uuid" value="<?= e($ev['uuid'])?>">
                        <input type="hidden" name="hidden_image" value="<?= e($ev['image'])?>">

                        <div class="modal-header">
                            <h5>Modifier événement</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <label>Titre</label>
                                    <input type="text" name="titre" class="form-control" value="<?= e($ev['titre'])?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Lieu</label>
                                    <input type="text" name="lieu" class="form-control" value="<?= e($ev['lieu'])?>" required>
                                </div>

                                <div class="col-md-4 mt-3">
                                    <label>Date début</label>
                                    <input type="date" name="date_debut" class="form-control" value="<?= e($ev['date_debut'])?>" required>
                                </div>

                                <div class="col-md-4 mt-3">
                                    <label>Date fin</label>
                                    <input type="date" name="date_fin" class="form-control" value="<?= e($ev['date_fin'])?>" required>
                                </div>
                                <div class="col-md-4 mt-3">
                                    <label>En ligne ?</label><br>
                                    <input type="checkbox" name="est_en_ligne" <?= e($ev['est_en_ligne'] ? 'checked' : '')?>>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control ckeditor" rows="3"><?= e($ev['description'])?></textarea>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label>Image</label>
                                    <input type="file" name="image" class="form-control">
                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                            <button class="btn btn-info">Modifier</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>




        <!-- ========= MODAL STATUS ========= -->
                        <div class="modal fade" id="Status_<?=e($ev['id'])?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Changer le status ?</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <form action="<?=base_url('Events/ChangeStatus')?>" method="POST">
                                        <input type="hidden" name="uuid" value="<?=e($ev['uuid'])?>">
                                        <input type="hidden" name="IsActive" value="<?=e($ev['IsActive'])?>">

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-info">Enregistrer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>



        <!-- DELETE MODAL -->
        <div class="modal fade" id="delete_<?= e($ev['id'])?>">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="<?= base_url('Events/delete'); ?>">

                        <input type="hidden" name="uuid" value="<?= e($ev['uuid'])?>">

                        <div class="modal-header">
                            <h5>Supprimer événement</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            Voulez-vous supprimer l’événement :
                            <b><?= e($ev['titre'])?></b> ?
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button class="btn btn-danger">Supprimer</button>
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
            <th>Image</th>
            <th>Titre</th>
            <th>Dates</th>
            <th>Lieu</th>
            <th>MOIS</th>
            <th>ANNE</th>
            <th>description</th>
            <th>STATUS</th>
            <th>En ligne ?</th>
            <th>Actions</th>
        </tr>
    </tfoot>
</table>

</div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal">
<div class="modal-dialog modal-xl">
    <div class="modal-content">

        <form method="POST" action="<?= base_url('Events/create') ?>" enctype="multipart/form-data">

            <div class="modal-header">
                <h5>Ajouter un événement</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-md-6">
                        <label>Titre</label>
                        <input type="text" name="titre" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label>Lieu</label>
                        <input type="text" name="lieu" class="form-control" required>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>Date début</label>
                        <input type="date" name="date_debut" class="form-control" required>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>Date fin</label>
                        <input type="date" name="date_fin" class="form-control" required>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>En ligne ?</label><br>
                        <input type="checkbox" name="est_en_ligne">
                    </div>

                    <div class="col-md-12 mt-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control ckeditor" rows="3"></textarea>
                    </div>

                    <div class="col-md-12 mt-3">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
                <button class="btn btn-success">Enregistrer</button>
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