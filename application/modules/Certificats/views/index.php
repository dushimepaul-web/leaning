<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Certificats</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Certificats</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span> Nouveau certificat
    </button>
  </div>
  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0 dataTable-wrapper">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
          <div class="d-flex flex-wrap align-items-center gap-16">
            <div class="dropdown">
              <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown">
                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm"><i class="ri-file-upload-line text-md line-height-1"></i> Export</span><span><i class="ri-arrow-down-s-line"></i></span>
              </button>
              <ul class="dropdown-menu p-12 border bg-base shadow">
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export PDF',text:'Fonctionnalité à venir'})"><i class="ri-file-3-line"></i> PDF</button></li>
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="exportCSV()"><i class="ri-file-excel-line"></i> CSV</button></li>
              </ul>
            </div>
            <form class="navbar-search dt-search m-0">
              <input type="text" id="dtSearch" class="dt-input bg-transparent radius-4" placeholder="Rechercher...">
              <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
            </form>
          </div>
          <div class="d-flex align-items-center gap-8 text-secondary-light">
            <span>Lignes :</span>
            <select id="dtLength" class="dt-input form-control form-select" style="width:auto;">
              <option value="10" selected>10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option>
            </select>
          </div>
        </div>
        <table class="table bordered-table mb-0 data-table" id="dataTable" style="width:100%">
          <thead>
            <tr>
              <th>#</th><th>N° Certificat</th><th>Étudiant</th><th>Type</th><th>Date émission</th><th></th>
            </tr>
          </thead>
          <tbody id="dataBody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="sidebarOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="addSidebar" style="width:50vw;max-width:50vw;box-shadow: -4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="sidebarTitle">Nouveau certificat</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="mainForm" class="d-flex flex-column p-20">
    <input type="hidden" id="recordId">
    <div class="mb-3 position-relative">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Étudiant *</label>
      <input type="hidden" id="id_etudiant">
      <div class="position-relative">
        <input type="text" class="form-control pe-5" id="id_etudiant_search" placeholder="Tapez le nom de l'étudiant..." autocomplete="off">
        <button type="button" id="clearSearch" class="btn p-0 border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y me-3 text-secondary-light ri-close-line" style="display:none;font-size:18px;line-height:1;" onclick="clearStudentSearch()"></button>
      </div>
      <div id="id_etudiant_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:220px;overflow-y:auto;"></div>
    </div>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type *</label>
        <select class="form-control form-select" id="type_certificat">
          <option value="scolarite">Scolarité</option><option value="inscription">Inscription</option><option value="stage">Stage</option><option value="fin_etudes">Fin d'études</option><option value="autre">Autre</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date d'émission</label>
        <input type="date" class="form-control" id="date_emission">
      </div>
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Signataire</label>
        <input type="text" class="form-control" id="signataire" placeholder="Nom du signataire">
      </div>
      <div class="col-12 d-flex align-items-center justify-content-center gap-3 mt-8">
        <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeSidebar()">Annuler</button>
        <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered"><div class="modal-content radius-16 bg-base">
    <div class="modal-body pt-32 px-36 pb-24 text-center">
      <span class="mb-16 fs-1 line-height-1 text-danger d-block"><i class="ri-delete-bin-6-line"></i></span>
      <h6 class="text-lg fw-semibold text-primary-light mb-0">Confirmer la suppression ?</h6>
      <p class="text-sm text-secondary-light mt-8">Cette action est irréversible.</p>
      <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
        <button type="button" class="flex-grow-1 border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-24 py-11 radius-8" data-bs-dismiss="modal">Annuler</button>
        <button type="button" id="confirmDeleteBtn" class="flex-grow-1 btn btn-danger border border-danger-600 text-md px-16 py-12 radius-8">Supprimer</button>
      </div>
    </div>
  </div></div>
</div>

<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script src="<?= base_url() ?>assets/js/autocomplete.js?v=<?= filemtime(FCPATH.'assets/js/autocomplete.js') ?>"></script>
<script id="id_etudiant_data" type="application/json"><?= json_encode($etudiants) ?></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let editingId = null, deleteId = null, etudiantsList = [];
try { etudiantsList = JSON.parse(document.getElementById('id_etudiant_data').textContent); } catch(e) {}

