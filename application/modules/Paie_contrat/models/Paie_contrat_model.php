<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paie_contrat_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('c.deleted_at', null);
        $this->db->select('c.*, e.nom, e.prenom, e.matricule');
        $this->db->from('paie_contrats c');
        $this->db->join('employes e', 'c.id_employe = e.id_employe', 'left');
        if (!empty($filters['id_employe'])) $this->db->where('c.id_employe', $filters['id_employe']);
        return $this->db->get()->result_array();
    }

    public function create($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('paie_contrats', $data) ? ['success' => true] : ['success' => false];
    }

    public function update($id, $data)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('paie_contrats', $data);
    }

    public function delete($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('paie_contrats', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}