<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>

<div class="page-wrapper">
<div class="page-content">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tableau de Bord</h1>
        <div class="d-flex">
            <span class="text-muted mr-3"><?= date('d/m/Y') ?></span>
            <a href="<?= base_url('dashboard/export') ?>" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-download"></i> Exporter
            </a>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="row">
        <!-- Étudiants -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Étudiants Totaux</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= e($total_students )?></div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success mr-2">
                                    <i class="fas fa-arrow-up"></i> <?= round(($total_students/($total_students+1))*100) ?>%
                                </span>
                                <span>Depuis début</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formateurs -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Formateurs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= e($total_teachers )?></div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span>Actifs: <?= e($total_teachers )?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cours -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Cours Disponibles
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?= e($total_courses )?></div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: <?= ($total_courses/10)*100 ?>%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-info mr-2"><?= count($courses_by_category) ?> catégories</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inscriptions -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Inscriptions Totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= e($total_inscriptions )?></div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success mr-2">Payées: <?= e($paid_inscriptions )?></span>
                                <span class="text-danger">En attente: <?= e($pending_inscriptions )?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deuxième ligne de cartes -->
    <div class="row">
        <!-- Validation Email -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-purple shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-purple text-uppercase mb-1">
                                Validation Email</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= e($email_confirmed )?></div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success mr-2">Validés: <?= e($email_confirmed )?></span>
                                <span class="text-warning">Non validés: <?= e($email_not_confirmed )?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emails Envoyés -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-indigo shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-indigo text-uppercase mb-1">
                                Emails Envoyés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= e($email_total_sent )?></div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success mr-2">Succès: <?= e($email_success )?></span>
                                <span class="text-danger">Échecs: <?= e($email_failed )?></span>
                            </div>
                            <div class="mt-1 mb-0 text-muted text-xs">
                                <span>Aujourd'hui: <?= e($email_today )?> • Ce mois: <?= e($email_month )?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mail-bulk fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Taux de Conversion -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-orange shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-orange text-uppercase mb-1">
                                Taux de Conversion</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= e($total_students > 0 ? round(($paid_inscriptions/$total_students)*100, 1) : 0 )?>%
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span>Inscriptions → Paiements</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-orange text-uppercase mb-1">
                                Revenue Estimé</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($paid_inscriptions * 50, 0, ',', ' ') ?> BIF
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span>Basé sur prix moyen 50$</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Satisfaction -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-teal shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-teal text-uppercase mb-1">
                                Taux Satisfaction</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">4.5/5</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span>Basé sur témoignages</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et tableaux -->
    <div class="row">
        <!-- Graphique en anneau - Paiements -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Statut des Paiements</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Actions:</div>
                            <a class="dropdown-item" href="<?= base_url('inscriptions') ?>">Voir toutes</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="refreshChart('payment')">
                                <i class="fas fa-sync-alt fa-sm"></i> Actualiser
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="paymentPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Payés (<?= e($paid_inscriptions )?>)
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> En attente (<?= e($pending_inscriptions )?>)
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Échoués (<?= e($failed_inscriptions ?? 0 )?>)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique à barres - Inscriptions par mois -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Évolution des Inscriptions (<?= date('Y') ?>)</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Période:</div>
                            <a class="dropdown-item" href="#" onclick="changePeriod('month')">Mensuel</a>
                            <a class="dropdown-item" href="#" onclick="changePeriod('quarter')">Trimestriel</a>
                            <a class="dropdown-item" href="#" onclick="changePeriod('year')">Annuel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="inscriptionsBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableaux -->
    <div class="row">
        <!-- Dernières inscriptions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dernières Inscriptions</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Étudiant</th>
                                    <th>Cours</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_inscriptions)): ?>
                                    <?php foreach($recent_inscriptions as $inscription): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                                        <span class="text-white"><?= strtoupper(substr($inscription->fullname, 0, 1)) ?></span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold"><?= htmlspecialchars($inscription->fullname) ?></div>
                                                    <div class="text-muted small"><?= htmlspecialchars($inscription->email) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($inscription->nom_course) ?></td>
                                        <td>
                                            <?php 
                                            $status_class = '';
                                            switch($inscription->status_payement) {
                                                case 'paid': $status_class = 'text-success'; break;
                                                case 'pending': $status_class = 'text-warning'; break;
                                                default: $status_class = 'text-danger';
                                            }
                                            ?>
                                            <span class="badge <?= e($status_class )?>">
                                                <?= ucfirst($inscription->status_payement) ?>
                                            </span>
                                        </td>

                                        <td class="text-muted small">
                                            <?= date('d/m/Y', strtotime($inscription->date_insertion)) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Aucune inscription récente</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="<?= base_url('inscriptions') ?>" class="btn btn-sm btn-outline-primary btn-block">
                        Voir toutes les inscriptions
                    </a>
                </div>
            </div>
        </div>

        <!-- Top Cours -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cours les plus Populaires</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($top_courses)): ?>
                        <?php foreach($top_courses as $index => $course): ?>
                        <div class="row align-items-center mb-3">
                            <div class="col-2 text-right">
                                <span class="badge badge-primary badge-pill"><?= e($index + 1 )?></span>
                            </div>
                            <div class="col-7">
                                <div class="progress-wrapper">
                                    <div class="progress-info">
                                        <div class="progress-label">
                                            <span class="font-weight-bold"><?= htmlspecialchars($course->nom_course) ?></span>
                                        </div>
                                        <div class="progress-percentage">
                                            <span><?= e($course->inscription_count )?> inscrits</span>
                                        </div>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-<?= ['primary','success','info','warning','danger'][$index % 5] ?>" 
                                             role="progressbar" 
                                             style="width: <?= ($course->inscription_count/max(array_column($top_courses, 'inscription_count')))*100 ?>%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3 text-right">
                                <span class="badge badge-light">
                                    <?= round(($course->inscription_count/$total_inscriptions)*100, 1) ?>%
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted">Aucun cours avec inscriptions</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations supplémentaires -->
    <div class="row">
        <!-- Répartition par pays -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Étudiants par Pays</h6>
                </div>
                <div class="card-body">
                    <?php 
                    $countries = $this->Model->get_students_by_country();
                    if (!empty($countries)):
                    ?>
                        <?php foreach($countries as $country): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <i class="fas fa-globe-africa text-primary mr-2"></i>
                                <span><?= htmlspecialchars($country->your_country ?: 'Non spécifié') ?></span>
                            </div>
                            <span class="badge badge-primary badge-pill"><?= e($country->student_count )?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted">Aucune donnée de pays disponible</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        

        <!-- Top Formateurs -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Formateurs</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($top_teachers)): ?>
                        <?php foreach($top_teachers as $index => $teacher): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <div class="avatar-sm bg-<?= ['primary','success','info','warning','danger'][$index % 5] ?> 
                                          rounded-circle d-flex align-items-center justify-content-center">
                                    <span class="text-white"><?= strtoupper(substr($teacher->teacher_name, 0, 1)) ?></span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold"><?= htmlspecialchars($teacher->teacher_name) ?></div>
                                <div class="text-muted small">
                                    <?= e($teacher->student_count )?> étudiants • <?= e($teacher->course_count )?> cours
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-light">Top <?= e($index + 1 )?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted">Aucun formateur avec étudiants</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

