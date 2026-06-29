<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Paiements</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Minervales / Paiements</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openSidebar()">
        <span class="d-flex text-md"><i class="ri-add-line"></i></span> Nouveau paiement
      </button>
    </div>
  </div>

  <div class="card mb-24">
    <div class="card-body p-16">
      <div class="row g-3 align-items-end">
        <div class="col-lg-2 col-md-4 col-sm-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Classe</label>
          <select id="filterClasse" class="form-control form-select" onchange="applyFilters()">
            <option value="">Toutes les classes</option>
          </select>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Section</label>
          <select id="filterSection" class="form-control form-select" onchange="applyFilters()">
            <option value="">Toutes les sections</option>
          </select>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Année scolaire</label>
          <select id="filterAnnee" class="form-control form-select" onchange="applyFilters()">
            <option value="">Toutes les années</option>
          </select>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Statut</label>
          <select id="filterStatut" class="form-control form-select" onchange="applyFilters()">
            <option value="">Tous</option>
            <option value="solde">Soldé</option>
            <option value="partiel">Partiel</option>
            <option value="annule">Annulé</option>
          </select>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 d-flex gap-2">
          <button type="button" class="btn btn-primary-600 flex-grow-1" onclick="applyFilters()"><i class="ri-filter-line"></i> Filtrer</button>
          <button type="button" class="btn btn-outline-danger-600 flex-grow-1" onclick="resetFilters()"><i class="ri-close-line"></i> Reset</button>
        </div>
      </div>
    </div>
  </div>

  <div class="card h-100">
    <div class="card-body p-0 dataTable-wrapper">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
        <div class="d-flex align-items-center gap-16">
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
      <table class="table bordered-table mb-0 data-table" id="paiementsTable" style="width:100%">
        <thead>
          <tr>
            <th>#</th>
            <th>Étudiant</th>
            <th>Matricule</th>
            <th>Classe</th>
            <th>Section</th>
            <th>Type</th>
            <th>Montant</th>
            <th>Date</th>
            <th>Mode</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="paiementsBody"></tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" id="recuModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-header border-bottom py-12 px-20">
        <h6 class="text-lg fw-semibold text-primary-light mb-0">Reçu de paiement</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-20" id="recuContent"></div>
      <div class="modal-footer border-top py-12 px-20">
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-2" onclick="imprimerRecu()"><i class="ri-printer-line"></i> Imprimer</button>
        <button type="button" class="btn btn-success-600 d-flex align-items-center gap-2" onclick="telechargerRecu()"><i class="ri-download-line"></i> Télécharger</button>
        <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-24 py-11 radius-8" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="formOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="formSidebar" style="width:50vw;max-width:50vw;box-shadow:-4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="formTitle">Nouveau paiement</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="paiementForm" class="d-flex flex-column p-20">
    <div class="mb-3 position-relative">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Étudiant *</label>
      <input type="hidden" id="payEtudiant">
      <input type="text" class="form-control" id="payEtudiant_search" placeholder="Rechercher un étudiant..." autocomplete="off">
      <div id="payEtudiant_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
    </div>
    <div class="row mb-3">
      <div class="col-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe</label>
        <input type="text" class="form-control" id="payClasse" readonly placeholder="Sélectionnez un étudiant">
      </div>
      <div class="col-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Section</label>
        <input type="text" class="form-control" id="paySection" readonly placeholder="Sélectionnez un étudiant">
      </div>
      <input type="hidden" id="payClasseId">
      <input type="hidden" id="paySectionId">
    </div>
    <div class="mb-3 position-relative">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type de frais</label>
      <input type="hidden" id="payType">
      <input type="text" class="form-control" id="payType_search" placeholder="Rechercher un type..." autocomplete="off">
      <div id="payType_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Montant *</label>
      <input type="number" class="form-control" id="payMontant" step="0.01" placeholder="0.00">
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Mode de paiement</label>
      <select class="form-control form-select" id="payMode">
        <option value="especes">Espèces</option>
        <option value="banque">Banque</option>
        <option value="mobile_money">Mobile Money</option>
        <option value="cheque">Chèque</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Référence</label>
      <input type="text" class="form-control" id="payReference" placeholder="N° de transaction">
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Statut</label>
      <select class="form-control form-select" id="payStatut">
        <option value="solde">Soldé</option>
        <option value="partiel">Partiel</option>
        <option value="annule">Annulé</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Notes</label>
      <textarea class="form-control" id="payNotes" rows="2"></textarea>
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
<script id="types_frais_data" type="application/json"><?= json_encode($types_frais) ?></script>
<script src="<?= base_url() ?>assets/js/autocomplete.js?v=2"></script>
<script src="<?= base_url() ?>assets/js/api.js?v=2"></script>
<script>
var BASE_URL = '<?= base_url() ?>';
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let editUuid = null;
let currentRecuUuid = null;
let allPaiements = [];
let etudiantsData = [];
let typesFraisData = [];
try { const el = document.getElementById('etudiants_data'); if (el) etudiantsData = JSON.parse(el.textContent); } catch(e) {}
try { const el = document.getElementById('types_frais_data'); if (el) typesFraisData = JSON.parse(el.textContent); } catch(e) {}

