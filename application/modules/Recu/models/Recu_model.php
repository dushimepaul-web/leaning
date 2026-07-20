<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recu_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('r.deleted_at', null);
        $this->db->select('r.*, e.nom, e.prenom, e.matricule, a.libelle as annee');
        $this->db->from('recus r');
        $this->db->join('etudiants e', 'r.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('annees_scolaires a', 'r.id_annee = a.id_annee', 'left');
        if (!empty($filters['id_etudiant'])) $this->db->where('r.id_etudiant', $filters['id_etudiant']);
        $this->db->order_by('r.id_recu', 'DESC');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->where('r.deleted_at', null);
        $this->db->where('r.uuid', $id);
        $this->db->select('r.*, e.nom, e.prenom, e.matricule, a.libelle as annee, u.nom as user_nom, u.prenom as user_prenom');
        $this->db->from('recus r');
        $this->db->join('etudiants e', 'r.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('annees_scolaires a', 'r.id_annee = a.id_annee', 'left');
        $this->db->join('utilisateurs u', 'r.id_utilisateur = u.id_utilisateur', 'left');
        $q = $this->db->get();
        if ($q === false) return null;
        return $q->row_array();
    }

    public function create_record($data)
    {
        $data['uuid'] = generate_uuid();
        $data['date_edition'] = date('Y-m-d H:i:s');
        return $this->db->insert('recus', $data) ? ['success' => true, 'id' => $this->db->insert_id()] : ['success' => false];
    }

    public function update_record($id, $data)
    {
        $this->db->where('uuid', $id);
        $data['modifie_le'] = date('Y-m-d H:i:s');
        return $this->db->update('recus', $data);
    }

    public function delete_record($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('recus', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}