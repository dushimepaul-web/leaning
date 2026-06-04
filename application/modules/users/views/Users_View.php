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
	<li class="breadcrumb-item active" aria-current="page">Utilisateurs</li>
</ol>
</nav>
</div>
<div class="ms-auto">
<a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#IdUser">Nouveau</a>
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
            <th>Prénom </th>
            <th>Nom </th>
            <th>Nom d'utilisateur </th>
            <th>Email </th>
            <th>Phone </th>
            <th>Groupe </th>
            <th>Date </th>
            <th>Action</th>
		</tr>
	</thead>
	<tbody>
<?php $i=1; foreach ($users as $value) {  ?>
            <tr>
                <td><?=e($i++)?></td>
                <td><?=e($value['firstName'])?></td>
                <td><?=e($value['lastName'])?></td>
                <td><?=e($value['username'])?></td>
                <td><?=e($value['email'])?></td>
                <td><?=e($value['telephone'])?></td>
                <td><?=e($value['group_name'])?></td>
                <td><?=e($value['dateinsertion'])?></td>
                <td>
                   <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Options</button>
                   <div class="dropdown-menu">
                    <a class="dropdown-item text-info" href="javascript:void()"  data-bs-toggle="modal" data-bs-target="#update_<?=e($value['idUser'])?>">Modifier</a>
                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?=e($value['idUser'])?>">Supprimer</a>

                    <a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#reset_<?=e($value['idUser'])?>">Initialiser le mot de passe</a>
                   </div> 
                </td>
            </tr>


<div class="modal fade" id="update_<?=e($value['idUser'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title" id="myLargeModalLabel">Modifier</h4>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
</div>
<form action="<?=base_url('UpdateUsers')?>" method="POST" enctype="multipart/form-data">
<input type="hidden" name="uuid" value="<?=e($value['uuid'])?>">
<div class="modal-body">
<div class="row">

<div class="mb-3 position-relative col-md-12">
<label class="form-label">Prénom</label>
<input type="text" class="form-control" name="FistName" placeholder="Fist Name" value="<?=e($value['firstName'])?>" required="">
</div>
<div class="mb-3 position-relative col-md-12">
<label class="form-label">Nom</label>
<input type="text" value="<?=e($value['lastName'])?>" class="form-control" name="LastName" placeholder="Last Name" required="">
</div>
<div class="mb-3 position-relative col-md-12">
<label class="form-label">Nom d'utilisateur</label>
<input type="text" value="<?=e($value['username'])?>" class="form-control" readonly name="Username" placeholder="Nom d'utilisateur" required="">
</div>
<div class="mb-3 position-relative col-md-12">
<label class="form-label">Email</label>
<input type="email" value="<?=e($value['email'])?>" class="form-control" name="email" placeholder="Email" >
</div>

<div class="mb-3 position-relative col-md-12">
<label class="form-label">Phone</label>
<input type="text" value="<?=e($value['telephone'])?>" class="form-control" name="Phone" placeholder="Phone" >
</div>

<div class="mb-3 position-relative col-md-12">
<label class="form-label">Groupe</label>
<select class="form-control" name="idGroup" required>
   <option value="">--Select--</option>
   <?php foreach ($groupes as $valuee) {
     ?>
   <option value="<?=e($valuee['idGroup'])?>" <?=  ($valuee['idGroup']==$value['idGroup']) ? 'selected' : '' ; ?>><?=e($valuee['group_name'])?></option>
   <?php } ?>
</select>

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


<div class="modal fade" id="delete_<?=e($value['idUser'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title" id="myLargeModalLabel">Voulez-vous vraiment supprimer ce contenu ?</h4>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
</div>
<form action="<?=base_url('DeleteUsers')?>" method="POST">
<input type="hidden" name="uuid" value="<?=e($value['uuid'])?>">
<div class="modal-footer">
<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
<button type="submit" class="btn btn-info">Supprimer</button>  
</div>                   
</form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" id="reset_<?=e($value['idUser'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title" id="myLargeModalLabel">Voulez-vous vraiment réunitialiser ce mot de passe ?</h4>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
</div>
<form action="<?=base_url('InitialPassword')?>" method="POST">
<input type="hidden" name="uuid" value="<?=e($value['uuid'])?>">
<div class="modal-footer">
<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
<button type="submit" class="btn btn-info">réunitialiser</button>  
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
            <th>Prénom </th>
            <th>Nom </th>
            <th>Nom d'utilisateur </th>
            <th>Email </th>
            <th>Phone </th>
            <th>Groupe </th>
            <th>Date </th>
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

<div class="modal fade" id="IdUser" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title" id="myLargeModalLabel">Nouveau</h4>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
</div>
<form action="<?=base_url('CreateUsers')?>" method="POST" enctype="multipart/form-data">
<div class="modal-body">
<div class="row">
<div class="mb-3 position-relative col-md-12">
<label class="form-label">Prénom</label>
<input type="text" class="form-control" name="FistName" placeholder="Prénom" required="">
</div>
<div class="mb-3 position-relative col-md-12">
<label class="form-label">Nom</label>
<input type="text" class="form-control" name="LastName" placeholder="Nom" required="">
</div>
<div class="mb-3 position-relative col-md-12">
<label class="form-label">Nom d'utilisateur</label>
<input type="text" onkeyup="checkUser(this.value)" class="form-control" name="Username" placeholder="Nom d'utilisateur" required="">
<small id="usernameMessage" class="text-danger"></small>
</div>
<div class="mb-3 position-relative col-md-12">
<label class="form-label">Email</label>
<input type="email" class="form-control" name="email" placeholder="email" required="">
</div>
<div class="mb-3 position-relative col-md-12">
<label class="form-label">Phone</label>
<input type="text" class="form-control" name="Phone" placeholder="Phone" required="">
</div>

<div class="mb-3 position-relative col-md-12">
<label class="form-label">Groupe</label>
<select class="form-control" name="idGroup" required>
   <option value="">--Sélectionner--</option>
   <?php foreach ($groupes as $valuee) {
     ?>
   <option value="<?=e($valuee['idGroup'])?>"><?=e($valuee['group_name'])?></option>
   <?php } ?>
</select>

</div>

</div>

</div>
<div class="modal-footer">
<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
<button type="submit" id="submitButton" class="btn btn-info">Enregistrer</button>  
</div>                   
</form>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<?php include VIEWPATH.'includes/Footer.php' ;?>

<script type="text/javascript">
  function checkUser(username) {
    $.ajax({
      url: "<?=base_url('users/Users/checkUser');?>",
      type: "POST",
      data: { username: username },
      success: function(data) {
        const usernameMessage = $('#usernameMessage');
        const submitButton = $('#submitButton');
        
        if (data === 'denied') {
          usernameMessage.text('Username is already taken!');
          submitButton.prop('disabled', true); // Disable the submit button
        } else {
          usernameMessage.text('');
          submitButton.prop('disabled', false); // Enable the submit button
        }
      },
      error: function() {
        console.error('An error occurred while checking the username.');
      }
    });
  }
</script>