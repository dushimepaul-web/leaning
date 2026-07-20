<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'Admin';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Redirection pour /index -> Admin
$route['index'] = 'Admin/index';
$route['index/(:any)'] = 'Admin/index';

// Fallback pour routes mal formées type Module/api
$route['Etudiants/api'] = 'Etudiants/Etudiants/api_list';
$route['Etudiants/api/(:any)'] = 'Etudiants/Etudiants/api_list';

// Auth
$route['Admin'] = 'Admin/index';
$route['Admin/Login'] = 'Admin/Login';
$route['Admin/do_login'] = 'Admin/do_login';
$route['Admin/register'] = 'Admin/register';
$route['Admin/do_register'] = 'Admin/do_register';
$route['Admin/forgot_password'] = 'Admin/forgot_password';
$route['Admin/do_forgot_password'] = 'Admin/do_forgot_password';
$route['Admin/verify_otp'] = 'Admin/verify_otp';
$route['Admin/do_verify_otp'] = 'Admin/do_verify_otp';
$route['Admin/reset_password'] = 'Admin/reset_password';
$route['Admin/do_reset_password'] = 'Admin/do_reset_password';
$route['Logout'] = 'Admin/Logout';

// Dashboard
$route['Dashboard'] = 'Dashboard/Dashboard/index';

// API Routes - Utilisateurs & Roles
$route['api/utilisateurs'] = 'Utilisateurs/Utilisateurs/api_list';
$route['api/utilisateurs/create'] = 'Utilisateurs/Utilisateurs/api_create';
$route['api/utilisateurs/(:any)'] = 'Utilisateurs/Utilisateurs/api_get/$1';
$route['api/utilisateurs/(:any)/update'] = 'Utilisateurs/Utilisateurs/api_update/$1';
$route['api/utilisateurs/(:any)/delete'] = 'Utilisateurs/Utilisateurs/api_delete/$1';
$route['api/roles'] = 'Utilisateurs/Roles/api_list';
$route['api/roles/create'] = 'Utilisateurs/Roles/api_create';
$route['api/roles/(:any)'] = 'Utilisateurs/Roles/api_get/$1';
$route['api/roles/(:any)/update'] = 'Utilisateurs/Roles/api_update/$1';
$route['api/roles/(:any)/delete'] = 'Utilisateurs/Roles/api_delete/$1';

// API - Etudiants
$route['api/etudiants'] = 'Etudiants/Etudiants/api_list';
$route['api/etudiants/create'] = 'Etudiants/Etudiants/api_create';
$route['api/etudiants/upload_photo'] = 'Etudiants/Etudiants/api_upload_photo';
$route['api/etudiants/(:any)'] = 'Etudiants/Etudiants/api_get/$1';
$route['api/etudiants/(:any)/update'] = 'Etudiants/Etudiants/api_update/$1';
$route['api/etudiants/(:any)/delete'] = 'Etudiants/Etudiants/api_delete/$1';

// API - Inscriptions
$route['api/inscriptions'] = 'Etudiants/Inscriptions/api_list';
$route['api/inscriptions/create'] = 'Etudiants/Inscriptions/api_create';
$route['api/inscriptions/(:any)'] = 'Etudiants/Inscriptions/api_get/$1';
$route['api/inscriptions/(:any)/update'] = 'Etudiants/Inscriptions/api_update/$1';
$route['api/inscriptions/(:any)/delete'] = 'Etudiants/Inscriptions/api_delete/$1';

// API - Enseignants
$route['api/enseignants'] = 'Enseignants/Enseignants/api_list';
$route['api/enseignants/create'] = 'Enseignants/Enseignants/api_create';
$route['api/enseignants/upload_photo'] = 'Enseignants/Enseignants/api_upload_photo';
$route['api/enseignants/(:any)'] = 'Enseignants/Enseignants/api_get/$1';
$route['api/enseignants/(:any)/update'] = 'Enseignants/Enseignants/api_update/$1';
$route['api/enseignants/(:any)/delete'] = 'Enseignants/Enseignants/api_delete/$1';

