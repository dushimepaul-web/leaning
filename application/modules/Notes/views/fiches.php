<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<style>
.fiche-app{font-family:Arial,Calibri,sans-serif}
.fiche-app table{width:100%;border-collapse:collapse;font-size:11px;table-layout:fixed}
.fiche-app table th,.fiche-app table td{border:1px solid #000;padding:2px 4px;text-align:center;font-weight:400}
.fiche-app table thead th{background:#D9D9D9;font-weight:700;font-size:10px}
.fiche-app table thead th.branches-header{background:#fff;width:140px;min-width:140px;text-align:center}
.fiche-app table td.matiere{text-align:left;font-weight:700;padding-left:6px}
.fiche-app table td.branches{text-align:left;font-weight:700;padding-left:6px}
.fiche-app table td.num{text-align:center;min-width:48px}
.fiche-app table td.gris{background:#D9D9D9}
.fiche-row-g{background:#f1f3f5}
.fiche-row-d{background:#e9ecef}
.bul-header{padding:8px 12px 4px;background:#fff}
.h-row{display:flex;justify-content:space-between;font-size:13px;font-weight:700;line-height:1.6;text-transform:uppercase}
.h-row .h-right{text-align:right}
.h-row .titre-cours{font-weight:900;color:#1e293b}
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
      <div class="row g-2 align-items-end flex-nowrap">
        <div class="col" style="min-width:220px;position:relative;">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe *</label>
          <input type="hidden" id="id_classe"><input type="text" class="form-control" id="id_classe_search" placeholder="Tapez pour chercher..." autocomplete="off">
          <div id="id_classe_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
        </div>
        <div class="col" style="min-width:220px;position:relative;">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Cours</label>
          <input type="hidden" id="id_matiere"><input type="text" class="form-control" id="id_matiere_search" placeholder="Tapez pour chercher..." autocomplete="off">
          <div id="id_matiere_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
        </div>
        <div class="col" style="min-width:160px;">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Période</label>
          <select class="form-control form-select" id="id_periode">
            <option value="all" selected>Tous les trimestres</option>
            <?php foreach($periodes as $p): ?><option value="<?=$p['id_periode']?>" data-annee="<?=$p['id_annee']?>"><?=$p['libelle']?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col" style="min-width:160px;">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Année</label>
          <select class="form-control form-select" id="id_annee">
            <?php foreach($annees as $a): ?><option value="<?=$a['id_annee']?>" <?=$a['est_en_cours']?'selected':''?>><?=$a['libelle']?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-success-600" onclick="loadFiche()"><i class="ri-search-line me-1"></i> Afficher</button>
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
      <div class="bul-header" id="ficheHeader" style="border-bottom:1px solid #000"></div>
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
var matieresList=[];

function chargerCours(idClasse){
  matieresList.length=0;
  var hid=document.getElementById('id_matiere'),inp=document.getElementById('id_matiere_search');
  if(hid){hid.value='';}
  if(inp){inp.value='';inp.classList.remove('border-success','border-2');}
  if(!idClasse){return;}
  fetch(API.base_url+'api/notes/matieres_by_classe/'+idClasse).then(function(r){return r.json();}).then(function(res){
    if(!res.success||!res.data.length){return;}
    res.data.forEach(function(m){matieresList.push({id:m.id_matiere,libelle:m.libelle});});
  });
}

function nf(v){return v!==undefined&&v!==null&&v>0?v.toFixed(1):'-'}

async function loadFiche(){
  var id_classe=document.getElementById('id_classe').value;
  if(!id_classe){Swal.fire({icon:'warning',title:'Sélection',text:'Veuillez choisir une classe'});return;}
  var p=document.getElementById('id_periode').value,a=document.getElementById('id_annee').value;
  Swal.fire({title:'Chargement...',allowOutsideClick:false,didOpen:function(){Swal.showLoading();}});
  var resp=await fetch(API.base_url+'api/bulletins/complet/'+id_classe+'?periode='+p+'&annee='+a+'&cumul=1');
  var r=await resp.json();
  Swal.close();
  if(!r.success){document.getElementById('statsRow').style.display='';document.getElementById('ficheCard').style.display='';document.getElementById('ficheHead').innerHTML='';document.getElementById('ficheBody').innerHTML='<tr><td colspan="20" class="text-center py-32 text-secondary-light">'+r.message+'</td></tr>';document.getElementById('ficheFoot').innerHTML='';return;}
  var data=r.data;

  document.getElementById('statsRow').style.display='';document.getElementById('ficheCard').style.display='';
  document.getElementById('statNbEleves').textContent=data.eleves.length;

  var periodes=data.periodes||[];
  var matieres=data.matieres||[];
  var idMatiere=document.getElementById('id_matiere').value;
  if(idMatiere){matieres=matieres.filter(function(m){return String(m.id_matiere)===String(idMatiere);});}
  var cls=data.classe||'';
  var an=data.annee_scolaire||'';
  var ressActive = (data.ressources_active===undefined ? 1 : parseInt(data.ressources_active)) !== 0;
  var compActive = (data.competences_active===undefined ? 1 : parseInt(data.competences_active)) !== 0;
  var bothActive = ressActive && compActive;
  var colSpan = bothActive ? 4 : 3;
  var subHead = bothActive ? '<th>TJ</th><th>RESS</th><th>COMP</th><th>TOT</th>' : '<th>TJ</th><th>EX</th><th>TOT</th>';
  function cells(t){ return bothActive
    ? [nf(t.tj), nf(t.ress), nf(t.comp), '<strong>'+nf(t.tot)+'</strong>']
    : [nf(t.tj), nf(t.comp + t.ress), '<strong>'+nf(t.tot)+'</strong>']; }
  var pids=periodes.map(function(p){return p.id_periode});

  var section=data.section||data.classe_section||'';
  var titreCours=idMatiere&&matieres.length?matieres[0].libelle:'TOUS LES COURS';

  // Mode cumul : trimestre choisi = cumul depuis le 1er trimestre ; 'all' = bulletin complet
  var cumulMode = p!=='all';
  var selIdx = periodes.length-1;
  if(p!=='all'){
    for(var i=0;i<periodes.length;i++){if(String(periodes[i].id_periode)===String(p)){selIdx=i;break;}}
  }

  var pLbl=cumulMode?(periodes[selIdx]&&(periodes[selIdx].libelle||'P'+periodes[selIdx].id_periode)||''):'';
  document.getElementById('ficheHeader').innerHTML=
    '<div class="h-row"><span>SECTION: '+(section||cls)+'</span><span class="h-right">ANNEE SCOLAIRE : '+(an||'')+'</span></div>'+
    '<div class="h-row"><span>Classe : '+(cls||'')+'</span><span class="h-right">Nombre d\'Eleves : '+data.eleves.length+'</span></div>'+
    '<div class="h-row"><span class="titre-cours">FICHE DE POINTS — '+titreCours+'</span><span class="h-right">'+(pLbl||'')+'</span></div>';

  function cumMax(mid,i){
    var t={tj:0,comp:0,ress:0,tot:0};
    for(var k=0;k<=i;k++){
      var mm=data.maxima&&data.maxima[mid]&&data.maxima[mid][pids[k]];
      if(mm){t.tj+=mm.tj||0;t.comp+=mm.comp||0;t.ress+=mm.ress||0;}
    }
    t.tot=t.tj+t.comp+t.ress;
    return t;
  }
  function midMax(mid,i){
    var mm=data.maxima&&data.maxima[mid]&&data.maxima[mid][pids[i]];
    if(!mm){return {tj:0,comp:0,ress:0,tot:0};}
    return {tj:mm.tj||0,comp:mm.comp||0,ress:mm.ress||0,tot:(mm.tj||0)+(mm.comp||0)+(mm.ress||0)};
  }
  function cumNotes(el,mid,i){
    var me=el.matieres.find(function(m){return m.id_matiere==mid;});
    var t={tj:0,comp:0,ress:0,tot:0};
    for(var k=0;k<=i;k++){
      var pp=(me&&me.periodes)?me.periodes[pids[k]]:null;
      if(pp){t.tj+=pp.tj||0;t.comp+=pp.comp||0;t.ress+=pp.ress||0;}
    }
    t.tot=t.tj+t.comp+t.ress;
    return t;
  }
  function maxCol(mid,i){return cumulMode?cumMax(mid,i):midMax(mid,i);}
  function noteCol(el,mid,i){return cumulMode?cumNotes(el,mid,i):(function(){var me=el.matieres.find(function(m){return m.id_matiere==mid;});var pp=(me&&me.periodes)?me.periodes[pids[i]]:null;var t={tj:0,comp:0,ress:0};if(pp){t.tj=pp.tj||0;t.comp=pp.comp||0;t.ress=pp.ress||0;}t.tot=t.tj+t.comp+t.ress;return t;})();}

  // En-tête identique au bulletin
  var head='<tr>';
  head+='<th class="branches-header" rowspan="2"></th>';
  head+='<th colspan="'+colSpan+'">MAXIMA</th>';
  periodes.forEach(function(pe){head+='<th colspan="'+colSpan+'">'+pe.libelle+'</th>';});
  head+='<th colspan="3">TOTAUX ANNUELS</th></tr>';
  head+='<tr>';
  head+=subHead;
  periodes.forEach(function(){head+=subHead;});
  head+='<th>MAX</th><th>TOT</th><th>%</th></tr>';
  document.getElementById('ficheHead').innerHTML=head;

  // Corps : ligne MAXIMA + matières
  var mTot={tj:0,comp:0,ress:0,tot:0};
  matieres.forEach(function(m){
    var mm=cumMax(m.id_matiere,selIdx);
    mTot.tj+=mm.tj;mTot.comp+=mm.comp;mTot.ress+=mm.ress;mTot.tot+=mm.tot;
  });

  var body='<tr class="fiche-row-g" style="font-weight:700">';
  body+='<td class="matiere">MAXIMA</td>';
  body+=cells(mTot).map(function(c){return '<td class="num">'+c+'</td>';}).join('');
  periodes.forEach(function(pe,i){
    var colT={tj:0,comp:0,ress:0,tot:0};
    if(!cumulMode||i<=selIdx){
      matieres.forEach(function(m){var mm=maxCol(m.id_matiere,i);colT.tj+=mm.tj;colT.comp+=mm.comp;colT.ress+=mm.ress;colT.tot+=mm.tot;});
    }
    body+=cells(colT).map(function(c){return '<td class="num">'+c+'</td>';}).join('');
  });
  body+='<td class="num"><strong>'+nf(mTot.tot)+'</strong></td><td class="num"><strong>'+nf(mTot.tot)+'</strong></td><td class="num">100%</td></tr>';

  matieres.forEach(function(m){
    var mm=cumMax(m.id_matiere,selIdx);
    body+='<tr>';
    body+='<td class="matiere">'+m.libelle+'</td>';
    body+=cells(mm).map(function(c){return '<td class="num">'+c+'</td>';}).join('');
    periodes.forEach(function(pe,i){
      if(cumulMode&&i>selIdx){body+=('<td class="num">-</td>').repeat(colSpan);return;}
      // Cumuls au niveau classe par matière (somme élèves)
      var t={tj:0,comp:0,ress:0,tot:0};
      data.eleves.forEach(function(el){var nm=noteCol(el,m.id_matiere,i);t.tj+=nm.tj;t.comp+=nm.comp;t.ress+=nm.ress;t.tot+=nm.tot;});
      body+=cells(t).map(function(c){return '<td class="num">'+c+'</td>';}).join('');
    });
    var annT={tj:0,comp:0,ress:0,tot:0};
    data.eleves.forEach(function(el){var nm=noteCol(el,m.id_matiere,selIdx);annT.tj+=nm.tj;annT.comp+=nm.comp;annT.ress+=nm.ress;annT.tot+=nm.tot;});
    body+='<td class="num"><strong>'+nf(mm.tot)+'</strong></td>';
    body+='<td class="num"><strong>'+nf(annT.tot)+'</strong></td>';
    body+='<td class="num"><strong>'+(mm.tot>0?(annT.tot/mm.tot*100).toFixed(2)+'%':'-')+'</strong></td>';
    body+='</tr>';
  });
  document.getElementById('ficheBody').innerHTML=body;

  // Footer: Totaux élèves
  var foot='<tr class="fiche-row-g" style="font-weight:bold">';
  foot+='<td class="matiere">TOTAUX ÉLÈVES</td>';
  foot+=cells(mTot).map(function(c){return '<td class="num">'+c+'</td>';}).join('');
  var gTot=0;
  periodes.forEach(function(pe,i){
    var t={tj:0,comp:0,ress:0,tot:0};
    if(!cumulMode||i<=selIdx){
      matieres.forEach(function(m){
        data.eleves.forEach(function(el){var nm=noteCol(el,m.id_matiere,i);t.tj+=nm.tj;t.comp+=nm.comp;t.ress+=nm.ress;t.tot+=nm.tot;});
      });
    }
    if(i===selIdx||(!cumulMode&&i===periodes.length-1)){gTot=t.tot;}
    foot+=cells(t).map(function(c){return '<td class="num">'+c+'</td>';}).join('');
  });
  foot+='<td class="num"><strong>'+nf(mTot.tot)+'</strong></td>';
  foot+='<td class="num"><strong>'+nf(gTot)+'</strong></td>';
  foot+='<td class="num"><strong>'+(mTot.tot>0?(gTot/mTot.tot*100).toFixed(2)+'%':'-')+'</strong></td></tr>';
  document.getElementById('ficheFoot').innerHTML=foot;

  // Stats (moyenne/taux recalculés sur la sélection)
  var mks=data.eleves.map(function(el){
    var n=0,mx=0;
    matieres.forEach(function(mat){
      var nm=noteCol(el,mat.id_matiere,selIdx);
      var mm=cumMax(mat.id_matiere,selIdx);
      n+=nm.tot;mx+=mm.tot;
    });
    return mx>0?n/mx*20:0;
  });
  var moyClasse=mks.reduce(function(s,k){return s+k},0)/mks.length;
  var taux=mks.filter(function(k){return k>=10}).length/mks.length*100;
  document.getElementById('statMoyClasse').textContent=moyClasse.toFixed(2);
  document.getElementById('statTaux').textContent=Math.round(taux)+'%';
  document.getElementById('statNbEval').textContent=matieres.length+' mat.';
}

function filterPeriodeFiches() {
  var aid=document.getElementById('id_annee').value;
  var sel=document.getElementById('id_periode');
  var ok=false;
  Array.prototype.forEach.call(sel.options,function(opt){
    if(!opt.value){return;}
    var visible=!opt.dataset.annee||opt.dataset.annee===aid;
    opt.style.display=visible?'':'none';
    if(visible&&opt.selected){ok=true;}
  });
  if(!ok){sel.value='all';}
}

document.getElementById('id_annee').addEventListener('change',function(){filterPeriodeFiches();});

function exportFiche(){
  var id_classe=document.getElementById('id_classe').value;
  if(!id_classe){Swal.fire({icon:'warning',title:'Sélection',text:'Veuillez choisir une classe'});return;}
  var p=document.getElementById('id_periode').value||'';
  var m=document.getElementById('id_matiere').value||'';
  var url=API.base_url+'Notes/Fiches/export/'+id_classe+'?'+(p?'periode='+p:'')+(m?'&matiere='+m:'');
  window.open(url,'_blank');
}

(function(){var wait=setInterval(function(){if(typeof API!=='undefined'){clearInterval(wait);autoSetup('id_classe_search','id_classe','id_classe_results',classesList.map(function(c){return{id:c.id_classe,libelle:c.libelle};}),function(c){return c.libelle;},function(){chargerCours(document.getElementById('id_classe').value);});autoSetup('id_matiere_search','id_matiere','id_matiere_results',matieresList,function(m){return m.libelle;});}},50);})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>