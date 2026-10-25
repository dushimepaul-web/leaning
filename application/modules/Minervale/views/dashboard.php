<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Minervale</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Minervale</span>
      </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="<?= base_url('Minervale/Dettes') ?>" class="btn btn-outline-primary-600 d-flex align-items-center gap-6">
        <i class="ri-list-check-3"></i> Dettes
      </a>
      <a href="<?= base_url('Minervale/Soldes') ?>" class="btn btn-outline-primary-600 d-flex align-items-center gap-6">
        <i class="ri-scales-3-line"></i> Soldes
      </a>
      <a href="<?= base_url('Minervale/Paiements') ?>" class="btn btn-primary-600 d-flex align-items-center gap-6">
        <i class="ri-cash-line"></i> Encaisser
      </a>
    </div>
  </div>

  <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 gy-4 mb-24">
    <div class="col">
      <div class="card h-100 radius-12">
        <div class="card-body p-20">
          <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
              <p class="text-secondary-light mb-4">Net a recouvrer</p>
              <h4 class="mb-0" id="cardNet">-</h4>
            </div>
            <span class="w-44-px h-44-px d-flex justify-content-center align-items-center rounded-circle bg-primary-focus text-primary-main text-xl">
              <i class="ri-wallet-3-line"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card h-100 radius-12">
        <div class="card-body p-20">
          <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
              <p class="text-secondary-light mb-4">Encaisse net</p>
              <h4 class="mb-0" id="cardEncaisse">-</h4>
            </div>
            <span class="w-44-px h-44-px d-flex justify-content-center align-items-center rounded-circle bg-success-focus text-success-main text-xl">
              <i class="ri-check-double-line"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card h-100 radius-12">
        <div class="card-body p-20">
          <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
              <p class="text-secondary-light mb-4">Solde restant</p>
              <h4 class="mb-0" id="cardSolde">-</h4>
            </div>
            <span class="w-44-px h-44-px d-flex justify-content-center align-items-center rounded-circle bg-danger-focus text-danger-main text-xl">
              <i class="ri-error-warning-line"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card h-100 radius-12">
        <div class="card-body p-20">
          <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
              <p class="text-secondary-light mb-4">Taux de recouvrement</p>
              <h4 class="mb-0" id="cardTaux">-</h4>
            </div>
            <span class="w-44-px h-44-px d-flex justify-content-center align-items-center rounded-circle bg-warning-focus text-warning-main text-xl">
              <i class="ri-pie-chart-2-line"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row gy-4 mb-24">
    <div class="col-xl-7">
      <div class="card h-100 radius-12">
        <div class="card-header border-bottom py-16 px-20 d-flex align-items-center justify-content-between">
          <h6 class="mb-0">Situation par classe</h6>
          <a href="<?= base_url('Minervale/Soldes') ?>" class="text-primary-600 text-sm">Voir les soldes</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table bordered-table mb-0">
              <thead>
                <tr>
                  <th>Classe</th>
                  <th class="text-end">Eleves</th>
                  <th class="text-end">Net du</th>
                  <th class="text-end">Encaisse</th>
                  <th class="text-end">Solde</th>
                </tr>
              </thead>
              <tbody id="classesBody">
                <tr><td colspan="5" class="text-center text-secondary-light py-20">Chargement...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-5">
      <div class="card h-100 radius-12">
        <div class="card-header border-bottom py-16 px-20">
          <h6 class="mb-0">Repartition par situation</h6>
        </div>
        <div class="card-body p-20" id="situationsBody">
          <div class="text-center text-secondary-light py-20">Chargement...</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card radius-12">
    <div class="card-header border-bottom py-16 px-20 d-flex align-items-center justify-content-between">
      <h6 class="mb-0">Dettes non soldees</h6>
      <a href="<?= base_url('Minervale/Dettes') ?>" class="text-primary-600 text-sm">Toutes les dettes</a>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table bordered-table mb-0">
          <thead>
            <tr>
              <th>Etudiant</th>
              <th>Classe</th>
              <th>Frais</th>
              <th class="text-end">Net du</th>
              <th class="text-end">Solde</th>
              <th>Statut</th>
            </tr>
          </thead>
          <tbody id="dettesBody">
            <tr><td colspan="6" class="text-center text-secondary-light py-20">Chargement...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<?php include __DIR__.'/includes/common.php'; ?>
<script>
(function () {
  var filters = { id_annee: <?= (int)$id_annee_active ?> };
  API.get('api/minervale/soldes/synthese', filters).then(function (r) {
    if (!r.success) { MV.error(r.message); return; }
    var t = r.data.totaux;
    document.getElementById('cardNet').textContent = MV.money(t.net_a_recouvrer);
    document.getElementById('cardEncaisse').textContent = MV.money(t.encaisse_net);
    document.getElementById('cardSolde').textContent = MV.money(t.solde_restant);
    document.getElementById('cardTaux').textContent = MV.number(t.taux_recouvrement) + ' %';

    var classes = r.data.par_classe || [];
    document.getElementById('classesBody').innerHTML = classes.length ? classes.map(function (c) {
      var parts = String(c.cle || '').split('|');
      return '<tr><td>' + MV.esc(parts[1] || parts[0]) + '</td><td class="text-end">' + MV.number(c.nb_eleves) + '</td><td class="text-end">' + MV.money(c.net_a_recouvrer) + '</td><td class="text-end">' + MV.money(c.encaisse_net) + '</td><td class="text-end fw-semibold">' + MV.money(c.solde_restant) + '</td></tr>';
    }).join('') : '<tr><td colspan="5" class="text-center text-secondary-light py-20">Aucune donnee</td></tr>';

    var situations = r.data.par_situation || [];
    document.getElementById('situationsBody').innerHTML = situations.length ? situations.map(function (s) {
      var percent = t.nb_dettes > 0 ? Math.round((s.nb / t.nb_dettes) * 100) : 0;
      return '<div class="mb-16"><div class="d-flex justify-content-between mb-6"><span>' + MV.esc(s.libelle) + '</span><span class="text-secondary-light">' + MV.number(s.nb) + ' dette(s)</span></div><div class="progress" style="height:8px"><div class="progress-bar" style="width:' + percent + '%"></div></div></div>';
    }).join('') : '<div class="text-center text-secondary-light py-20">Aucune donnee</div>';
  });

  API.get('api/minervale/soldes', filters).then(function (r) {
    if (!r.success) { MV.error(r.message); return; }
    var rows = (r.data || []).filter(function (d) { return Number(d.solde) > 0.009; }).slice(0, 10);
    document.getElementById('dettesBody').innerHTML = rows.length ? rows.map(function (d) {
      return '<tr><td><div class="fw-semibold">' + MV.esc(d.fullname) + '</div><div class="text-xs text-secondary-light">' + MV.esc(d.matricule) + '</div></td><td>' + MV.esc(d.classe_libelle || '-') + '</td><td>' + MV.esc(d.type_libelle || '-') + '</td><td class="text-end">' + MV.money(d.net_du) + '</td><td class="text-end fw-semibold text-danger-main">' + MV.money(d.solde) + '</td><td>' + MV.badge(d.situation) + '</td></tr>';
    }).join('') : '<tr><td colspan="6" class="text-center text-secondary-light py-20">Aucune dette non soldee</td></tr>';
  });
})();
</script>
