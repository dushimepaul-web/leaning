<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Messagerie</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Messages</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
      Nouveau message
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
              <th>Sujet</th>
              <th>Expéditeur</th>
              <th>Destinataire</th>
              <th>Date</th>
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

<style>
.overlay {
  position: fixed;
  top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  z-index: 1040;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.3s ease, visibility 0.3s ease;
}
.overlay.active {
  opacity: 1;
  visibility: visible;
}
.addSidebar {
  position: fixed;
  top: 0; right: -50vw;
  width: 50vw;
  height: 100%;
  background: #fff;
  z-index: 1050;
  transition: right 0.3s ease;
  overflow-y: auto;
  box-shadow: -4px 0 12px rgba(0,0,0,0.1);
}
.addSidebar.open {
  right: 0;
}
.addSidebar-content {
  display: flex;
  flex-direction: column;
  height: 100%;
}
.addSidebar-header {
  padding: 16px 24px;
  border-bottom: 1px solid #dee2e6;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.addSidebar-body {
  flex: 1;
  padding: 24px;
  overflow-y: auto;
}
.addSidebar-footer {
  padding: 16px 24px;
  border-top: 1px solid #dee2e6;
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}
</style>

<div class="overlay" id="formOverlay"></div>
<div class="addSidebar" id="formSidebar">
  <div class="addSidebar-content">
    <div class="addSidebar-header">
      <h5 class="modal-title" id="sidebarTitle">Nouveau message</h5>
      <button type="button" class="btn-close" onclick="closeSidebar()"></button>
    </div>
    <div class="addSidebar-body">
      <form id="mainForm">
        <div class="row g-3">
          <div class="col-12 position-relative">
            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Destinataire</label>
            <input type="hidden" id="id_destinataire">
            <input type="text" class="form-control" id="id_destinataire_search" placeholder="Rechercher..." autocomplete="off">
            <div id="id_destinataire_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
          </div>
          <div class="col-12">
            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Sujet *</label>
            <input type="text" class="form-control" id="sujet" required placeholder="Sujet du message">
          </div>
          <div class="col-12">
            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Message *</label>
            <textarea class="form-control" id="corps" rows="4" required placeholder="Votre message..."></textarea>
          </div>
        </div>
      </form>
    </div>
    <div class="addSidebar-footer">
      <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeSidebar()">Annuler</button>
      <button type="button" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8" onclick="saveRecord()">Envoyer</button>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js?v=1"></script>
<script src="<?= base_url() ?>assets/js/autocomplete.js"></script>
<script id="id_destinataire_data" type="application/json"><?= json_encode($utilisateurs) ?></script>
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

async function loadData() {
  const res = await API.messages.list();
  if (!res.success) { $('#dataBody').html('<tr><td colspan="7" class="text-center text-danger">Erreur</td></tr>'); return; }
  let rows = '';
  res.data.forEach((m, i) => {
    const statusBadge = m.lu ? 'bg-success-100 text-success-600' : 'bg-warning-100 text-warning-600';
    rows += `<tr>
      <td>${i + 1}</td>
      <td><span class="fw-semibold">${m.sujet}</span></td>
      <td>${m.id_expediteur || '-'}</td>
      <td>${m.id_destinataire || 'Tous'}</td>
      <td>${m.cree_le || '-'}</td>
      <td><span class="${statusBadge} px-24 py-4 radius-4 fw-medium text-sm">${m.lu ? 'Lu' : 'Non lu'}</span></td>
      <td>
        <button class="btn btn-sm btn-primary-600" onclick="viewMessage('${m.uuid}')"><i class="ri-eye-line"></i></button>
      </td>
    </tr>`;
  });
  $('#dataBody').html(rows);
  if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().destroy();
  $('#dataTable').DataTable({
    pageLength: 10, scrollX: true,
    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucun message trouvé', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

function openAddSidebar() {
  document.getElementById('mainForm').reset();
  document.getElementById('sidebarTitle').textContent = 'Nouveau message';
  document.getElementById('formSidebar').classList.add('open');
  document.getElementById('formOverlay').classList.add('active');
}

function closeSidebar() {
  document.getElementById('formSidebar').classList.remove('open');
  document.getElementById('formOverlay').classList.remove('active');
}

async function saveRecord() {
  const data = {
    id_destinataire: document.getElementById('id_destinataire').value || null,
    sujet: document.getElementById('sujet').value,
    corps: document.getElementById('corps').value
  };
  if (!data.sujet || !data.corps) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Sujet et message obligatoires' }); return; }
  const r = await API.messages.create(data);
  if (r.success) {
    closeSidebar();
    Toast.fire({ icon: 'success', title: 'Message envoyé' });
    loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
}

function viewMessage(id) {
  Swal.fire({ title: 'Message', text: 'Fonctionnalité à venir', icon: 'info' });
}

function exportCSV() {
  const table = $('#dataTable').DataTable();
  const data = table.rows({ filter: 'applied' }).data();
  let csv = '\uFEFF';
  const headers = ['#', 'Sujet', 'Expéditeur', 'Destinataire', 'Date', 'Statut'];
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
  link.download = 'messages.csv';
  link.click();
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && $.fn && $.fn.DataTable && typeof API !== 'undefined') {
      clearInterval(wait);
      loadData();
      autoSetup('id_destinataire_search', 'id_destinataire', 'id_destinataire_results',
        JSON.parse(document.getElementById('id_destinataire_data').textContent).map(function(u) { return {id: u.uuid, nom_utilisateur: u.nom_utilisateur, email: u.email}; }),
        function(u) { return u.nom_utilisateur + ' (' + u.email + ')'; }
      );
      $('#dtSearch').on('keyup', function() { $('#dataTable').DataTable().search(this.value).draw(); });
      $('#dtLength').on('change', function() { $('#dataTable').DataTable().page.len(+this.value).draw(); });
      document.getElementById('formOverlay').addEventListener('click', closeSidebar);
      document.getElementById('mainForm').addEventListener('submit', function(e) { e.preventDefault(); saveRecord(); });
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
