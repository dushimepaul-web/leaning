<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Bulletins de paie</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Bulletins de paie</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openSidebar()">
        <span class="d-flex text-md"><i class="ri-add-line"></i></span> Nouveau bulletin
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
            <th>Employé</th>
            <th>Mois</th>
            <th>Année</th>
            <th>Salaire base</th>
            <th>Net à payer</th>
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
    <h5 class="text-lg mb-0" id="formTitle">Nouveau bulletin</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="dataForm" class="d-flex flex-column p-20">
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Contrat *</label>
      <select class="form-control form-select" id="fContrat">
        <option value="">Sélectionner...</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Mois</label>
      <select class="form-control form-select" id="fMois"></select>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Année</label>
      <input type="number" class="form-control" id="fAnnee" min="2020" max="2030">
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Notes</label>
      <textarea class="form-control" id="fNotes" rows="2"></textarea>
    </div>
    <div class="col-12">
      <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
        <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeSidebar()">Annuler</button>
        <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
      </div>
    </div>
  </form>
</div>

<script id="contrats_data" type="application/json"><?= json_encode($contrats) ?></script>
<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let editUuid = null;
const mois = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

async function loadData() {
  const r = await API.paie_bulletins.list();
  if (!r.success) { $('#dataBody').html('<tr><td colspan="8" class="text-center text-danger">Erreur</td></tr>'); return; }
  let rows = '';
  r.data.forEach((d, i) => {
    const statutClass = d.statut === 'paye' ? 'success' : d.statut === 'valide' ? 'info' : d.statut === 'annule' ? 'danger' : 'warning';
    rows += `<tr>
      <td>${i + 1}</td>
      <td class="fw-semibold">${d.nom_complet}</td>
      <td>${mois[parseInt(d.mois) - 1] || d.mois}</td>
      <td>${d.annee}</td>
      <td>${parseFloat(d.salaire_base || 0).toLocaleString()}</td>
      <td><strong>${parseFloat(d.net_a_payer || 0).toLocaleString()}</strong></td>
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
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucun bulletin trouvé', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

function openSidebar() {
  editUuid = null;
  document.getElementById('dataForm').reset();
  document.getElementById('formTitle').textContent = 'Nouveau bulletin';
  document.getElementById('fAnnee').value = new Date().getFullYear();
  document.getElementById('formOverlay').classList.remove('visibility-hidden', 'opacity-0');
  document.getElementById('formSidebar').classList.remove('translate-x-full');
}

function closeSidebar() {
  document.getElementById('formOverlay').classList.add('visibility-hidden', 'opacity-0');
  document.getElementById('formSidebar').classList.add('translate-x-full');
}

async function editData(uuid) {
  const r = await API.paie_bulletins.get(uuid);
  if (!r.success) { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); return; }
  editUuid = uuid;
  const d = r.data;
  document.getElementById('formTitle').textContent = 'Modifier le bulletin';
  document.getElementById('fContrat').value = d.id_contrat || '';
  document.getElementById('fMois').value = d.mois || '';
  document.getElementById('fAnnee').value = d.annee || '';
  document.getElementById('fNotes').value = d.notes || '';
  document.getElementById('formOverlay').classList.remove('visibility-hidden', 'opacity-0');
  document.getElementById('formSidebar').classList.remove('translate-x-full');
}

async function deleteData(uuid) {
  const c = await Swal.fire({ title: 'Confirmation', text: 'Supprimer ce bulletin ?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Annuler', confirmButtonText: 'Oui, supprimer' });
  if (!c.isConfirmed) return;
  const r = await API.paie_bulletins.delete(uuid);
  if (r.success) { Toast.fire({ icon: 'success', title: 'Supprimé' }); loadData(); }
  else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
}

document.getElementById('dataForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const data = { id_contrat: document.getElementById('fContrat').value, mois: document.getElementById('fMois').value, annee: document.getElementById('fAnnee').value, notes: document.getElementById('fNotes').value };
  if (!data.id_contrat) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Contrat obligatoire' }); return; }
  const r = editUuid ? await API.paie_bulletins.update(editUuid, data) : await API.paie_bulletins.create(data);
  if (r.success) { closeSidebar(); Toast.fire({ icon: 'success', title: editUuid ? 'Mis à jour' : 'Créé' }); loadData(); }
  else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
});

document.getElementById('formOverlay').addEventListener('click', closeSidebar);
document.getElementById('dtSearch').addEventListener('keyup', function() { if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().search(this.value).draw(); });
document.getElementById('dtLength').addEventListener('change', function() { if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().page.len(+this.value).draw(); });
(function() {
  var _f = function() {
    if (typeof $ === 'undefined' || typeof API === 'undefined') { setTimeout(_f, 50); return; }
    var mSel = document.getElementById('fMois');
    for (var i = 1; i <= 12; i++) { var o = document.createElement('option'); o.value = i; o.textContent = mois[i - 1]; mSel.appendChild(o); }
    mSel.value = new Date().getMonth() + 1;
    loadData();
    var cData = JSON.parse(document.getElementById('contrats_data').textContent || '[]');
    var sel = document.getElementById('fContrat');
    cData.forEach(function(c) { var o = document.createElement('option'); o.value = c.id_contrat; o.textContent = 'Contrat #' + c.id_contrat; sel.appendChild(o); });
  };
  _f();
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
