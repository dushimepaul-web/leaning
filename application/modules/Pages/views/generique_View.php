<?php include VIEWPATH.'media/Header.php' ;?>
<?php include VIEWPATH.'media/navbar.php' ;?>

<div class="hero-bg" style="background-image: url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>')">
  <div class="hero-body text-center">
    <h1 class="hero-tete"><?= isset($title) ? e($title) : 'Institution' ?></h1>
    <p class="hero-descr">Home/<?= isset($title) ? e($title) : 'Contenu' ?></p>
  </div>
</div>

<section class="content-section py-5">
    <div class="container">
        <div class="row g-3 g-lg-4">
            <?php 
            $totalContents = count($contents);
            foreach ($contents as $content) { 
                // Classes responsives :
                // - Mobile (xs) : 100% (col-12)
                // - Tablette (sm/md) : 100% (col-12) 
                // - Desktop (lg) : 50% si plus d'1 contenu, sinon 100%
                if ($totalContents == 1) {
                    $columnClass = 'col-12';
                } else {
                    $columnClass = 'col-12 col-md-12 col-lg-6';
                }
            ?>
            <div class="<?= $columnClass ?>">
                <div class="content-card h-100">
                    <h3 class="content-title"><?= e($content['Title']) ?></h3>
                    <div class="content-body ck-content"><?= $content['Description'] ?></div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</section>

<style>
 :root {
  --primary-color: #0d6efd;
  --secondary-color: #0a58ca;
  --accent-color: #198754;
  --light-color: #f8f9fa;
}

/* ===== HERO RESPONSIVE ===== */
.hero-bg {
  background: linear-gradient(
      rgba(13, 110, 253, 0.85),
      rgba(13, 110, 253, 0.85)
    ),
    url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>') center/cover no-repeat;
  color: #fff;
  padding: 100px 0 80px;
}

@media (min-width: 768px) {
  .hero-bg {
    padding: 140px 0 100px;
  }
}

.hero-tete {
  font-size: 1.8rem;
  font-weight: bold;
}

@media (min-width: 768px) {
  .hero-tete {
    font-size: 2.5rem;
  }
}

@media (min-width: 992px) {
  .hero-tete {
    font-size: 3rem;
  }
}

.hero-descr {
  opacity: 0.9;
  font-size: 0.9rem;
}

@media (min-width: 768px) {
  .hero-descr {
    font-size: 1rem;
  }
}

/* ===== SECTION CONTENT RESPONSIVE ===== */
.content-section {
  background: var(--light-color);
  padding: 40px 0;
}

@media (min-width: 768px) {
  .content-section {
    padding: 60px 0;
  }
}

@media (min-width: 992px) {
  .content-section {
    padding: 80px 0;
  }
}

.content-card {
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  transition: 0.4s ease;
  border-left: 4px solid var(--primary-color);
  height: 100%;
}

@media (min-width: 768px) {
  .content-card {
    padding: 30px;
  }
}

@media (min-width: 992px) {
  .content-card {
    padding: 40px;
  }
}

.content-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
}

.content-title {
  font-weight: 700;
  color: var(--primary-color);
  margin-bottom: 15px;
  font-size: 1.2rem;
}

@media (min-width: 768px) {
  .content-title {
    margin-bottom: 20px;
    font-size: 1.3rem;
  }
}

.content-body {
  color: #555;
  line-height: 1.6;
  font-size: 0.9rem;
}

@media (min-width: 768px) {
  .content-body {
    line-height: 1.8;
    font-size: 1rem;
  }
}

.ck-content {
  word-wrap: break-word;
}

.ck-content img {
  max-width: 100%;
  height: auto;
}

/* Ajustements pour très petits écrans */
@media (max-width: 576px) {
  .content-card {
    padding: 20px;
  }
  
  .content-title {
    font-size: 1.1rem;
  }
  
  .content-body {
    font-size: 0.85rem;
  }
}

/* Gouttières responsives */
.row.g-3 {
  --bs-gutter-y: 1rem;
  --bs-gutter-x: 1rem;
}

@media (min-width: 992px) {
  .row.g-lg-4 {
    --bs-gutter-y: 1.5rem;
    --bs-gutter-x: 1.5rem;
  }
}
</style>

<?php include VIEWPATH.'media/Footer.php' ;?>