<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light"><?= $teacher ? 'Modifier l\'enseignant' : 'Ajouter un enseignant' ?></h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Tableau de bord</a>
        <a href="<?= base_url('Enseignants') ?>" class="text-secondary-light hover-text-primary hover-underline"> / Enseignants</a>
        <span class="text-secondary-light"> / <?= $teacher ? 'Modifier l\'enseignant' : 'Ajouter un enseignant' ?></span>
      </div>
    </div>
    <a href="<?= base_url('Enseignants') ?>" class="btn btn-outline-secondary d-flex align-items-center gap-6">
      <i class="ri-arrow-left-line"></i> Retour
    </a>
  </div>
  <form id="mainForm" class="mt-24">
    <input type="hidden" id="id_enseignant" value="<?= $teacher['uuid'] ?? '' ?>">
    <div class="row gy-3">
      <div class="col-lg-12">
        <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
          <div class="card-header border-bottom bg-base py-16 px-24">
            <h6 class="text-lg fw-semibold mb-0">Informations personnelles</h6>
          </div>
          <div class="card-body p-20">
            <div class="row gy-3">
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Matricule</label>
                <input type="text" class="form-control" id="matricule" value="<?= $teacher['matricule'] ?? '' ?>" placeholder="Matricule">
              </div>
              <div class="col-xxl-9 col-xl-8 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Nom complet <span class="text-danger-600">*</span></label>
                <input type="text" class="form-control" id="fullname" value="<?= $teacher['fullname'] ?? '' ?>" placeholder="Nom complet de l'enseignant">
              </div>
              <div class="col-12">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Matières et Classes</label>
                <div id="enseignementsContainer"></div>
                <button type="button" class="btn btn-sm btn-primary-600 mt-8" onclick="addEnseignement()">+ Ajouter une matière / classe</button>
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Sexe</label>
                <select id="sexe" class="form-control form-select">
                  <option value="">Sélectionner</option>
                  <option value="M" <?= ($teacher['sexe'] ?? '') == 'M' ? 'selected' : '' ?>>Masculin</option>
                  <option value="F" <?= ($teacher['sexe'] ?? '') == 'F' ? 'selected' : '' ?>>Féminin</option>
                </select>
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date de naissance</label>
                <input type="date" class="form-control" id="date_naissance" value="<?= $teacher['date_naissance'] ?? '' ?>">
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Qualification / Diplôme</label>
                <input type="text" class="form-control" id="qualification" value="<?= $teacher['qualification'] ?? '' ?>" placeholder="Diplôme / Qualification">
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Expérience</label>
                <input type="text" class="form-control" id="experience" value="<?= $teacher['experience'] ?? '' ?>" placeholder="Années d'expérience">
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date d'embauche</label>
                <input type="date" class="form-control" id="date_embauche" value="<?= $teacher['date_embauche'] ?? '' ?>">
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Numéro de téléphone</label>
                <input type="tel" class="form-control" id="telephone" value="<?= $teacher['telephone'] ?? '' ?>" placeholder="Numéro de téléphone">
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Email</label>
                <input type="email" class="form-control" id="email" value="<?= $teacher['email'] ?? '' ?>" placeholder="Email">
              </div>

            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-12">
        <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
          <div class="card-header border-bottom bg-base py-16 px-24">
            <h6 class="text-lg fw-semibold mb-0">Adresse</h6>
          </div>
          <div class="card-body p-20">
            <div class="row gy-3">
              <div class="col-sm-12">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Adresse</label>
                <textarea class="form-control" id="adresse" rows="2" placeholder="Adresse"><?= $teacher['adresse'] ?? '' ?></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
          <a href="<?= base_url('Enseignants') ?>" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8 text-decoration-none">Annuler</a>
          <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
        </div>
      </div>
    </div>
  </form>
</div>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script id="enseignementsData" type="application/json"><?= json_encode($teacher['enseignements'] ?? []) ?></script>
<script id="matieresData" type="application/json"><?= json_encode($matieres) ?></script>
<script id="classesData" type="application/json"><?= json_encode($classes) ?></script>
<script id="matieresClassesData" type="application/json"><?= json_encode($matieres_classes) ?></script>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

let matieresList = []; let classesList = []; let matieresClassesList = [];
try { const el = document.getElementById('matieresData'); if (el) matieresList = JSON.parse(el.textContent); } catch(e) {}
try { const el = document.getElementById('classesData'); if (el) classesList = JSON.parse(el.textContent); } catch(e) {}
try { const el = document.getElementById('matieresClassesData'); if (el) matieresClassesList = JSON.parse(el.textContent); } catch(e) {}

function renderSearchResults(container, items, query, labelFn, selectFn) {
  const q = (query || '').toLowerCase().trim();
  const matches = q ? items.filter(i => labelFn(i).toLowerCase().includes(q)) : items;
  if (!matches.length) {
    container.innerHTML = '<div class="list-group-item text-secondary-light text-center py-2 text-sm">Aucun résultat</div>';
  } else {
    container.innerHTML = matches.slice(0, 20).map(i =>
      `<button type="button" class="list-group-item list-group-item-action text-start py-1 px-3 border-0 border-bottom border-neutral-100 text-sm" data-id="${i.id_matiere || i.id_classe}">${labelFn(i)}</button>`
    ).join('');
  }
  container.style.display = 'block';
}

