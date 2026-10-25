<?php
$page_title = 'Charges';
$page_subtitle = 'Charges';
$page_actions = '<button type="button" class="btn btn-primary-600 d-flex align-items-center gap-2" onclick="openExpense()"><i class="ri-add-line"></i> Nouvelle charge</button>';
include __DIR__.'/_head.php';
?>
<div class="card">
  <div class="card-body p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-20 border-bottom">
      <div class="d-flex flex-wrap gap-2">
        <select class="form-select form-select-sm" id="categorie" style="width:170px"><option value="">Toutes categories</option><option value="loyer">Loyer</option><option value="salaires">Salaires</option><option value="transport">Transport</option><option value="fournitures">Fournitures</option><option value="entretien">Entretien</option><option value="autre">Autre</option></select>
        <input type="date" class="form-control form-control-sm" id="date_from" style="width:160px">
        <input type="date" class="form-control form-control-sm" id="date_to" style="width:160px">
        <button class="btn btn-sm btn-outline-primary" onclick="loadExpenses()">Filtrer</button>
      </div>
      <div class="d-flex align-items-center gap-3"><span class="text-secondary-light">Total affiche :</span><strong id="total">0</strong><button class="btn btn-sm btn-outline-secondary" onclick="loadExpenses()"><i class="ri-refresh-line"></i></button></div>
    </div>
    <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Date</th><th>Libelle</th><th>Categorie</th><th>Beneficiaire</th><th>Mode</th><th>Montant</th><th>Utilisateur</th><th class="text-end">Actions</th></tr></thead><tbody id="body"><tr><td colspan="8" class="text-center text-secondary-light py-4">Chargement...</td></tr></tbody></table></div>
  </div>
</div>

<div class="modal fade" id="expenseModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
  <div class="modal-header"><h6 class="mb-0" id="modalTitle">Nouvelle charge</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form id="expenseForm"><div class="modal-body"><input type="hidden" id="uuid">
    <div class="row g-3">
      <div class="col-12"><label class="form-label">Libelle *</label><input class="form-control" id="libelle" required></div>
      <div class="col-md-6"><label class="form-label">Categorie</label><select class="form-select" id="categorie_form"><option value="loyer">Loyer</option><option value="salaires">Salaires</option><option value="transport">Transport</option><option value="fournitures">Fournitures</option><option value="entretien">Entretien</option><option value="autre">Autre</option></select></div>
      <div class="col-md-6"><label class="form-label">Montant *</label><input type="number" step="0.01" min="0.01" class="form-control" id="montant" required></div>
      <div class="col-md-6"><label class="form-label">Date *</label><input type="date" class="form-control" id="date_expense" required></div>
      <div class="col-md-6"><label class="form-label">Mode de paiement</label><select class="form-select" id="mode_paiement"><option value="especes">Especes</option><option value="mobile_money">Mobile money</option><option value="banque">Banque</option><option value="cheque">Cheque</option><option value="autre">Autre</option></select></div>
      <div class="col-md-6"><label class="form-label">Beneficiaire</label><input class="form-control" id="beneficiaire"></div>
      <div class="col-md-6"><label class="form-label">Reference</label><input class="form-control" id="reference"></div>
      <div class="col-12"><label class="form-label">Notes</label><textarea class="form-control" id="notes" rows="2"></textarea></div>
    </div>
  </div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary-600">Enregistrer</button></div></form>
</div></div></div>

<script>
let expenses = [];
async function loadExpenses(){
  try {
    const p = new URLSearchParams();
    ['categorie','date_from','date_to'].forEach(function(id){ const v = document.getElementById(id).value; if (v) p.set(id, v); });
    const r = await Library.api('api/library/expenses' + (p.toString() ? '?' + p : ''));
    expenses = r.data || [];
    let total = 0;
    document.getElementById('body').innerHTML = expenses.length ? expenses.map(function(x){
      total += Number(x.montant);
      return '<tr><td>'+Library.esc(x.date_expense)+'</td><td><strong>'+Library.esc(x.libelle)+'</strong><br><small class="text-secondary-light">'+Library.esc(x.reference||'')+'</small></td><td>'+Library.esc(x.categorie)+'</td><td>'+Library.esc(x.beneficiaire||'-')+'</td><td>'+Library.esc(x.mode_paiement)+'</td><td>'+Library.money(x.montant)+'</td><td>'+Library.esc(x.utilisateur||'-')+'</td><td class="text-end"><button class="btn btn-sm btn-outline-primary me-1" data-edit="'+Library.esc(x.uuid)+'"><i class="ri-edit-line"></i></button><button class="btn btn-sm btn-outline-danger" data-del="'+Library.esc(x.uuid)+'"><i class="ri-delete-bin-line"></i></button></td></tr>';
    }).join('') : '<tr><td colspan="8" class="text-center text-secondary-light py-4">Aucune charge</td></tr>';
    document.getElementById('total').textContent = Library.money(total);
  } catch(e){ Library.toast('error', e.message); }
}
function openExpense(x){
  document.getElementById('expenseForm').reset();
  document.getElementById('uuid').value = x ? x.uuid : '';
  document.getElementById('modalTitle').textContent = x ? 'Modifier la charge' : 'Nouvelle charge';
  if (x) {
    ['libelle','montant','date_expense','mode_paiement','beneficiaire','reference','notes'].forEach(function(k){ document.getElementById(k).value = x[k] || ''; });
    document.getElementById('categorie_form').value = x.categorie || 'autre';
  } else {
    document.getElementById('date_expense').value = Library.today();
  }
  bootstrap.Modal.getOrCreateInstance(document.getElementById('expenseModal')).show();
}
document.getElementById('expenseForm').addEventListener('submit', async function(ev){
  ev.preventDefault();
  const uuid = document.getElementById('uuid').value;
  try {
    await Library.api('api/library/expenses/' + (uuid ? uuid + '/update' : 'create'), {
      libelle: document.getElementById('libelle').value, categorie: document.getElementById('categorie_form').value,
      montant: Number(document.getElementById('montant').value), date_expense: document.getElementById('date_expense').value,
      mode_paiement: document.getElementById('mode_paiement').value, beneficiaire: document.getElementById('beneficiaire').value,
      reference: document.getElementById('reference').value, notes: document.getElementById('notes').value
    });
    bootstrap.Modal.getInstance(document.getElementById('expenseModal')).hide();
    Library.toast('success', uuid ? 'Charge mise a jour' : 'Charge enregistree'); loadExpenses();
  } catch(e){ Library.toast('error', e.message); }
});
document.getElementById('body').addEventListener('click', async function(ev){
  const ed = ev.target.closest('[data-edit]'), del = ev.target.closest('[data-del]');
  if (ed) { const x = expenses.find(function(i){ return i.uuid === ed.dataset.edit; }); if (x) openExpense(x); }
  if (del && await Library.confirmAction('Supprimer cette charge ?')) {
    try { await Library.api('api/library/expenses/'+del.dataset.del+'/delete'); Library.toast('success','Charge supprimee'); loadExpenses(); } catch(e){ Library.toast('error',e.message); }
  }
});
loadExpenses();
</script>
<?php include __DIR__.'/_foot.php'; ?>
