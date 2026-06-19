<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Paramètres</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Paramètres</span>
      </div>
    </div>
    <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="document.getElementById('settingsForm').requestSubmit()">
      <span class="d-flex text-md"><i class="ri-save-line"></i></span> Enregistrer
    </button>
  </div>

  <div id="settingsMessage" class="alert d-none mb-16"></div>

  <div class="row gy-4">
    <div class="col-xxl-8 col-lg-7">
      <form id="settingsForm">
        <!-- Nav tabs -->
        <ul class="nav nav-pills bordered-tab mb-3" id="settingsTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="tab-general" data-bs-toggle="pill" data-bs-target="#pane-general" type="button">
              <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-building-line"></i></span> Général
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="tab-finances" data-bs-toggle="pill" data-bs-target="#pane-finances" type="button">
              <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-money-dollar-circle-line"></i></span> Finances
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="tab-email" data-bs-toggle="pill" data-bs-target="#pane-email" type="button">
              <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-mail-line"></i></span> Email
            </button>
          </li>
        </ul>

        <div class="tab-content">
          <!-- Onglet Général -->
          <div class="tab-pane fade show active" id="pane-general">
            <div class="card shadow-1 radius-12">
              <div class="card-header py-16 px-24 border-bottom bg-base d-flex align-items-center gap-12">
                <span class="d-flex align-items-center justify-content-center bg-primary-100 text-primary-600 radius-4" style="width:36px;height:36px;"><i class="ri-building-line text-lg"></i></span>
                <div>
                  <h6 class="text-lg fw-semibold mb-0">Informations de l'école</h6>
                  <small class="text-secondary-light">Ces informations apparaîtront sur les reçus et documents</small>
                </div>
              </div>
              <div class="card-body p-24">
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nom de l'école</label>
                    <input type="text" class="form-control radius-8" id="nom_ecole" placeholder="Ex: Complexe Scolaire VIP">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Téléphone</label>
                    <input type="text" class="form-control radius-8" id="telephone_ecole" placeholder="+243 800 000 000">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Email</label>
                    <input type="email" class="form-control radius-8" id="email_ecole" placeholder="contact@ecole.cd">
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Adresse</label>
                    <textarea class="form-control radius-8" id="adresse_ecole" rows="3" placeholder="N°, Avenue, Commune, Ville"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Onglet Finances -->
          <div class="tab-pane fade" id="pane-finances">
            <div class="card shadow-1 radius-12">
              <div class="card-header py-16 px-24 border-bottom bg-base d-flex align-items-center gap-12">
                <span class="d-flex align-items-center justify-content-center bg-success-100 text-success-600 radius-4" style="width:36px;height:36px;"><i class="ri-money-dollar-circle-line text-lg"></i></span>
                <div>
                  <h6 class="text-lg fw-semibold mb-0">Configuration financière</h6>
                  <small class="text-secondary-light">Devise, taxes et numérotation des reçus</small>
                </div>
              </div>
              <div class="card-body p-24">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Devise</label>
                    <input type="text" class="form-control radius-8" id="devise" placeholder="Ex: FC, USD, EUR">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">TVA (%)</label>
                    <input type="number" class="form-control radius-8" id="tva" placeholder="0" step="0.01" min="0">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Prochain N° reçu</label>
                    <input type="number" class="form-control radius-8" id="prochain_num_recu" placeholder="1" min="1">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Onglet Email -->
          <div class="tab-pane fade" id="pane-email">
            <div class="card shadow-1 radius-12">
              <div class="card-header py-16 px-24 border-bottom bg-base d-flex align-items-center gap-12">
                <span class="d-flex align-items-center justify-content-center bg-warning-100 text-warning-600 radius-4" style="width:36px;height:36px;"><i class="ri-mail-line text-lg"></i></span>
                <div>
                  <h6 class="text-lg fw-semibold mb-0">Configuration Email</h6>
                  <small class="text-secondary-light">Paramètres SMTP pour l'envoi des emails</small>
                </div>
              </div>
              <div class="card-body p-24">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Protocole</label>
                    <select class="form-control radius-8" id="email_protocol">
                      <option value="mail">PHP mail()</option>
                      <option value="smtp">SMTP</option>
                      <option value="sendmail">Sendmail</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Hôte SMTP</label>
                    <input type="text" class="form-control radius-8" id="email_smtp_host" placeholder="smtp.gmail.com">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Utilisateur SMTP</label>
                    <input type="text" class="form-control radius-8" id="email_smtp_user" placeholder="exemple@gmail.com">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Mot de passe SMTP</label>
                    <input type="password" class="form-control radius-8" id="email_smtp_pass" placeholder="••••••••">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Port SMTP</label>
                    <input type="number" class="form-control radius-8" id="email_smtp_port" placeholder="587">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Cryptage</label>
                    <select class="form-control radius-8" id="email_smtp_crypto">
                      <option value="tls">TLS</option>
                      <option value="ssl">SSL</option>
                      <option value="">Aucun</option>
                    </select>
                  </div>
                  <div class="col-md-4 d-flex align-items-end pb-2">
                    <button type="button" class="btn btn-success-600 w-100 d-flex align-items-center justify-content-center gap-6 py-10 radius-8" onclick="testEmail()">
                      <i class="ri-mail-send-line"></i> Tester l'envoi
                    </button>
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Chemin Sendmail</label>
                    <input type="text" class="form-control radius-8" id="email_sendmail_path" placeholder="/usr/sbin/sendmail">
                    <small class="text-secondary-light">Uniquement si protocole = Sendmail</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>

    <div class="col-xxl-4 col-lg-5">
      <!-- Logo -->
      <div class="card shadow-1 radius-12 mb-24">
        <div class="card-header py-16 px-24 border-bottom bg-base">
          <h6 class="text-lg fw-semibold mb-0 d-flex align-items-center gap-8">
            <i class="ri-image-line text-primary-600"></i> Logo de l'école
          </h6>
        </div>
        <div class="card-body p-24 text-center">
          <div class="mb-16 bg-neutral-50 rounded-8 p-20 d-flex align-items-center justify-content-center" style="min-height:130px;">
            <img src="<?= base_url($this->Model->get_setting('logo_ecole', 'assets/images/logo.png')) ?>" alt="Logo" class="img-fluid" style="max-height:110px;" id="logoImg">
          </div>
          <small class="text-secondary-light d-block mb-12">JPG, PNG, SVG, WEBP — max 2MB</small>
          <button type="button" class="btn btn-outline-primary-600 px-24 py-10 radius-8" onclick="document.getElementById('logoInput').click()">
            <i class="ri-upload-line me-1"></i> Changer le logo
          </button>
          <input type="file" id="logoInput" class="d-none" accept="image/jpeg,image/png,image/gif,image/svg+xml,image/webp">
        </div>
      </div>

      <!-- Favicon -->
      <div class="card shadow-1 radius-12 mb-24">
        <div class="card-header py-16 px-24 border-bottom bg-base">
          <h6 class="text-lg fw-semibold mb-0 d-flex align-items-center gap-8">
            <i class="ri-chrome-line text-primary-600"></i> Favicon
          </h6>
        </div>
        <div class="card-body p-24 text-center">
          <div class="d-flex align-items-center justify-content-center mb-12">
            <img src="<?= base_url($this->Model->get_setting('favicon_ecole', 'assets/images/favicon.png')) ?>" alt="Favicon" style="width:48px;height:48px;border-radius:8px;" id="faviconImg">
          </div>
          <small class="text-secondary-light d-block mb-12">PNG, ICO, SVG — max 1MB</small>
          <button type="button" class="btn btn-outline-primary-600 px-24 py-10 radius-8" onclick="document.getElementById('faviconInput').click()">
            <i class="ri-upload-line me-1"></i> Changer
          </button>
          <input type="file" id="faviconInput" class="d-none" accept="image/png,image/x-icon,image/svg+xml">
        </div>
      </div>

      <!-- Année scolaire -->
      <div class="card shadow-1 radius-12 mb-24">
        <div class="card-header py-16 px-24 border-bottom bg-base">
          <h6 class="text-lg fw-semibold mb-0 d-flex align-items-center gap-8">
            <i class="ri-calendar-line text-primary-600"></i> Année scolaire active
          </h6>
        </div>
        <div class="card-body p-24">
          <div class="d-flex gap-8 mb-12">
            <select class="form-control radius-8 flex-grow-1" id="annee_active">
              <option value="">Sélectionner...</option>
            </select>
            <button type="button" class="btn btn-primary-600 px-16 py-8 radius-8 flex-shrink-0 d-flex align-items-center gap-6" onclick="activerAnnee()">
              <i class="ri-check-line"></i> Activer
            </button>
          </div>
          <div id="anneeBadge" class="text-sm text-success-600 d-none align-items-center gap-4">
            <i class="ri-checkbox-circle-fill"></i> <span>Année active : <strong id="anneeName"></strong></span>
          </div>
        </div>
      </div>

      <!-- Trimestre -->
      <div class="card shadow-1 radius-12">
        <div class="card-header py-16 px-24 border-bottom bg-base">
          <h6 class="text-lg fw-semibold mb-0 d-flex align-items-center gap-8">
            <i class="ri-timer-line text-primary-600"></i> Trimestre actif
          </h6>
        </div>
        <div class="card-body p-24">
          <div class="d-flex gap-8 mb-12">
            <select class="form-control radius-8 flex-grow-1" id="periode_active">
              <option value="">Sélectionner...</option>
            </select>
            <button type="button" class="btn btn-primary-600 px-16 py-8 radius-8 flex-shrink-0 d-flex align-items-center gap-6" onclick="activerPeriode()">
              <i class="ri-check-line"></i> Activer
            </button>
          </div>
          <div id="periodeBadge" class="text-sm text-success-600 d-none align-items-center gap-4">
            <i class="ri-checkbox-circle-fill"></i> <span>Trimestre actif : <strong id="periodeName"></strong></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script>
