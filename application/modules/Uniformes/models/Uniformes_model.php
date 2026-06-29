<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Uniformes_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('u.deleted_at', null);
        $this->db->select('u.*, e.nom, e.prenom, e.matricule');
        $this->db->from('uniformes u');
        $this->db->join('etudiants e', 'u.id_etudiant = e.id_etudiant', 'left');
        if (!empty($filters['id_etudiant'])) $this->db->where('u.id_etudiant', $filters['id_etudiant']);
        $this->db->order_by('u.date_commande', 'DESC');
        return $this->db->get()->result_array();
    }

    public function create_record($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('uniformes', $data) ? ['success' => true] : ['success' => false];
    }

    public function update_record($id, $data)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('uniformes', $data);
    }

    public function delete_record($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('uniformes', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}