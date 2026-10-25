<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Base.php';

class Minervale_exoneration extends Minervale_base
{
    protected $audit;

    public function __construct($db, $ci = null, ?Minervale_audit $audit = null)
    {
        parent::__construct($db, $ci);
        $this->audit = $audit;
    }

    public function lister($filters = array())
    {
        $sql = "SELECT ex.*, e.fullname, e.matricule,
                       f.montant AS frais_montant, f.id_annee AS frais_annee, f.id_periode,
                       tf.libelle AS type_libelle, tf.code AS type_code,
                       a.libelle AS annee_libelle,
                       d.nom_complet AS demandeur_nom,
                       ap.nom_complet AS approbateur_nom
                FROM exonerations ex
                LEFT JOIN etudiants e ON e.id_etudiant = ex.id_etudiant
                LEFT JOIN frais f ON f.id_frais = ex.id_frais
                LEFT JOIN types_frais tf ON tf.id_type_frais = f.id_type_frais
                LEFT JOIN annees_scolaires a ON a.id_annee = ex.id_annee
                LEFT JOIN utilisateurs d ON d.id_utilisateur = ex.id_demandeur
                LEFT JOIN utilisateurs ap ON ap.id_utilisateur = ex.id_approbateur
                WHERE ex.deleted_at IS NULL";
        $binds = array();

        if (!empty($filters['id_annee'])) {
            $sql .= ' AND ex.id_annee = ?';
            $binds[] = (int)$filters['id_annee'];
        }
        if (!empty($filters['id_etudiant'])) {
            $sql .= ' AND ex.id_etudiant = ?';
            $binds[] = (int)$filters['id_etudiant'];
        }
        if (!empty($filters['id_frais'])) {
            $sql .= ' AND ex.id_frais = ?';
            $binds[] = (int)$filters['id_frais'];
        }
        if (!empty($filters['statut_demande'])) {
            $sql .= ' AND ex.statut_demande = ?';
            $binds[] = $filters['statut_demande'];
        }
        if (!empty($filters['statut'])) {
            $sql .= ' AND ex.statut = ?';
            $binds[] = $filters['statut'];
        }
        if (!empty($filters['reference_groupe'])) {
            $sql .= ' AND ex.reference_groupe = ?';
            $binds[] = $filters['reference_groupe'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (e.fullname LIKE ? OR e.matricule LIKE ?)';
            $like = '%' . trim($filters['search']) . '%';
            $binds[] = $like;
            $binds[] = $like;
        }
        $sql .= ' ORDER BY ex.date_demande DESC, ex.id_exoneration DESC';
        return $this->rows($sql, $binds);
    }

    public function getByUuid($uuid)
    {
        $rows = $this->lister(array('reference_groupe' => $uuid));
        if (!empty($rows)) {
            return $rows[0];
        }
        return $this->row(
            'SELECT * FROM exonerations WHERE uuid = ? AND deleted_at IS NULL',
            array((string)$uuid)
        );
    }

    public function demander($data)
    {
        $id_etudiant = !empty($data['id_etudiant']) ? (int)$data['id_etudiant'] : 0;
        if ($id_etudiant <= 0) {
            $this->fail('Étudiant obligatoire');
        }
        if (!isset($data['valeur']) || $data['valeur'] === '' || floatval($data['valeur']) <= 0) {
            $this->fail('Valeur de l\'exonération invalide');
        }
        $mode = isset($data['mode']) && in_array($data['mode'], array('pourcentage', 'montant'), true)
            ? $data['mode'] : 'montant';
        $valeur = floatval($data['valeur']);
        if ($mode === 'pourcentage' && $valeur > 100) {
            $this->fail('Le pourcentage ne peut dépasser 100');
        }

        $etudiant = $this->row(
            'SELECT id_etudiant FROM etudiants WHERE id_etudiant = ? AND deleted_at IS NULL',
            array($id_etudiant)
        );
        if (!$etudiant) {
            $this->fail('Étudiant introuvable', 404);
        }

        $id_frais = !empty($data['id_frais']) ? (int)$data['id_frais'] : null;
        $id_annee = !empty($data['id_annee']) ? (int)$data['id_annee'] : $this->activeYearId();
        if ($id_frais) {
            $frais = $this->row(
                'SELECT * FROM frais WHERE id_frais = ? AND deleted_at IS NULL',
                array($id_frais)
            );
            if (!$frais) {
                $this->fail('Frais introuvable', 404);
            }
            $id_annee = (int)$frais['id_annee'];
            $base = floatval($frais['montant']);
            $dette = $this->row(
                'SELECT montant_initial FROM dettes
                 WHERE id_etudiant = ? AND id_frais = ? AND id_annee = ? AND deleted_at IS NULL
                 ORDER BY id_dette DESC LIMIT 1',
                array($id_etudiant, $id_frais, $id_annee)
            );
            if ($dette) {
                $base = floatval($dette['montant_initial']);
            } else {
                $regime = $this->row(
                    'SELECT is_interne FROM etudiants WHERE id_etudiant = ? AND deleted_at IS NULL',
                    array($id_etudiant)
                );
                if (!empty($regime['is_interne']) && isset($frais['montant_interne']) && $frais['montant_interne'] !== null) {
                    $base = floatval($frais['montant_interne']);
                }
            }
            if ($mode === 'montant' && $valeur > $base) {
                $this->fail('Le montant de l\'exonération dépasse le frais');
            }
        }
        if ($id_annee <= 0) {
            $this->fail('Année scolaire introuvable');
        }

        $type = isset($data['type_demande']) && $data['type_demande'] === 'collective'
            ? 'collective' : 'individuelle';
        $reference = !empty($data['reference_groupe']) ? (string)$data['reference_groupe'] : null;
        if ($type === 'collective' && !$reference) {
            $reference = self::uuid();
        }

        $userId = $this->userId();
        $insert = array(
            'uuid' => self::uuid(),
            'id_etudiant' => $id_etudiant,
            'id_annee' => $id_annee,
            'id_frais' => $id_frais,
            'mode' => $mode,
            'valeur' => $valeur,
            'motif' => !empty($data['motif']) ? substr(trim($data['motif']), 0, 255) : null,
            'id_utilisateur' => $userId,
            'statut' => 'active',
            'statut_demande' => 'en_attente',
            'type_demande' => $type,
            'reference_groupe' => $reference,
            'id_demandeur' => $userId,
            'id_approbateur' => null,
            'date_demande' => $this->now(),
            'date_decision' => null
        );
        $this->insert('exonerations', $insert);
        $id = $this->insertId();

        if ($this->audit) {
            $this->audit->log('demande_exoneration', 'exonerations', $id, null, $insert, $insert['motif']);
        }

        return array(
            'id_exoneration' => $id,
            'uuid' => $insert['uuid'],
            'statut_demande' => 'en_attente',
            'reference_groupe' => $reference
        );
    }

    public function demanderCollective($data)
    {
        $etudiants = isset($data['id_etudiants']) && is_array($data['id_etudiants'])
            ? array_values(array_unique(array_map('intval', $data['id_etudiants'])))
            : array();
        if (empty($etudiants)) {
            $this->fail('Aucun étudiant sélectionné');
        }
        $reference = !empty($data['reference_groupe']) ? (string)$data['reference_groupe'] : self::uuid();
        $created = array();
        $skipped = 0;

        $this->begin();
        try {
            foreach ($etudiants as $id_etudiant) {
                $row = $data;
                $row['id_etudiant'] = $id_etudiant;
                $row['type_demande'] = 'collective';
                $row['reference_groupe'] = $reference;
                try {
                    $created[] = $this->demander($row);
                } catch (Minervale_exception $e) {
                    $skipped++;
                }
            }
            if (!$this->transactionOk()) {
                throw new RuntimeException('Transaction interrompue');
            }
            $this->commit();
        } catch (Throwable $e) {
            $this->rollback();
            $this->fail('Demande collective impossible : ' . $e->getMessage());
        }

        return array(
            'reference_groupe' => $reference,
            'created' => count($created),
            'skipped' => $skipped,
            'demandes' => $created
        );
    }

    public function approuver($uuid, $id_approbateur = null)
    {
        $exo = $this->getByUuid($uuid);
        if (!$exo) {
            $this->fail('Exonération introuvable', 404);
        }
        if ($exo['statut_demande'] !== 'en_attente') {
            $this->fail('Seule une demande en attente peut être approuvée');
        }
        $approbateur = $id_approbateur !== null ? (int)$id_approbateur : $this->userId();
        $update = array(
            'statut' => 'active',
            'statut_demande' => 'approuvee',
            'id_approbateur' => $approbateur,
            'date_decision' => $this->now()
        );
        $this->update('exonerations', array('id_exoneration' => (int)$exo['id_exoneration']), $update);
        $this->_recalculerStatuts((int)$exo['id_etudiant'], (int)$exo['id_annee'], !empty($exo['id_frais']) ? (int)$exo['id_frais'] : null);
        if ($this->audit) {
            $this->audit->log('approbation_exoneration', 'exonerations', (int)$exo['id_exoneration'], array(
                'statut_demande' => $exo['statut_demande']
            ), $update, !empty($exo['motif']) ? $exo['motif'] : null);
        }
        return $update;
    }

    public function refuser($uuid, $id_approbateur = null, $motif = null)
    {
        $exo = $this->getByUuid($uuid);
        if (!$exo) {
            $this->fail('Exonération introuvable', 404);
        }
        if ($exo['statut_demande'] !== 'en_attente') {
            $this->fail('Seule une demande en attente peut être refusée');
        }
        $approbateur = $id_approbateur !== null ? (int)$id_approbateur : $this->userId();
        $update = array(
            'statut' => 'annulee',
            'statut_demande' => 'refusee',
            'id_approbateur' => $approbateur,
            'date_decision' => $this->now()
        );
        $this->update('exonerations', array('id_exoneration' => (int)$exo['id_exoneration']), $update);
        $this->_recalculerStatuts((int)$exo['id_etudiant'], (int)$exo['id_annee'], !empty($exo['id_frais']) ? (int)$exo['id_frais'] : null);
        if ($this->audit) {
            $this->audit->log('refus_exoneration', 'exonerations', (int)$exo['id_exoneration'], array(
                'statut_demande' => $exo['statut_demande']
            ), $update, $motif);
        }
        return $update;
    }

    public function annuler($uuid, $id_approbateur = null, $motif = null)
    {
        $exo = $this->getByUuid($uuid);
        if (!$exo) {
            $this->fail('Exonération introuvable', 404);
        }
        if ($exo['statut'] === 'annulee') {
            return true;
        }
        $approbateur = $id_approbateur !== null ? (int)$id_approbateur : $this->userId();
        $update = array(
            'statut' => 'annulee',
            'id_approbateur' => $approbateur,
            'date_decision' => $this->now()
        );
        if ($exo['statut_demande'] === 'en_attente') {
            $update['statut_demande'] = 'refusee';
        }
        $this->update('exonerations', array('id_exoneration' => (int)$exo['id_exoneration']), $update);
        $this->_recalculerStatuts((int)$exo['id_etudiant'], (int)$exo['id_annee'], !empty($exo['id_frais']) ? (int)$exo['id_frais'] : null);
        if ($this->audit) {
            $this->audit->log('annulation_exoneration', 'exonerations', (int)$exo['id_exoneration'], array(
                'statut' => $exo['statut']
            ), $update, $motif);
        }
        return true;
    }

    protected function _recalculerStatuts($id_etudiant, $id_annee, $id_frais = null)
    {
        require_once __DIR__ . '/Solde.php';
        require_once __DIR__ . '/Dette.php';
        $dette = new Minervale_dette($this->db, $this->ci);
        $solde = new Minervale_solde($this->db, $this->ci, $dette, $this->audit);
        $filters = array(
            'id_etudiant' => (int)$id_etudiant,
            'id_annee' => (int)$id_annee
        );
        if ($id_frais) {
            $filters['id_frais'] = (int)$id_frais;
        }
        try {
            $solde->recalculerStatuts($filters);
        } catch (Throwable $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Minervale exoneration recalcul: ' . $e->getMessage());
            }
        }
    }

