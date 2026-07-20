<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Evaluations_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('ev.deleted_at', null);
        $this->db->select('ev.*, m.libelle as matiere, c.libelle as classe, p.libelle as periode');
        $this->db->from('evaluations ev');
        $this->db->join('matieres m', 'ev.id_matiere = m.id_matiere', 'left');
        $this->db->join('classes c', 'ev.id_classe = c.id_classe', 'left');
        $this->db->join('periodes p', 'ev.id_periode = p.id_periode', 'left');
        if (!empty($filters['id_classe'])) $this->db->where('ev.id_classe', $filters['id_classe']);
        if (!empty($filters['id_matiere'])) $this->db->where('ev.id_matiere', $filters['id_matiere']);
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function create_record($data)
    {
        $data['uuid'] = generate_uuid();
        $data['cree_le'] = date('Y-m-d H:i:s');
        return $this->db->insert('evaluations', $data) ? ['success' => true] : ['success' => false];
    }

    public function update_record($id, $data)
    {
        $this->db->where('uuid', $id);
        $data['modifie_le'] = date('Y-m-d H:i:s');
        return $this->db->update('evaluations', $data);
    }

    public function delete_record($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('evaluations', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}