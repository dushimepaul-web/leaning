<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Materiels_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('m.deleted_at', null);
        if (!empty($filters['statut'])) $this->db->where('m.statut', $filters['statut']);
        $this->db->order_by('m.id_materiel', 'DESC');
        return $this->db->get('materiels_scolaires m')->result_array();
    }

    public function create($data)
    {
        $data['uuid'] = generate_uuid();
        return $this->db->insert('materiels_scolaires', $data) ? ['success' => true] : ['success' => false];
    }

    public function update($id, $data)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('materiels_scolaires', $data);
    }

    public function delete($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('materiels_scolaires', ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    public function initialiser($data)
    {
        // Logique d'initialisation matériel
        return ['success' => true, 'message' => 'Matériels initialisés'];
    }
}