function setupAutocomplete(inputId, hiddenId, resultsId, items, labelFn, onSelect) {
  const input = document.getElementById(inputId);
  const hidden = document.getElementById(hiddenId);
  const results = document.getElementById(resultsId);
  if (!input) return;

  input.addEventListener('focus', function() {
    if (!hidden.value) renderSearchResults(results, items, this.value, labelFn);
  });
  input.addEventListener('input', function() {
    hidden.value = '';
    this.classList.remove('border-success', 'border-2');
    renderSearchResults(results, items, this.value, labelFn);
  });
  input.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
      const btns = results.querySelectorAll('button');
      if (!btns.length) return; e.preventDefault();
      let idx = Array.from(btns).indexOf(document.activeElement);
      idx = e.key === 'ArrowDown' ? Math.min(idx + 1, btns.length - 1) : Math.max(idx - 1, 0);
      btns[idx].focus();
    }
    if (e.key === 'Escape') results.style.display = 'none';
  });
  results.addEventListener('click', function(e) {
    const btn = e.target.closest('button');
    if (!btn) return;
    hidden.value = btn.dataset.id;
    input.value = btn.textContent.trim();
    input.classList.add('border-success', 'border-2');
    results.style.display = 'none';
    if (onSelect) onSelect(btn.dataset.id, btn.textContent.trim());
  });
  document.addEventListener('click', function(e) {
    if (!e.target.closest('#' + inputId) && !e.target.closest('#' + resultsId)) {
      results.style.display = 'none';
    }
  });
}

let ensCounter = 0;

function addEnseignement(matiereId, classeId) {
  const container = document.getElementById('enseignementsContainer');
  const idx = ensCounter++;
  const div = document.createElement('div');
  div.className = 'row gy-2 mb-2 enseignement-row';
  const matiereItem = matieresList.find(m => String(m.id_matiere) === String(matiereId));
  const classeItem = classesList.find(c => String(c.id_classe) === String(classeId));
  div.innerHTML = `
    <div class="col-md-5 position-relative">
      <label class="text-xs fw-semibold text-secondary-light mb-4">Classe <span class="text-danger">*</span></label>
      <input type="hidden" class="ens-classe-id" id="ens_classe_${idx}" value="${classeId || ''}">
      <input type="text" class="form-control ens-classe-search" id="ens_classe_search_${idx}" placeholder="Rechercher classe..." value="${classeItem ? classeItem.libelle : ''}" autocomplete="off" ${classeItem ? 'style="border-color:var(--bs-success);border-width:2px;"' : ''}>
      <div class="list-group position-absolute z-99 w-100 shadow radius-4 border" id="ens_classe_results_${idx}" style="display:none;max-height:180px;overflow-y:auto;"></div>
    </div>
    <div class="col-md-6 position-relative">
      <label class="text-xs fw-semibold text-secondary-light mb-4">Matière <span class="text-danger">*</span></label>
      <input type="hidden" class="ens-matiere-id" id="ens_matiere_${idx}" value="${matiereId || ''}">
      <input type="text" class="form-control ens-matiere-search" id="ens_matiere_search_${idx}" placeholder="Sélectionnez d'abord une classe..." value="${matiereItem ? matiereItem.libelle : ''}" autocomplete="off" ${matiereItem ? 'style="border-color:var(--bs-success);border-width:2px;"' : ''} ${!classeId ? 'disabled' : ''}>
      <div class="list-group position-absolute z-99 w-100 shadow radius-4 border" id="ens_matiere_results_${idx}" style="display:none;max-height:180px;overflow-y:auto;"></div>
    </div>
    <div class="col-md-1 d-flex align-items-end">
      <button type="button" class="btn btn-danger-600 text-white w-100 h-40-px" onclick="this.closest('.enseignement-row').remove()" title="Supprimer"><i class="ri-close-line"></i></button>
    </div>
  `;
  container.appendChild(div);

  const matiereSearchInput = document.getElementById('ens_matiere_search_' + idx);
  const matiereHiddenId = document.getElementById('ens_matiere_' + idx);

  // Setup Classe autocomplete
  setupAutocomplete('ens_classe_search_' + idx, 'ens_classe_' + idx, 'ens_classe_results_' + idx,
    classesList, i => i.libelle, function(selectedClasseId) {
      // When classe is selected, enable matiere and filter matieres based on matieres_classes for this classe
      matiereSearchInput.disabled = false;
      matiereSearchInput.value = '';
      matiereHiddenId.value = '';
      matiereSearchInput.placeholder = 'Rechercher matière...';
      matiereSearchInput.classList.remove('border-success', 'border-2');
    });

  // Setup Matière autocomplete filtered by selected classe
  const matiereInput = document.getElementById('ens_matiere_search_' + idx);
  const matiereHidden = document.getElementById('ens_matiere_' + idx);
  const matiereResults = document.getElementById('ens_matiere_results_' + idx);

  if (matiereInput) {
    matiereInput.addEventListener('focus', function() {
      if (this.disabled) return;
      const cId = document.getElementById('ens_classe_' + idx).value;
      const allowedMatierIds = matieresClassesList.filter(mc => String(mc.id_classe) === String(cId)).map(mc => String(mc.id_matiere));
      const filteredMatieres = matieresList.filter(m => allowedMatierIds.includes(String(m.id_matiere)));
      if (!matiereHidden.value) renderSearchResults(matiereResults, filteredMatieres, this.value, i => i.libelle);
    });
    matiereInput.addEventListener('input', function() {
      if (this.disabled) return;
      matiereHidden.value = '';
      this.classList.remove('border-success', 'border-2');
      const cId = document.getElementById('ens_classe_' + idx).value;
      const allowedMatierIds = matieresClassesList.filter(mc => String(mc.id_classe) === String(cId)).map(mc => String(mc.id_matiere));
      const filteredMatieres = matieresList.filter(m => allowedMatierIds.includes(String(m.id_matiere)));
      renderSearchResults(matiereResults, filteredMatieres, this.value, i => i.libelle);
    });
    matiereResults.addEventListener('click', function(e) {
      const btn = e.target.closest('button');
      if (!btn) return;
      matiereHidden.value = btn.dataset.id;
      matiereInput.value = btn.textContent.trim();
      matiereInput.classList.add('border-success', 'border-2');
      matiereResults.style.display = 'none';
    });
  }
}

