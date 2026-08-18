<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <span class="fw-semibold mb-4 h6 text-primary-light d-block">Evenements</span>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / Evenement</span>
      </div>
    </div>
    <button type="button" class="my-sidebar-btn btn btn-primary-600 d-flex align-items-center gap-6">
      <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
      Ajouter un evenement
    </button>
  </div>
  <div class="mt-24">
    <div class="row gy-4">
      <div class="col-xxl-3 col-lg-4">
        <div class="card h-100 p-0">
          <div class="card-body p-24">
            <div class="mt-8" id="eventList">
              <!-- loaded via JS -->
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-9 col-lg-8">
        <div class="card h-100 p-0">
          <div class="card-body p-24">
            <div id='wrap'>
              <div id='calendar'></div>
              <div style='clear:both'></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="overlay bg-black bg-opacity-50 w-100 h-100 position-fixed z-9 visibility-hidden opacity-0 duration-300"></div>

<!-- Add Event sidebar -->
<div class="my-sidebar bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 max-w-700-px w-100 translate-x-full duration-300 active-translate-0">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0">Nouvel evenement</h5>
    <button type="button" class="close-my-sidebar text-danger-600 text-lg d-flex">
      <i class="ri-close-large-line"></i>
    </button>
  </div>
  <form id="addEventForm" class="p-20">
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Titre :</label>
        <input type="text" class="form-control radius-8" id="addTitre" placeholder="Entrez le titre" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Date debut</label>
        <div class="position-relative">
          <input class="form-control radius-8 bg-base flatpickr-input" id="addStartDate" type="text" placeholder="dd/mm/aaaa HH:MM" readonly="readonly">
          <span class="position-absolute end-0 top-50 translate-middle-y me-12 line-height-1"><iconify-icon icon="solar:calendar-linear" class="icon text-lg"></iconify-icon></span>
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Date fin</label>
        <div class="position-relative">
          <input class="form-control radius-8 bg-base flatpickr-input" id="addEndDate" type="text" placeholder="dd/mm/aaaa HH:MM" readonly="readonly">
          <span class="position-absolute end-0 top-50 translate-middle-y me-12 line-height-1"><iconify-icon icon="solar:calendar-linear" class="icon text-lg"></iconify-icon></span>
        </div>
      </div>
      <div class="col-12">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Type</label>
        <div class="d-flex align-items-center flex-wrap gap-28">
          <div class="form-check checked-success d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="addType" value="scolaire" id="addTypeScolaire" checked>
            <label class="form-check-label line-height-1 fw-medium text-secondary-light text-sm d-flex align-items-center gap-1" for="addTypeScolaire">
              <span class="w-8-px h-8-px bg-success-600 rounded-circle"></span> Scolaire
            </label>
          </div>
          <div class="form-check checked-primary d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="addType" value="sportif" id="addTypeSportif">
            <label class="form-check-label line-height-1 fw-medium text-secondary-light text-sm d-flex align-items-center gap-1" for="addTypeSportif">
              <span class="w-8-px h-8-px bg-primary-600 rounded-circle"></span> Sportif
            </label>
          </div>
          <div class="form-check checked-warning d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="addType" value="culturel" id="addTypeCulturel">
            <label class="form-check-label line-height-1 fw-medium text-secondary-light text-sm d-flex align-items-center gap-1" for="addTypeCulturel">
              <span class="w-8-px h-8-px bg-warning-600 rounded-circle"></span> Culturel
            </label>
          </div>
          <div class="form-check checked-secondary d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="addType" value="reunion" id="addTypeReunion">
            <label class="form-check-label line-height-1 fw-medium text-secondary-light text-sm d-flex align-items-center gap-1" for="addTypeReunion">
              <span class="w-8-px h-8-px bg-lilac-600 rounded-circle"></span> Reunion
            </label>
          </div>
          <div class="form-check checked-danger d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="addType" value="autre" id="addTypeAutre">
            <label class="form-check-label line-height-1 fw-medium text-secondary-light text-sm d-flex align-items-center gap-1" for="addTypeAutre">
              <span class="w-8-px h-8-px bg-danger-600 rounded-circle"></span> Autre
            </label>
          </div>
        </div>
      </div>
      <div class="col-12">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Lieu</label>
        <input type="text" class="form-control radius-8" id="addLieu" placeholder="Lieu de l'evenement">
      </div>
      <div class="col-12">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Description</label>
        <textarea class="form-control" id="addDesc" rows="4" placeholder="Description"></textarea>
      </div>
      <div class="col-12">
        <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
          <button type="reset" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">Annuler</button>
          <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8 max-w-156-px w-100">Enregistrer</button>
        </div>
      </div>
    </div>
  </form>
