<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Echeance extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des échéances';
        $this->db->select('e.*, i.id_classe, i.id_section');
        $this->db->from('etudiants e');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL AND i.id_annee = ' . (int)$this->id_annee_active, 'left');
        $this->db->where('e.deleted_at', null);
        $q_e = $this->db->get();
        $data['etudiants'] = $q_e !== false ? $q_e->result_array() : array();
        $data['frais'] = $this->Model->read('frais', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('e.deleted_at', null);
        $this->db->select("e.*, et.fullname AS nom, '' AS prenom, et.matricule, tf.libelle as type_frais");
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
        $this->db->select("e.*, et.fullname AS nom, '' AS prenom, et.matricule, tf.libelle as type_frais");
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
        if (empty($data['id_etudiant']) || !isset($data['montant']) || $data['montant'] === '' || empty($data['date_echeance'])) {
            $this->json_error('Étudiant, montant et date d\'échéance obligatoires'); return;
        }
        $montant = floatval($data['montant']);
        if ($montant <= 0) { $this->json_error('Montant invalide'); return; }
        if (strtotime($data['date_echeance']) === false) { $this->json_error('Date d\'échéance invalide'); return; }
        $etudiant = $this->Model->readOne('etudiants', ['id_etudiant' => $data['id_etudiant'], 'deleted_at' => null]);
        if (!$etudiant) { $this->json_error('Étudiant introuvable'); return; }
        if (!empty($data['id_frais'])) {
            $frais = $this->Model->readOne('frais', ['id_frais' => $data['id_frais'], 'deleted_at' => null]);
            if (!$frais) { $this->json_error('Frais introuvable'); return; }
        }
        $allowed = ['id_etudiant', 'id_frais', 'montant', 'date_echeance', 'statut'];
        $clean = array_intersect_key($data, array_flip($allowed));
        $statuts = ['impaye', 'partiel', 'paye', 'annule'];
        $clean['statut'] = in_array($clean['statut'] ?? '', $statuts) ? $clean['statut'] : 'impaye';
        $clean['montant'] = $montant;
        $id = $this->Model->createLastId('echeances', $clean);
        if ($id) $this->json_success(['id_echeance' => $id], 'Échéance créée');
        else $this->json_error('Erreur de création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (isset($data['id_etudiant']) && !empty($data['id_etudiant'])) {
            $etudiant = $this->Model->readOne('etudiants', ['id_etudiant' => $data['id_etudiant'], 'deleted_at' => null]);
            if (!$etudiant) { $this->json_error('Étudiant introuvable'); return; }
        }
        if (isset($data['id_frais']) && !empty($data['id_frais'])) {
            $frais = $this->Model->readOne('frais', ['id_frais' => $data['id_frais'], 'deleted_at' => null]);
            if (!$frais) { $this->json_error('Frais introuvable'); return; }
        }
        if (isset($data['montant']) && $data['montant'] !== '') {
            $montant = floatval($data['montant']);
            if ($montant <= 0) { $this->json_error('Montant invalide'); return; }
            $data['montant'] = $montant;
        }
        if (!empty($data['date_echeance']) && strtotime($data['date_echeance']) === false) {
            $this->json_error('Date d\'échéance invalide'); return;
        }
        $allowed = ['id_etudiant', 'id_frais', 'montant', 'date_echeance', 'statut'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        $statuts = ['impaye', 'partiel', 'paye', 'annule'];
        if (isset($update['statut']) && !in_array($update['statut'], $statuts)) {
            $this->json_error('Statut invalide'); return;
        }
        if ($this->Model->update('echeances', ['uuid' => $id], $update))
            $this->json_success(null, 'Échéance mise à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('echeances', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Échéance supprimée');
        else $this->json_error('Erreur de suppression');
    }
}
