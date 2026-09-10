<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Evaluations extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des évaluations';
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['matieres'] = $this->Model->read('matieres', ['deleted_at' => null]);
        $data['enseignants'] = $this->Model->read('enseignants', ['deleted_at' => null]);
        $data['periodes'] = $this->Model->read('periodes', ['deleted_at' => null]);
        $this->load->view('evaluations', $data);
    }

    public function api_list() {
        $this->db->where('ev.deleted_at', null);
        $this->db->select('ev.*, mc.note_max_matiere, c.libelle as classe, m.libelle as matiere, p.libelle as periode, e.fullname as enseignant');
        $this->db->from('evaluations ev');
        $this->db->join('classes c', 'ev.id_classe = c.id_classe', 'left');
        $this->db->join('matieres m', 'ev.id_matiere = m.id_matiere', 'left');
        $this->db->join('periodes p', 'ev.id_periode = p.id_periode', 'left');
        $this->db->join('matieres_classes mc', 'ev.id_classe = mc.id_classe AND ev.id_matiere = mc.id_matiere AND mc.deleted_at IS NULL', 'left');
        $this->db->join('enseignants e', 'mc.id_enseignant = e.id_enseignant', 'left');
        $this->db->order_by('ev.date_eval', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle'])) { $this->json_error('Libellé obligatoire'); return; }
        foreach (['id_classe', 'id_matiere', 'id_periode'] as $f) {
            if (empty($data[$f])) { $this->json_error('Classe, matière et période obligatoires'); return; }
        }
        if (!$this->Model->readOne('classes', ['id_classe' => $data['id_classe'], 'deleted_at' => null])) { $this->json_error('Classe introuvable'); return; }
        if (!$this->Model->readOne('matieres', ['id_matiere' => $data['id_matiere'], 'deleted_at' => null])) { $this->json_error('Matière introuvable'); return; }
        if (!$this->Model->readOne('periodes', ['id_periode' => $data['id_periode'], 'deleted_at' => null])) { $this->json_error('Période introuvable'); return; }
        $allowed = ['libelle', 'type', 'ponderee_sur', 'date_eval', 'id_periode', 'id_classe', 'id_matiere'];
        $insert = array_intersect_key($data, array_flip($allowed));
        $insert['id_annee'] = $this->id_annee_active;
        $insert['date_eval'] = !empty($insert['date_eval']) ? $insert['date_eval'] : date('Y-m-d');
        $insert['type'] = !empty($insert['type']) ? $insert['type'] : 'devoir';
        $insert['ponderee_sur'] = !empty($insert['ponderee_sur']) ? $insert['ponderee_sur'] : 20.0;

        // Validation plafond ponderee_sur <= note_max_matiere (par groupe de type)
        $mc = $this->Model->readOne('matieres_classes', [
            'id_classe' => $data['id_classe'],
            'id_matiere' => $data['id_matiere'],
            'deleted_at' => null
        ]);
        if ($mc && $mc['note_max_matiere'] !== null && floatval($mc['note_max_matiere']) > 0) {
            $max_autorise = floatval($mc['note_max_matiere']);
            $sur = floatval($insert['ponderee_sur']);
            $newType = $insert['type'];
            $groupeTJ = ['interrogation', 'devoir'];
            $groupeCompRessEx = ['competance', 'ressource', 'examen'];

            if (in_array($newType, $groupeTJ)) {
                $row = $this->db->query("
                    SELECT COALESCE(SUM(ev.ponderee_sur), 0) AS total_sur
                    FROM evaluations ev
                    WHERE ev.id_classe = ? AND ev.id_matiere = ? AND ev.id_periode = ?
                    AND ev.type IN ('interrogation','devoir') AND ev.deleted_at IS NULL
                ", [$data['id_classe'], $data['id_matiere'], $data['id_periode']])->row_array();
                $total_actuel = floatval($row['total_sur']);
                $nouveau_total = $total_actuel + $sur;
                if ($nouveau_total > $max_autorise) {
                    $depassement = $nouveau_total - $max_autorise;
                    $this->json_error("Dépassement plafond TJ : total actuel = {$total_actuel}, + nouvelle = {$sur}, total = {$nouveau_total}, plafond = {$max_autorise} (dépassement de {$depassement})");
                    return;
                }
            } elseif (in_array($newType, $groupeCompRessEx)) {
                $row = $this->db->query("
                    SELECT COALESCE(SUM(ev.ponderee_sur), 0) AS total_sur
                    FROM evaluations ev
                    WHERE ev.id_classe = ? AND ev.id_matiere = ? AND ev.id_periode = ?
                    AND ev.type IN ('competance','ressource','examen') AND ev.deleted_at IS NULL
                ", [$data['id_classe'], $data['id_matiere'], $data['id_periode']])->row_array();
                $total_actuel = floatval($row['total_sur']);
                $nouveau_total = $total_actuel + $sur;
                if ($nouveau_total > $max_autorise) {
                    $depassement = $nouveau_total - $max_autorise;
                    $this->json_error("Dépassement plafond Comp/Ress/Examen : total actuel = {$total_actuel}, + nouvelle = {$sur}, total = {$nouveau_total}, plafond = {$max_autorise} (dépassement de {$depassement})");
                    return;
                }
            }
        }

        $id = $this->Model->createLastId('evaluations', $insert);
        if ($id) $this->json_success(['id_evaluation' => $id], 'Évaluation créée');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $existing = $this->Model->readOne('evaluations', ['uuid' => $id, 'deleted_at' => null]);
        if (!$existing) { $this->json_error('Évaluation introuvable', 404); return; }
        $allowed = ['libelle', 'type', 'ponderee_sur', 'date_eval', 'id_periode', 'id_classe', 'id_matiere'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée'); return; }

        // Validation plafond ponderee_sur <= note_max_matiere (par groupe de type)
        $check_classe = $update['id_classe'] ?? $existing['id_classe'];
        $check_matiere = $update['id_matiere'] ?? $existing['id_matiere'];
        $check_periode = $update['id_periode'] ?? $existing['id_periode'];
        $check_sur = isset($update['ponderee_sur']) ? floatval($update['ponderee_sur']) : floatval($existing['ponderee_sur']);
        $check_type = $update['type'] ?? $existing['type'];
        $mc = $this->Model->readOne('matieres_classes', [
            'id_classe' => $check_classe,
            'id_matiere' => $check_matiere,
            'deleted_at' => null
        ]);
        if ($mc && $mc['note_max_matiere'] !== null && floatval($mc['note_max_matiere']) > 0) {
            $max_autorise = floatval($mc['note_max_matiere']);
            $groupeTJ = ['interrogation', 'devoir'];
            $groupeCompRessEx = ['competance', 'ressource', 'examen'];

            if (in_array($check_type, $groupeTJ)) {
                $row = $this->db->query("
                    SELECT COALESCE(SUM(ev.ponderee_sur), 0) AS total_sur
                    FROM evaluations ev
                    WHERE ev.id_classe = ? AND ev.id_matiere = ? AND ev.id_periode = ?
                    AND ev.type IN ('interrogation','devoir') AND ev.id_evaluation != ? AND ev.deleted_at IS NULL
                ", [$check_classe, $check_matiere, $check_periode, $existing['id_evaluation']])->row_array();
                $total_actuel = floatval($row['total_sur']);
                $nouveau_total = $total_actuel + $check_sur;
                if ($nouveau_total > $max_autorise) {
                    $depassement = $nouveau_total - $max_autorise;
                    $this->json_error("Dépassement plafond TJ : total actuel = {$total_actuel}, + nouvelle = {$check_sur}, total = {$nouveau_total}, plafond = {$max_autorise} (dépassement de {$depassement})");
                    return;
                }
            } elseif (in_array($check_type, $groupeCompRessEx)) {
                $row = $this->db->query("
                    SELECT COALESCE(SUM(ev.ponderee_sur), 0) AS total_sur
                    FROM evaluations ev
                    WHERE ev.id_classe = ? AND ev.id_matiere = ? AND ev.id_periode = ?
                    AND ev.type IN ('competance','ressource','examen') AND ev.id_evaluation != ? AND ev.deleted_at IS NULL
                ", [$check_classe, $check_matiere, $check_periode, $existing['id_evaluation']])->row_array();
                $total_actuel = floatval($row['total_sur']);
                $nouveau_total = $total_actuel + $check_sur;
                if ($nouveau_total > $max_autorise) {
                    $depassement = $nouveau_total - $max_autorise;
                    $this->json_error("Dépassement plafond Comp/Ress/Examen : total actuel = {$total_actuel}, + nouvelle = {$check_sur}, total = {$nouveau_total}, plafond = {$max_autorise} (dépassement de {$depassement})");
                    return;
                }
            }
        }

        if ($this->Model->update('evaluations', ['uuid' => $id], $update))
            $this->json_success(null, 'Évaluation mise à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        $ev = $this->Model->readOne('evaluations', ['uuid' => $id]);
        if (!$ev) { $this->json_error('Évaluation introuvable', 404); return; }
        $now = date('Y-m-d H:i:s');
        $count = (int)$this->db->where('id_evaluation', $ev['id_evaluation'])->where('deleted_at', null)->count_all_results('notes');
        $this->db->where('id_evaluation', $ev['id_evaluation'])->where('deleted_at', null);
        $this->db->update('notes', ['deleted_at' => $now]);
        if ($this->Model->update('evaluations', ['uuid' => $id], ['deleted_at' => $now]))
            $this->json_success(['notes_supprimees' => $count], $count > 0 ? 'Évaluation supprimée — ' . $count . ' note(s) effacée(s)' : 'Évaluation supprimée');
        else $this->json_error('Erreur');
    }
}
