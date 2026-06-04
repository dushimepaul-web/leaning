<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Horaire extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
    	$sql = "
            SELECT
                c.id_course,
                c.nom_course,
                t.date_debut,
                t.date_defin,
                tc.localisation,
                tc.price
            FROM timetable_courses tc
            INNER JOIN courses c ON c.id_course = tc.id_course
            INNER JOIN timetable t ON t.id_timetable = tc.id_timetable
            ORDER BY c.nom_course ASC, t.date_debut ASC
        ";

        $data['timetables'] = $this->Model->readQuery($sql);
        $this->load->view('Horaire_View',$data);

    }

    }