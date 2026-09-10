<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bulletins_model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_bulletin_complet($id_classe, $id_annee, $id_periode = null, $cumul = false)
    {
        // Pass 1: Élèves inscrits
        $eleves = $this->db
            ->select("e.id_etudiant, e.matricule, e.fullname, i.id_annee")
            ->from('inscriptions i')
            ->join('etudiants e', 'i.id_etudiant = e.id_etudiant')
            ->where('i.id_classe', $id_classe)
            ->where('i.id_annee', $id_annee)
            ->where('i.deleted_at', null)
            ->where('e.deleted_at', null)
            ->order_by('e.fullname ASC')
            ->get()->result_array();

        if (empty($eleves)) return null;
        $etudiant_ids = array_column($eleves, 'id_etudiant');

        // Pass 2: Matières
        $all_matieres = $this->db
            ->query("
                SELECT DISTINCT m.id_matiere, m.libelle, m.code, m.est_general, m.est_actif, mc.note_max_matiere
                FROM matieres_classes mc
                JOIN matieres m ON mc.id_matiere = m.id_matiere
                WHERE mc.id_classe = ? AND mc.deleted_at IS NULL AND m.deleted_at IS NULL
                ORDER BY m.est_general DESC, m.libelle
            ", [$id_classe])->result_array();

        if (empty($all_matieres)) return null;

        // Séparer matières actives et inactives
        $matieres = [];
        $matieres_inactives = [];
        foreach ($all_matieres as $m) {
            if (intval($m['est_actif']) === 0) {
                $matieres_inactives[] = $m;
            } else {
                $matieres[] = $m;
            }
        }

        // Pass 3: Périodes de l'année scolaire sélectionnée uniquement
        // (les périodes des autres années sont ignorées pour ne pas polluer le bulletin).
        // En mode cumul (fiche de points), on garde toutes les périodes de l'année : l'affichage
        // cumule progressivement selon le trimestre choisi.
        $toutes_periodes = $this->db
            ->where('id_annee', $id_annee)
            ->where('deleted_at', null)
            ->order_by('id_periode ASC')
            ->get('periodes')->result_array();
        if (empty($toutes_periodes)) return null;

        if ($id_periode && $id_periode !== 'all' && !$cumul) {
            $toutes_periodes = array_values(array_filter($toutes_periodes, function ($p) use ($id_periode) {
                return $p['id_periode'] == intval($id_periode);
            }));
            if (empty($toutes_periodes)) return null;
        }

        $periode_ids = array_column($toutes_periodes, 'id_periode');
        $matiere_ids = array_column($matieres, 'id_matiere');
        $matiere_inactif_ids = array_column($matieres_inactives, 'id_matiere');
        $all_matiere_ids = array_merge($matiere_ids, $matiere_inactif_ids);

        // Pass 4: MAXIMA classe (uniquement matières actives)
        $maxima = $this->_get_maxima($id_classe, $periode_ids, $matiere_ids);

        // Pass 5: Notes élèves agrégées — toutes les matières (actives + inactives pour affichage)
        $filtre_notes = ($cumul || !$id_periode || $id_periode === 'all') ? null : $id_periode;
        $notes_map = $this->_get_notes_aggregated($etudiant_ids, $all_matiere_ids, $toutes_periodes, $filtre_notes);

        // Pass 5b: Points de conduite par élève et par période
        $conduite_map = $this->_get_conduite_map($etudiant_ids, $toutes_periodes);

        // Pass 6: Construire le résultat
        $result = $this->_build_result($eleves, $matieres, $toutes_periodes, $notes_map, $maxima, $conduite_map);

        // Pass 6b: Ajouter les matières inactives aux données de chaque élève
        if (!empty($matieres_inactives)) {
            foreach ($result['eleves'] as &$el) {
                $eid = $el['id_etudiant'];
                $el['matieres_inactives'] = [];
                foreach ($matieres_inactives as $mat) {
                    $mid = $mat['id_matiere'];
                    $mn = $notes_map[$eid][$mid] ?? null;
                    $mat_data = ['id_matiere' => $mid, 'libelle' => $mat['libelle'], 'code' => $mat['code'] ?? '', 'est_general' => $mat['est_general'], 'note_max_matiere' => $mat['note_max_matiere'] ?? 0, 'periodes' => []];
                    foreach ($toutes_periodes as $per) {
                        $pid = $per['id_periode'];
                        $p = $mn[$pid] ?? ['tj' => 0, 'comp' => 0, 'ress' => 0, 'ex' => 0];
                        $mat_data['periodes'][$pid] = $p;
                    }
                    $el['matieres_inactives'][] = $mat_data;
                }
            }
            unset($el);
            $result['matieres_inactives'] = $matieres_inactives;
        } else {
            foreach ($result['eleves'] as &$el) {
                $el['matieres_inactives'] = [];
            }
            unset($el);
            $result['matieres_inactives'] = [];
        }

        // Détection dynamique des catégories basée sur les données réelles d'évaluations
        $categories = $this->_detecter_categories($id_classe);
        $has_comp = $categories['competance'];
        $has_ress = $categories['ressource'];
        $has_ex   = $categories['examen'];

        // Déterminer le mode d'affichage
        // Mode B : competance OU ressource présent → TJ / COMP / RESS / TOT
        // Mode A : examen présent (sans competance/ressource) → TJ / EX / TOT
        // Défaut : ni competance, ni ressource, ni examen → TJ / TOT
        $mode_b = $has_comp || $has_ress;
        $mode_a = $has_ex && !$mode_b;

        $result['competences_active'] = ($mode_b && $has_comp) ? 1 : 0;
        $result['ressources_active']  = ($mode_b && $has_ress) ? 1 : 0;
        $result['examen_active']      = $mode_a ? 1 : 0;

        // Pourcentage COMP/RESS
        $result['ressources_pourcentage'] = floatval($this->get_setting('pourcentage_ressources_examen', 60));
        $result['competences_pourcentage'] = floatval($this->get_setting('pourcentage_competences_examen', 40));

        // Neutraliser les colonnes selon le mode
        if ($mode_b) {
            // Mode B : EX neutralisé (examen compté dans TJ si présent)
            foreach ($result['maxima'] as &$mid) {
                foreach ($mid as &$pid) {
                    $pid['ex'] = 0;
                }
            }
            unset($mid, $pid);
            foreach ($result['eleves'] as &$el) {
                foreach ($el['matieres'] as &$m) {
                    foreach ($m['periodes'] as &$per) {
                        $per['ex'] = 0;
                    }
                }
                unset($m, $per);
            }
            unset($el);
        } elseif ($mode_a) {
            // Mode A : COMP et RESS neutralisés
            foreach ($result['maxima'] as &$mid) {
                foreach ($mid as &$pid) {
                    $pid['comp'] = 0;
                    $pid['ress'] = 0;
                }
            }
            unset($mid, $pid);
            foreach ($result['eleves'] as &$el) {
                foreach ($el['matieres'] as &$m) {
                    foreach ($m['periodes'] as &$per) {
                        $per['comp'] = 0;
                        $per['ress'] = 0;
                    }
                }
                unset($m, $per);
            }
            unset($el);
        } else {
            // Défaut : COMP, RESS et EX neutralisés
            foreach ($result['maxima'] as &$mid) {
                foreach ($mid as &$pid) {
                    $pid['comp'] = 0;
                    $pid['ress'] = 0;
                    $pid['ex'] = 0;
                }
            }
            unset($mid, $pid);
            foreach ($result['eleves'] as &$el) {
                foreach ($el['matieres'] as &$m) {
                    foreach ($m['periodes'] as &$per) {
                        $per['comp'] = 0;
                        $per['ress'] = 0;
                        $per['ex'] = 0;
                    }
                }
                unset($m, $per);
            }
            unset($el);
        }

        $result['periode_filtree'] = ($id_periode && $id_periode !== 'all') ? intval($id_periode) : null;
        return $result;
    }

    private function _get_maxima($id_classe, $periode_ids, $matiere_ids)
    {
        if (empty($matiere_ids)) return [];

        $pct_comp = floatval($this->get_setting('pourcentage_competences_examen', 40));
        $pct_ress = floatval($this->get_setting('pourcentage_ressources_examen', 60));

        $coeffs = $this->db
            ->select('id_matiere, note_max_matiere')
            ->from('matieres_classes')
            ->where('id_classe', $id_classe)
            ->where_in('id_matiere', $matiere_ids)
            ->where('deleted_at', null)
            ->get()->result_array();

        $coeff_map = [];
        foreach ($coeffs as $c) {
            $coeff_map[$c['id_matiere']] = floatval($c['note_max_matiere'] ?: 0);
        }

        $maxima = [];
        foreach ($matiere_ids as $mid) {
            $tj = $coeff_map[$mid] ?? 0;
            $comp = round($tj * $pct_comp / 100, 1);
            $ress = round($tj * $pct_ress / 100, 1);
            foreach ($periode_ids as $pid) {
                $maxima[$mid][$pid] = [
                    'tj' => $tj,
                    'comp' => $comp,
                    'ress' => $ress,
                    'ex' => $tj,
                ];
            }
        }
        return $maxima;
    }

    /**
     * Détecte automatiquement les types d'évaluations réellement présents pour une classe.
     * Retourne ['competance' => bool, 'ressource' => bool, 'examen' => bool]
     */
    public function _detecter_categories($id_classe)
    {
        $rows = $this->db->query("
            SELECT ev.type, COUNT(*) AS cnt
            FROM evaluations ev
            WHERE ev.id_classe = ? AND ev.deleted_at IS NULL
            GROUP BY ev.type
        ", [$id_classe])->result_array();

        $types = [];
        foreach ($rows as $r) {
            $types[$r['type']] = (int)$r['cnt'];
        }

        return [
            'competance' => isset($types['competance']) && $types['competance'] > 0,
            'ressource'  => isset($types['ressource'])  && $types['ressource'] > 0,
            'examen'     => isset($types['examen'])     && $types['examen'] > 0,
        ];
    }

    /**
     * Mapping unifié des types d'évaluation → catégories TJ / COMP / RESS.
     * Basé sur l'enum réelle de la table evaluations :
     *   TJ   = interrogation + devoir
     *   COMP = competance
     *   RESS = ressource
     *   (examen est compté séparément si présent, mais pas dans COMP/RESS)
     */
    private static $TYPE_MAP = [
        'interrogation' => 'tj',
        'devoir'        => 'tj',
        'competance'    => 'comp',
        'ressource'     => 'ress',
        'examen'        => 'tj',  // examen ≈ travail journalier avancé, groupé avec TJ
    ];

    private function _get_notes_aggregated($etudiant_ids, $matiere_ids, $periodes, $id_periode_filter = null)
    {
        $cases = [];
        $bindings = [];

        $periodes_notes = $periodes;
        if ($id_periode_filter && $id_periode_filter !== 'all') {
            $periodes_notes = array_filter($periodes, function($p) use ($id_periode_filter) {
                return $p['id_periode'] == $id_periode_filter;
            });
        }

        foreach ($periodes_notes as $per) {
            $pid = $per['id_periode'];
            $cases["note_tj_{$pid}"]   = "SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ('interrogation', 'devoir') THEN n.note ELSE 0 END)";
            $cases["note_comp_{$pid}"] = "SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'competance' THEN n.note ELSE 0 END)";
            $cases["note_ress_{$pid}"] = "SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'ressource' THEN n.note ELSE 0 END)";
            $cases["note_ex_{$pid}"]   = "SUM(CASE WHEN ev.id_periode = ? AND ev.type = 'examen' THEN n.note ELSE 0 END)";
            $bindings[] = $pid;
            $bindings[] = $pid;
            $bindings[] = $pid;
            $bindings[] = $pid;
        }

        $etudiant_placeholders = str_repeat('?,', count($etudiant_ids) - 1) . '?';
        $matiere_placeholders = str_repeat('?,', count($matiere_ids) - 1) . '?';

        $select_parts = [];
        foreach ($cases as $alias => $sql) {
            $select_parts[] = "{$sql} AS `{$alias}`";
        }
        $select_cols = implode(', ', $select_parts);
        $sql = "
            SELECT n.id_etudiant, ev.id_matiere, {$select_cols}
            FROM notes n
            JOIN evaluations ev ON ev.id_evaluation = n.id_evaluation
            WHERE n.id_etudiant IN ({$etudiant_placeholders})
            AND ev.id_matiere IN ({$matiere_placeholders})
            AND n.deleted_at IS NULL
            AND ev.deleted_at IS NULL
            GROUP BY n.id_etudiant, ev.id_matiere
        ";

        $rows = $this->db->query($sql, array_merge($bindings, $etudiant_ids, $matiere_ids))->result_array();

        // Initialiser toutes les périodes à 0
        $notes_map = [];
        foreach ($rows as $r) {
            $eid = $r['id_etudiant'];
            $mid = $r['id_matiere'];
            $entry = [];
            foreach ($periodes as $per) {
                $pid = $per['id_periode'];
                $key_tj = "note_tj_{$pid}";
                $key_comp = "note_comp_{$pid}";
                $key_ress = "note_ress_{$pid}";
                $key_ex = "note_ex_{$pid}";
                $entry[$pid] = [
                    'tj' => isset($r[$key_tj]) ? floatval($r[$key_tj]) : 0,
                    'comp' => isset($r[$key_comp]) ? floatval($r[$key_comp]) : 0,
                    'ress' => isset($r[$key_ress]) ? floatval($r[$key_ress]) : 0,
                    'ex' => isset($r[$key_ex]) ? floatval($r[$key_ex]) : 0,
                ];
            }
            $notes_map[$eid][$mid] = $entry;
        }
        return $notes_map;
    }

    private function _get_conduite_map($etudiant_ids, $periodes)
    {
        if (empty($etudiant_ids) || empty($periodes)) return [];

        $periode_ids = array_column($periodes, 'id_periode');
        $etudiant_placeholders = str_repeat('?,', count($etudiant_ids) - 1) . '?';
        $periode_placeholders = str_repeat('?,', count($periode_ids) - 1) . '?';

        $rows = $this->db->query("
            SELECT pc.id_etudiant, pc.id_periode, pc.points_initial, pc.points_retires, pc.observation
            FROM points_conduite pc
            WHERE pc.id_etudiant IN ({$etudiant_placeholders})
            AND pc.id_periode IN ({$periode_placeholders})
            AND pc.deleted_at IS NULL
        ", array_merge($etudiant_ids, $periode_ids))->result_array();

        $map = [];
        foreach ($rows as $r) {
            $initial = floatval($r['points_initial'] ?: 0);
            $retires = floatval($r['points_retires'] ?: 0);
            $map[$r['id_etudiant']][$r['id_periode']] = [
                'points_initial' => $initial,
                'points_retires' => $retires,
                'points' => round($initial - $retires, 2),
                'observation' => $r['observation'],
            ];
        }
        return $map;
    }

    private function _build_result($eleves, $matieres, $periodes, $notes_map, $maxima, $conduite_map = array())
    {
        $first_ins = $this->db
            ->select('c.libelle as classe, a.libelle as annee, s.libelle as section')
            ->from('inscriptions i')
            ->join('classes c', 'i.id_classe = c.id_classe')
            ->join('sections s', 'c.id_section = s.id_section', 'left')
            ->join('annees_scolaires a', 'i.id_annee = a.id_annee')
            ->where('i.id_etudiant', $eleves[0]['id_etudiant'])
            ->where('i.deleted_at', null)
            ->get()->row_array();

        $result = [
            'classe' => $first_ins['classe'] ?? '',
            'section' => $first_ins['section'] ?? '',
            'annee_scolaire' => $first_ins['annee'] ?? '',
            'periodes' => $periodes,
            'matieres' => $matieres,
            'eleves' => [],
            'maxima' => $maxima,
        ];

        foreach ($eleves as $el) {
            $eid = $el['id_etudiant'];
            $eleve_notes = $notes_map[$eid] ?? [];

            $eleve_data = [
                'id_etudiant' => $eid,
                'fullname' => $el['fullname'],
                'matricule' => $el['matricule'],
                'matieres' => [],
                'points_conduite' => $conduite_map[$eid] ?? [],
                'totaux_periodes' => [],
                'total_annuel' => ['tj' => 0, 'comp' => 0, 'ress' => 0, 'ex' => 0, 'tot' => 0],
                'moyenne' => 0,
                'pourcentage' => 0,
            ];

            $annee_note = 0;

            foreach ($periodes as $per) {
                $pid = $per['id_periode'];
                $per_tj = 0; $per_comp = 0; $per_ress = 0; $per_ex = 0;

                foreach ($matieres as $mat) {
                    $mid = $mat['id_matiere'];
                    $mn = $eleve_notes[$mid][$pid] ?? null;
                    if ($mn) {
                        $per_tj += $mn['tj'];
                        $per_comp += $mn['comp'];
                        $per_ress += $mn['ress'];
                        $per_ex += $mn['ex'];
                    }
                }

                $per_tot = $per_tj + $per_comp + $per_ress + $per_ex;
                $eleve_data['totaux_periodes'][$pid] = [
                    'tj' => $per_tj, 'comp' => $per_comp, 'ress' => $per_ress, 'ex' => $per_ex, 'tot' => $per_tot,
                ];
                $annee_note += $per_tot;
            }

            // Par matière
            foreach ($matieres as $mat) {
                $mid = $mat['id_matiere'];
                $mn = $eleve_notes[$mid] ?? null;
                $mat_data = ['id_matiere' => $mid, 'libelle' => $mat['libelle'], 'code' => $mat['code'] ?? '', 'periodes' => []];
                $mat_annee_note = 0;

                foreach ($periodes as $per) {
                    $pid = $per['id_periode'];
                    $p = $mn[$pid] ?? ['tj' => 0, 'comp' => 0, 'ress' => 0, 'ex' => 0];
                    $note = $p['tj'] + $p['comp'] + $p['ress'] + ($p['ex'] ?? 0);
                    $mat_data['periodes'][$pid] = $p;
                    $mat_annee_note += $note;
                }

                $mat_max = 0;
                foreach ($periodes as $per) {
                    $pid = $per['id_periode'];
                    $mx = $maxima[$mid][$pid] ?? ['tj' => 0, 'comp' => 0, 'ress' => 0, 'ex' => 0];
                    $mat_max += ($mx['tj'] ?? 0) + ($mx['comp'] ?? 0) + ($mx['ress'] ?? 0) + ($mx['ex'] ?? 0);
                }

                $mat_data['annuel'] = [
                    'note' => $mat_annee_note,
                    'max' => $mat_max,
                    'pct' => $mat_max > 0 ? round(($mat_annee_note / $mat_max) * 100, 2) : 0,
                ];
                $eleve_data['matieres'][] = $mat_data;
            }

            // Calcul total annuel avec max
            $annee_max = 0;
            foreach ($matieres as $mat) {
                $mid = $mat['id_matiere'];
                foreach ($periodes as $per) {
                    $pid = $per['id_periode'];
                    $mx = $maxima[$mid][$pid] ?? ['tj' => 0, 'comp' => 0, 'ress' => 0, 'ex' => 0];
                    $annee_max += ($mx['tj'] ?? 0) + ($mx['comp'] ?? 0) + ($mx['ress'] ?? 0) + ($mx['ex'] ?? 0);
                }
            }

            $eleve_data['total_annuel']['tot'] = $annee_note;
            $eleve_data['moyenne'] = $annee_max > 0 ? round(($annee_note / $annee_max) * 20, 2) : 0;
            $eleve_data['pourcentage'] = $annee_max > 0 ? round(($annee_note / $annee_max) * 100, 1) : 0;
            $eleve_data['mention'] = get_mention($eleve_data['moyenne'], 20);

            $result['eleves'][] = $eleve_data;
        }

        // Totaux classe
        $result['totaux_classe'] = $this->_calculer_totaux_classe($result['eleves'], $periodes, $maxima, $matieres);

        // Maxima période regroupés
        foreach ($periodes as $per) {
            $pid = $per['id_periode'];
            $mtj = 0; $mcomp = 0; $mress = 0; $mex = 0;
            foreach ($matieres as $mat) {
                $mx = $maxima[$mat['id_matiere']][$pid] ?? ['tj' => 0, 'comp' => 0, 'ress' => 0, 'ex' => 0];
                $mtj += $mx['tj'] ?? 0;
                $mcomp += $mx['comp'] ?? 0;
                $mress += $mx['ress'] ?? 0;
                $mex += $mx['ex'] ?? 0;
            }
            $result['maxima_periode'][$pid] = ['tj' => $mtj, 'comp' => $mcomp, 'ress' => $mress, 'ex' => $mex, 'tot' => $mtj + $mcomp + $mress + $mex];
        }

        return $result;
    }

    private function _calculer_totaux_classe($eleves, $periodes, $maxima, $matieres)
    {
        $totaux = ['periodes' => [], 'annuel' => ['tj' => 0, 'comp' => 0, 'ress' => 0, 'ex' => 0, 'tot' => 0]];

        foreach ($periodes as $per) {
            $pid = $per['id_periode'];
            $t = ['tj' => 0, 'comp' => 0, 'ress' => 0, 'ex' => 0, 'tot' => 0];
            foreach ($eleves as $el) {
                $pt = $el['totaux_periodes'][$pid] ?? [];
                $t['tj'] += $pt['tj'] ?? 0;
                $t['comp'] += $pt['comp'] ?? 0;
                $t['ress'] += $pt['ress'] ?? 0;
                $t['ex'] += $pt['ex'] ?? 0;
                $t['tot'] += $pt['tot'] ?? 0;
            }
            $totaux['periodes'][$pid] = $t;
        }

        $totaux['annuel'] = [
            'tj' => array_sum(array_column($totaux['periodes'], 'tj')),
            'comp' => array_sum(array_column($totaux['periodes'], 'comp')),
            'ress' => array_sum(array_column($totaux['periodes'], 'ress')),
            'ex' => array_sum(array_column($totaux['periodes'], 'ex')),
            'tot' => array_sum(array_column($totaux['periodes'], 'tot')),
        ];

        return $totaux;
    }
}
