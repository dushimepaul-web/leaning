<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Base_minervale.php';

class Exonerations extends Base_minervale
{
    public function index()
    {
        $data = $this->_options(true);
        $data['title'] = 'Minervale - Exonerations';
        $this->load->view('exonerations/index', $data);
    }

    public function api_list()
    {
        $this->_run(function () {
            return $this->exoneration->lister($this->_filters());
        });
    }

    public function api_get($uuid)
    {
        $this->_run(function () use ($uuid) {
            $row = $this->exoneration->getByUuid($uuid);
            if (!$row) {
                throw new Minervale_exception('Exoneration introuvable', 404);
            }
            return $row;
        });
    }

    public function api_create()
    {
        $data = $this->_input();
        $this->_run(function () use ($data) {
            return $this->exoneration->demander($data);
        });
    }

    public function api_collective()
    {
        $data = $this->_input();
        $this->_run(function () use ($data) {
            return $this->exoneration->demanderCollective($data);
        });
    }

    public function api_approuver($uuid)
    {
        $data = $this->_input();
        $approbateur = isset($data['id_approbateur']) ? (int)$data['id_approbateur'] : null;
        $this->_run(function () use ($uuid, $approbateur) {
            return $this->exoneration->approuver($uuid, $approbateur);
        });
    }

    public function api_refuser($uuid)
    {
        $data = $this->_input();
        $approbateur = isset($data['id_approbateur']) ? (int)$data['id_approbateur'] : null;
        $motif = isset($data['motif']) ? $data['motif'] : null;
        $this->_run(function () use ($uuid, $approbateur, $motif) {
            return $this->exoneration->refuser($uuid, $approbateur, $motif);
        });
    }

    public function api_annuler($uuid)
    {
        $data = $this->_input();
        $motif = isset($data['motif']) ? $data['motif'] : null;
        $this->_run(function () use ($uuid, $motif) {
            return $this->exoneration->annuler($uuid, null, $motif);
        });
    }
}
