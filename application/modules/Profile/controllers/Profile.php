<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends MY_Controller {

    function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Admin'));
            exit;
        }
    }

    public function index()
    {
        $uuid = $this->session->userdata('uuid');
        $data['user'] = $this->Model->readOne('users', ['uuid' => $uuid]);
        $data['group'] = $this->Model->readOne('groups', ['uuid' => $this->session->userdata('uuidGroup')]);
        $data['page_title'] = 'Mon Profil';
        $this->load->view('Profile_View', $data);
    }

    public function update_info()
    {
        $uuid = $this->session->userdata('uuid');

        $data = [
            'firstName' => $this->input->post('firstName'),
            'lastName'  => $this->input->post('lastName'),
            'email'     => $this->input->post('email'),
            'telephone' => $this->input->post('telephone'),
        ];

        $rsp = $this->Model->update('users', ['uuid' => $uuid], $data);

        if ($rsp) {
            $user = $this->Model->readOne('users', ['uuid' => $uuid]);
            $this->session->set_userdata([
                'firstName' => $user['firstName'],
                'lastName'  => $user['lastName'],
                'user'      => $user['firstName'] . ' ' . $user['lastName'],
            ]);
            $this->session->set_flashdata('success', 'Profil mis à jour avec succès.');
        } else {
            $this->session->set_flashdata('error', 'Erreur lors de la mise à jour du profil.');
        }

        redirect(base_url('Profile'));
    }

    public function update_password()
    {
        $uuid = $this->session->userdata('uuid');
        $current = $this->input->post('current_password');
        $new     = $this->input->post('new_password');
        $confirm = $this->input->post('confirm_password');

        $user_group = $this->Model->readOne('user_group', ['uuid' => $uuid]);

        if (!$user_group || md5($current) !== $user_group['password']) {
            $this->session->set_flashdata('error', 'Mot de passe actuel incorrect.');
            redirect(base_url('Profile'));
            return;
        }

        if ($new !== $confirm) {
            $this->session->set_flashdata('error', 'Les nouveaux mots de passe ne correspondent pas.');
            redirect(base_url('Profile'));
            return;
        }

        if (strlen($new) < 6) {
            $this->session->set_flashdata('error', 'Le mot de passe doit contenir au moins 6 caractères.');
            redirect(base_url('Profile'));
            return;
        }

        $rsp = $this->Model->update('user_group', ['uuid' => $uuid], [
            'password' => md5($new)
        ]);

        if ($rsp) {
            $this->session->set_flashdata('success', 'Mot de passe modifié avec succès.');
        } else {
            $this->session->set_flashdata('error', 'Erreur lors de la modification du mot de passe.');
        }

        redirect(base_url('Profile'));
    }

    public function update_avatar()
    {
        $uuid = $this->session->userdata('uuid');

        if (!empty($_FILES['avatar']['name'])) {
            $avatar = $this->upload_avatar($_FILES['avatar']['tmp_name'], $_FILES['avatar']['name']);
            if ($avatar) {
                $this->Model->update('users', ['uuid' => $uuid], ['image' => $avatar]);
                $this->session->set_flashdata('success', 'Avatar mis à jour.');
            } else {
                $this->session->set_flashdata('error', 'Format d\'image invalide (jpg, png, gif uniquement).');
            }
        }

        redirect(base_url('Profile'));
    }

    private function upload_avatar($tmp_name, $filename)
    {
        $folder = FCPATH . 'attachments/Users/';
        $code = date("YmdHis") . uniqid();
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $valid_ext = ['gif', 'jpg', 'png', 'jpeg'];

        if (!in_array($ext, $valid_ext)) return null;

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $image_name = $code . '.' . $ext;
        move_uploaded_file($tmp_name, $folder . $image_name);
        return $image_name;
    }
}
