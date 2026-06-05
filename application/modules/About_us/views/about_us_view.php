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
						<li class="breadcrumb-item active" aria-current="page">about us</li>
					</ol>
				</nav>
			</div>
			<div class="ms-auto">
				<a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addabout_us">Nouveau about_us</a>
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
                                <th>title</th>
								<th>details</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1; foreach ($about_us as $value): ?>
								<tr>
									<td><?= e($i++)?></td>
                   <td><?= e($value['title'])?></td>
									<td><?= substr(strip_tags($value['details']), 0, 100) ?>...</td>
									<td>
										<div class="dropdown">
											<button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">Options</button>
											<div class="dropdown-menu">
												<a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#update_<?= e($value['id_about_us'])?>">Modifier</a>
												<a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#detail_<?= e($value['id_about_us'])?>">details</a>
												<a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?= e($value['id_about_us'])?>">Supprimer</a>
											</div>
										</div>
									</td>
								</tr>

								<!-- Modal Modifier -->
								<div class="modal fade" id="update_<?= e($value['id_about_us'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
									<div class="modal-dialog modal-xl">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title">Modifier la about_us</h5>
												<button type="button" class="btn-close--primary" data-bs-dismiss="modal"></button>
											</div>
											<form action="<?= base_url('About_us/Update_about_us') ?>" method="POST">
												<input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
												<div class="modal-body">
                                        <div class="mb-3 position-relative col-md-6">
                                                 <label class="form-label">Titre</label>
                                               <input type="text" class="form-control" value="<?=e($value['title'])?>" name="title" placeholder="Titre" required="">
                                            </div>
													<div class="mb-3">
														<label class="form-label">Détails</label>
														<textarea class="form-control ckeditor-about" name="details" id="ck_about_update_<?= $value['id_about_us'] ?>" style="height: 200px;" required><?= e($value['details'])?></textarea>
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

								<!--modal pour l'affichage d'une details -->
								
<div class="modal fade" id="detail_<?=e($value['id_about_us'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-fullscreen">
<div class="modal-content">
<div class="modal-header">
    <h4 class="modal-title" id="myLargeModalLabel">Détail</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
</div>
<div class="modal-body">
  <?= $value['details']?>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button> 
</div>   
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
								

								

                               <!-- Modal Supprimer -->

								<div class="modal fade" id="delete_<?= e($value['id_about_us'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
									<div class="modal-dialog modal-lg">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title">Confirmer la suppression</h5>
												<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
											</div>
											<form action="<?= base_url('About_us/Supprimer_about_us') ?>" method="POST">
												<input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
												<div class="modal-body">
													<p>Voulez-vous vraiment supprimer cette about_us ?</p>
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

		<!-- Modal Ajouter about_us -->
		<div class="modal fade" id="addabout_us" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
			<div class="modal-dialog modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Nouvelle about_us</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>
					<form action="<?= base_url('About_us/Creer_about_us') ?>" method="POST">
						<div class="modal-body">
                <div class="mb-3 position-relative col-md-6">
                    <label class="form-label">Titre</label>
                     <input type="text" class="form-control" name="title" placeholder="Titre" required="">
                       </div>
							<div class="mb-3">
								<label class="form-label">Détails</label>
								<textarea class="form-control ckeditor-about" name="details" id="ck_about_add" style="height: 200px;" required></textarea>
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

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('shown.bs.modal', function (e) {
        if (e.target.classList.contains('modal')) {
            e.target.querySelectorAll('textarea.ckeditor-about').forEach(function (el) {
                if (!CKEDITOR.instances[el.id]) {
                    CKEDITOR.replace(el.id, {
                        filebrowserUploadUrl: "<?= base_url('Carousel/uploadImage') ?>",
                        filebrowserUploadMethod: "form"
                    });
                }
            });
        }
    });
    document.addEventListener('hidden.bs.modal', function (e) {
        if (e.target.classList.contains('modal')) {
            e.target.querySelectorAll('textarea.ckeditor-about').forEach(function (el) {
                if (CKEDITOR.instances[el.id]) {
                    CKEDITOR.instances[el.id].destroy();
                }
            });
        }
    });
});
</script>

<?php include VIEWPATH.'includes/Footer.php'; ?>