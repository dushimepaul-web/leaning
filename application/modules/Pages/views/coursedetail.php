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
    <div class="row g-4 align-items-start">

      <!-- IMAGE + DESCRIPTION -->
      <div class="col-12 col-lg-6">
        <div class="joinus-card rounded shadow-sm overflow-hidden h-100">
           <div class="course-img">
          <img src="<?= base_url($this->Model->get_setting('site_hero_image', 'assets/images/good.png')) ?>"
               class="img-fluid w-100"
               style="height: 100%; object-fit: cover;"
               alt="Course image">
            </div>
          <div class="p-4 p-md-5">
            <div class="course-desc ck-content"><?= $detailcourse['description'] ?></div>
          </div>

        </div>
      </div>

      <!-- PROGRAMMES -->
      <div class="col-12 col-lg-6 sticky-top" style="top:100px">
        

        <!-- Formation en salle -->
        <h3 class="mb-3">Programme de formation</h3>

        <div class="table-responsive mb-5">
          <table class="table table-bordered align-middle text-nowrap">
            <thead class="table-light">
              <tr>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Emplacement</th>
                <th>Coût</th>
                <th>Appliquer</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($timetable_courses as $timetable_course): ?>
              <tr>
                <td><?= date('d/m/Y H:i', strtotime($timetable_course['date_debut'])) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($timetable_course['date_defin'])) ?></td>
                <td><?= e($timetable_course['localisation'] )?></td>
                <td><?= e($timetable_course['price'] )?> FBU</td>
                <td>
                  <a href="<?= base_url('Pages/Home/register/'.$timetable_course['uuid']) ?>"
                     class="btn btn-primary btn-sm"
                     style="background-color: #000099;">
                    Register
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
  
</section>








<?php include VIEWPATH.'media/Footer.php'; ?>