<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notes extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des notes';
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['matieres'] = $this->Model->read('matieres', ['deleted_at' => null]);
        $data['periodes'] = $this->Model->read('periodes', ['deleted_at' => null]);
        $data['annees'] = $this->Model->read('annees_scolaires');
        $data['id_periode_active'] = $this->id_periode_active;
        $data['id_annee_active'] = $this->id_annee_active;
        $this->load->view('index', $data);
    }

    public function api_get($id) {
        $this->db->where('n.uuid', $id);
        $this->db->where('n.deleted_at', null);
        $this->db->select("n.*, e.fullname AS nom, '' AS prenom, m.libelle as matiere, ev.libelle as evaluation, ev.ponderee_sur");
        $this->db->from('notes n');
        $this->db->join('etudiants e', 'n.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('evaluations ev', 'n.id_evaluation = ev.id_evaluation', 'left');
        $this->db->join('matieres m', 'ev.id_matiere = m.id_matiere', 'left');
        $q = $this->db->get();
        $d = $q !== false ? $q->row_array() : null;
        if (!$d) { $this->json_error('Note introuvable', 404); return; }
        $this->json_success($d);
    }

    public function api_list() {
        $this->db->where('n.deleted_at', null);
        $this->db->select("n.*, e.fullname AS nom, '' AS prenom, m.libelle as matiere, ev.libelle as evaluation");
        $this->db->from('notes n');
        $this->db->join('etudiants e', 'n.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('evaluations ev', 'n.id_evaluation = ev.id_evaluation', 'left');
        $this->db->join('matieres m', 'ev.id_matiere = m.id_matiere', 'left');

        $classe = $this->input->get('classe');
        $evaluation = $this->input->get('evaluation');
        if ($classe) {
            $this->db->where('ev.id_classe', $classe);
        }
        if ($evaluation) {
            $this->db->where('n.id_evaluation', $evaluation);
        }

        $this->db->order_by('n.id_note', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant']) || !isset($data['note'])) {
            $this->json_error('Étudiant et note obligatoires'); return;
        }
        $etudiant = $this->Model->readOne('etudiants', ['id_etudiant' => $data['id_etudiant'], 'deleted_at' => null]);
        if (!$etudiant) { $this->json_error('Étudiant introuvable'); return; }
        if (!empty($data['id_evaluation'])) {
            $eval = $this->Model->readOne('evaluations', ['id_evaluation' => $data['id_evaluation'], 'deleted_at' => null]);
            if (!$eval) { $this->json_error('Évaluation introuvable'); return; }
            $max = floatval($eval['ponderee_sur'] ?: 20);
            if (floatval($data['note']) > $max) { $this->json_error('La note ne peut pas dépasser ' . $max); return; }
        }
        $allowed = ['id_etudiant', 'id_evaluation', 'note', 'appreciation'];
        $clean = array_intersect_key($data, array_flip($allowed));
        $id = $this->Model->createLastId('notes', $clean);
        if ($id) $this->json_success(['id_note' => $id], 'Note ajoutée');
        else $this->json_error('Erreur');
    }

    public function api_batch() {
        $data = $this->get_json_input();
        if (empty($data['notes']) || !is_array($data['notes'])) {
            $this->json_error('Aucune note fournie'); return;
        }
        $created = 0;
        $updated = 0;
        foreach ($data['notes'] as $note) {
            if (empty($note['id_etudiant']) || !isset($note['note']) || empty($note['id_evaluation'])) continue;
            $eval = $this->Model->readOne('evaluations', ['id_evaluation' => $note['id_evaluation'], 'deleted_at' => null]);
            if (!$eval) continue;
            $max = floatval($eval['ponderee_sur'] ?: 20);
            if (floatval($note['note']) > $max) continue;
            $existing = $this->Model->readOne('notes', [
                'id_etudiant' => $note['id_etudiant'],
                'id_evaluation' => $note['id_evaluation'],
                'deleted_at' => null
            ]);
            if ($existing) {
                $this->Model->update('notes', ['id_note' => $existing['id_note']], [
                    'note' => $note['note'],
                    'appreciation' => isset($note['appreciation']) ? $note['appreciation'] : null
                ]);
                $updated++;
            } else {
                $this->Model->create('notes', [
                    'id_etudiant' => $note['id_etudiant'],
                    'id_evaluation' => $note['id_evaluation'],
                    'note' => $note['note'],
                    'appreciation' => isset($note['appreciation']) ? $note['appreciation'] : null
                ]);
                $created++;
            }
        }
        $this->json_success(['created' => $created, 'updated' => $updated], "$created créées, $updated mises à jour");
    }

    public function api_students_by_classe($id_classe) {
        $this->db->where('i.id_classe', $id_classe);
        $this->db->where('i.id_annee', $this->id_annee_active);
        $this->db->where('i.deleted_at', null);
        $this->db->where('e.deleted_at', null);
        $this->db->select("e.id_etudiant, e.fullname AS nom, '' AS prenom, e.matricule, e.numero_ordre");
        $this->db->from('inscriptions i');
        $this->db->join('etudiants e', 'i.id_etudiant = e.id_etudiant');
        $this->db->order_by('e.fullname ASC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_evaluations_by_classe($id_classe) {
        $this->db->where('ev.id_classe', $id_classe);
        $this->db->where('ev.deleted_at', null);
        $this->db->select('ev.*, m.libelle as matiere_libelle');
        $this->db->from('evaluations ev');
        $this->db->join('matieres m', 'ev.id_matiere = m.id_matiere', 'left');
        $this->db->order_by('ev.date_eval', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_matieres_by_classe($id_classe) {
        $matieres = $this->db
            ->distinct()
            ->select('m.id_matiere, m.libelle, m.code')
            ->from('matieres_classes mc')
            ->join('matieres m', 'mc.id_matiere = m.id_matiere')
            ->where('mc.id_classe', $id_classe)
            ->where('mc.deleted_at', null)
            ->order_by('m.libelle')
            ->get()->result_array();
        $this->json_success($matieres);
    }

    public function api_grille_notes($id_classe, $id_matiere) {
        $id_periode = $this->input->get('periode');
        $id_annee = $this->input->get('annee');
        if ($id_annee) {
            $annee_existe = $this->Model->readOne('annees_scolaires', ['id_annee' => $id_annee, 'deleted_at' => null]);
            if (!$annee_existe) { $this->json_error('Année introuvable'); return; }
        } else {
            $id_annee = $this->id_annee_active;
        }
        if ($id_periode) {
            $periode = $this->Model->readOne('periodes', ['id_periode' => $id_periode, 'id_annee' => $id_annee, 'deleted_at' => null]);
            if (!$periode) { $this->json_error('Période introuvable pour cette année'); return; }
        }
        $this->db->where('i.id_classe', $id_classe);
        $this->db->where('i.id_annee', (int)$id_annee);
        $this->db->where('i.deleted_at', null);
        $this->db->where('e.deleted_at', null);
        $this->db->select("e.id_etudiant, e.fullname AS nom, '' AS prenom, e.matricule");
        $this->db->from('inscriptions i');
        $this->db->join('etudiants e', 'i.id_etudiant = e.id_etudiant');
        $this->db->order_by('e.fullname ASC');
        $q_el = $this->db->get();
        $eleves = $q_el !== false ? $q_el->result_array() : array();

        $this->db->where('ev.id_classe', $id_classe);
        $this->db->where('ev.id_matiere', $id_matiere);
        $this->db->where('ev.id_annee', (int)$id_annee);
        $this->db->where('ev.deleted_at', null);
        if ($id_periode) $this->db->where('ev.id_periode', $id_periode);
        $this->db->select('ev.id_evaluation, ev.id_matiere, ev.libelle, ev.ponderee_sur, ev.date_eval, ev.type');
        $this->db->from('evaluations ev');
        $this->db->order_by('ev.date_eval', 'ASC');
        $q_ev = $this->db->get();
        $evaluations = $q_ev !== false ? $q_ev->result_array() : array();

        $notes = [];
        if (!empty($eleves) && !empty($evaluations)) {
            $ids_eleves = array_column($eleves, 'id_etudiant');
            $ids_evals = array_column($evaluations, 'id_evaluation');
            $this->db->where_in('n.id_etudiant', $ids_eleves);
            $this->db->where_in('n.id_evaluation', $ids_evals);
            $this->db->where('n.deleted_at', null);
            $raw = $this->db->get('notes n')->result_array();
            foreach ($raw as $n) {
                $notes[$n['id_etudiant']][$n['id_evaluation']] = [
                    'note' => floatval($n['note']),
                    'appreciation' => $n['appreciation'] ?? '',
                    'uuid' => $n['uuid']
                ];
            }
        }

        $classe = $this->Model->readOne('classes', ['id_classe' => $id_classe]);
        $matiere = $this->Model->readOne('matieres', ['id_matiere' => $id_matiere]);

        $this->json_success([
            'classe' => $classe ? $classe['libelle'] : '',
            'matiere' => $matiere ? $matiere['libelle'] : '',
            'eleves' => $eleves,
            'evaluations' => $evaluations,
            'notes' => $notes
        ]);
    }

    public function api_classes_summary() {
        $this->db->select('c.id_classe, c.libelle as classe, s.libelle as section');
        $this->db->from('classes c');
        $this->db->join('sections s', 'c.id_section = s.id_section', 'left');
        $this->db->where('c.deleted_at', null);
        $this->db->order_by('c.libelle');
        $q_c = $this->db->get();
        $classes = $q_c !== false ? $q_c->result_array() : array();

        foreach ($classes as &$cl) {
            $cl['nb_etudiants'] = $this->db
                ->where('i.id_classe', $cl['id_classe'])
                ->where('i.id_annee', $this->id_annee_active)
                ->where('i.deleted_at', null)
                ->count_all_results('inscriptions i');

            $cl['nb_matieres'] = $this->db
                ->where('mc.id_classe', $cl['id_classe'])
                ->where('mc.deleted_at', null)
                ->count_all_results('matieres_classes mc');
        }
        $this->json_success($classes);
    }

    public function api_delete($id) {
        if ($this->Model->update('notes', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Note supprimée');
        else $this->json_error('Erreur');
    }
}
