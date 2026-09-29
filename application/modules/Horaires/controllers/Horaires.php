<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Module Horaires — 100% PHP natif.
 * Aucune dépendance externe (Python supprimé) : rendu des vues + API + moteur
 * de génération d'emplois du temps (appariement bipartite avec retour en
 * arrière, heuristique gloutonne + réparation par swaps).
 */
class Horaires extends MY_Controller {

    /** Moteur de résolution (bibliothèque partagée avec tools/bench_horaires.php) */
    private $solver = null;

    public function __construct() {
        parent::__construct();
        $this->load->model('Horaires_model');
    }

    // ═══════════════════════════════════════════════════════════════
    // VUES
    // ═══════════════════════════════════════════════════════════════

    public function index() {
        $params = $this->_params();
        $data = [
            'classes'           => $this->_classes(),
            'creneaux'          => $this->Horaires_model->get_creneaux_cours(),
            'creneaux_mardi'    => $this->Horaires_model->get_creneaux_mardi(),
            'jours'             => $this->_jours(),
            'jour_special'      => $params['jour_special'] ?? 'mardi',
            'jour_special_actif' => (string)($params['jour_special_actif'] ?? '1'),
            'annee_label'       => $this->_annee_label(),
        ];
        $this->load->view('index', $data);
    }

    public function fixes() {
        $data = [
            'title'       => 'Sessions Fixes',
            'annee_label' => $this->_annee_label(),
            'id_annee'    => $this->id_annee_active,
            'classes'     => $this->_classes(),
            'creneaux'    => $this->Horaires_model->get_creneaux_cours(),
            'jours'       => $this->_jours(),
            'enseignants' => $this->db->where('deleted_at', null)->order_by('fullname ASC')->get('enseignants')->result_array(),
        ];
        $this->load->view('fixes', $data);
    }

    // ═══════════════════════════════════════════════════════════════
    // API — LISTE / CRUD
    // ═══════════════════════════════════════════════════════════════

