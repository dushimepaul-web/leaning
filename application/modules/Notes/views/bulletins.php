<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<style>
:root {
  --clr-excellent: #059669; --clr-bon: #2563eb; --clr-moyen: #d97706; --clr-faible: #dc2626;
  --bg-excellent: #ecfdf5; --bg-bon: #eff6ff; --bg-moyen: #fffbeb; --bg-faible: #fef2f2;
  --radius: 8px; --shadow-sm: 0 1px 2px rgba(0,0,0,.04); --shadow-md: 0 4px 12px rgba(0,0,0,.06);
}
.bull-app *{box-sizing:border-box}
.classe-dropdown{position:absolute;top:calc(100% + 4px);left:0;right:0;background:var(--base);border:1px solid var(--neutral-50);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;z-index:1050;padding:4px;}
.classe-item{display:flex;align-items:center;gap:10px;padding:9px 12px;cursor:pointer;font-size:.875rem;color:#1F2937;border-radius:6px;transition:background .15s;}
.classe-item:hover,.classe-item.active{background:var(--primary-100,#e6f5f4);color:var(--primary-600);}
.classe-item i{font-size:1rem;color:var(--primary-600);flex-shrink:0;}
.classe-empty{padding:12px;text-align:center;color:var(--neutral-500,#6B7280);font-size:.85rem;}
.search-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--neutral-500,#6B7280);font-size:1rem;pointer-events:none;}
#id_classe{padding-left:38px;}
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
.bulletin-card .bul-body table{width:100%;border-collapse:collapse;font-size:11px;table-layout:fixed}
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
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Bulletins</h1>
      <div><a href="<?=base_url('Dashboard')?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a><span class="text-secondary-light"> / Bulletins</span></div>
    </div>
    <a href="<?=base_url('Notes')?>" class="btn btn-outline-primary-600 d-flex align-items-center gap-6"><i class="ri-pencil-line"></i>Saisie des notes</a>
  </div>

  <div class="card mb-24" id="filtresCard">
    <div class="card-body p-16">
      <div class="row g-2 align-items-end flex-nowrap">
        <div class="col" style="min-width:200px; position:relative;">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe *</label>
          <div style="position:relative;">
            <i class="ri-search-line search-icon"></i>
            <input type="text" class="form-control" id="id_classe" placeholder="Tapez pour chercher..." autocomplete="off">
          </div>
          <div id="classeDropdown" class="classe-dropdown d-none">
            <?php foreach($classes as $c): ?>
              <div class="classe-item" data-id="<?=$c['id_classe']?>" data-nom="<?=htmlspecialchars($c['libelle'])?>">
                <i class="ri-graduation-cap-line"></i>
                <span><?=htmlspecialchars($c['libelle'])?></span>
              </div>
            <?php endforeach; ?>
            <div class="classe-empty d-none">Aucune classe trouvée</div>
          </div>
        </div>
        <div class="col" style="min-width:160px;">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Trimestre</label>
          <select class="form-control form-select" id="id_periode">
            <option value="all">Année complète</option>
            <?php foreach($periodes as $p): ?><option value="<?=$p['id_periode']?>" <?=$p['id_periode']==$id_periode_active?'selected':''?>><?=htmlspecialchars($p['libelle'])?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col" style="min-width:160px;">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Année</label>
          <select class="form-control form-select" id="id_annee">
            <?php foreach($annees as $a): ?><option value="<?=$a['id_annee']?>" <?=($a['est_en_cours']||$a['id_annee']==$id_annee_active)?'selected':''?>><?=htmlspecialchars($a['libelle'])?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-auto d-flex gap-2 align-items-end">
          <button type="button" class="btn btn-primary-600" onclick="showBulletins()"><i class="ri-file-list-3-line me-1"></i>Générer les bulletins</button>
        </div>
      </div>
    </div>
  </div>

  <div id="bulletinsCard" style="display:none;">
    <div class="bull-toolbar">
      <div class="btn-group">
        <button onclick="backToClasses()"><i class="ri-arrow-left-line"></i> Classes</button>
        <span style="font-weight:600;color:#1e293b;font-size:14px;padding:0 8px" id="bulTitle">—</span>
      </div>
      <div class="btn-group">
        <button onclick="if(gClasseId) window.open(BASE_URL + 'Notes/Bulletins/export/' + gClasseId, '_blank'); else Swal.fire({icon:'warning',title:'Attention',text:'Veuillez sélectionner une classe'});"><i class="ri-printer-line"></i> Imprimer tous</button>
        <button class="primary" onclick="genererBulletinsClasse()"><i class="ri-magic-line"></i> Générer bulletins</button>
      </div>
    </div>
    <div id="bulletinsList"></div>
  </div>
</div>

<script src="<?=base_url()?>assets/js/api.js"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
const Toast=Swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:2500,timerProgressBar:true});
let gClasseId=null,gClasseNom='',gPeriodeId='all';

document.getElementById('id_classe').addEventListener('click', function() {
  document.getElementById('classeDropdown').classList.remove('d-none');
});

document.getElementById('id_classe').addEventListener('focus', function() {
  document.getElementById('classeDropdown').classList.remove('d-none');
});

document.getElementById('id_classe').addEventListener('input', function() {
  const q = this.value.trim().toLowerCase();
  document.getElementById('classeDropdown').classList.remove('d-none');
  let visibles = 0;
  document.querySelectorAll('.classe-item').forEach(item => {
    const match = !q || item.dataset.nom.toLowerCase().includes(q);
    item.style.display = match ? '' : 'none';
    if (match) visibles++;
  });
  document.querySelector('.classe-empty').classList.toggle('d-none', visibles > 0);
});

document.querySelectorAll('.classe-item').forEach(item => {
  item.addEventListener('click', function() {
    document.getElementById('id_classe').value = this.dataset.nom;
    gClasseId = parseInt(this.dataset.id);
    gClasseNom = this.dataset.nom;
    document.getElementById('classeDropdown').classList.add('d-none');
  });
});

document.addEventListener('click', function(e) {
  if (!e.target.closest('#id_classe') && !e.target.closest('#classeDropdown')) {
    document.getElementById('classeDropdown').classList.add('d-none');
  }
});

document.getElementById('id_annee').addEventListener('change', async function() {
  const aid = this.value;
  const sel = document.getElementById('id_periode');
  try {
    const resp = await fetch(API.base_url + 'api/bulletins/periodes/' + aid);
    const r = await resp.json();
    if (r.success && r.data) {
      sel.innerHTML = '<option value="all">Année complète</option>';
      r.data.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id_periode;
        opt.textContent = p.libelle;
        sel.appendChild(opt);
      });
    }
  } catch(e) {
    console.error('[Periodes]', e);
  }
});

function periodeSelectionnee() {
  const sel = document.getElementById('id_periode');
  const nom = sel.options[sel.selectedIndex].text;
  return { id: sel.value, nom: nom };
}

function showBulletins() {
  if (!gClasseId) { Swal.fire({ icon: 'warning', title: 'Sélection', text: 'Veuillez choisir une classe' }); return; }
  const p = periodeSelectionnee();
  document.getElementById('bulletinsCard').style.display = '';
  document.getElementById('bulTitle').textContent = gClasseNom + ' — ' + p.nom;
  openBulletinPeriode(gClasseId, p.id, p.nom);
}

async function openBulletinPeriode(id,periodeId,periodeNom){
  document.getElementById('bulletinsList').innerHTML='<div class="text-center py-32"><div class="spinner-border text-primary-600"></div><p class="mt-8 text-secondary-light">Chargement des bulletins...</p></div>';

  const url=API.base_url+'api/bulletins/complet/'+id+'?periode='+periodeId+'&annee='+document.getElementById('id_annee').value+'&cumul=1';
  try {
    const resp = await fetch(url);
    if (!resp.ok) {
      document.getElementById('bulletinsList').innerHTML='<div class="text-center py-32 text-danger">Erreur HTTP '+resp.status+' — '+resp.statusText+'</div>';
      return;
    }
    const r = await resp.json();
    if(!r.success){Swal.fire({icon:'error',text:r.message});return}
    renderBulletins(r.data,periodeNom,periodeId);
  } catch(e) {
    document.getElementById('bulletinsList').innerHTML='<div class="text-center py-32 text-danger">Erreur JS: '+e.message+'</div>';
  }
}

function backToClasses(){gClasseId=null;document.getElementById('bulletinsCard').style.display='none'}

async function genererBulletinsClasse(){
  if(!gClasseId){return}
  Swal.fire({title:'Génération...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
  const r=await fetch(API.base_url+'api/bulletins/generer',{method:'POST',headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},body:JSON.stringify({id_classe:gClasseId,id_annee:document.getElementById('id_annee').value,csrf_test_name:typeof CSRF_TOKEN!=='undefined'?CSRF_TOKEN:''})}).then(r=>r.json());
  Swal.close();
  r.success?Toast.fire({icon:'success',title:r.message}):Swal.fire({icon:'error',text:r.message})
}

function renderBulletins(data,periodeNom,periodeId){
  const periodes=data.periodes||[];
  const matieres=data.matieres||[];
  const maxima=data.maxima||{};
  const section=data.section||data.classe_section||'';
  const anneeScolaire=data.annee_scolaire||'2025-2026';
  const classeNom=data.classe||'';
  const ressActive = (data.ressources_active===undefined ? 1 : parseInt(data.ressources_active)) !== 0;
  const compActive = (data.competences_active===undefined ? 1 : parseInt(data.competences_active)) !== 0;
  const examenActive = (data.examen_active===undefined ? 0 : parseInt(data.examen_active)) !== 0;
  const modeB = ressActive || compActive;
  const modeA = examenActive && !modeB;
  const CONDUITE_DEFAUT=60;
  const compPct = parseFloat(data.competences_pourcentage) || 40;
  const ressPct = parseFloat(data.ressources_pourcentage) || 60;
  const pids=periodes.map(p=>p.id_periode);

  // Cumul : trimestre choisi = cumul depuis le 1er trimestre ; 'all' = bulletin complet
  const cumulMode=periodeId!=='all';
  let selIdx=periodes.length-1;
  if(cumulMode){
    for(let i=0;i<periodes.length;i++){if(String(periodes[i].id_periode)===String(periodeId)){selIdx=i;break;}}
  }

  function nf(v){return v!==undefined&&v!==null&&v>0?v.toFixed(1):'-'}
  function fmtPct(num,den){return den>0?(num/den*100).toFixed(2)+'%':'-'}
  function matEl(el,mid){return el.matieres.find(m=>m.id_matiere==mid)||{periodes:{},annuel:{note:0,max:0,pct:0}};}
  function cumMax(mid,i){
    const t={tj:0,comp:0,ress:0,ex:0,tot:0};
    for(let k=0;k<=i;k++){
      const mm=maxima[mid]&&maxima[mid][pids[k]];
      if(mm){t.tj+=mm.tj||0;t.comp+=mm.comp||0;t.ress+=mm.ress||0;t.ex+=mm.ex||0;}
    }
    t.tot=t.tj+t.comp+t.ress+t.ex;
    return t;
  }
  function midMax(mid,i){
    const mm=maxima[mid]&&maxima[mid][pids[i]];
    return mm?{tj:mm.tj||0,comp:mm.comp||0,ress:mm.ress||0,ex:mm.ex||0,tot:(mm.tj||0)+(mm.comp||0)+(mm.ress||0)+(mm.ex||0)}:{tj:0,comp:0,ress:0,ex:0,tot:0};
  }
  function maxBlock(mid){return midMax(mid, cumulMode ? selIdx : 0);}
  function noteCol(el,mid,i){
    const me=matEl(el,mid);
    const t={tj:0,comp:0,ress:0,ex:0,tot:0};
    const per=me.periodes[pids[i]]||{tj:0,comp:0,ress:0,ex:0};
    t.tj=per.tj||0;t.comp=per.comp||0;t.ress=per.ress||0;t.ex=per.ex||0;
    t.tot=t.tj+t.comp+t.ress+t.ex;
    return t;
  }
  function matAnn(el,mid){
    const me=matEl(el,mid);
    let totNote = 0, totMax = 0;
    periodes.forEach(p => {
      const per = me.periodes[p.id_periode] || {tj:0, comp:0, ress:0, ex:0};
      totNote += (per.tj||0) + (per.comp||0) + (per.ress||0) + (per.ex||0);
      const mm = maxima[mid] && maxima[mid][p.id_periode] ? maxima[mid][p.id_periode] : {tj:0,comp:0,ress:0,ex:0};
      totMax += (mm.tj||0) + (mm.comp||0) + (mm.ress||0) + (mm.ex||0);
    });
    return {max: totMax, note: totNote};
  }
  function cdVal(el,pid){
    const cp=(el.points_conduite&&el.points_conduite[pid]);
    return cp?cp.points:CONDUITE_DEFAUT;
  }
  function cdCol(el,i){
    return cdVal(el,pids[i]);
  }
  function cdAn(el){
    return periodes.reduce(function(s,p){return s+cdVal(el,p.id_periode)},0);
  }
  function stCol(el,i){
    let st=0;
    matieres.forEach(mat=>{st+=noteCol(el,mat.id_matiere,i).tot;});
    return st;
  }
  function colBlank(i){return cumulMode&&i>selIdx;}

  const colSpan = modeB ? 4 : 3;
  const subHead = modeB ? '<th>TJ</th><th>COMP</th><th>RESS</th><th>TOT</th>' : '<th>TJ</th><th>EX</th><th>TOT</th>';
  const col_span = colSpan;
  function cells(t){ return modeB
    ? [nf(t.tj), nf(t.comp), nf(t.ress), `<strong>${nf(t.tot)}</strong>`]
    : [nf(t.tj), nf(t.ex), `<strong>${nf(t.tot)}</strong>`]; }

  let html='';
  data.eleves.forEach((el,idx)=>{
    let aAnnNote=0,aAnnMax=0;
    matieres.forEach(mat=>{
      const aa=matAnn(el,mat.id_matiere);
      aAnnNote+=aa.note;aAnnMax+=aa.max;
    });
    const cdTot=cdAn(el);
    const cdMax=cumulMode?CONDUITE_DEFAUT*(selIdx+1):CONDUITE_DEFAUT*periodes.length;

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
            ${modeB ? `
            <tr>
              <th class="branches-header" rowspan="3"></th>
              <th colspan="4">MAXIMA</th>
              ${periodes.map(p=>`<th colspan="4">${p.libelle||''}</th>`).join('')}
              <th colspan="3" rowspan="2">TOTAUX ANNUELS</th>
            </tr>
            <tr>
              <th rowspan="2">TJ</th><th colspan="2">EXAMEN</th><th rowspan="2">TOT</th>
              ${periodes.map(()=>`<th rowspan="2">TJ</th><th colspan="2">EXAMEN</th><th rowspan="2">TOT</th>`).join('')}
            </tr>
            <tr>
              <th>COMP</th><th>RESS</th>
              ${periodes.map(()=>`<th>COMP</th><th>RESS</th>`).join('')}
              <th>MAX</th><th>TOT</th><th>%</th>
            </tr>` : `
            <tr>
              <th class="branches-header" rowspan="2"></th>
              <th colspan="3">MAXIMA</th>
              ${periodes.map(p=>`<th colspan="3">${p.libelle||''}</th>`).join('')}
              <th colspan="3">TOTAUX ANNUELS</th>
            </tr>
            <tr>
              ${subHead}
              ${periodes.map(()=>subHead).join('')}
              <th>MAX</th><th>TOT</th><th>%</th>
            </tr>`}
          </thead>
          <tbody>`;

    // Séparer les matières actives (générales/techniques) et inactives
    const matieresActives = matieres.filter(m => parseInt(m.est_actif) !== 0);
    const matieresInactives = el.matieres_inactives || [];
    const generaux = matieresActives.filter(m => parseInt(m.est_general) === 1);
    const techniques = matieresActives.filter(m => parseInt(m.est_general) !== 1);

    function renderMatiereGroup(list) {
      let groupHtml = '';
      list.forEach((mat) => {
        const mid=mat.id_matiere;
        const mb=maxBlock(mid);
        const aa=matAnn(el,mid);

        groupHtml+=`<tr>
          <td class="branches matiere">${mat.libelle}</td>
          ${cells(mb).map(c=>`<td class="num">${c}</td>`).join('')}`;

        periodes.forEach((p,i)=>{
          if(colBlank(i)){groupHtml+=`<td class="num">-</td>`.repeat(colSpan);return;}
          const nt=noteCol(el,mid,i);
          groupHtml+=cells(nt).map(c=>`<td class="num">${c}</td>`).join('');
        });

        groupHtml+=`<td class="num"><strong>${nf(aa.max)}</strong></td>
          <td class="num"><strong>${nf(aa.note)}</strong></td>
          <td class="num"><strong>${fmtPct(aa.note,aa.max)}</strong></td>
        </tr>`;
      });
      return groupHtml;
    }

    // Calculs groupes pour sous-totaux
    function groupTotals(list, i) {
      let tMax = {tj:0, comp:0, ress:0, ex:0, tot:0};
      let tNote = {tj:0, comp:0, ress:0, ex:0, tot:0};
      list.forEach(mat => {
        const mb = maxBlock(mat.id_matiere);
        tMax.tj += mb.tj; tMax.comp += mb.comp; tMax.ress += mb.ress; tMax.ex += mb.ex; tMax.tot += mb.tot;
        const nt = noteCol(el, mat.id_matiere, i);
        tNote.tj += nt.tj; tNote.comp += nt.comp; tNote.ress += nt.ress; tNote.ex += nt.ex; tNote.tot += nt.tot;
      });
      return {max: tMax, note: tNote};
    }

    function groupAnn(list) {
      let aMax = 0, aNote = 0;
      list.forEach(mat => {
        const aa = matAnn(el, mat.id_matiere);
        aMax += aa.max; aNote += aa.note;
      });
      return {max: aMax, note: aNote};
    }

    // 1. COURS GENERAUX ET LANGUES
    html += `<tr><td class="branches" style="background:#e8e8e8; font-weight:bold;" colspan="${colSpan + periodes.length * colSpan + 3}">COURS GENERAUX ET LANGUES</td></tr>`;
    html += renderMatiereGroup(generaux);

    // SOUS-TOT1
    const genAnn = groupAnn(generaux);
    html += `<tr><td class="branches" style="font-weight:bold;">SOUS-TOT1</td>`;
    const genMb0 = groupTotals(generaux, 0);
    if(modeB) {
      html += `<td class="num"><strong>${nf(genMb0.max.tj)}</strong></td><td class="num"><strong>${nf(genMb0.max.comp)}</strong></td><td class="num"><strong>${nf(genMb0.max.ress)}</strong></td><td class="num"><strong>${nf(genMb0.max.tot)}</strong></td>`;
    } else {
      html += `<td class="num"><strong>${nf(genMb0.max.tj)}</strong></td><td class="num"><strong>${nf(genMb0.max.ex)}</strong></td><td class="num"><strong>${nf(genMb0.max.tot)}</strong></td>`;
    }
    periodes.forEach((p,i)=>{
      if(colBlank(i)){
        html += `<td class="num">-</td>`.repeat(colSpan);
        return;
      }
      const gt = groupTotals(generaux, i);
      html += cells(gt.note).map(c=>`<td class="num">${c}</td>`).join('');
    });
    html += `<td class="num"><strong>${nf(genAnn.max)}</strong></td>
      <td class="num"><strong>${nf(genAnn.note)}</strong></td>
      <td class="num"><strong>${fmtPct(genAnn.note,genAnn.max)}</strong></td>
    </tr>`;

    // 2. COURS TECHNIQUE
    html += `<tr><td class="branches" style="background:#e8e8e8; font-weight:bold;" colspan="${colSpan + periodes.length * colSpan + 3}">COURS TECHNIQUE</td></tr>`;
    html += renderMatiereGroup(techniques);

    // SOUS-TOT2
    const techAnn = groupAnn(techniques);
    html += `<tr><td class="branches" style="font-weight:bold;">SOUS-TOT2</td>`;
    const techMb0 = groupTotals(techniques, 0);
    if(modeB) {
      html += `<td class="num"><strong>${nf(techMb0.max.tj)}</strong></td><td class="num"><strong>${nf(techMb0.max.comp)}</strong></td><td class="num"><strong>${nf(techMb0.max.ress)}</strong></td><td class="num"><strong>${nf(techMb0.max.tot)}</strong></td>`;
    } else {
      html += `<td class="num"><strong>${nf(techMb0.max.tj)}</strong></td><td class="num"><strong>${nf(techMb0.max.ex)}</strong></td><td class="num"><strong>${nf(techMb0.max.tot)}</strong></td>`;
    }
    periodes.forEach((p,i)=>{
      if(colBlank(i)){
        html += `<td class="num">-</td>`.repeat(colSpan);
        return;
      }
      const gt = groupTotals(techniques, i);
      html += cells(gt.note).map(c=>`<td class="num">${c}</td>`).join('');
    });
    html += `<td class="num"><strong>${nf(techAnn.max)}</strong></td>
      <td class="num"><strong>${nf(techAnn.note)}</strong></td>
      <td class="num"><strong>${fmtPct(techAnn.note,techAnn.max)}</strong></td>
    </tr>`;

    // ---- C: Conduite ----
    const conduiteMax=CONDUITE_DEFAUT;
    html+=`<tr>
      <td class="branches">Conduite</td>
      <td class="num gris">${conduiteMax}</td>${'<td></td>'.repeat(colSpan-2)}<td class="num">${conduiteMax}</td>`;
    periodes.forEach((p,i)=>{
      if(colBlank(i)){html+=`<td></td>`.repeat(colSpan-1)+`<td class="num">-</td>`;return;}
      html+=`<td></td>`.repeat(colSpan-1)+`<td class="num">${cdCol(el,i)}</td>`;
    });
    html+=`<td class="num"><strong>${CONDUITE_DEFAUT * periodes.length}</strong></td>
      <td class="num"><strong>${cdTot.toFixed(1)}</strong></td>
      <td>${fmtPct(cdTot, CONDUITE_DEFAUT * periodes.length)}</td>
    </tr>`;

    // ---- Total ----
    const totalMax0 = {
      tj: genMb0.max.tj + techMb0.max.tj,
      comp: genMb0.max.comp + techMb0.max.comp,
      ress: genMb0.max.ress + techMb0.max.ress,
      ex: genMb0.max.ex + techMb0.max.ex,
      tot: genMb0.max.tot + techMb0.max.tot
    };
    html+=`<tr>
      <td class="branches" style="font-weight:bold;">Total</td>`;
    if(modeB) {
      html += `<td class="num"><strong>${nf(totalMax0.tj)}</strong></td><td class="num"><strong>${nf(totalMax0.comp)}</strong></td><td class="num"><strong>${nf(totalMax0.ress)}</strong></td><td class="num"><strong>${nf(totalMax0.tot)}</strong></td>`;
    } else {
      html += `<td class="num"><strong>${nf(totalMax0.tj)}</strong></td><td class="num"><strong>${nf(totalMax0.ex)}</strong></td><td class="num"><strong>${nf(totalMax0.tot)}</strong></td>`;
    }
    periodes.forEach((p,i)=>{
      if(colBlank(i)){html+=`<td></td>`.repeat(colSpan-1)+`<td class="num">-</td>`;return;}
      const ggt = {
        tj: groupTotals(generaux, i).note.tj + groupTotals(techniques, i).note.tj,
        comp: groupTotals(generaux, i).note.comp + groupTotals(techniques, i).note.comp,
        ress: groupTotals(generaux, i).note.ress + groupTotals(techniques, i).note.ress,
        ex: groupTotals(generaux, i).note.ex + groupTotals(techniques, i).note.ex,
        tot: groupTotals(generaux, i).note.tot + groupTotals(techniques, i).note.tot + cdCol(el, i)
      };
      html+=cells(ggt).map(c=>`<td class="num">${c}</td>`).join('');
    });
    html+=`<td class="num"><strong>${nf(aAnnMax+cdMax)}</strong></td>
      <td class="num"><strong>${nf(aAnnNote+cdTot)}</strong></td>
      <td class="num"><strong>${fmtPct(aAnnNote+cdTot,aAnnMax+cdMax)}</strong></td>
    </tr>`;

    // ---- E: Pourcentage ----
    html+=`<tr>
      <td class="branches">Pourcentage</td>`;
    
    // Maxima colonnes initiales (période 1 ou cumul)
    if(modeB) {
      html += `<td class="num"></td><td class="num"></td><td class="num"></td><td class="num"></td>`;
    } else {
      html += `<td class="num"></td><td class="num"></td><td class="num"></td>`;
    }

    periodes.forEach((p,i)=>{
      if(colBlank(i)){
        html+=`<td class="num">-</td>`.repeat(colSpan);
        return;
      }
      const pTotNote = stCol(el, i) + cdCol(el, i);
      let pTotMax = 0;
      matieres.forEach(mat => {
        const singleMm = midMax(mat.id_matiere, i);
        pTotMax += singleMm.tot;
      });
      pTotMax += CONDUITE_DEFAUT;
      
      const pPct = pTotMax > 0 ? (pTotNote / pTotMax * 100).toFixed(2) + '%' : '-';
      
      if(modeB) {
        html += `<td class="num"></td><td class="num"></td><td class="num"></td><td class="num"><strong>${pPct}</strong></td>`;
      } else {
        html += `<td class="num"></td><td class="num"></td><td class="num"><strong>${pPct}</strong></td>`;
      }
    });

    const totalAnnuelsMax = aAnnMax + (CONDUITE_DEFAUT * periodes.length);
    const totalAnnuelsNote = aAnnNote + cdTot;
    const annPct = totalAnnuelsMax > 0 ? (totalAnnuelsNote / totalAnnuelsMax * 100).toFixed(2) + '%' : '-';

    html+=`<td class="num"></td>
      <td class="num"></td>
      <td class="num"><strong>${annPct}</strong></td>
    </tr>`;

    // ---- F: Place ----
    const placeVal = el.rang > 0 ? el.rang + 'e' : '—';
    html+=`<tr>
      <td class="branches">Place</td>
      <td class="num gris"></td><td class="num gris"></td><td class="num gris"></td>`;
    
    if(modeB) {
      html += `<td class="num gris"></td>`;
    }

    periodes.forEach((p,i)=>{
      if(colBlank(i)){
        html+=`<td class="num gris">-</td>`.repeat(colSpan);
        return;
      }
      // Calculer le rang de l'élève pour la période i spécifique
      const pId = p.id_periode;
      let scoresP = [];
      data.eleves.forEach(otherEl => {
        let oNote = 0;
        matieres.forEach(mat => {
          const mid = mat.id_matiere;
          let me = null;
          otherEl.matieres.forEach(m_item => { if(m_item.id_matiere == mid) me = m_item; });
          if(me && me.periodes[pId]) {
            const per = me.periodes[pId];
            oNote += (per.tj||0) + (per.comp||0) + (per.ress||0) + (per.ex||0);
          }
        });
        const oCd = (otherEl.points_conduite && otherEl.points_conduite[pId]) ? otherEl.points_conduite[pId].points : 60;
        oNote += oCd;
        
        let oMax = 0;
        matieres.forEach(mat => {
          const mx = maxima[mat.id_matiere] && maxima[mat.id_matiere][pId] ? maxima[mat.id_matiere][pId] : {tj:0,comp:0,ress:0,ex:0};
          oMax += (mx.tj||0) + (mx.comp||0) + (mx.ress||0) + (mx.ex||0);
        });
        oMax += 60;
        const oPct = oMax > 0 ? (oNote / oMax * 100) : 0;
        scoresP.push({id: otherEl.id_etudiant, pct: oPct});
      });

      scoresP.sort((a,b)=>b.pct - a.pct);
      let r = 1, prevPct = -1, myRank = '-';
      scoresP.forEach((s, idx) => {
        if(prevPct >= 0 && s.pct < prevPct) r = idx + 1;
        if(s.id == el.id_etudiant) { myRank = (s.pct > 0) ? r + 'e' : '—'; }
        prevPct = s.pct;
      });

      if(modeB) {
        html += `<td class="num gris"></td><td class="num gris"></td><td class="num gris"></td><td class="num gris"><strong>${myRank}</strong></td>`;
      } else {
        html += `<td class="num gris"></td><td class="num gris"></td><td class="num gris"><strong>${myRank}</strong></td>`;
      }
    });

    html+=`<td class="num gris"></td>
      <td class="num gris"></td>
      <td class="num gris"><strong>${el.rang > 0 ? el.rang + 'e' : '—'}</strong></td>
    </tr>`;

    // ---- Cours Inactifs (après Place) ----
    if(matieresInactives.length > 0) {
      matieresInactives.forEach(mat => {
        const mid = mat.id_matiere;
        const maxTjInactif = parseFloat(mat.note_max_matiere) || 0;
        let maxCompInactif = 0, maxRessInactif = 0, maxExInactif = 0;
        let maxTotInactif = maxTjInactif;
        if(modeB) {
          maxCompInactif = Math.round(maxTjInactif * compPct / 100 * 10) / 10;
          maxRessInactif = Math.round(maxTjInactif * ressPct / 100 * 10) / 10;
          maxTotInactif = maxTjInactif + maxCompInactif + maxRessInactif;
        } else if(modeA) {
          maxExInactif = maxTjInactif;
          maxTotInactif = maxTjInactif * 2;
        }

        let matHtml = `<tr>
          <td class="branches matiere">${mat.libelle}</td>`;
        if(modeB) {
          matHtml += `<td class="num">${nf(maxTjInactif)}</td><td class="num">${nf(maxCompInactif)}</td><td class="num">${nf(maxRessInactif)}</td><td class="num"><strong>${nf(maxTotInactif)}</strong></td>`;
        } else {
          matHtml += `<td class="num">${nf(maxTjInactif)}</td><td class="num">${nf(maxExInactif)}</td><td class="num"><strong>${nf(maxTotInactif)}</strong></td>`;
        }

        let annNote = 0, annMax = 0;
        periodes.forEach((p,i) => {
          if(colBlank(i)){
            matHtml += `<td class="num">-</td>`.repeat(colSpan);
            return;
          }
          const per = mat.periodes[p.id_periode] || {tj:0,comp:0,ress:0,ex:0};
          let ntTj = per.tj||0, ntComp = per.comp||0, ntRess = per.ress||0, ntEx = per.ex||0;
          if(modeB) ntEx = 0;
          else if(modeA) { ntComp = 0; ntRess = 0; }
          else { ntComp = 0; ntRess = 0; ntEx = 0; }
          const ntTot = ntTj+ntComp+ntRess+ntEx;
          annNote += ntTot;
          if(modeB) {
            matHtml += `<td class="num">${nf(ntTj)}</td><td class="num">${nf(ntComp)}</td><td class="num">${nf(ntRess)}</td><td class="num"><strong>${nf(ntTot)}</strong></td>`;
          } else {
            matHtml += `<td class="num">${nf(ntTj)}</td><td class="num">${nf(ntEx)}</td><td class="num"><strong>${nf(ntTot)}</strong></td>`;
          }
        });

        matHtml += `<td class="num"><strong>-</strong></td>
          <td class="num"><strong>${nf(annNote)}</strong></td>
          <td class="num"><strong>-</strong></td>
        </tr>`;
        html += matHtml;
      });
    }

    // ---- H: Signatures (2 rows) ----
    html+=`<tr style="height:40px;">
      <td class="branches" rowspan="2">Signatures</td>
      <td colspan="${colSpan}">PARENTS</td>`;
    periodes.forEach((p,i)=>{
      html+=`<td colspan="${colSpan}"></td>`;
    });
    html+=`<td colspan="3" rowspan="2"></td>
    </tr>`;
    html+=`<tr style="height:40px;">
      <td colspan="${colSpan}">TITULAIRE</td>`;
    periodes.forEach((p,i)=>{
      html+=`<td colspan="${colSpan}"></td>`;
    });
    html+=`</tr>`;

    html+=`</tbody>
        </table>
      </div>
    </div>`;
  });

  document.getElementById('bulletinsList').innerHTML=html||'<div class="text-center py-32 text-secondary-light">Aucun élève trouvé</div>';
}
</script>