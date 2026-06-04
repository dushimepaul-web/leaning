<?php include VIEWPATH.'media/Header.php'; ?>
<?php include VIEWPATH.'media/navbar.php'; ?>

<div class="hero-bg" style="background-image: url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>')">
  <div class="hero-body text-center">
    <h1 class="hero-tete">Les cours de <?= e($categorie ? $categorie['nom_categories'] : '' )?></h1>
    <p class="hero-descr">Home/cours</p>
  </div>
</div>



<div class="container-fluid course-container px-3 px-md-4 px-lg-5">
    <div class="row justify-content-center">

        <?php if (!empty($cours)) : ?>
            <?php foreach ($cours as $c) : ?>

                <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex justify-content-center">
                    <div class="info-box-wrapper">
                        
                        <div class="elementor-image-box-img">
                            <a href="#">
                                <img class="h-100 w-100" src="<?= base_url($this->Model->get_setting('default_course_image', 'assets/images/abelab.png')) ?>"
                                     alt="<?= e($c['nom_course'] )?>">
                            </a>
                        </div>

                        <div class="info-box-subtitle">
                            <?= !empty($tc['date_debut']) && !empty($tc['date_defin'])
                                ? (new DateTime($tc['date_debut']))->diff(new DateTime($tc['date_defin']))->days . ' jours'
                                : 'Non spécifié'; ?> Training
                        </div>

                        <h4 class="info-box-title"><?= e($c['nom_course'] )?></h4>

                        <div class="info-box-inner">
                            <p class="text-ellipsis-3">
                                <?= substr(strip_tags($c['description']), 0, 200) ?>...
                                <a href="<?= base_url('Pages/Home/coursedetail/' . $c['uuid']) ?>">
                                    <strong>Learn more</strong>
                                </a>
                            </p>
                        </div>

                        <div class="wd-button-wrapper mt-auto">
                            <a class="btn" style="background-color: #000099; color: white;" href="<?= base_url('Pages/Home/coursedetail/' . $c['uuid']) ?>">
                                Register <span class="wd-btn-icon"><i class="fas fa-arrow-right"></i></span>
                            </a>
                        </div>

                    </div>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Aucun cours trouvé.</p>
        <?php endif; ?>

    </div>
</div>


<?php include VIEWPATH.'media/Footer.php'; ?>