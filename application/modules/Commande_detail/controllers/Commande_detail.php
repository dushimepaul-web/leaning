<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Commande_detail extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Détails des commandes';
        $data['commandes'] = $this->Model->read('commandes', ['deleted_at' => null]);
        $data['produits'] = $this->Model->read('produits', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->select('d.*, c.date_commande, c.statut as commande_statut, p.libelle as produit_libelle, p.code as produit_code');
        $this->db->from('commandes_details d');
        $this->db->join('commandes c', 'd.id_commande = c.id_commande', 'left');
        $this->db->join('produits p', 'd.id_produit = p.id_produit', 'left');
        $this->db->order_by('d.id_detail', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_get($id) {
        $this->db->where('d.uuid', $id);
        $this->db->select('d.*, c.date_commande, p.libelle as produit_libelle, p.code as produit_code');
        $this->db->from('commandes_details d');
        $this->db->join('commandes c', 'd.id_commande = c.id_commande', 'left');
        $this->db->join('produits p', 'd.id_produit = p.id_produit', 'left');
        $q = $this->db->get();
        $d = $q !== false ? $q->row_array() : null;
        if (!$d) { $this->json_error('Détail non trouvé', 404); return; }
        $this->json_success($d);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_commande']) || empty($data['id_produit']) || empty($data['quantite'])) {
            $this->json_error('Commande, produit et quantité obligatoires'); return;
        }
        $id = $this->Model->createLastId('commandes_details', $data);
        if ($id) {
            $detail = $this->Model->readOne('commandes_details', ['id_detail' => $id]);
            $total = $this->db->select_sum('(quantite * prix_unitaire)', 'total')->where('id_commande', $data['id_commande'])->get('commandes_details')->row()->total ?? 0;
            $this->Model->update('commandes', ['id_commande' => $data['id_commande']], ['total' => $total]);
            $this->json_success(['id_detail' => $id], 'Détail ajouté');
        } else $this->json_error('Erreur de création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $old = $this->Model->readOne('commandes_details', ['uuid' => $id]);
        if ($this->Model->update('commandes_details', ['uuid' => $id], $data)) {
            if ($old) {
                $total = $this->db->select_sum('(quantite * prix_unitaire)', 'total')->where('id_commande', $old['id_commande'])->get('commandes_details')->row()->total ?? 0;
                $this->Model->update('commandes', ['id_commande' => $old['id_commande']], ['total' => $total]);
            }
            $this->json_success(null, 'Détail mis à jour');
        } else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        $d = $this->Model->readOne('commandes_details', ['uuid' => $id]);
        if (!$d) { $this->json_error('Détail non trouvé', 404); return; }
        if ($this->Model->delete('commandes_details', ['uuid' => $id])) {
            $total = $this->db->select_sum('(quantite * prix_unitaire)', 'total')->where('id_commande', $d['id_commande'])->get('commandes_details')->row()->total ?? 0;
            $this->Model->update('commandes', ['id_commande' => $d['id_commande']], ['total' => $total]);
            $this->json_success(null, 'Détail supprimé');
        } else $this->json_error('Erreur de suppression');
    }
}
