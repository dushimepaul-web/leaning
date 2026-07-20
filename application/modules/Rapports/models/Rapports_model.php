<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rapports_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function rapport_etudiants_par_classe()
    {
        $this->db->select('c.libelle as classe, COUNT(e.id_etudiant) as total');
        $this->db->from('etudiants e');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL');
        $this->db->join('classes c', 'i.id_classe = c.id_classe', 'left');
        $this->db->where('e.deleted_at', null);
        $this->db->group_by('c.id_classe');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function rapport_paiements_par_mois($id_annee = null)
    {
        $id_annee = $id_annee ?? $this->id_annee_active;
        $this->db->select("MONTH(date_paiement) as mois, SUM(montant) as total, COUNT(*) as nb");
        $this->db->from('paiements');
        $this->db->where('id_annee', $id_annee);
        $this->db->where('deleted_at', null);
        $this->db->group_by('MONTH(date_paiement)');
        $this->db->order_by('mois');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function rapport_absences_par_classe($id_annee = null)
    {
        $this->db->select('c.libelle as classe, COUNT(a.id_absence) as total');
        $this->db->from('absences a');
        $this->db->join('etudiants e', 'a.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL', 'left');
        $this->db->join('classes c', 'i.id_classe = c.id_classe', 'left');
        $this->db->where('a.deleted_at', null);
        if ($id_annee) {
            $this->db->where('i.id_annee', $id_annee);
        }
        $this->db->group_by('c.id_classe');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function rapport_notes_par_matiere($id_classe, $id_periode)
    {
        $this->db->select('m.libelle as matiere, AVG((n.note/ev.note_max)*20) as moyenne');
        $this->db->from('notes n');
        $this->db->join('evaluations ev', 'n.id_evaluation = ev.id_evaluation');
        $this->db->join('matieres m', 'ev.id_matiere = m.id_matiere');
        $this->db->where('n.deleted_at', null);
        $this->db->where('ev.id_classe', $id_classe);
        $this->db->where('ev.id_periode', $id_periode);
        $this->db->where('ev.deleted_at', null);
        $this->db->group_by('ev.id_matiere');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }
}