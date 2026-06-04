<?php include VIEWPATH.'media/Header.php' ;?>
<?php include VIEWPATH.'media/navbar.php' ;?>

    <div class="hero-bg" style="background-image: url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>')">
  <div class="hero-body text-center">
    <h1 class="hero-tete">About-us</h1>
    <p class="hero-descr">Home/About-us</p>
  </div>
</div>



<section class="content-section-aboutus p-5">
    <div class="row">

        <div class="col-lg-4 d-flex">
            
            <img style="width: 400px; height: auto; object-fit: hover;" src="<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>">
        </div>

<div class="col-lg-8 ps-lg-5">

    <?php foreach ($about_us as $about) { ?>
    <p class="about-us-link text-center" style="font-size: 13px; color: #ff9900">About Us</p>
    <h2 class="company-name text-center"><?= e($about['title'])?></h2>

    <div class="company-description ck-content"><?= $about['details']?>
    </div>
    <?php }; ?>
</div>

    </div>
</section>



<section class="mission-vision py-5">
  <div class="container">

    <!-- Titre -->
    <div class="text-center mb-5">
      <h2 class="section-heading">Notre Mission & Vision</h2>
      
    </div>

    <div class="row g-4">

      <!-- Mission -->
      <?php foreach ($mission as $m) { ?>
        
     
      <div class="col-lg-6">
        <div class="mv-card h-100">
          <div class="mv-icon">
            <i class="fas fa-bullseye"></i>
          </div>
          <h3>Notre Mission</h3>
          <p><?= e($m['content'])?>
          </p>
        </div>
      </div>
<?php  } ?>


      <!-- Vision -->
      <?php foreach ($vision as $v) { ?>
      <div class="col-lg-6">
        <div class="mv-card h-100">
          <div class="mv-icon">
            <i class="fas fa-eye"></i>
          </div>
          <h3>Notre Vision</h3>
          <p><?= e($v['content'])?>
          </p>
        </div>
      </div>

      <?php  } ?>

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

/* ===== HERO ===== */
.about-hero {
  background: linear-gradient(
      rgba(13, 110, 253, 0.85),
      rgba(13, 110, 253, 0.85)
    ),
    url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>') center/cover no-repeat;
  color: #fff;
  padding: 140px 0 100px;
}

.about-hero h1 {
  font-size: 3rem;
}

.about-hero .lead {
  opacity: 0.9;
}

/* ===== SECTION TITLES ===== */
.section-heading {
  font-weight: 700;
  color: var(--primary-color);
}

.section-subtitle {
  max-width: 700px;
  margin: auto;
  color: #6c757d;
}

/* ===== MISSION & VISION ===== */
.mission-vision {
  background: var(--light-color);
}

.mv-card {
  background: #fff;
  padding: 45px;
  border-radius: 16px;
  text-align: center;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
  transition: 0.4s ease;
}

.mv-card:hover {
  transform: translateY(-10px);
}

.mv-icon {
  width: 90px;
  height: 90px;
  background: linear-gradient(
    135deg,
    var(--primary-color),
    var(--secondary-color)
  );
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 25px;
}

.mv-icon i {
  font-size: 2.2rem;
  color: #fff;
}

.mv-card h3 {
  font-weight: 700;
  margin-bottom: 15px;
  color: var(--primary-color);
}

.mv-card p {
  color: #555;
  line-height: 1.8;
}

</style>


<?php include VIEWPATH.'media/Footer.php' ;?>