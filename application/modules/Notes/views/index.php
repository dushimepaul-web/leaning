<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<style>
:root {
  --clr-excellent: #059669; --clr-bon: #2563eb; --clr-moyen: #d97706; --clr-faible: #dc2626;
  --clr-absent: #6b7280;
  --bg-excellent: #ecfdf5; --bg-bon: #eff6ff; --bg-moyen: #fffbeb; --bg-faible: #fef2f2;
  --bg-absent: #f9fafb;
  --cell-w: 82px; --cell-h: 34px;
  --excel-border: #d4d4d4; --excel-header: #e8e8e8; --excel-sticky: #dcdcdc;
  --excel-grid: #e0e0e0; --excel-focus: #217346;
  --shadow-sm: 0 1px 3px rgba(0,0,0,.08);
}
*{box-sizing:border-box}
.notes-app{font-family:'Segoe UI','Calibri','Inter',sans-serif;font-size:13px}
.notes-toolbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;margin-bottom:10px;padding:8px 12px;background:#f8f9fa;border:1px solid #ddd;border-radius:4px}
.notes-toolbar .btn-group{display:flex;gap:4px}
.notes-toolbar button{height:30px;padding:0 10px;font-size:12px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:4px;border:1px solid #c0c0c0;background:linear-gradient(to bottom,#fff,#f0f0f0);color:#333;border-radius:3px}
.notes-toolbar button:hover{background:linear-gradient(to bottom,#f0f0f0,#e0e0e0)}
.notes-toolbar button.primary{background:#217346;color:#fff;border-color:#1a5c38}
.notes-toolbar button.primary:hover{background:#1a5c38}
.notes-grid-container{overflow-x:auto;max-height:65vh;border:1px solid #c0c0c0;background:#fff}
.notes-table{border-collapse:collapse;width:100%;min-width:900px;table-layout:auto;font-size:12px}
.notes-table thead{position:sticky;top:0;z-index:5}
.notes-table thead th{background:var(--excel-header);color:#333;font-size:11px;font-weight:600;padding:6px 4px;border:1px solid var(--excel-border);white-space:nowrap;text-align:center}
.notes-table thead th.sticky-left{position:sticky;z-index:6;background:var(--excel-sticky)}
.notes-table thead th.col-no{left:0;width:32px;min-width:32px}
.notes-table thead th.col-matricule{left:32px;width:70px;min-width:70px}
.notes-table thead th.col-nom{left:102px;min-width:160px;text-align:left;padding-left:8px}
.notes-table thead th .eval-sur{display:block;font-size:10px;font-weight:400;color:#666;margin-top:1px}
.notes-table tbody td{padding:2px;border:1px solid var(--excel-grid);vertical-align:middle;height:34px}
.notes-table tbody td.sticky-left{position:sticky;z-index:3;background:#f5f5f5}
.notes-table tbody td.col-no{left:0;text-align:center;font-size:11px;color:#999;font-weight:400}
.notes-table tbody td.col-matricule{left:32px;font-size:11px;color:#555;text-align:center}
.notes-table tbody td.col-nom{left:102px;font-size:12px;font-weight:500;color:#222;padding-left:8px;text-align:left}
.notes-table tbody tr:nth-child(even) td{background:#fafafa}
.notes-table tbody tr:nth-child(even) td.sticky-left{background:#f0f0f0}
.notes-table tbody tr:hover td{background:#e8f0fe}
.notes-table tbody tr:hover td.sticky-left{background:#dce6f5}
.notes-table tbody tr.selected td{background:#d4e3fd}
.note-cell{position:relative;display:flex;align-items:center;justify-content:center}
.note-input{width:var(--cell-w);height:var(--cell-h);text-align:center;border:1px solid #d0d0d0;border-radius:0;font-size:12px;font-weight:500;padding:1px 2px;background:#fff;outline:none;font-family:inherit;-moz-appearance:textfield}
.note-input::-webkit-inner-spin-button,.note-input::-webkit-outer-spin-button{-webkit-appearance:none;margin:0}
.note-input:hover{border-color:#999}
.note-input:focus{border:2px solid var(--excel-focus);background:#fff;box-shadow:none}
.note-input.error{border-color:#ef4444!important;background:#fef2f2!important}
.note-input.error+.err-msg{display:block!important}
.err-msg{display:none;position:absolute;top:100%;left:50%;transform:translateX(-50%);background:#ef4444;color:#fff;font-size:9px;padding:1px 6px;white-space:nowrap;z-index:10;font-weight:400}
.note-input.excellent{color:var(--clr-excellent);background:var(--bg-excellent);border-color:#a7f3d0}
.note-input.bon{color:var(--clr-bon);background:var(--bg-bon);border-color:#bfdbfe}
.note-input.moyen{color:var(--clr-moyen);background:var(--bg-moyen);border-color:#fde68a}
.note-input.faible{color:var(--clr-faible);background:var(--bg-faible);border-color:#fecaca}
.note-input.absent{color:var(--clr-absent);background:var(--bg-absent);border-color:#e5e7eb}
.cell-total,.cell-moy{font-size:12px;text-align:center;font-weight:600}
.cell-total{color:#217346}.cell-moy{color:#2563eb}
.notes-table tfoot td{padding:4px 6px;background:#f0f0f0;font-weight:600;font-size:11px;text-align:center;color:#444;border:1px solid var(--excel-border)}
.notes-table tfoot td.sticky-left{position:sticky;z-index:3;background:#e0e0e0}
.notes-table tfoot td.col-no{left:0}.notes-table tfoot td.col-matricule{left:32px}.notes-table tfoot td.col-nom{left:102px}
.add-eval-bar{display:flex;align-items:center;gap:6px;padding:6px 12px;background:#f8f9fa;margin-bottom:8px;border:1px solid #ddd}
.add-eval-bar input,.add-eval-bar select{height:28px;border:1px solid #d0d0d0;border-radius:2px;padding:0 8px;font-size:12px;outline:none}
.add-eval-bar input:focus,.add-eval-bar select:focus{border-color:#217346}
.notes-stats{display:flex;gap:12px;flex-wrap:wrap;margin-top:10px}
.stat-card{background:#f8f9fa;border:1px solid #ddd;border-radius:3px;padding:6px 12px;display:flex;align-items:center;gap:8px}
.stat-card .stat-icon{width:28px;height:28px;border-radius:3px;display:flex;align-items:center;justify-content:center;font-size:14px}
.stat-card .stat-val{font-size:16px;font-weight:700}.stat-card .stat-lbl{font-size:10px;color:#666}
@media(max-width:768px){.notes-toolbar{flex-direction:column;align-items:stretch}}
</style>
<div class="dashboard-main-body notes-app">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Notes & Points</h1>
      <div><a href="<?=base_url('Dashboard')?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a><span class="text-secondary-light"> / Notes</span></div>
    </div>
    <a href="<?=base_url('Notes/Bulletins')?>" class="btn btn-outline-primary-600 d-flex align-items-center gap-6"><i class="ri-file-list-3-line"></i>Bulletins</a>
  </div>

  <div class="row gy-4" id="classesGrid"><div class="col-12 text-center py-32"><div class="spinner-border text-primary-600"></div><p class="mt-8 text-secondary-light">Chargement...</p></div></div>

  <div id="grilleCard" style="display:none;">
    <div class="notes-toolbar">
      <div class="btn-group">
        <button onclick="backToClasses()"><i class="ri-arrow-left-line"></i> Classes</button>
        <div style="display:flex;flex-direction:column;padding:0 12px">
          <span style="font-weight:600;color:#1e293b;font-size:14px" id="grilleTitle">—</span>
          <span style="font-size:11px;color:#6b7280" id="grilleSubtitle">—</span>
        </div>
      </div>
      <div class="btn-group">
        <button onclick="showAddEval()"><i class="ri-add-line"></i> Ajouter une évaluation</button>
        <button class="primary" onclick="saveGrilleNotes()"><i class="ri-save-line"></i> Enregistrer tout</button>
      </div>
    </div>
    <div class="add-eval-bar" id="addEvalRow" style="display:none">
      <span class="badge bg-primary-100 text-primary-600 px-12 py-6 radius-4 fw-semibold text-sm" id="evalCoursBadge">—</span>
      <input type="text" id="addEvalLibelle" placeholder="Libellé (ex: Interro 2)" style="width:180px">
      <select id="addEvalType"><option value="devoir">Devoir</option><option value="interrogation">Interro</option><option value="controle">Contrôle</option><option value="composition">Compo</option><option value="examen">Examen</option></select>
      <input type="number" id="addEvalSur" value="20" min="1" max="999" style="width:65px" title="Note maximale">
      <input type="date" id="addEvalDate" value="<?=date('Y-m-d')?>" style="width:135px">
      <button class="primary" onclick="addEvaluation()" style="height:34px"><i class="ri-check-line"></i> Ajouter</button>
      <button onclick="document.getElementById('addEvalRow').style.display='none'" style="height:34px"><i class="ri-close-line"></i></button>
    </div>
    <div class="notes-grid-container">
      <table class="notes-table">
        <thead id="grilleHead"></thead>
        <tbody id="grilleBody"><tr><td colspan="10" class="text-center py-32" style="color:#9ca3af">Chargement...</td></tr></tbody>
        <tfoot id="grilleFoot"></tfoot>
      </table>
    </div>
    <div class="notes-stats" id="grilleStats"></div>
  </div>
</div>

<div class="modal fade" id="coursModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content radius-16 bg-base"><div class="modal-header border-bottom px-24 py-16"><h6 class="text-lg fw-semibold mb-0" id="coursModalTitle">Choisir un cours</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body px-24 py-16"><p class="text-sm text-secondary-light mb-16">Classe : <strong id="coursModalClasse"></strong></p><div class="d-flex flex-column gap-8" id="coursList"></div></div></div></div></div>

<div class="modal fade" id="addEvalModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content radius-16 bg-base"><div class="modal-header border-bottom px-24 py-16"><h6 class="text-lg fw-semibold mb-0">Nouvelle évaluation</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body px-24 py-16">
<form id="evalForm">
  <div class="row g-3">
    <div class="col-md-6"><label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe</label><input type="text" class="form-control" id="evalClasse" readonly></div>
    <div class="col-md-6"><label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Matière</label><input type="text" class="form-control" id="evalMatiere" readonly></div>
    <div class="col-md-8"><label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Libellé *</label><input type="text" class="form-control" id="evalLibelle" placeholder="Ex: Interrogation 1"></div>
    <div class="col-md-4"><label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type</label>
      <select class="form-control form-select" id="evalType"><option value="devoir">Devoir</option><option value="interrogation">Interrogation</option><option value="controle">Contrôle</option><option value="composition">Composition</option><option value="examen">Examen</option></select>
    </div>
    <div class="col-md-4"><label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Note max.</label><input type="number" class="form-control" id="evalSur" step="0.1" value="20"></div>
    <div class="col-md-4"><label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Période</label>
      <select class="form-control form-select" id="evalPeriode"><?php foreach($periodes as $p): ?><option value="<?=$p['id_periode']?>" <?=$p['est_en_cours']?'selected':''?>><?=$p['libelle']?></option><?php endforeach; ?></select>
    </div>
    <div class="col-md-4"><label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date</label><input type="date" class="form-control" id="evalDate" value="<?=date('Y-m-d')?>"></div>
  </div>
</form>
</div><div class="modal-footer border-top px-24 py-16"><button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-40 py-11 radius-8" data-bs-dismiss="modal">Annuler</button><button type="button" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8" onclick="saveNewEval()">Enregistrer</button></div></div></div></div>

<script id="classes_data" type="application/json"><?=json_encode($classes)?></script>
<script id="matieres_data" type="application/json"><?=json_encode($matieres)?></script>
<script id="periodes_data" type="application/json"><?=json_encode($periodes)?></script>
<script id="annees_data" type="application/json"><?=json_encode($annees)?></script>
<script>var ACTIVE_PERIODE_ID='<?=$id_periode_active?>',ACTIVE_ANNEE_ID='<?=$id_annee_active?>';</script>
<script src="<?=base_url()?>assets/js/api.js"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
const Toast=Swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:2500,timerProgressBar:true});
let gMid=null,gMName='',gCId=null,gData=null;
try{var cList=JSON.parse(document.getElementById('classes_data').textContent)}catch(e){}
try{var mL=JSON.parse(document.getElementById('matieres_data').textContent)}catch(e){}
try{var pL=JSON.parse(document.getElementById('periodes_data').textContent)}catch(e){}
try{var aL=JSON.parse(document.getElementById('annees_data').textContent)}catch(e){}

/* ==== CLASS CARDS ==== */
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
            <button class="btn btn-outline-${cl}-600 flex-grow-1 d-flex align-items-center justify-content-center gap-6 py-8" onclick="openEvalForClass('${c.id_classe}','${c.classe.replace(/'/g,"\\'")}')"><i class="ri-file-edit-line"></i>Évaluation</button>
            <button class="btn btn-${cl}-600 flex-grow-1 d-flex align-items-center justify-content-center gap-6 py-8 text-white" onclick="openNotesForClass('${c.id_classe}','${c.classe.replace(/'/g,"\\'")}')"><i class="ri-pencil-line"></i>Notes</button>
          </div>
        </div>
      </div>
    </div>`;
  });
  document.getElementById('classesGrid').innerHTML=h||'<div class="col-12 text-center py-32 text-secondary-light">Aucune classe</div>';
}

/* ==== OPEN NOTES / EVAL ==== */
function openNotesForClass(id,cn){gCId=id;pickCours(id,cn,false)}
function openEvalForClass(id,cn){gCId=id;pickCours(id,cn,true)}
function pickCours(id,cn,isEval){
  fetch(API.base_url+'api/notes/matieres_by_classe/'+id).then(r=>r.json()).then(r=>{
    if(!r.success||!r.data.length){Swal.fire({icon:'warning',title:'Aucun cours',text:'Aucun cours lié à cette classe.'});return}
    if(r.data.length===1){gMid=r.data[0].id_matiere;gMName=r.data[0].libelle;if(isEval){showEvalModal(id,gMName);return}loadGrilleNotes();return}
    document.getElementById('coursModalTitle').textContent='Choisir un cours — '+(isEval?'Évaluation':'Notes');
    document.getElementById('coursModalClasse').textContent=cn;
    document.getElementById('coursList').innerHTML=r.data.map(m=>`<button class="btn border border-neutral-300 bg-hover-neutral-100 text-start px-16 py-12 radius-8 d-flex align-items-center gap-12 w-100" onclick="${isEval?'selectEval':'selectNotes'}('${id}','${m.id_matiere}','${m.libelle.replace(/'/g,"\\'")}',${isEval})"><span class="d-flex align-items-center justify-content-center bg-primary-100 text-primary-600 radius-4" style="width:40px;height:40px"><i class="ri-book-open-line"></i></span><div><span class="fw-semibold text-sm">${m.libelle}</span><small class="d-block text-secondary-light text-xs">${m.code||''}</small></div><i class="ri-arrow-right-s-line ms-auto text-secondary-light"></i></button>`).join('');
    new bootstrap.Modal(document.getElementById('coursModal')).show();
  }).catch(()=>Swal.fire({icon:'error',title:'Erreur',text:'Chargement impossible'}));
}
function selectNotes(id,mid,mn,isEval){bootstrap.Modal.getInstance(document.getElementById('coursModal')).hide();gCId=id;gMid=mid;gMName=mn;loadGrilleNotes()}
function selectEval(id,mid,mn,isEval){bootstrap.Modal.getInstance(document.getElementById('coursModal')).hide();gMid=mid;showEvalModal(id,mn)}

/* ==== GRILLE EXCEL ==== */
async function loadGrilleNotes(){
  document.getElementById('classesGrid').style.display='none';document.getElementById('grilleCard').style.display='';
  document.getElementById('grilleTitle').textContent=gMName;
  var anneeLabel='',periodeLabel='';
  if(typeof aL!=='undefined'&&aL.length){var ay=aL.find(function(x){return x.id_annee==ACTIVE_ANNEE_ID});if(ay)anneeLabel=ay.libelle}
  if(typeof pL!=='undefined'&&pL.length){var pp=pL.find(function(x){return x.id_periode==ACTIVE_PERIODE_ID});if(pp)periodeLabel=pp.libelle}
  document.getElementById('grilleSubtitle').textContent='Chargement... '+(anneeLabel?'('+anneeLabel+(periodeLabel?' | '+periodeLabel:'')+')':'');
  const r=await fetch(API.base_url+'api/notes/grille/'+gCId+'/'+gMid).then(r=>r.json());
  if(!r.success){Swal.fire({icon:'error',text:r.message});return}
  gData=r.data;
  let head='<tr><th class="sticky-left col-no">N°</th><th class="sticky-left col-matricule">Matricule</th><th class="sticky-left col-nom">Nom & Prénom</th>';
  r.data.evaluations.forEach(ev=>head+=`<th class="col-eval">${ev.libelle}<span class="eval-sur">/${ev.sur} ×${ev.coefficient}</span></th>`);
  head+='<th>Total</th><th>%</th><th>Moyenne</th></tr>';
  document.getElementById('grilleHead').innerHTML=head;

  let body='';
  let scores=[];
  r.data.eleves.forEach((el,i)=>{
    body+=`<tr><td class="sticky-left col-no">${i+1}</td><td class="sticky-left col-matricule">${el.matricule||'-'}</td><td class="sticky-left col-nom">${el.nom} ${el.prenom}</td>`;
    let total=0,totalSur=0,nb=0;
    r.data.evaluations.forEach(ev=>{
      let n=(r.data.notes[el.id_etudiant]||{})[ev.id_evaluation];
      let val=n?n.note:'';
      body+=`<td><div class="note-cell"><input class="note-input" type="number" step="any" data-e="${el.id_etudiant}" data-v="${ev.id_evaluation}" data-max="${ev.sur}" value="${val}" onchange="onNoteChange(this)" onkeydown="onNoteKey(event,this)" onfocus="onNoteFocus(this)" onblur="onNoteBlur(this)" placeholder="—"><span class="err-msg"></span></div></td>`;
      if(val!==''){total+=parseFloat(val);totalSur+=parseFloat(ev.sur);nb++}
    });
    let moy=nb>0?(total/totalSur)*20:0;
    scores.push({id:el.id_etudiant,moy:nb>0?moy:-1});
    body+=`<td class="cell-total" data-rt="${el.id_etudiant}">${nb>0?total.toFixed(2):'—'}</td>`;
    body+=`<td class="cell-moy" data-rm="${el.id_etudiant}">${nb>0?(total/totalSur*100).toFixed(1)+'%':'—'}</td>`;
    body+=`<td class="cell-moy" data-moy="${el.id_etudiant}">${nb>0?moy.toFixed(2):'—'}</td>`;
    body+=`</tr>`;
  });

  let rankMap={};
  scores.sort((a,b)=>b.moy-a.moy);
  let rk=1,pv=-1;
  scores.forEach((s,idx)=>{
    if(s.moy<0){rankMap[s.id]='—';return}
    if(pv>=0&&s.moy<pv)rk=idx+1;
    rankMap[s.id]=rk;pv=s.moy;
  });

  document.getElementById('grilleBody').innerHTML=body;
  for(let id in rankMap){let c=document.querySelector(`[data-rr="${id}"]`);if(c)c.textContent=rankMap[id]}
  document.getElementById('grilleFoot').innerHTML='<tr><td class="sticky-left col-no" colspan="3">Moyennes</td>'+r.data.evaluations.map(ev=>`<td data-cm="${ev.id_evaluation}">—</td>`).join('')+'<td id="ft">—</td><td id="fm">—</td><td id="fmoy">—</td></tr>';
  computeStats();
  colorCells();
  document.getElementById('grilleTitle').textContent=gData.matiere;
  var anneeLabel='',periodeLabel='';
  if(typeof aL!=='undefined'&&aL.length){var ay=aL.find(function(x){return x.id_annee==ACTIVE_ANNEE_ID});if(ay)anneeLabel=ay.libelle}
  if(typeof pL!=='undefined'&&pL.length){var pp=pL.find(function(x){return x.id_periode==ACTIVE_PERIODE_ID});if(pp)periodeLabel=pp.libelle}
  document.getElementById('grilleSubtitle').textContent=gData.classe+' | '+anneeLabel+(periodeLabel?' | '+periodeLabel:'');

  /* Stats */
  let ne=r.data.eleves.length,nev=r.data.evaluations.length;
  document.getElementById('grilleStats').innerHTML=`
    <div class="stat-card"><div class="stat-icon" style="background:#eef2ff;color:#6366f1"><i class="ri-user-3-line"></i></div><div><div class="stat-val">${ne}</div><div class="stat-lbl">Élèves</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:#ecfdf5;color:#059669"><i class="ri-file-list-3-line"></i></div><div><div class="stat-val">${nev}</div><div class="stat-lbl">Évaluations</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:#fffbeb;color:#d97706"><i class="ri-bar-chart-line"></i></div><div><div class="stat-val" id="statAvg">—</div><div class="stat-lbl">Moy. classe</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:#fef2f2;color:#dc2626"><i class="ri-arrow-down-line"></i></div><div><div class="stat-val" id="statMin">—</div><div class="stat-lbl">Mini</div></div></div>
    <div class="stat-card"><div class="stat-icon" style="background:#eff6ff;color:#2563eb"><i class="ri-arrow-up-line"></i></div><div><div class="stat-val" id="statMax">—</div><div class="stat-lbl">Maxi</div></div></div>
  `;
}

/* ==== CELL COLORS ==== */
function getColorClass(note,max){
  if(note===''||isNaN(note))return'';
  let pct=(note/max)*100;
  if(pct>=80)return'excellent';
  if(pct>=60)return'bon';
  if(pct>=40)return'moyen';
  if(pct>0)return'faible';
  if(note==0)return'absent';
  return'';
}
function colorCells(){
  document.querySelectorAll('.note-input').forEach(inp=>{
    let v=parseFloat(inp.value),max=parseFloat(inp.dataset.max)||20;
    inp.className='note-input';
    if(inp.value!==''&&!isNaN(v)){inp.classList.add(getColorClass(v,max))}
  });
}

/* ==== VALIDATION ==== */
function validateNote(inp){
  let val=inp.value.trim(),max=parseFloat(inp.dataset.max)||20,err=inp.nextElementSibling;
  if(val===''){inp.classList.remove('error');if(err)err.textContent='';return true}
  let n=parseFloat(val);
  if(isNaN(n)||!/^-?\d+([.,]\d+)?$/.test(val)){inp.classList.add('error');if(err)err.textContent='Nombre invalide';return false}
  if(n<0){inp.classList.add('error');if(err)err.textContent='Valeur négative';return false}
  if(n>max){inp.classList.add('error');if(err)err.textContent='Max '+max;return false}
  inp.classList.remove('error');if(err)err.textContent='';return true;
}

/* ==== EVENTS ==== */
function onNoteFocus(inp){inp.select()}
function onNoteBlur(inp){if(!validateNote(inp))return;colorOne(inp);recalcRow(inp);computeStats()}
function onNoteChange(inp){onNoteBlur(inp)}
function colorOne(inp){if(inp.value!==''&&!isNaN(parseFloat(inp.value))){let v=parseFloat(inp.value),max=parseFloat(inp.dataset.max)||20;inp.className='note-input '+getColorClass(v,max)}else{inp.className='note-input'}}
function onNoteKey(e,inp){
  let cells=Array.from(document.querySelectorAll('#grilleBody .note-input')),idx=cells.indexOf(inp),rowLen=gData.evaluations.length;
  if(e.key==='Enter'){e.preventDefault();if(!validateNote(inp))return;colorOne(inp);recalcRow(inp);computeStats();if(idx>=0&&idx<cells.length-1){let nxt=cells[idx+1];setTimeout(()=>{nxt.focus();nxt.select()},20)}}
  else if(e.key==='ArrowRight'&&idx>=0&&idx<cells.length-1&&(idx+1)%rowLen!==0){e.preventDefault();cells[idx+1].focus();cells[idx+1].select()}
  else if(e.key==='ArrowLeft'&&idx>0&&idx%rowLen!==0){e.preventDefault();cells[idx-1].focus();cells[idx-1].select()}
  else if(e.key==='ArrowDown'&&idx>=0&&idx+rowLen<cells.length){e.preventDefault();cells[idx+rowLen].focus();cells[idx+rowLen].select()}
  else if(e.key==='ArrowUp'&&idx>=rowLen){e.preventDefault();cells[idx-rowLen].focus();cells[idx-rowLen].select()}
}

/* ==== RECALC ==== */
function recalcRow(inp){
  let tr=inp.closest('tr'),total=0,ts=0,nb=0,id=inp.dataset.e;
  tr.querySelectorAll('.note-input').forEach(x=>{let v=parseFloat(x.value);if(!isNaN(v)&&x.value!==''){total+=v;ts+=parseFloat(x.dataset.max)||20;nb++}});
  let moy=nb>0?(total/ts)*20:0;
  let ct=tr.querySelector(`[data-rt="${id}"]`),cm=tr.querySelector(`[data-rm="${id}"]`),cmo=tr.querySelector(`[data-moy="${id}"]`);
  if(ct)ct.textContent=nb>0?total.toFixed(2):'—';
  if(cm)cm.textContent=nb>0?(total/ts*100).toFixed(1)+'%':'—';
  if(cmo)cmo.textContent=nb>0?moy.toFixed(2):'—';
  /* rerank */
  let scores=[];
  document.querySelectorAll('#grilleBody tr').forEach(r=>{
    let mcell=r.querySelector('[data-rm]'),eid=mcell?mcell.dataset.rm:'',mv=mcell?parseFloat(mcell.textContent):-1;
    scores.push({id:eid,moy:isNaN(mv)?-1:mv});
  });
  scores.sort((a,b)=>b.moy-a.moy);
  let rk=1,prev=-1;scores.forEach((s,idx)=>{if(s.moy<0){let c=document.querySelector(`[data-rr="${s.id}"]`);if(c)c.textContent='—';return}if(prev>=0&&s.moy<prev)rk=idx+1;let cell=document.querySelector(`[data-rr="${s.id}"]`);if(cell)cell.textContent=rk;prev=s.moy});
}

function computeStats(){
  let allMoy=[],footRows=document.querySelectorAll('#grilleBody tr');
  gData.evaluations.forEach(ev=>{let s=0,c=0;footRows.forEach(r=>{let x=r.querySelector(`.note-input[data-v="${ev.id_evaluation}"]`);if(x&&x.value!==''){s+=parseFloat(x.value);c++}});let cell=document.querySelector(`[data-cm="${ev.id_evaluation}"]`);if(cell)cell.textContent=c>0?(s/c).toFixed(2):'—'});
  let tc=0,nc=0,tm=0,tmoy=0,ne=0;footRows.forEach(r=>{let ct=r.querySelector('[data-rt]'),cm=r.querySelector('[data-rm]'),cmo=r.querySelector('[data-moy]');if(ct&&ct.textContent!=='—'){tc+=parseFloat(ct.textContent);nc++}if(cm&&cm.textContent!=='—'){let pct=parseFloat(cm.textContent);tm+=pct;allMoy.push(pct);ne++}if(cmo&&cmo.textContent!=='—'){tmoy+=parseFloat(cmo.textContent)}});
  document.getElementById('ft').textContent=nc>0?(tc/(nc/Math.max(gData.evaluations.length,1))).toFixed(2):'—';
  document.getElementById('fm').textContent=ne>0?(tm/ne).toFixed(1)+'%':'—';
  document.getElementById('fmoy').textContent=ne>0?(tmoy/ne).toFixed(2):'—';
  let stAvg=document.getElementById('statAvg'),stMin=document.getElementById('statMin'),stMax=document.getElementById('statMax');
  if(stAvg)stAvg.textContent=ne>0?(tm/ne).toFixed(1)+'%':'—';
  if(stMin)stMin.textContent=allMoy.length?Math.min(...allMoy).toFixed(1)+'%':'—';
  if(stMax)stMax.textContent=allMoy.length?Math.max(...allMoy).toFixed(1)+'%':'—';
}

/* ==== SAVE ==== */
async function saveGrilleNotes(){
  let notes=[];document.querySelectorAll('#grilleBody .note-input').forEach(x=>{let v=parseFloat(x.value);if(isNaN(v)||x.value==='')return;notes.push({id_etudiant:x.dataset.e,id_evaluation:x.dataset.v,note:v})});
  if(!notes.length){Swal.fire({icon:'warning',text:'Aucune note'});return}
  if(!(await Swal.fire({title:'Enregistrer?',text:notes.length+' notes',icon:'question',showCancelButton:true,confirmButtonText:'Oui'})).isConfirmed)return;
  Swal.fire({title:'Enregistrement...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
  let r=await fetch(API.base_url+'api/notes/batch',{method:'POST',headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},body:JSON.stringify({notes:notes})}).then(r=>r.json());
  Swal.close();r.success?Toast.fire({icon:'success',title:notes.length+' notes enregistrées'}):Swal.fire({icon:'error',text:r.message});
}
async function saveObs(inp){if(!inp.dataset.obs||!inp.value.trim())return;await fetch(API.base_url+'api/notes/create',{method:'POST',headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},body:JSON.stringify({id_etudiant:inp.dataset.obs,note:0,appreciation:inp.value.trim()})}).then(r=>r.json())}

/* ==== ADD EVAL ==== */
function showAddEval(){document.getElementById('addEvalRow').style.display='flex';document.getElementById('evalCoursBadge').textContent=gMName;document.getElementById('addEvalLibelle').focus()}
async function addEvaluation(){
  let lib=document.getElementById('addEvalLibelle').value.trim();
  if(!lib){Swal.fire({icon:'warning',text:'Libellé obligatoire'});return}
  let d={libelle:lib,id_classe:gCId,id_matiere:gMid,id_periode:ACTIVE_PERIODE_ID,id_annee:ACTIVE_ANNEE_ID,date_eval:document.getElementById('addEvalDate').value,type:document.getElementById('addEvalType').value,coefficient:1,sur:document.getElementById('addEvalSur').value||20};
  let r=await API.evaluations.create(d);
  if(r.success){document.getElementById('addEvalLibelle').value='';document.getElementById('addEvalRow').style.display='none';Toast.fire({icon:'success',title:'Évaluation ajoutée'});loadGrilleNotes()}else Swal.fire({icon:'error',text:r.message});
}

function backToClasses(){gCId=null;gMid=null;document.getElementById('grilleCard').style.display='none';document.getElementById('classesGrid').style.display=''}

function showEvalModal(classeId,matiereNom){
  var c=cList.find(function(x){return x.id_classe==classeId});
  document.getElementById('evalClasse').value=c?c.libelle:'';
  document.getElementById('evalMatiere').value=matiereNom;
  document.getElementById('evalClasse').dataset.id=classeId;
  document.getElementById('evalMatiere').dataset.id=gMid;
  new bootstrap.Modal(document.getElementById('addEvalModal')).show();
}
async function saveNewEval(){
  var lib=document.getElementById('evalLibelle').value.trim();
  var cid=document.getElementById('evalClasse').dataset.id,mid=document.getElementById('evalMatiere').dataset.id;
  if(!lib){Swal.fire({icon:'warning',text:'Libellé obligatoire'});return}
  if(!cid||!mid||cid==='null'||mid==='null'){Swal.fire({icon:'error',text:'Classe ou matière invalide'});return}
  var d={libelle:lib,id_classe:cid,id_matiere:mid,id_periode:document.getElementById('evalPeriode').value,id_annee:ACTIVE_ANNEE_ID,date_eval:document.getElementById('evalDate').value,type:document.getElementById('evalType').value,sur:document.getElementById('evalSur').value||20};
  var r=await API.evaluations.create(d);
  if(r.success){bootstrap.Modal.getInstance(document.getElementById('addEvalModal')).hide();document.getElementById('evalLibelle').value='';Toast.fire({icon:'success',title:'Évaluation créée'})}else Swal.fire({icon:'error',text:r.message});
}

(function(){var w=setInterval(function(){if(typeof jQuery!=='undefined'&&$.fn&&$.fn.DataTable&&typeof API!=='undefined'){clearInterval(w);loadClassesGrid()}},50)})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>