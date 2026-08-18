<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Permission Helper
 * 
 * Fournit des fonctions pour la vérification granulaire des droits d'accès des utilisateurs connectés.
 */

if (!function_exists('can_view')) {
    /** Vérifie si l'utilisateur peut voir un module. */
    function can_view($slug) { return has_perm($slug, 'can_view'); }
    
    /** Vérifie si l'utilisateur peut créer un enregistrement dans un module. */
    function can_create($slug) { return has_perm($slug, 'can_add'); }
    
    /** Vérifie si l'utilisateur peut modifier un enregistrement. */
    function can_edit($slug) { return has_perm($slug, 'can_edit'); }
    
    /** Vérifie si l'utilisateur peut supprimer un enregistrement. */
    function can_delete($slug) { return has_perm($slug, 'can_delete'); }
    
    /** Vérifie si l'utilisateur peut exporter les données d'un module. */
    function can_export($slug) { return has_perm($slug, 'can_export'); }
    
    /** Vérifie si l'utilisateur peut imprimer/générer des documents. */
    function can_imprimer($slug) { return has_perm($slug, 'can_imprimer'); }

    /**
     * Vérifie une permission spécifique pour le rôle de l'utilisateur connecté.
     * Les utilisateurs ayant le rôle ID 1 disposent de tous les droits (super-admin).
     * 
     * @param string $slug Le slug d'identification du module.
     * @param string $perm La permission ciblée (ex: 'can_view', 'can_add', etc.).
     * @return bool True si autorisé, False sinon.
     */
    function has_perm($slug, $perm = 'can_view') {
        $CI =& get_instance();
        $user_role = $CI->session->userdata('id_role');
        if ($user_role == 1) return true;
        $CI->load->model('Role_permission_model');
        return $CI->Role_permission_model->check_permission($user_role, $slug, $perm);
    }
}
