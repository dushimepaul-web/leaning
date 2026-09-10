<?php include VIEWPATH.'includes/Header.php'; ?>
<style>
.classe-dropdown{position:absolute;top:calc(100% + 4px);left:0;right:0;background:var(--base);border:1px solid var(--neutral-50);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;z-index:1050;padding:4px;}
.classe-item{display:flex;align-items:center;gap:10px;padding:9px 12px;cursor:pointer;font-size:.875rem;color:#1F2937;border-radius:6px;transition:background .15s;}
.classe-item:hover,.classe-item.active{background:var(--primary-100,#e6f5f4);color:var(--primary-600);}
.classe-item i{font-size:1rem;color:var(--primary-600);flex-shrink:0;}
.classe-empty{padding:12px;text-align:center;color:var(--neutral-500,#6B7280);font-size:.85rem;}
.search-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--neutral-500,#6B7280);font-size:1rem;pointer-events:none;}
#id_classe{padding-left:38px;}
</style>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Points de conduite</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Points de conduite</span>
      </div>
    </div>
  </div>

  <div class="card mb-24">
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
                <span class="classe-nom"><?=htmlspecialchars($c['libelle'])?></span>
              </div>
            <?php endforeach; ?>
            <div class="classe-empty d-none">Aucune classe trouvée</div>
          </div>
        </div>
        <div class="col" style="min-width:140px;">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Trimestre</label>
          <select class="form-control form-select" id="id_periode">
            <?php foreach($periodes as $p): ?><option value="<?=$p['id_periode']?>" data-annee="<?=$p['id_annee']?>" <?=$p['id_periode']==$id_periode_active?'selected':''?>><?=htmlspecialchars($p['libelle'])?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col" style="min-width:140px;">
          <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Année</label>
          <select class="form-control form-select" id="id_annee">
            <?php foreach($annees as $a): ?><option value="<?=$a['id_annee']?>" <?=($a['est_en_cours']||$a['id_annee']==$id_annee_active)?'selected':''?>><?=htmlspecialchars($a['libelle'])?></option><?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table bordered-table mb-0" id="dataTable" style="width:100%">
            <thead>
              <tr>
                <th>N°</th>
                <th>Matricule</th>
                <th>Nom et prénoms</th>
                <th style="min-width:110px">Points initiaux</th>
                <th>Sanctions</th>
                <th>Points retirés</th>
                <th>Solde</th>
                <th style="min-width:180px">Observation</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="dataBody">
              <tr><td colspan="9" class="text-center text-secondary-light py-32">Sélectionnez une classe</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="sanctionsModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-header">
        <h5 class="modal-title">Sanctions de <span id="sanctionEleve"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-16">
        <input type="hidden" id="sanctionPointId">
        <div class="row g-3 align-items-end mb-16">
          <div class="col-md-4">
            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Motif *</label>
            <input type="text" class="form-control" id="sMotif" placeholder="Ex: Retard, Indiscipline...">
          </div>
          <div class="col-md-2">
            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Points à retirer *</label>
            <input type="number" step="0.5" min="0.5" class="form-control" id="sPoints" value="5">
          </div>
          <div class="col-md-3">
            <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date</label>
            <input type="date" class="form-control" id="sDate" value="<?= date('Y-m-d') ?>">
          </div>
          <div class="col-md-3">
            <button type="button" class="btn btn-primary-600 w-100" onclick="addSanction()"><i class="ri-add-line me-1"></i> Ajouter</button>
          </div>
        </div>
        <div class="card bg-base-200">
          <div class="card-body p-12">
            <h6 class="text-sm fw-semibold mb-12">Historique des sanctions</h6>
            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <thead>
                  <tr><th>Date</th><th>Motif</th><th class="text-center">Points retirés</th><th class="text-center">Solde</th><th class="text-center">Action</th></tr>
                </thead>
                <tbody id="sanctionsBody"></tbody>
              </table>
            </div>
            <div class="d-flex justify-content-between mt-12 text-sm fw-semibold">
              <span>Total retirés : <span id="totalRetires" class="text-danger-600">0</span></span>
              <span>Solde actuel : <span id="soldeActuel" class="text-success-600">0</span></span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="ri-close-line me-1"></i> Fermer</button>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let currentList = [];
let currentPointView = null;
let currentClasseId = null;
const pointsDefautEcole = <?= (float)($points_defaut_ecole ?? 60) ?>;
const idAnneeActive = '<?= $id_annee_active ?>';
const idPeriodeActive = '<?= $id_periode_active ?>';

function filterPeriodes() {
  const aid = document.getElementById('id_annee').value;
  const sel = document.getElementById('id_periode');
  let firstVisible = null;
  Array.from(sel.options).forEach(function(opt) {
    if (!opt.value) return;
    const visible = opt.dataset.annee === aid;
    opt.style.display = visible ? '' : 'none';
    if (visible && !firstVisible) firstVisible = opt;
  });
  if (firstVisible && !Array.from(sel.options).some(o => o.dataset.annee === aid && o.selected)) {
    sel.value = firstVisible.value;
  }
}

filterPeriodes();

document.getElementById('id_annee').addEventListener('change', function() {
  filterPeriodes();
  if (currentClasseId) loadData();
});

['id_periode'].forEach(id => {
  document.getElementById(id).addEventListener('change', () => {
    if (currentClasseId) loadData();
  });
});

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
    currentClasseId = parseInt(this.dataset.id);
    document.getElementById('classeDropdown').classList.add('d-none');
    loadData();
  });
});

document.addEventListener('click', function(e) {
  if (!e.target.closest('#id_classe') && !e.target.closest('#classeDropdown')) {
    document.getElementById('classeDropdown').classList.add('d-none');
  }
});

async function loadData() {
  if (!currentClasseId) { Swal.fire({ icon: 'warning', title: 'Sélection', text: 'Veuillez choisir une classe' }); return; }
  const p = document.getElementById('id_periode').value;
  const a = document.getElementById('id_annee').value;
  const res = await API.conduite.list('?classe=' + currentClasseId + '&periode=' + p + '&annee=' + a);
  if (!res.success) { $('#dataBody').html('<tr><td colspan="9" class="text-center text-danger">' + res.message + '</td></tr>'); return; }
  currentList = res.data;
  renderTable();
}

function renderTable() {
  if (!currentList.length) {
    $('#dataBody').html('<tr><td colspan="9" class="text-center text-secondary-light py-32">Aucun élève dans cette classe</td></tr>');
    return;
  }
  let rows = '';
  currentList.forEach((el, i) => {
    const solde = el.points !== null ? el.points : '—';
    const badge = el.points !== null && el.points < 30 ? 'bg-danger-100 text-danger-600' : 'bg-success-100 text-success-600';
    rows += `<tr>
      <td>${i + 1}</td>
      <td>${el.matricule || '-'}</td>
      <td class="fw-semibold">${el.fullname || '-'}</td>
      <td class="text-center fw-semibold">${el.points_initial !== null ? el.points_initial : pointsDefautEcole}</td>
      <td class="text-center">${el.nb_sanctions || 0}</td>
      <td class="text-center text-danger-600 fw-semibold">${el.points_retires || 0}</td>
      <td class="text-center"><span class="${badge} px-24 py-4 radius-4 fw-medium text-sm">${solde}</span></td>
      <td><input type="text" class="form-control form-control-sm pt-observation" data-id="${el.id_point_conduite}" data-uuid="${el.point_uuid || ''}" value="${el.observation || ''}" placeholder="Observation..." ${el.id_point_conduite ? '' : 'disabled'}></td>
      <td>
        <button type="button" class="btn btn-sm btn-outline-primary-600" onclick="openSanctions('${el.id_point_conduite || ''}', '${(el.fullname || '').replace(/'/g, '')}', '${el.id_etudiant}')">
          <i class="ri-history-line me-1"></i> Sanctions
        </button>
      </td>
    </tr>`;
  });
  $('#dataBody').html(rows);

  $('.pt-observation').on('change', async function() {
    const id = $(this).data('id');
    if (!id) return;
    const r = await API.conduite.update({ id_point_conduite: id, observation: this.value });
    if (!r.success) Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
  });
}

let sanctionsModal = null;

function getSanctionsModal() {
  if (!sanctionsModal) {
    sanctionsModal = new bootstrap.Modal(document.getElementById('sanctionsModal'));
  }
  return sanctionsModal;
}

async function refreshSanctions(id, nom) {
  const res = await API.conduite.sanctions('?id_point_conduite=' + id);
  if (!res.success) { Swal.fire({ icon: 'error', title: 'Erreur', text: res.message }); return; }
  document.getElementById('sanctionPointId').value = res.data.point.id_point_conduite;
  document.getElementById('sanctionEleve').textContent = nom;
  currentPointView = res.data;
  renderSanctions();
}

async function openSanctions(id, nom, idEtudiant) {
  let params;
  if (id) {
    params = '?id_point_conduite=' + id;
  } else {
    params = '?id_etudiant=' + idEtudiant
           + '&annee=' + document.getElementById('id_annee').value
           + '&periode=' + document.getElementById('id_periode').value;
  }
  const res = await API.conduite.sanctions(params);
  if (!res.success) { Swal.fire({ icon: 'error', title: 'Erreur', text: res.message }); return; }
  document.getElementById('sanctionPointId').value = res.data.point.id_point_conduite;
  document.getElementById('sanctionEleve').textContent = nom;
  currentPointView = res.data;
  renderSanctions();
  const modal = getSanctionsModal();
  modal.show();
  loadData();
}

function renderSanctions() {
  const d = currentPointView;
  const point = d.point;
  let rows = '';
  let solde = parseFloat(point.points_initial);
  const list = d.sanctions.slice().reverse();
  list.forEach(s => {
    solde -= s.points_retires;
    rows += `<tr>
      <td>${s.date_sanction}</td>
      <td>${s.motif || '-'}</td>
      <td class="text-center text-danger-600 fw-semibold">-${s.points_retires}</td>
      <td class="text-center fw-semibold">${solde.toFixed(2)}</td>
      <td class="text-center"><button class="btn btn-sm btn-outline-danger-600" onclick="deleteSanction('${s.uuid}')"><i class="ri-delete-bin-6-line"></i></button></td>
    </tr>`;
  });
  $('#sanctionsBody').html(rows || '<tr><td colspan="5" class="text-center text-secondary-light py-16">Aucune sanction enregistrée</td></tr>');
  $('#totalRetires').text(point.points_retires);
  $('#soldeActuel').text((parseFloat(point.points_initial) - parseFloat(point.points_retires)).toFixed(2));
}

async function addSanction() {
  const id = document.getElementById('sanctionPointId').value;
  const motif = document.getElementById('sMotif').value.trim();
  const pts = parseFloat(document.getElementById('sPoints').value);
  const date = document.getElementById('sDate').value;
  if (!id || !motif || !pts || pts <= 0) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Motif et points à retirer obligatoires' });
    return;
  }
  const r = await API.conduite.sanctionCreate({ id_point_conduite: id, motif: motif, points_retires: pts, date_sanction: date });
  if (r.success) {
    document.getElementById('sMotif').value = '';
    document.getElementById('sPoints').value = '5';
    Toast.fire({ icon: 'success', title: 'Sanction enregistrée' });
    await refreshSanctions(id, document.getElementById('sanctionEleve').textContent);
    loadData();
  } else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
}

async function deleteSanction(uuid) {
  const { value } = await Swal.fire({
    title: 'Supprimer cette sanction ?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Supprimer',
    cancelButtonText: 'Annuler'
  });
  if (!value) return;
  const r = await API.conduite.sanctionDelete(uuid);
  if (r.success) {
    Toast.fire({ icon: 'success', title: 'Sanction supprimée' });
    await refreshSanctions(document.getElementById('sanctionPointId').value, document.getElementById('sanctionEleve').textContent);
    loadData();
  } else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
}
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>