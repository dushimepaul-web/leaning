<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Departements extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function api_list() {
        $this->db->where('deleted_at', null);
        $this->db->order_by('libelle');
        $this->json_success($this->db->get('departements')->result_array());
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle'])) {
            $this->json_error('Libellé obligatoire'); return;
        }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'libelle' => $data['libelle'],
            'description' => $data['description'] ?? null,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ];
        $id = $this->Model->createLastId('departements', $insert);
        if ($id) $this->json_success(['id_departement' => $id], 'Département créé');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('departements', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s'), 'modifie_le' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Département supprimé');
        else $this->json_error('Erreur');
    }
}
