<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light"><?= $etudiant ? 'Modifier l\'élève' : 'Nouvel élève' ?></h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <a href="<?= base_url('Etudiants') ?>" class="text-secondary-light hover-text-primary hover-underline"> / Élèves</a>
        <span class="text-secondary-light"> / <?= $etudiant ? 'Modifier' : 'Nouveau' ?></span>
      </div>
    </div>
  </div>
  <form id="mainForm" class="mt-24">
    <input type="hidden" id="id_etudiant" value="<?= $etudiant['uuid'] ?? '' ?>">
    <div class="row gy-3">
      <div class="col-lg-6">
        <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
          <div class="card-header border-bottom bg-base py-16 px-24">
            <h6 class="text-lg fw-semibold mb-0">Informations Inscription</h6>
          </div>
          <div class="card-body p-20">
            <div class="row gy-3">
              <div class="col-12 position-relative">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Année scolaire</label>
                <input type="hidden" id="id_annee" value="<?= $etudiant['inscription']['id_annee'] ?? $this->id_annee_active ?>">
                <input type="text" class="form-control" id="id_annee_search" placeholder="Rechercher..." autocomplete="off"
                  value="<?php
                    $selectedAnneeId = $etudiant['inscription']['id_annee'] ?? $this->id_annee_active;
                    foreach ($annees as $a) { if ($a['id_annee'] == $selectedAnneeId) { echo $a['libelle']; break; } }
                  ?>">
                <div id="id_annee_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
              </div>
              <div class="col-sm-6 position-relative">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe <span class="text-danger-600">*</span></label>
                <input type="hidden" id="id_classe" value="<?= $etudiant['inscription']['id_classe'] ?? '' ?>">
                <input type="text" class="form-control" id="id_classe_search" placeholder="Rechercher..." autocomplete="off"
                  value="<?php
                    $selectedClasseId = $etudiant['inscription']['id_classe'] ?? '';
                    foreach ($classes as $c) { if ($c['id_classe'] == $selectedClasseId) { echo $c['libelle']; break; } }
                  ?>">
                <div id="id_classe_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
              </div>
              <div class="col-sm-6 position-relative">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Section</label>
                <input type="hidden" id="id_section" value="<?= $etudiant['inscription']['id_section'] ?? '' ?>">
                <input type="text" class="form-control" id="id_section_search" placeholder="Rechercher..." autocomplete="off"
                  value="<?php
                    $selectedSectionId = $etudiant['inscription']['id_section'] ?? '';
                    foreach ($sections as $s) { if ($s['id_section'] == $selectedSectionId) { echo $s['libelle']; break; } }
                  ?>">
                <div id="id_section_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
          <div class="card-header border-bottom bg-base py-16 px-24">
            <h6 class="text-lg fw-semibold mb-0">Informations Personnelles</h6>
          </div>
          <div class="card-body p-20">
            <div class="row gy-3">
              <div class="col-sm-12">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Photo de l'élève</label>
                <div class="drop-zone height-44-px p-4 d-flex justify-content-center align-items-center text-center fw-medium text-md cursor-pointer border border-neutral-400 radius-8 border-dashed bg-hover-neutral-200">
                  <span class="drop-zone__prompt">Cliquer pour télécharger une photo</span>
                  <input type="file" id="photoInput" class="drop-zone__input" accept="image/*">
                </div>
                <input type="hidden" id="photo" value="<?= $etudiant['photo'] ?? '' ?>">
                <?php if (!empty($etudiant['photo'])): ?>
                  <img src="<?= base_url($etudiant['photo']) ?>" class="mt-8 radius-8" style="max-width:100px;max-height:100px;">
                <?php endif; ?>
              </div>
              <div class="col-sm-12">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Adresse de l'élève</label>
                <textarea class="form-control" id="adresse" rows="2" placeholder="Adresse complète"><?= $etudiant['adresse'] ?? '' ?></textarea>
              </div>
              <div class="col-sm-12">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Nom complet <span class="text-danger-600">*</span></label>
                <input type="text" class="form-control" id="nom" value="<?= $etudiant['nom'] ?? '' ?>" placeholder="Nom">
              </div>
              <div class="col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Post-nom</label>
                <input type="text" class="form-control" id="postnom" value="<?= $etudiant['postnom'] ?? '' ?>" placeholder="Post-nom">
              </div>
              <div class="col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Prénom <span class="text-danger-600">*</span></label>
                <input type="text" class="form-control" id="prenom" value="<?= $etudiant['prenom'] ?? '' ?>" placeholder="Prénom">
              </div>
              <div class="col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Genre</label>
                <select id="sexe" class="form-control form-select">
                  <option value="">Sélectionner</option>
                  <option value="M" <?= ($etudiant['sexe'] ?? '') == 'M' ? 'selected' : '' ?>>Masculin</option>
                  <option value="F" <?= ($etudiant['sexe'] ?? '') == 'F' ? 'selected' : '' ?>>Féminin</option>
                </select>
              </div>
              <div class="col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date de naissance</label>
                <input type="date" class="form-control" id="date_naissance" value="<?= $etudiant['date_naissance'] ?? '' ?>">
              </div>
              <div class="col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Téléphone</label>
                <input type="text" class="form-control" id="telephone" value="<?= $etudiant['telephone'] ?? '' ?>" placeholder="Téléphone">
              </div>
              <div class="col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Email</label>
                <input type="email" class="form-control" id="email" value="<?= $etudiant['email'] ?? '' ?>" placeholder="Email">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-12">
        <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
          <div class="card-header border-bottom bg-base py-16 px-24">
            <h6 class="text-lg fw-semibold mb-0">Informations Parent / Tuteur</h6>
          </div>
          <div class="card-body p-20">
            <div class="row gy-3">
              <div class="col-lg-3 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Nom complet</label>
                <input type="text" class="form-control" id="parent_nom" value="<?= $etudiant['parent_nom'] ?? '' ?>" placeholder="Nom complet du parent/tuteur">
              </div>
              <div class="col-lg-3 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Téléphone</label>
                <input type="tel" class="form-control" id="parent_telephone" value="<?= $etudiant['parent_telephone'] ?? '' ?>" placeholder="Téléphone">
              </div>
              <div class="col-lg-3 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Occupation</label>
                <input type="text" class="form-control" id="parent_profession" value="<?= $etudiant['parent_profession'] ?? '' ?>" placeholder="Profession / Occupation">
              </div>
              <div class="col-lg-3 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Adresse</label>
                <input type="text" class="form-control" id="parent_adresse" value="<?= $etudiant['parent_adresse'] ?? '' ?>" placeholder="Adresse du parent">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
          <a href="<?= base_url('Etudiants') ?>" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8 text-decoration-none">Annuler</a>
          <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
        </div>
      </div>
    </div>
  </form>
