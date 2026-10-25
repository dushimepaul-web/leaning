<?php
$page_title = 'Ventes';
$page_subtitle = 'Ventes';
$page_actions = '<button type="button" class="btn btn-primary-600 d-flex align-items-center gap-2" onclick="openSale()"><i class="ri-shopping-cart-2-line"></i> Nouvelle vente</button>';
include __DIR__.'/_head.php';
?>
<div class="card">
  <div class="card-body p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-20 border-bottom">
      <div class="d-flex flex-wrap gap-2">
        <input type="date" class="form-control form-control-sm" id="date_from" style="width:160px">
        <input type="date" class="form-control form-control-sm" id="date_to" style="width:160px">
        <select class="form-select form-select-sm" id="statut" style="width:150px"><option value="">Tous statuts</option><option value="payee">Payee</option><option value="partielle">Partielle</option><option value="credit">Credit</option><option value="en_attente">En attente</option><option value="annulee">Annulee</option><option value="retournee">Retournee</option></select>
        <button class="btn btn-sm btn-outline-primary" onclick="loadSales()">Filtrer</button>
      </div>
      <button class="btn btn-sm btn-outline-secondary" onclick="loadSales()"><i class="ri-refresh-line"></i> Actualiser</button>
    </div>
    <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Vente</th><th>Client</th><th>Date</th><th>Total</th><th>Paye</th><th>Solde</th><th>Statut</th><th class="text-end">Actions</th></tr></thead><tbody id="body"><tr><td colspan="8" class="text-center text-secondary-light py-4">Chargement...</td></tr></tbody></table></div>
  </div>
</div>

<div class="modal fade" id="saleModal" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content">
  <div class="modal-header"><h6 class="mb-0">Nouvelle vente</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form id="saleForm"><div class="modal-body">
    <div class="row g-3 mb-3">
      <div class="col-md-4"><label class="form-label">Eleve (optionnel)</label><select class="form-select" id="id_etudiant"><option value="">Client libre</option><?php foreach ($etudiants as $e): ?><option value="<?= (int)$e['id_etudiant'] ?>"><?= html_escape($e['fullname']) ?> (<?= html_escape($e['matricule']) ?>)</option><?php endforeach; ?></select></div>
      <div class="col-md-4"><label class="form-label">Nom du client</label><input class="form-control" id="client_nom" placeholder="Client comptant"></div>
      <div class="col-md-4"><label class="form-label">Telephone</label><input class="form-control" id="client_telephone"></div>
      <div class="col-md-3"><label class="form-label">Mode de paiement</label><select class="form-select" id="mode_paiement"><option value="especes">Especes</option><option value="mobile_money">Mobile money</option><option value="banque">Banque</option><option value="cheque">Cheque</option><option value="credit">Credit</option></select></div>
      <div class="col-md-3"><label class="form-label">Montant paye</label><input type="number" step="0.01" min="0" class="form-control" id="montant_paye" value="0"></div>
      <div class="col-md-3"><label class="form-label">Remise totale</label><input type="number" step="0.01" min="0" class="form-control" id="remise" value="0"></div>
      <div class="col-md-3"><label class="form-label">Reference paiement</label><input class="form-control" id="reference_paiement"></div>
    </div>
    <div class="border rounded" style="overflow:visible"><table class="table table-sm align-middle mb-0"><thead class="table-light"><tr><th style="min-width:240px">Produit</th><th style="width:100px" class="text-center">Quantité</th><th style="width:140px" class="text-end">Prix</th><th style="width:130px" class="text-end">Remise</th><th style="width:140px" class="text-end">Total</th><th style="width:52px"></th></tr></thead><tbody id="lines"></tbody></table></div>
    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addLine()"><i class="ri-add-line"></i> Ajouter une ligne</button>
    <div class="row g-3 mt-1">
      <div class="col-md-8"><label class="form-label">Notes</label><textarea class="form-control" id="notes" rows="2"></textarea></div>
      <div class="col-md-4 text-end"><div class="d-flex justify-content-between"><span>Sous-total</span><strong id="sousTotal">0</strong></div><div class="d-flex justify-content-between"><span>Remise</span><strong id="remiseView">0</strong></div><div class="d-flex justify-content-between fs-5"><span>Total</span><strong id="grandTotal">0</strong></div><div class="d-flex justify-content-between"><span>Reste</span><strong id="balance">0</strong></div></div>
    </div>
  </div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary-600">Enregistrer la vente</button></div></form>
</div></div></div>

<div class="modal fade" id="detailModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
  <div class="modal-header"><h6 class="mb-0" id="detailTitle">Detail de la vente</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body" id="detailBody"></div>
</div></div></div>

