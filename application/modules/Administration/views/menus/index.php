<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des menus</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Administration / Menus</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
      Ajouter un menu
    </button>
  </div>
  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0 dataTable-wrapper">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
          <form class="navbar-search dt-search m-0">
            <input type="text" id="dtSearch" class="dt-input bg-transparent radius-4" placeholder="Rechercher...">
            <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
          </form>
          <div class="d-flex align-items-center gap-8 text-secondary-light">
            <span>Lignes par page :</span>
            <div class="dt-length"><select id="dtLength" class="dt-input form-control form-select">
              <option value="5">5</option>
              <option value="10" selected>10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select></div>
          </div>
        </div>
        <table class="table bordered-table mb-0 data-table" id="dataTable" style="width:100%">
          <thead>
            <tr>
              <th>Code</th>
              <th>Libellé</th>
              <th>Parent</th>
              <th>Icône</th>
              <th>Route</th>
              <th>Ordre</th>
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

<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="addSidebar" style="width:50vw;max-width:50vw;">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="sidebarTitle">Ajouter un menu</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="mainForm" class="d-flex flex-column p-20">
    <input type="hidden" id="recordId">
    <div class="row g-3">
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Code *</label>
        <input type="text" class="form-control" id="code" required placeholder="mon_menu">
      </div>
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Libellé *</label>
        <input type="text" class="form-control" id="libelle" required placeholder="Mon Menu">
      </div>
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Menu parent</label>
        <select class="form-control" id="parent_id">
          <option value="">Aucun (menu racine)</option>
        </select>
      </div>
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Icône (RemixIcon)</label>
        <input type="text" class="form-control" id="icon" placeholder="ri-menu-line">
      </div>
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Route</label>
        <input type="text" class="form-control" id="route" placeholder="Module/Controller/Method">
      </div>
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Ordre</label>
        <input type="number" class="form-control" id="ordre" placeholder="1" min="0">
      </div>
      <div class="col-12">
        <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
          <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeSidebar()">Annuler</button>
          <button type="submit" class="btn btn-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
        </div>
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
          <button type="button" id="confirmDeleteBtn" class="flex-grow-1 btn btn-danger text-md px-16 py-12 radius-8">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js"></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let editingId = null;
let deleteId = null;

function openAddSidebar() {
  editingId = null;
  document.getElementById('sidebarTitle').textContent = 'Ajouter un menu';
  document.getElementById('mainForm').reset();
  document.getElementById('recordId').value = '';
  loadParentSelect();
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
}

function openEditSidebar(data) {
  editingId = data.uuid;
  document.getElementById('sidebarTitle').textContent = 'Modifier le menu';
  document.getElementById('recordId').value = data.uuid;
  document.getElementById('code').value = data.code || '';
  document.getElementById('libelle').value = data.libelle || '';
  loadParentSelect(data.parent_id || '');
  document.getElementById('icon').value = data.icon || '';
  document.getElementById('route').value = data.route || '';
  document.getElementById('ordre').value = data.ordre || '';
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
}

function closeSidebar() {
  document.getElementById('addSidebar').classList.remove('active');
  document.getElementById('sidebarOverlay').classList.remove('active');
}

async function loadParentSelect(selectedId) {
  const res = await API.request('GET', 'api/menus');
  const sel = document.getElementById('parent_id');
  const currentVal = selectedId || sel.dataset.current || '';
  sel.innerHTML = '<option value="">Aucun (menu racine)</option>';
  if (res.success) {
    res.data.forEach(m => {
      if (m.uuid == editingId) return;
      const opt = document.createElement('option');
      opt.value = m.uuid;
      opt.textContent = m.libelle || m.code;
      if (m.uuid == currentVal) opt.selected = true;
      sel.appendChild(opt);
    });
  }
  sel.dataset.current = '';
}

async function loadData() {
  const res = await API.request('GET', 'api/menus');
  if (!res.success) { $('#dataBody').html('<tr><td colspan="7" class="text-center text-danger">Erreur</td></tr>'); return; }
  const parents = {};
  res.data.forEach(m => { if (m.parent_id) parents[m.parent_id] = true; });
  const parentNames = {};
  res.data.forEach(m => { parentNames[m.uuid] = m.libelle || m.code; });
  let rows = '';
  res.data.forEach((m, i) => {
    const parentName = m.parent_id ? (parentNames[m.parent_id] || '-') : '<span class="text-primary fw-semibold">Racine</span>';
    const hasChildren = parents[m.uuid];
    rows += `<tr>
      <td><span class="fw-semibold">${m.code || '-'}</span></td>
      <td>${m.libelle || '-'}</td>
      <td>${parentName}</td>
      <td>${m.icon ? '<i class="' + m.icon + ' text-lg"></i> ' + m.icon : '-'}</td>
      <td>${m.route || '-'}</td>
      <td>${m.ordre || 0}</td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editRecord('${m.uuid}')"><i class="ri-edit-2-line"></i> Modifier</button></li>
            ${hasChildren ? '' : '<li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="confirmDelete(\'' + m.uuid + '\')"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li>'}
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
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucun menu trouvé', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

async function editRecord(id) {
  const res = await API.request('GET', 'api/menus/' + id);
  if (res.success) openEditSidebar(res.data);
}

document.getElementById('mainForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const data = {
    code: document.getElementById('code').value,
    libelle: document.getElementById('libelle').value,
    parent_id: document.getElementById('parent_id').value || null,
    icon: document.getElementById('icon').value || null,
    route: document.getElementById('route').value || null,
    ordre: document.getElementById('ordre').value || 0
  };
  if (!data.code || !data.libelle) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Code et libellé obligatoires' }); return;
  }
  let r;
  if (editingId) {
    r = await API.request('POST', 'api/menus/' + editingId + '/update', data);
  } else {
    r = await API.request('POST', 'api/menus/create', data);
  }
  if (r.success) {
    closeSidebar(); Toast.fire({ icon: 'success', title: editingId ? 'Menu modifié' : 'Menu créé' }); loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
});

function confirmDelete(id) {
  deleteId = id;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
  if (!deleteId) return;
  const r = await API.request('GET', 'api/menus/' + deleteId + '/delete');
  if (r.success) {
    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
    Toast.fire({ icon: 'success', title: 'Menu supprimé' }); loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
  deleteId = null;
});

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
<?php include VIEWPATH.'includes/Footer.php'; ?>
