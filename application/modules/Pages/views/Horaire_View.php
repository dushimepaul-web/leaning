<?php include VIEWPATH.'media/Header.php'; ?>
<?php include VIEWPATH.'media/navbar.php'; ?>

<!-- HERO SECTION -->
<div class="hero-bg" style="    background-image: url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>');">
    <div class="hero-body text-center text-white py-5">
        <h1 class="hero-tete display-4 fw-bold">Calendrier des Formations</h1>
        <p class="hero-descr lead">Consultez les dates et lieux de nos prochaines sessions</p>
    </div>
</div>

<!-- CALENDRIER DES FORMATIONS -->
<div class="container my-5">

    <?php
    // Regrouper par cours puis par localisation
    $grouped = [];
    foreach ($timetables as $row) {
        $grouped[$row['id_course']]['nom_course'] = $row['nom_course'];
        $grouped[$row['id_course']]['localisations'][$row['localisation']][] = $row;
    }
    ?>

    <?php foreach ($grouped as $course): ?>
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0"><?= htmlspecialchars($course['nom_course'], ENT_QUOTES, 'UTF-8') ?></h4>
            </div>

            <div class="card-body">
                <div class="row">
                    <?php foreach ($course['localisations'] as $localisation => $items): ?>
                        <div class="col-lg-6 col-md-12 mb-4">
                            <h5 class="text-success mb-3"> <?= htmlspecialchars($localisation, ENT_QUOTES, 'UTF-8') ?></h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date début</th>
                                            <th>Date fin</th>
                                            <th>Prix</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= date('d/m/Y', strtotime($item['date_debut'])) ?></td>
                                                <td><?= date('d/m/Y', strtotime($item['date_defin'])) ?></td>
                                                <td><?= htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8') ?> FBU</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<?php include VIEWPATH.'media/Footer.php'; ?>
