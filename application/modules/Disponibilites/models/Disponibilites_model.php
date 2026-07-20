<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Disponibilites_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('d.deleted_at', null);
        $this->db->select('d.*, e.nom, e.prenom, c.libelle as creneau, j.libelle as jour');
        $this->db->from('disponibilites_enseignants d');
        $this->db->join('enseignants e', 'd.id_enseignant = e.id_enseignant', 'left');
        $this->db->join('creneaux c', 'd.id_creneau = c.id_creneau', 'left');
        $this->db->join('jours_semaine j', 'd.id_jour = j.id_jour', 'left');
        if (!empty($filters['id_enseignant'])) $this->db->where('d.id_enseignant', $filters['id_enseignant']);
        if (!empty($filters['type'])) $this->db->where('d.type', $filters['type']);
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function create_record($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('disponibilites_enseignants', $data) ? ['success' => true] : ['success' => false];
    }

    public function delete_record($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('disponibilites_enseignants', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}