<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rapports extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Rapports';
        $this->load->view('index', $data);
    }
}
