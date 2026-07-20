<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Mouvements extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Mouvements de Stock';
        $data['produits'] = $this->Model->read('produits', ['deleted_at' => null]);
        $data['etudiants'] = $this->Model->read('etudiants', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->select('m.*, p.libelle as produit_libelle, p.code as produit_code, u.nom_complet as utilisateur, e.nom as etudiant_nom, e.prenom as etudiant_prenom');
        $this->db->from('mouvements_stock m');
        $this->db->join('produits p', 'm.id_produit = p.id_produit', 'left');
        $this->db->join('utilisateurs u', 'm.id_utilisateur = u.id_utilisateur', 'left');
        $this->db->join('etudiants e', 'm.id_etudiant = e.id_etudiant', 'left');
        $this->db->order_by('m.date_mvt', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_produit'])) { $this->json_error('Produit obligatoire'); return; }
        if (empty($data['type']) || !in_array($data['type'], ['entree', 'sortie'])) { $this->json_error('Type invalide'); return; }
        if (empty($data['quantite']) || intval($data['quantite']) <= 0) { $this->json_error('Quantité invalide'); return; }

        $produit = $this->Model->readOne('produits', ['id_produit' => $data['id_produit']]);
        if (!$produit) { $this->json_error('Produit non trouvé', 404); return; }

        $qty = intval($data['quantite']);
        $prix = floatval($data['prix_unitaire'] ?? 0);

        $mouvement = [
            'id_produit' => $data['id_produit'],
            'type' => $data['type'],
            'quantite' => $qty,
            'prix_unitaire' => $prix,
            'motif' => $data['motif'] ?? '',
            'id_utilisateur' => $this->session->userdata('id_utilisateur'),
            'id_etudiant' => !empty($data['id_etudiant']) ? intval($data['id_etudiant']) : null
        ];

        if ($data['type'] === 'entree') {
            $nouveau_stock = intval($produit['stock_actuel']) + $qty;
            $this->Model->update('produits', ['id_produit' => $data['id_produit']], ['stock_actuel' => $nouveau_stock]);
        } else {
            $nouveau_stock = max(0, intval($produit['stock_actuel']) - $qty);
            $this->Model->update('produits', ['id_produit' => $data['id_produit']], ['stock_actuel' => $nouveau_stock]);
        }

        $id = $this->Model->create('mouvements_stock', $mouvement);
        if ($id) {
            $this->json_success(['id_mouvement' => $id], 'Mouvement enregistré');
        } else {
            $this->json_error('Erreur lors de l\'enregistrement');
        }
    }

    public function api_batch() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant'])) { $this->json_error('Étudiant obligatoire'); return; }
        if (empty($data['produits']) || !is_array($data['produits'])) { $this->json_error('Aucun produit sélectionné'); return; }

        $id_etudiant = intval($data['id_etudiant']);
        $id_user = $this->session->userdata('id_utilisateur');
        $created = 0;
        $total = 0;

        foreach ($data['produits'] as $item) {
            if (empty($item['id_produit']) || intval($item['quantite']) <= 0) continue;
            $produit = $this->Model->readOne('produits', ['id_produit' => $item['id_produit']]);
            if (!$produit) continue;

            $qty = intval($item['quantite']);
            $prix = floatval($item['prix_unitaire'] ?? $produit['prix_unitaire'] ?? 0);
            $nouveau_stock = max(0, intval($produit['stock_actuel']) - $qty);

            $this->Model->create('mouvements_stock', [
                'id_produit' => $item['id_produit'],
                'type' => 'sortie',
                'quantite' => $qty,
                'prix_unitaire' => $prix,
                'motif' => 'Vente à ' . ($data['nom_etudiant'] ?? 'élève'),
                'id_utilisateur' => $id_user,
                'id_etudiant' => $id_etudiant
            ]);
            $this->Model->update('produits', ['id_produit' => $item['id_produit']], ['stock_actuel' => $nouveau_stock]);
            $total += $qty * $prix;
            $created++;
        }

        $this->json_success([
            'created' => $created,
            'total' => number_format($total, 2)
        ], "$created produit(s) vendu(s) — Total: " . number_format($total, 2) . " FCFA");
    }
}
