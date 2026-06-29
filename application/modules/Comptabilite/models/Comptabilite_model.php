<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comptabilite_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_plans($filters = [])
    {
        $this->db->where('deleted_at', null);
        return $this->db->get('comptabilite_plans')->result_array();
    }

    public function get_ecritures($filters = [])
    {
        $this->db->where('e.deleted_at', null);
        $this->db->select('e.*, p.code as plan_code, p.libelle as plan_libelle');
        $this->db->from('comptabilite_ecritures e');
        $this->db->join('comptabilite_plans p', 'e.id_plan = p.id_plan', 'left');
        if (!empty($filters['id_plan'])) $this->db->where('e.id_plan', $filters['id_plan']);
        $this->db->order_by('e.date_ecriture', 'DESC');
        return $this->db->get()->result_array();
    }

    public function create_plan($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('comptabilite_plans', $data) ? ['success' => true] : ['success' => false];
    }

    public function create_ecriture($data)
    {
        $data['uuid'] = generate_uuid();
        $data['date_ecriture'] = $data['date_ecriture'] ?? date('Y-m-d');
        return $this->db->insert('comptabilite_ecritures', $data) ? ['success' => true] : ['success' => false];
    }
}