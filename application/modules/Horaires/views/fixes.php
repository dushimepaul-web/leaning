<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Sessions Fixes (<?= htmlspecialchars($annee_label) ?>)</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / </span>
        <a href="<?= base_url('Horaires') ?>" class="text-secondary-light hover-text-primary hover-underline">Horaires</a>
        <span class="text-secondary-light"> / Sessions Fixes</span>
      </div>
    </div>
    <div class="d-flex gap-10">
      <a href="<?= base_url('Horaires') ?>" class="btn btn-secondary-600 d-flex align-items-center gap-6">
        <i class="ri-arrow-left-line"></i> Retour
      </a>
      <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openAddSidebar()">
        <i class="ri-add-large-line"></i> Ajouter
      </button>
    </div>
  </div>

  <div class="mt-24">
    <div class="card h-100">
      <div class="card-body p-0">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
          <span class="text-secondary-light text-sm fw-semibold" id="fixeCount"></span>
          <button type="button" class="btn btn-danger-200 text-danger-600 d-flex align-items-center gap-6" onclick="clearAllFixes()">
            <i class="ri-delete-bin-line"></i> Vider tout
          </button>
        </div>
        <div class="table-responsive">
          <table class="table mb-0" id="fixeTable" style="width:100%">
            <thead>
              <tr>
                <th class="text-center" style="width:5%">#</th>
                <th>Classe</th>
                <th>Matiere</th>
                <th>Enseignant</th>
                <th>Jour</th>
                <th>Creneau</th>
                <th class="text-center" style="width:8%">Actions</th>
              </tr>
            </thead>
            <tbody id="dataBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="sidebarOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="addSidebar" style="width:50vw;max-width:50vw;box-shadow:-4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="sidebarTitle">Ajouter une session fixe</h5>
    <button type="button" class="btn-close" onclick="closeSidebar()"></button>
  </div>
  <form id="mainForm" class="d-flex flex-column p-20">
    <div class="row g-3">
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe *</label>
        <select id="id_classe" class="form-select form-control" onchange="loadMatieres(this.value)">
          <option value="">-- Choisir une classe --</option>
          <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id_classe'] ?>"><?= htmlspecialchars($c['libelle']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Matiere *</label>
        <select id="id_matiere_classe" class="form-select form-control" onchange="onMatiereChange(this.value)">
          <option value="">-- Choisir d'abord une classe --</option>
        </select>
      </div>
      <div class="col-12">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Enseignant *</label>
        <input type="hidden" id="id_enseignant">
        <input type="text" class="form-control" id="id_enseignant_search" placeholder="Rechercher un enseignant..." autocomplete="off" readonly>
        <small id="enseignantInfo" class="text-secondary-light"></small>
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Jour *</label>
        <select id="id_jour" class="form-select form-control">
          <option value="">-- Choisir --</option>
          <?php foreach ($jours as $j): ?>
            <option value="<?= $j['id_jour'] ?>"><?= htmlspecialchars($j['libelle']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Creneau *</label>
        <select id="id_creneau" class="form-select form-control">
          <option value="">-- Choisir --</option>
          <?php foreach ($creneaux as $cr): ?>
            <?php if (($cr['type'] ?? '') === 'cours'): ?>
              <option value="<?= $cr['id_creneau'] ?>"><?= htmlspecialchars($cr['libelle']) ?> (<?= $cr['heure_debut'] ?> - <?= $cr['heure_fin'] ?>)</option>
            <?php endif; ?>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12">
        <input type="hidden" id="id_enseignement" value="">
        <button type="submit" class="btn btn-primary-600 w-100"><i class="ri-save-line"></i> Enregistrer</button>
      </div>
    </div>
  </form>
</div>

<script>var BASE_URL = '<?= base_url() ?>';</script>
<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

let allMatieresClasses = [];
let allEnseignements = [];

async function loadData() {
  const res = await API.get('api/horaires/fixes');
  if (!res.success) { $('#dataBody').html('<tr><td colspan="7" class="text-center text-danger">Erreur</td></tr>'); return; }
  const data = res.data || [];
  $('#fixeCount').text(data.length + ' session(s) fixe(s)');
  if (!data.length) { $('#dataBody').html('<tr><td colspan="7" class="text-center text-secondary-light py-20">Aucune session fixe. Ajoutez des sessions manuellement avant de generer.</td></tr>'); return; }
  let rows = '';
  data.forEach((d, i) => {
    rows += '<tr>';
    rows += '<td class="text-center">' + (i + 1) + '</td>';
    rows += '<td><span class="fw-semibold">' + (d.classe_libelle || '-') + '</span></td>';
    rows += '<td>' + (d.matiere_libelle || '-') + '</td>';
    rows += '<td>' + (d.enseignant || '-') + '</td>';
    rows += '<td>' + (d.jour_libelle || '-') + '</td>';
    rows += '<td>Cours ' + d.id_creneau + '</td>';
    rows += '<td class="text-center"><button class="btn btn-sm btn-outline-danger" onclick="deleteFixe(\'' + d.uuid + '\')"><i class="ri-delete-bin-line"></i></button></td>';
    rows += '</tr>';
  });
  $('#dataBody').html(rows);
}

function openAddSidebar() {
  document.getElementById('mainForm').reset();
  document.getElementById('id_enseignant').value = '';
  document.getElementById('id_enseignant_search').value = '';
  document.getElementById('enseignantInfo').textContent = '';
  document.getElementById('id_matiere_classe').innerHTML = '<option value="">-- Choisir d\'abord une classe --</option>';
  document.getElementById('addSidebar').classList.add('active');
  document.getElementById('sidebarOverlay').classList.add('active');
}

function closeSidebar() {
  document.getElementById('addSidebar').classList.remove('active');
  document.getElementById('sidebarOverlay').classList.remove('active');
}
document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);

