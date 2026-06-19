<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enseignements extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des enseignements';
        $data['enseignants'] = $this->Model->read('enseignants', ['deleted_at' => null], 'nom', 'ASC');
        $data['matieres'] = $this->Model->read('matieres', ['deleted_at' => null], 'libelle', 'ASC');
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null], 'libelle', 'ASC');
        $this->load->view('enseignements', $data);
    }

    public function api_list() {
        $this->db->where('e.deleted_at', null);
        $this->db->select('e.*, ens.nom as enseignant_nom, ens.prenom as enseignant_prenom, m.libelle as matiere_libelle, c.libelle as classe_libelle');
        $this->db->from('enseignements e');
        $this->db->join('enseignants ens', 'e.id_enseignant = ens.id_enseignant', 'left');
        $this->db->join('matieres m', 'e.id_matiere = m.id_matiere', 'left');
        $this->db->join('classes c', 'e.id_classe = c.id_classe', 'left');
        $this->db->order_by('e.id_enseignement', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_get($id) {
        $e = $this->Model->readOne('enseignements', ['uuid' => $id]);
        if (!$e) { $this->json_error('Enseignement non trouvé', 404); return; }
        $this->json_success($e);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_enseignant']) || empty($data['id_matiere']) || empty($data['id_classe'])) {
            $this->json_error('Enseignant, matière et classe obligatoires'); return;
        }
        $exists = $this->Model->readOne('enseignements', [
            'id_enseignant' => $data['id_enseignant'],
            'id_matiere' => $data['id_matiere'],
            'id_classe' => $data['id_classe'],
            'deleted_at' => null
        ]);
        if ($exists) {
            $this->json_error('Cet enseignement existe déjà pour cet enseignant, cette matière et cette classe');
            return;
        }
        $id = $this->Model->createLastId('enseignements', $data);
        if ($id) {
            $this->json_success(['id_enseignement' => $id], 'Enseignement créé');
        } else {
            $err = $this->db->error();
            $this->json_error($err['message'] ?: 'Erreur lors de la création');
        }
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (!$this->Model->readOne('enseignements', ['uuid' => $id])) {
            $this->json_error('Enseignement non trouvé', 404); return;
        }
        if ($this->Model->update('enseignements', ['uuid' => $id], $data))
            $this->json_success(null, 'Enseignement mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('enseignements', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Enseignement supprimé');
        else $this->json_error('Erreur');
    }
}
