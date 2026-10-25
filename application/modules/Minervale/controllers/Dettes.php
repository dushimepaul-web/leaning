<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Base_minervale.php';

class Dettes extends Base_minervale
{
    public function index()
    {
        $data = $this->_options(true);
        $data['title'] = 'Minervale - Dettes';
        $this->load->view('dettes/index', $data);
    }

    public function api_list()
    {
        $this->_run(function () {
            return $this->solde->charger($this->_filters());
        });
    }

    public function api_get($uuid)
    {
        $this->_run(function () use ($uuid) {
            $rows = $this->solde->charger(array('uuid' => $uuid));
            if (empty($rows)) {
                throw new Minervale_exception('Dette introuvable', 404);
            }
            return $rows[0];
        });
    }

    public function api_generer()
    {
        $data = $this->_input();
        $this->_run(function () use ($data) {
            return $this->dette->generer($data);
        });
    }

    public function api_recalculer()
    {
        $filters = $this->_filters();
        $this->_run(function () use ($filters) {
            return array('updated' => $this->solde->recalculerStatuts($filters));
        });
    }

    public function api_annuler($uuid)
    {
        $data = $this->_input();
        $motif = isset($data['motif']) ? $data['motif'] : null;
        $this->_run(function () use ($uuid, $motif) {
            return $this->dette->annuler($uuid, $motif);
        });
    }
}
