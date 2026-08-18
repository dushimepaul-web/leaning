<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Opérations groupées</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Administration / Opérations</span>
      </div>
    </div>
  </div>
  <div class="mt-24">
    <div class="row">
      <div class="col-4">
        <div class="card h-100">
          <div class="card-body">
            <h6 class="fw-semibold mb-16">Exporter une table</h6>
            <div class="mb-3"><label class="form-label">Table</label>
              <select id="exportTable" class="form-select"><option value="">Chargement...</option></select>
            </div>
            <button class="btn btn-primary-600 w-100" onclick="exportTable()"><i class="ri-file-download-line"></i> Exporter CSV</button>
          </div>
        </div>
      </div>
      <div class="col-8">
        <div class="card h-100">
          <div class="card-body">
            <h6 class="fw-semibold mb-16">Aperçu des données</h6>
            <div class="mb-3">
              <select id="previewTable" class="form-select" onchange="previewTable()"><option value="">Sélectionner une table</option></select>
            </div>
            <div id="previewContainer"><p class="text-secondary-light">Sélectionnez une table pour voir l'aperçu</p></div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
async function loadTables() {
  const res = await API.operations.tables();
  if (!res.success || !res.data) return;
  const opts = res.data.map(t => `<option value="${t.name}">${t.label}</option>`).join('');
  document.getElementById('exportTable').innerHTML = '<option value="">Choisir une table</option>' + opts;
  document.getElementById('previewTable').innerHTML = '<option value="">Sélectionner une table</option>' + opts;
}

function exportTable() {
  const t = document.getElementById('exportTable').value;
  if (!t) { Swal.fire('Attention', 'Sélectionnez une table', 'warning'); return; }
  window.location.href = '<?= base_url('api/operations/export/') ?>' + t;
}

async function previewTable() {
  const t = document.getElementById('previewTable').value;
  if (!t) { document.getElementById('previewContainer').innerHTML = '<p class="text-secondary-light">Sélectionnez une table pour voir l\'aperçu</p>'; return; }
  const res = await API.operations.preview(t);
  if (!res.success || !res.data) { document.getElementById('previewContainer').innerHTML = '<p class="text-danger">Erreur de chargement</p>'; return; }
  let html = `<p class="text-secondary-light mb-2">Total: ${res.data.total} enregistrements</p><div class="table-responsive"><table class="table bordered-table table-sm"><thead><tr>`;
  res.data.headers.forEach(h => { html += `<th>${h}</th>`; });
  html += '</tr></thead><tbody>';
  res.data.rows.forEach(r => {
    html += '<tr>';
    res.data.headers.forEach(h => { html += `<td>${r[h] ?? ''}</td>`; });
    html += '</tr>';
  });
  html += '</tbody></table></div>';
  document.getElementById('previewContainer').innerHTML = html;
}

loadTables();
</script>

