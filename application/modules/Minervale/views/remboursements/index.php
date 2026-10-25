<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Remboursements</h1>
      <div>
        <a href="<?= base_url('Minervale') ?>" class="text-secondary-light hover-text-primary hover-underline">Minervale</a>
        <span class="text-secondary-light"> / Remboursements</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openCreate()">
      <i class="ri-add-line"></i> Nouveau remboursement
    </button>
  </div>

  <div class="card radius-12 mb-24">
    <div class="card-body p-20">
      <form id="filterForm" class="row g-3 align-items-end" onsubmit="event.preventDefault(); loadData();">
        <div class="col-xl-3 col-md-4">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Annee</label>
          <select class="form-control form-select" name="id_annee">
            <?php foreach ($options['annees'] as $annee): ?>
              <option value="<?= (int)$annee['id_annee'] ?>" <?= (int)$annee['id_annee'] === (int)$id_annee_active ? 'selected' : '' ?>><?= htmlspecialchars($annee['libelle']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-xl-3 col-md-4">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Statut</label>
          <select class="form-control form-select" name="statut">
            <option value="">Tous</option>
            <option value="effectue">Effectue</option>
            <option value="annule">Annule</option>
          </select>
        </div>
        <div class="col-xl-3 col-md-4">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Recherche</label>
          <input type="text" class="form-control" name="search" placeholder="Nom, matricule ou reference" oninput="scheduleLoadData()">
        </div>
        <div class="col-xl-3 d-flex gap-2">
          <button type="submit" class="btn btn-primary-600"><i class="ri-filter-3-line me-1"></i> Filtrer</button>
          <button type="button" class="btn btn-outline-secondary-600" onclick="document.getElementById('filterForm').reset(); loadData();">Reinitialiser</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card radius-12">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table bordered-table mb-0">
          <thead>
            <tr>
              <th>Date</th>
              <th>Etudiant</th>
              <th>Paiement</th>
              <th>Frais</th>
              <th class="text-end">Montant</th>
              <th>Mode</th>
              <th>Motif</th>
              <th>Statut</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="dataBody">
            <tr><td colspan="9" class="text-center text-secondary-light py-20">Chargement...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer py-12 px-20 text-secondary-light text-sm" id="countLabel">0 remboursement</div>
  </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-header border-bottom py-12 px-20">
        <h6 class="text-lg fw-semibold text-primary-light mb-0">Nouveau remboursement</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="createForm">
        <div class="modal-body p-20">
          <div class="row g-3">
            <div class="col-12 position-relative">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Paiement source *</label>
              <input type="text" class="form-control" id="paymentSearch" placeholder="Tapez un nom, un matricule ou une reference..." autocomplete="off" oninput="searchPayments()" onfocus="searchPayments(true)">
              <input type="hidden" name="id_paiement" id="paymentSelect">
              <div id="paymentResults" class="list-group position-absolute w-100 shadow-sm" style="display:none;max-height:240px;overflow-y:auto;z-index:1055;"></div>
              <div class="text-xs text-secondary-light mt-4" id="paymentHint">Aucun paiement sélectionné.</div>
            </div>
            <div class="col-12">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Dette remboursee *</label>
              <select class="form-control form-select" name="id_dette" id="debtSelect" required>
                <option value="">Selectionner un paiement d'abord</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Montant *</label>
              <input type="number" step="0.01" min="0.01" class="form-control" name="montant" id="refundAmount" required>
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Mode *</label>
              <select class="form-control form-select" name="mode_remboursement" required>
                <option value="especes">Especes</option>
                <option value="mobile_money">Mobile money</option>
                <option value="banque">Banque</option>
                <option value="cheque">Cheque</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Date</label>
              <input type="date" class="form-control" name="date_remboursement" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Reference</label>
              <input type="text" class="form-control" name="reference" maxlength="100">
            </div>
            <div class="col-12">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Motif *</label>
              <textarea class="form-control" name="motif" rows="3" maxlength="255" required></textarea>
            </div>
          </div>
          <div class="alert alert-warning radius-8 mt-16 mb-0 text-sm" id="refundHint">
            Le remboursement ne peut pas depasser le montant affecte au paiement pour la dette choisie.
          </div>
        </div>
        <div class="modal-footer border-top py-12 px-20">
          <button type="button" class="btn btn-outline-secondary-600" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary-600">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<?php include __DIR__.'/../includes/common.php'; ?>
<script>
var rows = [];
var createModal = null;
var paymentDetails = null;
var PAYMENTS = [];
var selectedPaymentLabel = '';
var searchTimer = null;

function filterValues() {
  var data = {};
  new FormData(document.getElementById('filterForm')).forEach(function (value, key) {
    if (value !== '') data[key] = value;
  });
  return data;
}

function selectedAnnee() {
  var select = document.querySelector('#filterForm [name="id_annee"]');
  return select && select.value ? select.value : '<?= (int)$id_annee_active ?>';
}

function scheduleLoadData() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(loadData, 300);
}

