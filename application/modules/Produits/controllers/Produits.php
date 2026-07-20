<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produits extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des Stocks';
        $data['categories'] = $this->Model->read('categories_produits', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('p.deleted_at', null);
        $this->db->select('p.*, cp.libelle as categorie, cp.code as categorie_code');
        $this->db->from('produits p');
        $this->db->join('categories_produits cp', 'p.id_categorie = cp.id_categorie', 'left');
        $this->db->order_by('p.id_produit', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_get($id) {
        $p = $this->Model->readOne('produits', ['uuid' => $id]);
        if (!$p) { $this->json_error('Produit non trouvé', 404); return; }
        $p['mouvements'] = $this->db
            ->where('id_produit', $p['id_produit'])
            ->order_by('cree_le', 'DESC')
            ->get('mouvements_stock')
            ->result_array();
        $p['categorie_libelle'] = '';
        if ($p['id_categorie']) {
            $cat = $this->Model->readOne('categories_produits', ['id_categorie' => $p['id_categorie']]);
            $p['categorie_libelle'] = $cat ? $cat['libelle'] : '';
        }
        $this->json_success($p);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle'])) { $this->json_error('Libellé obligatoire'); return; }
        $this->_ensure_categories();
        if (empty($data['id_categorie'])) {
            $first = $this->Model->readOne('categories_produits', ['deleted_at' => null]);
            $data['id_categorie'] = $first ? $first['id_categorie'] : null;
        }
        $cols = ['code','libelle','id_categorie','prix_unitaire','stock_actuel','stock_mini','unite','description','taille'];
        $clean = [];
        foreach ($cols as $col) {
            if (isset($data[$col]) && $data[$col] !== '') {
                $clean[$col] = $data[$col];
            }
        }
        $id = $this->Model->createLastId('produits', $clean);
        if ($id) {
            $stock_initial = !empty($data['stock_initial']) ? intval($data['stock_initial']) : 0;
            if ($stock_initial > 0) {
                $this->Model->create('mouvements_stock', [
                    'id_produit' => $id,
                    'type' => 'entree',
                    'quantite' => $stock_initial,
                    'prix_unitaire' => $data['prix_unitaire'] ?? 0,
                    'motif' => 'Stock initial',
                    'id_utilisateur' => $this->session->userdata('id_utilisateur')
                ]);
            }
            $this->json_success(['id_produit' => $id], 'Produit créé');
        } else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $produit = $this->Model->readOne('produits', ['uuid' => $id]);
        if (!$produit) { $this->json_error('Produit non trouvé', 404); return; }

        if (isset($data['id_categorie']) && empty($data['id_categorie'])) {
            unset($data['id_categorie']);
        }

        $old_stock = intval($produit['stock_actuel']);
        $new_stock = isset($data['stock_actuel']) ? intval($data['stock_actuel']) : $old_stock;

        if ($new_stock != $old_stock) {
            $diff = $new_stock - $old_stock;
            $this->Model->create('mouvements_stock', [
                'id_produit' => $produit['id_produit'],
                'type' => $diff > 0 ? 'entree' : 'sortie',
                'quantite' => abs($diff),
                'prix_unitaire' => $data['prix_unitaire'] ?? $produit['prix_unitaire'],
                'motif' => 'Ajustement manuel',
                'id_utilisateur' => $this->session->userdata('id_utilisateur')
            ]);
        }

        if ($this->Model->update('produits', ['uuid' => $id], $data))
            $this->json_success(null, 'Produit mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('produits', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Produit supprimé');
        else $this->json_error('Erreur');
    }

    public function api_categories() {
        $this->_ensure_categories();
        $this->json_success($this->Model->read('categories_produits', ['deleted_at' => null]));
    }

    private function _ensure_categories() {
        $existing = $this->Model->read('categories_produits', ['deleted_at' => null]);
        if (empty($existing)) {
            $defaults = [
                ['code' => 'UNIFORME', 'libelle' => 'Uniformes'],
                ['code' => 'LIVRE', 'libelle' => 'Livres et manuels'],
                ['code' => 'MATERIEL', 'libelle' => 'Matériels scolaires'],
                ['code' => 'FOURNITURE', 'libelle' => 'Fournitures diverses'],
                ['code' => 'TOILETTE', 'libelle' => 'Produits de toilette'],
            ];
            $this->load->helper('uuid');
            foreach ($defaults as $d) {
                $d['uuid'] = generate_uuid();
                $this->db->insert('categories_produits', $d);
            }
        }
    }
}
