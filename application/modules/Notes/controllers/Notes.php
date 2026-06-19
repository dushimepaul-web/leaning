<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notes extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des notes';
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['matieres'] = $this->Model->read('matieres', ['deleted_at' => null]);
        $data['periodes'] = $this->Model->read('periodes', ['deleted_at' => null, 'id_annee' => $this->id_annee_active]);
        $data['annees'] = $this->Model->read('annees_scolaires');
        $data['id_periode_active'] = $this->id_periode_active;
        $data['id_annee_active'] = $this->id_annee_active;
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('n.deleted_at', null);
        $this->db->select('n.*, e.nom, e.prenom, m.libelle as matiere, ev.libelle as evaluation');
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
        $this->json_success($this->db->get()->result_array());
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant']) || !isset($data['note'])) {
            $this->json_error('Étudiant et note obligatoires'); return;
        }
        $id = $this->Model->createLastId('notes', $data);
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
        $this->db->select('e.id_etudiant, e.nom, e.prenom, e.matricule, i.numero_ordre');
        $this->db->from('inscriptions i');
        $this->db->join('etudiants e', 'i.id_etudiant = e.id_etudiant');
        $this->db->order_by('e.nom, e.prenom');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_evaluations_by_classe($id_classe) {
        $this->db->where('ev.id_classe', $id_classe);
        $this->db->where('ev.deleted_at', null);
        $this->db->select('ev.*, m.libelle as matiere_libelle');
        $this->db->from('evaluations ev');
        $this->db->join('matieres m', 'ev.id_matiere = m.id_matiere', 'left');
        $this->db->order_by('ev.date_eval', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_matieres_by_classe($id_classe) {
        $sql = "
            SELECT DISTINCT m.id_matiere, m.libelle, m.code
            FROM matieres_classes mc
            JOIN matieres m ON mc.id_matiere = m.id_matiere
            WHERE mc.id_classe = ? AND mc.deleted_at IS NULL
            UNION
            SELECT DISTINCT m.id_matiere, m.libelle, m.code
            FROM enseignements e
            JOIN matieres m ON e.id_matiere = m.id_matiere
            WHERE e.id_classe = ? AND e.deleted_at IS NULL
            ORDER BY libelle
        ";
        $matieres = $this->db->query($sql, [$id_classe, $id_classe])->result_array();
        $this->json_success($matieres);
    }

    public function api_grille_notes($id_classe, $id_matiere) {
        $this->db->where('i.id_classe', $id_classe);
        $this->db->where('i.id_annee', $this->id_annee_active);
        $this->db->where('i.deleted_at', null);
        $this->db->where('e.deleted_at', null);
        $this->db->select('e.id_etudiant, e.nom, e.prenom, e.matricule');
        $this->db->from('inscriptions i');
        $this->db->join('etudiants e', 'i.id_etudiant = e.id_etudiant');
        $this->db->order_by('e.nom, e.prenom');
        $eleves = $this->db->get()->result_array();

        $this->db->where('ev.id_classe', $id_classe);
        $this->db->where('ev.id_matiere', $id_matiere);
        $this->db->where('ev.deleted_at', null);
        $this->db->select('ev.id_evaluation, ev.libelle, ev.sur, ev.date_eval, ev.type, ev.coefficient');
        $this->db->from('evaluations ev');
        $this->db->order_by('ev.date_eval', 'ASC');
        $evaluations = $this->db->get()->result_array();

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
        $classes = $this->db->get()->result_array();

        foreach ($classes as &$cl) {
            $cl['nb_etudiants'] = $this->db
                ->where('i.id_classe', $cl['id_classe'])
                ->where('i.id_annee', $this->id_annee_active)
                ->where('i.deleted_at', null)
                ->count_all_results('inscriptions i');

            $cl['nb_matieres'] = $this->db->query(
                "SELECT COUNT(*) as cnt FROM (
                    SELECT id_matiere FROM matieres_classes WHERE id_classe = ? AND deleted_at IS NULL
                    UNION
                    SELECT id_matiere FROM enseignements WHERE id_classe = ? AND deleted_at IS NULL
                ) t", [$cl['id_classe'], $cl['id_classe']]
            )->row()->cnt;
        }
        $this->json_success($classes);
    }

    public function api_delete($id) {
        if ($this->Model->update('notes', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Note supprimée');
        else $this->json_error('Erreur');
    }
}
