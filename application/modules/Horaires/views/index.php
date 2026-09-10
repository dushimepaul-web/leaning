<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<script id="classes_data" type="application/json"><?= json_encode($classes) ?></script>
<script id="creneaux_data" type="application/json"><?= json_encode($creneaux) ?></script>
<script id="creneaux_mardi_data" type="application/json"><?= json_encode($creneaux_mardi) ?></script>
<script id="jours_data" type="application/json"><?= json_encode($jours) ?></script>
<script>var JOUR_SPECIAL = '<?= htmlspecialchars($jour_special) ?>'; var JOUR_SPECIAL_ACTIF = <?= ($jour_special_actif == '1') ? 'true' : 'false' ?>;</script>
<script src="<?= base_url() ?>assets/js/api.js?v=<?= time() ?>"></script>
<script src="<?= base_url() ?>assets/vendor/xlsx.full.min.js"></script>

<div style="background:#e5e5e5;padding:20px;min-height:100vh;font-family:'Times New Roman',Times,serif;">

  <div style="text-align:center;margin-bottom:20px;">
    <h1 style="margin:0;font-size:22px;font-weight:700;text-transform:uppercase;font-family:'Times New Roman',Times,serif;letter-spacing:1px;">HORAIRES DES COURS A/S : AFFICHE <?= htmlspecialchars($annee_label) ?></h1>
    <div style="border-bottom:2px solid #000;width:300px;margin:8px auto 0;"></div>
  </div>

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <div></div>
    <div style="display:flex;gap:10px;">
      <button id="btnExportA4" onclick="doExportA4()" style="background:#198754;color:#fff;border:none;padding:10px 24px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;font-family:Arial,sans-serif;display:flex;align-items:center;gap:8px;">
        <span style="font-size:16px;">&#x1F5A8;</span> Imprimer A4
      </button>
      <button id="btnExportExcel" onclick="doExportExcel()" style="background:#0b5ed7;color:#fff;border:none;padding:10px 24px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;font-family:Arial,sans-serif;display:flex;align-items:center;gap:8px;">
        <span style="font-size:16px;">&#x1F4CA;</span> Exporter Excel
      </button>
      <button id="btnRegenerer" onclick="regenerer()" style="background:#0d6efd;color:#fff;border:none;padding:10px 24px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;font-family:Arial,sans-serif;display:flex;align-items:center;gap:8px;">
        <span style="font-size:16px;">&#x21BB;</span> Régénérer
      </button>
      <button id="btnDiagnostiquer" onclick="diagnostiquer()" style="background:#ffc107;color:#000;border:none;padding:10px 24px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;font-family:Arial,sans-serif;display:flex;align-items:center;gap:8px;">
        <span style="font-size:16px;">&#x1F50D;</span> Diagnostiquer
      </button>
      <a href="<?= base_url('Horaires/fixes') ?>" style="background:#6f42c1;color:#fff;border:none;padding:10px 24px;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;font-family:Arial,sans-serif;display:flex;align-items:center;gap:8px;text-decoration:none;">
        <span style="font-size:16px;">&#x1F512;</span> Sessions Fixes
      </a>
    </div>
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

<style>
  @media print {
    body * { visibility: hidden !important; }
    #timetableContainer, #timetableContainer * { visibility: visible !important; }
    #timetableContainer {
      position: absolute !important;
      left: 0 !important;
      top: 0 !important;
      width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      background: #fff !important;
    }
    @page {
      size: A4 portrait;
      margin: 6mm 6mm 14mm 6mm;
    }
  }
</style>

<script>
const classesList = JSON.parse(document.getElementById('classes_data').textContent || '[]');
const creneauxList = JSON.parse(document.getElementById('creneaux_data').textContent || '[]');
const creneauxMardiList = JSON.parse(document.getElementById('creneaux_mardi_data').textContent || '[]');
const joursList = JSON.parse(document.getElementById('jours_data').textContent || '[]');

