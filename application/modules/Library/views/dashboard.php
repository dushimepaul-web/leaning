<?php $page_title = 'Tableau de bord Librairie'; $page_subtitle = 'Vue d\'ensemble'; include __DIR__.'/_head.php'; ?>
<div class="row g-3 mb-24">
  <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-secondary-light text-sm mb-6">Ventes du jour</p><h4 class="mb-2" id="kpiToday">0</h4><p class="text-sm text-secondary-light mb-0" id="kpiTodayNb">0 vente(s)</p></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-secondary-light text-sm mb-6">Ventes du mois</p><h4 class="mb-2" id="kpiMonth">0</h4><p class="text-sm text-secondary-light mb-0" id="kpiMonthNb">0 vente(s)</p></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-secondary-light text-sm mb-6">Marge brute du mois</p><h4 class="mb-2" id="kpiMargin">0</h4><p class="text-sm text-secondary-light mb-0" id="kpiNet">Benefice net : 0</p></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><p class="text-secondary-light text-sm mb-6">Valeur du stock</p><h4 class="mb-2" id="kpiStock">0</h4><p class="text-sm text-secondary-light mb-0" id="kpiStockInfo">0 produit(s)</p></div></div></div>
</div>
<div class="row g-3 mb-24">
  <div class="col-md-3"><div class="card"><div class="card-body d-flex justify-content-between"><div><p class="text-secondary-light text-sm mb-6">Alertes stock</p><h4 class="mb-0" id="kpiLow">0</h4></div><i class="ri-alarm-warning-line text-3xl text-warning"></i></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body d-flex justify-content-between"><div><p class="text-secondary-light text-sm mb-6">Appro. en attente</p><h4 class="mb-0" id="kpiPending">0</h4></div><i class="ri-truck-line text-3xl text-info"></i></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body d-flex justify-content-between"><div><p class="text-secondary-light text-sm mb-6">Ventes non soldees</p><h4 class="mb-0" id="kpiUnpaid">0</h4></div><i class="ri-money-dollar-circle-line text-3xl text-danger"></i></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body d-flex justify-content-between"><div><p class="text-secondary-light text-sm mb-6">Raccourcis</p><div class="d-flex flex-wrap gap-2"><a href="<?= base_url('Library_sales') ?>" class="btn btn-sm btn-primary-600">Vendre</a><a href="<?= base_url('Library_purchases') ?>" class="btn btn-sm btn-outline-primary">Approvisionner</a></div></div></div></div></div>
</div>
<div class="row g-3">
  <div class="col-lg-7"><div class="card h-100"><div class="card-header d-flex justify-content-between"><h6 class="mb-0">Dernieres ventes</h6><a href="<?= base_url('Library_sales') ?>" class="text-sm">Tout voir</a></div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>N&deg;</th><th>Client</th><th>Date</th><th>Total</th><th>Statut</th></tr></thead><tbody id="recentBody"><tr><td colspan="5" class="text-center text-secondary-light">Chargement...</td></tr></tbody></table></div></div></div></div>
  <div class="col-lg-5"><div class="card h-100"><div class="card-header"><h6 class="mb-0">Alertes de stock</h6></div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Produit</th><th>Stock</th><th>Mini</th></tr></thead><tbody id="lowBody"><tr><td colspan="3" class="text-center text-secondary-light">Chargement...</td></tr></tbody></table></div></div></div></div>
  <div class="col-lg-7"><div class="card"><div class="card-header"><h6 class="mb-0">Top produits du mois</h6></div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Produit</th><th>Quantite</th><th>Montant</th></tr></thead><tbody id="topBody"><tr><td colspan="3" class="text-center text-secondary-light">Chargement...</td></tr></tbody></table></div></div></div></div>
  <div class="col-lg-5"><div class="card"><div class="card-header"><h6 class="mb-0">Synthese du mois</h6></div><div class="card-body"><div class="d-flex justify-content-between py-2 border-bottom"><span>Ventes</span><strong id="sumSales">0</strong></div><div class="d-flex justify-content-between py-2 border-bottom"><span>Retours</span><strong id="sumReturns">0</strong></div><div class="d-flex justify-content-between py-2 border-bottom"><span>Cout des marchandises</span><strong id="sumCogs">0</strong></div><div class="d-flex justify-content-between py-2 border-bottom"><span>Charges</span><strong id="sumExpenses">0</strong></div><div class="d-flex justify-content-between py-2"><span class="fw-semibold">Benefice net</span><strong id="sumNet">0</strong></div></div></div></div>
</div>
<script>
(async function(){
  try {
    const r = await Library.api('api/library/dashboard');
    const d = r.data;
    document.getElementById('kpiToday').textContent = Library.money(d.aujourdhui.total);
    document.getElementById('kpiTodayNb').textContent = d.aujourdhui.nb + ' vente(s)';
    document.getElementById('kpiMonth').textContent = Library.money(d.mois.ventes);
    document.getElementById('kpiMonthNb').textContent = d.mois.nb + ' vente(s)';
    document.getElementById('kpiMargin').textContent = Library.money(d.mois.marge_brute);
    document.getElementById('kpiNet').textContent = 'Benefice net : ' + Library.money(d.mois.benefice_net);
    document.getElementById('kpiStock').textContent = Library.money(d.stock.valeur);
    document.getElementById('kpiStockInfo').textContent = d.stock.produits + ' produit(s)';
    document.getElementById('kpiLow').textContent = d.stock.alertes;
    document.getElementById('kpiPending').textContent = d.approvisionnements_en_attente;
    document.getElementById('kpiUnpaid').textContent = d.ventes_non_soldees;
    document.getElementById('sumSales').textContent = Library.money(d.mois.ventes);
    document.getElementById('sumReturns').textContent = Library.money(d.mois.retours);
    document.getElementById('sumCogs').textContent = Library.money(d.mois.cogs);
    document.getElementById('sumExpenses').textContent = Library.money(d.mois.charges);
    document.getElementById('sumNet').textContent = Library.money(d.mois.benefice_net);
    const recent = d.recentes || [];
    document.getElementById('recentBody').innerHTML = recent.length ? recent.map(function(s){
      return '<tr><td>'+Library.esc(s.numero_vente)+'</td><td>'+Library.esc(s.client)+'</td><td>'+Library.esc((s.date_vente||'').slice(0,10))+'</td><td>'+Library.money(s.total)+'</td><td>'+Library.badge(s.statut)+'</td></tr>';
    }).join('') : '<tr><td colspan="5" class="text-center text-secondary-light">Aucune vente</td></tr>';
    const low = d.alertes_stock || [];
    document.getElementById('lowBody').innerHTML = low.length ? low.map(function(p){
      return '<tr><td>'+Library.esc(p.libelle)+'</td><td>'+p.stock_actuel+'</td><td>'+p.stock_mini+'</td></tr>';
    }).join('') : '<tr><td colspan="3" class="text-center text-secondary-light">Aucune alerte</td></tr>';
    const top = d.top_produits || [];
    document.getElementById('topBody').innerHTML = top.length ? top.map(function(p){
      return '<tr><td>'+Library.esc(p.produit)+'</td><td>'+Library.number(p.quantite)+'</td><td>'+Library.money(p.montant)+'</td></tr>';
    }).join('') : '<tr><td colspan="3" class="text-center text-secondary-light">Aucune vente ce mois</td></tr>';
  } catch (e) { Library.toast('error', e.message); }
})();
</script>
<?php include __DIR__.'/_foot.php'; ?>
