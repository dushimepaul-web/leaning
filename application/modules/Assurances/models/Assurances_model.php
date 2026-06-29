<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assurances_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('a.deleted_at', null);
        $this->db->select('a.*, e.nom, e.prenom, e.matricule');
        $this->db->from('assurances a');
        $this->db->join('etudiants e', 'a.id_etudiant = e.id_etudiant', 'left');
        if (!empty($filters['statut'])) $this->db->where('a.statut', $filters['statut']);
        $this->db->order_by('a.date_debut', 'DESC');
        return $this->db->get()->result_array();
    }

    public function create($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('assurances', $data) ? ['success' => true] : ['success' => false];
    }

    public function update($id, $data)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('assurances', $data);
    }

    public function delete($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('assurances', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}