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
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="savePermissions()">
      <span class="d-flex text-md"><i class="ri-save-line"></i></span>
      Enregistrer
    </button>
  </div>

  <div id="permMessage" class="alert d-none"></div>

  <div class="row gy-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header py-16 px-24 border-bottom">
          <div class="d-flex align-items-center gap-3">
            <label class="text-sm fw-semibold text-primary-light">Sélectionner un rôle :</label>
            <select class="form-select w-auto" id="roleSelect" onchange="loadPermissions()">
              <option value="">Choisir...</option>
              <?php foreach ($roles as $r): ?>
              <option value="<?= $r['uuid'] ?>"><?= htmlspecialchars($r['libelle']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="card-body p-24">
          <div id="permContainer" class="table-responsive">
            <p class="text-secondary-light text-center py-48">Sélectionnez un rôle pour gérer ses permissions</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js"></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let currentRoleId = null;
let allMenus = [];

async function loadPermissions() {
  const roleId = document.getElementById('roleSelect').value;
  if (!roleId) {
    document.getElementById('permContainer').innerHTML = '<p class="text-secondary-light text-center py-48">Sélectionnez un rôle pour gérer ses permissions</p>';
    currentRoleId = null;
    return;
  }
  currentRoleId = roleId;
  const res = await API.roles.get(roleId);
  if (!res.success) { document.getElementById('permContainer').innerHTML = '<p class="text-danger text-center py-48">Erreur de chargement</p>'; return; }
  const roleMenus = res.data.menus || [];
  const permMap = {};
  roleMenus.forEach(pm => { permMap[pm.id_menu] = pm; });

  const menusRes = await API.request('GET', 'api/menus/all');
  if (!menusRes.success) { document.getElementById('permContainer').innerHTML = '<p class="text-danger text-center py-48">Erreur de chargement des menus</p>'; return; }
  allMenus = menusRes.data;

  let html = '<table class="table bordered-table mb-0"><thead><tr><th>Menu</th><th>Voir</th><th>Ajouter</th><th>Modifier</th><th>Supprimer</th><th>Exporter</th><th>Imprimer</th></tr></thead><tbody>';
  const parents = allMenus.filter(m => !m.parent_id);
  parents.forEach(p => {
    const pm = permMap[p.id_menu] || {};
    const children = allMenus.filter(m => m.parent_id == p.id_menu);
    const rowspan = children.length > 0 ? '' : '';
    html += `<tr class="bg-primary-50"><td><strong>${p.libelle || p.code}</strong></td>`;
    ['can_view','can_add','can_edit','can_delete','can_export','can_imprimer'].forEach(perm => {
      const checked = pm[perm] == 1 ? 'checked' : '';
      html += `<td class="text-center"><input type="checkbox" class="form-check-input perm-check" data-menu="${p.id_menu}" data-perm="${perm}" ${checked}></td>`;
    });
    html += '</tr>';
    children.forEach(c => {
      const cm = permMap[c.id_menu] || {};
      html += `<tr><td class="ps-40">${c.libelle || c.code}</td>`;
      ['can_view','can_add','can_edit','can_delete','can_export','can_imprimer'].forEach(perm => {
        const checked = cm[perm] == 1 ? 'checked' : '';
        html += `<td class="text-center"><input type="checkbox" class="form-check-input perm-check" data-menu="${c.id_menu}" data-perm="${perm}" ${checked}></td>`;
      });
      html += '</tr>';
    });
  });
  const orphans = allMenus.filter(m => m.parent_id && !parents.some(p => p.id_menu == m.parent_id));
  orphans.forEach(m => {
    const mm = permMap[m.id_menu] || {};
    html += `<tr><td>${m.libelle || m.code}</td>`;
    ['can_view','can_add','can_edit','can_delete','can_export','can_imprimer'].forEach(perm => {
      const checked = mm[perm] == 1 ? 'checked' : '';
      html += `<td class="text-center"><input type="checkbox" class="form-check-input perm-check" data-menu="${m.id_menu}" data-perm="${perm}" ${checked}></td>`;
    });
    html += '</tr>';
  });
  html += '</tbody></table>';
  document.getElementById('permContainer').innerHTML = html;
}

async function savePermissions() {
  if (!currentRoleId) { Swal.fire({ icon: 'warning', title: 'Sélection', text: 'Veuillez sélectionner un rôle' }); return; }
  const checkboxes = document.querySelectorAll('.perm-check');
  const permissions = {};
  checkboxes.forEach(cb => {
    const menuId = cb.dataset.menu;
    const perm = cb.dataset.perm;
    if (!permissions[menuId]) permissions[menuId] = {};
    permissions[menuId][perm] = cb.checked ? 1 : 0;
  });
  const res = await API.request('POST', 'api/roles/' + currentRoleId + '/permissions', { permissions: permissions });
  const msg = document.getElementById('permMessage');
  if (res.success) {
    msg.className = 'alert alert-success d-flex align-items-center gap-2';
    msg.innerHTML = '<i class="ri-check-line"></i> Permissions enregistrées';
    setTimeout(() => msg.classList.add('d-none'), 3000);
  } else {
    msg.className = 'alert alert-danger d-flex align-items-center gap-2';
    msg.innerHTML = '<i class="ri-close-circle-line"></i> ' + (res.message || 'Erreur');
  }
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined') {
      clearInterval(wait);
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
