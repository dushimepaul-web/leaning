<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Base_minervale.php';

class Soldes extends Base_minervale
{
    public function index()
    {
        $data = $this->_options();
        $data['title'] = 'Minervale - Soldes et impayes';
        $this->load->view('soldes/index', $data);
    }

    public function api_list()
    {
        $this->_run(function () {
            return $this->solde->charger($this->_filters());
        });
    }

    public function api_synthese()
    {
        $this->_run(function () {
            return $this->solde->synthese($this->_filters());
        });
    }

    public function api_classe($id_classe)
    {
        $filters = $this->_filters();
        $this->_run(function () use ($id_classe, $filters) {
            return $this->solde->suiviClasse((int)$id_classe, $filters);
        });
    }

    public function api_releve($id_etudiant)
    {
        $filters = $this->_filters();
        $this->_run(function () use ($id_etudiant, $filters) {
            return $this->solde->releveEtudiant((int)$id_etudiant, $filters);
        });
    }

    public function api_recalculer()
    {
        $filters = $this->_filters();
        $this->_run(function () use ($filters) {
            return array('updated' => $this->solde->recalculerStatuts($filters));
        });
    }
}
