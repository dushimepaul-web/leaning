<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Sauvegardes</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Administration / Sauvegardes</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="createBackup()" id="btnBackup">
      <span class="d-flex text-md"><i class="ri-database-2-line"></i></span>
      Créer une sauvegarde
    </button>
  </div>

  <div id="msg" class="alert d-none"></div>

  <div class="row gy-4">
    <div class="col-12">
      <div class="card h-100">
        <div class="card-body p-0">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
            <form class="navbar-search dt-search m-0">
              <input type="text" id="dtSearch" class="dt-input bg-transparent radius-4" placeholder="Rechercher...">
              <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
            </form>
            <div class="d-flex align-items-center gap-8 text-secondary-light">
              <span>Lignes par page :</span>
              <div class="dt-length"><select id="dtLength" class="dt-input form-control form-select">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select></div>
            </div>
          </div>
          <table class="table bordered-table mb-0" id="dataTable" style="width:100%">
            <thead>
              <tr>
                <th>Fichier</th>
                <th>Taille</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="dataBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-body pt-32 px-36 pb-24 text-center">
        <span class="mb-16 fs-1 line-height-1 text-danger d-block"><i class="ri-delete-bin-6-line"></i></span>
        <h6 class="text-lg fw-semibold text-primary-light mb-0">Supprimer cette sauvegarde ?</h6>
        <p class="text-sm text-secondary-light mt-8">Cette action est irréversible.</p>
        <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
          <button type="button" class="flex-grow-1 border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-24 py-11 radius-8" data-bs-dismiss="modal">Annuler</button>
          <button type="button" id="confirmDeleteBtn" class="flex-grow-1 btn btn-danger text-md px-16 py-12 radius-8">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js"></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let deleteFile = null;

async function createBackup() {
  const btn = document.getElementById('btnBackup');
  btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sauvegarde...';
  const r = await API.request('POST', 'api/sauvegardes/create');
  btn.disabled = false; btn.innerHTML = '<i class="ri-database-2-line me-1"></i>Créer une sauvegarde';
  const msg = document.getElementById('msg');
  if (r.success) {
    msg.className = 'alert alert-success'; msg.innerHTML = '<i class="ri-check-line me-1"></i> Sauvegarde créée : ' + r.data.file + ' (' + (r.data.size / 1024).toFixed(1) + ' Ko)';
    msg.classList.remove('d-none'); setTimeout(() => msg.classList.add('d-none'), 5000);
    loadData();
  } else {
    msg.className = 'alert alert-danger'; msg.innerHTML = '<i class="ri-close-circle-line me-1"></i> ' + (r.message || 'Erreur');
    msg.classList.remove('d-none');
  }
}

async function loadData() {
  const res = await API.request('GET', 'api/sauvegardes/list');
  if (!res.success) { $('#dataBody').html('<tr><td colspan="4" class="text-center text-danger">Erreur</td></tr>'); return; }
  let rows = '';
  res.data.forEach(f => {
    const size = f.size > 1048576 ? (f.size / 1048576).toFixed(2) + ' Mo' : (f.size / 1024).toFixed(1) + ' Ko';
    rows += `<tr>
      <td><span class="fw-semibold">${f.file}</span></td>
      <td>${size}</td>
      <td>${f.date}</td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><a class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" href="<?= base_url('api/sauvegardes/download/') ?>${f.file}"><i class="ri-download-2-line"></i> Télécharger</a></li>
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="confirmDelete('${f.file}')"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li>
          </ul>
        </div>
      </td>
    </tr>`;
  });
  $('#dataBody').html(rows);
  if ($.fn.DataTable.isDataTable('#dataTable')) $('#dataTable').DataTable().destroy();
  $('#dataTable').DataTable({
    pageLength: 10, scrollX: true, order: [[2, 'desc']],
    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucune sauvegarde', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

function confirmDelete(file) {
  deleteFile = file;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
  if (!deleteFile) return;
  const r = await API.request('GET', 'api/sauvegardes/delete/' + deleteFile);
  if (r.success) {
    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
    Toast.fire({ icon: 'success', title: 'Sauvegarde supprimée' }); loadData();
  }
  deleteFile = null;
});

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && $.fn && $.fn.DataTable) {
      clearInterval(wait);
      loadData();
      $('#dtSearch').on('keyup', function() { $('#dataTable').DataTable().search(this.value).draw(); });
      $('#dtLength').on('change', function() { $('#dataTable').DataTable().page.len(+this.value).draw(); });
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
