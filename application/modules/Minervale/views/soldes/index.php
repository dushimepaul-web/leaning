<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Soldes et impayes</h1>
      <div>
        <a href="<?= base_url('Minervale') ?>" class="text-secondary-light hover-text-primary hover-underline">Minervale</a>
        <span class="text-secondary-light"> / Soldes</span>
      </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <button type="button" class="btn btn-outline-primary-600 d-flex align-items-center gap-6" onclick="recalculer()">
        <i class="ri-refresh-line"></i> Recalculer les statuts
      </button>
      <a href="<?= base_url('Minervale/Dettes') ?>" class="btn btn-primary-600 d-flex align-items-center gap-6">
        <i class="ri-list-check-3"></i> Voir les dettes
      </a>
    </div>
  </div>

  <div class="row row-cols-xxl-5 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <div class="col">
      <div class="card radius-12 h-100"><div class="card-body p-20"><p class="text-secondary-light mb-4">Net a recouvrer</p><h5 class="mb-0" id="sumNet">-</h5></div></div>
    </div>
    <div class="col">
      <div class="card radius-12 h-100"><div class="card-body p-20"><p class="text-secondary-light mb-4">Exonerations</p><h5 class="mb-0" id="sumExo">-</h5></div></div>
    </div>
    <div class="col">
      <div class="card radius-12 h-100"><div class="card-body p-20"><p class="text-secondary-light mb-4">Encaisse net</p><h5 class="mb-0 text-success-main" id="sumPaye">-</h5></div></div>
    </div>
    <div class="col">
      <div class="card radius-12 h-100"><div class="card-body p-20"><p class="text-secondary-light mb-4">Solde restant</p><h5 class="mb-0 text-danger-main" id="sumSolde">-</h5></div></div>
    </div>
    <div class="col">
      <div class="card radius-12 h-100"><div class="card-body p-20"><p class="text-secondary-light mb-4">Taux de recouvrement</p><h5 class="mb-0" id="sumTaux">-</h5></div></div>
    </div>
  </div>

  <div class="card radius-12 mb-24">
    <div class="card-body p-20">
      <form id="filterForm" class="row g-3 align-items-end" onsubmit="event.preventDefault(); loadAll();">
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
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-4">Situation</label>
          <select class="form-control form-select" name="statut">
            <option value="">Toutes</option>
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

  <div class="row gy-4">
    <div class="col-xl-5">
      <div class="card radius-12 h-100">
        <div class="card-header border-bottom py-16 px-20"><h6 class="mb-0">Synthese par classe</h6></div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table bordered-table mb-0">
              <thead><tr><th>Classe</th><th class="text-end">Eleves</th><th class="text-end">Net du</th><th class="text-end">Solde</th></tr></thead>
              <tbody id="classesBody"><tr><td colspan="4" class="text-center text-secondary-light py-20">Chargement...</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-7">
      <div class="card radius-12 h-100">
        <div class="card-header border-bottom py-16 px-20 d-flex justify-content-between align-items-center">
          <h6 class="mb-0">Situation des etudiants</h6>
          <span class="text-sm text-secondary-light" id="studentsCount">0 etudiant</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table bordered-table mb-0">
              <thead><tr><th>Etudiant</th><th>Classe</th><th class="text-end">Net du</th><th class="text-end">Paye net</th><th class="text-end">Solde</th><th>Situation</th><th class="text-end">Releve</th></tr></thead>
              <tbody id="studentsBody"><tr><td colspan="7" class="text-center text-secondary-light py-20">Chargement...</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="releveModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-header border-bottom py-12 px-20">
        <div>
          <h6 class="text-lg fw-semibold text-primary-light mb-0" id="releveTitle">Releve de compte</h6>
          <small class="text-secondary-light" id="releveSubtitle"></small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-20">
        <div class="row row-cols-md-4 row-cols-2 gy-3 mb-20" id="releveCards"></div>
        <h6 class="mb-8">Dettes</h6>
        <div class="table-responsive mb-20">
          <table class="table bordered-table mb-0">
            <thead><tr><th>Frais</th><th>Periode</th><th class="text-end">Initial</th><th class="text-end">Exoneration</th><th class="text-end">Paye net</th><th class="text-end">Solde</th><th>Statut</th></tr></thead>
            <tbody id="releveDettes"><tr><td colspan="7" class="text-center text-secondary-light py-16">-</td></tr></tbody>
          </table>
        </div>
        <h6 class="mb-8">Echeances</h6>
        <div class="table-responsive">
          <table class="table bordered-table mb-0">
            <thead><tr><th>Date</th><th>Frais</th><th class="text-end">Montant</th><th>Statut</th></tr></thead>
            <tbody id="releveEcheances"><tr><td colspan="4" class="text-center text-secondary-light py-16">-</td></tr></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-top py-12 px-20">
        <button type="button" class="btn btn-outline-secondary-600" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<?php include __DIR__.'/../includes/common.php'; ?>
