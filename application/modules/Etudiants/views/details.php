<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Student Details</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <a href="<?= base_url('Etudiants') ?>" class="text-secondary-light hover-text-primary hover-underline"> / Student</a>
        <span class="text-secondary-light"> / Student Details</span>
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
            <h2 class="h6 text-primary-light mb-16 fw-semibold"><?= htmlspecialchars($etudiant['nom'] . ' ' . ($etudiant['postnom'] ?? '') . ' ' . ($etudiant['prenom'] ?? '')) ?></h2>
            <p class="mb-0">Admission No: <span class="text-primary-600 fw-semibold"><?= $etudiant['matricule'] ?? '-' ?></span></p>
            <p class="mb-0">Roll No: <span class="text-primary-light fw-semibold"><?= $etudiant['numero_ordre'] ?? '-' ?></span></p>
            <div class="mt-32 d-flex gap-16 w-100">
              <button type="button" class="btn border fw-medium border-danger-600 bg-hover-danger-200 text-danger-600 text-md d-flex justify-content-center align-items-center gap-8 flex-grow-1 px-12 py-8 radius-8" onclick="suspendStudent()">
                <span class="d-flex text-lg"><i class="ri-delete-bin-2-line"></i></span> Suspend
              </button>
              <a href="<?= base_url('Etudiants/edit/' . $etudiant['uuid']) ?>" class="btn btn-primary-600 border fw-medium border-primary-600 text-md d-flex justify-content-center align-items-center gap-8 flex-grow-1 px-12 py-8 radius-8">
                <span class="d-flex text-lg"><i class="ri-edit-line"></i></span> Edit
              </a>
            </div>
          </div>
          <div><span class="h-100 w-1-px bg-neutral-200"></span></div>
          <div class="flex-grow-1">
            <div class="pb-16 border-bottom d-flex align-items-center justify-content-between gap-20">
              <h3 class="h6 text-primary-light text-lg mb-0 fw-semibold">Personal Info</h3>
              <span class="bg-success-100 text-success-600 px-16 py-4 radius-4 fw-medium text-sm">
                <?= ucfirst($etudiant['inscription']['statut'] ?? ($etudiant['actif'] ? 'actif' : 'inactif')) ?>
              </span>
            </div>
            <div class="mt-16 d-flex flex-column gap-8">
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Class</span><span class="fw-normal text-sm text-secondary-light">: <?= $etudiant['classe_libelle'] ?? '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Section</span><span class="fw-normal text-sm text-secondary-light">: <?= $etudiant['section_libelle'] ?? '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Roll No</span><span class="fw-normal text-sm text-secondary-light">: <?= $etudiant['numero_ordre'] ?? '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Gender</span><span class="fw-normal text-sm text-secondary-light">: <?= $etudiant['sexe'] === 'M' ? 'Male' : ($etudiant['sexe'] === 'F' ? 'Female' : '-') ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Date Of Birth</span><span class="fw-normal text-sm text-secondary-light">: <?= $etudiant['date_naissance'] ? date('d M Y', strtotime($etudiant['date_naissance'])) : '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Category</span><span class="fw-normal text-sm text-secondary-light">: <?= $etudiant['categorie'] ?? '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Academic Year</span><span class="fw-normal text-sm text-secondary-light">: <?= $etudiant['annee_libelle'] ?? '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Phone Number</span><span class="fw-normal text-sm text-primary-600">: <?= $etudiant['telephone'] ?? '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Email</span><span class="fw-normal text-sm text-primary-600">: <?= $etudiant['email'] ?? '-' ?></span></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="my-16">
      <ul class="nav nav-pills bordered-tab mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="pills-studentDetails-tab" data-bs-toggle="pill" data-bs-target="#pills-studentDetails" type="button" role="tab">
            <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-group-line"></i></span> Student Details
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="pills-attendance-tab" data-bs-toggle="pill" data-bs-target="#pills-attendance" type="button" role="tab">
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
            <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-file-edit-line"></i></span> exam
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="pills-library-tab" data-bs-toggle="pill" data-bs-target="#pills-library" type="button" role="tab">
            <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-book-line"></i></span> library
          </button>
        </li>
      </ul>

      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-studentDetails" role="tabpanel">
          <div class="row gy-4">
            <div class="col-12">
              <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                <div class="card-header border-bottom bg-base py-16 px-24">
                  <h6 class="text-lg fw-semibold mb-0">Parent Guardian Detail</h6>
                </div>
                <div class="card-body p-0">
                  <?php foreach (['pere' => 'Father', 'mere' => 'Mother', 'tuteur' => 'Guardian'] as $type => $label): ?>
                    <?php $p = $etudiant['parents'][$type] ?? null; ?>
                    <div class="bg-hover-neutral-50 p-20">
                      <div class="row g-4">
                        <div class="col-sm-4">
                          <div class="d-flex align-items-center gap-12">
                            <figure class="w-48-px h-48-px rounded-circle overflow-hidden mb-0">
                              <img src="<?= base_url('assets/images/thumbs/guardian-img1.png') ?>" alt="" class="flex-shrink-0 w-100 h-100 object-fit-cover">
                            </figure>
                            <div>
                              <h6 class="text-md mb-2 fw-medium"><?= htmlspecialchars($p['nom'] ?? ('No ' . $label)) ?></h6>
                              <span class="text-sm"><?= $label ?></span>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <h6 class="text-md mb-2 fw-medium">Phone</h6>
                          <span class="text-sm"><?= htmlspecialchars($p['telephone'] ?? '-') ?></span>
                        </div>
                        <div class="col-sm-4">
                          <h6 class="text-md mb-2 fw-medium">Email</h6>
                          <span class="text-sm"><?= htmlspecialchars($p['email'] ?? '-') ?></span>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                <div class="card-header border-bottom bg-base py-16 px-24">
                  <h6 class="text-lg fw-semibold mb-0">Address</h6>
                </div>
                <div class="card-body p-20">
                  <div class="row gy-4">
                    <div class="col-sm-12">
                      <h6 class="text-md mb-2 fw-medium">Current Address</h6>
                      <span class="text-sm"><?= nl2br(htmlspecialchars($etudiant['adresse'] ?? '-')) ?></span>
                    </div>
                    <div class="col-sm-12">
                      <h6 class="text-md mb-2 fw-medium">Permanent Address</h6>
                      <span class="text-sm"><?= nl2br(htmlspecialchars($etudiant['adresse_permanente'] ?? '-')) ?></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                <div class="card-header border-bottom bg-base py-16 px-24">
                  <h6 class="text-lg fw-semibold mb-0">Guardian Info</h6>
                </div>
                <div class="card-body p-20">
                  <?php $t = $etudiant['parents']['tuteur'] ?? []; ?>
                  <div class="row gy-4">
                    <div class="col-sm-4">
                      <h6 class="text-md mb-2 fw-medium">Occupation</h6>
                      <span class="text-sm"><?= htmlspecialchars($t['profession'] ?? '-') ?></span>
                    </div>
                    <div class="col-sm-4">
                      <h6 class="text-md mb-2 fw-medium">Relation</h6>
                      <span class="text-sm"><?= htmlspecialchars($t['relation'] ?? '-') ?></span>
                    </div>
                    <div class="col-sm-4">
                      <h6 class="text-md mb-2 fw-medium">Address</h6>
                      <span class="text-sm"><?= htmlspecialchars($t['adresse'] ?? '-') ?></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="pills-attendance" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">
            Attendance module coming soon
          </div>
        </div>
        <div class="tab-pane fade" id="pills-leave" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">
            Leave module coming soon
          </div>
        </div>
        <div class="tab-pane fade" id="pills-fees" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">
            Fees module coming soon
          </div>
        </div>
        <div class="tab-pane fade" id="pills-exam" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">
            Exam module coming soon
          </div>
        </div>
        <div class="tab-pane fade" id="pills-library" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">
            Library module coming soon
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?= base_url() ?>assets/js/api.js"></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

async function suspendStudent() {
  const result = await Swal.fire({ title: 'Suspend Student?', text: 'This will deactivate the student account', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, suspend', cancelButtonText: 'Cancel', customClass: { confirmButton: 'swal2-styled--danger' } });
  if (!result.isConfirmed) return;
  const res = await API.etudiants.update('<?= $etudiant['uuid'] ?>', { actif: 0 });
  if (res.success) { await Toast.fire({ icon: 'success', title: 'Student suspended' }); location.reload(); }
  else Swal.fire({ icon: 'error', title: 'Error', text: res.message });
}
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