    public function api_list() {
        $rows = $this->db
            ->select('h.id_horaire, h.uuid, h.id_enseignement, h.id_matiere, h.id_enseignant,
                      h.id_classe, h.id_creneau, h.id_jour, h.type, h.id_generation')
            ->select('m.libelle AS matiere_libelle, m.code AS matiere_code')
            ->select('c.libelle AS classe_libelle')
            ->select('j.libelle AS jour_libelle, j.code AS jour_code, j.ordre AS jour_ordre')
            ->select('e.fullname AS enseignant')
            ->from('horaires h')
            ->join('matieres m', 'h.id_matiere = m.id_matiere', 'left')
            ->join('classes c', 'h.id_classe = c.id_classe', 'left')
            ->join('jours_semaine j', 'h.id_jour = j.id_jour', 'left')
            ->join('enseignants e', 'h.id_enseignant = e.id_enseignant', 'left')
            ->where('h.deleted_at', null)
            ->order_by('h.id_classe ASC, h.id_jour ASC, h.id_creneau ASC')
            ->get()
            ->result_array();
        $this->json_success($rows);
    }

    public function api_get($id) {
        $row = $this->db
            ->select('h.*, m.libelle AS matiere_libelle, m.code AS matiere_code,
                      c.libelle AS classe_libelle, j.libelle AS jour_libelle, e.fullname AS enseignant')
            ->from('horaires h')
            ->join('matieres m', 'h.id_matiere = m.id_matiere', 'left')
            ->join('classes c', 'h.id_classe = c.id_classe', 'left')
            ->join('jours_semaine j', 'h.id_jour = j.id_jour', 'left')
            ->join('enseignants e', 'h.id_enseignant = e.id_enseignant', 'left')
            ->where('h.deleted_at', null)
            ->where('h.id_horaire', $id)
            ->or_where('h.uuid', $id)
            ->get()
            ->row_array();
        if (!$row) {
            $this->json_error('Horaire introuvable', 404);
        }
        $this->json_success($row);
    }

    public function api_create() {
        $in = $this->get_json_input() ?: [];
        $required = ['id_classe', 'id_matiere', 'id_enseignement', 'id_enseignant', 'id_jour', 'id_creneau'];
        foreach ($required as $f) {
            if (empty($in[$f])) {
                $this->json_error('Champ obligatoire manquant : ' . $f, 422);
            }
        }
        if ($this->_slot_taken((int)$in['id_classe'], (int)$in['id_jour'], (int)$in['id_creneau'])) {
            $this->json_error('Ce créneau est déjà occupé pour cette classe.', 409);
        }
        if ($this->_teacher_slot_taken((int)$in['id_enseignant'], (int)$in['id_jour'], (int)$in['id_creneau'])) {
            $this->json_error('Cet enseignant est déjà occupé sur ce créneau.', 409);
        }
        $data = [
            'uuid'            => generate_uuid(),
            'id_generation'   => (int)($in['id_generation'] ?? $this->_last_generation_id()),
            'id_enseignement' => (int)$in['id_enseignement'],
            'id_matiere'      => (int)$in['id_matiere'],
            'id_enseignant'   => (int)$in['id_enseignant'],
            'id_classe'       => (int)$in['id_classe'],
            'id_creneau'      => (int)$in['id_creneau'],
            'id_jour'         => (int)$in['id_jour'],
            'type'            => ($in['type'] ?? 'auto') === 'fixe' ? 'fixe' : 'auto',
        ];
        $this->db->insert('horaires', $data);
        $this->json_success(['id' => $this->db->insert_id()], 'Horaire créé');
    }

    public function api_update($id) {
        $in = $this->get_json_input() ?: [];
        $allowed = ['id_matiere', 'id_enseignant', 'id_enseignement', 'id_jour', 'id_creneau', 'type'];
        $data = array_intersect_key($in, array_flip($allowed));
        if (!$data) {
            $this->json_error('Aucune donnée à mettre à jour', 422);
        }
        $this->db->where('id_horaire', $id)->or_where('uuid', $id)->update('horaires', $data);
        $this->json_success(null, 'Horaire mis à jour');
    }

    public function api_delete($id) {
        $this->db->where('id_horaire', $id)->or_where('uuid', $id)
            ->update('horaires', ['deleted_at' => date('Y-m-d H:i:s')]);
        $this->json_success(null, 'Horaire supprimé');
    }

    // ═══════════════════════════════════════════════════════════════
    // API — SESSIONS FIXES
    // ═══════════════════════════════════════════════════════════════

    public function api_fixes_list() {
        $rows = $this->db
            ->select('f.uuid, f.id_annee, f.id_classe, f.id_matiere_classe, f.id_enseignant,
                      f.id_jour, f.id_creneau, mc.id_matiere')
            ->select('c.libelle AS classe_libelle')
            ->select('m.libelle AS matiere_libelle, m.code AS matiere_code')
            ->select('j.libelle AS jour_libelle')
            ->select('e.fullname AS enseignant')
            ->from('horaires_fixes f')
            ->join('matieres_classes mc', 'f.id_matiere_classe = mc.id_matiere_classe', 'left')
            ->join('matieres m', 'mc.id_matiere = m.id_matiere', 'left')
            ->join('classes c', 'f.id_classe = c.id_classe', 'left')
            ->join('jours_semaine j', 'f.id_jour = j.id_jour', 'left')
            ->join('enseignants e', 'f.id_enseignant = e.id_enseignant', 'left')
            ->where('f.deleted_at', null)
            ->order_by('f.id_classe ASC, f.id_jour ASC, f.id_creneau ASC')
            ->get()
            ->result_array();
        $this->json_success($rows);
    }

    public function api_fixes_create() {
        $in = $this->get_json_input() ?: [];
        foreach (['id_classe', 'id_matiere_classe', 'id_enseignant', 'id_jour', 'id_creneau'] as $f) {
            if (empty($in[$f])) {
                $this->json_error('Champ obligatoire manquant : ' . $f, 422);
            }
        }
        $id_classe = (int)$in['id_classe'];
        $id_jour   = (int)$in['id_jour'];
        $id_creneau = (int)$in['id_creneau'];
        $id_enseignant = (int)$in['id_enseignant'];

        if (!$this->_creneau_valide($id_jour, $id_creneau)) {
            $this->json_error('Créneau invalide pour ce jour.', 422);
        }
        if ($this->_fix_slot_taken($id_classe, $id_jour, $id_creneau)) {
            $this->json_error('Cette classe a déjà une session fixe sur ce créneau.', 409);
        }
        if ($this->_fix_teacher_slot_taken($id_enseignant, $id_jour, $id_creneau)) {
            $this->json_error('Cet enseignant a déjà une session fixe sur ce créneau.', 409);
        }
        if ($this->_teacher_indispo($id_enseignant, $id_jour, $id_creneau)) {
            $this->json_error('Cet enseignant est indisponible sur ce créneau.', 409);
        }

        $mc = $this->db->where('id_matiere_classe', $in['id_matiere_classe'])->where('deleted_at', null)
            ->get('matieres_classes')->row_array();
        if (!$mc) {
            $this->json_error('Matière/classe introuvable', 404);
        }
        if ((int)$mc['id_classe'] !== $id_classe) {
            $this->json_error('Cette matière n\\appartient pas à cette classe.', 422);
        }

        $this->db->insert('horaires_fixes', [
            'uuid'             => generate_uuid(),
            'id_annee'         => $this->id_annee_active,
            'id_classe'        => $id_classe,
            'id_matiere_classe' => (int)$in['id_matiere_classe'],
            'id_enseignant'    => $id_enseignant,
            'id_jour'          => $id_jour,
            'id_creneau'       => $id_creneau,
        ]);
        $this->json_success(['uuid' => $this->db->insert_id()], 'Session fixe créée');
    }

    public function api_fixes_delete($uuid) {
        $this->db->where('uuid', $uuid)->or_where('id_horaire_fixe', $uuid)
            ->update('horaires_fixes', ['deleted_at' => date('Y-m-d H:i:s')]);
        $this->json_success(null, 'Session fixe supprimée');
    }

    public function api_fixes_clear() {
        $this->db->where('deleted_at', null)
            ->update('horaires_fixes', ['deleted_at' => date('Y-m-d H:i:s')]);
        $this->json_success(null, 'Toutes les sessions fixes ont été vidées');
    }

    public function api_matieres_by_classe($id_classe) {
        $rows = $this->db
            ->select('mc.id_matiere_classe, mc.id_matiere, mc.id_classe, mc.id_enseignant,
                      mc.nb_heures_par_semaine, mc.nb_heures_par_jour')
            ->select('m.libelle AS matiere_libelle, m.code AS matiere_code')
            ->select('e.fullname AS enseignant')
            ->from('matieres_classes mc')
            ->join('matieres m', 'mc.id_matiere = m.id_matiere', 'left')
            ->join('enseignants e', 'mc.id_enseignant = e.id_enseignant', 'left')
            ->where('mc.id_classe', $id_classe)
            ->where('mc.deleted_at', null)
            ->order_by('m.libelle ASC')
            ->get()
            ->result_array();
        $this->json_success($rows);
    }

    public function api_enseignant_by_classe_matiere($id_classe, $id_matiere) {
        $row = $this->db
            ->select('mc.id_enseignant, e.fullname AS enseignant, ens.id_enseignement')
            ->from('matieres_classes mc')
            ->join('enseignants e', 'mc.id_enseignant = e.id_enseignant', 'left')
            ->join('enseignements ens', 'ens.id_matiere_classe = mc.id_matiere_classe', 'left')
            ->where('mc.id_classe', $id_classe)
            ->where('mc.id_matiere', $id_matiere)
            ->where('mc.deleted_at', null)
            ->get()
            ->row_array();
        $this->json_success($row ?: []);
    }

    public function api_generations() {
        $rows = $this->db
            ->select('g.*, a.libelle AS annee_libelle')
            ->from('horaires_generations g')
            ->join('annees_scolaires a', 'g.id_annee = a.id_annee', 'left')
            ->where('g.deleted_at', null)
            ->order_by('g.id_generation DESC')
            ->limit(50)
            ->get()
            ->result_array();
        $this->json_success($rows);
    }

    // ═══════════════════════════════════════════════════════════════
    // API — DIAGNOSTIC
    // ═══════════════════════════════════════════════════════════════

    public function api_diagnostiquer() {
        $ctx = $this->_context();
        if (!$ctx['sessions'] && !$ctx['fix_rows']) {
            $this->json_success([
                'success'       => false,
                'diagnostics'   => [['blocking' => true, 'message' => 'Aucun enseignement à planifier (heures hebdomadaires nulles).']],
                'teacher_table' => [],
            ], 'Diagnostic');
        }
        $diag = $this->_diagnostic($ctx);
        $this->json_success($diag, 'Diagnostic');
    }

    // ═══════════════════════════════════════════════════════════════
    // API — GÉNÉRATION (moteur PHP)
    // ═══════════════════════════════════════════════════════════════

    public function api_generer() {
        $start = microtime(true);
        $ctx = $this->_context();

        if (empty($ctx['sessions'])) {
            $this->json_error('Aucun cours à placer : vérifiez les heures hebdomadaires et les enseignements.', 422);
        }

        $solution = $this->_solve($ctx);

        // ── Persistance ──────────────────────────────────────────
        $id_generation = $this->_create_generation();

        $this->db->where('deleted_at', null)->update('horaires', ['deleted_at' => date('Y-m-d H:i:s')]);

        $now = date('Y-m-d H:i:s');
        $batch = [];

        // $solution['rows'] contient déjà les sessions fixes (marquées) + les sessions auto
        foreach ($solution['rows'] as $r) {
            $type = ($r['_type'] ?? 'auto') === 'fixe' ? 'fixe' : 'auto';
            $batch[] = $this->_horaire_row($r, $id_generation, $type, $now);
        }

        $total = 0;
        foreach (array_chunk($batch, 200) as $chunk) {
            $this->db->insert_batch('horaires', $chunk);
            $total += count($chunk);
        }

        $elapsed = round(microtime(true) - $start, 3);
        $unplaced = $solution['unplaced'];
        $diagnostics = $this->_diagnostic($ctx);

        $details = [];
        $messages = [];
        foreach ($unplaced as $u) {
            $details[] = [
                'matiere'            => $u['matiere_libelle'] . ' (' . $u['matiere_code'] . ')',
                'classe'             => $u['classe_libelle'],
                'heures_manquantes'  => 1,
                'raison'             => $u['raison'],
            ];
        }
        // Regrouper par matière/classe
        $grouped = [];
        foreach ($details as $d) {
            $k = $d['matiere'] . '|' . $d['classe'];
            if (!isset($grouped[$k])) {
                $grouped[$k] = $d;
            } else {
                $grouped[$k]['heures_manquantes'] += 1;
            }
        }
        $grouped = array_values($grouped);

        foreach ($grouped as $g) {
            $messages[] = $g['matiere'] . ' (' . $g['classe'] . ') : ' . $g['heures_manquantes']
                . 'h non placée(s) — ' . $g['raison'];
        }

        if (empty($solution['rows']) && !empty($unplaced)) {
            $this->json_response([
                'success' => false,
                'message' => 'La génération a échoué : aucun cours n\'a pu être placé.',
                'data'    => [
                    'created'         => 0,
                    'conflits_restants' => count($unplaced),
                    'messages'        => $messages,
                    'solutions'       => $this->_solutions($diagnostics),
                    'details_conflits' => $grouped,
                    'teacher_table'   => $diagnostics['teacher_table'],
                    'diagnostics'     => $diagnostics['diagnostics'],
                ],
            ], 422);
        }

        $this->json_success([
            'created'             => $total,
            'conflits_restants'   => count($grouped),
            'details_conflits'    => $grouped,
            'messages'            => $messages,
            'teacher_table'       => $diagnostics['teacher_table'],
            'diagnostics'         => $diagnostics['diagnostics'],
            'placements_fixes'     => count($ctx['fix_rows']),
            'duree'               => $elapsed . 's',
        ], $messages
            ? 'Génération terminée : ' . $total . ' créneaux, ' . count($grouped) . ' cours non placé(s).'
            : 'Génération réussie : ' . $total . ' créneaux placés en ' . $elapsed . 's.');
    }

    // ═══════════════════════════════════════════════════════════════
    // CONTEXTE
    // ═══════════════════════════════════════════════════════════════

    private function _context() {
        return $this->_solver()->context($this->db, $this->id_annee_active);
    }

    // ═══════════════════════════════════════════════════════════════
    // SOLVEUR — délégation vers la bibliothèque partagée
    // ═══════════════════════════════════════════════════════════════

    private function _solver() {
        if ($this->solver === null) {
            require_once APPPATH . 'libraries/Horaires_solver.php';
            $this->solver = new Horaires_solver();
        }
        return $this->solver;
    }

    private function _solve(array $ctx) {
        return $this->_solver()->solve($ctx);
    }

    // ═══════════════════════════════════════════════════════════════
    // DIAGNOSTIC
    // ═══════════════════════════════════════════════════════════════

    private function _diagnostic(array $ctx) {
        $jour_ids = $ctx['jour_ids'];
        $creneau_ids = $ctx['creneau_ids'];
        $indispos = $ctx['indispos'];
        $jour_lib = [];
        foreach ($ctx['jours'] as $j) {
            $jour_lib[(int)$j['id_jour']] = $j['libelle'];
        }

        $ens_rows = $this->db->where('deleted_at', null)->get('enseignants')->result_array();
        $ens_lib = [];
        foreach ($ens_rows as $e) {
            $ens_lib[(int)$e['id_enseignant']] = $e['fullname'];
        }

        // Charges
        $load = [];
        foreach ($ctx['sessions'] as $s) {
            $t = (int)$s['id_enseignant'];
            $load[$t] = (isset($load[$t]) ? $load[$t] : 0) + 1;
        }
        $fixed = [];
        foreach ($ctx['fix_rows'] as $f) {
            $t = (int)$f['id_enseignant'];
            $fixed[$t] = (isset($fixed[$t]) ? $fixed[$t] : 0) + 1;
        }

        $teacher_table = [];
        $diagnostics = [];
        $ids = array_unique(array_merge(array_keys($load), array_keys($fixed)));
        sort($ids);

        foreach ($ids as $t) {
            if ($t <= 0) continue;
            $cap = 0;
            $joursDispo = [];
            foreach ($jour_ids as $j) {
                $free = 0;
                foreach ($creneau_ids as $c) {
                    if (!isset($indispos[$t][$j][$c])) $free++;
                }
                $cap += $free;
                if ($free > 0) $joursDispo[] = isset($jour_lib[$j]) ? $jour_lib[$j] : ('Jour ' . $j);
            }
            $sessions = (isset($load[$t]) ? $load[$t] : 0) + (isset($fixed[$t]) ? $fixed[$t] : 0);
            $marge = $cap - $sessions;

            // RÈGLE : marge minimale de 2 créneaux par enseignant.
            // marge 0 n'est pas permis (aucun filet de sécurité pour le placement).
            if ($marge < 0) {
                $status = 'impossible';
                $status_label = 'Surcharge';
                $color = '#d9534f';
            } elseif ($marge < 2) {
                $status = ($marge === 0) ? 'marge_zero' : 'marge_insuffisant';
                $status_label = ($marge === 0) ? 'Marge 0 — interdit' : 'Marge ' . $marge;
                $color = ($marge === 0) ? '#d9534f' : '#fd7e14';
            } elseif ($marge <= 3) {
                $status = 'serré';
                $status_label = 'Serré';
                $color = '#ff9800';
            } else {
                $status = 'ok';
                $status_label = 'OK';
                $color = '#28a745';
            }

            $teacher_table[] = [
                'nom'             => isset($ens_lib[$t]) ? $ens_lib[$t] : ('Enseignant #' . $t),
                'sessions_semaine' => $sessions,
                'jours_dispo'     => $joursDispo,
                'creneaux_dispo'  => $cap,
                'marge'           => $marge,
                'status'          => $status,
                'status_label'    => $status_label,
                'status_color'    => $color,
            ];

            if ($marge < 0) {
                $diagnostics[] = [
                    'blocking' => true,
                    'message'  => (isset($ens_lib[$t]) ? $ens_lib[$t] : 'Enseignant #' . $t)
                        . ' : ' . $sessions . 'h demandées pour seulement ' . $cap
                        . ' créneaux disponibles (manque ' . abs($marge) . ').',
                ];
            } elseif ($marge === 0) {
                $diagnostics[] = [
                    'blocking' => true,
                    'message'  => (isset($ens_lib[$t]) ? $ens_lib[$t] : 'Enseignant #' . $t)
                        . ' : marge 0 — non permis (aucun créneau de réserve). Minimum requis : 2.'
                        . ' Libérez au moins 2 créneaux dans le module Disponibilités.',
                ];
            } elseif ($marge < 2) {
                $diagnostics[] = [
                    'blocking' => true,
                    'message'  => (isset($ens_lib[$t]) ? $ens_lib[$t] : 'Enseignant #' . $t)
                        . ' : marge ' . $marge . ' — sous le minimum requis de 2.'
                        . ' Libérez au moins ' . (2 - $marge) . ' créneau(x) dans le module Disponibilités.',
                ];
            }
        }

        // Charge des classes
        $classLoad = [];
        foreach ($ctx['sessions'] as $s) {
            $classLoad[(int)$s['id_classe']] = (isset($classLoad[(int)$s['id_classe']]) ? $classLoad[(int)$s['id_classe']] : 0) + 1;
        }
        foreach ($ctx['fix_rows'] as $f) {
            $classLoad[(int)$f['id_classe']] = (isset($classLoad[(int)$f['id_classe']]) ? $classLoad[(int)$f['id_classe']] : 0) + 1;
        }
        foreach ($classLoad as $cl => $hours) {
            if ($hours > $ctx['nb_slots']) {
                $diagnostics[] = [
                    'blocking' => true,
                    'message'  => (isset($ctx['class_lib'][$cl]) ? $ctx['class_lib'][$cl] : 'Classe #' . $cl)
                        . ' : ' . $hours . 'h à placer pour seulement ' . $ctx['nb_slots'] . ' créneaux.',
                ];
            }
        }

        if (!$diagnostics) {
            $diagnostics[] = ['blocking' => false, 'message' => 'Aucun problème détecté. Prêt pour la génération.'];
        }

        return [
            'success'       => empty(array_filter($diagnostics, function ($d) { return !empty($d['blocking']); })),
            'diagnostics'   => $diagnostics,
            'teacher_table' => $teacher_table,
        ];
    }

    private function _solutions(array $diag) {
        $solutions = [];
        foreach ($diag['diagnostics'] as $d) {
            if (!empty($d['blocking'])) {
                $solutions[] = 'Augmenter les disponibilités de l\'enseignant concerné (module Disponibilités) ou réduire ses heures.';
            }
        }
        if (!$solutions) {
            $solutions[] = 'Réduire les heures hebdomadaires ou ajouter des disponibilités.';
        }
        array_unshift(
            $solutions,
            'Règle à respecter : chaque enseignant doit avoir une marge d\'au moins 2 créneaux — marge 0 n\'est pas permis.'
        );
        return array_values(array_unique($solutions));
    }

    // ═══════════════════════════════════════════════════════════════
    // UTILITAIRES
    // ═══════════════════════════════════════════════════════════════

    private function _params() {
        static $map = null;
        if ($map === null) {
            $map = [];
            foreach ($this->db->where('deleted_at', null)->get('parametres')->result_array() as $r) {
                if (!empty($r['clef'])) {
                    $map[$r['clef']] = $r['valeur'];
                }
            }
        }
        return $map;
    }

    private function _classes() {
        return $this->db->where('deleted_at', null)->order_by('ordre ASC, libelle ASC')->get('classes')->result_array();
    }

    private function _jours() {
        return $this->db->where('deleted_at', null)->where('actif', 1)->order_by('ordre ASC')->get('jours_semaine')->result_array();
    }

    private function _annee_label() {
        $row = $this->db->where('deleted_at', null)->where('id_annee', $this->id_annee_active)->get('annees_scolaires')->row_array();
        return $row ? $row['libelle'] : '';
    }

    private function _last_generation_id() {
        $row = $this->db->select('MAX(id_generation) AS m')->get('horaires_generations')->row_array();
        return $row && $row['m'] ? (int)$row['m'] : $this->_create_generation();
    }

    private function _create_generation() {
        $this->db->insert('horaires_generations', [
            'uuid'      => generate_uuid(),
            'libelle'   => 'Génération du ' . date('d/m/Y H:i'),
            'id_annee'  => $this->id_annee_active,
            'statut'    => 'brouillon',
        ]);
        return (int)$this->db->insert_id();
    }

    private function _horaire_row(array $r, $id_generation, $type, $now) {
        return [
            'uuid'            => generate_uuid(),
            'id_generation'   => $id_generation,
            'id_enseignement' => (int)$r['id_enseignement'],
            'id_matiere'      => (int)$r['id_matiere'],
            'id_enseignant'   => (int)$r['id_enseignant'],
            'id_classe'       => (int)$r['id_classe'],
            'id_creneau'      => (int)$r['id_creneau'],
            'id_jour'         => (int)$r['id_jour'],
            'type'            => $type,
            'cree_le'         => $now,
            'modifie_le'      => $now,
        ];
    }

    private function _slot_taken($id_classe, $id_jour, $id_creneau) {
        return $this->db->where('id_classe', $id_classe)->where('id_jour', $id_jour)
            ->where('id_creneau', $id_creneau)->where('deleted_at', null)
            ->count_all_results('horaires') > 0;
    }

    private function _teacher_slot_taken($id_enseignant, $id_jour, $id_creneau) {
        return $this->db->where('id_enseignant', $id_enseignant)->where('id_jour', $id_jour)
            ->where('id_creneau', $id_creneau)->where('deleted_at', null)
            ->count_all_results('horaires') > 0;
    }

    private function _fix_slot_taken($id_classe, $id_jour, $id_creneau) {
        return $this->db->where('id_classe', $id_classe)->where('id_jour', $id_jour)
            ->where('id_creneau', $id_creneau)->where('deleted_at', null)
            ->count_all_results('horaires_fixes') > 0;
    }

    private function _fix_teacher_slot_taken($id_enseignant, $id_jour, $id_creneau) {
        return $this->db->where('id_enseignant', $id_enseignant)->where('id_jour', $id_jour)
            ->where('id_creneau', $id_creneau)->where('deleted_at', null)
            ->count_all_results('horaires_fixes') > 0;
    }

    private function _teacher_indispo($id_enseignant, $id_jour, $id_creneau) {
        return $this->db->where('id_enseignant', $id_enseignant)->where('id_jour', $id_jour)
            ->where('id_creneau', $id_creneau)->where('type', 'indisponible')
            ->where('deleted_at', null)
            ->count_all_results('disponibilites_enseignants') > 0;
    }

    private function _creneau_valide($id_jour, $id_creneau) {
        $creneaux = $this->Horaires_model->get_creneaux_cours();
        $valid = false;
        foreach ($creneaux as $c) {
            if (($c['type'] ?? '') === 'cours' && (int)$c['id_creneau'] === (int)$id_creneau) {
                $valid = true;
                break;
            }
        }
        if (!$valid) return false;
        $jours = $this->_jours();
        foreach ($jours as $j) {
            if ((int)$j['id_jour'] === (int)$id_jour) return true;
        }
        return false;
    }
}
