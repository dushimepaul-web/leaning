<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Base.php';
require_once __DIR__ . '/Solde.php';
require_once __DIR__ . '/Exoneration.php';
require_once __DIR__ . '/Audit.php';

class Minervale_paiement extends Minervale_base
{
    protected $exoneration;
    protected $audit;

    public function __construct($db, $ci = null, ?Minervale_exoneration $exoneration = null, ?Minervale_audit $audit = null)
    {
        parent::__construct($db, $ci);
        $this->exoneration = $exoneration;
        $this->audit = $audit;
    }

    protected function _exonerationService()
    {
        if (!$this->exoneration) {
            $this->exoneration = new Minervale_exoneration($this->db, $this->ci);
        }
        return $this->exoneration;
    }

    public function enregistrer($data)
    {
        $id_etudiant = !empty($data['id_etudiant']) ? (int)$data['id_etudiant'] : 0;
        $montant = round(floatval(isset($data['montant']) ? $data['montant'] : 0), 2);
        if ($id_etudiant <= 0) {
            $this->fail('Étudiant obligatoire');
        }
        if ($montant <= 0) {
            $this->fail('Montant invalide');
        }

        $mode = isset($data['mode_paiement']) ? $data['mode_paiement'] : 'especes';
        if (!in_array($mode, array('especes', 'banque', 'mobile_money', 'cheque'), true)) {
            $this->fail('Mode de paiement invalide');
        }
        $date_paiement = $this->now();
        if (!empty($data['date_paiement'])) {
            $timestamp = strtotime($data['date_paiement']);
            if ($timestamp === false) {
                $this->fail('Date de paiement invalide');
            }
            $date_paiement = date('Y-m-d H:i:s', $timestamp);
        }

        $id_frais = !empty($data['id_frais']) ? (int)$data['id_frais'] : 0;
        if ($id_frais <= 0) {
            $this->fail('Frais obligatoire');
        }

        $frais = $this->row(
            'SELECT * FROM frais WHERE id_frais = ? AND deleted_at IS NULL',
            array($id_frais)
        );
        if (!$frais) {
            $this->fail('Frais introuvable', 404);
        }
        $id_annee = (int)$frais['id_annee'];

        $id_echeance = null;
        if (!empty($data['id_echeance'])) {
            $echeance = $this->row(
                'SELECT * FROM echeances WHERE id_echeance = ? AND deleted_at IS NULL',
                array((int)$data['id_echeance'])
            );
            if (!$echeance) {
                $this->fail('Échéance introuvable', 404);
            }
            if ((int)$echeance['id_etudiant'] !== $id_etudiant) {
                $this->fail('Cette échéance n\'appartient pas à cet étudiant');
            }
            if (!empty($echeance['id_frais']) && (int)$echeance['id_frais'] !== $id_frais) {
                $this->fail('Cette échéance ne correspond pas au frais choisi');
            }
            $id_echeance = (int)$echeance['id_echeance'];
        }

        $userId = $this->userId();
        $uuid = self::uuid();

        $this->begin();
        try {
            $dette = $this->row(
                'SELECT * FROM dettes WHERE id_etudiant = ? AND id_frais = ? AND deleted_at IS NULL LIMIT 1 FOR UPDATE',
                array($id_etudiant, $id_frais)
            );
            if (!$dette) {
                throw new Minervale_exception('Dette introuvable : générez les dettes avant d\'enregistrer le paiement', 404);
            }
            if ($dette['statut'] === 'annule') {
                throw new Minervale_exception('Impossible de payer une dette annulée');
            }

            $situation = $this->_situationDette($dette);
            if ($montant > $situation['solde'] + 0.009 && empty($data['depassement_autorise'])) {
                throw new Minervale_exception('Montant supérieur au solde restant (' . number_format($situation['solde'], 2, ',', ' ') . ')');
            }
            $statut = $montant + 0.001 >= $situation['solde'] ? 'solde' : 'partiel';

            $id_paiement = $this->insert('paiements', array(
                'uuid' => $uuid,
                'id_etudiant' => $id_etudiant,
                'id_frais' => $id_frais,
                'id_echeance' => $id_echeance,
                'id_annee' => $id_annee,
                'montant' => $montant,
                'date_paiement' => $date_paiement,
                'mode_paiement' => $mode,
                'reference' => !empty($data['reference']) ? substr(trim($data['reference']), 0, 100) : null,
                'preuve_paiement' => !empty($data['preuve_paiement']) ? $data['preuve_paiement'] : null,
                'payeur' => !empty($data['payeur']) ? substr(trim($data['payeur']), 0, 150) : null,
                'id_utilisateur' => $userId,
                'id_caissier' => $userId,
                'statut' => $statut,
                'notes' => !empty($data['notes']) ? $data['notes'] : null
            ));
            if (!$id_paiement) {
                throw new RuntimeException('Insertion du paiement impossible');
            }
            $id_paiement = $this->insertId();

            $this->insert('paiement_affectations', array(
                'uuid' => self::uuid(),
                'id_paiement' => $id_paiement,
                'id_dette' => (int)$dette['id_dette'],
                'montant' => $montant
            ));

            $apres = $this->_recalculerDette((int)$dette['id_dette']);
            $this->_syncEcheance($id_echeance);
            $recu = $this->_creerRecu($id_paiement, $id_etudiant, $id_annee, $montant);

            if (!$this->transactionOk()) {
                throw new RuntimeException('Transaction interrompue');
            }
            $this->commit();
        } catch (Minervale_exception $e) {
            $this->rollback();
            throw $e;
        } catch (Throwable $e) {
            $this->rollback();
            $this->fail('Enregistrement du paiement impossible : ' . $e->getMessage());
        }

        if ($this->audit) {
            $this->audit->log('creation_paiement', 'paiements', $id_paiement, null, array(
                'id_etudiant' => $id_etudiant,
                'id_frais' => $id_frais,
                'montant' => $montant,
                'id_dette' => (int)$dette['id_dette'],
                'id_recu' => $recu['id_recu'],
                'numero_recu' => $recu['numero_recu']
            ));
        }

        return array(
            'id_paiement' => $id_paiement,
            'uuid' => $uuid,
            'id_dette' => (int)$dette['id_dette'],
            'montant' => $montant,
            'id_recu' => $recu['id_recu'],
            'recu_uuid' => $recu['recu_uuid'],
            'numero_recu' => $recu['numero_recu'],
            'solde_avant' => $situation['solde'],
            'solde_apres' => $apres ? $apres['solde'] : null,
            'statut_dette' => $apres ? $apres['situation'] : null
        );
    }

