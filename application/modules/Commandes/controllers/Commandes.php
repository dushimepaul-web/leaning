<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Commandes extends MY_Controller {
    public function __construct() { parent::__construct(); $this->not_logged_in(); }

    public function index() {
        $data['title'] = 'Gestion des commandes';
        $data['etudiants'] = $this->Model->read('etudiants', ['deleted_at' => null]);
        $data['produits'] = $this->Model->read('produits', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('c.deleted_at', null);
        $this->db->select("c.*, e.fullname AS nom, '' AS prenom, e.matricule, COALESCE((SELECT SUM((cd.prix_unitaire - cd.prix_achat) * cd.quantite) FROM commandes_details cd WHERE cd.id_commande = c.id_commande), 0) AS benefice");
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
            ->select('cd.*, p.libelle as produit_libelle')
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

        foreach ($data['details'] as $d) {
            if (empty($d['id_produit']) || empty($d['quantite'])) continue;
            $produit = $this->Model->readOne('produits', ['id_produit' => $d['id_produit']]);
            if (!$produit) { $this->json_error('Produit introuvable (#' . $d['id_produit'] . ')'); return; }
            $qty = intval($d['quantite']);
            if (intval($produit['stock_actuel']) < $qty) {
                $this->json_error('Stock insuffisant pour "' . $produit['libelle'] . '" (stock: ' . $produit['stock_actuel'] . ', demandé: ' . $qty . ')');
                return;
            }
        }

        $id_user = $this->session->userdata('id_utilisateur');
        $this->db->trans_start();
        $insert = [
            'uuid' => generate_uuid(),
            'id_etudiant' => $data['id_etudiant'],
            'date_commande' => !empty($data['date_commande']) ? $data['date_commande'] : date('Y-m-d'),
            'statut' => !empty($data['statut']) ? $data['statut'] : 'en_attente',
            'total' => 0,
        ];
        $id = $this->Model->createLastId('commandes', $insert);
        if (!$id) { $this->db->trans_rollback(); $this->json_error('Erreur création'); return; }
        $total = 0;
        foreach ($data['details'] as $d) {
            if (empty($d['id_produit']) || empty($d['quantite'])) continue;
            $produit = $this->Model->readOne('produits', ['id_produit' => $d['id_produit']]);
            if (!$produit) continue;
            $qty = intval($d['quantite']);
            $price = floatval($d['prix_unitaire'] ?? 0);
            $prix_achat = floatval($produit['prix_achat'] ?? 0);
            $this->db->insert('commandes_details', [
                'uuid' => generate_uuid(),
                'id_commande' => $id,
                'id_produit' => $d['id_produit'],
                'quantite' => $qty,
                'prix_unitaire' => $price,
                'prix_achat' => $prix_achat,
            ]);
            $nouveau_stock = intval($produit['stock_actuel']) - $qty;
            $this->Model->update('produits', ['id_produit' => $d['id_produit']], ['stock_actuel' => $nouveau_stock]);
            $this->Model->create('mouvements_stock', [
                'id_produit' => $d['id_produit'],
                'type' => 'sortie',
                'quantite' => $qty,
                'prix_unitaire' => $price,
                'motif' => 'Commande #' . $id,
                'id_utilisateur' => $id_user,
                'id_etudiant' => $data['id_etudiant'],
            ]);
            $total += $qty * $price;
        }
        $this->db->where('id_commande', $id)->update('commandes', ['total' => $total]);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->json_error('Erreur lors de la création de la commande');
        } else {
            $this->json_success(['id_commande' => $id], 'Commande créée');
        }
    }

    private function restore_stock($id_commande) {
        $details = $this->Model->read('commandes_details', ['id_commande' => $id_commande]);
        $id_user = $this->session->userdata('id_utilisateur');
        foreach ($details as $d) {
            $qty = intval($d['quantite']);
            $produit = $this->Model->readOne('produits', ['id_produit' => $d['id_produit']]);
            if ($produit) {
                $this->Model->update('produits', ['id_produit' => $d['id_produit']], [
                    'stock_actuel' => intval($produit['stock_actuel']) + $qty,
                ]);
            }
            $this->Model->create('mouvements_stock', [
                'id_produit' => $d['id_produit'],
                'type' => 'entree',
                'quantite' => $qty,
                'prix_unitaire' => $d['prix_achat'] ?: null,
                'motif' => 'Annulation commande #' . $id_commande,
                'id_utilisateur' => $id_user,
            ]);
        }
    }

    private function deduct_stock($id_commande) {
        $details = $this->Model->read('commandes_details', ['id_commande' => $id_commande]);
        $id_user = $this->session->userdata('id_utilisateur');
        foreach ($details as $d) {
            $qty = intval($d['quantite']);
            $produit = $this->Model->readOne('produits', ['id_produit' => $d['id_produit']]);
            if ($produit) {
                $this->Model->update('produits', ['id_produit' => $d['id_produit']], [
                    'stock_actuel' => max(0, intval($produit['stock_actuel']) - $qty),
                ]);
            }
            $this->Model->create('mouvements_stock', [
                'id_produit' => $d['id_produit'],
                'type' => 'sortie',
                'quantite' => $qty,
                'prix_unitaire' => $d['prix_unitaire'] ?? 0,
                'motif' => 'Réactivation commande #' . $id_commande,
                'id_utilisateur' => $id_user,
            ]);
        }
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $commande = $this->Model->readOne('commandes', ['uuid' => $id, 'deleted_at' => null]);
        if (!$commande) { $this->json_error('Commande introuvable'); return; }

        if (isset($data['statut'])) {
            $new = $data['statut'];
            $old = $commande['statut'];
            $this->db->trans_start();
            if ($new === 'annulee' && $old !== 'annulee') {
                $this->restore_stock($commande['id_commande']);
            } elseif ($old === 'annulee' && $new !== 'annulee') {
                $this->deduct_stock($commande['id_commande']);
            }
            $this->Model->update('commandes', ['uuid' => $id], ['statut' => $new, 'modifie_le' => date('Y-m-d H:i:s')]);
            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                $this->json_error('Erreur lors du changement de statut');
            } else {
                $this->json_success(null, 'Commande mise à jour');
            }
        } else {
            $this->json_error('Aucune donnée à modifier');
        }
    }

    public function api_delete($id) {
        $commande = $this->Model->readOne('commandes', ['uuid' => $id, 'deleted_at' => null]);
        if (!$commande) { $this->json_error('Commande introuvable'); return; }

        $this->db->trans_start();
        if ($commande['statut'] !== 'annulee') {
            $this->restore_stock($commande['id_commande']);
        }
        $this->Model->update('commandes', ['uuid' => $id], [
            'deleted_at' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->json_error('Erreur lors de la suppression');
        } else {
            $this->json_success(null, 'Commande supprimée');
        }
    }
}
