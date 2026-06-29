<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utilisateurs_model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = [])
    {
        $this->db->where('u.deleted_at', null);
        $this->db->select('u.*, r.libelle as role_libelle');
        $this->db->from('utilisateurs u');
        $this->db->join('roles r', 'u.role_id = r.id_role', 'left');
        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('u.nom', $filters['search'])
                ->or_like('u.prenom', $filters['search'])
                ->or_like('u.email', $filters['search'])
                ->group_end();
        }
        $this->db->order_by('u.nom', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->where('u.deleted_at', null);
        $this->db->where('u.uuid', $id);
        $this->db->select('u.*, r.libelle as role_libelle, r.id_role');
        $this->db->from('utilisateurs u');
        $this->db->join('roles r', 'u.role_id = r.id_role', 'left');
        return $this->db->get()->row_array();
    }

    public function create_record($data)
    {
        $required = ['nom', 'prenom', 'email', 'role_id', 'mot_de_passe'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Champ requis manquant: $field"];
            }
        }
        $data['uuid'] = generate_uuid();
        $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        $data['actif'] = $data['actif'] ?? 1;
        $data['cree_le'] = date('Y-m-d H:i:s');
        $data['modifie_le'] = date('Y-m-d H:i:s');
        if ($this->db->insert('utilisateurs', $data)) {
            return ['success' => true, 'id_utilisateur' => $this->db->insert_id()];
        }
        return ['success' => false, 'message' => 'Erreur insertion'];
    }

    public function update_record($id, $data)
    {
        $this->db->where('uuid', $id);
        if (!empty($data['mot_de_passe'])) {
            $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        }
        $data['modifie_le'] = date('Y-m-d H:i:s');
        return $this->db->update('utilisateurs', $data);
    }

    public function delete_record($id)
    {
        return $this->update_record($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    // Rôles
    public function get_roles($filters = [])
    {
        $this->db->where('deleted_at', null);
        return $this->db->get('roles')->result_array();
    }

    public function get_role_permissions($id_role)
    {
        $this->db->where('id_role', $id_role);
        return $this->db->get('roles_menus')->result_array();
    }

    public function update_role_permissions($id_role, $menus)
    {
        $this->db->where('id_role', $id_role);
        $this->db->delete('roles_menus');
        
        foreach ($menus as $menu_id => $perms) {
            $this->db->insert('roles_menus', [
                'uuid' => generate_uuid(),
                'id_role' => $id_role,
                'id_menu' => $menu_id,
                'can_view' => $perms['view'] ?? 0,
                'can_create' => $perms['create'] ?? 0,
                'can_edit' => $perms['edit'] ?? 0,
                'can_delete' => $perms['delete'] ?? 0,
            ]);
        }
        return true;
    }

    // Menus
    public function get_menus_tree()
    {
        $menus = $this->read('menus', ['deleted_at' => null], 'ordre');
        $tree = [];
        foreach ($menus as $m) {
            if ($m['parent_id'] == 0 || empty($m['parent_id'])) {
                $tree[] = $m;
            }
        }
        return $tree;
    }
}