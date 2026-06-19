<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light" id="emploiTitle">Emploi du temps</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Horaires</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-outline-primary-600 d-flex align-items-center gap-6" onclick="switchView()">
        <span class="d-flex text-md"><i class="ri-list-check"></i></span>
        <span id="viewLabel">Vue liste</span>
      </button>
      <button type="button" class="btn btn-success-600 d-flex align-items-center gap-6" onclick="genererEmploi()">
        <span class="d-flex text-md"><i class="ri-magic-line"></i></span>
        Générer auto
      </button>
      <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
        <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
        Ajouter
      </button>
    </div>
  </div>

  <!-- Grille emploi du temps -->
  <div id="gridView" class="card h-100">
    <div class="card-body p-20">
      <div class="d-flex align-items-center justify-content-between mb-20">
        <h5 class="text-lg fw-semibold mb-0" id="gridGeneration"></h5>
        <div class="d-flex gap-8">
          <button type="button" class="btn btn-sm btn-neutral-200 text-secondary-light" onclick="printGrid()"><i class="ri-printer-line me-1"></i> Imprimer</button>
        </div>
      </div>
      <div class="table-responsive" style="overflow-x:auto;">
        <table class="table bordered-table table-sm mb-0" id="gridTable" style="min-width:700px;">
          <thead id="gridHead"></thead>
          <tbody id="gridBody">
            <tr><td colspan="10" class="text-center text-secondary-light py-32">
              <i class="ri-calendar-schedule-line d-block mb-8" style="font-size:40px;"></i>
              Aucun horaire généré. Cliquez sur <b>"Générer auto"</b> pour créer l'emploi du temps.
            </td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Vue liste (DataTable) -->
  <div id="listView" class="card h-100" style="display:none;">
    <div class="card-body p-0 dataTable-wrapper">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
        <div class="d-flex flex-wrap align-items-center gap-16">
          <div class="dropdown">
            <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown">
              <span class="d-flex align-items-center gap-1 text-secondary-light text-sm"><i class="ri-file-upload-line text-md line-height-1"></i> Export</span>
              <span><i class="ri-arrow-down-s-line"></i></span>
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
            <th>#</th><th>Jour</th><th>Créneau</th><th>Classe</th><th>Matière</th><th>Enseignant</th><th></th>
          </tr>
        </thead>
        <tbody id="dataBody"></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Sidebar -->
<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="sidebarOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="addSidebar" style="width:50vw;max-width:50vw;box-shadow: -4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="sidebarTitle">Ajouter un cours</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="mainForm" class="d-flex flex-column p-20">
    <div class="row g-3">
      <div class="col-md-6 position-relative">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe *</label>
        <input type="hidden" id="id_classe">
        <input type="text" class="form-control" id="id_classe_search" placeholder="Rechercher..." autocomplete="off">
        <div id="id_classe_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
      </div>
      <div class="col-md-6 position-relative">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Jour *</label>
        <input type="hidden" id="id_jour">
        <input type="text" class="form-control" id="id_jour_search" placeholder="Rechercher..." autocomplete="off">
        <div id="id_jour_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
      </div>
      <div class="col-md-6 position-relative">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Créneau *</label>
        <input type="hidden" id="id_creneau">
        <input type="text" class="form-control" id="id_creneau_search" placeholder="Rechercher..." autocomplete="off">
        <div id="id_creneau_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
      </div>
      <div class="col-md-6 position-relative">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Matière</label>
        <input type="hidden" id="id_matiere">
        <input type="text" class="form-control" id="id_matiere_search" placeholder="Rechercher..." autocomplete="off">
        <div id="id_matiere_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Enseignant (auto)</label>
        <div id="enseignant_info" class="form-control bg-neutral-50 text-sm d-flex align-items-center" style="min-height:38px;"><span class="text-secondary-light">Détecté automatiquement</span></div>
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Volume</label>
        <div id="volume_info" class="form-control bg-neutral-50 text-sm d-flex align-items-center" style="min-height:38px;"><span class="text-secondary-light">---</span></div>
      </div>
      <div class="col-12 d-flex align-items-center justify-content-center gap-3 mt-8">
        <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeSidebar()">Annuler</button>
        <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-body pt-32 px-36 pb-24 text-center">
        <span class="mb-16 fs-1 line-height-1 text-danger d-block"><i class="ri-delete-bin-6-line"></i></span>
        <h6 class="text-lg fw-semibold text-primary-light mb-0">Confirmer la suppression ?</h6>
        <p class="text-sm text-secondary-light mt-8">Cette action est irréversible.</p>
        <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
          <button type="button" class="flex-grow-1 border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-24 py-11 radius-8" data-bs-dismiss="modal">Annuler</button>
          <button type="button" id="confirmDeleteBtn" class="flex-grow-1 btn btn-danger border border-danger-600 text-md px-16 py-12 radius-8">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script id="classes_data" type="application/json"><?= json_encode($classes) ?></script>