    public function ventiler($paiement, $affectations, $remplacer = false)
    {
        $payment = $this->_trouverPaiement($paiement);
        if (!$payment) {
            $this->fail('Paiement introuvable', 404);
        }
        if ($payment['statut'] === 'annule') {
            $this->fail('Impossible de ventiler un paiement annulé');
        }

        $normalisees = $this->_normaliserAffectations($affectations);
        if (empty($normalisees)) {
            $this->fail('Aucune affectation fournie');
        }

        $total = 0;
        foreach ($normalisees as $ligne) {
            $total += $ligne['montant'];
        }
        if (abs($total - floatval($payment['montant'])) > 0.009) {
            $this->fail('Le total des affectations doit être égal au montant du paiement');
        }

        $anciennes = $this->rows(
            'SELECT * FROM paiement_affectations WHERE id_paiement = ?',
            array((int)$payment['id_paiement'])
        );
        if (!empty($anciennes) && !$remplacer) {
            $actives = 0;
            foreach ($anciennes as $ancienne) {
                if (empty($ancienne['deleted_at'])) {
                    $actives++;
                }
            }
            if ($actives > 0) {
                $this->fail('Ce paiement est déjà ventilé');
            }
        }

        $dettes = array();
        foreach ($normalisees as $ligne) {
            $dette = $this->row(
                'SELECT * FROM dettes WHERE id_dette = ? AND deleted_at IS NULL',
                array($ligne['id_dette'])
            );
            if (!$dette) {
                $this->fail('Dette introuvable : ' . $ligne['id_dette'], 404);
            }
            if ((int)$dette['id_etudiant'] !== (int)$payment['id_etudiant']) {
                $this->fail('Une dette ne correspond pas à l\'étudiant du paiement');
            }
            if ((int)$dette['id_annee'] !== (int)$payment['id_annee']) {
                $this->fail('Une dette ne correspond pas à l\'année du paiement');
            }
            if ($dette['statut'] === 'annule') {
                $this->fail('Impossible de ventiler sur une dette annulée');
            }
            $dettes[(int)$dette['id_dette']] = $dette;
        }

        $existantes = array();
        foreach ($anciennes as $ancienne) {
            $existantes[(int)$ancienne['id_dette']] = $ancienne;
        }

        $this->begin();
        try {
            foreach ($normalisees as $ligne) {
                $id_dette = $ligne['id_dette'];
                if (isset($existantes[$id_dette])) {
                    $this->update(
                        'paiement_affectations',
                        array('id_affectation' => (int)$existantes[$id_dette]['id_affectation']),
                        array('montant' => $ligne['montant'], 'deleted_at' => null)
                    );
                    unset($existantes[$id_dette]);
                } else {
                    $this->insert('paiement_affectations', array(
                        'uuid' => self::uuid(),
                        'id_paiement' => (int)$payment['id_paiement'],
                        'id_dette' => $id_dette,
                        'montant' => $ligne['montant']
                    ));
                }
            }

            foreach ($existantes as $restante) {
                if (empty($restante['deleted_at'])) {
                    $this->update(
                        'paiement_affectations',
                        array('id_affectation' => (int)$restante['id_affectation']),
                        array('deleted_at' => $this->now())
                    );
                }
            }

            foreach ($dettes as $id_dette => $dette) {
                $this->_recalculerDette($id_dette);
            }
            foreach ($existantes as $restante) {
                $this->_recalculerDette((int)$restante['id_dette']);
            }

            if (!$this->transactionOk()) {
                throw new RuntimeException('Transaction interrompue');
            }
            $this->commit();
        } catch (Throwable $e) {
            $this->rollback();
            $this->fail('Ventilation impossible : ' . $e->getMessage());
        }

        if ($this->audit) {
            $this->audit->log('ventilation_paiement', 'paiements', (int)$payment['id_paiement'], null, array(
                'affectations' => $normalisees
            ));
        }

        return $this->getAffectations((int)$payment['id_paiement']);
    }

