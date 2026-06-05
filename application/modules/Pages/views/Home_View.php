<?php include VIEWPATH.'media/Header.php' ;?>
<?php include VIEWPATH.'media/navbar.php' ;?>

<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <?php 
    $indicator_count = 0;
    foreach ($carousels as $carousel) { 
    ?>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="<?= e($indicator_count )?>" 
            class="<?= e($indicator_count === 0 ? 'active' : '' )?>" 
            aria-current="<?= e($indicator_count === 0 ? 'true' : 'false' )?>" 
            aria-label="Slide <?= e($indicator_count + 1 )?>"></button>
    <?php 
      $indicator_count++;
    } 
    ?>
  </div>
  
  <div class="carousel-inner">
    <?php 
    $is_first = true;
    foreach ($carousels as $carousel) { 
    ?>
    <div class="carousel-item <?= e($is_first ? 'active' : '' )?>">
      <!-- Image avec hauteur fixe de 400px -->
      <div style="height: 300px; width:auto; overflow: hidden; position: relative;" class="carousel-img-wrap">
        <img src="<?= htmlspecialchars(base_url('attachments/Carousel/'.$carousel['Image'])) ?>" 
             class="d-block w-100 h-100 object-fit-cover" 
             alt="<?= htmlspecialchars($carousel['Title']) ?>"
             style="object-fit: cover; width: 100%; height: 100%;">
      </div>

      <div class="carousel-caption d-none d-sm-block text-end pb-3 pb-md-4">
        <h5 class="display-6 fw-bold"><?= htmlspecialchars($carousel['Title']) ?></h5>
        <p class="lead mb-4"><?= e($carousel['Description'] )?></p>
        
        <!-- Boutons alignés à droite -->
        <div class="d-flex justify-content-end gap-3 mt-4">
          <a style="background-color:#000099; color: white;" href="<?= base_url('Pages/Home/Categorie') ?>" class="btn rounded-pill px-3 px-md-4 py-2">
            Explorer les cours
          </a>
          <a style="background-color:#000099; color: white;" href="<?= base_url('Pages/About_us/contact') ?>" class="btn rounded-pill px-3 px-md-4 py-2">
            Demande une formation
          </a>
        </div>
      </div>
    </div>
    <?php 
    $is_first = false;
    } 
    ?>
  </div>
  
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

<!-- Mobile buttons (visible only on xs) -->
<div class="d-sm-none text-center py-3 px-3">
  <a style="background-color:#000099; color: white;" href="<?= base_url('Pages/Home/Categorie') ?>" class="btn rounded-pill px-4 py-2 mb-2 w-100">
    Explorer les cours
  </a>
  <a style="background-color:#000099; color: white;" href="<?= base_url('Pages/About_us/contact') ?>" class="btn rounded-pill px-4 py-2 w-100">
    Demande une formation
  </a>
</div>








<section class="content-section-aboutus py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="row g-4 align-items-center">

            <div class="col-lg-5 col-md-6 col-12">
                <div class="stats-wrapper">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="stat-card bg-white rounded-4 shadow-sm p-4 d-flex align-items-center">
                                <div class="stat-icon bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="fas fa-users text-primary fa-2x"></i>
                                </div>
                                <div>
                                    <h2 class="stat-value mb-0 fw-bold display-5">5000+</h2>
                                    <p class="stat-label mb-0 text-muted small">Professionnels Formés</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-card bg-white rounded-4 shadow-sm p-4 d-flex align-items-center">
                                <div class="stat-icon bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="fas fa-calendar-check text-primary fa-2x"></i>
                                </div>
                                <div>
                                    <h2 class="stat-value mb-0 fw-bold display-5">10+</h2>
                                    <p class="stat-label mb-0 text-muted small">Années d'Expérience</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-card bg-white rounded-4 shadow-sm p-4 d-flex align-items-center">
                                <div class="stat-icon bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="fas fa-globe-africa text-primary fa-2x"></i>
                                </div>
                                <div>
                                    <h2 class="stat-value mb-0 fw-bold display-5">30+</h2>
                                    <p class="stat-label mb-0 text-muted small">Pays</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 col-md-6 col-12 d-flex">
                <div class="bg-white rounded-4 shadow-sm p-4 p-md-5 w-100 d-flex flex-column">
                <?php foreach ($about_us as $about) { ?>
                <p class="mb-1" style="color: #ff9900; font-weight: 600;">
                    <i class="fas fa-info-circle me-1"></i>À Propos
                </p>
                <h2 class="fw-bold mb-3"><?= e($about['title'])?></h2>
                <div class="ck-content flex-grow-1" style="overflow-y: auto;">
                    <?php
                    $pos = strpos($about['details'], '<h3>Axes');
                    echo $pos !== false ? substr($about['details'], 0, $pos) : $about['details'];
                    ?>
                </div>
                <a href="<?= base_url('Pages/About_us') ?>" class="btn mt-3 rounded-pill px-4 align-self-start"
                   style="background-color: #000099; color: white;">
                    Lire plus <i class="fas fa-arrow-right ms-1"></i>
                </a>
                <?php } ?>
                </div>
            </div>

        </div>
    </div>
