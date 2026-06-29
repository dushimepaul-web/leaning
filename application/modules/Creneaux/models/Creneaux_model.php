<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Creneaux_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('deleted_at', null);
        $this->db->order_by('ordre');
        return $this->db->get('creneaux')->result_array();
    }

    public function get_by_id($id)
    {
        return $this->readOne('creneaux', ['uuid' => $id]);
    }

    public function create($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('creneaux', $data) ? ['success' => true, 'id' => $this->db->insert_id()] : ['success' => false];
    }

    public function update($id, $data)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('creneaux', $data);
    }

    public function delete($id)
    {
        return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}