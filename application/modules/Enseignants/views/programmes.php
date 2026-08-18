<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Programmes (Matières / Classes)</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Programmes</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
      Ajouter un programme
    </button>
  </div>

  <div class="mt-24">
    <div class="card h-100 radius-12 border-0 shadow-sm overflow-hidden">
      <div class="card-body p-0 table-responsive" style="overflow-x: auto; white-space: nowrap;">
        <style>
          #dataTable tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.04) !important;
            transition: background-color 0.2s ease;
          }
          #dataTable th {
            background-color: #f8f9fa !important;
            color: #212529 !important;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
          }
          #dataTable td {
            border-color: #dee2e6;
            vertical-align: middle;
            padding: 12px 16px;
          }
        </style>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
          <div class="d-flex flex-wrap align-items-center gap-16">
            <div class="dropdown">
              <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">
                  <i class="ri-file-upload-line text-md line-height-1"></i>
                  Export
                </span>
                <span><i class="ri-arrow-down-s-line"></i></span>
              </button>
              <ul class="dropdown-menu p-12 border bg-base shadow">
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export PDF',text:'Fonctionnalité à venir'})"><i class="ri-file-3-line"></i> PDF</button></li>
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export Excel',text:'Fonctionnalité à venir'})"><i class="ri-file-excel-line"></i> Excel</button></li>
              </ul>
            </div>
            <form class="navbar-search dt-search m-0">
              <input type="text" id="dtSearch" class="dt-input bg-transparent radius-4" placeholder="Rechercher...">
              <i class="ri-search-line icon"></i>
            </form>
          </div>
          <div class="d-flex align-items-center gap-8 text-secondary-light">
            <span>Lignes par page :</span>
            <select id="dtLength" class="form-control form-select" style="width:auto;padding:0.375rem 2rem 0.375rem 0.75rem;">
              <option value="5">5</option>
              <option value="10" selected>10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>
        <table class="table mb-0" id="dataTable" style="width:100%; border-collapse: collapse; border: 1px solid #ced4da;">
          <thead style="background-color: #e9ecef;">
            <tr>
              <th class="text-center" style="width: 15%; padding: 12px 16px; border: 1px solid #ced4da; color: #212529; font-weight: 600;">CLASSE</th>
              <th class="text-start" style="width: 28%; padding: 12px 16px; border: 1px solid #ced4da; color: #212529; font-weight: 600;">COURS</th>
              <th class="text-center" style="width: 10%; padding: 12px 16px; border: 1px solid #ced4da; color: #212529; font-weight: 600;">Coeff.</th>
              <th class="text-center" style="width: 10%; padding: 12px 16px; border: 1px solid #ced4da; color: #212529; font-weight: 600;">Nb h / jour</th>
              <th class="text-center" style="width: 12%; padding: 12px 16px; border: 1px solid #ced4da; color: #212529; font-weight: 600;">Nb h / sem.</th>
              <th class="text-center" style="width: 17%; padding: 12px 16px; border: 1px solid #ced4da; color: #212529; font-weight: 600;">Enseignant</th>
              <th class="text-center" style="width: 8%; padding: 12px 16px; border: 1px solid #ced4da; color: #212529; font-weight: 600;">Actions</th>
            </tr>
          </thead>
          <tbody id="dataBody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="sidebarOverlay"></div>

<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="addSidebar" style="width:50vw;max-width:50vw;box-shadow: -4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="sidebarTitle">Ajouter un programme</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="mainForm" class="d-flex flex-column p-20">
    <input type="hidden" id="recordId">
    <input type="hidden" id="id_matiere">
    <input type="hidden" id="id_classe">
    <input type="hidden" id="id_enseignant">
    <input type="hidden" id="id_section">
    <div class="row g-3">
      <div class="col-sm-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Matière *</label>
        <input type="text" class="form-control" id="matiereSearch" placeholder="Rechercher une matière...">
        <div class="autocomplete-results" id="matiereResults"></div>
      </div>
      <div class="col-sm-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Section</label>
        <input type="text" class="form-control" id="sectionSearch" placeholder="Rechercher une section...">
        <div class="autocomplete-results" id="sectionResults"></div>
      </div>
      <div class="col-sm-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe *</label>
        <input type="text" class="form-control" id="classeSearch" placeholder="Rechercher une classe...">
        <div class="autocomplete-results" id="classeResults"></div>
      </div>
      <div class="col-sm-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Coefficient</label>
        <input type="number" step="0.1" min="0" class="form-control" id="coefficient" placeholder="Ex: 1.0">
      </div>
      <div class="col-sm-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Nb heures / jour</label>
        <input type="number" step="0.5" min="0" class="form-control" id="nb_heures_par_jour" placeholder="Ex: 2.0">
      </div>
      <div class="col-sm-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Nb heures / semaine</label>
        <input type="number" step="0.5" min="0" class="form-control" id="nb_heures_par_semaine" placeholder="Ex: 10.0">
      </div>
      <div class="col-12">
        <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
          <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeSidebar()">Annuler</button>
          <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
        </div>
      </div>
    </div>
  </form>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
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

