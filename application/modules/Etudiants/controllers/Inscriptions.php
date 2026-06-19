<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inscriptions extends MY_Controller {
    private function _exists($id_etudiant, $id_annee, $exclude_id = null) {
        $this->db->where('id_etudiant', $id_etudiant);
        $this->db->where('id_annee', $id_annee);
        $this->db->where('deleted_at', null);
        if ($exclude_id !== null) $this->db->where('uuid !=', $exclude_id);
        return (bool) $this->db->get('inscriptions')->row_array();
    }
    public function __construct() { parent::__construct(); $this->not_logged_in(); }

    public function index() {
        $data['title'] = 'Inscriptions';
        $data['etudiants'] = $this->Model->read('etudiants', ['deleted_at' => null]);
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['sections'] = $this->Model->read('sections', ['deleted_at' => null]);
        $data['annees'] = $this->Model->read('annees_scolaires', [], 'libelle', 'ASC');
        $this->load->view('inscriptions', $data);
    }

    public function api_list() {
        $this->db->where('i.deleted_at', null);
        $this->db->select('i.*, e.nom, e.prenom, e.matricule, c.libelle as classe, s.libelle as section, a.libelle as annee');
        $this->db->from('inscriptions i');
        $this->db->join('etudiants e', 'i.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('classes c', 'i.id_classe = c.id_classe', 'left');
        $this->db->join('sections s', 'i.id_section = s.id_section', 'left');
        $this->db->join('annees_scolaires a', 'i.id_annee = a.id_annee', 'left');
        $this->db->order_by('i.id_inscription', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_get($id) {
        $insc = $this->Model->readOne('inscriptions', ['uuid' => $id]);
        if (!$insc) { $this->json_error('Inscription non trouvée', 404); return; }
        $this->json_success($insc);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant']) || empty($data['id_classe'])) {
            $this->json_error('Étudiant et classe obligatoires'); return;
        }
        $data['id_annee'] = $data['id_annee'] ?? $this->id_annee_active;
        if ($this->_exists($data['id_etudiant'], $data['id_annee'])) {
            $this->json_error('Cet étudiant est déjà inscrit pour cette année scolaire'); return;
        }
        $data['date_inscription'] = $data['date_inscription'] ?? date('Y-m-d');
        $id = $this->Model->createLastId('inscriptions', $data);
        if ($id) {
            $this->_recalculer_numero_ordre();
            $this->json_success(['id_inscription' => $id], 'Inscription créée');
        } else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $insc = $this->Model->readOne('inscriptions', ['uuid' => $id, 'deleted_at' => null]);
        if (!$insc) { $this->json_error('Inscription non trouvée', 404); return; }
        $id_annee = $data['id_annee'] ?? $insc['id_annee'];
        $id_etudiant = $data['id_etudiant'] ?? $insc['id_etudiant'];
        if ($this->_exists($id_etudiant, $id_annee, $id)) {
            $this->json_error('Cet étudiant est déjà inscrit pour cette année scolaire'); return;
        }
        if ($this->Model->update('inscriptions', ['uuid' => $id], $data)) {
            $this->_recalculer_numero_ordre();
            $this->json_success(null, 'Inscription mise à jour');
        } else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('inscriptions', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')])) {
            $this->_recalculer_numero_ordre();
            $this->json_success(null, 'Inscription supprimée');
        } else $this->json_error('Erreur');
    }

    private function _recalculer_numero_ordre() {
        $this->db->select('i.id_classe, e.id_etudiant, e.nom, e.postnom, e.prenom');
        $this->db->from('inscriptions i');
        $this->db->join('etudiants e', 'e.id_etudiant = i.id_etudiant');
        $this->db->where('i.deleted_at', null);
        $this->db->where('e.deleted_at', null);
        $this->db->where('i.id_annee', $this->id_annee_active);
        $this->db->order_by('i.id_classe ASC, e.nom ASC, e.postnom ASC, e.prenom ASC');
        $rows = $this->db->get()->result_array();
        $current_classe = null;
        $seq = 0;
        foreach ($rows as $r) {
            if ($r['id_classe'] !== $current_classe) {
                $current_classe = $r['id_classe'];
                $seq = 1;
            }
            $this->db->where('id_etudiant', $r['id_etudiant']);
            $this->db->update('etudiants', ['numero_ordre' => $seq]);
            $seq++;
        }
    }
}
