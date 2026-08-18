<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Mon Profil</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Mon Profil</span>
      </div>
    </div>
  </div>

  <div id="profileMessage" class="alert d-none"></div>

  <div class="row gy-4">
    <!-- Photo + Infos -->
    <div class="col-xxl-4">
      <div class="card text-center">
        <div class="card-body p-24">
          <div class="position-relative d-inline-block mb-16">
            <?php if (!empty($user['photo'])): ?>
            <img id="profilePhoto" src="<?= base_url($user['photo']) ?>" class="w-120-px h-120-px rounded-circle object-fit-cover border border-4 border-neutral-100 shadow-sm">
            <?php else: ?>
            <div class="w-120-px h-120-px rounded-circle bg-primary-100 d-flex align-items-center justify-content-center border border-4 border-neutral-100 shadow-sm mx-auto" id="profilePhotoContainer">
              <iconify-icon icon="solar:user-bold" class="text-primary-600" style="font-size:48px;"></iconify-icon>
            </div>
            <?php endif; ?>
            <label for="photoInput" class="position-absolute bottom-0 end-0 bg-primary-600 text-white rounded-circle w-32-px h-32-px d-flex align-items-center justify-content-center cursor-pointer" style="cursor:pointer;">
              <iconify-icon icon="solar:camera-outline" class="text-lg"></iconify-icon>
            </label>
            <input type="file" id="photoInput" accept="image/*" style="display:none;" onchange="uploadPhoto(this)">
          </div>
          <h5 class="text-lg fw-semibold mb-4" id="displayName"><?= htmlspecialchars($user['nom_complet']) ?></h5>
          <p class="text-secondary-light text-sm mb-2" id="displayEmail"><?= htmlspecialchars($user['email'] ?? '') ?></p>
          <p class="text-secondary-light text-sm mb-0" id="displayPhone"><?= htmlspecialchars($user['telephone'] ?? '') ?></p>
          <span class="badge bg-primary-50 text-primary-600 px-16 py-4 radius-4 mt-8"><?= $this->session->userdata('role_libelle') ?? $group_name ?></span>
        </div>
      </div>
    </div>

    <!-- Formulaire -->
    <div class="col-xxl-8">
      <div class="card">
        <div class="card-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
          <h5 class="mb-0 text-primary-light fw-semibold text-lg">Informations personnelles</h5>
        </div>
        <div class="card-body p-24">
          <form id="profileForm">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nom complet</label>
                <input type="text" class="form-control radius-8" id="nom_complet" value="<?= htmlspecialchars($user['nom_complet']) ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Email</label>
                <input type="email" class="form-control radius-8" id="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Téléphone</label>
                <input type="tel" class="form-control radius-8" id="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" placeholder="+243...">
              </div>
              <div class="col-12 mt-24">
                <button type="submit" class="btn btn-primary-600 d-flex align-items-center gap-6">
                  <span class="d-flex text-md"><i class="ri-save-line"></i></span>
                  Enregistrer
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card mt-24">
        <div class="card-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
          <h5 class="mb-0 text-primary-light fw-semibold text-lg">Changer le mot de passe</h5>
        </div>
        <div class="card-body p-24">
          <form id="passwordForm">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Mot de passe actuel</label>
                <input type="password" class="form-control radius-8" id="current_password" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nouveau mot de passe</label>
                <input type="password" class="form-control radius-8" id="new_password" required minlength="6">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold text-primary-light text-sm mb-8">Confirmer le mot de passe</label>
                <input type="password" class="form-control radius-8" id="confirm_password" required minlength="6">
              </div>
              <div class="col-12 mt-24">
                <button type="submit" class="btn btn-primary-600 d-flex align-items-center gap-6">
                  <span class="d-flex text-md"><i class="ri-lock-line"></i></span>
                  Changer le mot de passe
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

function showMessage(success, text) {
  var msg = document.getElementById('profileMessage');
  msg.className = 'alert ' + (success ? 'alert-success' : 'alert-danger') + ' d-flex align-items-center gap-2';
  msg.innerHTML = '<i class="' + (success ? 'ri-check-line' : 'ri-close-circle-line') + '"></i> ' + text;
  msg.classList.remove('d-none');
  setTimeout(function() { msg.classList.add('d-none'); }, 4000);
}

async function uploadPhoto(input) {
  if (!input.files || !input.files[0]) return;
  var formData = new FormData();
  formData.append('photo', input.files[0]);
  var btn = input;
  btn.disabled = true;
  try {
    var res = await fetch(API.base_url + 'api/profile/upload_photo', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: formData });
    var r = await res.json();
    if (r.success) {
      var container = document.getElementById('profilePhotoContainer');
      var img = document.getElementById('profilePhoto');
      if (img) {
        img.src = API.base_url + r.data.photo + '?t=' + Date.now();
      } else if (container) {
        container.outerHTML = '<img id="profilePhoto" src="' + API.base_url + r.data.photo + '?t=' + Date.now() + '" class="w-120-px h-120-px rounded-circle object-fit-cover border border-4 border-neutral-100 shadow-sm">';
      }
      showMessage(true, 'Photo mise à jour');
    } else {
      showMessage(false, r.message || 'Erreur upload');
    }
  } catch (err) {
    showMessage(false, 'Erreur de connexion');
  }
  btn.disabled = false;
}

document.getElementById('profileForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  var btn = this.querySelector('button[type="submit"]');
  btn.disabled = true;
  var data = {
    nom_complet: document.getElementById('nom_complet').value,
    email: document.getElementById('email').value,
    telephone: document.getElementById('telephone').value
  };
  try {
    var res = await fetch(API.base_url + 'api/profile/update', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(data)
    });
    var r = await res.json();
    if (r.success) {
      document.getElementById('displayName').textContent = data.nom_complet;
      document.getElementById('displayEmail').textContent = data.email;
      document.getElementById('displayPhone').textContent = data.telephone;
      showMessage(true, r.message || 'Profil mis à jour');
    } else {
      showMessage(false, r.message || 'Erreur');
    }
  } catch (err) {
    showMessage(false, 'Erreur de connexion');
  }
  btn.disabled = false;
});

document.getElementById('passwordForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  var btn = this.querySelector('button[type="submit"]');
  btn.disabled = true;
  var newPass = document.getElementById('new_password').value;
  var confirmPass = document.getElementById('confirm_password').value;
  if (newPass !== confirmPass) {
    showMessage(false, 'Les mots de passe ne correspondent pas');
    btn.disabled = false;
    return;
  }
  var data = {
    current_password: document.getElementById('current_password').value,
    new_password: newPass,
    confirm_password: confirmPass
  };
  try {
    var res = await fetch(API.base_url + 'api/profile/change_password', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(data)
    });
    var r = await res.json();
    if (r.success) {
      this.reset();
      showMessage(true, r.message || 'Mot de passe changé');
    } else {
      showMessage(false, r.message || 'Erreur');
    }
  } catch (err) {
    showMessage(false, 'Erreur de connexion');
  }
  btn.disabled = false;
});
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
