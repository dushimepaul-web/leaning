<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administration extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        redirect(base_url('Parametres'));
    }
}
