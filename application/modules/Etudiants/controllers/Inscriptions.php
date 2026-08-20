<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inscriptions extends MY_Controller {
    private function _valider_relations(&$data) {
        if (!empty($data['id_etudiant'])) {
            $et = $this->Model->readOne('etudiants', ['id_etudiant' => $data['id_etudiant'], 'deleted_at' => null]);
            if (!$et) { $this->json_error('Étudiant introuvable'); return false; }
        }
        if (!empty($data['id_classe'])) {
            $section_finale = null;
            $err = $this->Model->valider_section_classe($data['id_classe'], $data['id_section'] ?? null, $section_finale);
            if ($err) { $this->json_error($err); return false; }
            if ($section_finale !== null) $data['id_section'] = $section_finale;
        }
        if (!empty($data['id_annee'])) {
            $an = $this->Model->readOne('annees_scolaires', ['id_annee' => $data['id_annee']]);
            if (!$an) { $this->json_error('Année scolaire introuvable'); return false; }
        }
        return true;
    }

    private function _exists($id_etudiant, $id_annee, $exclude_id = null) {
        $this->db->where('id_etudiant', $id_etudiant);
        $this->db->where('id_annee', $id_annee);
        $this->db->where('deleted_at', null);
        if ($exclude_id !== null) $this->db->where('uuid !=', $exclude_id);
        $q = $this->db->get('inscriptions');
        $r = $q !== false ? $q->row_array() : null;
        return (bool) $r;
    }
    public function __construct() { parent::__construct(); }

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
        $this->db->select("i.*, e.fullname AS nom, '' AS prenom, e.matricule, c.libelle as classe, s.libelle as section, a.libelle as annee");
        $this->db->from('inscriptions i');
        $this->db->join('etudiants e', 'i.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('classes c', 'i.id_classe = c.id_classe', 'left');
        $this->db->join('sections s', 'i.id_section = s.id_section', 'left');
        $this->db->join('annees_scolaires a', 'i.id_annee = a.id_annee', 'left');
        $this->db->order_by('i.id_inscription', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
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
        if (!$this->_valider_relations($data)) return;
        if ($this->_exists($data['id_etudiant'], $data['id_annee'])) {
            $this->json_error('Cet étudiant est déjà inscrit pour cette année scolaire'); return;
        }
        $data['date_inscription'] = $data['date_inscription'] ?? date('Y-m-d');
        $allowed = ['id_etudiant', 'id_classe', 'id_section', 'id_annee', 'date_inscription'];
        $clean = array_intersect_key($data, array_flip($allowed));
        $id = $this->Model->createLastId('inscriptions', $clean);
        if ($id) {
            $this->_ensure_conduite_points($data['id_etudiant'], $data['id_annee']);
            $this->Model->recalculer_numero_ordre($this->id_annee_active);
            $this->json_success(['id_inscription' => $id], 'Inscription créée');
        } else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $insc = $this->Model->readOne('inscriptions', ['uuid' => $id, 'deleted_at' => null]);
        if (!$insc) { $this->json_error('Inscription non trouvée', 404); return; }
        $id_annee = $data['id_annee'] ?? $insc['id_annee'];
        $id_etudiant = $data['id_etudiant'] ?? $insc['id_etudiant'];
        if (!$this->_valider_relations($data)) return;
        if ($this->_exists($id_etudiant, $id_annee, $id)) {
            $this->json_error('Cet étudiant est déjà inscrit pour cette année scolaire'); return;
        }
        $allowed = ['id_etudiant', 'id_classe', 'id_section', 'id_annee', 'date_inscription'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if ($this->Model->update('inscriptions', ['uuid' => $id], $update)) {
            $this->_ensure_conduite_points($id_etudiant, $id_annee);
            $this->Model->recalculer_numero_ordre($this->id_annee_active);
            $this->json_success(null, 'Inscription mise à jour');
        } else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        $insc = $this->Model->readOne('inscriptions', ['uuid' => $id]);
        if (!$insc) { $this->json_error('Inscription non trouvée', 404); return; }
        if ($this->Model->update('inscriptions', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')])) {
            $this->_remove_conduite_points($insc['id_etudiant'], $insc['id_annee']);
            $this->Model->recalculer_numero_ordre($this->id_annee_active);
            $this->json_success(null, 'Inscription supprimée');
        } else $this->json_error('Erreur');
    }
}
