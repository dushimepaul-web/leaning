<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light"><?= $etudiant ? 'Edit Student' : 'Add New Student' ?></h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <a href="<?= base_url('Etudiants') ?>" class="text-secondary-light hover-text-primary hover-underline"> / Student</a>
        <span class="text-secondary-light"> / <?= $etudiant ? 'Edit Student' : 'Add New Student' ?></span>
      </div>
    </div>
  </div>
  <form id="mainForm" class="mt-24">
    <input type="hidden" id="id_etudiant" value="<?= $etudiant['uuid'] ?? '' ?>">
    <div class="row gy-3">
      <div class="col-lg-12">
        <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
          <div class="card-header border-bottom bg-base py-16 px-24">
            <h6 class="text-lg fw-semibold mb-0">Personal Info</h6>
          </div>
          <div class="card-body p-20">
            <div class="row gy-3">
              <div class="col-xxl-3 col-xl-4 col-sm-6 position-relative">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Academic Year</label>
                <input type="hidden" id="id_annee" value="<?= $etudiant['inscription']['id_annee'] ?? $this->id_annee_active ?>">
                <input type="text" class="form-control" id="id_annee_search" placeholder="Rechercher..." autocomplete="off"
                  value="<?php
                    $selectedAnneeId = $etudiant['inscription']['id_annee'] ?? $this->id_annee_active;
                    foreach ($annees as $a) { if ($a['id_annee'] == $selectedAnneeId) { echo $a['libelle']; break; } }
                  ?>">
                <div id="id_annee_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6 position-relative">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Class <span class="text-danger-600">*</span></label>
                <input type="hidden" id="id_classe" value="<?= $etudiant['inscription']['id_classe'] ?? '' ?>">
                <input type="text" class="form-control" id="id_classe_search" placeholder="Rechercher..." autocomplete="off"
                  value="<?php
                    $selectedClasseId = $etudiant['inscription']['id_classe'] ?? '';
                    foreach ($classes as $c) { if ($c['id_classe'] == $selectedClasseId) { echo $c['libelle']; break; } }
                  ?>">
                <div id="id_classe_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6 position-relative">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Section</label>
                <input type="hidden" id="id_section" value="<?= $etudiant['inscription']['id_section'] ?? '' ?>">
                <input type="text" class="form-control" id="id_section_search" placeholder="Rechercher..." autocomplete="off"
                  value="<?php
                    $selectedSectionId = $etudiant['inscription']['id_section'] ?? '';
                    foreach ($sections as $s) { if ($s['id_section'] == $selectedSectionId) { echo $s['libelle']; break; } }
                  ?>">
                <div id="id_section_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Full Name <span class="text-danger-600">*</span></label>
                <input type="text" class="form-control" id="nom" value="<?= $etudiant['nom'] ?? '' ?>" placeholder="Full Name">
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Post Name</label>
                <input type="text" class="form-control" id="postnom" value="<?= $etudiant['postnom'] ?? '' ?>" placeholder="Post Name">
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">First Name <span class="text-danger-600">*</span></label>
                <input type="text" class="form-control" id="prenom" value="<?= $etudiant['prenom'] ?? '' ?>" placeholder="First Name">
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Gender</label>
                <select id="sexe" class="form-control form-select">
                  <option value="">Select Gender</option>
                  <option value="M" <?= ($etudiant['sexe'] ?? '') == 'M' ? 'selected' : '' ?>>Male</option>
                  <option value="F" <?= ($etudiant['sexe'] ?? '') == 'F' ? 'selected' : '' ?>>Female</option>
                </select>
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Date Of Birth</label>
                <input type="date" class="form-control" id="date_naissance" value="<?= $etudiant['date_naissance'] ?? '' ?>">
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Phone Number</label>
                <input type="text" class="form-control" id="telephone" value="<?= $etudiant['telephone'] ?? '' ?>" placeholder="Phone Number">
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Email</label>
                <input type="email" class="form-control" id="email" value="<?= $etudiant['email'] ?? '' ?>" placeholder="Email">
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Student Photo</label>
                <div class="drop-zone height-44-px p-4 d-flex justify-content-center align-items-center text-center fw-medium text-md cursor-pointer border border-neutral-400 radius-8 border-dashed bg-hover-neutral-200">
                  <span class="drop-zone__prompt">Click to upload photo</span>
                  <input type="file" id="photoInput" class="drop-zone__input" accept="image/*">
                </div>
                <input type="hidden" id="photo" value="<?= $etudiant['photo'] ?? '' ?>">
                <?php if (!empty($etudiant['photo'])): ?>
                  <img src="<?= base_url($etudiant['photo']) ?>" class="mt-8 radius-8" style="max-width:100px;max-height:100px;">
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-12">
        <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
          <div class="card-header border-bottom bg-base py-16 px-24">
            <h6 class="text-lg fw-semibold mb-0">Parent & Guardian Info</h6>
          </div>
          <div class="card-body p-20">
            <div class="row gy-3">
              <div class="col-xxl-4 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Father's Name</label>
                <input type="text" class="form-control" id="pere_nom" value="<?= $etudiant['parents']['pere']['nom'] ?? '' ?>" placeholder="Father's Name">
              </div>
              <div class="col-xxl-4 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Father's Phone</label>
                <input type="tel" class="form-control" id="pere_telephone" value="<?= $etudiant['parents']['pere']['telephone'] ?? '' ?>" placeholder="Phone">
              </div>
              <div class="col-xxl-4 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Father's Occupation</label>
                <input type="text" class="form-control" id="pere_profession" value="<?= $etudiant['parents']['pere']['profession'] ?? '' ?>" placeholder="Occupation">
              </div>
              <div class="col-xxl-4 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Mother's Name</label>
                <input type="text" class="form-control" id="mere_nom" value="<?= $etudiant['parents']['mere']['nom'] ?? '' ?>" placeholder="Mother's Name">
              </div>
              <div class="col-xxl-4 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Mother's Phone</label>
                <input type="tel" class="form-control" id="mere_telephone" value="<?= $etudiant['parents']['mere']['telephone'] ?? '' ?>" placeholder="Phone">
              </div>
              <div class="col-xxl-4 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Mother's Occupation</label>
                <input type="text" class="form-control" id="mere_profession" value="<?= $etudiant['parents']['mere']['profession'] ?? '' ?>" placeholder="Occupation">
              </div>
              <div class="col-12 mt-16">
                <h6 class="text-md fw-semibold mb-8">Guardian</h6>
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Guardian Name</label>
                <input type="text" class="form-control" id="tuteur_nom" value="<?= $etudiant['parents']['tuteur']['nom'] ?? $etudiant['tuteur_nom'] ?? '' ?>" placeholder="Guardian Name">
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Relation</label>
                <select id="tuteur_relation" class="form-control form-select">
                  <option value="">Select relation</option>
                  <option value="Father" <?= ($etudiant['parents']['tuteur']['relation'] ?? '') == 'Father' ? 'selected' : '' ?>>Father</option>
                  <option value="Mother" <?= ($etudiant['parents']['tuteur']['relation'] ?? '') == 'Mother' ? 'selected' : '' ?>>Mother</option>
                  <option value="Uncle" <?= ($etudiant['parents']['tuteur']['relation'] ?? '') == 'Uncle' ? 'selected' : '' ?>>Uncle</option>
                  <option value="Aunt" <?= ($etudiant['parents']['tuteur']['relation'] ?? '') == 'Aunt' ? 'selected' : '' ?>>Aunt</option>
                  <option value="Brother" <?= ($etudiant['parents']['tuteur']['relation'] ?? '') == 'Brother' ? 'selected' : '' ?>>Brother</option>
                  <option value="Sister" <?= ($etudiant['parents']['tuteur']['relation'] ?? '') == 'Sister' ? 'selected' : '' ?>>Sister</option>
                  <option value="Grandparent" <?= ($etudiant['parents']['tuteur']['relation'] ?? '') == 'Grandparent' ? 'selected' : '' ?>>Grandparent</option>
                  <option value="Other" <?= ($etudiant['parents']['tuteur']['relation'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Guardian Email</label>
                <input type="email" class="form-control" id="tuteur_email" value="<?= $etudiant['parents']['tuteur']['email'] ?? '' ?>" placeholder="Email">
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Guardian Phone</label>
                <input type="tel" class="form-control" id="tuteur_telephone" value="<?= $etudiant['parents']['tuteur']['telephone'] ?? $etudiant['tuteur_telephone'] ?? '' ?>" placeholder="Phone">
              </div>
              <div class="col-xxl-3 col-xl-4 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Guardian Occupation</label>
                <input type="text" class="form-control" id="tuteur_profession" value="<?= $etudiant['parents']['tuteur']['profession'] ?? '' ?>" placeholder="Occupation">
              </div>
              <div class="col-xl-9 col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Guardian Address</label>
                <input type="text" class="form-control" id="tuteur_adresse" value="<?= $etudiant['parents']['tuteur']['adresse'] ?? '' ?>" placeholder="Address">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-12">
        <div class="shadow-1 radius-12 bg-base h-100 overflow-hidden">
          <div class="card-header border-bottom bg-base py-16 px-24">
            <h6 class="text-lg fw-semibold mb-0">Address</h6>
          </div>
          <div class="card-body p-20">
            <div class="row gy-3">
              <div class="col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Current Address</label>
                <textarea class="form-control" id="adresse" rows="2" placeholder="Current Address"><?= $etudiant['adresse'] ?? '' ?></textarea>
              </div>
              <div class="col-sm-6">
                <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Permanent Address</label>
                <textarea class="form-control" id="adresse_permanente" rows="2" placeholder="Permanent Address"><?= $etudiant['adresse_permanente'] ?? '' ?></textarea>
                <div class="form-check mt-8">
                  <input class="form-check-input" type="checkbox" id="sameAddress">
                  <label class="form-check-label text-sm" for="sameAddress">Same as current address</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
          <a href="<?= base_url('Etudiants') ?>" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8 text-decoration-none">Cancel</a>
          <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Save Changes</button>
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
        Swal.fire({ icon: 'error', title: 'Upload Error', text: data.message });
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
        Toast.fire({ icon: 'success', title: 'Photo uploaded (' + file.size + ' bytes)' });
        return;
      }
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Upload Failed', text: err.message });
  }
});

