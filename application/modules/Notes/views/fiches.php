<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<style>
.fiche-app{font-family:Arial,Calibri,sans-serif}
.fiche-app table{width:100%;border-collapse:collapse;font-size:11px}
.fiche-app table th,.fiche-app table td{border:1px solid #000;padding:2px 4px;text-align:center;font-weight:400}
.fiche-app table thead th{background:#D9D9D9;font-weight:700;font-size:10px}
.fiche-app table td.matiere{text-align:left;font-weight:700;padding-left:6px}
.fiche-app table td.num{text-align:center;min-width:48px}
.fiche-app table td.gris{background:#D9D9D9}
.fiche-row-g{background:#f1f3f5}
.fiche-row-d{background:#e9ecef}
@media print {
  .fiche-sidebar,.fiche-breadcrumb{display:none!important}
  .fiche-card{position:absolute;left:0;top:0;width:100%}
  .fiche-app table th{background:#D9D9D9!important}
}
</style>
<div class="dashboard-main-body fiche-app">
  <div class="breadcrumb fiche-breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Fiches de points</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Fiches de points</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-outline-primary-600 btn-sm" onclick="exportFiche()"><i class="ri-printer-line me-1"></i> Imprimer</button>
      <button type="button" class="btn btn-primary-600 btn-sm" onclick="exportFiche()"><i class="ri-file-pdf-line me-1"></i> PDF</button>
    </div>
  </div>

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
            <option value="all" selected>Tous les trimestres</option>
            <?php foreach($periodes as $p): ?><option value="<?=$p['id_periode']?>"><?=$p['libelle']?></option><?php endforeach; ?>
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

  <div class="row g-16 mb-24" id="statsRow" style="display:none;">
    <div class="col-md-3"><div class="card bg-primary-50 border-0"><div class="card-body p-16 text-center"><h6 class="text-sm text-secondary-light mb-4">Élèves</h6><h3 class="mb-0 text-primary-light" id="statNbEleves">0</h3></div></div></div>
    <div class="col-md-3"><div class="card bg-success-50 border-0"><div class="card-body p-16 text-center"><h6 class="text-sm text-secondary-light mb-4">Moy. classe</h6><h3 class="mb-0 text-success-600" id="statMoyClasse">0</h3></div></div></div>
    <div class="col-md-3"><div class="card bg-warning-50 border-0"><div class="card-body p-16 text-center"><h6 class="text-sm text-secondary-light mb-4">Taux réussite</h6><h3 class="mb-0 text-warning-600" id="statTaux">0%</h3></div></div></div>
    <div class="col-md-3"><div class="card bg-info-50 border-0"><div class="card-body p-16 text-center"><h6 class="text-sm text-secondary-light mb-4">Évaluations</h6><h3 class="mb-0 text-info-600" id="statNbEval">0</h3></div></div></div>
  </div>

  <div class="card fiche-card" id="ficheCard" style="display:none;">
    <div class="card-body p-0" style="overflow-x:auto;">
      <div id="ficheTitle" class="text-center fw-bold py-8" style="font-size:13px;border-bottom:1px solid #000;background:#fff"></div>
      <table class="table mb-0" id="ficheTable">
        <thead id="ficheHead"></thead>
        <tbody id="ficheBody"></tbody>
        <tfoot id="ficheFoot"></tfoot>
      </table>
    </div>
  </div>
</div>

<script id="classes_data" type="application/json"><?= json_encode($classes) ?></script>
<script src="<?= base_url() ?>assets/js/autocomplete.js?v=<?= filemtime(FCPATH.'assets/js/autocomplete.js') ?>"></script>
<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
var classesList=[];try{classesList=JSON.parse(document.getElementById('classes_data').textContent);}catch(e){}

function nf(v){return v!==undefined&&v!==null&&v>0?v.toFixed(1):'-'}

async function loadFiche(){
  var id_classe=document.getElementById('id_classe').value;
  if(!id_classe){Swal.fire({icon:'warning',title:'Sélection',text:'Veuillez choisir une classe'});return;}
  var p=document.getElementById('id_periode').value,a=document.getElementById('id_annee').value;
  Swal.fire({title:'Chargement...',allowOutsideClick:false,didOpen:function(){Swal.showLoading();}});
  var resp=await fetch(API.base_url+'api/bulletins/complet/'+id_classe+'?periode='+p+'&annee='+a);
  var r=await resp.json();
  Swal.close();
  if(!r.success){document.getElementById('statsRow').style.display='';document.getElementById('ficheCard').style.display='';document.getElementById('ficheHead').innerHTML='';document.getElementById('ficheBody').innerHTML='<tr><td colspan="20" class="text-center py-32 text-secondary-light">'+r.message+'</td></tr>';document.getElementById('ficheFoot').innerHTML='';return;}
  var data=r.data;

  document.getElementById('statsRow').style.display='';document.getElementById('ficheCard').style.display='';
  document.getElementById('statNbEleves').textContent=data.eleves.length;
  var moyClasse=data.eleves.reduce(function(s,e){return s+e.moyenne},0)/data.eleves.length;
  document.getElementById('statMoyClasse').textContent=moyClasse.toFixed(2);

  var periodes=data.periodes||[];
  var matieres=data.matieres||[];
  var maxima=data.maxima||{};
  var mxp=data.maxima_periode||{};
  var cls=data.classe||'';
  var an=data.annee_scolaire||'';

  var pLbl=periodes.map(function(p){return p.libelle||'P'+p.id_periode}).join(' - ');
  document.getElementById('ficheTitle').textContent='FICHE DE POINTS — '+cls+' | '+an+' | '+pLbl;

  var head='<tr>';
  head+='<th rowspan="2" style="width:32px">#</th>';
  head+='<th rowspan="2" style="min-width:170px;text-align:left">NOM ET PRENOMS</th>';
  head+='<th colspan="4" style="background:#e9ecef">MAXIMA</th>';
  periodes.forEach(function(pe){head+='<th colspan="4" style="background:#e8e8e8">'+pe.libelle+'</th>';});
  head+='<th colspan="3" style="background:#d9d9d9">TOTAUX ANNUELS</th></tr>';
  head+='<tr>';
  head+='<th class="num">TJ</th><th class="num">EX</th><th class="num">TP</th><th class="num">TOT</th>';
  periodes.forEach(function(){head+='<th class="num">TJ</th><th class="num">EX</th><th class="num">TP</th><th class="num">TOT</th>';});
  head+='<th class="num">MAX</th><th class="num">TOT</th><th class="num">%</th></tr>';
  document.getElementById('ficheHead').innerHTML=head;

  var body='';
  data.eleves.forEach(function(el,idx){
    var annNote=0,annMax=0;
    body+='<tr><td class="num">'+(idx+1)+'</td><td style="text-align:left;font-weight:600">'+el.fullname+'</td>';

    var pid0=periodes.length?periodes[0].id_periode:null;
    var pm=mxp[pid0]||{tj:0,comp:0,ress:0,tot:0};
    body+='<td class="num">'+nf(pm.tj)+'</td><td class="num">'+nf(pm.comp)+'</td><td class="num">'+nf(pm.ress)+'</td><td class="num"><strong>'+nf(pm.tot)+'</strong></td>';

    periodes.forEach(function(pe){
      var pid=pe.id_periode;
      var pt=el.totaux_periodes[pid]||{tj:0,comp:0,ress:0,tot:0};
      body+='<td class="num">'+(pt.tj>0?pt.tj.toFixed(1):'-')+'</td>';
      body+='<td class="num">'+(pt.comp>0?pt.comp.toFixed(1):'-')+'</td>';
      body+='<td class="num">'+(pt.ress>0?pt.ress.toFixed(1):'-')+'</td>';
      body+='<td class="num"><strong>'+(pt.tot>0?pt.tot.toFixed(1):'-')+'</strong></td>';
    });

    matieres.forEach(function(mat){
      var mid=mat.id_matiere;
      var matEl=el.matieres.find(function(m){return m.id_matiere==mid});
      annMax+=matEl&&matEl.annuel?matEl.annuel.max:0;
      annNote+=matEl&&matEl.annuel?matEl.annuel.note:0;
    });

    body+='<td class="num"><strong>'+nf(annMax)+'</strong></td>';
    body+='<td class="num"><strong>'+nf(annNote)+'</strong></td>';
    body+='<td class="num"><strong>'+(annMax>0?(annNote/annMax*100).toFixed(2)+'%':'-')+'</strong></td>';
    body+='</tr>';
  });
  document.getElementById('ficheBody').innerHTML=body;

  // Footer: Totaux classe
  var ftTj=0,ftCmp=0,ftRess=0;
  data.eleves.forEach(function(el){
    periodes.forEach(function(pe){
      var pt=el.totaux_periodes[pe.id_periode]||{};
      ftTj+=pt.tj||0;ftCmp+=pt.comp||0;ftRess+=pt.ress||0;
    });
  });
  var ftTot=ftTj+ftCmp+ftRess;
  var foot='<tr class="fiche-row-g" style="font-weight:bold">';
  foot+='<td colspan="2" style="text-align:left">TOTAUX ÉLÈVES</td>';
  foot+='<td class="num">'+nf(ftTj)+'</td><td class="num">'+nf(ftCmp)+'</td><td class="num">'+nf(ftRess)+'</td><td class="num"><strong>'+nf(ftTot)+'</strong></td>';
  periodes.forEach(function(pe){
    var sTj=0,sCmp=0,sRess=0;
    data.eleves.forEach(function(el){
      var pt=el.totaux_periodes[pe.id_periode]||{};
      sTj+=pt.tj||0;sCmp+=pt.comp||0;sRess+=pt.ress||0;
    });
    var sTot=sTj+sCmp+sRess;
    foot+='<td class="num">'+nf(sTj)+'</td><td class="num">'+nf(sCmp)+'</td><td class="num">'+nf(sRess)+'</td><td class="num"><strong>'+nf(sTot)+'</strong></td>';
  });
  foot+='<td class="num">-</td><td class="num">'+nf(ftTot)+'</td><td class="num">-</td></tr>';
  document.getElementById('ficheFoot').innerHTML=foot;

  var taux=data.eleves.filter(function(e){return e.moyenne>=10}).length/data.eleves.length*100;
  document.getElementById('statTaux').textContent=Math.round(taux)+'%';
  document.getElementById('statNbEval').textContent=matieres.length+' mat.';
}

function exportFiche(){
  var id_classe=document.getElementById('id_classe').value;
  if(!id_classe){Swal.fire({icon:'warning',title:'Sélection',text:'Veuillez choisir une classe'});return;}
  var p=document.getElementById('id_periode').value||'';
  window.open(API.base_url+'Notes/Fiches/export/'+id_classe+(p?'?periode='+p:''),'_blank');
}

(function(){var wait=setInterval(function(){if(typeof API!=='undefined'){clearInterval(wait);autoSetup('id_classe_search','id_classe','id_classe_results',classesList.map(function(c){return{id:c.id_classe,libelle:c.libelle};}),function(c){return c.libelle;});}},50);})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>