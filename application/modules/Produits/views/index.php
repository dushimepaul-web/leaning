<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des Stocks / Produits</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Stocks</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
      Ajouter un produit
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
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="exportCSV()"><i class="ri-file-excel-line"></i> CSV</button></li>
              </ul>
            </div>
            <form class="navbar-search dt-search m-0">
              <input type="text" id="dtSearch" class="dt-input bg-transparent radius-4" name="search" placeholder="Rechercher...">
              <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
            </form>
            <div class="dropdown">
              <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">Filtrer</span>
                <span><i class="ri-arrow-down-s-line"></i></span>
              </button>
              <div class="dropdown-menu border bg-base shadow dropdown-menu-lg p-0">
                <div class="d-flex align-items-center justify-content-between border-bottom py-8 px-16">
                  <span class="fw-semibold text-lg text-primary-light">Filtre</span>
                </div>
                <form class="p-16 d-grid grid-cols-1 gap-16">
                  <div>
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Catégorie</label>
                    <select id="filterCategory" class="form-control form-select">
                      <option value="">Toutes les catégories</option>
                    </select>
                  </div>
                  <div><button type="reset" class="btn btn-danger-200 text-danger-600 w-100" onclick="resetFilters()">Réinitialiser</button></div>
                  <div><button type="button" class="btn btn-primary-600 w-100" onclick="applyFilters()">Appliquer</button></div>
                </form>
              </div>
            </div>
          </div>
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
              <th>S.L</th>
              <th>Code</th>
              <th>Libellé</th>
              <th>Catégorie</th>
              <th>Prix</th>
              <th>Stock</th>
              <th>Stock Min</th>
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
    <h5 class="text-lg mb-0" id="sidebarTitle">Ajouter un produit</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="mainForm" class="d-flex flex-column p-20">
    <input type="hidden" id="recordId">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Code</label>
        <input type="text" class="form-control" id="code" placeholder="Ex: PROD001">
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Libellé *</label>
        <input type="text" class="form-control" id="libelle" required placeholder="Nom du produit">
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Catégorie</label>
        <select class="form-control form-select" id="id_categorie">
          <option value="">Sélectionner</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Prix unitaire (FCFA)</label>
        <input type="number" class="form-control" id="prix_unitaire" step="0.01" placeholder="0.00">
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Taille</label>
        <input type="text" class="form-control" id="taille" placeholder="Ex: S, M, L, XL (uniformes)">
      </div>
      <div class="col-md-4">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Stock actuel</label>
        <input type="number" class="form-control" id="stock_initial" value="0">
      </div>
      <div class="col-md-4">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Stock minimum</label>
        <input type="number" class="form-control" id="stock_mini" value="5">
      </div>
      <div class="col-md-4">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Unité</label>
        <select class="form-control form-select" id="unite">
          <option value="pièce">Pièce</option>
          <option value="lot">Lot</option>
          <option value="paquet">Paquet</option>
          <option value="boîte">Boîte</option>
          <option value="kg">Kg</option>
          <option value="litre">Litre</option>
        </select>
      </div>
    </div>
    <div class="col-12">
      <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
        <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeSidebar()">Annuler</button>
        <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
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
<script>
var BASE_URL = '<?= base_url() ?>';
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let editingId = null;
let deleteId = null;

async function loadCategories() {
  try {
    const r = await fetch(BASE_URL + 'api/produits/categories').then(r => r.json());
    if (r.success && r.data) {
      let opts = '<option value="">Sélectionner</option>';
      r.data.forEach(c => { opts += `<option value="${c.id_categorie}">${c.libelle} (${c.code})</option>`; });
      document.getElementById('id_categorie').innerHTML = opts;
      let filterOpts = '<option value="">Toutes les catégories</option>';
      r.data.forEach(c => { filterOpts += `<option value="${c.id_categorie}">${c.libelle}</option>`; });
      document.getElementById('filterCategory').innerHTML = filterOpts;
    }
  } catch(e) {}
}

