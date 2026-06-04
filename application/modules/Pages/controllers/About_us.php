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
			$sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
						     Content created successfully.
						 </div>';
		}else{
            $sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
						     <strong class="text-danger">Oups!</strong> An unknown error, contact admin!.
						 </div>';
		}
		$this->session->set_flashdata($sms);
		redirect(base_url('Pages/About_us/contact'));
	}

}

