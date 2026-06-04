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
      <div style="height: 400px; width:auto; overflow: hidden; position: relative;">
        <img src="<?= htmlspecialchars(base_url('attachments/Carousel/'.$carousel['Image'])) ?>" 
             class="d-block w-100 h-100 object-fit-cover" 
             alt="<?= htmlspecialchars($carousel['Title']) ?>"
             style="object-fit: cover; width: 100%; height: 100%;">
      </div>


      
      <div class="carousel-caption d-none d-xs-block d-sm-block d-md-block text-end">
        <h5 class="display-6 fw-bold"><?= htmlspecialchars($carousel['Title']) ?></h5>
        <p class="lead mb-4"><?= e($carousel['Description'] )?></p>
        
        <!-- Boutons alignés à droite -->
        <div class="d-flex justify-content-end gap-3 mt-4">
          <a style="background-color:#000099; color: white;" href="<?= base_url('Pages/Home/Categorie') ?>" class="btn btn-ms btn-lg rounded-pill px-4 h-25">
            Explorer les cours
          </a>
          <a style="background-color:#000099; color: white;" href="<?= base_url('Pages/About_us/contact') ?>" class="btn btn-ms  btn-lg rounded-pill px-4 h-25 ">
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








<section class="content-section-aboutus p-5">
    <div class="row">

        <div class="col-lg-4 col-md-2 col-12 d-flex">
            <div class="stats-wrapper w-100">
                <div class="stat-item d-flex align-items-center mb-4">
                    <h2 class="stat-value">5000+</h2>
                    <p class="stat-label">Professionals Trained</p>
                </div>
                <div class="stat-item d-flex align-items-center mb-4">
                    <h2 class="stat-value">10+</h2>
                    <p class="stat-label">Years of Experience</p>
                </div>
                <div class="stat-item d-flex align-items-center mb-4">
                    <h2 class="stat-value">30+</h2>
                    <p class="stat-label">Countries</p>
                </div>
            </div>

            <div class="vertical-divider"></div>
        </div>



<div class="col-lg-8 col-12">

    <?php foreach ($about_us as $about) { ?>
    <p class="about-us-link text-center" style="font-size: 13px; color: #ff9900">About Us</p>
    <h2 class="company-name text-center"><?= e($about['title'])?></h2>

    <div class="company-description text-start text-justify ck-content"><?= $about['details']?>
    </div>
    <?php }; ?>
</div>

    </div>
</section>







 


<section class="content-section py-5">
  <div class="container-fluid">
    <!-- Header -->
    <div class="text-center mb-5">
      <h5 class="text-uppercase text-primary fw-bold">Formation vedette</h5>
      <h2 class="fw-bold">Upcoming Training Courses / Workshops</h2>
    </div>
    <div class="row g-4">
      <!-- Événements -->

      <div class="col-lg-5 col-ms-12">
        <?php if (!empty($events_by_month)): ?>
          <?php foreach ($events_by_month as $month => $events): ?>
            <h5 class="month-title mt-4 mb-3"><?= e($month )?></h5>
            <?php foreach ($events as $ev): ?>
              <div class="card event-card mb-3 shadow-sm border-0">
                <div class="row g-0 align-items-center">
                  <div class="col-auto date-tag text-center text-white p-3">
                    <div class="d-flex align-items-center">
                    <div class="day"><?= date('d', strtotime($ev['date_debut'])) ?></div>
                    <?php if($ev['date_debut'] != $ev['date_fin']): ?>
                      <sup><div class="day-end">‑<?= date('d', strtotime($ev['date_fin'])) ?></div></sup>
                    <?php endif; ?>
                    </div>
                    <div class="month"><?= date('M', strtotime($ev['date_debut'])) ?></div>
                  </div>
                  <div class="col">
                    <div class="card-body py-2">
                      <h6 class="card-title fw-bold mb-1"><?= e($ev['titre'] )?></h6>
                      <p class="mb-1"><i class="fas fa-map-marker-alt me-1"></i><?= e($ev['lieu'] )?></p>
                      <p class="mb-0 text-muted"><small>Du <?= date('d M Y', strtotime($ev['date_debut'])) ?> au <?= date('d M Y', strtotime($ev['date_fin'])) ?></small></p>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="alert alert-info">Aucun événement pour le moment.</div>
        <?php endif; ?>
      </div>
      <!-- Join Us -->

      <div class="col-12 col-ms-12 col-lg-7">
        <?php if (!empty($joinus)): ?>
          <?php foreach ($joinus as $join): ?>
      <div class="border-start border-2 ps-3">
        <h4 class="fw-bold text-primary text-center"><?= e($join['titre'] )?></h4>
        <p>
         <?= $join['description'] ?>
        </p>
      </div>
      <?php endforeach; ?>
        <?php endif; ?>
    </div>



    </div>
  </div>
</section>










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





        

    <!-- Section Témoignages -->
    <section class="testimonials-section" id="temoignages">
        <div class="container-fluid px-3 px-md-4 px-lg-5">
            <div class="row">
                <div class="col-12 text-center mb-4 mb-md-5">
                    <h2 class="section-title">Témoignages</h2>
                    <p class="section-subtitle">
                        Découvrez ce que nos clients disent de leur expérience avec nos services. 
                        Leurs retours nous aident à nous améliorer chaque jour.
                    </p>
                </div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-12 col-xxl-10">
                    <!-- Slider de témoignages -->
                    <div class="testimonials-slider" id="testimonialsSlider">
                        <?php foreach ($testimony as $testimony) { ?>
                        <div class="testimonial-slide mx-2">
                            <div class="testimonial-card">
                                <div class="testimonial-header">
                                    <img src="<?= htmlspecialchars(base_url('attachments/Testimony/'.$testimony['Image'])) ?>" 
                                         alt="<?= htmlspecialchars($testimony['Testifier']) ?>" 
                                         class="testimonial-avatar">
                                    <div class="testimonial-author">
                                        <h4><?= htmlspecialchars($testimony['Testifier']) ?></h4>
                                        <p><?= htmlspecialchars($testimony['Poste']) ?></p>
                                    </div>
                                </div>
                                <div class="testimonial-rating">
                                    <?php 
                                    // Générer les étoiles dynamiquement si vous avez un champ rating
                                    $rating = isset($testimony['rating']) ? $testimony['rating'] : 5;
                                    for ($i = 0; $i < 5; $i++) {
                                        echo '<i class="fas fa-star' . ($i < $rating ? '' : '-half-alt') . '"></i>';
                                    }
                                    ?>
                                </div>
                                <div class="testimonial-text">
                                    <?= nl2br(htmlspecialchars($testimony['Details'])) ?>
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
<section class="partners-section py-3 bg-light">
    <div class="container-fluid">
        <h2 class="text-center mb-5 font-weight-bold">Nos partenaires</h2>

        <div class="partners-slider">
            
             <?php foreach ($parteners as $partener) { ?>
            <div class="partner-item d-flex flex-column align-items-center p-3">
                <!-- Certification board placeholder -->
                <img src="<?=e($partener['link'])?>" class="img-fluid" style="max-height: 80px; width: auto;">
                <p class="small mt-2 mb-0"><?=e($partener['description'])?></p>
            </div>
            <?php }; ?>
        </div>
    </div>
</section>




<?php include VIEWPATH.'media/Footer.php' ;?>

