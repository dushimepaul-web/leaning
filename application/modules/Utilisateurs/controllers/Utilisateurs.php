<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utilisateurs extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['title'] = 'Gestion des utilisateurs';
        $this->load->view('index', $data);
    }

    public function api_list() {
        $users = $this->Model->read('utilisateurs', ['deleted_at' => null], 'cree_le');

        if (!empty($users)) {
            $role_ids = array_unique(array_column($users, 'id_role'));
            $roles = $this->db->where_in('id_role', $role_ids)->get('roles')->result_array();
            $roles_map = array_column($roles, 'libelle', 'id_role');
        }

        foreach ($users as &$u) {
            $u['role_libelle'] = $roles_map[$u['id_role']] ?? '';
        }
        $this->json_success($users);
    }

    public function api_get($id) {
        $user = $this->Model->readOne('utilisateurs', ['uuid' => $id]);
        if ($user) {
            $role = $this->Model->readOne('roles', ['id_role' => $user['id_role']]);
            $user['role_libelle'] = $role ? $role['libelle'] : '';
            $this->json_success($user);
        } else {
            $this->json_error('Utilisateur non trouvé', 404);
        }
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['email']) || empty($data['mot_de_passe']) || empty($data['nom_complet'])) {
            $this->json_error('Nom complet, email et mot de passe obligatoires');
            return;
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->json_error('Format d\'email invalide');
            return;
        }
        if (strlen($data['mot_de_passe']) < 4) {
            $this->json_error('Le mot de passe doit contenir au moins 4 caractères');
            return;
        }
        if ($this->Model->checkValue('utilisateurs', ['email' => $data['email']])) {
            $this->json_error('Cet email est déjà utilisé');
            return;
        }
        $allowed = ['nom_complet', 'email', 'mot_de_passe', 'id_role', 'actif'];
        $insert = array_intersect_key($data, array_flip($allowed));
        if (empty($insert['id_role'])) { $this->json_error('Rôle requis'); return; }
        if (!$this->Model->readOne('roles', ['id_role' => $insert['id_role']])) { $this->json_error('Rôle introuvable'); return; }
        $insert['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        if (array_key_exists('actif', $insert) && $insert['actif'] !== null && $insert['actif'] !== '') {
            $insert['actif'] = $insert['actif'] ? 1 : 0;
        } else {
            $insert['actif'] = 1;
        }
        $id = $this->Model->createLastId('utilisateurs', $insert);
        if ($id) {
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'create', 'Création utilisateur #'.$id, 'utilisateurs', $id, null, $insert);
            $this->json_success(['id_utilisateur' => $id], 'Utilisateur créé avec succès');
        } else {
            $this->json_error('Erreur lors de la création');
        }
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $user = $this->Model->readOne('utilisateurs', ['uuid' => $id]);
        if (!$user) {
            $this->json_error('Utilisateur non trouvé', 404);
            return;
        }
        if (isset($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->json_error('Format d\'email invalide'); return;
            }
            $existing = $this->Model->readOne('utilisateurs', ['email' => $data['email'], 'uuid !=' => $id]);
            if ($existing) {
                $this->json_error('Cet email est déjà utilisé'); return;
            }
        }
        if (isset($data['mot_de_passe']) && !empty($data['mot_de_passe'])) {
            if (strlen($data['mot_de_passe']) < 4) {
                $this->json_error('Le mot de passe doit contenir au moins 4 caractères'); return;
            }
            $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        } else {
            unset($data['mot_de_passe']);
        }
        $allowed = ['nom_complet', 'email', 'mot_de_passe', 'id_role', 'actif'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (isset($update['id_role']) && !$this->Model->readOne('roles', ['id_role' => $update['id_role']])) {
            $this->json_error('Rôle introuvable'); return;
        }
        if (array_key_exists('actif', $update)) {
            $update['actif'] = ($update['actif'] !== null && $update['actif'] !== '' && $update['actif']) ? 1 : 0;
        }
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if ($this->Model->update('utilisateurs', ['uuid' => $id], $update)) {
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'update', 'Modification utilisateur #'.$id, 'utilisateurs', $id, $user, $update);
            $this->json_success(null, 'Utilisateur mis à jour');
        } else {
            $this->json_error('Erreur de mise à jour');
        }
    }

    public function api_delete($id) {
        $user = $this->Model->readOne('utilisateurs', ['uuid' => $id]);
        if (!$user) {
            $this->json_error('Utilisateur non trouvé', 404);
            return;
        }
        if ($this->Model->update('utilisateurs', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')])) {
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'delete', 'Suppression utilisateur #'.$id, 'utilisateurs', $id, $user, ['deleted_at' => date('Y-m-d H:i:s')]);
            $this->json_success(null, 'Utilisateur supprimé');
        } else {
            $this->json_error('Erreur de suppression');
        }
    }
}
