<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Module Horaires — 100% PHP natif.
 * Aucune dépendance externe (Python supprimé) : rendu des vues + API + moteur
 * de génération d'emplois du temps (heuristique gloutonne + réparation par swaps).
 */
class Horaires extends MY_Controller {

    /** Nombre de redémarrages aléatoires du solveur (recherche du meilleur placement) */
    const SOLVER_RESTARTS = 60;
    /** Nombre maximal de tentatives de réparation (swaps) */
    const REPAIR_ATTEMPTS = 400;

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
        $jours = $this->_jours();
        $creneaux = $this->Horaires_model->get_creneaux_cours();
        $cours = [];
        foreach ($creneaux as $c) {
            if (($c['type'] ?? '') === 'cours') {
                $cours[] = $c;
            }
        }

        $jour_ids = [];
        foreach ($jours as $j) {
            $jour_ids[] = (int)$j['id_jour'];
        }
        $creneau_ids = [];
        foreach ($cours as $c) {
            $creneau_ids[] = (int)$c['id_creneau'];
        }
        $nb_jours = count($jour_ids);

        // Matières-classes + enseignements
        $mc_rows = $this->db->where('deleted_at', null)->get('matieres_classes')->result_array();
        $ens_rows = $this->db->where('deleted_at', null)->get('enseignements')->result_array();
        $ens_by_mc = [];
        foreach ($ens_rows as $e) {
            $ens_by_mc[(int)$e['id_matiere_classe']] = $e;
        }

        $mc_map = [];
        foreach ($mc_rows as $mc) {
            $mc_map[(int)$mc['id_matiere_classe']] = $mc;
        }

        // Libellés classes / matières
        $classes = $this->_classes();
        $class_lib = [];
        foreach ($classes as $c) {
            $class_lib[(int)$c['id_classe']] = $c['libelle'];
        }
        $mat_lib = [];
        foreach ($this->db->where('deleted_at', null)->get('matieres')->result_array() as $m) {
            $mat_lib[(int)$m['id_matiere']] = ['libelle' => $m['libelle'], 'code' => $m['code']];
        }

        // Indisponibilités enseignants
        $indispos = [];
        $dispo_rows = $this->db->where('deleted_at', null)->where('type', 'indisponible')
            ->get('disponibilites_enseignants')->result_array();
        foreach ($dispo_rows as $d) {
            $indispos[(int)$d['id_enseignant']][(int)$d['id_jour']][(int)$d['id_creneau']] = true;
        }

        // Sessions fixes
        $q = $this->db->where('deleted_at', null);
        if ($this->id_annee_active > 0) {
            $q->where('id_annee', $this->id_annee_active);
        }
        $fix_rows = $q->get('horaires_fixes')->result_array();

        $fixes_by_mc = [];
        $valid_fixes = [];
        foreach ($fix_rows as $i => $f) {
            $mc_id = (int)$f['id_matiere_classe'];
            $mc = isset($mc_map[$mc_id]) ? $mc_map[$mc_id] : null;
            $fix_rows[$i]['_valid'] = false;
            if (!$mc || !in_array((int)$f['id_jour'], $jour_ids, true)
                || !in_array((int)$f['id_creneau'], $creneau_ids, true)) {
                continue;
            }
            $ens = isset($ens_by_mc[$mc_id]) ? $ens_by_mc[$mc_id] : null;
            if (!$ens || empty($ens['id_enseignement'])) {
                continue;
            }
            $fix_rows[$i]['id_matiere'] = (int)$mc['id_matiere'];
            $fix_rows[$i]['id_enseignement'] = (int)$ens['id_enseignement'];
            $fix_rows[$i]['id_classe'] = (int)$mc['id_classe'];
            $fix_rows[$i]['id_jour'] = (int)$f['id_jour'];
            $fix_rows[$i]['id_creneau'] = (int)$f['id_creneau'];
            $fix_rows[$i]['id_enseignant'] = (int)$f['id_enseignant'];
            $fix_rows[$i]['_valid'] = true;
            $fixes_by_mc[$mc_id] = (isset($fixes_by_mc[$mc_id]) ? $fixes_by_mc[$mc_id] : 0) + 1;
            $valid_fixes[] = $fix_rows[$i];
        }

