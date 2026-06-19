<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Évaluations</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Évaluations</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span> Nouvelle évaluation
    </button>
  </div>
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
            <th>#</th><th>Libellé</th><th>Classe</th><th>Matière</th><th>Enseignant</th><th>Type</th><th>Coeff.</th><th>Sur</th><th>Période</th><th>Date</th><th></th>
          </tr>
        </thead>
        <tbody id="dataBody"></tbody>
      </table>
    </div>
  </div>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="sidebarOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="addSidebar" style="width:50vw;max-width:50vw;box-shadow: -4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="sidebarTitle">Nouvelle évaluation</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="mainForm" class="d-flex flex-column p-20">
    <input type="hidden" id="recordId">
    <div class="row g-3">
      <div class="col-md-8"><label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Libellé *</label><input class="form-control" id="libelle" placeholder="Ex: Interrogation 1"></div>
      <div class="col-md-4"><label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type</label>
        <select class="form-control form-select" id="type">
          <option value="devoir">Devoir</option><option value="interrogation">Interrogation</option><option value="controle">Contrôle</option><option value="composition">Composition</option><option value="examen">Examen</option><option value="tp">Travaux pratiques</option><option value="projet">Projet</option><option value="participation">Participation</option><option value="autre">Autre</option>
        </select>
      </div>
      <div class="col-md-6 position-relative">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe</label>
        <input type="hidden" id="id_classe"><input type="text" class="form-control" id="id_classe_search" placeholder="Rechercher..." autocomplete="off">
        <div id="id_classe_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
      </div>
      <div class="col-md-6 position-relative">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Matière</label>
        <input type="hidden" id="id_matiere"><input type="text" class="form-control" id="id_matiere_search" placeholder="Rechercher..." autocomplete="off">
        <div id="id_matiere_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
      </div>
      <div class="col-md-6"><label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Période</label>
        <select class="form-control form-select" id="id_periode"><option value="">Sélectionner</option><?php foreach($periodes as $p): ?><option value="<?=$p['id_periode']?>" <?=$p['est_en_cours']?'selected':''?>><?=$p['libelle']?></option><?php endforeach; ?></select>
      </div>
      <div class="col-md-6"><label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date</label><input type="date" class="form-control" id="date_eval" value="<?=date('Y-m-d')?>"></div>
      <div class="col-md-3"><label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Coefficient</label><input type="number" class="form-control" id="coefficient" step="0.1" value="1.0"></div>
      <div class="col-md-3"><label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Note max.</label><input type="number" class="form-control" id="sur" step="0.1" value="20.0"></div>
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

<script id="classes_data" type="application/json"><?= json_encode($classes) ?></script>
<script id="matieres_data" type="application/json"><?= json_encode($matieres) ?></script>
<script src="<?= base_url() ?>assets/js/autocomplete.js?v=<?= filemtime(FCPATH.'assets/js/autocomplete.js') ?>"></script>
<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script>
const Toast=Swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000,timerProgressBar:true});
let editingId=null,deleteId=null,classesList=[],matieresList=[];
try{classesList=JSON.parse(document.getElementById('classes_data').textContent);}catch(e){}
try{matieresList=JSON.parse(document.getElementById('matieres_data').textContent);}catch(e){}
var typeLabels={interrogation:'Interrogation',devoir:'Devoir',controle:'Contrôle',composition:'Composition',examen:'Examen',tp:'Travaux pratiques',projet:'Projet',participation:'Participation',autre:'Autre'};
var typeBadges={interrogation:'bg-info-100 text-info-600',devoir:'bg-primary-100 text-primary-600',controle:'bg-warning-100 text-warning-600',composition:'bg-danger-100 text-danger-600',examen:'bg-danger-200 text-danger-700',tp:'bg-success-100 text-success-600',projet:'bg-lilac-100 text-lilac-600',participation:'bg-neutral-100 text-neutral-600',autre:'bg-neutral-100 text-neutral-600'};

async function loadData(){
  const r=await API.evaluations.list();
  if(!r.success){$('#dataBody').html('<tr><td colspan="11" class="text-center text-danger">Erreur</td></tr>');return;}
  let rows='';
  r.data.forEach((e,i)=>{rows+='<tr><td>'+(i+1)+'</td><td><span class="fw-semibold">'+e.libelle+'</span></td><td>'+(e.classe||'-')+'</td><td>'+(e.matiere||'-')+'</td><td>'+(e.enseignant||'-')+'</td><td><span class="'+(typeBadges[e.type]||'')+' px-24 py-4 radius-4 fw-medium text-sm">'+(typeLabels[e.type]||e.type)+'</span></td><td>'+e.coefficient+'</td><td>'+e.sur+'</td><td>'+(e.periode||'-')+'</td><td>'+e.date_eval+'</td><td><div class="btn-group"><button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button><ul class="dropdown-menu dropdown-menu-lg-end border p-12"><li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editRecord(\''+e.uuid+'\')"><i class="ri-edit-2-line"></i> Modifier</button></li><li><button class="dropdown-item rounded text-danger d-flex align-items-center gap-2 py-6" onclick="confirmDelete(\''+e.uuid+'\')"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li></ul></div></td></tr>';});
  $('#dataBody').html(rows);
  if($.fn.DataTable.isDataTable('#dataTable'))$('#dataTable').DataTable().destroy();
  $('#dataTable').DataTable({pageLength:10,scrollX:true,lengthMenu:[[10,25,50,100],[10,25,50,100]],language:{search:'',searchPlaceholder:'Rechercher...',lengthMenu:'Lignes:_MENU_',info:'',zeroRecords:'Aucune',infoEmpty:'',infoFiltered:''},dom:'t<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'});
}

