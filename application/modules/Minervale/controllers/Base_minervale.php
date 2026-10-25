<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/Minervale/Base.php';
require_once APPPATH . 'libraries/Minervale/Audit.php';
require_once APPPATH . 'libraries/Minervale/Dette.php';
require_once APPPATH . 'libraries/Minervale/Solde.php';
require_once APPPATH . 'libraries/Minervale/Exoneration.php';
require_once APPPATH . 'libraries/Minervale/Paiement.php';
require_once APPPATH . 'libraries/Minervale/Remboursement.php';

class Base_minervale extends MY_Controller
{
    protected $audit;
    protected $dette;
    protected $solde;
    protected $exoneration;
    protected $paiement;
    protected $remboursement;

    public function __construct()
    {
        parent::__construct();

        $this->audit = new Minervale_audit($this->db, $this);
        $this->dette = new Minervale_dette($this->db, $this, $this->audit);
        $this->exoneration = new Minervale_exoneration($this->db, $this, $this->audit);
        $this->solde = new Minervale_solde($this->db, $this, $this->dette, $this->audit);
        $this->paiement = new Minervale_paiement($this->db, $this, $this->exoneration, $this->audit);
        $this->remboursement = new Minervale_remboursement($this->db, $this, $this->paiement, $this->audit);
    }

    protected function _filters()
    {
        $filters = array();
        $keys = array(
            'uuid', 'id_dette', 'id_paiement', 'id_etudiant', 'id_frais',
            'id_annee', 'id_periode', 'id_classe', 'id_section', 'id_type_frais',
            'statut', 'statut_demande', 'reference_groupe', 'search',
            'date_debut', 'date_fin'
        );
        foreach ($keys as $key) {
            $value = $this->input->get($key);
            if ($value !== null && $value !== '') {
                $filters[$key] = is_string($value) ? trim($value) : $value;
            }
        }

        $specific = !empty($filters['uuid']) || !empty($filters['id_dette']) || !empty($filters['id_paiement']);
        if (!$specific && !array_key_exists('id_annee', $filters) && $this->input->get('toutes_annees') !== '1') {
            $filters['id_annee'] = $this->id_annee_active;
        }
        return $filters;
    }

    protected function _options($extended = false)
    {
        $options = array(
            'annees' => $this->Model->read('annees_scolaires', array('deleted_at' => null), 'id_annee', 'DESC'),
            'periodes' => $this->_query(
                'SELECT * FROM periodes WHERE deleted_at IS NULL AND id_annee = ? ORDER BY id_periode ASC',
                array($this->id_annee_active)
            ),
            'periodes_toutes' => $this->_query(
                'SELECT p.*, a.libelle AS annee_libelle
                 FROM periodes p
                 LEFT JOIN annees_scolaires a ON a.id_annee = p.id_annee
                 WHERE p.deleted_at IS NULL
                 ORDER BY p.id_annee DESC, p.id_periode ASC'
            ),
            'classes' => $this->Model->read('classes', array('deleted_at' => null), 'libelle', 'ASC'),
            'sections' => $this->Model->read('sections', array('deleted_at' => null), 'libelle', 'ASC'),
            'types_frais' => $this->Model->read('types_frais', array('deleted_at' => null), 'libelle', 'ASC'),
            'id_annee_active' => $this->id_annee_active,
            'id_periode_active' => $this->id_periode_active
        );

        if ($extended) {
            $options['etudiants'] = $this->_query(
                'SELECT e.id_etudiant, e.fullname, e.matricule,
                        i.id_classe, c.libelle AS classe_libelle
                 FROM etudiants e
                 LEFT JOIN inscriptions i ON i.id_etudiant = e.id_etudiant
                    AND i.id_annee = ? AND i.deleted_at IS NULL
                 LEFT JOIN classes c ON c.id_classe = i.id_classe
                 WHERE e.deleted_at IS NULL
                 ORDER BY e.fullname ASC',
                array($this->id_annee_active)
            );
            $options['frais'] = $this->_query(
                'SELECT f.*, tf.libelle AS type_libelle, tf.code AS type_code,
                        COALESCE(c.libelle, \'Toutes les classes\') AS classe_libelle,
                        p.libelle AS periode_libelle, a.libelle AS annee_libelle
                 FROM frais f
                 LEFT JOIN types_frais tf ON tf.id_type_frais = f.id_type_frais
                 LEFT JOIN classes c ON c.id_classe = f.id_classe
                 LEFT JOIN periodes p ON p.id_periode = f.id_periode
                 LEFT JOIN annees_scolaires a ON a.id_annee = f.id_annee
                 WHERE f.deleted_at IS NULL AND f.id_annee = ?
                 ORDER BY c.libelle ASC, tf.libelle ASC',
                array($this->id_annee_active)
            );
        }
        return array(
            'options' => $options,
            'id_annee_active' => $this->id_annee_active,
            'id_periode_active' => $this->id_periode_active
        );
    }

    protected function _query($sql, $binds = array())
    {
        $query = $this->db->query($sql, $binds);
        return $query !== false ? $query->result_array() : array();
    }

    protected function _input()
    {
        return $this->get_json_input();
    }

    protected function _run($callback)
    {
        try {
            $result = $callback();
            $this->json_success($result);
        } catch (Minervale_exception $e) {
            $code = (int)$e->getCode();
            $this->json_error($e->getMessage(), $code >= 400 ? $code : 400);
        } catch (Throwable $e) {
            log_message('error', 'Minervale : ' . $e->getMessage());
            $this->json_error('Erreur interne lors du traitement de la demande.', 500);
        }
    }
}
