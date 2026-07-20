<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Classes_model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Sections
    public function get_sections($filters = [])
    {
        $this->db->where('deleted_at', null);
        if (!empty($filters['id_annee'])) $this->db->where('id_annee', $filters['id_annee']);
        $this->db->order_by('libelle');
        $q = $this->db->get('sections');
        if ($q === false) return array();
        return $q->result_array();
    }

    public function get_matieres($filters = [])
    {
        $this->db->where('deleted_at', null);
        if (!empty($filters['id_classe'])) {
            $this->db->join('matieres_classes mc', 'm.id_matiere = mc.id_matiere AND mc.deleted_at IS NULL', 'inner');
            $this->db->where('mc.id_classe', $filters['id_classe']);
        }
        $this->db->order_by('libelle');
        $q = $this->db->get('matieres m');
        if ($q === false) return array();
        return $q->result_array();
    }

    // Classes
    public function get_classes($filters = [])
    {
        $this->db->where('c.deleted_at', null);
        $this->db->select('c.*, s.libelle as section_libelle');
        $this->db->from('classes c');
        $this->db->join('sections s', 'c.id_section = s.id_section', 'left');
        if (!empty($filters['id_section'])) $this->db->where('c.id_section', $filters['id_section']);
        $this->db->order_by('c.libelle');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    // Enseignements
    public function get_enseignements($filters = [])
    {
        $this->db->where('en.deleted_at', null);
        $this->db->select('en.*, e.nom, e.prenom, e.matricule, m.libelle as matiere_libelle, c.libelle as classe_libelle');
        $this->db->from('enseignements en');
        $this->db->join('enseignants e', 'en.id_enseignant = e.id_enseignant', 'left');
        $this->db->join('matieres m', 'en.id_matiere = m.id_matiere', 'left');
        $this->db->join('classes c', 'en.id_classe = c.id_classe', 'left');
        if (!empty($filters['id_classe'])) $this->db->where('en.id_classe', $filters['id_classe']);
        if (!empty($filters['id_enseignant'])) $this->db->where('en.id_enseignant', $filters['id_enseignant']);
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    // Periodes
    public function get_periodes($filters = [])
    {
        $this->db->where('p.deleted_at', null);
        $this->db->select('p.*, a.libelle as annee_libelle');
        $this->db->from('periodes p');
        $this->db->join('annees_scolaires a', 'p.id_annee = a.id_annee', 'left');
        if (!empty($filters['id_annee'])) $this->db->where('p.id_annee', $filters['id_annee']);
        $this->db->order_by('p.date_debut');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }
}