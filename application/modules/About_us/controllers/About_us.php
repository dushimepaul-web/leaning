<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About_us extends MY_Controller {

	function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
		$this->load->model('Model');

		if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Admin'));
            exit;
        }
    }

    // Afficher tous les contenus (pour l'admin)
	public function index()
	{
		// Récupérer tous les contenus de la table institution_contents
		$data['about_us'] = $this->Model->read('institution_contents', null, 'IdContent');
		
		// Vérifier si des données ont été récupérées
		if($data['about_us'] === null) {
			$data['about_us'] = array(); // Initialiser à un tableau vide si null
		}
		
		// Debug: Afficher le nombre d'enregistrements trouvés (optionnel)
		// echo "Nombre de contenus trouvés : " . count($data['about_us']);
		
		$this->load->view('about_us_view', $data);
	}

    // Créer un contenu
	function Creer_about_us(){
        $type = $this->input->post('type');
        $title = $this->input->post('title');
		$description = $this->input->post('details');
        $status = $this->input->post('status') ? $this->input->post('status') : 'Active';
        
        // Si le type n'est pas spécifié, le définir par défaut
        if(empty($type)) {
            $type = 'VALEUR';
        }
        
		$data = array(
            'Type' => $type,
            'Title' => $title,
            'Description' => $description,
            'Status' => $status
        );

		$rsp = $this->Model->create('institution_contents', $data);

		if ($rsp) {
			$sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
						     Contenu créé avec succès.
						 </div>';
		} else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
						     <strong class="text-danger">Oups!</strong> Erreur inconnue, contactez l\'administrateur.
						 </div>';
		}
		$this->session->set_flashdata($sms);
		redirect(base_url('about_us'));
	}

    // Mettre à jour un contenu
	function Update_about_us(){
		$id_content = $this->input->post('uuid');
        $type = $this->input->post('type');
        $title = $this->input->post('title');
        $description = $this->input->post('details');
        $status = $this->input->post('status');
		
		$data = array(
            'Type' => $type,
            'Title' => $title,
            'Description' => $description,
            'Status' => $status
        );

		$rsp = $this->Model->update('institution_contents', ['IdContent' => $id_content], $data);

		if ($rsp) {
			$sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
						     Contenu mis à jour avec succès.
						 </div>';
		} else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
						     <strong class="text-danger">Oups!</strong> Erreur inconnue, contactez l\'administrateur.
						 </div>';
		}
		$this->session->set_flashdata($sms);
		redirect(base_url('about_us'));
	}

    // Supprimer un contenu
	function Supprimer_about_us(){
		$id_content = $this->input->post('uuid');
        
		$rsp = $this->Model->delete('institution_contents', ['IdContent' => $id_content]);

		if ($rsp) {
			$sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
						     Contenu supprimé avec succès.
						 </div>';
		} else {
            $sms['sms'] = '<div class="alert alert-background fade show mt-1 message" role="alert">
						     <strong class="text-danger">Oups!</strong> Erreur inconnue, contactez l\'administrateur.
						 </div>';
		}
		$this->session->set_flashdata($sms);
		redirect(base_url('about_us'));
	}
}
?>