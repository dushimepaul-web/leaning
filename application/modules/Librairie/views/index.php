<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Librairie</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Librairie</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-outline-primary-600 d-flex align-items-center gap-6" onclick="initArticles()">
        <span class="d-flex text-md"><i class="ri-download-line"></i></span> Initialiser les fournitures
      </button>
      <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openForm(null)">
        <span class="d-flex text-md"><i class="ri-add-line"></i></span> Nouveau produit
      </button>
    </div>
  </div>

  <div class="card h-100">
    <div class="card-header py-12 px-20 border-bottom border-neutral-200 d-flex flex-wrap align-items-center gap-12">
      <div class="nav nav-tabs border-0 gap-8" id="categoryTabs" role="tablist">
        <button class="btn btn-sm rounded-pill fw-medium px-16 active" data-category="" onclick="switchCategory(this)">Tous</button>
        <button class="btn btn-sm rounded-pill fw-medium px-16" data-category="LIVRE" onclick="switchCategory(this)">Livres</button>
        <button class="btn btn-sm rounded-pill fw-medium px-16" data-category="MATERIEL" onclick="switchCategory(this)">Fournitures</button>
      </div>
    </div>
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
            <th>Type</th>
            <th>Code</th>
            <th>Libellé / Titre</th>
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

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="formOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="formSidebar" style="width:50vw;max-width:50vw;box-shadow:-4px 0 20px rgba(0,0,0,0.1);">
  <div class="py-16 px-24 border-bottom d-flex align-items-center justify-content-between">
    <h5 class="text-lg mb-0" id="formTitle">Nouveau produit</h5>
    <button type="button" class="btn-close" onclick="closeForm()"></button>
  </div>
  <div class="p-24">
    <form id="mainForm">
      <input type="hidden" id="recordId">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type <span class="text-danger-600">*</span></label>
          <select class="form-control form-select" id="fCategorie" onchange="toggleBookFields()">
            <?php foreach ($categories as $cat): ?>
            <?php if (in_array($cat['code'], ['LIVRE','MATERIEL','FOURNITURE'])): ?>
            <option value="<?= $cat['id_categorie'] ?>" data-code="<?= $cat['code'] ?>"><?= $cat['libelle'] ?></option>
            <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Code</label>
          <input type="text" class="form-control" id="fCode" placeholder="Code unique">
        </div>
        <div class="col-md-12">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Libellé / Titre <span class="text-danger-600">*</span></label>
          <input type="text" class="form-control" id="fLibelle" placeholder="Nom du produit ou titre du livre">
        </div>
        <div class="col-md-6 book-field" style="display:none">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Éditeur</label>
          <input type="text" class="form-control" id="fEditeur" placeholder="Nom de l'éditeur">
        </div>
        <div class="col-md-6 book-field" style="display:none">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Année d'édition</label>
          <input type="number" class="form-control" id="fAnnee" placeholder="2026" min="1900" max="2099">
        </div>
        <div class="col-md-6 book-field" style="display:none">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Matière</label>
          <select class="form-control form-select" id="fMatiere">
            <option value="">-- Sélectionner --</option>
            <?php foreach ($matieres as $m): ?>
            <option value="<?= $m['id_matiere'] ?>"><?= $m['libelle'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6 book-field" style="display:none">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe</label>
          <select class="form-control form-select" id="fClasse">
            <option value="">-- Sélectionner --</option>
            <?php foreach ($classes as $cl): ?>
            <option value="<?= $cl['id_classe'] ?>"><?= $cl['libelle'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Prix unitaire (FC) <span class="text-danger-600">*</span></label>
          <input type="number" class="form-control" id="fPrix" step="0.01" placeholder="0.00">
        </div>
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Stock actuel</label>
          <input type="number" class="form-control" id="fStock" placeholder="0">
        </div>
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Stock minimum</label>
          <input type="number" class="form-control" id="fStockMin" placeholder="0">
        </div>
        <div class="col-md-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Unité</label>
          <input type="text" class="form-control" id="fUnite" placeholder="pièce">
        </div>
        <div class="col-md-12">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Description</label>
          <textarea class="form-control" id="fDescription" rows="2" placeholder="Description..."></textarea>
        </div>
      </div>
    </form>
  </div>
  <div class="p-24 border-top d-flex align-items-center justify-content-end gap-3">
    <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeForm()">Annuler</button>
    <button type="button" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8" onclick="saveProduct()">Enregistrer</button>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let editId = null;
let currentCategory = '';

function toggleBookFields() {
  const sel = document.getElementById('fCategorie');
  const opt = sel.options[sel.selectedIndex];
  const code = opt ? opt.dataset.code : '';
  const show = code === 'LIVRE';
  document.querySelectorAll('.book-field').forEach(function(el) { el.style.display = show ? 'block' : 'none'; });
}

async function loadData() {
  const endpoint = currentCategory ? 'api/librairie?categorie=' + currentCategory : 'api/librairie';
  const r = await API.get(endpoint);
  if (!r.success) { $('#dataBody').html('<tr><td colspan="7" class="text-center text-danger">Erreur</td></tr>'); return; }
  let rows = '';
  r.data.forEach(function(d, i) {
    const catLabel = d.code_categorie === 'LIVRE' ? '<span class="badge bg-primary-100 text-primary-600">Livre</span>' : '<span class="badge bg-info-100 text-info-600">Fourniture</span>';
    const stock = d.stock_actuel || 0;
    const stockBadge = stock > 0 ? 'bg-success-100 text-success-600' : 'bg-danger-100 text-danger-600';
    rows += `<tr>
      <td>${i + 1}</td>
      <td>${catLabel}</td>
      <td><span class="fw-semibold">${d.code || '-'}</span></td>
      <td>${d.libelle}</td>
      <td><strong>${parseFloat(d.prix_unitaire || 0).toLocaleString()} FC</strong></td>
      <td><span class="${stockBadge} px-16 py-4 radius-4 fw-medium text-sm">${stock}</span></td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editProduct('${d.uuid}')"><i class="ri-edit-2-line"></i> Modifier</button></li>
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="deleteProduct('${d.uuid}')"><i class="ri-delete-bin-line"></i> Supprimer</button></li>
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

function switchCategory(btn) {
  document.querySelectorAll('#categoryTabs button').forEach(function(b) { b.classList.remove('active'); });
  btn.classList.add('active');
  currentCategory = btn.dataset.category;
  loadData();
}

function openForm(data) {
  editId = null;
  document.getElementById('mainForm').reset();
  document.getElementById('recordId').value = '';
  document.getElementById('formTitle').textContent = 'Nouveau produit';
  document.getElementById('fStockMin').value = 0;
  document.getElementById('fUnite').value = 'pièce';
  toggleBookFields();
  if (data) {
    editId = data.uuid;
    document.getElementById('recordId').value = data.uuid;
    document.getElementById('formTitle').textContent = 'Modifier le produit';
    document.getElementById('fCategorie').value = data.id_categorie || '';
    document.getElementById('fCode').value = data.code || '';
    document.getElementById('fLibelle').value = data.libelle || '';
    document.getElementById('fEditeur').value = data.editeur || '';
    document.getElementById('fAnnee').value = data.annee_edition || '';
    document.getElementById('fMatiere').value = data.id_matiere || '';
    document.getElementById('fClasse').value = data.id_classe || '';
    document.getElementById('fPrix').value = data.prix_unitaire || 0;
    document.getElementById('fStock').value = data.stock_actuel || 0;
    document.getElementById('fStockMin').value = data.stock_mini || 0;
    document.getElementById('fUnite').value = data.unite || 'pièce';
    document.getElementById('fDescription').value = data.description || '';
    toggleBookFields();
  }
  document.getElementById('formOverlay').classList.add('active');
  document.getElementById('formSidebar').classList.add('active');
}

function closeForm() {
  document.getElementById('formOverlay').classList.remove('active');
  document.getElementById('formSidebar').classList.remove('active');
}

async function editProduct(uuid) {
  const r = await API.librairie.get(uuid);
  if (!r.success) { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); return; }
  openForm(r.data);
}

async function saveProduct() {
  const data = {
    id_categorie: document.getElementById('fCategorie').value,
    code: document.getElementById('fCode').value,
    libelle: document.getElementById('fLibelle').value,
    editeur: document.getElementById('fEditeur').value,
    annee_edition: document.getElementById('fAnnee').value,
    id_matiere: document.getElementById('fMatiere').value || null,
    id_classe: document.getElementById('fClasse').value || null,
    prix_unitaire: document.getElementById('fPrix').value || 0,
    stock_actuel: document.getElementById('fStock').value || 0,
    stock_mini: document.getElementById('fStockMin').value || 0,
    unite: document.getElementById('fUnite').value || 'pièce',
    description: document.getElementById('fDescription').value
  };
  if (!data.libelle) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Libellé obligatoire' }); return; }
  if (!data.id_categorie) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Type obligatoire' }); return; }
  const r = editId ? await API.librairie.update(editId, data) : await API.librairie.create(data);
  if (r.success) { closeForm(); Toast.fire({ icon: 'success', title: editId ? 'Produit mis à jour' : 'Produit créé' }); loadData(); }
  else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
}

async function deleteProduct(uuid) {
  const c = await Swal.fire({ title: 'Confirmation', text: 'Supprimer ce produit ?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Annuler', confirmButtonText: 'Oui, supprimer' });
  if (!c.isConfirmed) return;
  const r = await API.librairie.delete(uuid);
  if (r.success) { Toast.fire({ icon: 'success', title: 'Produit supprimé' }); loadData(); }
  else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
}

async function initArticles() {
  const c = await Swal.fire({ title: 'Initialiser les fournitures', text: 'Créer les articles pré-définis (Crayons, Stylos, etc.) ?', icon: 'info', showCancelButton: true, confirmButtonText: 'Initialiser', cancelButtonText: 'Annuler' });
  if (!c.isConfirmed) return;
  const r = await API.librairie.initialiser();
  if (r.success) { Toast.fire({ icon: 'success', title: r.message }); loadData(); }
  else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
}

document.getElementById('formOverlay').addEventListener('click', closeForm);
document.getElementById('dtSearch').addEventListener('keyup', function() { if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().search(this.value).draw(); });
document.getElementById('dtLength').addEventListener('change', function() { if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().page.len(+this.value).draw(); });

(function() {
  var w = setInterval(function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable && typeof API !== 'undefined' && API.librairie) { clearInterval(w); loadData(); }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