<script id="creneaux_data" type="application/json"><?= json_encode($creneaux) ?></script>
<script id="jours_data" type="application/json"><?= json_encode($jours) ?></script>
<script id="matieres_data" type="application/json"><?= json_encode($matieres) ?></script>
<script id="enseignants_data" type="application/json"><?= json_encode($enseignants) ?></script>
<script src="<?= base_url() ?>assets/js/autocomplete.js?v=<?= filemtime(FCPATH.'assets/js/autocomplete.js') ?>"></script>
<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let editingId = null, deleteId = null, gridMode = true;
let classesList = [], joursList = [], creneauxList = [], matieresList = [], enseignantsList = [], allHoraires = [];

try { const el = document.getElementById('classes_data'); if (el) classesList = JSON.parse(el.textContent); } catch(e) {}
try { const el = document.getElementById('creneaux_data'); if (el) creneauxList = JSON.parse(el.textContent); } catch(e) {}
try { const el = document.getElementById('jours_data'); if (el) joursList = JSON.parse(el.textContent); } catch(e) {}
try { const el = document.getElementById('matieres_data'); if (el) matieresList = JSON.parse(el.textContent); } catch(e) {}
try { const el = document.getElementById('enseignants_data'); if (el) enseignantsList = JSON.parse(el.textContent); } catch(e) {}

function switchView() {
  gridMode = !gridMode;
  document.getElementById('gridView').style.display = gridMode ? '' : 'none';
  document.getElementById('listView').style.display = gridMode ? 'none' : '';
  document.getElementById('viewLabel').textContent = gridMode ? 'Vue liste' : 'Vue grille';
  if (!gridMode && !allHoraires.length) loadAllData();
}

function renderGrid() {
  const thead = document.getElementById('gridHead');
  const tbody = document.getElementById('gridBody');
  const genLabel = document.getElementById('gridGeneration');

  if (!allHoraires.length) {
    genLabel.textContent = '';
    thead.innerHTML = '';
    tbody.innerHTML = '<tr><td colspan="10" class="text-center text-secondary-light py-32"><i class="ri-calendar-schedule-line d-block mb-8" style="font-size:40px;"></i>Aucun horaire généré. Cliquez sur <b>"Générer auto"</b> pour créer l\'emploi du temps.</td></tr>';
    return;
  }

  // Obtenir classes avec horaires
  var classIds = [...new Set(allHoraires.map(h => h.id_classe))];
  var classesGrid = classIds.map(id => classesList.find(c => c.id_classe == id)).filter(Boolean);
  var joursGrid = joursList.filter(j => allHoraires.some(h => h.id_jour == j.id_jour));
  var creneauxGrid = creneauxList.filter(cr => allHoraires.some(h => h.id_creneau == cr.id_creneau));

  genLabel.textContent = 'Emploi du temps - Année ' + new Date().getFullYear();

  // Entête
  thead.innerHTML = '<tr><th style="min-width:80px;">Jour</th><th style="min-width:130px;">Créneau</th>' +
    classesGrid.map(c => '<th class="text-center" style="min-width:160px;">' + c.libelle + '<br><small class="text-secondary-light fw-normal">' + (c.code || '') + '</small></th>').join('') + '</tr>';

  // Corps
  var rows = '';
  joursGrid.forEach(function(jour) {
    var creneauxCount = creneauxGrid.length;
    creneauxGrid.forEach(function(creneau, crIdx) {
      var cells = '';
      classesGrid.forEach(function(classe) {
        var h = allHoraires.find(function(h) {
          return h.id_jour == jour.id_jour && h.id_creneau == creneau.id_creneau && h.id_classe == classe.id_classe;
        });
        if (h) {
          cells += '<td class="text-center align-middle">' +
            '<span class="fw-semibold text-sm d-block">' + (h.matiere || '-') + '</span>' +
            '<small class="text-secondary-light">' + (h.enseignant || '-') + '</small>' +
            '<button class="btn btn-xs text-danger p-0 ms-1" onclick="confirmDelete(\'' + h.uuid + '\')" title="Supprimer" style="font-size:14px;line-height:1;">' +
            '<i class="ri-close-line"></i></button>' +
            '</td>';
        } else {
          cells += '<td class="text-center text-secondary-light align-middle"><small>---</small></td>';
        }
      });
      var dayCell = '';
      if (crIdx === 0) {
        dayCell = '<td rowspan="' + creneauxCount + '" class="align-middle text-center" style="background:#f8f9fc;"><span class="fw-semibold">' + jour.libelle + '</span></td>';
      }
      rows += '<tr>' + dayCell +
        '<td class="text-nowrap"><small>' + creneau.libelle + '</small><br><small class="text-secondary-light">' + creneau.heure_debut + ' - ' + creneau.heure_fin + '</small></td>' + cells + '</tr>';
    });
  });
  tbody.innerHTML = rows;
}

