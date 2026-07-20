<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Evaluations extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des évaluations';
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['matieres'] = $this->Model->read('matieres', ['deleted_at' => null]);
        $data['enseignants'] = $this->Model->read('enseignants', ['deleted_at' => null]);
        $data['periodes'] = $this->Model->read('periodes', ['deleted_at' => null, 'id_annee' => $this->id_annee_active]);
        $this->load->view('evaluations', $data);
    }

    public function api_list() {
        $this->db->where('ev.deleted_at', null);
        $this->db->select('ev.*, c.libelle as classe, m.libelle as matiere, p.libelle as periode, CONCAT(e.nom," ",e.prenom) as enseignant');
        $this->db->from('evaluations ev');
        $this->db->join('classes c', 'ev.id_classe = c.id_classe', 'left');
        $this->db->join('matieres m', 'ev.id_matiere = m.id_matiere', 'left');
        $this->db->join('periodes p', 'ev.id_periode = p.id_periode', 'left');
        $this->db->join('enseignements en', 'ev.id_classe = en.id_classe AND ev.id_matiere = en.id_matiere AND en.deleted_at IS NULL', 'left');
        $this->db->join('enseignants e', 'en.id_enseignant = e.id_enseignant', 'left');
        $this->db->order_by('ev.date_eval', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle'])) { $this->json_error('Libellé obligatoire'); return; }
        $data['id_annee'] = $this->id_annee_active;
        $data['id_periode'] = !empty($data['id_periode']) ? $data['id_periode'] : $this->id_periode_active;
        $data['date_eval'] = !empty($data['date_eval']) ? $data['date_eval'] : date('Y-m-d');
        $data['type'] = !empty($data['type']) ? $data['type'] : 'devoir';
        $data['coefficient'] = !empty($data['coefficient']) ? $data['coefficient'] : 1.0;
        $data['sur'] = !empty($data['sur']) ? $data['sur'] : 20.0;
        $id = $this->Model->createLastId('evaluations', $data);
        if ($id) $this->json_success(['id_evaluation' => $id], 'Évaluation créée');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $allowed = ['libelle', 'type', 'coefficient', 'sur', 'date_eval', 'id_periode', 'id_classe', 'id_matiere'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée'); return; }
        if ($this->Model->update('evaluations', ['uuid' => $id], $update))
            $this->json_success(null, 'Évaluation mise à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('evaluations', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Évaluation supprimée');
        else $this->json_error('Erreur');
    }
}
