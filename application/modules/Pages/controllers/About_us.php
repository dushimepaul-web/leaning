<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About_us extends MY_Controller {

	function __construct()
    {
        parent::__construct();
        //$this->not_logged_in();
    }

    // Page publique
	public function index()
	{
		$data['about_us'] = $this->Model->read('institution_contents', ['Type'=>'ABOUT_US', 'Status'=>'Active'], 'IdContent');
		$data['vision'] = $this->Model->read('institution_contents', ['Type'=>'VISION', 'Status'=>'Active'], 'IdContent');
		$data['mission'] = $this->Model->read('institution_contents', ['Type'=>'MISSION', 'Status'=>'Active'], 'IdContent');
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

	// Pages publiques génériques
   public function Valeurs(){
		$data['contents'] = $this->Model->read('institution_contents', ['Type'=>'VALEUR', 'Status'=>'Active'], 'IdContent');
		$data['title'] = 'Nos Valeurs';
		$this->load->view('generique_View',$data);
	}

	public function Axe_stategique(){
		$data['contents'] = $this->Model->read('institution_contents', ['Type'=>'AXE_STRATEGIQUE', 'Status'=>'Active'], 'IdContent');
		$data['title'] = 'Axes Stratégiques';
		$this->load->view('generique_View',$data);
	}

	public function Modele_pedagogique(){
		$data['contents'] = $this->Model->read('institution_contents', ['Type'=>'MODELE_PEDAGOGIQUE', 'Status'=>'Active'], 'IdContent');
		$data['title'] = 'Modèle Pédagogique';
		$this->load->view('generique_View',$data);
	}

	public function Partenariat_stategiques(){
		$data['contents'] = $this->Model->read('institution_contents', ['Type'=>'PARTENARIAT_STRATEGIQUE', 'Status'=>'Active'], 'IdContent');
		$data['title'] = 'Partenariats Stratégiques';
		$this->load->view('generique_View',$data);
	}

	// ADMINISTRATION - Gestion des contenus institutionnels
	public function manage(){
		$type = $this->input->get('type');
		$data['type'] = $type ?: 'VISION';
		$data['contents'] = $this->Model->read('institution_contents', ['Type'=>$data['type']], 'IdContent');
		$data['types'] = ['ABOUT_US'=>'À Propos', 'VISION'=>'Vision', 'MISSION'=>'Mission', 'VALEUR'=>'Valeurs', 'AXE_STRATEGIQUE'=>'Axes Stratégiques', 'MODELE_PEDAGOGIQUE'=>'Modèle Pédagogique', 'PARTENARIAT_STRATEGIQUE'=>'Partenariats Stratégiques'];
		$this->load->view('admin/manage_contents_View',$data);
	}







}


