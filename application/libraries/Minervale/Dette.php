<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Base.php';

class Minervale_dette extends Minervale_base
{
    protected $audit;

    public function __construct($db, $ci = null, ?Minervale_audit $audit = null)
    {
        parent::__construct($db, $ci);
        $this->audit = $audit;
    }

    public function generer($options = array())
    {
        $id_annee = isset($options['id_annee']) && $options['id_annee'] !== ''
            ? (int)$options['id_annee'] : $this->activeYearId();
        $id_periode = isset($options['id_periode']) && $options['id_periode'] !== ''
            ? (int)$options['id_periode'] : $this->activePeriodId();
        $id_classe = !empty($options['id_classe']) ? (int)$options['id_classe'] : null;
        $id_etudiant = !empty($options['id_etudiant']) ? (int)$options['id_etudiant'] : null;
        $id_type_frais = !empty($options['id_type_frais']) ? (int)$options['id_type_frais'] : null;
        $id_frais = !empty($options['id_frais']) ? (int)$options['id_frais'] : null;
        $mettre_a_jour = !empty($options['mettre_a_jour']);
        $source = isset($options['source']) && in_array($options['source'], array('inscription', 'echeancier', 'manuelle'), true)
            ? $options['source'] : 'inscription';

        if ($id_frais !== null) {
            $frais_cible = $this->row(
                'SELECT id_frais, id_annee, id_periode, id_classe FROM frais WHERE id_frais = ? AND deleted_at IS NULL',
                array($id_frais)
            );
            if (!$frais_cible) {
                $this->fail('Frais introuvable', 404);
            }
            $id_annee = (int)$frais_cible['id_annee'];
            $id_periode = (int)$frais_cible['id_periode'];
            $id_classe = !empty($frais_cible['id_classe']) ? (int)$frais_cible['id_classe'] : null;
            $id_type_frais = null;
        }

        if ($id_annee <= 0) {
            $this->fail('Année scolaire introuvable');
        }
        if ($id_periode <= 0) {
            $this->fail('Trimestre introuvable');
        }

        $periode = $this->row(
            'SELECT id_periode FROM periodes WHERE id_periode = ? AND id_annee = ? AND deleted_at IS NULL',
            array($id_periode, $id_annee)
        );
        if (!$periode) {
            $this->fail('Le trimestre ne correspond pas à l\'année scolaire');
        }

        $sql = 'SELECT f.* FROM frais f WHERE f.deleted_at IS NULL AND f.id_annee = ? AND f.id_periode = ?';
        $binds = array($id_annee, $id_periode);
        if ($id_frais !== null) {
            $sql .= ' AND f.id_frais = ?';
            $binds[] = $id_frais;
        }
        if ($id_classe !== null) {
            $sql .= ' AND (f.id_classe = ? OR f.id_classe IS NULL)';
            $binds[] = $id_classe;
        }
        if ($id_type_frais !== null) {
            $sql .= ' AND f.id_type_frais = ?';
            $binds[] = $id_type_frais;
        }
        $sql .= ' ORDER BY f.id_classe, f.id_type_frais';
        $frais_list = $this->rows($sql, $binds);

        $stats = array(
            'created' => 0,
            'updated' => 0,
            'restored' => 0,
            'skipped' => 0,
            'frais' => count($frais_list),
            'etudiants' => 0
        );
        if (empty($frais_list)) {
            return $stats;
        }

        $frais_specifiques = array();
        foreach ($frais_list as $frais) {
            if (!empty($frais['id_classe'])) {
                $frais_specifiques[(int)$frais['id_classe']][(int)$frais['id_type_frais']] = true;
            }
        }

        $this->begin();
        try {
            foreach ($frais_list as $frais) {
                $sql_inscrits = 'SELECT i.id_etudiant, i.id_inscription, i.id_classe, i.id_section, e.is_interne
                     FROM inscriptions i
                     JOIN etudiants e ON e.id_etudiant = i.id_etudiant AND e.deleted_at IS NULL
                     WHERE i.deleted_at IS NULL AND i.id_annee = ?';
                $binds_inscrits = array($id_annee);
                if ($id_etudiant !== null) {
                    $sql_inscrits .= ' AND i.id_etudiant = ?';
                    $binds_inscrits[] = $id_etudiant;
                }
                if (!empty($frais['id_classe'])) {
                    $sql_inscrits .= ' AND i.id_classe = ?';
                    $binds_inscrits[] = (int)$frais['id_classe'];
                } elseif ($id_classe !== null) {
                    $sql_inscrits .= ' AND i.id_classe = ?';
                    $binds_inscrits[] = $id_classe;
                }
                $inscrits = $this->rows($sql_inscrits, $binds_inscrits);
                $stats['etudiants'] += count($inscrits);

                foreach ($inscrits as $inscription) {
                    if (empty($frais['id_classe'])
                        && isset($frais_specifiques[(int)$inscription['id_classe']][(int)$frais['id_type_frais']])) {
                        $stats['skipped']++;
                        continue;
                    }

                    $existing = $this->row(
                        'SELECT * FROM dettes WHERE id_etudiant = ? AND id_frais = ? LIMIT 1 FOR UPDATE',
                        array((int)$inscription['id_etudiant'], (int)$frais['id_frais'])
                    );

                    $montant = floatval($frais['montant']);
                    if (!empty($inscription['is_interne']) && isset($frais['montant_interne']) && $frais['montant_interne'] !== null) {
                        $montant = floatval($frais['montant_interne']);
                    }

                    $snapshot = array(
                        'id_etudiant' => (int)$inscription['id_etudiant'],
                        'id_frais' => (int)$frais['id_frais'],
                        'id_inscription' => (int)$inscription['id_inscription'],
                        'id_annee' => $id_annee,
                        'id_periode' => $id_periode,
                        'id_classe' => (int)$inscription['id_classe'],
                        'id_section' => !empty($inscription['id_section']) ? (int)$inscription['id_section'] : null,
                        'montant_initial' => $montant
                    );

                    if ($existing) {
                        $has_links = $this->_hasFinancialLinks($existing);
                        if (!empty($existing['deleted_at'])) {
                            if ($has_links) {
                                $stats['skipped']++;
                                continue;
                            }
                            $this->update('dettes', array('id_dette' => (int)$existing['id_dette']), array_merge($snapshot, array(
                                'statut' => 'impaye',
                                'source' => $source,
                                'date_generation' => $this->now(),
                                'deleted_at' => null
                            )));
                            $stats['restored']++;
                            continue;
                        }
                        if ($mettre_a_jour && !$has_links) {
                            $this->update('dettes', array('id_dette' => (int)$existing['id_dette']), $snapshot);
                            $stats['updated']++;
                        } else {
                            $stats['skipped']++;
                        }
                        continue;
                    }

                    $this->insert('dettes', array_merge($snapshot, array(
                        'uuid' => self::uuid(),
                        'statut' => 'impaye',
                        'source' => $source,
                        'date_generation' => $this->now()
                    )));
                    $stats['created']++;
                }
            }

            if (!$this->transactionOk()) {
                throw new RuntimeException('Transaction interrompue');
            }
            $this->commit();
        } catch (Throwable $e) {
            $this->rollback();
            $this->fail('Génération des dettes impossible : ' . $e->getMessage());
        }

        if ($this->audit) {
            $this->audit->log('generation_dettes', 'dettes', null, null, $stats);
        }

        return $stats;
    }

