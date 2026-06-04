<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('e')) {
    function e($str)
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
