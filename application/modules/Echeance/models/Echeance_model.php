<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Echeance_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('e.deleted_at', null);
        $this->db->select('e.*, f.montant as frais_montant, tf.libelle as type_frais, et.nom, et.prenom, et.matricule');
        $this->db->from('echeances e');
        $this->db->join('frais f', 'e.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->join('etudiants et', 'e.id_etudiant = et.id_etudiant', 'left');
        if (!empty($filters['id_etudiant'])) $this->db->where('e.id_etudiant', $filters['id_etudiant']);
        if (!empty($filters['id_frais'])) $this->db->where('e.id_frais', $filters['id_frais']);
        if (!empty($filters['statut'])) $this->db->where('e.statut', $filters['statut']);
        $this->db->order_by('e.date_echeance', 'ASC');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function create_record($data)
    {
        $data['uuid'] = generate_uuid();
        $data['cree_le'] = date('Y-m-d H:i:s');
        return $this->db->insert('echeances', $data) ? ['success' => true, 'id' => $this->db->insert_id()] : ['success' => false];
    }

    public function update_record($id, $data)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('echeances', $data);
    }

    public function delete_record($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('echeances', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}