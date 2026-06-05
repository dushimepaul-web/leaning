<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
    * @author:   Jean de Dieu Ntirampeba
    * Email:     jeandedieuntirampeba@gmail.com
    * Gitgub:    https://github.com/porochen
 */
class Testimony extends MY_Controller {

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
		$data['testimonies']=$this->Model->readQuery('SELECT * FROM testimonies t  WHERE 1');
		$this->load->view('Testimony_View',$data);
	}

	function Create(){

		$Testifier=$this->input->post('FullName');
		$Poste=$this->input->post('Poste');
		$Details=$this->input->post('Details');

		$Image='';
		if (!empty($_FILES['Image']['name'])) {
		  $Image=$this->upload_document($_FILES['Image']['tmp_name'],$_FILES['Image']['name']);
		}

		$data=array('Testifier'=>$Testifier,
					'Poste'=>$Poste,
					'Image'=>$Image,
					'Details'=>$Details,
	               );
		$rsp=$this->Model->create('testimonies',$data);

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
		redirect(base_url('Testimony'));
	}

	function Update(){

		$Testifier=$this->input->post('FullName');
		$Poste=$this->input->post('Poste');
		$Details=$this->input->post('Details');
		$uuid=$this->input->post('uuid');

		$Image='';
		if (!empty($_FILES['Image']['name'])) {
		  $Image=$this->upload_document($_FILES['Image']['tmp_name'],$_FILES['Image']['name']);
		}else{
		  $Image=$this->input->post('HiddenImage');
		}

		$data=array('Testifier'=>$Testifier,
					'Poste'=>$Poste,
					'Image'=>$Image,
					'Details'=>$Details,
	               );


		$rsp=$this->Model->update('testimonies',['uuid'=>$uuid],$data);

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
		redirect(base_url('Testimony'));
	}


	function Delete(){
		$uuid=$this->input->post('uuid');
		$rsp=$this->Model->delete('testimonies',['uuid'=>$uuid]);

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
		redirect(base_url('Testimony'));
	}

	//upload images
	public function upload_document($nom_file,$nom_champ)
	{
	      $ref_folder =FCPATH.'attachments/Testimony/';
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