</div>

<!-- Edit Event sidebar -->
<div class="edit-sidebar bg-white position-fixed end-0 top-0 h-100vh overflow-y-auto z-99 max-w-700-px w-100 translate-x-full duration-300 active-translate-0">
  <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
    <h5 class="text-lg mb-0">Modifier l'evenement</h5>
    <button type="button" class="close-edit-sidebar text-danger-600 text-lg d-flex">
      <i class="ri-close-large-line"></i>
    </button>
  </div>
  <form id="editEventForm" class="p-20">
    <input type="hidden" id="editId">
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Titre :</label>
        <input type="text" class="form-control radius-8" id="editTitre" placeholder="Entrez le titre" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Date debut</label>
        <div class="position-relative">
          <input class="form-control radius-8 bg-base flatpickr-input" id="editStartDate" type="text" placeholder="dd/mm/aaaa HH:MM" readonly="readonly">
          <span class="position-absolute end-0 top-50 translate-middle-y me-12 line-height-1"><iconify-icon icon="solar:calendar-linear" class="icon text-lg"></iconify-icon></span>
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Date fin</label>
        <div class="position-relative">
          <input class="form-control radius-8 bg-base flatpickr-input" id="editEndDate" type="text" placeholder="dd/mm/aaaa HH:MM" readonly="readonly">
          <span class="position-absolute end-0 top-50 translate-middle-y me-12 line-height-1"><iconify-icon icon="solar:calendar-linear" class="icon text-lg"></iconify-icon></span>
        </div>
      </div>
      <div class="col-12">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Type</label>
        <div class="d-flex align-items-center flex-wrap gap-28">
          <div class="form-check checked-success d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="editType" value="scolaire" id="editTypeScolaire">
            <label class="form-check-label line-height-1 fw-medium text-secondary-light text-sm d-flex align-items-center gap-1" for="editTypeScolaire">
              <span class="w-8-px h-8-px bg-success-600 rounded-circle"></span> Scolaire
            </label>
          </div>
          <div class="form-check checked-primary d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="editType" value="sportif" id="editTypeSportif">
            <label class="form-check-label line-height-1 fw-medium text-secondary-light text-sm d-flex align-items-center gap-1" for="editTypeSportif">
              <span class="w-8-px h-8-px bg-primary-600 rounded-circle"></span> Sportif
            </label>
          </div>
          <div class="form-check checked-warning d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="editType" value="culturel" id="editTypeCulturel">
            <label class="form-check-label line-height-1 fw-medium text-secondary-light text-sm d-flex align-items-center gap-1" for="editTypeCulturel">
              <span class="w-8-px h-8-px bg-warning-600 rounded-circle"></span> Culturel
            </label>
          </div>
          <div class="form-check checked-secondary d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="editType" value="reunion" id="editTypeReunion">
            <label class="form-check-label line-height-1 fw-medium text-secondary-light text-sm d-flex align-items-center gap-1" for="editTypeReunion">
              <span class="w-8-px h-8-px bg-lilac-600 rounded-circle"></span> Reunion
            </label>
          </div>
          <div class="form-check checked-danger d-flex align-items-center gap-2">
            <input class="form-check-input" type="radio" name="editType" value="autre" id="editTypeAutre">
            <label class="form-check-label line-height-1 fw-medium text-secondary-light text-sm d-flex align-items-center gap-1" for="editTypeAutre">
              <span class="w-8-px h-8-px bg-danger-600 rounded-circle"></span> Autre
            </label>
          </div>
        </div>
      </div>
      <div class="col-12">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Lieu</label>
        <input type="text" class="form-control radius-8" id="editLieu" placeholder="Lieu de l'evenement">
      </div>
      <div class="col-12">
        <label class="form-label fw-semibold text-primary-light text-sm mb-8">Description</label>
        <textarea class="form-control" id="editDesc" rows="4" placeholder="Description"></textarea>
      </div>
      <div class="col-12">
        <div class="d-flex align-items-center justify-content-center gap-3 mt-8">
          <button type="reset" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">Annuler</button>
          <button type="submit" class="btn btn-primary-600 border border-primary-600 text-md px-28 py-12 radius-8 max-w-156-px w-100">Enregistrer</button>
        </div>
      </div>
    </div>
  </form>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewEventModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
        <span class="modal-title fs-5 d-block">Details de l'evenement</span>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-24" id="viewEventContent">
      </div>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteEventModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered max-w-340-px">
    <div class="modal-content radius-16 bg-base">
      <div class="modal-body pt-32 px-36 pb-24 text-center">
        <span class="mb-16 fs-1 line-height-1 text-danger"><iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon></span>
        <h6 class="text-lg fw-semibold text-primary-light mb-0">Etes-vous sur de vouloir supprimer cet evenement ?</h6>
        <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
          <button type="reset" class="flex-grow-1 border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-24 py-11 radius-8" data-bs-dismiss="modal">Annuler</button>
          <button type="button" id="confirmDelete" class="flex-grow-1 btn btn-primary-600 border border-primary-600 text-md px-16 py-12 radius-8">Oui, supprimer</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script src="<?= base_url('assets/js/flatpickr.js') ?>"></script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
