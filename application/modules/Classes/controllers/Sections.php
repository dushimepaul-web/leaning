<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sections extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des Sections';
        $this->load->view('sections', $data);
    }

    public function api_list() {
        $this->db->order_by('libelle');
        $this->json_success($this->db->get('sections')->result_array());
    }

    public function api_get($id) {
        $s = $this->Model->readOne('sections', ['uuid' => $id]);
        if (!$s) { $this->json_error('Section non trouvée', 404); return; }
        $this->json_success($s);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['code']) || empty($data['libelle'])) {
            $this->json_error('Code et libellé obligatoires'); return;
        }
        $allowed = ['code', 'libelle'];
        $insert = array_intersect_key($data, array_flip($allowed));
        $id = $this->Model->createLastId('sections', $insert);
        if ($id) $this->json_success(null, 'Section créée');
        else $this->json_error('Erreur lors de la création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (!$this->Model->readOne('sections', ['uuid' => $id])) {
            $this->json_error('Section non trouvée', 404); return;
        }
        $allowed = ['code', 'libelle'];
        $update = array_intersect_key($data, array_flip($allowed));
        if ($this->Model->update('sections', ['uuid' => $id], $update))
            $this->json_success(null, 'Section mise à jour');
        else $this->json_error('Erreur lors de la mise à jour');
    }

    public function api_delete($id) {
        if (!$this->Model->readOne('sections', ['uuid' => $id])) {
            $this->json_error('Section non trouvée', 404); return;
        }
        if ($this->Model->delete('sections', ['uuid' => $id]))
            $this->json_success(null, 'Section supprimée définitivement');
        else $this->json_error('Erreur lors de la suppression');
    }

    public function api_activate($id) {
        if ($this->Model->update('sections', ['uuid' => $id], ['actif' => 1]))
            $this->json_success(null, 'Section activée');
        else $this->json_error('Erreur');
    }

    public function api_deactivate($id) {
        if ($this->Model->update('sections', ['uuid' => $id], ['actif' => 0]))
            $this->json_success(null, 'Section désactivée');
        else $this->json_error('Erreur');
    }

}
