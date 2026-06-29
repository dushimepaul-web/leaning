<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Échéances</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Échéances</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openSidebar()">
        <span class="d-flex text-md"><i class="ri-add-line"></i></span> Nouvelle échéance
      </button>
    </div>
  </div>
  <div class="card h-100">
    <div class="card-body p-0 dataTable-wrapper">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
        <form class="navbar-search dt-search m-0">
          <input type="text" id="dtSearch" class="dt-input bg-transparent radius-4" name="search" placeholder="Rechercher...">
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
            <th>#</th>
            <th>Étudiant</th>
            <th>Type</th>
            <th>Montant</th>
            <th>Date échéance</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="dataBody"></tbody>
      </table>
    </div>
  </div>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="formOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="formSidebar" style="width:50vw;max-width:50vw;box-shadow:-4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="formTitle">Nouvelle échéance</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="dataForm" class="d-flex flex-column p-20">
    <div class="mb-3 position-relative">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Étudiant *</label>
      <input type="hidden" id="fEtudiant">
      <input type="text" class="form-control" id="fEtudiant_search" placeholder="Rechercher..." autocomplete="off">
      <div id="fEtudiant_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type de frais</label>
      <select class="form-control form-select" id="fFrais">
        <option value="">Sélectionner...</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Montant *</label>
      <input type="number" class="form-control" id="fMontant" step="0.01" placeholder="0.00">
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date d'échéance *</label>
      <input type="date" class="form-control" id="fDate">
    </div>
    <div class="col-12">
      <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
        <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeSidebar()">Annuler</button>
        <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
      </div>
    </div>
  </form>
</div>

<script id="etudiants_data" type="application/json"><?= json_encode($etudiants) ?></script>
<script src="<?= base_url() ?>assets/js/autocomplete.js?v=<?= filemtime(FCPATH.'assets/js/autocomplete.js') ?>"></script>
<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let editUuid = null;
let etudiantsData = [];
try { const el = document.getElementById('etudiants_data'); if (el) etudiantsData = JSON.parse(el.textContent); } catch(e) {}

async function loadData() {
  const r = await API.echeances.list();
  if (!r.success) { $('#dataBody').html('<tr><td colspan="7" class="text-center text-danger">Erreur</td></tr>'); return; }
  let rows = '';
  r.data.forEach((d, i) => {
    const statutClass = d.statut === 'paye' ? 'success' : d.statut === 'partiel' ? 'warning' : d.statut === 'annule' ? 'danger' : 'secondary';
    rows += `<tr>
      <td>${i + 1}</td>
      <td class="fw-semibold">${d.nom} ${d.prenom}</td>
      <td>${d.type_frais || '-'}</td>
      <td><strong>${parseFloat(d.montant || 0).toLocaleString()}</strong></td>
      <td>${d.date_echeance}</td>
      <td><span class="badge bg-${statutClass}-600">${d.statut}</span></td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editData('${d.uuid}')"><i class="ri-edit-2-line"></i> Modifier</button></li>
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="deleteData('${d.uuid}')"><i class="ri-delete-bin-line"></i> Supprimer</button></li>
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
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucune échéance trouvée', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

function openSidebar() {
  editUuid = null;
  document.getElementById('dataForm').reset();
  document.getElementById('formTitle').textContent = 'Nouvelle échéance';
  document.getElementById('formOverlay').classList.remove('visibility-hidden', 'opacity-0');
  document.getElementById('formSidebar').classList.remove('translate-x-full');
}

function closeSidebar() {
  document.getElementById('formOverlay').classList.add('visibility-hidden', 'opacity-0');
  document.getElementById('formSidebar').classList.add('translate-x-full');
}

async function editData(uuid) {
  const r = await API.echeances.get(uuid);
  if (!r.success) { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); return; }
  editUuid = uuid;
  const d = r.data;
  document.getElementById('formTitle').textContent = 'Modifier l\'échéance';
  document.getElementById('fEtudiant').value = d.id_etudiant || '';
  const _e = etudiantsData.find(function(x) { return String(x.id_etudiant) === String(d.id_etudiant); });
  if (_e) document.getElementById('fEtudiant_search').value = _e.nom + ' ' + _e.prenom + ' (' + (_e.matricule || '') + ')';
  document.getElementById('fFrais').value = d.id_frais || '';
  document.getElementById('fMontant').value = d.montant || '';
  document.getElementById('fDate').value = d.date_echeance || '';
  document.getElementById('formOverlay').classList.remove('visibility-hidden', 'opacity-0');
  document.getElementById('formSidebar').classList.remove('translate-x-full');
}

async function deleteData(uuid) {
  const c = await Swal.fire({ title: 'Confirmation', text: 'Supprimer cette échéance ?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Annuler', confirmButtonText: 'Oui, supprimer' });
  if (!c.isConfirmed) return;
  const r = await API.echeances.delete(uuid);
  if (r.success) { Toast.fire({ icon: 'success', title: 'Supprimée' }); loadData(); }
  else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
}

document.getElementById('dataForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const data = { id_etudiant: document.getElementById('fEtudiant').value, id_frais: document.getElementById('fFrais').value, montant: document.getElementById('fMontant').value, date_echeance: document.getElementById('fDate').value };
  if (!data.id_etudiant || !data.montant || !data.date_echeance) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Étudiant, montant et date obligatoires' }); return; }
  const r = editUuid ? await API.echeances.update(editUuid, data) : await API.echeances.create(data);
  if (r.success) { closeSidebar(); Toast.fire({ icon: 'success', title: editUuid ? 'Mis à jour' : 'Créée' }); loadData(); }
  else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
});

document.getElementById('formOverlay').addEventListener('click', closeSidebar);
document.getElementById('dtSearch').addEventListener('keyup', function() { if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().search(this.value).draw(); });
document.getElementById('dtLength').addEventListener('change', function() { if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().page.len(+this.value).draw(); });
(function() {
  var _f = function() {
    if (typeof $ === 'undefined' || typeof API === 'undefined' || typeof autoSetup === 'undefined') { setTimeout(_f, 50); return; }
    loadData();
    autoSetup('fEtudiant_search', 'fEtudiant', 'fEtudiant_results', etudiantsData.map(function(e) { return { id: e.id_etudiant, nom: e.nom, prenom: e.prenom, matricule: e.matricule }; }), function(e) { return e.nom + ' ' + e.prenom + ' (' + (e.matricule || '') + ')'; });
    API.frais.list().then(function(r) { if (r.success) { var sel = document.getElementById('fFrais'); r.data.forEach(function(d) { var o = document.createElement('option'); o.value = d.id_frais; o.textContent = (d.type_libelle || d.libelle || '') + ' - ' + parseFloat(d.montant || 0).toLocaleString(); sel.appendChild(o); }); } });
  };
  _f();
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>