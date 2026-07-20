<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('n.deleted_at', null);
        $this->db->select('n.*, u.nom as user_nom, u.prenom as user_prenom');
        $this->db->from('notifications n');
        $this->db->join('utilisateurs u', 'n.id_utilisateur = u.id_utilisateur', 'left');
        if (!empty($filters['id_utilisateur'])) $this->db->where('n.id_utilisateur', $filters['id_utilisateur']);
        if (isset($filters['lu']) && $filters['lu'] !== '') $this->db->where('n.lu', $filters['lu']);
        $this->db->order_by('n.cree_le', 'DESC');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function get_non_lues($id_utilisateur)
    {
        $this->db->where('id_utilisateur', $id_utilisateur);
        $this->db->where('lu', 0);
        $this->db->where('deleted_at', null);
        $q = $this->db->get('notifications');
        if ($q === false) return array();
        return $q->result_array();
    }

    public function create_record($data)
    {
        $data['uuid'] = generate_uuid();
        $data['cree_le'] = date('Y-m-d H:i:s');
        $data['lu'] = 0;
        return $this->db->insert('notifications', $data) ? ['success' => true] : ['success' => false];
    }

    public function marquer_lu($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('notifications', ['lu' => 1, 'date_lecture' => date('Y-m-d H:i:s')]);
    }

    public function marquer_tous_lus($id_utilisateur)
    {
        $this->db->where('id_utilisateur', $id_utilisateur);
        $this->db->where('lu', 0);
        return $this->db->update('notifications', ['lu' => 1, 'date_lecture' => date('Y-m-d H:i:s')]);
    }

    public function delete_record($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('notifications', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}