<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
    * @author:   Jean de Dieu Ntirampeba
    * Email:     jeandedieuntirampeba@gmail.com
    * Gitgub:    https://github.com/porochen
 */
class Admin extends MY_Controller {

	public function index()
	{
		$this->load->view('Login_View');
	}

	public function Login(){
		$this->load->view('Dashboard/Dashboard_View');
	}


	public function do_login($value='')
	{
		$username=$this->input->post('Username');
    	$password=$this->input->post('password');

    	$checkUsername=$this->Model->check_email($username);
    
    if ($checkUsername==TRUE) {

        $login=$this->Model->login($username,$password);

      if ($login && $login['isActive']!=0) {
        
        $result=$this->Model->readOne('users',['idUser'=>$login['idUser']]); 
        if (!$result) {
            $sms['sms']='<div id="message" class="alert alert-danger text-center"><strong>Oups!</strong> Utilisateur non trouvé.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin'));
            return;
        }
        $group=$this->Model->readOne('groups',['idGroup'=>$result['idGroup']]);

        $session = array(
                    'uuid' => $result['uuid'],
                    'username' => $result['username'],
                    'firstName' => $result['firstName'],
                    'lastName' => $result['lastName'],
                    'user' => $result['firstName'].' '.$result['lastName'],
                    'uuidGroup'=>$group ? $group['uuid'] : null,
                    'logged_in'=>TRUE
                );
                    
                $this->session->set_userdata($session);

                redirect(base_url('Dashboard'));

      
      }else{
              // $this->attempt_time($username);
            $sms['sms']='<div id="message" class="alert alert-danger text-center">
                                <strong>Oups!</strong> Mot de passe incorrect ou compte non activé.
                            </div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin'));
      }
    }else{
          $sms['sms']='<div id="message" class="alert alert-danger text-center">
                                <strong>Oups!</strong> Nom d\'utilisateur incorrect ou compte désactivé.
                            </div>';
        $this->session->set_flashdata($sms);
        redirect(base_url('Admin'));
    }
            
	}

	public function Logout(){

    $session = array(
            'uuid' => NULL,
            'username' => NULL,
            'firstName' => NULL,
            'lastName' => NULL,
            'logged_in'=> FALSE
        );

        $this->session->set_userdata($session);
        
        redirect(base_url('Admin'));

	}
}
