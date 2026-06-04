<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>

<div class="page-wrapper">
	<div class="page-content">

		<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
			<div class="breadcrumb-title pe-3">Admin</div>
			<div class="ps-3">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb mb-0 p-0">
						<li class="breadcrumb-item"><a href="#"><i class="bx bx-home-alt"></i></a></li>
						<li class="breadcrumb-item active">Timetable</li>
					</ol>
				</nav>
			</div>
			<div class="ms-auto">
				<a class="btn btn-outline-primary" href="#" data-bs-toggle="modal" data-bs-target="#addtimetable">New timetable</a>
			</div>
		</div>

		<hr/>

		<div class="card">
			<div class="card-body">
				<div class="table-responsive">
					<table id="example" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Date début</th>
								<th>Date fin</th>
								<th>Dernière mise à jour</th>
								<th>Teacher</th>
								<th>Actions</th>
							</tr>
						</thead>

						<tbody>
							<?php $i = 1; foreach ($timetable as $value): ?>
								<tr>
									<td><?= e($i++)?></td>
									<td><?= e($value['date_debut'])?></td>
									<td><?= e($value['date_defin'])?></td>
									<td><?= e($value['time'])?></td>
									<td><?= e($value['nom_teacher'] . ' ' . $value['prenom_teacher'])?></td>

									<td>
										<div class="dropdown">
											<button class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">Options</button>
											<div class="dropdown-menu">
												<a class="dropdown-item text-info" 
													href="#" data-bs-toggle="modal" 
													data-bs-target="#update_<?= e($value['id_timetable'])?>">
													Modifier
												</a>

												<a class="dropdown-item text-danger" 
													href="#" data-bs-toggle="modal" 
													data-bs-target="#delete_<?= e($value['id_timetable'])?>">
													Supprimer
												</a>
											</div>
										</div>
									</td>
								</tr>

								<!-- MODAL UPDATE -->
								<div class="modal fade" id="update_<?= e($value['id_timetable'])?>" tabindex="-1">
									<div class="modal-dialog modal-lg">
										<div class="modal-content">

											<div class="modal-header">
												<h5 class="modal-title">Modifier timetable</h5>
												<button class="btn-close" data-bs-dismiss="modal"></button>
											</div>

											<form action="<?= base_url('timetable/Update_timetable'); ?>" method="POST">
												<input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">

												<div class="modal-body">
													<div class="row">

														<div class="col-md-6 mb-3">
															<label class="form-label">Date début</label>
															<input type="date" class="form-control" 
																   name="date_debut" 
																   value="<?= substr($value['date_debut'],0,10); ?>" required>
														</div>

														<div class="col-md-6 mb-3">
															<label class="form-label">Date fin</label>
															<input type="date" class="form-control" 
																   name="date_defin" 
																   value="<?= substr($value['date_defin'],0,10); ?>" required>
														</div>

														<div class="col-md-6 mb-3">
															<label class="form-label">Teacher</label>
															<select class="form-control" name="id_teacher" required>
																<option value="">-- sélectionnez --</option>
																<?php foreach($teachers as $t): ?>
																	<option value="<?= e($t['id_teacher'])?>"
																		<?= e($t['id_teacher'] == $value['id_teacher'] ? 'selected':'')?>>
																		<?= e($t['nom'].' '.$t['prenom'])?>
																	</option>
																<?php endforeach; ?>
															</select>
														</div>

													</div>
												</div>

												<div class="modal-footer">
													<button class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
													<button class="btn btn-info">Modifier</button>
												</div>
											</form>

										</div>
									</div>
								</div>

								<!-- MODAL DELETE -->
								<div class="modal fade" id="delete_<?= e($value['id_timetable'])?>" tabindex="-1">
									<div class="modal-dialog modal-lg">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title">Confirmation</h5>
												<button class="btn-close" data-bs-dismiss="modal"></button>
											</div>

											<form action="<?= base_url('timetable/Supprimer_timetable'); ?>" method="POST">
												<input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">

												<div class="modal-body">
													Voulez-vous vraiment supprimer cette timetable ?
												</div>

												<div class="modal-footer">
													<button class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
													<button class="btn btn-info">Supprimer</button>
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
								<th>Date début</th>
								<th>Date fin</th>
								<th>Dernière mise à jour</th>
								<th>Teacher</th>
								<th>Actions</th>
							</tr>
						</tfoot>

					</table>

				</div>
			</div>
		</div>

		<!-- MODAL ADD -->
		<div class="modal fade" id="addtimetable" tabindex="-1">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">

					<div class="modal-header">
						<h5 class="modal-title">New timetable</h5>
						<button class="btn-close" data-bs-dismiss="modal"></button>
					</div>

					<form action="<?= base_url('timetable/Creer_timetable'); ?>" method="POST">
						<div class="modal-body">

							<div class="row">

								<div class="col-md-6 mb-3">
									<label class="form-label">Date début</label>
									<input type="date" class="form-control" name="date_debut" required>
								</div>

								<div class="col-md-6 mb-3">
									<label class="form-label">Date fin</label>
									<input type="date" class="form-control" name="date_defin" required>
								</div>

								<div class="col-md-6 mb-3">
									<label class="form-label">Teacher</label>
									<select class="form-control" name="id_teacher" required>
										<option value="">-- sélectionnez --</option>
										<?php foreach($teachers as $t): ?>
											<option value="<?= e($t['id_teacher'])?>">
												<?= e($t['nom'].' '.$t['prenom'])?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>

							</div>

						</div>

						<div class="modal-footer">
							<button class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
							<button class="btn btn-info">Enregistrer</button>
						</div>

					</form>

				</div>
			</div>
		</div>

	</div>
</div>

<?php include VIEWPATH.'includes/Footer.php'; ?>


						