document.getElementById('sameAddress')?.addEventListener('change', function() {
  if (this.checked) {
    document.getElementById('adresse_permanente').value = document.getElementById('adresse').value;
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
    adresse_permanente: document.getElementById('adresse_permanente').value,
    photo: document.getElementById('photo').value,
    id_classe: document.getElementById('id_classe').value || null,
    id_section: document.getElementById('id_section').value || null,
    id_annee: document.getElementById('id_annee').value || null,
    parents: {
      pere: { nom: document.getElementById('pere_nom').value, telephone: document.getElementById('pere_telephone').value, profession: document.getElementById('pere_profession').value },
      mere: { nom: document.getElementById('mere_nom').value, telephone: document.getElementById('mere_telephone').value, profession: document.getElementById('mere_profession').value },
      tuteur: { nom: document.getElementById('tuteur_nom').value, relation: document.getElementById('tuteur_relation').value, email: document.getElementById('tuteur_email').value, telephone: document.getElementById('tuteur_telephone').value, profession: document.getElementById('tuteur_profession').value, adresse: document.getElementById('tuteur_adresse').value }
    }
  };
  if (!data.nom || !data.prenom) {
    Swal.fire({ icon: 'warning', title: 'Validation Error', text: 'Name and First Name are required' });
    return;
  }
  let res;
  if (id) res = await API.etudiants.update(id, data);
  else res = await API.etudiants.create(data);
  if (res.success) {
    await Toast.fire({ icon: 'success', title: res.message || (id ? 'Student updated' : 'Student created') });
    window.location.href = '<?= base_url('Etudiants') ?>';
  } else {
    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
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
