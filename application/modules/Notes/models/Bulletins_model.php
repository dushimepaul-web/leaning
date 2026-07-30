<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bulletins_model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_bulletin_complet($id_classe, $id_annee, $id_periode = null)
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

        // Pass 3: Périodes — TOUJOURS toutes les périodes de l'année pour l'affichage complet
        $toutes_periodes = $this->db
            ->where('id_annee', $id_annee)
            ->where('deleted_at', null)
            ->order_by('id_periode ASC')
            ->get('periodes')->result_array();
        if (empty($toutes_periodes)) return null;

        $periode_ids = array_column($toutes_periodes, 'id_periode');
        $matiere_ids = array_column($matieres, 'id_matiere');

        // Pass 4: MAXIMA classe (depuis coefficient matieres_classes)
        $maxima = $this->_get_maxima($id_classe, $periode_ids, $matiere_ids);

        // Pass 5: Notes élèves agrégées — filtrées par période si spécifiée
        $notes_map = $this->_get_notes_aggregated($etudiant_ids, $matiere_ids, $toutes_periodes, $id_periode);

        // Pass 6: Construire le résultat
        $result = $this->_build_result($eleves, $matieres, $toutes_periodes, $notes_map, $maxima);
        $result['periode_filtree'] = ($id_periode && $id_periode !== 'all') ? intval($id_periode) : null;
        return $result;
    }

    private function _get_maxima($id_classe, $periode_ids, $matiere_ids)
    {
        if (empty($matiere_ids)) return [];

        $coeffs = $this->db
            ->select('id_matiere, coefficient')
            ->from('matieres_classes')
            ->where('id_classe', $id_classe)
            ->where_in('id_matiere', $matiere_ids)
            ->where('deleted_at', null)
            ->get()->result_array();

        $coeff_map = [];
        foreach ($coeffs as $c) {
            $coeff = floatval($c['coefficient'] ?: 1);
            $coeff_map[$c['id_matiere']] = $coeff;
        }

        $maxima = [];
        foreach ($matiere_ids as $mid) {
            $coeff = $coeff_map[$mid] ?? 1;
            $tj = $coeff;
            $comp = round($coeff * 0.6, 1);
            $ress = round($coeff * 0.4, 1);
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

    private function _build_result($eleves, $matieres, $periodes, $notes_map, $maxima)
    {
        $first_ins = $this->db
            ->select('c.libelle as classe, a.libelle as annee')
            ->from('inscriptions i')
            ->join('classes c', 'i.id_classe = c.id_classe')
            ->join('annees_scolaires a', 'i.id_annee = a.id_annee')
            ->where('i.id_etudiant', $eleves[0]['id_etudiant'])
            ->where('i.deleted_at', null)
            ->get()->row_array();

        $result = [
            'classe' => $first_ins['classe'] ?? '',
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
