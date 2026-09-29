<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <div>
      <h1 class="fw-semibold mb-4 h6 text-primary-light">Capacite des Enseignants</h1>
      <div>
        <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
        <span class="text-secondary-light"> / </span>
        <a href="<?= base_url('Enseignants/Programmes') ?>" class="text-secondary-light hover-text-primary hover-underline">Programmes</a>
        <span class="text-secondary-light"> / Capacite</span>
      </div>
    </div>
    <div class="d-flex gap-10">
      <a href="<?= base_url('Enseignants/Programmes') ?>" class="btn btn-secondary d-flex align-items-center gap-6">
        <span><i class="ri-arrow-left-line"></i></span> Retour
      </a>
      <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="loadData()">
        <span><i class="ri-refresh-line"></i></span> Actualiser
      </button>
    </div>
  </div>

  <div class="mt-24">
    <div class="card h-100 radius-12 border-0 shadow-sm overflow-hidden">
      <div class="card-body p-0">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
          <div class="d-flex align-items-center gap-16">
            <div class="text-secondary-light text-sm" id="totalCount">Chargement...</div>
          </div>
          <div class="d-flex align-items-center gap-8">
            <div class="badge bg-success" id="badgeOk">0 OK</div>
            <div class="badge bg-warning text-dark" id="badgeTight">0 Tendu</div>
            <div class="badge bg-danger" id="badgeImpossible">0 Impossible</div>
          </div>
        </div>
        <div class="table-responsive" style="overflow-x: auto;">
          <style>
            #capacityTable tbody tr:hover { background-color: rgba(13, 110, 253, 0.04) !important; transition: background-color 0.2s ease; }
            #capacityTable th { background-color: #f8f9fa !important; color: #212529 !important; font-weight: 600; border-bottom: 2px solid #dee2e6; }
            #capacityTable td { border-color: #dee2e6; vertical-align: middle; padding: 12px 16px; }
            .status-badge { padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #fff; display: inline-block; }
            .teacher-row-impossible { background-color: #fff5f5 !important; }
            .teacher-row-marge_zero { background-color: #fff8e1 !important; }
            .teacher-row-serre { background-color: #fff3e0 !important; }
          </style>
          <table class="table table-hover mb-0" id="capacityTable">
            <thead>
              <tr>
                <th style="padding:14px 16px;text-align:left;min-width:200px;">Enseignant</th>
                <th style="padding:14px 16px;text-align:center;min-width:80px;">Heures/Sem</th>
                <th style="padding:14px 16px;text-align:center;min-width:250px;">Jours Disponibles</th>
                <th style="padding:14px 16px;text-align:center;min-width:100px;">Creneaux</th>
                <th style="padding:14px 16px;text-align:center;min-width:80px;">Marge</th>
                <th style="padding:14px 16px;text-align:center;min-width:120px;">Statut</th>
              </tr>
            </thead>
            <tbody id="dataBody">
              <tr><td colspan="6" class="text-center text-secondary-light py-40">Chargement en cours...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-24" id="impossibleSection" style="display:none;">
    <div class="card radius-12 border-0 shadow-sm overflow-hidden" style="border-left: 4px solid #dc3545 !important;">
      <div class="card-header bg-danger bg-opacity-10 border-0 px-20 py-12">
        <h6 class="text-danger fw-bold mb-0"><i class="ri-error-warning-line"></i> Enseignants en SURCHARGE - Actions requises</h6>
      </div>
      <div class="card-body px-20 py-12" id="impossibleBody"></div>
    </div>
  </div>

  <div class="mt-24" id="tightSection" style="display:none;">
    <div class="card radius-12 border-0 shadow-sm overflow-hidden" style="border-left: 4px solid #ffc107 !important;">
      <div class="card-header bg-warning bg-opacity-10 border-0 px-20 py-12">
        <h6 class="text-warning fw-bold mb-0"><i class="ri-alert-line"></i> Enseignants a marge etroite</h6>
      </div>
      <div class="card-body px-20 py-12" id="tightBody"></div>
    </div>
  </div>

  <div class="mt-24" id="detailsSection" style="display:none;">
    <div class="card radius-12 border-0 shadow-sm overflow-hidden">
      <div class="card-header bg-primary bg-opacity-10 border-0 px-20 py-12">
        <h6 class="text-primary fw-bold mb-0"><i class="ri-information-line"></i> Details des cours par enseignant</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0" id="detailsTable">
            <thead>
              <tr>
                <th style="padding:12px 16px;text-align:left;">Enseignant</th>
                <th style="padding:12px 16px;text-align:left;">Matiere</th>
                <th style="padding:12px 16px;text-align:left;">Classe</th>
                <th style="padding:12px 16px;text-align:center;">Heures/Sem</th>
              </tr>
            </thead>
            <tbody id="detailsBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let teacherData = [];

async function loadData() {
  document.getElementById('dataBody').innerHTML = '<tr><td colspan="6" class="text-center text-secondary-light py-40">Chargement en cours...</td></tr>';
  try {
    const res = await API.matieres_classes.teacher_capacity();
    if (!res.success) {
      document.getElementById('dataBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger py-40">Erreur: ' + (res.message || 'Erreur inconnue') + '</td></tr>';
      return;
    }
    teacherData = res.data || [];
    render();
  } catch (e) {
    document.getElementById('dataBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger py-40">Erreur de connexion</td></tr>';
  }
}

function render() {
  const tbody = document.getElementById('dataBody');
  if (!teacherData.length) {
    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-secondary-light py-40">Aucun enseignant trouve</td></tr>';
    document.getElementById('totalCount').textContent = '0 enseignant';
    document.getElementById('badgeOk').textContent = '0 OK';
    document.getElementById('badgeTight').textContent = '0 Tendu';
    document.getElementById('badgeImpossible').textContent = '0 Impossible';
    document.getElementById('impossibleSection').style.display = 'none';
    document.getElementById('tightSection').style.display = 'none';
    document.getElementById('detailsSection').style.display = 'none';
    return;
  }

  let html = '';
  let countOk = 0, countTight = 0, countImpossible = 0;
  let impossibleHtml = '', tightHtml = '', detailsHtml = '';

  teacherData.forEach(function(t) {
    let rowClass = '';
    if (t.status === 'impossible') { rowClass = 'teacher-row-impossible'; countImpossible++; }
    else if (t.status === 'marge_zero') { rowClass = 'teacher-row-marge_zero'; countTight++; }
    else if (t.status === 'serré') { rowClass = 'teacher-row-serre'; countTight++; }
    else { countOk++; }

    let joursText = t.jours_dispo.join(', ');
    if (t.nb_jours > 0) {
      joursText += ' (' + t.nb_jours + ')';
    }

    html += '<tr class="' + rowClass + '">';
    html += '<td style="font-weight:600;">' + escapeHtml(t.nom) + '</td>';
    html += '<td class="text-center">' + t.sessions_semaine + 'h</td>';
    html += '<td class="text-center" style="font-size:13px;">' + escapeHtml(joursText) + '</td>';
    html += '<td class="text-center">' + t.creneaux_dispo + '</td>';
    html += '<td class="text-center fw-bold" style="color:' + t.status_color + ';font-size:16px;">' + t.marge + '</td>';
    html += '<td class="text-center"><span class="status-badge" style="background:' + t.status_color + ';">' + escapeHtml(t.status_label) + '</span></td>';
    html += '</tr>';

    if (t.status === 'impossible') {
      let deficit = Math.abs(t.marge);
      impossibleHtml += '<div class="d-flex align-items-start gap-12 py-8 border-bottom border-neutral-100">';
      impossibleHtml += '<span class="text-danger fs-18"><i class="ri-error-warning-fill"></i></span>';
      impossibleHtml += '<div>';
      impossibleHtml += '<div class="fw-bold text-danger">' + escapeHtml(t.nom) + '</div>';
      impossibleHtml += '<div class="text-secondary-light text-sm mt-4">' + t.sessions_semaine + 'h/semaine, ' + t.creneaux_dispo + ' creneaux disponibles (' + t.jours_dispo.length + ' jours). <b class="text-danger">Manque ' + deficit + 'h.</b></div>';
      impossibleHtml += '<div class="text-primary text-sm mt-4"><i class="ri-lightbulb-line"></i> <b>Solution :</b> Ajoutez au moins ' + deficit + ' jour(s) disponible(s) dans la page <a href="<?= base_url('Disponibilites') ?>" class="text-primary text-decoration-underline">Disponibilites</a>.</div>';
      impossibleHtml += '</div></div>';
    }

    if (t.status === 'marge_zero' || t.status === 'serré') {
      let sug = t.marge === 0 ? 'Marge nulle - Aucune flexibilite. Ajoutez 1+ jour.' : 'Marge etroite - Ajoutez 1 jour pour plus de flexibilite.';
      tightHtml += '<div class="d-flex align-items-start gap-12 py-8 border-bottom border-neutral-100">';
      tightHtml += '<span class="text-warning fs-18"><i class="ri-alert-fill"></i></span>';
      tightHtml += '<div>';
      tightHtml += '<div class="fw-bold text-warning">' + escapeHtml(t.nom) + '</div>';
      tightHtml += '<div class="text-secondary-light text-sm mt-4">' + t.sessions_semaine + 'h, ' + t.jours_dispo.length + ' jour(s), marge=' + t.marge + '.</div>';
      tightHtml += '<div class="text-primary text-sm mt-4"><i class="ri-lightbulb-line"></i> <b>Conseil :</b> ' + sug + '</div>';
      tightHtml += '</div></div>';
    }

    if (t.details && t.details.length) {
      t.details.forEach(function(d) {
        detailsHtml += '<tr>';
        detailsHtml += '<td style="font-weight:500;">' + escapeHtml(t.nom) + '</td>';
        detailsHtml += '<td>' + escapeHtml(d.matiere) + '</td>';
        detailsHtml += '<td>' + escapeHtml(d.classe) + '</td>';
        detailsHtml += '<td class="text-center">' + d.heures + 'h</td>';
        detailsHtml += '</tr>';
      });
    }
  });

  tbody.innerHTML = html;
  document.getElementById('totalCount').textContent = teacherData.length + ' enseignant(s)';
  document.getElementById('badgeOk').textContent = countOk + ' OK';
  document.getElementById('badgeTight').textContent = countTight + ' Tendu';
  document.getElementById('badgeImpossible').textContent = countImpossible + ' Impossible';

  if (countImpossible > 0) {
    document.getElementById('impossibleSection').style.display = 'block';
    document.getElementById('impossibleBody').innerHTML = impossibleHtml;
  } else {
    document.getElementById('impossibleSection').style.display = 'none';
  }

  if (countTight > 0) {
    document.getElementById('tightSection').style.display = 'block';
    document.getElementById('tightBody').innerHTML = tightHtml;
  } else {
    document.getElementById('tightSection').style.display = 'none';
  }

  if (detailsHtml) {
    document.getElementById('detailsSection').style.display = 'block';
    document.getElementById('detailsBody').innerHTML = detailsHtml;
  } else {
    document.getElementById('detailsSection').style.display = 'none';
  }
}

function escapeHtml(value) {
  return String(value || '').replace(/[&<>"']/g, function(char) {
    return {'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[char];
  });
}

loadData();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
