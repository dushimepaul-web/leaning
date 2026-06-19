<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menus extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des menus';
        $this->load->view('menus/index', $data);
    }

    public function api_list() {
        $this->db->order_by('ordre');
        $this->json_success($this->db->get('menus')->result_array());
    }

    public function api_get($id) {
        $r = $this->Model->readOne('menus', ['uuid' => $id]);
        if (!$r) { $this->json_error('Menu non trouvé', 404); return; }
        $this->json_success($r);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['code']) || empty($data['libelle'])) {
            $this->json_error('Code et libellé obligatoires'); return;
        }
        $id = $this->Model->createLastId('menus', $data);
        if ($id) $this->json_success(['id_menu' => $id], 'Menu créé');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if ($this->Model->update('menus', ['uuid' => $id], $data))
            $this->json_success(null, 'Menu mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        $children = $this->Model->read('menus', ['parent_id' => $id]);
        if (!empty($children)) {
            $this->json_error('Supprimez d\'abord les sous-menus'); return;
        }
        if ($this->db->where('uuid', $id)->delete('menus'))
            $this->json_success(null, 'Menu supprimé');
        else $this->json_error('Erreur');
    }
}
