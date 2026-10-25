<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends MX_Controller
{
    public $permission = array();
    public $permission_codes = array();
    public $group_name = "";
    public $id_annee_active = 0;
    public $id_periode_active = 0;
    public $menus_data = array();
    public $est_enseignant = false;
    public $id_enseignant = null;
    public $peut_voir_tout_notes = false;
    public $peut_voir_tout_bulletins = false;

    /**
     * Mapping module (contrôleur) -> code menu pour le contrôle d'accès
     */
    protected $module_menu_map = array(
        'Dashboard' => 'dashboard',
        'Etudiants' => 'Eleves',
        'Inscriptions' => 'inscriptions',
        'Classes' => 'sections',
        'Sections' => 'sections',
        'Periodes' => 'periodes',
        'Annees' => 'annees_scolaires',
        'Matieres' => 'matieres',
        'Enseignements' => 'enseignements',
        'Enseignants' => 'enseignants',
        'Programmes' => 'matieres',
        'Notes' => 'points',
        'Bulletins' => 'bulletins',
        'Fiches' => 'bulletins',
        'Evaluations' => 'points',
        'Paiements' => 'paiements',
        'Recu' => 'recus',
        'Echeance' => 'echeances',
        'Type_frais' => 'paiements',
        'Frais' => 'paiements',
        'Minervale' => 'paiements',
        'Dettes' => 'paiements',
        'Soldes' => 'paiements',
        'Exonerations' => 'paiements',
        'Remboursements' => 'paiements',
        'Paiement_recu' => 'recus',
        'Rapports' => 'rapports',
        'Parametres' => 'parametres',
        'Utilisateurs' => 'utilisateurs',
        'Roles' => 'utilisateurs',
        'Audit' => 'audit',
        'Horaires' => 'horaires',
        'horaires' => 'horaires',
        // 'Creneaux' => 'horaires_creneaux',
        'Disponibilites' => 'horaires_dispos',
        'Jours' => 'horaires',
        'Evenements' => 'horaires',
        'Produits' => 'produits',
        'Uniformes' => 'produits_uniformes',
        'Stock_Categories' => 'stock',
        'Stock_Mouvements' => 'stock',
        'Librairie' => 'produits_livres',
        'Library' => 'library_dashboard',
        'Library_suppliers' => 'library_suppliers',
        'Library_purchases' => 'library_purchases',
        'Library_sales' => 'library_sales',
        'Library_payments' => 'library_payments',
        'Library_stock' => 'library_stock',
        'Library_returns' => 'library_returns',
        'Library_expenses' => 'library_expenses',
        'Library_price_history' => 'library_price_history',
        'Library_reports' => 'library_reports',
        'Commandes' => 'produits',
        'Conduite' => 'bulletins',
        'Administration' => 'parametres',
        'Menus' => 'parametres',
        'Permissions' => 'utilisateurs',
        'Sauvegardes' => 'parametres',
        'Operations' => 'parametres',
    );

    public function __construct()
    {
        parent::__construct();
        $this->_hmvc_fixes();
        $this->_load_module_model();

        try {
            if (empty($this->session->userdata('logged_in'))) {
                $session_data = array('logged_in' => FALSE);
                $this->session->set_userdata($session_data);
            } else {
                $uuid = $this->session->userdata('uuid');
                $user_data = $this->Model->readOne('utilisateurs', ['uuid' => $uuid]);
                if ($user_data) {
                    $role_data = $this->Model->readOne('roles', ['id_role' => $user_data['id_role']]);
                    $this->group_name = $role_data ? $role_data['libelle'] : '';
                    $this->session->set_userdata('photo', $user_data['photo'] ?? null);
                    $this->session->set_userdata('role_libelle', $this->group_name);
                    $this->load->model('Role_permission_model');
                    $role_menus = $this->Model->read('roles_menus', ['id_role' => $user_data['id_role'], 'can_view' => 1]);
                    $this->permission = array_column($role_menus, 'id_menu');
                    if (!empty($this->permission)) {
                        $this->menus_data = $this->Role_permission_model->get_role_menus($user_data['id_role']);
                        $this->permission_codes = array_column($this->menus_data, 'code');
                    }
                    $this->peut_voir_tout_notes = $this->Role_permission_model->check_permission($user_data['id_role'], 'points', 'can_view_all');
                    $this->peut_voir_tout_bulletins = $this->Role_permission_model->check_permission($user_data['id_role'], 'bulletins', 'can_view_all');
                    if ((int)$user_data['id_role'] === 1) {
                        $this->peut_voir_tout_notes = true;
                        $this->peut_voir_tout_bulletins = true;
                    }
                    if ($role_data && $role_data['code'] === 'enseignant') {
                        $this->est_enseignant = true;
                        $enseignant = $this->Model->readOne('enseignants', [
                            'id_utilisateur' => $user_data['id_utilisateur'],
                            'deleted_at' => null
                        ]);
                        $this->id_enseignant = $enseignant ? (int)$enseignant['id_enseignant'] : 0;
                    }
                }
            }
        } catch (Throwable $e) {
            log_message('error', 'MY_Controller user load failed: ' . $e->getMessage());
        }

        try {
            $annee = $this->Model->readOne('annees_scolaires', ['est_en_cours' => 1]);
            if ($annee) {
                $this->id_annee_active = $annee['id_annee'];
            } else {
                $fallback = $this->db
                    ->select('id_annee')
                    ->where('deleted_at', null)
                    ->order_by('id_annee', 'DESC')
                    ->limit(1)
                    ->get('annees_scolaires');
                if ($fallback !== false) {
                    $fb = $fallback->row_array();
                    $this->id_annee_active = $fb ? $fb['id_annee'] : 0;
                }
            }
        } catch (Throwable $e) {
            log_message('error', 'MY_Controller annee query failed: ' . $e->getMessage());
        }

        try {
            $periode = $this->Model->readOne('periodes', ['est_en_cours' => 1]);
            if ($periode) {
                $this->id_periode_active = $periode['id_periode'];
            } else {
                $paramPeriode = $this->Model->get_setting('periode_active');
                if ($paramPeriode) {
                    $this->id_periode_active = intval($paramPeriode);
                } else {
                    $fallbackP = $this->db
                        ->select('id_periode')
                        ->where('id_annee', $this->id_annee_active)
                        ->where('deleted_at', null)
                        ->order_by('id_periode', 'DESC')
                        ->limit(1)
                        ->get('periodes');
                    if ($fallbackP !== false) {
                        $fbP = $fallbackP->row_array();
                        $this->id_periode_active = $fbP ? $fbP['id_periode'] : 0;
                    }
                }
            }
        } catch (Throwable $e) {
            log_message('error', 'MY_Controller periode query failed: ' . $e->getMessage());
        }

        $this->_check_auth();
    }

    /**
     * Code menu utilisé par le module courant pour la portée des données.
     */
    protected function _teacher_menu_code()
    {
        $map = array(
            'Notes' => 'points',
            'Evaluations' => 'points',
            'Bulletins' => 'bulletins',
            'Fiches' => 'bulletins',
            'Conduite' => 'bulletins',
        );
        $class = get_class($this);
        return isset($map[$class]) ? $map[$class] : 'points';
    }

    /**
     * L'utilisateur connecté a-t-il la permission « Voir tout » sur le menu courant ?
     */
    protected function _teacher_view_all()
    {
        if ($this->_teacher_menu_code() === 'bulletins') return $this->peut_voir_tout_bulletins;
        return $this->peut_voir_tout_notes;
    }

    /**
     * Classes autorisées pour l'enseignant connecté.
     * Retourne null si l'utilisateur n'est pas enseignant.
     */
    protected function _teacher_classes($write = false)
    {
        if (!$this->est_enseignant) return null;
        if ($this->_teacher_view_all() && !$write) {
            $rows = $this->db
                ->distinct()
                ->select('id_classe')
                ->from('classes')
                ->where('deleted_at', null)
                ->get()->result_array();
            return array_map('intval', array_column($rows, 'id_classe'));
        }
        if (!$this->id_enseignant) return array();
        $rows = $this->db
            ->distinct()
            ->select('id_classe')
            ->from('matieres_classes')
            ->where('id_enseignant', $this->id_enseignant)
            ->where('deleted_at', null)
            ->get()->result_array();
        return array_map('intval', array_column($rows, 'id_classe'));
    }

    /**
     * Matières autorisées pour l'enseignant connecté (éventuellement dans une classe).
     * Retourne null si l'utilisateur n'est pas enseignant.
     */
    protected function _teacher_matieres($id_classe = null)
    {
        if (!$this->est_enseignant) return null;
        if ($this->_teacher_view_all()) {
            $rows = $this->db
                ->distinct()
                ->select('id_matiere')
                ->from('matieres')
                ->where('deleted_at', null)
                ->get()->result_array();
            return array_map('intval', array_column($rows, 'id_matiere'));
        }
        if (!$this->id_enseignant) return array();
        $this->db
            ->distinct()
            ->select('id_matiere')
            ->from('matieres_classes')
            ->where('id_enseignant', $this->id_enseignant)
            ->where('deleted_at', null);
        if ($id_classe !== null) $this->db->where('id_classe', $id_classe);
        $rows = $this->db->get()->result_array();
        return array_map('intval', array_column($rows, 'id_matiere'));
    }

    /**
     * Vérifie qu'un couple classe/matière appartient bien à l'enseignant connecté.
     * Sans id_matiere, vérifie seulement que la classe est enseignée.
     */
    protected function _teacher_can_access($id_classe, $id_matiere = null, $write = false)
    {
        if (!$this->est_enseignant) return true;
        if (!$write && $this->_teacher_view_all()) return true;
        if (!$this->id_enseignant || !$id_classe) return false;
        $this->db
            ->where('id_enseignant', $this->id_enseignant)
            ->where('id_classe', $id_classe)
            ->where('deleted_at', null);
        if ($id_matiere !== null) $this->db->where('id_matiere', $id_matiere);
        return $this->db->count_all_results('matieres_classes') > 0;
    }

    protected function _teacher_deny()
    {
        $this->json_response(array(
            'success' => false,
            'message' => 'Accès refusé : cette classe ou ce cours ne vous est pas attribué.'
        ), 403);
    }

    /**
     * Vérification globale de l'authentification + permissions d'accès.
     * Les routes publiques (login/register/reset) restent accessibles.
     */
    private function _check_auth()
    {
        $class = get_class($this);
        $method = $this->router->fetch_method();

        // Routes publiques : authentification Admin
        $public_admin_methods = array('index', 'register', 'do_register', 'Login', 'do_login',
            'forgot_password', 'do_forgot_password', 'verify_otp', 'do_verify_otp',
            'reset_password', 'do_reset_password', 'Logout');
        if ($class === 'Admin' && in_array($method, $public_admin_methods, true)) {
            return;
        }

        if ($class === 'MY_Controller' || $class === 'MX_Controller') {
            return;
        }
        if ($this->session->userdata('logged_in') !== TRUE) {
            if ($this->input->is_ajax_request() || $this->uri->segment(1) === 'api') {
                $this->json_response(array('success' => false, 'message' => 'Non authentifié. Veuillez vous connecter.'), 401);
            }
            redirect(base_url('Admin'));
        }

        // Contrôle d'accès par menu (sauf admin complet)
        $id_role = (int)$this->session->userdata('id_role');
        if ($id_role === 1) {
            return; // Administrateur : tout accès
        }

        $menu_code = isset($this->module_menu_map[$class]) ? $this->module_menu_map[$class] : null;

        // Opérations sensibles réservées à l'administrateur
        if ($method === 'test_email' || $method === 'send_test_email' || strpos($method, 'api_test_email') === 0) {
            $this->json_response(array('success' => false, 'message' => 'Accès refusé : réservé à l\'administrateur.'), 403);
            return;
        }

        $is_api = ($this->uri->segment(1) === 'api' || strpos($method, 'api_') === 0);

        // Module sans menu associé : lecture autorisée, écriture réservée à l'administrateur
        if ($menu_code === null) {
            if ($is_api && $this->_is_write_method($method)) {
                $this->json_response(array('success' => false, 'message' => 'Accès refusé : réservé à l\'administrateur.'), 403);
                return;
            }
            return; // lecture autorisée pour tout utilisateur connecté
        }

        // Permission requise selon l'action
        $required = 'can_view';
        if ($is_api) {
            if (strpos($method, 'delete') !== false || strpos($method, 'deactivate') !== false
                || strpos($method, 'remove') !== false || strpos($method, 'annuler') !== false) {
                $required = 'can_delete';
            } elseif ($this->_is_write_method($method)) {
                $required = 'can_add';
            } elseif (strpos($method, 'update') !== false || strpos($method, 'activate') !== false
                || strpos($method, 'set_active') !== false || strpos($method, 'generer') !== false
                || strpos($method, 'recalculer') !== false) {
                $required = 'can_edit';
            }
        }

        $this->load->model('Role_permission_model');
        if (!$this->Role_permission_model->check_permission($id_role, $menu_code, $required)) {
            if ($this->input->is_ajax_request() || $this->uri->segment(1) === 'api') {
                $this->json_response(array('success' => false, 'message' => 'Accès refusé : permission insuffisante.'), 403);
            }
            $this->session->set_flashdata('sms', '<div id="message" class="alert alert-danger text-center"><strong>Accès refusé!</strong> Vous n\'avez pas la permission d\'accéder à cette page.</div>');
            redirect(base_url('Dashboard'));
        }
    }

    private function _is_write_method($method)
    {
        $keywords = array('create', 'initialiser', 'import', 'batch', 'upload', 'clotur',
            'approvisionn', 'payer', 'vendre', 'depens', 'transferer', 'valider',
            'archiver', 'restaur', 'sauvegarder', 'submit', 'marquer', 'activer',
            'deactiver', 'toggle', 'enregistrer', 'approuver', 'refuser', 'ventiler');
        foreach ($keywords as $kw) {
            if (stripos($method, $kw) !== false) return true;
        }
        return false;
    }

    function _hmvc_fixes()
    {
        $this->load->library('form_validation');
        $this->form_validation->CI =& $this;
    }

    /**
     * Charge automatiquement le modèle appartenant au module du contrôleur.
     *
     * La recherche tolère les variations de casse (systèmes de fichiers sensibles
     * à la casse sous Linux) : nom du modèle (Nom_model, nom_model,
     * ucfirst(strtolower(...))) et nom du dossier du module. Si aucun fichier
     * n'est trouvé, un message clair est journalisé au lieu de lever une
     * exception fatale (le modèle est facultatif).
     */
    private function _load_module_model()
    {
        $class = get_class($this);
        if ($class === 'MY_Controller' || $class === 'MX_Controller') return;
        $parts = explode('\\', $class);
        $class = end($parts);

        $module = $this->_current_module_name($class);
        $searched = array();

        foreach ($this->_model_file_candidates($class) as $candidate) {
            list($module_name, $model_file) = $candidate;

            $module_dir = ($module_name === null)
                ? null
                : $this->_dir_name_exists(APPPATH . 'modules/', $module_name);
            $dir = ($module_name === null)
                ? (is_dir(APPPATH . 'models/') ? APPPATH . 'models/' : null)
                : (($module_dir !== null && is_dir($module_dir . 'models/')) ? $module_dir . 'models/' : null);

            if ($dir === null) {
                $searched[] = ($module_name === null ? APPPATH . 'models/' : APPPATH . 'modules/' . $module_name . '/models/') . $model_file;
                continue;
            }

            $exact = $this->_resolve_file($dir, $model_file);
            if ($exact === null) {
                $searched[] = $dir . $model_file;
                continue;
            }

            // MX ne sait résoudre un modèle que depuis le module courant :
            // on expose donc explicitement le dossier du module trouvé.
            if ($module_dir !== null && method_exists($this->load, '_add_module_paths')) {
                $this->load->_add_module_paths(basename(rtrim($module_dir, '/')));
            }
            $this->load->model(pathinfo($exact, PATHINFO_FILENAME));
            return;
        }

        if ($module === null) {
            log_message('debug', 'MY_Controller : aucun module pour "' . $class . '", aucun modèle à charger.');
        } else {
            $module_dir = $this->_dir_name_exists(APPPATH . 'modules/', $module);
            $expects_model = ($module === $class && $module_dir !== null && is_dir($module_dir . 'models/'));
            log_message($expects_model ? 'error' : 'debug',
                'MY_Controller : modèle "' . $class . '_model" '
                . ($expects_model ? 'introuvable' : 'facultatif absent (module "' . $module . '")')
                . '. Chemins testés : ' . implode(' | ', $searched));
        }
    }

    /**
     * Nom du module en cours (null si le contrôleur n'appartient pas à un module).
     */
    private function _current_module_name($class)
    {
        if (property_exists($this, 'module') && is_string($this->module) && $this->module !== '') {
            return $this->module;
        }
        // MX expose les propriétés manquantes via __get() : isset() ne le
        // déclenche pas, on lit donc directement la propriété.
        $router = $this->router;
        if (is_object($router) && method_exists($router, 'fetch_module')) {
            $fetched = $router->fetch_module();
            if (is_string($fetched) && $fetched !== '') {
                return $fetched;
            }
        }
        return $this->_dir_name_exists(APPPATH . 'modules/', $class) !== null ? $class : null;
    }

    /**
     * Candidats [module (null = application/models), fichier modèle] à tester,
     * par ordre de priorité.
     *
     * @return array<int,array{0:?string,1:string}>
     */
    private function _model_file_candidates($class)
    {
        $base = $class . '_model';
        $variants = array_unique(array(
            $base,
            ucfirst(strtolower($base)),
            strtolower($base),
            $class . '_Model',
            strtolower($class) . '_model',
        ));

        $module_names = array();
        foreach (array($this->_current_module_name($class), $class, strtolower($class)) as $name) {
            if (is_string($name) && $name !== '' && !in_array($name, $module_names, true)) {
                $module_names[] = $name;
            }
        }

        $candidates = array();
        foreach ($module_names as $module_name) {
            foreach ($variants as $variant) {
                $candidates[] = array($module_name, $variant . '.php');
            }
        }
        foreach ($variants as $variant) {
            $candidates[] = array(null, $variant . '.php');
        }
        return $candidates;
    }

    /**
     * Nom réel (casse exacte) d'un dossier situé dans $parent, ou null.
     */
    private function _dir_name_exists($parent, $name)
    {
        if (!is_dir($parent)) return null;
        $entries = @scandir($parent);
        if (!is_array($entries)) return null;
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            if (strcasecmp($entry, $name) === 0 && is_dir($parent . $entry)) {
                return $parent . $entry . '/';
            }
        }
        return null;
    }

    /**
     * Nom de fichier réel (casse exacte) présent dans $dir, ou null.
     */
    private function _resolve_file($dir, $file)
    {
        if ($dir === null || !is_dir($dir)) return null;
        $entries = @scandir($dir);
        if (!is_array($entries)) return null;
        foreach ($entries as $entry) {
            if (strcasecmp($entry, $file) === 0 && is_file($dir . $entry)) {
                return $entry;
            }
        }
        return null;
    }

    public function render_view($view, $data = array())
    {
        $data['id_annee_active'] = $this->id_annee_active;
        $data['id_periode_active'] = $this->id_periode_active;
        $data['user_fullname'] = $this->session->userdata('nom_complet');
        $data['user_role'] = $this->group_name;
        $this->load->view($view, $data);
    }

    public function not_logged_in()
    {
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect(base_url('Admin'));
        }
    }

    protected function json_response($data, $status_code = 200)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($status_code)
            ->set_output(json_encode($data))
            ->_display();
        exit;
    }

    protected function json_success($data = null, $message = 'Succès')
    {
        $this->json_response(['success' => true, 'message' => $message, 'data' => $data]);
    }

    protected function json_error($message = 'Erreur', $status_code = 400)
    {
        $this->json_response(['success' => false, 'message' => $message], $status_code);
    }

    protected function get_json_input()
    {
        $input = file_get_contents('php://input');
        if (!$input) {
            return [];
        }
        $data = json_decode($input, true);
        return is_array($data) ? $data : [];
    }

    protected function _generer_dettes_etudiant($id_etudiant, $id_annee = null)
    {
        $id_etudiant = (int)$id_etudiant;
        if ($id_etudiant <= 0) {
            return null;
        }
        if (!$this->db->table_exists('dettes') || !$this->db->table_exists('inscriptions')) {
            return null;
        }

        $fichier = APPPATH . 'libraries/Minervale/Dette.php';
        if (!is_file($fichier)) {
            return null;
        }

        require_once APPPATH . 'libraries/Minervale/Base.php';
        require_once $fichier;

        try {
            $service = new Minervale_dette($this->db, $this);
            return $service->genererPourEtudiant($id_etudiant, $id_annee);
        } catch (Throwable $e) {
            log_message('error', 'Generation des dettes etudiant #' . $id_etudiant . ' : ' . $e->getMessage());
            return null;
        }
    }

    protected function _create_linked_user($table, $record_id, $data, $role_code = 'lecture')
    {
        $nom_complet = trim($data['fullname'] ?? '');
        if (empty(trim($nom_complet))) {
            $nom_complet = $data['matricule'] ?? 'Compte-' . uniqid();
        }
        $email = $data['email'] ?? null;

        $role = $this->Model->readOne('roles', ['code' => $role_code]);
        $id_role = $role ? $role['id_role'] : 5;

        $default_pwd = bin2hex(random_bytes(4));
        $user_id = $this->Model->createLastId('utilisateurs', [
            'id_role' => $id_role,
            'nom_complet' => trim($nom_complet),
            'email' => $email,
            'mot_de_passe' => password_hash($default_pwd, PASSWORD_DEFAULT),
            'actif' => 1
        ]);
        if ($user_id) {
            $pk = ($table === 'etudiants') ? 'id_etudiant' : 'id_enseignant';
            $this->Model->update($table, [$pk => $record_id], ['id_utilisateur' => $user_id]);
            return ['id_utilisateur' => $user_id, 'default_password' => $default_pwd];
        }
        return null;
    }

    protected function _sync_linked_user($table, $record_id, $data)
    {
        $pk = ($table === 'etudiants') ? 'id_etudiant' : 'id_enseignant';
        $record = $this->Model->readOne($table, [$pk => $record_id]);
        if (!$record || empty($record['id_utilisateur'])) {
            return;
        }
        $user_update = [];
        $nom_complet = trim($data['fullname'] ?? $record['fullname'] ?? '');
        $user_update['nom_complet'] = trim($nom_complet);
        if (isset($data['email']) && $data['email'] !== $record['email']) {
            $user_update['email'] = $data['email'];
        }
        if (!empty($user_update)) {
            $this->Model->update('utilisateurs', ['id_utilisateur' => $record['id_utilisateur']], $user_update);
        }
    }

    protected function _ensure_conduite_points($id_etudiant, $id_annee, $points_initial = null)
    {
        if (empty($id_etudiant) || empty($id_annee)) return 0;
        if (!isset($this->ConduiteModel)) {
            $this->load->model('Conduite/Conduite_model', 'ConduiteModel');
        }
        return $this->ConduiteModel->ensure_inscription_points($id_etudiant, $id_annee, $points_initial);
    }

    protected function _remove_conduite_points($id_etudiant, $id_annee)
    {
        if (empty($id_etudiant) || empty($id_annee)) return;
        $this->Model->update('points_conduite', [
            'id_etudiant' => $id_etudiant,
            'id_annee' => $id_annee
        ], ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}
