<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Inscriptions</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Étudiants / Inscriptions</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
      Nouvelle inscription
    </button>
  </div>
  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
          <div class="d-flex flex-wrap align-items-center gap-16">
            <div class="dropdown">
              <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown">
                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm"><i class="ri-file-upload-line text-md line-height-1"></i> Export</span>
                <span><i class="ri-arrow-down-s-line"></i></span>
              </button>
              <ul class="dropdown-menu p-12 border bg-base shadow">
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light d-flex align-items-center gap-10" onclick="exportCSV()"><i class="ri-file-excel-line"></i> CSV</button></li>
              </ul>
            </div>
            <form class="navbar-search dt-search m-0">
              <input type="text" id="dtSearch" class="dt-input bg-transparent radius-4" placeholder="Rechercher...">
              <i class="ri-search-line icon"></i>
            </form>
          </div>
          <div class="d-flex align-items-center gap-8 text-secondary-light">
            <span>Lignes par page :</span>
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
              <th>Étudiant</th>
              <th>Classe</th>
              <th>Section</th>
              <th>Année</th>
              <th>Date</th>
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

<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="addSidebar" style="width:50vw;max-width:50vw;box-shadow:-4px 0 20px rgba(0,0,0,0.1);">
  <div class="py-16 px-24 border-bottom d-flex align-items-center justify-content-between">
    <h5 class="text-lg mb-0" id="sidebarTitle">Nouvelle inscription</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <div class="p-24">
    <form id="mainForm">
      <input type="hidden" id="recordId">
      <div class="row g-3">
        <div class="col-12 position-relative">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Étudiant *</label>
          <input type="hidden" id="id_etudiant">
          <div class="position-relative">
            <input type="text" class="form-control pe-5" id="etudiantSearch" placeholder="Tapez le nom de l'étudiant..." autocomplete="off">
            <button type="button" id="clearSearch" class="btn p-0 border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y me-3 text-secondary-light ri-close-line" style="display:none;font-size:18px;line-height:1;" onclick="clearStudentSearch()"></button>
          </div>
          <div id="etudiantResults" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:220px;overflow-y:auto;"></div>
        </div>
        <div class="col-md-6 position-relative">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe *</label>
          <input type="hidden" id="id_classe">
          <div class="position-relative">
            <input type="text" class="form-control" id="id_classe_search" placeholder="Rechercher..." autocomplete="off">
          </div>
          <div id="id_classe_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
        </div>
        <div class="col-md-6 position-relative">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Section</label>
          <input type="hidden" id="id_section">
          <div class="position-relative">
            <input type="text" class="form-control" id="id_section_search" placeholder="Rechercher..." autocomplete="off">
          </div>
          <div id="id_section_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
        </div>
        <div class="col-md-6 position-relative">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Année scolaire</label>
          <input type="hidden" id="id_annee" value="<?php foreach ($annees as $a) { if ($a['est_en_cours']) { echo $a['id_annee']; break; } } ?>">
          <div class="position-relative">
            <input type="text" class="form-control" id="id_annee_search" placeholder="Rechercher..." autocomplete="off" value="<?php foreach ($annees as $a) { if ($a['est_en_cours']) { echo $a['libelle']; break; } } ?>">
          </div>
          <div id="id_annee_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
        </div>
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date inscription</label>
          <input type="date" class="form-control" id="date_inscription" value="<?= date('Y-m-d') ?>">
        </div>
      </div>
    </form>
  </div>
  <div class="p-24 border-top d-flex align-items-center justify-content-end gap-3">
    <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeSidebar()">Annuler</button>
    <button type="button" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8" onclick="saveRecord()">Enregistrer</button>
  </div>
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

<script id="etudiantsData" type="application/json"><?= json_encode($etudiants) ?></script>
<script id="classes_data" type="application/json"><?= json_encode($classes) ?></script>
<script id="sections_data" type="application/json"><?= json_encode($sections) ?></script>
<script id="annees_data" type="application/json"><?= json_encode($annees) ?></script>
<script src="<?= base_url() ?>assets/js/autocomplete.js?v=<?= filemtime(FCPATH.'assets/js/autocomplete.js') ?>"></script>
<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let deleteId = null;
let etudiantsList = [];
let classesList = [];
let sectionsList = [];
let anneesList = [];