function openAddSidebar(){editingId=null;document.getElementById('sidebarTitle').textContent='Nouvelle évaluation';document.getElementById('mainForm').reset();document.getElementById('recordId').value='';['id_classe','id_matiere'].forEach(function(id){document.getElementById(id).value='';var s=document.getElementById(id+'_search');if(s)s.value='';});document.getElementById('date_eval').value=new Date().toISOString().split('T')[0];document.getElementById('addSidebar').classList.add('active');document.getElementById('sidebarOverlay').classList.add('active');}
function closeSidebar(){document.getElementById('addSidebar').classList.remove('active');document.getElementById('sidebarOverlay').classList.remove('active');}
document.getElementById('sidebarOverlay').addEventListener('click',closeSidebar);

async function editRecord(id){const r=await API.evaluations.list();if(r.success){const e=r.data.find(x=>x.uuid==id);if(e){editingId=e.uuid;document.getElementById('sidebarTitle').textContent='Modifier l\'évaluation';document.getElementById('recordId').value=e.uuid;document.getElementById('libelle').value=e.libelle||'';document.getElementById('type').value=e.type||'devoir';document.getElementById('id_classe').value=e.id_classe||'';document.getElementById('id_classe_search').value=e.classe||'';document.getElementById('id_matiere').value=e.id_matiere||'';document.getElementById('id_matiere_search').value=e.matiere||'';document.getElementById('id_periode').value=e.id_periode||'';document.getElementById('date_eval').value=e.date_eval||'';document.getElementById('coefficient').value=e.coefficient||1;document.getElementById('sur').value=e.sur||20;document.getElementById('addSidebar').classList.add('active');document.getElementById('sidebarOverlay').classList.add('active');}}}

document.getElementById('mainForm').addEventListener('submit',async function(e){e.preventDefault();
  var d={libelle:document.getElementById('libelle').value,id_classe:document.getElementById('id_classe').value,id_matiere:document.getElementById('id_matiere').value,id_periode:document.getElementById('id_periode').value,date_eval:document.getElementById('date_eval').value,type:document.getElementById('type').value,coefficient:document.getElementById('coefficient').value||1,sur:document.getElementById('sur').value||20};
  if(!d.libelle){Swal.fire({icon:'warning',title:'Validation',text:'Libellé obligatoire'});return;}
  var r;if(editingId){r=await API.evaluations.update(editingId,d);}else{r=await API.evaluations.create(d);}
  if(r.success){closeSidebar();Toast.fire({icon:'success',title:editingId?'Évaluation modifiée':'Évaluation créée'});loadData();}else{Swal.fire({icon:'error',title:'Erreur',text:r.message});}
});

function confirmDelete(id){deleteId=id;new bootstrap.Modal(document.getElementById('deleteModal')).show();}
document.getElementById('confirmDeleteBtn').addEventListener('click',async function(){if(!deleteId)return;var r=await API.evaluations.delete(deleteId);if(r.success){bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();Toast.fire({icon:'success',title:'Supprimé'});loadData();}else{Swal.fire({icon:'error',text:r.message});}deleteId=null;});

function exportCSV(){var t=$('#dataTable').DataTable(),d=t.rows({filter:'applied'}).data(),csv='\uFEFF#;Libelle;Classe;Matiere;Enseignant;Type;Coeff;Sur;Periode;Date\n';d.each(function(r){var c=[];for(var j=0;j<10;j++)c.push('"'+($(r[j]).text().trim()||'').replace(/"/g,'""')+'"');csv+=c.join(';')+'\n';});var b=new Blob([csv],{type:'text/csv;charset=utf-8;'});var a=document.createElement('a');a.href=URL.createObjectURL(b);a.download='evaluations.csv';a.click();}

(function(){var wait=setInterval(function(){if(typeof jQuery!=='undefined'&&$.fn&&$.fn.DataTable&&typeof API!=='undefined'&&API.evaluations){clearInterval(wait);loadData();autoSetup('id_classe_search','id_classe','id_classe_results',classesList.map(function(c){return{id:c.id_classe,libelle:c.libelle};}),function(c){return c.libelle;});autoSetup('id_matiere_search','id_matiere','id_matiere_results',matieresList.map(function(m){return{id:m.id_matiere,libelle:m.libelle};}),function(m){return m.libelle;});$('#dtSearch').on('keyup',function(){$('#dataTable').DataTable().search(this.value).draw();});$('#dtLength').on('change',function(){$('#dataTable').DataTable().page.len(+this.value).draw();});}},50);})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
