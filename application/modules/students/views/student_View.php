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
						<li class="breadcrumb-item active" aria-current="page">Students</li>
					</ol>
				</nav>
			</div>
			<div class="ms-auto">
				<a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#students">Nouveau</a>
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
								<th>email</th>
								<th>Phone</th>
								<th>adress</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
                              <?php $i=1; foreach ($student as $value) {  ?>
								<tr>
									<td><?=e($i++)?></td>
									<td><?=e($value['fullname'])?></td>
									<td><?=substr($value['email'], 0,20)?></td>
									<td><?=substr($value['phone'], 0,20)?></td>
									<td>
									<td><?=substr($value['address'], 0,20)?>...</td>
									<td>
									
									   <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Options</button>
									   <div class="dropdown-menu">
										<a class="dropdown-item text-info" href="javascript:void()"  data-bs-toggle="modal" data-bs-target="#update_<?=e($value['id_student'])?>">Modifier</a>
										<a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?=e($value['id_student'])?>">Supprimer</a>
									   </div> 
									</td>
								</tr>


<div class="modal fade" id="update_<?=e($value['id_student'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title" id="myLargeModalLabel">Modifier</h4>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
	</div>
	<form action="<?=base_url('Updatestudent')?>" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="uuid" value="<?=e($value['uuid'])?>">
	<div class="modal-body">
		<div class="row">
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">Nom complet</label>
			<input type="text" class="form-control" value="<?=e($value['fullname'])?>" name="fullname" placeholder="Nom complet" required="">
		</div>
				<div class="mb-3 position-relative col-md-6">
			<label class="form-label">email</label>
			<input type="text" class="form-control" value="<?=e($value['email'])?>" name="email" placeholder="email" required="">
		</div>
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">adress</label>
			<input type="text" class="form-control" value="<?=e($value['address'])?>" name="address" placeholder="adress" required="">
		</div>
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">Phone </label>
			<input type="number" class="form-control" value="<?=e($value['phone'])?>" name="phone" placeholder="Phone number" required="">
		</div>
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




<div class="modal fade" id="delete_<?=e($value['id_student'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title" id="myLargeModalLabel">Voulez-vous vraiment supprimer <?=e($value['fullname'])?>  t ?</h4>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
	</div>
	<form action="<?=base_url('Students')?>" method="POST">
	<input type="hidden" name="uuid" value="<?=e($value['uuid'])?>">
	<div class="modal-footer">
	  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
	  <button type="submit" class="btn btn-info">Supprimer</button>  
	</div>                   
	</form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<?php } ?>
						</tbody>
						<tfoot>
							<tr>
								<th>#</th>
								<th>Nom complet</th>
								<th>email</th>
								<th>Phone </th>
								<th>adress</th>
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

<div class="modal fade" id="students" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title" id="myLargeModalLabel">Nouveau</h4>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
	</div>
	<form action="<?=base_url('Students/Createstudent')?>" method="POST" enctype="multipart/form-data">
	<div class="modal-body">
		<div class="row">
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">Nom complet</label>
			<input type="text" class="form-control" name="fullname" placeholder="Nom complet" required="">
		</div>
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">email</label>
			<input type="mail" class="form-control" name="email" required="">
		</div>
        <div class="mb-3 position-relative col-md-6">
			<label class="form-label">phone</label>
			<input type="number" class="form-control" name="phone" required="">
		</div>
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">adress</label>
			<input type="text" class="form-control" name="address" required="">
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