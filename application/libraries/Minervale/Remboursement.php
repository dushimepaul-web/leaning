<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Base.php';
require_once __DIR__ . '/Paiement.php';
require_once __DIR__ . '/Audit.php';

class Minervale_remboursement extends Minervale_base
{
    protected $paiement;
    protected $audit;

    public function __construct($db, $ci = null, ?Minervale_paiement $paiement = null, ?Minervale_audit $audit = null)
    {
        parent::__construct($db, $ci);
        $this->paiement = $paiement;
        $this->audit = $audit;
    }

    protected function _paiementService()
    {
        if (!$this->paiement) {
            $this->paiement = new Minervale_paiement($this->db, $this->ci);
        }
        return $this->paiement;
    }

    public function enregistrer($data)
    {
        $id_paiement = 0;
        if (!empty($data['id_paiement'])) {
            $reference_paiement = trim((string)$data['id_paiement']);
            if (ctype_digit($reference_paiement)) {
                $id_paiement = (int)$reference_paiement;
            } else {
                $paiement_reference = $this->row(
                    'SELECT id_paiement FROM paiements WHERE uuid = ? AND deleted_at IS NULL',
                    array($reference_paiement)
                );
                if ($paiement_reference) {
                    $id_paiement = (int)$paiement_reference['id_paiement'];
                }
            }
        }
        $montant = round(floatval(isset($data['montant']) ? $data['montant'] : 0), 2);
        if ($id_paiement <= 0) {
            $this->fail('Paiement obligatoire');
        }
        if ($montant <= 0) {
            $this->fail('Montant invalide');
        }
        if (empty($data['motif'])) {
            $this->fail('Motif du remboursement obligatoire');
        }

        $mode = isset($data['mode_remboursement']) ? $data['mode_remboursement'] : 'especes';
        if (!in_array($mode, array('especes', 'banque', 'mobile_money', 'cheque'), true)) {
            $this->fail('Mode de remboursement invalide');
        }
        $date_remboursement = $this->now();
        if (!empty($data['date_remboursement'])) {
            $timestamp = strtotime($data['date_remboursement']);
            if ($timestamp === false) {
                $this->fail('Date de remboursement invalide');
            }
            $date_remboursement = date('Y-m-d H:i:s', $timestamp);
        }

        $paiement = $this->row(
            'SELECT * FROM paiements WHERE id_paiement = ? AND deleted_at IS NULL',
            array($id_paiement)
        );
        if (!$paiement) {
            $this->fail('Paiement introuvable', 404);
        }
        if ($paiement['statut'] === 'annule') {
            $this->fail('Impossible de rembourser un paiement annulé');
        }

        $affectations = $this->_paiementService()->getAffectations($id_paiement);
        if (empty($affectations)) {
            $this->fail('Ce paiement n\'est affecté à aucune dette');
        }

        $id_dette = !empty($data['id_dette']) ? (int)$data['id_dette'] : null;
        if ($id_dette === null) {
            if (count($affectations) !== 1) {
                $this->fail('Précisez la dette à rembourser');
            }
            $id_dette = (int)$affectations[0]['id_dette'];
        }

        $affectation_cible = null;
        foreach ($affectations as $affectation) {
            if ((int)$affectation['id_dette'] === $id_dette) {
                $affectation_cible = $affectation;
                break;
            }
        }
        if (!$affectation_cible) {
            $this->fail('Cette dette n\'est pas affectée à ce paiement');
        }
        if ((int)$affectation_cible['id_etudiant'] !== (int)$paiement['id_etudiant']) {
            $this->fail('La dette ne correspond pas à l\'étudiant du paiement');
        }

        $deja_rembourse_paiement = (float)$this->scalar(
            'SELECT COALESCE(SUM(montant), 0) FROM remboursements
             WHERE id_paiement = ? AND deleted_at IS NULL AND statut = ?',
            array($id_paiement, 'effectue')
        );
        if ($deja_rembourse_paiement + $montant > floatval($paiement['montant']) + 0.009) {
            $this->fail('Le remboursement dépasse le montant du paiement');
        }

        $deja_rembourse_dette = (float)$this->scalar(
            'SELECT COALESCE(SUM(montant), 0) FROM remboursements
             WHERE id_paiement = ? AND id_dette = ? AND deleted_at IS NULL AND statut = ?',
            array($id_paiement, $id_dette, 'effectue')
        );
        if ($deja_rembourse_dette + $montant > floatval($affectation_cible['montant']) + 0.009) {
            $this->fail('Le remboursement dépasse le montant affecté à cette dette');
        }

        $uuid = self::uuid();
        $this->begin();
        try {
            $id_remboursement = $this->insert('remboursements', array(
                'uuid' => $uuid,
                'id_paiement' => $id_paiement,
                'id_dette' => $id_dette,
                'id_etudiant' => (int)$paiement['id_etudiant'],
                'id_annee' => (int)$paiement['id_annee'],
                'montant' => $montant,
                'date_remboursement' => $date_remboursement,
                'mode_remboursement' => $mode,
                'reference' => !empty($data['reference']) ? substr(trim($data['reference']), 0, 100) : null,
                'motif' => substr(trim($data['motif']), 0, 255),
                'id_utilisateur' => $this->userId(),
                'statut' => 'effectue'
            ));
            if (!$id_remboursement) {
                throw new RuntimeException('Insertion du remboursement impossible');
            }
            $id_remboursement = $this->insertId();

            $situation = $this->_paiementService()->recalculerDette($id_dette);
            $this->_paiementService()->syncEcheance(!empty($paiement['id_echeance']) ? (int)$paiement['id_echeance'] : null);

            if (!$this->transactionOk()) {
                throw new RuntimeException('Transaction interrompue');
            }
            $this->commit();
        } catch (Throwable $e) {
            $this->rollback();
            $this->fail('Enregistrement du remboursement impossible : ' . $e->getMessage());
        }

        if ($this->audit) {
            $this->audit->log('creation_remboursement', 'remboursements', $id_remboursement, null, array(
                'id_paiement' => $id_paiement,
                'id_dette' => $id_dette,
                'montant' => $montant
            ), $data['motif']);
        }

        return array(
            'id_remboursement' => $id_remboursement,
            'uuid' => $uuid,
            'id_paiement' => $id_paiement,
            'id_dette' => $id_dette,
            'montant' => $montant,
            'solde_apres' => $situation ? $situation['solde'] : null,
            'statut_dette' => $situation ? $situation['situation'] : null
        );
    }

