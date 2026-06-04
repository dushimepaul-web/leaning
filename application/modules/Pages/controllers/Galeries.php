<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Galeries extends MY_Controller {

	function __construct()
    {
        parent::__construct();
        //$this->not_logged_in();
    }

    
	public function index()
	{   
        
		
		$data['galleries'] = $this->Model->read('gallery', null, 'IdGallery');
		$this->load->view('Galeries_View',$data);
	}

	public function contact(){
		$data['contact'] = $this->Model->read('contact_us', null, 'IdContact');
		$this->load->view('Contact_us_View',$data);
	}


	
}