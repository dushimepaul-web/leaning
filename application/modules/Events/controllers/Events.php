<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Events extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Admin'));
            exit;
        }
    }

    public function index()
    {
        $data['events'] = $this->Model->read('events', null, 'id');
        $this->load->view('Events_View', $data);
    }

    public function create()
    {
        $titre           = $this->input->post('titre');
        $date_debut      = $this->input->post('date_debut');
        $date_fin        = $this->input->post('date_fin');
        $lieu            = $this->input->post('lieu');
         //Mois et année automatiques
        $mois  = strtoupper(date('F', strtotime($date_debut)));
        $annee = date('Y', strtotime($date_debut));              
        $est_en_ligne    = $this->input->post('est_en_ligne') ? 1 : 0;
        $description     = $this->input->post('description');

        // Upload image
        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $image = $this->upload_file($_FILES['image']);
        }

        $data = array(
            'titre'            => $titre,
            'date_debut'       => $date_debut,
            'date_fin'         => $date_fin,
            'lieu'             => $lieu,
            'mois'             => strtoupper($mois),
            'annee'            => $annee,
            'est_en_ligne'     => $est_en_ligne,
            'description'      => $description,
            'image'            => $image
        );

        $this->Model->create('events', $data);

        $this->session->set_flashdata(['sms' => '<div class="alert alert-success">Événement ajouté.</div>']);
        redirect(base_url('Events'));
    }

    public function update()
    {
        $uuid              = $this->input->post('uuid');
        $titre           = $this->input->post('titre');
        $date_debut      = $this->input->post('date_debut');
        $date_fin        = $this->input->post('date_fin');
        $lieu            = $this->input->post('lieu');
        $mois  = strtoupper(date('F', strtotime($date_debut)));
        $annee = date('Y', strtotime($date_debut));
        $est_en_ligne    = $this->input->post('est_en_ligne') ? 1 : 0;
        $description     = $this->input->post('description');

        // Image update
        if (!empty($_FILES['image']['name'])) {
            $image = $this->upload_file($_FILES['image']);
        } else {
            $image = $this->input->post('hidden_image');
        }

        $data = array(
            'titre'            => $titre,
            'date_debut'       => $date_debut,
            'date_fin'         => $date_fin,
            'lieu'             => $lieu,
            'mois'             => strtoupper($mois),
            'annee'            => $annee,
            'est_en_ligne'     => $est_en_ligne,
            'description'      => $description,
            'image'            => $image
        );

        $this->Model->update('events', ['uuid' => $uuid], $data);

        $this->session->set_flashdata(['sms' => '<div class="alert alert-info">Événement modifié.</div>']);
        redirect(base_url('Events'));
    }

    public function delete()
    {
        $uuid = $this->input->post('uuid');
        $this->Model->delete('events', ['uuid' => $uuid]);
        
        $this->session->set_flashdata(['sms' => '<div class="alert alert-danger">Événement supprimé.</div>']);
        redirect(base_url('Events'));
    }

    private function upload_file($file)
    {
        $folder = FCPATH . 'attachments/events/';

        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $valid_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $valid_ext)) return null;

        $filename = time() . "_" . uniqid() . "." . $ext;

        move_uploaded_file($file['tmp_name'], $folder . $filename);

        return $filename;
    }


    function ChangeStatus(){
      $uuid=$this->input->post('uuid');
      $IsActive=$this->input->post('IsActive');
      if ($IsActive==1) {
        $status=0;
      }else{
        $status=1;
      }
      $rsp=$this->Model->update('events',['uuid'=>$uuid],['IsActive'=>$status]);

        if ($rsp) {
            $sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
                             Content updated successfully.
                         </div>';
        }else{
            $sms['sms']='<div class="alert alert-background fade show mt-1 message" role="alert">
                             <strong class="text-danger">Oups!</strong> An unknown error, contact admin!.
                         </div>';
        }
        $this->session->set_flashdata($sms);
        redirect(base_url('Events')); 
    }
}