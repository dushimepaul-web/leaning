<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *@author:    Dushime Paul
 * Email:    dushimeyesupaulin@gmail.com
*/
class Parteners extends MY_Controller {

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
		$data['parteners']=$this->Model->read('partener',null,'id_partner');
		$this->load->view('Partener_View',$data);
	}


	function Changestatus(){
	  $uuid=$this->input->post('uuid');
	  $status=$this->input->post('status');
	  if ($status==1) {
	  	$status=0;
	  }else{
	  	$status=1;
	  }
	  $rsp=$this->Model->update('partener',['uuid'=>$uuid],['status'=>$status]);

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
		redirect(base_url('Parteners'));	
	}

	function Create(){
		$description=$this->input->post('description');
		$logo=$this->upload_document($_FILES['logo']['tmp_name'],$_FILES['logo']['name']);
		$link=$this->input->post('link');
		

		$data=array('description'=>$description,
			         'logo'=>$logo,
	                 'link'=>$link,
	                
	               );
		$rsp=$this->Model->create('partener',$data);

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
		redirect(base_url('Parteners'));
	}

	function Update(){
		$uuid=$this->input->post('uuid');
		$description=$this->input->post('description');

		if (!empty($_FILES['logo']['name'])) {
		  $logo=$this->upload_document($_FILES['logo']['tmp_name'],$_FILES['logo']['name']);
		}else{
		  $logo=$this->input->post('Hiddenlogo');
		}
		
		$link=$this->input->post('link');
     
		
		$data=array('description'=>$description,
			         'logo'=>$logo,
	                 'link'=>$link,
	                
	               );
		$rsp=$this->Model->update('partener',['uuid'=>$uuid],$data);

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
		redirect(base_url('Parteners'));
	}


	function Delete(){
		$uuid=$this->input->post('uuid');
		$rsp=$this->Model->delete('partener',['uuid'=>$uuid]);

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
		redirect(base_url('Parteners'));
	}

	  //upload logos
	public function upload_document($nom_file,$nom_champ)
	{
	      $ref_folder =FCPATH.'attachments/Partener/';
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
	      $logo_name=$code.".".$file_extension;
	      move_uploaded_file($nom_file, $ref_folder.$logo_name);
	      return $logo_name;
	}
}
