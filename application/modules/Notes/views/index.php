<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<style>
:root {
  --clr-excellent: #059669; --clr-bon: #2563eb; --clr-moyen: #d97706; --clr-faible: #dc2626;
  --clr-absent: #6b7280; --clr-empty: #9ca3af;
  --bg-excellent: #ecfdf5; --bg-bon: #eff6ff; --bg-moyen: #fffbeb; --bg-faible: #fef2f2;
  --bg-absent: #f9fafb;
  --cell-w: 82px; --cell-h: 38px; --radius: 8px;
  --shadow-sm: 0 1px 2px rgba(0,0,0,.04); --shadow-md: 0 4px 12px rgba(0,0,0,.06);
  --shadow-focus: 0 0 0 3px rgba(99,102,241,.15);
}
*{box-sizing:border-box}
.notes-app{font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif}
.notes-toolbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:16px;padding:12px 16px;background:#fff;border-radius:var(--radius);box-shadow:var(--shadow-sm)}
.notes-toolbar .btn-group{display:flex;gap:8px}
.notes-toolbar button{height:36px;padding:0 14px;font-size:13px;font-weight:500;border-radius:var(--radius);cursor:pointer;display:flex;align-items:center;gap:6px;transition:all .15s;border:1px solid #e5e7eb;background:#fff;color:#374151}
.notes-toolbar button:hover{background:#f9fafb;border-color:#d1d5db}
.notes-toolbar button.primary{background:#6366f1;color:#fff;border-color:#6366f1}
.notes-toolbar button.primary:hover{background:#4f46e5}
.notes-toolbar button.success{background:#059669;color:#fff;border-color:#059669}
.notes-toolbar button.success:hover{background:#047857}
.notes-grid-container{overflow-x:auto;max-height:62vh;border:1px solid #e5e7eb;border-radius:var(--radius);background:#fff;box-shadow:var(--shadow-md)}
.notes-table{border-collapse:collapse;width:100%;min-width:900px;table-layout:auto}
.notes-table thead{position:sticky;top:0;z-index:5}
.notes-table thead th{background:#217346;color:#fff;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;padding:10px 6px;border-right:1px solid #1a5c38;white-space:nowrap;text-align:center}
.notes-table thead th:last-child{border-right:none}
.notes-table thead th.sticky-left{position:sticky;z-index:6;background:#217346}
.notes-table thead th.col-no{left:0;width:36px;min-width:36px}
.notes-table thead th.col-matricule{left:36px;width:80px;min-width:80px}
.notes-table thead th.col-nom{left:116px;min-width:150px}
.notes-table thead th.col-eval{min-width:var(--cell-w)}
.notes-table thead th .eval-sur{display:block;font-size:10px;font-weight:400;color:#b7dfc5;margin-top:2px}
.notes-table tbody td{padding:4px 4px;border:1px solid #f1f5f9;vertical-align:middle;height:42px}
.notes-table tbody td.sticky-left{position:sticky;z-index:3}
.notes-table tbody td.col-no{left:0;background:#f8fafc;text-align:center;font-size:12px;color:#94a3b8;font-weight:500}
.notes-table tbody td.col-matricule{left:36px;background:#f8fafc;font-size:12px;color:#6366f1;font-weight:500;text-align:center}
.notes-table tbody td.col-nom{left:116px;background:#f8fafc;font-size:13px;font-weight:600;color:#1e293b}
.notes-table tbody tr:nth-child(even) td{background:#fff}
.notes-table tbody tr:nth-child(even) td.sticky-left{background:#f1f5f9}
.notes-table tbody tr:hover td{background:#f8fafc}
.notes-table tbody tr:hover td.sticky-left{background:#e2e8f0}
.note-cell{position:relative;display:flex;align-items:center;justify-content:center}
.note-input{width:var(--cell-w);height:var(--cell-h);text-align:center;border:1.5px solid #e5e7eb;border-radius:6px;font-size:13px;font-weight:600;padding:2px 4px;background:#fff;outline:none;transition:all .12s;color:#374151;font-family:inherit;-moz-appearance:textfield}
.note-input::-webkit-inner-spin-button,.note-input::-webkit-outer-spin-button{-webkit-appearance:none;margin:0}
.note-input:hover{border-color:#cbd5e1}
.note-input:focus{border-color:#6366f1;background:#eef2ff;box-shadow:var(--shadow-focus)}
.note-input.error{border-color:#ef4444!important;background:#fef2f2!important;box-shadow:0 0 0 3px rgba(239,68,68,.12)!important}
.note-input.error+.err-msg{display:block!important}
.err-msg{display:none;position:absolute;top:100%;left:50%;transform:translateX(-50%);background:#ef4444;color:#fff;font-size:10px;padding:2px 8px;border-radius:4px;white-space:nowrap;z-index:10;margin-top:2px;font-weight:400}
.note-input.excellent{color:var(--clr-excellent);background:var(--bg-excellent);border-color:#a7f3d0}
.note-input.bon{color:var(--clr-bon);background:var(--bg-bon);border-color:#bfdbfe}
.note-input.moyen{color:var(--clr-moyen);background:var(--bg-moyen);border-color:#fde68a}
.note-input.faible{color:var(--clr-faible);background:var(--bg-faible);border-color:#fecaca}
.note-input.absent{color:var(--clr-absent);background:var(--bg-absent);border-color:#e5e7eb}
.cell-total,.cell-moy,.cell-rang,.cell-obs{font-size:13px;text-align:center;font-weight:600}
.cell-total{color:#6366f1}.cell-moy{color:#059669}.cell-rang{color:#1e293b}
.cell-obs{min-width:120px;padding:2px 4px}
.cell-obs input{border:1px solid transparent;background:transparent;font-size:12px;padding:4px 6px;border-radius:6px;width:100%;outline:none;transition:all .15s}
.cell-obs input:hover{border-color:#e5e7eb}.cell-obs input:focus{border-color:#6366f1;background:#eef2ff}
.notes-table tfoot td{padding:8px 6px;background:#f1f5f9;font-weight:600;font-size:12px;text-align:center;color:#475569;border:1px solid #e5e7eb}
.notes-table tfoot td.sticky-left{position:sticky;z-index:3;background:#e2e8f0}
.notes-table tfoot td.col-no{left:0}.notes-table tfoot td.col-matricule{left:36px}.notes-table tfoot td.col-nom{left:116px}
.add-eval-bar{display:flex;align-items:center;gap:8px;padding:10px 16px;background:#f8fafc;border-radius:var(--radius);margin-bottom:12px;border:1px dashed #d1d5db}
.add-eval-bar input,.add-eval-bar select{height:34px;border:1px solid #e5e7eb;border-radius:6px;padding:0 10px;font-size:13px;outline:none;transition:all .15s}
.add-eval-bar input:focus,.add-eval-bar select:focus{border-color:#6366f1;box-shadow:0 0 0 2px rgba(99,102,241,.1)}
.notes-stats{display:flex;gap:16px;flex-wrap:wrap;margin-top:12px}
.stat-card{background:#fff;border-radius:var(--radius);padding:10px 14px;box-shadow:var(--shadow-sm);display:flex;align-items:center;gap:10px;min-width:120px}
.stat-card .stat-icon{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px}
.stat-card .stat-val{font-size:18px;font-weight:700}.stat-card .stat-lbl{font-size:11px;color:#6b7280}
@media(max-width:768px){.notes-toolbar{flex-direction:column;align-items:stretch}.notes-toolbar .btn-group{flex-wrap:wrap}}
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
        <span style="font-weight:600;color:#1e293b;font-size:14px;padding:0 8px" id="grilleTitle">—</span>
      </div>
      <div class="btn-group">
        <button onclick="showAddEval()"><i class="ri-add-line"></i> Ajouter une évaluation</button>
        <button class="primary" onclick="saveGrilleNotes()"><i class="ri-save-line"></i> Enregistrer tout</button>
      </div>
    </div>
    <div class="add-eval-bar" id="addEvalRow" style="display:none">
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

<script id="classes_data" type="application/json"><?=json_encode($classes)?></script>
<script id="matieres_data" type="application/json"><?=json_encode($matieres)?></script>
<script id="periodes_data" type="application/json"><?=json_encode($periodes)?></script>
<script id="annees_data" type="application/json"><?=json_encode($annees)?></script>
<script>var ACTIVE_PERIODE_ID='<?=$id_periode_active?>',ACTIVE_ANNEE_ID='<?=$id_annee_active?>';</script>
<script src="<?=base_url()?>assets/js/api.js"></script>
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
    if(r.data.length===1){gMid=r.data[0].id_matiere;gMName=r.data[0].libelle;loadGrilleNotes();if(isEval)setTimeout(()=>document.getElementById('addEvalRow').style.display='flex',200);return}
    document.getElementById('coursModalTitle').textContent='Choisir un cours — '+(isEval?'Évaluation':'Notes');
    document.getElementById('coursModalClasse').textContent=cn;
    document.getElementById('coursList').innerHTML=r.data.map(m=>`<button class="btn border border-neutral-300 bg-hover-neutral-100 text-start px-16 py-12 radius-8 d-flex align-items-center gap-12 w-100" onclick="${isEval?'selectEval':'selectNotes'}('${id}','${m.id_matiere}','${m.libelle.replace(/'/g,"\\'")}',${isEval})"><span class="d-flex align-items-center justify-content-center bg-primary-100 text-primary-600 radius-4" style="width:40px;height:40px"><i class="ri-book-open-line"></i></span><div><span class="fw-semibold text-sm">${m.libelle}</span><small class="d-block text-secondary-light text-xs">${m.code||''}</small></div><i class="ri-arrow-right-s-line ms-auto text-secondary-light"></i></button>`).join('');
    new bootstrap.Modal(document.getElementById('coursModal')).show();
  }).catch(()=>Swal.fire({icon:'error',title:'Erreur',text:'Chargement impossible'}));
}
function selectNotes(id,mid,mn,isEval){bootstrap.Modal.getInstance(document.getElementById('coursModal')).hide();gCId=id;gMid=mid;gMName=mn;loadGrilleNotes()}
function selectEval(id,mid,mn,isEval){bootstrap.Modal.getInstance(document.getElementById('coursModal')).hide();gCId=id;gMid=mid;gMName=mn;loadGrilleNotes();setTimeout(()=>document.getElementById('addEvalRow').style.display='flex',200)}

/* ==== GRILLE EXCEL ==== */
async function loadGrilleNotes(){
  document.getElementById('classesGrid').style.display='none';document.getElementById('grilleCard').style.display='';
  document.getElementById('grilleTitle').textContent=gMName+' | Chargement...';
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
  document.getElementById('grilleTitle').textContent=gMName;

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
function showAddEval(){document.getElementById('addEvalRow').style.display='flex';document.getElementById('addEvalLibelle').focus()}
async function addEvaluation(){
  let lib=document.getElementById('addEvalLibelle').value.trim();
  if(!lib){Swal.fire({icon:'warning',text:'Libellé obligatoire'});return}
  let d={libelle:lib,id_classe:gCId,id_matiere:gMid,id_periode:ACTIVE_PERIODE_ID,id_annee:ACTIVE_ANNEE_ID,date_eval:document.getElementById('addEvalDate').value,type:document.getElementById('addEvalType').value,coefficient:1,sur:document.getElementById('addEvalSur').value||20};
  let r=await API.evaluations.create(d);
  if(r.success){document.getElementById('addEvalLibelle').value='';document.getElementById('addEvalRow').style.display='none';Toast.fire({icon:'success',title:'Évaluation ajoutée'});loadGrilleNotes()}else Swal.fire({icon:'error',text:r.message});
}

function backToClasses(){gCId=null;gMid=null;document.getElementById('grilleCard').style.display='none';document.getElementById('classesGrid').style.display=''}

(function(){var w=setInterval(function(){if(typeof jQuery!=='undefined'&&$.fn&&$.fn.DataTable&&typeof API!=='undefined'){clearInterval(w);loadClassesGrid()}},50)})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