    public function annuler($uuid, $motif = null)
    {
        $payment = $this->_trouverPaiement($uuid);
        if (!$payment) {
            $this->fail('Paiement introuvable', 404);
        }
        if ($payment['statut'] === 'annule') {
            return true;
        }

        $remboursements = (int)$this->scalar(
            "SELECT COUNT(*) FROM remboursements
             WHERE id_paiement = ? AND deleted_at IS NULL AND statut = 'effectue'",
            array((int)$payment['id_paiement'])
        );
        if ($remboursements > 0) {
            $this->fail('Annulez d\'abord les remboursements liés à ce paiement');
        }

        $affectations = $this->rows(
            'SELECT id_dette FROM paiement_affectations
             WHERE id_paiement = ? AND deleted_at IS NULL',
            array((int)$payment['id_paiement'])
        );

        $this->begin();
        try {
            $this->update('paiements', array('id_paiement' => (int)$payment['id_paiement']), array(
                'statut' => 'annule',
                'id_annulateur' => $this->userId(),
                'annule_le' => $this->now(),
                'motif_annulation' => $motif !== null ? substr(trim($motif), 0, 255) : null
            ));
            $this->update(
                'paiement_affectations',
                array('id_paiement' => (int)$payment['id_paiement'], 'deleted_at' => null),
                array('deleted_at' => $this->now())
            );
            $this->_supprimerRecus((int)$payment['id_paiement']);
            foreach ($affectations as $affectation) {
                $this->_recalculerDette((int)$affectation['id_dette']);
            }
            $this->_syncEcheance(!empty($payment['id_echeance']) ? (int)$payment['id_echeance'] : null);
            if (!$this->transactionOk()) {
                throw new RuntimeException('Transaction interrompue');
            }
            $this->commit();
        } catch (Throwable $e) {
            $this->rollback();
            $this->fail('Annulation du paiement impossible : ' . $e->getMessage());
        }

        if ($this->audit) {
            $this->audit->log('annulation_paiement', 'paiements', (int)$payment['id_paiement'], array(
                'statut' => $payment['statut']
            ), array('statut' => 'annule'), $motif);
        }
        return true;
    }

