<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employes_model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = [])
    {
        $this->db->where('e.deleted_at', null);
        $this->db->select('e.*, d.libelle as departement_libelle, u.email, u.role_id');
        $this->db->from('employes e');
        $this->db->join('departements d', 'e.id_departement = d.id_departement', 'left');
        $this->db->join('utilisateurs u', 'e.id_utilisateur = u.id_utilisateur', 'left');
        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('e.nom', $filters['search'])
                ->or_like('e.prenom', $filters['search'])
                ->or_like('e.matricule', $filters['search'])
                ->group_end();
        }
        $this->db->order_by('e.nom', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->where('e.deleted_at', null);
        $this->db->where('e.uuid', $id);
        $this->db->select('e.*, d.libelle as departement_libelle, u.email');
        $this->db->from('employes e');
        $this->db->join('departements d', 'e.id_departement = d.id_departement', 'left');
        $this->db->join('utilisateurs u', 'e.id_utilisateur = u.id_utilisateur', 'left');
        return $this->db->get()->row_array();
    }

    public function create($data)
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
        if ($this->db->insert('employes', $data)) {
            return ['success' => true, 'id_employe' => $this->db->insert_id()];
        }
        return ['success' => false, 'message' => 'Erreur insertion'];
    }

    public function update($id, $data)
    {
        $this->db->where('uuid', $id);
        $data['modifie_le'] = date('Y-m-d H:i:s');
        return $this->db->update('employes', $data);
    }

    public function delete($id)
    {
        return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    // Contrats
    public function get_contrats($filters = [])
    {
        $this->db->where('c.deleted_at', null);
        $this->db->select('c.*, e.nom, e.prenom, e.matricule, r.libelle as rubrique_libelle');
        $this->db->from('paie_contrats c');
        $this->db->join('employes e', 'c.id_employe = e.id_employe', 'left');
        if (!empty($filters['id_employe'])) $this->db->where('c.id_employe', $filters['id_employe']);
        if (!empty($filters['statut'])) $this->db->where('c.statut', $filters['statut']);
        return $this->db->get()->result_array();
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

    // Rubriques
    public function get_rubriques($filters = [])
    {
        $this->db->where('deleted_at', null);
        if (!empty($filters['type'])) $this->db->where('type', $filters['type']);
        return $this->db->get('paie_rubriques')->result_array();
    }
}