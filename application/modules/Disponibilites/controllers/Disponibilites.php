<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Disponibilites extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Disponibilités enseignants';
        $data['enseignants'] = $this->Model->read('enseignants', ['deleted_at' => null]);
        $data['creneaux'] = $this->Model->read('creneaux', [], 'ordre');
        $data['jours'] = $this->Model->read('jours_semaine', [], 'ordre');
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('d.deleted_at', null);
        $this->db->select('d.*, CONCAT(e.nom, " ", e.prenom) as enseignant, c.libelle as creneau, j.libelle as jour');
        $this->db->from('disponibilites_enseignants d');
        $this->db->join('enseignants e', 'd.id_enseignant = e.id_enseignant', 'left');
        $this->db->join('creneaux c', 'd.id_creneau = c.id_creneau', 'left');
        $this->db->join('jours_semaine j', 'd.id_jour = j.id_jour', 'left');
        $this->db->order_by('d.id_disponibilite', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_get($id) {
        $d = $this->Model->readOne('disponibilites_enseignants', ['uuid' => $id]);
        if (!$d) { $this->json_error('Disponibilité non trouvée', 404); return; }
        $this->json_success($d);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_enseignant']) || empty($data['id_creneau']) || empty($data['id_jour']) ||
            !is_numeric($data['id_enseignant']) || !is_numeric($data['id_creneau']) || !is_numeric($data['id_jour'])) {
            $this->json_error('Enseignant, créneau et jour obligatoires'); return;
        }
        $data['type'] = !empty($data['type']) ? $data['type'] : 'disponible';
        $existing = $this->Model->readOne('disponibilites_enseignants', [
            'id_enseignant' => $data['id_enseignant'],
            'id_creneau' => $data['id_creneau'],
            'id_jour' => $data['id_jour'],
            'deleted_at' => null
        ]);
        if ($existing) {
            $this->json_error('Cette disponibilité existe déjà pour cet enseignant');
            return;
        }
        $id = $this->Model->createLastId('disponibilites_enseignants', $data);
        if ($id) $this->json_success(['id_disponibilite' => $id], 'Disponibilité créée');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if ($this->Model->update('disponibilites_enseignants', ['uuid' => $id], $data))
            $this->json_success(null, 'Disponibilité mise à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('disponibilites_enseignants', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Disponibilité supprimée');
        else $this->json_error('Erreur');
    }
}
