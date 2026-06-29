<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paie_bulletin_detail_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('bd.deleted_at', null);
        $this->db->select('bd.*, r.libelle as rubrique, r.type as rubrique_type');
        $this->db->from('paie_bulletins_details bd');
        $this->db->join('paie_rubriques r', 'bd.id_rubrique = r.id_rubrique', 'left');
        if (!empty($filters['id_bulletin'])) $this->db->where('bd.id_bulletin', $filters['id_bulletin']);
        return $this->db->get()->result_array();
    }

    public function create($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('paie_bulletins_details', $data) ? ['success' => true] : ['success' => false];
    }

    public function delete($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('paie_bulletins_details', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}