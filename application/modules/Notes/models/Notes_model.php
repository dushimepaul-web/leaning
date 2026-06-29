<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notes_model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = [])
    {
        $this->db->where('n.deleted_at', null);
        $this->db->select('n.*, e.nom, e.prenom, e.matricule, ev.libelle as evaluation_libelle, m.libelle as matiere, ev.id_periode, ev.id_classe');
        $this->db->from('notes n');
        $this->db->join('etudiants e', 'n.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('evaluations ev', 'n.id_evaluation = ev.id_evaluation', 'left');
        $this->db->join('matieres m', 'ev.id_matiere = m.id_matiere', 'left');

        if (!empty($filters['id_etudiant'])) $this->db->where('n.id_etudiant', $filters['id_etudiant']);
        if (!empty($filters['id_evaluation'])) $this->db->where('n.id_evaluation', $filters['id_evaluation']);
        if (!empty($filters['id_classe'])) $this->db->where('ev.id_classe', $filters['id_classe']);
        if (!empty($filters['id_matiere'])) $this->db->where('ev.id_matiere', $filters['id_matiere']);

        return $this->db->get()->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->where('n.deleted_at', null);
        $this->db->where('n.uuid', $id);
        $this->db->select('n.*, e.nom, e.prenom, ev.libelle as evaluation_libelle, m.libelle as matiere');
        $this->db->from('notes n');
        $this->db->join('etudiants e', 'n.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('evaluations ev', 'n.id_evaluation = ev.id_evaluation', 'left');
        $this->db->join('matieres m', 'ev.id_matiere = m.id_matiere', 'left');
        return $this->db->get()->row_array();
    }

    public function create_record($data)
    {
        $required = ['id_etudiant', 'id_evaluation', 'note'];
        foreach ($required as $field) {
            if (empty($data[$field]) && $field !== 'note') {
                return ['success' => false, 'message' => "Champ requis manquant: $field"];
            }
        }

        // Vérifier évaluation existe
        $eval = $this->readOne('evaluations', ['id_evaluation' => $data['id_evaluation'], 'deleted_at' => null]);
        if (!$eval) {
            return ['success' => false, 'message' => 'Évaluation inexistante'];
        }

        // Vérifier note dans les bornes
        $note = (float)$data['note'];
        if ($note < 0 || $note > $eval['note_max']) {
            return ['success' => false, 'message' => "Note doit être entre 0 et {$eval['note_max']}"];
        }

        // Vérifier si note existe déjà
        $exist = $this->readOne('notes', ['id_etudiant' => $data['id_etudiant'], 'id_evaluation' => $data['id_evaluation'], 'deleted_at' => null]);
        if ($exist) {
            return ['success' => false, 'message' => 'Note déjà existante pour cet étudiant/évaluation'];
        }

        $data['uuid'] = generate_uuid();
        $data['cree_le'] = date('Y-m-d H:i:s');
        $data['modifie_le'] = date('Y-m-d H:i:s');

        if ($this->db->insert('notes', $data)) {
            return ['success' => true, 'id_note' => $this->db->insert_id()];
        }
        return ['success' => false, 'message' => 'Erreur insertion note'];
    }

    public function create_batch($notes_data)
    {
        $results = ['success' => 0, 'errors' => []];
        foreach ($notes_data as $data) {
            $result = $this->create_record($data);
            if ($result['success']) {
                $results['success']++;
            } else {
                $results['errors'][] = $result['message'];
            }
        }
        return $results;
    }

    public function update_record($id, $data)
    {
        $this->db->where('uuid', $id);
        $data['modifie_le'] = date('Y-m-d H:i:s');
        return $this->db->update('notes', $data);
    }

    public function delete_record($id)
    {
        return $this->update_record($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    // Bulletins
    public function get_bulletins($filters = [])
    {
        $this->db->where('b.deleted_at', null);
        $this->db->select('b.*, e.nom, e.prenom, e.matricule, c.libelle as classe, p.libelle as periode, a.libelle as annee');
        $this->db->from('bulletins b');
        $this->db->join('etudiants e', 'b.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('classes c', 'b.id_classe = c.id_classe', 'left');
        $this->db->join('periodes p', 'b.id_periode = p.id_periode', 'left');
        $this->db->join('annees_scolaires a', 'b.id_annee = a.id_annee', 'left');

        if (!empty($filters['id_etudiant'])) $this->db->where('b.id_etudiant', $filters['id_etudiant']);
        if (!empty($filters['id_classe'])) $this->db->where('b.id_classe', $filters['id_classe']);
        if (!empty($filters['id_periode'])) $this->db->where('b.id_periode', $filters['id_periode']);

        return $this->db->get()->result_array();
    }

    public function get_grille_notes($id_classe, $id_periode, $id_matiere = null)
    {
        $this->db->where('ev.deleted_at', null);
        $this->db->where('ev.id_classe', $id_classe);
        $this->db->where('ev.id_periode', $id_periode);
        if ($id_matiere) $this->db->where('ev.id_matiere', $id_matiere);
        $this->db->select('ev.*, m.libelle as matiere');
        $this->db->from('evaluations ev');
        $this->db->join('matieres m', 'ev.id_matiere = m.id_matiere', 'left');
        $evaluations = $this->db->get()->result_array();

        $this->db->where('e.deleted_at', null);
        $this->db->where('i.id_classe', $id_classe);
        $this->db->where('i.id_annee', $this->id_annee_active);
        $this->db->select('e.*, i.id_inscription');
        $this->db->from('etudiants e');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant', 'left');
        $etudiants = $this->db->get()->result_array();

        // Récupérer toutes les notes
        $eval_ids = array_column($evaluations, 'id_evaluation');
        $notes = [];
        if (!empty($eval_ids)) {
            $this->db->where_in('id_evaluation', $eval_ids);
            $this->db->where('deleted_at', null);
            $notes = $this->db->get('notes')->result_array();
        }

        return [
            'evaluations' => $evaluations,
            'etudiants' => $etudiants,
            'notes' => $notes
        ];
    }

    public function generer_bulletins($id_classe, $id_periode, $id_annee)
    {
        // Logique de génération des bulletins
        $this->db->where('e.deleted_at', null);
        $this->db->where('i.id_classe', $id_classe);
        $this->db->where('i.id_annee', $id_annee);
        $this->db->select('e.id_etudiant');
        $this->db->from('etudiants e');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant', 'left');
        $etudiants = $this->db->get()->result_array();

        $created = 0;
        foreach ($etudiants as $et) {
            $id_etudiant = $et['id_etudiant'];
            
            // Calculer moyennes
            $this->db->where('n.deleted_at', null);
            $this->db->where('n.id_etudiant', $id_etudiant);
            $this->db->join('evaluations ev', 'n.id_evaluation = ev.id_evaluation', 'left');
            $this->db->where('ev.id_periode', $id_periode);
            $this->db->where('ev.deleted_at', null);
            $this->db->select('n.note, ev.coefficient, ev.note_max');
            $notes = $this->db->get('notes')->result_array();

            if (empty($notes)) continue;

            $somme_ponderee = 0;
            $somme_coef = 0;
            foreach ($notes as $n) {
                $coef = (float)($n['coefficient'] ?? 1);
                $note_max = (float)($n['note_max'] ?? 20);
                $note = (float)$n['note'];
                $note_sur_20 = ($note / $note_max) * 20;
                $somme_ponderee += $note_sur_20 * $coef;
                $somme_coef += $coef;
            }
            $moyenne = $somme_coef > 0 ? round($somme_ponderee / $somme_coef, 2) : 0;

            $bulletin = [
                'uuid' => generate_uuid(),
                'id_etudiant' => $id_etudiant,
                'id_classe' => $id_classe,
                'id_periode' => $id_periode,
                'id_annee' => $id_annee,
                'moyenne_generale' => $moyenne,
                'rang' => 0,
                'appreciation' => $this->_get_appreciation($moyenne),
                'cree_le' => date('Y-m-d H:i:s'),
                'modifie_le' => date('Y-m-d H:i:s'),
            ];

            if ($this->db->insert('bulletins', $bulletin)) {
                $created++;
            }
        }

        // Calculer rangs
        $this->db->where('id_classe', $id_classe);
        $this->db->where('id_periode', $id_periode);
        $this->db->where('id_annee', $id_annee);
        $this->db->order_by('moyenne_generale', 'DESC');
        $bulletins = $this->db->get('bulletins')->result_array();
        
        $rang = 1;
        $prev_moyenne = null;
        foreach ($bulletins as $b) {
            if ($b['moyenne_generale'] !== $prev_moyenne) {
                $this->db->where('uuid', $b['uuid']);
                $this->db->update('bulletins', ['rang' => $rang]);
                $prev_moyenne = $b['moyenne_generale'];
            }
            $rang++;
        }

        return ['success' => true, 'created' => $created];
    }

    private function _get_appreciation($moyenne)
    {
        if ($moyenne >= 16) return 'Excellent';
        if ($moyenne >= 14) return 'Très bien';
        if ($moyenne >= 12) return 'Bien';
        if ($moyenne >= 10) return 'Assez bien';
        if ($moyenne >= 8) return 'Passable';
        return 'Insuffisant';
    }
}