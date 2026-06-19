<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends MX_Controller
{
    var $permission = array();
    var $permission_codes = array();
    var $group_name = "";
    var $id_annee_active = 0;
    var $id_periode_active = 0;
    var $menus_data = array();

    public function __construct()
    {
        parent::__construct();
        $this->_hmvc_fixes();

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

        $annee = $this->Model->readOne('annees_scolaires', ['est_en_cours' => 1]);
        if ($annee) {
            $this->id_annee_active = $annee['id_annee'];
        } else {
            $fallback = $this->db
                ->select('id_annee')
                ->where('deleted_at', null)
                ->order_by('id_annee', 'DESC')
                ->limit(1)
                ->get('annees_scolaires')
                ->row_array();
            $this->id_annee_active = $fallback ? $fallback['id_annee'] : 0;
        }

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
                    ->get('periodes')
                    ->row_array();
                $this->id_periode_active = $fallbackP ? $fallbackP['id_periode'] : 0;
            }
        }
    }

    function _hmvc_fixes()
    {
        $this->load->library('form_validation');
        $this->form_validation->CI =& $this;
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
            ->set_output(json_encode($data));
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
        return json_decode(file_get_contents('php://input'), true);
    }

    protected function _create_linked_user($table, $record_id, $data, $role_code = 'lecture')
    {
        $nom_complet = trim(($data['nom'] ?? '') . ' ' . ($data['postnom'] ?? '') . ' ' . ($data['prenom'] ?? ''));
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
        $nom_complet = trim(($data['nom'] ?? $record['nom'] ?? '') . ' ' . ($data['postnom'] ?? $record['postnom'] ?? '') . ' ' . ($data['prenom'] ?? $record['prenom'] ?? ''));
        $user_update['nom_complet'] = trim($nom_complet);
        if (isset($data['email']) && $data['email'] !== $record['email']) {
            $user_update['email'] = $data['email'];
        }
        if (!empty($user_update)) {
            $this->Model->update('utilisateurs', ['id_utilisateur' => $record['id_utilisateur']], $user_update);
        }
    }
}