<!-- Scripts pour les graphiques -->
<script src="<?= base_url('assets/vendor/chart.js/chart.min.js') ?>"></script>

<script>
// Graphique en anneau - Paiements
var paymentCtx = document.getElementById('paymentPieChart').getContext('2d');
var paymentChart = new Chart(paymentCtx, {
    type: 'doughnut',
    data: {
        labels: ['Payés', 'En attente', 'Échoués'],
        datasets: [{
            data: [<?= e($paid_inscriptions )?>, <?= e($pending_inscriptions )?>, <?= e($failed_inscriptions ?? 0 )?>],
            backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
            hoverBackgroundColor: ['#17a673', '#dda20a', '#be2617'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Graphique à barres - Inscriptions par mois
var barCtx = document.getElementById('inscriptionsBarChart').getContext('2d');
var barChart = new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: [
            <?php if (!empty($inscriptions_by_month)): ?>
                <?php foreach($inscriptions_by_month as $month): ?>
                    '<?= substr($month->month, 0, 3) ?>',
                <?php endforeach; ?>
            <?php endif; ?>
        ],
        datasets: [{
            label: 'Inscriptions',
            data: [
                <?php if (!empty($inscriptions_by_month)): ?>
                    <?php foreach($inscriptions_by_month as $month): ?>
                        <?= e($month->count )?>,
                    <?php endforeach; ?>
                <?php endif; ?>
            ],
            backgroundColor: 'rgba(78, 115, 223, 0.8)',
            borderColor: 'rgba(78, 115, 223, 1)',
            borderWidth: 1
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Graphique radar - Modes de présence
<?php if (!empty($attendance_mode_stats)): ?>
var attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
var attendanceChart = new Chart(attendanceCtx, {
    type: 'radar',
    data: {
        labels: [
            <?php foreach($attendance_mode_stats as $mode): ?>
                '<?= e($mode->nom_attendance )?>',
            <?php endforeach; ?>
        ],
        datasets: [{
            label: 'Inscriptions',
            data: [
                <?php foreach($attendance_mode_stats as $mode): ?>
                    <?= e($mode->count )?>,
                <?php endforeach; ?>
            ],
            backgroundColor: 'rgba(54, 185, 204, 0.2)',
            borderColor: 'rgba(54, 185, 204, 1)',
            pointBackgroundColor: 'rgba(54, 185, 204, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(54, 185, 204, 1)'
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            r: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
<?php endif; ?>

// Fonctions utilitaires
function refreshChart(chartType) {
    fetch('<?= base_url("dashboard/get_chart_data") ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualiser les graphiques avec les nouvelles données
                // (À implémenter selon vos besoins)
                location.reload();
            }
        });
}

function changePeriod(period) {
    // Changer la période des graphiques
    // (À implémenter selon vos besoins)
    console.log('Changer période:', period);
}

// Actualiser automatiquement toutes les 5 minutes
setTimeout(function() {
    location.reload();
}, 5 * 60 * 1000);
</script>
<script src="<?= base_url() ?>assets/admin/js/index.js"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script src="<?=base_url()?>assets/admin/js/bootstrap.bundle.min.js"></script>



<style>
.avatar-sm {
    width: 36px;
    height: 36px;
    line-height: 36px;
    font-size: 14px;
}

.border-left-purple {
    border-left: .25rem solid #6f42c1!important;
}

.border-left-indigo {
    border-left: .25rem solid #6610f2!important;
}

.border-left-orange {
    border-left: .25rem solid #fd7e14!important;
}

.border-left-teal {
    border-left: .25rem solid #20c997!important;
}

.text-purple {
    color: #6f42c1!important;
}

.text-indigo {
    color: #6610f2!important;
}

.text-orange {
    color: #fd7e14!important;
}

.text-teal {
    color: #20c997!important;
}

.progress-wrapper {
    margin-bottom: 1rem;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.progress-label {
    font-size: 0.875rem;
}

.progress-percentage {
    font-size: 0.75rem;
    color: #6c757d;
}

.card {
    border-radius: 10px;
    overflow: hidden;
}

.card-header {
    border-bottom: 1px solid #e3e6f0;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #5a5c69;
}

.table td {
    vertical-align: middle;
}

.badge-pill {
    padding-right: 0.6em;
    padding-left: 0.6em;
}





/* dashboard.css */
:root {
    --primary-color: #4e73df;
    --success-color: #1cc88a;
    --info-color: #36b9cc;
    --warning-color: #f6c23e;
    --danger-color: #e74a3b;
    --secondary-color: #858796;
    --light-color: #f8f9fc;
    --dark-color: #5a5c69;
}

.card-stat {
    border-radius: 10px;
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    transition: all 0.3s ease;
}

.card-stat:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
}

.stat-icon {
    font-size: 2.5rem;
    opacity: 0.3;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
}

.stat-label {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-change {
    font-size: 0.75rem;
}

.chart-container {
    position: relative;
    height: 300px;
}

.mini-chart {
    height: 100px;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
}

.bg-gradient-primary {
    background: linear-gradient(87deg, #4e73df 0, #224abe 100%) !important;
}

.bg-gradient-success {
    background: linear-gradient(87deg, #1cc88a 0, #13855c 100%) !important;
}

.bg-gradient-info {
    background: linear-gradient(87deg, #36b9cc 0, #258391 100%) !important;
}

.bg-gradient-warning {
    background: linear-gradient(87deg, #f6c23e 0, #dda20a 100%) !important;
}

.progress-sm {
    height: 0.5rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.refresh-btn:hover .loading-spinner {
    animation: spin 0.5s linear infinite;
}
</style>