<script id="matieresData" type="application/json"><?= json_encode($matieres ?? []) ?></script>
<script id="classesData" type="application/json"><?= json_encode($classes ?? []) ?></script>
<script id="sectionsData" type="application/json"><?= json_encode($sections ?? []) ?></script>
<script src="<?= base_url() ?>assets/js/api.js"></script>
<script src="<?= base_url() ?>assets/js/autocomplete.js"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
let editingId = null;
let deleteId = null;

var allClasses = JSON.parse(document.getElementById('classesData').textContent);
const classItems = allClasses.map(function(c) { return {id: c.id_classe, libelle: c.libelle, code: c.code, id_section: c.id_section}; });
const matieresItems = JSON.parse(document.getElementById('matieresData').textContent).map(function(m) { return {id: m.id_matiere, libelle: m.libelle, code: m.code}; });
const allSections = JSON.parse(document.getElementById('sectionsData').textContent || '[]');

autoSetup('matiereSearch', 'id_matiere', 'matiereResults', matieresItems, function(m) { return m.libelle + ' (' + (m.code || '') + ')'; });
var classCtrl = autoSetup('classeSearch', 'id_classe', 'classeResults', classItems, function(c) { return c.libelle; });
autoSetup('sectionSearch', 'id_section', 'sectionResults', allSections.map(function(s) { return {id: s.id_section, libelle: s.libelle}; }), function(s) { return s.libelle; }, function(section) {
  if (!classCtrl) return;
  if (!section || !section.id) {
    classCtrl.updateItems(classItems);
  } else {
    var filtered = allClasses.filter(function(c) { return String(c.id_section) === String(section.id); });
    classCtrl.updateItems(filtered.map(function(c) { return {id: c.id_classe, libelle: c.libelle, id_section: c.id_section}; }));
  }
  document.getElementById('id_classe').value = '';
  document.getElementById('classeSearch').value = '';
  document.getElementById('classeSearch').classList.remove('border-success', 'border-2');
});

function openAddSidebar() {
  editingId = null;
  document.getElementById('sidebarTitle').textContent = 'Ajouter un programme';
  document.getElementById('recordId').value = '';
  document.getElementById('id_matiere').value = '';
  document.getElementById('id_classe').value = '';
  document.getElementById('id_section').value = '';
  document.getElementById('matiereSearch').value = '';
  document.getElementById('classeSearch').value = '';
  document.getElementById('sectionSearch').value = '';
  document.getElementById('coefficient').value = '';
  document.getElementById('nb_heures_par_jour').value = '';
  document.getElementById('nb_heures_par_semaine').value = '';
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
  if (classCtrl) classCtrl.updateItems(classItems);
}

function openEditSidebar(data) {
  editingId = data.uuid;
  document.getElementById('sidebarTitle').textContent = 'Modifier le programme';
  document.getElementById('recordId').value = data.uuid;
  document.getElementById('id_matiere').value = data.id_matiere;
  document.getElementById('id_classe').value = data.id_classe;
  document.getElementById('matiereSearch').value = data.matiere_libelle || '';
  document.getElementById('classeSearch').value = data.classe_libelle || '';
  document.getElementById('coefficient').value = data.coefficient || '';
  document.getElementById('nb_heures_par_jour').value = data.nb_heures_par_jour || '';
  document.getElementById('nb_heures_par_semaine').value = data.nb_heures_par_semaine || '';
  // Section from class
  var cls = allClasses.find(function(c) { return String(c.id_classe) === String(data.id_classe); });
  if (cls && cls.id_section) {
    document.getElementById('id_section').value = cls.id_section;
    var sec = allSections.find(function(s) { return String(s.id_section) === String(cls.id_section); });
    document.getElementById('sectionSearch').value = sec ? sec.libelle : '';
    if (classCtrl) {
      var filtered = allClasses.filter(function(c) { return String(c.id_section) === String(cls.id_section); });
      classCtrl.updateItems(filtered.map(function(c) { return {id: c.id_classe, libelle: c.libelle, id_section: c.id_section}; }));
    }
  } else {
    document.getElementById('id_section').value = '';
    document.getElementById('sectionSearch').value = '';
    if (classCtrl) classCtrl.updateItems(classItems);
  }
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
}

