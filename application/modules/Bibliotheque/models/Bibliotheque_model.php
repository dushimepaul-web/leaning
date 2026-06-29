<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bibliotheque_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('b.deleted_at', null);
        $this->db->select('b.*, l.titre as livre_titre, m.libelle as membre_nom, m.prenom as membre_prenom');
        $this->db->from('bibliotheque_emprunts b');
        $this->db->join('bibliotheque_livres l', 'b.id_livre = l.id_livre', 'left');
        $this->db->join('etudiants e', 'b.id_membre = e.id_etudiant AND b.type_membre = "etudiant"', 'left');
        $this->db->join('enseignants en', 'b.id_membre = en.id_enseignant AND b.type_membre = "enseignant"', 'left');
        if (!empty($filters['statut'])) $this->db->where('b.statut', $filters['statut']);
        return $this->db->get()->result_array();
    }

    public function create($data)
    {
        $data['uuid'] = generate_uuid();
        $data['date_emprunt'] = $data['date_emprunt'] ?? date('Y-m-d');
        $data['date_retour_prevue'] = $data['date_retour_prevue'] ?? date('Y-m-d', strtotime('+14 days'));
        $data['statut'] = 'en_cours';
        return $this->db->insert('bibliotheque_emprunts', $data);
    }

    public function retourner($id, $data = [])
    {
        $data['statut'] = 'retourne';
        $data['date_retour_effectif'] = date('Y-m-d');
        $this->db->where('uuid', $id);
        return $this->db->update('bibliotheque_emprunts', $data);
    }
}