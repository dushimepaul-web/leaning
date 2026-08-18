<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Rapports</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Rapports</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-outline-primary-600 d-flex align-items-center gap-2" onclick="exportPDF()"><i class="ri-file-pdf-line"></i> PDF</button>
      <button type="button" class="btn btn-success-600 d-flex align-items-center gap-2" onclick="exportExcel()"><i class="ri-file-excel-line"></i> Excel</button>
    </div>
  </div>

  <ul class="nav nav-tabs mb-24" id="rapportTabs" role="tablist">
    <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabEleves" type="button">Élèves</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabMinervales" type="button">Minervales</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabMateriels" type="button">Matériels</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabVentes" type="button">Ventes & Achats</button></li>
  </ul>

  <div class="tab-content">
    <div class="tab-pane fade show active" id="tabEleves">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-header border-bottom bg-base py-16 px-24">
              <h6 class="text-lg fw-semibold mb-0">Élèves par classe</h6>
            </div>
            <div class="card-body p-20"><div id="chartElevesClasse" style="min-height:320px;"></div></div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-header border-bottom bg-base py-16 px-24">
              <h6 class="text-lg fw-semibold mb-0">Élèves par section</h6>
            </div>
            <div class="card-body p-20"><div id="chartElevesSection" style="min-height:320px;"></div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="tabMinervales">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-header border-bottom bg-base py-16 px-24">
              <h6 class="text-lg fw-semibold mb-0">Paiements par classe</h6>
            </div>
            <div class="card-body p-20"><div id="chartPaiementsClasse" style="min-height:320px;"></div></div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-header border-bottom bg-base py-16 px-24">
              <h6 class="text-lg fw-semibold mb-0">Paiements par section</h6>
            </div>
            <div class="card-body p-20"><div id="chartPaiementsSection" style="min-height:320px;"></div></div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-header border-bottom bg-base py-16 px-24">
              <h6 class="text-lg fw-semibold mb-0">Statuts des paiements</h6>
            </div>
            <div class="card-body p-20"><div id="chartPaiementsStatuts" style="min-height:320px;"></div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-pane fade" id="tabMateriels">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-header border-bottom bg-base py-16 px-24">
              <h6 class="text-lg fw-semibold mb-0">Produits par classe</h6>
            </div>
            <div class="card-body p-20"><div id="chartProduitsClasse" style="min-height:320px;"></div></div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-header border-bottom bg-base py-16 px-24">
              <h6 class="text-lg fw-semibold mb-0">Produits par section</h6>
            </div>
            <div class="card-body p-20"><div id="chartProduitsSection" style="min-height:320px;"></div></div>
          </div>
        </div>
        <div class="col-md-12">
          <div class="card h-100">
            <div class="card-header border-bottom bg-base py-16 px-24">
              <h6 class="text-lg fw-semibold mb-0">Consommation du stock (top 10)</h6>
            </div>
            <div class="card-body p-20"><div id="chartConsommation" style="min-height:320px;"></div></div>
          </div>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="tabVentes">
      <div class="row g-3 mb-24" id="resumeCards"></div>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-header border-bottom bg-base py-16 px-24">
              <h6 class="text-lg fw-semibold mb-0">Ventes par mois</h6>
            </div>
            <div class="card-body p-20"><div id="chartVentes" style="min-height:320px;"></div></div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-header border-bottom bg-base py-16 px-24">
              <h6 class="text-lg fw-semibold mb-0">Achats par mois</h6>
            </div>
            <div class="card-body p-20"><div id="chartAchats" style="min-height:320px;"></div></div>
          </div>
        </div>
      </div>
      <div class="row g-3 mt-0">
        <div class="col-md-12">
          <div class="card h-100">
            <div class="card-header border-bottom bg-base py-16 px-24">
              <h6 class="text-lg fw-semibold mb-0">Bénéfice net par mois</h6>
            </div>
            <div class="card-body p-20"><div id="chartBenefice" style="min-height:320px;"></div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
var BASE_URL = '<?= base_url() ?>';
var charts = {};
var rapportsData = {};

function createBarChart(selector, categories, seriesData, title) {
  var el = document.querySelector(selector);
  if (!el) return;
  if (charts[selector]) charts[selector].destroy();
  charts[selector] = new ApexCharts(el, {
    chart: { type: 'bar', height: 320, toolbar: { show: true } },
    plotOptions: { bar: { borderRadius: 4, horizontal: false, columnWidth: '55%' } },
    dataLabels: { enabled: true },
    series: seriesData,
    xaxis: { categories: categories },
    colors: ['#25A194', '#FF7A2C', '#04B4FF'],
    title: { text: title, align: 'center' }
  });
  charts[selector].render();
}

