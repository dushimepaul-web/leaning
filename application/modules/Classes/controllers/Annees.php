<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Annees extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des années scolaires';
        $this->load->view('annees', $data);
    }

    public function api_list() {
        $this->db->order_by('id_annee', 'DESC');
        $this->json_success($this->db->get('annees_scolaires')->result_array());
    }

    public function api_get($id) {
        $a = $this->Model->readOne('annees_scolaires', ['uuid' => $id]);
        if (!$a) { $this->json_error('Année non trouvée', 404); return; }
        $this->json_success($a);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle'])) { $this->json_error('Libellé obligatoire'); return; }
        $id = $this->Model->createLastId('annees_scolaires', $data);
        if ($id) $this->json_success(['id_annee' => $id], 'Année scolaire créée');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (!$this->Model->readOne('annees_scolaires', ['uuid' => $id])) {
            $this->json_error('Année non trouvée', 404); return;
        }
        if ($this->Model->update('annees_scolaires', ['uuid' => $id], $data))
            $this->json_success(null, 'Année scolaire mise à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('annees_scolaires', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Année scolaire supprimée');
        else $this->json_error('Erreur');
    }

    public function api_activate($id) {
        if ($this->Model->update('annees_scolaires', ['uuid' => $id], ['deleted_at' => null]))
            $this->json_success(null, 'Année activée');
        else $this->json_error('Erreur');
    }

    public function api_deactivate($id) {
        if ($this->Model->update('annees_scolaires', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Année désactivée');
        else $this->json_error('Erreur');
    }

    public function api_set_active($id) {
        $this->db->update('annees_scolaires', ['est_en_cours' => 0]);
        $this->db->where('uuid', $id);
        if ($this->db->update('annees_scolaires', ['est_en_cours' => 1]))
            $this->json_success(null, 'Année définie en cours');
        else $this->json_error('Erreur');
    }
}