async function loadMatieres(idClasse) {
  const sel = document.getElementById('id_matiere_classe');
  sel.innerHTML = '<option value="">Chargement...</option>';
  document.getElementById('id_enseignant').value = '';
  document.getElementById('id_enseignant_search').value = '';
  document.getElementById('enseignantInfo').textContent = '';
  if (!idClasse) { sel.innerHTML = '<option value="">-- Choisir d\'abord une classe --</option>'; return; }
  const res = await API.get('api/horaires/matieres/' + idClasse);
  if (res.success && res.data) {
    allMatieresClasses = res.data;
    sel.innerHTML = '<option value="">-- Choisir --</option>';
    res.data.forEach(mc => {
      sel.innerHTML += '<option value="' + mc.id_matiere_classe + '" data-ens="' + (mc.id_enseignant || '') + '" data-ensname="' + (mc.enseignant || '') + '" data-idmatiere="' + (mc.id_matiere || '') + '">' + (mc.matiere_libelle || 'Matiere') + ' (' + (mc.matiere_code || '') + ')</option>';
    });
  } else {
    sel.innerHTML = '<option value="">Aucune matiere</option>';
  }
}

function onMatiereChange(val) {
  const opt = document.getElementById('id_matiere_classe').selectedOptions[0];
  if (opt && opt.dataset.ens) {
    document.getElementById('id_enseignant').value = opt.dataset.ens;
    document.getElementById('id_enseignant_search').value = opt.dataset.ensname;
    document.getElementById('enseignantInfo').textContent = 'Assigne automatiquement: ' + opt.dataset.ensname;
  } else {
    document.getElementById('id_enseignant').value = '';
    document.getElementById('id_enseignant_search').value = '';
    document.getElementById('enseignantInfo').textContent = '';
  }
}

document.getElementById('mainForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id_classe = parseInt(document.getElementById('id_classe').value) || 0;
  const id_matiere_classe = parseInt(document.getElementById('id_matiere_classe').value) || 0;
  const id_enseignant = parseInt(document.getElementById('id_enseignant').value) || 0;
  const id_jour = parseInt(document.getElementById('id_jour').value) || 0;
  const id_creneau = parseInt(document.getElementById('id_creneau').value) || 0;

  if (!id_classe || !id_matiere_classe || !id_enseignant || !id_jour || !id_creneau) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Tous les champs sont obligatoires' });
    return;
  }

  const r = await API.post('api/horaires/fixes/create', {
    id_classe, id_matiere_classe, id_enseignant, id_jour, id_creneau
  });

  if (r && r.success) {
    closeSidebar();
    Toast.fire({ icon: 'success', title: r.message || 'Session fixe creee' });
    loadData();
  } else {
    Swal.fire({ icon: 'error', title: 'Erreur', text: r ? r.message : 'Erreur inconnue' });
  }
});

async function deleteFixe(uuid) {
  const result = await Swal.fire({ title: 'Supprimer?', text: 'Cette session fixe sera supprimee.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Oui, supprimer', cancelButtonText: 'Annuler' });
  if (!result.isConfirmed) return;
  const r = await API.get('api/horaires/fixes/' + uuid + '/delete');
  if (r && r.success) { Toast.fire({ icon: 'success', title: 'Supprimee' }); loadData(); }
  else { Swal.fire({ icon: 'error', title: 'Erreur', text: r ? r.message : 'Erreur' }); }
}

async function clearAllFixes() {
  const result = await Swal.fire({ title: 'Vider toutes les fixes?', text: 'Toutes les sessions fixes seront supprimees.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Oui, vider', cancelButtonText: 'Annuler' });
  if (!result.isConfirmed) return;
  const r = await API.post('api/horaires/fixes/clear', {});
  if (r && r.success) { Toast.fire({ icon: 'success', title: 'Toutes les fixes supprimees' }); loadData(); }
  else { Swal.fire({ icon: 'error', title: 'Erreur', text: r ? r.message : 'Erreur' }); }
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && typeof API !== 'undefined') {
      clearInterval(wait);
      loadData();
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
