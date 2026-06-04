<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>

<!--start page wrapper -->
<div class="page-wrapper">
<div class="page-content">

    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Admin</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Carousels</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#carousel">Nouveau Carousel</a>
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
                            <th>Image</th>
                            <th>Titre</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Détail</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php $i=1; foreach ($carousel as $value) { ?>
                        <tr>
                            <td><?=e($i++)?></td>
                            
                            <td>
                                <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#Image_<?=e($value['IdCarousel'])?>">
                                <img src="<?=base_url()?>attachments/Carousel/<?=e($value['Image'])?>" style="width:50px; height:50px; border-radius:50%;">
                                </a>
                            </td>

                            <td><?=e($value['Title'])?></td>

                            <td>
                                <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#Status_<?=e($value['IdCarousel'])?>">
                                    <?=e($value['IsActive']==1?'Activated':'Disactivated')?>
                                </a>
                            </td>

                            <td><?=substr($value['Description'], 0,20)?>...</td>

                            <td>
                                <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#detail_<?=e($value['IdCarousel'])?>">View detail</a>
                            </td>

                            <td>
                                <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Options</button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item text-info" href="javascript:void()" data-bs-toggle="modal" data-bs-target="#update_<?=e($value['IdCarousel'])?>">Modifier</a>
                                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?=e($value['IdCarousel'])?>">Supprimer</a>
                                </div>
                            </td>
                        </tr>

                        <!-- ========= MODAL UPDATE ========= -->
                        <div class="modal fade" id="update_<?=e($value['IdCarousel'])?>" data-bs-backdrop="static">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Modifier</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <form action="<?=base_url('Carousel/Update')?>" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="uuid" value="<?=e($value['uuid'])?>">

                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label">Titre</label>
                                                    <input type="text" class="form-control" value="<?=e($value['Title'])?>" name="Title" required>
                                                </div>

                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label">Image</label>
                                                    <input type="hidden" name="HiddenImage" value="<?=e($value['Image'])?>">
                                                    <input type="file" class="form-control" name="Image">
                                                </div>
                                            </div>

                                            <div class="form-floating">
                                                <textarea class="form-control ckeditor" name="Description" style="height:120px;"><?=e($value['Description'])?></textarea>
                                                <label>Description</label>
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

                        <!-- ========= MODAL IMAGE ========= -->
                        <div class="modal fade" id="Image_<?=e($value['IdCarousel'])?>">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Image</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <img src="<?=base_url()?>attachments/Carousel/<?=e($value['Image'])?>" style="width:100%; height:auto;">
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- ========= MODAL DELETE ========= -->
                        <div class="modal fade" id="delete_<?=e($value['IdCarousel'])?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Voulez-vous supprimer ?</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <form action="<?=base_url('Carousel/Delete')?>" method="POST">
                                        <input type="hidden" name="uuid" value="<?=e($value['uuid'])?>">

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-info">Supprimer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>



                        <!-- ========= MODAL DETAIL ========= -->
                        <div class="modal fade" id="detail_<?=e($value['IdCarousel'])?>">
                            <div class="modal-dialog modal-fullscreen">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Détail</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <?=e($value['Description'])?>
                                    </div>

                                    <div class="modal-footer">
                                   <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
                                    </div>

                                </div>
                            </div>
                        </div>


                        <!-- ========= MODAL STATUS ========= -->
                        <div class="modal fade" id="Status_<?=e($value['IdCarousel'])?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Changer le status ?</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <form action="<?=base_url('Carousel/ChangeStatus')?>" method="POST">
                                        <input type="hidden" name="uuid" value="<?=e($value['uuid'])?>">
                                        <input type="hidden" name="IsActive" value="<?=e($value['IsActive'])?>">

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-info">Enregistrer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php } ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Titre</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Détail</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>

                </table>
            </div>
        </div>
    </div>

    <hr/>

</div>
</div>
<!--end page wrapper -->


<!-- ========= MODAL NEW CAROUSEL ========= -->
<div class="modal fade" id="carousel" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Nouveau Carousel</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="<?=base_url('Carousel/Create')?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Titre</label>
                            <input type="text" class="form-control" name="Title" required>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" name="Image" required>
                        </div>
                    </div>

                    <div class="form-floating">
                        <textarea class="form-control ckeditor" name="Description" style="height:120px;"></textarea>
                        <label>Description</label>
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