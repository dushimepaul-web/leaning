<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Base.php';

class Minervale_solde extends Minervale_base
{
    protected $dette;
    protected $audit;

    public function __construct($db, $ci = null, ?Minervale_dette $dette = null, ?Minervale_audit $audit = null)
    {
        parent::__construct($db, $ci);
        $this->dette = $dette;
        $this->audit = $audit;
    }

    public static function calculerSituation($montant_initial, $exoneration = 0, $paye_brut = 0, $rembourse = 0, $statut_dette = '')
    {
        $montant_initial = round(max(0, floatval($montant_initial)), 2);
        $exoneration = round(max(0, min($montant_initial, floatval($exoneration))), 2);
        $paye_brut = round(max(0, floatval($paye_brut)), 2);
        $rembourse = round(max(0, floatval($rembourse)), 2);
        $net_du = round(max(0, $montant_initial - $exoneration), 2);
        $paye_net = round(max(0, $paye_brut - $rembourse), 2);
        $solde = round(max(0, $net_du - $paye_net), 2);
        $trop_percu = round(max(0, $paye_net - $net_du), 2);

        if ($statut_dette === 'annule') {
            $situation = 'annule';
            $solde = 0.0;
            $trop_percu = 0.0;
        } elseif ($trop_percu > 0.009) {
            $situation = 'trop_percu';
        } elseif ($montant_initial <= 0.009) {
            $situation = 'paye';
        } elseif ($net_du <= 0.009) {
            $situation = 'exonere_total';
        } elseif ($solde <= 0.009) {
            $situation = 'paye';
        } elseif ($paye_net > 0.009) {
            $situation = 'partiel';
        } elseif ($exoneration > 0.009) {
            $situation = 'exonere_partiel';
        } else {
            $situation = 'impaye';
        }

        return array(
            'montant_initial' => $montant_initial,
            'exoneration' => $exoneration,
            'net_du' => $net_du,
            'paye_brut' => $paye_brut,
            'remboursements' => $rembourse,
            'paye_net' => $paye_net,
            'solde' => $solde,
            'trop_percu' => $trop_percu,
            'situation' => $situation,
            'situation_libelle' => self::situationLibelle($situation)
        );
    }

    public static function situationLibelle($situation)
    {
        $labels = array(
            'impaye' => 'Non payé',
            'partiel' => 'Payé partiellement',
            'paye' => 'Payé en totalité',
            'exonere_partiel' => 'Exonéré partiellement',
            'exonere_total' => 'Exonéré totalement',
            'trop_percu' => 'Trop-perçu',
            'annule' => 'Annulé'
        );
        return isset($labels[$situation]) ? $labels[$situation] : $situation;
    }

    protected function _detteService()
    {
        if (!$this->dette) {
            require_once __DIR__ . '/Dette.php';
            $this->dette = new Minervale_dette($this->db, $this->ci);
        }
        return $this->dette;
    }

    public function charger($filters = array())
    {
        $statut_filtre = !empty($filters['statut']) ? $filters['statut'] : null;
        unset($filters['statut']);

        $dettes = $this->_detteService()->listerBrut($filters);
        if (empty($dettes)) {
            return array();
        }

        $ids = array();
        $years = array();
        foreach ($dettes as $d) {
            $ids[] = (int)$d['id_dette'];
            $years[(int)$d['id_annee']] = (int)$d['id_annee'];
        }

        $exonerations = $this->_chargerExonerations(array_values($years));
        $affectations = $this->_chargerAffectations($ids);
        $remboursements = $this->_chargerRemboursements($ids);
        $legacy = $this->_chargerPaiementsLegacy(array_values($years));

        $rows = array();
        foreach ($dettes as $dette) {
            $id_dette = (int)$dette['id_dette'];
            $exoneration = $this->_exonerationPour($dette, $exonerations);
            $paye_brut = isset($affectations[$id_dette]) ? $affectations[$id_dette] : 0;
            $legacy_key = (int)$dette['id_etudiant'] . ':' . (int)$dette['id_frais'];
            if (isset($legacy[$legacy_key])) {
                $paye_brut += $legacy[$legacy_key];
            }
            $rembourse = isset($remboursements[$id_dette]) ? $remboursements[$id_dette] : 0;

            $calcul = self::calculerSituation(
                $dette['montant_initial'],
                $exoneration,
                $paye_brut,
                $rembourse,
                $dette['statut']
            );

            $rows[] = array_merge($dette, $calcul, array(
                'statut_stocke' => $dette['statut'],
                'statut' => $calcul['situation']
            ));
        }

        if ($statut_filtre !== null) {
            $rows = array_values(array_filter($rows, function ($row) use ($statut_filtre) {
                return $row['situation'] === $statut_filtre;
            }));
        }
        return $rows;
    }

