<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paie_model extends Model
{
    public function __construct() { parent::__construct(); }

    // Contrats
    public function get_contrats($filters = [])
    {
        $this->db->where('c.deleted_at', null);
        $this->db->select('c.*, e.nom, e.prenom, e.matricule');
        $this->db->from('paie_contrats c');
        $this->db->join('employes e', 'c.id_employe = e.id_employe', 'left');
        if (!empty($filters['id_employe'])) $this->db->where('c.id_employe', $filters['id_employe']);
        if (!empty($filters['statut'])) $this->db->where('c.statut', $filters['statut']);
        return $this->db->get()->result_array();
    }

    public function create_contrat($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('paie_contrats', $data) ? ['success' => true] : ['success' => false];
    }

    // Bulletins
    public function get_bulletins($filters = [])
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

    public function create_bulletin($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('paie_bulletins', $data) ? ['success' => true] : ['success' => false];
    }

    // Rubriques
}