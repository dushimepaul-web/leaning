<?php include VIEWPATH.'includes/Header.php'; ?>
<?php include VIEWPATH.'includes/Sidebar.php'; ?>
<div class="dashboard-main-body">
  <style>
    .settings-topbar { position: sticky; top: 0; z-index: 40; padding: 10px 0 16px; margin: -6px 0 18px; background: linear-gradient(180deg, #f5f6fa 72%, rgba(245,246,250,0)); }
    .settings-topbar .breadcrumb { margin-bottom: 0; }
    .settings-actions { position: sticky; bottom: 0; z-index: 40; margin-top: 28px; background: rgba(255,255,255,0.94); backdrop-filter: blur(8px); border: 1px solid var(--border-color,#e4e7ec); border-radius: 16px; box-shadow: 0 -6px 24px rgba(15,23,42,.10); padding: 14px 18px; }
    .settings-actions .info-chip { font-size: 13px; color: var(--text-secondary-light,#667085); }
    .settings-actions .info-chip.dirty { color: #b45309; }
    .field-dirty { border-color: #f59e0b !important; box-shadow: 0 0 0 3px rgba(245,158,11,.14) !important; }
    .tab-dirty-badge { display: none; width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; margin-left: 6px; flex-shrink: 0; }
    .tab-dirty-badge.show { display: inline-block; }
    .input-icon { position: relative; }
    .input-icon .form-control { padding-left: 42px; }
    .input-icon > i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--primary-600,#25A194); font-size: 17px; pointer-events: none; }
    .mention-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 22px; border-radius: 6px; font-size: 12px; font-weight: 600; padding: 0 6px; }
    .sensitive-warn { background: rgba(220,38,38,.05); border: 1px dashed rgba(220,38,38,.35); border-radius: 10px; padding: 10px 12px; }
    .diff-row { display:flex; gap:10px; align-items:flex-start; padding:6px 0; border-bottom:1px dashed var(--border-color,#e4e7ec); }
    .diff-row:last-child { border-bottom:0; }
    .diff-key { min-width: 170px; font-weight:600; color:#344054; }
    .diff-old { color:#98a2b3; text-decoration: line-through; }
    .diff-new { color:#039855; font-weight:500; }
    .diff-arrow { color:#98a2b3; }
  </style>

  <div class="settings-topbar">
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3">
      <div>
        <h1 class="fw-semibold mb-4 h6 text-primary-light d-flex align-items-center gap-2">
          <i class="ri-settings-3-line text-primary-600"></i> Paramètres
        </h1>
        <div>
          <a href="<?= base_url('Dashboard') ?>" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
          <span class="text-secondary-light"> / Paramètres</span>
        </div>
      </div>
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <span id="dirtyChip" class="badge bg-warning-100 text-warning-600 d-none d-inline-flex align-items-center gap-1 px-12 py-8 radius-8 fw-semibold">
          <i class="ri-edit-line"></i> Modifications non enregistrées
        </span>
        <button type="button" id="btnSaveTop" class="btn btn-primary-600 d-flex align-items-center gap-6" onclick="confirmSave()">
          <span class="d-flex text-md"><i class="ri-save-line"></i></span> Enregistrer
        </button>
      </div>
    </div>
  </div>

  <div id="settingsMessage" class="alert d-none mb-16"></div>

  <div class="row gy-4">
    <div class="col-xxl-8 col-lg-7">
      <form id="settingsForm">
        <!-- Nav tabs -->
        <ul class="nav nav-pills bordered-tab mb-3 flex-nowrap overflow-x-auto pb-1" id="settingsTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="tab-general" data-bs-toggle="pill" data-bs-target="#pane-general" type="button">
              <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-building-line"></i></span> Général
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="tab-horaires" data-bs-toggle="pill" data-bs-target="#pane-horaires" type="button">
              <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-time-line"></i></span> Horaires & Créneaux
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="tab-finances" data-bs-toggle="pill" data-bs-target="#pane-finances" type="button">
              <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-money-dollar-circle-line"></i></span> Finances
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="tab-discipline" data-bs-toggle="pill" data-bs-target="#pane-discipline" type="button">
              <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-emotion-line"></i></span> Discipline
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="tab-mentions" data-bs-toggle="pill" data-bs-target="#pane-mentions" type="button">
              <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-medal-line"></i></span> Mentions
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="tab-notation" data-bs-toggle="pill" data-bs-target="#pane-notation" type="button">
              <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-calculator-line"></i></span> Notation & Pédagogie
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-8 text-secondary-light fw-medium text-sm px-20 py-12" id="tab-email" data-bs-toggle="pill" data-bs-target="#pane-email" type="button">
              <span class="d-flex tab-icon line-height-1 text-md"><i class="ri-mail-line"></i></span> Notifications & Email
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
                    <div class="input-icon">
                      <i class="ri-school-line"></i>
                      <input type="text" class="form-control radius-8" id="nom_ecole" placeholder="Ex: Complexe Scolaire VIP">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Téléphone</label>
                    <div class="input-icon">
                      <i class="ri-phone-line"></i>
                      <input type="text" class="form-control radius-8" id="telephone_ecole" placeholder="+243 800 000 000">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Email</label>
                    <div class="input-icon">
                      <i class="ri-mail-line"></i>
                      <input type="email" class="form-control radius-8" id="email_ecole" placeholder="contact@ecole.cd">
                    </div>
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Adresse</label>
                    <div class="input-icon">
                      <i class="ri-map-pin-line" style="top:18px;transform:none;"></i>
                      <textarea class="form-control radius-8" id="adresse_ecole" rows="3" placeholder="N°, Avenue, Commune, Ville"></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Onglet Horaires & Créneaux -->
          <div class="tab-pane fade" id="pane-horaires">
            <div class="card shadow-1 radius-12">
              <div class="card-header py-16 px-24 border-bottom bg-base d-flex align-items-center gap-12">
                <span class="d-flex align-items-center justify-content-center bg-info-100 text-info-600 radius-4" style="width:36px;height:36px;"><i class="ri-time-line text-lg"></i></span>
                <div>
                  <h6 class="text-lg fw-semibold mb-0">Paramètres des horaires et créneaux</h6>
                  <small class="text-secondary-light">Configuration de la structure temporelle des cours et de la journée</small>
                </div>
              </div>
              <div class="card-body p-24">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Heure de début de journée</label>
                    <input type="time" class="form-control radius-8" id="heure_debut_journee" value="07:30">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Durée d'un cours (min)</label>
                    <input type="number" class="form-control radius-8" id="duree_cours" value="45" min="15" max="120">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Durée pause standard (min)</label>
                    <input type="number" class="form-control radius-8" id="duree_pause" value="20" min="5" max="60">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Vigile / Rassemblement matinal (min)</label>
                    <input type="number" class="form-control radius-8" id="duree_vigie" value="10" min="0" max="30">
                    <small class="text-secondary-light">Avant le premier cours (ex: 10 min)</small>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Nombre de créneaux de cours par jour</label>
                    <input type="number" class="form-control radius-8" id="nb_creneaux_jour" value="8" min="1" max="12">
                    <small class="text-secondary-light">Nombre de périodes de cours effectifs par jour (ex: 8 créneaux)</small>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Jour spécial (Culte)</label>
                    <select class="form-select radius-8" id="jour_special">
                      <option value="lundi">Lundi</option>
                      <option value="mardi" selected>Mardi</option>
                      <option value="mercredi">Mercredi</option>
                      <option value="jeudi">Jeudi</option>
                      <option value="vendredi">Vendredi</option>
                    </select>
                    <small class="text-secondary-light">Jour avec Culte après la pause (modifiable)</small>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Durée Culte (min)</label>
                    <input type="number" class="form-control radius-8" id="duree_culte" value="35" min="5" max="120">
                    <small class="text-secondary-light">Durée de la Culte le jour spécial</small>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Activer jour spécial</label>
                    <div class="form-check form-switch mt-8">
                      <input class="form-check-input" type="checkbox" id="jour_special_actif" checked>
                      <label class="form-check-label" for="jour_special_actif">Activé</label>
                    </div>
                    <small class="text-secondary-light">Désactiver si pas de jour spécial</small>
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
                    <input type="text" class="form-control radius-8" id="devise" placeholder="Ex: FC, USD, EUR" oninput="this.value=this.value.replace(/[^a-zA-Z]/g,'')" maxlength="10">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Onglet Discipline -->
          <div class="tab-pane fade" id="pane-discipline">
            <div class="card shadow-1 radius-12">
              <div class="card-header py-16 px-24 border-bottom bg-base d-flex align-items-center gap-12">
                <span class="d-flex align-items-center justify-content-center bg-danger-100 text-danger-600 radius-4" style="width:36px;height:36px;"><i class="ri-emotion-line text-lg"></i></span>
                <div>
                  <h6 class="text-lg fw-semibold mb-0">Points initiaux de conduite</h6>
                  <small class="text-secondary-light">Points de départ par classe pour les sanctions de conduite</small>
                </div>
              </div>
              <div class="card-body p-24">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Points initiaux de conduite</label>
                    <input type="number" class="form-control radius-8" id="points_conduite_defaut" value="60" min="0" step="0.5">
                    <small class="text-secondary-light">Points de départ pour chaque élève de l'école (sanctions de conduite)</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Onglet Mentions -->
          <div class="tab-pane fade" id="pane-mentions">
            <div class="card shadow-1 radius-12">
              <div class="card-header py-16 px-24 border-bottom bg-base d-flex align-items-center gap-12">
                <span class="d-flex align-items-center justify-content-center bg-warning-100 text-warning-600 radius-4" style="width:36px;height:36px;"><i class="ri-medal-line text-lg"></i></span>
                <div>
                  <h6 class="text-lg fw-semibold mb-0">Mentions des bulletins</h6>
                  <small class="text-secondary-light">Seuils dynamiques en pourcentage (%) pour chaque mention</small>
                </div>
              </div>
              <div class="card-body p-24">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-16 mb-16 p-16 radius-8 bg-base">
                  <div>
                    <h6 class="fw-semibold mb-1">Échelle des notes</h6>
                    <small class="text-secondary-light">Note maximale utilisée sur les bulletins, fiches et tableaux de bord</small>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <label class="text-sm fw-medium text-secondary-light">Notes sur</label>
                    <select class="form-select form-select-sm" id="echelle_notes" style="width:110px;">
                      <option value="100" selected>100</option>
                      <option value="20">20</option>
                    </select>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered align-middle mb-0">
                    <thead class="bg-base">
                      <tr>
                        <th style="width:40px;">#</th>
                        <th>Mention</th>
                        <th style="width:160px;">Seuil (%)</th>
                        <th>Libellé affiché</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td><span class="fw-semibold text-success d-flex align-items-center gap-2"><span class="mention-badge bg-success-100 text-success-600">E</span> Excellent</span></td>
                        <td><input type="number" class="form-control radius-8" id="mention_excellent" min="0" max="100" step="0.5" value="90"></td>
                        <td><input type="text" class="form-control radius-8" id="mention_excellent_libelle" value="Excellent"></td>
                      </tr>
                      <tr>
                        <td>2</td>
                        <td><span class="fw-semibold text-primary d-flex align-items-center gap-2"><span class="mention-badge bg-primary-100 text-primary-600">TB</span> Très bien</span></td>
                        <td><input type="number" class="form-control radius-8" id="mention_tres_bien" min="0" max="100" step="0.5" value="80"></td>
                        <td><input type="text" class="form-control radius-8" id="mention_tres_bien_libelle" value="Très Bien"></td>
                      </tr>
                      <tr>
                        <td>3</td>
                        <td><span class="fw-semibold text-info d-flex align-items-center gap-2"><span class="mention-badge bg-info-100 text-info-600">B</span> Bien</span></td>
                        <td><input type="number" class="form-control radius-8" id="mention_bien" min="0" max="100" step="0.5" value="70"></td>
                        <td><input type="text" class="form-control radius-8" id="mention_bien_libelle" value="Bien"></td>
                      </tr>
                      <tr>
                        <td>4</td>
                        <td><span class="fw-semibold text-secondary d-flex align-items-center gap-2"><span class="mention-badge bg-neutral-200 text-neutral-600">AB</span> Assez bien</span></td>
                        <td><input type="number" class="form-control radius-8" id="mention_assez_bien" min="0" max="100" step="0.5" value="60"></td>
                        <td><input type="text" class="form-control radius-8" id="mention_assez_bien_libelle" value="Assez Bien"></td>
                      </tr>
                      <tr>
                        <td>5</td>
                        <td><span class="fw-semibold text-warning d-flex align-items-center gap-2"><span class="mention-badge bg-warning-100 text-warning-600">P</span> Passable</span></td>
                        <td><input type="number" class="form-control radius-8" id="mention_passable" min="0" max="100" step="0.5" value="40"></td>
                        <td><input type="text" class="form-control radius-8" id="mention_passable_libelle" value="Passable"></td>
                      </tr>
                      <tr>
                        <td>6</td>
                        <td><span class="fw-semibold text-danger d-flex align-items-center gap-2"><span class="mention-badge bg-danger-100 text-danger-600">I</span> Insuffisant</span></td>
                        <td><input type="number" class="form-control radius-8" id="mention_insuffisant" min="0" max="100" step="0.5" value="0"></td>
                        <td><input type="text" class="form-control radius-8" id="mention_insuffisant_libelle" value="Insuffisant"></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <small class="text-secondary-light d-block mt-12"><i class="ri-information-line"></i> La mention est calculée automatiquement sur les bulletins et fiches selon le pourcentage de la moyenne (moyenne / note maximale × 100).</small>
              </div>
            </div>
          </div>

          <!-- Onglet Notation & Pédagogie -->
          <div class="tab-pane fade" id="pane-notation">
            <div class="card shadow-1 radius-12">
              <div class="card-header py-16 px-24 border-bottom bg-base d-flex align-items-center gap-12">
                <span class="d-flex align-items-center justify-content-center bg-success-100 text-success-600 radius-4" style="width:36px;height:36px;"><i class="ri-calculator-line text-lg"></i></span>
                <div>
                  <h6 class="text-lg fw-semibold mb-0">Notation & Pédagogie</h6>
                  <small class="text-secondary-light">Configuration du calcul des notes, points et pondérations</small>
                </div>
              </div>
              <div class="card-body p-24">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Facteur points / heure</label>
                    <input type="number" class="form-control radius-8" id="facteur_points_heure" min="0" step="0.01">
                    <small class="text-secondary-light">Points attribués par heure de cours hebdomadaire</small>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Ressources à l'examen (%)</label>
                    <input type="number" class="form-control radius-8" id="pourcentage_ressources_examen" min="0" max="100" step="0.01">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Compétences à l'examen (%)</label>
                    <input type="number" class="form-control radius-8" id="pourcentage_competences_examen" min="0" max="100" step="0.01">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Seuil de moyenne (%)</label>
                    <input type="number" class="form-control radius-8" id="seuil_moyenne" min="0" max="100" step="0.01">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Seuil matière (%)</label>
                    <input type="number" class="form-control radius-8" id="seuil_matiere" min="0" max="100" step="0.01">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Max de repêchage</label>
                    <input type="number" class="form-control radius-8" id="max_repechage" min="0">
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
                    <input type="password" class="form-control radius-8" id="email_smtp_pass" placeholder="Laisser vide pour conserver" autocomplete="new-password">
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

        <!-- Autres paramètres (dynamiques) -->
        <div class="card shadow-1 radius-12 mt-24">
          <div class="card-header py-16 px-24 border-bottom bg-base d-flex align-items-center gap-12">
            <span class="d-flex align-items-center justify-content-center bg-secondary-100 text-secondary-600 radius-4" style="width:36px;height:36px;"><i class="ri-settings-3-line text-lg"></i></span>
            <div>
              <h6 class="text-lg fw-semibold mb-0">Autres paramètres</h6>
              <small class="text-secondary-light">Champs générés automatiquement depuis la table <code>parametres</code></small>
            </div>
          </div>
          <div class="card-body p-24">
            <div id="dynamicEmpty" class="text-secondary-light text-sm"><i class="ri-information-line me-1"></i>Aucun paramètre supplémentaire</div>
            <div class="row g-3" id="dynamicParams"></div>
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
          <div class="mb-16 bg-neutral-50 rounded-8 p-20 d-flex align-items-center justify-content-center" style="min-height:130px;border:1px dashed var(--border-color,#e4e7ec);">
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

      <!-- Image de connexion -->
      <div class="card shadow-1 radius-12 mb-24">
        <div class="card-header py-16 px-24 border-bottom bg-base">
          <h6 class="text-lg fw-semibold mb-0 d-flex align-items-center gap-8">
            <i class="ri-image-2-line text-primary-600"></i> Image de connexion
          </h6>
        </div>
        <div class="card-body p-24 text-center">
          <div class="mb-16 bg-neutral-50 rounded-8 p-20 d-flex align-items-center justify-content-center" style="min-height:130px;border:1px dashed var(--border-color,#e4e7ec);">
            <img src="<?= base_url($this->Model->get_setting('login_img', 'assets/images/thumbs/login-img.png')) ?>" alt="Login Image" class="img-fluid" style="max-height:110px;object-fit:cover;" id="loginImg" onerror="this.style.display='none';this.parentElement.innerHTML='<i class=\'ri-image-line text-secondary-light\' style=\'font-size:48px\'></i>'">
          </div>
          <small class="text-secondary-light d-block mb-12">JPG, PNG, GIF, WEBP — max 2MB</small>
          <button type="button" class="btn btn-outline-primary-600 px-24 py-10 radius-8" onclick="document.getElementById('loginImgInput').click()">
            <i class="ri-upload-line me-1"></i> Changer l'image
          </button>
          <input type="file" id="loginImgInput" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp">
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

  <!-- Barre d'actions sticky (enregistrement sensible) -->
  <div class="settings-actions d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3 flex-wrap">
      <span class="info-chip d-flex align-items-center gap-2"><i class="ri-save-3-line"></i> Dernière sauvegarde : <strong id="lastSaved">—</strong></span>
      <span class="info-chip d-flex align-items-center gap-2" id="changesCountChip"><i class="ri-edit-2-line"></i> <strong id="changesCount">0</strong> modification(s)</span>
    </div>
    <div class="d-flex align-items-center gap-2 flex-shrink-0 flex-wrap">
      <button type="button" id="btnReset" class="btn btn-outline-secondary-600 px-24 py-10 radius-8 d-flex align-items-center gap-2" onclick="resetForm()">
        <i class="ri-refresh-line"></i> Réinitialiser
      </button>
      <button type="button" id="btnSave" class="btn btn-primary-600 px-28 py-10 radius-8 d-flex align-items-center gap-2 fw-semibold" onclick="confirmSave()">
        <i class="ri-save-3-line text-lg"></i> Enregistrer les modifications
      </button>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>assets/js/api.js?v=<?= filemtime(FCPATH.'assets/js/api.js') ?>"></script>
<script>
let anneesData = [];
let periodesData = [];
let initialValues = {};
let lastSaved = null;

const STATIC_KEYS = new Set([
  'nom_ecole','telephone_ecole','email_ecole','adresse_ecole',
  'heure_debut_journee','duree_cours','duree_pause','duree_vigie','duree_culte','nb_creneaux_jour','jour_special','jour_special_actif',
  'devise','points_conduite_defaut',
  'mention_excellent','mention_excellent_libelle','mention_tres_bien','mention_tres_bien_libelle',
  'mention_bien','mention_bien_libelle','mention_assez_bien','mention_assez_bien_libelle',
  'mention_passable','mention_passable_libelle','mention_insuffisant','mention_insuffisant_libelle',
  'echelle_notes','email_protocol','email_smtp_host','email_smtp_user','email_smtp_pass',
  'email_smtp_port','email_smtp_crypto','email_sendmail_path',
  'seuil_moyenne','seuil_matiere','max_repechage',
  'facteur_points_heure','pourcentage_ressources_examen','pourcentage_competences_examen',
  'logo_ecole','favicon_ecole','login_img','annee_active','periode_active'
]);

const SAFE_KEYS = new Set([
  'nom_ecole','telephone_ecole','email_ecole','adresse_ecole',
  'heure_debut_journee','duree_cours','duree_pause','duree_vigie','nb_creneaux_jour',
  'email_smtp_host','email_smtp_user','email_sendmail_path'
]);

const FIELD_LABELS = {
  nom_ecole: 'Nom de l\'école', telephone_ecole: 'Téléphone', email_ecole: 'Email', adresse_ecole: 'Adresse',
  heure_debut_journee: 'Heure de début', duree_cours: 'Durée d\'un cours', duree_pause: 'Durée pause',
  duree_vigie: 'Vigile', nb_creneaux_jour: 'Créneaux/jour', devise: 'Devise',
  points_conduite_defaut: 'Points conduite', echelle_notes: 'Échelle des notes',
  email_protocol: 'Protocole email', email_smtp_host: 'Hôte SMTP', email_smtp_user: 'Utilisateur SMTP',
  email_smtp_pass: 'Mot de passe SMTP', email_smtp_port: 'Port SMTP', email_smtp_crypto: 'Cryptage',
  seuil_moyenne: 'Seuil de moyenne', seuil_matiere: 'Seuil matière', max_repechage: 'Max de repêchage',
  facteur_points_heure: 'Facteur points/heure',
  pourcentage_ressources_examen: 'Ressources à l\'examen (%)', pourcentage_competences_examen: 'Compétences à l\'examen (%)'
};

function fieldLabel(k) {
  if (FIELD_LABELS[k]) return FIELD_LABELS[k];
  const base = k.replace(/_libelle$/, '').replace(/_/g, ' ');
  const cap = base.replace(/\b\w/g, c => c.toUpperCase());
  return k.endsWith('_libelle') ? cap + ' (libellé)' : cap;
}

function isSensitiveKey(k) {
  if (SAFE_KEYS.has(k)) return false;
  if (k === 'email_smtp_pass' || k === 'logo_ecole' || k === 'favicon_ecole' || k === 'login_img') return true;
  return true;
}

function esc(s) {
  return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function renderDynamicParams(s) {
  const container = document.getElementById('dynamicParams');
  if (!container) return;
  container.innerHTML = '';
  Object.keys(s).sort().forEach(k => {
    if (STATIC_KEYS.has(k)) return;
    const v = (s[k] !== undefined && s[k] !== null) ? s[k] : '';
    const col = document.createElement('div');
    col.className = 'col-md-4';
    const label = document.createElement('label');
    label.className = 'form-label fw-semibold text-primary-light text-sm mb-8';
    label.textContent = fieldLabel(k);
    const input = document.createElement('input');
    input.className = 'form-control radius-8 dyn-param';
    input.dataset.key = k;
    input.value = v;
    if (/^-?\d+(\.\d+)?$/.test(String(v))) input.type = 'number';
    col.appendChild(label);
    col.appendChild(input);
    container.appendChild(col);
  });
  const empty = document.getElementById('dynamicEmpty');
  if (empty) empty.classList.toggle('d-none', container.children.length > 0);
}

function collectFormData() {
  const d = {
    nom_ecole: document.getElementById('nom_ecole').value,
    telephone_ecole: document.getElementById('telephone_ecole').value,
    email_ecole: document.getElementById('email_ecole').value,
    adresse_ecole: document.getElementById('adresse_ecole').value,
    heure_debut_journee: document.getElementById('heure_debut_journee').value || '07:30',
    duree_cours: document.getElementById('duree_cours').value || '45',
    duree_pause: document.getElementById('duree_pause').value || '20',
    duree_vigie: document.getElementById('duree_vigie').value || '10',
    nb_creneaux_jour: document.getElementById('nb_creneaux_jour').value || '8',
    jour_special: document.getElementById('jour_special').value || 'mardi',
    duree_culte: document.getElementById('duree_culte').value || '35',
    jour_special_actif: document.getElementById('jour_special_actif').checked ? '1' : '0',
    devise: document.getElementById('devise').value,
    email_protocol: document.getElementById('email_protocol').value,
    email_smtp_host: document.getElementById('email_smtp_host').value,
    email_smtp_user: document.getElementById('email_smtp_user').value,
    email_smtp_pass: document.getElementById('email_smtp_pass').value,
    email_smtp_port: document.getElementById('email_smtp_port').value || '587',
    email_smtp_crypto: document.getElementById('email_smtp_crypto').value,
    email_sendmail_path: document.getElementById('email_sendmail_path').value,
    points_conduite_defaut: document.getElementById('points_conduite_defaut').value || '60',
    echelle_notes: document.getElementById('echelle_notes').value || '100',
    mention_excellent: document.getElementById('mention_excellent').value || '90',
    mention_excellent_libelle: document.getElementById('mention_excellent_libelle').value || 'Excellent',
    mention_tres_bien: document.getElementById('mention_tres_bien').value || '80',
    mention_tres_bien_libelle: document.getElementById('mention_tres_bien_libelle').value || 'Très Bien',
    mention_bien: document.getElementById('mention_bien').value || '70',
    mention_bien_libelle: document.getElementById('mention_bien_libelle').value || 'Bien',
    mention_assez_bien: document.getElementById('mention_assez_bien').value || '60',
    mention_assez_bien_libelle: document.getElementById('mention_assez_bien_libelle').value || 'Assez Bien',
    mention_passable: document.getElementById('mention_passable').value || '40',
    mention_passable_libelle: document.getElementById('mention_passable_libelle').value || 'Passable',
    mention_insuffisant: document.getElementById('mention_insuffisant').value || '0',
    mention_insuffisant_libelle: document.getElementById('mention_insuffisant_libelle').value || 'Insuffisant',
    facteur_points_heure: document.getElementById('facteur_points_heure').value,
    pourcentage_ressources_examen: document.getElementById('pourcentage_ressources_examen').value,
    pourcentage_competences_examen: document.getElementById('pourcentage_competences_examen').value,
    seuil_moyenne: document.getElementById('seuil_moyenne').value,
    seuil_matiere: document.getElementById('seuil_matiere').value,
    max_repechage: document.getElementById('max_repechage').value
  };
  document.querySelectorAll('.dyn-param').forEach(inp => { d[inp.dataset.key] = inp.value; });
  return d;
}

function computeDiff(current) {
  const changes = {};
  Object.keys(current).forEach(k => {
    const cur = current[k] ?? '';
    const old = (initialValues[k] !== undefined && initialValues[k] !== null) ? String(initialValues[k]) : '';
    if (k === 'email_smtp_pass' && cur === '') return;
    if (String(cur) !== old) {
      changes[k] = { label: fieldLabel(k), old, new: cur, sensitive: isSensitiveKey(k) };
    }
  });
  return changes;
}

function updateDirty() {
  const changes = computeDiff(collectFormData());
  const n = Object.keys(changes).length;
  const chip = document.getElementById('dirtyChip');
  const countEl = document.getElementById('changesCount');
  if (n > 0) { chip.classList.remove('d-none'); chip.classList.add('d-inline-flex'); }
  else { chip.classList.add('d-none'); chip.classList.remove('d-inline-flex'); }
  if (countEl) countEl.textContent = n;
  document.querySelectorAll('.tab-dirty-badge').forEach(b => b.classList.remove('show'));
  document.querySelectorAll('#settingsForm .form-control, #settingsForm .form-select').forEach(el => el.classList.remove('field-dirty'));
  Object.keys(changes).forEach(k => {
    const el = document.getElementById(k);
    if (el) {
      el.classList.add('field-dirty');
      const pane = el.closest('.tab-pane');
      if (pane) {
        const b = document.querySelector('[data-bs-target="#' + pane.id + '"] .tab-dirty-badge');
        if (b) b.classList.add('show');
      }
    }
  });
  document.querySelectorAll('.dyn-param').forEach(inp => { inp.classList.toggle('field-dirty', !!changes[inp.dataset.key]); });
}

async function loadSettings() {
  try {
    const r = await API.parametres.list();
    if (!r.success || !r.data) return;
    const s = r.data;
    if (s.nom_ecole) document.getElementById('nom_ecole').value = s.nom_ecole;
    if (s.telephone_ecole) document.getElementById('telephone_ecole').value = s.telephone_ecole;
    if (s.email_ecole) document.getElementById('email_ecole').value = s.email_ecole;
    if (s.adresse_ecole) document.getElementById('adresse_ecole').value = s.adresse_ecole;
    if (s.heure_debut_journee) document.getElementById('heure_debut_journee').value = s.heure_debut_journee;
    if (s.duree_cours) document.getElementById('duree_cours').value = s.duree_cours;
    if (s.duree_pause) document.getElementById('duree_pause').value = s.duree_pause;
    if (s.duree_vigie) document.getElementById('duree_vigie').value = s.duree_vigie;
    if (s.nb_creneaux_jour) document.getElementById('nb_creneaux_jour').value = s.nb_creneaux_jour;
    if (s.jour_special) document.getElementById('jour_special').value = s.jour_special;
    if (s.duree_culte) document.getElementById('duree_culte').value = s.duree_culte;
    document.getElementById('jour_special_actif').checked = (s.jour_special_actif === '1' || s.jour_special_actif === 'true' || s.jour_special_actif === 'on');
    if (s.devise) document.getElementById('devise').value = s.devise;
    if (s.email_protocol) document.getElementById('email_protocol').value = s.email_protocol;
    if (s.email_smtp_host) document.getElementById('email_smtp_host').value = s.email_smtp_host;
    if (s.email_smtp_user) document.getElementById('email_smtp_user').value = s.email_smtp_user;
    if (s.email_smtp_port) document.getElementById('email_smtp_port').value = s.email_smtp_port;
    if (s.email_smtp_crypto) document.getElementById('email_smtp_crypto').value = s.email_smtp_crypto;
    if (s.email_sendmail_path) document.getElementById('email_sendmail_path').value = s.email_sendmail_path;
    if (s.points_conduite_defaut) document.getElementById('points_conduite_defaut').value = s.points_conduite_defaut;
    if (s.echelle_notes) document.getElementById('echelle_notes').value = s.echelle_notes;
    ['mention_excellent','mention_tres_bien','mention_bien','mention_assez_bien','mention_passable','mention_insuffisant'].forEach(k => {
      if (s[k] !== undefined && s[k] !== null && s[k] !== '') document.getElementById(k).value = s[k];
      if (s[k + '_libelle'] !== undefined && s[k + '_libelle'] !== null && s[k + '_libelle'] !== '') document.getElementById(k + '_libelle').value = s[k + '_libelle'];
    });
    if (s.logo_ecole) document.getElementById('logoImg').src = '<?= base_url() ?>' + s.logo_ecole;
    if (s.favicon_ecole) document.getElementById('faviconImg').src = '<?= base_url() ?>' + s.favicon_ecole;
    if (s.login_img) document.getElementById('loginImg').src = '<?= base_url() ?>' + s.login_img;
    ['facteur_points_heure','pourcentage_ressources_examen','pourcentage_competences_examen','seuil_moyenne','seuil_matiere','max_repechage'].forEach(k => {
      if (s[k] !== undefined && s[k] !== null && s[k] !== '') document.getElementById(k).value = s[k];
    });
    renderDynamicParams(s);
    initialValues = collectFormData();
    updateDirty();
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
    renderPeriodes();
    updatePeriodeBadge();
  } catch (err) { console.error(err); }
}

function renderPeriodes() {
  const sel = document.getElementById('periode_active');
  const anneeSel = document.getElementById('annee_active');
  const aid = anneeSel.value;
  sel.innerHTML = '<option value="">Sélectionner...</option>';
  periodesData.forEach(p => {
    if (aid && String(p.id_annee) !== String(aid)) return;
    const opt = document.createElement('option');
    opt.value = p.id_periode;
    opt.textContent = p.libelle + (p.annee_libelle ? ' (' + p.annee_libelle + ')' : '');
    if (p.est_en_cours == 1) opt.selected = true;
    sel.appendChild(opt);
  });
  updatePeriodeBadge();
}

document.addEventListener('change', function(e) {
  if (e.target && e.target.id === 'annee_active') renderPeriodes();
});

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
  const anneeSel = document.getElementById('annee_active');
  const aid = anneeSel ? anneeSel.value : '';
  const active = periodesData.find(p => p.est_en_cours == 1 && (!aid || String(p.id_annee) === String(aid)));
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
  const res = await Swal.fire({
    title: 'Activer cette année scolaire ?',
    html: 'L\'année <strong>' + esc(a.libelle) + '</strong> deviendra l\'année active.<br><small class="text-secondary-light">Tous les bulletins et calculs utiliseront cette année.</small>',
    icon: 'warning', showCancelButton: true, confirmButtonText: 'Oui, activer', cancelButtonText: 'Annuler',
    confirmButtonColor: '#25A194', cancelButtonColor: '#667085', reverseButtons: true
  });
  if (!res.isConfirmed) return;
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
  const res = await Swal.fire({
    title: 'Activer ce trimestre ?',
    html: 'Le trimestre <strong>' + esc(p.libelle) + '</strong> deviendra le trimestre actif.',
    icon: 'warning', showCancelButton: true, confirmButtonText: 'Oui, activer', cancelButtonText: 'Annuler',
    confirmButtonColor: '#25A194', cancelButtonColor: '#667085', reverseButtons: true
  });
  if (!res.isConfirmed) return;
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

function showMessage(type, text) {
  const msg = document.getElementById('settingsMessage');
  msg.className = 'alert d-flex align-items-center gap-8 radius-8 mb-16 alert-' + type;
  msg.innerHTML = (type === 'success' ? '<i class="ri-check-line"></i>' : '<i class="ri-close-circle-line"></i>') + ' ' + text;
  setTimeout(() => msg.className = 'alert d-none mb-16', 4000);
}

async function doSave(data) {
  const btn = document.getElementById('btnSave');
  const btnTop = document.getElementById('btnSaveTop');
  if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enregistrement...'; }
  if (btnTop) { btnTop.disabled = true; }
  try {
    const r = await API.parametres.update(data);
    if (r.success) {
      const now = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
      document.getElementById('lastSaved').textContent = now;
      showMessage('success', 'Paramètres enregistrés avec succès');
      Toast.fire({ icon: 'success', title: 'Paramètres enregistrés' });
      await loadSettings();
    } else {
      showMessage('danger', r.message || 'Erreur');
    }
  } catch (err) {
    showMessage('danger', 'Erreur de connexion');
  } finally {
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ri-save-3-line text-lg"></i> Enregistrer les modifications'; }
    if (btnTop) { btnTop.disabled = false; }
  }
}

async function confirmSave() {
  const data = collectFormData();
  const changes = computeDiff(data);
  const n = Object.keys(changes).length;
  if (n === 0) {
    Swal.fire({ icon: 'info', title: 'Aucune modification', text: 'Aucun paramètre n\'a été modifié.', confirmButtonColor: '#25A194' });
    return;
  }
  const entries = Object.entries(changes);
  let rows = '';
  entries.slice(0, 15).forEach(([k, c]) => {
    const oldV = (k === 'email_smtp_pass' && c.old) ? '••••••••' : esc(c.old || '—');
    const newV = (k === 'email_smtp_pass' && c.new) ? '•••••••• (nouveau mot de passe)' : (c.new === '' ? '—' : esc(c.new));
    rows += '<div class="diff-row"><div class="diff-key">' + (c.sensitive ? '<i class="ri-shield-keyhole-line text-warning-600 me-1"></i>' : '') + esc(c.label) + '</div><div class="diff-old">' + oldV + '</div><div class="diff-arrow">→</div><div class="diff-new">' + newV + '</div></div>';
  });
  if (entries.length > 15) rows += '<div class="text-sm text-secondary-light mt-2">… et ' + (entries.length - 15) + ' autre(s) modification(s)</div>';
  const warn = '<div class="sensitive-warn mt-3 d-flex gap-2 align-items-start"><i class="ri-alert-fill text-danger-600 mt-1"></i><small class="text-secondary-dark">Ces paramètres sont <strong>sensibles</strong> et affectent les bulletins, reçus et communications. Vérifiez attentivement avant de confirmer.</small></div>';
  const res = await Swal.fire({
    title: 'Confirmer l\'enregistrement ?',
    html: '<div style="text-align:left;max-height:320px;overflow:auto;font-size:13px;">' + rows + warn + '</div>',
    icon: 'question',
    showCancelButton: true, confirmButtonText: 'Oui, enregistrer', cancelButtonText: 'Annuler',
    confirmButtonColor: '#25A194', cancelButtonColor: '#667085', reverseButtons: true
  });
  if (!res.isConfirmed) return;
  await doSave(data);
}

function resetForm() {
  const n = Object.keys(computeDiff(collectFormData())).length;
  if (n === 0) { Swal.fire({ icon: 'info', title: 'Rien à réinitialiser', text: 'Aucune modification en attente.', confirmButtonColor: '#25A194' }); return; }
  Swal.fire({
    title: 'Annuler les modifications ?',
    text: 'Toutes les valeurs reviendront à l\'état sauvegardé.',
    icon: 'warning', showCancelButton: true, confirmButtonText: 'Oui, réinitialiser', cancelButtonText: 'Non',
    confirmButtonColor: '#25A194', cancelButtonColor: '#667085', reverseButtons: true
  }).then(async (res) => {
    if (!res.isConfirmed) return;
    await loadSettings();
    Toast.fire({ icon: 'info', title: 'Modifications annulées' });
  });
}

// Ajout des badges de modifications sur les onglets
document.querySelectorAll('.nav-pills.bordered-tab .nav-link').forEach(link => {
  const s = document.createElement('span');
  s.className = 'tab-dirty-badge';
  link.appendChild(s);
});

// --- Dynamique Ressources/Compétences : somme toujours = 100% + au moins une active ---
function syncPourcentagesExamen(changed) {
  const r = document.getElementById('pourcentage_ressources_examen');
  const c = document.getElementById('pourcentage_competences_examen');
  const rv = parseFloat(r.value);
  const cv = parseFloat(c.value);
  if (changed === 'ress') {
    if (!isNaN(rv) && rv >= 0 && rv <= 100) c.value = (100 - rv).toFixed(2);
  } else {
    if (!isNaN(cv) && cv >= 0 && cv <= 100) r.value = (100 - cv).toFixed(2);
  }
  updateDirty();
}

document.getElementById('pourcentage_ressources_examen').addEventListener('input', function() { syncPourcentagesExamen('ress'); });
document.getElementById('pourcentage_competences_examen').addEventListener('input', function() { syncPourcentagesExamen('comp'); });

// Suivi des modifications
document.getElementById('settingsForm').addEventListener('input', updateDirty);
document.getElementById('settingsForm').addEventListener('change', updateDirty);

// Validation de la soumission du formulaire (touche Entrée)
document.getElementById('settingsForm').addEventListener('submit', function(e) { e.preventDefault(); confirmSave(); });

document.getElementById('logoInput').addEventListener('change', async function() {
  if (!this.files || !this.files[0]) return;
  const res = await Swal.fire({
    title: 'Confirmer le changement du logo ?', text: 'Le nouveau logo apparaîtra sur les reçus et documents.',
    icon: 'question', showCancelButton: true, confirmButtonText: 'Oui, changer', cancelButtonText: 'Annuler',
    confirmButtonColor: '#25A194', cancelButtonColor: '#667085', reverseButtons: true
  });
  if (!res.isConfirmed) { this.value = ''; return; }
  const fd = new FormData(); fd.append('logo', this.files[0]); fd.append('csrf_test_name', typeof CSRF_TOKEN !== 'undefined' ? CSRF_TOKEN : '');
  try {
    const res2 = await fetch(API.base_url + 'api/parametres/upload_logo', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const r = await res2.json();
    if (r.success) {
      document.getElementById('logoImg').src = API.base_url + r.data.path;
      Toast.fire({ icon: 'success', title: 'Logo mis à jour' });
      await loadSettings();
    } else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message || 'Erreur upload' });
  } catch (e) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion' }); }
});

document.getElementById('faviconInput').addEventListener('change', async function() {
  if (!this.files || !this.files[0]) return;
  const res = await Swal.fire({
    title: 'Confirmer le changement du favicon ?', text: 'Le favicon apparaîtra dans l\'onglet du navigateur.',
    icon: 'question', showCancelButton: true, confirmButtonText: 'Oui, changer', cancelButtonText: 'Annuler',
    confirmButtonColor: '#25A194', cancelButtonColor: '#667085', reverseButtons: true
  });
  if (!res.isConfirmed) { this.value = ''; return; }
  const fd = new FormData(); fd.append('favicon', this.files[0]); fd.append('csrf_test_name', typeof CSRF_TOKEN !== 'undefined' ? CSRF_TOKEN : '');
  try {
    const res2 = await fetch(API.base_url + 'api/parametres/upload_favicon', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const r = await res2.json();
    if (r.success) {
      document.getElementById('faviconImg').src = API.base_url + r.data.path;
      Toast.fire({ icon: 'success', title: 'Favicon mis à jour' });
      await loadSettings();
    } else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message || 'Erreur upload' });
  } catch (e) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion' }); }
});

document.getElementById('loginImgInput').addEventListener('change', async function() {
  if (!this.files || !this.files[0]) return;
  const res = await Swal.fire({
    title: 'Confirmer le changement de l\'image de connexion ?', text: 'Cette image apparaîtra sur la page de connexion.',
    icon: 'question', showCancelButton: true, confirmButtonText: 'Oui, changer', cancelButtonText: 'Annuler',
    confirmButtonColor: '#25A194', cancelButtonColor: '#667085', reverseButtons: true
  });
  if (!res.isConfirmed) { this.value = ''; return; }
  const fd = new FormData(); fd.append('login_img', this.files[0]); fd.append('csrf_test_name', typeof CSRF_TOKEN !== 'undefined' ? CSRF_TOKEN : '');
  try {
    const res2 = await fetch(API.base_url + 'api/parametres/upload_login_img', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const r = await res2.json();
    if (r.success) {
      document.getElementById('loginImg').src = API.base_url + r.data.path;
      Toast.fire({ icon: 'success', title: 'Image de connexion mise à jour' });
      await loadSettings();
    } else Swal.fire({ icon: 'error', title: 'Erreur', text: r.message || 'Erreur upload' });
  } catch (e) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion' }); }
});

async function testEmail() {
  const email = document.getElementById('email_ecole').value || document.getElementById('email_smtp_user').value;
  if (!email) { Swal.fire({ icon: 'warning', title: 'Email requis', text: 'Configurez l\'email ou l\'utilisateur SMTP' }); return; }
  const res = await Swal.fire({
    title: 'Envoyer un email de test ?', html: 'Un email de test sera envoyé à <strong>' + esc(email) + '</strong>.',
    icon: 'question', showCancelButton: true, confirmButtonText: 'Oui, envoyer', cancelButtonText: 'Annuler',
    confirmButtonColor: '#25A194', cancelButtonColor: '#667085', reverseButtons: true
  });
  if (!res.isConfirmed) return;
  const btn = document.querySelector('.btn-success-600');
  if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Envoi...'; }
  try {
    const res2 = await fetch(API.base_url + 'api/email/test', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ to: email, csrf_test_name: typeof CSRF_TOKEN !== 'undefined' ? CSRF_TOKEN : '' }) });
    const r = await res2.json();
    if (r.success) Swal.fire({ icon: 'success', title: 'Email envoyé', text: 'Vérifiez votre boîte de réception.' });
    else Swal.fire({ icon: 'error', title: 'Échec', text: r.message || 'Erreur inconnue' });
  } catch (e) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur de connexion' }); }
  finally { if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ri-mail-send-line"></i> Tester l\'envoi'; } }
}

(function() { loadAnnees().then(loadPeriodes).then(loadSettings); })();
</script>
<?php include VIEWPATH.'includes/Footer.php'; ?>