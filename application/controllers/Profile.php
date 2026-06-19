<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends MY_Controller {
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('uuid')) {
            redirect('Admin');
        }
    }

    public function index() {
        $uuid = $this->session->userdata('uuid');
        $data['user'] = $this->Model->readOne('utilisateurs', ['uuid' => $uuid, 'deleted_at' => null]);
        if (!$data['user']) show_404();
        $data['title'] = 'Mon Profil';
        $this->load->view('Profile_View', $data);
    }

    public function api_update() {
        $uuid = $this->session->userdata('uuid');
        $input = $this->get_json_input();
        if (empty($input['nom_complet']) && empty($input['email']) && empty($input['telephone'])) {
            $this->json_error('Aucune donnée à modifier'); return;
        }
        $update = [];
        if (!empty($input['nom_complet'])) $update['nom_complet'] = $input['nom_complet'];
        if (!empty($input['email'])) {
            $existing = $this->Model->readOne('utilisateurs', ['email' => $input['email'], 'uuid !=' => $uuid, 'deleted_at' => null]);
            if ($existing) {
                $this->json_error('Cet email est déjà utilisé'); return;
            }
            $update['email'] = $input['email'];
        }
        if (isset($input['telephone'])) $update['telephone'] = $input['telephone'];
        if (!empty($update)) {
            $this->db->where('uuid', $uuid)->update('utilisateurs', $update);
        }
        if (!empty($input['nom_complet'])) $this->session->set_userdata('nom_complet', $input['nom_complet']);
        $this->json_success(null, 'Profil mis à jour');
    }

    public function api_change_password() {
        $uuid = $this->session->userdata('uuid');
        $input = $this->get_json_input();
        if (empty($input['current_password']) || empty($input['new_password']) || empty($input['confirm_password'])) {
            $this->json_error('Tous les champs sont requis'); return;
        }
        if ($input['new_password'] !== $input['confirm_password']) {
            $this->json_error('Les mots de passe ne correspondent pas'); return;
        }
        if (strlen($input['new_password']) < 6) {
            $this->json_error('Le mot de passe doit contenir au moins 6 caractères'); return;
        }
        $user = $this->Model->readOne('utilisateurs', ['uuid' => $uuid, 'deleted_at' => null]);
        if (!password_verify($input['current_password'], $user['mot_de_passe'])) {
            $this->json_error('Mot de passe actuel incorrect'); return;
        }
        $this->db->where('uuid', $uuid)->update('utilisateurs', ['mot_de_passe' => password_hash($input['new_password'], PASSWORD_DEFAULT)]);
        $this->json_success(null, 'Mot de passe changé avec succès');
    }

    public function api_upload_photo() {
        $uuid = $this->session->userdata('uuid');
        if (empty($_FILES['photo'])) {
            $this->json_error('Aucun fichier'); return;
        }
        $config['upload_path'] = './uploads/profiles/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
        $config['max_size'] = 2048;
        $config['file_name'] = 'profile_' . $uuid . '_' . time();
        if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('photo')) {
            $this->json_error($this->upload->display_errors('', ''));
            return;
        }
        $upload_data = $this->upload->data();
        $photo = 'uploads/profiles/' . $upload_data['file_name'];
        // Supprimer l'ancienne photo
        $user = $this->Model->readOne('utilisateurs', ['uuid' => $uuid]);
        if ($user && !empty($user['photo']) && file_exists('./' . $user['photo']) && strpos($user['photo'], 'profiles/') !== false) {
            @unlink('./' . $user['photo']);
        }
        $this->db->where('uuid', $uuid)->update('utilisateurs', ['photo' => $photo]);
        $this->session->set_userdata('photo', $photo);
        $this->json_success(['photo' => $photo], 'Photo mise à jour');
    }
}
