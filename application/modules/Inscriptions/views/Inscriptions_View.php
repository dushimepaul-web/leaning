

php
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
                        <li class="breadcrumb-item active" aria-current="page">Inscriptions</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a class="btn btn-outline-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addInscription">
                        Nouvelle Inscription
                    </a>
                    <a class="btn btn-outline-success" href="<?= base_url('Inscriptions/ExportInscriptions'); ?>">
                        Exporter CSV
                    </a>
                </div>
            </div>
        </div>
        <!--end breadcrumb-->
        <hr/>




        <!-- Statistiques -->
        <div class="row">
            <div class="col-12 col-lg-3">
                <div class="card bg-primary bg-gradient">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-1 text-white">Total Inscriptions</p>
                                <h4 class="mb-0 text-white"><?= e($stats['total_inscriptions'])?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card bg-success bg-gradient">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-1 text-white">Paiements Validés</p>
                                <h4 class="mb-0 text-white"><?= e($stats['paid_inscriptions'])?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card bg-warning bg-gradient">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-1 text-white">Paiements En Attente</p>
                                <h4 class="mb-0 text-white"><?= e($stats['pending_inscriptions'])?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card bg-info bg-gradient">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-1 text-white">Email Verifie</p>
                                <h4 class="mb-0 text-white"><?= e($stats['email_confirmed'])?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Étudiant</th>
                                <th>Cours</th>
                                <th>Planning</th>
                                <th>Prix</th>
                                <th>Statut Paiement</th>
                                <th>Statut email</th>
                                <th>Date Inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($inscriptions as $value): ?>
                                <tr>
                                    <td><?= e($i++)?></td>
                                    <td><?= e($value['student_name'] ?? ($value['nom_student'] ?? 'N/A') . ' ' . ($value['prenom_student'] ?? ''))?></td>
                                    <td><?= e($value['course_name'] ?? ($value['nom_course'] ?? 'N/A'))?></td>
                                    <td>
                                        <?php 
                                        $schedule = '';
                                        if (isset($value['date_debut'])) {
                                            $schedule = date('d/m/Y', strtotime($value['date_debut']));
                                            if (isset($value['date_fin'])) {
                                                $schedule .= ' - ' . date('d/m/Y', strtotime($value['date_fin']));
                                            }
                                        }
                                        echo $schedule ?: 'N/A';
                                        ?>
                                    </td>
                                    <td><?= isset($value['price']) ? $value['price'] . ' €' : 'N/A'; ?></td>
                                    <td>
                                        <?php
                                        $payment_status = $value['status_payement'] ?? 'pending';
                                        $payment_text = '';
                                        $payment_class = '';
                                        
                                        switch($payment_status) {
                                            case 'paid':
                                                $payment_text = 'Payé';
                                                $payment_class = 'success';
                                                break;
                                            case 'pending':
                                                $payment_text = 'En attente';
                                                $payment_class = 'warning';
                                                break;
                                            case 'failed':
                                                $payment_text = 'Échoué';
                                                $payment_class = 'danger';
                                                break;
                                            default:
                                                $payment_text = $payment_status;
                                                $payment_class = 'secondary';
                                        }
                                        ?>
                                        <span class="badge bg-<?= e($payment_class)?>">
                                            <?= e($payment_text)?>
                                        </span>
                                        <?php if ($payment_status != 'paid'): ?>
                                            <button class="btn btn-sm btn-outline-success ms-1" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#markPaid_<?= e($value['id_inscription'])?>">
                                                ✓
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#Status_<?=e($value['id_inscription'])?>">
                                       <?=e($value['email_confirmed']==1?'Verified':'unverified')?>
                                    </a>
                                </td>
                                    <td><?= isset($value['date_insertion']) ? date('d/m/Y H:i', strtotime($value['date_insertion'])) : 'N/A'; ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
                                                Options
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item text-success" href="#" data-bs-toggle="modal" data-bs-target="#afficher_<?= e($value['id_inscription'])?>">
                                                    Afficher
                                                </a>
                                                <a class="dropdown-item text-info" href="#" data-bs-toggle="modal" data-bs-target="#update_<?= e($value['id_inscription'])?>">
                                                    Modifier
                                                </a>
                                                <a class="dropdown-item text-warning" href="<?= base_url('Inscriptions/GenerateInvoice/' . $value['uuid']); ?>">
                                                    Facture
                                                </a>
                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#delete_<?= e($value['id_inscription'])?>">
                                                    Supprimer
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Modifier -->
                                <div class="modal fade" id="update_<?= e($value['id_inscription'])?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Modifier l'inscription</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('Inscriptions/UpdateInscription') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-control" name="id_student" required>
                                                                    <option value="">Sélectionner un étudiant</option>
                                                                    <?php foreach ($students as $student): ?>
                                                                        <option value="<?= e($student['id_student'])?>" <?= isset($value['id_student']) && $student['id_student'] == $value['id_student'] ? 'selected' : ''; ?>>
                                                                            <?= e($student['fullname'] ?? ($student['nom'] ?? '') . ' ' . ($student['prenom'] ?? ''))?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <label>Étudiant</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-control" name="id_course" required>
                                                                    <option value="">Sélectionner un cours</option>
                                                                    <?php foreach ($courses as $course): ?>
                                                                        <option value="<?= e($course['id_course'])?>" <?= isset($value['id_course']) && $course['id_course'] == $value['id_course'] ? 'selected' : ''; ?>>
                                                                            <?= e($course['nom_course'])?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <label>Cours</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-control" name="id_timetable_course" required>
                                                                    <option value="">Sélectionner un planning</option>
                                                                    <?php foreach ($timetable_courses as $timetable): ?>
                                                                        <option value="<?= e($timetable['id_timetable_course'])?>" <?= isset($value['id_timetable_course']) && $timetable['id_timetable_course'] == $value['id_timetable_course'] ? 'selected' : ''; ?>>
                                                                            <?= ($timetable['nom_course'] ?? 'Cours') . ' - ' . ($timetable['localisation'] ?? 'Localisation'); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <label>Planning</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-control" name="id_attendance">
                                                                    <option value="">Mode de présence</option>
                                                                    <?php foreach ($attendance_modes as $mode): ?>
                                                                        <option value="<?= e($mode['id_attendance'])?>" <?= isset($value['id_attendance']) && $mode['id_attendance'] == $value['id_attendance'] ? 'selected' : ''; ?>>
                                                                            <?= e($mode['nom_attendance'])?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <label>Mode de présence</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-control" name="id_mode_payement">
                                                                    <option value="">Mode de paiement</option>
                                                                    <?php foreach ($payment_modes as $payment): ?>
                                                                        <option value="<?= e($payment['id_mode_payement'])?>" <?= isset($value['id_mode_payement']) && $payment['id_mode_payement'] == $value['id_mode_payement'] ? 'selected' : ''; ?>>
                                                                            <?= e($payment['description'] ?? ('Mode #' . $payment['id_mode_payement']))?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <label>Mode de paiement</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" name="your_country" value="<?= e($value['your_country'] ?? '')?>">
                                                                <label>Pays</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-control" name="invoice_type">
                                                                    <option value="individual" <?= isset($value['invoice_type']) && $value['invoice_type'] == 'individual' ? 'selected' : ''; ?>>Individuel</option>
                                                                    <option value="company" <?= isset($value['invoice_type']) && $value['invoice_type'] == 'company' ? 'selected' : ''; ?>>Entreprise</option>
                                                                </select>
                                                                <label>Type de facturation</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-control" name="status_payement">
                                                                    <option value="pending" <?= isset($value['status_payement']) && $value['status_payement'] == 'pending' ? 'selected' : ''; ?>>En attente</option>
                                                                    <option value="paid" <?= isset($value['status_payement']) && $value['status_payement'] == 'paid' ? 'selected' : ''; ?>>Payé</option>
                                                                    <option value="failed" <?= isset($value['status_payement']) && $value['status_payement'] == 'failed' ? 'selected' : ''; ?>>Échoué</option>
                                                                </select>
                                                                <label>Statut paiement</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-control" name="status_started_course">
                                                                    <option value="0" <?= isset($value['status_started_course']) && $value['status_started_course'] == 0 ? 'selected' : ''; ?>>Non commencé</option>
                                                                    <option value="1" <?= isset($value['status_started_course']) && $value['status_started_course'] == 1 ? 'selected' : ''; ?>>Commencé</option>
                                                                </select>
                                                                <label>Début du cours</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-control" name="status_ended_course">
                                                                    <option value="0" <?= isset($value['status_ended_course']) && $value['status_ended_course'] == 0 ? 'selected' : ''; ?>>En cours</option>
                                                                    <option value="1" <?= isset($value['status_ended_course']) && $value['status_ended_course'] == 1 ? 'selected' : ''; ?>>Terminé</option>
                                                                </select>
                                                                <label>Fin du cours</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Marquer comme Payé -->
                                <div class="modal fade" id="markPaid_<?= e($value['id_inscription'])?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="<?= base_url('Inscriptions/MarkAsPaid') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Marquer comme payé</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Confirmez-vous que le paiement a été reçu pour cette inscription?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-success">Confirmer le paiement</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Marquer comme Commencé -->
                                <div class="modal fade" id="markStarted_<?= e($value['id_inscription'])?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="<?= base_url('Inscriptions/MarkAsStarted') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Marquer comme commencé</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Confirmez-vous que le cours a commencé pour cet étudiant?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-info">Marquer comme commencé</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Marquer comme Terminé + Certificat -->
                                <div class="modal fade" id="markEnded_<?= e($value['id_inscription'])?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="<?= base_url('Inscriptions/MarkAsCompleted') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Marquer comme terminé</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Confirmez-vous que le cours est terminé pour cet étudiant ?</p>
                                                    <p class="text-success mb-0"><i class="bi bi-award"></i> Un certificat sera envoyé automatiquement.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-success">Terminer & envoyer le certificat</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Afficher -->
                                <div class="modal fade" id="afficher_<?= e($value['id_inscription'])?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Détails de l'inscription</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Informations étudiant</h6>
                                                        <p><strong>Nom:</strong> <?= e($value['student_name'] ?? 'N/A')?></p>
                                                        <p><strong>Pays:</strong> <?= e($value['your_country'] ?? 'N/A')?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Informations cours</h6>
                                                        <p><strong>Cours:</strong> <?= e($value['course_name'] ?? 'N/A')?></p>
                                                        <p><strong>Prix:</strong> <?= e($value['price'] ?? 'N/A')?> €</p>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Statuts</h6>
                                                        <p><strong>Paiement:</strong> 
                                                            <span class="badge bg-<?= e($payment_class)?>">
                                                                <?= e($payment_text)?>
                                                            </span>
                                                        </p>
                                                        <p><strong>Cours:</strong> 
                                                            <span class="badge bg-<?= e($status_class)?>">
                                                                <?= e($status_text)?>
                                                            </span>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Dates</h6>
                                                        <p><strong>Inscription:</strong> <?= isset($value['date_insertion']) ? date('d/m/Y H:i', strtotime($value['date_insertion'])) : 'N/A'; ?></p>
                                                        <p><strong>Type facturation:</strong> <?= ($value['invoice_type'] ?? 'individual') == 'individual' ? 'Individuel' : 'Entreprise'; ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Supprimer -->
                                <div class="modal fade" id="delete_<?= e($value['id_inscription'])?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="<?= base_url('Inscriptions/DeleteInscription') ?>" method="POST">
                                                <input type="hidden" name="uuid" value="<?= e($value['uuid'])?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Supprimer l'inscription</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Êtes-vous sûr de vouloir supprimer cette inscription? Cette action est irréversible.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
   