// API - Classes
$route['api/classes'] = 'Classes/Classes/api_list';
$route['api/classes/create'] = 'Classes/Classes/api_create';
$route['api/classes/(:any)'] = 'Classes/Classes/api_get/$1';
$route['api/classes/(:any)/update'] = 'Classes/Classes/api_update/$1';
$route['api/classes/(:any)/delete'] = 'Classes/Classes/api_delete/$1';
$route['api/sections'] = 'Classes/Sections/api_list';
$route['api/sections/create'] = 'Classes/Sections/api_create';
$route['api/sections/(:any)'] = 'Classes/Sections/api_get/$1';
$route['api/sections/(:any)/update'] = 'Classes/Sections/api_update/$1';
$route['api/sections/(:any)/delete'] = 'Classes/Sections/api_delete/$1';
$route['api/sections/(:any)/activate'] = 'Classes/Sections/api_activate/$1';
$route['api/sections/(:any)/deactivate'] = 'Classes/Sections/api_deactivate/$1';
$route['api/matieres'] = 'Classes/Matieres/api_list';
$route['api/matieres/create'] = 'Classes/Matieres/api_create';
$route['api/matieres/(:any)'] = 'Classes/Matieres/api_get/$1';
$route['api/matieres/(:any)/update'] = 'Classes/Matieres/api_update/$1';
$route['api/matieres/(:any)/delete'] = 'Classes/Matieres/api_delete/$1';

$route['api/periodes'] = 'Classes/Periodes/api_list';
$route['api/periodes/create'] = 'Classes/Periodes/api_create';
$route['api/periodes/(:any)'] = 'Classes/Periodes/api_get/$1';
$route['api/periodes/(:any)/update'] = 'Classes/Periodes/api_update/$1';
$route['api/periodes/(:any)/delete'] = 'Classes/Periodes/api_delete/$1';
$route['api/periodes/(:any)/activate'] = 'Classes/Periodes/api_activate/$1';
$route['api/periodes/(:any)/deactivate'] = 'Classes/Periodes/api_deactivate/$1';
$route['api/periodes/(:any)/active'] = 'Classes/Periodes/api_set_active/$1';

$route['api/enseignements'] = 'Classes/Enseignements/api_list';
$route['api/enseignements/create'] = 'Classes/Enseignements/api_create';
$route['api/enseignements/(:any)'] = 'Classes/Enseignements/api_get/$1';
$route['api/enseignements/(:any)/update'] = 'Classes/Enseignements/api_update/$1';
$route['api/enseignements/(:any)/delete'] = 'Classes/Enseignements/api_delete/$1';

// API - Frais & Paiements
$route['api/frais'] = 'Frais/Frais/api_list';
$route['api/frais/create'] = 'Frais/Frais/api_create';
$route['api/frais/(:any)'] = 'Frais/Frais/api_get/$1';
$route['api/frais/(:any)/update'] = 'Frais/Frais/api_update/$1';
$route['api/frais/(:any)/delete'] = 'Frais/Frais/api_delete/$1';


// API - Notes & Evaluations
$route['api/notes'] = 'Notes/Notes/api_list';
$route['api/notes/create'] = 'Notes/Notes/api_create';
$route['api/notes/batch'] = 'Notes/Notes/api_batch';
$route['api/notes/students_by_classe/(:any)'] = 'Notes/Notes/api_students_by_classe/$1';
$route['api/notes/evaluations_by_classe/(:any)'] = 'Notes/Notes/api_evaluations_by_classe/$1';
$route['api/notes/grille/(:any)/(:any)'] = 'Notes/Notes/api_grille_notes/$1/$2';
$route['api/notes/matieres_by_classe/(:any)'] = 'Notes/Notes/api_matieres_by_classe/$1';
$route['api/notes/classes_summary'] = 'Notes/Notes/api_classes_summary';
$route['api/notes/(:any)/delete'] = 'Notes/Notes/api_delete/$1';
$route['api/evaluations'] = 'Evaluations/Evaluations/api_list';
$route['api/evaluations/create'] = 'Evaluations/Evaluations/api_create';
$route['api/evaluations/(:any)/update'] = 'Evaluations/Evaluations/api_update/$1';
$route['api/evaluations/(:any)/delete'] = 'Evaluations/Evaluations/api_delete/$1';

