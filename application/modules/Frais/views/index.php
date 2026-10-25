<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Frais scolaires</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Frais</span>
      </div>
      <div class="text-secondary-light text-sm mt-4">Tous les frais sont conservés dans l'historique. La création utilise automatiquement l'année et le trimestre actifs.</div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-warning-600 d-flex align-items-center gap-6" onclick="openFraisSidebar()">
        <span class="d-flex text-md"><i class="ri-price-tag-3-line"></i></span> Nouveau frais
      </button>
    </div>
  </div>
  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0 dataTable-wrapper">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
          <div class="d-flex flex-wrap align-items-center gap-16">
            <form class="navbar-search dt-search m-0">
              <input type="text" id="dtSearch3" class="dt-input bg-transparent radius-4" aria-controls="fraisTable" name="search" placeholder="Rechercher...">
              <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
            </form>
          </div>
          <div class="d-flex align-items-center gap-8 text-secondary-light">
            <span>Lignes par page :</span>
            <div class="dt-length"><select id="dtLength3" name="fraisTable_length" aria-controls="fraisTable" class="dt-input form-control form-select">
              <option value="5">5</option>
              <option value="10" selected>10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select></div>
          </div>
        </div>
        <table class="table bordered-table mb-0 data-table" id="fraisTable" data-page-length='10' style="width:100%">
          <thead>
            <tr>
              <th scope="col"><div class="form-check style-check d-flex align-items-center"><input class="form-check-input" type="checkbox"><label class="form-check-label">S.L</label></div></th>
              <th>Type</th>
              <th>Classe</th>
              <th>Année</th>
              <th>Trimestre</th>
              <th>Montant</th>
              <th>Date limite</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="fraisBody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="fraisOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="fraisSidebar" style="width:50vw;max-width:50vw;box-shadow: -4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="fraisSidebarTitle">Ajouter un frais</h5>
    <button type="button" class="btn-close" onclick="closeFraisSidebar()"></button>
  </div>
  <form id="fraisForm" class="d-flex flex-column p-20">
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type de frais *</label>
      <select class="form-control form-select" id="fraisType">
        <option value="">-- Sélectionner --</option>
    <?php foreach ($types_frais as $t): ?>
    <option value="<?= $t['id_type_frais'] ?>"><?= htmlspecialchars($t['libelle']) ?></option>
    <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Enregistrement</label>
      <div class="border border-neutral-300 radius-8 p-12 text-sm" id="fraisContexte">
        Année active : <strong><?= htmlspecialchars($annee_active['libelle'] ?? '-') ?></strong>
        <span class="text-secondary-light mx-8">|</span>
        Trimestre actif : <strong><?= htmlspecialchars($periode_active['libelle'] ?? '-') ?></strong>
      </div>
      <div class="text-secondary-light text-xs mt-4">Le frais sera enregistré automatiquement dans l'année et le trimestre actifs.</div>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Section</label>
      <select class="form-control form-select" id="fraisSection">
        <option value="">-- Toutes les sections --</option>
        <?php foreach ($sections as $s): ?>
        <option value="<?= $s['id_section'] ?>"><?= htmlspecialchars($s['libelle']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3" id="fraisPorteeWrap">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classes concernées *</label>
      <select class="form-control form-select" id="fraisPortee">
        <option value="all">Toutes les classes</option>
        <option value="selected">Choisir les classes</option>
      </select>
      <div id="fraisClassesWrap" class="border border-neutral-300 radius-8 p-12 mt-8" style="display:none;max-height:220px;overflow-y:auto;">
        <?php foreach ($classes as $c): ?>
        <div class="form-check frais-classe-item" data-section="<?= $c['id_section'] ?? '' ?>">
          <input class="form-check-input frais-classe-check" type="checkbox" value="<?= $c['id_classe'] ?>" id="fraisClasseCheck<?= $c['id_classe'] ?>">
          <label class="form-check-label" for="fraisClasseCheck<?= $c['id_classe'] ?>"><?= htmlspecialchars($c['libelle']) ?></label>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="text-secondary-light text-xs mt-4" id="fraisPorteeHint">Le frais s'appliquera à tous les élèves inscrits, toutes classes confondues.</div>
    </div>
    <div class="mb-3" id="fraisClasseWrap" style="display:none;">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe *</label>
      <select class="form-control form-select" id="fraisClasse">
        <option value="all">Toutes les classes</option>
        <?php foreach ($classes as $c): ?>
        <option value="<?= $c['id_classe'] ?>" data-section="<?= $c['id_section'] ?? '' ?>"><?= htmlspecialchars($c['libelle']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="row gy-3 mb-3">
      <div class="col-sm-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Montant externe *</label>
        <input type="number" class="form-control" id="fraisMontant" step="0.01" min="0" placeholder="0.00">
      </div>
      <div class="col-sm-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Montant interne</label>
        <input type="number" class="form-control" id="fraisMontantInterne" step="0.01" min="0" placeholder="0.00">
      </div>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date limite indicative</label>
      <input type="date" class="form-control" id="fraisEcheance">
      <div class="text-secondary-light text-xs mt-4">Date par défaut utilisée pour l'échéancier ; ne crée pas de suivi individuel.</div>
    </div>
    <div class="col-12">
      <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
        <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeFraisSidebar()">Annuler</button>
        <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
      </div>
    </div>
  </form>
</div>

<script id="classes_data" type="application/json"><?= json_encode($classes) ?></script>
<script src="<?= base_url() ?>assets/js/api.js"></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
var BASE_URL = '<?= base_url() ?>';
var ANNEE_ACTIVE = <?= (int)$id_annee_active ?>;
var ANNEE_ACTIVE_LABEL = <?= json_encode($annee_active['libelle'] ?? '-') ?>;
var PERIODE_ACTIVE_LABEL = <?= json_encode($periode_active['libelle'] ?? '-') ?>;

function setFraisContexte(annee, periode) {
  document.getElementById('fraisContexte').textContent = 'Année : ' + annee + ' | Trimestre : ' + periode;
}

let fraisData = [];
let classesData = [];
try { const el = document.getElementById('classes_data'); if (el) classesData = JSON.parse(el.textContent); } catch(e) {}

function filterClassesBySection(sectionId) {
  const classSelect = document.getElementById('fraisClasse');
  classSelect.querySelectorAll('option').forEach(function(opt) {
    if (!opt.value || opt.value === 'all') return;
    opt.style.display = (!sectionId || String(opt.getAttribute('data-section')) === String(sectionId)) ? '' : 'none';
  });
  document.querySelectorAll('.frais-classe-item').forEach(function(item) {
    const visible = !sectionId || String(item.getAttribute('data-section')) === String(sectionId);
    item.style.display = visible ? '' : 'none';
    if (!visible) {
      const check = item.querySelector('.frais-classe-check');
      if (check) check.checked = false;
    }
  });
}

function setFraisPortee(mode) {
  document.getElementById('fraisPortee').value = mode;
  document.getElementById('fraisClassesWrap').style.display = mode === 'selected' ? '' : 'none';
  document.getElementById('fraisPorteeHint').textContent = mode === 'selected'
    ? 'Sélectionnez une ou plusieurs classes. Un frais sera créé pour chaque classe.'
    : 'Le frais s\'appliquera à tous les élèves inscrits, toutes classes confondues.';
}

let editingFraisUuid = null;

function openFraisSidebar() {
  editingFraisUuid = null;
  document.getElementById('fraisForm').reset();
  document.getElementById('fraisSidebarTitle').textContent = 'Ajouter un frais';
  filterClassesBySection('');
  document.querySelectorAll('.frais-classe-check').forEach(function(c) { c.checked = false; });
  setFraisPortee('all');
  document.getElementById('fraisPorteeWrap').style.display = '';
  document.getElementById('fraisClasseWrap').style.display = 'none';
  document.getElementById('fraisClasse').value = 'all';
  setFraisContexte(ANNEE_ACTIVE_LABEL, PERIODE_ACTIVE_LABEL);
  document.getElementById('fraisOverlay').classList.remove('visibility-hidden', 'opacity-0');
  document.getElementById('fraisSidebar').classList.remove('translate-x-full');
}

function editFraisSidebar(uuid) {
  const f = fraisData.find(function(x) { return x.uuid === uuid; });
  if (!f) return;
  editingFraisUuid = uuid;
  document.getElementById('fraisForm').reset();
  document.getElementById('fraisSidebarTitle').textContent = 'Modifier un frais';
  document.getElementById('fraisType').value = f.id_type_frais || '';
  var cls = classesData.find(function(c) { return String(c.id_classe) === String(f.id_classe); });
  if (cls) document.getElementById('fraisSection').value = cls.id_section || '';
  filterClassesBySection(cls ? cls.id_section : '');
  document.getElementById('fraisPorteeWrap').style.display = 'none';
  document.getElementById('fraisClasseWrap').style.display = '';
  document.getElementById('fraisClasse').value = f.id_classe ? f.id_classe : 'all';
  document.getElementById('fraisMontant').value = f.montant || '';
  document.getElementById('fraisMontantInterne').value = f.montant_interne || '';
  document.getElementById('fraisEcheance').value = f.echeance || '';
  setFraisContexte(f.annee_libelle || '-', f.periode_libelle || '-');
  document.getElementById('fraisOverlay').classList.remove('visibility-hidden', 'opacity-0');
  document.getElementById('fraisSidebar').classList.remove('translate-x-full');
}

function closeFraisSidebar() {
  document.getElementById('fraisOverlay').classList.add('visibility-hidden', 'opacity-0');
  document.getElementById('fraisSidebar').classList.add('translate-x-full');
}

async function loadFrais() {
  const res = await API.frais.list();
  if (!res.success) { $('#fraisBody').html('<tr><td colspan="8" class="text-center text-danger">Erreur</td></tr>'); return; }
  fraisData = res.data;
  let rows = '';
  fraisData.forEach((f, i) => {
    const montantInterne = Number(f.montant_interne || 0) > 0
      ? '<div class="text-xs text-secondary-light">Interne : ' + parseFloat(f.montant_interne).toLocaleString() + '</div>'
      : '';
    const actions = Number(f.id_annee) === Number(ANNEE_ACTIVE)
      ? `<div class="d-flex align-items-center gap-8">
          <button type="button" class="text-success-main text-xl" title="Générer les dettes" onclick="genererDettesFrais('${f.uuid}')"><i class="ri-file-list-3-line"></i></button>
          <button type="button" class="text-primary-light text-xl" onclick="editFraisSidebar('${f.uuid}')"><i class="ri-edit-2-line"></i></button>
          <button type="button" class="text-danger text-xl" onclick="deleteFrais('${f.uuid}')"><i class="ri-delete-bin-6-line"></i></button>
        </div>`
      : '<span class="text-secondary-light text-xl" title="Frais historique conservé"><i class="ri-lock-2-line"></i></span>';
    rows += `<tr>
      <td>${i + 1}</td>
      <td><span class="fw-semibold">${f.type_libelle || '-'}</span></td>
      <td>${f.classe_libelle || '<span class="text-primary-600 fw-semibold">Toutes les classes</span>'}</td>
      <td>${f.annee_libelle || '-'}</td>
      <td>${f.periode_libelle || '-'}</td>
      <td><strong>${parseFloat(f.montant || 0).toLocaleString()}</strong>${montantInterne}</td>
      <td>${f.echeance || '-'}</td>
      <td>${actions}</td>
    </tr>`;
  });
  if ($.fn.DataTable.isDataTable('#fraisTable')) $('#fraisTable').DataTable().destroy();
  $('#fraisBody').html(rows);
  $('#fraisTable').DataTable({
    pageLength: 10, scrollX: true,
    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucun frais trouvé', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

async function genererDettesFrais(uuid) {
  const f = fraisData.find(function (item) { return item.uuid === uuid; });
  if (!f) return;
  const c = await Swal.fire({
    icon: 'question',
    title: 'Générer les dettes',
    text: 'Type : ' + (f.type_libelle || '-') +
      '\nClasse : ' + (f.classe_libelle || 'Toutes les classes') +
      '\nAnnée : ' + (f.annee_libelle || '-') +
      '\nTrimestre : ' + (f.periode_libelle || '-') +
      '\nMontant : ' + parseFloat(f.montant || 0).toLocaleString(),
    showCancelButton: true,
    confirmButtonText: 'Générer les dettes',
    cancelButtonText: 'Annuler'
  });
  if (!c.isConfirmed) return;
  const r = await API.post('api/minervale/dettes/generer', { id_frais: f.id_frais });
  if (!r.success) { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); return; }
  const s = r.data || {};
  if (!s.etudiants) {
    Swal.fire({ icon: 'info', title: 'Aucun étudiant', text: 'Aucun étudiant inscrit dans cette classe pour cette année.' });
    return;
  }
  Toast.fire({ icon: 'success', title: (s.created || 0) + ' créée(s), ' + (s.updated || 0) + ' mise(s) à jour, ' + (s.skipped || 0) + ' ignorée(s)' });
}

async function deleteFrais(uuid) {
  const c = await Swal.fire({ icon: 'warning', title: 'Supprimer', text: 'Supprimer ce frais ?', showCancelButton: true, confirmButtonText: 'Supprimer', cancelButtonText: 'Annuler' });
  if (!c.isConfirmed) return;
  const r = await API.frais.delete(uuid);
  if (r.success) { await loadFrais(); Toast.fire({ icon: 'success', title: 'Frais supprimé' }); }
  else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
}

async function saveFrais(e) {
  e.preventDefault();
  const data = {
    id_type_frais: document.getElementById('fraisType').value,
    montant: document.getElementById('fraisMontant').value,
    montant_interne: document.getElementById('fraisMontantInterne').value || null,
    echeance: document.getElementById('fraisEcheance').value || null
  };
  if (editingFraisUuid) {
    const classe = document.getElementById('fraisClasse').value;
    data.id_classe = classe && classe !== 'all' ? classe : null;
  } else if (document.getElementById('fraisPortee').value === 'selected') {
    const ids = Array.from(document.querySelectorAll('.frais-classe-check:checked')).map(function(c) { return c.value; });
    if (!ids.length) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Sélectionnez au moins une classe' }); return; }
    data.id_classes = ids;
  } else {
    data.id_classe = null;
  }
  if (!data.id_type_frais || !data.montant) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Type et montant obligatoires' }); return; }
  const r = editingFraisUuid ? await API.frais.update(editingFraisUuid, data) : await API.frais.create(data);
  if (r.success) {
    closeFraisSidebar();
    await loadFrais();
    Toast.fire({ icon: 'success', title: editingFraisUuid ? 'Frais mis à jour' : 'Frais créé' });
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
}

(function() {
  var _f = function() {
    if (typeof $ === 'undefined' || typeof API === 'undefined') { setTimeout(_f, 50); return; }
    loadFrais();
    $('#dtSearch3').on('keyup', function() { $('#fraisTable').DataTable().search(this.value).draw(); });
    $('#dtLength3').on('change', function() { $('#fraisTable').DataTable().page.len(+this.value).draw(); });
    document.getElementById('fraisOverlay').addEventListener('click', closeFraisSidebar);
    document.getElementById('fraisForm').addEventListener('submit', saveFrais);
    document.getElementById('fraisPortee').addEventListener('change', function() {
      setFraisPortee(this.value);
    });
    document.getElementById('fraisSection').addEventListener('change', function() {
      filterClassesBySection(this.value);
      document.getElementById('fraisClasse').value = 'all';
    });

  };
  _f();
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
