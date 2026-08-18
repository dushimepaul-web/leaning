<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>

<div class="dashboard-main-body">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h4 class="fw-bold mb-0">Tableau de Bord Analytique</h4>
      <p class="text-secondary-light text-sm mb-0">Vue globale et indicateurs de l'établissement.</p>
    </div>
    <?php if (isset($isAdminOrDirection) && $isAdminOrDirection): ?>
    <form method="GET" class="d-flex align-items-center gap-2">
      <select name="id_annee" class="form-select form-select-sm" onchange="this.form.submit()">
        <?php foreach ($annees as $a): ?>
          <option value="<?= $a['id_annee'] ?>" <?= $id_annee_sel == $a['id_annee'] ? 'selected' : '' ?>><?= htmlspecialchars($a['libelle']) ?></option>
        <?php endforeach; ?>
      </select>
    </form>
    <?php endif; ?>
  </div>

  <?php if (isset($isAdminOrDirection) && $isAdminOrDirection): ?>
  <!-- 4 KPI CARDS -->
  <div class="row gy-4 mb-24">
    <div class="col-xl-3 col-sm-6">
      <div class="card shadow-sm border-0 radius-8 h-100" style="border-left: 4px solid #25A194 !important;">
        <div class="card-body p-20">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-uppercase text-secondary-light text-xs fw-semibold">Élèves Actifs</span>
              <h4 class="fw-bold mt-4 mb-0"><?= number_format($total_etudiants) ?></h4>
            </div>
            <div class="w-48-px h-48-px rounded-circle d-flex justify-content-center align-items-center" style="background: rgba(37, 161, 148, 0.1); color: #25A194;">
              <i class="ri-graduation-cap-line text-2xl"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card shadow-sm border-0 radius-8 h-100" style="border-left: 4px solid #3b82f6 !important;">
        <div class="card-body p-20">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-uppercase text-secondary-light text-xs fw-semibold">Enseignants</span>
              <h4 class="fw-bold mt-4 mb-0"><?= number_format($total_enseignants) ?></h4>
            </div>
            <div class="w-48-px h-48-px rounded-circle d-flex justify-content-center align-items-center bg-blue-50 text-blue-600">
              <i class="ri-user-star-line text-2xl"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card shadow-sm border-0 radius-8 h-100" style="border-left: 4px solid #10b981 !important;">
        <div class="card-body p-20">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-uppercase text-secondary-light text-xs fw-semibold">Total Encaissé</span>
              <h4 class="fw-bold mt-4 mb-0"><?= number_format($total_encaisse, 0, ',', ' ') ?> FBU</h4>
            </div>
            <div class="w-48-px h-48-px rounded-circle d-flex justify-content-center align-items-center bg-success-50 text-success-600">
              <i class="ri-money-dollar-circle-line text-2xl"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card shadow-sm border-0 radius-8 h-100" style="border-left: 4px solid #f59e0b !important;">
        <div class="card-body p-20">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-uppercase text-secondary-light text-xs fw-semibold">Taux Recouvrement</span>
              <h4 class="fw-bold mt-4 mb-0"><?= $taux_recouvrement ?>%</h4>
            </div>
            <div class="w-48-px h-48-px rounded-circle d-flex justify-content-center align-items-center bg-warning-50 text-warning-600">
              <i class="ri-percent-line text-2xl"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CHARTS -->
  <div class="row gy-4 mb-24">
    <div class="col-xl-8">
      <div class="card border-0 shadow-sm radius-8 h-100">
        <div class="card-body p-24">
          <h6 class="fw-bold mb-16">Évolution des Paiements (12 derniers mois)</h6>
          <?php if(!empty($paiements_mois)): ?>
            <canvas id="paiementsChart" height="110"></canvas>
          <?php else: ?><p class="text-muted text-center py-5">Aucune donnée de paiement disponible.</p><?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-xl-4">
      <div class="card border-0 shadow-sm radius-8 h-100">
        <div class="card-body p-24">
          <h6 class="fw-bold mb-16">Répartition Élèves par Classe</h6>
          <?php if(!empty($eleves_classe)): ?>
            <canvas id="classesChart" height="190"></canvas>
          <?php else: ?><p class="text-muted text-center py-5">Aucune classe trouvée.</p><?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-xl-12">
      <div class="card border-0 shadow-sm radius-8">
        <div class="card-body p-24">
          <h6 class="fw-bold mb-16">État des Stocks Produits (Alerte si < Seuil Min)</h6>
          <?php if(!empty($stocks)): ?>
            <canvas id="stocksChart" height="80"></canvas>
          <?php else: ?><p class="text-muted text-center py-5">Aucun produit en stock.</p><?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- PERFORMANCE SCOLAIRE -->
  <div class="row gy-4 mb-24">
    <div class="col-xl-7">
      <div class="card border-0 shadow-sm radius-8 h-100">
        <div class="card-body p-24">
          <div class="d-flex justify-content-between align-items-center mb-16">
            <h6 class="fw-bold mb-0">Performance des Classes (Moyenne sur <?= $echelle_notes ?>)</h6>
            <div class="d-flex gap-3 text-xs">
              <span class="d-flex align-items-center gap-1"><span class="d-inline-block w-2 h-2 rounded-circle" style="background:#10b981;"></span> Excellente (≥70%)</span>
              <span class="d-flex align-items-center gap-1"><span class="d-inline-block w-2 h-2 rounded-circle" style="background:#3b82f6;"></span> Moyenne (50-70%)</span>
              <span class="d-flex align-items-center gap-1"><span class="d-inline-block w-2 h-2 rounded-circle" style="background:#ef4444;"></span> Faible (&lt;50%)</span>
            </div>
          </div>
          <?php if(!empty($moyennes_classes)): ?>
            <canvas id="perfClassesChart" height="190"></canvas>
          <?php else: ?><p class="text-muted text-center py-5">Aucun bulletin disponible pour cette année.</p><?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-xl-5">
      <div class="card border-0 shadow-sm radius-8 h-100">
        <div class="card-body p-24">
          <h6 class="fw-bold mb-16">Élèves Brillants (Top 5)</h6>
          <?php if(!empty($top_eleves)): ?>
            <?php $medals = ['🥇', '🥈', '🥉']; $i = 0; ?>
            <div class="d-flex flex-column gap-16">
              <?php foreach ($top_eleves as $t): $i++; ?>
              <div class="d-flex align-items-center gap-3 border rounded-3 p-3">
                <div class="w-40-px h-40-px rounded-circle d-flex justify-content-center align-items-center fw-bold text-white flex-shrink-0" style="background: <?= $i === 1 ? '#f59e0b' : ($i === 2 ? '#94a3b8' : ($i === 3 ? '#b45309' : '#25A194')) ?>;">
                  <?= $i ?>
                </div>
                <div class="flex-grow-1">
                  <h6 class="fw-bold mb-0 text-truncate"><?= htmlspecialchars($t['fullname'] ?: $t['matricule']) ?></h6>
                  <small class="text-secondary-light"><?= htmlspecialchars($t['classe']) ?> · <?= htmlspecialchars($t['matricule']) ?></small>
                </div>
                <span class="fw-bold text-success"><?= number_format((float)$t['moyenne'], 2) ?>/<?= $echelle_notes ?></span>
              </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?><p class="text-muted text-center py-5">Aucun élève classé pour cette année.</p><?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-xl-8">
      <div class="card border-0 shadow-sm radius-8 h-100">
        <div class="card-body p-24">
          <h6 class="fw-bold mb-16">Évolution des Classes par Période</h6>
          <?php if(!empty($evolution_classes)): ?>
            <canvas id="evolutionClassesChart" height="160"></canvas>
          <?php else: ?><p class="text-muted text-center py-5">Aucune donnée de bulletin pour cette année.</p><?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-xl-4">
      <div class="card border-0 shadow-sm radius-8 h-100">
        <div class="card-body p-24">
          <h6 class="fw-bold mb-16">Décisions des Bulletins</h6>
          <?php if(!empty($repartition_decision)): ?>
            <canvas id="decisionsChart" height="160"></canvas>
          <?php else: ?><p class="text-muted text-center py-5">Aucune décision enregistrée.</p><?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart.js CDN & Init -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
  const mainColor = '#25A194';
  const echelleNotes = <?= (int)$echelle_notes ?>;

  <?php if(!empty($paiements_mois)): ?>
  new Chart(document.getElementById('paiementsChart').getContext('2d'), {
      type: 'line',
      data: {
          labels: <?= json_encode(array_column($paiements_mois, 'mois')) ?>,
          datasets: [{ label: 'Montant (FBU)', data: <?= json_encode(array_column($paiements_mois, 'total')) ?>, borderColor: mainColor, backgroundColor: 'rgba(37, 161, 148, 0.1)', fill: true, tension: 0.3 }]
      },
      options: { responsive: true, plugins: { legend: { display: false } } }
  });
  <?php endif; ?>

  <?php if(!empty($eleves_classe)): ?>
  new Chart(document.getElementById('classesChart').getContext('2d'), {
      type: 'doughnut',
      data: {
          labels: <?= json_encode(array_column($eleves_classe, 'classe')) ?>,
          datasets: [{ data: <?= json_encode(array_column($eleves_classe, 'total')) ?>, backgroundColor: [mainColor, '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'] }]
      },
      options: { responsive: true }
  });
  <?php endif; ?>

  <?php if(!empty($stocks)): ?>
  const stockData = <?= json_encode($stocks) ?>;
  new Chart(document.getElementById('stocksChart').getContext('2d'), {
      type: 'bar',
      data: {
          labels: stockData.map(s => s.libelle),
          datasets: [{ label: 'Stock Actuel', data: stockData.map(s => s.stock_actuel), backgroundColor: stockData.map(s => parseInt(s.stock_actuel) < parseInt(s.stock_mini) ? '#ef4444' : '#10b981') }]
      },
      options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } }
  });
  <?php endif; ?>

  <?php if(!empty($moyennes_classes)): ?>
  const perfData = <?= json_encode($moyennes_classes) ?>;
  new Chart(document.getElementById('perfClassesChart').getContext('2d'), {
      type: 'bar',
      data: {
          labels: perfData.map(c => c.classe),
          datasets: [{
              label: 'Moyenne (/' + echelleNotes + ')',
              data: perfData.map(c => parseFloat(c.moyenne)),
              backgroundColor: perfData.map(c => {
                  const pct = parseFloat(c.moyenne) / echelleNotes * 100;
                  return pct >= 70 ? '#10b981' : (pct >= 50 ? '#3b82f6' : '#ef4444');
              }),
              borderRadius: 6
          }]
      },
      options: {
          indexAxis: 'y', responsive: true,
          scales: { x: { min: 0, max: Math.max(echelleNotes, Math.ceil(Math.max(...perfData.map(c => parseFloat(c.moyenne)), echelleNotes) * 1.05)) } },
          plugins: { legend: { display: false } }
      }
  });
  <?php endif; ?>

  <?php if(!empty($evolution_classes)): ?>
  const evolData = <?= json_encode($evolution_classes) ?>;
  const evolPeriodes = [...new Set(evolData.map(e => e.periode))];
  const evolClasses = [...new Set(evolData.map(e => e.classe))];
  const evolPalette = ['#25A194', '#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444', '#10b981', '#0ea5e9'];
  new Chart(document.getElementById('evolutionClassesChart').getContext('2d'), {
      type: 'line',
      data: {
          labels: evolPeriodes,
          datasets: evolClasses.map((cl, i) => ({
              label: cl,
              data: evolPeriodes.map(p => {
                  const row = evolData.find(e => e.classe === cl && e.periode === p);
                  return row ? parseFloat(row.moyenne) : null;
              }),
              borderColor: evolPalette[i % evolPalette.length],
              backgroundColor: 'transparent',
              tension: 0.3,
              spanGaps: true
          }))
      },
      options: { responsive: true, scales: { y: { min: 0, max: Math.max(echelleNotes, Math.ceil(Math.max(...evolData.map(e => parseFloat(e.moyenne)), echelleNotes) * 1.05)) } } }
  });
  <?php endif; ?>

  <?php if(!empty($repartition_decision)): ?>
  const decLabels = <?= json_encode(array_column($repartition_decision, 'decision')) ?>;
  const decColors = decLabels.map(d => d === 'admis' ? '#10b981' : (d === 'ajourne' ? '#f59e0b' : '#ef4444'));
  new Chart(document.getElementById('decisionsChart').getContext('2d'), {
      type: 'doughnut',
      data: {
          labels: decLabels,
          datasets: [{ data: <?= json_encode(array_column($repartition_decision, 'total')) ?>, backgroundColor: decColors }]
      },
      options: { responsive: true, cutout: '65%' }
  });
  <?php endif; ?>
  </script>

  <?php else: ?>
  <div class="card border-0 shadow-sm radius-8 text-center p-5">
    <h4>Espace Dédié</h4>
    <p class="text-muted">Vous disposez d'un accès personnalisé à vos modules de gestion autorisés.</p>
  </div>
  <?php endif; ?>
</div>

<?php include VIEWPATH.'includes/Footer.php'; ?>
