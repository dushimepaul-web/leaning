<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Mouvements de Stock</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Stocks</span>
        <span class="text-secondary-light"> / Mouvements</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
        <span class="d-flex text-md"><i class="ri-add-large-line"></i></span> Nouveau mouvement
      </button>
      <button type="button" class="btn btn-success-600 d-flex align-items-center gap-6" onclick="openVenteSidebar()">
        <span class="d-flex text-md"><i class="ri-shopping-bag-line"></i></span> Nouvelle vente
      </button>
    </div>
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
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light d-flex align-items-center gap-10" onclick="exportCSV()"><i class="ri-file-excel-line"></i> CSV</button></li>
              </ul>
            </div>
            <form class="navbar-search dt-search m-0">
              <input type="text" id="dtSearch" class="dt-input bg-transparent radius-4" placeholder="Rechercher...">
              <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
            </form>
          </div>
          <div class="d-flex align-items-center gap-8 text-secondary-light">
            <div class="dropdown">
              <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown">
                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">Filtrer</span>
                <span><i class="ri-arrow-down-s-line"></i></span>
              </button>
              <div class="dropdown-menu border bg-base shadow dropdown-menu-lg p-0">
                <div class="d-flex align-items-center justify-content-between border-bottom py-8 px-16">
                  <span class="fw-semibold text-lg text-primary-light">Filtre</span>
                </div>
                <form class="p-16 d-grid grid-cols-2 gap-16">
                  <div>
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type</label>
                    <select id="filterType" class="form-control form-select">
                      <option value="">Tous</option>
                      <option value="entree">Entrée</option>
                      <option value="sortie">Sortie</option>
                    </select>
                  </div>
                  <div>
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date début</label>
                    <input type="date" id="filterDateDebut" class="form-control">
                  </div>
                  <div>
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date fin</label>
                    <input type="date" id="filterDateFin" class="form-control">
                  </div>
                  <div><button type="reset" class="btn btn-danger-200 text-danger-600 w-100" onclick="resetFilters()">Réinitialiser</button></div>
                  <div><button type="button" class="btn btn-primary-600 w-100" onclick="applyFilters()">Appliquer</button></div>
                </form>
              </div>
            </div>
            <span>Lignes par page :</span>
            <select id="dtLength" class="form-control form-select" style="width:auto;">
              <option value="5">5</option>
              <option value="10" selected>10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>
        <table class="table bordered-table mb-0 data-table" id="dataTable" style="width:100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>Produit</th>
              <th>Type</th>
              <th>Qté</th>
              <th>Prix</th>
              <th>Élève</th>
              <th>Motif</th>
              <th>Utilisateur</th>
            </tr>
          </thead>
          <tbody id="dataBody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- SIDEBAR: Mouvement simple -->
<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="sidebarOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="addSidebar" style="width:50vw;max-width:50vw;box-shadow: -4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="sidebarTitle">Nouveau mouvement</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="mainForm" class="d-flex flex-column p-20">
    <div class="row g-3">
      <div class="col-md-6 position-relative">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Élève (optionnel)</label>
        <input type="hidden" id="id_etudiant">
        <input type="text" id="etudiantSearch" class="form-control" placeholder="Rechercher un élève..." autocomplete="off">
        <div id="etudiantList" class="list-group position-absolute z-99 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;width:calc(100% - 24px);"></div>
      </div>
      <div class="col-md-6 position-relative">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Produit *</label>
        <input type="hidden" id="id_produit">
        <input type="text" id="produitSearch" class="form-control" placeholder="Cliquer pour voir tous les produits..." autocomplete="off">
        <div id="produitList" class="list-group position-absolute z-99 shadow radius-8 border" style="display:none;max-height:250px;overflow-y:auto;width:calc(100% - 24px);"></div>
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type *</label>
        <select class="form-control form-select" id="type" required>
          <option value="">Sélectionner</option>
          <option value="entree">Entrée</option>
          <option value="sortie">Sortie</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Quantité *</label>
        <input type="number" class="form-control" id="quantite" min="1" value="1">
      </div>
      <div class="col-md-3">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Prix unitaire</label>
        <input type="number" class="form-control" id="prix_unitaire" step="0.01" placeholder="0.00">
      </div>
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Motif</label>
        <input type="text" class="form-control" id="motif" placeholder="Motif du mouvement">
      </div>
    </div>
    <div class="col-12"><div class="d-flex align-items-center justify-content-center gap-3 mt-8">
      <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeSidebar()">Annuler</button>
      <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
    </div></div>
  </form>
