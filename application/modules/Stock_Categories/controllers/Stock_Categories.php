<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Categories extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des Catégories';
        $data['categories'] = $this->Model->read('categories_produits', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('deleted_at', null);
        $this->db->order_by('libelle');
        $this->json_success($this->db->get('categories_produits')->result_array());
    }

    public function api_get($id) {
        $c = $this->Model->readOne('categories_produits', ['uuid' => $id]);
        if (!$c) { $this->json_error('Catégorie non trouvée', 404); return; }
        $this->json_success($c);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['code']) || empty($data['libelle'])) {
            $this->json_error('Code et libellé obligatoires'); return;
        }
        $allowed = ['code', 'libelle'];
        $insert = array_intersect_key($data, array_flip($allowed));
        $id = $this->Model->createLastId('categories_produits', $insert);
        if ($id) $this->json_success(null, 'Catégorie créée');
        else $this->json_error('Erreur lors de la création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (!$this->Model->readOne('categories_produits', ['uuid' => $id])) {
            $this->json_error('Catégorie non trouvée', 404); return;
        }
        $allowed = ['code', 'libelle'];
        $update = array_intersect_key($data, array_flip($allowed));
        if ($this->Model->update('categories_produits', ['uuid' => $id], $update))
            $this->json_success(null, 'Catégorie mise à jour');
        else $this->json_error('Erreur lors de la mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('categories_produits', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Catégorie supprimée');
        else $this->json_error('Erreur lors de la suppression');
    }
}
