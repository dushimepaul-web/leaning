<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('can_view')) {
    function can_view($slug) { return has_perm($slug, 'can_view'); }
    function can_create($slug) { return has_perm($slug, 'can_add'); }
    function can_edit($slug) { return has_perm($slug, 'can_edit'); }
    function can_delete($slug) { return has_perm($slug, 'can_delete'); }
    function can_export($slug) { return has_perm($slug, 'can_export'); }
    function can_imprimer($slug) { return has_perm($slug, 'can_imprimer'); }

    function has_perm($slug, $perm = 'can_view') {
        $CI =& get_instance();
        $user_role = $CI->session->userdata('id_role');
        if ($user_role == 1) return true;
        $CI->load->model('Role_permission_model');
        return $CI->Role_permission_model->check_permission($user_role, $slug, $perm);
    }
}
