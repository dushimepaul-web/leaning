<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Student List</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Student List</span>
      </div>
    </div>
    <a href="<?= base_url('Etudiants/add') ?>" class="btn btn-primary-600 d-flex align-items-center gap-6">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
      Add Student
    </a>
  </div>
  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
          <div class="d-flex flex-wrap align-items-center gap-16">
            <div class="dropdown">
              <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown">
                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">
                  <i class="ri-file-upload-line text-md line-height-1"></i> Export
                </span>
                <span><i class="ri-arrow-down-s-line"></i></span>
              </button>
              <ul class="dropdown-menu p-12 border bg-base shadow">
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light d-flex align-items-center gap-10" onclick="exportCSV()"><i class="ri-file-excel-line"></i> CSV</button></li>
              </ul>
            </div>
            <form class="navbar-search dt-search m-0">
              <input type="text" id="dtSearch" class="dt-input bg-transparent radius-4" placeholder="Search...">
              <i class="ri-search-line icon"></i>
            </form>
            <div class="dropdown">
              <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown">
                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">Filter</span>
                <span><i class="ri-arrow-down-s-line"></i></span>
              </button>
              <div class="dropdown-menu border bg-base shadow dropdown-menu-lg p-0">
                <div class="d-flex align-items-center justify-content-between border-bottom py-8 px-16">
                  <span class="fw-semibold text-lg text-primary-light">Filter</span>
                </div>
                <form class="p-16 d-grid grid-cols-2 gap-16">
                  <div>
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Class</label>
                    <select id="filterClass" class="form-control form-select">
                      <option value="">All Classes</option>
                      <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id_classe'] ?>"><?= $c['libelle'] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div>
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Section</label>
                    <select id="filterSection" class="form-control form-select">
                      <option value="">All Sections</option>
                      <?php foreach ($sections as $s): ?>
                        <option value="<?= $s['id_section'] ?>"><?= $s['libelle'] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div>
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Gender</label>
                    <select id="filterGender" class="form-control form-select">
                      <option value="">All</option>
                      <option value="M">Male</option>
                      <option value="F">Female</option>
                    </select>
                  </div>
                  <div>
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Status</label>
                    <select id="filterStatus" class="form-control form-select">
                      <option value="">All</option>
                      <option value="actif">Active</option>
                      <option value="inactif">Inactive</option>
                      <option value="suspendu">Suspended</option>
                    </select>
                  </div>
                  <div><button type="reset" class="btn btn-danger-200 text-danger-600 w-100" onclick="resetFilters()">Reset</button></div>
                  <div><button type="button" class="btn btn-primary-600 w-100" onclick="applyFilters()">Apply</button></div>
                </form>
              </div>
            </div>
          </div>
          <div class="d-flex align-items-center gap-8 text-secondary-light">
            <span>Rows per page:</span>
            <select id="dtLength" class="form-control form-select" style="width:auto;padding:0.375rem 2rem 0.375rem 0.75rem;">
              <option value="5">5</option>
              <option value="10" selected>10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>
          <table class="table bordered-table mb-0" id="dataTable" style="width:100%">
            <thead>
              <tr>
                <th>#</th>
                <th>Admission No</th>
                <th>Name</th>
                <th>Class</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Mobile Number</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="dataBody"></tbody>
          </table>
      </div>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
BASE_URL = '<?= base_url() ?>';

const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

