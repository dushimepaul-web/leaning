<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Notifications';
        $this->load->view('index', $data);
    }

    public function api_list() {
        $userId = $this->session->userdata('id_utilisateur');
        if (!$userId) $this->json_error('Non authentifié', 401);
        $this->db->where('deleted_at', null);
        $this->db->where('id_utilisateur', $userId);
        $this->db->order_by('cree_le', 'DESC');
        $this->db->limit(50);
        $this->json_success($this->db->get('notifications')->result_array());
    }

    public function api_mark_read($id) {
        $userId = $this->session->userdata('id_utilisateur');
        if (!$userId) $this->json_error('Non authentifié', 401);
        $this->db->where('uuid', $id);
        $this->db->where('id_utilisateur', $userId);
        if ($this->db->update('notifications', ['lu' => 1, 'date_lu' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Notification marquée comme lue');
        else $this->json_error('Erreur');
    }
}
