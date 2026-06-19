  
  <!-- User Info end -->
  <div class="sidebar-menu-area">
    <ul class="sidebar-menu" id="sidebar-menu">
      <li>
        <a href="<?=base_url('Dashboard')?>">
          <i class="ri-home-4-line"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li>
        <a href="<?=base_url('Parents/MesEnfants')?>">
          <i class="ri-group-line"></i>
          <span>Mes enfants</span>
        </a>
      </li>
      <li class="dropdown">
        <a href="javascript:void(0)">
          <i class="ri-graduation-cap-line"></i>
          <span>Étudiants</span>
        </a>
        <ul class="sidebar-submenu">
          <li>
            <a href="<?=base_url('Etudiants')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Étudiants
            </a>
          </li>
          <li>
            <a href="<?=base_url('Etudiants/Inscriptions')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Inscriptions
            </a>
          </li>
        </ul>
      </li>
      <li>
        <a href="<?=base_url('Enseignants')?>">
          <i class="ri-user-follow-line"></i>
          <span>Enseignants</span>
        </a>
      </li>
      <li class="dropdown">
        <a href="javascript:void(0)">
          <i class="ri-list-view"></i>
          <span>Classes</span>
        </a>
        <ul class="sidebar-submenu">
          <li>
            <a href="<?=base_url('Classes/Sections')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Sections
            </a>
          </li>
          <li>
            <a href="<?=base_url('Classes/Matieres')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Matières
            </a>
          </li>
          <li>
            <a href="<?=base_url('Enseignants/Programmes')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Programmes
            </a>
          </li>
          <li>
            <a href="<?=base_url('Classes/Enseignements')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Enseignements
            </a>
          </li>
          <li>
            <a href="<?=base_url('Classes/Periodes')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Périodes
            </a>
          </li>
          <li>
            <a href="<?=base_url('Classes/Annees')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Années scolaires
            </a>
          </li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="javascript:void(0)">
          <i class="ri-money-dollar-circle-line"></i>
          <span>Scolarité</span>
        </a>
        <ul class="sidebar-submenu">
          <li>
            <a href="<?=base_url('Types_frais')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Types de frais
            </a>
          </li>
          <li>
            <a href="<?=base_url('Paiements')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Paiements
            </a>
          </li>
          <li>
            <a href="<?=base_url('Recus')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Reçus
            </a>
          </li>
          <li>
            <a href="<?=base_url('Echeances')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Échéances
            </a>
          </li>
          <li>
            <a href="<?=base_url('Paiement_recus')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Reçus de paiement
            </a>
          </li>
          <li>
            <a href="<?=base_url('Toilettes')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Matériels de toilettes
            </a>
          </li>
          <li>
            <a href="<?=base_url('Materiels')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Matériels scolaires
            </a>
          </li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="javascript:void(0)">
          <i class="ri-stack-line"></i>
          <span>Produits</span>
        </a>
        <ul class="sidebar-submenu">
          <li>
            <a href="<?=base_url('Produits/Stock')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Stock
            </a>
          </li>
        </ul>
      </li>
      <li>
        <a href="<?=base_url('Uniformes')?>">
          <i class="ri-shirt-line"></i>
          <span>Uniformes</span>
        </a>
      </li>
      <li class="dropdown">
        <a href="javascript:void(0)">
          <i class="ri-shopping-cart-line"></i>
          <span>Commandes</span>
        </a>
        <ul class="sidebar-submenu">
          <li>
            <a href="<?=base_url('Commandes')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Commandes
            </a>
          </li>
          <li>
            <a href="<?=base_url('Commande_details')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Détails commandes
            </a>
          </li>
        </ul>
      </li>
      <li>
        <a href="<?=base_url('Absences')?>">
          <i class="ri-user-unfollow-line"></i>
          <span>Absences</span>
        </a>
      </li>
      <li class="dropdown">
        <a href="javascript:void(0)">
          <i class="ri-file-edit-line"></i>
          <span>Notes</span>
        </a>
        <ul class="sidebar-submenu">
          <li>
            <a href="<?=base_url('Notes')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Saisie notes
            </a>
          </li>
          <li>
            <a href="<?=base_url('Evaluations')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Évaluations
            </a>
          </li>
          <li>
            <a href="<?=base_url('Fiches')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Fiches de points
            </a>
          </li>
          <li>
            <a href="<?=base_url('Notes/Bulletins')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Bulletins
            </a>
          </li>
        </ul>
      </li>
      <li class="dropdown">
        <a href="javascript:void(0)">
          <i class="ri-time-line"></i>
          <span>Horaires</span>
        </a>
        <ul class="sidebar-submenu">
          <li>
            <a href="<?=base_url('Horaires')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Emploi du temps
            </a>
          </li>
          <li>
            <a href="<?=base_url('Creneaux')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Créneaux
            </a>
          </li>
          <li>
            <a href="<?=base_url('Disponibilites')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Disponibilités
            </a>
          </li>
        </ul>
      </li>
      <li>
        <a href="<?=base_url('Evenements')?>">
          <i class="ri-calendar-check-line"></i>
          <span>Événements</span>
        </a>
      </li>
      <li>
        <a href="<?=base_url('Bibliotheque')?>">
          <i class="ri-book-shelf-line"></i>
          <span>Bibliothèque</span>
        </a>
      </li>
      <li>
        <a href="<?=base_url('Certificats')?>">
          <i class="ri-award-line"></i>
          <span>Certificats</span>
        </a>
      </li>
      <li>
        <a href="<?=base_url('Comptabilite')?>">
          <i class="ri-calculator-line"></i>
          <span>Comptabilité</span>
        </a>
      </li>
      <li class="dropdown">
        <a href="javascript:void(0)">
          <i class="ri-user-heart-line"></i>
          <span>Employés</span>
        </a>
        <ul class="sidebar-submenu">
          <li>
            <a href="<?=base_url('Employes')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Employés
            </a>
          </li>
          <li>
            <a href="<?=base_url('Paie')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Paie
            </a>
          </li>
          <li>
            <a href="<?=base_url('Paie_contrat')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Contrats
            </a>
          </li>
          <li>
            <a href="<?=base_url('Paie_bulletin')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Bulletins paie
            </a>
          </li>
          <li>
            <a href="<?=base_url('Paie_bulletin_detail')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Détails bulletins
            </a>
          </li>
          <li>
            <a href="<?=base_url('Paie_rubrique')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Rubriques
            </a>
          </li>
        </ul>
      </li>
      <li>
        <a href="<?=base_url('Rapports')?>">
          <i class="ri-bar-chart-line"></i>
          <span>Rapports</span>
        </a>
      </li>
      <li>
        <a href="<?=base_url('Messages')?>">
          <i class="ri-message-line"></i>
          <span>Messages</span>
        </a>
      </li>
      <li class="dropdown">
        <a href="javascript:void(0)">
          <i class="ri-shield-user-line"></i>
          <span>Administration</span>
        </a>
        <ul class="sidebar-submenu">
          <li>
            <a href="<?=base_url('Parametres')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Paramètres
            </a>
          </li>
          <li>
            <a href="<?=base_url('Utilisateurs')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Utilisateurs
            </a>
          </li>
          <li>
            <a href="<?=base_url('Administration/Roles')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Rôles
            </a>
          </li>
          <li>
            <a href="<?=base_url('Administration/Permissions')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Permissions
            </a>
          </li>
          <li>
            <a href="<?=base_url('Administration/Menus')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Menus
            </a>
          </li>
          <li>
            <a href="<?=base_url('Utilisateurs/Audit')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Journal d'audit
            </a>
          </li>
          <li>
            <a href="<?=base_url('Administration/Operations')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Opérations groupées
            </a>
          </li>
          <li>
            <a href="<?=base_url('Administration/Sauvegardes')?>">
              <i class="ri-circle-fill circle-icon w-auto"></i>
              Sauvegardes
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</aside>
<main class="dashboard-main">
  <div class="navbar-header shadow-1">
  <div class="row align-items-center justify-content-between">
    <div class="col-auto">
      <div class="d-flex flex-wrap align-items-center gap-4">
        <button type="button" class="sidebar-mobile-toggle" aria-label="Sidebar Mobile Toggler Button">
          <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
        </button>
        <form class="navbar-search">
          <input type="text" class="bg-transparent" name="search" placeholder="Search">
          <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
        </form>
      </div>
    </div>
    <div class="col-auto">
      <div class="d-flex flex-wrap align-items-center gap-3">
        <button type="button" data-theme-toggle
          class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center" aria-label="Dark & Light Mode Button"></button>
        <div class="dropdown d-inline-block">
          <button
            class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
            type="button" data-bs-toggle="dropdown" aria-label="Language Change Button">
            <img src="<?=base_url()?>assets/images/flags/flag1.png" alt="image" class="w-24 h-24 object-fit-cover rounded-circle">
          </button>
          <div class="dropdown-menu to-top dropdown-menu-sm">
            <div
              class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
              <div>
                <h6 class="text-lg text-primary-light fw-semibold mb-0">Choose Your Language</h6>
              </div>
            </div>

            <div class="max-h-400-px overflow-y-auto scroll-sm pe-8">
              <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="english">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="<?=base_url()?>assets/images/flags/flag1.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">English</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="english">
              </div>

              <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="japan">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="<?=base_url()?>assets/images/flags/flag2.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">Japan</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="japan">
              </div>

              <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="france">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="<?=base_url()?>assets/images/flags/flag3.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">France</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="france">
              </div>

              <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="germany">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="<?=base_url()?>assets/images/flags/flag4.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">Germany</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="germany">
              </div>

              <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="korea">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="<?=base_url()?>assets/images/flags/flag5.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">South Korea</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="korea">
              </div>

              <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="bangladesh">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="<?=base_url()?>assets/images/flags/flag6.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">Bangladesh</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="bangladesh">
              </div>

              <div class="form-check style-check d-flex align-items-center justify-content-between mb-16">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="india">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="<?=base_url()?>assets/images/flags/flag7.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">India</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="india">
              </div>
              <div class="form-check style-check d-flex align-items-center justify-content-between">
                <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="canada">
                  <span class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                    <img src="<?=base_url()?>assets/images/flags/flag8.png" alt="Image"
                      class="w-36-px h-36-px bg-success-subtle text-success-main rounded-circle flex-shrink-0">
                    <span class="text-md fw-semibold mb-0">Canada</span>
                  </span>
                </label>
                <input class="form-check-input" type="radio" name="crypto" id="canada">
              </div>
            </div>
          </div>
        </div><!-- Language dropdown end -->

        <div class="dropdown">
          <button
            class="has-indicator w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center position-relative"
            type="button" data-bs-toggle="dropdown" aria-label="Notification Button">
            <iconify-icon icon="iconoir:bell" class="text-primary-light text-xl"></iconify-icon>
            <span class="w-8-px h-8-px bg-danger-600 position-absolute end-0 top-0 rounded-circle mt-2 me-2"></span>
          </button>
          <div class="dropdown-menu to-top dropdown-menu-lg p-0">
            <div
              class="m-16 py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
              <div>
                <h6 class="text-lg text-primary-light fw-semibold mb-0">Notifications</h6>
              </div>
              <span
                class="text-primary-600 fw-semibold text-lg w-40-px h-40-px rounded-circle bg-base d-flex justify-content-center align-items-center">0</span>
            </div>

            <div class="max-h-400-px overflow-y-auto scroll-sm pe-4">
              <a href="javascript:void(0)"
                class="px-24 py-12 d-flex align-items-start gap-3 mb-2 justify-content-between">
                <div class="text-black hover-bg-transparent hover-text-primary d-flex align-items-center gap-3">
                  <span
                    class="w-44-px h-44-px bg-success-subtle text-success-main rounded-circle d-flex justify-content-center align-items-center flex-shrink-0">
                    <iconify-icon icon="bitcoin-icons:verify-outline" class="icon text-xxl"></iconify-icon>
                  </span>
                  <div>
                    <h6 class="text-md fw-semibold mb-4">Bienvenue</h6>
                    <p class="mb-0 text-sm text-secondary-light text-w-200-px">Aucune notification pour le moment.</p>
                  </div>
                </div>
              </a>
            </div>

            <div class="text-center py-12 px-16">
              <a href="javascript:void(0)" class="text-primary-600 fw-semibold text-md hover-underline">Voir toutes les notifications</a>
            </div>

          </div>
        </div><!-- Notification dropdown end -->

      </div>
    </div>
  </div>
</div>
  <div class="dashboard-main-body">
