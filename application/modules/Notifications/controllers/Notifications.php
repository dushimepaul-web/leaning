<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Notifications';
        $this->load->view('notifications/index', $data);
    }

    public function api_list() {
        $userId = $this->session->userdata('uuid');
        $this->db->where('deleted_at', null);
        $this->db->where('id_utilisateur', $userId);
        $this->db->order_by('cree_le', 'DESC');
        $this->db->limit(20);
        $this->json_success($this->db->get('notifications')->result_array());
    }

    public function api_mark_read($id) {
        $this->db->where('uuid', $id);
        if ($this->db->update('notifications', ['lu' => 1, 'date_lu' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Notification marquée comme lue');
        else $this->json_error('Erreur');
    }
}
