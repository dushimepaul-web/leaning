<?php
$page_title = 'Paiements';
$page_subtitle = 'Paiements';
$page_actions = '<button type="button" class="btn btn-primary-600 d-flex align-items-center gap-2" onclick="openPayment()"><i class="ri-add-line"></i> Nouveau paiement</button>';
include __DIR__.'/_head.php';
?>
<div class="card">
  <div class="card-body p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-20 border-bottom">
      <div class="d-flex flex-wrap gap-2">
        <input type="date" class="form-control form-control-sm" id="date_from" style="width:160px">
        <input type="date" class="form-control form-control-sm" id="date_to" style="width:160px">
        <select class="form-select form-select-sm" id="mode" style="width:150px"><option value="">Tous modes</option><option value="especes">Especes</option><option value="mobile_money">Mobile money</option><option value="banque">Banque</option><option value="cheque">Cheque</option><option value="autre">Autre</option></select>
        <button class="btn btn-sm btn-outline-primary" onclick="loadPayments()">Filtrer</button>
      </div>
      <button class="btn btn-sm btn-outline-secondary" onclick="loadPayments()"><i class="ri-refresh-line"></i> Actualiser</button>
    </div>
    <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Date</th><th>Vente</th><th>Client</th><th>Montant</th><th>Mode</th><th>Reference</th><th>Caissier</th><th class="text-end">Actions</th></tr></thead><tbody id="body"><tr><td colspan="8" class="text-center text-secondary-light py-4">Chargement...</td></tr></tbody></table></div>
  </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
  <div class="modal-header"><h6 class="mb-0">Nouveau paiement</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form id="paymentForm"><div class="modal-body">
    <div class="mb-3"><label class="form-label">Vente *</label><select class="form-select" id="sale" required><option value="">-- Selectionner une vente non soldee --</option></select></div>
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">Montant *</label><input type="number" step="0.01" min="0.01" class="form-control" id="montant" required></div>
      <div class="col-md-6"><label class="form-label">Date</label><input type="datetime-local" class="form-control" id="date_paiement"></div>
      <div class="col-md-6"><label class="form-label">Mode</label><select class="form-select" id="mode_paiement"><option value="especes">Especes</option><option value="mobile_money">Mobile money</option><option value="banque">Banque</option><option value="cheque">Cheque</option><option value="autre">Autre</option></select></div>
      <div class="col-md-6"><label class="form-label">Reference</label><input class="form-control" id="reference"></div>
      <div class="col-12"><label class="form-label">Notes</label><textarea class="form-control" id="notes" rows="2"></textarea></div>
    </div>
    <div class="alert alert-info mt-3 mb-0 d-none" id="saleInfo"></div>
  </div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary-600">Encaisser</button></div></form>
</div></div></div>

<script>
let payments = [], openSales = [];
Library.combo(document.getElementById('sale'), 'Tapez pour rechercher une vente...');
async function loadPayments(){
  try {
    const p = new URLSearchParams();
    ['date_from','date_to','mode'].forEach(function(id){ const v = document.getElementById(id).value; if (v) p.set(id, v); });
    const r = await Library.api('api/library/payments' + (p.toString() ? '?' + p : ''));
    payments = r.data || [];
    document.getElementById('body').innerHTML = payments.length ? payments.map(function(x){
      return '<tr><td>'+Library.esc((x.date_paiement||'').slice(0,16).replace('T',' '))+'</td><td>'+Library.esc(x.numero_vente||'-')+'</td><td>'+Library.esc(x.client)+'</td><td><strong>'+Library.money(x.montant)+'</strong></td><td>'+Library.esc(x.mode_paiement)+'</td><td>'+Library.esc(x.reference||'-')+'</td><td>'+Library.esc(x.caissier||'-')+'</td><td class="text-end"><button class="btn btn-sm btn-outline-danger" data-del="'+Library.esc(x.uuid)+'" title="Annuler le paiement"><i class="ri-close-circle-line"></i></button></td></tr>';
    }).join('') : '<tr><td colspan="8" class="text-center text-secondary-light py-4">Aucun paiement</td></tr>';
  } catch(e){ Library.toast('error', e.message); }
}
async function openPayment(){
  document.getElementById('paymentForm').reset();
  document.getElementById('date_paiement').value = new Date().toISOString().slice(0,16);
  document.getElementById('saleInfo').classList.add('d-none');
  const sel = document.getElementById('sale');
  sel.innerHTML = '<option value="">Chargement...</option>';
  bootstrap.Modal.getOrCreateInstance(document.getElementById('paymentModal')).show();
  try {
    const r = await Library.api('api/library/payments/sales'); openSales = r.data || [];
    sel.innerHTML = '<option value="">-- Selectionner une vente non soldee --</option>' + openSales.map(function(s){
      return '<option value="'+Library.esc(s.uuid)+'" data-solde="'+s.solde+'">'+Library.esc(s.numero_vente)+' - '+Library.esc(s.client)+' (solde '+Library.money(s.solde)+')</option>';
    }).join('');
  } catch(e){ sel.innerHTML = '<option value="">Erreur de chargement</option>'; Library.toast('error', e.message); }
}
document.getElementById('sale').addEventListener('change', function(){
  const o = this.options[this.selectedIndex];
  const info = document.getElementById('saleInfo');
  if (o && o.dataset.solde) {
    info.classList.remove('d-none');
    info.textContent = 'Solde restant : ' + Library.money(o.dataset.solde);
    document.getElementById('montant').value = Number(o.dataset.solde).toFixed(2);
  } else { info.classList.add('d-none'); }
});
document.getElementById('paymentForm').addEventListener('submit', async function(ev){
  ev.preventDefault();
  try {
    await Library.api('api/library/payments/create', {
      uuid: document.getElementById('sale').value,
      montant: Number(document.getElementById('montant').value),
      mode_paiement: document.getElementById('mode_paiement').value,
      reference: document.getElementById('reference').value,
      date_paiement: document.getElementById('date_paiement').value,
      notes: document.getElementById('notes').value
    });
    bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
    Library.toast('success','Paiement enregistre'); loadPayments();
  } catch(e){ Library.toast('error', e.message); }
});
document.getElementById('body').addEventListener('click', async function(ev){
  const del = ev.target.closest('[data-del]');
  if (del && await Library.confirmAction('Annuler ce paiement ?', 'Le solde de la vente sera recalcule.')) {
    try { await Library.api('api/library/payments/'+del.dataset.del+'/delete'); Library.toast('success','Paiement annule'); loadPayments(); } catch(e){ Library.toast('error',e.message); }
  }
});
loadPayments();
</script>
<?php include __DIR__.'/_foot.php'; ?>
