<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jours extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Jours de la semaine';
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('deleted_at', null);
        $this->db->order_by('ordre');
        $this->json_success($this->db->get('jours_semaine')->result_array());
    }

    public function api_get($id) {
        $c = $this->Model->readOne('jours_semaine', ['uuid' => $id]);
        if (!$c) { $this->json_error('Jour non trouvé', 404); return; }
        $this->json_success($c);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle']) || empty($data['code'])) {
            $this->json_error('Libellé et code obligatoires'); return;
        }
        $allowed = ['code', 'libelle', 'actif', 'ordre'];
        $insert = array_intersect_key($data, array_flip($allowed));
        $id = $this->Model->createLastId('jours_semaine', $insert);
        if ($id) $this->json_success(['id_jour' => $id], 'Jour créé');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (!$this->Model->readOne('jours_semaine', ['uuid' => $id])) {
            $this->json_error('Jour non trouvé', 404); return;
        }
        $allowed = ['code', 'libelle', 'actif', 'ordre'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if ($this->Model->update('jours_semaine', ['uuid' => $id], $update))
            $this->json_success(null, 'Jour mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        $now = date('Y-m-d H:i:s');
        if ($this->Model->update('jours_semaine', ['uuid' => $id], ['deleted_at' => $now]))
            $this->json_success(null, 'Jour supprimé');
        else $this->json_error('Erreur');
    }
}
