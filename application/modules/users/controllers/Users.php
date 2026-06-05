<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
    * @author:   Jean de Dieu Ntirampeba
    * Email:     jeandedieuntirampeba@gmail.com
    * Gitgub:    https://github.com/porochen
 */
class Users extends MY_Controller {

		function __construct()
    {
        parent::__construct();
        $this->not_logged_in();

		if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Admin'));
            exit;
        }
    }

    

	public function index($uuid='')
	{
		if (!empty($uuid)) {
			$data['users']=$this->Model->readQuery('SELECT u.*,g.group_name FROM users u JOIN `groups` g ON g.idGroup=u.idGroup WHERE u.uuid = ?', [$uuid]);
		} else {
			$data['users']=$this->Model->readQuery('SELECT u.*,g.group_name FROM users u JOIN `groups` g ON g.idGroup=u.idGroup WHERE 1');
		}
		$data['groupes']=$this->Model->readQuery('SELECT * FROM `groups` WHERE 1');
		$this->load->view('Users_View',$data);
	}

	function Create(){

		$FistName=$this->input->post('FistName');
		$LastName=$this->input->post('LastName');
		$Username=$this->input->post('Username');
		$Phone=$this->input->post('Phone');
		$idGroup=$this->input->post('idGroup');
		$email=$this->input->post('email');

		$data=array('firstName'=>$FistName,
					'lastName'=>$LastName,
					'idGroup'=>$idGroup,
					'username'=>$Username,
					'telephone'=>$Phone,
					'email'=>$email,
	               );
		$idUser=$this->Model->createLastId('users',$data);

		$session=array('idUser'=>$idUser,
                       'idGroup'=>$idGroup,
                       'username'=>$Username,
                       'password'=>$this->password_hash('Admin@2025'),
                      );

        $rsp=$this->Model->create('user_group',$session);

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
		redirect(base_url('Users'));
	}

	function Update(){
		
		$FistName=$this->input->post('FistName');
		$LastName=$this->input->post('LastName');
		$Username=$this->input->post('Username');
		$Phone=$this->input->post('Phone');
		$idGroup=$this->input->post('idGroup');
		$email=$this->input->post('email');

		$uuid=$this->input->post('uuid');

		$data=array('firstName'=>$FistName,
								'lastName'=>$LastName,
								'idGroup'=>$idGroup,
								'username'=>$Username,
								'telephone'=>$Phone,
								'email'=>$email,
	               );

		$session=array('idGroup'=>$idGroup,
                   'username'=>$Username
                      );

		$this->Model->update('users',['uuid'=>$uuid],$data);

		$rsp=$this->Model->update('user_group',['uuid'=>$uuid],$session);

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
		redirect(base_url('Users'));
	}


	function Delete(){
		$uuid=$this->input->post('uuid');
		$rsp=$this->Model->delete('users',['uuid'=>$uuid]);

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
		redirect(base_url('Users'));
	}


	public function password_hash($pass = ''){
      if($pass) {
        $password = password_hash($pass, PASSWORD_DEFAULT);
        return $password;
      }
    }

	public function upload_document($nom_file,$nom_champ)
	{
	      $ref_folder =FCPATH.'attachments/Users/';
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

	  public function checkUser(){
      $username=$this->input->post('username');
      $user=$this->Model->readOne('users',['username'=>$username]);
      if (!empty($user)) {
        echo "refusé";
      }else{
        echo "succès";
      }
    }

    public function initialPWD(){

      $uuid=$this->input->post('uuid');
     
      $data=array('password'=>$this->password_hash('Admin@2025'),
                );

      $rsp=$this->Model->update('user_group',['uuid'=>$uuid],$data);
     
     if ($rsp) {
            $sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
                            Contenu mis à jour avec succès.
                         </div>';
        }else{
            $sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
                             Erreur inconnue, contactez l\'administrateur.
                         </div>';
        }

        $this->session->set_flashdata($sms);
        redirect(base_url('Users'));

    }

}