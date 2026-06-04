<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Students extends MY_Controller {

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
		$data['student']=$this->Model->read('students',null,'id_student');
		$this->load->view('student_View',$data);
	}


	
	function Createstudent(){
		$fullname=$this->input->post('fullname');
		$email=$this->input->post('email');
		$phone=$this->input->post('phone');
		$address=$this->input->post('address');
		$localisation=$this->input->post('localisation');
		
		

		$data=array('fullname'=>$fullname,
	                'email'=>$email,
	                'phone'=>$phone,
	                'address'=>$address,
	                'localisation'=>$localisation,
	                
	               );
		$rsp=$this->Model->create('students',$data);

		if ($rsp) {
			$sms['sms']='<div class="alert alert-background fade show mt-1 address" role="alert">
						     Content created successfully.
						 </div>';
		}else{
            $sms['sms']='<div class="alert alert-background fade show mt-1 address" role="alert">
						     <strong class="text-danger">Oups!</strong> An unknown error, contact admin!.
						 </div>';
		}
		$this->session->set_flashdata($sms);
		redirect(base_url('students'));
	}

	function Updatestudent(){
		$uuid=$this->input->post('uuid');
        $fullname=$this->input->post('fullname');
		$email=$this->input->post('email');
		$phone=$this->input->post('phone');
		$address=$this->input->post('address');
		$localisation=$this->input->post('localisation');
		
		

		$data=array('fullname'=>$fullname,
	                'email'=>$email,
	                'phone'=>$phone,
	                'address'=>$address,
	                'localisation'=>$localisation,
	                
	               );
		$rsp=$this->Model->update('students',['uuid'=>$uuid],$data);

		if ($rsp) {
			$sms['sms']='<div class="alert alert-background fade show mt-1 address" role="alert">
						     Content updated successfully.
						 </div>';
		}else{
            $sms['sms']='<div class="alert alert-background fade show mt-1 address" role="alert">
						     <strong class="text-danger">Oups!</strong> An unknown error, contact admin!.
						 </div>';
		}
		$this->session->set_flashdata($sms);
		redirect(base_url('students'));
	}


	function Deletestudent(){
		$uuid=$this->input->post('uuid');
		$rsp=$this->Model->delete('students',['uuid'=>$uuid]);

		if ($rsp) {
			$sms['sms']='<div class="alert alert-background fade show mt-1 address" role="alert">
						     Content deleted successfully.
						 </div>';
		}else{
            $sms['sms']='<div class="alert alert-background fade show mt-1 address" role="alert">
						     <strong class="text-danger">Oups!</strong> An unknown error, contact admin!.
						 </div>';
		}
		$this->session->set_flashdata($sms);
		redirect(base_url('students'));
	}


}


