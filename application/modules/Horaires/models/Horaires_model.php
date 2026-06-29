<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Horaires_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('h.deleted_at', null);
        $this->db->select('h.*, j.libelle as jour, c.libelle as creneau, c.heure_debut, c.heure_fin, cl.libelle as classe, CONCAT(e.nom, " ", e.prenom) as enseignant, m.libelle as matiere');
        $this->db->from('horaires h');
        $this->db->join('jours_semaine j', 'h.id_jour = j.id_jour', 'left');
        $this->db->join('creneaux c', 'h.id_creneau = c.id_creneau', 'left');
        $this->db->join('classes cl', 'h.id_classe = cl.id_classe', 'left');
        $this->db->join('enseignants e', 'h.id_enseignant = e.id_enseignant', 'left');
        $this->db->join('enseignements en', 'h.id_enseignement = en.id_enseignement', 'left');
        $this->db->join('matieres m', 'en.id_matiere = m.id_matiere', 'left');
        if (!empty($filters['id_classe'])) $this->db->where('h.id_classe', $filters['id_classe']);
        if (!empty($filters['id_generation'])) $this->db->where('h.id_generation', $filters['id_generation']);
        $this->db->order_by('h.id_jour, h.id_creneau');
        return $this->db->get()->result_array();
    }

    public function get_matieres_by_classe($id_classe)
    {
        $this->db->select('m.id_matiere, m.libelle, m.code');
        $this->db->from('matieres m');
        $this->db->join('matieres_classes mc', 'm.id_matiere = mc.id_matiere AND mc.deleted_at IS NULL', 'inner');
        $this->db->where('mc.id_classe', $id_classe);
        $this->db->where('m.deleted_at', null);
        $this->db->order_by('m.libelle');
        return $this->db->get()->result_array();
    }

    public function get_enseignant_by_classe_matiere($id_classe, $id_matiere)
    {
        $this->db->select('e.id_enseignant, e.nom, e.prenom, e.matricule');
        $this->db->from('enseignements en');
        $this->db->join('enseignants e', 'en.id_enseignant = e.id_enseignant');
        $this->db->where('en.id_classe', $id_classe);
        $this->db->where('en.id_matiere', $id_matiere);
        $this->db->where('en.deleted_at', null);
        $this->db->where('e.deleted_at', null);
        return $this->db->get()->row_array();
    }
}