function closeSidebar() {
  document.getElementById('addSidebar').classList.remove('active');
  document.getElementById('sidebarOverlay').classList.remove('active');
}

async function loadData() {
  const res = await API.matieres_classes.list();
  if (!res.success) { $('#dataBody').html('<tr><td colspan="7" class="text-center text-danger">Erreur de chargement</td></tr>'); return; }
  
  // Regrouper par classe
  const grouped = {};
  res.data.forEach(function(s) {
    const cName = s.classe_libelle || 'Inconnue';
    if (!grouped[cName]) grouped[cName] = [];
    grouped[cName].push(s);
  });

  let rows = '';
  let index = 1;

  Object.keys(grouped).forEach(function(classe) {
    const items = grouped[classe];
    const rowSpan = items.length;

    items.forEach(function(s, idx) {
      rows += '<tr style="transition:0.2s;">';
      if (idx === 0) {
        rows += '<td rowspan="' + rowSpan + '" class="align-middle text-center fw-bold" style="font-size:0.95rem; vertical-align:middle; padding: 12px 16px; border: 1px solid #ced4da; background-color: #f1f3f5;">' + classe + '</td>';
      }
      rows += '<td class="align-middle text-start" style="padding: 12px 16px; border: 1px solid #ced4da; color: #212529;">' + (s.matiere_code || '-') + '</td>';
      rows += '<td class="align-middle text-center" style="padding: 12px 16px; border: 1px solid #ced4da; color: #212529;">' + (s.coefficient ?? '-') + '</td>';
      rows += '<td class="align-middle text-center" style="padding: 12px 16px; border: 1px solid #ced4da; color: #212529;">' + (s.nb_heures_par_jour ?? '0.0') + '</td>';
      rows += '<td class="align-middle text-center" style="padding: 12px 16px; border: 1px solid #ced4da; color: #212529;">' + (s.nb_heures_par_semaine ?? '0.0') + '</td>';
      rows += '<td class="align-middle text-center" style="padding: 12px 16px; border: 1px solid #ced4da; color: #212529;">' + (s.enseignant_fullname || '-') + '</td>';
      rows += '<td class="align-middle text-center" style="padding: 12px 16px; border: 1px solid #ced4da;"><div class="btn-group"><button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button><ul class="dropdown-menu dropdown-menu-lg-end border p-12"><li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editRecord(\'' + s.uuid + '\')"><i class="ri-edit-2-line"></i> Modifier</button></li><li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="confirmDelete(\'' + s.uuid + '\')"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li></ul></div></td>';
      rows += '</tr>';
    });
  });

  $('#dataBody').html(rows);
  // DataTables désactivé pour ce tableau groupé avec rowspan pour éviter les conflits d'indexation de colonnes.
}

async function editRecord(id) {
  const res = await API.matieres_classes.get(id);
  if (res.success) openEditSidebar(res.data);
}

document.getElementById('mainForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const data = {
    id_matiere: document.getElementById('id_matiere').value,
    id_classe: document.getElementById('id_classe').value,
    coefficient: document.getElementById('coefficient').value || null,
    nb_heures_par_jour: document.getElementById('nb_heures_par_jour').value || null,
    nb_heures_par_semaine: document.getElementById('nb_heures_par_semaine').value || null
  };
  if (!data.id_matiere || !data.id_classe) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Matière et classe sont obligatoires' });
    return;
  }
  let res;
  if (editingId) res = await API.matieres_classes.update(editingId, data);
  else res = await API.matieres_classes.create(data);
  if (res.success) {
    closeSidebar();
    loadData();
  } else {
    Swal.fire({ icon: 'error', title: 'Erreur', text: res.message });
  }
});

function confirmDelete(id) {
  deleteId = id;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
  if (!deleteId) return;
  const res = await API.matieres_classes.delete(deleteId);
  if (res.success) {
    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
    loadData();
  } else {
    Swal.fire({ icon: 'error', title: 'Erreur', text: res.message });
  }
  deleteId = null;
});

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && typeof API !== 'undefined') {
      clearInterval(wait);
      loadData();
      $('#dtSearch').on('keyup', function() {
        var q = this.value.toLowerCase();
        $('#dataTable tbody tr').each(function() {
          var text = $(this).text().toLowerCase();
          $(this).toggle(text.indexOf(q) > -1);
        });
      });
      $('#dtLength').parent().hide();
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>