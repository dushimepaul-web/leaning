<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *@author:    Dushime paul
 * Email:    dushimeyesupaulin@gmail.com
*/

class Attendace_course_mode extends MY_Controller {

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
        $data['attendance_modes'] = $this->Model->read('attendace_course_mode', null, 'id_attendance');
        $this->load->view('Attendace_course_mode_View', $data);
    }


    function CreateAttendaceMode(){
        $nom_attendance = $this->input->post('nom_attendance');

        $data = array(
            'nom_attendance' => $nom_attendance
        );
        
        $rsp = $this->Model->create('attendace_course_mode', $data);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Mode de présence créé avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Attendace_course_mode'));
    }



    function UpdateAttendaceMode(){
        $uuid = $this->input->post('uuid');
        $nom_attendance = $this->input->post('nom_attendance');

        $data = array(
            'nom_attendance' => $nom_attendance
        );
        
        $rsp = $this->Model->update('attendace_course_mode', ['uuid' => $uuid], $data);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Mode de présence modifié avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Attendace_course_mode'));
    }


    function DeleteAttendaceMode(){
        $uuid = $this->input->post('uuid');
        $rsp = $this->Model->delete('attendace_course_mode', ['uuid' => $uuid]);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Mode de présence supprimé avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Attendace_course_mode'));
    }
}