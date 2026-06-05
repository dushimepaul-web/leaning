<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *@author:    Dushime Paul
 * Email:    dushimeyesupaulin@gmail.com
*/

class Teachers extends MY_Controller {

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
        $data['teachers'] = $this->Model->read('teachers', null, 'id_teacher');
        $this->load->view('Teachers_View', $data);
    }


    function CreateTeacher(){
        $nom = $this->input->post('nom');
        $prenom = $this->input->post('prenom');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        $status = $this->input->post('status');
        $address = $this->input->post('address');

        $data = array(
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'phone' => $phone,
            'status' => $status,
            'address' => $address,
            'date_insertion' => date('Y-m-d H:i:s')
        );
        
        $rsp = $this->Model->create('teachers', $data);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Enseignant créé avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Teachers'));
    }



    function UpdateTeacher(){
        $uuid = $this->input->post('uuid');
        $nom = $this->input->post('nom');
        $prenom = $this->input->post('prenom');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        $status = $this->input->post('status');
        $address = $this->input->post('address');

        $data = array(
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'phone' => $phone,
            'status' => $status,
            'address' => $address
        );
        
        $rsp = $this->Model->update('teachers', ['uuid' => $uuid], $data);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Enseignant modifié avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Teachers'));
    }


    function DeleteTeacher(){
        $uuid = $this->input->post('uuid');
        $rsp = $this->Model->delete('teachers', ['uuid' => $uuid]);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             Enseignant supprimé avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Une erreur inconnue, contactez l\'administrateur!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Teachers'));
    }

    function ChangeStatus(){
      $uuid=$this->input->post('uuid');
      $IsActive=$this->input->post('status');
      if ($IsActive==1) {
        $status=0;
      }else{
        $status=1;
      }
      $rsp=$this->Model->update('teachers',['uuid'=>$uuid],['status'=>$status]);

        if ($rsp) {
            $sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
                             Contenu mis à jour avec succès.
                         </div>';
        }else{
            $sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> Erreur inconnue, contactez l\'administrateur.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Teachers')); 
    }
}