try {
  const el = document.getElementById('etudiantsData');
  if (el) etudiantsList = JSON.parse(el.textContent);
} catch(e) {}
try {
  const el = document.getElementById('classes_data');
  if (el) classesList = JSON.parse(el.textContent);
} catch(e) {}
try {
  const el = document.getElementById('sections_data');
  if (el) sectionsList = JSON.parse(el.textContent);
} catch(e) {}
try {
  const el = document.getElementById('annees_data');
  if (el) anneesList = JSON.parse(el.textContent);
} catch(e) {}

function renderStudentList(filter) {
  const container = document.getElementById('etudiantResults');
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
          <small class="d-block text-secondary-light text-xs">${e.matricule||'No matricule'}</small>
        </div>
      </button>`
    ).join('');
  }
  container.style.display = 'block';
}

document.getElementById('etudiantSearch')?.addEventListener('focus', function() {
  if (!document.getElementById('id_etudiant').value) renderStudentList(this.value);
});

document.getElementById('etudiantSearch')?.addEventListener('input', function() {
  const clearBtn = document.getElementById('clearSearch');
  document.getElementById('id_etudiant').value = '';
  if (this.value) clearBtn.style.display = 'block';
  else clearBtn.style.display = 'none';
  renderStudentList(this.value);
});

document.getElementById('etudiantSearch')?.addEventListener('keydown', function(e) {
  if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
    const items = document.querySelectorAll('#etudiantResults button');
    if (!items.length) return;
    e.preventDefault();
    let idx = Array.from(items).indexOf(document.activeElement);
    idx = e.key === 'ArrowDown' ? Math.min(idx + 1, items.length - 1) : Math.max(idx - 1, 0);
    items[idx].focus();
  }
  if (e.key === 'Escape') document.getElementById('etudiantResults').style.display = 'none';
});

document.addEventListener('click', function(e) {
  const target = e.target.closest('#etudiantResults button');
  if (target) {
    selectStudent(target);
    return;
  }
  if (!e.target.closest('#etudiantSearch') && !e.target.closest('#clearSearch')) {
    document.getElementById('etudiantResults').style.display = 'none';
  }
});

function selectStudent(el) {
  document.getElementById('id_etudiant').value = el.dataset.id;
  document.getElementById('etudiantSearch').value = el.dataset.nom + ' ' + el.dataset.prenom + ' (' + el.dataset.matricule + ')';
  document.getElementById('etudiantSearch').classList.add('border-success', 'border-2');
  document.getElementById('etudiantResults').style.display = 'none';
  document.getElementById('clearSearch').style.display = 'block';
  Toast.fire({ icon: 'success', title: 'Étudiant sélectionné' });
}

function clearStudentSearch() {
  document.getElementById('id_etudiant').value = '';
  document.getElementById('etudiantSearch').value = '';
  document.getElementById('etudiantSearch').classList.remove('border-success', 'border-2');
  document.getElementById('etudiantResults').style.display = 'none';
  document.getElementById('clearSearch').style.display = 'none';
  document.getElementById('etudiantSearch').focus();
}

async function loadData() {
  const res = await API.inscriptions.list();
  if (!res.success) { $('#dataBody').html('<tr><td colspan="7" class="text-center text-danger">Erreur</td></tr>'); return; }
  let rows = '';
  res.data.forEach((ins, i) => {
    rows += `<tr>
      <td>${i + 1}</td>
      <td><span class="fw-semibold">${ins.nom} ${ins.prenom}</span> <small class="text-secondary-light">(${ins.matricule || ''})</small></td>
      <td>${ins.classe || '-'}</td>
      <td>${ins.section || '-'}</td>
      <td>${ins.annee || '-'}</td>
      <td>${ins.date_inscription || '-'}</td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="confirmDelete(${ins.uuid})"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li>
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
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucune inscription trouvée', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

function openAddSidebar() {
  document.getElementById('mainForm').reset();
  clearStudentSearch();
  document.getElementById('recordId').value = '';
  document.getElementById('date_inscription').value = '<?= date('Y-m-d') ?>';
  document.getElementById('sidebarTitle').textContent = 'Nouvelle inscription';
  document.getElementById('sidebarOverlay').classList.add('active');
  document.getElementById('addSidebar').classList.add('active');
}

function openEditSidebar(data) {
  document.getElementById('mainForm').reset();
  document.getElementById('recordId').value = data.uuid;
  document.getElementById('id_etudiant').value = data.id_etudiant;
  const e = etudiantsList.find(x => String(x.id_etudiant) === String(data.id_etudiant));
  document.getElementById('etudiantSearch').value = e ? (e.nom + ' ' + e.prenom + ' (' + (e.matricule||'') + ')') : '';
  document.getElementById('id_classe').value = data.id_classe;
  document.getElementById('id_section').value = data.id_section || '';
  document.getElementById('id_annee').value = data.id_annee || '';
  var _c = classesList.find(function(x) { return String(x.id_classe) === String(data.id_classe); });
  if (_c) document.getElementById('id_classe_search').value = _c.libelle;
  var _s = sectionsList.find(function(x) { return String(x.id_section) === String(data.id_section); });
  if (_s) document.getElementById('id_section_search').value = _s.libelle;
  var _a = anneesList.find(function(x) { return String(x.id_annee) === String(data.id_annee); });
  if (_a) document.getElementById('id_annee_search').value = _a.libelle;
  document.getElementById('date_inscription').value = data.date_inscription || '<?= date('Y-m-d') ?>';
  document.getElementById('sidebarTitle').textContent = 'Modifier l\'inscription';
  document.getElementById('sidebarOverlay').classList.add('active');
  document.getElementById('addSidebar').classList.add('active');
}

function closeSidebar() {
  document.getElementById('sidebarOverlay').classList.remove('active');
  document.getElementById('addSidebar').classList.remove('active');
}

async function saveRecord() {
  const data = {
    id_etudiant: document.getElementById('id_etudiant').value,
    id_classe: document.getElementById('id_classe').value,
    id_section: document.getElementById('id_section').value || null,
    id_annee: document.getElementById('id_annee').value || null,
    date_inscription: document.getElementById('date_inscription').value
  };
  if (!data.id_etudiant || !data.id_classe) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Étudiant et classe obligatoires' });
    return;
  }
  const id = document.getElementById('recordId').value;
  const r = id ? await API.inscriptions.update(id, data) : await API.inscriptions.create(data);
  if (r.success) {
    closeSidebar();
    Toast.fire({ icon: 'success', title: id ? 'Inscription modifiée' : 'Inscription créée' });
    loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
}

function confirmDelete(id) {
  deleteId = id;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
  if (!deleteId) return;
  const r = await API.inscriptions.delete(deleteId);
  if (r.success) {
    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
    Toast.fire({ icon: 'success', title: 'Inscription supprimée' });
    loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
  deleteId = null;
});

document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);