const allCreneaux = creneauxList;
const allCreneauxMardi = creneauxMardiList;
const joursActifs = joursList.filter(function(j) { return j.actif == 1; }).sort(function(a, b) { return a.ordre - b.ordre; });
const joursNormaux = joursActifs.filter(function(j) { return j.code !== JOUR_SPECIAL; });
const jourMardi = joursActifs.filter(function(j) { return j.code === JOUR_SPECIAL; });

function getHeureLabel(cr) {
  return (cr.heure_debut || '').substring(0, 5) + '-' + (cr.heure_fin || '').substring(0, 5);
}

function buildTable(classeName, horaires, creneaux, jours, title) {
  var html = '';
  html += '<div style="background:#fff;box-shadow:0 3px 12px rgba(0,0,0,0.18);padding:16px 24px;margin-bottom:20px;border-radius:2px;">';
  html += '<div style="text-align:center;margin-bottom:8px;">';
  if (classeName) {
    html += '<div style="margin:0 0 3px 0;font-size:25px;font-weight:700;text-transform:uppercase;font-family:\'Times New Roman\',Times,serif;">' + classeName + '</div>';
  }
  if (title) {
    html += '<div style="margin:0 0 3px 0;font-size:13px;font-weight:600;color:#555;white-space:nowrap;">' + title + '</div>';
  }
  html += '<div style="border-bottom:1.5px solid #000;width:50px;margin:0 auto;"></div>';
  html += '</div>';

  html += '<table style="border-collapse:collapse;border:1px solid #000;width:100%;font-size:10px;font-family:\'Times New Roman\',Times,serif;">';
  html += '<thead><tr>';
  html += '<th style="border:1px solid #000;padding:2px 4px;font-weight:700;text-align:center;background:#fff;color:#000;font-size:10px;width:14%;">HEURE</th>';
  jours.forEach(function(j) {
    html += '<th style="border:1px solid #000;padding:2px 4px;font-weight:700;text-align:center;background:#fff;color:#000;font-size:10px;">' + j.libelle.toUpperCase() + '</th>';
  });
  html += '</tr></thead><tbody>';

  creneaux.forEach(function(cr) {
    var timeLabel = getHeureLabel(cr);
    if (cr.type_creneau === 'vigile') {
      html += '<tr>';
      html += '<td style="border:1px solid #000;padding:2px 4px;font-size:9px;text-align:center;white-space:nowrap;">' + timeLabel + '</td>';
      html += '<td colspan="' + jours.length + '" style="border:1px solid #000;padding:2px 4px;font-size:9px;text-align:center;font-style:italic;font-weight:600;">' + cr.libelle + '</td>';
      html += '</tr>';
      return;
    }
    if (cr.type_creneau === 'pause') {
      html += '<tr>';
      html += '<td style="border:1px solid #000;padding:2px 4px;font-size:9px;text-align:center;font-weight:700;background:#f0f0f0;">' + timeLabel + '</td>';
      html += '<td colspan="' + jours.length + '" style="border:1px solid #000;padding:2px 4px;font-size:9px;text-align:center;font-weight:700;background:#f0f0f0;">PAUSE</td>';
      html += '</tr>';
      return;
    }
    if (cr.type_creneau === 'culte') {
      html += '<tr>';
      html += '<td style="border:1px solid #000;padding:2px 4px;font-size:9px;text-align:center;font-weight:700;background:#fff3cd;">' + timeLabel + '</td>';
      html += '<td colspan="' + jours.length + '" style="border:1px solid #000;padding:2px 4px;font-size:9px;text-align:center;font-weight:700;background:#fff3cd;">CULTE</td>';
      html += '</tr>';
      return;
    }

    html += '<tr>';
    html += '<td style="border:1px solid #000;padding:2px 4px;font-size:9px;text-align:center;">' + timeLabel + '</td>';
    jours.forEach(function(j) {
      var h = horaires.find(function(hh) { return hh.id_creneau == cr.id_creneau && hh.id_jour == j.id_jour; });
      var cellContent = h && h.matiere_code ? h.matiere_code : '';
      html += '<td style="border:1px solid #000;padding:2px 4px;text-align:center;">' + cellContent + '</td>';
    });
    html += '</tr>';
  });

  html += '</tbody></table>';
  html += '</div>';
  return html;
}

