<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author    Dushime Paul
 * Email:     dushimeyesupaulin@gmail.com
 */

class Categories extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Admin'));
            exit;
        }
    }

    // Afficher la liste des catégories
    public function index()
    {
        $data['categories'] = $this->Model->read('categories', null, 'id_categorie');
        $this->load->view('Categories_View', $data);
    }

    // Créer une nouvelle catégorie
    public function create()
    {
        $nom_categories = $this->input->post('nom_categories');
        $image = null;

        if (!empty($_FILES['Image']['name'])) {
            $image = $this->upload_document($_FILES['Image']['tmp_name'], $_FILES['Image']['name']);
        }

        $data = array(
            'nom_categories' => $nom_categories,
            'Image'          => $image
        );

        $rsp = $this->Model->create('categories', $data);

        $sms = [];
        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-success fade show mt-1 message" role="alert">
                             Catégorie créée avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-danger fade show mt-1 message" role="alert">
                             <strong>Oups!</strong> Une erreur inconnue, contactez l\'administrateur.
                         </div>';
        }

        $this->session->set_flashdata($sms);
        redirect(base_url('Categories'));
    }

    // Mettre à jour une catégorie
    public function update()
    {
        $uuid = $this->input->post('uuid');
        $nom_categories = $this->input->post('nom_categories');

        if (!empty($_FILES['Image']['name'])) {
            $image = $this->upload_document($_FILES['Image']['tmp_name'], $_FILES['Image']['name']);
        } else {
            $image = $this->input->post('HiddenImage');
        }

        $data = array(
            'nom_categories' => $nom_categories,
            'Image'          => $image
        );

        $rsp = $this->Model->update('categories', ['uuid' => $uuid], $data);

        $sms = [];
        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-success fade show mt-1 message" role="alert">
                             Catégorie modifiée avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-danger fade show mt-1 message" role="alert">
                             <strong>Oups!</strong> Une erreur inconnue, contactez l\'administrateur.
                         </div>';
        }

        $this->session->set_flashdata($sms);
        redirect(base_url('Categories'));
    }

    // Supprimer une catégorie
    public function delete()
    {
        $uuid = $this->input->post('uuid');
        $rsp = $this->Model->delete('categories', ['uuid' => $uuid]);

        $sms = [];
        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-success fade show mt-1 message" role="alert">
                             Catégorie supprimée avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-danger fade show mt-1 message" role="alert">
                             <strong>Oups!</strong> Une erreur inconnue, contactez l\'administrateur.
                         </div>';
        }

        $this->session->set_flashdata($sms);
        redirect(base_url('Categories'));
    }

    // Upload des images
    public function upload_document($nom_file, $nom_champ)
    {
        $ref_folder = FCPATH . 'attachments/Categorie/';
        $code = date("YmdHis") . uniqid();
        $file_extension = strtolower(pathinfo($nom_champ, PATHINFO_EXTENSION));
        $valid_ext = ['gif','jpg','png','jpeg'];

        if (!in_array($file_extension, $valid_ext)) return null;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $nom_file);
        finfo_close($finfo);
        if (!in_array($mime, ['image/gif','image/jpeg','image/png'])) return null;

        if (!is_dir($ref_folder)) {
            mkdir($ref_folder, 0777, TRUE);
        }

        $fichier = $code . "." . $file_extension;
        move_uploaded_file($nom_file, $ref_folder . $fichier);

        return $fichier;
    }
}