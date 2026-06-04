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
                        <li class="breadcrumb-item active" aria-current="page">Planning des Cours</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addTimetableCourse">
                    Nouvelle Planification
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
                                <th>Cours</th>
                                <th>Emploi du temps</th>
                                <th>Localisation</th>
                                <th>Prix</th>
                                <th>Date d'insertion</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($timetable_courses as $value): ?>
                                <tr>
                                    <td><?= e($i++)?></td>
                                    <td><?= e($value['nom_course'])?></td>
                                    <td><?= e($value['date_debut'])?></td>
                                    <td><?= e($value['localisation'])?></td>
                                    <td><?= e($value['price'])?> €</td>
                                    <td><?= date('d/m/Y H:i', strtotime($value['date_insertion'])); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
                                                Options
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item text-success" href="#" data-bs-toggle="modal" data-bs-target="#afficher_<?= e($value['id_timetable_course'])?>">
                                                    Afficher
                                                </a>
                                                <a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#update_<?= e($value['id_timetable_course'])?>">
                                                    Modifier
                                                </a>
                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?= e($value['id_timetable_course'])?>">
                                                    Supprimer
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Modifier -->
                                <div class="modal fade" id="update_<?= e($value['id_timetable_course'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier la planification</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('Timetable_courses/UpdateTimetableCourse') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-control" name="id_course" required>
                                                                    <option value="">Sélectionner un cours</option>
                                                                    <?php foreach ($courses as $course): ?>
                                                                        <option value="<?= e($course['id_course'])?>" <?= e($course['id_course'] == $value['id_course'] ? 'selected' : '')?>>
                                                                            <?= e($course['nom_course'])?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <label>Cours</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-control" name="id_timetable" required>
                                                                    <option value="">Sélectionner un emploi du temps</option>
                                                                    <?php foreach ($timetables as $timetable): ?>
                                                                        <option value="<?= e($timetable['id_timetable'])?>" <?= e($timetable['id_timetable'] == $value['id_timetable'] ? 'selected' : '')?>>
                                                                            <?= e($timetable['nom_timetable'] ?? 'Emploi du temps #' . $timetable['id_timetable'])?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <label>Emploi du temps</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" name="localisation" value="<?= e($value['localisation'])?>" required>
                                                                <label>Localisation</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" name="price" value="<?= e($value['price'])?>" required>
                                                                <label>Prix (€)</label>
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

                                <!-- MODAL DETAIL -->
                                <div class="modal fade" id="afficher_<?= e($value['id_timetable_course'] )?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myLargeModalLabel">Détails de la planification</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>Cours :</strong> <?= e($value['nom_course'])?></p>
                                                        <p><strong>Emploi du temps :</strong> <?= e($value['nom_timetable'] ?? 'Emploi du temps #' . $value['id_timetable'])?></p>
                                                        <p><strong>Localisation :</strong> <?= e($value['localisation'])?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Prix :</strong> <?= e($value['price'])?> €</p>
                                                        <p><strong>Date d'insertion :</strong> <?= date('d/m/Y H:i', strtotime($value['date_insertion'])); ?></p>
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
                                <div class="modal fade" id="delete_<?= e($value['id_timetable_course'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmer la suppression</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('Timetable_courses/DeleteTimetableCourse') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-body">
                                                    <p>Voulez-vous vraiment supprimer la planification du cours "<?= e($value['nom_course'])?>" ?</p>
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
                                <th>Cours</th>
                                <th>Emploi du temps</th>
                                <th>Localisation</th>
                                <th>Prix</th>
                                <th>Date d'insertion</th>
                                <th>Actions</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter Planification -->
        <div class="modal fade" id="addTimetableCourse" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nouvelle planification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?= base_url('Timetable_courses/CreateTimetableCourse') ?>" method="POST">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-control" name="id_course" required>
                                            <option value="">Sélectionner un cours</option>
                                            <?php foreach ($courses as $course): ?>
                                                <option value="<?= e($course['id_course'])?>"><?= e($course['nom_course'])?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label>Cours</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-control" name="id_timetable" required>
                                            <option value="">Sélectionner un emploi du temps</option>
                                            <?php foreach ($timetables as $timetable): ?>
                                                <option value="<?= e($timetable['id_timetable'])?>">
                                                    <?= e($timetable['date_debut'] ?? 'Emploi du temps #' . $timetable['id_timetable'])?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label>Emploi du temps</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="localisation" required>
                                        <label>Localisation</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="price" required>
                                        <label>Prix (€)</label>
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