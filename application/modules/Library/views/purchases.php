<?php
$page_title = 'Approvisionnements';
$page_subtitle = 'Approvisionnements';
$page_actions = '<button type="button" class="btn btn-primary-600 d-flex align-items-center gap-2" onclick="openPurchase()"><i class="ri-add-line"></i> Nouvel approvisionnement</button>';
include __DIR__.'/_head.php';
?>
<div class="card">
  <div class="card-body p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-20 border-bottom">
      <div class="d-flex flex-wrap gap-2">
        <input type="date" class="form-control form-control-sm" id="date_from" style="width:160px">
        <input type="date" class="form-control form-control-sm" id="date_to" style="width:160px">
        <select class="form-select form-select-sm" id="statut" style="width:150px"><option value="">Tous statuts</option><option value="brouillon">Brouillon</option><option value="validee">Valide</option><option value="annulee">Annule</option></select>
        <button class="btn btn-sm btn-outline-primary" onclick="loadPurchases()">Filtrer</button>
      </div>
      <button class="btn btn-sm btn-outline-secondary" onclick="loadPurchases()"><i class="ri-refresh-line"></i> Actualiser</button>
    </div>
    <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Bon</th><th>Fournisseur</th><th>Date</th><th>Lignes</th><th>Total</th><th>Statut</th><th class="text-end">Actions</th></tr></thead><tbody id="body"><tr><td colspan="7" class="text-center text-secondary-light py-4">Chargement...</td></tr></tbody></table></div>
  </div>
</div>

<div class="modal fade" id="purchaseModal" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content">
  <div class="modal-header"><h6 class="mb-0">Nouvel approvisionnement</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form id="purchaseForm"><div class="modal-body">
    <div class="row g-3 mb-3">
      <div class="col-md-4"><label class="form-label">Fournisseur</label><select class="form-select" id="id_supplier"><option value="">-- Aucun --</option><?php foreach ($suppliers as $s): ?><option value="<?= (int)$s['id_supplier'] ?>" <?= ($s['code'] ?? '') === 'FOURN-DEFAUT' ? 'selected' : '' ?>><?= html_escape($s['nom']) ?></option><?php endforeach; ?></select></div>
      <div class="col-md-4"><label class="form-label">Reference facture <span class="text-secondary-light">(auto si vide)</span></label><input class="form-control" id="reference" placeholder="Auto si vide"></div>
      <div class="col-md-4"><label class="form-label">Date</label><input type="datetime-local" class="form-control" id="date_purchase"></div>
    </div>
    <div class="border rounded" style="overflow:visible">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="min-width:240px">Produit</th>
            <th style="width:110px" class="text-center">Quantité</th>
            <th style="width:170px" class="text-end">Prix achat unit.</th>
            <th style="width:160px" class="text-end">Total</th>
            <th style="width:52px"></th>
          </tr>
        </thead>
        <tbody id="lines"></tbody>
      </table>
    </div>
    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addLine()"><i class="ri-add-line"></i> Ajouter une ligne</button>
    <div class="row g-3 mt-1">
      <div class="col-md-6"><label class="form-label">Notes</label><textarea class="form-control" id="notes" rows="2"></textarea></div>
      <div class="col-md-3"><label class="form-label">Statut</label><select class="form-select" id="statut_create"><option value="brouillon">Brouillon</option><option value="validee">Valide (entree en stock)</option></select></div>
      <div class="col-md-3 d-flex flex-column justify-content-end">
        <div class="border rounded bg-light p-3">
          <div class="d-flex justify-content-between mb-2"><span class="text-secondary-light">Sous-total</span><strong id="sousTotal">0</strong></div>
          <div class="d-flex justify-content-between border-top pt-2"><span class="fw-semibold">Total</span><strong class="fs-5 text-primary-600" id="grandTotal">0</strong></div>
        </div>
      </div>
    </div>
  </div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary-600">Enregistrer</button></div></form>
</div></div></div>

<div class="modal fade" id="detailModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
  <div class="modal-header"><h6 class="mb-0" id="detailTitle">Detail</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body" id="detailBody"></div>
</div></div></div>

