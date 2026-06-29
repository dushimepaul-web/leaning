<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Commandes_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('c.deleted_at', null);
        $this->db->select('c.*, e.nom, e.prenom, e.matricule');
        $this->db->from('commandes c');
        $this->db->join('etudiants e', 'c.id_etudiant = e.id_etudiant', 'left');
        if (!empty($filters['id_etudiant'])) $this->db->where('c.id_etudiant', $filters['id_etudiant']);
        if (!empty($filters['statut'])) $this->db->where('c.statut', $filters['statut']);
        $this->db->order_by('c.date_commande', 'DESC');
        return $this->db->get()->result_array();
    }

    public function create_record($data)
    {
        $data['uuid'] = generate_uuid();
        $data['date_commande'] = $data['date_commande'] ?? date('Y-m-d');
        $data['statut'] = $data['statut'] ?? 'en_attente';
        return $this->db->insert('commandes', $data) ? ['success' => true, 'id' => $this->db->insert_id()] : ['success' => false];
    }

    public function update_record($id, $data)
    {
        $this->db->where('uuid', $id);
        $data['modifie_le'] = date('Y-m-d H:i:s');
        return $this->db->update('commandes', $data);
    }

    public function delete_record($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('commandes', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}