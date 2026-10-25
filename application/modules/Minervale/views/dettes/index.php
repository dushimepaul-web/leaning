<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Dettes</h1>
      <div>
        <a href="<?= base_url('Minervale') ?>" class="text-secondary-light hover-text-primary hover-underline">Minervale</a>
        <span class="text-secondary-light"> / Dettes</span>
      </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <button type="button" class="btn btn-outline-primary-600 d-flex align-items-center gap-6" onclick="recalculer()">
        <i class="ri-refresh-line"></i> Recalculer les statuts
      </button>
      <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openGenerer()">
        <i class="ri-add-line"></i> Generer les dettes
      </button>
    </div>
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
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Periode</label>
          <select class="form-control form-select" name="id_periode">
            <option value="">Toutes</option>
            <?php foreach ($options['periodes_toutes'] as $periode): ?>
              <option value="<?= (int)$periode['id_periode'] ?>" data-annee="<?= (int)$periode['id_annee'] ?>"><?= htmlspecialchars(($periode['annee_libelle'] ? $periode['annee_libelle'] . ' - ' : '') . $periode['libelle']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-xl-2 col-md-4">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Classe</label>
          <select class="form-control form-select" name="id_classe">
            <option value="">Toutes</option>
            <?php foreach ($options['classes'] as $classe): ?>
              <option value="<?= (int)$classe['id_classe'] ?>"><?= htmlspecialchars($classe['libelle']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-xl-2 col-md-4">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Type de frais</label>
          <select class="form-control form-select" name="id_type_frais">
            <option value="">Tous</option>
            <?php foreach ($options['types_frais'] as $type): ?>
              <option value="<?= (int)$type['id_type_frais'] ?>"><?= htmlspecialchars($type['libelle']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-xl-2 col-md-4">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Statut</label>
          <select class="form-control form-select" name="statut">
            <option value="">Tous</option>
            <option value="impaye">Non paye</option>
            <option value="partiel">Partiel</option>
            <option value="paye">Paye</option>
            <option value="exonere_partiel">Exonere partiellement</option>
            <option value="exonere_total">Exonere totalement</option>
            <option value="trop_percu">Trop-percu</option>
            <option value="annule">Annule</option>
          </select>
        </div>
        <div class="col-xl-2 col-md-4">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Recherche</label>
          <input type="text" class="form-control" name="search" placeholder="Nom ou matricule">
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary-600"><i class="ri-filter-3-line me-1"></i> Filtrer</button>
          <button type="button" class="btn btn-outline-secondary-600" onclick="resetFilters()">Reinitialiser</button>
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
              <th>Classe</th>
              <th>Frais</th>
              <th class="text-end">Initial</th>
              <th class="text-end">Exoneration</th>
              <th class="text-end">Net du</th>
              <th class="text-end">Paye net</th>
              <th class="text-end">Solde</th>
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
    <div class="card-footer py-12 px-20 d-flex justify-content-between text-secondary-light text-sm">
      <span id="countLabel">0 dette</span>
      <span id="totalLabel"></span>
    </div>
  </div>
</div>

<div class="modal fade" id="genererModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-header border-bottom py-12 px-20">
        <h6 class="text-lg fw-semibold text-primary-light mb-0">Generer les dettes</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="genererForm">
        <div class="modal-body p-20">
          <div class="row g-3">
            <div class="col-12">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Frais a generer</label>
              <select class="form-control form-select" name="id_frais" id="genererFrais">
                <option value="">Selection manuelle (tous les frais)</option>
                <?php if (!empty($options['frais'])): ?>
                  <?php foreach ($options['frais'] as $frais): ?>
                    <option value="<?= (int)$frais['id_frais'] ?>"
                      data-annee="<?= (int)$frais['id_annee'] ?>"
                      data-periode="<?= (int)$frais['id_periode'] ?>"
                      data-classe="<?= !empty($frais['id_classe']) ? (int)$frais['id_classe'] : '' ?>"
                      data-type="<?= (int)$frais['id_type_frais'] ?>">
                      <?= htmlspecialchars(($frais['annee_libelle'] ? $frais['annee_libelle'] . ' - ' : '') . ($frais['periode_libelle'] ?: '-') . ' - ' . ($frais['classe_libelle'] ?: 'Toutes les classes') . ' - ' . ($frais['type_libelle'] ?: '-') . ' - ' . number_format((float)$frais['montant'], 0, ',', ' ')) ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
              <div class="text-xs text-secondary-light mt-1">La selection d'un frais reprend automatiquement son annee, son trimestre et sa classe.</div>
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Annee *</label>
              <select class="form-control form-select" name="id_annee" required>
                <?php foreach ($options['annees'] as $annee): ?>
                  <option value="<?= (int)$annee['id_annee'] ?>" <?= (int)$annee['id_annee'] === (int)$id_annee_active ? 'selected' : '' ?>><?= htmlspecialchars($annee['libelle']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Periode *</label>
              <select class="form-control form-select" name="id_periode" required>
                <?php foreach ($options['periodes_toutes'] as $periode): ?>
                  <option value="<?= (int)$periode['id_periode'] ?>" data-annee="<?= (int)$periode['id_annee'] ?>" <?= (int)$periode['id_periode'] === (int)$id_periode_active ? 'selected' : '' ?>><?= htmlspecialchars(($periode['annee_libelle'] ? $periode['annee_libelle'] . ' - ' : '') . $periode['libelle']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Classe</label>
              <select class="form-control form-select" name="id_classe">
                <option value="">Toutes les classes</option>
                <?php foreach ($options['classes'] as $classe): ?>
                  <option value="<?= (int)$classe['id_classe'] ?>"><?= htmlspecialchars($classe['libelle']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Type de frais</label>
              <select class="form-control form-select" name="id_type_frais">
                <option value="">Tous les types</option>
                <?php foreach ($options['types_frais'] as $type): ?>
                  <option value="<?= (int)$type['id_type_frais'] ?>"><?= htmlspecialchars($type['libelle']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Source</label>
              <select class="form-control form-select" name="source">
                <option value="inscription">Inscription</option>
                <option value="echeancier">Echeancier</option>
                <option value="manuelle">Manuelle</option>
              </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="mettre_a_jour" id="mettreAJour" value="1">
                <label class="form-check-label" for="mettreAJour">Mettre a jour les dettes sans paiement</label>
              </div>
            </div>
          </div>
          <div class="alert alert-info radius-8 mt-16 mb-0 text-sm">
            Une dette est creee par etudiant et par frais. Les dettes deja payees ou exonerees ne sont pas modifiees.
            Lorsqu'un frais global et un frais specifique a une classe existent pour le meme type, le frais de la classe est prioritaire.
          </div>
        </div>
        <div class="modal-footer border-top py-12 px-20">
          <button type="button" class="btn btn-outline-secondary-600" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary-600">Generer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="paiementModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-header border-bottom py-12 px-20">
        <h6 class="text-lg fw-semibold text-primary-light mb-0">Encaisser un paiement</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="paiementForm">
        <input type="hidden" name="id_etudiant" id="payEtudiant">
        <input type="hidden" name="id_frais" id="payFrais">
        <div class="modal-body p-20">
          <div class="p-12 radius-8 bg-neutral-100 mb-16">
            <div class="d-flex justify-content-between mb-4"><span class="text-secondary-light">Etudiant</span><span class="fw-semibold" id="payEtudiantLabel">-</span></div>
            <div class="d-flex justify-content-between mb-4"><span class="text-secondary-light">Frais</span><span class="fw-semibold" id="payFraisLabel">-</span></div>
            <div class="d-flex justify-content-between"><span class="text-secondary-light">Solde restant</span><span class="fw-semibold text-danger-main" id="paySoldeLabel">-</span></div>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Echeance (optionnel)</label>
              <select class="form-control form-select" name="id_echeance" id="payEcheance">
                <option value="">Aucune echeance liee</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Montant *</label>
              <input type="number" step="0.01" min="0.01" class="form-control" name="montant" id="payMontant" required>
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Mode *</label>
              <select class="form-control form-select" name="mode_paiement" required>
                <option value="especes">Especes</option>
                <option value="mobile_money">Mobile money</option>
                <option value="banque">Banque</option>
                <option value="cheque">Cheque</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Date</label>
              <input type="date" class="form-control" name="date_paiement" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Reference</label>
              <input type="text" class="form-control" name="reference" maxlength="100">
            </div>
            <div class="col-12">
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

<div class="modal fade" id="annulerModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-header border-bottom py-12 px-20">
        <h6 class="text-lg fw-semibold text-primary-light mb-0">Annuler la dette</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="annulerForm">
        <div class="modal-body p-20">
          <p class="text-secondary-light">Cette action est possible uniquement si aucun paiement n'a ete affecte a la dette.</p>
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Motif</label>
          <textarea class="form-control" name="motif" rows="3" maxlength="255" required></textarea>
        </div>
        <div class="modal-footer border-top py-12 px-20">
          <button type="button" class="btn btn-outline-secondary-600" data-bs-dismiss="modal">Fermer</button>
          <button type="submit" class="btn btn-danger-600">Annuler la dette</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<?php include __DIR__.'/../includes/common.php'; ?>
<script>
var OPTIONS = <?= json_encode($options, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
var rows = [];
var currentUuid = null;
var genererModal = null;
var paiementModal = null;
var annulerModal = null;

function filterValues() {
  var data = {};
  new FormData(document.getElementById('filterForm')).forEach(function (value, key) {
    if (value !== '') data[key] = value;
  });
  return data;
}

function queryString(data) {
  var params = new URLSearchParams();
  Object.keys(data).forEach(function (key) { params.append(key, data[key]); });
  return params.toString();
}

function todayLocal() {
  var d = new Date();
  return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
}

function loadData() {
  var body = document.getElementById('dataBody');
  body.innerHTML = '<tr><td colspan="10" class="text-center text-secondary-light py-20">Chargement...</td></tr>';
  API.get('api/minervale/soldes', filterValues()).then(function (r) {
    if (!r.success) { body.innerHTML = '<tr><td colspan="10" class="text-center text-danger-main py-20">' + MV.esc(r.message) + '</td></tr>'; return; }
    rows = r.data || [];
    renderRows();
  });
}

function renderRows() {
  var body = document.getElementById('dataBody');
  var total = 0;
  if (!rows.length) {
    body.innerHTML = '<tr><td colspan="10" class="text-center text-secondary-light py-20">Aucune dette trouvee</td></tr>';
  } else {
    body.innerHTML = rows.map(function (d, index) {
      total += Number(d.solde || 0);
      var actions = '<div class="d-flex justify-content-end gap-1">';
      if (Number(d.solde) > 0.009 && d.situation !== 'annule') {
        actions += '<button type="button" class="btn btn-sm btn-success-focus text-success-main radius-4" title="Encaisser" onclick="openPaiement(' + index + ')"><i class="ri-cash-line"></i></button>';
      }
      if (d.situation !== 'annule') {
        actions += '<button type="button" class="btn btn-sm btn-danger-focus text-danger-main radius-4" title="Annuler" onclick="openAnnuler(' + index + ')"><i class="ri-close-circle-line"></i></button>';
      }
      actions += '</div>';
      return '<tr>' +
        '<td><div class="fw-semibold">' + MV.esc(d.fullname || '-') + '</div><div class="text-xs text-secondary-light">' + MV.esc(d.matricule || '') + '</div></td>' +
        '<td>' + MV.esc(d.classe_libelle || '-') + '</td>' +
        '<td><div>' + MV.esc(d.type_libelle || '-') + '</div><div class="text-xs text-secondary-light">' + MV.esc(d.periode_libelle || '') + '</div></td>' +
        '<td class="text-end">' + MV.money(d.montant_initial) + '</td>' +
        '<td class="text-end">' + MV.money(d.exoneration) + '</td>' +
        '<td class="text-end">' + MV.money(d.net_du) + '</td>' +
        '<td class="text-end">' + MV.money(d.paye_net) + '</td>' +
        '<td class="text-end fw-semibold ' + (Number(d.solde) > 0 ? 'text-danger-main' : 'text-success-main') + '">' + MV.money(d.solde) + '</td>' +
        '<td>' + MV.badge(d.situation) + '</td>' +
        '<td>' + actions + '</td>' +
        '</tr>';
    }).join('');
  }
  document.getElementById('countLabel').textContent = rows.length + ' dette(s)';
  document.getElementById('totalLabel').textContent = 'Solde affiche : ' + MV.money(total);
}

function resetFilters() {
  document.getElementById('filterForm').reset();
  MV.filterPeriodes(document.querySelector('#filterForm [name="id_annee"]'), document.querySelector('#filterForm [name="id_periode"]'));
  loadData();
}

function openGenerer() {
  genererModal = genererModal || new bootstrap.Modal(document.getElementById('genererModal'));
  var selectFrais = document.getElementById('genererFrais');
  if (selectFrais) {
    selectFrais.value = '';
    syncGenererFrais();
    genererAnnee.value = OPTIONS.id_annee_active;
    MV.filterPeriodes(genererAnnee, genererPeriode);
    genererPeriode.value = OPTIONS.id_periode_active;
    document.querySelector('#genererForm [name="id_classe"]').value = '';
    document.querySelector('#genererForm [name="id_type_frais"]').value = '';
  }
  genererModal.show();
}

function openPaiement(index) {
  var d = rows[index];
  if (!d) return;
  document.getElementById('payEtudiant').value = d.id_etudiant;
  document.getElementById('payFrais').value = d.id_frais;
  document.getElementById('payEtudiantLabel').textContent = (d.fullname || '') + ' (' + (d.matricule || '') + ')';
  document.getElementById('payFraisLabel').textContent = d.type_libelle || '-';
  document.getElementById('paySoldeLabel').textContent = MV.money(d.solde);
  document.getElementById('payMontant').value = Number(d.solde).toFixed(2);
  document.getElementById('payMontant').max = Number(d.solde).toFixed(2);
  var echeanceSelect = document.getElementById('payEcheance');
  echeanceSelect.innerHTML = '<option value="">Chargement...</option>';
  API.get('api/echeances', { id_etudiant: d.id_etudiant, id_frais: d.id_frais }).then(function (r) {
    var list = r.success ? (r.data || []).filter(function (e) {
      return e.statut !== 'paye' && e.statut !== 'annule' && Number(e.montant) > 0;
    }) : [];
    if (!list.length) {
      echeanceSelect.innerHTML = '<option value="">Aucune echeance ouverte</option>';
      return;
    }
    echeanceSelect.innerHTML = '<option value="">Aucune echeance liee</option>' + list.map(function (e) {
      return '<option value="' + Number(e.id_echeance) + '">' + MV.esc((e.date_echeance || '') + ' - ' + MV.money(e.montant)) + '</option>';
    }).join('');
    if (list.length === 1) echeanceSelect.selectedIndex = 1;
  });
  document.querySelector('#paiementForm [name="date_paiement"]').value = todayLocal();
  paiementModal = paiementModal || new bootstrap.Modal(document.getElementById('paiementModal'));
  paiementModal.show();
}

function openAnnuler(index) {
  var d = rows[index];
  if (!d) return;
  currentUuid = d.uuid;
  document.getElementById('annulerForm').reset();
  annulerModal = annulerModal || new bootstrap.Modal(document.getElementById('annulerModal'));
  annulerModal.show();
}

function recalculer() {
  var data = filterValues();
  MV.confirm('Recalculer les statuts ?', 'Les statuts des dettes seront synchronises avec les paiements, exonérations et remboursements.').then(function (result) {
    if (!result.isConfirmed) return;
    API.post('api/minervale/dettes/recalculer?' + queryString(data), {}).then(function (r) {
      if (r.success) { MV.toast('success', r.data.updated + ' dette(s) mise(s) a jour'); loadData(); }
      else MV.error(r.message);
    });
  });
}

document.getElementById('filterForm').addEventListener('submit', function (event) {
  event.preventDefault();
  loadData();
});

document.getElementById('genererForm').addEventListener('submit', function (event) {
  event.preventDefault();
  var data = MV.formData(this);
  data.mettre_a_jour = this.querySelector('[name="mettre_a_jour"]').checked ? 1 : 0;
  API.post('api/minervale/dettes/generer', data).then(function (r) {
    if (!r.success) { MV.error(r.message); return; }
    var stats = r.data || {};
    if (!stats.frais) {
      MV.error('Aucun frais actif pour cette annee et ce trimestre. Creez les frais avant de generer les dettes.');
      return;
    }
    if (!stats.etudiants) {
      MV.error('Aucun etudiant inscrit pour la classe ou l\'annee selectionnee.');
      return;
    }
    genererModal.hide();
    MV.toast('success', stats.created + ' creee(s), ' + stats.updated + ' mise(s) a jour, ' + stats.skipped + ' ignoree(s)');
    loadData();
  });
});

document.getElementById('paiementForm').addEventListener('submit', function (event) {
  event.preventDefault();
  var data = MV.formData(this);
  API.post('api/minervale/paiements/create', data).then(function (r) {
    if (!r.success) { MV.error(r.message); return; }
    paiementModal.hide();
    MV.toast('success', 'Paiement enregistre');
    loadData();
  });
});

document.getElementById('annulerForm').addEventListener('submit', function (event) {
  event.preventDefault();
  var data = MV.formData(this);
  API.post('api/minervale/dettes/' + encodeURIComponent(currentUuid) + '/annuler', data).then(function (r) {
    if (!r.success) { MV.error(r.message); return; }
    annulerModal.hide();
    MV.toast('success', 'Dette annulee');
    loadData();
  });
});

var filterAnnee = document.querySelector('#filterForm [name="id_annee"]');
var filterPeriode = document.querySelector('#filterForm [name="id_periode"]');
filterAnnee.addEventListener('change', function () { MV.filterPeriodes(filterAnnee, filterPeriode); });
MV.filterPeriodes(filterAnnee, filterPeriode);

var genererAnnee = document.querySelector('#genererForm [name="id_annee"]');
var genererPeriode = document.querySelector('#genererForm [name="id_periode"]');
genererAnnee.addEventListener('change', function () { MV.filterPeriodes(genererAnnee, genererPeriode); });
MV.filterPeriodes(genererAnnee, genererPeriode);

var genererFrais = document.getElementById('genererFrais');
function syncGenererFrais() {
  var locked = !!(genererFrais && genererFrais.value !== '');
  ['id_annee', 'id_periode', 'id_classe', 'id_type_frais'].forEach(function (name) {
    var field = document.querySelector('#genererForm [name="' + name + '"]');
    if (field) field.disabled = locked;
  });
  if (!locked) return;
  var option = genererFrais.options[genererFrais.selectedIndex];
  genererAnnee.value = option.getAttribute('data-annee');
  MV.filterPeriodes(genererAnnee, genererPeriode);
  genererPeriode.value = option.getAttribute('data-periode');
  document.querySelector('#genererForm [name="id_classe"]').value = option.getAttribute('data-classe');
  document.querySelector('#genererForm [name="id_type_frais"]').value = option.getAttribute('data-type');
}
if (genererFrais) genererFrais.addEventListener('change', syncGenererFrais);

loadData();
</script>