function exportCSV() {
  const table = $('#dataTable').DataTable();
  const data = table.rows({ filter: 'applied' }).data();
  let csv = '\uFEFF';
  const headers = ['#', 'Étudiant', 'Classe', 'Section', 'Année', 'Date'];
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
  link.download = 'inscriptions.csv';
  link.click();
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && $.fn && $.fn.DataTable && typeof API !== 'undefined' && API.inscriptions) {
      clearInterval(wait);
      loadData();
      $('#dtSearch').on('keyup', function() { $('#dataTable').DataTable().search(this.value).draw(); });
      $('#dtLength').on('change', function() { $('#dataTable').DataTable().page.len(+this.value).draw(); });
      autoSetup('id_classe_search', 'id_classe', 'id_classe_results', classesList.map(function(c) { return { id: c.id_classe, libelle: c.libelle }; }), function(c) { return c.libelle; });
      autoSetup('id_section_search', 'id_section', 'id_section_results', sectionsList.map(function(s) { return { id: s.id_section, libelle: s.libelle }; }), function(s) { return s.libelle; });
      autoSetup('id_annee_search', 'id_annee', 'id_annee_results', anneesList.map(function(a) { return { id: a.id_annee, libelle: a.libelle }; }), function(a) { return a.libelle; });
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