<script>
let products = [], purchases = [];
Library.combo(document.getElementById('id_supplier'), 'Tapez pour rechercher un fournisseur...');
async function loadProducts(){
  const r = await Library.api('api/library/stock');
  products = r.data || [];
}
function addLine(){
  const tr = document.createElement('tr');
  tr.innerHTML = '<td><select class="form-select form-select-sm line-product"><option value="" data-cost="0">-- Selectionner un produit --</option>'+products.map(function(p){ return '<option value="'+p.id_produit+'" data-cost="'+p.prix_achat+'">'+Library.esc(p.libelle)+'</option>'; }).join('')+'</select></td>'+
    '<td><input type="number" min="1" step="1" value="1" class="form-control form-control-sm text-center line-qty"></td>'+
    '<td><input type="number" min="0" step="0.01" class="form-control form-control-sm text-end line-cost"></td>'+
    '<td class="line-total text-end fw-semibold text-nowrap">0</td>'+
    '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer la ligne" onclick="this.closest(\'tr\').remove(); recalc();"><i class="ri-delete-bin-line"></i></button></td>';
  document.getElementById('lines').appendChild(tr);
  const sel = tr.querySelector('.line-product'); const cost = tr.querySelector('.line-cost');
  Library.combo(sel, 'Tapez pour rechercher un produit...');
  cost.value = sel.value && sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].dataset.cost : '';
  sel.addEventListener('change', function(){
    const o = this.options[this.selectedIndex];
    cost.value = this.value && o ? (o.dataset.cost || 0) : '';
    recalc();
  });
  tr.querySelector('.line-qty').addEventListener('input', recalc);
  cost.addEventListener('input', recalc);
  recalc();
}
function recalc(){
  let st = 0;
  document.querySelectorAll('#lines tr').forEach(function(tr){
    const q = Number(tr.querySelector('.line-qty').value || 0);
    const c = Number(tr.querySelector('.line-cost').value || 0);
    const t = q * c; st += t;
    tr.querySelector('.line-total').textContent = Library.money(t);
  });
  document.getElementById('sousTotal').textContent = Library.money(st);
  document.getElementById('grandTotal').textContent = Library.money(st);
}
async function loadPurchases(){
  try {
    const p = new URLSearchParams();
    ['date_from','date_to','statut'].forEach(function(id){ const v = document.getElementById(id).value; if (v) p.set(id, v); });
    const r = await Library.api('api/library/purchases' + (p.toString() ? '?' + p : ''));
    purchases = r.data || [];
    document.getElementById('body').innerHTML = purchases.length ? purchases.map(function(x){
      let actions = '<button class="btn btn-sm btn-outline-secondary me-1" data-view="'+Library.esc(x.uuid)+'"><i class="ri-eye-line"></i></button>';
      if (x.statut === 'brouillon') actions += '<button class="btn btn-sm btn-outline-success me-1" data-validate="'+Library.esc(x.uuid)+'">Valider</button><button class="btn btn-sm btn-outline-danger me-1" data-cancel="'+Library.esc(x.uuid)+'">Annuler</button>';
      if (x.statut !== 'validee') actions += '<button class="btn btn-sm btn-outline-danger" data-del="'+Library.esc(x.uuid)+'"><i class="ri-delete-bin-line"></i></button>';
      return '<tr><td><strong>'+Library.esc(x.numero_bon)+'</strong><br><small class="text-secondary-light">'+Library.esc(x.reference||'')+'</small></td><td>'+Library.esc(x.fournisseur||'-')+'</td><td>'+Library.esc((x.date_purchase||'').slice(0,10))+'</td><td>'+x.nb_lignes+'</td><td>'+Library.money(x.total)+'</td><td>'+Library.badge(x.statut)+'</td><td class="text-end">'+actions+'</td></tr>';
    }).join('') : '<tr><td colspan="7" class="text-center text-secondary-light py-4">Aucun approvisionnement</td></tr>';
  } catch(e){ Library.toast('error', e.message); }
}
async function openPurchase(){
  document.getElementById('purchaseForm').reset();
  document.getElementById('id_supplier').dispatchEvent(new Event('change'));
  document.getElementById('lines').innerHTML = '';
  document.getElementById('date_purchase').value = new Date().toISOString().slice(0,16);
  try { await loadProducts(); } catch(e){ Library.toast('error', e.message); }
  addLine(); recalc();
  bootstrap.Modal.getOrCreateInstance(document.getElementById('purchaseModal')).show();
}
document.getElementById('purchaseForm').addEventListener('submit', async function(ev){
  ev.preventDefault();
  const items = [];
  document.querySelectorAll('#lines tr').forEach(function(tr){
    const id = Number(tr.querySelector('.line-product').value);
    const q = Number(tr.querySelector('.line-qty').value);
    const c = Number(tr.querySelector('.line-cost').value);
    if (id && q > 0) items.push({ id_produit: id, quantite: q, prix_achat_unitaire: c });
  });
  if (!items.length) { Library.toast('warning','Ajoutez une ligne'); return; }
  try {
    await Library.api('api/library/purchases/create', {
      id_supplier: document.getElementById('id_supplier').value || null,
      reference: document.getElementById('reference').value,
      date_purchase: document.getElementById('date_purchase').value,
      notes: document.getElementById('notes').value,
      statut: document.getElementById('statut_create').value,
      items: items
    });
    bootstrap.Modal.getInstance(document.getElementById('purchaseModal')).hide();
    Library.toast('success','Approvisionnement enregistre'); loadPurchases();
  } catch(e){ Library.toast('error', e.message); }
});
async function viewPurchase(uuid){
  try {
    const r = await Library.api('api/library/purchases/' + uuid); const p = r.data;
    document.getElementById('detailTitle').textContent = p.numero_bon;
    document.getElementById('detailBody').innerHTML = '<p class="mb-2"><strong>Fournisseur :</strong> '+Library.esc(p.fournisseur||'-')+'</p><p class="mb-2"><strong>Date :</strong> '+Library.esc(p.date_purchase)+'</p><div class="table-responsive"><table class="table"><thead><tr><th>Produit</th><th>Qte</th><th>Prix achat</th><th>Total</th></tr></thead><tbody>'+(p.items||[]).map(function(i){ return '<tr><td>'+Library.esc(i.produit)+'</td><td>'+i.quantite+'</td><td>'+Library.money(i.prix_achat_unitaire)+'</td><td>'+Library.money(i.total_ligne)+'</td></tr>'; }).join('')+'</tbody></table></div><div class="text-end"><div>Sous-total : <strong>'+Library.money(p.sous_total)+'</strong></div><div>Total : <strong>'+Library.money(p.total)+'</strong></div></div>';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('detailModal')).show();
  } catch(e){ Library.toast('error', e.message); }
}
document.getElementById('body').addEventListener('click', async function(ev){
  const v = ev.target.closest('[data-view]'), val = ev.target.closest('[data-validate]'), can = ev.target.closest('[data-cancel]'), del = ev.target.closest('[data-del]');
  if (v) viewPurchase(v.dataset.view);
  if (val && await Library.confirmAction('Valider cet approvisionnement ?', 'Le stock sera augmente.')) {
    try { await Library.api('api/library/purchases/'+val.dataset.validate+'/update',{statut:'validee'}); Library.toast('success','Approvisionnement valide'); loadPurchases(); } catch(e){ Library.toast('error',e.message); }
  }
  if (can && await Library.confirmAction('Annuler cet approvisionnement ?')) {
    try { await Library.api('api/library/purchases/'+can.dataset.cancel+'/update',{statut:'annulee'}); Library.toast('success','Approvisionnement annule'); loadPurchases(); } catch(e){ Library.toast('error',e.message); }
  }
  if (del && await Library.confirmAction('Supprimer cet approvisionnement ?')) {
    try { await Library.api('api/library/purchases/'+del.dataset.del+'/delete'); Library.toast('success','Supprime'); loadPurchases(); } catch(e){ Library.toast('error',e.message); }
  }
});
(async function(){ try { await loadProducts(); await loadPurchases(); } catch(e){ Library.toast('error', e.message); } })();
</script>
<?php include __DIR__.'/_foot.php'; ?>
