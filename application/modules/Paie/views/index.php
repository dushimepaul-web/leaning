<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion de la Paie</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Paie</span>
      </div>
    </div>
  </div>
  <ul class="nav nav-tabs mb-24">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#contrats">Contrats</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#bulletins">Bulletins</button></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="contrats">
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
                  <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="exportContratsCSV()"><i class="ri-file-excel-line"></i> CSV</button></li>
                </ul>
              </div>
              <form class="navbar-search dt-search m-0">
                <input type="text" id="dtSearchContrats" class="dt-input bg-transparent radius-4" aria-controls="contratsTable" name="search" placeholder="Rechercher...">
                <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
              </form>
            </div>
            <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="showContratModal()">
              <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
              Nouveau contrat
            </button>
          </div>
            <table class="table bordered-table mb-0 data-table" id="contratsTable" data-page-length='10' style="width:100%">
            <thead>
              <tr>
                <th scope="col"><div class="form-check style-check d-flex align-items-center"><input class="form-check-input" type="checkbox"><label class="form-check-label">S.L</label></div></th>
                <th>Employé</th>
                <th>Type</th>
                <th>Salaire de base</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody id="contratsBody"></tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="bulletins">
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
                  <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="exportBulletinsCSV()"><i class="ri-file-excel-line"></i> CSV</button></li>
                </ul>
              </div>
              <form class="navbar-search dt-search m-0">
                <input type="text" id="dtSearchBulletins" class="dt-input bg-transparent radius-4" aria-controls="bulletinsTable" name="search" placeholder="Rechercher...">
                <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
              </form>
            </div>
            <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="showBulletinModal()">
              <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
              Nouveau bulletin
            </button>
          </div>
            <table class="table bordered-table mb-0 data-table" id="bulletinsTable" data-page-length='10' style="width:100%">
            <thead>
              <tr>
                <th scope="col"><div class="form-check style-check d-flex align-items-center"><input class="form-check-input" type="checkbox"><label class="form-check-label">S.L</label></div></th>
                <th>Employé</th>
                <th>Période</th>
                <th>Salaire base</th>
                <th>Gains</th>
                <th>Retenues</th>
                <th>Net à payer</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody id="bulletinsBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="contratOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="contratSidebar" style="width:50vw;max-width:50vw;box-shadow: -4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="contratTitle">Nouveau contrat</h5>
    <button type="button" class="btn-close" onclick="closeContrat()"></button>
  </div>
  <form id="contratForm" class="d-flex flex-column p-20">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Employé *</label>
        <select class="form-control form-select" id="contrat_id_employe">
          <option value="">Sélectionner</option>
          <?php foreach ($employes as $e): ?>
            <option value="<?= $e['id_employe'] ?>"><?= $e['nom_complet'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type</label>
        <select class="form-control form-select" id="contrat_type_employe">
          <option value="cdi">CDI</option>
          <option value="cdd">CDD</option>
          <option value="stage">Stage</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Salaire de base *</label>
        <input type="number" class="form-control" id="contrat_salaire_base" step="0.01" placeholder="0.00">
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Statut</label>
        <select class="form-control form-select" id="contrat_statut">
          <option value="actif">Actif</option>
          <option value="termine">Terminé</option>
          <option value="suspendu">Suspendu</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date début *</label>
        <input type="date" class="form-control" id="contrat_date_debut">
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date fin</label>
        <input type="date" class="form-control" id="contrat_date_fin">
      </div>
    </div>
    <div class="col-12">
      <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
        <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeContrat()">Annuler</button>
        <button type="button" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8" onclick="saveContrat()">Enregistrer</button>
      </div>
    </div>
  </form>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="bulletinOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="bulletinSidebar" style="width:50vw;max-width:50vw;box-shadow: -4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="bulletinTitle">Nouveau bulletin</h5>
    <button type="button" class="btn-close" onclick="closeBulletin()"></button>
  </div>
  <form id="bulletinForm" class="d-flex flex-column p-20">
    <div class="row g-3">
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Contrat *</label>
        <select class="form-control form-select" id="bulletin_id_contrat">
          <option value="">Sélectionner</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Mois</label>
        <select class="form-control form-select" id="bulletin_mois">
          <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $m == date('m') ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Année</label>
        <input type="number" class="form-control" id="bulletin_annee" value="<?= date('Y') ?>">
      </div>
    </div>
    <div class="col-12">
      <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
        <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeBulletin()">Annuler</button>
        <button type="button" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8" onclick="saveBulletin()">Enregistrer</button>
      </div>
    </div>
  </form>
</div>

<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let contrats = [];

async function loadContrats() {
  const res = await API.paie.contrats();
  if (!res.success) { $('#contratsBody').html('<tr><td colspan="7" class="text-center text-danger">Erreur</td></tr>'); return; }
  contrats = res.data;
  let rows = '';
  res.data.forEach((c, i) => {
    const statusBadge = c.statut === 'actif' ? 'bg-success-100 text-success-600' : (c.statut === 'termine' ? 'bg-secondary-100 text-secondary-600' : 'bg-warning-100 text-warning-600');
    rows += `<tr>
      <td>${i + 1}</td>
      <td><span class="fw-semibold">${c.nom_complet || '-'}</span> <small class="text-secondary-light">(${c.matricule || ''})</small></td>
      <td><span class="text-capitalize">${c.type_employe || '-'}</span></td>
      <td><strong>${parseFloat(c.salaire_base || 0).toLocaleString()}</strong></td>
      <td>${c.date_debut || '-'}</td>
      <td>${c.date_fin || '-'}</td>
      <td><span class="${statusBadge} px-24 py-4 radius-4 fw-medium text-sm text-capitalize">${c.statut}</span></td>
    </tr>`;
  });
  $('#contratsBody').html(rows);
  if ($.fn.DataTable.isDataTable('#contratsTable')) $('#contratsTable').DataTable().destroy();
  $('#contratsTable').DataTable({
    pageLength: 10, scrollX: true,
    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucun contrat trouvé', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

async function loadBulletins() {
  const res = await API.paie.bulletins();
  if (!res.success) { $('#bulletinsBody').html('<tr><td colspan="8" class="text-center text-danger">Erreur</td></tr>'); return; }
  let rows = '';
  res.data.forEach((b, i) => {
    const moisNom = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'][parseInt(b.mois) - 1] || b.mois;
    const statusBadge = b.statut === 'paye' ? 'bg-success-100 text-success-600' : (b.statut === 'valide' ? 'bg-info-100 text-info-600' : (b.statut === 'annule' ? 'bg-danger-100 text-danger-600' : 'bg-warning-100 text-warning-600'));
    rows += `<tr>
      <td>${i + 1}</td>
      <td><span class="fw-semibold">${b.nom_complet || '-'}</span></td>
      <td>${moisNom} ${b.annee}</td>
      <td>${parseFloat(b.salaire_base || 0).toLocaleString()}</td>
      <td>${parseFloat(b.total_gains || 0).toLocaleString()}</td>
      <td>${parseFloat(b.total_retenues || 0).toLocaleString()}</td>
      <td><strong>${parseFloat(b.net_a_payer || 0).toLocaleString()}</strong></td>
      <td><span class="${statusBadge} px-24 py-4 radius-4 fw-medium text-sm text-capitalize">${b.statut}</span></td>
    </tr>`;
  });
  $('#bulletinsBody').html(rows);
  if ($.fn.DataTable.isDataTable('#bulletinsTable')) $('#bulletinsTable').DataTable().destroy();
  $('#bulletinsTable').DataTable({
    pageLength: 10, scrollX: true,
    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucun bulletin trouvé', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

function closeContrat() {
  document.getElementById('contratSidebar').classList.remove('active');
  document.getElementById('contratOverlay').classList.remove('active');
}

function showContratModal() {
  document.getElementById('contratForm').reset();
  document.getElementById('contratSidebar').classList.add('active');
  document.getElementById('contratOverlay').classList.add('active');
}

async function saveContrat() {
  const data = {
    id_employe: document.getElementById('contrat_id_employe').value,
    type_employe: document.getElementById('contrat_type_employe').value,
    salaire_base: document.getElementById('contrat_salaire_base').value || 0,
    date_debut: document.getElementById('contrat_date_debut').value,
    date_fin: document.getElementById('contrat_date_fin').value || null,
    statut: document.getElementById('contrat_statut').value
  };
  if (!data.id_employe || !data.date_debut || !data.salaire_base) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Employé, salaire de base et date début obligatoires' });
    return;
  }
  const r = await API.paie.createContrat(data);
  if (r.success) {
    closeContrat();
    Toast.fire({ icon: 'success', title: 'Contrat créé' });
    loadContrats();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
}

function closeBulletin() {
  document.getElementById('bulletinSidebar').classList.remove('active');
  document.getElementById('bulletinOverlay').classList.remove('active');
}

function showBulletinModal() {
  document.getElementById('bulletinForm').reset();
  const select = document.getElementById('bulletin_id_contrat');
  select.innerHTML = '<option value="">Sélectionner</option>';
  contrats.filter(c => c.statut === 'actif').forEach(c => {
    select.innerHTML += `<option value="${c.id_contrat}">${c.nom_complet} - ${c.matricule || ''}</option>`;
  });
  document.getElementById('bulletinSidebar').classList.add('active');
  document.getElementById('bulletinOverlay').classList.add('active');
}

async function saveBulletin() {
  const data = {
    id_contrat: document.getElementById('bulletin_id_contrat').value,
    mois: document.getElementById('bulletin_mois').value,
    annee: document.getElementById('bulletin_annee').value
  };
  if (!data.id_contrat) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Contrat obligatoire' }); return; }
  const r = await API.paie.createBulletin(data);
  if (r.success) {
    closeBulletin();
    Toast.fire({ icon: 'success', title: 'Bulletin créé' });
    loadBulletins();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
}

function exportContratsCSV() {
  const table = $('#contratsTable').DataTable();
  const data = table.rows({ filter: 'applied' }).data();
  let csv = '\uFEFF';
  const headers = ['#', 'Employé', 'Type', 'Salaire de base', 'Début', 'Fin', 'Statut'];
  csv += headers.join(',') + '\n';
  data.each(function(row) {
    const cols = [];
    for (let i = 0; i < 7; i++) {
      let val = $(row[i]).text().trim() || row[i] || '';
      val = '"' + val.replace(/"/g, '""') + '"';
      cols.push(val);
    }
    csv += cols.join(',') + '\n';
  });
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'contrats.csv';
  link.click();
}

function exportBulletinsCSV() {
  const table = $('#bulletinsTable').DataTable();
  const data = table.rows({ filter: 'applied' }).data();
  let csv = '\uFEFF';
  const headers = ['#', 'Employé', 'Période', 'Salaire base', 'Gains', 'Retenues', 'Net à payer', 'Statut'];
  csv += headers.join(',') + '\n';
  data.each(function(row) {
    const cols = [];
    for (let i = 0; i < 8; i++) {
      let val = $(row[i]).text().trim() || row[i] || '';
      val = '"' + val.replace(/"/g, '""') + '"';
      cols.push(val);
    }
    csv += cols.join(',') + '\n';
  });
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'bulletins.csv';
  link.click();
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && $.fn && $.fn.DataTable && typeof API !== 'undefined') {
      clearInterval(wait);
      loadContrats();
      loadBulletins();
      $('#dtSearchContrats').on('keyup', function() { $('#contratsTable').DataTable().search(this.value).draw(); });
      $('#dtSearchBulletins').on('keyup', function() { $('#bulletinsTable').DataTable().search(this.value).draw(); });
      document.getElementById('contratOverlay').addEventListener('click', closeContrat);
      document.getElementById('bulletinOverlay').addEventListener('click', closeBulletin);
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