function normalizeText(value) {
  return String(value == null ? '' : value).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
}

function todayLocal() {
  var d = new Date();
  return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
}

function loadData() {
  var body = document.getElementById('dataBody');
  body.innerHTML = '<tr><td colspan="9" class="text-center text-secondary-light py-20">Chargement...</td></tr>';
  API.get('api/minervale/remboursements', filterValues()).then(function (r) {
    if (!r.success) { body.innerHTML = '<tr><td colspan="9" class="text-center text-danger-main py-20">' + MV.esc(r.message) + '</td></tr>'; return; }
    rows = r.data || [];
    document.getElementById('countLabel').textContent = rows.length + ' remboursement(s)';
    if (!rows.length) {
      body.innerHTML = '<tr><td colspan="9" class="text-center text-secondary-light py-20">Aucun remboursement</td></tr>';
      return;
    }
    body.innerHTML = rows.map(function (x, index) {
      var actions = '<div class="d-flex justify-content-end">';
      if (x.statut === 'effectue') {
        actions += '<button type="button" class="btn btn-sm btn-danger-focus text-danger-main radius-4" title="Annuler" onclick="annuler(' + index + ')"><i class="ri-close-circle-line"></i></button>';
      }
      actions += '</div>';
      return '<tr>' +
        '<td>' + MV.esc((x.date_remboursement || '').substring(0, 16)) + '</td>' +
        '<td><div class="fw-semibold">' + MV.esc(x.fullname || '-') + '</div><div class="text-xs text-secondary-light">' + MV.esc(x.matricule || '') + '</div></td>' +
        '<td><div class="text-xs">' + MV.esc((x.date_paiement || '').substring(0, 16)) + '</div><div class="text-xs text-secondary-light">' + MV.money(x.paiement_montant) + '</div></td>' +
        '<td>' + MV.esc(x.type_libelle || '-') + '</td>' +
        '<td class="text-end fw-semibold text-danger-main">' + MV.money(x.montant) + '</td>' +
        '<td>' + MV.esc(x.mode_remboursement || '-') + '</td>' +
        '<td><span title="' + MV.esc(x.motif || '') + '">' + MV.esc((x.motif || '-').substring(0, 40)) + (x.motif && x.motif.length > 40 ? '...' : '') + '</span></td>' +
        '<td>' + MV.badge(x.statut) + '</td>' +
        '<td>' + actions + '</td>' +
        '</tr>';
    }).join('');
  });
}

function openCreate() {
  createModal = createModal || new bootstrap.Modal(document.getElementById('createModal'));
  document.getElementById('createForm').reset();
  document.querySelector('#createForm [name="date_remboursement"]').value = todayLocal();
  document.getElementById('paymentSearch').value = '';
  document.getElementById('paymentSelect').value = '';
  document.getElementById('paymentResults').style.display = 'none';
  document.getElementById('paymentHint').textContent = 'Chargement des paiements...';
  document.getElementById('debtSelect').innerHTML = '<option value="">Selectionner un paiement d\'abord</option>';
  document.getElementById('refundAmount').value = '';
  paymentDetails = null;
  PAYMENTS = [];
  selectedPaymentLabel = '';
  createModal.show();
  API.get('api/minervale/remboursements/paiements', { id_annee: selectedAnnee() }).then(function (r) {
    if (!r.success) { document.getElementById('paymentHint').textContent = 'Impossible de charger les paiements.'; MV.error(r.message); return; }
    PAYMENTS = r.data || [];
    document.getElementById('paymentHint').textContent = PAYMENTS.length + ' paiement(s) disponible(s). Cliquez dans le champ pour choisir.';
    if (document.activeElement === document.getElementById('paymentSearch')) {
      searchPayments(true);
    }
  });
}

function searchPayments(showAll) {
  var input = document.getElementById('paymentSearch');
  var box = document.getElementById('paymentResults');
  var query = normalizeText(input.value.trim());
  if (selectedPaymentLabel && input.value !== selectedPaymentLabel) {
    document.getElementById('paymentSelect').value = '';
    document.getElementById('debtSelect').innerHTML = '<option value="">Selectionner un paiement d\'abord</option>';
    document.getElementById('refundAmount').value = '';
    document.getElementById('paymentHint').textContent = 'Aucun paiement sélectionné.';
    selectedPaymentLabel = '';
    paymentDetails = null;
  }
  var matches;
  if (showAll || !query) {
    matches = PAYMENTS.slice(0, 100);
  } else {
    matches = PAYMENTS.filter(function (p) {
      return normalizeText(p.fullname).indexOf(query) !== -1
        || normalizeText(p.matricule).indexOf(query) !== -1
        || normalizeText(p.reference).indexOf(query) !== -1
        || normalizeText(p.numero_recu).indexOf(query) !== -1;
    }).slice(0, 30);
  }
  if (!matches.length) {
    box.innerHTML = '<div class="list-group-item text-secondary-light">Aucun paiement trouvé</div>';
    box.style.display = 'block';
    return;
  }
  box.innerHTML = matches.map(function (p) {
    return '<button type="button" class="list-group-item list-group-item-action" onclick="selectPayment(\'' + MV.esc(p.uuid) + '\')">' +
      '<span class="fw-semibold">' + MV.esc(p.fullname || '') + '</span>' +
      '<span class="text-xs text-secondary-light"> ' + MV.esc(p.matricule || '') + ' - ' + MV.money(p.affecte) + ' - ' + MV.esc((p.date_paiement || '').substring(0, 10)) +
      (p.numero_recu ? ' - ' + MV.esc(p.numero_recu) : (p.reference ? ' - ' + MV.esc(p.reference) : '')) + '</span>' +
      '</button>';
  }).join('');
  box.style.display = 'block';
}

