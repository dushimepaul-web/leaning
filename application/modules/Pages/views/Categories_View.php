<?php include VIEWPATH.'media/Header.php' ;?>
<?php include VIEWPATH.'media/navbar.php' ;?>


 <div class="hero-bg" style="background-image: url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>')">
  <div class="hero-body text-center">
    <h1 class="hero-tete">Catégories des cours</h1>
    <p class="hero-descr">Home/Categories</p>
  </div>
</div>
<!-- Section Categories des cours -->

<section class="container py-5">
    <h1 class="text-center mb-5">Catégories des cours</h1>
    
    <div class="row g-4">
        <?php foreach ($categories as $categorie) { ?>
        <div class="col-sm-6 col-md-4 col-lg-3 d-flex">
            <div class="card course-card w-100 shadow-sm">
                <img src="<?=base_url('attachments/Categorie/'.$categorie['Image']) ?>" class="card-img-top" alt="<?= e($categorie['nom_categories'] )?>">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= e($categorie['nom_categories'])?></h5>
                    <a href="<?= base_url('Pages/Home/viewcourses/' . $categorie['uuid']) ?>" class="btn btn-primary mt-auto view-course-link">
                        Consultez les cours <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                    <div class="stats mt-3 d-flex justify-content-between text-muted small">
                        <span><i class="fas fa-book-open me-1"></i> 22 cours</span>
                        <span><i class="fas fa-clock me-1"></i> 65h</span>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</section>



<?php include VIEWPATH.'media/Footer.php' ;?>