<script>
var rows = [];
var releveModal = null;

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

function loadAll() {
  var filters = filterValues();
  loadSynthese(filters);
  loadStudents(filters);
}

function loadSynthese(filters) {
  API.get('api/minervale/soldes/synthese', filters).then(function (r) {
    if (!r.success) { MV.error(r.message); return; }
    var t = r.data.totaux;
    document.getElementById('sumNet').textContent = MV.money(t.net_a_recouvrer);
    document.getElementById('sumExo').textContent = MV.money(t.exonerations);
    document.getElementById('sumPaye').textContent = MV.money(t.encaisse_net);
    document.getElementById('sumSolde').textContent = MV.money(t.solde_restant);
    document.getElementById('sumTaux').textContent = MV.number(t.taux_recouvrement) + ' %';
    var classes = r.data.par_classe || [];
    document.getElementById('classesBody').innerHTML = classes.length ? classes.map(function (c) {
      var parts = String(c.cle || '').split('|');
      return '<tr><td>' + MV.esc(parts[1] || parts[0]) + '</td><td class="text-end">' + MV.number(c.nb_eleves) + '</td><td class="text-end">' + MV.money(c.net_a_recouvrer) + '</td><td class="text-end fw-semibold ' + (Number(c.solde_restant) > 0 ? 'text-danger-main' : 'text-success-main') + '">' + MV.money(c.solde_restant) + '</td></tr>';
    }).join('') : '<tr><td colspan="4" class="text-center text-secondary-light py-20">Aucune donnee</td></tr>';
  });
}

function loadStudents(filters) {
  var body = document.getElementById('studentsBody');
  body.innerHTML = '<tr><td colspan="7" class="text-center text-secondary-light py-20">Chargement...</td></tr>';
  API.get('api/minervale/soldes', filters).then(function (r) {
    if (!r.success) { body.innerHTML = '<tr><td colspan="7" class="text-center text-danger-main py-20">' + MV.esc(r.message) + '</td></tr>'; return; }
    rows = r.data || [];
    var groups = {};
    rows.forEach(function (d) {
      var key = String(d.id_etudiant);
      if (!groups[key]) {
        groups[key] = { id_etudiant: d.id_etudiant, fullname: d.fullname, matricule: d.matricule, classe_libelle: d.classe_libelle, net_du: 0, paye_net: 0, solde: 0, situations: {} };
      }
      groups[key].net_du += Number(d.net_du || 0);
      groups[key].paye_net += Number(d.paye_net || 0);
      groups[key].solde += Number(d.solde || 0);
      groups[key].situations[d.situation] = true;
    });
    var list = Object.keys(groups).map(function (key) { return groups[key]; });
    document.getElementById('studentsCount').textContent = list.length + ' etudiant(s)';
    body.innerHTML = list.length ? list.map(function (s) {
      var situation = s.solde > 0.009 ? (s.paye_net > 0.009 ? 'partiel' : 'impaye') : 'paye';
      return '<tr><td><div class="fw-semibold">' + MV.esc(s.fullname || '-') + '</div><div class="text-xs text-secondary-light">' + MV.esc(s.matricule || '') + '</div></td>' +
        '<td>' + MV.esc(s.classe_libelle || '-') + '</td>' +
        '<td class="text-end">' + MV.money(s.net_du) + '</td>' +
        '<td class="text-end">' + MV.money(s.paye_net) + '</td>' +
        '<td class="text-end fw-semibold ' + (s.solde > 0.009 ? 'text-danger-main' : 'text-success-main') + '">' + MV.money(s.solde) + '</td>' +
        '<td>' + MV.badge(situation) + '</td>' +
        '<td class="text-end"><button type="button" class="btn btn-sm btn-outline-primary-600 radius-4" onclick="openReleve(' + s.id_etudiant + ')"><i class="ri-file-list-3-line"></i></button></td></tr>';
    }).join('') : '<tr><td colspan="7" class="text-center text-secondary-light py-20">Aucun etudiant</td></tr>';
  });
}