// API - Bulletins
$route['api/bulletins'] = 'Notes/Bulletins/api_list';
$route['api/bulletins/create'] = 'Notes/Bulletins/api_create';
$route['api/bulletins/generer'] = 'Notes/Bulletins/api_generer';
$route['api/bulletins/(:any)/detail'] = 'Notes/Bulletins/api_detail/$1';
$route['api/bulletins/(:any)/update'] = 'Notes/Bulletins/api_update/$1';
$route['api/bulletins/(:any)/delete'] = 'Notes/Bulletins/api_delete/$1';

// Fiches de points
$route['Fiches'] = 'Notes/Fiches/index';
$route['Evaluations'] = 'Evaluations/Evaluations/index';
$route['api/fiches/fiche'] = 'Notes/Fiches/api_fiche';
$route['api/fiches/fiche_par_cours/(:any)'] = 'Notes/Fiches/api_fiche_par_cours/$1';
$route['api/fiches/fiche_par_cours'] = 'Notes/Fiches/api_fiche_par_cours';

// API - Produits
$route['api/produits/categories'] = 'Produits/Produits/api_categories';
$route['api/produits'] = 'Produits/Produits/api_list';
$route['api/produits/create'] = 'Produits/Produits/api_create';
$route['api/produits/(:any)'] = 'Produits/Produits/api_get/$1';
$route['api/produits/(:any)/update'] = 'Produits/Produits/api_update/$1';
$route['api/produits/(:any)/delete'] = 'Produits/Produits/api_delete/$1';

// API - Stock Catégories
$route['api/categories'] = 'Stock_Categories/Stock_Categories/api_list';
$route['api/categories/create'] = 'Stock_Categories/Stock_Categories/api_create';
$route['api/categories/(:any)'] = 'Stock_Categories/Stock_Categories/api_get/$1';
$route['api/categories/(:any)/update'] = 'Stock_Categories/Stock_Categories/api_update/$1';
$route['api/categories/(:any)/delete'] = 'Stock_Categories/Stock_Categories/api_delete/$1';


// API - Horaires
$route['api/horaires'] = 'Horaires/Horaires/api_list';
$route['api/horaires/create'] = 'Horaires/Horaires/api_create';
$route['api/horaires/generer'] = 'Horaires/Horaires/api_generer';
$route['api/horaires/generations'] = 'Horaires/Horaires/api_generations';
$route['api/horaires/(:any)/update'] = 'Horaires/Horaires/api_update/$1';
$route['api/horaires/(:any)/delete'] = 'Horaires/Horaires/api_delete/$1';
$route['api/horaires/matieres/(:any)'] = 'Horaires/Horaires/api_matieres_by_classe/$1';
$route['api/horaires/enseignant/(:any)/(:any)'] = 'Horaires/Horaires/api_enseignant_by_classe_matiere/$1/$2';



// API - Parametres
$route['api/parametres'] = 'Parametres/Parametres/api_list';
$route['api/parametres/update'] = 'Parametres/Parametres/api_update';
$route['api/parametres/upload_logo'] = 'Parametres/Parametres/api_upload_logo';
$route['api/parametres/upload_favicon'] = 'Parametres/Parametres/api_upload_favicon';
$route['api/parametres/upload_login_img'] = 'Parametres/Parametres/api_upload_login_img';
$route['api/email/test'] = 'Parametres/Parametres/api_test_email';


