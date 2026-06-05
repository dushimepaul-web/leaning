<?php include VIEWPATH.'media/Header.php'; ?>
<?php include VIEWPATH.'media/navbar.php'; ?>

<!-- HERO SECTION -->
<div class="hero-bg" style="background-image: url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>');">
    <div class="hero-body text-center text-white py-5">
        <h1 class="hero-tete display-4 fw-bold">Calendrier des Formations</h1>
        <p class="hero-descr lead">Consultez les dates et lieux de nos prochaines sessions</p>
    </div>
</div>

<!-- CALENDRIER DES FORMATIONS -->
<div class="container my-5">

    <?php
    $grouped = [];
    foreach ($timetables as $row) {
        $grouped[$row['id_course']]['nom_course'] = $row['nom_course'];
        $grouped[$row['id_course']]['localisations'][$row['localisation']][] = $row;
    }
    ?>

    <?php foreach ($grouped as $course): ?>
        <div class="card mb-5 border-0 shadow rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-white text-center py-3 border-0">
                <h4 class="mb-0 fw-semibold">
                    <i class="fas fa-graduation-cap me-2"></i>
                    <?= htmlspecialchars($course['nom_course'], ENT_QUOTES, 'UTF-8') ?>
                </h4>
            </div>

            <div class="card-body bg-white p-3 p-md-4">
                <div class="row g-4">
                    <?php foreach ($course['localisations'] as $localisation => $items): ?>
                        <?php
                        $hasPrice = false;
                        foreach ($items as $item) {
                            if (!empty($item['price'])) { $hasPrice = true; break; }
                        }
                        ?>
                        <div class="col-12 col-lg-6">
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-success h-100">
                                <h5 class="text-success fw-bold mb-3">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?= htmlspecialchars($localisation, ENT_QUOTES, 'UTF-8') ?>
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="py-2">Date début</th>
                                                <th class="py-2">Date fin</th>
                                                <?php if ($hasPrice): ?><th class="py-2">Prix</th><?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($items as $item): ?>
                                                <tr>
                                                    <td class="py-2">
                                                        <i class="far fa-calendar-alt text-primary me-1"></i>
                                                        <?= date('d/m/Y', strtotime($item['date_debut'])) ?>
                                                    </td>
                                                    <td class="py-2">
                                                        <i class="far fa-calendar-check text-primary me-1"></i>
                                                        <?= date('d/m/Y', strtotime($item['date_defin'])) ?>
                                                    </td>
                                                    <?php if ($hasPrice): ?>
                                                        <td class="py-2 fw-semibold">
                                                            <?= htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8') ?> FBU
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<?php include VIEWPATH.'media/Footer.php'; ?>