let currentDeleteId = null;
const typeColors = { scolaire: '#10b981', sportif: '#2563eb', culturel: '#f59e0b', reunion: '#7c3aed', autre: '#ef4444' };
const typeLabels = { scolaire: 'Scolaire', sportif: 'Sportif', culturel: 'Culturel', reunion: 'Reunion', autre: 'Autre' };
let fullCalendarReady = false;

function formatDate(d) {
  if (!d) return '-';
  const dt = new Date(d);
  return dt.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function initCalendar() {
  if ($('#calendar').length && $.fn.fullCalendar) {
    $('#calendar').fullCalendar({
      header: { left: 'prev,next today', center: 'title', right: 'month,agendaWeek,agendaDay' },
      editable: false, firstDay: 1,
      monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
      monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'],
      dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
      dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
      buttonText: { today: "Aujourd'hui", month: 'Mois', week: 'Semaine', day: 'Jour' },
      events: [],
      eventClick: function(event) { viewEvent(event.id); return false; }
    });
    fullCalendarReady = true;
    loadCalendarEvents();
  }
}

function loadCalendarEvents() {
  if (!fullCalendarReady) return;
  API.evenements.list().then(function(r) {
    if (!r.success) return;
    var events = r.data.filter(function(e) { return !e.deleted_at; });
    $('#calendar').fullCalendar('removeEvents');
    $('#calendar').fullCalendar('addEventSource', events.map(function(e) {
      return { id: e.uuid, title: e.titre, start: e.date_debut, end: e.date_fin || null, color: typeColors[e.type] || '#25A194', textColor: '#fff' };
    }));
  });
}

async function loadEvents() {
  try {
    const r = await API.evenements.list();
    if (!r.success) return;
    const events = r.data.filter(function(e) { return !e.deleted_at; });
    const list = document.getElementById('eventList');
    list.innerHTML = events.map(function(e) {
      const color = typeColors[e.type] || '#25A194';
      const label = typeLabels[e.type] || 'Autre';
      return '<div class="event-item d-flex align-items-center justify-content-between gap-4 pb-16 mb-16 border border-start-0 border-end-0 border-top-0">'+
        '<div><div class="d-flex align-items-center gap-10"><span class="w-12-px h-12-px rounded-circle fw-medium d-inline-block" style="background:'+color+'"></span>'+
        '<span class="text-secondary-light">'+formatDate(e.date_debut)+(e.date_fin?' - '+formatDate(e.date_fin):'')+'</span></div>'+
        '<span class="text-primary-light fw-semibold text-md mt-4 d-block">'+e.titre+'</span></div>'+
        '<div class="dropdown"><button type="button" data-bs-toggle="dropdown" aria-expanded="false"><iconify-icon icon="entypo:dots-three-vertical" class="icon text-secondary-light"></iconify-icon></button>'+
        '<ul class="dropdown-menu p-12 border bg-base shadow">'+
        '<li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="viewEvent(\''+e.uuid+'\')"><iconify-icon icon="hugeicons:view" class="icon text-lg line-height-1"></iconify-icon> Voir</button></li>'+
        '<li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-neutral-200 text-hover-neutral-900 d-flex align-items-center gap-10" onclick="editEvent(\''+e.uuid+'\')"><iconify-icon icon="lucide:edit" class="icon text-lg line-height-1"></iconify-icon> Modifier</button></li>'+
        '<li><button type="button" class="dropdown-item px-16 py-8 rounded text-secondary-light bg-hover-danger-100 text-hover-danger-600 d-flex align-items-center gap-10" onclick="confirmDelete(\''+e.uuid+'\')"><iconify-icon icon="fluent:delete-24-regular" class="icon text-lg line-height-1"></iconify-icon> Supprimer</button></li>'+
        '</ul></div></div>';
    }).join('');
    loadCalendarEvents();
  } catch (err) { console.error(err); }
}

function initSidebars() {
  $('.my-sidebar-btn').on('click', function() { $('.my-sidebar').addClass('active'); $('.overlay').addClass('active'); });
  $('.close-my-sidebar').on('click', function() { $('.my-sidebar').removeClass('active'); $('.overlay').removeClass('active'); });
  $('.close-edit-sidebar').on('click', function() { $('.edit-sidebar').removeClass('active'); $('.overlay').removeClass('active'); });
  $('.overlay').on('click', function() { $('.my-sidebar').removeClass('active'); $('.edit-sidebar').removeClass('active'); $('.overlay').removeClass('active'); });
}

document.getElementById('addEventForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const data = {
    titre: document.getElementById('addTitre').value,
    date_debut: document.getElementById('addStartDate').value,
    date_fin: document.getElementById('addEndDate').value || null,
    type: document.querySelector('input[name="addType"]:checked')?.value || 'scolaire',
    lieu: document.getElementById('addLieu').value || null,
    description: document.getElementById('addDesc').value || null,
    couleur: typeColors[document.querySelector('input[name="addType"]:checked')?.value] || '#25A194'
  };
  try {
    const r = await API.evenements.create(data);
    if (r.success) {
      document.querySelector('.close-my-sidebar').click();
      document.getElementById('addEventForm').reset();
      loadEvents();
    } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
  } catch (err) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion' }); }
});

