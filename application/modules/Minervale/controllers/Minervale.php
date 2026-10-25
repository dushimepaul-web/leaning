<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Base_minervale.php';

class Minervale extends Base_minervale
{
    public function index()
    {
        $data = $this->_options();
        $data['title'] = 'Minervale - Tableau de bord';
        $this->load->view('dashboard', $data);
    }
}
