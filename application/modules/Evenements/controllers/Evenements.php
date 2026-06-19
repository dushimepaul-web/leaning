<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Evenements extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Événements';
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('deleted_at', null);
        $this->db->order_by('date_debut', 'DESC');
        $this->json_success($this->db->get('evenements')->result_array());
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['titre']) || empty($data['date_debut'])) {
            $this->json_error('Titre et date début obligatoires'); return;
        }
        $id = $this->Model->createLastId('evenements', $data);
        if ($id) $this->json_success(['id_evenement' => $id], 'Événement créé');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if ($this->Model->update('evenements', ['uuid' => $id], $data))
            $this->json_success(null, 'Événement mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('evenements', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Événement supprimé');
        else $this->json_error('Erreur');
    }
}
