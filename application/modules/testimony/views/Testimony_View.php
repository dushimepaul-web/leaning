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
	<li class="breadcrumb-item active" aria-current="page">Temoignage</li>
</ol>
</nav>
</div>
<div class="ms-auto">
<a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#Testimony">Nouveau</a>
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
            <th>Nom complet </th>
            <th>Image </th>
            <th>Poste </th>
            <th>Détails </th>
            <th>Action</th>
		</tr>
	</thead>
	<tbody>
<?php $i=1; foreach ($testimonies as $value) {  ?>
            <tr>
                <td><?=e($i++)?></td>
                <td><?=e($value['Testifier'])?></td>
                <td><img src="<?=base_url('attachments/Testimony/'.$value['Image'])?>" style="width: 40px;"></td>
                <td><?=e($value['Poste'])?></td>
                <td><?=substr($value['Details'], 0,30)?>..</td>
                <td>
                   <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Options</button>
                   <div class="dropdown-menu">
                    <a class="dropdown-item text-info" href="javascript:void()"  data-bs-toggle="modal" data-bs-target="#update_<?=e($value['IdTestimony'])?>">Modifier</a>
                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?=e($value['IdTestimony'])?>">Supprimer</a>
                   </div> 
                </td>
            </tr>


<div class="modal fade" id="update_<?=e($value['IdTestimony'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title" id="myLargeModalLabel">Modifier</h4>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
</div>
<form action="<?=base_url('Testimony/Update')?>" method="POST" enctype="multipart/form-data">
<input type="hidden" name="uuid" value="<?=e($value['uuid'])?>">
<div class="modal-body">
<div class="row">

<div class="mb-3 position-relative col-md-12">
<label class="form-label">Nom complet</label>
<input type="text" class="form-control" name="FullName" placeholder="Nom complet" value="<?=e($value['Testifier'])?>" required="">
</div>

<div class="mb-3 position-relative col-md-12">
<label class="form-label">Poste</label>
<input type="text" value="<?=e($value['Poste'])?>" class="form-control" name="Poste" placeholder="Poste" required="">
</div>
<div class="mb-3 position-relative col-md-12">
<label class="form-label">Image</label>
<input type="hidden" name="HiddenImage" value="<?=e($value['Image'])?>">
<input type="file" class="form-control" name="Image" placeholder="Image" >
</div>

</div>
<div class="form-floating">
<textarea class="form-control" id="floatingTextarea" style="height: 100px;" required="" name="Details"><?=e($value['Details'])?></textarea>
<label for="floatingTextarea">Détails</label>
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


<div class="modal fade" id="delete_<?=e($value['IdTestimony'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title" id="myLargeModalLabel">Voulez-vous vraiment supprimer ce contenu ?</h4>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
</div>
<form action="<?=base_url('Testimony/Delete')?>" method="POST">
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
            <th>Nom complet </th>
            <th>Image </th>
            <th>Poste </th>
            <th>Détails </th>
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

<div class="modal fade" id="Testimony" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title" id="myLargeModalLabel">Nouveau temoignage</h4>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
</div>
<form action="<?=base_url('Testimony/Create')?>" method="POST" enctype="multipart/form-data">
<div class="modal-body">
<div class="row">
<div class="mb-3 position-relative col-md-12">
<label class="form-label">Nom complet</label>
<input type="text" class="form-control" name="FullName" placeholder="Nom complet" required="">
</div>

<div class="mb-3 position-relative col-md-12">
<label class="form-label">Poste</label>
<input type="text" class="form-control" name="Poste" placeholder="Poste" required="">
</div>
<div class="mb-3 position-relative col-md-12">
<label class="form-label">Image</label>
<input type="file" class="form-control" name="Image" placeholder="Image" required="">
</div>

</div>
<div class="form-floating">
<textarea class="form-control" id="floatingTextarea" style="height: 100px;" required="" name="Details"></textarea>
<label for="floatingTextarea">Détails</label>
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