async function loadTimetable() {
  var container = document.getElementById('timetableContainer');
  container.innerHTML = '<div style="text-align:center;padding:60px;color:#666;font-size:16px;">Chargement...</div>';

  try {
    var res = await API.horaires.list();
    if (!res.success || !res.data || !res.data.length) {
      container.innerHTML = '<div style="text-align:center;padding:60px;color:#666;font-size:16px;">Aucun horaire généré. Cliquez sur <b>Régénérer</b> pour créer l\'emploi du temps.</div>';
      return;
    }

    var horaires = res.data;
    _cachedHoraires = horaires;
    var classIds = [...new Set(horaires.map(function(h) { return h.id_classe; }))];
    var classesWithData = classIds.map(function(id) { return classesList.find(function(c) { return c.id_classe == id; }); }).filter(Boolean);
    _cachedClassesWithData = classesWithData;

    var html = '<div style="display:flex;flex-direction:column;gap:20px;">';
  _cachedClassesWithData.forEach(function(cl, idx) {
      var classH = horaires.filter(function(h) { return h.id_classe == cl.id_classe; });
      if (JOUR_SPECIAL_ACTIF) {
        html += '<div class="class-block" style="display:grid;grid-template-columns:9fr 3fr;gap:16px;align-items:start;">';
        if (joursNormaux.length > 0) {
          html += buildTable(cl.libelle, classH, allCreneaux, joursNormaux, '');
        }
        if (jourMardi.length > 0) {
          html += buildTable('', classH, allCreneauxMardi, jourMardi, JOUR_SPECIAL.toUpperCase() + ' - JOUR SPÉCIAL (CULTE)');
        }
        html += '</div>';
      } else {
        html += '<div class="class-block">' + buildTable(cl.libelle, classH, allCreneaux, joursActifs, '') + '</div>';
      }
    });
    html += '</div>';

    container.innerHTML = html || '<div style="text-align:center;padding:60px;color:#666;font-size:16px;">Aucune classe trouvée.</div>';
  } catch (e) {
    container.innerHTML = '<div style="text-align:center;padding:60px;color:#dc3545;font-size:16px;">Erreur de chargement des horaires.</div>';
  }
}