    public function annuler($uuid, $motif = null)
    {
        $remboursement = $this->_trouver($uuid);
        if (!$remboursement) {
            $this->fail('Remboursement introuvable', 404);
        }
        if ($remboursement['statut'] === 'annule') {
            return true;
        }

        $this->begin();
        try {
            $this->update(
                'remboursements',
                array('id_remboursement' => (int)$remboursement['id_remboursement']),
                array('statut' => 'annule')
            );
            if (!empty($remboursement['id_dette'])) {
                $this->_paiementService()->recalculerDette((int)$remboursement['id_dette']);
            }
            $paiement = $this->row(
                'SELECT id_echeance FROM paiements WHERE id_paiement = ? AND deleted_at IS NULL',
                array((int)$remboursement['id_paiement'])
            );
            if ($paiement) {
                $this->_paiementService()->syncEcheance(!empty($paiement['id_echeance']) ? (int)$paiement['id_echeance'] : null);
            }
            if (!$this->transactionOk()) {
                throw new RuntimeException('Transaction interrompue');
            }
            $this->commit();
        } catch (Throwable $e) {
            $this->rollback();
            $this->fail('Annulation du remboursement impossible : ' . $e->getMessage());
        }

        if ($this->audit) {
            $this->audit->log('annulation_remboursement', 'remboursements', (int)$remboursement['id_remboursement'], array(
                'statut' => $remboursement['statut']
            ), array('statut' => 'annule'), $motif);
        }
        return true;
    }

    public function lister($filters = array())
    {
        $sql = "SELECT r.*, e.fullname, e.matricule,
                       p.montant AS paiement_montant, p.date_paiement, p.mode_paiement,
                       tf.libelle AS type_libelle,
                       d.montant_initial AS dette_montant_initial, d.statut AS statut_dette
                FROM remboursements r
                LEFT JOIN paiements p ON p.id_paiement = r.id_paiement
                LEFT JOIN etudiants e ON e.id_etudiant = r.id_etudiant
                LEFT JOIN dettes d ON d.id_dette = r.id_dette
                LEFT JOIN frais f ON f.id_frais = d.id_frais
                LEFT JOIN types_frais tf ON tf.id_type_frais = f.id_type_frais
                WHERE r.deleted_at IS NULL";
        $binds = array();

        if (!empty($filters['uuid'])) {
            $sql .= ' AND r.uuid = ?';
            $binds[] = (string)$filters['uuid'];
        }
        if (!empty($filters['id_paiement'])) {
            $sql .= ' AND r.id_paiement = ?';
            $binds[] = (int)$filters['id_paiement'];
        }
        if (!empty($filters['id_dette'])) {
            $sql .= ' AND r.id_dette = ?';
            $binds[] = (int)$filters['id_dette'];
        }
        if (!empty($filters['id_etudiant'])) {
            $sql .= ' AND r.id_etudiant = ?';
            $binds[] = (int)$filters['id_etudiant'];
        }
        if (!empty($filters['id_annee'])) {
            $sql .= ' AND r.id_annee = ?';
            $binds[] = (int)$filters['id_annee'];
        }
        if (!empty($filters['statut'])) {
            $sql .= ' AND r.statut = ?';
            $binds[] = $filters['statut'];
        }
        if (!empty($filters['date_debut'])) {
            $sql .= ' AND r.date_remboursement >= ?';
            $binds[] = $filters['date_debut'] . ' 00:00:00';
        }
        if (!empty($filters['date_fin'])) {
            $sql .= ' AND r.date_remboursement <= ?';
            $binds[] = $filters['date_fin'] . ' 23:59:59';
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (e.fullname LIKE ? OR e.matricule LIKE ? OR r.reference LIKE ?)';
            $like = '%' . trim($filters['search']) . '%';
            $binds[] = $like;
            $binds[] = $like;
            $binds[] = $like;
        }
        $sql .= ' ORDER BY r.date_remboursement DESC, r.id_remboursement DESC';
        return $this->rows($sql, $binds);
    }

    public function getByUuid($uuid)
    {
        $rows = $this->lister(array('uuid' => $uuid));
        return $rows ? $rows[0] : null;
    }

    protected function _trouver($uuid)
    {
        if (is_numeric($uuid)) {
            return $this->row(
                'SELECT * FROM remboursements WHERE id_remboursement = ? AND deleted_at IS NULL',
                array((int)$uuid)
            );
        }
        return $this->row(
            'SELECT * FROM remboursements WHERE uuid = ? AND deleted_at IS NULL',
            array((string)$uuid)
        );
    }
}
