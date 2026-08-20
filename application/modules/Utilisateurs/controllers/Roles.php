<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function api_list() {
        $this->json_success($this->Model->read('roles', ['deleted_at' => null]));
    }

    public function api_get($id) {
        $r = $this->Model->readOne('roles', ['uuid' => $id]);
        if (!$r) { $this->json_error('Rôle non trouvé', 404); return; }
        $r['menus'] = $this->Model->read('roles_menus', ['id_role' => $r['id_role']]);
        $this->json_success($r);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['code']) || empty($data['libelle'])) {
            $this->json_error('Code et libellé obligatoires'); return;
        }
        $allowed = ['code', 'libelle', 'hierarchie'];
        $insert = array_intersect_key($data, array_flip($allowed));
        $id = $this->Model->createLastId('roles', $insert);
        if ($id) $this->json_success(['id_role' => $id], 'Rôle créé');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (!$this->Model->readOne('roles', ['uuid' => $id])) {
            $this->json_error('Rôle non trouvé', 404); return;
        }
        $allowed = ['code', 'libelle', 'hierarchie'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if ($this->Model->update('roles', ['uuid' => $id], $update))
            $this->json_success(null, 'Rôle mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('roles', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Rôle supprimé');
        else $this->json_error('Erreur');
    }

    public function api_permissions_get($idRole) {
        $roleId = (int)$idRole;
        $role = $this->Model->readOne('roles', ['id_role' => $roleId]);
        if (!$role) { $this->json_error('Rôle non trouvé', 404); return; }
        $perms = $this->Model->read('roles_menus', ['id_role' => $roleId]);
        $this->json_success($perms);
    }

    public function api_permissions_update_by_id($idRole) {
        $roleId = (int)$idRole;
        $role = $this->Model->readOne('roles', ['id_role' => $roleId]);
        if (!$role) { $this->json_error('Rôle non trouvé', 404); return; }
        $input = $this->get_json_input();
        $items = $input['permissions'] ?? [];
        if (empty($items)) { $this->json_error('Aucune permission fournie'); return; }
        $permFields = ['can_view', 'can_add', 'can_edit', 'can_delete', 'can_export', 'can_imprimer'];
        $grouped = [];
        foreach ($items as $item) {
            $menuId = (int)$item['id_menu'];
            if (!$this->Model->readOne('menus', ['id_menu' => $menuId])) continue;
            if (!in_array($item['field'], $permFields, true)) continue;
            if (!isset($grouped[$menuId])) $grouped[$menuId] = [];
            $grouped[$menuId][$item['field']] = (int)$item['value'];
        }
        $this->db->trans_start();
        foreach ($grouped as $menuId => $perms) {
            $existing = $this->Model->readOne('roles_menus', ['id_role' => $roleId, 'id_menu' => $menuId]);
            if ($existing) {
                $this->Model->update('roles_menus', ['id_role_menu' => $existing['id_role_menu']], $perms);
            } else {
                $perms['id_role'] = $roleId;
                $perms['id_menu'] = $menuId;
                $this->Model->create('roles_menus', $perms);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'update', 'Mise à jour permissions rôle #'.$roleId, 'roles_menus', $roleId);
            $this->json_success(null, 'Permissions mises à jour');
        } else {
            $this->json_error('Erreur lors de la sauvegarde');
        }
    }

    public function api_permissions_update($id) {
        $role = $this->Model->readOne('roles', ['uuid' => $id]);
        if (!$role) { $this->json_error('Rôle non trouvé', 404); return; }
        $roleId = $role['id_role'];
        $input = $this->get_json_input();
        $permissions = $input['permissions'] ?? [];
        if (empty($permissions)) {
            $this->json_error('Aucune permission fournie'); return;
        }
        $permFields = ['can_view', 'can_add', 'can_edit', 'can_delete', 'can_export', 'can_imprimer'];
        $this->db->trans_start();
        foreach ($permissions as $menuId => $perms) {
            if (!$this->Model->readOne('menus', ['id_menu' => (int)$menuId])) continue;
            $perms = array_intersect_key((array)$perms, array_flip($permFields));
            $perms = array_map('intval', $perms);
            if (empty($perms)) continue;
            $existing = $this->Model->readOne('roles_menus', ['id_role' => $roleId, 'id_menu' => $menuId]);
            if ($existing) {
                $this->Model->update('roles_menus', ['id_role_menu' => $existing['id_role_menu']], $perms);
            } else {
                $perms['id_role'] = $roleId;
                $perms['id_menu'] = $menuId;
                $this->Model->create('roles_menus', $perms);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'update', 'Mise à jour permissions rôle #'.$roleId, 'roles_menus', $roleId);
            $this->json_success(null, 'Permissions mises à jour');
        } else {
            $this->json_error('Erreur lors de la sauvegarde');
        }
    }
}
