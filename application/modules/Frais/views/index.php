<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Frais & Paiements</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Frais</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-info-600 d-flex align-items-center gap-6" onclick="openTypeSidebar()">
        <span class="d-flex text-md"><i class="ri-add-line"></i></span> Type de frais
      </button>
      <button type="button" class="btn btn-warning-600 d-flex align-items-center gap-6" onclick="openFraisSidebar()">
        <span class="d-flex text-md"><i class="ri-price-tag-3-line"></i></span> Nouveau frais
      </button>
      <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="openPaiementSidebar()">
        <span class="d-flex text-md"><i class="ri-add-large-line"></i></span> Nouveau paiement
      </button>
    </div>
  </div>
  <div class="mt-24">
    <ul class="nav nav-tabs mb-24" id="fraisTabs">
      <li class="nav-item"><button class="nav-link active" id="tab-types" data-bs-toggle="tab" data-bs-target="#tabTypes">Types de frais</button></li>
      <li class="nav-item"><button class="nav-link" id="tab-frais" data-bs-toggle="tab" data-bs-target="#tabFrais">Frais</button></li>
      <li class="nav-item"><button class="nav-link" id="tab-paiements" data-bs-toggle="tab" data-bs-target="#tabPaiements">Paiements</button></li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane fade show active" id="tabTypes">
        <div class="card h-100">
          <div class="card-body p-0 dataTable-wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
              <div class="d-flex flex-wrap align-items-center gap-16">
                <div class="dropdown">
                  <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-flex align-items-center gap-1 text-secondary-light text-sm"><i class="ri-file-upload-line text-md line-height-1"></i> Export</span>
                    <span><i class="ri-arrow-down-s-line"></i></span>
                  </button>
                  <ul class="dropdown-menu p-12 border bg-base shadow">
                    <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export PDF',text:'Fonctionnalité à venir'})"><i class="ri-file-3-line"></i> PDF</button></li>
                    <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export Excel',text:'Fonctionnalité à venir'})"><i class="ri-file-excel-line"></i> Excel</button></li>
                    <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="exportCSV()"><i class="ri-file-excel-line"></i> CSV</button></li>
                  </ul>
                </div>
                <form class="navbar-search dt-search m-0">
                  <input type="text" id="dtSearch1" class="dt-input bg-transparent radius-4" aria-controls="typesTable" name="search" placeholder="Rechercher...">
                  <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
                <div class="dropdown">
                  <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">Filtrer</span>
                    <span class=""><i class="ri-arrow-down-s-line"></i></span>
                  </button>
                  <div class="dropdown-menu border bg-base shadow dropdown-menu-lg p-0">
                    <div class="d-flex align-items-center justify-content-between border-bottom py-8 px-16">
                      <span class="fw-semibold text-lg text-primary-light">Filtre</span>
                    </div>
                    <form class="p-16 d-grid grid-cols-2 gap-16">
                      <div>
                        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Recherche</label>
                        <input type="text" id="filterSearch1" class="form-control" placeholder="Mot-clé...">
                      </div>
                      <div><button type="reset" class="btn btn-danger-200 text-danger-600 w-100" onclick="document.getElementById('filterSearch1').value='';$('#typesTable').DataTable().search('').draw()">Réinitialiser</button></div>
                      <div><button type="button" class="btn btn-primary-600 w-100" onclick="$('#typesTable').DataTable().search(document.getElementById('filterSearch1').value).draw()">Appliquer</button></div>
                    </form>
                  </div>
                </div>
              </div>
              <div class="d-flex align-items-center gap-8 text-secondary-light">
                <span>Lignes par page :</span>
                <div class="dt-length"><select id="dtLength1" name="typesTable_length" aria-controls="typesTable" class="dt-input form-control form-select">
                  <option value="5">5</option>
                  <option value="10" selected>10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select></div>
              </div>
            </div>
            <table class="table bordered-table mb-0 data-table" id="typesTable" data-page-length='10' style="width:100%">
              <thead>
                <tr>
                  <th scope="col"><div class="form-check style-check d-flex align-items-center"><input class="form-check-input" type="checkbox"><label class="form-check-label">S.L</label></div></th>
                  <th>Code</th>
                  <th>Libellé</th>
                  <th>Description</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="typesBody"></tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="tabFrais">
        <div class="card h-100">
          <div class="card-body p-0 dataTable-wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
              <div class="d-flex flex-wrap align-items-center gap-16">
                <form class="navbar-search dt-search m-0">
                  <input type="text" id="dtSearch3" class="dt-input bg-transparent radius-4" aria-controls="fraisTable" name="search" placeholder="Rechercher...">
                  <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
              </div>
              <div class="d-flex align-items-center gap-8 text-secondary-light">
                <span>Lignes par page :</span>
                <div class="dt-length"><select id="dtLength3" name="fraisTable_length" aria-controls="fraisTable" class="dt-input form-control form-select">
                  <option value="5">5</option>
                  <option value="10" selected>10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select></div>
              </div>
            </div>
            <table class="table bordered-table mb-0 data-table" id="fraisTable" data-page-length='10' style="width:100%">
              <thead>
                <tr>
                  <th scope="col"><div class="form-check style-check d-flex align-items-center"><input class="form-check-input" type="checkbox"><label class="form-check-label">S.L</label></div></th>
                  <th>Type</th>
                  <th>Classe</th>
                  <th>Année</th>
                  <th>Montant</th>
                  <th>Échéance</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="fraisBody"></tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="tabPaiements">
        <div class="card h-100">
          <div class="card-body p-0 dataTable-wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
              <div class="d-flex flex-wrap align-items-center gap-16">
                <div class="dropdown">
                  <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-flex align-items-center gap-1 text-secondary-light text-sm"><i class="ri-file-upload-line text-md line-height-1"></i> Export</span>
                    <span><i class="ri-arrow-down-s-line"></i></span>
                  </button>
                  <ul class="dropdown-menu p-12 border bg-base shadow">
                    <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export PDF',text:'Fonctionnalité à venir'})"><i class="ri-file-3-line"></i> PDF</button></li>
                    <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="Swal.fire({icon:'info',title:'Export Excel',text:'Fonctionnalité à venir'})"><i class="ri-file-excel-line"></i> Excel</button></li>
                    <li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="exportCSV()"><i class="ri-file-excel-line"></i> CSV</button></li>
                  </ul>
                </div>
                <form class="navbar-search dt-search m-0">
                  <input type="text" id="dtSearch2" class="dt-input bg-transparent radius-4" aria-controls="paiementsTable" name="search" placeholder="Rechercher...">
                  <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
                <div class="dropdown">
                  <button type="button" class="px-12 py-5-px border border-neutral-300 radius-8 d-flex align-items-center gap-20" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-flex align-items-center gap-1 text-secondary-light text-sm">Filtrer</span>
                    <span class=""><i class="ri-arrow-down-s-line"></i></span>
                  </button>
                  <div class="dropdown-menu border bg-base shadow dropdown-menu-lg p-0">
                    <div class="d-flex align-items-center justify-content-between border-bottom py-8 px-16">
                      <span class="fw-semibold text-lg text-primary-light">Filtre</span>
                    </div>
                    <form class="p-16 d-grid grid-cols-2 gap-16">
                      <div>
                        <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Recherche</label>
                        <input type="text" id="filterSearch2" class="form-control" placeholder="Mot-clé...">
                      </div>
                      <div><button type="reset" class="btn btn-danger-200 text-danger-600 w-100" onclick="document.getElementById('filterSearch2').value='';$('#paiementsTable').DataTable().search('').draw()">Réinitialiser</button></div>
                      <div><button type="button" class="btn btn-primary-600 w-100" onclick="$('#paiementsTable').DataTable().search(document.getElementById('filterSearch2').value).draw()">Appliquer</button></div>
                    </form>
                  </div>
                </div>
              </div>
              <div class="d-flex align-items-center gap-8 text-secondary-light">
                <span>Lignes par page :</span>
                <div class="dt-length"><select id="dtLength2" name="paiementsTable_length" aria-controls="paiementsTable" class="dt-input form-control form-select">
                  <option value="5">5</option>
                  <option value="10" selected>10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select></div>
              </div>
            </div>
            <table class="table bordered-table mb-0 data-table" id="paiementsTable" data-page-length='10' style="width:100%">
              <thead>
                <tr>
                  <th scope="col"><div class="form-check style-check d-flex align-items-center"><input class="form-check-input" type="checkbox"><label class="form-check-label">S.L</label></div></th>
                  <th>Étudiant</th>
                  <th>Matricule</th>
                  <th>Type</th>
                  <th>Montant</th>
                  <th>Date</th>
                  <th>Réf.</th>
                </tr>
              </thead>
              <tbody id="paiementsBody"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="typeOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="typeSidebar" style="width:50vw;max-width:50vw;box-shadow: -4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="typeSidebarTitle">Ajouter un type de frais</h5>
    <button type="button" class="btn-close" onclick="closeTypeSidebar()"></button>
  </div>
  <form id="typeForm" class="d-flex flex-column p-20">
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Libellé *</label>
      <input type="text" class="form-control" id="typeLibelle" placeholder="Ex: Minerval">
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Code</label>
      <input type="text" class="form-control" id="typeCode" placeholder="Ex: MINERVAL">
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Description</label>
      <textarea class="form-control" id="typeDescription" rows="2"></textarea>
    </div>
    <div class="col-12">
      <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
        <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeTypeSidebar()">Annuler</button>
        <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
      </div>
    </div>
  </form>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="fraisOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="fraisSidebar" style="width:50vw;max-width:50vw;box-shadow: -4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="fraisSidebarTitle">Ajouter un frais</h5>
    <button type="button" class="btn-close" onclick="closeFraisSidebar()"></button>
  </div>
  <form id="fraisForm" class="d-flex flex-column p-20">
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type de frais *</label>
      <select class="form-control form-select" id="fraisType">
        <option value="">-- Sélectionner --</option>
        <?php foreach ($types_frais as $t): ?>
        <option value="<?= $t['id_type_frais'] ?>"><?= htmlspecialchars($t['libelle']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Section *</label>
      <select class="form-control form-select" id="fraisSection">
        <option value="">-- Sélectionner --</option>
        <?php foreach ($sections as $s): ?>
        <option value="<?= $s['id_section'] ?>"><?= htmlspecialchars($s['libelle']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Classe *</label>
      <select class="form-control form-select" id="fraisClasse">
        <option value="">-- Sélectionnez d'abord une section --</option>
        <?php foreach ($classes as $c): ?>
        <option value="<?= $c['id_classe'] ?>" data-section="<?= $c['id_section'] ?? '' ?>"><?= htmlspecialchars($c['libelle']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Montant *</label>
      <input type="number" class="form-control" id="fraisMontant" step="0.01" min="0" placeholder="0.00">
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Échéance</label>
      <input type="date" class="form-control" id="fraisEcheance">
    </div>
    <div class="col-12">
      <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
        <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closeFraisSidebar()">Annuler</button>
        <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
      </div>
    </div>
  </form>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300" id="paiementOverlay"></div>
<div class="bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 w-100 translate-x-full duration-300 active-translate-0" id="paiementSidebar" style="width:50vw;max-width:50vw;box-shadow: -4px 0 20px rgba(0,0,0,0.1);">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0" id="paiementSidebarTitle">Nouveau paiement</h5>
    <button type="button" class="btn-close" onclick="closePaiementSidebar()"></button>
  </div>
  <form id="paiementForm" class="d-flex flex-column p-20">
    <div class="mb-3 position-relative">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Étudiant *</label>
      <input type="hidden" id="payEtudiant">
      <div class="position-relative">
        <input type="text" class="form-control" id="payEtudiant_search" placeholder="Rechercher..." autocomplete="off">
      </div>
      <div id="payEtudiant_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
    </div>
    <div class="mb-3 position-relative">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Type de frais</label>
      <input type="hidden" id="payType">
      <div class="position-relative">
        <input type="text" class="form-control" id="payType_search" placeholder="Rechercher..." autocomplete="off">
      </div>
      <div id="payType_results" class="list-group position-absolute z-99 w-100 shadow radius-8 border" style="display:none;max-height:200px;overflow-y:auto;"></div>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Montant *</label>
      <input type="number" class="form-control" id="payMontant" step="0.01" placeholder="0.00">
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Mode de paiement</label>
      <select class="form-control form-select" id="payMode">
        <option value="especes">Espèces</option>
        <option value="banque">Banque</option>
        <option value="mobile_money">Mobile Money</option>
        <option value="cheque">Chèque</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="text-sm fw-semibold text-primary-light d-inline-block mb-8">Référence</label>
      <input type="text" class="form-control" id="payReference" placeholder="N° de transaction">
    </div>
    <div class="col-12">
      <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
        <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8" onclick="closePaiementSidebar()">Annuler</button>
        <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8">Enregistrer</button>
      </div>
    </div>
  </form>
</div>

<script id="etudiants_data" type="application/json"><?= json_encode($etudiants) ?></script>
<script id="types_frais_data" type="application/json"><?= json_encode($types_frais) ?></script>
<script id="frais_data" type="application/json"><?= json_encode($frais) ?></script>
<script id="sections_data" type="application/json"><?= json_encode($sections) ?></script>
<script id="classes_data" type="application/json"><?= json_encode($classes) ?></script>
<script src="<?= base_url() ?>assets/js/autocomplete.js?v=<?= filemtime(FCPATH.'assets/js/autocomplete.js') ?>"></script>
<script src="<?= base_url() ?>assets/js/api.js"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
let BASE_URL = '<?= base_url() ?>';

let typesData = [];
let paiementsData = [];
let etudiantsData = [];
let typesFraisData = [];
let fraisData = [];
let sectionsData = [];
let classesData = [];
try { const el = document.getElementById('etudiants_data'); if (el) etudiantsData = JSON.parse(el.textContent); } catch(e) {}
try { const el = document.getElementById('types_frais_data'); if (el) typesFraisData = JSON.parse(el.textContent); } catch(e) {}
try { const el = document.getElementById('frais_data'); if (el) fraisData = JSON.parse(el.textContent); } catch(e) {}
try { const el = document.getElementById('sections_data'); if (el) sectionsData = JSON.parse(el.textContent); } catch(e) {}
try { const el = document.getElementById('classes_data'); if (el) classesData = JSON.parse(el.textContent); } catch(e) {}

function filterClassesBySection(sectionId) {
  const classSelect = document.getElementById('fraisClasse');
  classSelect.value = '';
  classSelect.querySelectorAll('option').forEach(function(opt) {
    if (!opt.value) return;
    opt.style.display = (!sectionId || String(opt.getAttribute('data-section')) === String(sectionId)) ? '' : 'none';
  });
}

let editingFraisUuid = null;

function openFraisSidebar() {
  editingFraisUuid = null;
  document.getElementById('fraisForm').reset();
  document.getElementById('fraisSidebarTitle').textContent = 'Ajouter un frais';
  filterClassesBySection('');
  document.getElementById('fraisOverlay').classList.remove('visibility-hidden', 'opacity-0');
  document.getElementById('fraisSidebar').classList.remove('translate-x-full');
}

function editFraisSidebar(uuid) {
  const f = fraisData.find(function(x) { return x.uuid === uuid; });
  if (!f) return;
  editingFraisUuid = uuid;
  document.getElementById('fraisForm').reset();
  document.getElementById('fraisSidebarTitle').textContent = 'Modifier un frais';
  document.getElementById('fraisType').value = f.id_type_frais || '';
  var cls = classesData.find(function(c) { return String(c.id_classe) === String(f.id_classe); });
  if (cls) document.getElementById('fraisSection').value = cls.id_section || '';
  filterClassesBySection(cls ? cls.id_section : '');
  document.getElementById('fraisClasse').value = f.id_classe || '';
  document.getElementById('fraisMontant').value = f.montant || '';
  document.getElementById('fraisEcheance').value = f.echeance || '';
  document.getElementById('fraisOverlay').classList.remove('visibility-hidden', 'opacity-0');
  document.getElementById('fraisSidebar').classList.remove('translate-x-full');
}

function closeFraisSidebar() {
  document.getElementById('fraisOverlay').classList.add('visibility-hidden', 'opacity-0');
  document.getElementById('fraisSidebar').classList.add('translate-x-full');
}

async function loadFrais() {
  const res = await API.frais.list();
  if (!res.success) { $('#fraisBody').html('<tr><td colspan="7" class="text-center text-danger">Erreur</td></tr>'); return; }
  fraisData = res.data;
  let rows = '';
  fraisData.forEach((f, i) => {
    rows += `<tr>
      <td>${i + 1}</td>
      <td><span class="fw-semibold">${f.type_libelle || '-'}</span></td>
      <td>${f.classe_libelle || '-'}</td>
      <td>${f.annee_libelle || '-'}</td>
      <td><strong>${parseFloat(f.montant || 0).toLocaleString()}</strong></td>
      <td>${f.echeance || '-'}</td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editFraisSidebar('${f.uuid}')"><i class="ri-edit-2-line"></i> Modifier</button></li>
            <li><button class="dropdown-item rounded text-danger-600 d-flex align-items-center gap-2 py-6" onclick="deleteFrais('${f.uuid}')"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li>
          </ul>
        </div>
      </td>
    </tr>`;
  });
  $('#fraisBody').html(rows);
  if ($.fn.DataTable.isDataTable('#fraisTable')) $('#fraisTable').DataTable().destroy();
  $('#fraisTable').DataTable({
    pageLength: 10, scrollX: true,
    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucun frais trouvé', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

async function deleteFrais(uuid) {
  const c = await Swal.fire({ icon: 'warning', title: 'Supprimer', text: 'Supprimer ce frais ?', showCancelButton: true, confirmButtonText: 'Supprimer', cancelButtonText: 'Annuler' });
  if (!c.isConfirmed) return;
  const r = await API.frais.delete(uuid);
  if (r.success) { Toast.fire({ icon: 'success', title: 'Frais supprimé' }); loadFrais(); }
  else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
}

async function saveFrais(e) {
  e.preventDefault();
  const data = {
    id_type_frais: document.getElementById('fraisType').value,
    id_classe: document.getElementById('fraisClasse').value,
    montant: document.getElementById('fraisMontant').value,
    echeance: document.getElementById('fraisEcheance').value || null
  };
  if (!data.id_type_frais || !data.id_classe || !data.montant) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Type, classe et montant obligatoires' }); return; }
  const r = editingFraisUuid ? await API.frais.update(editingFraisUuid, data) : await API.frais.create(data);
  if (r.success) {
    closeFraisSidebar();
    Toast.fire({ icon: 'success', title: editingFraisUuid ? 'Frais mis à jour' : 'Frais créé' });
    loadFrais();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
}

async function loadTypes() {
  const res = await API.types_frais.list();
  if (!res.success) { $('#typesBody').html('<tr><td colspan="6" class="text-center text-danger">Erreur</td></tr>'); return; }
  typesData = res.data;
  let rows = '';
  typesData.forEach((f, i) => {
    rows += `<tr>
      <td>${i + 1}</td>
      <td>${f.code || '-'}</td>
      <td><span class="fw-semibold">${f.libelle || '-'}</span></td>
      <td>${f.description || '-'}</td>
      <td>
        <div class="btn-group">
          <button type="button" class="text-primary-light text-xl" data-bs-toggle="dropdown"><iconify-icon icon="tabler:dots-vertical"></iconify-icon></button>
          <ul class="dropdown-menu dropdown-menu-lg-end border p-12">
            <li><button class="dropdown-item rounded text-secondary-light d-flex align-items-center gap-2 py-6" onclick="editTypeSidebar(${i})"><i class="ri-edit-2-line"></i> Modifier</button></li>
            <li><button class="dropdown-item rounded text-danger-600 d-flex align-items-center gap-2 py-6" onclick="deleteType('${f.uuid}')"><i class="ri-delete-bin-6-line"></i> Supprimer</button></li>
          </ul>
        </div>
      </td>
    </tr>`;
  });
  $('#typesBody').html(rows);
  if ($.fn.DataTable.isDataTable('#typesTable')) $('#typesTable').DataTable().destroy();
  $('#typesTable').DataTable({
    pageLength: 10, scrollX: true,
    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucun type de frais trouvé', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

async function deleteType(uuid) {
  const c = await Swal.fire({ icon: 'warning', title: 'Supprimer', text: 'Supprimer ce type de frais ?', showCancelButton: true, confirmButtonText: 'Supprimer', cancelButtonText: 'Annuler' });
  if (!c.isConfirmed) return;
  const r = await API.types_frais.delete(uuid);
  if (r.success) { Toast.fire({ icon: 'success', title: 'Type de frais supprimé' }); loadTypes(); }
  else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message });
}

async function loadPaiements() {
  const res = await API.paiements.list();
  if (!res.success) { $('#paiementsBody').html('<tr><td colspan="7" class="text-center text-danger">Erreur</td></tr>'); return; }
  paiementsData = res.data;
  let rows = '';
  paiementsData.forEach((p, i) => {
    rows += `<tr>
      <td>${i + 1}</td>
      <td><span class="fw-semibold">${p.nom} ${p.prenom}</span></td>
      <td>${p.matricule || '-'}</td>
      <td>${p.type_frais || '-'}</td>
      <td><strong>${parseFloat(p.montant || 0).toLocaleString()}</strong></td>
      <td>${p.date_paiement}</td>
      <td>${p.reference || '-'}</td>
    </tr>`;
  });
  $('#paiementsBody').html(rows);
  if ($.fn.DataTable.isDataTable('#paiementsTable')) $('#paiementsTable').DataTable().destroy();
  $('#paiementsTable').DataTable({
    pageLength: 10, scrollX: true,
    lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: 'Lignes par page: _MENU_', info: '', zeroRecords: 'Aucun paiement trouvé', infoEmpty: '', infoFiltered: '' },
    dom: 't<"d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-top border-neutral-200"<"d-flex align-items-center gap-8 text-secondary-light"i><"d-flex align-items-center gap-2"p>>'
  });
}

let editingTypeUuid = null;

function openTypeSidebar() {
  editingTypeUuid = null;
  document.getElementById('typeForm').reset();
  document.getElementById('typeSidebarTitle').textContent = 'Ajouter un type de frais';
  document.getElementById('typeOverlay').classList.remove('visibility-hidden', 'opacity-0');
  document.getElementById('typeSidebar').classList.remove('translate-x-full');
}

function editTypeSidebar(index) {
  const f = typesData[index];
  editingTypeUuid = f.uuid;
  document.getElementById('typeForm').reset();
  document.getElementById('typeSidebarTitle').textContent = 'Modifier un type de frais';
  document.getElementById('typeLibelle').value = f.libelle || '';
  document.getElementById('typeCode').value = f.code || '';
  document.getElementById('typeDescription').value = f.description || '';
  document.getElementById('typeOverlay').classList.remove('visibility-hidden', 'opacity-0');
  document.getElementById('typeSidebar').classList.remove('translate-x-full');
}

function closeTypeSidebar() {
  document.getElementById('typeOverlay').classList.add('visibility-hidden', 'opacity-0');
  document.getElementById('typeSidebar').classList.add('translate-x-full');
}

function openPaiementSidebar() {
  document.getElementById('paiementForm').reset();
  document.getElementById('paiementSidebarTitle').textContent = 'Nouveau paiement';
  document.getElementById('paiementOverlay').classList.remove('visibility-hidden', 'opacity-0');
  document.getElementById('paiementSidebar').classList.remove('translate-x-full');
}

function editPaiementSidebar(index) {
  const p = paiementsData[index];
  document.getElementById('paiementForm').reset();
  document.getElementById('paiementSidebarTitle').textContent = 'Modifier paiement';
  document.getElementById('payEtudiant').value = p.id_etudiant || '';
  document.getElementById('payType').value = p.id_type_frais || '';
  var _e = etudiantsData.find(function(x) { return String(x.id_etudiant) === String(p.id_etudiant); });
  if (_e) document.getElementById('payEtudiant_search').value = _e.nom + ' (' + (_e.matricule || '') + ')';
  var _t = typesFraisData.find(function(x) { return String(x.id_type_frais) === String(p.id_type_frais); });
  if (_t) document.getElementById('payType_search').value = _t.libelle;
  document.getElementById('payMontant').value = p.montant || '';
  document.getElementById('payMode').value = p.mode_paiement || 'especes';
  document.getElementById('payReference').value = p.reference || '';
  document.getElementById('paiementOverlay').classList.remove('visibility-hidden', 'opacity-0');
  document.getElementById('paiementSidebar').classList.remove('translate-x-full');
}

function closePaiementSidebar() {
  document.getElementById('paiementOverlay').classList.add('visibility-hidden', 'opacity-0');
  document.getElementById('paiementSidebar').classList.add('translate-x-full');
}

async function saveType(e) {
  e.preventDefault();
  const data = {
    libelle: document.getElementById('typeLibelle').value,
    code: document.getElementById('typeCode').value,
    description: document.getElementById('typeDescription').value
  };
  if (!data.libelle) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Libellé obligatoire' }); return; }
  const r = editingTypeUuid ? await API.types_frais.update(editingTypeUuid, data) : await API.types_frais.create(data);
  if (r.success) {
    closeTypeSidebar();
    Toast.fire({ icon: 'success', title: editingTypeUuid ? 'Type de frais mis à jour' : 'Type de frais créé' });
    loadTypes();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
}

async function savePaiement(e) {
  e.preventDefault();
  const data = {
    id_etudiant: document.getElementById('payEtudiant').value,
    id_frais: document.getElementById('payType').value,
    montant: document.getElementById('payMontant').value,
    mode_paiement: document.getElementById('payMode').value,
    reference: document.getElementById('payReference').value
  };
  if (!data.id_etudiant || !data.montant || !data.id_frais) { Swal.fire({ icon: 'warning', title: 'Validation', text: 'Étudiant, frais et montant obligatoires' }); return; }
  const r = await API.paiements.create(data);
  if (r.success) {
    closePaiementSidebar();
    if (r.data && r.data.numero_recu) {
      const result = await Swal.fire({
        icon: 'success', title: 'Paiement enregistré',
        text: 'Reçu N° ' + r.data.numero_recu + ' généré automatiquement',
        showCancelButton: true, confirmButtonText: 'Imprimer le reçu', cancelButtonText: 'Fermer'
      });
      if (result.isConfirmed) { window.open(BASE_URL + 'Recus/imprimer/' + r.data.recu_uuid, '_blank'); }
    } else {
      Toast.fire({ icon: 'success', title: 'Paiement enregistré' });
    }
    loadPaiements();
  } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
}

function exportCSV() {
  const table = $('#typesTable').DataTable();
  const data = table.rows({ filter: 'applied' }).data();
  let csv = '\uFEFF';
  const headers = ['#', 'Code', 'Libellé', 'Description'];
  csv += headers.join(',') + '\n';
  data.each(function(row) {
    const cols = [];
    for (let i = 1; i < 5; i++) {
      let val = $(row[i]).text().trim() || row[i] || '';
      val = '"' + val.replace(/"/g, '""') + '"';
      cols.push(val);
    }
    csv += cols.join(',') + '\n';
  });
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'types_frais.csv';
  link.click();
}

(function() {
  var _f = function() {
    if (typeof $ === 'undefined' || typeof API === 'undefined' || typeof autoSetup === 'undefined') { setTimeout(_f, 50); return; }
    loadTypes();
    loadPaiements();
    loadFrais();
    var payEtudiantCtrl = autoSetup('payEtudiant_search', 'payEtudiant', 'payEtudiant_results', etudiantsData.map(function(e) { return { id: e.id_etudiant, nom: e.fullname || e.nom, prenom: e.prenom || '', matricule: e.matricule, id_classe: e.id_classe, id_section: e.id_section }; }), function(e) { return e.nom + ' (' + (e.matricule || '') + ')'; });
    var payTypeCtrl = autoSetup('payType_search', 'payType', 'payType_results', fraisData.map(function(f) { return { id: f.id_frais, libelle: f.type_libelle, classe_libelle: f.classe_libelle, montant: f.montant, id_classe: f.id_classe }; }), function(f) { return f.libelle + ' (' + (f.classe_libelle || '') + ') - ' + parseFloat(f.montant || 0).toLocaleString(); });
    document.getElementById('payEtudiant_search').addEventListener('change', function() {
      var eId = document.getElementById('payEtudiant').value;
      if (!eId || !payTypeCtrl) return;
      var etu = etudiantsData.find(function(x) { return String(x.id_etudiant) === String(eId); });
      if (!etu) return;
      var filtered = fraisData.filter(function(f) { return String(f.id_classe) === String(etu.id_classe); }).map(function(f) { return { id: f.id_frais, libelle: f.type_libelle, classe_libelle: f.classe_libelle, montant: f.montant, id_classe: f.id_classe }; });
      payTypeCtrl.updateItems(filtered);
      document.getElementById('payType_search').value = '';
      document.getElementById('payType').value = '';
    });
    $('#dtSearch1').on('keyup', function() { $('#typesTable').DataTable().search(this.value).draw(); });
    $('#dtLength1').on('change', function() { $('#typesTable').DataTable().page.len(+this.value).draw(); });
    $('#dtSearch2').on('keyup', function() { $('#paiementsTable').DataTable().search(this.value).draw(); });
    $('#dtLength2').on('change', function() { $('#paiementsTable').DataTable().page.len(+this.value).draw(); });
    $('#dtSearch3').on('keyup', function() { $('#fraisTable').DataTable().search(this.value).draw(); });
    $('#dtLength3').on('change', function() { $('#fraisTable').DataTable().page.len(+this.value).draw(); });
    document.getElementById('typeOverlay').addEventListener('click', closeTypeSidebar);
    document.getElementById('paiementOverlay').addEventListener('click', closePaiementSidebar);
    document.getElementById('fraisOverlay').addEventListener('click', closeFraisSidebar);
    document.getElementById('typeForm').addEventListener('submit', saveType);
    document.getElementById('paiementForm').addEventListener('submit', savePaiement);
    document.getElementById('fraisForm').addEventListener('submit', saveFrais);
    document.getElementById('fraisSection').addEventListener('change', function() { filterClassesBySection(this.value); });
  };
  _f();
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>