<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *@author:    Votre Nom
 * Email:    votre.email@gmail.com
*/

class Courses extends MY_Controller {

    function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Admin'));
            exit;
        }
    }

    
    public function index()
    {
        // Récupérer les cours avec les informations des tables liées
        $data['courses'] = $this->Model->getCoursesWithDetails();
        $data['categories'] = $this->Model->read('categories', null, 'id_categorie');
        $data['teachers'] = $this->Model->read('teachers', null, 'id_teacher');
        $this->load->view('Courses_View', $data);
    }


    function CreateCourse(){
        $nom_course = $this->input->post('nom_course');
        $id_categorie = $this->input->post('id_categorie');
        $id_teacher = $this->input->post('id_teacher');
        $description = $this->input->post('description');

        $image = '';
        if (!empty($_FILES['image']['name'])) {
            $image = $this->upload_document($_FILES['image']['tmp_name'], $_FILES['image']['name']);
        }

        $data = array(
            'nom_course' => $nom_course,
            'image' => $image,
            'id_categorie' => $id_categorie,
            'id_teacher' => $id_teacher,
            'description' => $description,
            'date_insertion' => date('Y-m-d H:i:s')
        );
        
        $rsp = $this->Model->create('courses', $data);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Cours créé avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Courses'));
    }



    function UpdateCourse(){
        $uuid = $this->input->post('uuid');
        $nom_course = $this->input->post('nom_course');
        $id_categorie = $this->input->post('id_categorie');
        $id_teacher = $this->input->post('id_teacher');
        $description = $this->input->post('description');

        $data = array(
            'nom_course' => $nom_course,
            'id_categorie' => $id_categorie,
            'id_teacher' => $id_teacher,
            'description' => $description
        );

        if (!empty($_FILES['image']['name'])) {
            $data['image'] = $this->upload_document($_FILES['image']['tmp_name'], $_FILES['image']['name']);
        }
        
        $rsp = $this->Model->update('courses', ['uuid' => $uuid], $data);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Cours modifié avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Courses'));
    }


    function DeleteCourse(){
        $uuid = $this->input->post('uuid');
        $rsp = $this->Model->delete('courses', ['uuid' => $uuid]);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Cours supprimé avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Courses'));
    }

    public function upload_document($tmp_name, $file_name) {
        $folder = FCPATH . 'attachments/Courses/';
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $valid = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $valid)) return null;
        if (!is_dir($folder)) mkdir($folder, 0777, true);
        $name = date('YmdHis') . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($tmp_name, $folder . $name);
        return $name;
    }
}