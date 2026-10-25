<?php
$page_title = 'Fournisseurs';
$page_subtitle = 'Fournisseurs';
$page_actions = '<button type="button" class="btn btn-primary-600 d-flex align-items-center gap-2" onclick="openSupplier()"><i class="ri-add-line"></i> Nouveau fournisseur</button>';
include __DIR__.'/_head.php';
?>
<div class="card">
  <div class="card-body p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-20 border-bottom">
      <div class="navbar-search dt-search m-0"><input type="text" id="search" class="dt-input" placeholder="Rechercher un fournisseur..."><iconify-icon icon="ion:search-outline" class="icon"></iconify-icon></div>
      <button class="btn btn-sm btn-outline-secondary" onclick="loadSuppliers()"><i class="ri-refresh-line"></i> Actualiser</button>
    </div>
    <div class="table-responsive"><table class="table mb-0"><thead><tr><th>#</th><th>Nom</th><th>Contact</th><th>Telephone</th><th>Email</th><th>Achats</th><th>Statut</th><th class="text-end">Actions</th></tr></thead><tbody id="body"><tr><td colspan="8" class="text-center text-secondary-light py-4">Chargement...</td></tr></tbody></table></div>
  </div>
</div>

<div class="modal fade" id="supplierModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
  <div class="modal-header"><h6 class="mb-0" id="modalTitle">Nouveau fournisseur</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <form id="supplierForm"><div class="modal-body"><input type="hidden" id="uuid">
    <div class="row g-3">
      <div class="col-12"><label class="form-label">Nom *</label><input class="form-control" id="nom" required></div>
      <div class="col-md-6"><label class="form-label">Contact</label><input class="form-control" id="contact_nom"></div>
      <div class="col-md-6"><label class="form-label">Telephone</label><input class="form-control" id="telephone"></div>
      <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" id="email"></div>
      <div class="col-md-6"><label class="form-label">Statut</label><select class="form-select" id="actif"><option value="1">Actif</option><option value="0">Inactif</option></select></div>
      <div class="col-12"><label class="form-label">Adresse</label><textarea class="form-control" id="adresse" rows="2"></textarea></div>
      <div class="col-12"><label class="form-label">Notes</label><textarea class="form-control" id="notes" rows="2"></textarea></div>
    </div>
  </div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary-600">Enregistrer</button></div></form>
</div></div></div>

<script>
let suppliers = [];
async function loadSuppliers(){
  try {
    const q = document.getElementById('search').value.trim();
    const r = await Library.api('api/library/suppliers' + (q ? '?q=' + encodeURIComponent(q) : ''));
    suppliers = r.data || [];
    document.getElementById('body').innerHTML = suppliers.length ? suppliers.map(function(s, i){
      return '<tr><td>'+(i+1)+'</td><td><strong>'+Library.esc(s.nom)+'</strong></td><td>'+Library.esc(s.contact_nom)+'</td><td>'+Library.esc(s.telephone)+'</td><td>'+Library.esc(s.email)+'</td><td>'+s.nb_achats+'</td><td>'+(Number(s.actif)?'<span class="badge bg-success-100 text-success-600">Actif</span>':'<span class="badge bg-secondary-100 text-secondary-600">Inactif</span>')+'</td><td class="text-end"><button class="btn btn-sm btn-outline-primary me-1" data-edit="'+Library.esc(s.uuid)+'"><i class="ri-edit-line"></i></button><button class="btn btn-sm btn-outline-danger" data-del="'+Library.esc(s.uuid)+'"><i class="ri-delete-bin-line"></i></button></td></tr>';
    }).join('') : '<tr><td colspan="8" class="text-center text-secondary-light py-4">Aucun fournisseur</td></tr>';
  } catch(e){ Library.toast('error', e.message); }
}
function openSupplier(s){
  document.getElementById('supplierForm').reset();
  document.getElementById('uuid').value = s ? s.uuid : '';
  document.getElementById('modalTitle').textContent = s ? 'Modifier le fournisseur' : 'Nouveau fournisseur';
  if (s) {
    ['nom','contact_nom','telephone','email','adresse','notes'].forEach(function(k){ document.getElementById(k).value = s[k] || ''; });
    document.getElementById('actif').value = String(s.actif);
  }
  bootstrap.Modal.getOrCreateInstance(document.getElementById('supplierModal')).show();
}
document.getElementById('supplierForm').addEventListener('submit', async function(ev){
  ev.preventDefault();
  const uuid = document.getElementById('uuid').value;
  const data = {
    nom: document.getElementById('nom').value,
    contact_nom: document.getElementById('contact_nom').value, telephone: document.getElementById('telephone').value,
    email: document.getElementById('email').value, adresse: document.getElementById('adresse').value,
    notes: document.getElementById('notes').value, actif: Number(document.getElementById('actif').value)
  };
  try {
    await Library.api('api/library/suppliers/' + (uuid ? uuid + '/update' : 'create'), data);
    bootstrap.Modal.getInstance(document.getElementById('supplierModal')).hide();
    Library.toast('success', uuid ? 'Fournisseur mis a jour' : 'Fournisseur cree');
    loadSuppliers();
  } catch(e){ Library.toast('error', e.message); }
});
document.getElementById('body').addEventListener('click', async function(ev){
  const edit = ev.target.closest('[data-edit]'); const del = ev.target.closest('[data-del]');
  if (edit) { const s = suppliers.find(function(x){ return x.uuid === edit.dataset.edit; }); if (s) openSupplier(s); }
  if (del) {
    if (!await Library.confirmAction('Supprimer ce fournisseur ?', 'Cette action est irreversible.')) return;
    try { await Library.api('api/library/suppliers/' + del.dataset.del + '/delete'); Library.toast('success','Fournisseur supprime'); loadSuppliers(); }
    catch(e){ Library.toast('error', e.message); }
  }
});
document.getElementById('search').addEventListener('keyup', function(e){ if (e.key === 'Enter') loadSuppliers(); });
loadSuppliers();
</script>
<?php include __DIR__.'/_foot.php'; ?>
