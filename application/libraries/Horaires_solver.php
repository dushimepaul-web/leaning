<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Moteur de résolution d'emplois du temps (100 % PHP natif).
 *
 * Classe sans dépendance CodeIgniter : utilisée par le contrôleur du module
 * Horaires et par le banc de mesure CLI tools/bench_horaires.php (même code,
 * mêmes résultats, aucun HTTP ni session).
 */
class Horaires_solver {

    /** Nombre de redémarrages aléatoires du solveur (recherche du meilleur placement) */
    const SOLVER_RESTARTS = 24;
    /** Profondeur de nœuds explorés par la recherche classe par classe */
    const CW_BUDGET = 1200;
    /** Durée maximale totale du solveur, en secondes */
    const SOLVER_SECONDS = 25;
    /** Nombre maximal de tentatives de réparation (swaps) */
    const REPAIR_ATTEMPTS = 400;

    /** Date limite (timestamp) du moteur de résolution */
    private $deadline = 0;

    /** Graine globale du solveur : rend chaque exécution déterministe et reproductible */
    private $seed = 0;

    // ═══════════════════════════════════════════════════════════════
    // CONTEXTE (lecture base — adaptateur $db sans dépendance CodeIgniter)
    // ═══════════════════════════════════════════════════════════════

    /**
     * Adaptateur attendu par params() et context() :
     *   $db->query($sql) doit renvoyer un objet exposant result_array().
     * En environnement CodeIgniter on passe $this->db (CI_DB_driver) ;
     * l'outil CLI tools/bench_horaires.php passe un adaptateur mysqli.
     */
    private static function rows($db, $sql) {
        $res = $db->query($sql);
        if (!$res) {
            return array();
        }
        return $res->result_array();
    }

    /** Paramètres applicatifs, indexés par leur clé (table `parametres`). */
    public static function params($db) {
        $map = array();
        foreach (self::rows($db, 'SELECT clef, valeur FROM parametres WHERE deleted_at IS NULL') as $r) {
            if (!empty($r['clef'])) {
                $map[$r['clef']] = $r['valeur'];
            }
        }
        return $map;
    }

    /** Créneaux de la journée (vigile, cours, pauses) déduits des paramètres. */
    public static function creneaux(array $params) {
        $nb = intval($params['nb_creneaux_jour'] ?? 8);
        $heure_debut = $params['heure_debut_journee'] ?? '07:30';
        $duree_cours = intval($params['duree_cours'] ?? 45);
        $duree_pause = intval($params['duree_pause'] ?? 20);
        $duree_vigie = intval($params['duree_vigie'] ?? 10);

        $h = intval(substr($heure_debut, 0, 2));
        $m = intval(substr($heure_debut, 3, 2));
        $pause_after = intdiv($nb, 2);
        $ordre = 0;
        $creneaux = array();

        for ($i = 1; $i <= $nb; $i++) {
            if ($duree_vigie > 0 && $i === 1) {
                $debut = sprintf('%02d:%02d', $h, $m);
                $m += $duree_vigie;
                $h += intdiv($m, 60);
                $m = $m % 60;
                $fin = sprintf('%02d:%02d', $h, $m);
                $ordre++;
                $creneaux[] = array(
                    'id_creneau' => 'vigile',
                    'type' => 'vigile',
                    'type_creneau' => 'vigile',
                    'heure_debut' => $debut,
                    'heure_fin' => $fin,
                    'libelle' => 'Salut du drapeau',
                    'ordre' => $ordre,
                );
            }

            $debut = sprintf('%02d:%02d', $h, $m);
            $m += $duree_cours;
            $h += intdiv($m, 60);
            $m = $m % 60;
            $fin = sprintf('%02d:%02d', $h, $m);
            $ordre++;
            $creneaux[] = array(
                'id_creneau' => $i,
                'type' => 'cours',
                'type_creneau' => 'cours',
                'heure_debut' => $debut,
                'heure_fin' => $fin,
                'libelle' => "Cours $i",
                'ordre' => $ordre,
            );

            if ($i === $pause_after && $i < $nb) {
                $debut = sprintf('%02d:%02d', $h, $m);
                $m += $duree_pause;
                $h += intdiv($m, 60);
                $m = $m % 60;
                $fin = sprintf('%02d:%02d', $h, $m);
                $ordre++;
                $creneaux[] = array(
                    'id_creneau' => "pause$i",
                    'type' => 'pause',
                    'type_creneau' => 'pause',
                    'heure_debut' => $debut,
                    'heure_fin' => $fin,
                    'libelle' => 'Pause',
                    'ordre' => $ordre,
                );
            }
        }

        return $creneaux;
    }

