<?php include VIEWPATH.'includes/Header.php' ;?>
<?php include VIEWPATH.'includes/Sidebar.php' ;?>
<!--start page wrapper -->
<div class="page-wrapper">
	<div class="page-content">
		<!--breadcrumb-->
		<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
			<div class="breadcrumb-title pe-3">Admin</div>
			<div class="ps-3">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb mb-0 p-0">
						<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
						</li>
						<li class="breadcrumb-item active" aria-current="page">Contactez-nous</li>
					</ol>
				</nav>
			</div>
			<div class="ms-auto">
				<a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#contactus">Nouveau</a>
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
								<th>Nom complet</th>
								<th>Email</th>
								<th>Subject</th>
								<th>Message</th>
								<th>Phone</th>
								<th>location</th>
								<th> Date</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
                              <?php $i=1; foreach ($contactus as $value) {  ?>
								<tr>
									<td><?=e($i++)?></td>
									<td><?=e($value['FullName'])?></td>
									<td><?=substr($value['Email'], 0,20)?></td>
									<td><?=substr($value['PhoneNumber'], 0,20)?></td>
									<td>
									<td><?=substr($value['Subject'], 0,20)?>...</td>
									<td>
								   <td><?=substr($value['Message'], 0,20)?>...</td>
									<td>
									<td><?=substr($value['Date_creation'], 0,20)?>...</td>
								
									<td>
									
									   <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Options</button>
									   <div class="dropdown-menu">
										<a class="dropdown-item text-info" href="javascript:void()"  data-bs-toggle="modal" data-bs-target="#update_<?=e($value['IdContact'])?>">Modifier</a>
										<a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?=e($value['IdContact'])?>">Supprimer</a>
									   </div> 
									</td>
								</tr>


<div class="modal fade" id="update_<?=e($value['IdContact'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title" id="myLargeModalLabel">Modifier</h4>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
	</div>
	<form action="<?=base_url('Contact_Us/Update')?>" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="uuid" value="<?=e($value['uuid'])?>">
	<div class="modal-body">
		<div class="row">
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">Nom complet</label>
			<input type="text" class="form-control" value="<?=e($value['FullName'])?>" name="FullName" placeholder="Nom complet" required="">
		</div>
				<div class="mb-3 position-relative col-md-6">
			<label class="form-label">Email</label>
			<input type="text" class="form-control" value="<?=e($value['Email'])?>" name="Email" placeholder="Email" required="">
		</div>
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">Objet</label>
			<input type="text" class="form-control" value="<?=e($value['Subject'])?>" name="Subject" placeholder="Objet" required="">
		</div>
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">Phone </label>
			<input type="number" class="form-control" value="<?=e($value['PhoneNumber'])?>" name="PhoneNumber" placeholder="Phone" required="">
		</div>
		</div>

		<div class="form-floating">
			<textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea" style="height: 100px;" required="" name="Message"><?=e($value['Message'])?></textarea>
			<label for="floatingTextarea">Message</label>
		</div>
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">	Date </label>
			<input type="date" class="form-control" value="<?=e($value['Date_creation'])?>" name="Date_creation" placeholder="Date_creation" required="">
		</div>
	</div>
	<div class="modal-footer">
	  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
	  <button type="submit" class="btn btn-info">Modifier</button>  
	</div>                   
	</form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" id="Image_<?=e($value['IdContact'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title" id="myLargeModalLabel">Image</h4>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
	</div>
	<div class="modal-body">
	  <img src="<?=base_url()?>attachments/carousel/<?=e($value['Image'])?>" style="width: 750px; height:500px;">
	</div>
	<div class="modal-footer">
	  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button> 
	</div>   
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" id="delete_<?=e($value['IdContact'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title" id="myLargeModalLabel">Voulez-vous vraiment supprimer <?=e($value['FullName'])?>  t ?</h4>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
	</div>
	<form action="<?=base_url('Contact_Us/Delete')?>" method="POST">
	<input type="hidden" name="uuid" value="<?=e($value['uuid'])?>">
	<div class="modal-footer">
	  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
	  <button type="submit" class="btn btn-info">Supprimer</button>  
	</div>                   
	</form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div class="modal fade" id="detail_<?=e($value['IdContact'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-fullscreen">
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title" id="myLargeModalLabel">Détail</h4>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
	</div>
	<div class="modal-body">
	  <?=e($value['Detail'])?>
	</div>
	<div class="modal-footer">
	  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button> 
	</div>   
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php } ?>
						</tbody>
						<tfoot>
							<tr>
								<th>#</th>
								<th>Nom complet</th>
								<th>Email</th>
								<th>Phone </th>
								<th>Objet</th>
								<th>Message</th>
								<th> Date</th>
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

<div class="modal fade" id="contactus" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title" id="myLargeModalLabel">Nouveau</h4>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
	</div>
	<form action="<?=base_url('Contact_Us/Create')?>" method="POST" enctype="multipart/form-data">
	<div class="modal-body">
		<div class="row">
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">Nom complet</label>
			<input type="text" class="form-control" name="FullName" placeholder="Nom complet" required="">
		</div>
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">Email</label>
			<input type="mail" class="form-control" name="Email" required="">
		</div>
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">Objet</label>
			<input type="text" class="form-control" name="Subject" required="">
		</div>

		<div class="form-floating">
			<textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea" style="height: 100px;" required="" name="Message"></textarea>
			<label for="floatingTextarea">Message</label>
		</div>
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">Subject</label>
			<input type="number" class="form-control" name="PhoneNumber" required="">
		</div>
			<div class="mb-3 position-relative col-md-6">
			<label class="form-label">Date </label>
			<input type="date" class="form-control" name="Date_creation" required="">
		</div>
		</div>

		
	</div>
	<div class="modal-footer">
	  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
	  <button type="submit" class="btn btn-info">Enregistrer</button>  
	</div>                   
	</form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<?php include VIEWPATH.'includes/Footer.php' ;?>