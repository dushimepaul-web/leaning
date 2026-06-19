<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paie_rubrique extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Rubriques de paie';
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->json_success($this->Model->read('paie_rubriques', ['deleted_at' => null]));
    }

    public function api_get($id) {
        $d = $this->Model->readOne('paie_rubriques', ['uuid' => $id]);
        if (!$d) { $this->json_error('Rubrique non trouvée', 404); return; }
        $this->json_success($d);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['code']) || empty($data['libelle'])) {
            $this->json_error('Code et libellé obligatoires'); return;
        }
        $id = $this->Model->createLastId('paie_rubriques', $data);
        if ($id) $this->json_success(['id_rubrique' => $id], 'Rubrique créée');
        else $this->json_error('Erreur de création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if ($this->Model->update('paie_rubriques', ['uuid' => $id], $data))
            $this->json_success(null, 'Rubrique mise à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('paie_rubriques', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Rubrique supprimée');
        else $this->json_error('Erreur de suppression');
    }
}
