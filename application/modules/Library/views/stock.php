<?php
$page_title = 'Stock Librairie';
$page_subtitle = 'Stock';
$page_actions = '<a href="'.base_url('Produits?add=1').'" class="btn btn-primary-600 d-flex align-items-center gap-2"><i class="ri-add-line"></i> Ajouter un produit</a>
<a href="'.base_url('Stock_Categories?add=1').'" class="btn btn-outline-primary d-flex align-items-center gap-2"><i class="ri-price-tag-3-line"></i> Ajouter un type</a>
<button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2" onclick="openInventory()"><i class="ri-file-list-3-line"></i> Inventaire</button>';
include __DIR__.'/_head.php';
?>
<ul class="nav nav-tabs mb-3" role="tablist">
  <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabProducts" type="button">Produits</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabMovements" type="button" onclick="loadMovements()">Mouvements</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabInventories" type="button" onclick="loadInventories()">Inventaires</button></li>
</ul>
<div class="tab-content">
  <div class="tab-pane fade show active" id="tabProducts">
    <div class="card"><div class="card-body p-0">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-20 border-bottom">
        <div class="d-flex flex-wrap gap-2">
          <input type="text" class="form-control form-control-sm" id="q" placeholder="Rechercher..." style="width:220px">
          <select class="form-select form-select-sm" id="categorie" style="width:200px"><option value="">Toutes categories</option><?php foreach ($categories as $c): ?><option value="<?= (int)$c['id_categorie'] ?>"><?= html_escape($c['libelle']) ?></option><?php endforeach; ?></select>
          <button class="btn btn-sm btn-outline-primary" onclick="loadProducts()">Filtrer</button>
        </div>
        <a href="<?= base_url('Produits') ?>" class="btn btn-sm btn-outline-secondary"><i class="ri-external-link-line"></i> Gerer les produits</a>
      </div>
      <div class="table-responsive"><table class="table mb-0"><thead><tr><th>#</th><th>Produit</th><th>Categorie</th><th>Stock</th><th>Mini</th><th>Prix achat</th><th>Prix vente</th><th>Valeur</th><th>Etat</th><th class="text-end">Actions</th></tr></thead><tbody id="productsBody"><tr><td colspan="10" class="text-center text-secondary-light py-4">Chargement...</td></tr></tbody></table></div>
    </div></div>
  </div>
  <div class="tab-pane fade" id="tabMovements">
    <div class="card"><div class="card-body p-0">
      <div class="d-flex flex-wrap gap-2 p-20 border-bottom">
        <select class="form-select form-select-sm" id="mvt_product" style="width:220px"><option value="">Tous produits</option></select>
        <select class="form-select form-select-sm" id="mvt_type" style="width:130px"><option value="">Type</option><option value="entree">Entree</option><option value="sortie">Sortie</option></select>
        <select class="form-select form-select-sm" id="mvt_source" style="width:170px"><option value="">Source</option><option value="approvisionnement">Approvisionnement</option><option value="vente">Vente</option><option value="retour">Retour</option><option value="inventaire">Inventaire</option><option value="correction">Correction</option><option value="annulation">Annulation</option></select>
        <input type="date" class="form-control form-control-sm" id="mvt_from" style="width:150px">
        <input type="date" class="form-control form-control-sm" id="mvt_to" style="width:150px">
        <button class="btn btn-sm btn-outline-primary" onclick="loadMovements()">Filtrer</button>
      </div>
      <div class="table-responsive"><table class="table mb-0"><thead><tr><th>#</th><th>Date</th><th>Produit</th><th>Type</th><th>Source</th><th>Quantite</th><th>Prix achat</th><th>Motif</th><th>Utilisateur</th></tr></thead><tbody id="movementsBody"><tr><td colspan="9" class="text-center text-secondary-light py-4">Ouvrez l'onglet pour charger</td></tr></tbody></table></div>
    </div></div>
  </div>
  <div class="tab-pane fade" id="tabInventories">
    <div class="card"><div class="card-body p-0">
      <div class="d-flex justify-content-end p-20 border-bottom"><button class="btn btn-sm btn-outline-secondary" onclick="loadInventories()"><i class="ri-refresh-line"></i> Actualiser</button></div>
      <div class="table-responsive"><table class="table mb-0"><thead><tr><th>#</th><th>Numero</th><th>Date</th><th>Lignes</th><th>Ecarts</th><th>Statut</th><th>Utilisateur</th><th class="text-end">Actions</th></tr></thead><tbody id="invBody"><tr><td colspan="8" class="text-center text-secondary-light py-4">Ouvrez l'onglet pour charger</td></tr></tbody></table></div>
    </div></div>
  </div>
