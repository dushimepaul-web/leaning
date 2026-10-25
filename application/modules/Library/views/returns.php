<?php
$page_title = 'Retours';
$page_subtitle = 'Retours';
$page_actions = '<button type="button" class="btn btn-primary-600 d-flex align-items-center gap-2" onclick="openReturn()"><i class="ri-add-line"></i> Nouvelle demande</button>';
include __DIR__.'/_head.php';
?>
<div class="card">
  <div class="card-body p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-20 border-bottom">
      <div class="d-flex flex-wrap gap-2">
        <input type="date" class="form-control form-control-sm" id="date_from" style="width:160px">
        <input type="date" class="form-control form-control-sm" id="date_to" style="width:160px">
        <select class="form-select form-select-sm" id="statut" style="width:150px"><option value="">Tous statuts</option><option value="demande">Demande</option><option value="validee">Validee</option><option value="refusee">Refusee</option><option value="remboursee">Remboursee</option></select>
        <button class="btn btn-sm btn-outline-primary" onclick="loadReturns()">Filtrer</button>
      </div>
      <button class="btn btn-sm btn-outline-secondary" onclick="loadReturns()"><i class="ri-refresh-line"></i> Actualiser</button>
    </div>
    <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Retour</th><th>Vente</th><th>Client</th><th>Type</th><th>Motif</th><th>Montant</th><th>Statut</th><th class="text-end">Actions</th></tr></thead><tbody id="body"><tr><td colspan="8" class="text-center text-secondary-light py-4">Chargement...</td></tr></tbody></table></div>
  </div>
</div>

<div class="modal fade" id="returnModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
  <div class="modal-header"><h6 class="mb-0">Nouvelle demande de retour</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form id="returnForm"><div class="modal-body">
    <div class="row g-3 mb-3">
      <div class="col-md-7"><label class="form-label">Vente *</label><select class="form-select" id="sale" required><option value="">-- Selectionner une vente --</option></select></div>
      <div class="col-md-5"><label class="form-label">Type</label><select class="form-select" id="type"><option value="retour">Retour</option><option value="annulation">Annulation</option><option value="remboursement">Remboursement</option></select></div>
      <div class="col-md-7"><label class="form-label">Motif *</label><input class="form-control" id="motif" required></div>
      <div class="col-md-5"><label class="form-label">Notes</label><input class="form-control" id="notes"></div>
    </div>
    <div class="table-responsive border rounded"><table class="table mb-0"><thead><tr><th>Produit</th><th>Achete</th><th>Deja retourne</th><th style="width:120px">A retourner</th><th>Prix unit.</th></tr></thead><tbody id="returnLines"><tr><td colspan="5" class="text-center text-secondary-light">Selectionnez une vente</td></tr></tbody></table></div>
    <div class="text-end mt-2">Montant estime : <strong id="returnTotal">0</strong></div>
  </div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary-600">Enregistrer la demande</button></div></form>
</div></div></div>

<div class="modal fade" id="detailModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
  <div class="modal-header"><h6 class="mb-0" id="detailTitle">Detail du retour</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body" id="detailBody"></div>
</div></div></div>