</section>







 


<section class="content-section py-5">
  <div class="container-fluid">
    <div class="text-center mb-5">
      <h5 class="text-uppercase fw-bold" style="color: #000099;">Formation vedette</h5>
      <h2 class="fw-bold">Upcoming Training Courses / Workshops</h2>
    </div>
    <div class="row g-4">
      <!-- Événements -->
      <div class="col-lg-5 col-12">
        <?php if (!empty($events_by_month)): ?>
          <?php foreach ($events_by_month as $month => $events): ?>
            <h5 class="month-title fw-semibold text-primary mt-4 mb-3">
              <i class="far fa-calendar-alt me-2"></i><?= e($month )?>
            </h5>
            <?php foreach ($events as $ev): ?>
              <div class="card event-card mb-3 shadow-sm border-0 rounded-3 overflow-hidden transition-hover">
                <div class="row g-0 align-items-center">
                  <div class="col-auto" style="background-color: #000099;">
                    <div class="text-center text-white px-3 py-3">
                      <div class="d-flex align-items-center justify-content-center">
                        <span class="fw-bold fs-4"><?= date('d', strtotime($ev['date_debut'])) ?></span>
                        <?php if($ev['date_debut'] != $ev['date_fin']): ?>
                          <span class="fs-6 mx-1">‑</span>
                          <span class="fw-bold fs-4"><?= date('d', strtotime($ev['date_fin'])) ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="small text-uppercase"><?= date('M', strtotime($ev['date_debut'])) ?></div>
                    </div>
                  </div>
                  <div class="col">
                    <div class="card-body py-2 px-3">
                      <h6 class="card-title fw-bold mb-1"><?= e($ev['titre'] )?></h6>
                      <p class="mb-1 small"><i class="fas fa-map-marker-alt text-success me-1"></i><?= e($ev['lieu'] )?></p>
                      <p class="mb-0 text-muted small"><i class="far fa-clock me-1"></i>Du <?= date('d M Y', strtotime($ev['date_debut'])) ?> au <?= date('d M Y', strtotime($ev['date_fin'])) ?></p>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="alert alert-info rounded-3">Aucun événement pour le moment.</div>
        <?php endif; ?>
      </div>
      <!-- Join Us -->
      <div class="col-12 col-lg-7">
        <?php if (!empty($joinus)): ?>
          <?php foreach ($joinus as $join): ?>
            <div class="card border-0 shadow-sm rounded-4 h-100">
              <div class="card-header bg-primary text-white py-3 border-0 rounded-top-4">
                <h4 class="mb-0 fw-semibold"><i class="fas fa-handshake me-2"></i><?= e($join['titre'] )?></h4>
              </div>
              <div class="card-body p-4">
                <div class="ck-content"><?= $join['description'] ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>










<!-- Section Categories des cours -->

