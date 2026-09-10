<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bulletins extends MY_Controller {
    public function __construct() { parent::__construct(); $this->load->model('Bulletins_model', 'BulletinsModel'); }

    public function index() {
        $data['title'] = 'Bulletins & Fiches de points';
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['periodes'] = $this->Model->read('periodes', ['id_annee' => $this->id_annee_active, 'deleted_at' => null]);
        $data['annees'] = $this->Model->read('annees_scolaires', ['deleted_at' => null]);
        $data['id_annee_active'] = $this->id_annee_active;
        $data['id_periode_active'] = $this->id_periode_active;
        $this->load->view('bulletins', $data);
    }

    public function api_periodes($id_annee = null)
    {
        if (!$id_annee) {
            $this->json_error('Année requise');
            return;
        }
        $periodes = $this->Model->read('periodes', ['id_annee' => $id_annee, 'deleted_at' => null], 'id_periode');
        $this->json_success($periodes);
    }

    public function api_get($id) {
        $this->db->where('b.uuid', $id);
        $this->db->where('b.deleted_at', null);
        $this->db->select("b.*, e.fullname AS nom, '' AS prenom, e.matricule, c.libelle as classe, p.libelle as periode, a.libelle as annee");
        $this->db->from('bulletins b');
        $this->db->join('etudiants e', 'b.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('classes c', 'b.id_classe = c.id_classe', 'left');
        $this->db->join('periodes p', 'b.id_periode = p.id_periode', 'left');
        $this->db->join('annees_scolaires a', 'b.id_annee = a.id_annee', 'left');
        $q = $this->db->get();
        $d = $q !== false ? $q->row_array() : null;
        if (!$d) { $this->json_error('Bulletin introuvable', 404); return; }
        $this->json_success($d);
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
        if (!$this->Model->readOne('etudiants', ['id_etudiant' => $data['id_etudiant'], 'deleted_at' => null])) {
            $this->json_error('Étudiant introuvable'); return;
        }
        if (!$this->Model->readOne('classes', ['id_classe' => $data['id_classe'], 'deleted_at' => null])) {
            $this->json_error('Classe introuvable'); return;
        }
        $periode = $this->Model->readOne('periodes', ['id_periode' => $data['id_periode'], 'deleted_at' => null]);
        if (!$periode) { $this->json_error('Période introuvable'); return; }
        if ((int)$periode['id_annee'] !== (int)$data['id_annee']) {
            $this->json_error('La période ne correspond pas à l\'année choisie'); return;
        }
        if (!$this->Model->readOne('annees_scolaires', ['id_annee' => $data['id_annee'], 'deleted_at' => null])) {
            $this->json_error('Année scolaire introuvable'); return;
        }
        $inscription = $this->Model->readOne('inscriptions', [
            'id_etudiant' => $data['id_etudiant'],
            'id_annee' => $data['id_annee'],
            'deleted_at' => null
        ]);
        if (!$inscription) { $this->json_error('Aucune inscription pour cet étudiant cette année'); return; }
        if ((int)$inscription['id_classe'] !== (int)$data['id_classe']) {
            $this->json_error('La classe ne correspond pas à l\'inscription de l\'étudiant'); return;
        }
        $existing = $this->Model->readOne('bulletins', [
            'id_etudiant' => $data['id_etudiant'],
            'id_periode' => $data['id_periode'],
            'id_annee' => $data['id_annee'],
            'deleted_at' => null
        ]);
        if ($existing) { $this->json_error('Un bulletin existe déjà pour cet étudiant sur cette période'); return; }
        $moyenne = isset($data['moyenne']) && $data['moyenne'] !== '' && $data['moyenne'] !== null ? floatval($data['moyenne']) : null;
        if ($moyenne !== null && ($moyenne < 0 || $moyenne > 100)) { $this->json_error('Moyenne invalide'); return; }
        if (isset($data['rang']) && $data['rang'] !== '' && $data['rang'] !== null && intval($data['rang']) < 1) {
            $this->json_error('Rang invalide'); return;
        }
        $decision = $data['decision'] ?? 'admis';
        if (!in_array($decision, ['admis', 'ajourne', 'echoue'], true)) { $this->json_error('Décision invalide'); return; }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'id_etudiant' => $data['id_etudiant'],
            'id_classe' => $data['id_classe'],
            'id_periode' => $data['id_periode'],
            'id_annee' => $data['id_annee'],
            'moyenne' => $moyenne,
            'rang' => isset($data['rang']) && $data['rang'] !== '' ? intval($data['rang']) : null,
            'decision' => $decision,
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
        if (isset($update['moyenne']) && $update['moyenne'] !== '' && $update['moyenne'] !== null) {
            $moyenne = floatval($update['moyenne']);
            if ($moyenne < 0 || $moyenne > 100) { $this->json_error('Moyenne invalide'); return; }
            $update['moyenne'] = $moyenne;
        }
        if (isset($update['rang']) && $update['rang'] !== '' && $update['rang'] !== null && intval($update['rang']) < 1) {
            $this->json_error('Rang invalide'); return;
        }
        if (isset($update['decision']) && !in_array($update['decision'], ['admis', 'ajourne', 'echoue'], true)) {
            $this->json_error('Décision invalide'); return;
        }
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
        $mc_rows = $this->db->select('id_matiere, note_max_matiere')->from('matieres_classes')->where('id_classe', $b['id_classe'])->where('deleted_at', null)->get()->result_array();
        $coeff_map = [];
        foreach ($mc_rows as $mc) { $coeff_map[$mc['id_matiere']] = floatval($mc['note_max_matiere'] ?: 1); }
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
                'coefficient' => $coeff_map[$ev['id_matiere']] ?? 1,
                'ponderee_sur' => floatval($ev['ponderee_sur']),
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

        $this->db->where('ev.id_periode', $id_periode);
        $this->db->where('ev.deleted_at', null);
        if ($id_classe) $this->db->where('ev.id_classe', $id_classe);
        $evaluations = $this->db->get('evaluations ev')->result_array();

        if (empty($evaluations)) { $this->json_error('Aucune évaluation trouvée pour cette période'); return; }

        $evalIds = array_column($evaluations, 'id_evaluation');

        $mc_query = $this->db->select('id_matiere, note_max_matiere')->from('matieres_classes')->where('deleted_at', null);
        if ($id_classe) $this->db->where('id_classe', $id_classe);
        $mc_rows = $mc_query->get()->result_array();
        $coeff_map = [];
        foreach ($mc_rows as $mc) { $coeff_map[$mc['id_matiere']] = floatval($mc['note_max_matiere'] ?: 1); }

        $etudiant_ids = array_column($students, 'id_etudiant');
        $this->db->where_in('n.id_etudiant', $etudiant_ids);
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
        $bulletin_ids_to_update = [];

        foreach ($students as $student) {
            $notes = isset($notesByStudent[$student['id_etudiant']]) ? $notesByStudent[$student['id_etudiant']] : [];
            if (empty($notes)) continue;

            $sum = 0;
            $count = 0;
            foreach ($notes as $note) {
                $eval = null;
                foreach ($evaluations as $ev) {
                    if ($ev['id_evaluation'] == $note['id_evaluation']) { $eval = $ev; break; }
                }
                $coeff = $eval ? ($coeff_map[$eval['id_matiere']] ?? 1.0) : 1.0;
                $sur = $eval && floatval($eval['ponderee_sur']) > 0 ? floatval($eval['ponderee_sur']) : 20;
                $sum += (floatval($note['note']) / $sur) * 20 * $coeff;
                $count += $coeff;
            }
            $moyenne = $count > 0 ? round($sum / $count, 2) : 0;
            $moyennes[$student['id_etudiant']] = $moyenne;

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
                $insert['rang'] = null;
                $this->Model->update('bulletins', ['id_bulletin' => $existing['id_bulletin']], $insert);
                $bulletin_ids_to_update[] = $existing['id_bulletin'];
                $updated++;
            } else {
                $insert['uuid'] = generate_uuid();
                $this->Model->create('bulletins', $insert);
                $created++;
            }
        }

        $all_bulletins = $this->Model->read('bulletins', [
            'id_periode' => $id_periode,
            'id_annee' => $id_annee,
            'deleted_at' => null
        ]);
        $bulletin_map = [];
        foreach ($all_bulletins as $b) {
            $bulletin_map[$b['id_etudiant']] = $b['id_bulletin'];
        }

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
            $batch_update = [];
            foreach ($classMoyennes as $idEtudiant => $moy) {
                if (isset($bulletin_map[$idEtudiant])) {
                    $batch_update[] = [
                        'id_bulletin' => $bulletin_map[$idEtudiant],
                        'rang' => $rang,
                    ];
                }
                $rang++;
            }
            if (!empty($batch_update)) {
                $this->Model->updateBatch('bulletins', $batch_update, 'id_bulletin');
            }
        }

        $this->json_success([
            'created' => $created,
            'updated' => $updated,
            'total' => count($students)
        ], "Bulletins générés : $created créés, $updated mis à jour");
    }

    public function api_bulletin_complet($id_classe = null)
    {
        $id_periode = $this->input->get('periode');
        $id_annee = $this->input->get('annee') ?: $this->id_annee_active;
        $cumul = $this->input->get('cumul') === '1';

        if (!$id_classe) { $this->json_error('Classe requise'); return; }

        $data = $this->BulletinsModel->get_bulletin_complet($id_classe, $id_annee, $id_periode, $cumul);

        if ($data === null) {
            $this->json_error('Aucune donnée trouvée pour cette classe');
            return;
        }

        // Calculer rangs et pourcentages selon la période choisie ou l'année complète
        $eleves = $data['eleves'];
        $periodes = $data['periodes'];
        $maxima = $data['maxima'];
        $matieres = $data['matieres'];

        // Si une période spécifique est filtrée (et pas 'all'), on calcule le pourcentage et le rang de cette période précise
        $filtered_pid = ($id_periode && $id_periode !== 'all') ? intval($id_periode) : null;

        foreach ($eleves as &$el) {
            if ($filtered_pid) {
                // Pourcentage de la période spécifique
                $p_note = 0;
                foreach ($matieres as $mat) {
                    $mid = $mat['id_matiere'];
                    $me = null;
                    foreach ($el['matieres'] as $m_item) {
                        if ($m_item['id_matiere'] == $mid) { $me = $m_item; break; }
                    }
                    if ($me && isset($me['periodes'][$filtered_pid])) {
                        $p = $me['periodes'][$filtered_pid];
                        $p_note += ($p['tj'] ?? 0) + ($p['comp'] ?? 0) + ($p['ress'] ?? 0) + ($p['ex'] ?? 0);
                    }
                }
                $cd_p = (isset($el['points_conduite'][$filtered_pid]) ? $el['points_conduite'][$filtered_pid]['points'] : 60);
                $p_note += $cd_p;

                $p_max = 0;
                foreach ($matieres as $mat) {
                    $mid = $mat['id_matiere'];
                    $mx = $maxima[$mid][$filtered_pid] ?? ['tj' => 0, 'comp' => 0, 'ress' => 0, 'ex' => 0];
                    $p_max += ($mx['tj'] ?? 0) + ($mx['comp'] ?? 0) + ($mx['ress'] ?? 0) + ($mx['ex'] ?? 0);
                }
                $p_max += 60; // conduite max

                $el['sort_pourcentage'] = $p_max > 0 ? ($p_note / $p_max * 100) : 0;
            } else {
                // Pourcentage annuel complet
                $el['sort_pourcentage'] = $el['pourcentage'];
            }
        }
        unset($el);

        usort($eleves, function($a, $b) { return $b['sort_pourcentage'] <=> $a['sort_pourcentage']; });
        $rang = 1; $prev = -1;
        foreach ($eleves as $i => &$el) {
            if ($prev >= 0 && $el['sort_pourcentage'] < $prev) $rang = $i + 1;
            $el['rang'] = ($el['sort_pourcentage'] > 0) ? $rang : 0;
            $prev = $el['sort_pourcentage'];
        }
        unset($el);

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
        $annee_id = $this->id_annee_active;

        $eleves_db = $this->Model->readQuery("
            SELECT i.id_etudiant AS inscription_id, e.fullname, e.matricule
            FROM inscriptions i
            LEFT JOIN etudiants e ON e.id_etudiant = i.id_etudiant
            WHERE i.id_classe = ? AND i.id_annee = ? AND i.deleted_at IS NULL AND e.deleted_at IS NULL
            ORDER BY e.fullname ASC
        ", [$class_id, $annee_id]);

        if (empty($eleves_db)) {
            echo "<h3 style='font-family:Arial; text-align:center; margin-top:50px;'>Aucun élève trouvé pour cette classe.</h3>";
            return;
        }

        $eleves = [];
        foreach ($eleves_db as $e) {
            $eleves[$e['inscription_id']] = $e;
        }

        $annee_scolaire = $annee_id
            ? ($this->Model->readQuery('SELECT libelle FROM annees_scolaires WHERE id_annee = ?', [$annee_id])[0]['libelle'] ?? 'N/A')
            : 'N/A';

        $all_subjects = $this->Model->readQuery("
            SELECT m.id_matiere AS id, m.libelle AS name, m.code, m.est_general, m.est_actif, mc.note_max_matiere, mc.nb_heures_par_semaine
            FROM matieres_classes mc
            JOIN matieres m ON m.id_matiere = mc.id_matiere
            WHERE mc.id_classe = ? AND mc.deleted_at IS NULL AND m.deleted_at IS NULL
            ORDER BY m.est_general DESC, m.libelle
        ", [$class_id]);

        if (empty($all_subjects)) {
            echo "<h3 style='font-family:Arial; text-align:center; margin-top:50px;'>Aucune matière assignée à cette classe.</h3>";
            return;
        }

        // Séparer matières actives et inactives
        $subjects = [];
        $subjects_inactifs = [];
        foreach ($all_subjects as $s) {
            if (intval($s['est_actif']) === 0) {
                $subjects_inactifs[] = $s;
            } else {
                $subjects[] = $s;
            }
        }

        $all_subject_ids = array_column($all_subjects, 'id');
        $etudiant_ids = array_column($eleves_db, 'inscription_id');

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

        $etudiant_placeholders = !empty($etudiant_ids) ? str_repeat('?,', count($etudiant_ids) - 1).'?' : '?';
        $matiere_placeholders = !empty($all_subject_ids) ? str_repeat('?,', count($all_subject_ids) - 1).'?' : '?';

        $query = "
            SELECT 
                n.id_etudiant AS inscription_id,
                ev.id_matiere AS subject_id,
                
                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('interrogation', 'devoir') THEN n.note ELSE 0 END) AS note_t1_tj,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'competance' THEN n.note ELSE 0 END) AS note_t1_comp,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'ressource' THEN n.note ELSE 0 END) AS note_t1_ress,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'examen' THEN n.note ELSE 0 END) AS note_t1_ex,

                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('interrogation', 'devoir') THEN n.note ELSE 0 END) AS note_t2_tj,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'competance' THEN n.note ELSE 0 END) AS note_t2_comp,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'ressource' THEN n.note ELSE 0 END) AS note_t2_ress,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'examen' THEN n.note ELSE 0 END) AS note_t2_ex,

                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('interrogation', 'devoir') THEN n.note ELSE 0 END) AS note_t3_tj,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'competance' THEN n.note ELSE 0 END) AS note_t3_comp,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'ressource' THEN n.note ELSE 0 END) AS note_t3_ress,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'examen' THEN n.note ELSE 0 END) AS note_t3_ex,

                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('interrogation', 'devoir') THEN ev.ponderee_sur ELSE 0 END) AS max_t1_tj,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'competance' THEN ev.ponderee_sur ELSE 0 END) AS max_t1_comp,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'ressource' THEN ev.ponderee_sur ELSE 0 END) AS max_t1_ress,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'examen' THEN ev.ponderee_sur ELSE 0 END) AS max_t1_ex,

                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('interrogation', 'devoir') THEN ev.ponderee_sur ELSE 0 END) AS max_t2_tj,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'competance' THEN ev.ponderee_sur ELSE 0 END) AS max_t2_comp,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'ressource' THEN ev.ponderee_sur ELSE 0 END) AS max_t2_ress,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'examen' THEN ev.ponderee_sur ELSE 0 END) AS max_t2_ex,

                SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('interrogation', 'devoir') THEN ev.ponderee_sur ELSE 0 END) AS max_t3_tj,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'competance' THEN ev.ponderee_sur ELSE 0 END) AS max_t3_comp,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'ressource' THEN ev.ponderee_sur ELSE 0 END) AS max_t3_ress,
                SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'examen' THEN ev.ponderee_sur ELSE 0 END) AS max_t3_ex
            
            FROM notes n
            JOIN evaluations ev ON ev.id_evaluation = n.id_evaluation
            WHERE n.id_etudiant IN ({$etudiant_placeholders})
            AND ev.id_matiere IN ({$matiere_placeholders})
            AND ev.id_annee = ?
            AND n.deleted_at IS NULL AND ev.deleted_at IS NULL
            GROUP BY n.id_etudiant, ev.id_matiere
        ";

        $aggregated_data = $this->Model->readQuery($query, array_merge(
            [$p1, $p1, $p1, $p1, $p2, $p2, $p2, $p2, $p3, $p3, $p3, $p3,
             $p1, $p1, $p1, $p1, $p2, $p2, $p2, $p2, $p3, $p3, $p3, $p3],
            $etudiant_ids,
            $all_subject_ids,
            [$annee_id]
        ));

        $conduite_rows = $this->Model->readQuery("
            SELECT pc.id_etudiant, pc.id_periode, pc.points_initial, pc.points_retires
            FROM points_conduite pc
            WHERE pc.id_etudiant IN ({$etudiant_placeholders})
            AND pc.id_periode IN (?,?,?)
            AND pc.deleted_at IS NULL
        ", array_merge($etudiant_ids, [$p1, $p2, $p3]));
        $conduite_map = [];
        foreach ($conduite_rows as $c) {
            $pos = array_search($c['id_periode'], $periode_map, true);
            if ($pos) {
                $conduite_map[$c['id_etudiant']][$pos] = floatval($c['points_initial']) - floatval($c['points_retires']);
            }
        }

        $classe_info = $this->Model->readOne('classes', ['id_classe' => $class_id]);
        $classe_nom = $classe_info ? $classe_info['libelle'] : 'Classe';
        $section_info = $classe_info ? $this->Model->readOne('sections', ['id_section' => $classe_info['id_section']]) : null;
        $section_nom = $section_info ? $section_info['libelle'] : $classe_nom;

        // Détection dynamique des catégories basée sur les données réelles d'évaluations
        $categories = $this->BulletinsModel->_detecter_categories($class_id);
        $has_comp = $categories['competance'];
        $has_ress = $categories['ressource'];
        $has_ex   = $categories['examen'];
        $mode_b = $has_comp || $has_ress;
        $mode_a = $has_ex && !$mode_b;

        $data['competences_active'] = ($mode_b && $has_comp) ? 1 : 0;
        $data['ressources_active']  = ($mode_b && $has_ress) ? 1 : 0;
        $data['examen_active']      = $mode_a ? 1 : 0;
        $data['ressources_pourcentage'] = floatval($this->Model->get_setting('pourcentage_ressources_examen', 60));
        $data['competences_pourcentage'] = floatval($this->Model->get_setting('pourcentage_competences_examen', 40));
        $data['facteur_points_heure'] = floatval($this->Model->get_setting('facteur_points_heure', 15));

        $data['title'] = 'Bulletins de la classe ' . $classe_nom;
        $data['subjects'] = $subjects;
        $data['subjects_inactifs'] = $subjects_inactifs;
        $data['periodes'] = $periodes;
        $data['annee_scolaire'] = $annee_scolaire;
        $data['classe_nom'] = $classe_nom;
        $data['section_nom'] = $section_nom;
        $data['aggregated_data'] = $aggregated_data;
        $data['conduite_map'] = $conduite_map;
        $data['conduite_val'] = 60;

        $data['per_tots'] = [];
        $data['maxima_map'] = [];
        $data['rangs_periode'] = [];

        $pct_comp_val = floatval($this->Model->get_setting('pourcentage_competences_examen', 40));
        $pct_ress_val = floatval($this->Model->get_setting('pourcentage_ressources_examen', 60));

        foreach ($subjects as $subj) {
            $max_tj_subj = floatval($subj['note_max_matiere'] ?: 15);
            $max_comp_subj = round($max_tj_subj * $pct_comp_val / 100, 1);
            $max_ress_subj = round($max_tj_subj * $pct_ress_val / 100, 1);
            foreach ([1, 2, 3] as $pnum) {
                if ($mode_b) {
                    $data['maxima_map'][$subj['id']][$pnum] = ['tj' => $max_tj_subj, 'comp' => $max_comp_subj, 'ress' => $max_ress_subj, 'ex' => 0, 'tot' => $max_tj_subj + $max_comp_subj + $max_ress_subj];
                } elseif ($mode_a) {
                    $data['maxima_map'][$subj['id']][$pnum] = ['tj' => $max_tj_subj, 'comp' => 0, 'ress' => 0, 'ex' => $max_tj_subj, 'tot' => $max_tj_subj * 2];
                } else {
                    $data['maxima_map'][$subj['id']][$pnum] = ['tj' => $max_tj_subj, 'comp' => 0, 'ress' => 0, 'ex' => 0, 'tot' => $max_tj_subj];
                }
            }
        }

        $notes_map = [];
        foreach ($aggregated_data as $row) {
            $notes_map[$row['inscription_id']][$row['subject_id']] = $row;
        }

        foreach ($eleves as $eid => $el) {
            $stud_notes = $notes_map[$eid] ?? [];
            $cd = isset($conduite_map[$eid]) ? $conduite_map[$eid] : [];
            $per_tots_el = [];
            foreach ([1, 2, 3] as $pnum) {
                $pid = $periode_map[$pnum] ?? 0;
                $total = 0;
                if ($pid) {
                    foreach ($subjects as $subj) {
                        $sd = $stud_notes[$subj['id']] ?? null;
                        if ($sd) {
                            $tj = (float)$sd["note_t{$pnum}_tj"];
                            $comp = (float)$sd["note_t{$pnum}_comp"];
                            $ress = (float)$sd["note_t{$pnum}_ress"];
                            $ex = (float)$sd["note_t{$pnum}_ex"];
                            if ($mode_b) $ex = 0;
                            elseif ($mode_a) { $comp = 0; $ress = 0; }
                            else { $comp = 0; $ress = 0; $ex = 0; }
                            $total += $tj + $comp + $ress + $ex;
                        }
                    }
                    $cd_val = isset($cd[$pid]) ? $cd[$pid] : 60;
                    $total += $cd_val;
                }
                $per_tots_el[$pnum] = ['tot' => $total];
            }
            $data['per_tots'][$eid] = $per_tots_el;
        }

        foreach ([1, 2, 3] as $pnum) {
            $scores = [];
            foreach ($eleves as $eid => $el) {
                $stud_notes = $notes_map[$eid] ?? [];
                $cd = isset($conduite_map[$eid]) ? $conduite_map[$eid] : [];
                $pid = $periode_map[$pnum] ?? 0;
                $note_tot = 0;
                $max_tot = 0;
                if ($pid) {
                    foreach ($subjects as $subj) {
                        $sd = $stud_notes[$subj['id']] ?? null;
                        $mx = $data['maxima_map'][$subj['id']][$pnum] ?? ['tj'=>0,'comp'=>0,'ress'=>0,'ex'=>0,'tot'=>0];
                        if ($sd) {
                            $tj = (float)$sd["note_t{$pnum}_tj"];
                            $comp = (float)$sd["note_t{$pnum}_comp"];
                            $ress = (float)$sd["note_t{$pnum}_ress"];
                            $ex = (float)$sd["note_t{$pnum}_ex"];
                            if ($mode_b) $ex = 0;
                            elseif ($mode_a) { $comp = 0; $ress = 0; }
                            else { $comp = 0; $ress = 0; $ex = 0; }
                            $note_tot += $tj + $comp + $ress + $ex;
                        }
                        $max_tot += $mx['tot'];
                    }
                    $cd_val = isset($cd[$pid]) ? $cd[$pid] : 60;
                    $note_tot += $cd_val;
                    $max_tot += 60;
                }
                $pct = $max_tot > 0 ? ($note_tot / $max_tot * 100) : 0;
                $scores[$eid] = $pct;
            }
            arsort($scores);
            $rang = 1; $prev = -1;
            foreach ($scores as $eid => $pct) {
                if ($prev >= 0 && $pct < $prev) $rang++;
                $data['rangs_periode'][$pnum][$eid] = ($pct > 0) ? $rang : 0;
                $prev = $pct;
            }
        }

        // Rang annuel
        $ann_scores = [];
        $max_tot_annuel_all = 0;
        foreach ($subjects as $subj) {
            $mx = $data['maxima_map'][$subj['id']][1] ?? ['tot'=>0];
            $max_tot_annuel_all += $mx['tot'] * 3;
        }
        $max_tot_annuel_all += 60 * 3;
        foreach ($eleves as $eid => $el) {
            $stud_notes = $notes_map[$eid] ?? [];
            $cd = isset($conduite_map[$eid]) ? $conduite_map[$eid] : [];
            $note_ann = 0;
            foreach ([1,2,3] as $pnum) {
                $pid = $periode_map[$pnum] ?? 0;
                if ($pid) {
                    foreach ($subjects as $subj) {
                        $sd = $stud_notes[$subj['id']] ?? null;
                        if ($sd) {
                            $tj = (float)$sd["note_t{$pnum}_tj"];
                            $comp = (float)$sd["note_t{$pnum}_comp"];
                            $ress = (float)$sd["note_t{$pnum}_ress"];
                            $ex = (float)$sd["note_t{$pnum}_ex"];
                            if ($mode_b) $ex = 0;
                            elseif ($mode_a) { $comp = 0; $ress = 0; }
                            else { $comp = 0; $ress = 0; $ex = 0; }
                            $note_ann += $tj + $comp + $ress + $ex;
                        }
                    }
                    $cd_val = isset($cd[$pid]) ? $cd[$pid] : 60;
                    $note_ann += $cd_val;
                }
            }
            $ann_scores[$eid] = $max_tot_annuel_all > 0 ? ($note_ann / $max_tot_annuel_all * 100) : 0;
        }
        arsort($ann_scores);
        $rang = 1; $prev = -1;
        foreach ($ann_scores as $eid => $pct) {
            if ($prev >= 0 && $pct < $prev) $rang++;
            $eleves[$eid]['rang'] = ($pct > 0) ? $rang : 0;
            $eleves[$eid]['pourcentage'] = round($pct, 2);
            $prev = $pct;
        }

        $data['eleves'] = $eleves;

        $this->load->view('print_bulletins', $data);
    }
}