</div>

<!-- SIDEBAR: Vente batch -->
<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="venteOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="venteSidebar" style="width:60vw;max-width:60vw;box-shadow: -4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0">Nouvelle Vente</h5>
    <button type="button" class="btn-close" onclick="closeVenteSidebar()"></button>
  </div>
  <form id="venteForm" class="d-flex flex-column p-20">
    <div class="mb-3 position-relative">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Élève *</label>
      <input type="hidden" id="venteEtudiant">
      <input type="text" id="venteEtudiantSearch" class="form-control" placeholder="Cliquer pour voir tous les élèves..." autocomplete="off">
      <div id="venteEtudiantList" class="list-group position-absolute z-99 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;width:calc(100% - 24px);"></div>
    </div>
    <hr>
    <h6 class="text-md fw-semibold text-primary-light mb-12">Produits à vendre</h6>
    <div class="row mb-3 align-items-end">
      <div class="col-md-5 position-relative">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Produit</label>
        <input type="hidden" id="venteProduit">
        <input type="text" id="venteProduitSearch" class="form-control" placeholder="Cliquer pour voir..." autocomplete="off">
        <div id="venteProduitList" class="list-group position-absolute z-99 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;width:calc(100% - 24px);"></div>
      </div>
      <div class="col-md-2">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Prix unit.</label>
        <input type="number" class="form-control" id="ventePrix" step="0.01" value="0.00">
      </div>
      <div class="col-md-2">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Quantité</label>
        <input type="number" class="form-control" id="venteQuantite" min="1" value="1">
      </div>
      <div class="col-md-3">
        <button type="button" class="btn btn-primary-600 w-100" onclick="addProduitVente()"><i class="ri-add-line"></i> Ajouter</button>
      </div>
    </div>
    <div class="mb-3">
      <table class="table table-sm bordered-table" id="venteTable">
        <thead><tr><th>Produit</th><th>Prix</th><th>Qté</th><th>Total</th><th></th></tr></thead>
        <tbody id="venteBody">
          <tr id="noVenteRow"><td colspan="5" class="text-center text-secondary-light py-3">Aucun produit ajouté</td></tr>
        </tbody>
        <tfoot><tr><th colspan="3" class="text-end">Total général</th><th id="venteTotal">0,00</th><th></th></tr></tfoot>
      </table>
    </div>
    <div class="col-12"><div class="d-flex align-items-center justify-content-center gap-3 mt-8">
      <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeVenteSidebar()">Annuler</button>
      <button type="submit" class="btn btn-success-600 border border-success-600 text-md px-28 py-12 radius-8">Enregistrer la vente</button>
    </div></div>
  </form>
</div>

<script src="<?= base_url() ?>assets/js/api.js"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
var BASE_URL = '<?= base_url() ?>';
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

var produitsList = <?= json_encode($produits) ?>;
var etudiantsList = <?= json_encode($etudiants) ?>;
var venteProduits = [];

// ===== AUTOCOMPLETE: show all on focus =====
function setupAutocomplete(searchId, hiddenId, listId, dataItems, renderFn) {
  var searchEl = document.getElementById(searchId);
  var listEl = document.getElementById(listId);
  var hiddenEl = document.getElementById(hiddenId);

  searchEl.addEventListener('focus', function() { renderList(this.value); });
  searchEl.addEventListener('input', function() { renderList(this.value); });

  function renderList(query) {
    var q = (query || '').toLowerCase().trim();
    var matches = q ? dataItems.filter(renderFn.filter).filter(function(item) {
      return renderFn.match(item, q);
    }) : dataItems.filter(renderFn.filter);
    if (matches.length === 0) { listEl.style.display = 'none'; return; }
    listEl.innerHTML = matches.map(function(item) {
      return renderFn.html(item);
    }).join('');
    listEl.style.display = 'block';
    listEl.querySelectorAll('button').forEach(function(btn) {
      btn.addEventListener('mousedown', function(e) {
        e.preventDefault();
        hiddenEl.value = this.dataset.id;
        searchEl.value = this.dataset.label;
        if (renderFn.onSelect) renderFn.onSelect(this.dataset);
        listEl.style.display = 'none';
      });
    });
  }

  document.addEventListener('click', function(e) {
    if (!e.target.closest('#' + searchId) && !e.target.closest('#' + listId)) {
      listEl.style.display = 'none';
    }
  });
}

