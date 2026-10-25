<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Exonerations</h1>
      <div>
        <a href="<?= base_url('Minervale') ?>" class="text-secondary-light hover-text-primary hover-underline">Minervale</a>
        <span class="text-secondary-light"> / Exonerations</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openCreate()">
      <i class="ri-add-line"></i> Nouvelle demande
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
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Statut de la demande</label>
          <select class="form-control form-select" name="statut_demande">
            <option value="">Tous</option>
            <option value="en_attente">En attente</option>
            <option value="approuvee">Approuvee</option>
            <option value="refusee">Refusee</option>
          </select>
        </div>
        <div class="col-xl-3 col-md-4">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Recherche</label>
          <input type="text" class="form-control" name="search" placeholder="Nom ou matricule">
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
              <th>Etudiant</th>
              <th>Frais</th>
              <th>Mode</th>
              <th class="text-end">Valeur</th>
              <th>Motif</th>
              <th>Demande</th>
              <th>Decision</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody id="dataBody">
            <tr><td colspan="8" class="text-center text-secondary-light py-20">Chargement...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer py-12 px-20 text-secondary-light text-sm" id="countLabel">0 demande</div>
  </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-header border-bottom py-12 px-20">
        <h6 class="text-lg fw-semibold text-primary-light mb-0">Nouvelle demande d'exoneration</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="createForm">
        <div class="modal-body p-20">
          <div class="form-check mb-16">
            <input class="form-check-input" type="checkbox" id="collectiveToggle" onchange="toggleCollective()">
            <label class="form-check-label" for="collectiveToggle">Demande collective (plusieurs etudiants)</label>
          </div>
          <div class="row g-3">
            <div class="col-12" id="singleStudentBox">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Etudiant *</label>
              <select class="form-control form-select" name="id_etudiant" id="singleStudent">
                <option value="">Selectionner...</option>
                <?php foreach ($options['etudiants'] as $etudiant): ?>
                  <option value="<?= (int)$etudiant['id_etudiant'] ?>"><?= htmlspecialchars($etudiant['fullname'] . ' (' . $etudiant['matricule'] . ')') ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12 d-none" id="multiStudentBox">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Etudiants * <span class="text-secondary-light">(Ctrl pour plusieurs)</span></label>
              <select class="form-control form-select" id="multiStudents" multiple size="8">
                <?php foreach ($options['etudiants'] as $etudiant): ?>
                  <option value="<?= (int)$etudiant['id_etudiant'] ?>"><?= htmlspecialchars($etudiant['fullname'] . ' (' . $etudiant['matricule'] . ')') ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Frais</label>
              <select class="form-control form-select" name="id_frais">
                <option value="">Tous les frais (annee)</option>
                <?php foreach ($options['frais'] as $frais): ?>
                  <option value="<?= (int)$frais['id_frais'] ?>"><?= htmlspecialchars(($frais['classe_libelle'] ? $frais['classe_libelle'] . ' - ' : '') . $frais['type_libelle'] . ' - ' . number_format($frais['montant'], 0, ',', ' ')) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Mode *</label>
              <select class="form-control form-select" name="mode" id="modeSelect" onchange="updateValueHint()">
                <option value="montant">Montant</option>
                <option value="pourcentage">Pourcentage</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Valeur *</label>
              <input type="number" step="0.01" min="0.01" class="form-control" name="valeur" id="valeurInput" required>
              <small class="text-secondary-light" id="valueHint">Montant en devise</small>
            </div>
            <div class="col-12">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Motif *</label>
              <textarea class="form-control" name="motif" rows="3" maxlength="255" required></textarea>
            </div>
          </div>
          <div class="alert alert-info radius-8 mt-16 mb-0 text-sm">
            La demande reste en attente jusqu'a approbation. Seules les demandes approuvees sont deduites des soldes.
          </div>
        </div>
        <div class="modal-footer border-top py-12 px-20">
          <button type="button" class="btn btn-outline-secondary-600" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary-600">Envoyer la demande</button>
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

function filterValues() {
  var data = {};
  new FormData(document.getElementById('filterForm')).forEach(function (value, key) {
    if (value !== '') data[key] = value;
  });
  return data;
}

