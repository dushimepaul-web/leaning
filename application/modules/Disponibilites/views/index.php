<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Disponibilités des enseignants</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Disponibilités</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
      Ajouter une disponibilité
    </button>
  </div>
  <div class="mt-24">
    <div class="alert alert-info mb-16">
      <strong>Règle de génération :</strong> si un enseignant possède au moins un créneau marqué
      <em>disponible</em>, seuls ses créneaux marqués disponibles peuvent être utilisés. Sans créneau
      disponible explicite, tous les créneaux restent ouverts sauf ceux marqués <em>indisponible</em>.
    </div>
    <div class="card h-100">
      <div class="card-body p-0 dataTable-wrapper">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
          <div class="d-flex flex-wrap align-items-center gap-16">
            <div class="dropdown">
              <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown">
                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm"><i class="ri-file-upload-line text-md line-height-1"></i> Export</span>
                <span><i class="ri-arrow-down-s-line"></i></span>
              </button>
              <ul class="dropdown-menu p-12 border bg-base shadow">
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export PDF',text:'Fonctionnalité à venir'})"><i class="ri-file-3-line"></i> PDF</button></li>
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export Excel',text:'Fonctionnalité à venir'})"><i class="ri-file-excel-line"></i> Excel</button></li>
                <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="exportCSV()"><i class="ri-file-excel-line"></i> CSV</button></li>
              </ul>
            </div>
            <form class="navbar-search dt-search m-0">
              <input type="text" id="dtSearch" class="dt-input bg-transparent radius-4" aria-controls="dataTable" name="search" placeholder="Rechercher...">
              <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
            </form>
          </div>
          <div class="d-flex align-items-center gap-8 text-secondary-light">
            <div class="dropdown">
              <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">Filtrer</span>
                <span class=""><i class="ri-arrow-down-s-line"></i></span>
              </button>
              <div class="dropdown-menu border bg-base shadow dropdown-menu-lg p-0">
                <div class="d-flex align-items-center justify-content-between border-bottom py-8 px-16">
                  <span class="fw-semibold text-lg text-primary-light">Filtre</span>
                </div>
                <form class="p-16 d-grid grid-cols-2 gap-16">
                  <div>
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Statut</label>
                    <select id="filterStatut" class="form-control form-select">
                      <option value="">Tous</option>
                      <option value="actif">Actif</option>
                      <option value="inactif">Inactif</option>
                    </select>
                  </div>
                  <div>
                    <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Recherche</label>
                    <input type="text" id="filterSearch" class="form-control" placeholder="Mot-clé...">
                  </div>
                  <div><button type="reset" class="btn btn-danger-200 text-danger-600 w-100" onclick="resetFilters()">Réinitialiser</button></div>
                  <div><button type="button" class="btn btn-primary-600 w-100" onclick="applyFilters()">Appliquer</button></div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <table class="table bordered-table mb-0" id="dataTable" style="width:100%">
          <thead>
            <tr>
              <th class="text-center" style="width:5%">#</th>
              <th>Enseignant</th>
              <th>Jour</th>
              <th>Créneau</th>
              <th>Type</th>
              <th>Actions</th>
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
    <h5 class="text-lg mb-0" id="sidebarTitle">Ajouter une disponibilité</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="mainForm" class="d-flex flex-column p-20">
    <input type="hidden" id="recordId">
    <div class="row g-3">
      <div class="col-12 position-relative">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Enseignant *</label>
        <input type="hidden" id="id_enseignant">
        <input type="text" class="form-control" id="id_enseignant_search" placeholder="Rechercher..." autocomplete="off">
        <div id="id_enseignant_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
      </div>
      <div class="col-md-6 position-relative">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Jour *</label>
        <input type="hidden" id="id_jour">
        <input type="text" class="form-control" id="id_jour_search" placeholder="Rechercher..." autocomplete="off">
        <div id="id_jour_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
      </div>
      <div class="col-md-6 position-relative">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Créneau *</label>
        <input type="hidden" id="id_creneau">
        <input type="text" class="form-control" id="id_creneau_search" placeholder="Rechercher..." autocomplete="off">
        <div id="id_creneau_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
      </div>
      <div class="col-12">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="all_creneaux" onchange="toggleAllCreneaux()">
          <label class="form-check-label text-sm fw-semibold text-primary-light" for="all_creneaux">
            Tous les créneaux de cette journée
          </label>
        </div>
        <small class="text-secondary-light">Si coché, créera une entrée pour chaque créneau du jour sélectionné</small>
      </div>
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type</label>
        <select class="form-control form-select" id="type">
          <option value="disponible">Disponible</option>
          <option value="indisponible">Indisponible</option>
        </select>
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

