<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Creneaux extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Créneaux horaires';
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->order_by('ordre');
        $this->json_success($this->db->get('creneaux')->result_array());
    }

    public function api_get($id) {
        $c = $this->Model->readOne('creneaux', ['uuid' => $id]);
        if (!$c) { $this->json_error('Créneau non trouvé', 404); return; }
        $this->json_success($c);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle']) || empty($data['heure_debut']) || empty($data['heure_fin'])) {
            $this->json_error('Libellé, heure début et heure fin obligatoires'); return;
        }
        $id = $this->Model->createLastId('creneaux', $data);
        if ($id) $this->json_success(['id_creneau' => $id], 'Créneau créé');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if ($this->Model->update('creneaux', ['uuid' => $id], $data))
            $this->json_success(null, 'Créneau mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('creneaux', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Créneau supprimé');
        else $this->json_error('Erreur');
    }
}