function editEvent(id) {
  API.evenements.list().then(function(r) {
    if (!r.success) return;
    var e = r.data.find(function(x) { return x.uuid == id; });
    if (!e) return;
    document.getElementById('editId').value = e.uuid;
    document.getElementById('editTitre').value = e.titre;
    document.getElementById('editStartDate').value = e.date_debut;
    document.getElementById('editEndDate').value = e.date_fin || '';
    document.getElementById('editLieu').value = e.lieu || '';
    document.getElementById('editDesc').value = e.description || '';
    var typeRadio = document.querySelector('input[name="editType"][value="'+e.type+'"]');
    if (typeRadio) typeRadio.checked = true;
    if (typeof flatpickr !== 'undefined') {
      flatpickr('#editStartDate', { enableTime: true, dateFormat: 'd/m/Y H:i' });
      flatpickr('#editEndDate', { enableTime: true, dateFormat: 'd/m/Y H:i' });
    }
    document.querySelector('.edit-sidebar').classList.add('active');
    document.querySelector('.overlay').classList.add('active');
  });
}

document.getElementById('editEventForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id = document.getElementById('editId').value;
  const data = {
    titre: document.getElementById('editTitre').value,
    date_debut: document.getElementById('editStartDate').value,
    date_fin: document.getElementById('editEndDate').value || null,
    type: document.querySelector('input[name="editType"]:checked')?.value || 'scolaire',
    lieu: document.getElementById('editLieu').value || null,
    description: document.getElementById('editDesc').value || null,
    couleur: typeColors[document.querySelector('input[name="editType"]:checked')?.value] || '#25A194'
  };
  try {
    const r = await API.evenements.update(id, data);
    if (r.success) {
      document.querySelector('.close-edit-sidebar').click();
      loadEvents();
    } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
  } catch (err) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion' }); }
});