let anneesData = [];
let periodesData = [];

async function loadSettings() {
  try {
    const r = await API.parametres.list();
    if (!r.success || !r.data) return;
    const s = r.data;
    if (s.nom_ecole) document.getElementById('nom_ecole').value = s.nom_ecole;
    if (s.telephone_ecole) document.getElementById('telephone_ecole').value = s.telephone_ecole;
    if (s.email_ecole) document.getElementById('email_ecole').value = s.email_ecole;
    if (s.adresse_ecole) document.getElementById('adresse_ecole').value = s.adresse_ecole;
    if (s.devise) document.getElementById('devise').value = s.devise;
    if (s.tva) document.getElementById('tva').value = s.tva;
    if (s.prochain_num_recu) document.getElementById('prochain_num_recu').value = s.prochain_num_recu;
    if (s.email_protocol) document.getElementById('email_protocol').value = s.email_protocol;
    if (s.email_smtp_host) document.getElementById('email_smtp_host').value = s.email_smtp_host;
    if (s.email_smtp_user) document.getElementById('email_smtp_user').value = s.email_smtp_user;
    if (s.email_smtp_pass) document.getElementById('email_smtp_pass').value = s.email_smtp_pass;
    if (s.email_smtp_port) document.getElementById('email_smtp_port').value = s.email_smtp_port;
    if (s.email_smtp_crypto) document.getElementById('email_smtp_crypto').value = s.email_smtp_crypto;
    if (s.email_sendmail_path) document.getElementById('email_sendmail_path').value = s.email_sendmail_path;
    if (s.logo_ecole) document.getElementById('logoImg').src = '<?= base_url() ?>' + s.logo_ecole;
    if (s.favicon_ecole) document.getElementById('faviconImg').src = '<?= base_url() ?>' + s.favicon_ecole;
  } catch (err) { console.error(err); }
}

