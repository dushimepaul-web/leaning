<?php
include VIEWPATH.'media/Header.php';
include VIEWPATH.'media/navbar.php';

/* Sécurité : premier résultat */
$event = $events[0];
?>

<!-- HERO -->
<div class="hero-bg" style="background-image: url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>')">
    <div class="hero-body text-center text-white">
        <h1 class="hero-tete">Événement</h1>
        <p class="hero-descr">Accueil / Événement</p>
    </div>
</div>

<!-- CONTENU -->
<div class="container my-5">
    <div class="row g-5">

        <!-- ARTICLE -->
        <div class="col-lg-8">
            <article class="bg-white p-4 rounded shadow-sm border">

                <header class="mb-4">
                    <h1 class="fw-bold mb-2">
                        <?= htmlspecialchars($event['titre'], ENT_QUOTES, 'UTF-8') ?>
                    </h1>

                    <p class="text-muted small">
                        <i class="bi bi-calendar-event"></i>
                        Du <?= date('d M Y', strtotime($event['date_debut'])) ?>
                        au <?= date('d M Y', strtotime($event['date_fin'])) ?>
                        &nbsp;•&nbsp; <strong><?= htmlspecialchars($event['lieu']) ?></strong>
                    </p>
                </header>

                <!-- IMAGE -->
                <?php if (!empty($event['image'])): ?>
                  <div style="height: 500px; overflow: hidden; width: 100%;" class="rounded mb-4">
                    <img
                        src="<?= base_url('attachments/events/'.$event['image']) ?>"
                        class="img-fluid rounded mb-4 w-100 h-100"
                        alt="<?= htmlspecialchars($event['titre']) ?>"
                    >
                  </div>
                <?php endif; ?>

                <!-- CONTENU CKEDITOR -->
                <section class="post-content">
                    <?= $event['description']?>
                </section>

            </article>
        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top:100px">

                <div class="p-4 mb-4 bg-white border rounded shadow-sm">
                    <h5 class="fw-bold">Informations</h5>
                    <p class="mb-1"><strong>Mois :</strong> <?= e($event['mois'])?></p>
                    <p class="mb-1"><strong>Année :</strong> <?= e($event['annee'])?></p>
                    <p class="mb-0">
                        <strong>Type :</strong>
                        <?= e($event['est_en_ligne'] ? 'En ligne' : 'Présentiel')?>
                    </p>
                </div>

                <div class="p-4 bg-white border rounded shadow-sm">
                    <h5 class="fw-bold">Contact</h5>
                    <p class="small mb-1"><strong>Tél :</strong> <?= e($this->Model->get_setting('site_phone', '+257 68 86 39 45')) ?></p>
                    <p class="small mb-0"><strong>Email :</strong> <?= e($this->Model->get_setting('site_email', 'contact@abelab.com')) ?></p>
                </div>

            </div>
        </div>

    </div>
</div>

<?php include VIEWPATH.'media/Footer.php'; ?>
