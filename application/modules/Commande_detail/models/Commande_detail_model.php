<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Commande_detail_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('cd.deleted_at', null);
        $this->db->select('cd.*, c.libelle as commande_ref, p.libelle as produit');
        $this->db->from('commandes_details cd');
        $this->db->join('commandes c', 'cd.id_commande = c.id_commande', 'left');
        $this->db->join('produits p', 'cd.id_produit = p.id_produit', 'left');
        if (!empty($filters['id_commande'])) $this->db->where('cd.id_commande', $filters['id_commande']);
        return $this->db->get()->result_array();
    }

    public function create($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('commandes_details', $data) ? ['success' => true] : ['success' => false];
    }
}