function renderStudentList(filter) {
  var c = document.getElementById('id_etudiant_results'), q = (filter||'').toLowerCase().trim();
  var m = q ? etudiantsList.filter(function(e){ return (e.nom+' '+e.prenom+' '+(e.matricule||'')).toLowerCase().includes(q); }) : etudiantsList;
  if (!m.length) { c.innerHTML = '<div class="list-group-item text-secondary-light text-center py-3"><i class="ri-user-search-line me-1"></i>Aucun étudiant trouvé</div>'; }
  else { c.innerHTML = m.map(function(e){ return '<button type="button" class="list-group-item list-group-item-action text-start d-flex align-items-center gap-2 py-2 px-3 border-0 border-bottom border-neutral-100" data-id="'+e.id_etudiant+'" data-nom="'+e.nom+'" data-prenom="'+e.prenom+'" data-matricule="'+(e.matricule||'')+'"><span class="d-flex align-items-center justify-content-center bg-primary-100 text-primary-600 radius-4" style="width:36px;height:36px;flex-shrink:0;"><i class="ri-user-3-line"></i></span><div class="text-start"><span class="fw-medium text-sm">'+e.nom+' '+e.prenom+'</span><small class="d-block text-secondary-light text-xs">'+(e.matricule||'Sans matricule')+'</small></div></button>'; }).join(''); }
  c.style.display = 'block';
}
function selectStudent(el) {
  document.getElementById('id_etudiant').value = el.dataset.id;
  document.getElementById('id_etudiant_search').value = el.dataset.nom + ' ' + el.dataset.prenom + ' (' + el.dataset.matricule + ')';
  document.getElementById('id_etudiant_search').classList.add('border-success','border-2');
  document.getElementById('id_etudiant_results').style.display = 'none';
  document.getElementById('clearSearch').style.display = 'block';
}
function clearStudentSearch() {
  document.getElementById('id_etudiant').value = '';
  document.getElementById('id_etudiant_search').value = '';
  document.getElementById('id_etudiant_search').classList.remove('border-success','border-2');
  document.getElementById('id_etudiant_results').style.display = 'none';
  document.getElementById('clearSearch').style.display = 'none';
}
document.getElementById('id_etudiant_search')?.addEventListener('focus', function(){ if(!document.getElementById('id_etudiant').value) renderStudentList(this.value); });
document.getElementById('id_etudiant_search')?.addEventListener('input', function(){ document.getElementById('id_etudiant').value=''; document.getElementById('clearSearch').style.display=this.value?'block':'none'; renderStudentList(this.value); });
document.getElementById('id_etudiant_search')?.addEventListener('keydown', function(e){
  if(e.key==='ArrowDown'||e.key==='ArrowUp'){ var it=document.querySelectorAll('#id_etudiant_results button'); if(!it.length)return; e.preventDefault(); var idx=Array.from(it).indexOf(document.activeElement); idx=e.key==='ArrowDown'?Math.min(idx+1,it.length-1):Math.max(idx-1,0); it[idx].focus(); }
  if(e.key==='Escape') document.getElementById('id_etudiant_results').style.display='none';
});
document.addEventListener('click', function(e){
  var t = e.target.closest('#id_etudiant_results button');
  if(t){ selectStudent(t); return; }
  if(!e.target.closest('#id_etudiant_search') && !e.target.closest('#clearSearch')) document.getElementById('id_etudiant_results').style.display='none';
});

async function loadData(){
  var r=await API.certificats.list();
  if(!r.success){ $('#dataBody').html('<tr><td colspan="6" class="text-center text-danger">Erreur</td></tr>'); return; }
  var rows='';
  r.data.forEach(function(c,i){ rows+='<tr><td>'+(i+1)+'</td><td><span class="fw-semibold">'+c.numero_certificat+'</span></td><td>'+c.nom+' '+c.prenom+' <small class="text-secondary-light">('+(c.matricule||'')+')</small></td><td><span class="px-24 py-4 radius-4 fw-medium text-sm bg-info-100 text-info-600">'+c.type_certificat.replace(/_/g,' ')+'</span></td><td>'+(c.date_emission||'-')+'</td><td><div class="btn-group"><button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button><ul class="dropdown-menu dropdown-menu-lg-end border p-12"><li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editRecord(\''+c.uuid+'\')"><i class="ri-edit-2-line"></i> Modifier</button></li><li><button class="dropdown-item rounded text-danger d-flex align-items-center gap-2 py-6" onclick="confirmDelete(\''+c.uuid+'\')"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li></ul></div></td></tr>'; });
  $('#dataBody').html(rows);
  if($.fn.DataTable.isDataTable('#dataTable'))$('#dataTable').DataTable().destroy();
  $('#dataTable').DataTable({pageLength:10,scrollX:true,lengthMenu:[[10,25,50,100],[10,25,50,100]],language:{search:'',searchPlaceholder:'Rechercher...',lengthMenu:'Lignes:_MENU_',info:'',zeroRecords:'Aucun certificat',infoEmpty:'',infoFiltered:''},dom:'t<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'});
}

