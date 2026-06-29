<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Certificats_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('c.deleted_at', null);
        $this->db->select('c.*, e.nom, e.prenom, e.matricule');
        $this->db->from('certificats c');
        $this->db->join('etudiants e', 'c.id_etudiant = e.id_etudiant', 'left');
        if (!empty($filters['id_etudiant'])) $this->db->where('c.id_etudiant', $filters['id_etudiant']);
        if (!empty($filters['type'])) $this->db->where('c.type', $filters['type']);
        $this->db->order_by('c.date_delivrance', 'DESC');
        return $this->db->get()->result_array();
    }

    public function create($data)
    {
        $data['uuid'] = generate_uuid();
        $data['date_delivrance'] = $data['date_delivrance'] ?? date('Y-m-d');
        return $this->db->insert('certificats', $data) ? ['success' => true, 'id' => $this->db->insert_id()] : ['success' => false];
    }

    public function update($id, $data)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('certificats', $data);
    }

    public function delete($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('certificats', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}