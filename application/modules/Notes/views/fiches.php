<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Fiches de points</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Fiches de points</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-outline-primary-600 btn-sm" onclick="printFiche()"><i class="ri-printer-line me-1"></i> Imprimer</button>
      <button type="button" class="btn btn-primary-600 btn-sm" onclick="exportPDF()"><i class="ri-file-pdf-line me-1"></i> PDF</button>
    </div>
  </div>

  <!-- Selection -->
  <div class="card mb-24">
    <div class="card-body p-16">
      <div class="row g-3 align-items-end">
        <div class="col-md-3 position-relative">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe</label>
          <input type="hidden" id="id_classe"><input type="text" class="form-control" id="id_classe_search" placeholder="Chercher..." autocomplete="off">
          <div id="id_classe_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
        </div>
        <div class="col-md-3">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Période</label>
          <select class="form-control form-select" id="id_periode">
            <?php foreach($periodes as $p): ?><option value="<?=$p['id_periode']?>" <?=$p['est_en_cours']?'selected':''?>><?=$p['libelle']?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Année</label>
          <select class="form-control form-select" id="id_annee">
            <?php foreach($annees as $a): ?><option value="<?=$a['id_annee']?>" <?=$a['est_en_cours']?'selected':''?>><?=$a['libelle']?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <button type="button" class="btn btn-success-600 w-100" onclick="loadFiche()"><i class="ri-search-line me-1"></i> Afficher</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Stats -->
  <div class="row g-16 mb-24" id="statsRow" style="display:none;">
    <div class="col-md-3"><div class="card bg-primary-50 border-0"><div class="card-body p-16 text-center"><h6 class="text-sm text-secondary-light mb-4">Élèves</h6><h3 class="mb-0 text-primary-light" id="statNbEleves">0</h3></div></div></div>
    <div class="col-md-3"><div class="card bg-success-50 border-0"><div class="card-body p-16 text-center"><h6 class="text-sm text-secondary-light mb-4">Moy. classe</h6><h3 class="mb-0 text-success-600" id="statMoyClasse">0</h3></div></div></div>
    <div class="col-md-3"><div class="card bg-warning-50 border-0"><div class="card-body p-16 text-center"><h6 class="text-sm text-secondary-light mb-4">Taux réussite</h6><h3 class="mb-0 text-warning-600" id="statTaux">0%</h3></div></div></div>
    <div class="col-md-3"><div class="card bg-info-50 border-0"><div class="card-body p-16 text-center"><h6 class="text-sm text-secondary-light mb-4">Évaluations</h6><h3 class="mb-0 text-info-600" id="statNbEval">0</h3></div></div></div>
  </div>

  <!-- Table -->
  <div class="card" id="ficheCard" style="display:none;">
    <div class="card-body p-0" style="overflow-x:auto;">
      <table class="table bordered-table mb-0 table-sm" id="ficheTable">
        <thead id="ficheHead"></thead>
        <tbody id="ficheBody"></tbody>
      </table>
    </div>
  </div>
</div>

<script id="classes_data" type="application/json"><?= json_encode($classes) ?></script>
<script src="<?= base_url() ?>assets/js/autocomplete.js?v=<?= filemtime(FCPATH.'assets/js/autocomplete.js') ?>"></script>
<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script>
var classesList=[];try{classesList=JSON.parse(document.getElementById('classes_data').textContent);}catch(e){}
var appBadges={Excellent:'bg-success-100 text-success-700','Très Bien':'bg-success-50 text-success-600',Bien:'bg-info-100 text-info-600','Assez Bien':'bg-warning-100 text-warning-600',Passable:'bg-warning-50 text-warning-700',Insuffisant:'bg-danger-100 text-danger-600','Sans notes':'bg-neutral-100 text-neutral-600'};

async function loadFiche(){
  var id_classe=document.getElementById('id_classe').value;
  if(!id_classe){Swal.fire({icon:'warning',title:'Sélection',text:'Veuillez choisir une classe'});return;}
  var p=document.getElementById('id_periode').value,a=document.getElementById('id_annee').value;
  Swal.fire({title:'Chargement...',allowOutsideClick:false,didOpen:function(){Swal.showLoading();}});
  var r=await fetch(API.base_url+'api/fiches/fiche?classe='+id_classe+'&periode='+p+'&annee='+a).then(r=>r.json());
  Swal.close();
  if(!r.success){Swal.fire({icon:'error',text:r.message});return;}

  document.getElementById('statsRow').style.display='';document.getElementById('ficheCard').style.display='';
  document.getElementById('statNbEleves').textContent=r.data.stats.nb_eleves;
  document.getElementById('statMoyClasse').textContent=r.data.stats.moyenne_classe.toFixed(2);
  document.getElementById('statTaux').textContent=r.data.stats.taux_reussite+'%';
  document.getElementById('statNbEval').textContent=r.data.stats.nb_evaluations;

  var evals=r.data.evaluations;
  var thead='<tr><th rowspan="2" class="text-center align-middle">#</th><th rowspan="2" class="text-center align-middle">Matricule</th><th rowspan="2" class="text-center align-middle">Élève</th>';
  evals.forEach(function(ev){thead+='<th class="text-center" style="min-width:70px;"><small class="d-block text-secondary-light">'+ev.matiere+'</small><small>'+ev.libelle+'</small><br><small class="text-secondary-light">/'+ev.sur+'</small></th>';});
  thead+='<th rowspan="2" class="text-center align-middle">Total</th><th rowspan="2" class="text-center align-middle">Moy.</th><th rowspan="2" class="text-center align-middle">Rang</th><th rowspan="2" class="text-center align-middle">Appréciation</th><th rowspan="2" class="text-center align-middle">Décision</th></tr>';
  document.getElementById('ficheHead').innerHTML=thead;

  var rows='';
  r.data.students.forEach(function(s,i){
    rows+='<tr><td class="text-center">'+(i+1)+'</td><td class="text-nowrap"><small>'+s.etudiant.matricule+'</small></td><td class="text-nowrap"><span class="fw-semibold text-sm">'+s.etudiant.nom+' '+s.etudiant.prenom+'</span></td>';
    evals.forEach(function(ev){rows+='<td class="text-center">'+(s.notes[ev.id_evaluation]!==null?s.notes[ev.id_evaluation].toFixed(1):'<span class="text-secondary-light">-</span>')+'</td>';});
    rows+='<td class="text-center fw-semibold">'+s.total.toFixed(1)+'</td><td class="text-center fw-bold">'+s.moyenne.toFixed(2)+'</td><td class="text-center">'+s.rang+'</td>';
    rows+='<td class="text-center"><span class="'+(appBadges[s.appreciation]||'')+' px-8 py-2 radius-4 text-xs fw-medium">'+s.appreciation+'</span></td>';
    rows+='<td class="text-center"><span class="fw-semibold text-sm">'+s.decision+'</span></td></tr>';
  });
  document.getElementById('ficheBody').innerHTML=rows;
}

function printFiche(){window.print();}
function exportPDF(){Swal.fire({icon:'info',title:'Export PDF',text:'Fonctionnalité à venir'});}

(function(){var wait=setInterval(function(){if(typeof API!=='undefined'){clearInterval(wait);autoSetup('id_classe_search','id_classe','id_classe_results',classesList.map(function(c){return{id:c.id_classe,libelle:c.libelle};}),function(c){return c.libelle;});}},50);})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
