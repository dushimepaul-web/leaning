<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Settings Helper
 * 
 * Fournit des fonctions pour la récupération des configurations globales et la gestion des mentions académiques.
 */

if (!function_exists('get_setting')) {
    /**
     * Récupère une valeur de paramètre général de l'établissement.
     * 
     * @param string $key La clé du paramètre.
     * @param mixed $default Valeur par défaut si le paramètre n'existe pas.
     * @return mixed La valeur du paramètre.
     */
    function get_setting($key, $default = null) {
        $CI =& get_instance();
        $CI->load->model('Model');
        return $CI->Model->get_setting($key, $default);
    }
}

if (!function_exists('get_mention')) {
    /**
     * Retourne la mention d'un élève selon sa moyenne et sa note de référence (sur /20, /100, etc.).
     * Les seuils sont dynamiques et modifiables depuis le module de configuration.
     * 
     * @param float|null $moyenne La moyenne obtenue par l'élève.
     * @param float $sur La note maximale de référence (ex: 20).
     * @return string Le libellé de la mention correspondante.
     */
    function get_mention($moyenne, $sur = 20) {
        if ($moyenne === null || $moyenne === '' || $sur <= 0) return '-';
        $moyenne = floatval($moyenne);
        $sur = floatval($sur);
        $pct = ($moyenne / $sur) * 100;

        $seuils = array(
            'mention_excellent'   => 90,
            'mention_tres_bien'   => 80,
            'mention_bien'        => 70,
            'mention_assez_bien'  => 60,
            'mention_passable'    => 40,
            'mention_insuffisant' => 0,
        );
        $libelles = array(
            'mention_excellent'   => 'Excellent',
            'mention_tres_bien'   => 'Très Bien',
            'mention_bien'        => 'Bien',
            'mention_assez_bien'  => 'Assez Bien',
            'mention_passable'    => 'Passable',
            'mention_insuffisant' => 'Insuffisant',
        );

        $CI =& get_instance();
        $ordre = array('mention_excellent', 'mention_tres_bien', 'mention_bien', 'mention_assez_bien', 'mention_passable', 'mention_insuffisant');

        foreach ($ordre as $cle) {
            $seuil = floatval(get_setting($cle, $seuils[$cle]));
            if ($pct >= $seuil) {
                return get_setting($cle . '_libelle', $libelles[$cle]);
            }
        }
        return $libelles['mention_insuffisant'];
    }
}
