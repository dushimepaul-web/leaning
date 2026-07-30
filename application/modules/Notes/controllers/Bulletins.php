<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bulletins extends MY_Controller {
    public function __construct() { parent::__construct(); $this->load->model('Bulletins_model', 'BulletinsModel'); }

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
        $this->db->select("b.*, e.fullname AS nom, '' AS prenom, e.matricule, c.libelle as classe, p.libelle as periode, a.libelle as annee");
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
        $b['etudiant_nom'] = $etudiant ? $etudiant['fullname'] : '';
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
        $this->db->select("i.id_etudiant, i.id_classe, e.fullname AS nom, '' AS prenom");
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

    public function api_bulletin_complet($id_classe)
    {
        $id_periode = $this->input->get('periode');
        $id_annee = $this->input->get('annee') ?: $this->id_annee_active;

        if (!$id_classe) { $this->json_error('Classe requise'); return; }

        $data = $this->BulletinsModel->get_bulletin_complet($id_classe, $id_annee, $id_periode);

        if ($data === null) {
            $this->json_error('Aucune donnée trouvée pour cette classe');
            return;
        }

        // Calculer rangs
        $eleves = $data['eleves'];
        usort($eleves, function($a, $b) { return $b['moyenne'] <=> $a['moyenne']; });
        $rang = 1; $prev = -1;
        foreach ($eleves as $i => &$el) {
            if ($prev >= 0 && $el['moyenne'] < $prev) $rang = $i + 1;
            $el['rang'] = ($el['moyenne'] > 0) ? $rang : 0;
            $prev = $el['moyenne'];
        }
        unset($el);
        usort($eleves, function($a, $b) { return strcasecmp($a['fullname'], $b['fullname']); });
        $data['eleves'] = $eleves;

        $this->json_success($data);
    }

    private function _getDecision($moyenne) {
        $admis = floatval($this->Model->get_setting('regle_admis_moy', 12));
        $ajourne = floatval($this->Model->get_setting('regle_ajourne_moy', 10));
        if ($moyenne >= $admis) return 'admis';
        if ($moyenne >= $ajourne) return 'ajourne';
        return 'echoue';
    }

    public function export_bulletins_classe($class_id)
    {
        $eleves_db = $this->Model->readQuery("
            SELECT i.id_etudiant AS inscription_id, e.fullname, e.matricule
            FROM inscriptions i
            LEFT JOIN etudiants e ON e.id_etudiant = i.id_etudiant
            WHERE i.id_classe = ? AND i.deleted_at IS NULL AND e.deleted_at IS NULL
            ORDER BY e.fullname ASC
        ", [$class_id]);

        if (empty($eleves_db)) {
            echo "<h3 style='font-family:Arial; text-align:center; margin-top:50px;'>Aucun élève trouvé pour cette classe.</h3>";
            return;
        }

        $eleves = [];
        foreach ($eleves_db as $e) {
            $eleves[$e['inscription_id']] = $e;
        }

        $annee_id = $this->id_annee_active;
        $annee_scolaire = $annee_id  
            ? ($this->Model->readQuery('SELECT libelle FROM annees_scolaires WHERE id_annee = ?', [$annee_id])[0]['libelle'] ?? 'N/A')  
            : 'N/A';

        $all_subjects = $this->Model->readQuery("
            SELECT m.id_matiere AS id, m.libelle AS name, m.code, mc.coefficient, 1 AS is_active
            FROM matieres_classes mc
            JOIN matieres m ON m.id_matiere = mc.id_matiere
            WHERE mc.id_classe = ? AND mc.deleted_at IS NULL AND m.deleted_at IS NULL
        ", [$class_id]);

        if (empty($all_subjects)) {
            echo "<h3 style='font-family:Arial; text-align:center; margin-top:50px;'>Aucune matière assignée à cette classe.</h3>";
            return;
        }

        $all_subject_ids = array_column($all_subjects, 'id');
        $etudiant_ids = array_column($eleves_db, 'inscription_id');
        $etudiant_ids_str = implode(',', $etudiant_ids);
        $all_subject_ids_str = implode(',', $all_subject_ids);

        $periodes = $this->Model->read('periodes', ['id_annee' => $annee_id, 'deleted_at' => null], 'id_periode');
        $periode_map = [];
        foreach ($periodes as $p) {
            if (stripos($p['libelle'], '1') !== false) $periode_map[1] = $p['id_periode'];
            elseif (stripos($p['libelle'], '2') !== false) $periode_map[2] = $p['id_periode'];
            elseif (stripos($p['libelle'], '3') !== false) $periode_map[3] = $p['id_periode'];
        }

        $p1 = $periode_map[1] ?? 0;
        $p2 = $periode_map[2] ?? 0;
        $p3 = $periode_map[3] ?? 0;

        $query = "
            SELECT 
                n.id_etudiant AS inscription_id,
                ev.id_matiere AS subject_id,
                
                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('interrogation', 'devoir') THEN n.note ELSE 0 END) AS note_t1_tj,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('composition', 'examen') THEN n.note ELSE 0 END) AS note_t1_comp,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'tp' THEN n.note ELSE 0 END) AS note_t1_ress,

                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('interrogation', 'devoir') THEN n.note ELSE 0 END) AS note_t2_tj,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('composition', 'examen') THEN n.note ELSE 0 END) AS note_t2_comp,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'tp' THEN n.note ELSE 0 END) AS note_t2_ress,

                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('interrogation', 'devoir') THEN n.note ELSE 0 END) AS note_t3_tj,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('composition', 'examen') THEN n.note ELSE 0 END) AS note_t3_comp,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'tp' THEN n.note ELSE 0 END) AS note_t3_ress,

                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('interrogation', 'devoir') THEN ev.sur ELSE 0 END) AS max_t1_tj,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('composition', 'examen') THEN ev.sur ELSE 0 END) AS max_t1_comp,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'tp' THEN ev.sur ELSE 0 END) AS max_t1_ress,

                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('interrogation', 'devoir') THEN ev.sur ELSE 0 END) AS max_t2_tj,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('composition', 'examen') THEN ev.sur ELSE 0 END) AS max_t2_comp,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'tp' THEN ev.sur ELSE 0 END) AS max_t2_ress,

                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('interrogation', 'devoir') THEN ev.sur ELSE 0 END) AS max_t3_tj,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('composition', 'examen') THEN ev.sur ELSE 0 END) AS max_t3_comp,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'tp' THEN ev.sur ELSE 0 END) AS max_t3_ress
            
            FROM notes n
            JOIN evaluations ev ON ev.id_evaluation = n.id_evaluation
            WHERE n.id_etudiant IN ({$etudiant_ids_str}) 
            AND ev.id_matiere IN ({$all_subject_ids_str})
            AND n.deleted_at IS NULL AND ev.deleted_at IS NULL
            GROUP BY n.id_etudiant, ev.id_matiere
        ";

        $aggregated_data = $this->Model->readQuery($query, [
            $p1, $p1, $p1,
            $p2, $p2, $p2,
            $p3, $p3, $p3,
            $p1, $p1, $p1,
            $p2, $p2, $p2,
            $p3, $p3, $p3,
        ]);

        $classe_info = $this->Model->readOne('classes', ['id_classe' => $class_id]);
        $classe_nom = $classe_info ? $classe_info['libelle'] : 'Classe';

        $data['title'] = 'Bulletins de la classe ' . $classe_nom;
        $data['eleves'] = $eleves;
        $data['subjects'] = $all_subjects;
        $data['annee_scolaire'] = $annee_scolaire;
        $data['classe_nom'] = $classe_nom;
        $data['aggregated_data'] = $aggregated_data;
        
        $this->load->view('print_bulletins', $data);
    }
}
