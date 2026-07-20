<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enseignants_model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = [])
    {
        $this->db->where('e.deleted_at', null);
        $this->db->select('e.*, d.libelle as departement_libelle');
        $this->db->from('enseignants e');
        $this->db->join('departements d', 'e.id_departement = d.id_departement', 'left');
        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('e.nom', $filters['search'])
                ->or_like('e.prenom', $filters['search'])
                ->or_like('e.matricule', $filters['search'])
                ->group_end();
        }
        $this->db->order_by('e.nom', 'ASC');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->where('e.deleted_at', null);
        $this->db->where('e.uuid', $id);
        $this->db->select('e.*, d.libelle as departement_libelle, u.email');
        $this->db->from('enseignants e');
        $this->db->join('departements d', 'e.id_departement = d.id_departement', 'left');
        $this->db->join('utilisateurs u', 'e.id_utilisateur = u.id_utilisateur', 'left');
        $q = $this->db->get();
        if ($q === false) return null;
        return $q->row_array();
    }

    public function create_record($data)
    {
        $required = ['nom', 'prenom', 'email', 'id_departement'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Champ requis manquant: $field"];
            }
        }
        $data['uuid'] = generate_uuid();
        $data['cree_le'] = date('Y-m-d H:i:s');
        $data['modifie_le'] = date('Y-m-d H:i:s');
        if ($this->db->insert('enseignants', $data)) {
            return ['success' => true, 'id_enseignant' => $this->db->insert_id()];
        }
        return ['success' => false, 'message' => 'Erreur insertion'];
    }

    public function update_record($id, $data)
    {
        $this->db->where('uuid', $id);
        $data['modifie_le'] = date('Y-m-d H:i:s');
        return $this->db->update('enseignants', $data);
    }

    public function delete_record($id)
    {
        return $this->update_record($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    // Programmes
    public function get_programmes($filters = [])
    {
        $this->db->where('p.deleted_at', null);
        $this->db->select('p.*, m.libelle as matiere, c.libelle as classe, e.nom, e.prenom');
        $this->db->from('enseignements p');
        $this->db->join('matieres m', 'p.id_matiere = m.id_matiere', 'left');
        $this->db->join('classes c', 'p.id_classe = c.id_classe', 'left');
        $this->db->join('enseignants e', 'p.id_enseignant = e.id_enseignant', 'left');
        if (!empty($filters['id_classe'])) $this->db->where('p.id_classe', $filters['id_classe']);
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }
}