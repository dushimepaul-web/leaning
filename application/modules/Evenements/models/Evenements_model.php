<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Evenements_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('e.deleted_at', null);
        $this->db->order_by('e.date_debut', 'DESC');
        return $this->db->get('evenements e')->result_array();
    }

    public function create($data)
    {
        $data['uuid'] = generate_uuid();
        $data['cree_le'] = date('Y-m-d H:i:s');
        return $this->db->insert('evenements', $data) ? ['success' => true, 'id' => $this->db->insert_id()] : ['success' => false];
    }

    public function update($id, $data)
    {
        $this->db->where('uuid', $id);
        $data['modifie_le'] = date('Y-m-d H:i:s');
        return $this->db->update('evenements', $data);
    }

    public function delete($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('evenements', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}