async function loadAnnees() {
  try {
    const r = await API.annees.list();
    if (!r.success) return;
    anneesData = r.data;
    const sel = document.getElementById('annee_active');
    sel.innerHTML = '<option value="">Sélectionner...</option>';
    r.data.forEach(a => {
      const opt = document.createElement('option');
      opt.value = a.id_annee;
      opt.textContent = a.libelle;
      if (a.est_en_cours == 1) opt.selected = true;
      sel.appendChild(opt);
    });
    updateAnneeBadge();
  } catch (err) { console.error(err); }
}

async function loadPeriodes() {
  try {
    const r = await API.periodes.list();
    if (!r.success) return;
    periodesData = r.data;
    const sel = document.getElementById('periode_active');
    sel.innerHTML = '<option value="">Sélectionner...</option>';
    r.data.forEach(p => {
      const opt = document.createElement('option');
      opt.value = p.id_periode;
      opt.textContent = p.libelle + (p.annee_libelle ? ' (' + p.annee_libelle + ')' : '');
      if (p.est_en_cours == 1) opt.selected = true;
      sel.appendChild(opt);
    });
    updatePeriodeBadge();
  } catch (err) { console.error(err); }
}

function updateAnneeBadge() {
  const active = anneesData.find(a => a.est_en_cours == 1);
  const badge = document.getElementById('anneeBadge');
  if (active) {
    badge.classList.remove('d-none');
    badge.classList.add('d-flex');
    document.getElementById('anneeName').textContent = active.libelle;
  } else {
    badge.classList.add('d-none');
    badge.classList.remove('d-flex');
  }
}

function updatePeriodeBadge() {
  const active = periodesData.find(p => p.est_en_cours == 1);
  const badge = document.getElementById('periodeBadge');
  if (active) {
    badge.classList.remove('d-none');
    badge.classList.add('d-flex');
    document.getElementById('periodeName').textContent = active.libelle;
  } else {
    badge.classList.add('d-none');
    badge.classList.remove('d-flex');
  }
}

async function activerAnnee() {
  const sel = document.getElementById('annee_active');
  if (!sel.value) { Swal.fire({ icon: 'warning', title: 'Sélection', text: 'Choisissez une année scolaire' }); return; }
  const a = anneesData.find(a => a.id_annee == sel.value);
  if (!a) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Année introuvable' }); return; }
  try {
    const r = await API.annees.setActive(a.uuid);
    if (r.success) {
      Toast.fire({ icon: 'success', title: 'Année ' + a.libelle + ' activée' });
      loadAnnees();
    } else {
      Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
    }
  } catch(e) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion' }); }
}

