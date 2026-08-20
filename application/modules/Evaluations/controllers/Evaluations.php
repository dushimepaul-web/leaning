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
        $data['periodes'] = $this->Model->read('periodes', ['id_annee' => $this->id_annee_active, 'deleted_at' => null]);
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

    public function api_get($id) {
        $this->db->where('ev.uuid', $id);
        $this->db->where('ev.deleted_at', null);
        $this->db->select('ev.*, m.libelle as matiere, c.libelle as classe, p.libelle as periode');
        $this->db->from('evaluations ev');
        $this->db->join('matieres m', 'ev.id_matiere = m.id_matiere', 'left');
        $this->db->join('classes c', 'ev.id_classe = c.id_classe', 'left');
        $this->db->join('periodes p', 'ev.id_periode = p.id_periode', 'left');
        $q = $this->db->get();
        $d = $q !== false ? $q->row_array() : null;
        if (!$d) { $this->json_error('Évaluation introuvable', 404); return; }
        $this->json_success($d);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_classe']) || empty($data['id_matiere']) || empty($data['id_periode']) || empty($data['libelle'])) {
            $this->json_error('Classe, matière, période et libellé obligatoires'); return;
        }
        $classe = $this->Model->readOne('classes', ['id_classe' => $data['id_classe'], 'deleted_at' => null]);
        if (!$classe) { $this->json_error('Classe introuvable'); return; }
        $matiere = $this->Model->readOne('matieres', ['id_matiere' => $data['id_matiere'], 'deleted_at' => null]);
        if (!$matiere) { $this->json_error('Matière introuvable'); return; }
        $periode = $this->Model->readOne('periodes', ['id_periode' => $data['id_periode'], 'deleted_at' => null]);
        if (!$periode) { $this->json_error('Période introuvable'); return; }
        $annee = $this->Model->readOne('annees_scolaires', ['id_annee' => $periode['id_annee'], 'deleted_at' => null]);
        if (!$annee) { $this->json_error('Année de la période introuvable'); return; }
        $this->load->helper('uuid');
        $types_valides = ['interrogation','devoir','controle','composition','examen','tp','projet','participation','autre'];
        $sur = (int)($data['note_max'] ?? 20);
        if ($sur <= 0 || $sur > 1000) { $this->json_error('Note maximale invalide'); return; }
        $coefficient = (float)($data['coefficient'] ?? 1);
        if ($coefficient <= 0 || $coefficient > 100) { $this->json_error('Coefficient invalide'); return; }
        $insert = [
            'uuid' => generate_uuid(),
            'libelle' => $data['libelle'],
            'id_classe' => $data['id_classe'],
            'id_matiere' => $data['id_matiere'],
            'id_periode' => $data['id_periode'],
            'id_annee' => $periode['id_annee'],
            'date_eval' => $data['date_evaluation'] ?? date('Y-m-d'),
            'type' => in_array($data['type'] ?? '', $types_valides) ? $data['type'] : 'devoir',
            'sur' => $sur,
            'coefficient' => $coefficient,
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
        $existing = $this->Model->readOne('evaluations', ['uuid' => $id, 'deleted_at' => null]);
        if (!$existing) { $this->json_error('Évaluation introuvable', 404); return; }
        $allowed = ['libelle','id_classe','id_matiere','id_periode','date_eval','sur','coefficient','type'];
        $update = ['modifie_le' => date('Y-m-d H:i:s')];
        if (isset($data['id_classe']) && $data['id_classe'] !== '') {
            $classe = $this->Model->readOne('classes', ['id_classe' => $data['id_classe'], 'deleted_at' => null]);
            if (!$classe) { $this->json_error('Classe introuvable'); return; }
        }
        if (isset($data['id_matiere']) && $data['id_matiere'] !== '') {
            $matiere = $this->Model->readOne('matieres', ['id_matiere' => $data['id_matiere'], 'deleted_at' => null]);
            if (!$matiere) { $this->json_error('Matière introuvable'); return; }
        }
        if (isset($data['id_periode']) && $data['id_periode'] !== '') {
            $periode = $this->Model->readOne('periodes', ['id_periode' => $data['id_periode'], 'deleted_at' => null]);
            if (!$periode) { $this->json_error('Période introuvable'); return; }
        }
        if (!empty($data['note_max'])) {
            $sur = (int)$data['note_max'];
            if ($sur <= 0 || $sur > 1000) { $this->json_error('Note maximale invalide'); return; }
            $update['sur'] = $sur;
        }
        if (isset($data['coefficient']) && $data['coefficient'] !== '') {
            $coefficient = (float)$data['coefficient'];
            if ($coefficient <= 0 || $coefficient > 100) { $this->json_error('Coefficient invalide'); return; }
            $update['coefficient'] = $coefficient;
        }
        if (!empty($data['date_evaluation'])) $update['date_eval'] = $data['date_evaluation'];
        foreach ($allowed as $col) {
            if (isset($data[$col]) && $data[$col] !== '') $update[$col] = $data[$col];
        }
        if (isset($update['id_periode'])) {
            $periode = $this->Model->readOne('periodes', ['id_periode' => $update['id_periode']]);
            if ($periode) $update['id_annee'] = $periode['id_annee'];
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