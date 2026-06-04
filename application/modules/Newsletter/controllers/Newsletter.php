<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Newsletter extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Admin'));
            exit;
        }
    }

    // Afficher la liste des emails inscrits
    public function index()
    {
        $data['emails'] = $this->Model->read('newsletter', null, 'id_newsletter');
        $this->load->view('Newsletter_View', $data);
    }

    // Ajouter un nouvel email
    public function create()
    {
        $email = $this->input->post('email');

        $data = array(
            'email' => $email
        );

        $rsp = $this->Model->create('newsletter', $data);

        $sms = [];
        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-success fade show mt-1 message" role="alert">
                             Email ajouté avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-danger fade show mt-1 message" role="alert">
                             <strong>Oups!</strong> Cet email existe déjà ou une erreur est survenue.
                         </div>';
        }

        $this->session->set_flashdata($sms);
        redirect(base_url('Newsletter'));
    }

    // Supprimer un email
    public function delete()
    {
        $uuid = $this->input->post('uuid');
        $rsp = $this->Model->delete('newsletter', ['uuid' => $uuid]);

        $sms = [];
        if ($rsp) {
            $sms['sms'] = '<div class="alert alert-success fade show mt-1 message" role="alert">
                             Email supprimé avec succès.
                         </div>';
        } else {
            $sms['sms'] = '<div class="alert alert-danger fade show mt-1 message" role="alert">
                             <strong>Oups!</strong> Une erreur est survenue.
                         </div>';
        }

        $this->session->set_flashdata($sms);
        redirect(base_url('Newsletter'));
    }
}