    /**
     * Construit le contexte complet du solveur à partir de la base.
     *
     * @param object $db       adaptateur exposant query($sql) → result_array()
     * @param int    $id_annee année scolaire active (0 = toutes les sessions fixes)
     * @return array
     */
    public function context($db, $id_annee = 0) {
        $jours = self::rows($db, 'SELECT * FROM jours_semaine WHERE deleted_at IS NULL AND actif = 1 ORDER BY ordre ASC');
        $cours = array();
        foreach (self::creneaux(self::params($db)) as $c) {
            if (($c['type'] ?? '') === 'cours') {
                $cours[] = $c;
            }
        }

        $jour_ids = array();
        foreach ($jours as $j) {
            $jour_ids[] = (int)$j['id_jour'];
        }
        $creneau_ids = array();
        foreach ($cours as $c) {
            $creneau_ids[] = (int)$c['id_creneau'];
        }
        $nb_jours = count($jour_ids);

        // Matières-classes + enseignements
        $mc_rows = self::rows($db, 'SELECT * FROM matieres_classes WHERE deleted_at IS NULL');
        $ens_rows = self::rows($db, 'SELECT * FROM enseignements WHERE deleted_at IS NULL');
        $ens_by_mc = array();
        foreach ($ens_rows as $e) {
            $ens_by_mc[(int)$e['id_matiere_classe']] = $e;
        }
        $mc_map = array();
        foreach ($mc_rows as $mc) {
            $mc_map[(int)$mc['id_matiere_classe']] = $mc;
        }

        // Libellés classes / matières
        $class_lib = array();
        foreach (self::rows($db, 'SELECT id_classe, libelle FROM classes WHERE deleted_at IS NULL ORDER BY ordre ASC, libelle ASC') as $c) {
            $class_lib[(int)$c['id_classe']] = $c['libelle'];
        }
        $mat_lib = array();
        foreach (self::rows($db, 'SELECT id_matiere, libelle, code FROM matieres WHERE deleted_at IS NULL') as $m) {
            $mat_lib[(int)$m['id_matiere']] = array('libelle' => $m['libelle'], 'code' => $m['code']);
        }

        // Indisponibilités enseignants
        $indispos = array();
        foreach (self::rows($db, "SELECT id_enseignant, id_jour, id_creneau FROM disponibilites_enseignants"
            . " WHERE deleted_at IS NULL AND type = 'indisponible'") as $d) {
            $indispos[(int)$d['id_enseignant']][(int)$d['id_jour']][(int)$d['id_creneau']] = true;
        }

        // Sessions fixes
        $sql = 'SELECT * FROM horaires_fixes WHERE deleted_at IS NULL';
        if ((int)$id_annee > 0) {
            $sql .= ' AND id_annee = ' . (int)$id_annee;
        }
        $fix_rows = self::rows($db, $sql);

        $fixes_by_mc = array();
        $valid_fixes = array();
        foreach ($fix_rows as $f) {
            $mc_id = (int)$f['id_matiere_classe'];
            $mc = isset($mc_map[$mc_id]) ? $mc_map[$mc_id] : null;
            if (!$mc || !in_array((int)$f['id_jour'], $jour_ids, true)
                || !in_array((int)$f['id_creneau'], $creneau_ids, true)) {
                continue;
            }
            $ens = isset($ens_by_mc[$mc_id]) ? $ens_by_mc[$mc_id] : null;
            if (!$ens || empty($ens['id_enseignement'])) {
                continue;
            }
            $f['id_matiere'] = (int)$mc['id_matiere'];
            $f['id_enseignement'] = (int)$ens['id_enseignement'];
            $f['id_classe'] = (int)$mc['id_classe'];
            $f['id_jour'] = (int)$f['id_jour'];
            $f['id_creneau'] = (int)$f['id_creneau'];
            $f['id_enseignant'] = (int)$f['id_enseignant'];
            $f['_valid'] = true;
            $fixes_by_mc[$mc_id] = (isset($fixes_by_mc[$mc_id]) ? $fixes_by_mc[$mc_id] : 0) + 1;
            $valid_fixes[] = $f;
        }

        // Sessions automatiques
        $sessions = array();
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
                $sessions[] = array(
                    'id_matiere_classe' => $mc_id,
                    'id_matiere'        => $id_matiere,
                    'id_classe'         => $id_classe,
                    'id_enseignement'   => (int)$ens['id_enseignement'],
                    'id_enseignant'     => $teacher,
                    'daily'             => $daily,
                    'matiere_libelle'   => isset($mat_lib[$id_matiere]) ? $mat_lib[$id_matiere]['libelle'] : '',
                    'matiere_code'      => isset($mat_lib[$id_matiere]) ? $mat_lib[$id_matiere]['code'] : '',
                    'classe_libelle'    => isset($class_lib[$id_classe]) ? $class_lib[$id_classe] : '',
                );
            }
        }

        return array(
            'jours'       => $jours,
            'jour_ids'    => $jour_ids,
            'creneau_ids' => $creneau_ids,
            'nb_slots'    => $nb_jours * count($creneau_ids),
            'sessions'    => $sessions,
            'fix_rows'    => $valid_fixes,
            'indispos'    => $indispos,
            'class_lib'   => $class_lib,
        );
    }

    public function solve(array $ctx, $seed = 0) {
        $best = null;
        $best_unplaced = PHP_INT_MAX;
        $this->seed = (int)$seed;
        $this->deadline = microtime(true) + self::SOLVER_SECONDS;

        $consider = function ($res) use (&$best, &$best_unplaced) {
            $n = count($res['unplaced']);
            if ($n < $best_unplaced) {
                $best_unplaced = $n;
                $best = $res;
            }
            return $n === 0;
        };
        $outOfTime = function () {
            return microtime(true) > $this->deadline;
        };

        // 1) Classe par classe avec retour en arrière : chaque classe doit
        //    occuper la totalité de ses créneaux
        for ($attempt = 0; $attempt < 16 && $best_unplaced > 0; $attempt++) {
            if ($consider($this->_attempt_classwise($ctx, $attempt))) break;
            if ($outOfTime()) break;
        }

        // 2) Appariement bipartite (classe ↔ enseignant) créneau par créneau
        for ($attempt = 0; $attempt < 8 && $best_unplaced > 0; $attempt++) {
            if ($consider($this->_attempt_matching($ctx, $attempt))) break;
            if ($outOfTime()) break;
        }

        // 3) Glouton multi-démarrage
        for ($attempt = 0; $attempt < 10 && $best_unplaced > 0; $attempt++) {
            if ($consider($this->_attempt_greedy($ctx, $attempt))) break;
            if ($outOfTime()) break;
        }

        // 4) Réparation par déplacements (sur la meilleure solution trouvée)
        if ($best_unplaced > 0 && $best !== null) {
            for ($pass = 0; $pass < 3 && $best_unplaced > 0; $pass++) {
                $repaired = $this->_repair($ctx, $best);
                if (count($repaired['unplaced']) >= $best_unplaced) break;
                $best_unplaced = count($repaired['unplaced']);
                $best = $repaired;
            }
        }

        return $best;
    }

    /**
     * Strategie 1 — affectation classe par classe avec retour en arrière.
     * Chaque classe doit occuper la totalité de ses créneaux : on calcule pour
     * chaque classe un appariement parfait sessions ↔ créneaux sous réserve de
     * la disponibilité des enseignants. Si une classe échoue, on revient en
     * arrière et on essaie un autre appariement maximal pour la classe
     * précédente (plusieurs tirages aléatoires).
     */
    private function _attempt_classwise(array $ctx, $seed) {
        $jour_ids = $ctx['jour_ids'];
        $creneau_ids = $ctx['creneau_ids'];
        $indispos = $ctx['indispos'];

        list($classBusy, $teachBusy, $classDaySubj, $classDayLoad, $placed)
            = $this->_init_state($ctx);

        // Sessions par classe
        $byClass = [];
        foreach ($ctx['sessions'] as $i => $s) {
            $byClass[(int)$s['id_classe']][] = $i;
        }

        // Tous les créneaux (une classe occupe chacun exactement une fois)
        $slots = [];
        foreach ($jour_ids as $j) {
            foreach ($creneau_ids as $c) {
                $slots[] = [$j, $c];
            }
        }
        $slotKeys = [];
        foreach ($slots as $k => $sc) {
            $slotKeys[$k] = $sc[0] * 1000 + $sc[1];
        }

        mt_srand($seed * 104729 + $this->seed * 7919 + 7);

        // Les classes les plus contraintes d'abord (peu d'arêtes disponibles)
        $edgesByClass = [];
        foreach ($byClass as $cl => $idxs) {
            $edges = 0;
            foreach ($idxs as $i) {
                $s = $ctx['sessions'][$i];
                $t = (int)$s['id_enseignant'];
                foreach ($slots as $sc) {
                    if (isset($indispos[$t][$sc[0]][$sc[1]])) continue;
                    if (isset($teachBusy[$t][$sc[0]][$sc[1]])) continue;
                    $edges++;
                }
            }
            $edgesByClass[$cl] = $edges;
        }
        asort($edgesByClass);
        $edgeOrder = array_keys($edgesByClass);

        // Variations d'ordre : les classes difficiles doivent être traitées
        // en premier, mais l'ordre exact conditionne fortement le résultat.
        if ($seed < 12) {
            $k = $seed % 6;
            $first = $edgeOrder[$k];
            $rest = array_values(array_diff($edgeOrder, [$first]));
            if (intdiv($seed, 6) === 1) $rest = array_reverse($rest);
            $classOrder = array_merge([$first], $rest);
        } elseif ($seed < 24) {
            $classOrder = array_reverse($edgeOrder);
            $rot = $seed - 12;
            for ($r = 0; $r < $rot; $r++) {
                $classOrder[] = array_shift($classOrder);
            }
        } else {
            $classOrder = $edgeOrder;
            shuffle($classOrder);
        }

        $state = [
            'classBusy'    => $classBusy,
            'teachBusy'    => $teachBusy,
            'classDaySubj' => $classDaySubj,
            'classDayLoad' => $classDayLoad,
            'rows'         => $placed,
            'placed'       => [],
        ];
        $aux = ['slots' => $slots, 'slotKeys' => $slotKeys, 'byClass' => $byClass,
                'total' => count($ctx['sessions'])];

        $best = ['n' => -1, 'state' => $state];
        $budget = self::CW_BUDGET;
        $this->_cw_recurse($ctx, $state, $classOrder, 0, $aux, $budget, $best, $seed);

        $final = $best['state'];
        $placedSessions = $final['placed'];
        $placedRows = $final['rows'];

        // Sessions non placées
        $unplaced = [];
        foreach ($ctx['sessions'] as $i => $s) {
            if (isset($placedSessions[$i])) continue;
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
                'raison'          => 'conflit de recouvrement (créneau déjà pris)',
            ];
        }

        return ['rows' => $placedRows, 'unplaced' => $unplaced];
    }

    /**
     * Retour en arrière sur l'ordre des classes.
     * À chaque niveau : appariement maximal de la classe. Si l'appariement est
     * complet on descend d'un niveau ; sinon on applique quand même le
     * meilleur appariement (pour conserver le meilleur état partiel) et on
     * tente d'autres tirages qui libèrent éventuellement des enseignants pour
     * les classes suivantes.
     */
    private function _cw_recurse(array $ctx, array $state, array $classOrder,
                                 $pos, array $aux, &$budget, &$best, $seed) {
        if ($pos >= count($classOrder)) {
            return true;
        }
        if (isset($aux['total']) && $best['n'] >= $aux['total']) {
            return true;
        }
        if ($budget <= 0) return false;
        if (microtime(true) > $this->deadline) return false;
        $budget--;

        $cl = $classOrder[$pos];
        $idxs = isset($aux['byClass'][$cl]) ? $aux['byClass'][$cl] : [];
        if (!$idxs) {
            return $this->_cw_recurse($ctx, $state, $classOrder, $pos + 1, $aux, $budget, $best, $seed);
        }

        $need = count($idxs);
        $adj = $this->_cw_adjacency($ctx, $state, $cl, $idxs, $aux['slots'], $aux['slotKeys']);

        for ($try = 0; $try < 3; $try++) {
            $match = $this->_cw_kuhn($idxs, $adj, $try > 0);
            $complete = count($match) >= $need;

            $new = $this->_cw_apply($ctx, $state, $cl, $idxs, $match, $aux);
            if (count($new['placed']) > $best['n']) {
                $best['n'] = count($new['placed']);
                $best['state'] = $new;
            }

            $deeper = $this->_cw_recurse($ctx, $new, $classOrder, $pos + 1, $aux, $budget, $best, $seed);
            if ($deeper && $complete) return true;
            if ($budget <= 0) break;
            if (isset($aux['total']) && $best['n'] >= $aux['total']) break;
        }

        return false;
    }

    /** Graphe sessions → créneaux disponibles pour une classe. */
    private function _cw_adjacency(array $ctx, array $state, $cl, array $idxs,
                                   array $slots, array $slotKeys) {
        $indispos = $ctx['indispos'];
        $adj = [];
        foreach ($idxs as $i) {
            $s = $ctx['sessions'][$i];
            $t = (int)$s['id_enseignant'];
            $mat = (int)$s['id_matiere'];
            $daily = (int)$s['daily'];
            $list = [];
            foreach ($slots as $k => $sc) {
                $j = $sc[0];
                $c = $sc[1];
                if (isset($state['classBusy'][$cl][$j][$c])) continue;
                if ($t > 0 && isset($indispos[$t][$j][$c])) continue;
                if ($t > 0 && isset($state['teachBusy'][$t][$j][$c])) continue;
                $daySubj = isset($state['classDaySubj'][$cl][$j]) ? $state['classDaySubj'][$cl][$j] : [];
                if ((isset($daySubj[$mat]) ? $daySubj[$mat] : 0) >= $daily) continue;
                $list[] = $slotKeys[$k];
            }
            $adj[$i] = $list;
        }
        return $adj;
    }

    /** Appariement maximal sessions → créneaux (Kuhn). Retourne session => slotKey. */
    private function _cw_kuhn(array $idxs, array $adj, $shuffle) {
        $work = $adj;
        if ($shuffle) {
            foreach ($work as $k => $v) {
                shuffle($v);
                $work[$k] = $v;
            }
        }

        $seq = $idxs;
        if ($shuffle) {
            shuffle($seq);
        } else {
            usort($seq, function ($a, $b) use ($work) {
                $da = isset($work[$a]) ? count($work[$a]) : 0;
                $db = isset($work[$b]) ? count($work[$b]) : 0;
                if ($da === $db) return $a - $b;
                return $da - $db;
            });
        }

        $matchSlot = [];
        foreach ($seq as $i) {
            $vis = [];
            $this->_kuhn_slot($i, $work, $matchSlot, $vis);
        }

        $res = [];
        foreach ($matchSlot as $sk => $i) {
            $res[$i] = $sk;
        }
        return $res;
    }

    /** Applique un appariement complet à l'état (copie) et renvoie le nouvel état. */
    private function _cw_apply(array $ctx, array $state, $cl, array $idxs,
                               array $match, array $aux) {
        foreach ($idxs as $i) {
            if (!isset($match[$i])) continue;
            $sk = $match[$i];
            $j = intdiv($sk, 1000);
            $c = $sk % 1000;

            $s = $ctx['sessions'][$i];
            $t = (int)$s['id_enseignant'];
            $mat = (int)$s['id_matiere'];

            if (isset($state['classBusy'][$cl][$j][$c])) continue;
            if ($t > 0 && isset($state['teachBusy'][$t][$j][$c])) continue;

            $state['classBusy'][$cl][$j][$c] = true;
            if ($t > 0) $state['teachBusy'][$t][$j][$c] = true;
            $state['classDayLoad'][$cl][$j] = (isset($state['classDayLoad'][$cl][$j]) ? $state['classDayLoad'][$cl][$j] : 0) + 1;
            $state['classDaySubj'][$cl][$j][$mat] = (isset($state['classDaySubj'][$cl][$j][$mat]) ? $state['classDaySubj'][$cl][$j][$mat] : 0) + 1;

            $state['rows'][] = [
                '_type'          => 'auto',
                'id_classe'      => $cl,
                'id_matiere'     => $mat,
                'id_enseignement' => (int)$s['id_enseignement'],
                'id_enseignant'  => $t,
                'id_jour'        => $j,
                'id_creneau'     => $c,
            ];
            $state['placed'][$i] = true;
        }
        return $state;
    }

    /** Kuhn sur le graphe session → créneau. */
    private function _kuhn_slot($i, array $adj, array &$matchSlot, array &$vis) {
        if (!isset($adj[$i])) return false;
        foreach ($adj[$i] as $sk) {
            if (isset($vis[$sk])) continue;
            $vis[$sk] = true;
            if (!isset($matchSlot[$sk]) || $this->_kuhn_slot($matchSlot[$sk], $adj, $matchSlot, $vis)) {
                $matchSlot[$sk] = $i;
                return true;
            }
        }
        return false;
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

        // Sessions restantes par classe (+ compteur par matière)
        $remaining = [];
        $remaining_subj = [];
        foreach ($ctx['sessions'] as $i => $s) {
            $cl = (int)$s['id_classe'];
            $m  = (int)$s['id_matiere'];
            $remaining[$cl][] = $i;
            $remaining_subj[$cl][$m] = (isset($remaining_subj[$cl][$m]) ? $remaining_subj[$cl][$m] : 0) + 1;
        }

        // Liste des créneaux, ordonnés par rareté (créneaux les plus contraints
        // d'abord) puis perturbés aléatoirement selon la tentative.
        $slots = [];
        foreach ($jour_ids as $j) {
            foreach ($creneau_ids as $c) {
                $slots[] = [$j, $c];
            }
        }

        $teacher_set = [];
        foreach ($ctx['sessions'] as $s) {
            $teacher_set[(int)$s['id_enseignant']] = true;
        }
        mt_srand($seed * 7919 + $this->seed * 104729 + 13);
        $scored = [];
        foreach ($slots as $k => $slot) {
            $j = $slot[0];
            $c = $slot[1];
            $n = 0;
            foreach ($teacher_set as $t => $_x) {
                if ($t > 0 && !isset($indispos[$t][$j][$c])) $n++;
            }
            $scored[$k] = $n + mt_rand(0, 4);
        }
        asort($scored);
        $ordered = [];
        foreach ($scored as $k => $_x) {
            $ordered[] = $slots[$k];
        }
        $slots = $ordered;

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
                shuffle($teach);
                $adj[$cl] = $teach;
            }

            // MRV : les classes ayant le moins d'options d'abord
            $optCount = [];
            foreach ($classes as $cl) {
                $optCount[$cl] = count($adj[$cl]) * 10 + mt_rand(0, 9);
            }
            asort($optCount);
            $classes = array_keys($optCount);

            // Kuhn : enseignant → classe
            $matchT = [];
            foreach ($classes as $cl) {
                $vis = [];
                $this->_kuhn($cl, $adj, $matchT, $vis);
            }

            if (!$matchT) continue;

            foreach ($matchT as $t => $cl) {
                // Choisir la session de cette classe avec cet enseignant :
                // on privilégie la matière qui a encore le plus d'heures à placer.
                $pick = null;
                $pickScore = -1;
                foreach ($remaining[$cl] as $i) {
                    $s = $ctx['sessions'][$i];
                    if ((int)$s['id_enseignant'] !== (int)$t) continue;
                    if (isset($teachBusy[$t][$j][$c])) { $pick = null; break; }
                    if (isset($indispos[$t][$j][$c])) { $pick = null; break; }
                    $mat = (int)$s['id_matiere'];
                    if ((isset($classDaySubj[$cl][$j][$mat]) ? $classDaySubj[$cl][$j][$mat] : 0) >= (int)$s['daily']) continue;
                    $score = isset($remaining_subj[$cl][$mat]) ? $remaining_subj[$cl][$mat] : 0;
                    if ($score > $pickScore) {
                        $pickScore = $score;
                        $pick = $i;
                    }
                }
                if ($pick === null) continue;
                if (isset($classBusy[$cl][$j][$c])) continue;

                $s = $ctx['sessions'][$pick];
                $mat = (int)$s['id_matiere'];

                $classBusy[$cl][$j][$c] = true;
                $teachBusy[$t][$j][$c] = true;
                $classDayLoad[$cl][$j] = (isset($classDayLoad[$cl][$j]) ? $classDayLoad[$cl][$j] : 0) + 1;
                $classDaySubj[$cl][$j][$mat] = (isset($classDaySubj[$cl][$j][$mat]) ? $classDaySubj[$cl][$j][$mat] : 0) + 1;
                if (isset($remaining_subj[$cl][$mat])) {
                    $remaining_subj[$cl][$mat]--;
                }

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
        mt_srand($seed * 6151 + $this->seed * 7919 + 17);

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

        // Limite journalière réelle de chaque matière (par classe)
        $dailyMap = [];
        foreach ($ctx['sessions'] as $s) {
            $dailyMap[(int)$s['id_classe'] . '|' . (int)$s['id_matiere']] = (int)$s['daily'];
        }
        foreach ($ctx['fix_rows'] as $f) {
            $k = (int)$f['id_classe'] . '|' . (int)$f['id_matiere'];
            if (!isset($dailyMap[$k])) {
                $dailyMap[$k] = 10;
            }
        }

        // Tous les occupants potentiels (sessions fixes jamais déplacées)
        $candidates = [];
        foreach ($rows as $i => $r) {
            if (isset($r['_type']) && $r['_type'] === 'fixe') continue;
            if ((int)$r['id_enseignant'] === (int)$t && (int)$r['id_jour'] === (int)$j && (int)$r['id_creneau'] === (int)$c) {
                $candidates[] = $i;
            }
        }
        if (!$candidates) return false;

        // Tous les créneaux cibles possibles, les plus proches d'abord
        $targets = [];
        foreach ($jour_ids as $j2) {
            foreach ($creneau_ids as $c2) {
                if ($j2 === $j && $c2 === $c) continue;
                $targets[] = [$j2, $c2];
            }
        }

        foreach ($candidates as $idx) {
            $p = $rows[$idx];
            $p_cl = (int)$p['id_classe'];
            $p_t  = (int)$p['id_enseignant'];
            $p_mat = (int)$p['id_matiere'];
            $dk = $p_cl . '|' . $p_mat;
            $p_daily = isset($dailyMap[$dk]) ? $dailyMap[$dk] : 10;
            $p_daysubj = isset($classDaySubj[$p_cl]) ? $classDaySubj[$p_cl] : [];
            $free_load = isset($classDayLoad[$p_cl]) ? $classDayLoad[$p_cl] : [];

            foreach ($targets as $tgt) {
                $j2 = $tgt[0];
                $c2 = $tgt[1];
                if (isset($classBusy[$p_cl][$j2][$c2])) continue;
                if ($p_t > 0 && isset($teachBusy[$p_t][$j2][$c2])) continue;
                if ($p_t > 0 && isset($indispos[$p_t][$j2][$c2])) continue;
                if ((isset($p_daysubj[$j2][$p_mat]) ? $p_daysubj[$j2][$p_mat] : 0) >= $p_daily) continue;
                if ((isset($free_load[$j2]) ? $free_load[$j2] : 0) >= count($creneau_ids)) continue;

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
}
