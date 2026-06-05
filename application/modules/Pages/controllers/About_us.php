<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About_us extends MY_Controller {

	function __construct()
    {
        parent::__construct();
        //$this->not_logged_in();
    }

    
	public function index()
	{   
        
		$data['about_us'] = $this->Model->read('about_us', null, 'id_about_us');
		$data['vision']=$this->Model->read('vision',null,'id_vision');
		$data['mission']=$this->Model->read('mission',null,'id_mission');
		$this->load->view('about_us_View',$data);
	}

	public function contact(){
		$data['contact'] = $this->Model->read('contact_us', null, 'IdContact');
		$this->load->view('Contact_us_View',$data);
	}
     
   function Createcontactus(){
		$FullName=$this->input->post('FullName');
		$Email=$this->input->post('Email');
		$Subject=$this->input->post('Subject');
		$Message=$this->input->post('Message');
		$PhoneNumber=$this->input->post('PhoneNumber');
		

		$data=array('FullName'=>$FullName,
	                'Email'=>$Email,
	                'Subject'=>$Subject,
	                'Message'=>$Message,
	                'PhoneNumber'=>$PhoneNumber,
	               );
		$rsp=$this->Model->create('contact_us',$data);

		if ($rsp) {
			$this->load->library('Cpanel_email');
			$msg_admin = "
			<h2>Nouveau message de contact</h2>
			<p><strong>Nom :</strong> $FullName</p>
			<p><strong>Email :</strong> $Email</p>
			<p><strong>Téléphone :</strong> $PhoneNumber</p>
			<p><strong>Sujet :</strong> $Subject</p>
			<p><strong>Message :</strong><br>" . nl2br($Message) . "</p>
			";
			$this->cpanel_email_lib->send_email('infos@cerfop.bi', 'Nouveau contact - ' . $Subject, $msg_admin);

			$sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
						     Contenu créé avec succès.
						 </div>';
		}else{
            $sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
						     <strong class="text-danger">Oups!</strong> Erreur inconnue, contactez l\'administrateur.
						 </div>';
		}
		$this->session->set_flashdata($sms);
		redirect(base_url('Pages/About_us/contact'));
	}

}

