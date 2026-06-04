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
                        <li class="breadcrumb-item active" aria-current="page">Modes de Présence</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addAttendaceMode">
                    Nouveau Mode
                </a>
            </div>
        </div>
        <!--end breadcrumb-->
        <hr/>
         <?php if ($this->session->flashdata('sms')): ?>
            <div class="bg-success text-white mb-1 fs-5">
             <?=e($this->session->flashdata('sms'))?>
             </div>
         <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom du Mode</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($attendance_modes as $value): ?>
                                <tr>
                                    <td><?= e($i++)?></td>
                                    <td><?= e($value['nom_attendance'])?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
                                                Options
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#update_<?= e($value['id_attendance'])?>">
                                                    Modifier
                                                </a>
                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?= e($value['id_attendance'])?>">
                                                    Supprimer
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Modifier -->
                                <div class="modal fade" id="update_<?= e($value['id_attendance'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier le mode de présence</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('Attendace_course_mode/UpdateAttendaceMode') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-body">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="nom_attendance" id="nom_attendance_<?= e($value['id_attendance'])?>" value="<?= e($value['nom_attendance'])?>" required>
                                                        <label for="nom_attendance_<?= e($value['id_attendance'])?>">Nom du mode</label>
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

                                <!-- Modal Supprimer -->
                                <div class="modal fade" id="delete_<?= e($value['id_attendance'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmer la suppression</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('Attendace_course_mode/DeleteAttendaceMode') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-body">
                                                    <p>Voulez-vous vraiment supprimer le mode "<?= e($value['nom_attendance'])?>" ?</p>
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
                                <th>Nom du Mode</th>
                                <th>Actions</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter Mode -->
        <div class="modal fade" id="addAttendaceMode" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nouveau mode de présence</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?= base_url('Attendace_course_mode/CreateAttendaceMode') ?>" method="POST">
                        <div class="modal-body">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="nom_attendance" id="nom_attendance" required>
                                <label for="nom_attendance">Nom du mode</label>
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




Développement Web
Design Graphique
Gestion de Projet
Cybersécurité
Certifications