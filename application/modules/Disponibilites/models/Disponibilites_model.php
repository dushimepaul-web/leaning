<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Disponibilites_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('d.deleted_at', null);
        $this->db->select('d.*, e.fullname, j.libelle as jour');
        $this->db->from('disponibilites_enseignants d');
        $this->db->join('enseignants e', 'd.id_enseignant = e.id_enseignant', 'left');
        $this->db->join('jours_semaine j', 'd.id_jour = j.id_jour', 'left');
        if (!empty($filters['id_enseignant'])) $this->db->where('d.id_enseignant', $filters['id_enseignant']);
        if (!empty($filters['type'])) $this->db->where('d.type', $filters['type']);
        $q = $this->db->get();
        if ($q === false) return array();
        $rows = $q->result_array();

        // Injecter le nom et les heures du créneau généré automatiquement
        $CI =& get_instance();
        $CI->load->model('Horaires/Horaires_model');
        $creneaux = $CI->Horaires_model->get_creneaux_cours();
        $creneauxMap = [];
        foreach ($creneaux as $cr) {
            $creneauxMap[$cr['id_creneau']] = $cr;
        }

        foreach ($rows as &$r) {
            $cid = $r['id_creneau'];
            if (isset($creneauxMap[$cid])) {
                $r['creneau'] = $creneauxMap[$cid]['libelle'] . ' (' . $creneauxMap[$cid]['heure_debut'] . ' - ' . $creneauxMap[$cid]['heure_fin'] . ')';
            } else {
                $r['creneau'] = 'Cours ' . $cid;
            }
        }
        return $rows;
    }

    public function create_record($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('disponibilites_enseignants', $data) ? ['success' => true] : ['success' => false];
    }

    public function delete_record($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('disponibilites_enseignants', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}