async function loadAllData() {
  const r = await API.horaires.list();
  if (r.success) { allHoraires = r.data; renderGrid(); }
}

async function loadDataList() {
  const r = await API.horaires.list();
  if (!r.success) { $('#dataBody').html('<tr><td colspan="7" class="text-center text-danger">Erreur</td></tr>'); return; }
  allHoraires = r.data;
  var rows = '';
  r.data.forEach(function(h, i) {
    rows += '<tr><td>' + (i+1) + '</td><td><span class="fw-semibold">' + (h.jour||'-') + '</span></td>' +
      '<td>' + (h.creneau||'-') + '<small class="text-secondary-light d-block">' + (h.heure_debut||'') + '-' + (h.heure_fin||'') + '</small></td>' +
      '<td>' + (h.classe||'-') + '</td><td>' + (h.matiere||'-') + '</td><td>' + (h.enseignant||'-') + '</td>' +
      '<td><div class="btn-group"><button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>' +
      '<ul class="dropdown-menu dropdown-menu-lg-end border p-12"><li><button class="dropdown-item rounded text-danger d-flex align-items-center gap-2 py-6" onclick="confirmDelete(\'' + h.uuid + '\')"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li></ul></div></td></tr>';
  });
  $('#dataBody').html(rows);
  if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().destroy();
  $('#dataTable').DataTable({
    pageLength: 10, scrollX: true,
    lengthMenu: [[10,25,50,100],[10,25,50,100]],
    language: { search:'', searchPlaceholder:'Rechercher...', lengthMenu:'Lignes: _MENU_', info:'', zeroRecords:'Aucun', infoEmpty:'', infoFiltered:'' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

function openAddSidebar() {
  editingId = null;
  document.getElementById('sidebarTitle').textContent = 'Ajouter un cours';
  document.getElementById('mainForm').reset();
  ['id_classe','id_matiere','id_jour','id_creneau'].forEach(function(id) {
    document.getElementById(id).value = '';
    var si = document.getElementById(id+'_search');
    if (si) si.value = '';
  });
  document.getElementById('enseignant_info').innerHTML = '<span class="text-secondary-light">Détecté automatiquement</span>';
  document.getElementById('volume_info').innerHTML = '<span class="text-secondary-light">---</span>';
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
}

function closeSidebar() {
  document.getElementById('addSidebar').classList.remove('active');
  document.getElementById('sidebarOverlay').classList.remove('active');
}

document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);

document.getElementById('mainForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  var data = {
    id_classe: document.getElementById('id_classe').value,
    id_matiere: document.getElementById('id_matiere').value,
    id_jour: document.getElementById('id_jour').value,
    id_creneau: document.getElementById('id_creneau').value
  };
  if (!data.id_classe || !data.id_jour || !data.id_creneau) {
    Swal.fire({ icon:'warning', title:'Validation', text:'Classe, jour et créneau obligatoires' }); return;
  }
  var r = await API.horaires.create(data);
  if (r.success) {
    closeSidebar();
    Toast.fire({ icon:'success', title:'Cours ajouté' });
    loadAllData();
  } else { Swal.fire({ icon:'error', title:'Erreur', text:r.message }); }
});

function confirmDelete(id) {
  deleteId = id;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
  if (!deleteId) return;
  var r = await API.horaires.delete(deleteId);
  if (r.success) {
    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
    Toast.fire({ icon:'success', title:'Supprimé' });
    loadAllData();
  } else { Swal.fire({ icon:'error', title:'Erreur', text:r.message }); }
  deleteId = null;
});

