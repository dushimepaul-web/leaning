<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paiement_recu_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('deleted_at', null);
        return $this->db->get('paiements_recus')->result_array();
    }

    public function create_record($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('paiements_recus', $data) ? ['success' => true] : ['success' => false];
    }
}