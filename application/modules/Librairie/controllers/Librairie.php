<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Librairie extends MY_Controller {
    public function __construct() { parent::__construct(); $this->not_logged_in(); }

    public function index() {
        $data['title'] = 'Librairie';
        $data['categories'] = $this->Model->read('categories_produits', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $categorie = $this->input->get('categorie');
        $this->db->where('produits.deleted_at', null);
        $this->db->select('produits.*, categories_produits.libelle as categorie, categories_produits.code as code_categorie');
        $this->db->from('produits');
        $this->db->join('categories_produits', 'produits.id_categorie = categories_produits.id_categorie', 'left');
        if ($categorie) $this->db->where('categories_produits.code', strtoupper($categorie));
        $this->db->order_by('produits.libelle', 'ASC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_get($id) {
        $p = null;
        try {
            $p = $this->db
                ->select('produits.*, categories_produits.libelle as categorie, categories_produits.code as code_categorie')
                ->from('produits')
                ->join('categories_produits', 'produits.id_categorie = categories_produits.id_categorie', 'left')
                ->where('produits.uuid', (string)$id)
                ->where('produits.deleted_at IS NULL', null, false)
                ->get()->row_array();
        } catch (Exception $e) {
            $this->json_error('Erreur SQL: ' . $e->getMessage(), 500);
            return;
        }

        if (!$p) { 
            $this->json_error('Produit non trouvé', 404); 
            return; 
        }
        
        $mouvements = [];
        try {
            if (!empty($p['id_produit'])) {
                $mouvements = $this->Model->read('mouvements_stock', ['id_produit' => $p['id_produit']], 'date_mvt', 'DESC');
            }
        } catch (Exception $e) {
            $mouvements = [];
        }
        $p['mouvements'] = is_array($mouvements) ? $mouvements : [];
        $this->json_success($p);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle'])) { $this->json_error('Libellé obligatoire'); return; }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'id_categorie' => $data['id_categorie'] ?? null,
            'libelle' => $data['libelle'],
            'description' => $data['description'] ?? null,
            'taille' => $data['taille'] ?? null,
            'editeur' => $data['editeur'] ?? null,
            'annee_edition' => $data['annee_edition'] ?? null,
            'prix_achat' => $data['prix_achat'] ?? 0,
            'prix_unitaire' => $data['prix_unitaire'] ?? 0,
            'stock_mini' => $data['stock_mini'] ?? 0,
            'stock_actuel' => $data['stock_actuel'] ?? $data['stock'] ?? 0,
            'unite' => $data['unite'] ?? 'pièce',
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ];
        $id = $this->Model->createLastId('produits', $insert);
        if ($id) {
            $stock = intval($data['stock_actuel'] ?? $data['stock'] ?? 0);
            if ($stock > 0) {
                $this->Model->create('mouvements_stock', [
                    'id_produit' => $id, 'type' => 'entree', 'quantite' => $stock,
                    'motif' => 'Stock initial', 'date_mvt' => date('Y-m-d H:i:s'),
                ]);
            }
            $this->json_success(['id_produit' => $id], 'Produit créé');
        } else $this->json_error('Erreur de création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $allowed = ['id_categorie','libelle','description','taille','editeur','annee_edition','prix_achat','prix_unitaire','stock_mini','stock_actuel','unite'];
        if (isset($data['stock'])) $data['stock_actuel'] = $data['stock'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        $update['modifie_le'] = date('Y-m-d H:i:s');
        if ($this->Model->update('produits', ['uuid' => (string)$id], $update))
            $this->json_success(null, 'Produit mis à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('produits', ['uuid' => (string)$id], ['deleted_at' => date('Y-m-d H:i:s'), 'modifie_le' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Produit supprimé');
        else $this->json_error('Erreur de suppression');
    }

    public function api_initialiser() {
        $cat = $this->Model->readOne('categories_produits', ['code' => 'MATERIEL']);
        if (!$cat) { $this->json_error('Catégorie MATERIEL introuvable'); return; }
        $this->load->helper('uuid');
        $articles = [
            ['libelle' => 'Crayons', 'prix' => 500],
            ['libelle' => 'Taille-crayons', 'prix' => 300],
            ['libelle' => 'Rame de papiers', 'prix' => 15000],
            ['libelle' => 'Crayons de couleurs', 'prix' => 2500],
            ['libelle' => 'Gommes', 'prix' => 200],
            ['libelle' => 'Stylos', 'prix' => 400],
            ['libelle' => 'Cahiers', 'prix' => 1500],
            ['libelle' => 'Règles', 'prix' => 500],
        ];
        $created = 0;
        foreach ($articles as $a) {
            $exists = $this->Model->readOne('produits', ['libelle' => $a['libelle'], 'id_categorie' => $cat['id_categorie'], 'deleted_at' => null]);
            if ($exists) continue;
            $this->Model->create('produits', [
                'uuid' => generate_uuid(),
                'libelle' => $a['libelle'],
                'id_categorie' => $cat['id_categorie'],
                'prix_achat' => 0,
                'prix_unitaire' => $a['prix'],
                'stock_actuel' => 0, 'stock_mini' => 0,
                'unite' => 'pièce',
                'cree_le' => date('Y-m-d H:i:s'),
                'modifie_le' => date('Y-m-d H:i:s'),
            ]);
            $created++;
        }
        $this->json_success(['created' => $created], "$created article(s) créé(s)");
    }

    public function api_approvisionner() {
        $data = $this->get_json_input();
        $uuid = $data['uuid'] ?? null;
        $quantite = intval($data['quantite'] ?? 0);
        $prix_achat_lot = floatval($data['prix_achat'] ?? 0);
        $motif = trim($data['motif'] ?? 'Approvisionnement');
        if (!$uuid || $quantite <= 0) { $this->json_error('UUID produit et quantité requis'); return; }
        $produit = $this->Model->readOne('produits', ['uuid' => $uuid, 'deleted_at' => null]);
        if (!$produit) { $this->json_error('Produit introuvable'); return; }

        $stock_actuel = intval($produit['stock_actuel']);
        $prix_achat_actuel = floatval($produit['prix_achat']);
        $nouveau_stock = $stock_actuel + $quantite;
        $prix_achat_moyen = ($stock_actuel > 0)
            ? round(($stock_actuel * $prix_achat_actuel + $quantite * $prix_achat_lot) / $nouveau_stock, 2)
            : $prix_achat_lot;

        $this->db->trans_start();
        $this->Model->update('produits', ['uuid' => $uuid], [
            'stock_actuel' => $nouveau_stock,
            'prix_achat' => $prix_achat_moyen,
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        $this->load->helper('uuid');
        $this->Model->create('mouvements_stock', [
            'uuid' => generate_uuid(),
            'id_produit' => $produit['id_produit'],
            'type' => 'entree',
            'quantite' => $quantite,
            'prix_unitaire' => $prix_achat_lot ?: null,
            'motif' => $motif,
            'date_mvt' => date('Y-m-d H:i:s'),
            'id_utilisateur' => $this->session->userdata('id_utilisateur') ?? null,
            'cree_le' => date('Y-m-d H:i:s'),
        ]);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->json_error('Erreur lors de l\'approvisionnement');
        } else {
            $this->json_success(['stock_actuel' => $nouveau_stock], "Approvisionnement de $quantite effectué");
        }
    }
}
