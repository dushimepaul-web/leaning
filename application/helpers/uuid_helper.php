<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * UUID Helper
 * 
 * Fournit des fonctions utilitaires pour la génération d'identifiants uniques universels (UUID v4).
 */

if (!function_exists('generate_uuid')) {
    /**
     * Génère un UUID version 4 conforme aux standards RFC 4122.
     * 
     * @return string L'UUID généré sous forme de chaîne hexadécimale formatée.
     */
    function generate_uuid() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