    protected function _chargerExonerations(array $years)
    {
        if (empty($years)) {
            return array();
        }
        $in = $this->inClause($years);
        return $this->rows(
            "SELECT id_etudiant, id_frais, id_annee, mode, valeur
             FROM exonerations
             WHERE deleted_at IS NULL AND statut = 'active' AND statut_demande = 'approuvee'
               AND id_annee IN (" . $in['sql'] . ')',
            $in['binds']
        );
    }

    protected function _chargerAffectations(array $ids)
    {
        $map = array();
        foreach (array_chunk($ids, 500) as $chunk) {
            $in = $this->inClause($chunk);
            $rows = $this->rows(
                "SELECT pa.id_dette, SUM(pa.montant) AS total
                 FROM paiement_affectations pa
                 JOIN paiements p ON p.id_paiement = pa.id_paiement
                 WHERE pa.deleted_at IS NULL AND p.deleted_at IS NULL AND p.statut <> 'annule'
                   AND pa.id_dette IN (" . $in['sql'] . ')
                 GROUP BY pa.id_dette',
                $in['binds']
            );
            foreach ($rows as $row) {
                $map[(int)$row['id_dette']] = floatval($row['total']);
            }
        }
        return $map;
    }

    protected function _chargerRemboursements(array $ids)
    {
        $map = array();
        foreach (array_chunk($ids, 500) as $chunk) {
            $in = $this->inClause($chunk);
            $rows = $this->rows(
                "SELECT id_dette, SUM(montant) AS total
                 FROM remboursements
                 WHERE deleted_at IS NULL AND statut = 'effectue'
                   AND id_dette IS NOT NULL AND id_dette IN (" . $in['sql'] . ')
                 GROUP BY id_dette',
                $in['binds']
            );
            foreach ($rows as $row) {
                $map[(int)$row['id_dette']] = floatval($row['total']);
            }
        }
        return $map;
    }

    protected function _chargerPaiementsLegacy(array $years)
    {
        if (empty($years)) {
            return array();
        }
        $in = $this->inClause($years);
        $rows = $this->rows(
            "SELECT p.id_etudiant, p.id_frais, SUM(p.montant) AS total
             FROM paiements p
             WHERE p.deleted_at IS NULL AND p.statut <> 'annule'
               AND p.id_annee IN (" . $in['sql'] . ')
               AND NOT EXISTS (
                   SELECT 1 FROM paiement_affectations pa
                   WHERE pa.id_paiement = p.id_paiement
               )
             GROUP BY p.id_etudiant, p.id_frais',
            $in['binds']
        );
        $map = array();
        foreach ($rows as $row) {
            $map[(int)$row['id_etudiant'] . ':' . (int)$row['id_frais']] = floatval($row['total']);
        }
        return $map;
    }

    protected function _exonerationPour(array $dette, array $exonerations)
    {
        $base = floatval($dette['montant_initial']);
        $total = 0;
        foreach ($exonerations as $exo) {
            if ((int)$exo['id_etudiant'] !== (int)$dette['id_etudiant']) {
                continue;
            }
            if ((int)$exo['id_annee'] !== (int)$dette['id_annee']) {
                continue;
            }
            if (!empty($exo['id_frais']) && (int)$exo['id_frais'] !== (int)$dette['id_frais']) {
                continue;
            }
            $total += $exo['mode'] === 'pourcentage'
                ? $base * floatval($exo['valeur']) / 100
                : floatval($exo['valeur']);
        }
        return max(0, min($base, $total));
    }

    public function synthese($filters = array())
    {
        $rows = $this->charger($filters);
        return $this->_syntheseDepuisLignes($rows);
    }

    protected function _syntheseDepuisLignes(array $rows)
    {
        $totaux = array(
            'nb_dettes' => 0,
            'nb_eleves' => 0,
            'prevu' => 0.0,
            'exonerations' => 0.0,
            'net_a_recouvrer' => 0.0,
            'encaisse_brut' => 0.0,
            'remboursements' => 0.0,
            'encaisse_net' => 0.0,
            'solde_restant' => 0.0,
            'trop_percu' => 0.0,
            'taux_recouvrement' => 0.0,
            'nb_en_ordre' => 0,
            'nb_impayes' => 0,
            'nb_exoneres' => 0,
            'nb_trop_percu' => 0
        );
        $eleves = array();
        $par_classe = array();
        $par_type = array();
        $par_periode = array();
        $par_situation = array();

        foreach ($rows as $row) {
            $annule = $row['situation'] === 'annule';
            $totaux['nb_dettes']++;
            $eleves[(int)$row['id_etudiant']] = true;

            if (!$annule) {
                $totaux['prevu'] += floatval($row['montant_initial']);
                $totaux['exonerations'] += floatval($row['exoneration']);
                $totaux['net_a_recouvrer'] += floatval($row['net_du']);
                $totaux['encaisse_brut'] += floatval($row['paye_brut']);
                $totaux['remboursements'] += floatval($row['remboursements']);
                $totaux['encaisse_net'] += floatval($row['paye_net']);
                $totaux['solde_restant'] += floatval($row['solde']);
                $totaux['trop_percu'] += floatval($row['trop_percu']);

                if (in_array($row['situation'], array('paye', 'exonere_total'), true)) {
                    $totaux['nb_en_ordre']++;
                }
                if (floatval($row['solde']) > 0.009) {
                    $totaux['nb_impayes']++;
                }
                if (in_array($row['situation'], array('exonere_total', 'exonere_partiel'), true)) {
                    $totaux['nb_exoneres']++;
                }
                if ($row['situation'] === 'trop_percu') {
                    $totaux['nb_trop_percu']++;
                }

                $this->_ajouterAgregat(
                    $par_classe,
                    (string)($row['classe_id'] ?? '0') . '|' . ($row['classe_libelle'] ?? 'Non affectée'),
                    $row
                );
                $this->_ajouterAgregat(
                    $par_type,
                    (string)($row['type_code'] ?? '') . '|' . ($row['type_libelle'] ?? 'Sans type'),
                    $row
                );
                $this->_ajouterAgregat(
                    $par_periode,
                    (string)($row['id_periode'] ?? '0') . '|' . ($row['periode_libelle'] ?? 'Sans période'),
                    $row
                );
            }

            if (!isset($par_situation[$row['situation']])) {
                $par_situation[$row['situation']] = array(
                    'situation' => $row['situation'],
                    'libelle' => $row['situation_libelle'],
                    'nb' => 0,
                    'net_du' => 0.0,
                    'encaisse' => 0.0,
                    'solde' => 0.0
                );
            }
            $par_situation[$row['situation']]['nb']++;
            $par_situation[$row['situation']]['net_du'] += floatval($row['net_du']);
            $par_situation[$row['situation']]['encaisse'] += floatval($row['paye_net']);
            $par_situation[$row['situation']]['solde'] += floatval($row['solde']);
        }

        $totaux['nb_eleves'] = count($eleves);
        $totaux['taux_recouvrement'] = $totaux['net_a_recouvrer'] > 0.009
            ? round(($totaux['encaisse_net'] / $totaux['net_a_recouvrer']) * 100, 2)
            : 0.0;

        foreach ($totaux as $key => $value) {
            if (is_float($value)) {
                $totaux[$key] = round($value, 2);
            }
        }

        return array(
            'totaux' => $totaux,
            'par_classe' => $this->_finaliserAgregats($par_classe),
            'par_type_frais' => $this->_finaliserAgregats($par_type),
            'par_periode' => $this->_finaliserAgregats($par_periode),
            'par_situation' => array_values($par_situation)
        );
    }

    protected function _ajouterAgregat(array &$groupes, $key, array $row)
    {
        if (!isset($groupes[$key])) {
            $groupes[$key] = array(
                'cle' => $key,
                'nb_dettes' => 0,
                'nb_eleves' => 0,
                'prevu' => 0.0,
                'exonerations' => 0.0,
                'net_a_recouvrer' => 0.0,
                'encaisse_net' => 0.0,
                'solde_restant' => 0.0,
                'taux_recouvrement' => 0.0,
                '_eleves' => array()
            );
        }
        $groupes[$key]['nb_dettes']++;
        $groupes[$key]['_eleves'][(int)$row['id_etudiant']] = true;
        $groupes[$key]['prevu'] += floatval($row['montant_initial']);
        $groupes[$key]['exonerations'] += floatval($row['exoneration']);
        $groupes[$key]['net_a_recouvrer'] += floatval($row['net_du']);
        $groupes[$key]['encaisse_net'] += floatval($row['paye_net']);
        $groupes[$key]['solde_restant'] += floatval($row['solde']);
    }

    protected function _finaliserAgregats(array $groupes)
    {
        foreach ($groupes as &$g) {
            $g['nb_eleves'] = count($g['_eleves']);
            unset($g['_eleves']);
            $g['taux_recouvrement'] = $g['net_a_recouvrer'] > 0.009
                ? round(($g['encaisse_net'] / $g['net_a_recouvrer']) * 100, 2)
                : 0.0;
            foreach (array('prevu', 'exonerations', 'net_a_recouvrer', 'encaisse_net', 'solde_restant') as $k) {
                $g[$k] = round($g[$k], 2);
            }
        }
        unset($g);
        return array_values($groupes);
    }

    public function suiviClasse($id_classe, $filters = array())
    {
        $filters['id_classe'] = (int)$id_classe;
        $rows = $this->charger($filters);
        $synthese = $this->_syntheseDepuisLignes($rows);
        $synthese['lignes'] = $rows;
        return $synthese;
    }

    public function recalculerStatuts($filters = array())
    {
        $rows = $this->charger($filters);
        $updated = 0;
        $this->begin();
        try {
            foreach ($rows as $row) {
                if ($row['statut_stocke'] === 'annule' || $row['situation'] === 'annule') {
                    continue;
                }
                if ($row['statut_stocke'] !== $row['situation']) {
                    $this->update(
                        'dettes',
                        array('id_dette' => (int)$row['id_dette']),
                        array('statut' => $row['situation'])
                    );
                    $updated++;
                }
            }
            if (!$this->transactionOk()) {
                throw new RuntimeException('Transaction interrompue');
            }
            $this->commit();
        } catch (Throwable $e) {
            $this->rollback();
            $this->fail('Recalcul des statuts impossible : ' . $e->getMessage());
        }

        if ($this->audit) {
            $this->audit->log('recalcul_statuts', 'dettes', null, null, array(
                'updated' => $updated,
                'filters' => $filters
            ));
        }
        return $updated;
    }

    public function releveEtudiant($id_etudiant, $filters = array())
    {
        $filters['id_etudiant'] = (int)$id_etudiant;
        $rows = $this->charger($filters);
        $synthese = $this->_syntheseDepuisLignes($rows);

        $sql = "SELECT ech.*, f.montant AS frais_montant, tf.libelle AS type_libelle, tf.code AS type_code
                FROM echeances ech
                LEFT JOIN frais f ON f.id_frais = ech.id_frais
                LEFT JOIN types_frais tf ON tf.id_type_frais = f.id_type_frais
                WHERE ech.deleted_at IS NULL AND ech.id_etudiant = ?";
        $binds = array((int)$id_etudiant);
        if (!empty($filters['id_annee'])) {
            $sql .= ' AND f.id_annee = ?';
            $binds[] = (int)$filters['id_annee'];
        }
        if (!empty($filters['id_periode'])) {
            $sql .= ' AND f.id_periode = ?';
            $binds[] = (int)$filters['id_periode'];
        }
        $sql .= ' ORDER BY ech.date_echeance ASC';
        $echeances = $this->rows($sql, $binds);

        $retards = array();
        $today = $this->today();
        foreach ($echeances as $echeance) {
            if ($echeance['statut'] !== 'paye' && $echeance['statut'] !== 'annule'
                && substr($echeance['date_echeance'], 0, 10) < $today) {
                $retards[] = $echeance;
            }
        }

        $etudiant = $this->row(
            'SELECT id_etudiant, fullname, matricule FROM etudiants WHERE id_etudiant = ? AND deleted_at IS NULL',
            array((int)$id_etudiant)
        );

        return array(
            'etudiant' => $etudiant,
            'lignes' => $rows,
            'echeances' => $echeances,
            'retards' => $retards,
            'synthese' => $synthese
        );
    }
}