function createPieChart(selector, labels, series, title) {
  var el = document.querySelector(selector);
  if (!el) return;
  if (charts[selector]) charts[selector].destroy();
  charts[selector] = new ApexCharts(el, {
    chart: { type: 'pie', height: 320 },
    labels: labels,
    series: series,
    colors: ['#25A194', '#FF7A2C', '#DC2626', '#04B4FF', '#7C3AED'],
    title: { text: title, align: 'center' },
    responsive: [{ breakpoint: 480, options: { chart: { width: 200 }, legend: { position: 'bottom' } } }]
  });
  charts[selector].render();
}

async function loadElevesCharts() {
  try {
    var r1 = await fetch(BASE_URL + 'api/rapports/eleves_par_classe').then(r => r.json());
    if (r1.success && r1.data) {
      rapportsData.elevesClasse = r1.data;
      createBarChart('#chartElevesClasse',
        r1.data.map(function(d) { return d.classe; }),
        [{ name: 'Élèves', data: r1.data.map(function(d) { return parseInt(d.total); }) }],
        'Élèves par classe'
      );
    }
    var r2 = await fetch(BASE_URL + 'api/rapports/eleves_par_section').then(r => r.json());
    if (r2.success && r2.data) {
      rapportsData.elevesSection = r2.data;
      createBarChart('#chartElevesSection',
        r2.data.map(function(d) { return d.section; }),
        [{ name: 'Élèves', data: r2.data.map(function(d) { return parseInt(d.total); }) }],
        'Élèves par section'
      );
    }
  } catch(e) { console.error(e); }
}

async function loadMinervalesCharts() {
  try {
    var r1 = await fetch(BASE_URL + 'api/rapports/paiements_par_classe').then(r => r.json());
    if (r1.success && r1.data) {
      rapportsData.paiementsClasse = r1.data;
      createBarChart('#chartPaiementsClasse',
        r1.data.map(function(d) { return d.classe; }),
        [{ name: 'Nb paiements', data: r1.data.map(function(d) { return parseInt(d.total); }) }, { name: 'Montant (' + DEVISE + ')', data: r1.data.map(function(d) { return parseFloat(d.montant_total); }) }],
        'Paiements par classe'
      );
    }
    var r2 = await fetch(BASE_URL + 'api/rapports/paiements_par_section').then(r => r.json());
    if (r2.success && r2.data) {
      rapportsData.paiementsSection = r2.data;
      createBarChart('#chartPaiementsSection',
        r2.data.map(function(d) { return d.section; }),
        [{ name: 'Nb paiements', data: r2.data.map(function(d) { return parseInt(d.total); }) }, { name: 'Montant (' + DEVISE + ')', data: r2.data.map(function(d) { return parseFloat(d.montant_total); }) }],
        'Paiements par section'
      );
    }
    var r3 = await fetch(BASE_URL + 'api/rapports/paiements_statuts').then(r => r.json());
    if (r3.success && r3.data) {
      rapportsData.paiementsStatuts = r3.data;
      createPieChart('#chartPaiementsStatuts',
        r3.data.map(function(d) { return d.statut; }),
        r3.data.map(function(d) { return parseInt(d.total); }),
        'Statuts des paiements'
      );
    }
  } catch(e) { console.error(e); }
}