    public function totalPourDette($id_etudiant, $id_frais, $id_annee)
    {
        $id_etudiant = (int)$id_etudiant;
        $id_frais = (int)$id_frais;
        $id_annee = (int)$id_annee;
        if ($id_etudiant <= 0 || $id_frais <= 0 || $id_annee <= 0) {
            return 0.0;
        }
        $dette = $this->row(
            'SELECT montant_initial FROM dettes
             WHERE id_etudiant = ? AND id_frais = ? AND id_annee = ? AND deleted_at IS NULL
             ORDER BY id_dette DESC LIMIT 1',
            array($id_etudiant, $id_frais, $id_annee)
        );
        if ($dette) {
            $base = floatval($dette['montant_initial']);
        } else {
            $frais = $this->row('SELECT montant FROM frais WHERE id_frais = ?', array($id_frais));
            $base = $frais ? floatval($frais['montant']) : 0.0;
        }
        if ($base <= 0) {
            return 0.0;
        }
        $rows = $this->rows(
            "SELECT mode, valeur FROM exonerations
             WHERE deleted_at IS NULL AND statut = 'active' AND statut_demande = 'approuvee'
               AND id_etudiant = ? AND id_annee = ?
               AND (id_frais = ? OR id_frais IS NULL)",
            array($id_etudiant, $id_annee, $id_frais)
        );
        $total = 0.0;
        foreach ($rows as $row) {
            $total += $row['mode'] === 'pourcentage'
                ? $base * floatval($row['valeur']) / 100
                : floatval($row['valeur']);
        }
        return max(0, min($base, $total));
    }
}
