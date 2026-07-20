<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Commandes extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des commandes';
        $data['etudiants'] = $this->Model->read('etudiants', ['deleted_at' => null]);
        $data['produits'] = $this->Model->read('produits', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('c.deleted_at', null);
        $this->db->select('c.*, e.nom, e.prenom, e.matricule');
        $this->db->from('commandes c');
        $this->db->join('etudiants e', 'c.id_etudiant = e.id_etudiant', 'left');
        $this->db->order_by('c.date_commande', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_get($id) {
        $row = $this->Model->readOne('commandes', ['uuid' => $id, 'deleted_at' => null]);
        if (!$row) { $this->json_error('Commande introuvable'); return; }
        $q_d = $this->db
            ->select('cd.*, p.libelle as produit_libelle, p.code as produit_code')
            ->from('commandes_details cd')
            ->join('produits p', 'cd.id_produit = p.id_produit', 'left')
            ->where('cd.id_commande', $row['id_commande'])
            ->get();
        $row['details'] = $q_d !== false ? $q_d->result_array() : array();
        $this->json_success($row);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant'])) { $this->json_error('Étudiant requis'); return; }
        if (empty($data['details']) || !is_array($data['details'])) { $this->json_error('Aucun produit sélectionné'); return; }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'id_etudiant' => $data['id_etudiant'],
            'date_commande' => !empty($data['date_commande']) ? $data['date_commande'] : date('Y-m-d'),
            'statut' => !empty($data['statut']) ? $data['statut'] : 'en_attente',
            'total' => 0,
        ];
        $id = $this->Model->createLastId('commandes', $insert);
        if (!$id) { $this->json_error('Erreur création'); return; }
        $total = 0;
        foreach ($data['details'] as $d) {
            if (empty($d['id_produit']) || empty($d['quantite'])) continue;
            $price = floatval($d['prix_unitaire'] ?? 0);
            $qty = intval($d['quantite']);
            $this->db->insert('commandes_details', [
                'uuid' => generate_uuid(),
                'id_commande' => $id,
                'id_produit' => $d['id_produit'],
                'quantite' => $qty,
                'prix_unitaire' => $price,
            ]);
            $produit = $this->Model->readOne('produits', ['id_produit' => $d['id_produit']]);
            if ($produit) {
                $nouveau_stock = max(0, intval($produit['stock_actuel']) - $qty);
                $this->Model->update('produits', ['id_produit' => $d['id_produit']], ['stock_actuel' => $nouveau_stock]);
                $this->Model->create('mouvements_stock', [
                    'id_produit' => $d['id_produit'],
                    'type' => 'sortie',
                    'quantite' => $qty,
                    'prix_unitaire' => $price,
                    'motif' => 'Commande #' . $id,
                    'id_utilisateur' => $this->session->userdata('id_utilisateur')
                ]);
            }
            $total += $qty * $price;
        }
        $this->db->where('id_commande', $id)->update('commandes', ['total' => $total]);
        $this->json_success(['id_commande' => $id], 'Commande créée');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $allowed = ['statut'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if ($this->Model->update('commandes', ['uuid' => $id], $update))
            $this->json_success(null, 'Commande mise à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('commandes', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Commande supprimée');
        else $this->json_error('Erreur');
    }
}
