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
						<li class="breadcrumb-item active" aria-current="page">mission</li>
					</ol>
				</nav>
			</div>
			<div class="ms-auto">
				<a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addmission">Nouveau mission</a>
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
							<?php $i = 1; foreach ($mission as $value): ?>
								<tr>
									<td><?= e($i++)?></td>
									<td><?= e($value['content'])?></td>
									<td>
										<div class="dropdown">
											<button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">Options</button>
											<div class="dropdown-menu">
												<a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#update_<?= e($value['id_mission'])?>">Modifier</a>
												<a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#detail_<?= e($value['id_mission'])?>">Description</a>
												<a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?= e($value['id_mission'])?>">Supprimer</a>
											</div>
										</div>
									</td>
								</tr>

								<!-- Modal Modifier -->
								<div class="modal fade" id="update_<?= e($value['id_mission'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
									<div class="modal-dialog modal-lg">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title">Modifier la mission</h5>
												<button type="button" class="btn-close--primary" data-bs-dismiss="modal"></button>
											</div>
											<form action="<?= base_url('Mission/Update_mission') ?>" method="POST">
												<input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
												<div class="modal-body">
													<div class="form-floating">
														<textarea class="form-control" name="Description" id="floatingTextarea" style="height: 100px;" required><?= e($value['content'])?></textarea>
														<label for="floatingTextarea">Description</label>
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

								<!--modal pour l'affichage d'une description -->
								
<div class="modal fade" id="detail_<?=e($value['id_mission'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-fullscreen">
<div class="modal-content">
<div class="modal-header">
    <h4 class="modal-title" id="myLargeModalLabel">Détail</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
</div>
<div class="modal-body">
  <?=e($value['content'])?>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button> 
</div>   
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
								

								

                               <!-- Modal Supprimer -->

								<div class="modal fade" id="delete_<?= e($value['id_mission'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
									<div class="modal-dialog modal-lg">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title">Confirmer la suppression</h5>
												<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
											</div>
											<form action="<?= base_url('Mission/Supprimer_mision') ?>" method="POST">
												<input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
												<div class="modal-body">
													<p>Voulez-vous vraiment supprimer cette mission ?</p>
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

		<!-- Modal Ajouter mission -->
		<div class="modal fade" id="addmission" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Nouvelle mission</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>
					<form action="<?= base_url('Mission/Creer_mission') ?>" method="POST">
						<div class="modal-body">
							<div class="form-floating">
								<textarea class="form-control" name="Description" id="floatingTextarea" style="height: 100px;" required></textarea>
								<label for="floatingTextarea">Description</label>
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

 

<?php include VIEWPATH.'includes/Footer.php'; ?>