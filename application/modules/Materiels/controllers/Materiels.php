<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Materiels extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Matériels Scolaires';
        $data['etudiants'] = $this->Model->read('etudiants', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('p.deleted_at', null);
        $this->db->where('cp.code', 'MATERIEL');
        $this->db->select('p.*, cp.libelle as categorie');
        $this->db->from('produits p');
        $this->db->join('categories_produits cp', 'p.id_categorie = cp.id_categorie');
        $this->db->order_by('p.libelle', 'ASC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_get($id) {
        $p = $this->Model->readOne('produits', ['uuid' => $id, 'deleted_at' => null]);
        if (!$p) { $this->json_error('Produit non trouvé', 404); return; }
        $p['mouvements'] = $this->Model->read('mouvements_stock', ['id_produit' => $p['id_produit']], 'cree_le');
        $this->json_success($p);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle'])) { $this->json_error('Libellé obligatoire'); return; }
        $this->load->helper('uuid');
        $categorie = $this->Model->readOne('categories_produits', ['code' => 'MATERIEL']);
        $insert = [
            'uuid' => generate_uuid(),
            'code' => $data['code'] ?? null,
            'libelle' => $data['libelle'],
            'id_categorie' => $categorie ? $categorie['id_categorie'] : null,
            'prix_unitaire' => $data['prix_unitaire'] ?? 0,
            'stock_actuel' => $data['stock_actuel'] ?? $data['stock'] ?? 0,
            'stock_mini' => $data['stock_mini'] ?? 0,
            'description' => $data['description'] ?? null,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ];
        $id = $this->Model->createLastId('produits', $insert);
        if ($id) {
            if (!empty($data['stock_actuel']) && $data['stock_actuel'] > 0) {
                $this->Model->create('mouvements_stock', [
                    'id_produit' => $id, 'type' => 'entree', 'quantite' => $data['stock_actuel'],
                    'motif' => 'Stock initial'
                ]);
            }
            $this->json_success(['id_produit' => $id], 'Produit créé');
        } else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $update = ['modifie_le' => date('Y-m-d H:i:s')];
        foreach (['libelle', 'code', 'prix_unitaire', 'stock_actuel', 'stock_mini', 'description'] as $f) {
            if (isset($data[$f])) $update[$f] = $data[$f];
        }
        if (isset($data['stock'])) $update['stock_actuel'] = $data['stock'];
        if ($this->Model->update('produits', ['uuid' => $id], $update))
            $this->json_success(null, 'Produit mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('produits', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s'), 'modifie_le' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Produit supprimé');
        else $this->json_error('Erreur');
    }

    public function api_initialiser() {
        $categorie = $this->Model->readOne('categories_produits', ['code' => 'MATERIEL']);
        if (!$categorie) { $this->json_error('Catégorie MATERIEL introuvable'); return; }
        $this->load->helper('uuid');
        $articles = [
            ['code' => 'CRAYON', 'libelle' => 'Crayons', 'prix_unitaire' => 500],
            ['code' => 'TAILLECRAY', 'libelle' => 'Taille-crayons', 'prix_unitaire' => 300],
            ['code' => 'RAMPAPIER', 'libelle' => 'Rame de papiers', 'prix_unitaire' => 15000],
            ['code' => 'CRAYCOULEUR', 'libelle' => 'Crayons de couleurs', 'prix_unitaire' => 2500],
            ['code' => 'GOMME', 'libelle' => 'Gommes', 'prix_unitaire' => 200],
            ['code' => 'STYLO', 'libelle' => 'Stylos', 'prix_unitaire' => 400],
            ['code' => 'CAHIER', 'libelle' => 'Cahiers', 'prix_unitaire' => 1500],
            ['code' => 'REGLE', 'libelle' => 'Règles', 'prix_unitaire' => 500],
        ];
        $created = 0;
        foreach ($articles as $a) {
            $exists = $this->Model->readOne('produits', ['code' => $a['code'], 'id_categorie' => $categorie['id_categorie'], 'deleted_at' => null]);
            if ($exists) continue;
            $this->Model->create('produits', [
                'uuid' => generate_uuid(),
                'code' => $a['code'],
                'libelle' => $a['libelle'],
                'id_categorie' => $categorie['id_categorie'],
                'prix_unitaire' => $a['prix_unitaire'],
                'stock_actuel' => 0,
                'stock_mini' => 0,
                'cree_le' => date('Y-m-d H:i:s'),
                'modifie_le' => date('Y-m-d H:i:s'),
            ]);
            $created++;
        }
        $this->json_success(['created' => $created], "$created article(s) créé(s)");
    }
}