function viewEvent(id) {
  API.evenements.list().then(function(r) {
    if (!r.success) return;
    var e = r.data.find(function(x) { return x.uuid == id; });
    if (!e) return;
    var color = typeColors[e.type] || '#25A194';
    document.getElementById('viewEventContent').innerHTML =
      '<div class="mb-12"><span class="text-secondary-light txt-sm fw-medium">Titre</span><h6 class="text-primary-light fw-semibold text-md mb-0 mt-4">'+e.titre+'</h6></div>'+
      '<div class="mb-12"><span class="text-secondary-light txt-sm fw-medium">Date debut</span><h6 class="text-primary-light fw-semibold text-md mb-0 mt-4">'+formatDate(e.date_debut)+'</h6></div>'+
      '<div class="mb-12"><span class="text-secondary-light txt-sm fw-medium">Date fin</span><h6 class="text-primary-light fw-semibold text-md mb-0 mt-4">'+(e.date_fin ? formatDate(e.date_fin) : '-')+'</h6></div>'+
      '<div class="mb-12"><span class="text-secondary-light txt-sm fw-medium">Lieu</span><h6 class="text-primary-light fw-semibold text-md mb-0 mt-4">'+(e.lieu || 'N/A')+'</h6></div>'+
      '<div class="mb-12"><span class="text-secondary-light txt-sm fw-medium">Description</span><h6 class="text-primary-light fw-semibold text-md mb-0 mt-4">'+(e.description || 'N/A')+'</h6></div>'+
      '<div class="mb-12"><span class="text-secondary-light txt-sm fw-medium">Type</span><h6 class="text-primary-light fw-semibold text-md mb-0 mt-4 d-flex align-items-center gap-2"><span class="w-8-px h-8-px rounded-circle d-inline-block" style="background:'+color+'"></span> '+(typeLabels[e.type] || 'Autre')+'</h6></div>';
    $('#viewEventModal').modal('show');
  });
}

function confirmDelete(id) {
  currentDeleteId = id;
  $('#deleteEventModal').modal('show');
}

document.getElementById('confirmDelete').addEventListener('click', async function() {
  if (!currentDeleteId) return;
  try {
    const r = await API.evenements.delete(currentDeleteId);
    if (r.success) {
      $('#deleteEventModal').modal('hide');
      loadEvents();
    } else { Swal.fire({ icon: 'error', title: 'Erreur', text: r.message }); }
  } catch (err) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion' }); }
});

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && typeof API !== 'undefined' && API.evenements) {
      clearInterval(wait);
      initSidebars();
      loadEvents();
      if (typeof flatpickr !== 'undefined') {
        flatpickr('#addStartDate', { enableTime: true, dateFormat: 'd/m/Y H:i' });
        flatpickr('#addEndDate', { enableTime: true, dateFormat: 'd/m/Y H:i' });
      }
      var s = document.createElement('script');
      s.src = 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/1.6.4/fullcalendar.min.js';
      s.onload = initCalendar;
      document.body.appendChild(s);
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>