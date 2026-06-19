<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Matériels de Toilettes</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Scolarité / Matériels de Toilettes</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openSidebar()">
        <span class="d-flex text-md"><i class="ri-add-line"></i></span> Nouveau produit
      </button>
    </div>
  </div>
  <div class="card h-100">
    <div class="card-body p-0 dataTable-wrapper">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
        <form class="navbar-search dt-search m-0">
          <input type="text" id="dtSearch" class="dt-input bg-transparent radius-4" placeholder="Rechercher...">
          <i class="ri-search-line icon"></i>
        </form>
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
            <th>Code</th>
            <th>Libellé</th>
            <th>Prix unitaire</th>
            <th>Stock</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="dataBody"></tbody>
      </table>
    </div>
  </div>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="sidebarOverlay"></div>

<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="addSidebar" style="width:50vw;max-width:50vw;box-shadow:-4px 0 20px rgba(0,0,0,0.1);">
  <div class="py-16 px-24 border-bottom d-flex align-items-center justify-content-between">
    <h5 class="text-lg mb-0" id="sidebarTitle">Nouveau produit</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <div class="p-24">
    <form id="mainForm">
      <input type="hidden" id="recordId">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Code</label>
          <input type="text" class="form-control" id="fCode" placeholder="Code produit">
        </div>
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Libellé <span class="text-danger-600">*</span></label>
          <input type="text" class="form-control" id="fLibelle" placeholder="Nom du produit">
        </div>
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Prix unitaire (FC)</label>
          <input type="number" class="form-control" id="fPrix" step="0.01" placeholder="0.00">
        </div>
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Stock</label>
          <input type="number" class="form-control" id="fStock" placeholder="0">
        </div>
        <div class="col-12">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Description</label>
          <textarea class="form-control" id="fDescription" rows="2" placeholder="Description..."></textarea>
        </div>
      </div>
    </form>
  </div>
  <div class="p-24 border-top d-flex align-items-center justify-content-end gap-3">
    <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeSidebar()">Annuler</button>
    <button type="button" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8" onclick="saveRecord()">Enregistrer</button>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let editId = null;

async function loadData() {
  const r = await API.toilettes.list();
  if (!r.success) { $('#dataBody').html('<tr><td colspan="6" class="text-center text-danger">Erreur</td></tr>'); return; }
  let rows = '';
  r.data.forEach((d, i) => {
    const stock = d.stock_actuel || 0;
    const stockBadge = stock > 0 ? 'bg-success-100 text-success-600' : 'bg-danger-100 text-danger-600';
    rows += `<tr>
      <td>${i + 1}</td>
      <td><span class="fw-semibold">${d.code || '-'}</span></td>
      <td>${d.libelle}</td>
      <td><strong>${parseFloat(d.prix_unitaire || 0).toLocaleString()} FC</strong></td>
      <td><span class="${stockBadge} px-16 py-4 radius-4 fw-medium text-sm">${stock || 0}</span></td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editRecord('${d.uuid}')"><i class="ri-edit-2-line"></i> Modifier</button></li>
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="deleteRecord('${d.uuid}')"><i class="ri-delete-bin-line"></i> Supprimer</button></li>
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
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucun produit trouvé', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

function openSidebar() { editId = null; document.getElementById('mainForm').reset(); document.getElementById('recordId').value = ''; document.getElementById('sidebarTitle').textContent = 'Nouveau produit'; document.getElementById('sidebarOverlay').classList.add('active'); document.getElementById('addSidebar').classList.add('active'); }

function closeSidebar() { document.getElementById('sidebarOverlay').classList.remove('active'); document.getElementById('addSidebar').classList.remove('active'); }

async function editRecord(uuid) {
  const r = await API.toilettes.get(uuid);
  if (!r.success) { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); return; }
  editId = uuid;
  const d = r.data;
  document.getElementById('recordId').value = uuid;
  document.getElementById('fCode').value = d.code || '';
  document.getElementById('fLibelle').value = d.libelle;
  document.getElementById('fPrix').value = d.prix_unitaire || 0;
  document.getElementById('fStock').value = d.stock_actuel || 0;
  document.getElementById('fDescription').value = d.description || '';
  document.getElementById('sidebarTitle').textContent = 'Modifier le produit';
  document.getElementById('sidebarOverlay').classList.add('active');
  document.getElementById('addSidebar').classList.add('active');
}

async function saveRecord() {
  const data = {
    code: document.getElementById('fCode').value,
    libelle: document.getElementById('fLibelle').value,
    prix_unitaire: document.getElementById('fPrix').value,
    stock: document.getElementById('fStock').value,
    description: document.getElementById('fDescription').value
  };
  if (!data.libelle) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Libellé obligatoire' }); return; }
  const r = editId ? await API.toilettes.update(editId, data) : await API.toilettes.create(data);
  if (r.success) { closeSidebar(); Toast.fire({ icon: 'success', title: editId ? 'Produit mis à jour' : 'Produit créé' }); loadData(); }
  else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
}

async function deleteRecord(uuid) {
  const c = await Swal.fire({ title: 'Confirmation', text: 'Supprimer ce produit ?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Annuler', confirmButtonText: 'Oui, supprimer' });
  if (!c.isConfirmed) return;
  const r = await API.toilettes.delete(uuid);
  if (r.success) { Toast.fire({ icon: 'success', title: 'Produit supprimé' }); loadData(); }
  else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
}

document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);
document.getElementById('dtSearch').addEventListener('keyup', function() { if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().search(this.value).draw(); });
document.getElementById('dtLength').addEventListener('change', function() { if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().page.len(+this.value).draw(); });

(function() {
  var w = setInterval(function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable && typeof API !== 'undefined' && API.toilettes) { clearInterval(w); loadData(); }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
