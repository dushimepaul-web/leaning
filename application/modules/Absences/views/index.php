<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des Absences</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Absences</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
      Nouvelle absence
    </button>
  </div>
  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0 dataTable-wrapper">
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
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export PDF',text:'Fonctionnalité à venir'})"><i class="ri-file-3-line"></i> PDF</button></li>
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export Excel',text:'Fonctionnalité à venir'})"><i class="ri-file-excel-line"></i> Excel</button></li>
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="exportCSV()"><i class="ri-file-excel-line"></i> CSV</button></li>
              </ul>
            </div>
            <form class="navbar-search dt-search m-0">
              <input type="text" id="dtSearch" class="dt-input bg-transparent radius-4" aria-controls="dataTable" name="search" placeholder="Rechercher...">
              <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
            </form>
            <div class="dropdown">
              <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown">
                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">Filtrer</span>
                <span><i class="ri-arrow-down-s-line"></i></span>
              </button>
              <div class="dropdown-menu border bg-base shadow dropdown-menu-lg p-0">
                <div class="d-flex align-items-center justify-content-between border-bottom py-8 px-16">
                  <span class="fw-semibold text-lg text-primary-light">Filtre</span>
                </div>
                <form class="p-16 d-grid grid-cols-2 gap-16">
                  <div>
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Justifiée</label>
                    <select id="filterJustified" class="form-control form-select">
                      <option value="">Tous</option>
                      <option value="1">Oui</option>
                      <option value="0">Non</option>
                    </select>
                  </div>
                  <div>
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date début</label>
                    <input type="date" id="filterDateStart" class="form-control">
                  </div>
                  <div><button type="reset" class="btn btn-danger-200 text-danger-600 w-100" onclick="resetFilters()">Réinitialiser</button></div>
                  <div><button type="button" class="btn btn-primary-600 w-100" onclick="applyFilters()">Appliquer</button></div>
                </form>
              </div>
            </div>
          </div>
          <div class="d-flex align-items-center gap-8 text-secondary-light">
            <span>Lignes par page :</span>
            <div class="dt-length"><select id="dtLength" name="dataTable_length" aria-controls="dataTable" class="dt-input form-control form-select">
              <option value="5">5</option>
              <option value="10" selected>10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select></div>
          </div>
        </div>
        <table class="table bordered-table mb-0 data-table" id="dataTable" data-page-length='10' style="width:100%">
          <thead>
            <tr>
              <th scope="col"><div class="form-check style-check d-flex align-items-center"><input class="form-check-input" type="checkbox"><label class="form-check-label">S.L</label></div></th>
              <th>Étudiant</th>
              <th>Matricule</th>
              <th>Date</th>
              <th>Motif</th>
              <th>Justifiée</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="dataBody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="sidebarOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="addSidebar" style="width:50vw;max-width:50vw;box-shadow: -4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="sidebarTitle">Nouvelle absence</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="mainForm" class="d-flex flex-column p-20">
    <div class="mb-3 position-relative">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Étudiant *</label>
      <input type="hidden" id="id_etudiant">
      <div class="position-relative">
        <input type="text" class="form-control pe-5" id="id_etudiant_search" placeholder="Tapez le nom de l'étudiant..." autocomplete="off">
        <button type="button" id="clearSearch" class="btn p-0 border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y me-3 text-secondary-light ri-close-line" style="display:none;font-size:18px;line-height:1;" onclick="clearStudentSearch()"></button>
      </div>
      <div id="id_etudiant_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:220px;overflow-y:auto;"></div>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date *</label>
      <input type="date" class="form-control" id="date_absence" value="<?= date('Y-m-d') ?>">
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Motif</label>
      <textarea class="form-control" id="motif" rows="3" placeholder="Motif de l'absence"></textarea>
    </div>
    <div class="mb-3 form-check">
      <input type="checkbox" class="form-check-input" id="justifiee">
      <label class="form-check-label text-sm fw-semibold text-primary-light" for="justifiee">Justifiée</label>
    </div>
    <div class="col-12">
      <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
        <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeSidebar()">Annuler</button>
        <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-body pt-32 px-36 pb-24 text-center">
        <span class="mb-16 fs-1 line-height-1 text-danger d-block"><i class="ri-delete-bin-6-line"></i></span>
        <h6 class="text-lg fw-semibold text-primary-light mb-0">Confirmer la suppression ?</h6>
        <p class="text-sm text-secondary-light mt-8">Cette action est irréversible.</p>
        <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
          <button type="button" class="flex-grow-1 border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-24 py-11 radius-8" data-bs-dismiss="modal">Annuler</button>
          <button type="button" id="confirmDeleteBtn" class="flex-grow-1 btn btn-danger border border-danger-600 text-md px-16 py-12 radius-8">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js"></script>
