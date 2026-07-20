<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paiement_recu extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des reçus de paiement';
        $data['paiements'] = $this->Model->read('paiements', ['deleted_at' => null]);
        $data['recus'] = $this->Model->read('recus', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->select('pr.*, r.numero_recu, p.montant, p.date_paiement, e.nom, e.prenom, e.matricule');
        $this->db->from('paiements_recus pr');
        $this->db->join('recus r', 'pr.id_recu = r.id_recu', 'left');
        $this->db->join('paiements p', 'pr.id_paiement = p.id_paiement', 'left');
        $this->db->join('etudiants e', 'p.id_etudiant = e.id_etudiant', 'left');
        $this->db->order_by('pr.id_paiement_recu', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_get($id) {
        $this->db->where('pr.uuid', $id);
        $this->db->select('pr.*, r.numero_recu, p.montant, p.date_paiement, e.nom, e.prenom, e.matricule');
        $this->db->from('paiements_recus pr');
        $this->db->join('recus r', 'pr.id_recu = r.id_recu', 'left');
        $this->db->join('paiements p', 'pr.id_paiement = p.id_paiement', 'left');
        $this->db->join('etudiants e', 'p.id_etudiant = e.id_etudiant', 'left');
        $q = $this->db->get();
        $d = $q !== false ? $q->row_array() : null;
        if (!$d) { $this->json_error('Reçu de paiement non trouvé', 404); return; }
        $this->json_success($d);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_recu']) || empty($data['id_paiement'])) {
            $this->json_error('Reçu et paiement obligatoires'); return;
        }
        $id = $this->Model->createLastId('paiements_recus', $data);
        if ($id) $this->json_success(['id_paiement_recu' => $id], 'Association créée');
        else $this->json_error('Erreur de création');
    }

    public function api_delete($id) {
        $d = $this->Model->readOne('paiements_recus', ['uuid' => $id]);
        if (!$d) { $this->json_error('Association non trouvée', 404); return; }
        if ($this->Model->delete('paiements_recus', ['uuid' => $id]))
            $this->json_success(null, 'Association supprimée');
        else $this->json_error('Erreur de suppression');
    }
}
