<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_permission_model extends CI_Model
{
    public function check_permission($id_role, $slug, $perm = 'can_view')
    {
        $this->db->select('rm.' . $perm);
        $this->db->from('roles_menus rm');
        $this->db->join('menus m', 'rm.id_menu = m.id_menu');
        $this->db->where('rm.id_role', $id_role);
        $this->db->where('m.code', $slug);
        $this->db->limit(1);
        $query = $this->db->get();
        $row = $query->row_array();
        return $row ? (bool)$row[$perm] : false;
    }

    public function get_role_menus($id_role)
    {
        $this->db->select('m.*, rm.can_view, rm.can_add, rm.can_edit, rm.can_delete, rm.can_export, rm.can_imprimer');
        $this->db->from('roles_menus rm');
        $this->db->join('menus m', 'rm.id_menu = m.id_menu');
        $this->db->where('rm.id_role', $id_role);
        $this->db->where('rm.can_view', 1);
        $this->db->order_by('m.ordre', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
}