    public function lister($filters = array())
    {
        $sql = "SELECT p.*, e.fullname, e.matricule,
                       tf.libelle AS type_libelle, tf.code AS type_code,
                       COALESCE(pa.total, 0) AS affecte,
                       COALESCE(rb.total, 0) AS rembourse,
                       i.id_classe, i.id_section,
                       c.libelle AS classe_libelle, s.libelle AS section_libelle,
                       r.uuid AS recu_uuid, r.numero_recu
                FROM paiements p
                LEFT JOIN etudiants e ON e.id_etudiant = p.id_etudiant
                LEFT JOIN frais f ON f.id_frais = p.id_frais
                LEFT JOIN types_frais tf ON tf.id_type_frais = f.id_type_frais
                LEFT JOIN inscriptions i ON i.id_etudiant = p.id_etudiant
                    AND i.id_annee = p.id_annee AND i.deleted_at IS NULL
                LEFT JOIN classes c ON c.id_classe = i.id_classe
                LEFT JOIN sections s ON s.id_section = i.id_section
                LEFT JOIN (
                    SELECT id_paiement, MIN(id_recu) AS id_recu
                    FROM paiements_recus
                    GROUP BY id_paiement
                ) pr ON pr.id_paiement = p.id_paiement
                LEFT JOIN recus r ON r.id_recu = pr.id_recu AND r.deleted_at IS NULL
                LEFT JOIN (
                    SELECT id_paiement, SUM(montant) AS total
                    FROM paiement_affectations WHERE deleted_at IS NULL
                    GROUP BY id_paiement
                ) pa ON pa.id_paiement = p.id_paiement
                LEFT JOIN (
                    SELECT id_paiement, SUM(montant) AS total
                    FROM remboursements
                    WHERE deleted_at IS NULL AND statut = 'effectue'
                    GROUP BY id_paiement
                ) rb ON rb.id_paiement = p.id_paiement
                WHERE p.deleted_at IS NULL";
        $binds = array();

        if (!empty($filters['uuid'])) {
            $sql .= ' AND p.uuid = ?';
            $binds[] = (string)$filters['uuid'];
        }
        if (!empty($filters['id_etudiant'])) {
            $sql .= ' AND p.id_etudiant = ?';
            $binds[] = (int)$filters['id_etudiant'];
        }
        if (!empty($filters['id_annee'])) {
            $sql .= ' AND p.id_annee = ?';
            $binds[] = (int)$filters['id_annee'];
        }
        if (!empty($filters['id_frais'])) {
            $sql .= ' AND p.id_frais = ?';
            $binds[] = (int)$filters['id_frais'];
        }
        if (!empty($filters['statut'])) {
            $sql .= ' AND p.statut = ?';
            $binds[] = $filters['statut'];
        }
        if (!empty($filters['date_debut'])) {
            $sql .= ' AND p.date_paiement >= ?';
            $binds[] = $filters['date_debut'] . ' 00:00:00';
        }
        if (!empty($filters['date_fin'])) {
            $sql .= ' AND p.date_paiement <= ?';
            $binds[] = $filters['date_fin'] . ' 23:59:59';
        }
        if (!empty($filters['id_dette'])) {
            $sql .= ' AND EXISTS (SELECT 1 FROM paiement_affectations x
                      WHERE x.id_paiement = p.id_paiement AND x.id_dette = ?
                        AND x.deleted_at IS NULL)';
            $binds[] = (int)$filters['id_dette'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (e.fullname LIKE ? OR e.matricule LIKE ? OR p.reference LIKE ?)';
            $like = '%' . trim($filters['search']) . '%';
            $binds[] = $like;
            $binds[] = $like;
            $binds[] = $like;
        }

        $sql .= ' ORDER BY p.date_paiement DESC, p.id_paiement DESC';
        return $this->rows($sql, $binds);
    }

    public function getByUuid($uuid)
    {
        $rows = $this->lister(array('uuid' => $uuid));
        if (!empty($rows)) {
            return $rows[0];
        }
        return $this->row('SELECT * FROM paiements WHERE uuid = ? AND deleted_at IS NULL', array($uuid));
    }