// API - Evenements
$route['api/evenements'] = 'Evenements/Evenements/api_list';
$route['api/evenements/create'] = 'Evenements/Evenements/api_create';
$route['api/evenements/(:any)/update'] = 'Evenements/Evenements/api_update/$1';
$route['api/evenements/(:any)/delete'] = 'Evenements/Evenements/api_delete/$1';




// API - Paiements (dedicated module)
$route['api/paiements_data'] = 'Paiements/Paiements/api_list';
$route['api/paiements_data/create'] = 'Paiements/Paiements/api_create';
$route['api/paiements_data/(:any)/update'] = 'Paiements/Paiements/api_update/$1';
$route['api/paiements_data/(:any)/delete'] = 'Paiements/Paiements/api_delete/$1';
$route['api/paiements_data/(:any)'] = 'Paiements/Paiements/api_get/$1';

// API - Reçus
$route['api/recus'] = 'Recu/Recu/api_list';
$route['api/recus/create'] = 'Recu/Recu/api_create';
$route['api/recus/(:any)/update'] = 'Recu/Recu/api_update/$1';
$route['api/recus/(:any)/delete'] = 'Recu/Recu/api_delete/$1';
$route['api/recus/(:any)'] = 'Recu/Recu/api_get/$1';
$route['api/recus/(:any)/imprimer'] = 'Recu/Recu/imprimer/$1';

// API - Échéances
$route['api/echeances'] = 'Echeance/Echeance/api_list';
$route['api/echeances/create'] = 'Echeance/Echeance/api_create';
$route['api/echeances/(:any)/update'] = 'Echeance/Echeance/api_update/$1';
$route['api/echeances/(:any)/delete'] = 'Echeance/Echeance/api_delete/$1';
$route['api/echeances/(:any)'] = 'Echeance/Echeance/api_get/$1';

// API - Types de frais
$route['api/types_frais'] = 'Type_frais/Type_frais/api_list';
$route['api/types_frais/create'] = 'Type_frais/Type_frais/api_create';
$route['api/types_frais/(:any)/update'] = 'Type_frais/Type_frais/api_update/$1';
$route['api/types_frais/(:any)/delete'] = 'Type_frais/Type_frais/api_delete/$1';
$route['api/types_frais/(:any)'] = 'Type_frais/Type_frais/api_get/$1';

// API - Détails commandes
$route['api/commandes_details'] = 'Commande_detail/Commande_detail/api_list';
$route['api/commandes_details/create'] = 'Commande_detail/Commande_detail/api_create';
$route['api/commandes_details/(:any)/update'] = 'Commande_detail/Commande_detail/api_update/$1';
$route['api/commandes_details/(:any)/delete'] = 'Commande_detail/Commande_detail/api_delete/$1';
$route['api/commandes_details/(:any)'] = 'Commande_detail/Commande_detail/api_get/$1';

// API - Reçus de paiement
$route['api/paiements_recus'] = 'Paiement_recu/Paiement_recu/api_list';
$route['api/paiements_recus/create'] = 'Paiement_recu/Paiement_recu/api_create';
$route['api/paiements_recus/(:any)/delete'] = 'Paiement_recu/Paiement_recu/api_delete/$1';


// API - Uniformes
$route['api/uniformes'] = 'Uniformes/Uniformes/api_list';
$route['api/uniformes/create'] = 'Uniformes/Uniformes/api_create';
$route['api/uniformes/(:any)'] = 'Uniformes/Uniformes/api_get/$1';
$route['api/uniformes/(:any)/update'] = 'Uniformes/Uniformes/api_update/$1';
$route['api/uniformes/(:any)/delete'] = 'Uniformes/Uniformes/api_delete/$1';

