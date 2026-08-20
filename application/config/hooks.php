<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$hook['pre_system'] = function ()
{
    if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && empty($_POST))
    {
        if (isset($_SERVER['HTTP_X_CSRF_TOKEN']) && $_SERVER['HTTP_X_CSRF_TOKEN'] !== '')
        {
            $_POST['csrf_test_name'] = $_SERVER['HTTP_X_CSRF_TOKEN'];
        }
        else
        {
            $raw = file_get_contents('php://input');
            $decoded = $raw ? json_decode($raw, true) : null;
            if (is_array($decoded) && isset($decoded['csrf_test_name']) && is_string($decoded['csrf_test_name']))
            {
                $_POST['csrf_test_name'] = $decoded['csrf_test_name'];
            }
        }
    }
};