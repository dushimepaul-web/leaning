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
	<li class="breadcrumb-item active" aria-current="page">Settings</li>
</ol>
</nav>
</div>
<div class="ms-auto">
<a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#keyvalue">Nouveau</a>
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
            <th>Clé </th>
            <th>description</th>
            <th>Valeur</th>
            <th>Action</th>
		</tr>
	</thead>
	<tbody>
<?php $i=1; foreach ($settings as $value) {  ?>
            <tr>
                <td><?=e($i++)?></td>
                <td><?=e($value['KeyValue'])?></td>
                <td><?=e($value['TitlePage'])?></td>
                <?php if ($value['IsFile']==1) { 
                    $imgSrc = (strpos($value['Value'], 'assets/') === 0 || strpos($value['Value'], './') === 0) 
                        ? base_url($value['Value']) 
                        : base_url('attachments/Other/'.$value['Value']);
                ?>
                   <td><a target="_blank" href="<?= $imgSrc ?>"><img style="width: 50px; height:50px; object-fit:cover;" src="<?= $imgSrc ?>"></a></td>

                <?php }else{  ?>
                    <td><?= substr($value['Value'], 0,70)?>...</td>
                <?php } ?>
                
                <td>
                   <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Options</button>
                   <div class="dropdown-menu">
                    <a class="dropdown-item text-info" href="javascript:void()"  data-bs-toggle="modal" data-bs-target="#update_<?=e($value['IdSetting'])?>">Modifier</a>
                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?=e($value['IdSetting'])?>">Supprimer</a>
                   </div> 
                </td>
            </tr>





 <!-- Modal Update -->
                        <div class="modal fade" id="update_<?= e($value['IdSetting'] )?>" data-bs-backdrop="static">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                    <form action="<?= base_url('Settings/Update') ?>" method="POST" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Modifier</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <input type="hidden" name="uuid" value="<?= e($value['uuid'] )?>">
                                            <input type="hidden" name="IsFile" value="<?= e($value['IsFile'] )?>">

                                            <div class="mb-3">
                                                <label class="form-label">Clé</label>
                                                <input type="text" class="form-control" name="KeyValue" value="<?= e($value['KeyValue'] )?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Clé</label>
                                                <input type="text" class="form-control" name="TitlePage" value="<?= e($value['TitlePage'] )?>" required>
                                            </div>

                                            <?php if ($value['IsFile'] == 1): 
                                                $imgPrev = (strpos($value['Value'], 'assets/') === 0 || strpos($value['Value'], './') === 0) 
                                                    ? base_url($value['Value']) 
                                                    : base_url('attachments/Other/'.$value['Value']);
                                            ?>
                                                <div class="mb-3 text-center">
                                                    <img src="<?= $imgPrev ?>" style="max-width:200px;max-height:100px;object-fit:cover;" class="rounded border">
                                                    <small class="d-block text-muted">Image actuelle</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nouveau fichier</label>
                                                    <input type="file" class="form-control" name="Value">
                                                    <input type="hidden" name="HiddenImage" value="<?= e($value['Value'] )?>">
                                                </div>
                                            <?php else: ?>
                                                <div class="mb-3">
                                                    <label class="form-label">Valeur</label>
                                                    <textarea class="form-control" name="Value" style="height: 100px;" required><?= e($value['Value'] )?></textarea>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
                                            <button type="submit" class="btn btn-info">Enregistrer</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>








<div class="modal fade" id="delete_<?=e($value['IdSetting'])?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title" id="myLargeModalLabel">Voulez-vous vraiment supprimer ce contenu ?</h4>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
</div>
<form action="<?=base_url('Settings/Delete')?>" method="POST">
<input type="hidden" name="IdSetting" value="<?=e($value['IdSetting'])?>">
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
            <th>Clé</th>
            <th>Valeur</th>
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

<div class="modal fade" id="keyvalue" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h4 class="modal-title" id="myLargeModalLabel">Clé-Valeur</h4>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
</div>
<form action="<?=base_url('Settings/Create')?>" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
        <div class="row">
            <div class="mb-3 position-relative col-md-12">
                <label class="form-label">Clé</label>
                <input type="text" class="form-control" name="KeyValue" placeholder="Clé" required="">
            </div>
            <div class="mb-3 position-relative col-md-12">
                <label class="form-label">description</label>
                <input type="text" class="form-control" name="TitlePage" placeholder="Description" required="">
            </div>
        </div>

        <!-- Case à cocher pour activer le champ file -->
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="toggleInputCheckbox" name="toggleInputCheckbox" onchange="toggleTextareaToFile()">
            <label class="form-check-label" for="toggleInputCheckbox">
                Charger un fichier au lieu d'un texte
            </label>
        </div>

        <!-- Champ dynamique (textarea par défaut) -->
        <div class="mb-3" id="dynamicInputContainer">
            <textarea class="form-control" id="dynamicInput" style="height: 100px;" name="Value" placeholder="Entrez une valeur"></textarea>
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

<script>
    // Fonction pour changer le champ textarea en champ file
    function toggleTextareaToFile() {
        const checkbox = document.getElementById('toggleInputCheckbox');
        const container = document.getElementById('dynamicInputContainer');

        if (checkbox.checked) {
            // Remplace le textarea par un input type="file"
            container.innerHTML = `
                <input type="file" class="form-control" id="dynamicInput" name="Value" required>
            `;
        } else {
            // Remet un textarea si la case est décochée
            container.innerHTML = `
                <textarea class="form-control" id="dynamicInput" style="height: 100px;" name="Value" placeholder="Entrez une valeur"></textarea>
            `;
        }
    }
</script>