<script>
let returns = [], saleItems = [];
Library.combo(document.getElementById('sale'), 'Tapez pour rechercher une vente...');
async function loadReturns(){
  try {
    const p = new URLSearchParams();
    ['date_from','date_to','statut'].forEach(function(id){ const v = document.getElementById(id).value; if (v) p.set(id, v); });
    const r = await Library.api('api/library/returns' + (p.toString() ? '?' + p : ''));
    returns = r.data || [];
    document.getElementById('body').innerHTML = returns.length ? returns.map(function(x){
      let actions = '<button class="btn btn-sm btn-outline-secondary me-1" data-view="'+Library.esc(x.uuid)+'"><i class="ri-eye-line"></i></button>';
      if (x.statut === 'demande') actions += '<button class="btn btn-sm btn-outline-success me-1" data-valid="'+Library.esc(x.uuid)+'">Valider</button><button class="btn btn-sm btn-outline-danger me-1" data-refuse="'+Library.esc(x.uuid)+'">Refuser</button><button class="btn btn-sm btn-outline-danger" data-del="'+Library.esc(x.uuid)+'"><i class="ri-delete-bin-line"></i></button>';
      if (x.statut === 'validee') actions += '<button class="btn btn-sm btn-outline-info" data-refund="'+Library.esc(x.uuid)+'">Rembourser</button>';
      return '<tr><td><strong>'+Library.esc(x.numero_retour)+'</strong></td><td>'+Library.esc(x.numero_vente||'-')+'</td><td>'+Library.esc(x.client)+'</td><td>'+Library.esc(x.type)+'</td><td>'+Library.esc(x.motif)+'</td><td>'+Library.money(x.montant_total)+'</td><td>'+Library.badge(x.statut)+'</td><td class="text-end">'+actions+'</td></tr>';
    }).join('') : '<tr><td colspan="8" class="text-center text-secondary-light py-4">Aucun retour</td></tr>';
  } catch(e){ Library.toast('error', e.message); }
}
async function openReturn(){
  document.getElementById('returnForm').reset();
  document.getElementById('returnTotal').textContent = Library.money(0);
  document.getElementById('returnLines').innerHTML = '<tr><td colspan="5" class="text-center text-secondary-light">Selectionnez une vente</td></tr>';
  const sel = document.getElementById('sale');
  sel.innerHTML = '<option value="">Chargement...</option>';
  bootstrap.Modal.getOrCreateInstance(document.getElementById('returnModal')).show();
  try {
    const r = await Library.api('api/library/returns/sales');
    sel.innerHTML = '<option value="">-- Selectionner une vente --</option>' + (r.data||[]).map(function(s){ return '<option value="'+Library.esc(s.uuid)+'">'+Library.esc(s.numero_vente)+' - '+Library.esc(s.client)+' ('+Library.money(s.total)+')</option>'; }).join('');
  } catch(e){ sel.innerHTML = '<option value="">Erreur</option>'; Library.toast('error', e.message); }
}
document.getElementById('sale').addEventListener('change', async function(){
  const uuid = this.value;
  const body = document.getElementById('returnLines');
  if (!uuid) { body.innerHTML = '<tr><td colspan="5" class="text-center text-secondary-light">Selectionnez une vente</td></tr>'; return; }
  body.innerHTML = '<tr><td colspan="5" class="text-center text-secondary-light">Chargement...</td></tr>';
  try {
    const r = await Library.api('api/library/returns/sales/'+uuid+'/items'); saleItems = r.data.items || [];
    body.innerHTML = saleItems.length ? saleItems.map(function(i){
      return '<tr data-id="'+i.id_sale_item+'" data-price="'+i.prix_unitaire+'"><td>'+Library.esc(i.produit)+'</td><td>'+i.quantite+'</td><td>'+i.deja_retourne+'</td><td><input type="number" min="0" max="'+i.retournable+'" value="'+(i.retournable>0?i.retournable:0)+'" class="form-control form-control-sm qty" '+(i.retournable>0?'':'disabled')+'></td><td>'+Library.money(i.prix_unitaire)+'</td></tr>';
    }).join('') : '<tr><td colspan="5" class="text-center text-secondary-light">Aucune ligne</td></tr>';
    document.querySelectorAll('#returnLines .qty').forEach(function(inp){ inp.addEventListener('input', recalc); });
    recalc();
  } catch(e){ body.innerHTML = '<tr><td colspan="5" class="text-center text-danger">'+Library.esc(e.message)+'</td></tr>'; }
});
function recalc(){
  let t = 0;
  document.querySelectorAll('#returnLines tr[data-id]').forEach(function(tr){ t += Number(tr.querySelector('.qty').value || 0) * Number(tr.dataset.price || 0); });
  document.getElementById('returnTotal').textContent = Library.money(t);
}
document.getElementById('returnForm').addEventListener('submit', async function(ev){
  ev.preventDefault();
  const items = [];
  document.querySelectorAll('#returnLines tr[data-id]').forEach(function(tr){
    const q = Number(tr.querySelector('.qty').value || 0);
    if (q > 0) items.push({ id_sale_item: Number(tr.dataset.id), quantite: q });
  });
  if (!items.length) { Library.toast('warning','Selectionnez au moins un produit'); return; }
  try {
    await Library.api('api/library/returns/create', {
      uuid: document.getElementById('sale').value, type: document.getElementById('type').value,
      motif: document.getElementById('motif').value, notes: document.getElementById('notes').value, items: items
    });
    bootstrap.Modal.getInstance(document.getElementById('returnModal')).hide();
    Library.toast('success','Demande enregistree'); loadReturns();
  } catch(e){ Library.toast('error', e.message); }
});
async function viewReturn(uuid){
  try {
    const r = await Library.api('api/library/returns/'+uuid); const x = r.data;
    document.getElementById('detailTitle').textContent = x.numero_retour;
    document.getElementById('detailBody').innerHTML = '<p><strong>Vente :</strong> '+Library.esc(x.numero_vente||'-')+'<br><strong>Type :</strong> '+Library.esc(x.type)+'<br><strong>Motif :</strong> '+Library.esc(x.motif)+'<br><strong>Statut :</strong> '+Library.badge(x.statut)+'</p><div class="table-responsive"><table class="table"><thead><tr><th>Produit</th><th>Qte</th><th>Prix</th><th>Montant</th></tr></thead><tbody>'+(x.items||[]).map(function(i){ return '<tr><td>'+Library.esc(i.produit)+'</td><td>'+i.quantite+'</td><td>'+Library.money(i.prix_unitaire)+'</td><td>'+Library.money(i.montant)+'</td></tr>'; }).join('')+'</tbody></table></div><div class="text-end">Total : <strong>'+Library.money(x.montant_total)+'</strong></div>';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('detailModal')).show();
  } catch(e){ Library.toast('error', e.message); }
}
document.getElementById('body').addEventListener('click', async function(ev){
  const v = ev.target.closest('[data-view]'), val = ev.target.closest('[data-valid]'), ref = ev.target.closest('[data-refuse]'), rem = ev.target.closest('[data-refund]'), del = ev.target.closest('[data-del]');
  if (v) viewReturn(v.dataset.view);
  if (val && await Library.confirmAction('Valider ce retour ?','Le stock sera restaure.')) {
    try { await Library.api('api/library/returns/'+val.dataset.valid+'/update',{statut:'validee'}); Library.toast('success','Retour valide'); loadReturns(); } catch(e){ Library.toast('error',e.message); }
  }
  if (ref && await Library.confirmAction('Refuser cette demande ?')) {
    try { await Library.api('api/library/returns/'+ref.dataset.refuse+'/update',{statut:'refusee'}); Library.toast('success','Demande refusee'); loadReturns(); } catch(e){ Library.toast('error',e.message); }
  }
  if (rem && await Library.confirmAction('Marquer comme rembourse ?')) {
    try { await Library.api('api/library/returns/'+rem.dataset.refund+'/update',{statut:'remboursee'}); Library.toast('success','Retour rembourse'); loadReturns(); } catch(e){ Library.toast('error',e.message); }
  }
  if (del && await Library.confirmAction('Supprimer cette demande ?')) {
    try { await Library.api('api/library/returns/'+del.dataset.del+'/delete'); Library.toast('success','Demande supprimee'); loadReturns(); } catch(e){ Library.toast('error',e.message); }
  }
});
loadReturns();
</script>
<?php include __DIR__.'/_foot.php'; ?>
