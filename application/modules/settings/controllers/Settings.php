<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
    * @author:   Jean de Dieu Ntirampeba
    * Email:     jeandedieuntirampeba@gmail.com
    * Gitgub:    https://github.com/porochen
 */
class Settings extends MY_Controller {

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
		$data['settings']=$this->Model->read('settings');
		$this->load->view('Settings_View',$data);
	}

	function Create(){
		$TitlePage=$this->input->post('TitlePage');
		$KeyValue=$this->input->post('KeyValue');
		$Value=$this->input->post('Value');
		$isFile = $this->input->post('toggleInputCheckbox');

		$ISFILE=0;

		if (isset($isFile)) {
			$Value=$this->upload_document($_FILES['Value']['tmp_name'],$_FILES['Value']['name']);
			$ISFILE=1;
		}

		$data=array('KeyValue'=>$KeyValue,
			        'TitlePage'=>$TitlePage,
	                'Value'=>$Value,
	                'IsFile'=>$ISFILE
	               );
		$rsp=$this->Model->create('settings',$data);

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
		redirect(base_url('Settings'));
	}

	function Update(){
		$uuid=$this->input->post('uuid');
		$KeyValue=$this->input->post('KeyValue');
		$TitlePage=$this->input->post('TitlePage');
		$Value=$this->input->post('Value');
		$IsFile=$this->input->post('IsFile');

		if ($IsFile==1) {
		
			if (!empty($_FILES['Value']['name'])) {
			  $Value=$this->upload_document($_FILES['Value']['tmp_name'],$_FILES['Value']['name']);
			}else{
			  $Value=$this->input->post('HiddenImage');
			}

		}
		
		$data=array('KeyValue'=>$KeyValue,
	                'Value'=>$Value,
	                'TitlePage'=>$TitlePage,
	               );

		$rsp=$this->Model->update('settings',['uuid'=>$uuid],$data);

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
		redirect(base_url('Settings'));
	}


	function Delete(){
		$uuid=$this->input->post('uuid');
		$rsp=$this->Model->delete('settings',['uuid'=>$uuid]);

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
		redirect(base_url('Settings'));
	}


	public function upload_document($nom_file,$nom_champ)
	{
	      $ref_folder =FCPATH.'attachments/Other/';
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