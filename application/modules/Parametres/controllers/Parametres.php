<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parametres extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Paramètres';
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('deleted_at', null);
        $rows = $this->db->get('parametres')->result_array();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['clef']] = $r['valeur'];
        }
        $this->json_success($settings);
    }

    public function api_update() {
        $data = $this->get_json_input();
        if (empty($data) || !is_array($data)) {
            $this->json_error('Données invalides'); return;
        }
        foreach ($data as $key => $value) {
            $this->Model->setValueStore($key, $value);
        }
        // Handle logo separately (file upload)
        $this->json_success(null, 'Paramètres mis à jour');
    }

    public function api_upload_logo() {
        if (empty($_FILES['logo'])) {
            $this->json_error('Aucun fichier reçu'); return;
        }
        if ($_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            $errors = [0=>'OK',1=>'Taille dépassée',2=>'Taille HTML dépassée',3=>'Partiel',4=>'Aucun fichier',6=>'Dossier tmp manquant',7=>'Écriture impossible',8=>'Extension bloquée'];
            $this->json_error('Erreur PHP: ' . ($errors[$_FILES['logo']['error']] ?? 'Inconnue')); return;
        }
        $upload_dir = FCPATH . 'assets/uploads/logo/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','gif','svg','webp'])) {
            $this->json_error('Format non autorisé'); return;
        }
        $new_name = md5(uniqid()) . '.' . $ext;
        $dest = $upload_dir . $new_name;
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $dest)) {
            $path = 'assets/uploads/logo/' . $new_name;
            $this->Model->setValueStore('logo_ecole', $path);
            $this->json_success(['path' => $path], 'Logo mis à jour');
        } else {
            $this->json_error('Erreur lors de la sauvegarde du fichier');
        }
    }

    public function api_test_email() {
        $input = $this->get_json_input();
        $to = $input['to'] ?? '';
        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->json_error('Adresse email invalide'); return;
        }
        $this->load->library('Cpanel_email');
        $result = $this->cpanel_email->test_email($to);
        if ($result['success']) {
            $this->json_success(null, 'Email de test envoyé à ' . $to);
        } else {
            $this->json_error($result['message'] ?? 'Échec de l\'envoi');
        }
    }

    public function api_upload_favicon() {
        if (empty($_FILES['favicon'])) {
            $this->json_error('Aucun fichier reçu'); return;
        }
        if ($_FILES['favicon']['error'] !== UPLOAD_ERR_OK) {
            $errors = [0=>'OK',1=>'Taille dépassée',2=>'Taille HTML dépassée',3=>'Partiel',4=>'Aucun fichier',6=>'Dossier tmp manquant',7=>'Écriture impossible',8=>'Extension bloquée'];
            $this->json_error('Erreur PHP: ' . ($errors[$_FILES['favicon']['error']] ?? 'Inconnue')); return;
        }
        $upload_dir = FCPATH . 'assets/uploads/logo/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $ext = strtolower(pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['png','ico','svg'])) {
            $this->json_error('Format non autorisé (png, ico, svg)'); return;
        }
        $new_name = 'favicon_' . md5(uniqid()) . '.' . $ext;
        $dest = $upload_dir . $new_name;
        if (move_uploaded_file($_FILES['favicon']['tmp_name'], $dest)) {
            $path = 'assets/uploads/logo/' . $new_name;
            $this->Model->setValueStore('favicon_ecole', $path);
            $this->json_success(['path' => $path], 'Favicon mis à jour');
        } else {
            $this->json_error('Erreur lors de la sauvegarde du fichier');
        }
    }

    public function api_upload_login_img() {
        if (empty($_FILES['login_img'])) {
            $this->json_error('Aucun fichier reçu'); return;
        }
        if ($_FILES['login_img']['error'] !== UPLOAD_ERR_OK) {
            $errors = [0=>'OK',1=>'Taille dépassée',2=>'Taille HTML dépassée',3=>'Partiel',4=>'Aucun fichier',6=>'Dossier tmp manquant',7=>'Écriture impossible',8=>'Extension bloquée'];
            $this->json_error('Erreur PHP: ' . ($errors[$_FILES['login_img']['error']] ?? 'Inconnue')); return;
        }
        $upload_dir = FCPATH . 'assets/uploads/logo/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $ext = strtolower(pathinfo($_FILES['login_img']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','gif','svg','webp'])) {
            $this->json_error('Format non autorisé'); return;
        }
        $new_name = 'login_img_' . md5(uniqid()) . '.' . $ext;
        $dest = $upload_dir . $new_name;
        if (move_uploaded_file($_FILES['login_img']['tmp_name'], $dest)) {
            $path = 'assets/uploads/logo/' . $new_name;
            $this->Model->setValueStore('login_img', $path);
            $this->json_success(['path' => $path], 'Image de connexion mise à jour');
        } else {
            $this->json_error('Erreur lors de la sauvegarde du fichier');
        }
    }
}