async function loadFilters() {
  try {
    const classesRes = await API.classes.list();
    if (classesRes.success && classesRes.data) {
      let opts = '<option value="">Toutes les classes</option>';
      classesRes.data.forEach(c => { opts += '<option value="' + c.libelle + '">' + c.libelle + '</option>'; });
      document.getElementById('filterClasse').innerHTML = opts;
    }
    const sectionsRes = await API.sections.list();
    if (sectionsRes.success && sectionsRes.data) {
      let opts = '<option value="">Toutes les sections</option>';
      sectionsRes.data.forEach(s => { opts += '<option value="' + s.libelle + '">' + s.libelle + '</option>'; });
      document.getElementById('filterSection').innerHTML = opts;
    }
    const anneesRes = await API.annees.list();
    if (anneesRes.success && anneesRes.data) {
      let opts = '<option value="">Toutes les années</option>';
      anneesRes.data.forEach(a => { opts += '<option value="' + a.libelle + '">' + a.libelle + '</option>'; });
      document.getElementById('filterAnnee').innerHTML = opts;
    }
  } catch(e) { console.error(e); }
}

function applyFilters() {
  if ($.fn.DataTable.isDataTable('#paiementsTable')) {
    var classe = document.getElementById('filterClasse').value;
    var section = document.getElementById('filterSection').value;
    var annee = document.getElementById('filterAnnee').value;
    var statut = document.getElementById('filterStatut').value;

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
      var rowData = allPaiements[dataIndex];
      if (!rowData) return true;
      if (classe && rowData.classe_libelle !== classe) return false;
      if (section && rowData.section_libelle !== section) return false;
      if (annee && rowData.annee_libelle !== annee) return false;
      if (statut && rowData.statut !== statut) return false;
      return true;
    });
    $('#paiementsTable').DataTable().draw();
  }
}

function resetFilters() {
  document.getElementById('filterClasse').value = '';
  document.getElementById('filterSection').value = '';
  document.getElementById('filterAnnee').value = '';
  document.getElementById('filterStatut').value = '';
  if ($.fn.DataTable.isDataTable('#paiementsTable')) {
    $.fn.dataTable.ext.search = [];
    $('#paiementsTable').DataTable().draw();
  }
}

async function loadData() {
  try {
    const classesRes = await API.classes.list();
    window._classesMap = {};
    if (classesRes.success && classesRes.data) {
      classesRes.data.forEach(function(c) { window._classesMap[c.id_classe] = c.libelle; });
    }
    const sectionsRes = await API.sections.list();
    window._sectionsMap = {};
    if (sectionsRes.success && sectionsRes.data) {
      sectionsRes.data.forEach(function(s) { window._sectionsMap[s.id_section] = s.libelle; });
    }
    const anneesRes = await API.annees.list();
    window._anneesMap = {};
    if (anneesRes.success && anneesRes.data) {
      window._anneesMap[a.id_annee] = a.libelle;  // fixed typo: a not anneesData
    }
  } catch(e) {}

  const r = await API.paiements_data.list();
  if (!r.success) { $('#paiementsBody').html('<tr><td colspan="11" class="text-center text-danger">Erreur</td></tr>'); return; }
  allPaiements = r.data || [];
  var rows = '';
  allPaiements.forEach(function(p, i) {
    var classeLib = (window._classesMap && window._classesMap[p.id_classe]) || '';
    var sectionLib = (window._sectionsMap && window._sectionsMap[p.id_section]) || '';
    var anneeLib = (window._anneesMap && window._anneesMap[p.id_annee]) || '';
    p.classe_libelle = classeLib;
    p.section_libelle = sectionLib;
    p.annee_libelle = anneeLib;
    var statutClass = p.statut === 'solde' ? 'success' : p.statut === 'annule' ? 'danger' : 'warning';
    rows += '<tr data-classe="' + classeLib + '" data-section="' + sectionLib + '" data-statut="' + (p.statut||'') + '">';
    rows += '<td>' + (i + 1) + '</td>';
    rows += '<td class="fw-semibold">' + (p.nom||'') + ' ' + (p.prenom||'') + '</td>';
    rows += '<td>' + (p.matricule || '-') + '</td>';
    rows += '<td>' + (classeLib || '-') + '</td>';
    rows += '<td>' + (sectionLib || '-') + '</td>';
    rows += '<td>' + (p.type_frais || '-') + '</td>';
    rows += '<td><strong>' + parseFloat(p.montant || 0).toLocaleString() + '</strong></td>';
    rows += '<td>' + (p.date_paiement || '-') + '</td>';
    rows += '<td>' + (p.mode_paiement || '-') + '</td>';
    rows += '<td><span class="badge bg-' + statutClass + '-600">' + (p.statut || '-') + '</span></td>';
    rows += '<td><div class="d-flex gap-1">';
    rows += '<button class="btn btn-sm btn-outline-primary-600" title="Voir" onclick="voirPaiement(\'' + p.uuid + '\')"><i class="ri-eye-line"></i></button>';
    rows += '<button class="btn btn-sm btn-outline-success-600" title="Imprimer reçu" onclick="voirRecu(\'' + p.uuid + '\')"><i class="ri-printer-line"></i></button>';
    rows += '<button class="btn btn-sm btn-outline-info-600" title="Télécharger reçu" onclick="telechargerRecuPaiement(\'' + p.uuid + '\')"><i class="ri-download-line"></i></button>';
    rows += '</div></td>';
    rows += '</tr>';
  });
  $('#paiementsBody').html(rows);
  if ($.fn.DataTable.isDataTable('#paiementsTable')) $('#paiementsTable').DataTable().destroy();
  $('#paiementsTable').DataTable({
    pageLength: 10, scrollX: true,
    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucun paiement trouvé', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>',
    drawCallback: function() {
      $.fn.dataTable.ext.search = [];
    }
  });
}

