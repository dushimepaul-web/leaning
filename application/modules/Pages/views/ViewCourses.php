<?php include VIEWPATH.'media/Header.php'; ?>
<?php include VIEWPATH.'media/navbar.php'; ?>

<div class="hero-bg" style="background-image: url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>')">
  <div class="hero-body text-center">
    <h1 class="hero-tete">Les cours de <?= e($categorie ? $categorie['nom_categories'] : '' )?></h1>
    <p class="hero-descr">Home/cours</p>
  </div>
</div>



<div class="container-fluid course-container px-3 px-md-4 px-lg-5 py-4">
    <div class="row justify-content-center g-4">

        <?php if (!empty($cours)) : ?>
            <?php foreach ($cours as $c) : ?>

                <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                    <div class="info-box-wrapper card border-0 shadow-sm rounded-4 overflow-hidden w-100 transition-hover">

                        <div class="elementor-image-box-img" style="height: 200px; overflow: hidden;">
                            <a href="#">
                                <?php if (!empty($c['image'])): ?>
                                <img class="w-100 h-100" src="<?= base_url('attachments/Courses/' . $c['image']) ?>"
                                     style="object-fit: cover;"
                                     alt="<?= e($c['nom_course'] )?>">
                                <?php else: ?>
                                <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-muted">
                                    <i class="fas fa-image fa-3x"></i>
                                </div>
                                <?php endif; ?>
                            </a>
                        </div>

                        <div class="card-body d-flex flex-column p-3 p-md-4">
                            <span class="badge bg-primary bg-opacity-10 text-primary align-self-start mb-2 px-3 py-1">
                                <i class="fas fa-clock me-1"></i> Formation
                            </span>

                            <h4 class="info-box-title fw-bold h5 mt-2"><?= e($c['nom_course'] )?></h4>

                            <div class="info-box-inner flex-grow-1">
                                <p class="text-muted small text-ellipsis-3 mb-0">
                                    <?= substr(strip_tags($c['description']), 0, 200) ?>...
                                </p>
                            </div>

                            <div class="mt-3 pt-3 border-top">
                                <a href="<?= base_url('Pages/Home/coursedetail/' . $c['uuid']) ?>"
                                   class="btn w-100"
                                   style="background-color: #000099; color: white; border-radius: 50px;">
                                    Register <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <p class="text-muted">Aucun cours trouvé.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<style>
.info-box-wrapper { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.info-box-wrapper:hover { transform: translateY(-4px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important; }
.text-ellipsis-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>


<?php include VIEWPATH.'media/Footer.php'; ?>