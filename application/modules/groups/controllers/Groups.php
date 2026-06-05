<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groups extends MY_Controller {

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
		$data['groups']=$this->Model->read('groups',null,'idGroup');
		$this->load->view('groupsView',$data);
	}


	function Creer_groups(){
        $group_name=$this->input->post('group_name');
		$permission=$this->input->post('permission');
		

		$data=array(
            'group_name'=>$group_name,
            'permission'=>$permission);
	               
		$rsp=$this->Model->create('groups',$data);

		if ($rsp) {
			$sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
						     Contenu créé avec succès.
						 </div>';
		}else{
            $sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
						     <strong class="text-danger">Oups!</strong> Erreur inconnue, contactez l\'administrateur.
						 </div>';
		}
		$this->session->set_flashdata($sms);
		redirect(base_url('groups'));
	}



	function Update_groups(){
		$uuid=$this->input->post('uuid');
        $group_name=$this->input->post('group_name');
        $permission=$this->input->post('permission');
		
		

		$data=array('group_name'=>$group_name,
            'permission'=>$permission,
	               );
		$rsp=$this->Model->update('groups',['uuid'=>$uuid],$data);

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
		redirect(base_url('groups'));
	}


	

	function Supprimer_groups(){
		$uuid=$this->input->post('uuid');
		$rsp=$this->Model->delete('groups',['uuid'=>$uuid]);

		if ($rsp) {
			$sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
						     Contenu supprimé avec succès.
						 </div>';
		}else{
            $sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
						     <strong class="text-danger">Oups!</strong> Erreur inconnue, contactez l\'administrateur.
						 </div>';
		}
		$this->session->set_flashdata($sms);
		redirect(base_url('groups'));
	}
}
