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
        'Paiement_recu' => 'recus',
        'Rapports' => 'rapports',
        'Parametres' => 'parametres',
        'Utilisateurs' => 'utilisateurs',
        'Roles' => 'utilisateurs',
        'Audit' => 'audit',
        'Horaires' => 'horaires',
        // 'Creneaux' => 'horaires_creneaux',
        'Disponibilites' => 'horaires_dispos',
        'Jours' => 'horaires',
        'Evenements' => 'horaires',
        'Produits' => 'produits',
        'Uniformes' => 'produits_uniformes',
        'Stock_Categories' => 'stock',
        'Stock_Mouvements' => 'stock',
        'Librairie' => 'produits_livres',
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
                || strpos($method, 'remove') !== false) {
                $required = 'can_delete';
            } elseif ($this->_is_write_method($method)) {
                $required = 'can_add';
            } elseif (strpos($method, 'update') !== false || strpos($method, 'activate') !== false
                || strpos($method, 'set_active') !== false || strpos($method, 'generer') !== false) {
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
            'deactiver', 'toggle', 'enregistrer');
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

    private function _load_module_model()
    {
        $class = get_class($this);
        if ($class === 'MY_Controller' || $class === 'MX_Controller') return;
        $parts = explode('\\', $class);
        $class = end($parts);
        $model_name = $class . '_model';

        $current_module = property_exists($this, 'module') ? $this->module : '';
        if ($current_module) {
            $model_in_current = APPPATH . 'modules/' . $current_module . '/models/' . $model_name . '.php';
            if (file_exists($model_in_current)) {
                $this->load->model($model_name);
                return;
            }
        }

        $model_file = APPPATH . 'modules/' . $class . '/models/' . $model_name . '.php';
        if (file_exists($model_file)) {
            $this->load->model($class . '/' . $model_name);
        }
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
        return $input ? json_decode($input, true) : null;
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
            $this->load->model('conduite/Conduite_model', 'ConduiteModel');
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
