<?php include VIEWPATH.'media/Header.php'; ?>
<?php include VIEWPATH.'media/navbar.php'; ?>

<div class="hero-bg" style="background-image: url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>')">
  <div class="hero-body text-center">
    <h1 class="hero-tete">Blog</h1>
    <p class="hero-descr">Home/Blog</p>
  </div>
</div>




<section class="py-5 bg-light">
  <div class="container">


    <!-- BOUCLE ÉVÉNEMENTS -->
    <?php if (!empty($events)): ?>
      <?php foreach ($events as $e): ?>
      <article class="blog-item mb-4">
        <div class="row g-0">
          <!-- IMAGE -->
          <div class="col-md-4">
            <div class="image-blog position-relative">
              <img src="<?= base_url('attachments/events/'.$e['image']) ?>" class="img-fluid h-100 object-fit-cover" alt="<?= e($e['titre'] )?>">
              <div class="blog-date text-center">
                <span class="day"><?= date('d', strtotime($e['date_debut'])) ?></span>
                <span class="month"><?= ucfirst(substr($e['mois'], 0, 3)) ?></span>
              </div>
            </div>
          </div>

          <!-- CONTENU -->
          <div class="col-md-8 p-4">
            <div class="d-flex justify-content-between align-items-center">
              <p class="small text-muted mb-1">
                <i class="bi bi-calendar"></i> <?= date('d M Y', strtotime($e['date_debut'])) ?>
              </p>
              <span class="badge bg-primary mt-2">Événement</span>
            </div>
            <h5 class="fw-bold"><?= e($e['titre'] )?></h5>
            <p class="text-muted"><?= strip_tags(substr($e['description'],0,500)) ?>...</p>
            <a href="<?= base_url('Pages/Blog/events_detail/'.$e['uuid']) ?>" class="btn btn-outline-primary btn-sm">
              Lire la suite
            </a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- BOUCLE NEWS / MEDIA -->
    <?php if (!empty($news_media)): ?>
      <?php foreach ($news_media as $new): ?>
      <article class="blog-item mb-4">
        <div class="row g-0">

          <div class="col-md-4">
           <div class="image-blog position-relative">
                <img src="<?= base_url('attachments/news_media/'.$new['image']) ?>" alt="<?= e($new['title'] )?>">
            <div class="blog-date text-center">
           <span class="day"><?= date('d', strtotime($new['date_insertion'])) ?></span>
          <span class="month"><?= date('M', strtotime($new['date_insertion'])) ?></span>
    </div>
  </div>
</div>

          <div class="col-md-8 p-4">
            <div class="d-flex justify-content-between align-items-center">
              <p class="small text-muted mb-1">Comité</p>
              <span class="badge bg-success mt-2">Actualité</span>
            </div>
            <h5 class="fw-bold"><?= e($new['title'] )?></h5>
            <p class="text-muted"><?= strip_tags(substr($new['details'],0,500)) ?>...</p>
            <a href="<?= base_url('Pages/Blog/News_detail/'.$new['uuid']) ?>" class="btn btn-outline-success btn-sm">
              Lire plus
            </a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>
</section>

<style>

.image-blog {
  position: relative;
  width: 100%;
  height: 250px; /* hauteur fixe pour toutes les images */
  overflow: hidden;
  border-radius: 12px;
}

.image-blog img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* remplissage sans déformation */
  transition: transform 0.3s ease;
}

.blog-date {
  position: absolute;
  top: 15px;
  left: 15px;
  background: #fff;
  color: #333;
  padding: 10px 14px;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 6px 15px rgba(0,0,0,0.25);
  min-width: 70px;
}

.blog-date .day {
  display: block;
  font-size: 1.8rem;
  font-weight: 700;
}

.blog-date .month {
  font-size: 0.9rem;
  text-transform: uppercase;
}

.blog-item {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 10px 25px rgba(0,0,0,0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.blog-item:hover img {
  transform: scale(1.05);
}

.blog-item:hover {
  transform: translateY(-5px);
  box-shadow: 0 18px 45px rgba(0,0,0,0.12);
}
</style>

<?php include VIEWPATH.'media/Footer.php'; ?>