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
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="createBackup()">
      <span class="d-flex text-md"><i class="ri-download-line"></i></span>
      Créer une sauvegarde
    </button>
  </div>
  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0">
        <table class="table bordered-table mb-0" id="dataTable" style="width:100%">
          <thead>
            <tr>
              <th>#</th>
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
<script>
async function loadBackups() {
  const res = await API.sauvegardes.list();
  if (!res.success) return;
  let html = '';
  (res.data || []).forEach((b, i) => {
    const size = b.size > 1024*1024 ? (b.size/1024/1024).toFixed(2)+' MB' : (b.size/1024).toFixed(2)+' KB';
    html += `<tr>
      <td>${i+1}</td>
      <td>${b.file}</td>
      <td>${size}</td>
      <td>${b.date}</td>
      <td>
        <a href="<?= base_url('api/sauvegardes/download/') ?>${b.file}" class="btn btn-sm btn-primary-600"><i class="ri-download-line"></i></a>
        <button class="btn btn-sm btn-danger" onclick="deleteBackup('${b.file}')"><i class="ri-delete-bin-line"></i></button>
      </td>
    </tr>`;
  });
  document.getElementById('dataBody').innerHTML = html;
}

async function createBackup() {
  Swal.fire({ title: 'Création...', text: 'Sauvegarde en cours', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
  const res = await API.sauvegardes.create();
  Swal.close();
  if (res.success) { Swal.fire('Succès', 'Sauvegarde créée', 'success'); loadBackups(); }
  else Swal.fire('Erreur', res.message, 'error');
}

async function deleteBackup(file) {
  const c = await Swal.fire({ title: 'Confirmer', text: 'Supprimer cette sauvegarde ?', icon: 'warning', showCancelButton: true });
  if (c.isConfirmed) { const r = await API.sauvegardes.delete(file); if (r.success) loadBackups(); else Swal.fire('Erreur', r.message, 'error'); }
}

loadBackups();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