async function diagnostiquer() {
  Swal.fire({ title: 'Diagnostic en cours...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
  try {
    var r = await API.horaires.diagnostiquer();
    Swal.close();
    if (r.success && r.data && r.data.success) {
      var v = r.data;
      var diagnostics = v.diagnostics || [];
      var warnings = diagnostics.filter(function(d) { return !d.blocking; });

      if (warnings.length > 0) {
        var html = '<div style="text-align:left;font-size:13px;">';
        html += '<p style="color:#198754;font-weight:700;margin-bottom:8px;">&#x2705; Aucun problème bloquant</p>';
        html += '<p style="color:#856404;font-weight:700;margin-bottom:6px;">&#x26A0; Avertissements (' + warnings.length + ')</p>';
        html += '<ul style="padding-left:16px;margin-bottom:12px;">';
        warnings.forEach(function(d) {
          html += '<li style="margin:4px 0;color:#856404;">' + d.message + '</li>';
        });
        html += '</ul>';
        html += '</div>';
        Swal.fire({ icon: 'warning', title: 'Diagnostic : avertissements', html: html, confirmButtonText: 'Générer quand même', showDenyButton: true, denyButtonText: 'Fermer', width: 550 }).then(function(res) {
          if (res.isConfirmed) regenerer();
        });
      } else {
        var html = '<div style="text-align:left;font-size:14px;">';
        html += '<p style="color:#198754;font-weight:700;margin-bottom:8px;">&#x2705; Aucun problème détecté</p>';
        html += '</div>';
        Swal.fire({ icon: 'success', title: 'Diagnostic OK', html: html, confirmButtonText: 'Générer maintenant', showDenyButton: true, denyButtonText: 'Fermer' }).then(function(res) {
          if (res.isConfirmed) regenerer();
        });
      }
    } else {
      var diagnostics = (r.data && r.data.diagnostics) ? r.data.diagnostics : [];
      var blocking = diagnostics.filter(function(d) { return d.blocking; });
      var warnings = diagnostics.filter(function(d) { return !d.blocking; });

      var html = '<div style="text-align:left;font-size:13px;">';

      if (blocking.length > 0) {
        html += '<p style="color:#dc3545;font-weight:700;margin-bottom:6px;">&#x274C; Problèmes bloquants (' + blocking.length + ')</p>';
        html += '<ul style="padding-left:16px;margin-bottom:12px;">';
        blocking.forEach(function(d) {
          html += '<li style="margin:4px 0;color:#dc3545;">' + d.message + '</li>';
        });
        html += '</ul>';
      }

      if (warnings.length > 0) {
        html += '<p style="color:#ffc107;font-weight:700;margin-bottom:6px;">&#x26A0; Avertissements (' + warnings.length + ')</p>';
        html += '<ul style="padding-left:16px;margin-bottom:12px;">';
        warnings.forEach(function(d) {
          html += '<li style="margin:4px 0;color:#856404;">' + d.message + '</li>';
        });
        html += '</ul>';
      }

      if (blocking.length > 0) {
        html += '<p style="font-size:12px;color:#666;margin-top:8px;"><strong>Que souhaitez-vous faire ?</strong></p>';
      }

      html += '</div>';

      if (blocking.length > 0) {
        Swal.fire({
          icon: 'error',
          title: 'Diagnostic : problèmes détectés',
          html: html,
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: 'Modifier les disponibilités',
          denyButtonText: 'Modifier les fixes',
          cancelButtonText: 'Fermer',
          confirmButtonColor: '#0d6efd',
          denyButtonColor: '#6c757d',
          width: 550
        }).then(function(result) {
          if (result.isConfirmed) window.location.href = '<?= base_url("Disponibilites") ?>';
          else if (result.isDenied) window.location.href = '<?= base_url("Horaires/fixes") ?>';
        });
      } else {
        Swal.fire({ icon: 'warning', title: 'Diagnostic : avertissements', html: html, confirmButtonText: 'Compris', width: 500 });
      }
    }
  } catch (e) {
    Swal.close();
    Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion au serveur.' });
  }
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
    var r = await API.horaires.generer();

    Swal.close();
    if (r.success) {
      var rapportHtml = '<div style="font-family:Arial,sans-serif;font-size:14px;">';
      rapportHtml += '<p><strong>' + r.data.created + '</strong> cr\u00E9neaux cr\u00E9\u00E9s</p>';
      var nbConflits = r.data.conflits_restants || 0;
      rapportHtml += '<p><strong>' + nbConflits + '</strong> cours non plac\u00E9(s)</p>';
      if (r.data.pass5 && r.data.pass5.swap_logistique > 0) {
        rapportHtml += '<div style="margin-top:8px;padding:8px;background:#d1e7dd;border:1px solid #0f5132;border-radius:4px;font-size:12px;">';
        rapportHtml += '<strong style="color:#0f5132;">Swap logistique : </strong>' + r.data.pass5.swap_logistique + ' cours plac\u00E9(s) par substitution/d\u00E9placement</div>';
      }
      if (r.data.cre && r.data.cre.placees > 0) {
        rapportHtml += '<div style="margin-top:8px;padding:8px;background:#e2e3f1;border:1px solid #6610f2;border-radius:4px;font-size:12px;">';
        rapportHtml += '<strong style="color:#6610f2;">Conflict Resolution Engine : </strong>' + r.data.cre.placees + ' cours r\u00E9solu(s) par cha\u00EEne de d\u00E9placements</div>';
        if (r.data.cre.log && r.data.cre.log.length > 0) {
          rapportHtml += '<div style="margin-top:6px;font-size:11px;">';
          r.data.cre.log.forEach(function(entry) {
            var icon = entry.result === 'SUCCESS' ? '\u2705' : '\u274C';
            var detail = icon + ' ' + entry.matiere + ' (classe ' + entry.classe + ') - ' + entry.result;
            if (entry.result === 'SUCCESS' && entry.chain) {
              detail += ' (' + entry.chain.length + ' op\u00E9rations, score: ' + entry.score + ', ' + entry.explored + ' \u00E9tats)';
            } else if (entry.reasons) {
              detail += ' - ' + entry.reasons;
            }
            rapportHtml += '<div style="padding:2px 0;">' + detail + '</div>';
          });
          rapportHtml += '</div>';
        }
      }
      if (r.data.placements_swap_logistique && r.data.placements_swap_logistique.length > 0) {
        rapportHtml += '<div style="margin-top:8px;padding:10px;background:#d1e7dd;border:1px solid #0f5132;border-radius:4px;font-size:12px;">';
        rapportHtml += '<strong style="color:#0f5132;">D\u00E9tail swaps logistiques :</strong>';
        r.data.placements_swap_logistique.forEach(function(s) {
          var detail = s.matiere + ' (classe ' + s.classe + ') - ' + s.action;
          if (s.nouvel_enseignant) detail += ' -> enseignant #' + s.nouvel_enseignant;
          if (s.cours_deplace_vers) detail += ' -> d\u00E9plac\u00E9 jour ' + s.cours_deplace_vers.jour + ' cr\u00E9neau ' + s.cours_deplace_vers.creneau;
          rapportHtml += '<div style="padding:4px 0;">' + detail + '</div>';
        });
        rapportHtml += '</div>';
      }
      if (r.data.details_conflits && r.data.details_conflits.length > 0) {
        rapportHtml += '<div style="margin-top:12px;padding:10px;background:#fff3cd;border:1px solid #ffc107;border-radius:4px;font-size:12px;">';
        rapportHtml += '<strong style="color:#856404;">D\u00E9tail des \u00E9checs :</strong>';
        r.data.details_conflits.forEach(function(m) {
          var detail = typeof m === 'object' ? m.matiere + ' : ' + m.heures_manquantes + 'h manquante(s) - ' + m.raison : m;
          rapportHtml += '<div style="padding:4px 0;">' + detail + '</div>';
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
      var errText = r.message || 'Une erreur est survenue.';
      if (r.messages && r.messages.length) {
        var escapeHtml = function(value) {
          return String(value || '').replace(/[&<>'"]/g, function(char) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[char];
          });
        };
        var reasons = r.messages.map(function(m) {
          return '<li style="margin:4px 0;text-align:left;font-size:13px;">' + escapeHtml(m) + '</li>';
        }).join('');

        var html = '<div style="text-align:left;">';
        html += '<p style="margin-bottom:8px;">La génération a échoué en raison des conflits suivants :</p>';
        html += '<ul style="padding-left:18px;margin-bottom:12px;">' + reasons + '</ul>';
        html += '<p style="font-size:13px;color:#666;margin-bottom:4px;"><strong>Que souhaitez-vous faire ?</strong></p>';
        html += '</div>';

        Swal.fire({
          icon: 'error',
          title: 'Conflits détectés',
          html: html,
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: '<i class="ri-calendar-line"></i> Modifier les disponibilités',
          denyButtonText: '<i class="ri-lock-line"></i> Modifier les fixes',
          cancelButtonText: 'Relancer la génération',
          confirmButtonColor: '#0d6efd',
          denyButtonColor: '#6c757d',
          cancelButtonColor: '#198754',
          width: 550
        }).then(function(result) {
          if (result.isConfirmed) {
            window.location.href = '<?= base_url('Disponibilites') ?>';
          } else if (result.isDenied) {
            window.location.href = '<?= base_url('Horaires/fixes') ?>';
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            regenerer();
          }
        });
      } else if (r.diagnostics && r.diagnostics.length) {
        var escapeHtml = function(value) {
          return String(value || '').replace(/[&<>'"]/g, function(char) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[char];
          });
        };
        var reasons = r.diagnostics.map(function(d) {
          return '<li style="margin:6px 0;text-align:left;">' + escapeHtml(d.message || d.type) + '</li>';
        }).join('');
        Swal.fire({
          icon: 'warning',
          title: 'Génération non effectuée',
          html: '<p>Le planning actuel n\'a pas été modifié. Corrigez les éléments suivants :</p><ul style="padding-left:20px;">' + reasons + '</ul>',
          confirmButtonText: 'Compris'
        });
      } else {
        Swal.fire({ icon: 'error', title: 'Erreur de génération', text: errText });
      }
    }
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion au serveur.' });
  }
}

var _cachedHoraires = [];
var _cachedClassesWithData = [];

function buildA4Table(classeName, horaires, creneaux, jours, title) {
  var html = '<div class="a4-class-block">';
  if (classeName) {
    html += '<div class="a4-class-block-title">' + classeName + '</div>';
  }
  if (title) {
    html += '<div style="text-align:center;font-size:10px;font-weight:600;color:#555;margin-bottom:4px;white-space:nowrap;">' + title + '</div>';
  }
  html += '<table class="a4-tbl">';
  html += '<colgroup><col style="width:15%;">';
  jours.forEach(function() { html += '<col>'; });
  html += '</colgroup>';
  html += '<thead><tr><th>HEURE</th>';
  jours.forEach(function(j) {
    html += '<th>' + j.libelle.toUpperCase() + '</th>';
  });
  html += '</tr></thead><tbody>';

  creneaux.forEach(function(cr) {
    var timeLabel = getHeureLabel(cr);
    if (cr.type_creneau === 'vigile') {
      html += '<tr class="vigile-row"><td>' + timeLabel + '</td>';
      html += '<td colspan="' + jours.length + '">' + cr.libelle + '</td></tr>';
      return;
    }
    if (cr.type_creneau === 'pause') {
      html += '<tr class="pause-row"><td>' + timeLabel + '</td>';
      html += '<td colspan="' + jours.length + '">PAUSE</td></tr>';
      return;
    }
    if (cr.type_creneau === 'culte') {
      html += '<tr class="pause-row"><td>' + timeLabel + '</td>';
      html += '<td colspan="' + jours.length + '" style="font-weight:700;">CULTE</td></tr>';
      return;
    }
    html += '<tr><td>' + timeLabel + '</td>';
    jours.forEach(function(j) {
      var h = horaires.find(function(hh) { return hh.id_creneau == cr.id_creneau && hh.id_jour == j.id_jour; });
      html += '<td>' + (h && h.matiere_code ? h.matiere_code : '') + '</td>';
    });
    html += '</tr>';
  });
  html += '</tbody></table></div>';
  return html;
}

function buildPrintTable(classeName, horaires, creneaux, jours) {
  var h = '<table><thead><tr><th style="width:11%;">HEURE</th>';
  jours.forEach(function(j) { h += '<th>' + j.libelle.toUpperCase() + '</th>'; });
  h += '</tr></thead><tbody>';
  creneaux.forEach(function(cr) {
    var tl = (cr.heure_debut||'').substring(0,5) + '-' + (cr.heure_fin||'').substring(0,5);
    if (cr.type_creneau === 'vigile') {
      h += '<tr><td style="font-style:italic;font-weight:600;">' + tl + '</td>';
      h += '<td colspan="' + jours.length + '" style="font-style:italic;font-weight:600;">' + cr.libelle + '</td></tr>';
    } else if (cr.type_creneau === 'pause') {
      h += '<tr><td style="font-weight:700;background:#f0f0f0;">' + tl + '</td>';
      h += '<td colspan="' + jours.length + '" style="font-weight:700;background:#f0f0f0;">PAUSE</td></tr>';
    } else if (cr.type_creneau === 'culte') {
      h += '<tr><td style="font-weight:700;background:#fff3cd;">' + tl + '</td>';
      h += '<td colspan="' + jours.length + '" style="font-weight:700;background:#fff3cd;">CULTE</td></tr>';
    } else {
      h += '<tr><td>' + tl + '</td>';
      jours.forEach(function(j) {
        var found = null;
        horaires.forEach(function(hh) { if (hh.id_creneau == cr.id_creneau && hh.id_jour == j.id_jour) found = hh; });
        h += '<td>' + (found && found.matiere_code ? found.matiere_code : '') + '</td>';
      });
      h += '</tr>';
    }
  });
  h += '</tbody></table>';
  return h;
}

function buildPrintGrid(cl, creneauxN, joursN, creneauxM, joursM) {
  var g = '';
  g += '<div class="cls-title">' + cl.libelle + '</div><div class="cls-line"></div>';
  if (JOUR_SPECIAL_ACTIF && joursM && joursM.length > 0) {
    g += '<div style="display:grid;grid-template-columns:7fr 3fr;gap:3mm;">';
    g += '<div>' + buildPrintTable('', _cachedHoraires.filter(function(h){return h.id_classe==cl.id_classe;}), creneauxN, joursN) + '</div>';
    g += '<div><div style="font-size:6pt;font-weight:600;color:#555;margin-bottom:1mm;">' + JOUR_SPECIAL.toUpperCase() + ' - CULTE</div>';
    g += buildPrintTable('', _cachedHoraires.filter(function(h){return h.id_classe==cl.id_classe;}), creneauxM, joursM) + '</div>';
    g += '</div>';
  } else {
    g += buildPrintTable('', _cachedHoraires.filter(function(h){return h.id_classe==cl.id_classe;}), creneauxN, joursActifs);
  }
  return g;
}

function doExportA4() {
  if (!_cachedHoraires || !_cachedHoraires.length) {
    Swal.fire({ icon: 'warning', title: 'Aucun horaire', text: 'Générez d\'abord les horaires avant d\'exporter.' });
    return;
  }
  var title = 'HORAIRES DES COURS A/S : <?= htmlspecialchars($annee_label) ?>';
  var perPage = 4;
  var blocks = [];
  _cachedClassesWithData.forEach(function(cl) {
    blocks.push(buildPrintGrid(cl, allCreneaux, joursNormaux, allCreneauxMardi, jourMardi));
  });
  var pages = [];
  for (var i = 0; i < blocks.length; i += perPage) {
    pages.push(blocks.slice(i, i + perPage));
  }

  var w = window.open('', '_blank');
  w.document.write('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>'+title+'</title>');
  w.document.write('<style>');
  w.document.write('*{margin:0;padding:0;box-sizing:border-box;}');
  w.document.write('body{font-family:"Times New Roman",Times,serif;padding:5mm;}');
  w.document.write('@page{size:A4 portrait;margin:5mm 5mm 10mm 5mm;}');
  w.document.write('@media print{body{padding:0;}}');
  w.document.write('h1{font-size:13px;font-weight:700;text-transform:uppercase;text-align:center;margin-bottom:2mm;}');
  w.document.write('.line{border-bottom:2px solid #000;width:180px;margin:1px auto 2mm;}');
  w.document.write('.page{height:277mm;padding:0;page-break-after:always;}');
  w.document.write('.page:last-child{page-break-after:auto;}');
  w.document.write('.page-inner{display:flex;flex-direction:column;gap:2mm;}');
  w.document.write('.cls-title{text-align:center;font-size:10px;font-weight:700;text-transform:uppercase;margin:1mm 0 0.5mm;}');
  w.document.write('.cls-line{border-bottom:1.5px solid #000;width:30px;margin:0 auto 1mm;}');
  w.document.write('table{border-collapse:collapse;border:1px solid #000;width:100%;font-size:7pt;}');
  w.document.write('th,td{border:1px solid #000;padding:0.6mm 1mm;text-align:center;white-space:nowrap;}');
  w.document.write('th{font-weight:700;background:#f5f5f5;font-size:6.5pt;}');
  w.document.write('.footer{text-align:center;font-size:6pt;color:#888;margin-top:2mm;}');
  w.document.write('</style></head><body>');

  pages.forEach(function(pageBlocks, pi) {
    w.document.write('<div class="page"><div class="page-inner">');
    w.document.write('<h1>'+title+'</h1><div class="line"></div>');
    pageBlocks.forEach(function(b) { w.document.write(b); });
    if (pi === pages.length - 1) {
      w.document.write('<div class="footer">Généré le '+new Date().toLocaleDateString('fr-FR')+'</div>');
    }
    w.document.write('</div></div>');
  });

  w.document.write('</body></html>');
  w.document.close();
  setTimeout(function(){ w.print(); }, 400);
}

function doExportExcel() {
  if (!_cachedHoraires || !_cachedHoraires.length) {
    Swal.fire({ icon: 'warning', title: 'Aucun horaire', text: 'Générez d\'abord les horaires avant d\'exporter.' });
    return;
  }
  var horaires = _cachedHoraires;
  var jours = joursActifs;
  var wb = XLSX.utils.book_new();

  _cachedClassesWithData.forEach(function(cl) {
    var classH = horaires.filter(function(h) { return h.id_classe == cl.id_classe; });
    var rows = [];
    var headers = ['HEURE'];
    jours.forEach(function(j) { headers.push(j.libelle.toUpperCase()); });
    rows.push(headers);

    var creneauxToUse = JOUR_SPECIAL_ACTIF ? allCreneaux : allCreneaux;
    creneauxToUse.forEach(function(cr) {
      var tl = cr.heure_debut.substring(0,5) + '-' + cr.heure_fin.substring(0,5);
      if (cr.type_creneau === 'vigile') {
        var row = [tl];
        for (var i = 0; i < jours.length; i++) row.push(cr.libelle);
        rows.push(row);
      } else if (cr.type_creneau === 'pause') {
        var row = [tl];
        for (var i = 0; i < jours.length; i++) row.push('PAUSE');
        rows.push(row);
      } else if (cr.type_creneau === 'culte') {
        var row = [tl];
        for (var i = 0; i < jours.length; i++) row.push('CULTE');
        rows.push(row);
      } else {
        var row = [tl];
        jours.forEach(function(j) {
          var h = classH.find(function(hh) { return hh.id_creneau == cr.id_creneau && hh.id_jour == j.id_jour; });
          row.push(h && h.matiere_code ? h.matiere_code : '');
        });
        rows.push(row);
      }
    });

    var ws = XLSX.utils.aoa_to_sheet(rows);
    var wrap = {alignment:{wrapText:true,vertical:'top'}};
    var range = XLSX.utils.decode_range(ws['!ref']);
    for (var R = range.s.r; R <= range.e.r; R++) {
      for (var C = range.s.c; C <= range.e.c; C++) {
        var addr = XLSX.utils.encode_cell({r:R,c:C});
        if (ws[addr]) { ws[addr].s = wrap; }
      }
    }
    ws['!cols'] = [{wch:12}];
    for (var i = 1; i < headers.length; i++) ws['!cols'].push({wch:18});
    XLSX.utils.book_append_sheet(wb, ws, cl.libelle.substring(0,31));
  });

  XLSX.writeFile(wb, 'Horaires_' + new Date().toISOString().substring(0,10) + '.xlsx');
}

function escHtml(s) {
  var d = document.createElement('div');
  d.appendChild(document.createTextNode(s || ''));
  return d.innerHTML;
}

(function() {
  var retries = 0;
  var wait = setInterval(function() {
    retries++;
    if (retries > 200) { clearInterval(wait); return; }
    if (typeof API !== 'undefined' && API.horaires) {
      clearInterval(wait);
      loadTimetable();
    }
  }, 50);
})();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>
