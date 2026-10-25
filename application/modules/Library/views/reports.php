<?php
$page_title = 'Rapports Librairie';
$page_subtitle = 'Rapports';
include __DIR__.'/_head.php';
?>
<div class="card mb-3"><div class="card-body">
  <div class="d-flex flex-wrap align-items-end gap-2">
    <div><label class="form-label mb-1">Du</label><input type="date" class="form-control form-control-sm" id="date_from"></div>
    <div><label class="form-label mb-1">Au</label><input type="date" class="form-control form-control-sm" id="date_to"></div>
    <button class="btn btn-sm btn-primary-600" onclick="loadReport()">Afficher</button>
    <button class="btn btn-sm btn-outline-success ms-auto" onclick="exportReport()"><i class="ri-download-line"></i> Exporter CSV</button>
  </div>
</div></div>
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-secondary-light text-sm mb-1">Chiffre d'affaires</p><h5 class="mb-0" id="ca">0</h5></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-secondary-light text-sm mb-1">CA net (retours deduits)</p><h5 class="mb-0" id="caNet">0</h5></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-secondary-light text-sm mb-1">Marge brute</p><h5 class="mb-0" id="marge">0</h5></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-secondary-light text-sm mb-1">Benefice net</p><h5 class="mb-0" id="net">0</h5></div></div></div>
</div>
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-secondary-light text-sm mb-1">Ventes</p><h5 class="mb-0" id="nbVentes">0</h5></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-secondary-light text-sm mb-1">Retours</p><h5 class="mb-0" id="retours">0</h5></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-secondary-light text-sm mb-1">Cout des marchandises</p><h5 class="mb-0" id="cogs">0</h5></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-secondary-light text-sm mb-1">Charges</p><h5 class="mb-0" id="charges">0</h5></div></div></div>
</div>
<div class="row g-3">
  <div class="col-lg-6"><div class="card"><div class="card-header"><h6 class="mb-0">Ventes par jour</h6></div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Jour</th><th>Ventes</th><th>Montant</th></tr></thead><tbody id="byDay"><tr><td colspan="3" class="text-center text-secondary-light">Chargement...</td></tr></tbody></table></div></div></div></div>
  <div class="col-lg-6"><div class="card"><div class="card-header"><h6 class="mb-0">Ventes par produit</h6></div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Produit</th><th>Qte</th><th>CA</th><th>Marge</th></tr></thead><tbody id="byProduct"><tr><td colspan="4" class="text-center text-secondary-light">Chargement...</td></tr></tbody></table></div></div></div></div>
  <div class="col-lg-6"><div class="card"><div class="card-header"><h6 class="mb-0">Ventes par utilisateur</h6></div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Utilisateur</th><th>Ventes</th><th>Montant</th></tr></thead><tbody id="byUser"><tr><td colspan="3" class="text-center text-secondary-light">Chargement...</td></tr></tbody></table></div></div></div></div>
  <div class="col-lg-6"><div class="card"><div class="card-header"><h6 class="mb-0">Encaissements par mode</h6></div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Mode</th><th>Nombre</th><th>Montant</th></tr></thead><tbody id="byMode"><tr><td colspan="3" class="text-center text-secondary-light">Chargement...</td></tr></tbody></table></div></div></div></div>
</div>
<script>
let report = null;
async function loadReport(){
  try {
    const p = new URLSearchParams();
    if (document.getElementById('date_from').value) p.set('date_from', document.getElementById('date_from').value);
    if (document.getElementById('date_to').value) p.set('date_to', document.getElementById('date_to').value);
    const r = await Library.api('api/library/reports' + (p.toString() ? '?' + p : '')); report = r.data;
    const s = report.resume;
    document.getElementById('ca').textContent = Library.money(s.chiffre_affaires);
    document.getElementById('caNet').textContent = Library.money(s.ca_net);
    document.getElementById('marge').textContent = Library.money(s.marge_brute);
    document.getElementById('net').textContent = Library.money(s.benefice_net);
    document.getElementById('nbVentes').textContent = s.nb_ventes;
    document.getElementById('retours').textContent = Library.money(s.retours);
    document.getElementById('cogs').textContent = Library.money(s.cogs);
    document.getElementById('charges').textContent = Library.money(s.charges);
    document.getElementById('byDay').innerHTML = (report.par_jour||[]).length ? report.par_jour.map(function(x){ return '<tr><td>'+Library.esc(x.jour)+'</td><td>'+x.nb+'</td><td>'+Library.money(x.total)+'</td></tr>'; }).join('') : '<tr><td colspan="3" class="text-center text-secondary-light">Aucune vente</td></tr>';
    document.getElementById('byProduct').innerHTML = (report.par_produit||[]).length ? report.par_produit.map(function(x){ return '<tr><td>'+Library.esc(x.produit||'-')+'</td><td>'+x.quantite+'</td><td>'+Library.money(x.ca)+'</td><td>'+Library.money(Number(x.ca||0)-Number(x.cogs||0))+'</td></tr>'; }).join('') : '<tr><td colspan="4" class="text-center text-secondary-light">Aucune vente</td></tr>';
    document.getElementById('byUser').innerHTML = (report.par_utilisateur||[]).length ? report.par_utilisateur.map(function(x){ return '<tr><td>'+Library.esc(x.utilisateur||'-')+'</td><td>'+x.nb+'</td><td>'+Library.money(x.total)+'</td></tr>'; }).join('') : '<tr><td colspan="3" class="text-center text-secondary-light">Aucune vente</td></tr>';
    document.getElementById('byMode').innerHTML = (report.par_mode||[]).length ? report.par_mode.map(function(x){ return '<tr><td>'+Library.esc(x.mode_paiement)+'</td><td>'+x.nb+'</td><td>'+Library.money(x.total)+'</td></tr>'; }).join('') : '<tr><td colspan="3" class="text-center text-secondary-light">Aucun paiement</td></tr>';
  } catch(e){ Library.toast('error', e.message); }
}
function exportReport(){
  if (!report) { Library.toast('warning','Affichez d\'abord le rapport'); return; }
  const s = report.resume, rows = [];
  rows.push(['Periode', report.periode.du, report.periode.au]);
  rows.push(['Chiffre d\'affaires', s.chiffre_affaires]);
  rows.push(['Retours', s.retours]);
  rows.push(['CA net', s.ca_net]);
  rows.push(['Cout des marchandises', s.cogs]);
  rows.push(['Marge brute', s.marge_brute]);
  rows.push(['Charges', s.charges]);
  rows.push(['Benefice net', s.benefice_net]);
  Library.downloadCSV('rapport-librairie-'+report.periode.du+'-'+report.periode.au+'.csv', ['Indicateur','Valeur'], rows);
}
document.getElementById('date_to').value = Library.today();
const d = new Date(); d.setDate(1);
document.getElementById('date_from').value = d.toISOString().slice(0,10);
loadReport();
</script>
<?php include __DIR__.'/_foot.php'; ?>