</div>

<div class="modal fade" id="adjustModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
  <div class="modal-header"><h6 class="mb-0">Ajuster le stock</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form id="adjustForm"><div class="modal-body">
    <input type="hidden" id="adj_product">
    <p class="mb-3">Produit : <strong id="adj_name"></strong> <span class="text-secondary-light">(stock actuel : <span id="adj_stock"></span>)</span></p>
    <div class="mb-3"><label class="form-label">Quantite (+ entree / - sortie) *</label><input type="number" step="1" class="form-control" id="adj_qty" required></div>
    <div class="mb-3"><label class="form-label">Motif *</label><textarea class="form-control" id="adj_motif" rows="2" required></textarea></div>
  </div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary-600">Ajuster</button></div></form>
</div></div></div>

<div class="modal fade" id="inventoryModal" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content">
  <div class="modal-header"><h6 class="mb-0">Nouvel inventaire</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form id="inventoryForm"><div class="modal-body">
    <div class="row g-3 mb-3">
      <div class="col-md-3"><label class="form-label">Date</label><input type="date" class="form-control" id="inv_date"></div>
      <div class="col-md-9"><label class="form-label">Notes</label><input class="form-control" id="inv_notes"></div>
    </div>
    <div class="table-responsive border rounded" style="max-height:400px;overflow:auto"><table class="table mb-0"><thead class="sticky-top bg-white"><tr><th>Produit</th><th>Theorique</th><th style="width:140px">Stock reel</th><th>Ecart</th></tr></thead><tbody id="invLines"></tbody></table></div>
  </div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary-600">Enregistrer (brouillon)</button></div></form>
</div></div></div>

<div class="modal fade" id="invDetailModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
  <div class="modal-header"><h6 class="mb-0" id="invDetailTitle">Inventaire</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body" id="invDetailBody"></div>
</div></div></div>

