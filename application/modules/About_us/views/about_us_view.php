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
						<li class="breadcrumb-item active" aria-current="page">Contenus Institutionnels</li>
					</ol>
				</nav>
			</div>
			<div class="ms-auto">
				<a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addabout_us">Nouveau contenu</a>
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
								<th>Type</th>
								<th>Titre</th>
								<th>Description</th>
								<th>Statut</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1; foreach ($about_us as $value): ?>
								<tr>
									<td><?= e($i++)?></td>
									<td>
										<?php 
										switch($value['Type']) {
											case 'VALEUR': echo '<span class="badge bg-primary">Valeur</span>'; break;
											case 'MISSION': echo '<span class="badge bg-success">Mission</span>'; break;
											case 'VISION': echo '<span class="badge bg-info">Vision</span>'; break;
											case 'AXE_STRATEGIQUE': echo '<span class="badge bg-warning">Axe Stratégique</span>'; break;
											case 'MODELE_PEDAGOGIQUE': echo '<span class="badge bg-secondary">Modèle Pédagogique</span>'; break;
											case 'PARTENARIAT_STRATEGIQUE': echo '<span class="badge bg-dark">Partenariat Stratégique</span>'; break;
											default: echo $value['Type'];
										}
										?>
									</td>
									<td><?= e($value['Title'])?></td>
									<td><?= substr(strip_tags($value['Description']), 0, 100) ?>...</td>
									<td>
										<span class="badge bg-<?= $value['Status'] == 'Active' ? 'success' : 'danger' ?>">
											<?= $value['Status'] == 'Active' ? 'Actif' : 'Inactif' ?>
										</span>
									</td>
									<td>
										<div class="dropdown">
											<button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">Options</button>
											<div class="dropdown-menu">
												<a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#update_<?= e($value['IdContent'])?>">Modifier</a>
												<a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#detail_<?= e($value['IdContent'])?>">Détails</a>
												<a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?= e($value['IdContent'])?>">Supprimer</a>
											</div>
										</div>
									</td>
								</tr>

								<!-- Modal Modifier -->
								<div class="modal fade" id="update_<?= e($value['IdContent'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
									<div class="modal-dialog modal-xl">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title">Modifier le contenu</h5>
												<button type="button" class="btn-close--primary" data-bs-dismiss="modal"></button>
											</div>
											<form action="<?= base_url('About_us/Update_about_us') ?>" method="POST">
												<input type="hidden" name="uuid" value="<?= e($value['IdContent'])?>">
												<div class="modal-body">
													<div class="mb-3 position-relative col-md-6">
														<label class="form-label">Type</label>
														<select class="form-control" name="type" required>
															<option value="VALEUR" <?= $value['Type'] == 'VALEUR' ? 'selected' : '' ?>>Valeur</option>
															<option value="MISSION" <?= $value['Type'] == 'MISSION' ? 'selected' : '' ?>>Mission</option>
															<option value="VISION" <?= $value['Type'] == 'VISION' ? 'selected' : '' ?>>Vision</option>
															<option value="AXE_STRATEGIQUE" <?= $value['Type'] == 'AXE_STRATEGIQUE' ? 'selected' : '' ?>>Axe Stratégique</option>
															<option value="MODELE_PEDAGOGIQUE" <?= $value['Type'] == 'MODELE_PEDAGOGIQUE' ? 'selected' : '' ?>>Modèle Pédagogique</option>
															<option value="PARTENARIAT_STRATEGIQUE" <?= $value['Type'] == 'PARTENARIAT_STRATEGIQUE' ? 'selected' : '' ?>>Partenariat Stratégique</option>
														</select>
													</div>
													<div class="mb-3 position-relative col-md-6">
														<label class="form-label">Titre</label>
														<input type="text" class="form-control" value="<?=e($value['Title'])?>" name="title" placeholder="Titre" required="">
													</div>
													<div class="mb-3 position-relative col-md-6">
														<label class="form-label">Statut</label>
														<select class="form-control" name="status">
															<option value="Active" <?= $value['Status'] == 'Active' ? 'selected' : '' ?>>Actif</option>
															<option value="Inactive" <?= $value['Status'] == 'Inactive' ? 'selected' : '' ?>>Inactif</option>
														</select>
													</div>
													<div class="mb-3">
														<label class="form-label">Description</label>
														<textarea class="form-control ckeditor-about" name="details" id="ck_about_update_<?= $value['IdContent'] ?>" style="height: 200px;" required><?= e($value['Description'])?></textarea>
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

								<!-- Modal pour l'affichage des détails -->
								<div class="modal fade" id="detail_<?=e($value['IdContent'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
									<div class="modal-dialog modal-fullscreen">
										<div class="modal-content">
											<div class="modal-header">
												<h4 class="modal-title" id="myLargeModalLabel">Détail - <?= e($value['Title'])?></h4>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
											</div>
											<div class="modal-body">
												<?= $value['Description']?>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button> 
											</div>   
										</div><!-- /.modal-content -->
									</div><!-- /.modal-dialog -->
								</div><!-- /.modal -->

								<!-- Modal Supprimer -->
								<div class="modal fade" id="delete_<?= e($value['IdContent'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
									<div class="modal-dialog modal-lg">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title">Confirmer la suppression</h5>
												<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
											</div>
											<form action="<?= base_url('About_us/Supprimer_about_us') ?>" method="POST">
												<input type="hidden" name="uuid" value="<?= e($value['IdContent'])?>">
												<div class="modal-body">
													<p>Voulez-vous vraiment supprimer ce contenu ?</p>
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

		<!-- Modal Ajouter contenu -->
		<div class="modal fade" id="addabout_us" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
			<div class="modal-dialog modal-xl">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Nouveau contenu</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>
					<form action="<?= base_url('About_us/Creer_about_us') ?>" method="POST">
						<div class="modal-body">
							<div class="mb-3 position-relative col-md-6">
								<label class="form-label">Type</label>
								<select class="form-control" name="type" required>
									<option value="VALEUR">Valeur</option>
									<option value="MISSION">Mission</option>
									<option value="VISION">Vision</option>
									<option value="AXE_STRATEGIQUE">Axe Stratégique</option>
									<option value="MODELE_PEDAGOGIQUE">Modèle Pédagogique</option>
									<option value="PARTENARIAT_STRATEGIQUE">Partenariat Stratégique</option>
								</select>
							</div>
							<div class="mb-3 position-relative col-md-6">
								<label class="form-label">Titre</label>
								<input type="text" class="form-control" name="title" placeholder="Titre" required="">
							</div>
							<div class="mb-3 position-relative col-md-6">
								<label class="form-label">Statut</label>
								<select class="form-control" name="status">
									<option value="Active">Actif</option>
									<option value="Inactive">Inactif</option>
								</select>
							</div>
							<div class="mb-3">
								<label class="form-label">Description</label>
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