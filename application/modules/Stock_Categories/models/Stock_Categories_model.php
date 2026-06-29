<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Categories_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('deleted_at', null);
        return $this->db->get('categories_produits')->result_array();
    }

    public function create($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('categories_produits', $data) ? ['success' => true] : ['success' => false];
    }

    public function update($id, $data)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('categories_produits', $data);
    }

    public function delete($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('categories_produits', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}