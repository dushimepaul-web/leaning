<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Messages extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Messagerie';
        $data['utilisateurs'] = $this->Model->read('utilisateurs', ['actif' => 1, 'deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $user = $this->Model->readOne('utilisateurs', ['uuid' => $this->session->userdata('uuid')]);
        $id_utilisateur = $user ? $user['id_utilisateur'] : 0;
        $this->db->where('m.deleted_at', null);
        $this->db->group_start();
        $this->db->where('m.id_destinataire', $id_utilisateur);
        $this->db->or_where('m.id_expediteur', $id_utilisateur);
        $this->db->group_end();
        $this->db->order_by('m.cree_le', 'DESC');
        $this->json_success($this->db->get('messages m')->result_array());
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['sujet']) || empty($data['corps'])) {
            $this->json_error('Sujet et message obligatoires'); return;
        }
        $user = $this->Model->readOne('utilisateurs', ['uuid' => $this->session->userdata('uuid')]);
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'id_expediteur' => $user ? $user['id_utilisateur'] : 0,
            'id_destinataire' => $data['id_destinataire'] ?? 0,
            'sujet' => $data['sujet'],
            'corps' => $data['corps'],
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ];
        $id = $this->Model->createLastId('messages', $insert);
        if ($id) $this->json_success(['id_message' => $id], 'Message envoyé');
        else $this->json_error('Erreur');
    }
}