    public function genererPourEtudiant($id_etudiant, $id_annee = null)
    {
        $id_etudiant = (int)$id_etudiant;
        $id_annee = ($id_annee !== null && $id_annee !== '') ? (int)$id_annee : $this->activeYearId();

        $stats = array(
            'created' => 0,
            'updated' => 0,
            'restored' => 0,
            'skipped' => 0,
            'frais' => 0,
            'etudiants' => 0,
            'periodes' => 0
        );

        if ($id_etudiant <= 0 || $id_annee <= 0) {
            return $stats;
        }

        $inscription = $this->row(
            'SELECT id_inscription, id_etudiant, id_classe, id_section, id_annee
             FROM inscriptions
             WHERE id_etudiant = ? AND id_annee = ? AND deleted_at IS NULL
             ORDER BY id_inscription DESC LIMIT 1',
            array($id_etudiant, $id_annee)
        );
        if (!$inscription) {
            return $stats;
        }

        $id_classe = !empty($inscription['id_classe']) ? (int)$inscription['id_classe'] : null;

        $sql = 'SELECT DISTINCT d.id_periode
                FROM dettes d
                WHERE d.id_annee = ? AND d.deleted_at IS NULL
                  AND EXISTS (
                      SELECT 1 FROM frais f
                      WHERE f.id_annee = d.id_annee AND f.id_periode = d.id_periode
                        AND f.deleted_at IS NULL
                        AND (f.id_classe IS NULL';
        $binds = array($id_annee);
        if ($id_classe !== null) {
            $sql .= ' OR f.id_classe = ?';
            $binds[] = $id_classe;
        }
        $sql .= ')
                  )
                ORDER BY d.id_periode ASC';

        $periodes = $this->rows($sql, $binds);
        foreach ($periodes as $periode) {
            $options = array(
                'id_annee' => $id_annee,
                'id_periode' => (int)$periode['id_periode'],
                'id_etudiant' => $id_etudiant,
                'source' => 'inscription'
            );
            if ($id_classe !== null) {
                $options['id_classe'] = $id_classe;
            }

            try {
                $result = $this->generer($options);
            } catch (Throwable $e) {
                log_message('error', 'Minervale generation etudiant #' . $id_etudiant
                    . ' periode #' . (int)$periode['id_periode'] . ' : ' . $e->getMessage());
                break;
            }

            foreach (array('created', 'updated', 'restored', 'skipped', 'frais') as $key) {
                $stats[$key] += (int)$result[$key];
            }
            $stats['periodes']++;
        }

        if ($stats['created'] > 0 || $stats['restored'] > 0 || $stats['updated'] > 0) {
            $stats['etudiants'] = 1;
        }

        return $stats;
    }

    protected function _hasFinancialLinks(array $dette)
    {
        $id_dette = (int)$dette['id_dette'];
        $has_affectation = (int)$this->scalar(
            'SELECT COUNT(*) FROM paiement_affectations WHERE id_dette = ? AND deleted_at IS NULL',
            array($id_dette)
        );
        if ($has_affectation > 0) {
            return true;
        }

        $has_remboursement = (int)$this->scalar(
            'SELECT COUNT(*) FROM remboursements WHERE id_dette = ? AND deleted_at IS NULL',
            array($id_dette)
        );
        if ($has_remboursement > 0) {
            return true;
        }

        $has_exoneration = (int)$this->scalar(
            "SELECT COUNT(*) FROM exonerations
             WHERE deleted_at IS NULL AND statut = 'active' AND statut_demande = 'approuvee'
               AND id_annee = ? AND id_etudiant = ?
               AND (id_frais = ? OR id_frais IS NULL)",
            array((int)$dette['id_annee'], (int)$dette['id_etudiant'], (int)$dette['id_frais'])
        );
        return $has_exoneration > 0;
    }

    public function listerBrut($filters = array())
    {
        $sql = "SELECT d.*, f.id_type_frais, f.montant AS frais_montant,
                       tf.libelle AS type_libelle, tf.code AS type_code,
                       e.fullname, e.matricule,
                       COALESCE(d.id_classe, i.id_classe) AS classe_id,
                       c.libelle AS classe_libelle,
                       COALESCE(d.id_section, i.id_section) AS section_id,
                       s.libelle AS section_libelle,
                       p.libelle AS periode_libelle,
                       a.libelle AS annee_libelle
                FROM dettes d
                JOIN frais f ON f.id_frais = d.id_frais
                LEFT JOIN types_frais tf ON tf.id_type_frais = f.id_type_frais
                LEFT JOIN etudiants e ON e.id_etudiant = d.id_etudiant
                LEFT JOIN inscriptions i ON i.id_inscription = d.id_inscription
                LEFT JOIN classes c ON c.id_classe = COALESCE(d.id_classe, i.id_classe)
                LEFT JOIN sections s ON s.id_section = COALESCE(d.id_section, i.id_section)
                LEFT JOIN periodes p ON p.id_periode = d.id_periode
                LEFT JOIN annees_scolaires a ON a.id_annee = d.id_annee
                WHERE d.deleted_at IS NULL";
        $binds = array();

        if (!empty($filters['uuid'])) {
            $sql .= ' AND d.uuid = ?';
            $binds[] = $filters['uuid'];
        }
        if (!empty($filters['id_dette'])) {
            $sql .= ' AND d.id_dette = ?';
            $binds[] = (int)$filters['id_dette'];
        }
        if (!empty($filters['id_annee'])) {
            $sql .= ' AND d.id_annee = ?';
            $binds[] = (int)$filters['id_annee'];
        }
        if (!empty($filters['id_periode'])) {
            $sql .= ' AND d.id_periode = ?';
            $binds[] = (int)$filters['id_periode'];
        }
        if (!empty($filters['id_classe'])) {
            $sql .= ' AND COALESCE(d.id_classe, i.id_classe) = ?';
            $binds[] = (int)$filters['id_classe'];
        }
        if (!empty($filters['id_section'])) {
            $sql .= ' AND COALESCE(d.id_section, i.id_section) = ?';
            $binds[] = (int)$filters['id_section'];
        }
        if (!empty($filters['id_type_frais'])) {
            $sql .= ' AND f.id_type_frais = ?';
            $binds[] = (int)$filters['id_type_frais'];
        }
        if (!empty($filters['id_etudiant'])) {
            $sql .= ' AND d.id_etudiant = ?';
            $binds[] = (int)$filters['id_etudiant'];
        }
        if (!empty($filters['id_frais'])) {
            $sql .= ' AND d.id_frais = ?';
            $binds[] = (int)$filters['id_frais'];
        }
        if (!empty($filters['statut'])) {
            $sql .= ' AND d.statut = ?';
            $binds[] = $filters['statut'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (e.fullname LIKE ? OR e.matricule LIKE ?)';
            $like = '%' . trim($filters['search']) . '%';
            $binds[] = $like;
            $binds[] = $like;
        }

        $sql .= ' ORDER BY e.fullname ASC, d.id_periode ASC, tf.libelle ASC, d.id_dette ASC';
        return $this->rows($sql, $binds);
    }

    public function getByUuid($uuid)
    {
        $rows = $this->listerBrut(array('uuid' => $uuid));
        return $rows ? $rows[0] : null;
    }

    public function getById($id_dette)
    {
        $rows = $this->listerBrut(array('id_dette' => $id_dette));
        return $rows ? $rows[0] : null;
    }

    public function updateStatut($id_dette, $statut)
    {
        $allowed = array('impaye', 'partiel', 'paye', 'exonere_partiel', 'exonere_total', 'trop_percu', 'annule');
        if (!in_array($statut, $allowed, true)) {
            return false;
        }
        return $this->update('dettes', array('id_dette' => (int)$id_dette), array('statut' => $statut));
    }

    public function annuler($uuid, $motif = null)
    {
        $dette = $this->getByUuid($uuid);
        if (!$dette) {
            $this->fail('Dette introuvable', 404);
        }
        if ($dette['statut'] === 'annule') {
            return true;
        }
        $has_paid = (float)$this->scalar(
            'SELECT COALESCE(SUM(pa.montant),0) FROM paiement_affectations pa
             JOIN paiements p ON p.id_paiement = pa.id_paiement
             WHERE pa.id_dette = ? AND pa.deleted_at IS NULL AND p.deleted_at IS NULL AND p.statut <> ?',
            array((int)$dette['id_dette'], 'annule')
        );
        if ($has_paid > 0.009) {
            $this->fail('Impossible d\'annuler une dette déjà payée');
        }
        $this->update('dettes', array('id_dette' => (int)$dette['id_dette']), array('statut' => 'annule'));
        if ($this->audit) {
            $this->audit->log('annulation_dette', 'dettes', (int)$dette['id_dette'], array('statut' => $dette['statut']), array('statut' => 'annule'), $motif);
        }
        return true;
    }
}
