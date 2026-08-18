<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Evaluations extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->not_logged_in();
    }

    public function index() {
        $data['title'] = 'Évaluations';
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['matieres'] = $this->Model->read('matieres', ['deleted_at' => null]);
        $data['periodes'] = $this->Model->read('periodes', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('ev.deleted_at', null);
        $this->db->select('ev.*, m.libelle as matiere, c.libelle as classe, p.libelle as periode');
        $this->db->from('evaluations ev');
        $this->db->join('matieres m', 'ev.id_matiere = m.id_matiere', 'left');
        $this->db->join('classes c', 'ev.id_classe = c.id_classe', 'left');
        $this->db->join('periodes p', 'ev.id_periode = p.id_periode', 'left');
        $this->db->order_by('ev.date_eval', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_classe']) || empty($data['id_matiere']) || empty($data['id_periode']) || empty($data['libelle'])) {
            $this->json_error('Classe, matière, période et libellé obligatoires'); return;
        }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'libelle' => $data['libelle'],
            'id_classe' => $data['id_classe'],
            'id_matiere' => $data['id_matiere'],
            'id_periode' => $data['id_periode'],
            'id_annee' => $this->id_annee_active,
            'date_eval' => $data['date_evaluation'] ?? date('Y-m-d'),
            'sur' => $data['note_max'] ?? 20,
            'coefficient' => $data['coefficient'] ?? 1,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ];
        if ($this->db->insert('evaluations', $insert)) {
            $this->json_success(['id' => $this->db->insert_id()], 'Évaluation créée');
        } else {
            $this->json_error('Erreur de création');
        }
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $allowed = ['libelle','id_classe','id_matiere','id_periode','date_eval','sur','coefficient'];
        $update = ['modifie_le' => date('Y-m-d H:i:s')];
        if (!empty($data['date_evaluation'])) $update['date_eval'] = $data['date_evaluation'];
        if (!empty($data['note_max'])) $update['sur'] = $data['note_max'];
        foreach ($allowed as $col) {
            if (isset($data[$col])) $update[$col] = $data[$col];
        }
        $this->db->where('uuid', $id);
        if ($this->db->update('evaluations', $update))
            $this->json_success(null, 'Évaluation mise à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        $this->db->where('uuid', $id);
        if ($this->db->update('evaluations', ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Évaluation supprimée');
        else $this->json_error('Erreur');
    }
}