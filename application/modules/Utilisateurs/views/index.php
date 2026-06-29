<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des utilisateurs</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Utilisateurs</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
      Ajouter un utilisateur
    </button>
  </div>
  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0 dataTable-wrapper">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
          <div class="d-flex flex-wrap align-items-center gap-16">
            <div class="dropdown">
              <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown" aria-expanded="false">
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
              <th>Nom complet</th>
              <th>Email</th>
              <th>Rôle</th>
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

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="sidebarOverlay"></div>

<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="addSidebar" style="width:50vw;max-width:50vw;box-shadow: -4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="sidebarTitle">Ajouter un utilisateur</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="mainForm" class="d-flex flex-column p-20">
    <input type="hidden" id="recordId">
    <div class="row g-3">
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Nom complet *</label>
        <input type="text" class="form-control" id="nom_complet" required placeholder="Nom et prénom">
      </div>
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Email *</label>
        <input type="email" class="form-control" id="email" required placeholder="email@exemple.com">
      </div>
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Mot de passe *</label>
        <input type="password" class="form-control" id="mot_de_passe" placeholder="••••••••">
      </div>
      <div class="col-12 position-relative">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Rôle *</label>
        <input type="hidden" id="id_role">
        <input type="text" class="form-control" id="id_role_search" placeholder="Rechercher..." autocomplete="off">
        <div id="id_role_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
      </div>
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Statut</label>
        <select class="form-control form-select" id="actif">
          <option value="1">Actif</option>
          <option value="0">Inactif</option>
        </select>
      </div>
      <div class="col-12">
        <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
          <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeSidebar()">Annuler</button>
          <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
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
          <button type="button" id="confirmDeleteBtn" class="flex-grow-1 btn btn-danger border border-danger-600 text-md px-16 py-12 radius-8">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js"></script>
<script src="<?= base_url() ?>assets/js/autocomplete.js"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let editingId = null;
let deleteId = null;

function openAddSidebar() {
  editingId = null;
  document.getElementById('sidebarTitle').textContent = 'Ajouter un utilisateur';
  document.getElementById('mainForm').reset();
  document.getElementById('recordId').value = '';
  document.getElementById('mot_de_passe').required = true;
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
}

function openEditSidebar(data) {
  editingId = data.uuid;
  document.getElementById('sidebarTitle').textContent = "Modifier l'utilisateur";
  document.getElementById('recordId').value = data.uuid;
  document.getElementById('nom_complet').value = data.nom_complet || data.nom_utilisateur || '';
  document.getElementById('email').value = data.email || '';
  document.getElementById('mot_de_passe').value = '';
  document.getElementById('mot_de_passe').required = false;
  document.getElementById('id_role').value = data.id_role || '';
  document.getElementById('id_role_search').value = rolesMap[data.id_role] || '';
  document.getElementById('actif').value = data.actif || '1';
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
}

function closeSidebar() {
  document.getElementById('addSidebar').classList.remove('active');
  document.getElementById('sidebarOverlay').classList.remove('active');
}

var rolesData = [];
var rolesMap = {};

async function loadRoles() {
  const r = await API.roles.list();
  if (!r.success) return;
  rolesData = r.data.map(function(role) {
    rolesMap[role.id_role] = role.libelle;
    return {id: role.id_role, libelle: role.libelle};
  });
  autoSetup('id_role_search', 'id_role', 'id_role_results', rolesData, function(r) { return r.libelle; });
}

async function loadData() {
  const res = await API.utilisateurs.list();
  if (!res.success) { $('#dataBody').html('<tr><td colspan="6" class="text-center text-danger">Erreur</td></tr>'); return; }
  let rows = '';
  res.data.forEach((u, i) => {
    const statusBadge = u.actif == 1 ? 'bg-success-100 text-success-600' : 'bg-danger-100 text-danger-600';
    const statusText = u.actif == 1 ? 'Actif' : 'Inactif';
    rows += `<tr>
      <td><div class="form-check style-check d-flex align-items-center"><input class="form-check-input" type="checkbox"></div></td>
      <td><span class="fw-semibold">${u.nom_complet || u.nom_utilisateur || '-'}</span></td>
      <td>${u.email || '-'}</td>
      <td><span class="px-24 py-4 radius-4 fw-medium text-sm bg-primary-100 text-primary-600">${u.role_libelle || '-'}</span></td>
      <td><span class="${statusBadge} px-24 py-4 radius-4 fw-medium text-sm">${statusText}</span></td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editRecord('${u.uuid}')"><i class="ri-edit-2-line"></i> Modifier</button></li>
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="confirmDelete('${u.uuid}')"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li>
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
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucun utilisateur trouvé', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

async function editRecord(id) {
  const res = await API.utilisateurs.get(id);
  if (res.success) openEditSidebar(res.data);
}

document.getElementById('mainForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const data = {
    nom_complet: document.getElementById('nom_complet').value,
    email: document.getElementById('email').value,
    mot_de_passe: document.getElementById('mot_de_passe').value,
    id_role: document.getElementById('id_role').value || null,
    actif: document.getElementById('actif').value
  };
  if (!data.nom_complet || !data.email) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Nom complet et email obligatoires' });
    return;
  }
  if (!editingId && !data.mot_de_passe) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Mot de passe obligatoire pour un nouvel utilisateur' });
    return;
  }
  if (!editingId && data.mot_de_passe.length < 4) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Le mot de passe doit contenir au moins 4 caractères' });
    return;
  }
  let r;
  if (editingId) {
    if (!data.mot_de_passe) delete data.mot_de_passe;
    r = await API.utilisateurs.update(editingId, data);
  } else {
    r = await API.utilisateurs.create(data);
  }
  if (r.success) {
    closeSidebar();
    Toast.fire({ icon: 'success', title: editingId ? 'Utilisateur modifié' : 'Utilisateur créé' });
    loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
});

function confirmDelete(id) {
  deleteId = id;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
  if (!deleteId) return;
  const r = await API.utilisateurs.delete(deleteId);
  if (r.success) {
    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
    Toast.fire({ icon: 'success', title: 'Utilisateur supprimé' });
    loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
  deleteId = null;
});

function exportCSV() {
  const table = $('#dataTable').DataTable();
  const data = table.rows({ filter: 'applied' }).data();
  let csv = '\uFEFF';
  const headers = ['Nom complet', 'Email', 'Rôle', 'Statut'];
  csv += headers.join(',') + '\n';
  data.each(function(row) {
    const cols = [];
    for (let i = 1; i < 5; i++) {
      let val = $(row[i]).text().trim() || row[i] || '';
      val = '"' + val.replace(/"/g, '""') + '"';
      cols.push(val);
    }
    csv += cols.join(',') + '\n';
  });
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'utilisateurs.csv';
  link.click();
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && $.fn && $.fn.DataTable) {
      clearInterval(wait);
      loadRoles();
      loadData();
      $('#dtSearch').on('keyup', function() { $('#dataTable').DataTable().search(this.value).draw(); });
      $('#dtLength').on('change', function() { $('#dataTable').DataTable().page.len(+this.value).draw(); });
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>