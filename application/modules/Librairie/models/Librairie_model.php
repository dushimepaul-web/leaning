<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Librairie_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('p.deleted_at', null);
        $this->db->select('p.*, cp.libelle as categorie');
        $this->db->from('produits p');
        $this->db->join('categories_produits cp', 'p.id_categorie = cp.id_categorie');
        if (!empty($filters['categorie'])) $this->db->where('cp.code', strtoupper($filters['categorie']));
        $this->db->order_by('cp.libelle', 'ASC')->order_by('p.libelle', 'ASC');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function create_record($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('produits', $data) ? ['success' => true, 'id' => $this->db->insert_id()] : ['success' => false];
    }

    public function update_record($id, $data)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('produits', $data);
    }

    public function delete_record($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('produits', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}