async function loadData() {
  const res = await API.produits.list();
  if (!res.success) { $('#dataBody').html('<tr><td colspan="8" class="text-center text-danger">Erreur</td></tr>'); return; }
  let rows = '';
  const filtreCat = document.getElementById('filterCategory').value;
  res.data.forEach((p, i) => {
    if (filtreCat && String(p.id_categorie) !== filtreCat) return;
    const stockRatio = p.stock_actuel <= p.stock_mini ? 'bg-danger-100 text-danger-600' : 'bg-success-100 text-success-600';
    rows += `<tr>
      <td>${i + 1}</td>
      <td><span class="fw-semibold">${p.code || '-'}</span></td>
      <td>${p.libelle}</td>
      <td>${p.categorie || '-'}</td>
      <td><strong>${parseFloat(p.prix_unitaire || 0).toLocaleString()} FCFA</strong></td>
      <td><span class="${stockRatio} px-24 py-4 radius-4 fw-medium text-sm">${p.stock_actuel || 0}</span></td>
      <td>${p.stock_mini || 0}</td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editRecord('${p.uuid}')"><i class="ri-edit-2-line"></i> Modifier</button></li>
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="confirmDelete('${p.uuid}')"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li>
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

function applyFilters() { loadData(); }
function resetFilters() { document.getElementById('filterCategory').value = ''; loadData(); }

function openAddSidebar() {
  editingId = null;
  document.getElementById('sidebarTitle').textContent = 'Ajouter un produit';
  document.getElementById('mainForm').reset();
  document.getElementById('recordId').value = '';
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
  loadCategories();
}

function openEditSidebar(data) {
  editingId = data.uuid;
  document.getElementById('sidebarTitle').textContent = 'Modifier le produit';
  document.getElementById('recordId').value = data.uuid;
  document.getElementById('code').value = data.code || '';
  document.getElementById('libelle').value = data.libelle || '';
  document.getElementById('id_categorie').value = data.id_categorie || '';
  document.getElementById('prix_unitaire').value = data.prix_unitaire || '';
  document.getElementById('stock_mini').value = data.stock_mini || 5;
  document.getElementById('unite').value = data.unite || 'pièce';
  document.getElementById('taille').value = data.taille || '';
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
  loadCategories();
}

async function editRecord(id) {
  const res = await API.produits.get(id);
  if (res.success) openEditSidebar(res.data);
}

async function saveRecord() {
  const data = {
    code: document.getElementById('code').value,
    libelle: document.getElementById('libelle').value,
    id_categorie: document.getElementById('id_categorie').value || null,
    prix_unitaire: document.getElementById('prix_unitaire').value || 0,
    stock_actuel: document.getElementById('stock_initial').value || 0,
    stock_mini: document.getElementById('stock_mini').value || 5,
    unite: document.getElementById('unite').value || 'pièce',
    taille: document.getElementById('taille').value
  };
  if (!data.libelle) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Libellé obligatoire' }); return; }
  let r;
  if (editingId) {
    const original = await API.produits.get(editingId);
    if (original.success) {
      data.stock_actuel = document.getElementById('stock_initial').value || original.data.stock_actuel;
    }
    r = await API.produits.update(editingId, data);
  } else {
    r = await API.produits.create(data);
  }
  if (r.success) {
    closeSidebar();
    Toast.fire({ icon: 'success', title: editingId ? 'Produit modifié' : 'Produit créé' });
    loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
}

function closeSidebar() {
  document.getElementById('addSidebar').classList.remove('active');
  document.getElementById('sidebarOverlay').classList.remove('active');
}

document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);
document.getElementById('mainForm').addEventListener('submit', function(e) { e.preventDefault(); saveRecord(); });

function confirmDelete(id) {
  deleteId = id;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
  if (!deleteId) return;
  const r = await API.produits.delete(deleteId);
  if (r.success) {
    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
    Toast.fire({ icon: 'success', title: 'Produit supprimé' });
    loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
  deleteId = null;
});

function exportCSV() {
  const table = $('#dataTable').DataTable();
  const data = table.rows({ filter: 'applied' }).data();
  let csv = '\uFEFFCode,Libellé,Catégorie,Prix,Stock,Stock Min\n';
  data.each(function(row) {
    const cols = [];
    for (let i = 1; i <= 6; i++) {
      let val = $(row[i]).text().trim() || row[i] || '';
      val = '"' + val.replace(/"/g, '""') + '"';
      cols.push(val);
    }
    csv += cols.join(',') + '\n';
  });
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'stocks_produits.csv';
  link.click();
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && $.fn && $.fn.DataTable && typeof API !== 'undefined') {
      clearInterval(wait);
      loadCategories();
      loadData();
      $('#dtSearch').on('keyup', function() { $('#dataTable').DataTable().search(this.value).draw(); });
      $('#dtLength').on('change', function() { $('#dataTable').DataTable().page.len(+this.value).draw(); });
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
