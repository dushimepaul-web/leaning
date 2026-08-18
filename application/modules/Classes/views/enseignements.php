<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Enseignements</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Classes / Enseignements</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
      Ajouter un enseignement
    </button>
  </div>

  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
          <div class="d-flex flex-wrap align-items-center gap-16">
            <div class="dropdown">
              <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">
                  <i class="ri-file-upload-line text-md line-height-1"></i>
                  Export
                </span>
                <span><i class="ri-arrow-down-s-line"></i></span>
              </button>
              <ul class="dropdown-menu p-12 border bg-base shadow">
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="exportCSV()"><i class="ri-file-excel-line"></i> CSV</button></li>
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
              <th>Enseignant</th>
              <th>Matière</th>
              <th>Classe</th>
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
    <h5 class="text-lg mb-0" id="sidebarTitle">Ajouter un enseignement</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="mainForm" class="d-flex flex-column p-20">
    <input type="hidden" id="recordId">
    <div class="row g-3">
      <div class="col-sm-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Enseignant *</label>
        <select class="form-control form-select" id="id_enseignant" required>
          <option value="">-- Sélectionner --</option>
        </select>
      </div>
      <div class="col-sm-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Matière *</label>
        <select class="form-control form-select" id="id_matiere" required>
          <option value="">-- Sélectionner --</option>
        </select>
      </div>
      <div class="col-sm-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe *</label>
        <select class="form-control form-select" id="id_classe" required>
          <option value="">-- Sélectionner --</option>
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

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
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
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

let editingId = null;
let deleteId = null;
let cacheEnseignants = [];
let cacheMatieres = [];
let cacheClasses = [];

function openAddSidebar() {
  editingId = null;
  document.getElementById('sidebarTitle').textContent = 'Ajouter un enseignement';
  document.getElementById('recordId').value = '';
  document.getElementById('id_enseignant').value = '';
  document.getElementById('id_matiere').value = '';
  document.getElementById('id_classe').value = '';
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
}

function openEditSidebar(data) {
  editingId = data.uuid;
  document.getElementById('sidebarTitle').textContent = 'Modifier l\'enseignement';
  document.getElementById('recordId').value = data.uuid;
  document.getElementById('id_enseignant').value = data.id_enseignant || '';
  document.getElementById('id_matiere').value = data.id_matiere || '';
  document.getElementById('id_classe').value = data.id_classe || '';
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
}

function closeSidebar() {
  document.getElementById('addSidebar').classList.remove('active');
  document.getElementById('sidebarOverlay').classList.remove('active');
}

async function loadOptions() {
  if (!cacheEnseignants.length || !cacheMatieres.length || !cacheClasses.length) {
    const [ens, mat, cls] = await Promise.all([API.enseignants.list(), API.matieres.list(), API.classes.list()]);
    cacheEnseignants = (ens.success && ens.data) ? ens.data : [];
    cacheMatieres = (mat.success && mat.data) ? mat.data : [];
    cacheClasses = (cls.success && cls.data) ? cls.data : [];
    cacheEnseignants.forEach(e => {
      const name = (e.fullname || '').trim() || e.matricule || 'Enseignant';
      const opt = document.createElement('option');
      opt.value = e.id_enseignant;
      opt.textContent = name + (e.matricule ? ' (' + e.matricule + ')' : '');
      document.getElementById('id_enseignant').appendChild(opt);
    });
    cacheMatieres.forEach(m => {
      const opt = document.createElement('option');
      opt.value = m.id_matiere;
      opt.textContent = m.libelle || m.code;
      document.getElementById('id_matiere').appendChild(opt);
    });
    cacheClasses.forEach(c => {
      const opt = document.createElement('option');
      opt.value = c.id_classe;
      opt.textContent = c.libelle;
      document.getElementById('id_classe').appendChild(opt);
    });
  }
}

async function loadData() {
  const res = await API.enseignements.list();
  if (!res.success) { $('#dataBody').html('<tr><td colspan="5" class="text-center text-danger">Erreur de chargement</td></tr>'); return; }
  let rows = '';
  res.data.forEach((s, i) => {
    rows += `<tr>
      <td>${i + 1}</td>
      <td><span class="fw-semibold">${s.fullname || s.matricule || '-'}</span></td>
      <td>${s.matiere_libelle || '-'}</td>
      <td>${s.classe_libelle || '-'}</td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editRecord('${s.uuid}')"><i class="ri-edit-2-line"></i> Modifier</button></li>
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="confirmDelete('${s.uuid}')"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li>
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
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucun enseignement trouvé', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

async function editRecord(id) {
  const res = await API.enseignements.get(id);
  if (res.success) openEditSidebar(res.data);
  else Toast.fire({ icon: 'error', title: res.message || 'Erreur' });
}

document.getElementById('mainForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const data = {
    id_enseignant: document.getElementById('id_enseignant').value,
    id_matiere: document.getElementById('id_matiere').value,
    id_classe: document.getElementById('id_classe').value
  };
  if (!data.id_enseignant || !data.id_matiere || !data.id_classe) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Enseignant, matière et classe sont obligatoires' });
    return;
  }
  let res;
  if (editingId) res = await API.enseignements.update(editingId, data);
  else res = await API.enseignements.create(data);
  if (res.success) {
    closeSidebar();
    Toast.fire({ icon: 'success', title: editingId ? 'Enseignement modifié' : 'Enseignement créé' });
    loadData();
  } else {
    Swal.fire({ icon: 'error', title: 'Erreur', text: res.message });
  }
});

function confirmDelete(id) {
  deleteId = id;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('sidebarOverlay').addEventListener('click', function() {
  closeSidebar();
});

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
  if (!deleteId) return;
  const res = await API.enseignements.delete(deleteId);
  if (res.success) {
    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
    Toast.fire({ icon: 'success', title: 'Enseignement supprimé' });
    loadData();
  } else {
    Swal.fire({ icon: 'error', title: 'Erreur', text: res.message });
  }
  deleteId = null;
});

function exportCSV() {
  const table = $('#dataTable').DataTable();
  const data = table.rows({ filter: 'applied' }).data();
  let csv = '\uFEFFEnseignant,Matière,Classe\n';
  data.each(function(row) {
    const enseignant = $(row[1]).text().trim();
    const matiere = $(row[2]).text().trim();
    const classe = $(row[3]).text().trim();
    csv += '"' + enseignant + '","' + matiere + '","' + classe + '"\n';
  });
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'enseignements.csv';
  link.click();
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && typeof API !== 'undefined' && $.fn && $.fn.DataTable) {
      clearInterval(wait);
      loadOptions();
      loadData();
      $('#dtSearch').on('keyup', function() {
        $('#dataTable').DataTable().search(this.value).draw();
      });
      $('#dtLength').on('change', function() {
        $('#dataTable').DataTable().page.len(+this.value).draw();
      });
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>