async function activerPeriode() {
  const sel = document.getElementById('periode_active');
  if (!sel.value) { Swal.fire({ icon: 'warning', title: 'Sélection', text: 'Choisissez un trimestre' }); return; }
  const p = periodesData.find(p => p.id_periode == sel.value);
  if (!p) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Période introuvable' }); return; }
  try {
    const r = await API.periodes.setActive(p.uuid);
    if (r.success) {
      Toast.fire({ icon: 'success', title: 'Trimestre ' + p.libelle + ' activé' });
      loadPeriodes();
    } else {
      Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
    }
  } catch(e) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion' }); }
}

const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2500, timerProgressBar: true });

document.getElementById('settingsForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.querySelector('.btn-primary-600 .ri-save-line')?.closest('button');
  if (btn) btn.disabled = true;
  const msg = document.getElementById('settingsMessage');
  msg.className = 'alert d-none';
  const data = {
    nom_ecole: document.getElementById('nom_ecole').value,
    telephone_ecole: document.getElementById('telephone_ecole').value,
    email_ecole: document.getElementById('email_ecole').value,
    adresse_ecole: document.getElementById('adresse_ecole').value,
    devise: document.getElementById('devise').value,
    tva: document.getElementById('tva').value || '0',
    prochain_num_recu: document.getElementById('prochain_num_recu').value || '1',
    email_protocol: document.getElementById('email_protocol').value,
    email_smtp_host: document.getElementById('email_smtp_host').value,
    email_smtp_user: document.getElementById('email_smtp_user').value,
    email_smtp_pass: document.getElementById('email_smtp_pass').value,
    email_smtp_port: document.getElementById('email_smtp_port').value || '587',
    email_smtp_crypto: document.getElementById('email_smtp_crypto').value,
    email_sendmail_path: document.getElementById('email_sendmail_path').value
  };
  try {
    const r = await API.parametres.update(data);
    if (r.success) {
      msg.className = 'alert alert-success d-flex align-items-center gap-8 radius-8';
      msg.innerHTML = '<i class="ri-check-line"></i> Paramètres enregistrés avec succès';
      setTimeout(() => msg.className = 'alert d-none', 3000);
    } else {
      msg.className = 'alert alert-danger d-flex align-items-center gap-8 radius-8';
      msg.innerHTML = '<i class="ri-close-circle-line"></i> ' + (r.message || 'Erreur');
    }
  } catch (err) {
    msg.className = 'alert alert-danger d-flex align-items-center gap-8 radius-8';
    msg.innerHTML = '<i class="ri-close-circle-line"></i> Erreur de connexion';
  } finally { if (btn) btn.disabled = false; }
});

document.getElementById('logoInput').addEventListener('change', async function() {
  if (!this.files || !this.files[0]) return;
  const fd = new FormData(); fd.append('logo', this.files[0]);
  try {
    const res = await fetch(API.base_url + 'api/parametres/upload_logo', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const r = await res.json();
    if (r.success) {
      document.getElementById('logoImg').src = API.base_url + r.data.path;
      Toast.fire({ icon: 'success', title: 'Logo mis à jour' });
    } else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message || 'Erreur upload' });
  } catch (e) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion' }); }
});

document.getElementById('faviconInput').addEventListener('change', async function() {
  if (!this.files || !this.files[0]) return;
  const fd = new FormData(); fd.append('favicon', this.files[0]);
  try {
    const res = await fetch(API.base_url + 'api/parametres/upload_favicon', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const r = await res.json();
    if (r.success) {
      document.getElementById('faviconImg').src = API.base_url + r.data.path;
      Toast.fire({ icon: 'success', title: 'Favicon mis à jour' });
    } else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message || 'Erreur upload' });
  } catch (e) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion' }); }
});

async function testEmail() {
  const email = document.getElementById('email_ecole').value || document.getElementById('email_smtp_user').value;
  if (!email) { Swal.fire({ icon: 'warning', title: 'Email requis', text: 'Configurez l\'email ou l\'utilisateur SMTP' }); return; }
  const btn = document.querySelector('.btn-success-600');
  if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Envoi...'; }
  try {
    const res = await fetch(API.base_url + 'api/email/test', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ to: email }) });
    const r = await res.json();
    if (r.success) Swal.fire({ icon: 'success', title: 'Email envoyé', text: 'Vérifiez votre boîte de réception.' });
    else Swal.fire({ icon: 'error', title: 'Échec', text: r.message || 'Erreur inconnue' });
  } catch (e) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion' }); }
  finally { if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ri-mail-send-line"></i> Tester l\'envoi'; } }
}

(function() { loadAnnees().then(loadPeriodes).then(loadSettings); })();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
