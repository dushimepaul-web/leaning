<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paie_rubrique_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('deleted_at', null);
        if (!empty($filters['type'])) $this->db->where('type', $filters['type']);
        return $this->db->get('paie_rubriques')->result_array();
    }

    public function create($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('paie_rubriques', $data) ? ['success' => true] : ['success' => false];
    }

    public function update($id, $data)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('paie_rubriques', $data);
    }

    public function delete($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('paie_rubriques', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}