function selectPayment(uuid) {
  var paiement = PAYMENTS.filter(function (p) { return p.uuid === uuid; })[0];
  if (!paiement) return;
  selectedPaymentLabel = (paiement.fullname || '') + ' - ' + MV.money(paiement.affecte) + ' - ' + (paiement.date_paiement || '').substring(0, 10);
  document.getElementById('paymentSelect').value = paiement.uuid;
  document.getElementById('paymentSearch').value = selectedPaymentLabel;
  document.getElementById('paymentHint').textContent = 'Paiement sélectionné : ' + selectedPaymentLabel;
  document.getElementById('paymentResults').style.display = 'none';
  loadPayment();
}

function loadPayment() {
  var uuid = document.getElementById('paymentSelect').value;
  var debtSelect = document.getElementById('debtSelect');
  debtSelect.innerHTML = '<option value="">Chargement...</option>';
  paymentDetails = null;
  document.getElementById('refundAmount').value = '';
  if (!uuid) {
    debtSelect.innerHTML = '<option value="">Selectionner un paiement d\'abord</option>';
    return;
  }
  API.get('api/minervale/paiements/' + encodeURIComponent(uuid)).then(function (r) {
    if (!r.success) { MV.error(r.message); return; }
    paymentDetails = r.data;
    var options = '<option value="">Selectionner la dette...</option>';
    (paymentDetails.affectations || []).forEach(function (a) {
      options += '<option value="' + a.id_dette + '" data-montant="' + a.montant + '">Dette #' + a.id_dette + ' - affecte : ' + MV.money(a.montant) + ' - statut : ' + (MV.labels[a.statut_dette] || a.statut_dette) + '</option>';
    });
    debtSelect.innerHTML = options;
    if ((paymentDetails.affectations || []).length === 1) {
      debtSelect.selectedIndex = 1;
      setMaxRefund();
    }
  });
}

function setMaxRefund() {
  var option = document.getElementById('debtSelect').selectedOptions[0];
  if (!option || !option.value) { document.getElementById('refundAmount').value = ''; return; }
  var montant = option.getAttribute('data-montant');
  document.getElementById('refundAmount').max = montant;
  document.getElementById('refundAmount').value = Number(montant).toFixed(2);
  document.getElementById('refundHint').textContent = 'Montant affecte a cette dette : ' + MV.money(montant);
}

function annuler(index) {
  var x = rows[index];
  if (!x) return;
  Swal.fire({ title: 'Annuler le remboursement', input: 'textarea', inputLabel: 'Motif', showCancelButton: true, confirmButtonText: 'Annuler le remboursement', cancelButtonText: 'Fermer' }).then(function (result) {
    if (!result.isConfirmed) return;
    API.post('api/minervale/remboursements/' + encodeURIComponent(x.uuid) + '/annuler', { motif: result.value || '' }).then(function (r) {
      if (r.success) { MV.toast('success', 'Remboursement annule'); loadData(); }
      else MV.error(r.message);
    });
  });
}

document.getElementById('filterForm').addEventListener('submit', function (event) {
  event.preventDefault();
  loadData();
});

document.getElementById('debtSelect').addEventListener('change', setMaxRefund);

document.getElementById('createForm').addEventListener('submit', function (event) {
  event.preventDefault();
  var data = MV.formData(this);
  if (!data.id_paiement || !data.id_dette) { MV.error('Selectionnez un paiement et une dette'); return; }
  API.post('api/minervale/remboursements/create', data).then(function (r) {
    if (!r.success) { MV.error(r.message); return; }
    createModal.hide();
    MV.toast('success', 'Remboursement enregistre');
    loadData();
  });
});

document.addEventListener('click', function (event) {
  var box = document.getElementById('paymentResults');
  if (!box || box.style.display === 'none') return;
  if (event.target.closest('#paymentSearch') || event.target.closest('#paymentResults')) return;
  box.style.display = 'none';
});

loadData();
</script>