// API - Commandes
$route['api/commandes'] = 'Commandes/Commandes/api_list';
$route['api/commandes/create'] = 'Commandes/Commandes/api_create';
$route['api/commandes/(:any)'] = 'Commandes/Commandes/api_get/$1';
$route['api/commandes/(:any)/update'] = 'Commandes/Commandes/api_update/$1';
$route['api/commandes/(:any)/delete'] = 'Commandes/Commandes/api_delete/$1';

// API - Assurances
$route['api/assurances'] = 'Assurances/Assurances/api_list';
$route['api/assurances/create'] = 'Assurances/Assurances/api_create';
$route['api/assurances/(:any)'] = 'Assurances/Assurances/api_get/$1';
$route['api/assurances/(:any)/update'] = 'Assurances/Assurances/api_update/$1';
$route['api/assurances/(:any)/delete'] = 'Assurances/Assurances/api_delete/$1';

// API - Toilettes
$route['api/toilettes'] = 'Toilettes/Toilettes/api_list';
$route['api/toilettes/create'] = 'Toilettes/Toilettes/api_create';
$route['api/toilettes/(:any)'] = 'Toilettes/Toilettes/api_get/$1';
$route['api/toilettes/(:any)/update'] = 'Toilettes/Toilettes/api_update/$1';
$route['api/toilettes/(:any)/delete'] = 'Toilettes/Toilettes/api_delete/$1';

// API - Librairie
$route['api/librairie'] = 'Librairie/Librairie/api_list';
$route['api/librairie/create'] = 'Librairie/Librairie/api_create';
$route['api/librairie/initialiser'] = 'Librairie/Librairie/api_initialiser';
$route['api/librairie/(:any)'] = 'Librairie/Librairie/api_get/$1';
$route['api/librairie/(:any)/update'] = 'Librairie/Librairie/api_update/$1';
$route['api/librairie/(:any)/delete'] = 'Librairie/Librairie/api_delete/$1';

// API - Creneaux
$route['api/creneaux'] = 'Creneaux/Creneaux/api_list';
$route['api/creneaux/create'] = 'Creneaux/Creneaux/api_create';
$route['api/creneaux/(:any)'] = 'Creneaux/Creneaux/api_get/$1';
$route['api/creneaux/(:any)/update'] = 'Creneaux/Creneaux/api_update/$1';
$route['api/creneaux/(:any)/delete'] = 'Creneaux/Creneaux/api_delete/$1';

// API - Disponibilites
$route['api/disponibilites'] = 'Disponibilites/Disponibilites/api_list';
$route['api/disponibilites/create'] = 'Disponibilites/Disponibilites/api_create';
$route['api/disponibilites/(:any)'] = 'Disponibilites/Disponibilites/api_get/$1';
$route['api/disponibilites/(:any)/update'] = 'Disponibilites/Disponibilites/api_update/$1';
$route['api/disponibilites/(:any)/delete'] = 'Disponibilites/Disponibilites/api_delete/$1';


// API - Audit
$route['api/audit'] = 'Utilisateurs/Audit/api_list';

$route['Notifications'] = 'Notifications/Notifications/index';

// API - Notifications
$route['api/notifications'] = 'Notifications/Notifications/api_list';
$route['api/notifications/(:any)/read'] = 'Notifications/Notifications/api_mark_read/$1';

// API - Matieres_classes (Programmes)
$route['api/matieres_classes'] = 'Enseignants/Programmes/api_list';
$route['api/matieres_classes/create'] = 'Enseignants/Programmes/api_create';
$route['api/matieres_classes/(:any)'] = 'Enseignants/Programmes/api_get/$1';
$route['api/matieres_classes/(:any)/update'] = 'Enseignants/Programmes/api_update/$1';
$route['api/matieres_classes/(:any)/delete'] = 'Enseignants/Programmes/api_delete/$1';