<script src="<?= base_url() ?>assets/js/autocomplete.js"></script>
<script id="id_etudiant_data" type="application/json"><?= json_encode($etudiants) ?></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let deleteId = null;
let editingId = null;

async function loadData() {
  const r = await API.absences.list();
  if (!r.success) { $('#dataBody').html('<tr><td colspan="7" class="text-center text-danger">Erreur de chargement</td></tr>'); return; }
  let rows = '';
  r.data.forEach((a, i) => {
    const badge = a.justifiee ? 'bg-success-100 text-success-600' : 'bg-warning-100 text-warning-600';
    const text = a.justifiee ? 'Oui' : 'Non';
    rows += `<tr>
      <td>${i + 1}</td>
      <td><span class="fw-semibold">${a.nom} ${a.prenom}</span></td>
      <td>${a.matricule || '-'}</td>
      <td>${a.date_absence}</td>
      <td>${a.motif || '-'}</td>
      <td><span class="${badge} px-24 py-4 radius-4 fw-medium text-sm">${text}</span></td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="confirmDelete('${a.uuid}')"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li>
          </ul>
        </div>
      </td>
    </tr>`;
  });
  $('#dataBody').html(rows);
  if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().destroy();
  $('#dataTable').DataTable({
    pageLength: 10, scrollX: true,
    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucune absence trouvée', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

function openAddSidebar() {
  editingId = null;
  document.getElementById('sidebarTitle').textContent = 'Nouvelle absence';
  document.getElementById('mainForm').reset();
  document.getElementById('date_absence').value = new Date().toISOString().split('T')[0];
  clearStudentSearch();
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
}

function closeSidebar() {
  document.getElementById('addSidebar').classList.remove('active');
  document.getElementById('sidebarOverlay').classList.remove('active');
  clearStudentSearch();
}

document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);

document.getElementById('mainForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const data = {
    id_etudiant: document.getElementById('id_etudiant').value,
    date_absence: document.getElementById('date_absence').value,
    motif: document.getElementById('motif').value,
    justifiee: document.getElementById('justifiee').checked ? 1 : 0
  };
  if (!data.id_etudiant || !data.date_absence) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Étudiant et date obligatoires' });
    return;
  }
  const r = await API.absences.create(data);
  if (r.success) {
    closeSidebar();
    Toast.fire({ icon: 'success', title: 'Absence enregistrée' });
    loadData();
  } else {
    Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
  }
});

function confirmDelete(id) {
  deleteId = id;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
  if (!deleteId) return;
  const r = await API.absences.delete(deleteId);
  if (r.success) {
    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
    Toast.fire({ icon: 'success', title: 'Absence supprimée' });
    loadData();
  } else {
    Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
  }
  deleteId = null;
});

function applyFilters() { loadData(); }
function resetFilters() { $('#filterJustified, #filterDateStart').val(''); loadData(); }

