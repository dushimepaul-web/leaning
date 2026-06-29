<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Type_frais_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('deleted_at', null);
        $this->db->order_by('libelle');
        return $this->db->get('types_frais')->result_array();
    }

    public function create_record($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('types_frais', $data) ? ['success' => true] : ['success' => false];
    }

    public function update_record($id, $data)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('types_frais', $data);
    }

    public function delete_record($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('types_frais', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}