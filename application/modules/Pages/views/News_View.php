<?php
include VIEWPATH.'media/Header.php';
include VIEWPATH.'media/navbar.php';

/* Sécurité : on prend le premier résultat */
$event = $news_media[0];
?>

<!-- HERO -->
<div class="hero-bg" style="background-image: url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>')">
    <div class="hero-body text-center text-white">
        <h1 class="hero-tete">Actualite</h1>
        <p class="hero-descr">Accueil / Actualite</p>
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
                        <?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>
                    </h1>

                    <p class="text-muted small">
                        <i class="bi bi-calendar-event"></i>
                        <?= date('d M Y', strtotime($event['date_insertion'])) ?>
                        &nbsp;•&nbsp; Par <strong>Admin</strong>
                    </p>
                </header>

                <!-- IMAGE -->
                <div style="height: 500px; overflow: hidden; width: 100%;" class="rounded mb-4">
    <img
        src="<?= base_url('attachments/news_media/'.$event['image']) ?>"
        class="img-fluid w-100 h-100"
        style="object-fit: cover;"
        alt="<?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>"
    >
</div>

                <!-- CONTENU CKEDITOR -->
                <section class="post-content">
                    <?= $event['details']?>
                </section>

            </article>
        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top:100px">

                <div class="p-4 mb-4 bg-white border rounded shadow-sm">
                    <h5 class="fw-bold">À propos</h5>
                    <p class="text-muted small mb-0">
                        Les actualités et événements organisés par <?= e($this->Model->get_setting('site_name', 'AbeLab')) ?>.
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
