<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absences_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('a.deleted_at', null);
        $this->db->select('a.*, e.nom, e.prenom, e.matricule, en.nom as ens_nom, en.prenom as ens_prenom');
        $this->db->from('absences a');
        $this->db->join('etudiants e', 'a.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('enseignants en', 'a.id_enseignant = en.id_enseignant', 'left');
        if (!empty($filters['id_etudiant'])) $this->db->where('a.id_etudiant', $filters['id_etudiant']);
        if (!empty($filters['date_from'])) $this->db->where('a.date_absence >=', $filters['date_from']);
        if (!empty($filters['date_to'])) $this->db->where('a.date_absence <=', $filters['date_to']);
        $this->db->order_by('a.date_absence', 'DESC');
        return $this->db->get()->result_array();
    }

    public function create($data)
    {
        $required = ['id_etudiant', 'date_absence'];
        foreach ($required as $field) {
            if (empty($data[$field])) return ['success' => false, 'message' => "Champ requis: $field"];
        }
        $data['uuid'] = generate_uuid();
        $data['cree_le'] = date('Y-m-d H:i:s');
        $data['modifie_le'] = date('Y-m-d H:i:s');
        return $this->db->insert('absences', $data) ? ['success' => true, 'id' => $this->db->insert_id()] : ['success' => false];
    }

    public function delete($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('absences', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}