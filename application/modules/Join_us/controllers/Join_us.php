<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Join_us extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Admin'));
            exit;
        }
    }

    // LISTE
    public function index()
    {
        $data['joinus'] = $this->Model->read('join_us', null, 'id');
        $this->load->view('Join_us_View', $data);
    }

    // CREATE
    public function create()
    {
        $titre = $this->input->post('titre');
        $description = $this->input->post('description');

        $data = array(
            'titre'       => $titre,
            'description' => $description
        );

        $rsp = $this->Model->create('join_us', $data);

        $sms = [];
        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-success fade show mt-1 message">Section Join Us créée avec succès.</div>';
        } else {
            $sms['sms'] = '<div class="alert alert-danger fade show mt-1 message">Erreur inconnue, contactez l\'administrateur.</div>';
        }

        $this->session->set_flashdata($sms);
        redirect(base_url('Join_us'));
    }

    // UPDATE
    public function update()
    {
        $uuid          = $this->input->post('uuid');
        $titre       = $this->input->post('titre');
        $description = $this->input->post('description');

        $data = array(
            'titre'       => $titre,
            'description' => $description
        );

        $rsp = $this->Model->update('join_us', ['uuid' => $uuid], $data);

        $sms = [];
        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-success fade show mt-1 message">Section Join Us modifiée avec succès.</div>';
        } else {
            $sms['sms'] = '<div class="alert alert-danger fade show mt-1 message">Erreur inconnue, contactez l\'administrateur.</div>';
        }

        $this->session->set_flashdata($sms);
        redirect(base_url('Join_us'));
    }

    // DELETE
    public function delete()
    {
        $uuid = $this->input->post('uuid');
        $rsp = $this->Model->delete('join_us', ['uuid' => $uuid]);

        $sms = [];
        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-success fade show mt-1 message">Section Join Us supprimée avec succès.</div>';
        } else {
            $sms['sms'] = '<div class="alert alert-danger fade show mt-1 message">Erreur inconnue, contactez l\'administrateur.</div>';
        }

        $this->session->set_flashdata($sms);
        redirect(base_url('Join_us'));
    }
}