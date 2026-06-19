<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Paiements</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Paiements</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openSidebar()">
        <span class="d-flex text-md"><i class="ri-add-line"></i></span> Nouveau paiement
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
      <table class="table bordered-table mb-0 data-table" id="paiementsTable" style="width:100%">
        <thead>
          <tr>
            <th>#</th>
            <th>Étudiant</th>
            <th>Matricule</th>
            <th>Type</th>
            <th>Montant</th>
            <th>Date</th>
            <th>Mode</th>
            <th>Réf.</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="paiementsBody"></tbody>
      </table>
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
      <input type="text" class="form-control" id="payEtudiant_search" placeholder="Rechercher..." autocomplete="off">
      <div id="payEtudiant_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
    </div>
    <div class="mb-3 position-relative">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type de frais</label>
      <input type="hidden" id="payType">
      <input type="text" class="form-control" id="payType_search" placeholder="Rechercher..." autocomplete="off">
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
        <option value="partiel">Partiel</option>
        <option value="solde">Soldé</option>
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
<script src="<?= base_url() ?>assets/js/autocomplete.js?v=<?= filemtime(FCPATH.'assets/js/autocomplete.js') ?>"></script>
<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let BASE_URL = '<?= base_url() ?>';
let editUuid = null;
let etudiantsData = [];
let typesFraisData = [];
try { const el = document.getElementById('etudiants_data'); if (el) etudiantsData = JSON.parse(el.textContent); } catch(e) {}
try { const el = document.getElementById('types_frais_data'); if (el) typesFraisData = JSON.parse(el.textContent); } catch(e) {}

async function loadData() {
  const r = await API.paiements_data.list();
  if (!r.success) { $('#paiementsBody').html('<tr><td colspan="10" class="text-center text-danger">Erreur</td></tr>'); return; }
  let rows = '';
  r.data.forEach((p, i) => {
    const statutClass = p.statut === 'solde' ? 'success' : p.statut === 'annule' ? 'danger' : 'warning';
    rows += `<tr>
      <td>${i + 1}</td>
      <td class="fw-semibold">${p.nom} ${p.prenom}</td>
      <td>${p.matricule || '-'}</td>
      <td>${p.type_frais || '-'}</td>
      <td><strong>${parseFloat(p.montant || 0).toLocaleString()}</strong></td>
      <td>${p.date_paiement || '-'}</td>
      <td>${p.mode_paiement || '-'}</td>
      <td>${p.reference || '-'}</td>
      <td><span class="badge bg-${statutClass}-600">${p.statut || '-'}</span></td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editData('${p.uuid}')"><i class="ri-edit-2-line"></i> Modifier</button></li>
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="deleteData('${p.uuid}')"><i class="ri-delete-bin-line"></i> Supprimer</button></li>
          </ul>
        </div>
      </td>
    </tr>`;
  });
  $('#paiementsBody').html(rows);
  if ($.fn.DataTable.isDataTable('#paiementsTable')) $('#paiementsTable').DataTable().destroy();
  $('#paiementsTable').DataTable({
    pageLength: 10, scrollX: true,
    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucun paiement trouvé', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
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

async function editData(uuid) {
  const r = await API.paiements_data.get(uuid);
  if (!r.success) { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); return; }
  editUuid = uuid;
  const p = r.data;
  document.getElementById('formTitle').textContent = 'Modifier le paiement';
  document.getElementById('payEtudiant').value = p.id_etudiant || '';
  document.getElementById('payType').value = p.id_frais || '';
  const _e = etudiantsData.find(function(x) { return String(x.id_etudiant) === String(p.id_etudiant); });
  if (_e) document.getElementById('payEtudiant_search').value = _e.nom + ' ' + _e.prenom + ' (' + (_e.matricule || '') + ')';
  document.getElementById('payMontant').value = p.montant || '';
  document.getElementById('payMode').value = p.mode_paiement || 'especes';
  document.getElementById('payReference').value = p.reference || '';
  document.getElementById('payStatut').value = p.statut || 'partiel';
  document.getElementById('payNotes').value = p.notes || '';
  document.getElementById('formOverlay').classList.remove('visibility-hidden', 'opacity-0');
  document.getElementById('formSidebar').classList.remove('translate-x-full');
}

async function deleteData(uuid) {
  const c = await Swal.fire({ title: 'Confirmation', text: 'Supprimer ce paiement ?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Annuler', confirmButtonText: 'Oui, supprimer' });
  if (!c.isConfirmed) return;
  const r = await API.paiements_data.delete(uuid);
  if (r.success) { Toast.fire({ icon: 'success', title: 'Supprimé' }); loadData(); }
  else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
}

document.getElementById('paiementForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const data = {
    id_etudiant: document.getElementById('payEtudiant').value,
    id_frais: document.getElementById('payType').value,
    montant: document.getElementById('payMontant').value,
    mode_paiement: document.getElementById('payMode').value,
    reference: document.getElementById('payReference').value,
    statut: document.getElementById('payStatut').value,
    notes: document.getElementById('payNotes').value
  };
  if (!data.id_etudiant || !data.montant) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Étudiant et montant obligatoires' }); return; }
  const r = editUuid ? await API.paiements_data.update(editUuid, data) : await API.paiements_data.create(data);
  if (r.success) {
    closeSidebar();
    if (!editUuid && r.data && r.data.numero_recu) {
      const result = await Swal.fire({
        icon: 'success', title: 'Paiement enregistré',
        text: 'Reçu N° ' + r.data.numero_recu + ' généré automatiquement',
        showCancelButton: true, confirmButtonText: 'Imprimer le reçu', cancelButtonText: 'Fermer'
      });
      if (result.isConfirmed) { window.open(BASE_URL + 'Recus/imprimer/' + r.data.recu_uuid, '_blank'); }
    } else {
      Toast.fire({ icon: 'success', title: editUuid ? 'Mis à jour' : 'Enregistré' });
    }
    loadData();
  }
  else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
});

document.getElementById('formOverlay').addEventListener('click', closeSidebar);
document.getElementById('dtSearch').addEventListener('keyup', function() { if ($.fn.DataTable.isDataTable('#paiementsTable')) $('#paiementsTable').DataTable().search(this.value).draw(); });
document.getElementById('dtLength').addEventListener('change', function() { if ($.fn.DataTable.isDataTable('#paiementsTable')) $('#paiementsTable').DataTable().page.len(+this.value).draw(); });
(function() {
  var _f = function() {
    if (typeof $ === 'undefined' || typeof API === 'undefined' || typeof autoSetup === 'undefined') { setTimeout(_f, 50); return; }
    loadData();
    autoSetup('payEtudiant_search', 'payEtudiant', 'payEtudiant_results', etudiantsData.map(function(e) { return { id: e.id_etudiant, nom: e.nom, prenom: e.prenom, matricule: e.matricule }; }), function(e) { return e.nom + ' ' + e.prenom + ' (' + (e.matricule || '') + ')'; });
    autoSetup('payType_search', 'payType', 'payType_results', typesFraisData.map(function(t) { return { id: t.id_type_frais, libelle: t.libelle }; }), function(t) { return t.libelle; });
  };
  _f();
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