function formatDate(d) {
  if (!d) return '-';
  const date = new Date(d);
  return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

async function loadData() {
  const res = await API.etudiants.list();
  if (!res.success) { $('#dataBody').html('<tr><td colspan="9" class="text-center text-danger">Error loading data</td></tr>'); return; }
  let rows = '';
  res.data.forEach((e, i) => {
    const statut = e.inscription_statut || (e.actif ? 'actif' : 'inactif');
    const statusBadge = statut === 'actif' ? 'bg-success-100 text-success-600' : (statut === 'inactif' ? 'bg-danger-100 text-danger-600' : 'bg-warning-100 text-warning-600');
    const statusText = statut.charAt(0).toUpperCase() + statut.slice(1);
    const nom = e.nom_complet || e.nom;
    const photo = e.photo ? BASE_URL + e.photo : BASE_URL + 'assets/images/thumbs/avatar-img1.png';
    rows += `<tr>
      <td>${i + 1}</td>
      <td><span class="text-primary-600">${e.matricule || '-'}</span></td>
      <td>
        <div class="d-flex align-items-center">
          <img src="${photo}" alt="" class="flex-shrink-0 me-12 radius-8" style="width:40px;height:40px;object-fit:cover;">
          <div>
            <h6 class="text-md mb-0 fw-medium"><a href="${BASE_URL}Etudiants/details/${e.uuid}" class="text-primary-light">${nom}</a></h6>
            <span class="text-sm">Roll No: <span class="fw-semibold">${e.numero_ordre || '-'}</span></span>
          </div>
        </div>
      </td>
      <td>${e.classe_libelle || '-'}</td>
      <td>${formatDate(e.date_naissance)}</td>
      <td>${e.sexe === 'M' ? 'Male' : e.sexe === 'F' ? 'Female' : '-'}</td>
      <td>${e.telephone || '-'}</td>
      <td><span class="${statusBadge} px-24 py-4 radius-4 fw-medium text-sm">${statusText}</span></td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><a href="${BASE_URL}Etudiants/details/${e.uuid}" class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6"><i class="ri-user-3-line"></i> View</a></li>
            <li><a href="${BASE_URL}Etudiants/edit/${e.uuid}" class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6"><i class="ri-edit-2-line"></i> Edit</a></li>
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="deleteRecord(${e.uuid})"><i class="ri-delete-bin-6-line"></i> Delete</button></li>
          </ul>
        </div>
      </td>
    </tr>`;
  });
  $('#dataBody').html(rows);
  if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().destroy();
  $('#dataTable').DataTable({
    pageLength: 10,
    scrollX: true,
    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
    language: { search: '', searchPlaceholder: 'Search...', lengthMenu: 'Rows per page: _MENU_', info: '', zeroRecords: 'No students found', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

function applyFilters() { loadData(); }
function resetFilters() { $('#filterClass, #filterSection, #filterGender, #filterStatus').val(''); loadData(); }

async function deleteRecord(id) {
  const result = await Swal.fire({
    title: 'Delete Student?',
    text: 'This action cannot be undone!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete',
    cancelButtonText: 'Cancel',
    customClass: { confirmButton: 'swal2-styled--danger' }
  });
  if (!result.isConfirmed) return;
  const res = await API.etudiants.delete(id);
  if (res.success) {
    Toast.fire({ icon: 'success', title: res.message || 'Student deleted' });
    loadData();
  } else {
    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
  }
}

function exportCSV() {
  const table = $('#dataTable').DataTable();
  const data = table.rows({ filter: 'applied' }).data();
  let csv = '\uFEFF';
  const headers = ['#', 'Admission No', 'Name', 'Class', 'DOB', 'Gender', 'Mobile', 'Status'];
  csv += headers.join(',') + '\n';
  data.each(function(row) {
    const cols = [];
    for (let i = 0; i < 8; i++) {
      let val = $(row[i]).text().trim() || row[i] || '';
      val = '"' + val.replace(/"/g, '""') + '"';
      cols.push(val);
    }
    csv += cols.join(',') + '\n';
  });
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'students.csv';
  link.click();
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && $.fn && $.fn.DataTable) {
      clearInterval(wait);
      loadData();
      $('#dtSearch').on('keyup', function() { $('#dataTable').DataTable().search(this.value).draw(); });
      $('#dtLength').on('change', function() { $('#dataTable').DataTable().page.len(+this.value).draw(); });
    }
  }, 50);
})();
</script>
