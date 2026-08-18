<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Security Helper
 * 
 * Fournit des fonctions d'échappement et de sécurisation des données affichées.
 */

if (!function_exists('e')) {
    /**
     * Échappe les caractères spéciaux pour éviter les failles XSS.
     * 
     * @param string|null $str La chaîne de caractères à échapper.
     * @return string La chaîne sécurisée.
     */
    function e($str)
    {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
} 
