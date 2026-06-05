<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timetable extends MY_Controller {

    function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Admin'));
            exit;
        }
    }

    public function index()
    {    
        $data['timetable'] = $this->Model->readQuery("
            SELECT ti.*, 
                   te.nom AS nom_teacher, 
                   te.prenom AS prenom_teacher 
            FROM timetable ti
            LEFT JOIN teachers te ON te.id_teacher = ti.id_teacher
        ");

        $data['teachers'] = $this->Model->read('teachers');

        $this->load->view('timetableView',$data);
    }

    function Creer_timetable()
    {
        $date_debut   = $this->input->post('date_debut');
        $date_defin   = $this->input->post('date_defin');
        $id_teacher   = $this->input->post('id_teacher');

        // Sécurité simple
        if(empty($date_debut) || empty($date_defin) || empty($id_teacher)){
            $sms['sms'] = '<div class="alert alert-danger">Veuillez remplir tous les champs.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('timetable'));
        }

        $data = array(
            'date_debut'     => $date_debut,
            'date_defin'     => $date_defin,
            'id_teacher'     => $id_teacher,
            'date_insertion' => date('Y-m-d H:i:s')  // obligatoire car NOT NULL
        );

        $rsp = $this->Model->create('timetable', $data);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message">Contenu créé avec succès.</div>';
        } else {
            $sms['sms'] = '<div class="alert alert-danger">Erreur inconnue.</div>';
        }

        $this->session->set_flashdata($sms);
        redirect(base_url('timetable'));
    }

    // =====================================================
    // UPDATE
    // =====================================================
    function Update_timetable()
    {
        $uuid = $this->input->post('uuid');
        $date_debut   = $this->input->post('date_debut');
        $date_defin   = $this->input->post('date_defin');
        $id_teacher   = $this->input->post('id_teacher');

        if(empty($uuid) || empty($date_debut) || empty($date_defin) || empty($id_teacher)){
            $sms['sms'] = '<div class="alert alert-danger">Veuillez remplir tous les champs.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('timetable'));
        }

        $data = array(
            'date_debut' => $date_debut,
            'date_defin' => $date_defin,
            'id_teacher' => $id_teacher
            // NE PAS mettre "time" → MySQL le gère automatiquement
        );

        $rsp = $this->Model->update('timetable', ['uuid' => $uuid], $data);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message">Contenu mis à jour avec succès.</div>';
        } else {
            $sms['sms'] = '<div class="alert alert-danger">Erreur inconnue.</div>';
        }

        $this->session->set_flashdata($sms);
        redirect(base_url('timetable'));
    }

    
    function Supprimer_timetable()
    {
        $uuid = $this->input->post('uuid');

        $rsp = $this->Model->delete('timetable', ['uuid' => $uuid]);

        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message">Contenu supprimé avec succès.</div>';
        } else {
            $sms['sms'] = '<div class="alert alert-danger">Erreur inconnue.</div>';
        }

        $this->session->set_flashdata($sms);
        redirect(base_url('timetable'));
    }
}