setupAutocomplete('produitSearch', 'id_produit', 'produitList', produitsList, {
  filter: function(p) { return true; },
  match: function(p, q) {
    return (p.libelle + ' ' + (p.code || '')).toLowerCase().includes(q);
  },
  html: function(p) {
    return '<button type="button" class="list-group-item list-group-item-action text-start py-2 px-3 border-bottom" data-id="' + p.id_produit + '" data-label="' + p.libelle + '" data-prix="' + (p.prix_unitaire || 0) + '">' +
      '<span class="fw-medium text-sm">' + p.libelle + '</span>' +
      '<small class="d-block text-secondary-light text-xs">Code: ' + (p.code || '-') + ' | Stock: ' + (p.stock_actuel || 0) + ' | Prix: ' + parseFloat(p.prix_unitaire || 0).toLocaleString() + ' FCFA</small>' +
    '</button>';
  },
  onSelect: function(data) {
    document.getElementById('prix_unitaire').value = data.prix;
  }
});

setupAutocomplete('etudiantSearch', 'id_etudiant', 'etudiantList', etudiantsList, {
  filter: function(e) { return true; },
  match: function(e, q) {
    return (e.nom + ' ' + (e.prenom || '') + ' ' + (e.matricule || '')).toLowerCase().includes(q);
  },
  html: function(e) {
    return '<button type="button" class="list-group-item list-group-item-action text-start py-2 px-3 border-bottom" data-id="' + e.id_etudiant + '" data-label="' + e.nom + ' ' + e.prenom + ' (' + e.matricule + ')">' +
      '<span class="fw-medium text-sm">' + e.nom + ' ' + e.prenom + '</span>' +
      '<small class="d-block text-secondary-light text-xs">Matricule: ' + (e.matricule || '-') + '</small>' +
    '</button>';
  }
});

// ===== TABLE LOAD =====
async function loadData() {
  var r = await fetch(BASE_URL + 'api/mouvements').then(function(r) { return r.json(); });
  if (!r.success) { document.getElementById('dataBody').innerHTML = '<tr><td colspan="9" class="text-center text-danger">Erreur</td></tr>'; return; }
  var filterType = document.getElementById('filterType').value;
  var filterD = document.getElementById('filterDateDebut').value;
  var filterF = document.getElementById('filterDateFin').value;
  var filtered = r.data.filter(function(m) {
    if (filterType && m.type !== filterType) return false;
    if (filterD && m.date_mvt < filterD + ' 00:00:00') return false;
    if (filterF && m.date_mvt > filterF + ' 23:59:59') return false;
    return true;
  });
  var rows = '';
  filtered.forEach(function(m, i) {
    var badge = m.type === 'entree' ? '<span class="bg-success-100 text-success-600 px-12 py-4 radius-4 fw-medium text-sm">Entrée</span>' : '<span class="bg-danger-100 text-danger-600 px-12 py-4 radius-4 fw-medium text-sm">Sortie</span>';
    var d = m.date_mvt ? new Date(m.date_mvt).toLocaleDateString('fr-FR') + ' ' + new Date(m.date_mvt).toLocaleTimeString('fr-FR', {hour:'2-digit',minute:'2-digit'}) : '-';
    var eleve = m.etudiant_nom ? (m.etudiant_nom + ' ' + (m.etudiant_prenom || '')) : '-';
    rows += '<tr><td>' + (i+1) + '</td><td>' + d + '</td><td class="fw-semibold">' + (m.produit_libelle||'-') + '</td><td>' + badge + '</td><td>' + m.quantite + '</td><td>' + parseFloat(m.prix_unitaire||0).toLocaleString() + '</td><td>' + eleve + '</td><td>' + (m.motif||'-') + '</td><td>' + (m.utilisateur||'-') + '</td></tr>';
  });
  document.getElementById('dataBody').innerHTML = rows;
  if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().destroy();
  $('#dataTable').DataTable({ pageLength:10, scrollX:true, lengthMenu:[[5,10,25,50,100],[5,10,25,50,100]], language:{search:'',searchPlaceholder:'Rechercher...',lengthMenu:'Lignes par page: _MENU_',info:'',zeroRecords:'Aucun mouvement',infoEmpty:'',infoFiltered:''}, dom:'t<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>' });
}

function applyFilters() { loadData(); }
function resetFilters() { document.getElementById('filterType').value=''; document.getElementById('filterDateDebut').value=''; document.getElementById('filterDateFin').value=''; loadData(); }

