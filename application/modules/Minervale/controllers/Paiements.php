<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Base_minervale.php';

class Paiements extends Base_minervale
{
    public function index()
    {
        $data = $this->_options(true);
        $data['title'] = 'Minervale - Encaissements';
        $this->load->view('paiements/index', $data);
    }

    public function api_list()
    {
        $this->_run(function () {
            return $this->paiement->lister($this->_filters());
        });
    }

    public function api_get($uuid)
    {
        $this->_run(function () use ($uuid) {
            $paiement = $this->paiement->getByUuid($uuid);
            if (!$paiement) {
                throw new Minervale_exception('Paiement introuvable', 404);
            }
            $paiement['affectations'] = $this->paiement->getAffectations((int)$paiement['id_paiement']);
            return $paiement;
        });
    }

    public function api_create()
    {
        $data = $this->_input();
        $this->_run(function () use ($data) {
            return $this->paiement->enregistrer($data);
        });
    }

    public function api_annuler($uuid)
    {
        $data = $this->_input();
        $motif = isset($data['motif']) ? $data['motif'] : null;
        $this->_run(function () use ($uuid, $motif) {
            return $this->paiement->annuler($uuid, $motif);
        });
    }

    public function api_affectations($uuid)
    {
        $this->_run(function () use ($uuid) {
            $paiement = $this->paiement->getByUuid($uuid);
            if (!$paiement) {
                throw new Minervale_exception('Paiement introuvable', 404);
            }
            return $this->paiement->getAffectations((int)$paiement['id_paiement']);
        });
    }

    public function api_ventiler($uuid)
    {
        $data = $this->_input();
        $affectations = isset($data['affectations']) ? $data['affectations'] : array();
        $remplacer = !empty($data['remplacer']);
        $this->_run(function () use ($uuid, $affectations, $remplacer) {
            return $this->paiement->ventiler($uuid, $affectations, $remplacer);
        });
    }
}
