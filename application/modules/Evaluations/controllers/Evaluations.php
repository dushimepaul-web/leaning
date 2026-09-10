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
        $this->db->select('ev.*, m.libelle as matiere, c.libelle as classe, p.libelle as periode, (SELECT COUNT(*) FROM notes nn WHERE nn.id_evaluation = ev.id_evaluation AND nn.deleted_at IS NULL) as note_count');
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
        $types_valides = ['interrogation','devoir','ressource','competance','examen'];
        $sur = (int)($data['ponderee_sur'] ?? 20);
        if ($sur <= 0 || $sur > 1000) { $this->json_error('Note maximale invalide'); return; }

        // Validation plafond ponderee_sur <= note_max_matiere (par groupe de type)
        $mc = $this->Model->readOne('matieres_classes', [
            'id_classe' => $data['id_classe'],
            'id_matiere' => $data['id_matiere'],
            'deleted_at' => null
        ]);
        if ($mc && $mc['note_max_matiere'] !== null && floatval($mc['note_max_matiere']) > 0) {
            $max_autorise = floatval($mc['note_max_matiere']);
            $newType = in_array($data['type'] ?? '', $types_valides) ? $data['type'] : 'devoir';

            // Groupe 1 : interrogation + devoir (TJ)
            $groupeTJ = ['interrogation', 'devoir'];
            // Groupe 2 : competance + ressource + examen
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

        $insert = [
            'uuid' => generate_uuid(),
            'libelle' => $data['libelle'],
            'id_classe' => $data['id_classe'],
            'id_matiere' => $data['id_matiere'],
            'id_periode' => $data['id_periode'],
            'id_annee' => $periode['id_annee'],
            'date_eval' => $data['date_evaluation'] ?? date('Y-m-d'),
            'type' => in_array($data['type'] ?? '', $types_valides) ? $data['type'] : 'devoir',
            'ponderee_sur' => $sur,
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
        $allowed = ['libelle','id_classe','id_matiere','id_periode','date_eval','ponderee_sur','type'];
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
        if (!empty($data['ponderee_sur'])) {
            $sur = (int)$data['ponderee_sur'];
            if ($sur <= 0 || $sur > 1000) { $this->json_error('Note maximale invalide'); return; }
            $update['ponderee_sur'] = $sur;
        }
        if (!empty($data['date_evaluation'])) $update['date_eval'] = $data['date_evaluation'];
        foreach ($allowed as $col) {
            if (isset($data[$col]) && $data[$col] !== '') $update[$col] = $data[$col];
        }
        if (isset($update['id_periode'])) {
            $periode = $this->Model->readOne('periodes', ['id_periode' => $update['id_periode']]);
            if ($periode) $update['id_annee'] = $periode['id_annee'];
        }

        // Validation plafond ponderee_sur <= note_max_matiere (par groupe de type)
        $check_classe = $update['id_classe'] ?? $existing['id_classe'];
        $check_matiere = $update['id_matiere'] ?? $existing['id_matiere'];
        $check_periode = $update['id_periode'] ?? $existing['id_periode'];
        $check_sur = $update['ponderee_sur'] ?? $existing['ponderee_sur'];
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

        $this->db->where('uuid', $id);
        if ($this->db->update('evaluations', $update))
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
        $this->db->where('uuid', $id);
        if ($this->db->update('evaluations', ['deleted_at' => $now]))
            $this->json_success(['notes_supprimees' => $count], $count > 0 ? 'Évaluation supprimée — ' . $count . ' note(s) effacée(s)' : 'Évaluation supprimée');
        else $this->json_error('Erreur');
    }
}