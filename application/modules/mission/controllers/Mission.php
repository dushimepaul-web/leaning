<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mission extends MY_Controller {

	function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
		$this->load->model('Model');


	if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Admin'));
            exit;
        }
    }

    
	public function index()
	{
		$data['mission']=$this->Model->read('mission',null,'id_mission');
		$this->load->view('missionView',$data);
	}


	function Creer_mission(){
		$Description=$this->input->post('Description');
		

		$data=array('content'=>$Description);
	               
		$rsp=$this->Model->create('mission',$data);

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
		redirect(base_url('mission'));
	}



	function Update_mission(){
		$uuid=$this->input->post('uuid');
        $Description=$this->input->post('Description');
		
		

		$data=array('content'=>$Description,
	               );
		$rsp=$this->Model->update('mission',['uuid'=>$uuid],$data);

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
		redirect(base_url('mission'));
	}


	

	function Supprimer_mision(){
		$uuid=$this->input->post('uuid');
		$rsp=$this->Model->delete('mission',['uuid'=>$uuid]);

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
		redirect(base_url('mission'));
	}
}
