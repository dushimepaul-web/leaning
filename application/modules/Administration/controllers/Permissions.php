<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permissions extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des permissions';
        $data['roles'] = $this->Model->read('roles', ['deleted_at' => null]);
        $data['menus'] = $this->db->order_by('ordre')->get_where('menus', ['parent_id' => null])->result_array();
        $this->load->view('permissions/index', $data);
    }
}
