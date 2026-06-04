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
						<li class="breadcrumb-item active" aria-current="page">Partenaires</li>
					</ol>
				</nav>
			</div>
			<div class="ms-auto">
				<a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#partener">Nouveau Partenaire</a>
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
		<th>logo</th>
		<th>description</th>
		<th>link</th>
		<th>Status</th>
		<th>Action</th>
	</tr>
</thead>
<tbody>
      <?php $i=1; foreach ($parteners as $value) {  ?>
		<tr>
			<td><?=e($i++)?></td>
			<td>
			<a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#Image_<?=e($value['id_partner'])?>">
			<img src="<?=base_url()?>attachments/Partener/<?=e($value['logo'])?>" style="width:50px; height:50px; border-radius:50%;">
			</a>
			</td>
			<td><?=e($value['description'])?></td>
			
			<td><?=substr($value['link'], 0,20)?>...</td>
			<td>
			  <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#STATUS_<?=e($value['id_partner'])?>">
				  <?=e($value['status']==1?'Activated':'Disactivated')?>
			  </a>
			</td>
			<td>
			   <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Options</button>
			   <div class="dropdown-menu">
				<a class="dropdown-item text-info" href="javascript:void()"  data-bs-toggle="modal" data-bs-target="#update_<?=e($value['id_partner'])?>">Modifier</a>
				<a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?=e($value['id_partner'])?>">Supprimer</a>
			   </div> 
			</td>
		</tr>


<div class="modal fade" id="update_<?=e($value['id_partner'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title" id="myLargeModalLabel">Modifier</h4>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
	</div>
	<form action="<?=base_url('Parteners/Update')?>" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="uuid" value="<?=e($value['uuid'])?>">
	<div class="modal-body">
		<div class="row">
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">logo</label>
			<input type="hidden" name="Hiddenlogo" value="<?=e($value['logo'])?>">
			<input type="file" class="form-control" name="logo">
		</div>
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">link</label>
			<input type="text" class="form-control" value="<?=e($value['link'])?>" name="link" placeholder="link" required="">
		</div>
		</div>
		<div class="row">
		<div class="form-floating">
			<textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea" style="height: 100px;" required="" name="description"><?=e($value['description'])?></textarea>
			<label for="floatingTextarea">description</label>
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


<div class="modal fade" id="Image_<?=e($value['id_partner'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title" id="myLargeModalLabel">Image</h4>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
	</div>
	<div class="modal-body">
	  <img src="<?=base_url()?>attachments/Partener/<?=e($value['logo'])?>" style="width: 750px; height:500px;">
	</div>
	<div class="modal-footer">
	  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button> 
	</div>   
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" id="delete_<?=e($value['id_partner'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title" id="myLargeModalLabel">Voulez-vous vraiment supprimer ce contenu ?</h4>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
	</div>
	<form action="<?=base_url('Parteners/Delete')?>" method="POST">
	<input type="hidden" name="uuid" value="<?=e($value['uuid'])?>">
	<div class="modal-footer">
	  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
	  <button type="submit" class="btn btn-info">Supprimer</button>  
	</div>                   
	</form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="STATUS_<?=e($value['id_partner'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title" id="myLargeModalLabel">Voulez-vous vraiment changer ce status ?</h4>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
	</div>
	<form action="<?=base_url('Parteners/ChangeStatus')?>" method="POST">
	<input type="hidden" name="uuid" value="<?=e($value['uuid'])?>">
	<input type="hidden" name="status" value="<?=e($value['status'])?>">
	<div class="modal-footer">
	  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
	  <button type="submit" class="btn btn-info">Enregistrer</button>  
	</div>                   
	</form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="detail_<?=e($value['id_partner'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
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
								<th>logo</th>
								<th>description</th>
								<th>link</th>
								<th>Status</th>
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

<div class="modal fade" id="partener" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title" id="myLargeModalLabel">Nouveau Partenaire</h4>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
	</div>
	<form action="<?=base_url('Parteners/Create')?>" method="POST" enctype="multipart/form-data">
	<div class="modal-body">
		<div class="row">
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">logo</label>
			<input type="file" class="form-control" name="logo" required="">
		</div>
		<div class="mb-3 position-relative col-md-6">
			<label class="form-label">link</label>
			<input type="text" class="form-control" name="link" placeholder="link" required="">
		</div>
		</div>
		<div class="row">
		  
		<div class="form-floating">
			<textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea" style="height: 100px;" required="" name="description"></textarea>
			<label for="floatingTextarea">description</label>
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

<?php include VIEWPATH.'includes/Footer.php' ;?>