async function genererEmploi() {
  var result = await Swal.fire({
    title: 'Générer l\'emploi du temps ?',
    html: 'Cette action va <b>remplacer</b> tous les horaires de l\'année.<br>Assignation automatique selon :<br>• Matières/classes/enseignants<br>• Disponibilités<br>• Contraintes horaires',
    icon: 'question', showCancelButton: true, confirmButtonText: 'Oui, générer', cancelButtonText: 'Annuler'
  });
  if (!result.isConfirmed) return;
  Swal.fire({ title:'Génération en cours...', allowOutsideClick:false, didOpen:function(){Swal.showLoading();} });
  var r = await fetch(API.base_url + 'api/horaires/generer', { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'} }).then(function(res){return res.json();});
  if (r.success) {
    Swal.fire({ icon:'success', title:'Génération terminée', text:r.message });
    gridMode = true;
    document.getElementById('gridView').style.display = '';
    document.getElementById('listView').style.display = 'none';
    document.getElementById('viewLabel').textContent = 'Vue liste';
    loadAllData();
  } else { Swal.fire({ icon:'error', title:'Erreur', text:r.message }); }
}

function exportCSV() {
  var t = $('#dataTable').DataTable();
  var d = t.rows({filter:'applied'}).data();
  var csv='\uFEFF#;Jour;Creneau;Classe;Matiere;Enseignant\n';
  d.each(function(r){var c=[];for(var j=0;j<6;j++)c.push('"'+($(r[j]).text().trim()||'').replace(/"/g,'""')+'"');csv+=c.join(';')+'\n';});
  var b=new Blob([csv],{type:'text/csv;charset=utf-8;'});var a=document.createElement('a');a.href=URL.createObjectURL(b);a.download='horaires.csv';a.click();
}

function printGrid() {
  var w = window.open('','_blank','width=1000,height=700');
  w.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Emploi du temps</title>');
  w.document.write('<style>body{font-family:Arial,sans-serif;padding:20px;}table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ccc;padding:8px 12px;text-align:center;font-size:13px;}th{background:#f5f5f5;}.matiere{font-weight:bold;}.ens{font-size:11px;color:#666;}</style></head><body>');
  w.document.write('<h2>Emploi du temps</h2>');
  w.document.write(document.getElementById('gridTable').outerHTML);
  w.document.write('</body></html>');
  w.document.close();
  setTimeout(function(){w.print();},500);
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && $.fn && $.fn.DataTable && typeof API !== 'undefined' && API.horaires) {
      clearInterval(wait);
      loadAllData();
      autoSetup('id_classe_search', 'id_classe', 'id_classe_results', classesList.map(function(c){return{id:c.id_classe,libelle:c.libelle};}), function(c){return c.libelle;});
      autoSetup('id_jour_search', 'id_jour', 'id_jour_results', joursList.map(function(j){return{id:j.id_jour,libelle:j.libelle};}), function(j){return j.libelle;});
      autoSetup('id_creneau_search', 'id_creneau', 'id_creneau_results', creneauxList.map(function(cr){return{id:cr.id_creneau,libelle:cr.libelle,heure_debut:cr.heure_debut,heure_fin:cr.heure_fin};}), function(cr){return cr.libelle+' ('+cr.heure_debut+'-'+cr.heure_fin+')';});
      autoSetup('id_matiere_search', 'id_matiere', 'id_matiere_results', matieresList.map(function(m){return{id:m.id_matiere,libelle:m.libelle};}), function(m){return m.libelle;});
      $('#dtSearch').on('keyup', function(){ $('#dataTable').DataTable().search(this.value).draw(); });
      $('#dtLength').on('change', function(){ $('#dataTable').DataTable().page.len(+this.value).draw(); });
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