async function loadMaterielsCharts() {
  try {
    var r1 = await fetch(BASE_URL + 'api/rapports/produits_par_classe').then(r => r.json());
    if (r1.success && r1.data) {
      rapportsData.produitsClasse = r1.data;
      createBarChart('#chartProduitsClasse',
        r1.data.map(function(d) { return d.classe; }),
        [{ name: 'Produits', data: r1.data.map(function(d) { return parseInt(d.produits_count || 0); }) }, { name: 'Quantité', data: r1.data.map(function(d) { return parseInt(d.quantite_totale || 0); }) }],
        'Produits par classe'
      );
    }
    var r2 = await fetch(BASE_URL + 'api/rapports/produits_par_section').then(r => r.json());
    if (r2.success && r2.data) {
      rapportsData.produitsSection = r2.data;
      createBarChart('#chartProduitsSection',
        r2.data.map(function(d) { return d.section; }),
        [{ name: 'Produits', data: r2.data.map(function(d) { return parseInt(d.produits_count || 0); }) }, { name: 'Quantité', data: r2.data.map(function(d) { return parseInt(d.quantite_totale || 0); }) }],
        'Produits par section'
      );
    }
    var r3 = await fetch(BASE_URL + 'api/rapports/consommation_stock').then(r => r.json());
    if (r3.success && r3.data) {
      rapportsData.consommation = r3.data;
      createBarChart('#chartConsommation',
        r3.data.map(function(d) { return d.libelle; }),
        [{ name: 'Stock actuel', data: r3.data.map(function(d) { return parseInt(d.stock_actuel); }) }, { name: 'Stock minimum', data: r3.data.map(function(d) { return parseInt(d.stock_mini); }) }],
        'État du stock'
      );
    }
  } catch(e) { console.error(e); }
}

async function loadVentesCharts() {
  try {
    var r0 = await fetch(BASE_URL + 'api/rapports/resume_ventes').then(r => r.json());
    if (r0.success && r0.data) {
      var d = r0.data;
      var devise = typeof DEVISE !== 'undefined' ? DEVISE : 'BIF';
      document.getElementById('resumeCards').innerHTML =
        '<div class="col-md-4"><div class="card h-100 border-start border-4 border-success"><div class="card-body p-20"><h6 class="text-sm text-secondary-light mb-4">Chiffre d\'affaires</h6><h4 class="fw-bold text-success">' + parseFloat(d.ca_total).toLocaleString() + ' ' + devise + '</h4></div></div></div>' +
        '<div class="col-md-4"><div class="card h-100 border-start border-4 border-info"><div class="card-body p-20"><h6 class="text-sm text-secondary-light mb-4">Bénéfice total</h6><h4 class="fw-bold text-info">' + parseFloat(d.benefice_total).toLocaleString() + ' ' + devise + '</h4></div></div></div>' +
        '<div class="col-md-4"><div class="card h-100 border-start border-4 border-warning"><div class="card-body p-20"><h6 class="text-sm text-secondary-light mb-4">Total achats</h6><h4 class="fw-bold text-warning">' + parseFloat(d.achats_total).toLocaleString() + ' ' + devise + '</h4></div></div></div>';
    }
    var r1 = await fetch(BASE_URL + 'api/rapports/ventes_par_mois').then(r => r.json());
    if (r1.success && r1.data) {
      rapportsData.ventes = r1.data;
      var mois = r1.data.map(function(d) { return d.mois; });
      createBarChart('#chartVentes', mois,
        [{ name: 'CA', data: r1.data.map(function(d) { return parseFloat(d.total_ventes); }) }, { name: 'Bénéfice', data: r1.data.map(function(d) { return parseFloat(d.benefice); }) }],
        'Ventes par mois'
      );
    }
    var r2 = await fetch(BASE_URL + 'api/rapports/achats_par_mois').then(r => r.json());
    if (r2.success && r2.data) {
      rapportsData.achats = r2.data;
      createBarChart('#chartAchats', r2.data.map(function(d) { return d.mois; }),
        [{ name: 'Achats', data: r2.data.map(function(d) { return parseFloat(d.total_achats); }) }],
        'Achats par mois'
      );
    }
    if (r1.success && r1.data && r2.success && r2.data) {
      var ventesMap = {}, achatsMap = {};
      r1.data.forEach(function(d) { ventesMap[d.mois] = parseFloat(d.benefice); });
      r2.data.forEach(function(d) { achatsMap[d.mois] = parseFloat(d.total_achats); });
      var allMois = [...new Set([...Object.keys(ventesMap), ...Object.keys(achatsMap)])].sort();
      createBarChart('#chartBenefice', allMois,
        [{ name: 'Bénéfice', data: allMois.map(function(m) { return ventesMap[m] || 0; }) }],
        'Bénéfice net par mois'
      );
    }
  } catch(e) { console.error(e); }
}

