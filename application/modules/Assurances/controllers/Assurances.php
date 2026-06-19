<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assurances extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Assurances';
        $data['etudiants'] = $this->Model->read('etudiants', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('a.deleted_at', null);
        $this->db->select('a.*, e.nom, e.prenom, e.matricule');
        $this->db->from('assurances a');
        $this->db->join('etudiants e', 'a.id_etudiant = e.id_etudiant', 'left');
        $this->db->order_by('a.id_assurance', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_get($id) {
        $a = $this->Model->readOne('assurances', ['uuid' => $id]);
        if (!$a) { $this->json_error('Assurance non trouvée', 404); return; }
        $this->json_success($a);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant']) || empty($data['police'])) {
            $this->json_error('Étudiant et police obligatoires'); return;
        }
        $id = $this->Model->createLastId('assurances', $data);
        if ($id) $this->json_success(['id_assurance' => $id], 'Assurance créée');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if ($this->Model->update('assurances', ['uuid' => $id], $data))
            $this->json_success(null, 'Assurance mise à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('assurances', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Assurance supprimée');
        else $this->json_error('Erreur');
    }
}
