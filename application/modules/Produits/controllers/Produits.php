<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produits extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des produits';
        $data['categories'] = $this->Model->read('categories_produits', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('p.deleted_at', null);
        $this->db->select('p.*, cp.libelle as categorie');
        $this->db->from('produits p');
        $this->db->join('categories_produits cp', 'p.id_categorie = cp.id_categorie', 'left');
        $this->db->order_by('p.id_produit', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_get($id) {
        $p = $this->Model->readOne('produits', ['uuid' => $id]);
        if (!$p) { $this->json_error('Produit non trouvé', 404); return; }
        $p['mouvements'] = $this->Model->read('mouvements_stock', ['id_produit' => $p['id_produit']], 'cree_le');
        $this->json_success($p);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle'])) { $this->json_error('Libellé obligatoire'); return; }
        $id = $this->Model->createLastId('produits', $data);
        if ($id) {
            if (!empty($data['stock_initial']) && $data['stock_initial'] > 0) {
                $this->Model->create('mouvements_stock', [
                    'id_produit' => $id, 'type' => 'entree', 'quantite' => $data['stock_initial'],
                    'stock_apres' => $data['stock_initial'], 'motif' => 'Stock initial'
                ]);
            }
            $this->json_success(['id_produit' => $id], 'Produit créé');
        } else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if ($this->Model->update('produits', ['uuid' => $id], $data))
            $this->json_success(null, 'Produit mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('produits', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Produit supprimé');
        else $this->json_error('Erreur');
    }
}
