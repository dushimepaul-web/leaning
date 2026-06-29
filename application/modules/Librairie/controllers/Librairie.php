<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Librairie extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Librairie';
        $data['categories'] = $this->Model->read('categories_produits', ['deleted_at' => null]);
        $data['matieres'] = $this->Model->read('matieres', ['deleted_at' => null], 'libelle');
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null], 'libelle');
        $data['etudiants'] = $this->Model->read('etudiants', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $categorie = $this->input->get('categorie');
        $this->db->where('p.deleted_at', null);
        $this->db->select('p.*, cp.libelle as categorie, cp.code as code_categorie');
        $this->db->from('produits p');
        $this->db->join('categories_produits cp', 'p.id_categorie = cp.id_categorie');
        if ($categorie) $this->db->where('cp.code', strtoupper($categorie));
        $this->db->order_by('cp.libelle', 'ASC')->order_by('p.libelle', 'ASC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_get($id) {
        $p = $this->db
            ->select('p.*, cp.libelle as categorie, cp.code as code_categorie')
            ->from('produits p')
            ->join('categories_produits cp', 'p.id_categorie = cp.id_categorie')
            ->where('p.uuid', $id)->where('p.deleted_at', null)
            ->get()->row_array();
        if (!$p) { $this->json_error('Produit non trouvé', 404); return; }
        $p['mouvements'] = $this->Model->read('mouvements_stock', ['id_produit' => $p['id_produit']], 'date_mvt DESC');
        $this->json_success($p);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle'])) { $this->json_error('Libellé obligatoire'); return; }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'id_categorie' => $data['id_categorie'] ?? null,
            'code' => $data['code'] ?? null,
            'libelle' => $data['libelle'],
            'description' => $data['description'] ?? null,
            'taille' => $data['taille'] ?? null,
            'editeur' => $data['editeur'] ?? null,
            'annee_edition' => $data['annee_edition'] ?? null,
            'id_matiere' => $data['id_matiere'] ?? null,
            'id_classe' => $data['id_classe'] ?? null,
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
        $allowed = ['id_categorie','code','libelle','description','taille','editeur','annee_edition','id_matiere','id_classe','prix_unitaire','stock_mini','stock_actuel','unite'];
        if (isset($data['stock'])) $data['stock_actuel'] = $data['stock'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        $update['modifie_le'] = date('Y-m-d H:i:s');
        if ($this->Model->update('produits', ['uuid' => $id], $update))
            $this->json_success(null, 'Produit mis à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('produits', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s'), 'modifie_le' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Produit supprimé');
        else $this->json_error('Erreur de suppression');
    }

    public function api_initialiser() {
        $cat = $this->Model->readOne('categories_produits', ['code' => 'MATERIEL']);
        if (!$cat) { $this->json_error('Catégorie MATERIEL introuvable'); return; }
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
            $exists = $this->Model->readOne('produits', ['code' => $a['code'], 'id_categorie' => $cat['id_categorie'], 'deleted_at' => null]);
            if ($exists) continue;
            $this->Model->create('produits', [
                'uuid' => generate_uuid(),
                'code' => $a['code'],
                'libelle' => $a['libelle'],
                'id_categorie' => $cat['id_categorie'],
                'prix_unitaire' => $a['prix_unitaire'],
                'stock_actuel' => 0, 'stock_mini' => 0,
                'unite' => 'pièce',
                'cree_le' => date('Y-m-d H:i:s'),
                'modifie_le' => date('Y-m-d H:i:s'),
            ]);
            $created++;
        }
        $this->json_success(['created' => $created], "$created article(s) créé(s)");
    }
}