// API - Annees
$route['api/annees'] = 'Classes/Annees/api_list';
$route['api/annees/create'] = 'Classes/Annees/api_create';
$route['api/annees/(:any)'] = 'Classes/Annees/api_get/$1';
$route['api/annees/(:any)/update'] = 'Classes/Annees/api_update/$1';
$route['api/annees/(:any)/delete'] = 'Classes/Annees/api_delete/$1';
$route['api/annees/(:any)/activate'] = 'Classes/Annees/api_activate/$1';
$route['api/annees/(:any)/deactivate'] = 'Classes/Annees/api_deactivate/$1';
$route['api/annees/(:any)/active'] = 'Classes/Annees/api_set_active/$1';

// Module pages
$route['Utilisateurs'] = 'Utilisateurs/Utilisateurs/index';
$route['Etudiants'] = 'Etudiants/Etudiants/index';
$route['Etudiants/add'] = 'Etudiants/Etudiants/add';
$route['Etudiants/edit/(:any)'] = 'Etudiants/Etudiants/edit/$1';
$route['Etudiants/details/(:any)'] = 'Etudiants/Etudiants/details/$1';
$route['Enseignants'] = 'Enseignants/Enseignants/index';
$route['Enseignants/add'] = 'Enseignants/Enseignants/add';
$route['Enseignants/edit/(:any)'] = 'Enseignants/Enseignants/edit/$1';
$route['Enseignants/details/(:any)'] = 'Enseignants/Enseignants/details/$1';
$route['Enseignants/timetable/(:any)'] = 'Enseignants/Enseignants/timetable/$1';
$route['Enseignants/Programmes'] = 'Enseignants/Programmes/index';
$route['Classes'] = 'Classes/Classes/index';
$route['Frais'] = 'Frais/Frais/index';
$route['Notes'] = 'Notes/Notes/index';
$route['Produits'] = 'Produits/Produits/index';
$route['Horaires'] = 'Horaires/Horaires/index';

$route['Evenements'] = 'Evenements/Evenements/index';
$route['Uniformes'] = 'Uniformes/Uniformes/index';
$route['Parametres'] = 'Parametres/Parametres/index';
$route['Commandes'] = 'Commandes/Commandes/index';
$route['Rapports'] = 'Rapports/Rapports/index';
$route['Assurances'] = 'Assurances/Assurances/index';
$route['Creneaux'] = 'Creneaux/Creneaux/index';
$route['Disponibilites'] = 'Disponibilites/Disponibilites/index';

$route['Paiements'] = 'Paiements/Paiements/index';
$route['Recus'] = 'Recu/Recu/index';
$route['Echeance'] = 'Echeance/Echeance/index';
$route['Echeances'] = 'Echeance/Echeance/index';
$route['Types_frais'] = 'Type_frais/Type_frais/index';
$route['Commande_details'] = 'Commande_detail/Commande_detail/index';
$route['Paiement_recus'] = 'Paiement_recu/Paiement_recu/index';
$route['Recus/imprimer/(:any)'] = 'Recu/Recu/imprimer/$1';
$route['Toilettes'] = 'Toilettes/Toilettes/index';
$route['Librairie'] = 'Librairie/Librairie/index';

// Sub-module pages for sidebar route_map
$route['Etudiants/Inscriptions'] = 'Etudiants/Inscriptions/index';
$route['Classes/Sections'] = 'Classes/Sections/index';
$route['Classes/Periodes'] = 'Classes/Periodes/index';
$route['Classes/Matieres'] = 'Classes/Matieres/index';
$route['Classes/Enseignements'] = 'Classes/Enseignements/index';
$route['Classes/Annees'] = 'Classes/Annees/index';
$route['Frais/Paiements'] = 'Frais/Frais/index';
$route['Frais/Recus'] = 'Frais/Frais/index';
$route['Frais/Echeances'] = 'Frais/Frais/index';
$route['Notes/Bulletins'] = 'Notes/Bulletins/index';
$route['Classes/Matieres_classes'] = 'Enseignants/Programmes/index';
$route['Produits/Stock'] = 'Produits/Produits/index';
$route['Stock_Categories'] = 'Stock_Categories/Stock_Categories/index';
// Module - Stock Mouvements
$route['Stock_Mouvements'] = 'Stock_Mouvements/Stock_Mouvements/index';
$route['api/mouvements'] = 'Stock_Mouvements/Stock_Mouvements/api_list';
$route['api/mouvements/create'] = 'Stock_Mouvements/Stock_Mouvements/api_create';
$route['api/mouvements/batch'] = 'Stock_Mouvements/Stock_Mouvements/api_batch';
$route['Utilisateurs/Audit'] = 'Utilisateurs/Audit/index';

