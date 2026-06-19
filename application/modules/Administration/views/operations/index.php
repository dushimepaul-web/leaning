<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Opérations groupées</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Administration / Opérations groupées</span>
      </div>
    </div>
  </div>

  <ul class="nav nav-tabs mb-24" id="opsTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="export-tab" data-bs-toggle="tab" data-bs-target="#exportPane" type="button"><i class="ri-download-2-line me-1"></i>Export CSV</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="import-tab" data-bs-toggle="tab" data-bs-target="#importPane" type="button"><i class="ri-upload-2-line me-1"></i>Import CSV</button>
    </li>
  </ul>

  <div class="tab-content" id="opsTabContent">
    <!-- Export -->
    <div class="tab-pane fade show active" id="exportPane">
      <div class="card">
        <div class="card-body p-24">
          <div class="row g-3 align-items-end">
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Table à exporter</label>
              <select class="form-control" id="exportTable"><option value="">Chargement...</option></select>
            </div>
            <div class="col-md-6 d-flex gap-3">
              <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-2" onclick="exportCSV()"><i class="ri-download-2-line"></i> Exporter en CSV</button>
              <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-2" onclick="previewExport()"><i class="ri-eye-line"></i> Aperçu</button>
            </div>
          </div>
          <div id="exportPreview" class="mt-16 table-responsive d-none"></div>
        </div>
      </div>
    </div>

    <!-- Import -->
    <div class="tab-pane fade" id="importPane">
      <div class="card">
        <div class="card-body p-24">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Table de destination</label>
              <select class="form-control" id="importTable"><option value="">Chargement...</option></select>
            </div>
            <div class="col-md-6">
              <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Fichier CSV</label>
              <input type="file" class="form-control" id="importFile" accept=".csv">
            </div>
            <div class="col-12">
              <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-2" onclick="previewImport()"><i class="ri-eye-line"></i> Prévisualiser</button>
              <button type="button" class="btn btn-success d-flex align-items-center gap-2 ms-2" onclick="runImport()"><i class="ri-upload-2-line"></i> Importer</button>
            </div>
          </div>
          <div id="importPreview" class="mt-16 table-responsive d-none"></div>
          <div id="importResult" class="mt-16 d-none"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js"></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let importHeaders = [];

async function loadTables(selectId) {
  const r = await API.request('GET', 'api/operations/tables');
  const sel = document.getElementById(selectId);
  sel.innerHTML = '<option value="">Sélectionner une table</option>';
  if (r.success) r.data.forEach(t => {
    sel.innerHTML += '<option value="' + t.name + '">' + t.label + '</option>';
  });
}

async function exportCSV() {
  const table = document.getElementById('exportTable').value;
  if (!table) { Swal.fire({ icon: 'warning', title: 'Choisissez une table' }); return; }
  window.location.href = API.base_url + 'api/operations/export/' + table;
}

async function previewExport() {
  const table = document.getElementById('exportTable').value;
  if (!table) { Swal.fire({ icon: 'warning', title: 'Choisissez une table' }); return; }
  const r = await API.request('GET', 'api/operations/preview/' + table);
  const div = document.getElementById('exportPreview');
  if (!r.success) { div.innerHTML = '<p class="text-danger mt-2">Erreur</p>'; div.classList.remove('d-none'); return; }
  let html = '<table class="table bordered-table mb-0 mt-2"><thead><tr>';
  r.data.headers.forEach(h => { html += '<th>' + h + '</th>'; });
  html += '</tr></thead><tbody>';
  r.data.rows.forEach(row => {
    html += '<tr>';
    r.data.headers.forEach(h => { html += '<td>' + (row[h] || '').toString().substring(0, 50) + '</td>'; });
    html += '</tr>';
  });
  html += '</tbody></table><small class="text-secondary-light">' + r.data.total + ' lignes au total</small>';
  div.innerHTML = html;
  div.classList.remove('d-none');
}

async function previewImport() {
  const file = document.getElementById('importFile').files[0];
  if (!file) { Swal.fire({ icon: 'warning', title: 'Choisissez un fichier' }); return; }
  const fd = new FormData();
  fd.append('file', file);
  const r = await fetch(API.base_url + 'api/operations/import-preview', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(res => res.json());
  const div = document.getElementById('importPreview');
  if (!r.success) { div.innerHTML = '<p class="text-danger mt-2">Erreur</p>'; div.classList.remove('d-none'); return; }
  importHeaders = r.data.headers;
  let html = '<h6 class="mt-3 mb-2">Aperçu (' + r.data.total_rows + ' lignes)</h6><table class="table bordered-table mb-0"><thead><tr>';
  r.data.headers.forEach(h => { html += '<th>' + h + '</th>'; });
  html += '</tr></thead><tbody>';
  r.data.preview.forEach(row => {
    html += '<tr>';
    row.forEach(cell => { html += '<td>' + (cell || '').toString().substring(0, 50) + '</td>'; });
    html += '</tr>';
  });
  html += '</tbody></table>';
  div.innerHTML = html;
  div.classList.remove('d-none');
  document.getElementById('importResult').classList.add('d-none');
}

async function runImport() {
  const table = document.getElementById('importTable').value;
  const file = document.getElementById('importFile').files[0];
  if (!table || !file) { Swal.fire({ icon: 'warning', title: 'Table et fichier requis' }); return; }
  const fd = new FormData();
  fd.append('table', table);
  fd.append('file', file);
  const btn = event.target; btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Import...';
  const r = await fetch(API.base_url + 'api/operations/import', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(res => res.json());
  btn.disabled = false; btn.innerHTML = '<i class="ri-upload-2-line"></i> Importer';
  const div = document.getElementById('importResult');
  if (r.success) {
    div.className = 'alert alert-success mt-16';
    div.innerHTML = '<i class="ri-check-line me-1"></i> ' + r.message;
  } else {
    div.className = 'alert alert-danger mt-16';
    div.innerHTML = '<i class="ri-close-circle-line me-1"></i> ' + (r.message || 'Erreur');
  }
  div.classList.remove('d-none');
}

(function() {
  loadTables('exportTable');
  loadTables('importTable');
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
