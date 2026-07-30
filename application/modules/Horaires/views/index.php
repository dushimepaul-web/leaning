<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>

<script id="classes_data" type="application/json"><?= json_encode($classes) ?></script>
<script id="creneaux_data" type="application/json"><?= json_encode($creneaux) ?></script>
<script id="jours_data" type="application/json"><?= json_encode($jours) ?></script>
<script id="matieres_data" type="application/json"><?= json_encode($matieres) ?></script>
<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>

<div style="background:#e5e5e5;padding:20px;min-height:100vh;font-family:'Times New Roman',Times,serif;">

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <div></div>
    <button id="btnRegenerer" onclick="regenerer()" style="background:#0d6efd;color:#fff;border:none;padding:10px 24px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;font-family:Arial,sans-serif;display:flex;align-items:center;gap:8px;">
      <span style="font-size:16px;">&#x21BB;</span> Régénérer
    </button>
  </div>

  <div id="timetableContainer"></div>

</div>

<div id="reportModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;justify-content:center;align-items:center;">
  <div style="background:#fff;border-radius:8px;padding:32px;max-width:700px;width:90%;max-height:80vh;overflow-y:auto;box-shadow:0 8px 32px rgba(0,0,0,0.3);">
    <h3 style="margin:0 0 16px 0;font-size:20px;font-weight:700;font-family:Arial,sans-serif;" id="reportTitle">Rapport de génération</h3>
    <div id="reportBody"></div>
    <div style="text-align:right;margin-top:20px;">
      <button onclick="document.getElementById('reportModal').style.display='none'" style="background:#6c757d;color:#fff;border:none;padding:8px 20px;border-radius:6px;font-size:14px;cursor:pointer;font-family:Arial,sans-serif;">Fermer</button>
    </div>
  </div>
</div>

<?php include VIEWPATH.'includes/Footer.php'; ?>
<script>
const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
const classesList = JSON.parse(document.getElementById('classes_data').textContent || '[]');
const creneauxList = JSON.parse(document.getElementById('creneaux_data').textContent || '[]');
const joursList = JSON.parse(document.getElementById('jours_data').textContent || '[]');
const matieresList = JSON.parse(document.getElementById('matieres_data').textContent || '[]');

const coursCreneaux = creneauxList.filter(function(cr) { return cr.type_creneau === 'cours'; });
const joursActifs = joursList.filter(function(j) { return j.actif == 1; });

function getHeureLabel(cr) {
  return (cr.heure_debut || '').substring(0, 5) + '-' + (cr.heure_fin || '').substring(0, 5);
}

async function loadTimetable() {
  var container = document.getElementById('timetableContainer');
  container.innerHTML = '<div style="text-align:center;padding:60px;color:#666;font-size:16px;">Chargement...</div>';

  var res = await API.horaires.list();
  if (!res.success || !res.data.length) {
    container.innerHTML = '<div style="text-align:center;padding:60px;color:#666;font-size:16px;">Aucun horaire généré. Cliquez sur <b>Régénérer</b> pour créer l\'emploi du temps.</div>';
    return;
  }

  var horaires = res.data;
  var classIds = [...new Set(horaires.map(function(h) { return h.id_classe; }))];
  var classesWithData = classIds.map(function(id) { return classesList.find(function(c) { return c.id_classe == id; }); }).filter(Boolean);

  var html = '';
  classesWithData.forEach(function(cl) {
    var classHoraires = horaires.filter(function(h) { return h.id_classe == cl.id_classe; });
    html += buildTimetableSheet(cl.libelle, classHoraires);
  });

  container.innerHTML = html || '<div style="text-align:center;padding:60px;color:#666;font-size:16px;">Aucune classe trouvée.</div>';
}

