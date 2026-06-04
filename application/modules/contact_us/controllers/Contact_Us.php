<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_Us extends MY_Controller {

	function __construct()
    {
        parent::__construct();
        $this->not_logged_in();

		if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Admin'));
            exit;
        }
    }

    
	public function index()
	{
		$data['contactus']=$this->Model->read('contact_us',null,'IdContact');
		$this->load->view('Contact_Us_View',$data);
	}


	function Create(){
		$FullName=$this->input->post('FullName');
		$Email=$this->input->post('Email');
		$Subject=$this->input->post('Subject');
		$Message=$this->input->post('Message');
		$PhoneNumber=$this->input->post('PhoneNumber');
		$Date_creation=$this->input->post('Date_creation');
		

		$data=array('FullName'=>$FullName,
	                'Email'=>$Email,
	                'Subject'=>$Subject,
	                'Message'=>$Message,
	                'PhoneNumber'=>$PhoneNumber,
	                'Date_creation'=>$Date_creation,
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
		redirect(base_url('Contact_us'));
	}

	function Update(){
		$uuid=$this->input->post('uuid');
        $FullName=$this->input->post('FullName');
		$Email=$this->input->post('Email');
		$Subject=$this->input->post('Subject');
		$Message=$this->input->post('Message');
		$PhoneNumber=$this->input->post('PhoneNumber');
		$Date_creation=$this->input->post('Date_creation');
		

		$data=array('FullName'=>$FullName,
	                'Email'=>$Email,
	                'Subject'=>$Subject,
	                'Message'=>$Message,
	                'PhoneNumber'=>$PhoneNumber,
	                'Date_creation'=>$Date_creation,
	               );
		$rsp=$this->Model->update('contact_us',['uuid'=>$uuid],$data);

		if ($rsp) {
			$sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
						     Content updated successfully.
						 </div>';
		}else{
            $sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
						     <strong class="text-danger">Oups!</strong> An unknown error, contact admin!.
						 </div>';
		}
		$this->session->set_flashdata($sms);
		redirect(base_url('Contact_us'));
	}


	function Delete(){
		$uuid=$this->input->post('uuid');
		$rsp=$this->Model->delete('contact_us',['uuid'=>$uuid]);

		if ($rsp) {
			$sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
						     Content deleted successfully.
						 </div>';
		}else{
            $sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
						     <strong class="text-danger">Oups!</strong> An unknown error, contact admin!.
						 </div>';
		}
		$this->session->set_flashdata($sms);
		redirect(base_url('Contact_us'));
	}







	//   //upload images
	// public function upload_document($nom_file,$nom_champ)
	// {
	//       $ref_folder =FCPATH.'attachments/Carousel/';
	//       $code=date("YmdHis").uniqid();
	//       $fichier=basename($code);
	//       $file_extension = pathinfo($nom_champ, PATHINFO_EXTENSION);
	//       $file_extension = strtolower($file_extension);
	//       $valid_ext = array('gif','jpg','png','jpeg','JPG','PNG','JPEG');

	//       if(!is_dir($ref_folder)) //create the folder if it does not already exists   
	//       {
	//           mkdir($ref_folder,0777,TRUE);                                        
	//       }  
	//       move_uploaded_file($nom_file, $ref_folder.$fichier.".".$file_extension);
	//       // $pathfile="attachments/shop_images/".$fichier.".".$file_extension;
	//       $image_name=$fichier.".".$file_extension;
	//       return $image_name;
	// }
}
