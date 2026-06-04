<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Carousel extends MY_Controller {

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
		$data['carousel']=$this->Model->read('carousels',null,'IdCarousel');
		$this->load->view('Carousel_View',$data);
	}


	function ChangeStatus(){
	  $uuid=$this->input->post('uuid');
	  $IsActive=$this->input->post('IsActive');
	  if ($IsActive==1) {
	  	$status=0;
	  }else{
	  	$status=1;
	  }
	  $rsp=$this->Model->update('carousels',['uuid'=>$uuid],['IsActive'=>$status]);

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
		redirect(base_url('Carousel'));	
	}

	function CarouselDetail($CarouselDetail){
	  $IdCarousel=explode('_', $CarouselDetail);
	  $data['detail']=$this->Model->readOne('carousels',['uuid'=>$uuid]);
	  $this->load->view('CarouselDetail_View',$data);
	}

	function SaveDetail(){
	  $uuid=$this->input->post('uuid');
	  $Detail=$this->input->post('Detail');
	  $rsp=$this->Model->update('carousels',['uuid'=>$uuid],['Detail'=>$Detail]);
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
		redirect(base_url('Carousel'));
	}

	function Create(){
		$Title=$this->input->post('Title');
		$Description=$this->input->post('Description');
		$Image=$this->upload_document($_FILES['Image']['tmp_name'],$_FILES['Image']['name']);

		$data=array('Title'=>$Title,
	                'Description'=>$Description,
	                'Image'=>$Image,
	               );
		$rsp=$this->Model->create('carousels',$data);

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
		redirect(base_url('Carousel'));
	}

	function Update(){
		$uuid=$this->input->post('uuid');
		$Title=$this->input->post('Title');
		$Description=$this->input->post('Description');
		// $HiddenImage=$this->input->post('HiddenImage');
       
		if (!empty($_FILES['Image']['name'])) {
		  $Image=$this->upload_document($_FILES['Image']['tmp_name'],$_FILES['Image']['name']);
		}else{
		  $Image=$this->input->post('HiddenImage');
		}
		

		$data=array('Title'=>$Title,
	                'Description'=>$Description,
	                'Image'=>$Image,
	               );
		$rsp=$this->Model->update('carousels',['uuid'=>$uuid],$data);

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
		redirect(base_url('Carousel'));
	}


	function Delete(){
		$uuid=$this->input->post('uuid');
		$rsp=$this->Model->delete('carousels',['uuid'=>$uuid]);

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
		redirect(base_url('Carousel'));
	}







	  //upload images
	public function upload_document($nom_file,$nom_champ)
	{
	      $ref_folder =FCPATH.'attachments/Carousel/';
	      $code=date("YmdHis").uniqid();
	      $file_extension = strtolower(pathinfo($nom_champ, PATHINFO_EXTENSION));
	      $valid_ext = ['gif','jpg','png','jpeg'];

	      if (!in_array($file_extension, $valid_ext)) return null;

	      $finfo = finfo_open(FILEINFO_MIME_TYPE);
	      $mime = finfo_file($finfo, $nom_file);
	      finfo_close($finfo);
	      if (!in_array($mime, ['image/gif','image/jpeg','image/png'])) return null;

	      if(!is_dir($ref_folder))
	      {
	          mkdir($ref_folder,0777,TRUE);
	      }
	      $image_name=$code.".".$file_extension;
	      move_uploaded_file($nom_file, $ref_folder.$image_name);
	      return $image_name;
	}
}