function buildTimetableSheet(classeName, horaires) {
  var html = '';
  html += '<div style="background:#fff;box-shadow:0 3px 12px rgba(0,0,0,0.18);padding:32px 40px;margin-bottom:30px;border-radius:2px;">';
  html += '<div style="text-align:center;margin-bottom:20px;">';
  html += '<h2 style="margin:0 0 4px 0;font-size:15px;font-weight:700;text-transform:uppercase;font-family:\'Times New Roman\',Times,serif;">CLASSE ' + classeName + '</h2>';
  html += '<div style="border-bottom:2px solid #000;width:60px;margin:6px auto 0 auto;"></div>';
  html += '</div>';

  html += '<table style="border-collapse:collapse;border:1px solid #000;width:100%;font-size:11px;font-family:\'Times New Roman\',Times,serif;">';
  html += '<thead><tr>';
  html += '<th style="border:1px solid #000;padding:3px 5px;font-weight:700;text-align:center;background:#fff;color:#000;font-size:11px;">HEURE</th>';
  joursActifs.forEach(function(j) {
    html += '<th style="border:1px solid #000;padding:3px 5px;font-weight:700;text-align:center;background:#fff;color:#000;font-size:11px;">' + j.libelle.toUpperCase() + '</th>';
  });
  html += '</tr></thead><tbody>';

  var totalCols = 1 + joursActifs.length;

  var foundSalut = false;
  var foundPause = false;

  coursCreneaux.forEach(function(cr, idx) {
    var timeLabel = getHeureLabel(cr);
    var isFirstCreneau = (idx === 0);
    var isPauseCreneau = (cr.type_creneau === 'pause' || cr.type_creneau === 'recreation');

    if (isFirstCreneau && timeLabel.indexOf('7H') > -1 || timeLabel.indexOf('07:') > -1 || timeLabel.indexOf('07H') > -1) {
      html += '<tr>';
      html += '<td style="border:1px solid #000;padding:3px 5px;font-size:10px;text-align:center;white-space:nowrap;">' + timeLabel + '</td>';
      html += '<td colspan="' + (joursActifs.length) + '" style="border:1px solid #000;padding:3px 5px;font-size:10px;text-align:center;font-style:italic;font-weight:600;">SALUT DU DRAPEAU ET VIGILE MATINAL</td>';
      html += '</tr>';
      foundSalut = true;
      return;
    }

    if (isPauseCreneau) {
      html += '<tr>';
      html += '<td style="border:1px solid #000;padding:3px 5px;font-size:10px;text-align:center;font-weight:700;">' + timeLabel + '</td>';
      html += '<td colspan="' + (joursActifs.length) + '" style="border:1px solid #000;padding:3px 5px;font-size:10px;text-align:center;font-weight:700;">PAUSE</td>';
      html += '</tr>';
      foundPause = true;
      return;
    }

    html += '<tr>';
    html += '<td style="border:1px solid #000;padding:3px 5px;font-size:10px;text-align:center;">' + timeLabel + '</td>';
    joursActifs.forEach(function(j) {
      var h = horaires.find(function(hh) { return hh.id_creneau == cr.id_creneau && hh.id_jour == j.id_jour; });
      var cellContent = h && h.matiere ? h.matiere : '—';
      html += '<td style="border:1px solid #000;padding:3px 5px;text-align:center;">' + cellContent + '</td>';
    });
    html += '</tr>';
  });

  html += '</tbody></table>';
  html += '</div>';
  return html;
}

async function regenerer() {
  var result = await Swal.fire({
    title: 'Régénérer l\'emploi du temps ?',
    html: 'Cette action va <b>remplacer</b> tous les horaires actuels.<br>L\'algorithme va placer tous les cours automatiquement.',
    icon: 'question', showCancelButton: true, confirmButtonText: 'Oui, régénérer', cancelButtonText: 'Annuler'
  });
  if (!result.isConfirmed) return;

  Swal.fire({ title: 'Génération en cours...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });

  try {
    var r = await fetch(API.base_url + 'api/horaires/generer', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' }
    }).then(function(res) { return res.json(); });

    if (r.success) {
      var rapportHtml = '<div style="font-family:Arial,sans-serif;font-size:14px;">';
      rapportHtml += '<p><strong>' + r.data.created + '</strong> cr\u00E9neaux cr\u00E9\u00E9s</p>';
      var nbConflits = r.data.conflits_restants || 0;
      rapportHtml += '<p><strong>' + nbConflits + '</strong> cours non plac\u00E9(s)</p>';
      if (r.data.details_conflits && r.data.details_conflits.length > 0) {
        rapportHtml += '<div style="margin-top:12px;padding:10px;background:#fff3cd;border:1px solid #ffc107;border-radius:4px;font-size:12px;">';
        rapportHtml += '<strong style="color:#856404;">D\u00E9tail des \u00E9checs :</strong>';
        r.data.details_conflits.forEach(function(m) {
          rapportHtml += '<div style="padding:4px 0;">' + m + '</div>';
        });
        rapportHtml += '</div>';
      }
      rapportHtml += '<p style="margin-top:12px;font-size:13px;">' + r.data.message + '</p>';
      rapportHtml += '</div>';
      document.getElementById('reportBody').innerHTML = rapportHtml;
      document.getElementById('reportTitle').textContent = 'Rapport de g\u00E9n\u00E9ration';
      document.getElementById('reportModal').style.display = 'flex';
      loadTimetable();
    } else {
      Swal.fire({ icon: 'error', title: 'Erreur', text: r.message || 'Une erreur est survenue.' });
    }
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion au serveur.' });
  }
}

(function() {
  var wait = setInterval(function() {
    if (typeof jQuery !== 'undefined' && typeof API !== 'undefined' && API.horaires) {
      clearInterval(wait);
      loadTimetable();
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