// ===== MOUVEMENT SIMPLE =====
function openAddSidebar() {
  document.getElementById('sidebarTitle').textContent = 'Nouveau mouvement';
  document.getElementById('mainForm').reset();
  document.getElementById('id_produit').value = '';
  document.getElementById('produitSearch').value = '';
  document.getElementById('id_etudiant').value = '';
  document.getElementById('etudiantSearch').value = '';
  document.getElementById('quantite').value = '1';
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
}
function closeSidebar() {
  document.getElementById('addSidebar').classList.remove('active');
  document.getElementById('sidebarOverlay').classList.remove('active');
}
document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);

document.getElementById('mainForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  var id_produit = document.getElementById('id_produit').value;
  var type = document.getElementById('type').value;
  var quantite = document.getElementById('quantite').value;
  if (!id_produit) { Swal.fire({icon:'warning',title:'Validation',text:'Produit obligatoire'}); return; }
  if (!type) { Swal.fire({icon:'warning',title:'Validation',text:'Type obligatoire'}); return; }
  if (!quantite || parseInt(quantite) <= 0) { Swal.fire({icon:'warning',title:'Validation',text:'Quantité invalide'}); return; }
  var data = {
    id_produit: id_produit, type: type, quantite: parseInt(quantite),
    prix_unitaire: parseFloat(document.getElementById('prix_unitaire').value) || 0,
    motif: document.getElementById('motif').value || '',
    id_etudiant: document.getElementById('id_etudiant').value || null
  };
  var r = await fetch(BASE_URL + 'api/mouvements/create', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data) }).then(function(r){return r.json();});
  if (r.success) { closeSidebar(); Toast.fire({icon:'success',title:'Mouvement enregistré'}); loadData(); }
  else Swal.fire({icon:'error',title:'Erreur',text:r.message});
});

// ===== VENTE BATCH =====
setupAutocomplete('venteEtudiantSearch', 'venteEtudiant', 'venteEtudiantList', etudiantsList, {
  filter: function(e) { return true; },
  match: function(e, q) { return (e.nom + ' ' + (e.prenom || '') + ' ' + (e.matricule || '')).toLowerCase().includes(q); },
  html: function(e) { return '<button type="button" class="list-group-item list-group-item-action text-start py-2 px-3 border-bottom" data-id="' + e.id_etudiant + '" data-label="' + e.nom + ' ' + e.prenom + ' (' + e.matricule + ')"><span class="fw-medium text-sm">' + e.nom + ' ' + e.prenom + '</span><small class="d-block text-secondary-light text-xs">Matricule: ' + (e.matricule || '-') + '</small></button>'; }
});
setupAutocomplete('venteProduitSearch', 'venteProduit', 'venteProduitList', produitsList, {
  filter: function(p) { return true; },
  match: function(p, q) { return (p.libelle + ' ' + (p.code || '')).toLowerCase().includes(q); },
  html: function(p) { return '<button type="button" class="list-group-item list-group-item-action text-start py-2 px-3 border-bottom" data-id="' + p.id_produit + '" data-label="' + p.libelle + '" data-prix="' + (p.prix_unitaire || 0) + '"><span class="fw-medium text-sm">' + p.libelle + '</span><small class="d-block text-secondary-light text-xs">Stock: ' + (p.stock_actuel || 0) + ' | Prix: ' + parseFloat(p.prix_unitaire || 0).toLocaleString() + ' FCFA</small></button>'; },
  onSelect: function(data) { document.getElementById('ventePrix').value = data.prix; }
});

function openVenteSidebar() {
  venteProduits = [];
  document.getElementById('venteForm').reset();
  document.getElementById('venteEtudiant').value = '';
  document.getElementById('venteEtudiantSearch').value = '';
  document.getElementById('venteProduit').value = '';
  document.getElementById('venteProduitSearch').value = '';
  document.getElementById('ventePrix').value = '0.00';
  document.getElementById('venteQuantite').value = '1';
  renderVenteTable();
  document.getElementById('venteOverlay').classList.remove('visibility-hidden','opacity-0');
  document.getElementById('venteSidebar').classList.remove('translate-x-full');
}
function closeVenteSidebar() {
  document.getElementById('venteOverlay').classList.add('visibility-hidden','opacity-0');
  document.getElementById('venteSidebar').classList.add('translate-x-full');
}
document.getElementById('venteOverlay').addEventListener('click', closeVenteSidebar);

