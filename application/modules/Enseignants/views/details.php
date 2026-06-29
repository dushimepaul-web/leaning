<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Teacher Details</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <a href="<?= base_url('Enseignants') ?>" class="text-secondary-light hover-text-primary hover-underline"> / Teacher</a>
        <span class="text-secondary-light"> / Teacher Details</span>
      </div>
    </div>
  </div>
  <?php $t = $teacher; ?>
  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-24">
        <div class="d-flex gap-32 flex-md-row flex-column">
          <div class="max-w-300-px w-100 text-center">
            <figure class="mb-24 w-120-px h-120-px mx-auto rounded-circle overflow-hidden">
              <img src="<?= base_url('assets/images/thumbs/student-details-img.png') ?>" alt="Photo" class="w-100 h-100 object-fit-cover">
            </figure>
            <h2 class="h6 text-primary-light mb-16 fw-semibold"><?= htmlspecialchars($t['nom'] . ' ' . ($t['postnom'] ?? '') . ' ' . ($t['prenom'] ?? '')) ?></h2>
            <p class="mb-0">ID: <span class="text-primary-600 fw-semibold"><?= $t['matricule'] ?? '-' ?></span></p>
            <p class="mb-0">Courses: <span class="text-primary-light fw-semibold"><?= count($t['enseignements']) ?></span></p>
            <div class="mt-32 d-flex gap-16 w-100">
              <button type="button" class="btn border fw-medium border-danger-600 bg-hover-danger-200 text-danger-600 text-md d-flex justify-content-center align-items-center gap-8 flex-grow-1 px-12 py-8 radius-8" onclick="suspendTeacher()">
                <span class="d-flex text-lg"><i class="ri-delete-bin-2-line"></i></span> Suspend
              </button>
              <a href="<?= base_url('Enseignants/timetable/' . $t['uuid']) ?>" class="btn border fw-medium border-info-600 bg-hover-info-200 text-info-600 text-md d-flex justify-content-center align-items-center gap-8 flex-grow-1 px-12 py-8 radius-8">
                <span class="d-flex text-lg"><i class="ri-calendar-check-line"></i></span> Timetable
              </a>
              <a href="<?= base_url('Enseignants/edit/' . $t['uuid']) ?>" class="btn btn-primary-600 border fw-medium border-primary-600 text-md d-flex justify-content-center align-items-center gap-8 flex-grow-1 px-12 py-8 radius-8">
                <span class="d-flex text-lg"><i class="ri-edit-line"></i></span> Edit
              </a>
            </div>
          </div>
          <div><span class="h-100 w-1-px bg-neutral-200"></span></div>
          <div class="flex-grow-1">
            <div class="pb-16 border-bottom d-flex align-items-center justify-content-between gap-20">
              <h3 class="h6 text-primary-light text-lg mb-0 fw-semibold">Personal Info</h3>
              <span class="bg-success-100 text-success-600 px-16 py-4 radius-4 fw-medium text-sm"><?= ($t['actif'] ?? 1) ? 'Active' : 'Inactive' ?></span>
            </div>
            <div class="mt-16 d-flex flex-column gap-8">
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Classes</span><span class="fw-normal text-sm text-secondary-light">: <a href="#" onclick="document.getElementById('pills-courses-tab').click(); return false;" class="text-primary-600"><?= implode(', ', $t['classes_list'] ?: ['-']) ?></a></span></div>

              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Date Of Birth</span><span class="fw-normal text-sm text-secondary-light">: <?= $t['date_naissance'] ? date('d M Y', strtotime($t['date_naissance'])) : '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Gender</span><span class="fw-normal text-sm text-secondary-light">: <?= $t['sexe'] === 'M' ? 'Male' : ($t['sexe'] === 'F' ? 'Female' : '-') ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Join Date</span><span class="fw-normal text-sm text-secondary-light">: <?= $t['date_embauche'] ? date('d M Y', strtotime($t['date_embauche'])) : '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Phone Number</span><span class="fw-normal text-sm text-primary-600">: <?= $t['telephone'] ?? '-' ?></span></div>
              <div class="d-flex gap-4"><span class="fw-semibold text-sm text-primary-light w-110-px">Email</span><span class="fw-normal text-sm text-primary-600">: <?= $t['email'] ?? '-' ?></span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="my-16">
      <ul class="nav nav-pills bordered-tab mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="pills-details-tab" data-bs-toggle="pill" data-bs-target="#pills-details" type="button" role="tab">
            <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-group-line"></i></span> Teacher Details
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="pills-timetable-tab" data-bs-toggle="pill" data-bs-target="#pills-timetable" type="button" role="tab">
            <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-calendar-check-line"></i></span> Class Routine
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
          <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="pills-payroll-tab" data-bs-toggle="pill" data-bs-target="#pills-payroll" type="button" role="tab">
            <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-money-dollar-box-line"></i></span> Payroll
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="pills-courses-tab" data-bs-toggle="pill" data-bs-target="#pills-courses" type="button" role="tab">
            <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-book-open-line"></i></span> Courses
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="pills-library-tab" data-bs-toggle="pill" data-bs-target="#pills-library" type="button" role="tab">
            <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-book-line"></i></span> Library
          </button>
        </li>
      </ul>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-details" role="tabpanel">
          <div class="row gy-4">
            <div class="col-md-6">
              <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
                <div class="card-header border-bottom bg-base py-16 px-24">
                  <h6 class="text-lg fw-semibold mb-0">Profile Detail</h6>
                </div>
                <div class="card-body p-20">
                  <div class="d-flex flex-column gap-8">
                    <div><span class="fw-semibold text-sm text-primary-light">Date of Birth</span><br><span class="text-sm text-secondary-light"><?= $t['date_naissance'] ? date('d M Y', strtotime($t['date_naissance'])) : '-' ?></span></div>
                    <div><span class="fw-semibold text-sm text-primary-light">Qualification</span><br><span class="text-sm text-secondary-light"><?= $t['qualification'] ?? '-' ?></span></div>
                    <div><span class="fw-semibold text-sm text-primary-light">Experience</span><br><span class="text-sm text-secondary-light"><?= $t['experience'] ?? '-' ?></span></div>

                  </div>
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
                      <h6 class="text-md mb-2 fw-medium">Address</h6>
                      <span class="text-sm"><?= nl2br(htmlspecialchars($t['adresse'] ?? '-')) ?></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>


          </div>
        </div>
        <div class="tab-pane fade" id="pills-timetable" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">
            <a href="<?= base_url('Enseignants/timetable/' . $t['uuid']) ?>" class="btn btn-primary-600">View Full Timetable</a>
          </div>
        </div>
        <div class="tab-pane fade" id="pills-attendance" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">Attendance module coming soon</div>
        </div>
        <div class="tab-pane fade" id="pills-leave" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">Leave module coming soon</div>
        </div>
        <div class="tab-pane fade" id="pills-payroll" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">Payroll module coming soon</div>
        </div>
        <div class="tab-pane fade" id="pills-courses" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base overflow-hidden">
            <div class="card-header border-bottom bg-base py-16 px-24">
              <h6 class="text-lg fw-semibold mb-0">Courses taught by <?= htmlspecialchars($t['prenom'] . ' ' . $t['nom']) ?></h6>
            </div>
            <div class="card-body p-0">
              <table class="table bordered-table mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Class</th>
                    <th>Coefficient</th>
                    <th>Hours/Day</th>
                    <th>Hours/Week</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($t['enseignements'])): $i = 0; ?>
                    <?php foreach ($t['enseignements'] as $ens): $i++; ?>
                    <tr>
                      <td><?= $i ?></td>
                      <td><?= htmlspecialchars($ens['matiere_libelle']) ?></td>
                      <td><?= htmlspecialchars($ens['classe_libelle']) ?></td>
                      <td><?= $ens['coefficient'] ?></td>
                      <td><?= $ens['nb_heures_par_jour'] ?></td>
                      <td><?= $ens['nb_heures_par_semaine'] ?></td>
                      <td>
                        <button class="btn btn-sm btn-primary-600 px-12 py-4 radius-4" onclick="openEditCourse(<?= htmlspecialchars(json_encode($ens)) ?>)"><i class="ri-edit-2-line"></i></button>
                        <button class="btn btn-sm btn-danger px-12 py-4 radius-4" onclick="confirmDeleteCourse(<?= $ens['uuid'] ?>)"><i class="ri-delete-bin-6-line"></i></button>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr><td colspan="7" class="text-center text-secondary-light">No courses assigned</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="pills-library" role="tabpanel">
          <div class="shadow-1 radius-12 bg-base p-24 text-center text-secondary-light">Library module coming soon</div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-header border-bottom">
        <h5 class="modal-title text-lg fw-semibold">Modifier le cours</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editCourseForm" class="p-20">
        <input type="hidden" id="edit_id_matiere_classe">
        <div class="row g-3">
          <div class="col-sm-12">
            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Coefficient</label>
            <input type="number" step="0.1" min="0" class="form-control" id="edit_coefficient">
          </div>
          <div class="col-sm-6">
            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Hours/Day</label>
            <input type="number" step="0.5" min="0" class="form-control" id="edit_nb_heures_par_jour">
          </div>
          <div class="col-sm-6">
            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Hours/Week</label>
            <input type="number" step="0.5" min="0" class="form-control" id="edit_nb_heures_par_semaine">
          </div>
          <div class="col-12">
            <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
              <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" data-bs-dismiss="modal">Annuler</button>
              <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

