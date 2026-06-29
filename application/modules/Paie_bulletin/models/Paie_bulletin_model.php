<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paie_bulletin_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('b.deleted_at', null);
        $this->db->select('b.*, c.id_employe, e.nom, e.prenom, e.matricule');
        $this->db->from('paie_bulletins b');
        $this->db->join('paie_contrats c', 'b.id_contrat = c.id_contrat', 'left');
        $this->db->join('employes e', 'c.id_employe = e.id_employe', 'left');
        if (!empty($filters['id_contrat'])) $this->db->where('b.id_contrat', $filters['id_contrat']);
        if (!empty($filters['mois'])) $this->db->where('b.mois', $filters['mois']);
        if (!empty($filters['annee'])) $this->db->where('b.annee', $filters['annee']);
        return $this->db->get()->result_array();
    }

    public function create($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('paie_bulletins', $data) ? ['success' => true] : ['success' => false];
    }

    public function update($id, $data)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('paie_bulletins', $data);
    }

    public function delete($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('paie_bulletins', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}