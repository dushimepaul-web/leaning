<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des rôles';
        $this->load->view('roles/index', $data);
    }

    public function api_menus() {
        $this->db->order_by('ordre');
        $menus = $this->db->get('menus')->result_array();
        $this->json_success($menus);
    }
}
