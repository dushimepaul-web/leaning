<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Toilettes_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('t.deleted_at', null);
        return $this->db->get('toilettes t')->result_array();
    }

    public function create($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('toilettes', $data) ? ['success' => true] : ['success' => false];
    }

    public function update($id, $data)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('toilettes', $data);
    }

    public function delete($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('toilettes', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}