<div class="modal fade" id="deleteModal" tabindex="-1">
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

<script src="<?= base_url() ?>assets/js/autocomplete.js?v=<?= filemtime(FCPATH.'assets/js/autocomplete.js') ?>"></script>
<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script id="disponibilites_enseignants_data" type="application/json"><?= json_encode($enseignants) ?></script>
<script id="disponibilites_jours_data" type="application/json"><?= json_encode($jours) ?></script>
<script id="disponibilites_creneaux_data" type="application/json"><?= json_encode($creneaux) ?></script>
<script>


function applyFilters() {
  const q = (document.getElementById('filterSearch')?.value || '').toLowerCase();
  const statutVal = document.getElementById('filterStatut')?.value || '';
  $('#dataTable tbody tr').each(function() {
    const text = $(this).text().toLowerCase();
    const matchSearch = !q || text.indexOf(q) > -1;
    let matchStatut = true;
    if (statutVal === 'actif') matchStatut = text.indexOf('disponible') > -1 && text.indexOf('indisponible') === -1;
    else if (statutVal === 'inactif') matchStatut = text.indexOf('indisponible') > -1;
    $(this).toggle(matchSearch && matchStatut);
  });
}

function resetFilters() {
  if (document.getElementById('filterSearch')) document.getElementById('filterSearch').value = '';
  if (document.getElementById('filterStatut')) document.getElementById('filterStatut').value = '';
  $('#dataTable tbody tr').show();
}
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let editingId = null;
let deleteId = null;

async function loadData() {
  const res = await API.disponibilites.list();
  if (!res.success) { $('#dataBody').html('<tr><td colspan="6" class="text-center text-danger">Erreur</td></tr>'); return; }
  let rows = '';
  res.data.forEach((d, i) => {
    const typeBadge = d.type === 'disponible' ? 'bg-success-100 text-success-600' : 'bg-danger-100 text-danger-600';
    rows += `<tr>
      <td>${i + 1}</td>
      <td><span class="fw-semibold">${d.enseignant || '-'}</span></td>
      <td>${d.jour || '-'}</td>
      <td>${d.creneau || '-'}</td>
      <td><span class="${typeBadge} px-24 py-4 radius-4 fw-medium text-sm text-capitalize">${d.type}</span></td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editRecord('${d.uuid}')"><i class="ri-edit-2-line"></i> Modifier</button></li>
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="confirmDelete('${d.uuid}')"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li>
          </ul>
        </div>
      </td>
    </tr>`;
  });
  $('#dataBody').html(rows);
}

function openAddSidebar() {
  editingId = null;
  document.getElementById('sidebarTitle').textContent = 'Ajouter une disponibilité';
  document.getElementById('mainForm').reset();
  document.getElementById('recordId').value = '';
  document.getElementById('all_creneaux').checked = false;
  document.getElementById('id_creneau').value = '';
  document.getElementById('id_creneau_search').value = '';
  document.getElementById('id_creneau').closest('.col-md-6').style.display = '';
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
}

function openEditSidebar(data) {
  editingId = data.uuid;
  document.getElementById('sidebarTitle').textContent = 'Modifier la disponibilité';
  document.getElementById('recordId').value = data.uuid;
  document.getElementById('id_enseignant').value = data.id_enseignant || '';
  document.getElementById('id_enseignant_search').value = data.enseignant || '';
  document.getElementById('id_jour').value = data.id_jour || '';
  document.getElementById('id_jour_search').value = data.jour || '';
  document.getElementById('id_creneau').value = data.id_creneau || '';
  document.getElementById('id_creneau_search').value = data.creneau || '';
  document.getElementById('type').value = data.type || 'disponible';
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
}

