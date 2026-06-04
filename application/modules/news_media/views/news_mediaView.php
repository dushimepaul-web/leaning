
<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>

<div class="page-wrapper">
    <div class="page-content">

        <?php if($this->session->flashdata('sms')) echo $this->session->flashdata('sms'); ?>

        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Admin</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">News Media</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <a class="btn btn-outline-primary" href="#" data-bs-toggle="modal" data-bs-target="#addnews_media">Ajouter un news_media</a>
            </div>
        </div>
        <hr/>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Titre</th>
                                <th>Image</th>
                                <th>Détails</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($news_media as $value): ?>
                            <tr>
                                <td><?= e($i++)?></td>
                                <td><?= htmlspecialchars($value['title']); ?></td>
                                <td>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#Image_<?= e($value['id_news_media'])?>">
                                        <img src="<?= base_url('attachments/news_media/'.$value['image']); ?>" style="width:50px;height:50px;border-radius:50%;">
                                    </a>
                                </td>
                                <td><?= strip_tags(substr($value['details'],0,100)); ?>...</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown">Options</button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#update_<?= e($value['id_news_media'])?>">Modifier</a>
                                            <a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#detail_<?= e($value['id_news_media'])?>">Détails</a>
                                            <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?= e($value['id_news_media'])?>">Supprimer</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Modifier -->
                            <div class="modal fade" id="update_<?= e($value['id_news_media'])?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Modifier news_media</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= base_url('News_media/Update') ?>" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label>Titre</label>
                                                    <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($value['title']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Image (laisser vide si inchangé)</label>
                                                    <input type="file" class="form-control" name="image">
                                                </div>
                                                <div class="form-floating">
                                                    <textarea class="form-control ckeditor" name="details" style="height:120px;" required><?= htmlspecialchars($value['details']); ?></textarea>
                                                    <label>Détails</label>
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

                            <!-- Modal Détails -->
                            <div class="modal fade" id="detail_<?= e($value['id_news_media'])?>" tabindex="-1">
                                <div class="modal-dialog modal-fullscreen">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Détail complet</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?= e($value['details'])?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Supprimer -->
                            <div class="modal fade" id="delete_<?= e($value['id_news_media'])?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirmer la suppression</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="<?= base_url('News_media/Delete') ?>" method="POST">
                                            <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                            <div class="modal-body">
                                                <p>Voulez-vous vraiment supprimer cette news_media ?</p>
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
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter -->
        <div class="modal fade" id="addnews_media" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nouvelle news_media</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?= base_url('News_media/Create') ?>" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Titre</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label>Image</label>
                                <input type="file" class="form-control" name="image" required>
                            </div>
                            <div class="form-floating">
                                <textarea class="form-control ckeditor" name="details" style="height:120px;" required></textarea>
                                <label>Détails</label>
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