// Administration module
$route['Administration'] = 'Administration/Administration/index';
$route['Administration/Roles'] = 'Administration/Roles/index';
$route['Administration/Permissions'] = 'Administration/Permissions/index';
$route['Administration/Menus'] = 'Administration/Menus/index';
$route['Administration/Sauvegardes'] = 'Administration/Sauvegardes/index';
$route['Administration/Operations'] = 'Administration/Operations/index';

// API - Rapports
$route['api/rapports/eleves_par_classe'] = 'Rapports/Rapports/api_eleves_par_classe';
$route['api/rapports/eleves_par_section'] = 'Rapports/Rapports/api_eleves_par_section';
$route['api/rapports/paiements_par_classe'] = 'Rapports/Rapports/api_paiements_par_classe';
$route['api/rapports/paiements_par_section'] = 'Rapports/Rapports/api_paiements_par_section';
$route['api/rapports/paiements_statuts'] = 'Rapports/Rapports/api_paiements_statuts';
$route['api/rapports/produits_par_classe'] = 'Rapports/Rapports/api_produits_par_classe';
$route['api/rapports/produits_par_section'] = 'Rapports/Rapports/api_produits_par_section';
$route['api/rapports/consommation_stock'] = 'Rapports/Rapports/api_consommation_stock';

// API - Menus
$route['api/menus/all'] = 'Administration/Roles/api_menus';
$route['api/menus'] = 'Administration/Menus/api_list';
$route['api/menus/create'] = 'Administration/Menus/api_create';
$route['api/menus/(:any)'] = 'Administration/Menus/api_get/$1';
$route['api/menus/(:any)/update'] = 'Administration/Menus/api_update/$1';
$route['api/menus/(:any)/delete'] = 'Administration/Menus/api_delete/$1';

// API - Role permissions
$route['api/permissions/(:num)'] = 'Utilisateurs/Roles/api_permissions_get/$1';
$route['api/permissions/(:num)/update'] = 'Utilisateurs/Roles/api_permissions_update_by_id/$1';
$route['api/roles/(:any)/permissions'] = 'Utilisateurs/Roles/api_permissions_update/$1';

// API - Sauvegardes
$route['api/sauvegardes/create'] = 'Administration/Sauvegardes/api_create';
$route['api/sauvegardes/list'] = 'Administration/Sauvegardes/api_list';
$route['api/sauvegardes/download/(:any)'] = 'Administration/Sauvegardes/api_download/$1';
$route['api/sauvegardes/delete/(:any)'] = 'Administration/Sauvegardes/api_delete/$1';

// API - Operations groupées
$route['api/operations/tables'] = 'Administration/Operations/api_tables';
$route['api/operations/export/(:any)'] = 'Administration/Operations/api_export/$1';
$route['api/operations/preview/(:any)'] = 'Administration/Operations/api_preview/$1';
$route['api/operations/import-preview'] = 'Administration/Operations/api_import_preview';
$route['api/operations/import'] = 'Administration/Operations/api_import';

// Profile
$route['Profile'] = 'Profile/index';
$route['api/profile/update'] = 'Profile/api_update';
$route['api/profile/change_password'] = 'Profile/api_change_password';
$route['api/profile/upload_photo'] = 'Profile/api_upload_photo';
