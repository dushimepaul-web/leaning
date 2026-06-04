<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *@author:    Dushime Paul
 * Email:    dushimeyesupaulin@gmail.com
*/
class Timetable_courses extends MY_Controller {

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
    // Requête SQL corrigée
    $sql = "
        SELECT tc.*, c.nom_course, t.date_debut, t.date_defin
        FROM timetable_courses tc
        LEFT JOIN courses c ON tc.id_course = c.id_course
        LEFT JOIN timetable t ON tc.id_timetable = t.id_timetable
        ORDER BY tc.id_timetable_course DESC
    ";
    $data['timetable_courses'] = $this->Model->readQuery($sql);
    $data['courses'] = $this->Model->read('courses');
    $data['timetables'] = $this->Model->read('timetable');

    $this->load->view('Timetable_courses_View', $data);
}
    function CreateTimetableCourse(){
        $id_course = $this->input->post('id_course');
        $id_timetable = $this->input->post('id_timetable');
        $localisation = $this->input->post('localisation');
        $price = $this->input->post('price');

        $data = array(
            'id_course' => $id_course,
            'id_timetable' => $id_timetable,
            'localisation' => $localisation,
            'price' => $price,
            'date_insertion' => date('Y-m-d H:i:s')
        );
        
        $rsp = $this->Model->create('timetable_courses', $data);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Cours planifié avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Timetable_courses'));
    }



    function UpdateTimetableCourse(){
        $uuid = $this->input->post('uuid');
        $id_course = $this->input->post('id_course');
        $id_timetable = $this->input->post('id_timetable');
        $localisation = $this->input->post('localisation');
        $price = $this->input->post('price');

        $data = array(
            'id_course' => $id_course,
            'id_timetable' => $id_timetable,
            'localisation' => $localisation,
            'price' => $price
        );
        
        $rsp = $this->Model->update('timetable_courses', ['uuid' => $uuid], $data);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Planification modifiée avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Timetable_courses'));
    }


    function DeleteTimetableCourse(){
        $uuid = $this->input->post('uuid');
        $rsp = $this->Model->delete('timetable_courses', ['uuid' => $uuid]);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Planification supprimée avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Timetable_courses'));
    }
}