function voirPaiement(uuid) {
  var p = allPaiements.find(function(x) { return x.uuid === uuid; });
  if (!p) return;
  Swal.fire({
    title: 'Détail du paiement',
    html: '<div class="text-start">' +
      '<p><strong>Étudiant:</strong> ' + (p.nom||'') + ' ' + (p.prenom||'') + ' (' + (p.matricule||'') + ')</p>' +
      '<p><strong>Type:</strong> ' + (p.type_frais||'-') + '</p>' +
      '<p><strong>Montant:</strong> ' + parseFloat(p.montant||0).toLocaleString() + ' FCFA</p>' +
      '<p><strong>Date:</strong> ' + (p.date_paiement||'-') + '</p>' +
      '<p><strong>Mode:</strong> ' + (p.mode_paiement||'-') + '</p>' +
      '<p><strong>Référence:</strong> ' + (p.reference||'-') + '</p>' +
      '<p><strong>Statut:</strong> ' + (p.statut||'-') + '</p>' +
      '</div>',
    icon: 'info', confirmButtonText: 'Fermer'
  });
}

async function voirRecu(uuid) {
  try {
    var p = allPaiements.find(function(x) { return x.uuid === uuid; });
    if (!p) return;
    var recusRes = await API.recus.list();
    if (!recusRes.success) { Swal.fire({icon:'error',title:'Erreur',text:'Reçus non trouvés'}); return; }
    var found = null;
    var recus = recusRes.data || [];
    for (var i = 0; i < recus.length; i++) {
      var detailRes = await API.recus.get(recus[i].uuid);
      if (detailRes.success && detailRes.data && String(detailRes.data.id_etudiant) === String(p.id_etudiant)) {
        found = detailRes.data;
        break;
      }
    }
    if (!found) { Swal.fire({icon:'info',title:'Info',text:'Aucun reçu trouvé pour ce paiement'}); return; }
    currentRecuUuid = found.uuid;
    document.getElementById('recuContent').innerHTML =
      '<div class="text-center mb-3"><h5>REÇU N° ' + (found.numero_recu||'-') + '</h5></div>' +
      '<div class="row"><div class="col-6"><p><strong>Date:</strong> ' + (found.date_edition||'-') + '</p></div><div class="col-6"><p><strong>Montant:</strong> ' + parseFloat(found.montant_total||0).toLocaleString() + ' FCFA</p></div></div>';
    new bootstrap.Modal(document.getElementById('recuModal')).show();
  } catch(e) { Swal.fire({icon:'error',title:'Erreur',text:e.message}); }
}

function imprimerRecu() {
  if (!currentRecuUuid) return;
  window.open(BASE_URL + 'Recus/imprimer/' + currentRecuUuid, '_blank');
}

function telechargerRecu() {
  if (!currentRecuUuid) return;
  window.open(BASE_URL + 'Recus/imprimer/' + currentRecuUuid + '?download=1', '_blank');
}