<section class="container py-5">
    <h2 class="text-center fw-bold mb-2">Catégories des cours</h2>
    <p class="text-center text-muted mb-5">Explorez nos formations par domaine</p>
    
    <div class="row g-4">
        <?php foreach ($categories as $categorie) { ?>
        <div class="col-sm-6 col-md-4 col-lg-3 d-flex">
            <div class="card course-card w-100 border-0 shadow-sm rounded-4 overflow-hidden transition-hover">
                <div style="height: 180px; overflow: hidden;">
                    <img src="<?=base_url('attachments/Categorie/'.$categorie['Image']) ?>" 
                         class="card-img-top h-100 w-100" 
                         style="object-fit: cover;"
                         alt="<?= e($categorie['nom_categories'] )?>">
                </div>
                <div class="card-body d-flex flex-column p-4">
                    <h5 class="card-title fw-bold"><?= e($categorie['nom_categories'])?></h5>
                    <a href="<?= base_url('Pages/Home/viewcourses/' . $categorie['uuid']) ?>" 
                       class="btn mt-2 px-4 rounded-pill align-self-start"
                       style="background-color: #000099; color: white;">
                        Consultez les cours <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</section>





        

    <!-- Section Témoignages -->
    <section class="testimonials-section py-5" id="temoignages">
        <div class="container-fluid px-3 px-md-4 px-lg-5">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title fw-bold">Témoignages</h2>
                    <p class="section-subtitle text-muted">
                        Découvrez ce que nos clients disent de leur expérience avec nos services. 
                        Leurs retours nous aident à nous améliorer chaque jour.
                    </p>
                </div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-12 col-xxl-10">
                    <div class="testimonials-slider" id="testimonialsSlider">
                        <?php foreach ($testimony as $testimonial) { ?>
                        <div class="testimonial-slide mx-2">
                            <div class="testimonial-card border-0 shadow-sm rounded-4 p-4">
                                <div class="testimonial-header d-flex align-items-center mb-3">
                                    <img src="<?= htmlspecialchars(base_url('attachments/Testimony/'.$testimonial['Image'])) ?>" 
                                         alt="<?= htmlspecialchars($testimonial['Testifier']) ?>" 
                                         class="testimonial-avatar rounded-circle me-3"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                    <div class="testimonial-author">
                                        <h4 class="mb-0 fw-bold"><?= htmlspecialchars($testimonial['Testifier']) ?></h4>
                                        <p class="mb-0 text-muted small"><?= htmlspecialchars($testimonial['Poste']) ?></p>
                                    </div>
                                </div>
                                <div class="testimonial-rating mb-2" style="color: #ff9900;">
                                    <?php 
                                    $rating = isset($testimonial['rating']) ? $testimonial['rating'] : 5;
                                    for ($i = 0; $i < 5; $i++) {
                                        echo '<i class="fas fa-star' . ($i < $rating ? '' : '-half-alt') . '"></i> ';
                                    }
                                    ?>
                                </div>
                                <div class="testimonial-text text-muted">
                                    <i class="fas fa-quote-left text-primary opacity-25 me-1"></i>
                                    <?= nl2br(htmlspecialchars($testimonial['Details'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
    
   
       
 


<!-- section partners -->
<section class="partners-section py-5 bg-light">
    <div class="container-fluid">
        <h2 class="text-center fw-bold mb-5">Nos partenaires</h2>

        <div class="partners-slider">
             <?php foreach ($parteners as $partener) { ?>
            <div class="partner-item d-flex flex-column align-items-center p-3">
                <img src="<?=e($partener['link'])?>" class="img-fluid" style="max-height: 80px; width: auto; filter: grayscale(0.4); transition: filter 0.3s;">
                <p class="small mt-2 mb-0 text-muted"><?=e($partener['description'])?></p>
            </div>
            <?php } ?>
        </div>
    </div>
</section>

<style>
.transition-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.transition-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.12) !important;
}
.partner-item img:hover {
    filter: grayscale(0) !important;
}
@media (max-width: 576px) {
    .carousel-img-wrap { height: 220px !important; }
    .carousel-caption .display-6 { font-size: 1.25rem; }
    .carousel-caption .lead { font-size: 0.9rem; }
}
@media (min-width: 577px) and (max-width: 992px) {
    .carousel-img-wrap { height: 350px !important; }
}
</style>




<?php include VIEWPATH.'media/Footer.php' ;?>