        // Sessions automatiques
        $sessions = [];
        foreach ($mc_rows as $mc) {
            $mc_id = (int)$mc['id_matiere_classe'];
            $weekly = (int)floatval($mc['nb_heures_par_semaine'] ?? 0);
            if ($weekly <= 0) continue;
            $ens = isset($ens_by_mc[$mc_id]) ? $ens_by_mc[$mc_id] : null;
            if (!$ens || empty($ens['id_enseignement'])) continue;
            $teacher = (int)$ens['id_enseignant'];
            if ($teacher <= 0) continue;
            $id_matiere = (int)$mc['id_matiere'];
            $id_classe = (int)$mc['id_classe'];
            $fix_count = isset($fixes_by_mc[$mc_id]) ? $fixes_by_mc[$mc_id] : 0;
            $auto = max(0, $weekly - $fix_count);
            $daily = max(1, (int)floatval($mc['nb_heures_par_jour'] ?? 2));
            // La limite journalière ne peut pas rendre la matière infaisable
            if ($nb_jours > 0) {
                $daily = max($daily, (int)ceil($weekly / $nb_jours));
            }
            for ($i = 0; $i < $auto; $i++) {
                $sessions[] = [
                    'id_matiere_classe' => $mc_id,
                    'id_matiere'        => $id_matiere,
                    'id_classe'         => $id_classe,
                    'id_enseignement'   => (int)$ens['id_enseignement'],
                    'id_enseignant'     => $teacher,
                    'daily'             => $daily,
                    'matiere_libelle'   => isset($mat_lib[$id_matiere]) ? $mat_lib[$id_matiere]['libelle'] : '',
                    'matiere_code'      => isset($mat_lib[$id_matiere]) ? $mat_lib[$id_matiere]['code'] : '',
                    'classe_libelle'    => isset($class_lib[$id_classe]) ? $class_lib[$id_classe] : '',
                ];
            }
        }