function openEditCourse(ens) {
  document.getElementById('edit_id_matiere_classe').value = ens.uuid || '';
  document.getElementById('edit_coefficient').value = ens.coefficient || '';
  document.getElementById('edit_nb_heures_par_jour').value = ens.nb_heures_par_jour || '';
  document.getElementById('edit_nb_heures_par_semaine').value = ens.nb_heures_par_semaine || '';
  new bootstrap.Modal(document.getElementById('editCourseModal')).show();
}

document.getElementById('editCourseForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id = document.getElementById('edit_id_matiere_classe').value;
  if (!id) return;
  const data = {
    coefficient: document.getElementById('edit_coefficient').value || null,
    nb_heures_par_jour: document.getElementById('edit_nb_heures_par_jour').value || null,
    nb_heures_par_semaine: document.getElementById('edit_nb_heures_par_semaine').value || null
  };
  const res = await API.matieres_classes.update(id, data);
  if (res.success) {
    bootstrap.Modal.getInstance(document.getElementById('editCourseModal')).hide();
    Toast.fire({ icon: 'success', title: 'Course updated' });
    setTimeout(() => location.reload(), 500);
  } else {
    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
  }
});

async function confirmDeleteCourse(id) {
  const result = await Swal.fire({ title: 'Delete Course?', text: 'This action is irreversible', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete', cancelButtonText: 'Cancel', customClass: { confirmButton: 'swal2-styled--danger' } });
  if (!result.isConfirmed) return;
  const res = await API.matieres_classes.delete(id);
  if (res.success) {
    Toast.fire({ icon: 'success', title: 'Course deleted' });
    setTimeout(() => location.reload(), 500);
  } else {
    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
  }
}

async function suspendTeacher() {
  const result = await Swal.fire({ title: 'Suspend Teacher?', text: 'This will deactivate the teacher account', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, suspend', cancelButtonText: 'Cancel', customClass: { confirmButton: 'swal2-styled--danger' } });
  if (!result.isConfirmed) return;
  const res = await API.enseignants.update('<?= $t['uuid'] ?>', { actif: 0 });
  if (res.success) { await Toast.fire({ icon: 'success', title: 'Teacher suspended' }); location.reload(); }
  else Swal.fire({ icon: 'error', title: 'Error', text: res.message });
}
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>