<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bulletins extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Bulletins & Fiches de points';
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['periodes'] = $this->Model->read('periodes', ['deleted_at' => null, 'id_annee' => $this->id_annee_active]);
        $data['annees'] = $this->Model->read('annees_scolaires', ['deleted_at' => null]);
        $data['id_annee_active'] = $this->id_annee_active;
        $data['id_periode_active'] = $this->id_periode_active;
        $this->load->view('bulletins', $data);
    }

    public function api_list() {
        $this->db->where('b.deleted_at', null);
        $this->db->select('b.*, e.nom, e.prenom, e.matricule, c.libelle as classe, p.libelle as periode, a.libelle as annee');
        $this->db->from('bulletins b');
        $this->db->join('etudiants e', 'b.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('classes c', 'b.id_classe = c.id_classe', 'left');
        $this->db->join('periodes p', 'b.id_periode = p.id_periode', 'left');
        $this->db->join('annees_scolaires a', 'b.id_annee = a.id_annee', 'left');
        $this->db->order_by('b.date_edition', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant']) || empty($data['id_classe']) || empty($data['id_periode']) || empty($data['id_annee'])) {
            $this->json_error('Étudiant, classe, période et année obligatoires'); return;
        }
        $existing = $this->Model->readOne('bulletins', [
            'id_etudiant' => $data['id_etudiant'],
            'id_periode' => $data['id_periode'],
            'id_annee' => $data['id_annee'],
            'deleted_at' => null
        ]);
        if ($existing) { $this->json_error('Un bulletin existe déjà pour cet étudiant sur cette période'); return; }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'id_etudiant' => $data['id_etudiant'],
            'id_classe' => $data['id_classe'],
            'id_periode' => $data['id_periode'],
            'id_annee' => $data['id_annee'],
            'moyenne' => isset($data['moyenne']) ? $data['moyenne'] : null,
            'rang' => isset($data['rang']) ? $data['rang'] : null,
            'decision' => isset($data['decision']) ? $data['decision'] : 'admis',
            'date_edition' => !empty($data['date_edition']) ? $data['date_edition'] : date('Y-m-d'),
        ];
        $id = $this->Model->createLastId('bulletins', $insert);
        if ($id) $this->json_success(['id_bulletin' => $id], 'Bulletin créé');
        else $this->json_error('Erreur création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $allowed = ['moyenne', 'rang', 'decision', 'date_edition'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if ($this->Model->update('bulletins', ['uuid' => $id], $update))
            $this->json_success(null, 'Bulletin mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('bulletins', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Bulletin supprimé');
        else $this->json_error('Erreur');
    }

    public function api_detail($id) {
        $b = $this->Model->readOne('bulletins', ['uuid' => $id, 'deleted_at' => null]);
        if (!$b) { $this->json_error('Bulletin introuvable'); return; }
        $etudiant = $this->Model->readOne('etudiants', ['id_etudiant' => $b['id_etudiant']]);
        $classe = $this->Model->readOne('classes', ['id_classe' => $b['id_classe']]);
        $periode = $this->Model->readOne('periodes', ['id_periode' => $b['id_periode']]);
        $annee = $this->Model->readOne('annees_scolaires', ['id_annee' => $b['id_annee']]);
        $b['etudiant_nom'] = $etudiant ? $etudiant['nom'].' '.$etudiant['prenom'] : '';
        $b['classe'] = $classe ? $classe['libelle'] : '';
        $b['periode'] = $periode ? $periode['libelle'] : '';
        $b['annee'] = $annee ? $annee['libelle'] : '';

        $evals = $this->db->where('id_classe', $b['id_classe'])->where('id_periode', $b['id_periode'])->where('deleted_at', null)->get('evaluations')->result_array();
        $evalIds = array_column($evals, 'id_evaluation');
        $notes = empty($evalIds) ? [] : $this->db
            ->where('id_etudiant', $b['id_etudiant'])->where_in('id_evaluation', $evalIds)->where('deleted_at', null)
            ->get('notes')->result_array();
        $notesByEval = [];
        foreach ($notes as $n) { $notesByEval[$n['id_evaluation']] = $n; }

        $b['notes'] = [];
        foreach ($evals as $ev) {
            $matiere = $this->Model->readOne('matieres', ['id_matiere' => $ev['id_matiere']]);
            $n = isset($notesByEval[$ev['id_evaluation']]) ? $notesByEval[$ev['id_evaluation']] : null;
            $b['notes'][] = [
                'matiere' => $matiere ? $matiere['libelle'] : '-',
                'evaluation' => $ev['libelle'],
                'note' => $n ? floatval($n['note']) : null,
                'coefficient' => floatval($ev['coefficient']),
                'sur' => floatval($ev['sur']),
            ];
        }
        $this->json_success($b);
    }

    public function api_generer() {
        $data = $this->get_json_input();
        $id_classe = !empty($data['id_classe']) ? $data['id_classe'] : null;
        $id_periode = !empty($data['id_periode']) ? $data['id_periode'] : $this->id_periode_active;
        $id_annee = $this->id_annee_active;

        if (!$id_periode) { $this->json_error('Aucune période active définie'); return; }

        // Récupérer les étudiants de la classe
        $this->db->where('i.id_annee', $id_annee);
        $this->db->where('i.deleted_at', null);
        $this->db->where('e.deleted_at', null);
        if ($id_classe) $this->db->where('i.id_classe', $id_classe);
        $this->db->select('i.id_etudiant, i.id_classe, e.nom, e.prenom');
        $this->db->from('inscriptions i');
        $this->db->join('etudiants e', 'i.id_etudiant = e.id_etudiant');
        $q_s = $this->db->get();
        $students = $q_s !== false ? $q_s->result_array() : array();

        if (empty($students)) { $this->json_error('Aucun étudiant trouvé'); return; }

        // Récupérer toutes les évaluations de la période
        $this->db->where('ev.id_periode', $id_periode);
        $this->db->where('ev.deleted_at', null);
        if ($id_classe) $this->db->where('ev.id_classe', $id_classe);
        $evaluations = $this->db->get('evaluations ev')->result_array();

        if (empty($evaluations)) { $this->json_error('Aucune évaluation trouvée pour cette période'); return; }

        $evalIds = array_column($evaluations, 'id_evaluation');

        // Récupérer toutes les notes pour ces évaluations
        $this->db->where_in('n.id_evaluation', $evalIds);
        $this->db->where('n.deleted_at', null);
        $allNotes = $this->db->get('notes n')->result_array();

        $notesByStudent = [];
        foreach ($allNotes as $note) {
            $notesByStudent[$note['id_etudiant']][] = $note;
        }

        $this->load->helper('uuid');
        $created = 0;
        $updated = 0;
        $moyennes = [];

        foreach ($students as $student) {
            $notes = isset($notesByStudent[$student['id_etudiant']]) ? $notesByStudent[$student['id_etudiant']] : [];
            if (empty($notes)) continue;

            // Calculer la moyenne pondérée
            $sum = 0;
            $count = 0;
            foreach ($notes as $note) {
                $eval = null;
                foreach ($evaluations as $ev) {
                    if ($ev['id_evaluation'] == $note['id_evaluation']) { $eval = $ev; break; }
                }
                $coeff = $eval ? floatval($eval['coefficient']) : 1.0;
                $sum += floatval($note['note']) * $coeff;
                $count += $coeff;
            }
            $moyenne = $count > 0 ? round($sum / $count, 2) : 0;
            $moyennes[$student['id_etudiant']] = $moyenne;

            // Décision
            $decision = $this->_getDecision($moyenne);

            $existing = $this->Model->readOne('bulletins', [
                'id_etudiant' => $student['id_etudiant'],
                'id_periode' => $id_periode,
                'id_annee' => $id_annee,
                'deleted_at' => null
            ]);

            $insert = [
                'id_etudiant' => $student['id_etudiant'],
                'id_classe' => $student['id_classe'],
                'id_periode' => $id_periode,
                'id_annee' => $id_annee,
                'moyenne' => $moyenne,
                'decision' => $decision,
                'date_edition' => date('Y-m-d'),
            ];

            if ($existing) {
                $insert['rang'] = null; // sera calculé après
                $this->Model->update('bulletins', ['id_bulletin' => $existing['id_bulletin']], $insert);
                $updated++;
            } else {
                $insert['uuid'] = generate_uuid();
                $this->Model->create('bulletins', $insert);
                $created++;
            }
        }

        // Calculer les rangs par classe
        $classes = array_unique(array_column($students, 'id_classe'));
        foreach ($classes as $classeId) {
            $classStudents = array_filter($students, function($s) use ($classeId) { return $s['id_classe'] == $classeId; });
            $classMoyennes = [];
            foreach ($classStudents as $s) {
                if (isset($moyennes[$s['id_etudiant']])) {
                    $classMoyennes[$s['id_etudiant']] = $moyennes[$s['id_etudiant']];
                }
            }
            arsort($classMoyennes);
            $rang = 1;
            foreach ($classMoyennes as $idEtudiant => $moy) {
                $bulletin = $this->Model->readOne('bulletins', [
                    'id_etudiant' => $idEtudiant,
                    'id_periode' => $id_periode,
                    'id_annee' => $id_annee,
                    'deleted_at' => null
                ]);
                if ($bulletin) {
                    $this->Model->update('bulletins', ['id_bulletin' => $bulletin['id_bulletin']], ['rang' => $rang]);
                }
                $rang++;
            }
        }

        $this->json_success([
            'created' => $created,
            'updated' => $updated,
            'total' => count($students)
        ], "Bulletins générés : $created créés, $updated mis à jour");
    }

    private function _getDecision($moyenne) {
        $admis = floatval($this->Model->get_setting('regle_admis_moy', 12));
        $ajourne = floatval($this->Model->get_setting('regle_ajourne_moy', 10));
        if ($moyenne >= $admis) return 'admis';
        if ($moyenne >= $ajourne) return 'ajourne';
        return 'echoue';
    }
}