document.getElementById('mainForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id = document.getElementById('id_enseignant').value;
  const rows = document.querySelectorAll('.enseignement-row');
  const enseignements = [];
  let specialites = [];
  rows.forEach(row => {
    const mid = row.querySelector('.ens-matiere-id');
    const cid = row.querySelector('.ens-classe-id');
    if (mid && mid.value && cid && cid.value) {
      enseignements.push({
        id_matiere: mid.value,
        id_classe: cid.value
      });
      const mi = matieresList.find(m => String(m.id_matiere) === String(mid.value));
      if (mi) specialites.push(mi.libelle);
    }
  });
  const data = {
    fullname: document.getElementById('fullname').value,
    matricule: document.getElementById('matricule').value,
    sexe: document.getElementById('sexe').value,
    date_naissance: document.getElementById('date_naissance').value,
    telephone: document.getElementById('telephone').value,
    email: document.getElementById('email').value,
    adresse: document.getElementById('adresse').value,
    specialite: specialites.join(', '),
    qualification: document.getElementById('qualification').value,
    experience: document.getElementById('experience').value,
    enseignements: enseignements,
    date_embauche: document.getElementById('date_embauche').value,
  };
  if (!data.fullname) { Swal.fire({ icon: 'warning', title: 'Validation Error', text: 'Full Name is required' }); return; }
  let res;
  if (id) res = await API.enseignants.update(id, data);
  else res = await API.enseignants.create(data);
  if (res.success) { await Toast.fire({ icon: 'success', title: res.message || (id ? 'Teacher updated' : 'Teacher created') }); window.location.href = '<?= base_url('Enseignants') ?>'; }
  else { Swal.fire({ icon: 'error', title: 'Error', text: res.message }); }
});

document.querySelector('.drop-zone')?.addEventListener('click', function() {
  document.getElementById('photoInput')?.click();
});
document.getElementById('photoInput')?.addEventListener('click', function(e) { e.stopPropagation(); });
document.getElementById('photoInput')?.addEventListener('change', async function(e) {
  if (!this.files.length) return;
  const file = this.files[0];
  const fd = new FormData();
  fd.append('file', file, file.name);
  try {
    const res = await fetch(API.base_url + 'api/enseignants/upload_photo', {
      method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await res.json();
    if (data.success) {
      document.getElementById('photo').value = data.data.path;
      let preview = document.getElementById('photoPreview');
      if (!preview) {
        preview = document.createElement('img');
        preview.id = 'photoPreview';
        preview.className = 'mt-8 radius-8';
        preview.style.cssText = 'max-width:100px;max-height:100px;object-fit:cover;';
        document.querySelector('.drop-zone').after(preview);
      }
      preview.src = '<?= base_url() ?>' + data.data.path;
      Toast.fire({ icon: 'success', title: 'Photo uploaded' });
    } else {
      Swal.fire({ icon: 'error', title: 'Upload Error', text: data.message });
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Upload Failed', text: err.message });
  }
});

(function() {
  try {
    const el = document.getElementById('enseignementsData');
    if (el) {
      const ens = JSON.parse(el.textContent);
      if (ens && ens.length) {
        ens.forEach(e => addEnseignement(e.id_matiere, e.id_classe, e.coefficient, e.nb_heures_par_jour, e.nb_heures_par_semaine));
        return;
      }
    }
  } catch(e) {}
  addEnseignement();
})();
</script>
