<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Fonction pour charger tous les réglages dans une variable globale
if (!function_exists('get_setting')) {
    function get_setting($key) {
        // Accéder à l'instance CodeIgniter
        $CI =& get_instance();
        // Charger le modèle Settings_model si ce n'est pas déjà fait
        $CI->load->model('Model');
        
        return $CI->Model->get_setting($key);
    }
}