<script>
let products = [], inventories = [];
Library.combo(document.getElementById('mvt_product'), 'Tapez pour rechercher un produit...');
async function loadProducts(){
  try {
    const p = new URLSearchParams();
    if (document.getElementById('q').value.trim()) p.set('q', document.getElementById('q').value.trim());
    if (document.getElementById('categorie').value) p.set('categorie', document.getElementById('categorie').value);
    const r = await Library.api('api/library/stock' + (p.toString() ? '?' + p : ''));
    products = r.data || [];
    document.getElementById('productsBody').innerHTML = products.length ? products.map(function(x, i){
      return '<tr><td>'+(i+1)+'</td><td><strong>'+Library.esc(x.libelle)+'</strong><br><small class="text-secondary-light">'+Library.esc(x.editeur||'')+'</small></td><td>'+Library.esc(x.categorie||'-')+'</td><td>'+x.stock_actuel+'</td><td>'+x.stock_mini+'</td><td>'+Library.money(x.prix_achat)+'</td><td>'+Library.money(x.prix_unitaire)+'</td><td>'+Library.money(x.valeur_stock)+'</td><td>'+(Number(x.alerte)?'<span class="badge bg-warning-100 text-warning-600">Alerte</span>':'<span class="badge bg-success-100 text-success-600">OK</span>')+'</td><td class="text-end"><button class="btn btn-sm btn-outline-primary" data-adjust="'+x.id_produit+'"><i class="ri-equalizer-line"></i></button></td></tr>';
    }).join('') : '<tr><td colspan="10" class="text-center text-secondary-light py-4">Aucun produit</td></tr>';
    const sel = document.getElementById('mvt_product');
    const cur = sel.value;
    sel.innerHTML = '<option value="">Tous produits</option>' + products.map(function(x){ return '<option value="'+x.id_produit+'">'+Library.esc(x.libelle)+'</option>'; }).join('');
    sel.value = cur;
  } catch(e){ Library.toast('error', e.message); }
}
function openAdjust(id){
  const p = products.find(function(x){ return String(x.id_produit) === String(id); }); if (!p) return;
  document.getElementById('adjustForm').reset();
  document.getElementById('adj_product').value = p.id_produit;
  document.getElementById('adj_name').textContent = p.libelle;
  document.getElementById('adj_stock').textContent = p.stock_actuel;
  bootstrap.Modal.getOrCreateInstance(document.getElementById('adjustModal')).show();
}
document.getElementById('adjustForm').addEventListener('submit', async function(ev){
  ev.preventDefault();
  try {
    await Library.api('api/library/stock/create', {
      action: 'adjust', id_produit: Number(document.getElementById('adj_product').value),
      quantite: Number(document.getElementById('adj_qty').value), motif: document.getElementById('adj_motif').value
    });
    bootstrap.Modal.getInstance(document.getElementById('adjustModal')).hide();
    Library.toast('success','Stock ajuste'); loadProducts();
  } catch(e){ Library.toast('error', e.message); }
});
async function loadMovements(){
  try {
    const p = new URLSearchParams();
    [['id_produit','mvt_product'],['type','mvt_type'],['source_type','mvt_source'],['date_from','mvt_from'],['date_to','mvt_to']].forEach(function(x){ const v = document.getElementById(x[1]).value; if (v) p.set(x[0], v); });
    const r = await Library.api('api/library/stock/movements' + (p.toString() ? '?' + p : ''));
    const rows = r.data || [];
    document.getElementById('movementsBody').innerHTML = rows.length ? rows.map(function(m, i){
      return '<tr><td>'+(i+1)+'</td><td>'+Library.esc((m.date_mvt||'').slice(0,16).replace('T',' '))+'</td><td>'+Library.esc(m.produit||'-')+'</td><td>'+(m.type==='entree'?'<span class="text-success">Entree</span>':'<span class="text-danger">Sortie</span>')+'</td><td>'+Library.esc(m.source_type||'-')+'</td><td>'+m.quantite+'</td><td>'+Library.money(m.prix_achat_unitaire)+'</td><td>'+Library.esc(m.motif||'')+'</td><td>'+Library.esc(m.utilisateur||'-')+'</td></tr>';
    }).join('') : '<tr><td colspan="9" class="text-center text-secondary-light py-4">Aucun mouvement</td></tr>';
  } catch(e){ Library.toast('error', e.message); }
}
async function loadInventories(){
  try {
    const r = await Library.api('api/library/stock/inventories'); inventories = r.data || [];
    document.getElementById('invBody').innerHTML = inventories.length ? inventories.map(function(x, i){
      let actions = '<button class="btn btn-sm btn-outline-secondary me-1" data-inv-view="'+Library.esc(x.uuid)+'"><i class="ri-eye-line"></i></button>';
      if (x.statut === 'brouillon') actions += '<button class="btn btn-sm btn-outline-success me-1" data-inv-valid="'+Library.esc(x.uuid)+'">Valider</button><button class="btn btn-sm btn-outline-danger me-1" data-inv-cancel="'+Library.esc(x.uuid)+'">Annuler</button><button class="btn btn-sm btn-outline-danger" data-inv-del="'+Library.esc(x.uuid)+'"><i class="ri-delete-bin-line"></i></button>';
      return '<tr><td>'+(i+1)+'</td><td><strong>'+Library.esc(x.numero_inventaire)+'</strong></td><td>'+Library.esc(x.date_inventaire)+'</td><td>'+x.nb_lignes+'</td><td>'+x.nb_ecarts+'</td><td>'+Library.badge(x.statut)+'</td><td>'+Library.esc(x.utilisateur||'-')+'</td><td class="text-end">'+actions+'</td></tr>';
    }).join('') : '<tr><td colspan="8" class="text-center text-secondary-light py-4">Aucun inventaire</td></tr>';
  } catch(e){ Library.toast('error', e.message); }
}
function openInventory(){
  if (!products.length) { Library.toast('warning','Aucun produit disponible'); return; }
  document.getElementById('inventoryForm').reset();
  document.getElementById('inv_date').value = Library.today();
  document.getElementById('invLines').innerHTML = products.map(function(p){
    return '<tr data-id="'+p.id_produit+'"><td>'+Library.esc(p.libelle)+'</td><td class="theo">'+p.stock_actuel+'</td><td><input type="number" min="0" class="form-control form-control-sm real" value="'+p.stock_actuel+'"></td><td class="ecart">0</td></tr>';
  }).join('');
  document.querySelectorAll('#invLines .real').forEach(function(inp){
    inp.addEventListener('input', function(){
      const tr = this.closest('tr'); const e = Number(this.value || 0) - Number(tr.querySelector('.theo').textContent || 0);
      tr.querySelector('.ecart').textContent = e > 0 ? '+' + e : e;
    });
  });
  bootstrap.Modal.getOrCreateInstance(document.getElementById('inventoryModal')).show();
}
document.getElementById('inventoryForm').addEventListener('submit', async function(ev){
  ev.preventDefault();
  const items = [];
  document.querySelectorAll('#invLines tr').forEach(function(tr){
    items.push({ id_produit: Number(tr.dataset.id), stock_reel: Number(tr.querySelector('.real').value || 0) });
  });
  try {
    await Library.api('api/library/stock/create', { action: 'inventory', date_inventaire: document.getElementById('inv_date').value, notes: document.getElementById('inv_notes').value, items: items });
    bootstrap.Modal.getInstance(document.getElementById('inventoryModal')).hide();
    Library.toast('success','Inventaire cree'); loadInventories();
  } catch(e){ Library.toast('error', e.message); }
});
async function viewInventory(uuid){
  try {
    const r = await Library.api('api/library/stock/inventories/'+uuid); const x = r.data;
    document.getElementById('invDetailTitle').textContent = x.numero_inventaire;
    document.getElementById('invDetailBody').innerHTML = '<div class="table-responsive"><table class="table"><thead><tr><th>Produit</th><th>Theorique</th><th>Reel</th><th>Ecart</th></tr></thead><tbody>'+(x.items||[]).map(function(i){ const e = Number(i.ecart); return '<tr><td>'+Library.esc(i.produit)+'</td><td>'+i.stock_theorique+'</td><td>'+i.stock_reel+'</td><td class="'+(e>0?'text-success':(e<0?'text-danger':''))+'">'+(e>0?'+':'')+e+'</td></tr>'; }).join('')+'</tbody></table></div>';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('invDetailModal')).show();
  } catch(e){ Library.toast('error', e.message); }
}
document.getElementById('productsBody').addEventListener('click', function(ev){ const b = ev.target.closest('[data-adjust]'); if (b) openAdjust(b.dataset.adjust); });
document.getElementById('invBody').addEventListener('click', async function(ev){
  const v = ev.target.closest('[data-inv-view]'), va = ev.target.closest('[data-inv-valid]'), ca = ev.target.closest('[data-inv-cancel]'), de = ev.target.closest('[data-inv-del]');
  if (v) viewInventory(v.dataset.invView);
  if (va && await Library.confirmAction('Valider cet inventaire ?','Le stock sera ajuste selon les ecarts.')) {
    try { await Library.api('api/library/stock/'+va.dataset.invValid+'/update',{statut:'valide'}); Library.toast('success','Inventaire valide'); loadInventories(); loadProducts(); } catch(e){ Library.toast('error',e.message); }
  }
  if (ca && await Library.confirmAction('Annuler cet inventaire ?')) {
    try { await Library.api('api/library/stock/'+ca.dataset.invCancel+'/update',{statut:'annule'}); Library.toast('success','Inventaire annule'); loadInventories(); } catch(e){ Library.toast('error',e.message); }
  }
  if (de && await Library.confirmAction('Supprimer cet inventaire ?')) {
    try { await Library.api('api/library/stock/'+de.dataset.invDel+'/delete'); Library.toast('success','Inventaire supprime'); loadInventories(); } catch(e){ Library.toast('error',e.message); }
  }
});
loadProducts();
</script>
<?php include __DIR__.'/_foot.php'; ?>
