<?php include VIEWPATH.'media/Header.php'; ?>  
<?php include VIEWPATH.'media/navbar.php'; ?>  

<div class="hero-bg" style="background-image: url('<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>')">  
  <div class="hero-body text-center">  
    <h1 class="hero-tete"><?= e($detailcourse['nom_course'] )?></h1>  
    <p class="hero-descr">Home / cours</p>  
  </div>  
</div>  

<section class="content-section py-5">
  <div class="container-fluid px-3 px-md-4 px-lg-5">
    <div class="row g-5 align-items-start">

      <!-- IMAGE + DESCRIPTION -->
      <div class="col-12 col-lg-6">
        <div class="joinus-card rounded-4 shadow overflow-hidden h-100 border-0">
          <?php if (!empty($detailcourse['image'])): ?>
          <div class="overflow-hidden" style="max-height: 400px;">
            <img src="<?= base_url('attachments/Courses/' . $detailcourse['image']) ?>"
                 class="img-fluid w-100"
                 style="object-fit: cover; min-height: 200px;"
                 alt="<?= e($detailcourse['nom_course']) ?>">
          </div>
          <?php endif; ?>
          <div class="p-4 p-md-5">
            <?php if (!empty($detailcourse['nom_categorie'])): ?>
            <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2">
              <i class="fas fa-tag me-1"></i><?= e($detailcourse['nom_categorie']) ?>
            </span>
            <?php endif; ?>
            <div class="course-desc ck-content mt-3"><?= $detailcourse['description'] ?></div>
          </div>
        </div>
      </div>

      <!-- PROGRAMMES -->
      <div class="col-12 col-lg-6 sticky-top" style="top:100px">

        <div class="card border-0 shadow rounded-4 overflow-hidden">
          <div class="card-header bg-primary text-white py-3 border-0">
            <h4 class="mb-0 fw-semibold"><i class="fas fa-calendar-alt me-2"></i>Programme de formation</h4>
          </div>
          <div class="card-body p-4">
            <?php
            $hasPrice = false;
            foreach ($timetable_courses as $tc) {
                if (!empty($tc['price'])) { $hasPrice = true; break; }
            }
            ?>
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="py-2">Date début</th>
                    <th class="py-2">Date fin</th>
                    <th class="py-2">Emplacement</th>
                    <?php if ($hasPrice): ?><th class="py-2">Coût</th><?php endif; ?>
                    <th class="py-2">Appliquer</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($timetable_courses as $timetable_course): ?>
                  <tr>
                    <td class="py-2">
                      <i class="far fa-calendar-alt text-primary me-1"></i>
                      <?= date('d/m/Y H:i', strtotime($timetable_course['date_debut'])) ?>
                    </td>
                    <td class="py-2">
                      <i class="far fa-calendar-check text-primary me-1"></i>
                      <?= date('d/m/Y H:i', strtotime($timetable_course['date_defin'])) ?>
                    </td>
                    <td class="py-2">
                      <i class="fas fa-map-marker-alt text-success me-1"></i>
                      <?= e($timetable_course['localisation'] )?>
                    </td>
                    <?php if ($hasPrice): ?>
                    <td class="py-2 fw-semibold"><?= e($timetable_course['price'] )?> FBU</td>
                    <?php endif; ?>
                    <td class="py-2">
                      <a href="<?= base_url('Pages/Home/register/'.$timetable_course['uuid']) ?>"
                         class="btn btn-sm px-3"
                         style="background-color: #000099; color: white; border-radius: 50px;">
                        <i class="fas fa-arrow-right me-1"></i> Register
                      </a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>








<?php include VIEWPATH.'media/Footer.php'; ?>