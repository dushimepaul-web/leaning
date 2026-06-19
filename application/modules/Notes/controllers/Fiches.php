<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fiches extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Fiches de points';
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['periodes'] = $this->Model->read('periodes', ['deleted_at' => null, 'id_annee' => $this->id_annee_active]);
        $data['annees'] = $this->Model->read('annees_scolaires', ['deleted_at' => null]);
        $this->load->view('fiches', $data);
    }

    public function api_fiche($id_classe = null) {
        $id_classe = $id_classe ?: $this->input->get('classe');
        $id_periode = $this->input->get('periode') ?: $this->id_periode_active;
        $id_annee = $this->input->get('annee') ?: $this->id_annee_active;

        if (!$id_classe || !$id_periode) { $this->json_error('Classe et période requis'); return; }

        $students = $this->db
            ->select('e.id_etudiant, e.matricule, e.nom, e.prenom, e.sexe, e.photo')
            ->from('inscriptions i')
            ->join('etudiants e', 'i.id_etudiant = e.id_etudiant')
            ->where('i.id_classe', $id_classe)->where('i.id_annee', $id_annee)->where('i.deleted_at', null)->where('e.deleted_at', null)
            ->order_by('e.nom, e.prenom')->get()->result_array();

        $evaluations = $this->db
            ->select('ev.*, m.libelle as matiere')
            ->from('evaluations ev')
            ->join('matieres m', 'ev.id_matiere = m.id_matiere', 'left')
            ->where('ev.id_classe', $id_classe)->where('ev.id_periode', $id_periode)->where('ev.deleted_at', null)
            ->order_by('ev.date_eval')->get()->result_array();

        $evalIds = array_column($evaluations, 'id_evaluation');
        $notes = empty($evalIds) ? [] : $this->db
            ->where_in('id_evaluation', $evalIds)->where('deleted_at', null)
            ->get('notes')->result_array();

        $notesByStudent = [];
        foreach ($notes as $n) { $notesByStudent[$n['id_etudiant']][$n['id_evaluation']] = floatval($n['note']); }

        $thresholds = $this->_getThresholds();
        $decisionRules = $this->_getDecisionRules();

        $result = [];
        foreach ($students as $s) {
            $row = ['etudiant' => $s, 'notes' => [], 'total' => 0, 'count' => 0, 'moyenne' => 0, 'rang' => 0, 'appreciation' => '', 'decision' => ''];
            $sum = 0; $cnt = 0; $coeffSum = 0;
            foreach ($evaluations as $ev) {
                $note = isset($notesByStudent[$s['id_etudiant']][$ev['id_evaluation']]) ? $notesByStudent[$s['id_etudiant']][$ev['id_evaluation']] : null;
                $row['notes'][$ev['id_evaluation']] = $note;
                if ($note !== null) {
                    $coeff = floatval($ev['coefficient']);
                    $sum += $note * $coeff; $coeffSum += $coeff; $cnt++;
                }
            }
            $row['total'] = $cnt ? round($sum, 2) : 0;
            $row['moyenne'] = $coeffSum > 0 ? round($sum / $coeffSum, 2) : 0;
            $row['count'] = $cnt;
            $row['appreciation'] = $this->_appreciation($row['moyenne'], $thresholds);
            $row['decision'] = $this->_decision($row['moyenne'], $cnt, $decisionRules);
            $result[] = $row;
        }

        // Rangs
        usort($result, function($a, $b) { return $b['moyenne'] <=> $a['moyenne']; });
        $rang = 1; $prev = null;
        foreach ($result as &$r) {
            if ($prev !== null && $r['moyenne'] < $prev) $rang++;
            $r['rang'] = $rang;
            $prev = $r['moyenne'];
        }
        usort($result, function($a, $b) { return strcasecmp($a['etudiant']['nom'].$a['etudiant']['prenom'], $b['etudiant']['nom'].$b['etudiant']['prenom']); });

        // Stats
        $moyennes = array_column($result, 'moyenne');
        $stats = [
            'nb_eleves' => count($students),
            'nb_evaluations' => count($evaluations),
            'meilleure' => count($moyennes) ? max($moyennes) : 0,
            'faible' => count($moyennes) ? min($moyennes) : 0,
            'moyenne_classe' => count($moyennes) ? round(array_sum($moyennes) / count($moyennes), 2) : 0,
            'taux_reussite' => count($moyennes) ? round(count(array_filter($moyennes, function($m){return $m>=10;})) / count($moyennes) * 100, 1) : 0,
        ];

        $this->json_success(['students' => $result, 'evaluations' => $evaluations, 'stats' => $stats]);
    }

    public function api_fiche_par_cours($id_classe = null) {
        $id_classe = $id_classe ?: $this->input->get('classe');
        $id_periode = $this->input->get('periode'); // 'all' pour année entière
        $id_annee = $this->input->get('annee') ?: $this->id_annee_active;

        if (!$id_classe) { $this->json_error('Classe requise'); return; }

        // Récupérer les élèves
        $students = $this->db
            ->select('e.id_etudiant, e.matricule, e.nom, e.prenom')
            ->from('inscriptions i')
            ->join('etudiants e', 'i.id_etudiant = e.id_etudiant')
            ->where('i.id_classe', $id_classe)->where('i.id_annee', $id_annee)->where('i.deleted_at', null)->where('e.deleted_at', null)
            ->order_by('e.nom, e.prenom')->get()->result_array();

        // Récupérer les matières de la classe
        $matieres = $this->db->query(
            "SELECT DISTINCT m.id_matiere, m.libelle, m.code FROM matieres_classes mc JOIN matieres m ON mc.id_matiere = m.id_matiere WHERE mc.id_classe = ? AND mc.deleted_at IS NULL UNION SELECT DISTINCT m.id_matiere, m.libelle, m.code FROM enseignements e JOIN matieres m ON e.id_matiere = m.id_matiere WHERE e.id_classe = ? AND e.deleted_at IS NULL ORDER BY libelle",
            [$id_classe, $id_classe]
        )->result_array();

        if ($id_periode && $id_periode !== 'all') {
            // Période unique
            $cours = $this->_buildCoursForPeriod($id_classe, $id_periode, $id_annee, $matieres);
            $allEvalIds = [];
            foreach ($cours as $c) {
                foreach ($c['devoirs'] as $ev) $allEvalIds[] = $ev['id_evaluation'];
                foreach ($c['examens'] as $ev) $allEvalIds[] = $ev['id_evaluation'];
            }
            $notes = $this->_getNotesForEvals($allEvalIds, $students);
            $periodes = [$this->Model->readOne('periodes', ['id_periode' => $id_periode])];
            $notesByStudent = $notes;
            $allCours = [['periode_id' => $id_periode, 'cours' => $cours]];
        } else {
            // Année : tous les trimestres
            $allPeriodes = $this->db->where('id_annee', $id_annee)->where('deleted_at', null)->order_by('id_periode')->get('periodes')->result_array();
            $allCours = [];
            $allEvalIds = [];
            foreach ($allPeriodes as $periode) {
                $cours = $this->_buildCoursForPeriod($id_classe, $periode['id_periode'], $id_annee, $matieres);
                foreach ($cours as $c) {
                    foreach ($c['devoirs'] as $ev) $allEvalIds[] = $ev['id_evaluation'];
                    foreach ($c['examens'] as $ev) $allEvalIds[] = $ev['id_evaluation'];
                }
                $allCours[] = ['periode_id' => $periode['id_periode'], 'periode_libelle' => $periode['libelle'], 'cours' => $cours];
            }
            $notes = $this->_getNotesForEvals($allEvalIds, $students);
            $periodes = $allPeriodes;
            $notesByStudent = $notes;
        }

        // Calculer totaux, moyennes par élève
        $result = [];
        foreach ($students as $s) {
            $row = ['etudiant' => $s, 'cours' => [], 'total' => 0, 'total_sur' => 0, 'nb_notes' => 0];
            $grandTotal = 0; $grandCoef = 0;
            foreach ($allCours as $pc) {
                foreach ($pc['cours'] as $coursItem) {
                    $devNote = null; $devCoef = 1; $devCount = 0; $devSum = 0; $devMax = 0;
                    $examNote = null; $examCoef = 1; $examMax = 0;
                    foreach ($coursItem['devoirs'] as $ev) {
                        $noteVal = isset($notesByStudent[$s['id_etudiant']][$ev['id_evaluation']]) ? $notesByStudent[$s['id_etudiant']][$ev['id_evaluation']] : null;
                        if ($noteVal !== null) { $devSum += $noteVal * $ev['coefficient']; $devCount += $ev['coefficient']; $devMax += $ev['sur']; }
                    }
                    foreach ($coursItem['examens'] as $ev) {
                        $noteVal = isset($notesByStudent[$s['id_etudiant']][$ev['id_evaluation']]) ? $notesByStudent[$s['id_etudiant']][$ev['id_evaluation']] : null;
                        if ($noteVal !== null) { $examNote = $noteVal; $examCoef = $ev['coefficient']; $examMax = $ev['sur']; }
                    }
                    $devAvg = $devCount > 0 ? round($devSum / $devCount, 2) : null;
                    $courseData = [
                        'id_matiere' => $coursItem['id_matiere'],
                        'dev_note' => $devAvg,
                        'dev_max' => $devMax,
                        'exam_note' => $examNote,
                        'exam_max' => $examMax,
                    ];
                    // Calcul note du cours (moyenne devoirs+examen)
                    if ($devAvg !== null && $examNote !== null) {
                        $courseData['cours_note'] = round(($devAvg + $examNote) / 2, 2);
                        $courseData['cours_max'] = round(($devMax + $examMax) / 2, 2) ?: 20;
                        $grandTotal += $courseData['cours_note']; $grandCoef++;
                    } elseif ($devAvg !== null) {
                        $courseData['cours_note'] = $devAvg;
                        $courseData['cours_max'] = $devMax ?: 20;
                        $grandTotal += $devAvg; $grandCoef++;
                    } elseif ($examNote !== null) {
                        $courseData['cours_note'] = $examNote;
                        $courseData['cours_max'] = $examMax ?: 20;
                        $grandTotal += $examNote; $grandCoef++;
                    } else {
                        $courseData['cours_note'] = null;
                        $courseData['cours_max'] = null;
                    }
                    $row['cours'][] = $courseData;
                }
            }
            $row['total'] = $grandTotal;
            $row['moyenne'] = $grandCoef > 0 ? round($grandTotal / $grandCoef, 2) : 0;
            $row['pourcentage'] = $grandCoef > 0 ? round(($grandTotal / ($grandCoef * 20)) * 100, 1) : 0;
            $result[] = $row;
        }

        $classe = $this->Model->readOne('classes', ['id_classe' => $id_classe]);
        $this->json_success([
            'classe' => $classe ? $classe['libelle'] : '',
            'eleves' => $result,
            'periodes' => $allCours,
            'type' => ($id_periode && $id_periode !== 'all') ? 'periode' : 'annee',
        ]);
    }

    private function _buildCoursForPeriod($id_classe, $id_periode, $id_annee, $matieres) {
        $cours = [];
        foreach ($matieres as $mat) {
            $devoirs = $this->db
                ->where('id_classe', $id_classe)->where('id_matiere', $mat['id_matiere'])
                ->where('id_periode', $id_periode)->where('deleted_at', null)
                ->where("(type != 'examen' OR type IS NULL)")
                ->order_by('date_eval')->get('evaluations')->result_array();
            $examens = $this->db
                ->where('id_classe', $id_classe)->where('id_matiere', $mat['id_matiere'])
                ->where('id_periode', $id_periode)->where('deleted_at', null)
                ->where('type', 'examen')
                ->order_by('date_eval')->get('evaluations')->result_array();
            if (!empty($devoirs) || !empty($examens)) {
                $devoirsClean = array_map(function($ev) { return ['id_evaluation' => $ev['id_evaluation'], 'libelle' => $ev['libelle'], 'sur' => $ev['sur'], 'coefficient' => $ev['coefficient'], 'type' => $ev['type']]; }, $devoirs);
                $examensClean = array_map(function($ev) { return ['id_evaluation' => $ev['id_evaluation'], 'libelle' => $ev['libelle'], 'sur' => $ev['sur'], 'coefficient' => $ev['coefficient'], 'type' => $ev['type']]; }, $examens);
                $cours[] = ['id_matiere' => $mat['id_matiere'], 'libelle' => $mat['libelle'], 'code' => $mat['code'] ?? '', 'devoirs' => $devoirsClean, 'examens' => $examensClean];
            }
        }
        return $cours;
    }

    private function _getNotesForEvals($evalIds, $students) {
        if (empty($evalIds)) return [];
        $studentIds = array_column($students, 'id_etudiant');
        $notes = $this->db->where_in('id_evaluation', $evalIds)->where_in('id_etudiant', $studentIds)->where('deleted_at', null)->get('notes')->result_array();
        $out = [];
        foreach ($notes as $n) { $out[$n['id_etudiant']][$n['id_evaluation']] = floatval($n['note']); }
        return $out;
    }

    private function _getThresholds() {
        $keys = ['seuil_excellent','seuil_tres_bien','seuil_bien','seuil_assez_bien','seuil_passable'];
        $appKeys = ['appreciation_excellent','appreciation_tres_bien','appreciation_bien','appreciation_assez_bien','appreciation_passable','appreciation_insuffisant'];
        $th = [];
        foreach ($keys as $k) $th[$k] = floatval($this->Model->get_setting($k, 0));
        foreach ($appKeys as $k) $th[$k] = $this->Model->get_setting($k, '');
        if (!$th['seuil_excellent']) { $th['seuil_excellent']=18; $th['appreciation_excellent']='Excellent'; }
        if (!$th['seuil_tres_bien']) { $th['seuil_tres_bien']=16; $th['appreciation_tres_bien']='Très Bien'; }
        if (!$th['seuil_bien']) { $th['seuil_bien']=14; $th['appreciation_bien']='Bien'; }
        if (!$th['seuil_assez_bien']) { $th['seuil_assez_bien']=12; $th['appreciation_assez_bien']='Assez Bien'; }
        if (!$th['seuil_passable']) { $th['seuil_passable']=10; $th['appreciation_passable']='Passable'; }
        if (!$th['appreciation_insuffisant']) $th['appreciation_insuffisant']='Insuffisant';
        return $th;
    }

    private function _getDecisionRules() {
        return [
            'admis' => floatval($this->Model->get_setting('regle_admis_moy', 12)),
            'ajourne' => floatval($this->Model->get_setting('regle_ajourne_moy', 10)),
        ];
    }

    private function _appreciation($moy, $th) {
        if ($moy >= $th['seuil_excellent'] && $moy > 0) return $th['appreciation_excellent'];
        if ($moy >= $th['seuil_tres_bien']) return $th['appreciation_tres_bien'];
        if ($moy >= $th['seuil_bien']) return $th['appreciation_bien'];
        if ($moy >= $th['seuil_assez_bien']) return $th['appreciation_assez_bien'];
        if ($moy >= $th['seuil_passable']) return $th['appreciation_passable'];
        return $th['appreciation_insuffisant'];
    }

    private function _decision($moy, $cnt, $rules) {
        if ($cnt == 0) return 'Sans notes';
        if ($moy >= $rules['admis']) return 'Admis';
        if ($moy >= $rules['ajourne']) return 'Ajourné';
        return 'Échoué';
    }
}
