<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Assurances</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Assurances</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
      Nouvelle assurance
    </button>
  </div>
  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0 dataTable-wrapper">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
          <div class="d-flex flex-wrap align-items-center gap-16">
            <div class="dropdown">
              <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown">
                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm"><i class="ri-file-upload-line text-md line-height-1"></i> Export</span>
                <span><i class="ri-arrow-down-s-line"></i></span>
              </button>
              <ul class="dropdown-menu p-12 border bg-base shadow">
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export PDF',text:'Fonctionnalit� � venir'})"><i class="ri-file-3-line"></i> PDF</button></li>
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export Excel',text:'Fonctionnalit� � venir'})"><i class="ri-file-excel-line"></i> Excel</button></li>
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="exportCSV()"><i class="ri-file-excel-line"></i> CSV</button></li>
              </ul>
            </div>
            <form class="navbar-search dt-search m-0">
              <input type="text" id="dtSearch" class="dt-input bg-transparent radius-4" aria-controls="dataTable" name="search" placeholder="Rechercher...">
              <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
            </form>
          </div>
          <div class="d-flex align-items-center gap-8 text-secondary-light">
            <div class="dropdown">
              <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">Filtrer</span>
                <span class=""><i class="ri-arrow-down-s-line"></i></span>
              </button>
              <div class="dropdown-menu border bg-base shadow dropdown-menu-lg p-0">
                <div class="d-flex align-items-center justify-content-between border-bottom py-8 px-16">
                  <span class="fw-semibold text-lg text-primary-light">Filtre</span>
                </div>
                <form class="p-16 d-grid grid-cols-2 gap-16">
                  <div>
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Statut</label>
                    <select id="filterStatut" class="form-control form-select">
                      <option value="">Tous</option>
                      <option value="actif">Actif</option>
                      <option value="inactif">Inactif</option>
                    </select>
                  </div>
                  <div>
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Recherche</label>
                    <input type="text" id="filterSearch" class="form-control" placeholder="Mot-cl�...">
                  </div>
                  <div><button type="reset" class="btn btn-danger-200 text-danger-600 w-100" onclick="resetFilters()">R�initialiser</button></div>
                  <div><button type="button" class="btn btn-primary-600 w-100" onclick="applyFilters()">Appliquer</button></div>
                </form>
              </div>
            </div>
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
              <th>Police</th>
              <th>Compagnie</th>
              <th>Début</th>
              <th>Fin</th>
              <th>Montant</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="dataBody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Overlay -->
<div class="overlay"></div>

<!-- Form Sidebar -->
<div id="addSidebar" class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 max-w-700-px w-100 translate-x-full duration-300 active-translate-0">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="modalTitle">Nouvelle assurance</h5>
    <button type="button" class="close-sidebar text-danger-600 text-lg d-flex">
      <i class="ri-close-large-line"></i>
    </button>
  </div>
  <form id="mainForm" class="p-20">
    <input type="hidden" id="recordId">
    <div class="row g-3">
      <div class="col-12 position-relative">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Étudiant *</label>
        <input type="hidden" id="id_etudiant">
        <input type="text" class="form-control" id="id_etudiant_search" placeholder="Rechercher..." autocomplete="off">
        <div id="id_etudiant_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Police *</label>
        <input type="text" class="form-control" id="police" placeholder="Numéro de police">
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Compagnie</label>
        <input type="text" class="form-control" id="compagnie" placeholder="Compagnie d'assurance">
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date début *</label>
        <input type="date" class="form-control" id="date_debut">
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date fin *</label>
        <input type="date" class="form-control" id="date_fin">
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Montant</label>
        <input type="number" class="form-control" id="montant" step="0.01" placeholder="0.00">
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Statut</label>
        <select class="form-control form-select" id="statut">
          <option value="active">Active</option>
          <option value="expiree">Expirée</option>
          <option value="resiliee">Résiliée</option>
        </select>
      </div>
    </div>
    <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
      <button type="reset" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">Annuler</button>
      <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
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


function applyFilters() {
  const searchVal = document.getElementById('filterSearch')?.value || '';
  $('#dataTable').DataTable().search(searchVal).draw();
}

function resetFilters() {
  if (document.getElementById('filterSearch')) document.getElementById('filterSearch').value = '';
  if (document.getElementById('filterStatut')) document.getElementById('filterStatut').value = '';
  $('#dataTable').DataTable().search('').draw();
}
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let editingId = null;
let deleteId = null;