async function telechargerRecuPaiement(uuid) {
  var p = allPaiements.find(function(x) { return x.uuid === uuid; });
  if (!p) return;
  try {
    var recusRes = await API.recus.list();
    if (!recusRes.success) return;
    var recus = recusRes.data || [];
    for (var i = 0; i < recus.length; i++) {
      var detailRes = await API.recus.get(recus[i].uuid);
      if (detailRes.success && detailRes.data && String(detailRes.data.id_etudiant) === String(p.id_etudiant)) {
        window.open(BASE_URL + 'Recus/imprimer/' + detailRes.data.uuid + '?download=1', '_blank');
        return;
      }
    }
    Swal.fire({icon:'info',title:'Info',text:'Aucun reçu trouvé'});
  } catch(e) {}
}

function openSidebar() {
  editUuid = null;
  document.getElementById('paiementForm').reset();
  document.getElementById('formTitle').textContent = 'Nouveau paiement';
  document.getElementById('formOverlay').classList.remove('visibility-hidden', 'opacity-0');
  document.getElementById('formSidebar').classList.remove('translate-x-full');
}

function closeSidebar() {
  document.getElementById('formOverlay').classList.add('visibility-hidden', 'opacity-0');
  document.getElementById('formSidebar').classList.add('translate-x-full');
}

document.getElementById('paiementForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  var data = {
    id_etudiant: document.getElementById('payEtudiant').value,
    id_frais: document.getElementById('payType').value,
    montant: document.getElementById('payMontant').value,
    mode_paiement: document.getElementById('payMode').value,
    reference: document.getElementById('payReference').value,
    statut: document.getElementById('payStatut').value,
    notes: document.getElementById('payNotes').value,
    id_classe: document.getElementById('payClasseId').value || null,
    id_section: document.getElementById('paySectionId').value || null
  };
  if (!data.id_etudiant || !data.montant) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Étudiant et montant obligatoires' }); return; }
  var r = editUuid ? await API.paiements_data.update(editUuid, data) : await API.paiements_data.create(data);
  if (r.success) {
    closeSidebar();
    if (!editUuid && r.data && r.data.numero_recu) {
      currentRecuUuid = r.data.recu_uuid;
      var result = await Swal.fire({
        icon: 'success', title: 'Paiement enregistré',
        text: 'Reçu N° ' + r.data.numero_recu + ' généré',
        showCancelButton: true, confirmButtonText: 'Imprimer le reçu', cancelButtonText: 'Fermer'
      });
      if (result.isConfirmed) window.open(BASE_URL + 'Recus/imprimer/' + r.data.recu_uuid, '_blank');
    } else {
      Toast.fire({ icon: 'success', title: editUuid ? 'Mis à jour' : 'Enregistré' });
    }
    loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
});

document.getElementById('formOverlay').addEventListener('click', closeSidebar);
document.getElementById('dtSearch').addEventListener('keyup', function() {
  if ($.fn.DataTable.isDataTable('#paiementsTable')) $('#paiementsTable').DataTable().search(this.value).draw();
});
document.getElementById('dtLength').addEventListener('change', function() {
  if ($.fn.DataTable.isDataTable('#paiementsTable')) $('#paiementsTable').DataTable().page.len(+this.value).draw();
});

function exportCSV() {
  var table = $('#paiementsTable').DataTable();
  var data = table.rows({ filter: 'applied' }).data();
  var csv = '\uFEFF';
  csv += 'Étudiant,Matricule,Classe,Section,Type,Montant,Date,Mode,Statut\n';
  data.each(function(row) {
    var cols = [];
    for (var i = 1; i <= 9; i++) {
      var val = $(row[i]).text().trim() || row[i] || '';
      val = '"' + val.replace(/"/g, '""') + '"';
      cols.push(val);
    }
    csv += cols.join(',') + '\n';
  });
  var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  var link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'paiements.csv';
  link.click();
}

(function() {
  var done = false;
  var _f = function() {
    if (typeof $ === 'undefined' || typeof API === 'undefined' || typeof autoSetup === 'undefined') { setTimeout(_f, 50); return; }
    if (done) return; done = true;
    loadFilters();
    loadData();
    autoSetup('payEtudiant_search', 'payEtudiant', 'payEtudiant_results',
      etudiantsData.map(function(e) { return { id: e.id_etudiant, nom: e.nom, prenom: e.prenom, matricule: e.matricule, id_classe: e.id_classe, id_section: e.id_section, classe_libelle: e.classe_libelle, section_libelle: e.section_libelle }; }),
      function(e) { return e.nom + ' ' + e.prenom + ' (' + (e.matricule || '') + ')'; },
      function(e) {
        document.getElementById('payClasse').value = e.classe_libelle || '';
        document.getElementById('payClasseId').value = e.id_classe || '';
        document.getElementById('paySection').value = e.section_libelle || '';
        document.getElementById('paySectionId').value = e.id_section || '';
      }
    );
    autoSetup('payType_search', 'payType', 'payType_results',
      typesFraisData.map(function(t) { return { id: t.id_type_frais, libelle: t.libelle }; }),
      function(t) { return t.libelle; }
    );
  };
  _f();
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