function loadData() {
  var body = document.getElementById('dataBody');
  body.innerHTML = '<tr><td colspan="8" class="text-center text-secondary-light py-20">Chargement...</td></tr>';
  API.get('api/minervale/exonerations', filterValues()).then(function (r) {
    if (!r.success) { body.innerHTML = '<tr><td colspan="8" class="text-center text-danger-main py-20">' + MV.esc(r.message) + '</td></tr>'; return; }
    rows = r.data || [];
    document.getElementById('countLabel').textContent = rows.length + ' demande(s)';
    if (!rows.length) {
      body.innerHTML = '<tr><td colspan="8" class="text-center text-secondary-light py-20">Aucune demande</td></tr>';
      return;
    }
    body.innerHTML = rows.map(function (x, index) {
      var actions = '<div class="d-flex justify-content-end gap-1">';
      if (x.statut_demande === 'en_attente') {
        actions += '<button type="button" class="btn btn-sm btn-success-focus text-success-main radius-4" title="Approuver" onclick="approuver(' + index + ')"><i class="ri-check-line"></i></button>';
        actions += '<button type="button" class="btn btn-sm btn-danger-focus text-danger-main radius-4" title="Refuser" onclick="refuser(' + index + ')"><i class="ri-close-line"></i></button>';
      }
      if (x.statut !== 'annulee') {
        actions += '<button type="button" class="btn btn-sm btn-neutral-200 text-secondary-light radius-4" title="Annuler" onclick="annuler(' + index + ')"><i class="ri-arrow-go-back-line"></i></button>';
      }
      actions += '</div>';
      var valeur = x.mode === 'pourcentage' ? MV.number(x.valeur) + ' %' : MV.money(x.valeur);
      return '<tr>' +
        '<td><div class="fw-semibold">' + MV.esc(x.fullname || '-') + '</div><div class="text-xs text-secondary-light">' + MV.esc(x.matricule || '') + '</div></td>' +
        '<td><div>' + MV.esc(x.type_libelle || 'Tous les frais') + '</div><div class="text-xs text-secondary-light">' + (x.frais_montant ? MV.money(x.frais_montant) : '') + '</div></td>' +
        '<td>' + (x.mode === 'pourcentage' ? 'Pourcentage' : 'Montant') + '</td>' +
        '<td class="text-end fw-semibold">' + valeur + '</td>' +
        '<td><span title="' + MV.esc(x.motif || '') + '">' + MV.esc((x.motif || '-').substring(0, 40)) + (x.motif && x.motif.length > 40 ? '...' : '') + '</span></td>' +
        '<td><div class="text-xs">' + MV.esc(x.date_demande || '-') + '</div><div class="text-xs text-secondary-light">' + MV.esc(x.demandeur_nom || '') + '</div></td>' +
        '<td>' + MV.badge(x.statut_demande) + '<div class="text-xs text-secondary-light mt-4">' + MV.esc(x.approbateur_nom || '') + '</div></td>' +
        '<td>' + actions + '</td>' +
        '</tr>';
    }).join('');
  });
}

function openCreate() {
  createModal = createModal || new bootstrap.Modal(document.getElementById('createModal'));
  document.getElementById('createForm').reset();
  document.getElementById('collectiveToggle').checked = false;
  toggleCollective();
  updateValueHint();
  createModal.show();
}

function toggleCollective() {
  var collective = document.getElementById('collectiveToggle').checked;
  document.getElementById('singleStudentBox').classList.toggle('d-none', collective);
  document.getElementById('multiStudentBox').classList.toggle('d-none', !collective);
}

function updateValueHint() {
  var mode = document.getElementById('modeSelect').value;
  document.getElementById('valueHint').textContent = mode === 'pourcentage' ? 'Pourcentage entre 1 et 100' : 'Montant en devise';
  document.getElementById('valeurInput').max = mode === 'pourcentage' ? 100 : '';
}

function approuver(index) {
  var x = rows[index];
  if (!x) return;
  MV.confirm('Approuver la demande ?', 'L\'exoneration sera deduite du solde de l\'etudiant.').then(function (result) {
    if (!result.isConfirmed) return;
    API.post('api/minervale/exonerations/' + encodeURIComponent(x.uuid) + '/approuver', {}).then(function (r) {
      if (r.success) { MV.toast('success', 'Demande approuvee'); loadData(); }
      else MV.error(r.message);
    });
  });
}

function refuser(index) {
  var x = rows[index];
  if (!x) return;
  Swal.fire({ title: 'Refuser la demande', input: 'textarea', inputLabel: 'Motif du refus', inputPlaceholder: 'Precisez le motif...', showCancelButton: true, confirmButtonText: 'Refuser', cancelButtonText: 'Annuler', inputValidator: function (value) { return value ? null : 'Le motif est obligatoire'; } }).then(function (result) {
    if (!result.isConfirmed) return;
    API.post('api/minervale/exonerations/' + encodeURIComponent(x.uuid) + '/refuser', { motif: result.value }).then(function (r) {
      if (r.success) { MV.toast('success', 'Demande refusee'); loadData(); }
      else MV.error(r.message);
    });
  });
}

function annuler(index) {
  var x = rows[index];
  if (!x) return;
  Swal.fire({ title: 'Annuler l\'exoneration', input: 'textarea', inputLabel: 'Motif', showCancelButton: true, confirmButtonText: 'Annuler l\'exoneration', cancelButtonText: 'Fermer' }).then(function (result) {
    if (!result.isConfirmed) return;
    API.post('api/minervale/exonerations/' + encodeURIComponent(x.uuid) + '/annuler', { motif: result.value || '' }).then(function (r) {
      if (r.success) { MV.toast('success', 'Exoneration annulee'); loadData(); }
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
  var form = this;
  var collective = document.getElementById('collectiveToggle').checked;
  var data = MV.formData(form);
  if (collective) {
    data.id_etudiants = Array.prototype.slice.call(document.getElementById('multiStudents').selectedOptions).map(function (o) { return o.value; });
    if (!data.id_etudiants.length) { MV.error('Selectionnez au moins un etudiant'); return; }
    delete data.id_etudiant;
    API.post('api/minervale/exonerations/collective', data).then(function (r) {
      if (!r.success) { MV.error(r.message); return; }
      createModal.hide();
      MV.toast('success', r.data.created + ' demande(s) creee(s)');
      loadData();
    });
    return;
  }
  if (!data.id_etudiant) { MV.error('Selectionnez un etudiant'); return; }
  API.post('api/minervale/exonerations/create', data).then(function (r) {
    if (!r.success) { MV.error(r.message); return; }
    createModal.hide();
    MV.toast('success', 'Demande envoyee');
    loadData();
  });
});

loadData();
</script>