function closeSidebar() {
  document.getElementById('addSidebar').classList.remove('active');
  document.getElementById('sidebarOverlay').classList.remove('active');
}

async function editRecord(id) {
  const res = await API.disponibilites.list();
  if (res.success) {
    const item = res.data.find(d => d.uuid == id);
    if (item) openEditSidebar(item);
  }
}

function toggleAllCreneaux() {
  const checked = document.getElementById('all_creneaux').checked;
  const creneauField = document.getElementById('id_creneau').closest('.col-md-6');
  creneauField.style.display = checked ? 'none' : '';
  if (checked) {
    document.getElementById('id_creneau').value = '';
    document.getElementById('id_creneau_search').value = '';
  }
}

document.getElementById('mainForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id_enseignant = parseInt(document.getElementById('id_enseignant').value) || 0;
  const id_jour = parseInt(document.getElementById('id_jour').value) || 0;
  const id_creneau = parseInt(document.getElementById('id_creneau').value) || 0;
  const type = document.getElementById('type').value;
  const allCreneaux = document.getElementById('all_creneaux').checked;

  if (!id_enseignant || !id_jour) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Veuillez sélectionner un enseignant et un jour' });
    return;
  }
  if (!allCreneaux && !id_creneau) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Veuillez sélectionner un créneau ou cocher "Tous les créneaux"' });
    return;
  }

  let r;
  if (allCreneaux) {
    r = await API.disponibilites.bulk({ id_enseignant, id_jour, type });
  } else {
    const data = { id_enseignant, id_jour, id_creneau, type };
    if (editingId) {
      r = await API.disponibilites.update(editingId, data);
    } else {
      r = await API.disponibilites.create(data);
    }
  }

  if (r && r.success) {
    closeSidebar();
    Toast.fire({ icon: 'success', title: r.message || (editingId ? 'Disponibilité modifiée' : 'Disponibilité créée') });
    editingId = null;
    await loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r ? r.message : 'Erreur inconnue' }); }
});

function confirmDelete(id) {
  deleteId = id;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
  if (!deleteId) return;
  const r = await API.disponibilites.delete(deleteId);
  if (r && r.success) {
    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
    Toast.fire({ icon: 'success', title: 'Disponibilité supprimée' });
    await loadData();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r ? r.message : 'Erreur inconnue' }); }
  deleteId = null;
});

function exportCSV() {
  const headers = ['#', 'Enseignant', 'Jour', 'Créneau', 'Type'];
  let csv = '\uFEFF' + headers.join(',') + '\n';
  $('#dataTable tbody tr:visible').each(function() {
    const cols = [];
    $(this).find('td').each(function(i) {
      if (i < 5) {
        let val = $(this).text().trim();
        val = '"' + val.replace(/"/g, '""') + '"';
        cols.push(val);
      }
    });
    if (cols.length) csv += cols.join(',') + '\n';
  });
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'disponibilites.csv';
  link.click();
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && $.fn && typeof API !== 'undefined' && API.disponibilites) {
      clearInterval(wait);
      loadData();
      autoSetup('id_enseignant_search', 'id_enseignant', 'id_enseignant_results', JSON.parse(document.getElementById('disponibilites_enseignants_data').textContent).map(function(e) { return { id: e.id_enseignant, nom: e.fullname || e.nom, prenom: e.prenom || '' }; }), function(e) { return e.nom + (e.prenom ? ' ' + e.prenom : ''); });
      autoSetup('id_jour_search', 'id_jour', 'id_jour_results', JSON.parse(document.getElementById('disponibilites_jours_data').textContent).map(function(j) { return { id: j.id_jour, libelle: j.libelle }; }), function(j) { return j.libelle; });
      autoSetup('id_creneau_search', 'id_creneau', 'id_creneau_results', JSON.parse(document.getElementById('disponibilites_creneaux_data').textContent).map(function(c) { return { id: c.id_creneau, libelle: c.libelle, heure_debut: c.heure_debut, heure_fin: c.heure_fin }; }), function(c) { return c.libelle + ' (' + c.heure_debut + '-' + c.heure_fin + ')'; });
      $('#dtSearch').on('keyup', function() { applyFilters(); });
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
