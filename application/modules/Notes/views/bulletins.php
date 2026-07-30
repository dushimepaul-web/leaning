<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<style>
:root {
  --clr-excellent: #059669; --clr-bon: #2563eb; --clr-moyen: #d97706; --clr-faible: #dc2626;
  --bg-excellent: #ecfdf5; --bg-bon: #eff6ff; --bg-moyen: #fffbeb; --bg-faible: #fef2f2;
  --radius: 8px; --shadow-sm: 0 1px 2px rgba(0,0,0,.04); --shadow-md: 0 4px 12px rgba(0,0,0,.06);
}
.bull-app *{box-sizing:border-box}
.bull-toolbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:16px;padding:12px 16px;background:#fff;border-radius:var(--radius);box-shadow:var(--shadow-sm)}
.bull-toolbar .btn-group{display:flex;gap:8px}
.bull-toolbar button{height:36px;padding:0 14px;font-size:13px;font-weight:500;border-radius:var(--radius);cursor:pointer;display:flex;align-items:center;gap:6px;transition:all .15s;border:1px solid #e5e7eb;background:#fff;color:#374151}
.bull-toolbar button:hover{background:#f9fafb;border-color:#d1d5db}
.bull-toolbar button.primary{background:#6366f1;color:#fff;border-color:#6366f1}
.bull-toolbar button.primary:hover{background:#4f46e5}
.bulletin-card{font-family:Arial,Calibri,sans-serif;background:#fff;border:1px solid #000;margin-bottom:20px;page-break-after:always;width:100%}
.bulletin-card .bul-header{padding:8px 12px 4px}
.bulletin-card .bul-header .h-row{display:flex;justify-content:space-between;font-size:13px;font-weight:700;line-height:1.6}
.bulletin-card .bul-header .h-row .h-right{text-align:right}
.bulletin-card .bul-body{overflow-x:auto}
.bulletin-card .bul-body table{width:100%;border-collapse:collapse;font-size:11px}
.bulletin-card .bul-body table th,.bulletin-card .bul-body table td{border:1px solid #000;padding:3px 4px;text-align:center;font-weight:400}
.bulletin-card .bul-body table thead th{background:#D9D9D9;font-weight:700;font-size:10px}
.bulletin-card .bul-body table thead th.branches-header{background:#fff;width:140px;min-width:140px;text-align:center}
.bulletin-card .bul-body table td.branches{text-align:left;font-weight:700;padding-left:6px}
.bulletin-card .bul-body table td.matiere{color:#000;font-weight:400}
.bulletin-card .bul-body table td.num{text-align:center}
.bulletin-card .bul-body table td.gris{background:#D9D9D9}
@media print {
  body *{visibility:hidden}
  #bulletinsCard,#bulletinsCard *{visibility:visible}
  #bulletinsCard{position:absolute;left:0;top:0;width:100%}
  .bull-toolbar{display:none!important}
  .bulletin-card{border:1px solid #000;margin-bottom:10px;page-break-after:always}
  .sidebar,.dashboard-main-body .breadcrumb{display:none!important}
}
@media(max-width:768px){.bull-toolbar{flex-direction:column;align-items:stretch}.bull-toolbar .btn-group{flex-wrap:wrap}}
</style>
<div class="dashboard-main-body bull-app">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Bulletins & Fiches de points</h1>
      <div><a href="<?=base_url('Dashboard')?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a><span class="text-secondary-light"> / Bulletins</span></div>
    </div>
    <a href="<?=base_url('Notes')?>" class="btn btn-outline-primary-600 d-flex align-items-center gap-6"><i class="ri-pencil-line"></i>Saisie des notes</a>
  </div>

  <div class="row gy-4" id="classesGrid"><div class="col-12 text-center py-32"><div class="spinner-border text-primary-600"></div><p class="mt-8 text-secondary-light">Chargement...</p></div></div>

  <div id="ficheCard" style="display:none;">
    <div class="bull-toolbar">
      <div class="btn-group">
        <button onclick="backToClasses()"><i class="ri-arrow-left-line"></i> Classes</button>
        <span style="font-weight:600;color:#1e293b;font-size:14px;padding:0 8px" id="ficheTitle">—</span>
      </div>
      <div class="btn-group">
        <button onclick="printFiche()"><i class="ri-printer-line"></i> Imprimer</button>
        <button class="primary" onclick="genererBulletinsClasse()"><i class="ri-magic-line"></i> Générer bulletins</button>
      </div>
    </div>
    <div class="bull-grid-container">
      <table class="bull-table">
        <thead id="ficheHead"></thead>
        <tbody id="ficheBody"><tr><td colspan="10" class="text-center py-32" style="color:#9ca3af">Chargement...</td></tr></tbody>
        <tfoot id="ficheFoot"></tfoot>
      </table>
    </div>
    <div class="bull-stats" id="ficheStats"></div>
  </div>

  <div id="bulletinsCard" style="display:none;">
    <div class="bull-toolbar">
      <div class="btn-group">
        <button onclick="backToClasses()"><i class="ri-arrow-left-line"></i> Classes</button>
        <span style="font-weight:600;color:#1e293b;font-size:14px;padding:0 8px" id="bulTitle">—</span>
      </div>
      <div class="btn-group">
        <button onclick="if(gClasseId) window.open(API.base_url + 'Notes/Bulletins/export/' + gClasseId, '_blank'); else Swal.fire({icon:'warning',title:'Attention',text:'Veuillez sélectionner une classe'});"><i class="ri-printer-line"></i> Imprimer tous</button>
        <button class="primary" onclick="genererBulletinsClasse()"><i class="ri-magic-line"></i> Générer bulletins</button>
      </div>
    </div>
    <div id="bulletinsList"></div>
  </div>
</div>

<div class="modal fade" id="trimestreModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-header border-bottom px-24 py-16"><h6 class="text-lg fw-semibold mb-0">Fiche de points</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body px-24 py-20">
        <p class="text-sm text-secondary-light mb-16">Choisissez la période :</p>
        <div class="d-flex flex-column gap-8" id="trimestreList"></div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="coursBulletinModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-header border-bottom px-24 py-16"><h6 class="text-lg fw-semibold mb-0">Bulletin</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body px-24 py-16">
        <p class="text-sm text-secondary-light mb-16">Classe : <strong id="bulClasseNom"></strong></p>
        <p class="text-sm text-secondary-light mb-16">Choisissez la période pour générer/voir les bulletins :</p>
        <div class="d-flex flex-column gap-8" id="bulPeriodeList"></div>
      </div>
    </div>
  </div>
</div>

<script id="classes_data" type="application/json"><?=json_encode($classes)?></script>
<script id="periodes_data" type="application/json"><?=json_encode($periodes)?></script>
<script id="annees_data" type="application/json"><?=json_encode($annees)?></script>
<script>var ACTIVE_ANNEE_ID='<?=$id_annee_active?>',ACTIVE_PERIODE_ID='<?=$id_periode_active?>';</script>
<script src="<?=base_url()?>assets/js/api.js"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
const Toast=Swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:2500,timerProgressBar:true});
let gClasseId=null,gClasseNom='',gFicheData=null;
try{var cList=JSON.parse(document.getElementById('classes_data').textContent)}catch(e){}
try{var pList=JSON.parse(document.getElementById('periodes_data').textContent)}catch(e){}
try{var aList=JSON.parse(document.getElementById('annees_data').textContent)}catch(e){}

async function loadClassesGrid(){
  const r=await fetch(API.base_url+'api/notes/classes_summary').then(r=>r.json());
  if(!r.success){document.getElementById('classesGrid').innerHTML='<div class="col-12 text-center py-32 text-danger">Erreur</div>';return}
  const colors=['primary','success','warning','danger','info'];
  let h='';
  r.data.forEach((c,i)=>{
    const cl=colors[i%colors.length];
    h+=`<div class="col-xxl-4 col-lg-4 col-md-6">
      <div class="card shadow-1 radius-12 overflow-hidden h-100" style="border-top:4px solid var(--bs-${cl})">
        <div class="card-body p-20">
          <div class="d-flex align-items-center gap-12 mb-16">
            <span class="d-flex align-items-center justify-content-center bg-${cl}-100 text-${cl}-600 radius-8 flex-shrink-0" style="width:48px;height:48px;font-size:20px"><i class="ri-school-line"></i></span>
            <div><h6 class="text-md fw-semibold mb-2">${c.classe}</h6>${c.section?`<small class="text-secondary-light">${c.section}</small>`:''}</div>
          </div>
          <div class="d-flex gap-8 mb-16">
            <div class="d-flex align-items-center gap-6 bg-neutral-50 px-10 py-6 radius-6 flex-grow-1"><i class="ri-user-3-line text-${cl}-600"></i><span class="fw-bold text-sm">${c.nb_etudiants}</span><span class="text-xs text-secondary-light"> élèves</span></div>
            <div class="d-flex align-items-center gap-6 bg-neutral-50 px-10 py-6 radius-6 flex-grow-1"><i class="ri-book-open-line text-${cl}-600"></i><span class="fw-bold text-sm">${c.nb_matieres}</span><span class="text-xs text-secondary-light"> cours</span></div>
          </div>
          <div class="d-flex gap-8">
            <button class="btn btn-outline-${cl}-600 flex-grow-1 d-flex align-items-center justify-content-center gap-6 py-8" onclick="openFicheModal('${c.id_classe}','${c.classe.replace(/'/g,"\\'")}')"><i class="ri-file-chart-line"></i>Fiche des points</button>
            <button class="btn btn-${cl}-600 flex-grow-1 d-flex align-items-center justify-content-center gap-6 py-8 text-white" onclick="openBulletinModal('${c.id_classe}','${c.classe.replace(/'/g,"\\'")}')"><i class="ri-file-list-3-line"></i>Bulletin</button>
          </div>
        </div>
      </div>
    </div>`;
  });
  document.getElementById('classesGrid').innerHTML=h||'<div class="col-12 text-center py-32 text-secondary-light">Aucune classe</div>';
}

function openFicheModal(id,nom){
  gClasseId=id;gClasseNom=nom;
  let html='';
  pList.forEach(p=>{html+=`<button class="btn border border-neutral-300 bg-hover-neutral-100 text-start px-16 py-12 radius-8 d-flex align-items-center gap-12 w-100" onclick="loadFicheParCours('${id}','${p.id_periode}','${p.libelle.replace(/'/g,"\\'")}')"><span class="d-flex align-items-center justify-content-center bg-success-100 text-success-600 radius-4" style="width:40px;height:40px"><i class="ri-calendar-check-line"></i></span><div><span class="fw-semibold text-sm">${p.libelle}</span></div><i class="ri-arrow-right-s-line ms-auto text-secondary-light"></i></button>`;});
  html+=`<button class="btn border border-neutral-300 bg-hover-neutral-100 text-start px-16 py-12 radius-8 d-flex align-items-center gap-12 w-100" onclick="loadFicheParCours('${id}','all','Année complète')"><span class="d-flex align-items-center justify-content-center bg-primary-100 text-primary-600 radius-4" style="width:40px;height:40px"><i class="ri-calendar-2-line"></i></span><div><span class="fw-semibold text-sm">Année complète</span><small class="d-block text-secondary-light text-xs">Tous les trimestres</small></div><i class="ri-arrow-right-s-line ms-auto text-secondary-light"></i></button>`;
  document.getElementById('trimestreList').innerHTML=html;
  new bootstrap.Modal(document.getElementById('trimestreModal')).show();
}

function openBulletinModal(id,nom){
  gClasseId=id;gClasseNom=nom;
  document.getElementById('bulClasseNom').textContent=nom;
  let html='';
  pList.forEach(p=>{html+=`<button class="btn border border-neutral-300 bg-hover-neutral-100 text-start px-16 py-12 radius-8 d-flex align-items-center gap-12 w-100" onclick="openBulletinPeriode('${id}','${p.id_periode}','${p.libelle.replace(/'/g,"\\'")}')"><span class="d-flex align-items-center justify-content-center bg-warning-100 text-warning-600 radius-4" style="width:40px;height:40px"><i class="ri-file-list-3-line"></i></span><div><span class="fw-semibold text-sm">${p.libelle}</span></div><i class="ri-arrow-right-s-line ms-auto text-secondary-light"></i></button>`;});
  html+=`<button class="btn border border-neutral-300 bg-hover-neutral-100 text-start px-16 py-12 radius-8 d-flex align-items-center gap-12 w-100" onclick="openBulletinPeriode('${id}','all','Année complète')"><span class="d-flex align-items-center justify-content-center bg-primary-100 text-primary-600 radius-4" style="width:40px;height:40px"><i class="ri-calendar-2-line"></i></span><div><span class="fw-semibold text-sm">Année complète</span></div><i class="ri-arrow-right-s-line ms-auto text-secondary-light"></i></button>`;
  document.getElementById('bulPeriodeList').innerHTML=html;
  new bootstrap.Modal(document.getElementById('coursBulletinModal')).show();
}

async function openBulletinPeriode(id,periodeId,periodeNom){
  bootstrap.Modal.getInstance(document.getElementById('coursBulletinModal')).hide();
  document.getElementById('classesGrid').style.display='none';
  document.getElementById('ficheCard').style.display='none';
  document.getElementById('bulletinsCard').style.display='';
  document.getElementById('bulTitle').textContent=gClasseNom+' — '+periodeNom;
  document.getElementById('bulletinsList').innerHTML='<div class="text-center py-32"><div class="spinner-border text-primary-600"></div><p class="mt-8 text-secondary-light">Chargement des bulletins...</p></div>';

  const url=API.base_url+'api/bulletins/complet/'+id+'?periode='+periodeId+'&annee='+ACTIVE_ANNEE_ID;
  const r=await fetch(url).then(r=>r.json());
  if(!r.success){Swal.fire({icon:'error',text:r.message});return}
  renderBulletins(r.data,periodeNom);
}

async function loadFicheParCours(id,periodeId,periodeNom){
  bootstrap.Modal.getInstance(document.getElementById('trimestreModal')).hide();
  document.getElementById('classesGrid').style.display='none';
  document.getElementById('ficheCard').style.display='';
  document.getElementById('ficheTitle').textContent=gClasseNom+' — '+periodeNom;
  document.getElementById('ficheHead').innerHTML='<tr><td colspan="10" class="text-center py-16" style="color:#9ca3af">Chargement...</td></tr>';
  document.getElementById('ficheBody').innerHTML='<tr><td colspan="10" class="text-center py-32" style="color:#9ca3af">Chargement...</td></tr>';

  const url=API.base_url+'api/fiches/fiche_par_cours/'+id+'?periode='+periodeId+'&annee='+ACTIVE_ANNEE_ID;
  const r=await fetch(url).then(r=>r.json());
  if(!r.success){Swal.fire({icon:'error',text:r.message});return}
  gFicheData=r.data;

  if(r.data.type==='periode'){
    renderFichePeriode(r.data);
  } else {
    renderFicheAnnee(r.data);
  }
}

function renderFichePeriode(data){
  const periodes=data.periodes;
  if(!periodes.length||!periodes[0].cours.length){document.getElementById('ficheBody').innerHTML='<tr><td colspan="10" class="text-center py-32 text-secondary-light">Aucune évaluation pour cette période</td></tr>';return}
  const cours=periodes[0].cours;

  // En-tête : 2 lignes
  let head='<tr>';
  head+='<th class="sticky-left col-no" rowspan="2">N°</th>';
  head+='<th class="sticky-left col-nom" rowspan="2">Nom & Prénom</th>';
  cours.forEach((c,i)=>{
    const isLast=(i===cours.length-1);
    head+=`<th class="course-group${isLast?' course-last':''}" colspan="2">${c.libelle}</th>`;
  });
  head+='<th rowspan="2">Total</th><th rowspan="2">Moyenne</th><th rowspan="2">%</th></tr>';
  head+='<tr>';
  cours.forEach((c,i)=>{
    const isLast=(i===cours.length-1);
    head+=`<th class="sub${isLast?' course-last':''}">Devoirs</th><th class="sub${isLast?' course-last':''}">Examen</th>`;
  });
  head+='</tr>';
  document.getElementById('ficheHead').innerHTML=head;

  // Corps
  let body='',totalMoy=0,nbMoy=0,allMoyennes=[];
  data.eleves.forEach((el,idx)=>{
    body+=`<tr><td class="sticky-left col-no">${idx+1}</td><td class="sticky-left col-nom">${el.etudiant.nom}</td>`;
    let colIdx=0;
    el.cours.forEach((c,i)=>{
      const isLast=(i===el.cours.length-1);
      body+=`<td class="note-dev${isLast?' course-last-col':''}">${c.dev_note!==null?c.dev_note.toFixed(2):'<span style="color:#d1d5db">—</span>'}</td>`;
      body+=`<td class="note-exam${isLast?' course-last-col':''}">${c.exam_note!==null?c.exam_note.toFixed(2):'<span style="color:#d1d5db">—</span>'}</td>`;
    });
    body+=`<td class="cell-total">${el.moyenne>0?el.total.toFixed(2):'—'}</td>`;
    body+=`<td class="cell-moy">${el.moyenne>0?el.moyenne.toFixed(2):'—'}</td>`;
    body+=`<td class="cell-pct">${el.moyenne>0?el.pourcentage.toFixed(1)+'%':'—'}</td>`;
    body+=`</tr>`;
    if(el.moyenne>0){totalMoy+=el.moyenne;nbMoy++;allMoyennes.push(el.moyenne)}
  });
  document.getElementById('ficheBody').innerHTML=body;

  // Footer
  const avgMoy=nbMoy>0?(totalMoy/nbMoy).toFixed(2):'—';
  const avgPct=nbMoy>0?((totalMoy/nbMoy)/20*100).toFixed(1)+'%':'—';
  let foot='<tr><td class="sticky-left col-no" colspan="2">Moyennes classe</td>';
  cours.forEach((c,i)=>{const isLast=(i===cours.length-1);foot+=`<td class="${isLast?'course-last-col':''}">—</td><td class="${isLast?'course-last-col':''}">—</td>`;});
  foot+=`<td>—</td><td>${avgMoy}</td><td>${avgPct}</td></tr>`;
  document.getElementById('ficheFoot').innerHTML=foot;

  // Stats
  document.getElementById('ficheStats').innerHTML=`
    <div class="stat-card"><div class="stat-icon" style="background:#eef2ff;color:#6366f1"><i class="ri-user-3-line"></i></div><div><div class="stat-val">${data.eleves.length}</div><div class="stat-lbl">Élèves</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:#ecfdf5;color:#059669"><i class="ri-book-open-line"></i></div><div><div class="stat-val">${cours.length}</div><div class="stat-lbl">Cours</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:#fffbeb;color:#d97706"><i class="ri-bar-chart-line"></i></div><div><div class="stat-val">${avgMoy}</div><div class="stat-lbl">Moy. classe /20</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:#eff6ff;color:#2563eb"><i class="ri-percent-line"></i></div><div><div class="stat-val">${avgPct}</div><div class="stat-lbl">% réussite</div></div></div>
  `;
}

function renderFicheAnnee(data){
  const allPeriodes=data.periodes;
  if(!allPeriodes.length){document.getElementById('ficheBody').innerHTML='<tr><td colspan="10" class="text-center py-32 text-secondary-light">Aucune donnée</td></tr>';return}

  const nbPeriodes=allPeriodes.length;
  const allCoursNames=[];
  const coursMap={};
  allPeriodes.forEach(pc=>{
    pc.cours.forEach(c=>{
      if(!coursMap[c.id_matiere]){coursMap[c.id_matiere]=c;allCoursNames.push(c)}
    });
  });

  // En-tête : années - une colonne par période + cours
  let head='<tr>';
  head+='<th class="sticky-left col-no" rowspan="2">N°</th>';
  head+='<th class="sticky-left col-nom" rowspan="2">Nom & Prénom</th>';
  // Colonnes par période (moyenne du trimestre)
  allPeriodes.forEach(pc=>{head+=`<th rowspan="2">${pc.periode_libelle}</th>`;});
  // Colonnes par cours
  allCoursNames.forEach((c,i)=>{const isLast=(i===allCoursNames.length-1);head+=`<th class="course-group${isLast?' course-last':''}" colspan="2">${c.libelle}</th>`;});
  head+='<th rowspan="2">Total</th><th rowspan="2">Moyenne</th><th rowspan="2">%</th></tr>';
  head+='<tr>';
  allCoursNames.forEach((c,i)=>{const isLast=(i===allCoursNames.length-1);head+=`<th class="sub${isLast?' course-last':''}">Devoirs</th><th class="sub${isLast?' course-last':''}">Examen</th>`;});
  head+='</tr>';
  document.getElementById('ficheHead').innerHTML=head;

  // Corps
  let body='',totalMoy=0,nbMoy=0,allMoyennes=[];
  data.eleves.forEach((el,idx)=>{
    body+=`<tr><td class="sticky-left col-no">${idx+1}</td><td class="sticky-left col-nom">${el.etudiant.nom}</td>`;
    // Moyennes par période (les cours sont groupés par période dans el.cours)
    let courseIdx=0;
    allPeriodes.forEach(pc=>{
      let perTotal=0,perCoef=0;
      for(let j=0;j<pc.cours.length;j++){
        const cd=el.cours[courseIdx+j];
        if(cd&&cd.cours_note!==null){perTotal+=cd.cours_note;perCoef++;}
      }
      const perMoy=perCoef>0?(perTotal/perCoef).toFixed(2):'—';
      body+=`<td style="font-weight:600;color:#6366f1">${perMoy}</td>`;
      courseIdx+=pc.cours.length;
    });
    // Colonnes cours
    courseIdx=0;
    allPeriodes.forEach(pc=>{
      for(let j=0;j<pc.cours.length;j++){
        const cd=el.cours[courseIdx+j];
        const isLastCourse=(courseIdx+j===el.cours.length-1);
        body+=`<td class="note-dev${isLastCourse?' course-last-col':''}">${cd&&cd.dev_note!==null?cd.dev_note.toFixed(2):'<span style="color:#d1d5db">—</span>'}</td>`;
        body+=`<td class="note-exam${isLastCourse?' course-last-col':''}">${cd&&cd.exam_note!==null?cd.exam_note.toFixed(2):'<span style="color:#d1d5db">—</span>'}</td>`;
      }
      courseIdx+=pc.cours.length;
    });
    body+=`<td class="cell-total">${el.moyenne>0?el.total.toFixed(2):'—'}</td>`;
    body+=`<td class="cell-moy">${el.moyenne>0?el.moyenne.toFixed(2):'—'}</td>`;
    body+=`<td class="cell-pct">${el.moyenne>0?el.pourcentage.toFixed(1)+'%':'—'}</td>`;
    body+=`</tr>`;
    if(el.moyenne>0){totalMoy+=el.moyenne;nbMoy++;allMoyennes.push(el.moyenne)}
  });
  document.getElementById('ficheBody').innerHTML=body;

  // Footer
  const avgMoy=nbMoy>0?(totalMoy/nbMoy).toFixed(2):'—';
  const avgPct=nbMoy>0?((totalMoy/nbMoy)/20*100).toFixed(1)+'%':'—';
  let foot='<tr><td class="sticky-left col-no" colspan="2">Moyennes classe</td>';
  allPeriodes.forEach(()=>{foot+='<td>—</td>';});
  allCoursNames.forEach((c,i)=>{const isLast=(i===allCoursNames.length-1);foot+=`<td class="${isLast?'course-last-col':''}">—</td><td class="${isLast?'course-last-col':''}">—</td>`;});
  foot+=`<td>—</td><td>${avgMoy}</td><td>${avgPct}</td></tr>`;
  document.getElementById('ficheFoot').innerHTML=foot;

  document.getElementById('ficheStats').innerHTML=`
    <div class="stat-card"><div class="stat-icon" style="background:#eef2ff;color:#6366f1"><i class="ri-user-3-line"></i></div><div><div class="stat-val">${data.eleves.length}</div><div class="stat-lbl">Élèves</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:#ecfdf5;color:#059669"><i class="ri-book-open-line"></i></div><div><div class="stat-val">${allCoursNames.length}</div><div class="stat-lbl">Cours</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:#fffbeb;color:#d97706"><i class="ri-bar-chart-line"></i></div><div><div class="stat-val">${avgMoy}</div><div class="stat-lbl">Moy. classe /20</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:#eff6ff;color:#2563eb"><i class="ri-percent-line"></i></div><div><div class="stat-val">${avgPct}</div><div class="stat-lbl">% réussite</div></div></div>
  `;
}

function backToClasses(){gClasseId=null;gFicheData=null;document.getElementById('ficheCard').style.display='none';document.getElementById('bulletinsCard').style.display='none';document.getElementById('classesGrid').style.display=''}

function printFiche(){window.print()}

async function genererBulletinsClasse(){
  if(!gClasseId){return}
  Swal.fire({title:'Génération...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
  const r=await fetch(API.base_url+'api/bulletins/generer',{method:'POST',headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},body:JSON.stringify({id_classe:gClasseId,id_annee:ACTIVE_ANNEE_ID})}).then(r=>r.json());
  Swal.close();
  r.success?Toast.fire({icon:'success',title:r.message}):Swal.fire({icon:'error',text:r.message})
}

function renderBulletins(data,periodeNom){
  const periodes=data.periodes||[];
  const matieres=data.matieres||[];
  const maxima=data.maxima||{};
  const maximaPeriode=data.maxima_periode||{};
  const section=data.section||data.classe_section||'';
  const anneeScolaire=data.annee_scolaire||'2025-2026';
  const classeNom=data.classe||'';

  function nf(v){return v!==undefined&&v!==null&&v>0?v.toFixed(1):'-'}

  let html='';
  data.eleves.forEach((el,idx)=>{
    // Compute per-period subject totals for A (matieres)
    const aPer={};
    let aAnnNote=0, aAnnMax=0;
    periodes.forEach(p=>{
      const pid=p.id_periode;
      let tj=0,ex=0,tp=0;
      matieres.forEach(mat=>{
        const matEl=el.matieres.find(m=>m.id_matiere==mat.id_matiere)||{periodes:{}};
        const per=matEl.periodes[pid]||{tj:0,comp:0,ress:0};
        tj+=per.tj; ex+=per.comp; tp+=per.ress;
      });
      aPer[pid]={tj,ex,tp,tot:tj+ex+tp};
    });
    matieres.forEach(mat=>{
      const matEl=el.matieres.find(m=>m.id_matiere==mat.id_matiere)||{annuel:{note:0,max:0}};
      aAnnNote+=matEl.annuel.note||0;
      aAnnMax+=matEl.annuel.max||0;
    });

    const conduiteVal=60, relTj=10, relEx=10, relTot=20;

    html+=`<div class="bulletin-card">
      <div class="bul-header">
        <div class="h-row">
          <span>SECTION: ${section||classeNom}</span>
          <span class="h-right">ANNEE SCOLAIRE : ${anneeScolaire}</span>
        </div>
        <div class="h-row">
          <span>NOM ET PRENOM : ${el.fullname||''}</span>
          <span class="h-right">Nombre d'Eleves : ${data.eleves.length}</span>
        </div>
        <div class="h-row">
          <span>N&deg; d'ordre : ${el.matricule||'.................'}</span>
        </div>
        <div class="h-row">
          <span>Classe : ${classeNom}</span>
        </div>
      </div>
      <div class="bul-body">
        <table>
          <thead>
            <tr>
              <th class="branches-header" rowspan="2"></th>
              <th colspan="4">MAXIMA</th>
              ${periodes.map(p=>`<th colspan="4">${p.libelle||''}</th>`).join('')}
              <th colspan="3">TOTAUX ANNUELS</th>
            </tr>
            <tr>
              <th>TJ</th><th>EX</th><th>TP</th><th>TOT</th>
              ${periodes.map(()=>'<th>TJ</th><th>EX</th><th>TP</th><th>TOT</th>').join('')}
              <th>MAX</th><th>TOT</th><th>%</th>
            </tr>
          </thead>
          <tbody>`;

    // ---- A: Matières ----
    matieres.forEach((mat,mi)=>{
      const mid=mat.id_matiere;
      const matEl=el.matieres.find(m=>m.id_matiere==mid)||{periodes:{},annuel:{note:0,max:0,pct:0}};

      const mxP=maxima[mid]&&maxima[mid][periodes[0].id_periode]||{};
      const max_tj=mxP.tj||0, max_ex=mxP.comp||0, max_tp=mxP.ress||0;
      const max_tot=max_tj+max_ex+max_tp;

      html+=`<tr>
        <td class="branches matiere">${mat.libelle}</td>
        <td class="num">${nf(max_tj)}</td><td class="num">${nf(max_ex)}</td><td class="num">${nf(max_tp)}</td><td class="num"><strong>${nf(max_tot)}</strong></td>`;

      periodes.forEach(p=>{
        const pid=p.id_periode;
        const per=matEl.periodes[pid]||{tj:0,comp:0,ress:0};
        const pt=per.tj+per.comp+per.ress;
        html+=`<td class="num">${nf(per.tj)}</td><td class="num">${nf(per.comp)}</td><td class="num">${nf(per.ress)}</td><td class="num"><strong>${nf(pt)}</strong></td>`;
      });

      html+=`<td class="num"><strong>${nf(matEl.annuel.max)}</strong></td>
        <td class="num"><strong>${nf(matEl.annuel.note)}</strong></td>
        <td class="num"><strong>${matEl.annuel.pct>0?matEl.annuel.pct.toFixed(2)+'%':'-'}</strong></td>
      </tr>`;
    });

    // ---- B: Sous-Tot ----
    html+=`<tr>
      <td class="branches">Sous-Tot</td>
      <td></td><td></td><td></td><td></td>`;
    periodes.forEach(p=>{
      const st=aPer[p.id_periode]||{tj:0,ex:0,tp:0,tot:0};
      html+=`<td></td><td></td><td></td><td class="num"><strong>${nf(st.tot)}</strong></td>`;
    });
    html+=`<td class="num"><strong>${nf(aAnnMax)}</strong></td>
      <td class="num"><strong>${nf(aAnnNote)}</strong></td>
      <td class="num"><strong>${aAnnMax>0?(aAnnNote/aAnnMax*100).toFixed(2)+'%':'-'}</strong></td>
    </tr>`;

    // ---- C: Conduite ----
    html+=`<tr>
      <td class="branches">Conduite</td>
      <td class="num gris">${conduiteVal}</td><td></td><td></td><td class="num">${conduiteVal}</td>`;
    periodes.forEach(()=>{
      html+=`<td></td><td></td><td></td><td class="num">${conduiteVal}</td>`;
    });
    html+=`<td class="num"><strong>${conduiteVal*periodes.length}</strong></td>
      <td class="num"><strong>${conduiteVal*periodes.length}</strong></td>
      <td>100.00%</td>
    </tr>`;

    // ---- D: Totaux ----
    html+=`<tr>
      <td class="branches">Totaux</td>
      <td></td><td></td><td></td><td></td>`;
    periodes.forEach(p=>{
      const st=aPer[p.id_periode]||{tj:0,ex:0,tp:0,tot:0};
      const totD=st.tot+conduiteVal;
      html+=`<td></td><td></td><td></td><td class="num"><strong>${nf(totD)}</strong></td>`;
    });
    html+=`<td class="num"><strong>${nf(aAnnMax+conduiteVal*periodes.length)}</strong></td>
      <td class="num"><strong>${nf(aAnnNote+conduiteVal*periodes.length)}</strong></td>
      <td class="num"><strong>${(aAnnMax+conduiteVal*periodes.length)>0?((aAnnNote+conduiteVal*periodes.length)/(aAnnMax+conduiteVal*periodes.length)*100).toFixed(2)+'%':'-'}</strong></td>
    </tr>`;

    // ---- E: Pourcentage ----
    html+=`<tr>
      <td class="branches">Pourcentage</td>
      <td></td><td></td><td></td><td></td>`;
    periodes.forEach(()=>{
      html+=`<td></td><td></td><td></td><td></td>`;
    });
    html+=`<td></td><td></td><td></td>
    </tr>`;

    // ---- F: Place ----
    html+=`<tr>
      <td class="branches">Place</td>
      <td></td><td></td><td></td><td></td>`;
    periodes.forEach(()=>{
      html+=`<td></td><td></td><td></td><td></td>`;
    });
    html+=`<td></td><td></td><td></td>
    </tr>`;

    // ---- G: Religion ----
    html+=`<tr>
      <td class="branches">Religion</td>
      <td class="num">${relTj}</td><td class="num">${relEx}</td><td></td><td class="num">${relTot}</td>`;
    periodes.forEach(()=>{
      html+=`<td class="num">${relTj}</td><td class="num">${relEx}</td><td></td><td class="num">${relTot}</td>`;
    });
    html+=`<td class="num"><strong>${relTot*periodes.length}</strong></td>
      <td></td><td></td>
    </tr>`;

    // ---- H: Signatures (2 rows) ----
    html+=`<tr>
      <td class="branches" rowspan="2">Signatures</td>
      <td>PARENTS</td><td></td><td></td><td></td>`;
    periodes.forEach(()=>{
      html+=`<td></td><td></td><td></td><td></td>`;
    });
    html+=`<td></td><td></td><td></td>
    </tr>`;
    html+=`<tr>
      <td>TITULAIRE</td><td></td><td></td><td></td>`;
    periodes.forEach(()=>{
      html+=`<td></td><td></td><td></td><td></td>`;
    });
    html+=`<td></td><td></td><td></td>
    </tr>`;

    html+=`</tbody>
        </table>
      </div>
    </div>`;
  });

  document.getElementById('bulletinsList').innerHTML=html||'<div class="text-center py-32 text-secondary-light">Aucun élève trouvé</div>';
}

(function(){var w=setInterval(function(){if(typeof jQuery!=='undefined'&&typeof API!=='undefined'){clearInterval(w);loadClassesGrid()}},50)})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>