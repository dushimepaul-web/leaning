<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parents_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_mes_enfants($id_utilisateur)
    {
        $this->db->where('e.deleted_at', null);
        $this->db->select('e.*, i.id_classe, i.id_section, c.libelle as classe, s.libelle as section');
        $this->db->from('etudiants e');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL');
        $this->db->join('classes c', 'i.id_classe = c.id_classe', 'left');
        $this->db->join('sections s', 'i.id_section = s.id_section', 'left');
        $this->db->where('e.id_utilisateur', $id_utilisateur);
        return $this->db->get()->result_array();
    }
}