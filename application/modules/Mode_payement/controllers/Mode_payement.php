<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *@author:    Dushime Paul
 * Email:    dushimeyesupaulin@gmail.com
*/

class Mode_payement extends MY_Controller {

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
        $data['mode_payements'] = $this->Model->read('mode_payement', null, 'id_mode_payement');
        $this->load->view('Mode_payement_View', $data);
    }


    function CreateModePayement(){
        $description = $this->input->post('description');

        $data = array(
            'description' => $description
        );
        
        $rsp = $this->Model->create('mode_payement', $data);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Mode de paiement créé avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Mode_payement'));
    }



    function UpdateModePayement(){
        $uuid = $this->input->post('uuid');
        $description = $this->input->post('description');

        $data = array(
            'description' => $description
        );
        
        $rsp = $this->Model->update('mode_payement', ['uuid' => $uuid], $data);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Mode de paiement modifié avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Mode_payement'));
    }


    function DeleteModePayement(){
        $uuid = $this->input->post('uuid');
        $rsp = $this->Model->delete('mode_payement', ['uuid' => $uuid]);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Mode de paiement supprimé avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Mode_payement'));
    }
}