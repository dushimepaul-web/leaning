<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About_us extends MY_Controller {

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
		$data['about_us']=$this->Model->read('about_us',null,'id_about_us');
		$this->load->view('about_us_view',$data);
	}


	function Creer_about_us(){
        $title=$this->input->post('title');
		$details=$this->input->post('details');
		

        
		$data=array('title'=>$title,
        'details'=>$details);

	               
		$rsp=$this->Model->create('about_us',$data);

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
		redirect(base_url('about_us'));
	}



	function Update_about_us(){
		$uuid=$this->input->post('uuid');
        $title=$this->input->post('title');
        $details=$this->input->post('details');
		
		

       
		$data=array(
        'title'=>$title,
        'details'=>$details,
	               );
		$rsp=$this->Model->update('about_us',['uuid'=>$uuid],$data);

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
		redirect(base_url('about_us'));
	}


	

	function Supprimer_about_us(){
		$uuid=$this->input->post('uuid');
		$rsp=$this->Model->delete('about_us',['uuid'=>$uuid]);

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
		redirect(base_url('about_us'));
	}
}
