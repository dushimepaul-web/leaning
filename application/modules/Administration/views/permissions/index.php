<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Gestion des permissions</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Administration / Permissions</span>
      </div>
    </div>
  </div>
  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0">
        <div class="p-20 border-bottom border-neutral-200">
          <div class="mb-3"><label class="form-label">Rôle</label>
            <select id="roleSelect" class="form-select" onchange="loadPermissions()">
              <option value="">Sélectionner un rôle</option>
              <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id_role'] ?>"><?= htmlspecialchars($r['libelle']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div id="permsContainer" class="p-20"></div>
      </div>
    </div>
  </div>
</div>
<script>
let allMenus = <?= json_encode($menus) ?>;

async function loadPermissions() {
  const idRole = document.getElementById('roleSelect').value;
  if (!idRole) { document.getElementById('permsContainer').innerHTML = ''; return; }
  const res = await API.get('api/permissions/' + idRole);
  const rolePerms = res.success && res.data ? res.data : [];
  const permMap = {};
  rolePerms.forEach(p => { permMap[p.id_menu] = p; });

  let html = '<div class="table-responsive"><table class="table bordered-table"><thead><tr><th>Menu</th><th>Voir</th><th>Ajouter</th><th>Modifier</th><th>Supprimer</th><th>Exporter</th><th>Imprimer</th></tr></thead><tbody>';
  allMenus.forEach(m => {
    const p = permMap[m.id_menu] || {};
    html += `<tr>
      <td>${m.libelle}</td>
      <td><input type="checkbox" class="form-check-input perm-cb" data-menu="${m.id_menu}" data-field="can_view" ${p.can_view ? 'checked' : ''}></td>
      <td><input type="checkbox" class="form-check-input perm-cb" data-menu="${m.id_menu}" data-field="can_add" ${p.can_add ? 'checked' : ''}></td>
      <td><input type="checkbox" class="form-check-input perm-cb" data-menu="${m.id_menu}" data-field="can_edit" ${p.can_edit ? 'checked' : ''}></td>
      <td><input type="checkbox" class="form-check-input perm-cb" data-menu="${m.id_menu}" data-field="can_delete" ${p.can_delete ? 'checked' : ''}></td>
      <td><input type="checkbox" class="form-check-input perm-cb" data-menu="${m.id_menu}" data-field="can_export" ${p.can_export ? 'checked' : ''}></td>
      <td><input type="checkbox" class="form-check-input perm-cb" data-menu="${m.id_menu}" data-field="can_imprimer" ${p.can_imprimer ? 'checked' : ''}></td>
    </tr>`;
  });
  html += '</tbody></table></div>';
  html += '<button class="btn btn-primary-600" onclick="savePermissions()">Enregistrer les permissions</button>';
  document.getElementById('permsContainer').innerHTML = html;
}

async function savePermissions() {
  const idRole = document.getElementById('roleSelect').value;
  if (!idRole) return;
  const cbs = document.querySelectorAll('.perm-cb');
  const perms = [];
  cbs.forEach(cb => {
    perms.push({ id_menu: parseInt(cb.dataset.menu), field: cb.dataset.field, value: cb.checked ? 1 : 0 });
  });
  const res = await API.post('api/permissions/' + idRole + '/update', { permissions: perms });
  if (res.success) Swal.fire('Succès', 'Permissions mises à jour', 'success');
  else Swal.fire('Erreur', res.message, 'error');
}
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