function exportCSV() {
  const table = $('#dataTable').DataTable();
  const data = table.rows({ filter: 'applied' }).data();
  let csv = '\uFEFF';
  const headers = ['#', 'Étudiant', 'Matricule', 'Date', 'Motif', 'Justifiée'];
  csv += headers.join(',') + '\n';
  data.each(function(row) {
    const cols = [];
    for (let i = 0; i < 6; i++) {
      let val = $(row[i]).text().trim() || row[i] || '';
      val = '"' + val.replace(/"/g, '""') + '"';
      cols.push(val);
    }
    csv += cols.join(',') + '\n';
  });
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'absences.csv';
  link.click();
}

let etudiantsList = [];
try {
  const el = document.getElementById('id_etudiant_data');
  if (el) etudiantsList = JSON.parse(el.textContent);
} catch(e) {}

function renderStudentList(filter) {
  const container = document.getElementById('id_etudiant_results');
  const q = (filter || '').toLowerCase().trim();
  const matches = q ? etudiantsList.filter(e => (e.nom+' '+e.prenom+' '+(e.matricule||'')).toLowerCase().includes(q)) : etudiantsList;
  if (!matches.length) {
    container.innerHTML = '<div class="list-group-item text-secondary-light text-center py-3"><i class="ri-user-search-line me-1"></i>Aucun étudiant trouvé</div>';
  } else {
    container.innerHTML = matches.map(e =>
      `<button type="button" class="list-group-item list-group-item-action text-start d-flex align-items-center gap-2 py-2 px-3 border-0 border-bottom border-neutral-100" data-id="${e.id_etudiant}" data-nom="${e.nom}" data-prenom="${e.prenom}" data-matricule="${e.matricule||''}">
        <span class="d-flex align-items-center justify-content-center bg-primary-100 text-primary-600 radius-4" style="width:36px;height:36px;flex-shrink:0;"><i class="ri-user-3-line"></i></span>
        <div class="text-start">
          <span class="fw-medium text-sm">${e.nom} ${e.prenom}</span>
          <small class="d-block text-secondary-light text-xs">${e.matricule||'Sans matricule'}</small>
        </div>
      </button>`
    ).join('');
  }
  container.style.display = 'block';
}

document.getElementById('id_etudiant_search')?.addEventListener('focus', function() {
  if (!document.getElementById('id_etudiant').value) renderStudentList(this.value);
});

document.getElementById('id_etudiant_search')?.addEventListener('input', function() {
  const clearBtn = document.getElementById('clearSearch');
  document.getElementById('id_etudiant').value = '';
  document.getElementById('id_etudiant_search').classList.remove('border-success', 'border-2');
  if (this.value) clearBtn.style.display = 'block';
  else clearBtn.style.display = 'none';
  renderStudentList(this.value);
});

document.getElementById('id_etudiant_search')?.addEventListener('keydown', function(e) {
  if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
    const items = document.querySelectorAll('#id_etudiant_results button');
    if (!items.length) return;
    e.preventDefault();
    let idx = Array.from(items).indexOf(document.activeElement);
    idx = e.key === 'ArrowDown' ? Math.min(idx + 1, items.length - 1) : Math.max(idx - 1, 0);
    items[idx].focus();
  }
  if (e.key === 'Escape') document.getElementById('id_etudiant_results').style.display = 'none';
});

document.addEventListener('click', function(e) {
  const target = e.target.closest('#id_etudiant_results button');
  if (target) {
    selectStudent(target);
    return;
  }
  if (!e.target.closest('#id_etudiant_search') && !e.target.closest('#clearSearch')) {
    document.getElementById('id_etudiant_results').style.display = 'none';
  }
});

function selectStudent(el) {
  document.getElementById('id_etudiant').value = el.dataset.id;
  document.getElementById('id_etudiant_search').value = el.dataset.nom + ' ' + el.dataset.prenom + ' (' + el.dataset.matricule + ')';
  document.getElementById('id_etudiant_search').classList.add('border-success', 'border-2');
  document.getElementById('id_etudiant_results').style.display = 'none';
  document.getElementById('clearSearch').style.display = 'block';
}

function clearStudentSearch() {
  document.getElementById('id_etudiant').value = '';
  document.getElementById('id_etudiant_search').value = '';
  document.getElementById('id_etudiant_search').classList.remove('border-success', 'border-2');
  document.getElementById('id_etudiant_results').style.display = 'none';
  document.getElementById('clearSearch').style.display = 'none';
  document.getElementById('id_etudiant_search').focus();
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && $.fn && $.fn.DataTable && typeof API !== 'undefined' && API.absences) {
      clearInterval(wait);
      loadData();
      $('#dtSearch').on('keyup', function() { $('#dataTable').DataTable().search(this.value).draw(); });
      $('#dtLength').on('change', function() { $('#dataTable').DataTable().page.len(+this.value).draw(); });
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
