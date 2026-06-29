<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Détails Élève</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <a href="<?= base_url('Etudiants') ?>" class="text-secondary-light hover-text-primary hover-underline"> / Élèves</a>
        <span class="text-secondary-light"> / Détails</span>
      </div>
    </div>
  </div>

  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-24">
        <div class="d-flex gap-32 flex-md-row flex-column">
          <div class="max-w-300-px w-100 text-center">
            <figure class="mb-24 w-120-px h-120-px mx-auto rounded-circle overflow-hidden">
              <img src="<?= $etudiant['photo'] ? base_url($etudiant['photo']) : base_url('assets/images/thumbs/student-details-img.png') ?>" alt="Photo" class="w-100 h-100 object-fit-cover">
            </figure>
            <h2 class="h6 text-primary-light mb-16 fw-semibold"><?= htmlspecialchars(trim($etudiant['nom'] . ' ' . ($etudiant['postnom'] ?? '') . ' ' . ($etudiant['prenom'] ?? ''))) ?></h2>
            <p class="mb-0">Matricule : <span class="text-primary-600 fw-semibold"><?= $etudiant['matricule'] ?? '-' ?></span></p>
            <p class="mb-0">N° ordre : <span class="text-primary-light fw-semibold"><?= $etudiant['numero_ordre'] ?? '-' ?></span></p>
            <p class="mt-8 text-sm text-secondary-light">Adresse : <?= nl2br(htmlspecialchars($etudiant['adresse'] ?? '-')) ?></p>
            <div class="mt-32 d-flex gap-16 w-100">
              <a href="<?= base_url('Etudiants/edit/' . $etudiant['uuid']) ?>" class="btn btn-primary-600 border fw-medium border-primary-600 text-md d-flex justify-content-center align-items-center gap-8 flex-grow-1 px-12 py-8 radius-8">
                <span class="d-flex text-lg"><i class="ri-edit-line"></i></span> Modifier
              </a>
            </div>
          </div>
          <div><span class="h-100 w-1-px bg-neutral-200"></span></div>
          <div class="flex-grow-1">
            <div class="pb-16 border-bottom d-flex align-items-center justify-content-between gap-20">
              <h3 class="h6 text-primary-light text-lg mb-0 fw-semibold">Informations Personnelles</h3>
              <span class="bg-success-100 text-success-600 px-16 py-4 radius-4 fw-medium text-sm">
                <?= ucfirst($etudiant['inscription']['statut'] ?? ($etudiant['actif'] ? 'Actif' : 'Inactif')) ?>
              </span>
            </div>
            <div class="mt-16 d-flex flex-column gap-8">
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-140-px">Classe</span><span class="fw-normal text-sm text-secondary-light">: <?= $etudiant['classe_libelle'] ?? '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-140-px">Section</span><span class="fw-normal text-sm text-secondary-light">: <?= $etudiant['section_libelle'] ?? '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-140-px">Genre</span><span class="fw-normal text-sm text-secondary-light">: <?= $etudiant['sexe'] === 'M' ? 'Masculin' : ($etudiant['sexe'] === 'F' ? 'Féminin' : '-') ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-140-px">Date de naissance</span><span class="fw-normal text-sm text-secondary-light">: <?= $etudiant['date_naissance'] ? date('d/m/Y', strtotime($etudiant['date_naissance'])) : '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-140-px">Année scolaire</span><span class="fw-normal text-sm text-secondary-light">: <?= $etudiant['annee_libelle'] ?? '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-140-px">Téléphone</span><span class="fw-normal text-sm text-primary-600">: <?= $etudiant['telephone'] ?? '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-140-px">Email</span><span class="fw-normal text-sm text-primary-600">: <?= $etudiant['email'] ?? '-' ?></span></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row gy-4 mt-16">
      <div class="col-12">
        <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
          <div class="card-header border-bottom bg-base py-16 px-24">
            <h6 class="text-lg fw-semibold mb-0">Informations Parent / Tuteur</h6>
          </div>
          <div class="card-body p-20">
            <div class="row gy-4">
              <div class="col-lg-3 col-sm-6">
                <h6 class="text-md mb-2 fw-medium">Nom complet</h6>
                <span class="text-sm"><?= htmlspecialchars($etudiant['parent_nom'] ?? '-') ?></span>
              </div>
              <div class="col-lg-3 col-sm-6">
                <h6 class="text-md mb-2 fw-medium">Téléphone</h6>
                <span class="text-sm"><?= htmlspecialchars($etudiant['parent_telephone'] ?? '-') ?></span>
              </div>
              <div class="col-lg-3 col-sm-6">
                <h6 class="text-md mb-2 fw-medium">Occupation</h6>
                <span class="text-sm"><?= htmlspecialchars($etudiant['parent_profession'] ?? '-') ?></span>
              </div>
              <div class="col-lg-3 col-sm-6">
                <h6 class="text-md mb-2 fw-medium">Adresse</h6>
                <span class="text-sm"><?= htmlspecialchars($etudiant['parent_adresse'] ?? '-') ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="my-16">
      <ul class="nav nav-pills bordered-tab mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="pills-attendance-tab" data-bs-toggle="pill" data-bs-target="#pills-attendance" type="button" role="tab">
            <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-calendar-check-line"></i></span> Attendance
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="pills-leave-tab" data-bs-toggle="pill" data-bs-target="#pills-leave" type="button" role="tab">
            <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-login-box-line"></i></span> Leave
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="pills-fees-tab" data-bs-toggle="pill" data-bs-target="#pills-fees" type="button" role="tab">
            <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-money-dollar-box-line"></i></span> Fees
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="pills-exam-tab" data-bs-toggle="pill" data-bs-target="#pills-exam" type="button" role="tab">
            <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-file-edit-line"></i></span> Exam
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="pills-library-tab" data-bs-toggle="pill" data-bs-target="#pills-library" type="button" role="tab">
            <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-book-line"></i></span> Library
          </button>
        </li>
      </ul>

      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-attendance" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">
            Module de présence à venir
          </div>
        </div>
        <div class="tab-pane fade" id="pills-leave" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">
            Module de congés à venir
          </div>
        </div>
        <div class="tab-pane fade" id="pills-fees" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">
            Module de frais à venir
          </div>
        </div>
        <div class="tab-pane fade" id="pills-exam" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">
            Module d'examens à venir
          </div>
        </div>
        <div class="tab-pane fade" id="pills-library" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">
            Module de bibliothèque à venir
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?= base_url() ?>assets/js/api.js"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