</div>
<script src="<?= base_url() ?>assets/js/autocomplete.js"></script>
<script id="etudiants_annees_data" type="application/json"><?= json_encode($annees) ?></script>
<script id="etudiants_classes_data" type="application/json"><?= json_encode($classes) ?></script>
<script id="etudiants_sections_data" type="application/json"><?= json_encode($sections) ?></script>
<script src="<?= base_url() ?>assets/js/api.js?v=1"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

document.querySelector('.drop-zone').addEventListener('click', function() {
  document.getElementById('photoInput').click();
});
document.getElementById('photoInput').addEventListener('click', function(e) { e.stopPropagation(); });
document.getElementById('photoInput')?.addEventListener('change', async function(e) {
  if (!this.files.length) return;
  const file = this.files[0];
  const chunkSize = 1.5 * 1024 * 1024;
  const totalChunks = Math.ceil(file.size / chunkSize);
  const fileId = Math.random().toString(36).substring(2, 15) + Date.now().toString(36);

  try {
    for (let i = 0; i < totalChunks; i++) {
      const start = i * chunkSize;
      const end = Math.min(start + chunkSize, file.size);
      const chunk = file.slice(start, end);
      const fd = new FormData();
      fd.append('file', chunk, file.name);
      fd.append('file_id', fileId);
      fd.append('chunk_index', i);
      fd.append('total_chunks', totalChunks);
      fd.append('original_name', file.name);
      const res = await fetch(API.base_url + 'api/etudiants/upload_photo', {
        method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const data = await res.json();
      if (!data.success) {
        Swal.fire({ icon: 'error', title: 'Erreur Upload', text: data.message });
        return;
      }
      if (data.data?.completed) {
        document.getElementById('photo').value = data.data.path;
        let preview = document.getElementById('photoPreview');
        if (!preview) {
          preview = document.createElement('img');
          preview.id = 'photoPreview';
          preview.className = 'mt-8 radius-8';
          preview.style.cssText = 'max-width:100px;max-height:100px;object-fit:cover;';
          document.querySelector('.drop-zone').after(preview);
        }
        preview.src = BASE_URL + data.data.path;
        Toast.fire({ icon: 'success', title: 'Photo téléchargée' });
        return;
      }
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Échec Upload', text: err.message });
  }
});

document.getElementById('mainForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id = document.getElementById('id_etudiant').value;
  const data = {
    nom: document.getElementById('nom').value,
    postnom: document.getElementById('postnom').value,
    prenom: document.getElementById('prenom').value,
    date_naissance: document.getElementById('date_naissance').value,
    sexe: document.getElementById('sexe').value,
    telephone: document.getElementById('telephone').value,
    email: document.getElementById('email').value,
    adresse: document.getElementById('adresse').value,
    photo: document.getElementById('photo').value,
    id_classe: document.getElementById('id_classe').value || null,
    id_section: document.getElementById('id_section').value || null,
    id_annee: document.getElementById('id_annee').value || null,
    parent_nom: document.getElementById('parent_nom').value,
    parent_telephone: document.getElementById('parent_telephone').value,
    parent_profession: document.getElementById('parent_profession').value,
    parent_adresse: document.getElementById('parent_adresse').value
  };
  if (!data.nom || !data.prenom) {
    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Le nom et le prénom sont obligatoires' });
    return;
  }
  let res;
  if (id) res = await API.etudiants.update(id, data);
  else res = await API.etudiants.create(data);
  if (res.success) {
    await Toast.fire({ icon: 'success', title: res.message || (id ? 'Élève modifié' : 'Élève créé') });
    window.location.href = '<?= base_url('Etudiants') ?>';
  } else {
    Swal.fire({ icon: 'error', title: 'Erreur', text: res.message });
  }
});

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && $.fn && $.fn.DataTable && typeof API !== 'undefined') {
      clearInterval(wait);
      autoSetup('id_annee_search', 'id_annee', 'id_annee_results', JSON.parse(document.getElementById('etudiants_annees_data').textContent).map(function(a) { return {id: a.id_annee, libelle: a.libelle}; }), function(a) { return a.libelle; });
      autoSetup('id_classe_search', 'id_classe', 'id_classe_results', JSON.parse(document.getElementById('etudiants_classes_data').textContent).map(function(c) { return {id: c.id_classe, libelle: c.libelle}; }), function(c) { return c.libelle; });
      autoSetup('id_section_search', 'id_section', 'id_section_results', JSON.parse(document.getElementById('etudiants_sections_data').textContent).map(function(s) { return {id: s.id_section, libelle: s.libelle}; }), function(s) { return s.libelle; });
    }
  }, 50);
})();
</script>