    public function getAffectations($id_paiement)
    {
        return $this->rows(
            'SELECT pa.*, d.id_etudiant, d.id_frais, d.id_annee, d.id_periode,
                    d.montant_initial, d.statut AS statut_dette
             FROM paiement_affectations pa
             JOIN dettes d ON d.id_dette = pa.id_dette
             WHERE pa.id_paiement = ? AND pa.deleted_at IS NULL
             ORDER BY pa.id_affectation ASC',
            array((int)$id_paiement)
        );
    }

    public function recalculerDette($id_dette)
    {
        return $this->_recalculerDette((int)$id_dette);
    }

    protected function _genererNumeroRecu()
    {
        $prefix = 'RECU-' . date('Ymd') . '-';
        $last = $this->scalar(
            'SELECT numero_recu FROM recus
             WHERE numero_recu LIKE ?
             ORDER BY numero_recu DESC LIMIT 1 FOR UPDATE',
            array($prefix . '%'),
            null
        );
        if ($last && preg_match('/RECU-\d{8}-(\d+)$/', $last, $m)) {
            $next = (int)$m[1] + 1;
        } else {
            $next = 1;
        }
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    protected function _creerRecu($id_paiement, $id_etudiant, $id_annee, $montant)
    {
        $id_recu = 0;
        $recu_uuid = self::uuid();
        $numero_recu = $this->_genererNumeroRecu();

        for ($tentative = 0; $tentative < 5; $tentative++) {
            $ok = $this->insert('recus', array(
                'uuid' => $recu_uuid,
                'numero_recu' => $numero_recu,
                'id_etudiant' => (int)$id_etudiant,
                'id_annee' => (int)$id_annee,
                'montant_total' => $montant,
                'id_utilisateur' => $this->userId(),
                'date_edition' => $this->now()
            ));
            if ($ok) {
                $id_recu = $this->insertId();
                break;
            }
            $recu_uuid = self::uuid();
            $numero_recu = $this->_genererNumeroRecu();
        }

        if (!$id_recu) {
            throw new RuntimeException('Génération du reçu impossible');
        }

        $this->insert('paiements_recus', array(
            'uuid' => self::uuid(),
            'id_recu' => $id_recu,
            'id_paiement' => (int)$id_paiement
        ));

        return array(
            'id_recu' => $id_recu,
            'recu_uuid' => $recu_uuid,
            'numero_recu' => $numero_recu
        );
    }

    protected function _supprimerRecus($id_paiement)
    {
        $links = $this->rows(
            'SELECT id_recu FROM paiements_recus WHERE id_paiement = ?',
            array((int)$id_paiement)
        );
        foreach ($links as $lien) {
            $autres = (int)$this->scalar(
                'SELECT COUNT(*) FROM paiements_recus pr
                 JOIN paiements p ON p.id_paiement = pr.id_paiement
                 WHERE pr.id_recu = ? AND pr.id_paiement <> ?
                   AND p.deleted_at IS NULL AND p.statut <> \'annule\'',
                array((int)$lien['id_recu'], (int)$id_paiement)
            );
            if ($autres === 0) {
                $this->update(
                    'recus',
                    array('id_recu' => (int)$lien['id_recu']),
                    array('deleted_at' => $this->now())
                );
            }
        }
    }

    protected function _trouverPaiement($paiement)
    {
        if (is_numeric($paiement)) {
            return $this->row(
                'SELECT * FROM paiements WHERE id_paiement = ? AND deleted_at IS NULL',
                array((int)$paiement)
            );
        }
        return $this->row(
            'SELECT * FROM paiements WHERE uuid = ? AND deleted_at IS NULL',
            array((string)$paiement)
        );
    }

    protected function _normaliserAffectations($affectations)
    {
        if (!is_array($affectations)) {
            return array();
        }
        $lignes = array();
        foreach ($affectations as $cle => $valeur) {
            if (is_array($valeur)) {
                $id_dette = !empty($valeur['id_dette']) ? (int)$valeur['id_dette'] : 0;
                $montant = isset($valeur['montant']) ? floatval($valeur['montant']) : 0;
            } else {
                $id_dette = (int)$cle;
                $montant = floatval($valeur);
            }
            if ($id_dette <= 0 || $montant <= 0) {
                continue;
            }
            if (!isset($lignes[$id_dette])) {
                $lignes[$id_dette] = array('id_dette' => $id_dette, 'montant' => 0.0);
            }
            $lignes[$id_dette]['montant'] = round($lignes[$id_dette]['montant'] + $montant, 2);
        }
        return array_values($lignes);
    }

    protected function _syncEcheance($id_echeance)
    {
        if (empty($id_echeance)) {
            return;
        }
        $echeance = $this->row(
            'SELECT * FROM echeances WHERE id_echeance = ? AND deleted_at IS NULL',
            array((int)$id_echeance)
        );
        if (!$echeance) {
            return;
        }
        $paye = (float)$this->scalar(
            'SELECT COALESCE(SUM(montant), 0) FROM paiements
             WHERE id_etudiant = ? AND id_echeance = ? AND deleted_at IS NULL AND statut <> ?',
            array((int)$echeance['id_etudiant'], (int)$id_echeance, 'annule')
        );
        $rembourse = (float)$this->scalar(
            "SELECT COALESCE(SUM(r.montant), 0)
             FROM remboursements r
             JOIN paiements p ON p.id_paiement = r.id_paiement
             WHERE p.id_etudiant = ? AND p.id_echeance = ? AND p.deleted_at IS NULL
               AND p.statut <> ? AND r.deleted_at IS NULL AND r.statut = ?",
            array((int)$echeance['id_etudiant'], (int)$id_echeance, 'annule', 'effectue')
        );
        $net = max(0, $paye - $rembourse);
        if (floatval($echeance['montant']) <= 0.009) {
            $statut = 'paye';
        } elseif ($net <= 0) {
            $statut = 'impaye';
        } elseif ($net + 0.001 >= floatval($echeance['montant'])) {
            $statut = 'paye';
        } else {
            $statut = 'partiel';
        }
        $this->update('echeances', array('id_echeance' => (int)$id_echeance), array('statut' => $statut));
    }

    public function syncEcheance($id_echeance)
    {
        $this->_syncEcheance($id_echeance);
    }

    protected function _situationDette(array $dette)
    {
        $id_dette = (int)$dette['id_dette'];
        $paye = (float)$this->scalar(
            'SELECT COALESCE(SUM(pa.montant), 0)
             FROM paiement_affectations pa
             JOIN paiements p ON p.id_paiement = pa.id_paiement
             WHERE pa.id_dette = ? AND pa.deleted_at IS NULL
               AND p.deleted_at IS NULL AND p.statut <> ?',
            array($id_dette, 'annule')
        );
        $legacy = (float)$this->scalar(
            'SELECT COALESCE(SUM(p.montant), 0)
             FROM paiements p
             WHERE p.id_etudiant = ? AND p.id_frais = ? AND p.id_annee = ?
               AND p.deleted_at IS NULL AND p.statut <> ?
               AND NOT EXISTS (
                   SELECT 1 FROM paiement_affectations pa
                   WHERE pa.id_paiement = p.id_paiement
               )',
            array((int)$dette['id_etudiant'], (int)$dette['id_frais'], (int)$dette['id_annee'], 'annule')
        );
        $paye += $legacy;
        $rembourse = (float)$this->scalar(
            'SELECT COALESCE(SUM(montant), 0) FROM remboursements
             WHERE id_dette = ? AND deleted_at IS NULL AND statut = ?',
            array($id_dette, 'effectue')
        );
        $exoneration = $this->_exonerationService()->totalPourDette(
            $dette['id_etudiant'],
            $dette['id_frais'],
            $dette['id_annee']
        );
        return Minervale_solde::calculerSituation(
            $dette['montant_initial'],
            $exoneration,
            $paye,
            $rembourse,
            $dette['statut']
        );
    }

    protected function _recalculerDette($id_dette)
    {
        $dette = $this->row(
            'SELECT * FROM dettes WHERE id_dette = ? AND deleted_at IS NULL',
            array((int)$id_dette)
        );
        if (!$dette) {
            return null;
        }
        $situation = $this->_situationDette($dette);
        if ($dette['statut'] !== 'annule' && $dette['statut'] !== $situation['situation']) {
            $this->update(
                'dettes',
                array('id_dette' => (int)$dette['id_dette']),
                array('statut' => $situation['situation'])
            );
        }
        return $situation;
    }
}
