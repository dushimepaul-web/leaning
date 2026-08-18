<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Classes extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des classes';
        $data['sections'] = $this->Model->read('sections', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('c.deleted_at', null);
        $this->db->select('c.*, s.libelle as section_libelle');
        $this->db->from('classes c');
        $this->db->join('sections s', 'c.id_section = s.id_section', 'left');
        $this->db->order_by('c.id_classe', 'DESC');
        $query = $this->db->get();
        $this->json_success($query->result_array());
    }

    public function api_get($id) {
        $c = $this->Model->readOne('classes', ['uuid' => $id]);
        if (!$c) { $this->json_error('Classe non trouvée', 404); return; }
        $this->json_success($c);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle'])) { $this->json_error('Libellé obligatoire'); return; }
        $id = $this->Model->createLastId('classes', $data);
        if ($id) $this->json_success(null, 'Classe créée');
        else $this->json_error('Erreur de création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if ($this->Model->update('classes', ['uuid' => $id], $data))
            $this->json_success(null, 'Classe mise à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('classes', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Classe supprimée');
        else $this->json_error('Erreur de suppression');
    }
}