function exportPDF() {
  var activeTab = document.querySelector('.tab-pane.active');
  var content = activeTab ? activeTab.cloneNode(true) : document.body.cloneNode(true);
  var printWindow = window.open('', '_blank');
  printWindow.document.write('<html><head><title>Rapport</title>');
  printWindow.document.write('<link rel="stylesheet" href="<?= base_url() ?>assets/css/lib/bootstrap.min.css">');
  printWindow.document.write('<style>body{padding:20px;font-family:sans-serif;} .card{border:1px solid #ddd;margin-bottom:15px;} .card-header{background:#f8f9fa;padding:10px 15px;font-weight:bold;}</style>');
  printWindow.document.write('</head><body>');
  printWindow.document.write('<h2>Rapport VIP School - <?= date('d/m/Y') ?></h2>');
  printWindow.document.write(content.innerHTML);
  printWindow.document.write('</body></html>');
  printWindow.document.close();
  setTimeout(function() { printWindow.print(); }, 500);
}

function exportExcel() {
  var csv = '\uFEFFRapport VIP School\n';
  var activeTabId = document.querySelector('.tab-pane.active').id;
  if (activeTabId === 'tabEleves' && rapportsData.elevesClasse) {
    csv += '\nÉlèves par classe\nClasse,Total\n';
    rapportsData.elevesClasse.forEach(function(d) { csv += d.classe + ',' + d.total + '\n'; });
    if (rapportsData.elevesSection) {
      csv += '\nÉlèves par section\nSection,Total\n';
      rapportsData.elevesSection.forEach(function(d) { csv += d.section + ',' + d.total + '\n'; });
    }
  } else if (activeTabId === 'tabMinervales') {
    if (rapportsData.paiementsClasse) {
      csv += '\nPaiements par classe\nClasse,Nombre,Montant\n';
      rapportsData.paiementsClasse.forEach(function(d) { csv += d.classe + ',' + d.total + ',' + d.montant_total + '\n'; });
    }
    if (rapportsData.paiementsSection) {
      csv += '\nPaiements par section\nSection,Nombre,Montant\n';
      rapportsData.paiementsSection.forEach(function(d) { csv += d.section + ',' + d.total + ',' + d.montant_total + '\n'; });
    }
    if (rapportsData.paiementsStatuts) {
      csv += '\nStatuts\nStatut,Total\n';
      rapportsData.paiementsStatuts.forEach(function(d) { csv += d.statut + ',' + d.total + '\n'; });
    }
  } else if (activeTabId === 'tabMateriels') {
    if (rapportsData.produitsClasse) {
      csv += '\nProduits par classe\nClasse,Produits,Quantité\n';
      rapportsData.produitsClasse.forEach(function(d) { csv += d.classe + ',' + (d.produits_count||0) + ',' + (d.quantite_totale||0) + '\n'; });
    }
    if (rapportsData.produitsSection) {
      csv += '\nProduits par section\nSection,Produits,Quantité\n';
      rapportsData.produitsSection.forEach(function(d) { csv += d.section + ',' + (d.produits_count||0) + ',' + (d.quantite_totale||0) + '\n'; });
    }
    if (rapportsData.consommation) {
      csv += '\nÉtat du stock\nProduit,Stock actuel,Stock minimum,Stock\n';
      rapportsData.consommation.forEach(function(d) { csv += d.libelle + ',' + d.stock_actuel + ',' + d.stock_mini + '\n'; });
    }
  } else if (activeTabId === 'tabVentes') {
    if (rapportsData.ventes) {
      csv += '\nVentes par mois\nMois,Total,Bénéfice\n';
      rapportsData.ventes.forEach(function(d) { csv += d.mois + ',' + d.total_ventes + ',' + d.benefice + '\n'; });
    }
    if (rapportsData.achats) {
      csv += '\nAchats par mois\nMois,Total\n';
      rapportsData.achats.forEach(function(d) { csv += d.mois + ',' + d.total_achats + '\n'; });
    }
  }
  var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  var link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'rapport_' + new Date().toISOString().split('T')[0] + '.csv';
  link.click();
}

document.addEventListener('DOMContentLoaded', function() {
  loadElevesCharts();
  document.querySelector('#tabMinervales').addEventListener('shown.bs.tab', function() {
    if (!charts['#chartPaiementsClasse']) loadMinervalesCharts();
  });
  document.querySelector('#tabMateriels').addEventListener('shown.bs.tab', function() {
    if (!charts['#chartProduitsClasse']) loadMaterielsCharts();
  });
  document.querySelector('#tabVentes').addEventListener('shown.bs.tab', function() {
    if (!charts['#chartVentes']) loadVentesCharts();
  });
  document.querySelector('#tabEleves').addEventListener('shown.bs.tab', function() {
    if (!charts['#chartElevesClasse']) loadElevesCharts();
  });
});
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>