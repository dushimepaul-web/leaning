<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Comptabilité</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Comptabilité</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
      Nouvelle écriture
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
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export PDF',text:'Fonctionnalité à venir'})"><i class="ri-file-3-line"></i> PDF</button></li>
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export Excel',text:'Fonctionnalité à venir'})"><i class="ri-file-excel-line"></i> Excel</button></li>
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
                    <input type="text" id="filterSearch" class="form-control" placeholder="Mot-clé...">
                  </div>
                  <div><button type="reset" class="btn btn-danger-200 text-danger-600 w-100" onclick="resetFilters()">Réinitialiser</button></div>
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
              <th>Date</th>
              <th>Compte</th>
              <th>Libellé</th>
              <th>Débit</th>
              <th>Crédit</th>
              <th>Réf.</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="dataBody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<style>
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  z-index: 1040;
  display: none;
}
.overlay.active {
  display: block;
}
.addSidebar {
  position: fixed;
  top: 0;
  right: 0;
  width: 50vw;
  max-width: 100%;
  height: 100vh;
  z-index: 1050;
  transform: translateX(100%);
  transition: transform 0.3s ease;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.addSidebar.open {
  transform: translateX(0);
}
.addSidebar .sidebar-body {
  flex: 1;
  overflow-y: auto;
}
</style>

<div class="overlay" id="formOverlay"></div>
<div class="addSidebar bg-base" id="formSidebar">
  <div class="py-16 px-24 border-bottom d-flex align-items-center justify-content-between">
    <h5 class="modal-title" id="modalTitle">Nouvelle écriture</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <div class="sidebar-body p-24">
    <form id="mainForm">
      <input type="hidden" id="recordId">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date</label>
          <input type="date" class="form-control" id="date_ecriture" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Compte *</label>
          <select class="form-control form-select" id="id_plan">
            <option value="">Sélectionner</option>
            <?php foreach ($plans as $p): ?>
              <option value="<?= $p['id_plan'] ?>"><?= $p['code_compte'] ?> - <?= $p['libelle'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Libellé *</label>
          <input type="text" class="form-control" id="libelle" required placeholder="Libellé de l'écriture">
        </div>
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Débit</label>
          <input type="number" class="form-control" id="debit" step="0.01" value="0" placeholder="0.00">
        </div>
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Crédit</label>
          <input type="number" class="form-control" id="credit" step="0.01" value="0" placeholder="0.00">
        </div>
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Référence</label>
          <input type="text" class="form-control" id="reference" placeholder="Ex: FAC-001">
        </div>
        <div class="col-12">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Notes</label>
          <textarea class="form-control" id="notes" rows="2" placeholder="Notes..."></textarea>
        </div>
      </div>
    </form>
  </div>
  <div class="border-top py-16 px-24 d-flex align-items-center justify-content-end gap-3">
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

<script src="<?= base_url() ?>assets/js/api.js"></script>
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
  const res = await API.comptabilite.list();
  if (!res.success) { $('#dataBody').html('<tr><td colspan="8" class="text-center text-danger">Erreur</td></tr>'); return; }
  let rows = '';
  res.data.forEach((e, i) => {
    rows += `<tr>
      <td>${i + 1}</td>
      <td>${e.date_ecriture || '-'}</td>
      <td><span class="fw-semibold">${e.code_compte || '-'}</span> ${e.plan_libelle ? '- ' + e.plan_libelle : ''}</td>
      <td>${e.libelle}</td>
      <td><strong>${parseFloat(e.debit || 0).toLocaleString()}</strong></td>
      <td><strong>${parseFloat(e.credit || 0).toLocaleString()}</strong></td>
      <td>${e.reference || '-'}</td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editRecord('${e.uuid}')"><i class="ri-edit-2-line"></i> Modifier</button></li>
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="confirmDelete('${e.uuid}')"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li>
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
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucune écriture trouvée', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

function openAddSidebar() {
  editingId = null;
  document.getElementById('modalTitle').textContent = 'Nouvelle écriture';
  document.getElementById('mainForm').reset();
  document.getElementById('recordId').value = '';
  document.getElementById('date_ecriture').value = '<?= date('Y-m-d') ?>';
  document.getElementById('debit').value = '0';
  document.getElementById('credit').value = '0';
  document.getElementById('formSidebar').classList.add('open');
  document.getElementById('formOverlay').classList.add('active');
}

function openEditSidebar(data) {
  editingId = data.uuid;
  document.getElementById('modalTitle').textContent = 'Modifier l\'écriture';
  document.getElementById('recordId').value = data.uuid;
  document.getElementById('date_ecriture').value = data.date_ecriture || '';
  document.getElementById('id_plan').value = data.id_plan || '';
  document.getElementById('libelle').value = data.libelle || '';
  document.getElementById('debit').value = data.debit || 0;
  document.getElementById('credit').value = data.credit || 0;
  document.getElementById('reference').value = data.reference || '';
  document.getElementById('notes').value = data.notes || '';
  document.getElementById('formSidebar').classList.add('open');
  document.getElementById('formOverlay').classList.add('active');
}

function closeSidebar() {
  document.getElementById('formSidebar').classList.remove('open');
  document.getElementById('formOverlay').classList.remove('active');
}

async function editRecord(id) {
  const res = await API.comptabilite.list();
  if (res.success) {
    const item = res.data.find(e => e.uuid == id);
    if (item) openEditSidebar(item);
  }
}

async function saveRecord() {
  const data = {
    date_ecriture: document.getElementById('date_ecriture').value,
    id_plan: document.getElementById('id_plan').value,
    libelle: document.getElementById('libelle').value,
    debit: document.getElementById('debit').value || 0,
    credit: document.getElementById('credit').value || 0,
    reference: document.getElementById('reference').value,
    notes: document.getElementById('notes').value
  };
  if (!data.id_plan || !data.libelle) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Compte et libellé obligatoires' });
    return;
  }
  let r;
  if (editingId) {
    r = await API.comptabilite.update(editingId, data);
  } else {
    r = await API.comptabilite.create(data);
  }
  if (r.success) {
    closeSidebar();
    Toast.fire({ icon: 'success', title: editingId ? 'Écriture modifiée' : 'Écriture créée' });
    loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
}

function confirmDelete(id) {
  deleteId = id;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
  if (!deleteId) return;
  const r = await API.comptabilite.delete(deleteId);
  if (r.success) {
    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
    Toast.fire({ icon: 'success', title: 'Écriture supprimée' });
    loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
  deleteId = null;
});

document.getElementById('formOverlay').addEventListener('click', closeSidebar);

document.getElementById('mainForm').addEventListener('submit', function(e) {
  e.preventDefault();
  saveRecord();
});

function exportCSV() {
  const table = $('#dataTable').DataTable();
  const data = table.rows({ filter: 'applied' }).data();
  let csv = '\uFEFF';
  const headers = ['#', 'Date', 'Compte', 'Libellé', 'Débit', 'Crédit', 'Réf.'];
  csv += headers.join(',') + '\n';
  data.each(function(row) {
    const cols = [];
    for (let i = 0; i < 7; i++) {
      let val = $(row[i]).text().trim() || row[i] || '';
      val = '"' + val.replace(/"/g, '""') + '"';
      cols.push(val);
    }
    csv += cols.join(',') + '\n';
  });
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'comptabilite.csv';
  link.click();
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && $.fn && $.fn.DataTable && typeof API !== 'undefined') {
      clearInterval(wait);
      loadData();
      $('#dtSearch').on('keyup', function() { $('#dataTable').DataTable().search(this.value).draw(); });
      $('#dtLength').on('change', function() { $('#dataTable').DataTable().page.len(+this.value).draw(); });
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