<script>
let products = [], sales = [];
Library.combo(document.getElementById('id_etudiant'), 'Tapez pour rechercher un eleve...');
document.getElementById('id_etudiant').addEventListener('change', function(){
  const o = this.options[this.selectedIndex];
  document.getElementById('client_nom').value = this.value ? o.text.replace(/\s*\([^)]*\)\s*$/, '') : '';
});
async function loadProducts(){ const r = await Library.api('api/library/stock'); products = r.data || []; }
function addLine(){
  const tr = document.createElement('tr');
  tr.innerHTML = '<td><select class="form-select form-select-sm line-product"><option value="" data-price="0" data-stock="">-- Selectionner un produit --</option>'+products.map(function(p){ return '<option value="'+p.id_produit+'" data-price="'+p.prix_unitaire+'" data-stock="'+p.stock_actuel+'">'+Library.esc(p.libelle)+' (stock '+p.stock_actuel+')</option>'; }).join('')+'</select></td>'+
    '<td><input type="number" min="1" step="1" value="1" class="form-control form-control-sm text-center line-qty"></td>'+
    '<td><input type="number" min="0" step="0.01" class="form-control form-control-sm text-end line-price"></td>'+
    '<td><input type="number" min="0" step="0.01" value="0" class="form-control form-control-sm text-end line-discount"></td>'+
    '<td class="line-total text-end fw-semibold text-nowrap">0</td>'+
    '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer la ligne" onclick="this.closest(\'tr\').remove(); recalc();"><i class="ri-delete-bin-line"></i></button></td>';
  document.getElementById('lines').appendChild(tr);
  const sel = tr.querySelector('.line-product'), price = tr.querySelector('.line-price');
  Library.combo(sel, 'Tapez pour rechercher un produit...');
  price.value = sel.value && sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].dataset.price : '';
  sel.addEventListener('change', function(){
    const o = this.options[this.selectedIndex];
    price.value = this.value && o ? (o.dataset.price || 0) : '';
    recalc();
  });
  ['line-qty','line-price','line-discount'].forEach(function(c){ tr.querySelector('.'+c).addEventListener('input', recalc); });
  recalc();
}
function recalc(){
  let st = 0;
  document.querySelectorAll('#lines tr').forEach(function(tr){
    const q = Number(tr.querySelector('.line-qty').value || 0);
    const p = Number(tr.querySelector('.line-price').value || 0);
    const d = Number(tr.querySelector('.line-discount').value || 0);
    const t = Math.max(0, q * p - d); st += t;
    tr.querySelector('.line-total').textContent = Library.money(t);
  });
  const remise = Number(document.getElementById('remise').value || 0);
  const total = Math.max(0, st - remise);
  const paid = Number(document.getElementById('montant_paye').value || 0);
  document.getElementById('sousTotal').textContent = Library.money(st);
  document.getElementById('remiseView').textContent = Library.money(remise);
  document.getElementById('grandTotal').textContent = Library.money(total);
  document.getElementById('balance').textContent = Library.money(Math.max(0, total - paid));
}
async function loadSales(){
  try {
    const p = new URLSearchParams();
    ['date_from','date_to','statut'].forEach(function(id){ const v = document.getElementById(id).value; if (v) p.set(id, v); });
    const r = await Library.api('api/library/sales' + (p.toString() ? '?' + p : ''));
    sales = r.data || [];
    document.getElementById('body').innerHTML = sales.length ? sales.map(function(s){
      let actions = '<button class="btn btn-sm btn-outline-secondary me-1" data-view="'+Library.esc(s.uuid)+'"><i class="ri-eye-line"></i></button>';
      if (!['annulee','retournee'].includes(s.statut)) actions += '<button class="btn btn-sm btn-outline-danger" data-cancel="'+Library.esc(s.uuid)+'">Annuler</button>';
      return '<tr><td><strong>'+Library.esc(s.numero_vente)+'</strong></td><td>'+Library.esc(s.client)+'<br><small class="text-secondary-light">'+Library.esc(s.matricule||'')+'</small></td><td>'+Library.esc((s.date_vente||'').slice(0,16).replace('T',' '))+'</td><td>'+Library.money(s.total)+'</td><td>'+Library.money(s.paye)+'</td><td>'+Library.money(s.solde)+'</td><td>'+Library.badge(s.statut)+'</td><td class="text-end">'+actions+'</td></tr>';
    }).join('') : '<tr><td colspan="8" class="text-center text-secondary-light py-4">Aucune vente</td></tr>';
  } catch(e){ Library.toast('error', e.message); }
}
async function openSale(){
  document.getElementById('saleForm').reset();
  document.getElementById('id_etudiant').dispatchEvent(new Event('change'));
  document.getElementById('lines').innerHTML = '';
  document.getElementById('mode_paiement').value = 'especes';
  document.getElementById('montant_paye').value = 0;
  document.getElementById('remise').value = 0;
  document.getElementById('montant_paye').disabled = false;
  try { await loadProducts(); } catch(e){ Library.toast('error', e.message); }
  addLine(); recalc();
  bootstrap.Modal.getOrCreateInstance(document.getElementById('saleModal')).show();
}
document.getElementById('mode_paiement').addEventListener('change', function(){
  const credit = this.value === 'credit';
  const paid = document.getElementById('montant_paye');
  paid.disabled = credit; if (credit) paid.value = 0; else if (Number(paid.value) === 0) paid.value = document.getElementById('grandTotal').textContent.replace(/[^0-9,.]/g,'').replace(',','.');
  recalc();
});
['remise','montant_paye'].forEach(function(id){ document.getElementById(id).addEventListener('input', recalc); });
document.getElementById('saleForm').addEventListener('submit', async function(ev){
  ev.preventDefault();
  const items = [];
  document.querySelectorAll('#lines tr').forEach(function(tr){
    const id = Number(tr.querySelector('.line-product').value);
    const q = Number(tr.querySelector('.line-qty').value);
    const p = Number(tr.querySelector('.line-price').value);
    const d = Number(tr.querySelector('.line-discount').value || 0);
    if (id && q > 0) items.push({ id_produit: id, quantite: q, prix_unitaire: p, remise: d });
  });
  if (!items.length) { Library.toast('warning','Ajoutez une ligne'); return; }
  try {
    await Library.api('api/library/sales/create', {
      id_etudiant: document.getElementById('id_etudiant').value || null,
      client_nom: document.getElementById('client_nom').value,
      client_telephone: document.getElementById('client_telephone').value,
      mode_paiement: document.getElementById('mode_paiement').value,
      montant_paye: Number(document.getElementById('montant_paye').value || 0),
      remise: Number(document.getElementById('remise').value || 0),
      reference_paiement: document.getElementById('reference_paiement').value,
      notes: document.getElementById('notes').value,
      items: items
    });
    bootstrap.Modal.getInstance(document.getElementById('saleModal')).hide();
    Library.toast('success','Vente enregistree'); loadSales(); loadProducts();
  } catch(e){
    if (e.message && e.message.indexOf('Stock insuffisant') !== -1) {
      Swal.fire({ icon: 'error', title: 'Stock insuffisant', text: e.message });
    } else {
      Library.toast('error', e.message);
    }
  }
});
async function viewSale(uuid){
  try {
    const r = await Library.api('api/library/sales/' + uuid); const s = r.data;
    document.getElementById('detailTitle').textContent = s.numero_vente + ' - ' + s.client;
    let html = '<div class="row mb-3"><div class="col-6"><strong>Date :</strong> '+Library.esc(s.date_vente)+'<br><strong>Caissier :</strong> '+Library.esc(s.caissier||'-')+'<br><strong>Paiement :</strong> '+Library.esc(s.mode_paiement)+'</div><div class="col-6 text-end"><strong>Total :</strong> '+Library.money(s.total)+'<br><strong>Paye :</strong> '+Library.money(s.paye)+'<br><strong>Solde :</strong> '+Library.money(s.solde)+'</div></div>';
    html += '<div class="table-responsive"><table class="table"><thead><tr><th>Produit</th><th>Qte</th><th>Prix</th><th>Remise</th><th>Total</th></tr></thead><tbody>'+(s.items||[]).map(function(i){ return '<tr><td>'+Library.esc(i.produit)+'</td><td>'+i.quantite+'</td><td>'+Library.money(i.prix_unitaire)+'</td><td>'+Library.money(i.remise)+'</td><td>'+Library.money(i.total_ligne)+'</td></tr>'; }).join('')+'</tbody></table></div>';
    if ((s.payments||[]).length) html += '<h6 class="mt-3">Paiements</h6><ul class="list-group">'+s.payments.map(function(p){ return '<li class="list-group-item d-flex justify-content-between"><span>'+Library.esc(p.mode_paiement)+' '+(p.reference?'('+Library.esc(p.reference)+')':'')+'</span><strong>'+Library.money(p.montant)+'</strong></li>'; }).join('')+'</ul>';
    if ((s.returns||[]).length) html += '<h6 class="mt-3">Retours</h6><ul class="list-group">'+s.returns.map(function(x){ return '<li class="list-group-item d-flex justify-content-between"><span>'+Library.esc(x.numero_retour)+' - '+Library.esc(x.motif)+'</span><span>'+Library.badge(x.statut)+' '+Library.money(x.montant_total)+'</span></li>'; }).join('')+'</ul>';
    document.getElementById('detailBody').innerHTML = html;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('detailModal')).show();
  } catch(e){ Library.toast('error', e.message); }
}
document.getElementById('body').addEventListener('click', async function(ev){
  const v = ev.target.closest('[data-view]'), c = ev.target.closest('[data-cancel]');
  if (v) viewSale(v.dataset.view);
  if (c && await Library.confirmAction('Annuler cette vente ?', 'Le stock sera restaure et les paiements annules.')) {
    try { await Library.api('api/library/sales/'+c.dataset.cancel+'/update',{statut:'annulee'}); Library.toast('success','Vente annulee'); loadSales(); loadProducts(); } catch(e){ Library.toast('error',e.message); }
  }
});
(async function(){ try { await loadProducts(); await loadSales(); } catch(e){ Library.toast('error', e.message); } })();
</script>
<?php include __DIR__.'/_foot.php'; ?>
