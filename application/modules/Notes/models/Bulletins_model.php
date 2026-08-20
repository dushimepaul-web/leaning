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
        $matieres = $this->db
            ->query("
                SELECT DISTINCT m.id_matiere, m.libelle, m.code
                FROM matieres_classes mc
                JOIN matieres m ON mc.id_matiere = m.id_matiere
                WHERE mc.id_classe = ? AND mc.deleted_at IS NULL AND m.deleted_at IS NULL
                ORDER BY m.libelle
            ", [$id_classe])->result_array();

        if (empty($matieres)) return null;

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

        // Pass 4: MAXIMA classe (depuis coefficient matieres_classes)
        $maxima = $this->_get_maxima($id_classe, $periode_ids, $matiere_ids);

        // Pass 5: Notes élèves agrégées — filtrées par période si spécifiée (sauf mode cumul)
        $filtre_notes = ($cumul || !$id_periode || $id_periode === 'all') ? null : $id_periode;
        $notes_map = $this->_get_notes_aggregated($etudiant_ids, $matiere_ids, $toutes_periodes, $filtre_notes);

        // Pass 5b: Points de conduite par élève et par période
        $conduite_map = $this->_get_conduite_map($etudiant_ids, $toutes_periodes);

        // Pass 6: Construire le résultat
        $result = $this->_build_result($eleves, $matieres, $toutes_periodes, $notes_map, $maxima, $conduite_map);

        // Activations Ressources/Compétences : niveau classe si renseigné, sinon global
        $classe = $this->db->select('ressources_active, competences_active, ressources_pourcentage, competences_pourcentage')
            ->from('classes')->where('id_classe', $id_classe)->where('deleted_at', null)->get()->row_array();
        $result['ressources_active'] = ($classe && $classe['ressources_active'] !== null)
            ? intval($classe['ressources_active'])
            : intval($this->get_setting('ressources_active', 1));
        $result['competences_active'] = ($classe && $classe['competences_active'] !== null)
            ? intval($classe['competences_active'])
            : intval($this->get_setting('competences_active', 1));
        $result['ressources_pourcentage'] = ($classe && $classe['ressources_pourcentage'] !== null)
            ? floatval($classe['ressources_pourcentage'])
            : floatval($this->get_setting('pourcentage_ressources_examen', 60));
        $result['competences_pourcentage'] = ($classe && $classe['competences_pourcentage'] !== null)
            ? floatval($classe['competences_pourcentage'])
            : floatval($this->get_setting('pourcentage_competences_examen', 40));

        // Neutraliser la catégorie désactivée : l'EX absorbe tout le TJ (EX = TJ)
        // Les deux actifs : RESS = TJ×%, COMP = TJ×%  |  Un seul actif : la catégorie active = TJ (100%), l'autre = 0
        $ress_active = intval($result['ressources_active']) !== 0;
        $comp_active = intval($result['competences_active']) !== 0;
        if (!$ress_active || !$comp_active) {
            $cat_active = $ress_active ? 'ress' : 'comp';
            foreach ($result['maxima'] as &$mid) {
                foreach ($mid as &$pid) {
                    if ($cat_active === 'ress') { $pid['ress'] = $pid['tj']; $pid['comp'] = 0; }
                    else { $pid['comp'] = $pid['tj']; $pid['ress'] = 0; }
                }
            }
            unset($mid, $pid);
            foreach ($result['eleves'] as &$el) {
                foreach ($el['matieres'] as &$m) {
                    foreach ($m['periodes'] as &$per) {
                        if ($cat_active === 'ress') { $per['comp'] = 0; }
                        else { $per['ress'] = 0; }
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

        $facteur_points = floatval($this->get_setting('facteur_points_heure', 15));
        $pct_comp = floatval($this->get_setting('pourcentage_competences_examen', 40));
        $pct_ress = floatval($this->get_setting('pourcentage_ressources_examen', 60));

        // Coefficient calculé = nb_heures_par_semaine × facteur_points_heure → TJ = ce coefficient calculé
        // (comp et ress = pourcentages configurés dans Paramètres du TJ)
        $coeffs = $this->db
            ->select('id_matiere, nb_heures_par_semaine')
            ->from('matieres_classes')
            ->where('id_classe', $id_classe)
            ->where_in('id_matiere', $matiere_ids)
            ->where('deleted_at', null)
            ->get()->result_array();

        $coeff_map = [];
        foreach ($coeffs as $c) {
            $heures = floatval($c['nb_heures_par_semaine'] ?: 0);
            $coeff_map[$c['id_matiere']] = $heures * $facteur_points;
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
                ];
            }
        }
        return $maxima;
    }

    private function _get_notes_aggregated($etudiant_ids, $matiere_ids, $periodes, $id_periode_filter = null)
    {
        $etudiant_str = implode(',', $etudiant_ids);
        $matiere_str = implode(',', $matiere_ids);

        $cases = [];
        $bindings = [];

        // Si filtre sur une période spécifique, ne garder que cette période pour les notes
        $periodes_notes = $periodes;
        if ($id_periode_filter && $id_periode_filter !== 'all') {
            $periodes_notes = array_filter($periodes, function($p) use ($id_periode_filter) {
                return $p['id_periode'] == $id_periode_filter;
            });
        }

        foreach ($periodes_notes as $per) {
            $pid = $per['id_periode'];
            foreach (['tj' => ["'interrogation'", "'devoir'"], 'comp' => ["'composition'", "'examen'"], 'ress' => ["'tp'"]] as $cat => $types) {
                $type_cond = implode(',', $types);
                $cases["note_{$cat}_{$pid}"] = "SUM(CASE WHEN ev.id_periode = ? AND ev.type IN ({$type_cond}) THEN n.note ELSE 0 END)";
                $bindings[] = $pid;
            }
        }

        $select_cols = implode(', ', $cases);
        $sql = "
            SELECT n.id_etudiant, ev.id_matiere, {$select_cols}
            FROM notes n
            JOIN evaluations ev ON ev.id_evaluation = n.id_evaluation
            WHERE n.id_etudiant IN ({$etudiant_str})
            AND ev.id_matiere IN ({$matiere_str})
            AND n.deleted_at IS NULL
            AND ev.deleted_at IS NULL
            GROUP BY n.id_etudiant, ev.id_matiere
        ";

        $rows = $this->db->query($sql, $bindings)->result_array();

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
                $entry[$pid] = [
                    'tj' => isset($r[$key_tj]) ? floatval($r[$key_tj]) : 0,
                    'comp' => isset($r[$key_comp]) ? floatval($r[$key_comp]) : 0,
                    'ress' => isset($r[$key_ress]) ? floatval($r[$key_ress]) : 0,
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
        $etudiant_str = implode(',', $etudiant_ids);
        $periode_str = implode(',', $periode_ids);

        $rows = $this->db->query("
            SELECT pc.id_etudiant, pc.id_periode, pc.points_initial, pc.points_retires, pc.observation
            FROM points_conduite pc
            WHERE pc.id_etudiant IN ({$etudiant_str})
            AND pc.id_periode IN ({$periode_str})
            AND pc.deleted_at IS NULL
        ")->result_array();

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
                'total_annuel' => ['tj' => 0, 'comp' => 0, 'ress' => 0, 'tot' => 0],
                'moyenne' => 0,
                'pourcentage' => 0,
            ];

            $annee_note = 0;

            foreach ($periodes as $per) {
                $pid = $per['id_periode'];
                $per_tj = 0; $per_comp = 0; $per_ress = 0;

                foreach ($matieres as $mat) {
                    $mid = $mat['id_matiere'];
                    $mn = $eleve_notes[$mid][$pid] ?? null;
                    if ($mn) {
                        $per_tj += $mn['tj'];
                        $per_comp += $mn['comp'];
                        $per_ress += $mn['ress'];
                    }
                }

                $per_tot = $per_tj + $per_comp + $per_ress;
                $eleve_data['totaux_periodes'][$pid] = [
                    'tj' => $per_tj, 'comp' => $per_comp, 'ress' => $per_ress, 'tot' => $per_tot,
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
                    $p = $mn[$pid] ?? ['tj' => 0, 'comp' => 0, 'ress' => 0];
                    $note = $p['tj'] + $p['comp'] + $p['ress'];
                    $mat_data['periodes'][$pid] = $p;
                    $mat_annee_note += $note;
                }

                $mat_max = 0;
                foreach ($periodes as $per) {
                    $pid = $per['id_periode'];
                    $mx = $maxima[$mid][$pid] ?? ['tj' => 0, 'comp' => 0, 'ress' => 0];
                    $mat_max += ($mx['tj'] ?? 0) + ($mx['comp'] ?? 0) + ($mx['ress'] ?? 0);
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
                    $mx = $maxima[$mid][$pid] ?? ['tj' => 0, 'comp' => 0, 'ress' => 0];
                    $annee_max += ($mx['tj'] ?? 0) + ($mx['comp'] ?? 0) + ($mx['ress'] ?? 0);
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
            $mtj = 0; $mcomp = 0; $mress = 0;
            foreach ($matieres as $mat) {
                $mx = $maxima[$mat['id_matiere']][$pid] ?? ['tj' => 0, 'comp' => 0, 'ress' => 0];
                $mtj += $mx['tj'] ?? 0;
                $mcomp += $mx['comp'] ?? 0;
                $mress += $mx['ress'] ?? 0;
            }
            $result['maxima_periode'][$pid] = ['tj' => $mtj, 'comp' => $mcomp, 'ress' => $mress, 'tot' => $mtj + $mcomp + $mress];
        }

        return $result;
    }

    private function _calculer_totaux_classe($eleves, $periodes, $maxima, $matieres)
    {
        $totaux = ['periodes' => [], 'annuel' => ['tj' => 0, 'comp' => 0, 'ress' => 0, 'tot' => 0]];

        foreach ($periodes as $per) {
            $pid = $per['id_periode'];
            $t = ['tj' => 0, 'comp' => 0, 'ress' => 0, 'tot' => 0];
            foreach ($eleves as $el) {
                $pt = $el['totaux_periodes'][$pid] ?? [];
                $t['tj'] += $pt['tj'] ?? 0;
                $t['comp'] += $pt['comp'] ?? 0;
                $t['ress'] += $pt['ress'] ?? 0;
                $t['tot'] += $pt['tot'] ?? 0;
            }
            $totaux['periodes'][$pid] = $t;
        }

        $totaux['annuel'] = [
            'tj' => array_sum(array_column($totaux['periodes'], 'tj')),
            'comp' => array_sum(array_column($totaux['periodes'], 'comp')),
            'ress' => array_sum(array_column($totaux['periodes'], 'ress')),
            'tot' => array_sum(array_column($totaux['periodes'], 'tot')),
        ];

        return $totaux;
    }
}
