<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Encaissements</h1>
      <div>
        <a href="<?= base_url('Minervale') ?>" class="text-secondary-light hover-text-primary hover-underline">Minervale</a>
        <span class="text-secondary-light"> / Encaissements</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openCreate()">
      <i class="ri-add-line"></i> Nouveau paiement
    </button>
  </div>

  <div class="card radius-12 mb-24">
    <div class="card-body p-20">
      <form id="filterForm" class="row g-3 align-items-end" onsubmit="event.preventDefault(); loadData();">
        <div class="col-xl-2 col-md-4">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Annee</label>
          <select class="form-control form-select" name="id_annee">
            <?php foreach ($options['annees'] as $annee): ?>
              <option value="<?= (int)$annee['id_annee'] ?>" <?= (int)$annee['id_annee'] === (int)$id_annee_active ? 'selected' : '' ?>><?= htmlspecialchars($annee['libelle']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-xl-2 col-md-4">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Statut</label>
          <select class="form-control form-select" name="statut">
            <option value="">Tous</option>
            <option value="solde">Solde</option>
            <option value="partiel">Partiel</option>
            <option value="annule">Annule</option>
          </select>
        </div>
        <div class="col-xl-2 col-md-4">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Du</label>
          <input type="date" class="form-control" name="date_debut">
        </div>
        <div class="col-xl-2 col-md-4">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Au</label>
          <input type="date" class="form-control" name="date_fin">
        </div>
        <div class="col-xl-2 col-md-4">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Recherche</label>
          <input type="text" class="form-control" name="search" placeholder="Nom, matricule ou reference">
        </div>
        <div class="col-xl-2 col-md-4 d-flex gap-2">
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
              <th>Frais</th>
              <th class="text-end">Montant</th>
              <th class="text-end">Affecte</th>
              <th class="text-end">Rembourse</th>
              <th>Mode</th>
              <th>Reference</th>
              <th>Statut</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="dataBody">
            <tr><td colspan="10" class="text-center text-secondary-light py-20">Chargement...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer py-12 px-20 text-secondary-light text-sm" id="countLabel">0 paiement</div>
  </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-header border-bottom py-12 px-20">
        <h6 class="text-lg fw-semibold text-primary-light mb-0">Nouveau paiement</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="createForm">
        <div class="modal-body p-20">
          <div class="row g-3">
            <div class="col-md-8 position-relative">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Etudiant *</label>
              <input type="text" class="form-control" id="studentSearch" placeholder="Tapez un nom ou un matricule..." autocomplete="off" oninput="searchStudents()" onfocus="searchStudents(true)">
              <input type="hidden" name="id_etudiant" id="studentSelect">
              <div id="studentResults" class="list-group position-absolute w-100 shadow-sm" style="display:none;max-height:240px;overflow-y:auto;z-index:1055;"></div>
              <div class="text-xs text-secondary-light mt-4" id="studentHint">Aucun élève sélectionné.</div>
            </div>
            <div class="col-md-4">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Classe</label>
              <div class="form-control bg-neutral-100" id="studentClasse">-</div>
            </div>
            <div class="col-12">
              <div class="table-responsive">
                <table class="table bordered-table mb-0">
                  <thead>
                    <tr>
                      <th>Frais</th>
                      <th class="text-end">Solde</th>
                      <th>Montant a payer</th>
                      <th>Echeance</th>
                    </tr>
                  </thead>
                  <tbody id="debtsBody">
                    <tr><td colspan="4" class="text-center text-secondary-light py-12">Selectionnez un eleve pour afficher ses dettes.</td></tr>
                  </tbody>
                </table>
              </div>
              <div class="text-xs text-secondary-light mt-4" id="debtHint">Saisissez un ou plusieurs montants puis cliquez sur Encaisser.</div>
            </div>
            <div class="col-md-4">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Mode *</label>
              <select class="form-control form-select" name="mode_paiement" required>
                <option value="especes">Especes</option>
                <option value="mobile_money">Mobile money</option>
                <option value="banque">Banque</option>
                <option value="cheque">Cheque</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Date</label>
              <input type="date" class="form-control" name="date_paiement" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-4">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Reference</label>
              <input type="text" class="form-control" name="reference" maxlength="100">
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Payeur</label>
              <input type="text" class="form-control" name="payeur" maxlength="150">
            </div>
            <div class="col-12">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Notes</label>
              <textarea class="form-control" name="notes" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer border-top py-12 px-20">
          <button type="button" class="btn btn-outline-secondary-600" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary-600">Encaisser</button>
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
var debts = [];
var selectedStudentLabel = '';

var ETUDIANTS = <?= json_encode($options['etudiants'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

function filterValues() {
  var data = {};
  new FormData(document.getElementById('filterForm')).forEach(function (value, key) {
    if (value !== '') data[key] = value;
  });
  return data;
}

function loadData() {
  var body = document.getElementById('dataBody');
  body.innerHTML = '<tr><td colspan="10" class="text-center text-secondary-light py-20">Chargement...</td></tr>';
  API.get('api/minervale/paiements', filterValues()).then(function (r) {
    if (!r.success) { body.innerHTML = '<tr><td colspan="10" class="text-center text-danger-main py-20">' + MV.esc(r.message) + '</td></tr>'; return; }
    rows = r.data || [];
    document.getElementById('countLabel').textContent = rows.length + ' paiement(s)';
    if (!rows.length) {
      body.innerHTML = '<tr><td colspan="10" class="text-center text-secondary-light py-20">Aucun paiement</td></tr>';
      return;
    }
    body.innerHTML = rows.map(function (p, index) {
      var actions = '<div class="d-flex justify-content-end gap-1">';
      if (p.recu_uuid) {
        actions += '<a href="' + BASE_URL + 'Recus/imprimer/' + encodeURIComponent(p.recu_uuid) + '" target="_blank" class="btn btn-sm btn-outline-primary-600 radius-4" title="Imprimer le recu"><i class="ri-printer-line"></i></a>';
      }
      if (p.statut !== 'annule') {
        actions += '<button type="button" class="btn btn-sm btn-danger-focus text-danger-main radius-4" title="Annuler" onclick="annuler(' + index + ')"><i class="ri-close-circle-line"></i></button>';
      }
      actions += '</div>';
      return '<tr>' +
        '<td>' + MV.esc((p.date_paiement || '').substring(0, 16)) + '</td>' +
        '<td><div class="fw-semibold">' + MV.esc(p.fullname || '-') + '</div><div class="text-xs text-secondary-light">' + MV.esc(p.matricule || '') + '</div></td>' +
        '<td>' + MV.esc(p.type_libelle || '-') + '</td>' +
        '<td class="text-end fw-semibold">' + MV.money(p.montant) + '</td>' +
        '<td class="text-end">' + MV.money(p.affecte) + '</td>' +
        '<td class="text-end">' + MV.money(p.rembourse) + '</td>' +
        '<td>' + MV.esc(p.mode_paiement || '-') + '</td>' +
        '<td>' + MV.esc(p.reference || '-') + (p.numero_recu ? '<div class="text-xs text-secondary-light">' + MV.esc(p.numero_recu) + '</div>' : '') + '</td>' +
        '<td>' + MV.badge(p.statut) + '</td>' +
        '<td>' + actions + '</td>' +
        '</tr>';
    }).join('');
  });
}

function selectedAnnee() {
  var select = document.querySelector('#filterForm [name="id_annee"]');
  return select && select.value ? select.value : '<?= (int)$id_annee_active ?>';
}

function openCreate() {
  createModal = createModal || new bootstrap.Modal(document.getElementById('createModal'));
  document.getElementById('createForm').reset();
  document.querySelector('#createForm [name="date_paiement"]').value = todayLocal();
  document.getElementById('studentSearch').value = '';
  document.getElementById('studentSelect').value = '';
  document.getElementById('studentClasse').textContent = '-';
  document.getElementById('studentHint').textContent = 'Aucun élève sélectionné.';
  document.getElementById('studentResults').style.display = 'none';
  document.getElementById('debtsBody').innerHTML = '<tr><td colspan="4" class="text-center text-secondary-light py-12">Selectionnez un eleve pour afficher ses dettes.</td></tr>';
  document.getElementById('debtHint').textContent = 'Saisissez un ou plusieurs montants puis cliquez sur Encaisser.';
  debts = [];
  selectedStudentLabel = '';
  createModal.show();
}

function normalizeText(value) {
  return String(value == null ? '' : value).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
}

function todayLocal() {
  var d = new Date();
  return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
}

function searchStudents(showAll) {
  var input = document.getElementById('studentSearch');
  var box = document.getElementById('studentResults');
  var query = normalizeText(input.value.trim());
  if (selectedStudentLabel && input.value !== selectedStudentLabel) {
    document.getElementById('studentSelect').value = '';
    document.getElementById('studentClasse').textContent = '-';
    document.getElementById('studentHint').textContent = 'Aucun élève sélectionné.';
    selectedStudentLabel = '';
    debts = [];
    document.getElementById('debtsBody').innerHTML = '<tr><td colspan="4" class="text-center text-secondary-light py-12">Selectionnez un eleve pour afficher ses dettes.</td></tr>';
    document.getElementById('debtHint').textContent = 'Saisissez un ou plusieurs montants puis cliquez sur Encaisser.';
  }
  var matches;
  if (showAll || !query) {
    matches = ETUDIANTS.slice(0, 100);
  } else {
    matches = ETUDIANTS.filter(function (e) {
      return normalizeText(e.fullname).indexOf(query) !== -1 || normalizeText(e.matricule).indexOf(query) !== -1;
    }).slice(0, 20);
  }
  if (!matches.length) {
    box.innerHTML = '<div class="list-group-item text-secondary-light">Aucun élève trouvé</div>';
    box.style.display = 'block';
    return;
  }
  box.innerHTML = matches.map(function (e) {
    return '<button type="button" class="list-group-item list-group-item-action" onclick="selectStudent(' + Number(e.id_etudiant) + ')">' +
      '<span class="fw-semibold">' + MV.esc(e.fullname || '') + '</span>' +
      '<span class="text-xs text-secondary-light"> ' + MV.esc(e.matricule || '') + (e.classe_libelle ? ' - ' + MV.esc(e.classe_libelle) : '') + '</span>' +
      '</button>';
  }).join('');
  box.style.display = 'block';
}

function selectStudent(id) {
  var etudiant = ETUDIANTS.filter(function (e) { return Number(e.id_etudiant) === Number(id); })[0];
  if (!etudiant) return;
  selectedStudentLabel = (etudiant.fullname || '') + ' (' + (etudiant.matricule || '') + ')';
  document.getElementById('studentSelect').value = etudiant.id_etudiant;
  document.getElementById('studentSearch').value = selectedStudentLabel;
  document.getElementById('studentClasse').textContent = etudiant.classe_libelle || '-';
  document.getElementById('studentHint').textContent = 'Élève sélectionné : ' + selectedStudentLabel;
  document.getElementById('studentResults').style.display = 'none';
  loadStudentDebts();
}

function loadStudentDebts() {
  var idEtudiant = document.getElementById('studentSelect').value;
  var body = document.getElementById('debtsBody');
  if (!idEtudiant) {
    body.innerHTML = '<tr><td colspan="4" class="text-center text-secondary-light py-12">Selectionnez un eleve pour afficher ses dettes.</td></tr>';
    return;
  }
  body.innerHTML = '<tr><td colspan="4" class="text-center text-secondary-light py-12">Chargement...</td></tr>';
  document.getElementById('debtHint').textContent = 'Chargement des dettes...';
  API.get('api/minervale/soldes', { id_etudiant: idEtudiant, id_annee: selectedAnnee() }).then(function (r) {
    if (!r.success) {
      body.innerHTML = '<tr><td colspan="4" class="text-center text-danger-main py-12">' + MV.esc(r.message) + '</td></tr>';
      return;
    }
    debts = (r.data || []).filter(function (d) { return Number(d.solde) > 0.009 && d.situation !== 'annule'; });
    renderDebts();
  });
}

function renderDebts() {
  var body = document.getElementById('debtsBody');
  if (!debts.length) {
    body.innerHTML = '<tr><td colspan="4" class="text-center text-secondary-light py-12">Aucune dette non soldée pour cette année.</td></tr>';
    document.getElementById('debtHint').textContent = 'Aucune dette à encaisser pour cet élève.';
    return;
  }
  body.innerHTML = debts.map(function (d, index) {
    return '<tr>' +
      '<td><div class="fw-semibold">' + MV.esc(d.type_libelle || 'Frais') + '</div><div class="text-xs text-secondary-light">' + MV.esc(d.periode_libelle || '') + '</div></td>' +
      '<td class="text-end">' + MV.money(d.solde) + '</td>' +
      '<td><input type="number" class="form-control form-control-sm debt-amount" data-index="' + index + '" min="0" step="0.01" max="' + Number(d.solde).toFixed(2) + '" placeholder="0"></td>' +
      '<td><select class="form-control form-select form-select-sm debt-echeance" data-index="' + index + '" onfocus="loadRowEcheances(' + index + ')"><option value="">Aucune</option></select></td>' +
      '</tr>';
  }).join('');
  document.getElementById('debtHint').textContent = debts.length + ' dette(s) non soldée(s). Saisissez un ou plusieurs montants.';
}

function loadRowEcheances(index) {
  var debt = debts[index];
  var select = document.querySelector('.debt-echeance[data-index="' + index + '"]');
  if (!debt || !select || select.getAttribute('data-loaded') === '1') return;
  select.setAttribute('data-loaded', '1');
  select.innerHTML = '<option value="">Chargement...</option>';
  API.get('api/echeances', { id_etudiant: debt.id_etudiant, id_frais: debt.id_frais }).then(function (r) {
    var list = r.success ? (r.data || []).filter(function (e) {
      return e.statut !== 'paye' && e.statut !== 'annule' && Number(e.montant) > 0;
    }) : [];
    select.innerHTML = '<option value="">Aucune</option>' + list.map(function (e) {
      return '<option value="' + Number(e.id_echeance) + '">' + MV.esc((e.date_echeance || '') + ' - ' + MV.money(e.montant)) + '</option>';
    }).join('');
    if (list.length === 1) select.selectedIndex = 1;
  });
}

function annuler(index) {
  var p = rows[index];
  if (!p) return;
  Swal.fire({ title: 'Annuler le paiement', input: 'textarea', inputLabel: 'Motif', showCancelButton: true, confirmButtonText: 'Annuler le paiement', cancelButtonText: 'Fermer' }).then(function (result) {
    if (!result.isConfirmed) return;
    API.post('api/minervale/paiements/' + encodeURIComponent(p.uuid) + '/annuler', { motif: result.value || '' }).then(function (r) {
      if (r.success) { MV.toast('success', 'Paiement annule'); loadData(); }
      else MV.error(r.message);
    });
  });
}

document.getElementById('filterForm').addEventListener('submit', function (event) {
  event.preventDefault();
  loadData();
});

document.getElementById('createForm').addEventListener('submit', function (event) {
  event.preventDefault();
  payAll(this);
});

function payAll(form) {
  var idEtudiant = document.getElementById('studentSelect').value;
  if (!idEtudiant) { MV.error('Sélectionnez un élève.'); return; }
  var common = MV.formData(form);
  var selections = [];
  debts.forEach(function (debt, index) {
    var input = document.querySelector('.debt-amount[data-index="' + index + '"]');
    var montant = input ? Number(input.value) : 0;
    if (!(montant > 0)) return;
    if (montant > Number(debt.solde) + 0.009) {
      selections.push({ error: 'Le montant saisi pour ' + (debt.type_libelle || 'ce frais') + ' dépasse le solde de ' + MV.money(debt.solde) + '.' });
      return;
    }
    var echeance = document.querySelector('.debt-echeance[data-index="' + index + '"]');
    selections.push({
      id_frais: debt.id_frais,
      montant: montant,
      id_echeance: echeance ? echeance.value : '',
      label: debt.type_libelle || 'Frais'
    });
  });
  var validationErrors = selections.filter(function (s) { return s.error; }).map(function (s) { return s.error; });
  var paiements = selections.filter(function (s) { return !s.error; });
  if (validationErrors.length) { MV.error(validationErrors.join(' ')); return; }
  if (!paiements.length) { MV.error('Saisissez au moins un montant à payer.'); return; }

  var base = {
    id_etudiant: idEtudiant,
    mode_paiement: common.mode_paiement,
    date_paiement: common.date_paiement,
    reference: common.reference,
    payeur: common.payeur,
    notes: common.notes
  };
  var errors = [];
  var numeros = [];
  var chain = Promise.resolve();
  paiements.forEach(function (p) {
    chain = chain.then(function () {
      var payload = Object.assign({}, base, {
        id_frais: p.id_frais,
        montant: p.montant,
        id_echeance: p.id_echeance
      });
      return API.post('api/minervale/paiements/create', payload).then(function (r) {
        if (r.success) {
          if (r.data && r.data.numero_recu) numeros.push(r.data.numero_recu);
        } else {
          errors.push(p.label + ' : ' + r.message);
        }
      });
    });
  });
  chain.then(function () {
    if (numeros.length) {
      MV.toast('success', numeros.length + ' paiement(s) enregistre(s) - recu ' + numeros.join(', '));
      loadData();
      loadStudentDebts();
    }
    if (errors.length) MV.error(errors.join(' '));
  });
}

document.addEventListener('click', function (event) {
  var box = document.getElementById('studentResults');
  if (!box || box.style.display === 'none') return;
  if (event.target.closest('#studentSearch') || event.target.closest('#studentResults')) return;
  box.style.display = 'none';
});

loadData();
</script>