function openAddSidebar(){ editingId=null; document.getElementById('sidebarTitle').textContent='Nouveau certificat'; document.getElementById('mainForm').reset(); document.getElementById('recordId').value=''; document.getElementById('date_emission').value=new Date().toISOString().split('T')[0]; clearStudentSearch(); document.getElementById('addSidebar').classList.add('active'); document.getElementById('sidebarOverlay').classList.add('active'); }
function closeSidebar(){ document.getElementById('addSidebar').classList.remove('active'); document.getElementById('sidebarOverlay').classList.remove('active'); clearStudentSearch(); }
document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);

async function editRecord(id){
  var r=await API.certificats.list();
  if(!r.success)return;
  var c=r.data.find(function(x){return x.uuid==id;});
  if(!c)return;
  editingId=c.uuid;
  document.getElementById('sidebarTitle').textContent='Modifier le certificat';
  document.getElementById('recordId').value=c.uuid;
  document.getElementById('id_etudiant').value=c.id_etudiant||'';
  var etu=etudiantsList.find(function(e){return e.id_etudiant==c.id_etudiant;});
  if(etu){ document.getElementById('id_etudiant_search').value=etu.nom+' '+etu.prenom+' ('+(etu.matricule||'')+')'; document.getElementById('clearSearch').style.display='block'; }
  document.getElementById('type_certificat').value=c.type_certificat||'scolarite';
  document.getElementById('date_emission').value=c.date_emission||'';
  document.getElementById('signataire').value=c.signataire||'';
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
}

document.getElementById('mainForm').addEventListener('submit', async function(e){
  e.preventDefault();
  var d={ id_etudiant:document.getElementById('id_etudiant').value, type_certificat:document.getElementById('type_certificat').value, date_emission:document.getElementById('date_emission').value, signataire:document.getElementById('signataire').value };
  if(!d.id_etudiant||!d.type_certificat){ Swal.fire({icon:'warning',title:'Validation',text:'Étudiant et type obligatoires'}); return; }
  var r;
  if(editingId){ r=await API.certificats.update(editingId,d); } else { r=await API.certificats.create(d); }
  if(r.success){ closeSidebar(); Toast.fire({icon:'success',title:editingId?'Certificat modifié':'Certificat créé'}); loadData(); }
  else{ Swal.fire({icon:'error',text:r.message}); }
});

function confirmDelete(id){ deleteId=id; new bootstrap.Modal(document.getElementById('deleteModal')).show(); }
document.getElementById('confirmDeleteBtn').addEventListener('click', async function(){
  if(!deleteId)return;
  var r=await API.certificats.delete(deleteId);
  if(r.success){ bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide(); Toast.fire({icon:'success',title:'Supprimé'}); loadData(); }
  else{ Swal.fire({icon:'error',text:r.message}); }
  deleteId=null;
});

function exportCSV(){ var t=$('#dataTable').DataTable(),d=t.rows({filter:'applied'}).data(),csv='\uFEFF#;N°Certificat;Etudiant;Type;Date\n'; d.each(function(r){ var c=[];for(var j=0;j<5;j++)c.push('"'+($(r[j]).text().trim()||'').replace(/"/g,'""')+'"'); csv+=c.join(';')+'\n'; }); var b=new Blob([csv],{type:'text/csv;charset=utf-8;'});var a=document.createElement('a');a.href=URL.createObjectURL(b);a.download='certificats.csv';a.click(); }

(function(){ var wait=setInterval(function(){ if(typeof jQuery!=='undefined'&&$.fn&&$.fn.DataTable&&typeof API!=='undefined'&&API.certificats){ clearInterval(wait); loadData(); $('#dtSearch').on('keyup',function(){$('#dataTable').DataTable().search(this.value).draw();}); $('#dtLength').on('change',function(){$('#dataTable').DataTable().page.len(+this.value).draw();}); } },50); })();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
