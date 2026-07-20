<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Echeance extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des échéances';
        $data['etudiants'] = $this->Model->read('etudiants', ['deleted_at' => null]);
        $data['frais'] = $this->Model->read('frais', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('e.deleted_at', null);
        $this->db->select('e.*, et.nom, et.prenom, et.matricule, tf.libelle as type_frais');
        $this->db->from('echeances e');
        $this->db->join('etudiants et', 'e.id_etudiant = et.id_etudiant', 'left');
        $this->db->join('frais f', 'e.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->order_by('e.date_echeance', 'ASC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_get($id) {
        $this->db->where('e.uuid', $id);
        $this->db->select('e.*, et.nom, et.prenom, et.matricule, tf.libelle as type_frais');
        $this->db->from('echeances e');
        $this->db->join('etudiants et', 'e.id_etudiant = et.id_etudiant', 'left');
        $this->db->join('frais f', 'e.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $q = $this->db->get();
        $d = $q !== false ? $q->row_array() : null;
        if (!$d) { $this->json_error('Échéance non trouvée', 404); return; }
        $this->json_success($d);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant']) || empty($data['montant']) || empty($data['date_echeance'])) {
            $this->json_error('Étudiant, montant et date d\'échéance obligatoires'); return;
        }
        $id = $this->Model->createLastId('echeances', $data);
        if ($id) $this->json_success(['id_echeance' => $id], 'Échéance créée');
        else $this->json_error('Erreur de création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if ($this->Model->update('echeances', ['uuid' => $id], $data))
            $this->json_success(null, 'Échéance mise à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('echeances', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Échéance supprimée');
        else $this->json_error('Erreur de suppression');
    }
}