function openReleve(idEtudiant) {
  releveModal = releveModal || new bootstrap.Modal(document.getElementById('releveModal'));
  document.getElementById('releveTitle').textContent = 'Releve de compte';
  document.getElementById('releveSubtitle').textContent = 'Chargement...';
  document.getElementById('releveDettes').innerHTML = '<tr><td colspan="7" class="text-center text-secondary-light py-16">Chargement...</td></tr>';
  document.getElementById('releveEcheances').innerHTML = '<tr><td colspan="4" class="text-center text-secondary-light py-16">Chargement...</td></tr>';
  document.getElementById('releveCards').innerHTML = '';
  releveModal.show();
  API.get('api/minervale/soldes/releve/' + idEtudiant, filterValues()).then(function (r) {
    if (!r.success) { MV.error(r.message); return; }
    var d = r.data;
    var e = d.etudiant || {};
    document.getElementById('releveSubtitle').textContent = (e.fullname || '') + ' - ' + (e.matricule || '');
    var t = d.synthese.totaux;
    document.getElementById('releveCards').innerHTML =
      '<div class="col"><div class="p-12 radius-8 bg-neutral-100"><div class="text-xs text-secondary-light">Net du</div><div class="fw-semibold">' + MV.money(t.net_a_recouvrer) + '</div></div></div>' +
      '<div class="col"><div class="p-12 radius-8 bg-neutral-100"><div class="text-xs text-secondary-light">Paye net</div><div class="fw-semibold text-success-main">' + MV.money(t.encaisse_net) + '</div></div></div>' +
      '<div class="col"><div class="p-12 radius-8 bg-neutral-100"><div class="text-xs text-secondary-light">Solde</div><div class="fw-semibold text-danger-main">' + MV.money(t.solde_restant) + '</div></div></div>' +
      '<div class="col"><div class="p-12 radius-8 bg-neutral-100"><div class="text-xs text-secondary-light">Taux</div><div class="fw-semibold">' + MV.number(t.taux_recouvrement) + ' %</div></div></div>';
    document.getElementById('releveDettes').innerHTML = (d.lignes || []).length ? d.lignes.map(function (l) {
      return '<tr><td>' + MV.esc(l.type_libelle || '-') + '</td><td>' + MV.esc(l.periode_libelle || '-') + '</td><td class="text-end">' + MV.money(l.montant_initial) + '</td><td class="text-end">' + MV.money(l.exoneration) + '</td><td class="text-end">' + MV.money(l.paye_net) + '</td><td class="text-end fw-semibold">' + MV.money(l.solde) + '</td><td>' + MV.badge(l.situation) + '</td></tr>';
    }).join('') : '<tr><td colspan="7" class="text-center text-secondary-light py-16">Aucune dette</td></tr>';
    document.getElementById('releveEcheances').innerHTML = (d.echeances || []).length ? d.echeances.map(function (ech) {
      return '<tr><td>' + MV.esc(ech.date_echeance || '-') + '</td><td>' + MV.esc(ech.type_libelle || '-') + '</td><td class="text-end">' + MV.money(ech.montant) + '</td><td>' + MV.badge(ech.statut) + '</td></tr>';
    }).join('') : '<tr><td colspan="4" class="text-center text-secondary-light py-16">Aucune echeance</td></tr>';
  });
}

function resetFilters() {
  document.getElementById('filterForm').reset();
  MV.filterPeriodes(document.querySelector('#filterForm [name="id_annee"]'), document.querySelector('#filterForm [name="id_periode"]'));
  loadAll();
}

function recalculer() {
  var data = filterValues();
  MV.confirm('Recalculer les statuts ?', 'Les statuts des dettes seront synchronises avec les paiements, exonérations et remboursements.').then(function (result) {
    if (!result.isConfirmed) return;
    API.post('api/minervale/soldes/recalculer?' + queryString(data), {}).then(function (r) {
      if (r.success) { MV.toast('success', r.data.updated + ' dette(s) mise(s) a jour'); loadAll(); }
      else MV.error(r.message);
    });
  });
}

document.getElementById('filterForm').addEventListener('submit', function (event) {
  event.preventDefault();
  loadAll();
});

var filterAnnee = document.querySelector('#filterForm [name="id_annee"]');
var filterPeriode = document.querySelector('#filterForm [name="id_periode"]');
filterAnnee.addEventListener('change', function () { MV.filterPeriodes(filterAnnee, filterPeriode); });
MV.filterPeriodes(filterAnnee, filterPeriode);

loadAll();
</script>
