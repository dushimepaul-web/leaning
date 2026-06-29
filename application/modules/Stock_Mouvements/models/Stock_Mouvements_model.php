<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Mouvements_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('m.deleted_at', null);
        $this->db->select('m.*, p.libelle as produit_libelle, p.code as produit_code');
        $this->db->from('mouvements_stock m');
        $this->db->join('produits p', 'm.id_produit = p.id_produit', 'left');
        if (!empty($filters['id_produit'])) $this->db->where('m.id_produit', $filters['id_produit']);
        if (!empty($filters['type'])) $this->db->where('m.type', $filters['type']);
        $this->db->order_by('m.date_mouvement', 'DESC');
        return $this->db->get()->result_array();
    }

    public function create_record($data)
    {
        $data['uuid'] = generate_uuid();
        $data['date_mouvement'] = $data['date_mouvement'] ?? date('Y-m-d H:i:s');
        if ($this->db->insert('mouvements_stock', $data)) {
            $this->update_stock($data['id_produit'], $data['type'], $data['quantite']);
            return ['success' => true];
        }
        return ['success' => false];
    }

    private function update_stock($id_produit, $type, $quantite)
    {
        $produit = $this->readOne('produits', ['id_produit' => $id_produit]);
        if ($produit) {
            $new_stock = ($produit['stock_actuel'] ?? 0) + ($type === 'entree' ? $quantite : -$quantite);
            $this->update('produits', ['id_produit' => $id_produit], ['stock_actuel' => max(0, $new_stock)]);
        }
    }
}