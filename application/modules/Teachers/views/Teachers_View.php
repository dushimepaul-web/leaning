<?php include VIEWPATH . 'includes/Header.php'; ?>
<?php include VIEWPATH . 'includes/Sidebar.php'; ?>

<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Admin</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="javascript:;">
                                <i class="bx bx-home-alt"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Enseignants</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addTeacher">
                    Nouvel Enseignant
                </a>
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
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Specilite</th>
                                <th>Experiance</th>
                                <th>status</th>
                                <th>Date d'insertion</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($teachers as $value): ?>
                                <tr>
                                    <td><?= e($i++)?></td>
                                    <td><?= e($value['nom'])?></td>
                                    <td><?= e($value['prenom'])?></td>
                                    <td><?= e($value['email'])?></td>
                                    <td><?= e($value['phone'])?></td>
                                    <td><?= e($value['specialite'])?></td>
                                    <td><?= e($value['experience'])?></td>
                                    <td>
                                <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#Status_<?=e($value['id_teacher'])?>">
                                    <?=e($value['status']==1?'Activated':'Disactivated')?>
                                </a>
                            </td>
                                    <td><?= date('d/m/Y H:i', strtotime($value['date_insertion'])); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
                                                Options
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item text-success" href="#" data-bs-toggle="modal" data-bs-target="#afficher_<?= e($value['id_teacher'])?>">
                                                    Afficher
                                                </a>
                                                <a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#update_<?= e($value['id_teacher'])?>">
                                                    Modifier
                                                </a>
                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?= e($value['id_teacher'])?>">
                                                    Supprimer
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Modifier -->
                                <div class="modal fade" id="update_<?= e($value['id_teacher'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier l'enseignant</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('Teachers/UpdateTeacher') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" name="nom" value="<?= e($value['nom'])?>" required>
                                                                <label>Nom</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" name="prenom" value="<?= e($value['prenom'])?>" required>
                                                                <label>Prénom</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="email" class="form-control" name="email" value="<?= e($value['email'])?>" required>
                                                                <label>Email</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" name="phone" value="<?= e($value['phone'])?>" required>
                                                                <label>Téléphone</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-control" name="status" required>
                                                                    <option value="Actif" <?= e($value['status'] == 'Actif' ? 'selected' : '')?>>Actif</option>
                                                                    <option value="Inactif" <?= e($value['status'] == 'Inactif' ? 'selected' : '')?>>Inactif</option>
                                                                </select>
                                                                <label>Statut</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" name="address" value="<?= e($value['address'])?>" required>
                                                                <label>Adresse</label>
                                                            </div>
                                                        </div>
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





                                <!-- ========= MODAL STATUS ========= -->
                        <div class="modal fade" id="Status_<?=e($value['id_teacher'])?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Changer le status ?</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <form action="<?=base_url('Teachers/ChangeStatus')?>" method="POST">
                                        <input type="hidden" name="id_teacher" value="<?=e($value['id_teacher'])?>">
                                        <input type="hidden" name="status" value="<?=e($value['status'])?>">

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-info">Enregistrer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>




                                <!-- MODAL DETAIL -->
                                <div class="modal fade" id="afficher_<?= e($value['id_teacher'] )?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myLargeModalLabel">Détails de l'enseignant</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>Nom :</strong> <?= e($value['nom'])?></p>
                                                        <p><strong>Prénom :</strong> <?= e($value['prenom'])?></p>
                                                        <p><strong>Email :</strong> <?= e($value['email'])?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Téléphone :</strong> <?= e($value['phone'])?></p>
                                                        <p><strong>Statut :</strong> <?= e($value['status'])?></p>
                                                        <p><strong>Date d'insertion :</strong> <?= date('d/m/Y H:i', strtotime($value['date_insertion'])); ?></p>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-12">
                                                        <p><strong>Adresse :</strong> <?= e($value['address'])?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Supprimer -->
                                <div class="modal fade" id="delete_<?= e($value['id_teacher'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmer la suppression</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('Teachers/DeleteTeacher') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-body">
                                                    <p>Voulez-vous vraiment supprimer l'enseignant "<?= e($value['nom'] . ' ' . $value['prenom'])?>" ?</p>
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
                        <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Statut</th>
                                <th>Date d'insertion</th>
                                <th>Actions</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter Enseignant -->
        <div class="modal fade" id="addTeacher" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nouvel enseignant</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?= base_url('Teachers/CreateTeacher') ?>" method="POST">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="nom" required>
                                        <label>Nom</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="prenom" required>
                                        <label>Prénom</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" name="email" required>
                                        <label>Email</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="phone" required>
                                        <label>Téléphone</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-control" name="status" required>
                                            <option value="Actif">Actif</option>
                                            <option value="Inactif">Inactif</option>
                                        </select>
                                        <label>Statut</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="address" required>
                                        <label>Adresse</label>
                                    </div>
                                </div>
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

<?php include VIEWPATH . 'includes/Footer.php'; ?>