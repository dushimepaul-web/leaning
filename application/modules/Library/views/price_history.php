<?php
$page_title = 'Historique des prix';
$page_subtitle = 'Historique des prix';
include __DIR__.'/_head.php';
?>
<div class="card">
  <div class="card-body p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-20 border-bottom">
      <div class="d-flex flex-wrap gap-2">
        <select class="form-select form-select-sm" id="produit" style="width:220px"><option value="">Tous produits</option></select>
        <select class="form-select form-select-sm" id="source" style="width:160px"><option value="">Toutes sources</option><option value="approvisionnement">Approvisionnement</option><option value="vente">Vente</option><option value="manuel">Manuel</option></select>
        <input type="date" class="form-control form-control-sm" id="date_from" style="width:160px">
        <input type="date" class="form-control form-control-sm" id="date_to" style="width:160px">
        <button class="btn btn-sm btn-outline-primary" onclick="loadHistory()">Filtrer</button>
      </div>
      <button class="btn btn-sm btn-outline-secondary" onclick="loadHistory()"><i class="ri-refresh-line"></i> Actualiser</button>
    </div>
    <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Date</th><th>Produit</th><th>Prix achat</th><th>Prix vente</th><th>Variation</th><th>Source</th><th>Utilisateur</th></tr></thead><tbody id="body"><tr><td colspan="7" class="text-center text-secondary-light py-4">Chargement...</td></tr></tbody></table></div>
  </div>
</div>
<script>
Library.combo(document.getElementById('produit'), 'Tapez pour rechercher un produit...');
async function loadProducts(){
  try {
    const r = await Library.api('api/library/stock');
    document.getElementById('produit').innerHTML = '<option value="">Tous produits</option>' + (r.data||[]).map(function(p){ return '<option value="'+p.id_produit+'">'+Library.esc(p.libelle)+'</option>'; }).join('');
  } catch(e){ Library.toast('error', e.message); }
}
async function loadHistory(){
  try {
    const p = new URLSearchParams();
    [['id_produit','produit'],['source','source'],['date_from','date_from'],['date_to','date_to']].forEach(function(x){ const v = document.getElementById(x[1]).value; if (v) p.set(x[0], v); });
    const r = await Library.api('api/library/price-history' + (p.toString() ? '?' + p : ''));
    const rows = r.data || [];
    document.getElementById('body').innerHTML = rows.length ? rows.map(function(h){
      const oldP = Number(h.ancien_prix_achat), newP = Number(h.nouveau_prix_achat);
      const diff = newP - oldP;
      const variation = diff === 0 ? '-' : (diff > 0 ? '<span class="text-danger">+' + Library.money(diff) + '</span>' : '<span class="text-success">' + Library.money(diff) + '</span>');
      const oldV = h.ancien_prix_vente === null ? '-' : Library.money(h.ancien_prix_vente);
      const newV = h.nouveau_prix_vente === null ? '-' : Library.money(h.nouveau_prix_vente);
      return '<tr><td>'+Library.esc((h.date_changement||'').slice(0,16).replace('T',' '))+'</td><td>'+Library.esc(h.produit||'-')+'</td><td>'+Library.money(h.ancien_prix_achat)+' &rarr; '+Library.money(h.nouveau_prix_achat)+'</td><td>'+oldV+' &rarr; '+newV+'</td><td>'+variation+'</td><td>'+Library.esc(h.source||'-')+'</td><td>'+Library.esc(h.utilisateur||'-')+'</td></tr>';
    }).join('') : '<tr><td colspan="7" class="text-center text-secondary-light py-4">Aucun historique</td></tr>';
  } catch(e){ Library.toast('error', e.message); }
}
(async function(){ await loadProducts(); loadHistory(); })();
</script>
<?php include __DIR__.'/_foot.php'; ?>
