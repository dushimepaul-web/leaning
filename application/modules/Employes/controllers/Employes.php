<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employes extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion du personnel';
        $data['departements'] = $this->Model->read('departements', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('e.deleted_at', null);
        $this->db->select('e.*, d.libelle as departement');
        $this->db->from('employes e');
        $this->db->join('departements d', 'e.id_departement = d.id_departement', 'left');
        $this->db->order_by('e.id_employe', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_get($id) {
        $e = $this->Model->readOne('employes', ['uuid' => $id]);
        if (!$e) { $this->json_error('Employé non trouvé', 404); return; }
        $this->json_success($e);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['nom_complet'])) { $this->json_error('Nom complet obligatoire'); return; }
        $this->load->helper('uuid');
        $insert = array_merge($data, [
            'uuid' => generate_uuid(),
            'matricule' => !empty($data['matricule']) ? $data['matricule'] : 'EMP-'.strtoupper(uniqid()),
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        $id = $this->Model->createLastId('employes', $insert);
        if ($id) $this->json_success(['id_employe' => $id], 'Employé créé');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $data['modifie_le'] = date('Y-m-d H:i:s');
        if ($this->Model->update('employes', ['uuid' => $id], $data))
            $this->json_success(null, 'Employé mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('employes', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s'), 'modifie_le' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Employé supprimé');
        else $this->json_error('Erreur');
    }
}
