<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Matieres extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des matières';
        $this->load->view('matieres', $data);
    }

    public function api_list() {
        $this->db->where('deleted_at', null);
        $this->db->order_by('libelle');
        $this->json_success($this->db->get('matieres')->result_array());
    }

    public function api_get($id) {
        $m = $this->Model->readOne('matieres', ['uuid' => $id]);
        if (!$m) { $this->json_error('Matière non trouvée', 404); return; }
        $this->json_success($m);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['code']) || empty($data['libelle'])) {
            $this->json_error('Code et libellé obligatoires'); return;
        }
        $id = $this->Model->createLastId('matieres', $data);
        if ($id) $this->json_success(null, 'Matière créée');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (!$this->Model->readOne('matieres', ['uuid' => $id])) {
            $this->json_error('Matière non trouvée', 404); return;
        }
        if ($this->Model->update('matieres', ['uuid' => $id], $data))
            $this->json_success(null, 'Matière mise à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('matieres', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Matière supprimée');
        else $this->json_error('Erreur');
    }
}
