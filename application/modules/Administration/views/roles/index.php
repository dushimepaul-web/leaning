<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des rôles</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Administration / Rôles</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" data-bs-toggle="modal" data-bs-target="#addModal">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
      Ajouter un rôle
    </button>
  </div>
  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0">
        <table class="table bordered-table mb-0" id="dataTable" style="width:100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Code</th>
              <th>Libellé</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="dataBody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Ajouter un rôle</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <form id="addForm">
          <div class="mb-3"><label class="form-label">Code</label><input type="text" name="code" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Libellé</label><input type="text" name="libelle" class="form-control" required></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        <button type="button" class="btn btn-primary-600" onclick="saveRole()">Enregistrer</button>
      </div>
    </div>
  </div>
</div>
<script>
const API_ROLES = { list: () => API.get('api/roles'), create: (d) => API.post('api/roles/create', d), update: (id, d) => API.post('api/roles/' + id + '/update', d), delete: (id) => API.get('api/roles/' + id + '/delete') };

async function loadTable() {
  const res = await API_ROLES.list();
  if (!res.success) return;
  let html = '';
  res.data.forEach((r, i) => {
    html += `<tr>
      <td>${i+1}</td>
      <td>${r.code}</td>
      <td>${r.libelle}</td>
      <td>
        <button class="btn btn-sm btn-danger" onclick="confirmDelete('${r.uuid}')"><i class="ri-delete-bin-line"></i></button>
      </td>
    </tr>`;
  });
  document.getElementById('dataBody').innerHTML = html;
}

async function saveRole() {
  const f = document.getElementById('addForm');
  const fd = new FormData(f);
  const data = Object.fromEntries(fd);
  const res = await API_ROLES.create(data);
  if (res.success) { f.reset(); bootstrap.Modal.getInstance(document.getElementById('addModal')).hide(); loadTable(); }
  else Swal.fire('Erreur', res.message, 'error');
}

async function confirmDelete(uuid) {
  const c = await Swal.fire({ title: 'Confirmer', text: 'Supprimer ce rôle ?', icon: 'warning', showCancelButton: true });
  if (c.isConfirmed) { const r = await API_ROLES.delete(uuid); if (r.success) loadTable(); else Swal.fire('Erreur', r.message, 'error'); }
}

loadTable();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