        return [
            'jours'       => $jours,
            'creneaux'    => $cours,
            'jour_ids'    => $jour_ids,
            'creneau_ids' => $creneau_ids,
            'nb_jours'    => $nb_jours,
            'nb_slots'    => $nb_jours * count($creneau_ids),
            'sessions'    => $sessions,
            'fix_rows'    => $valid_fixes,
            'indispos'    => $indispos,
            'mc_map'      => $mc_map,
            'class_lib'   => $class_lib,
        ];
    }

    // ═══════════════════════════════════════════════════════════════
    // SOLVEUR (heuristique gloutonne multi-démarrage + réparation)
    // ═══════════════════════════════════════════════════════════════

    private function _solve(array $ctx) {
        $best = null;
        $best_unplaced = PHP_INT_MAX;

        // 1) Appariement bipartite (classe ↔ enseignant) créneau par créneau
        for ($attempt = 0; $attempt < self::SOLVER_RESTARTS; $attempt++) {
            $res = $this->_attempt_matching($ctx, $attempt);
            $n = count($res['unplaced']);
            if ($n < $best_unplaced) {
                $best_unplaced = $n;
                $best = $res;
            }
            if ($n === 0) {
                break;
            }
        }

        // 2) Glouton multi-démarrage (secours)
        for ($attempt = 0; $attempt < 20 && $best_unplaced > 0; $attempt++) {
            $res = $this->_attempt_greedy($ctx, $attempt);
            $n = count($res['unplaced']);
            if ($n < $best_unplaced) {
                $best_unplaced = $n;
                $best = $res;
            }
            if ($n === 0) {
                break;
            }
        }

        // 3) Réparation par déplacements
        if ($best_unplaced > 0) {
            $repaired = $this->_repair($ctx, $best);
            if (count($repaired['unplaced']) < $best_unplaced) {
                $best = $repaired;
            }
        }

        return $best;
    }

    /**
     * État initial : place les sessions fixes et prépare les index.
     * Retourne [$classBusy, $teachBusy, $classDaySubj, $classDayLoad, $placed].
     */
    private function _init_state(array $ctx) {
        $classBusy = [];
        $teachBusy = [];
        $classDaySubj = [];
        $classDayLoad = [];
        $placed = [];

        foreach ($ctx['fix_rows'] as $f) {
            $cl = (int)$f['id_classe'];
            $j  = (int)$f['id_jour'];
            $c  = (int)$f['id_creneau'];
            $t  = (int)$f['id_enseignant'];
            if (isset($classBusy[$cl][$j][$c])) continue;
            if ($t > 0 && isset($teachBusy[$t][$j][$c])) continue;
            if ($t > 0 && isset($ctx['indispos'][$t][$j][$c])) continue;

            $classBusy[$cl][$j][$c] = true;
            if ($t > 0) $teachBusy[$t][$j][$c] = true;
            $classDayLoad[$cl][$j] = (isset($classDayLoad[$cl][$j]) ? $classDayLoad[$cl][$j] : 0) + 1;
            $mat = (int)$f['id_matiere'];
            $classDaySubj[$cl][$j][$mat] = (isset($classDaySubj[$cl][$j][$mat]) ? $classDaySubj[$cl][$j][$mat] : 0) + 1;

            $placed[] = [
                '_type'          => 'fixe',
                'id_classe'      => $cl,
                'id_matiere'     => $mat,
                'id_enseignement' => (int)$f['id_enseignement'],
                'id_enseignant'  => $t,
                'id_jour'        => $j,
                'id_creneau'     => $c,
            ];
        }

        return [$classBusy, $teachBusy, $classDaySubj, $classDayLoad, $placed];
    }

    /**
     * Placement par appariement bipartite : pour chaque créneau on construit
     * un graphe classe → enseignants disponibles puis on calcule un appariement
     * maximum (algorithm de Kuhn / chemins augmentants).
     */
    private function _attempt_matching(array $ctx, $seed) {
        $jour_ids = $ctx['jour_ids'];
        $creneau_ids = $ctx['creneau_ids'];
        $indispos = $ctx['indispos'];

        list($classBusy, $teachBusy, $classDaySubj, $classDayLoad, $placed)
            = $this->_init_state($ctx);

        // Sessions restantes par classe
        $remaining = [];
        foreach ($ctx['sessions'] as $i => $s) {
            $remaining[(int)$s['id_classe']][] = $i;
        }

        // Liste des créneaux (ordre aléatoire par tentatives)
        $slots = [];
        foreach ($jour_ids as $j) {
            foreach ($creneau_ids as $c) {
                $slots[] = [$j, $c];
            }
        }
        if ($seed > 0) {
            mt_srand($seed * 7919 + 13);
            shuffle($slots);
        }

        foreach ($slots as $slot) {
            $j = $slot[0];
            $c = $slot[1];

            $classes = [];
            foreach ($remaining as $cl => $idxs) {
                if (empty($idxs)) continue;
                if (isset($classBusy[$cl][$j][$c])) continue;
                $classes[] = $cl;
            }
            if (!$classes) continue;

            // Graphe : classe → enseignants utilisables sur ce créneau
            $adj = [];
            foreach ($classes as $cl) {
                $teach = [];
                foreach ($remaining[$cl] as $i) {
                    $s = $ctx['sessions'][$i];
                    $t = (int)$s['id_enseignant'];
                    if ($t <= 0) continue;
                    if (isset($teachBusy[$t][$j][$c])) continue;
                    if (isset($indispos[$t][$j][$c])) continue;
                    $mat = (int)$s['id_matiere'];
                    if ((isset($classDaySubj[$cl][$j][$mat]) ? $classDaySubj[$cl][$j][$mat] : 0) >= (int)$s['daily']) continue;
                    $teach[$t] = true;
                }
                $teach = array_keys($teach);
                if ($seed > 0) shuffle($teach);
                $adj[$cl] = $teach;
            }

            if ($seed > 0) shuffle($classes);

            // Kuhn : enseignant → classe
            $matchT = [];
            foreach ($classes as $cl) {
                $vis = [];
                $this->_kuhn($cl, $adj, $matchT, $vis);
            }

            if (!$matchT) continue;

            foreach ($matchT as $t => $cl) {
                // Choisir la session de cette classe avec cet enseignant
                $pick = null;
                foreach ($remaining[$cl] as $i) {
                    $s = $ctx['sessions'][$i];
                    if ((int)$s['id_enseignant'] !== (int)$t) continue;
                    if (isset($teachBusy[$t][$j][$c])) { $pick = null; break; }
                    if (isset($indispos[$t][$j][$c])) { $pick = null; break; }
                    $mat = (int)$s['id_matiere'];
                    if ((isset($classDaySubj[$cl][$j][$mat]) ? $classDaySubj[$cl][$j][$mat] : 0) >= (int)$s['daily']) continue;
                    $pick = $i;
                    break;
                }
                if ($pick === null) continue;
                if (isset($classBusy[$cl][$j][$c])) continue;

                $s = $ctx['sessions'][$pick];
                $mat = (int)$s['id_matiere'];

                $classBusy[$cl][$j][$c] = true;
                $teachBusy[$t][$j][$c] = true;
                $classDayLoad[$cl][$j] = (isset($classDayLoad[$cl][$j]) ? $classDayLoad[$cl][$j] : 0) + 1;
                $classDaySubj[$cl][$j][$mat] = (isset($classDaySubj[$cl][$j][$mat]) ? $classDaySubj[$cl][$j][$mat] : 0) + 1;

                $placed[] = [
                    '_type'          => 'auto',
                    'id_classe'      => $cl,
                    'id_matiere'     => $mat,
                    'id_enseignement' => (int)$s['id_enseignement'],
                    'id_enseignant'  => (int)$t,
                    'id_jour'        => $j,
                    'id_creneau'     => $c,
                ];

                foreach ($remaining[$cl] as $k => $i) {
                    if ($i === $pick) {
                        unset($remaining[$cl][$k]);
                        $remaining[$cl] = array_values($remaining[$cl]);
                        break;
                    }
                }
            }
        }

        // Collecte des sessions non placées
        $unplaced = [];
        foreach ($remaining as $cl => $idxs) {
            foreach ($idxs as $i) {
                $s = $ctx['sessions'][$i];
                $unplaced[] = [
                    'id_matiere_classe' => $s['id_matiere_classe'],
                    'id_matiere'      => (int)$s['id_matiere'],
                    'id_classe'       => (int)$s['id_classe'],
                    'id_enseignement' => (int)$s['id_enseignement'],
                    'id_enseignant'   => (int)$s['id_enseignant'],
                    'daily'           => (int)$s['daily'],
                    'matiere_libelle' => $s['matiere_libelle'],
                    'matiere_code'    => $s['matiere_code'],
                    'classe_libelle'  => $s['classe_libelle'],
                    'raison'          => 'conflit enseignant (créneaux indisponibles)',
                ];
            }
        }

        return ['rows' => $placed, 'unplaced' => $unplaced];
    }

    /** Chemins augmentants (Kuhn) : affecte $cl à un enseignant libre. */
    private function _kuhn($cl, array $adj, array &$matchT, array &$vis) {
        if (!isset($adj[$cl])) return false;
        foreach ($adj[$cl] as $t) {
            if (isset($vis[$t])) continue;
            $vis[$t] = true;
            if (!isset($matchT[$t]) || $this->_kuhn($matchT[$t], $adj, $matchT, $vis)) {
                $matchT[$t] = $cl;
                return true;
            }
        }
        return false;
    }

    /** Un passage de placement glouton. */
    private function _attempt_greedy(array $ctx, $seed) {
        $jour_ids = $ctx['jour_ids'];
        $creneau_ids = $ctx['creneau_ids'];
        $indispos = $ctx['indispos'];

        $classBusy = [];
        $teachBusy = [];
        $classDaySubj = [];
        $classDayLoad = [];
        $teachDayLoad = [];
        $placed = [];

        // ── 1. Sessions fixes ────────────────────────────────────
        foreach ($ctx['fix_rows'] as $f) {
            $cl = (int)$f['id_classe'];
            $j  = (int)$f['id_jour'];
            $c  = (int)$f['id_creneau'];
            $t  = (int)$f['id_enseignant'];
            if (isset($classBusy[$cl][$j][$c])) continue;
            if ($t > 0 && isset($teachBusy[$t][$j][$c])) continue;
            if ($t > 0 && isset($indispos[$t][$j][$c])) continue;

            $classBusy[$cl][$j][$c] = true;
            if ($t > 0) $teachBusy[$t][$j][$c] = true;
            $classDayLoad[$cl][$j] = (isset($classDayLoad[$cl][$j]) ? $classDayLoad[$cl][$j] : 0) + 1;
            if ($t > 0) $teachDayLoad[$t][$j] = (isset($teachDayLoad[$t][$j]) ? $teachDayLoad[$t][$j] : 0) + 1;
            $mat = (int)$f['id_matiere'];
            $classDaySubj[$cl][$j][$mat] = (isset($classDaySubj[$cl][$j][$mat]) ? $classDaySubj[$cl][$j][$mat] : 0) + 1;

            $placed[] = [
                '_type'          => 'fixe',
                'id_classe'       => $cl,
                'id_matiere'      => $mat,
                'id_enseignement' => (int)$f['id_enseignement'],
                'id_enseignant'   => $t,
                'id_jour'         => $j,
                'id_creneau'      => $c,
            ];
        }

        // ── 2. Capacité restante par enseignant (tri contraint-d'abord) ──
        $teacherCap = [];
        foreach ($ctx['sessions'] as $s) {
            $t = (int)$s['id_enseignant'];
            if (!isset($teacherCap[$t])) {
                $cap = 0;
                foreach ($jour_ids as $j) {
                    foreach ($creneau_ids as $c) {
                        if (!isset($indispos[$t][$j][$c])) $cap++;
                    }
                }
                $teacherCap[$t] = $cap;
            }
        }

        $sessions = $ctx['sessions'];
        $order = [];
        foreach ($sessions as $i => $s) {
            $order[$i] = [
                'cap' => isset($teacherCap[(int)$s['id_enseignant']]) ? $teacherCap[(int)$s['id_enseignant']] : 999,
                'jitter' => mt_rand(),
            ];
        }
        // Les enseignants les plus contraints d'abord, puis aléatoire (démarrages multiples)
        uksort($order, function ($a, $b) use ($order) {
            if ($order[$a]['cap'] !== $order[$b]['cap']) {
                return $order[$a]['cap'] - $order[$b]['cap'];
            }
            return $order[$a]['jitter'] - $order[$b]['jitter'];
        });

        // ── 3. Placement glouton ─────────────────────────────────
        $unplaced = [];
        foreach ($order as $idx => $_ignore) {
            $s = $sessions[$idx];
            $cl = (int)$s['id_classe'];
            $t  = (int)$s['id_enseignant'];
            $mat = (int)$s['id_matiere'];
            $daily = (int)$s['daily'];

            $bestJ = null;
            $bestC = null;
            $bestScore = PHP_INT_MAX;

            foreach ($jour_ids as $j) {
                if (isset($classBusy[$cl][$j])) {
                    // déjà complètement chargé ce jour ?
                }
                if ((isset($classDayLoad[$cl][$j]) ? $classDayLoad[$cl][$j] : 0) >= count($creneau_ids)) continue;
                if ((isset($classDaySubj[$cl][$j][$mat]) ? $classDaySubj[$cl][$j][$mat] : 0) >= $daily) continue;

                foreach ($creneau_ids as $c) {
                    if (isset($classBusy[$cl][$j][$c])) continue;
                    if ($t > 0 && isset($teachBusy[$t][$j][$c])) continue;
                    if ($t > 0 && isset($indispos[$t][$j][$c])) continue;

                    $score = (isset($classDayLoad[$cl][$j]) ? $classDayLoad[$cl][$j] : 0) * 100
                           + (isset($teachDayLoad[$t][$j]) ? $teachDayLoad[$t][$j] : 0) * 7
                           + mt_rand(0, 5);
                    if ($score < $bestScore) {
                        $bestScore = $score;
                        $bestJ = $j;
                        $bestC = $c;
                    }
                }
            }

            if ($bestJ === null) {
                $unplaced[] = [
                    'id_matiere_classe' => $s['id_matiere_classe'],
                    'id_matiere'      => $mat,
                    'id_classe'       => $cl,
                    'id_enseignement' => (int)$s['id_enseignement'],
                    'id_enseignant'   => $t,
                    'daily'           => $daily,
                    'matiere_libelle' => $s['matiere_libelle'],
                    'matiere_code'    => $s['matiere_code'],
                    'classe_libelle'  => $s['classe_libelle'],
                    'raison'          => 'conflit enseignant ou classe (créneaux épuisés)',
                ];
                continue;
            }

            $classBusy[$cl][$bestJ][$bestC] = true;
            if ($t > 0) $teachBusy[$t][$bestJ][$bestC] = true;
            $classDayLoad[$cl][$bestJ] = (isset($classDayLoad[$cl][$bestJ]) ? $classDayLoad[$cl][$bestJ] : 0) + 1;
            if ($t > 0) $teachDayLoad[$t][$bestJ] = (isset($teachDayLoad[$t][$bestJ]) ? $teachDayLoad[$t][$bestJ] : 0) + 1;
            $classDaySubj[$cl][$bestJ][$mat] = (isset($classDaySubj[$cl][$bestJ][$mat]) ? $classDaySubj[$cl][$bestJ][$mat] : 0) + 1;

            $placed[] = [
                '_type'          => 'auto',
                'id_classe'       => $cl,
                'id_matiere'      => $mat,
                'id_enseignement' => (int)$s['id_enseignement'],
                'id_enseignant'   => $t,
                'id_jour'         => $bestJ,
                'id_creneau'      => $bestC,
            ];
        }

        return ['rows' => $placed, 'unplaced' => $unplaced];
    }

    /** Réparation : tente de libérer un créneau en déplaçant une session déjà placée. */
    private function _repair(array $ctx, array $sol) {
        $jour_ids = $ctx['jour_ids'];
        $creneau_ids = $ctx['creneau_ids'];
        $indispos = $ctx['indispos'];

        $rows = $sol['rows'];
        $unplaced = $sol['unplaced'];

        // Indexe l'état
        $classBusy = [];
        $teachBusy = [];
        $classDaySubj = [];
        $classDayLoad = [];
        foreach ($rows as $r) {
            $cl = $r['id_classe'];
            $j = $r['id_jour'];
            $c = $r['id_creneau'];
            $classBusy[$cl][$j][$c] = true;
            if ($r['id_enseignant'] > 0) $teachBusy[(int)$r['id_enseignant']][$j][$c] = true;
            $classDayLoad[$cl][$j] = (isset($classDayLoad[$cl][$j]) ? $classDayLoad[$cl][$j] : 0) + 1;
            $classDaySubj[$cl][$j][(int)$r['id_matiere']] = (isset($classDaySubj[$cl][$j][(int)$r['id_matiere']]) ? $classDaySubj[$cl][$j][(int)$r['id_matiere']] : 0) + 1;
        }

        $stillUnplaced = [];
        $attempts = 0;

        foreach ($unplaced as $u) {
            $cl = (int)$u['id_classe'];
            $t  = (int)$u['id_enseignant'];
            $mat = (int)$u['id_matiere'];
            $daily = (int)$u['daily'];

            $done = false;

            foreach ($jour_ids as $j) {
                if ($done) break;
                foreach ($creneau_ids as $c) {
                    if (isset($classBusy[$cl][$j][$c])) continue;
                    if ($t > 0 && isset($indispos[$t][$j][$c])) continue;
                    if ((isset($classDaySubj[$cl][$j][$mat]) ? $classDaySubj[$cl][$j][$mat] : 0) >= $daily) continue;
                    if ($t > 0 && !isset($teachBusy[$t][$j][$c])) {
                        // Placement direct
                        $classBusy[$cl][$j][$c] = true;
                        $teachBusy[$t][$j][$c] = true;
                        $classDayLoad[$cl][$j] = (isset($classDayLoad[$cl][$j]) ? $classDayLoad[$cl][$j] : 0) + 1;
                        $classDaySubj[$cl][$j][$mat] = (isset($classDaySubj[$cl][$j][$mat]) ? $classDaySubj[$cl][$j][$mat] : 0) + 1;
                        $rows[] = [
                            '_type' => 'auto',
                            'id_classe' => $cl, 'id_matiere' => $mat,
                            'id_enseignement' => (int)$u['id_enseignement'],
                            'id_enseignant' => $t, 'id_jour' => $j, 'id_creneau' => $c,
                        ];
                        $done = true;
                        break;
                    }

                    // Créneau bloqué par une session à déplacer ?
                    if ($t > 0 && isset($teachBusy[$t][$j][$c]) && $attempts < self::REPAIR_ATTEMPTS) {
                        $attempts++;
                        $moved = $this->_try_displace($rows, $classBusy, $teachBusy, $classDayLoad,
                            $classDaySubj, $ctx, $t, $j, $c, $cl, $mat, $daily);
                        if ($moved !== false) {
                            $rows = $moved;
                            // Reconstruire les index (simple et sûr)
                            $classBusy = $teachBusy = $classDaySubj = $classDayLoad = [];
                            foreach ($rows as $r) {
                                $rc = $r['id_classe'];
                                $rj = $r['id_jour'];
                                $rcr = $r['id_creneau'];
                                $classBusy[$rc][$rj][$rcr] = true;
                                if ($r['id_enseignant'] > 0) $teachBusy[(int)$r['id_enseignant']][$rj][$rcr] = true;
                                $classDayLoad[$rc][$rj] = (isset($classDayLoad[$rc][$rj]) ? $classDayLoad[$rc][$rj] : 0) + 1;
                                $rm = (int)$r['id_matiere'];
                                $classDaySubj[$rc][$rj][$rm] = (isset($classDaySubj[$rc][$rj][$rm]) ? $classDaySubj[$rc][$rj][$rm] : 0) + 1;
                            }

                            $classBusy[$cl][$j][$c] = true;
                            $teachBusy[$t][$j][$c] = true;
                            $classDayLoad[$cl][$j] = (isset($classDayLoad[$cl][$j]) ? $classDayLoad[$cl][$j] : 0) + 1;
                            $classDaySubj[$cl][$j][$mat] = (isset($classDaySubj[$cl][$j][$mat]) ? $classDaySubj[$cl][$j][$mat] : 0) + 1;
                            $rows[] = [
                                '_type' => 'auto',
                                'id_classe' => $cl, 'id_matiere' => $mat,
                                'id_enseignement' => (int)$u['id_enseignement'],
                                'id_enseignant' => $t, 'id_jour' => $j, 'id_creneau' => $c,
                            ];
                            $done = true;
                            break;
                        }
                    }
                }
            }

            if (!$done) {
                $stillUnplaced[] = $u;
            }
        }

        return ['rows' => $rows, 'unplaced' => $stillUnplaced];
    }

    /**
     * Cherche une session occupant (j,c) pour l'enseignant $t et la fait
     * migrer vers un autre créneau libre, afin de libérer (j,c).
     * Retourne les lignes mises à jour, ou false.
     */
    private function _try_displace(array $rows, array $classBusy, array $teachBusy,
                                   array $classDayLoad, array $classDaySubj,
                                   array $ctx, $t, $j, $c, $cl, $mat, $daily) {
        $jour_ids = $ctx['jour_ids'];
        $creneau_ids = $ctx['creneau_ids'];
        $indispos = $ctx['indispos'];

        // Trouve l'occupant (session de l'enseignant $t sur ce créneau)
        // Les sessions fixes ne doivent jamais être déplacées.
        $idx = null;
        foreach ($rows as $i => $r) {
            if (isset($r['_type']) && $r['_type'] === 'fixe') continue;
            if ((int)$r['id_enseignant'] === (int)$t && (int)$r['id_jour'] === (int)$j && (int)$r['id_creneau'] === (int)$c) {
                $idx = $i;
                break;
            }
        }
        if ($idx === null) return false;

        $p = $rows[$idx];
        $p_cl = (int)$p['id_classe'];
        $p_t  = (int)$p['id_enseignant'];
        $p_mat = (int)$p['id_matiere'];
        $p_daily = 10; // on n'applique pas la limite journalière au déplacement (déjà validée à l'origine)

        foreach ($jour_ids as $j2) {
            foreach ($creneau_ids as $c2) {
                if ($j2 === $j && $c2 === $c) continue;
                if (isset($classBusy[$p_cl][$j2][$c2])) continue;
                if ($p_t > 0 && isset($teachBusy[$p_t][$j2][$c2])) continue;
                if ($p_t > 0 && isset($indispos[$p_t][$j2][$c2])) continue;
                if ((isset($classDaySubj[$p_cl][$j2][$p_mat]) ? $classDaySubj[$p_cl][$j2][$p_mat] : 0) >= $p_daily) continue;
                if ((isset($classDayLoad[$p_cl][$j2]) ? $classDayLoad[$p_cl][$j2] : 0) >= count($creneau_ids)) continue;

                // Applique le déplacement
                unset($classBusy[$p_cl][$j][$c]);
                if ($p_t > 0) unset($teachBusy[$p_t][$j][$c]);
                $rows[$idx]['id_jour'] = $j2;
                $rows[$idx]['id_creneau'] = $c2;
                return $rows;
            }
        }
        return false;
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

            if ($marge < 0) {
                $status = 'impossible';
                $color = '#d9534f';
            } elseif ($marge === 0) {
                $status = 'marge_zero';
                $color = '#f0ad4e';
            } elseif ($marge <= 3) {
                $status = 'serré';
                $color = '#ff9800';
            } else {
                $status = 'ok';
                $color = '#28a745';
            }

            $teacher_table[] = [
                'nom'             => isset($ens_lib[$t]) ? $ens_lib[$t] : ('Enseignant #' . $t),
                'sessions_semaine' => $sessions,
                'jours_dispo'     => $joursDispo,
                'creneaux_dispo'  => $cap,
                'marge'           => $marge,
                'status'          => $status,
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
                    'blocking' => false,
                    'message'  => (isset($ens_lib[$t]) ? $ens_lib[$t] : 'Enseignant #' . $t)
                        . ' : marge nulle, aucun créneau de réserve.',
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
