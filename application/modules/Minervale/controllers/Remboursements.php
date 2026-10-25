<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Base_minervale.php';

class Remboursements extends Base_minervale
{
    public function index()
    {
        $data = $this->_options();
        $data['title'] = 'Minervale - Remboursements';
        $this->load->view('remboursements/index', $data);
    }

    public function api_list()
    {
        $this->_run(function () {
            return $this->remboursement->lister($this->_filters());
        });
    }

    public function api_get($uuid)
    {
        $this->_run(function () use ($uuid) {
            $row = $this->remboursement->getByUuid($uuid);
            if (!$row) {
                throw new Minervale_exception('Remboursement introuvable', 404);
            }
            return $row;
        });
    }

    public function api_create()
    {
        $data = $this->_input();
        $this->_run(function () use ($data) {
            return $this->remboursement->enregistrer($data);
        });
    }

    public function api_annuler($uuid)
    {
        $data = $this->_input();
        $motif = isset($data['motif']) ? $data['motif'] : null;
        $this->_run(function () use ($uuid, $motif) {
            return $this->remboursement->annuler($uuid, $motif);
        });
    }

    public function api_paiements()
    {
        $filters = $this->_filters();
        $this->_run(function () use ($filters) {
            $rows = $this->paiement->lister($filters);
            $result = array();
            foreach ($rows as $row) {
                if ($row['statut'] === 'annule' || (float)$row['affecte'] <= 0.009) {
                    continue;
                }
                $result[] = $row;
                if (count($result) >= 300) {
                    break;
                }
            }
            return $result;
        });
    }
}