function addProduitVente() {
  var id = document.getElementById('venteProduit').value;
  if (!id) { Swal.fire({icon:'warning',title:'Validation',text:'Sélectionnez un produit'}); return; }
  var produit = produitsList.find(function(p) { return p.id_produit == id; });
  if (!produit) return;
  var qte = parseInt(document.getElementById('venteQuantite').value) || 1;
  var prix = parseFloat(document.getElementById('ventePrix').value) || parseFloat(produit.prix_unitaire) || 0;
  var exist = venteProduits.findIndex(function(v) { return v.id_produit == id; });
  if (exist >= 0) { venteProduits[exist].quantite += qte; }
  else { venteProduits.push({ id_produit: id, libelle: produit.libelle, prix_unitaire: prix, quantite: qte }); }
  document.getElementById('venteProduit').value = '';
  document.getElementById('venteProduitSearch').value = '';
  document.getElementById('ventePrix').value = '0.00';
  document.getElementById('venteQuantite').value = '1';
  renderVenteTable();
}

function removeProduitVente(idx) { venteProduits.splice(idx, 1); renderVenteTable(); }

function renderVenteTable() {
  var tbody = document.getElementById('venteBody');
  if (!venteProduits.length) {
    tbody.innerHTML = '<tr id="noVenteRow"><td colspan="5" class="text-center text-secondary-light py-3">Aucun produit ajouté</td></tr>';
    document.getElementById('venteTotal').textContent = '0,00';
    return;
  }
  var total = 0;
  tbody.innerHTML = venteProduits.map(function(v, i) {
    var st = v.prix_unitaire * v.quantite;
    total += st;
    return '<tr><td>' + v.libelle + '</td><td>' + v.prix_unitaire.toLocaleString() + '</td><td>' + v.quantite + '</td><td>' + st.toLocaleString() + '</td><td><button type="button" class="btn btn-sm text-danger" onclick="removeProduitVente(' + i + ')"><i class="ri-delete-bin-6-line"></i></button></td></tr>';
  }).join('');
  document.getElementById('venteTotal').textContent = total.toLocaleString();
}

document.getElementById('venteForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  var id_etudiant = document.getElementById('venteEtudiant').value;
  if (!id_etudiant) { Swal.fire({icon:'warning',title:'Validation',text:'Sélectionnez un élève'}); return; }
  if (!venteProduits.length) { Swal.fire({icon:'warning',title:'Validation',text:'Ajoutez au moins un produit'}); return; }
  var etudiant = etudiantsList.find(function(e) { return e.id_etudiant == id_etudiant; });
  var data = {
    id_etudiant: id_etudiant,
    nom_etudiant: etudiant ? etudiant.nom + ' ' + etudiant.prenom : 'élève',
    produits: venteProduits.map(function(v) { return { id_produit: v.id_produit, quantite: v.quantite, prix_unitaire: v.prix_unitaire }; })
  };
  var r = await fetch(BASE_URL + 'api/mouvements/batch', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data) }).then(function(r){return r.json();});
  if (r.success) {
    closeVenteSidebar();
    Swal.fire({icon:'success',title:'Vente enregistrée',text:r.message});
    loadData();
  } else Swal.fire({icon:'error',title:'Erreur',text:r.message});
});

// ===== EXPORT =====
function exportCSV() {
  var table = $('#dataTable').DataTable();
  var data = table.rows({filter:'applied'}).data();
  var csv = '\uFEFFDate,Produit,Type,Quantité,Prix,Élève,Motif,Utilisateur\n';
  data.each(function(r){var c=[];for(var i=1;i<=8;i++){var v=$(r[i]).text().trim()||r[i]||'';c.push('"'+v.replace(/"/g,'""')+'"');}csv+=c.join(',')+'\n';});
  var b=new Blob([csv],{type:'text/csv;charset=utf-8;'});var a=document.createElement('a');a.href=URL.createObjectURL(b);a.download='mouvements_stock.csv';a.click();
}

(function(){
  var w=setInterval(function(){
    if(typeof jQuery!=='undefined'&&$.fn&&$.fn.DataTable){clearInterval(w);loadData();
      document.getElementById('dtSearch').addEventListener('keyup',function(){$('#dataTable').DataTable().search(this.value).draw();});
      document.getElementById('dtLength').addEventListener('change',function(){$('#dataTable').DataTable().page.len(+this.value).draw();});
    }
  },50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>