<!--end page wrapper -->

<!-- Modal Nouvelle Inscription -->
<div class="modal fade" id="addInscription" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Inscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('Inscriptions/CreateInscription') ?>" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-control" name="id_student" required>
                                    <option value="">Sélectionner un étudiant</option>
                                    <?php foreach ($students as $student): ?>
                                        <option value="<?= e($student['id_student'])?>">
                                            <?= e($student['fullname'] ?? ($student['nom'] ?? '') . ' ' . ($student['prenom'] ?? ''))?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Étudiant</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-control" name="id_course" required>
                                    <option value="">Sélectionner un cours</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?= e($course['id_course'])?>">
                                            <?= e($course['nom_course'])?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Cours</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-control" name="id_timetable_course" required>
                                    <option value="">Sélectionner un planning</option>
                                    <?php foreach ($timetable_courses as $timetable): ?>
                                        <option value="<?= e($timetable['id_timetable_course'])?>">
                                            <?= ($timetable['nom_course'] ?? 'Cours') . ' - ' . ($timetable['localisation'] ?? 'Localisation'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Planning</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-control" name="id_attendance">
                                    <option value="">Mode de présence</option>
                                    <?php foreach ($attendance_modes as $mode): ?>
                                        <option value="<?= e($mode['id_attendance'])?>">
                                            <?= e($mode['nom_attendance'])?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Mode de présence</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <select class="form-control" name="id_mode_payement">
                                    <option value="">Mode de paiement</option>
                                    <?php foreach ($payment_modes as $payment): ?>
                                        <option value="<?= e($payment['id_mode_payement'])?>">
                                            <?= e($payment['description'] ?? ('Mode #' . $payment['id_mode_payement']))?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Mode de paiement</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="your_country" required>
                                <label>Pays</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <select class="form-control" name="invoice_type">
                                    <option value="individual">Individuel</option>
                                    <option value="company">Entreprise</option>
                                </select>
                                <label>Type de facturation</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer l'inscription</button>
                </div>
            </form>
        </div>
    </div>
</div>
 </div>
</div>

<?php include VIEWPATH . 'includes/Footer.php'; ?>