async function loadData() {
  const res = await API.assurances.list();
  if (!res.success) { $('#dataBody').html('<tr><td colspan="9" class="text-center text-danger">Erreur</td></tr>'); return; }
  let rows = '';
  res.data.forEach((a, i) => {
    const statusBadge = a.statut === 'active' ? 'bg-success-100 text-success-600' : (a.statut === 'expiree' ? 'bg-warning-100 text-warning-600' : 'bg-danger-100 text-danger-600');
    rows += `<tr>
      <td>${i + 1}</td>
      <td><span class="fw-semibold">${a.nom} ${a.prenom}</span> <small class="text-secondary-light">(${a.matricule || ''})</small></td>
      <td>${a.police}</td>
      <td>${a.compagnie || '-'}</td>
      <td>${a.date_debut}</td>
      <td>${a.date_fin}</td>
      <td>${parseFloat(a.montant || 0).toLocaleString()}</td>
      <td><span class="${statusBadge} px-24 py-4 radius-4 fw-medium text-sm text-capitalize">${a.statut}</span></td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editRecord('${a.uuid}')"><i class="ri-edit-2-line"></i> Modifier</button></li>
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
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucune assurance trouvée', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

function openAddSidebar() {
  editingId = null;
  document.getElementById('modalTitle').textContent = 'Nouvelle assurance';
  document.getElementById('mainForm').reset();
  document.getElementById('recordId').value = '';
  document.getElementById('addSidebar').classList.add('active');
  document.querySelector('.overlay').classList.add('active');
}

function openEditSidebar(data) {
  editingId = data.uuid;
  document.getElementById('modalTitle').textContent = "Modifier l'assurance";
  document.getElementById('recordId').value = data.uuid;
  document.getElementById('id_etudiant').value = data.id_etudiant || '';
  var etu = JSON.parse(document.getElementById('id_etudiant_data').textContent).find(function(e) { return e.id_etudiant == data.id_etudiant; });
  document.getElementById('id_etudiant_search').value = etu ? etu.nom + ' ' + etu.prenom + ' (' + (etu.matricule || '') + ')' : '';
  document.getElementById('police').value = data.police || '';
  document.getElementById('compagnie').value = data.compagnie || '';
  document.getElementById('date_debut').value = data.date_debut || '';
  document.getElementById('date_fin').value = data.date_fin || '';
  document.getElementById('montant').value = data.montant || 0;
  document.getElementById('statut').value = data.statut || 'active';
  document.getElementById('addSidebar').classList.add('active');
  document.querySelector('.overlay').classList.add('active');
}

async function editRecord(id) {
  const res = await API.assurances.list();
  if (res.success) {
    const item = res.data.find(a => a.uuid == id);
    if (item) openEditSidebar(item);
  }
}

function closeSidebar() {
  document.getElementById('addSidebar').classList.remove('active');
  document.querySelector('.overlay').classList.remove('active');
}

document.getElementById('mainForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const data = {
    id_etudiant: document.getElementById('id_etudiant').value,
    police: document.getElementById('police').value,
    compagnie: document.getElementById('compagnie').value,
    date_debut: document.getElementById('date_debut').value,
    date_fin: document.getElementById('date_fin').value,
    montant: document.getElementById('montant').value || 0,
    statut: document.getElementById('statut').value
  };
  if (!data.id_etudiant || !data.police || !data.date_debut || !data.date_fin) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Étudiant, police, date début et date fin obligatoires' });
    return;
  }
  let r;
  if (editingId) {
    r = await API.assurances.update(editingId, data);
  } else {
    r = await API.assurances.create(data);
  }
  if (r.success) {
    closeSidebar();
    Toast.fire({ icon: 'success', title: editingId ? 'Assurance modifiée' : 'Assurance créée' });
    loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
});

function confirmDelete(id) {
  deleteId = id;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
  if (!deleteId) return;
  const r = await API.assurances.delete(deleteId);
  if (r.success) {
    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
    Toast.fire({ icon: 'success', title: 'Assurance supprimée' });
    loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
  deleteId = null;
});

document.querySelector('.overlay').addEventListener('click', closeSidebar);
document.querySelector('.close-sidebar').addEventListener('click', closeSidebar);

function exportCSV() {
  const table = $('#dataTable').DataTable();
  const data = table.rows({ filter: 'applied' }).data();
  let csv = '\uFEFF';
  const headers = ['#', 'Étudiant', 'Police', 'Compagnie', 'Début', 'Fin', 'Montant', 'Statut'];
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
  link.download = 'assurances.csv';
  link.click();
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && $.fn && $.fn.DataTable && typeof API !== 'undefined') {
      clearInterval(wait);
      loadData();
      autoSetup('id_etudiant_search', 'id_etudiant', 'id_etudiant_results', JSON.parse(document.getElementById('id_etudiant_data').textContent).map(function(e) { return { id: e.id_etudiant, nom: e.nom, prenom: e.prenom, matricule: e.matricule }; }), function(e) { return e.nom + ' ' + e.prenom + ' (' + (e.matricule || '') + ')'; });
      $('#dtSearch').on('keyup', function() { $('#dataTable').DataTable().search(this.value).draw(); });
      $('#dtLength').on('change', function() { $('#dataTable').DataTable().page.len(+this.value).draw(); });
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
