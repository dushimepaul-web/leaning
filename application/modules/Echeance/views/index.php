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
      <div class="text-secondary-light text-sm mt-4">Échéancier individuel par étudiant. La date limite du frais sert de date par défaut.</div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-outline-primary-600 d-flex align-items-center gap-6" onclick="openGenerer()">
        <span class="d-flex text-md"><i class="ri-calendar-2-line"></i></span> Générer un échéancier
      </button>
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

<div class="modal fade" id="genererModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-header border-bottom py-12 px-20">
        <h6 class="text-lg fw-semibold text-primary-light mb-0">Générer un échéancier</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-20">
        <div class="mb-3">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Frais *</label>
          <select id="genFrais" class="form-control form-select"><option value="">Sélectionner...</option></select>
        </div>
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Nombre d'échéances *</label>
            <input type="number" id="genNb" class="form-control" min="1" max="24" value="3">
          </div>
          <div class="col-6">
            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Intervalle (mois)</label>
            <input type="number" id="genIntervalle" class="form-control" min="1" max="12" value="1">
          </div>
        </div>
        <div class="mb-3">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Date de la 1ère échéance *</label>
          <input type="date" id="genDate" class="form-control">
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="genRemplacer">
          <label class="form-check-label" for="genRemplacer">Remplacer les échéances existantes (si aucun paiement)</label>
        </div>
        <small class="text-secondary-light">Pour un frais global, tous les étudiants inscrits sont concernés. Le montant est calculé selon le régime (interne/externe) et diminué des exonérations. La date peut être préremplie depuis la date limite du frais.</small>
      </div>
      <div class="modal-footer border-top py-12 px-20">
        <button type="button" class="btn btn-primary-600" onclick="genererEcheancier()">Générer</button>
        <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-24 py-11 radius-8" data-bs-dismiss="modal">Annuler</button>
      </div>
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
        <div class="d-flex align-items-center gap-8">
          <button type="button" class="text-primary-light text-xl" onclick="editData('${d.uuid}')"><i class="ri-edit-2-line"></i></button>
          <button type="button" class="text-danger text-xl" onclick="deleteData('${d.uuid}')"><i class="ri-delete-bin-6-line"></i></button>
        </div>
      </td>
    </tr>`;
  });
  if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().destroy();
  $('#dataBody').html(rows);
  $('#dataTable').DataTable({
    pageLength: 10, scrollX: true,
    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucune échéance trouvée', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

async function openGenerer() {
  var sel = document.getElementById('genFrais');
  var fraisMap = {};
  sel.innerHTML = '<option value="">Sélectionner...</option>';
  var r = await API.frais.list({ id_annee: <?= (int)$id_annee_active ?>, id_periode: <?= (int)$id_periode_active ?> });
  if (r.success) {
    r.data.forEach(function(f) {
      var o = document.createElement('option');
      var classe = f.classe_libelle || 'Toutes les classes';
      var externe = parseFloat(f.montant || 0).toLocaleString();
      var interne = (f.montant_interne !== null && f.montant_interne !== undefined && f.montant_interne !== '')
        ? parseFloat(f.montant_interne).toLocaleString() : null;
      o.value = f.id_frais;
      o.textContent = (f.type_libelle || '') + ' - ' + classe + ' - ' + (f.periode_libelle || '') + ' - Ext: ' + externe + (interne !== null ? ' / Int: ' + interne : '');
      sel.appendChild(o);
      fraisMap[f.id_frais] = f;
    });
  }
  sel.onchange = function() {
    var f = fraisMap[this.value];
    document.getElementById('genDate').value = (f && f.echeance) ? String(f.echeance).substring(0, 10) : '';
  };
  document.getElementById('genNb').value = 3;
  document.getElementById('genDate').value = '';
  document.getElementById('genIntervalle').value = 1;
  document.getElementById('genRemplacer').checked = false;
  new bootstrap.Modal(document.getElementById('genererModal')).show();
}

async function genererEcheancier() {
  var d = {
    id_frais: document.getElementById('genFrais').value,
    nb_echeances: document.getElementById('genNb').value,
    date_premiere: document.getElementById('genDate').value,
    intervalle_mois: document.getElementById('genIntervalle').value,
    remplacer: document.getElementById('genRemplacer').checked ? 1 : 0
  };
  if (!d.id_frais) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Frais obligatoire' }); return; }
  var r = await API.echeances.generer(d);
  if (r.success) {
    var m = bootstrap.Modal.getInstance(document.getElementById('genererModal'));
    if (m) m.hide();
    Toast.fire({ icon: 'success', title: r.message });
    loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
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
  if (_e) document.getElementById('fEtudiant_search').value = (_e.fullname || _e.nom || '') + ' (' + (_e.matricule || '') + ')';
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
    var fraisOptions = [];
    API.frais.list({ id_annee: <?= (int)$id_annee_active ?>, id_periode: <?= (int)$id_periode_active ?> }).then(function(r) { if (r.success) fraisOptions = r.data; });
    var fillFrais = function(classeId) {
      var sel = document.getElementById('fFrais');
      sel.innerHTML = '<option value="">Sélectionner...</option>';
      fraisOptions.filter(function(f) { return !classeId || String(f.id_classe) === String(classeId); })
        .forEach(function(d) { var o = document.createElement('option'); o.value = d.id_frais; o.textContent = (d.type_libelle || '') + ' - ' + parseFloat(d.montant || 0).toLocaleString(); sel.appendChild(o); });
    };
    autoSetup('fEtudiant_search', 'fEtudiant', 'fEtudiant_results', etudiantsData.map(function(e) { return { id: e.id_etudiant, nom: e.fullname || e.nom, prenom: e.prenom || '', matricule: e.matricule, id_classe: e.id_classe }; }), function(e) { return e.nom + ' (' + (e.matricule || '') + ')'; }, function(item) { fillFrais(item.id_classe); });
    fillFrais(null);
  };
  _f();
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>